<?php

namespace App\Support;

use App\Models\User;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Giỏ hàng cửa hàng: một tài khoản — một giỏ trong DB.
 * Session chỉ còn để chuyển giỏ cũ (trước khi có bảng) vào tài khoản lúc đăng nhập.
 */
class StorefrontCart
{
    public const LEGACY_SESSION_KEY = 'theme_storefront_cart';

    /** @var array<int, array<string, mixed>>|null */
    private ?array $cachedItems = null;

    private bool $loaded = false;

    public function __construct(private readonly StorefrontIdentity $identity) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function items(): array
    {
        if ($this->loaded) {
            return $this->cachedItems ?? [];
        }

        $this->loaded = true;
        $user = $this->currentUser();
        if (! $user) {
            return $this->cachedItems = [];
        }

        return $this->cachedItems = $this->loadForUser((int) $user->ID);
    }

    /**
     * Ghi đè toàn bộ giỏ của tài khoản đang đăng nhập.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    public function replace(array $items): void
    {
        $user = $this->currentUser();
        $normalized = $this->normalizeLines($items);
        if (! $user) {
            $this->cachedItems = [];
            $this->loaded = true;

            return;
        }

        $this->persist((int) $user->ID, $normalized);
        $this->cachedItems = $normalized;
        $this->loaded = true;
    }

    public function clear(?User $user = null): void
    {
        $target = $user ?? $this->currentUser();
        if ($target) {
            UserCart::query()->where('USER_ID', $target->ID)->delete();
        }

        $this->forgetLegacySession();
        $this->cachedItems = [];
        $this->loaded = true;
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, totalQuantity: int, totalPrice: int}
     */
    public function snapshot(): array
    {
        $items = $this->items();
        $totalQuantity = 0;
        $totalPrice = 0;
        foreach ($items as $line) {
            $qty = (int) ($line['quantity'] ?? 0);
            $totalQuantity += $qty;
            $totalPrice += (int) ($line['line_price'] ?? ((int) ($line['price'] ?? 0) * $qty));
        }

        return [
            'items' => $items,
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
        ];
    }

    /**
     * Payload checkout khi form không gửi ITEMS.
     *
     * @return array<int, array<string, mixed>>
     */
    public function toOrderItems(): array
    {
        return array_values(array_map(static function (array $line): array {
            return [
                'PRODUCT_ID' => (int) ($line['variant_id'] ?? $line['PRODUCT_ID'] ?? 0),
                'QUANTITY' => (float) ($line['quantity'] ?? $line['QUANTITY'] ?? 0),
                'PRICE' => (float) ($line['price'] ?? $line['PRICE'] ?? 0),
                'TEN_SAN_PHAM' => $line['title'] ?? $line['TEN_SAN_PHAM'] ?? null,
                'HINH_ANH' => $line['image'] ?? $line['HINH_ANH'] ?? null,
                'HANDLE' => $line['handle'] ?? $line['HANDLE'] ?? null,
            ];
        }, $this->items()));
    }

    /**
     * Đưa giỏ còn sót trên session vào tài khoản (một lần, lúc đăng nhập).
     */
    public function absorbLegacySession(?Request $request = null, ?User $user = null): void
    {
        $request ??= request();
        $user ??= $this->currentUser();
        if (! $user || ! $request->hasSession()) {
            return;
        }

        $legacy = $request->session()->pull(self::LEGACY_SESSION_KEY, []);
        if (! is_array($legacy) || $legacy === []) {
            return;
        }

        $merged = $this->mergeLines(
            $this->loadForUser((int) $user->ID),
            array_values($legacy)
        );
        $this->persist((int) $user->ID, $merged);
        $this->cachedItems = $merged;
        $this->loaded = true;
    }

    private function currentUser(): ?User
    {
        $user = $this->identity->user();
        if ($user) {
            return $user;
        }

        $auth = Auth::user();

        return $auth instanceof User ? $auth : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadForUser(int $userId): array
    {
        return UserCart::query()
            ->where('USER_ID', $userId)
            ->orderBy('POSITION')
            ->orderBy('ID')
            ->get()
            ->map(static fn (UserCart $row): array => $row->toCartLine())
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function persist(int $userId, array $items): void
    {
        DB::transaction(function () use ($userId, $items): void {
            UserCart::query()->where('USER_ID', $userId)->delete();

            $now = now();
            $rows = [];
            foreach (array_values($items) as $position => $line) {
                $quantity = max(0, (int) ($line['quantity'] ?? 0));
                $variantId = (int) ($line['variant_id'] ?? 0);
                if ($quantity <= 0 || $variantId <= 0) {
                    continue;
                }

                $price = max(0, (int) ($line['price'] ?? 0));
                $rows[] = [
                    'USER_ID' => $userId,
                    'VARIANT_ID' => $variantId,
                    'PRODUCT_ID' => (int) ($line['product_id'] ?? 0) ?: null,
                    'SAPO_VARIANT_ID' => isset($line['sapo_variant_id']) && (int) $line['sapo_variant_id'] > 0
                        ? (int) $line['sapo_variant_id']
                        : null,
                    'TITLE' => $this->limit((string) ($line['title'] ?? ''), 1000),
                    'VARIANT_TITLE' => $this->limit((string) ($line['variant_title'] ?? 'Mặc định'), 500),
                    'QUANTITY' => $quantity,
                    'PRICE' => $price,
                    'LINE_PRICE' => $quantity * $price,
                    'IMAGE' => (string) ($line['image'] ?? ''),
                    'HANDLE' => $this->limit((string) ($line['handle'] ?? ''), 500),
                    'CATEGORY_ID' => (int) ($line['category_id'] ?? 0) ?: null,
                    'STOCK' => isset($line['stock']) ? max(0, (int) $line['stock']) : null,
                    'POSITION' => $position,
                    'CRT_DT' => $now,
                    'UPD_DT' => $now,
                ];
            }

            if ($rows !== []) {
                UserCart::query()->insert($rows);
            }
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $current
     * @param  array<int, array<string, mixed>>  $incoming
     * @return array<int, array<string, mixed>>
     */
    private function mergeLines(array $current, array $incoming): array
    {
        $byVariant = [];
        foreach ($current as $line) {
            $id = (int) ($line['variant_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $byVariant[$id] = $line;
        }

        foreach ($incoming as $line) {
            if (! is_array($line)) {
                continue;
            }
            $id = (int) ($line['variant_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            if (! isset($byVariant[$id])) {
                $byVariant[$id] = $line;
                continue;
            }

            $qty = (int) ($byVariant[$id]['quantity'] ?? 0) + (int) ($line['quantity'] ?? 0);
            $price = (int) ($line['price'] ?? $byVariant[$id]['price'] ?? 0);
            $byVariant[$id]['quantity'] = $qty;
            $byVariant[$id]['price'] = $price;
            $byVariant[$id]['line_price'] = $qty * $price;
        }

        return $this->normalizeLines(array_values($byVariant));
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private function normalizeLines(array $items): array
    {
        $normalized = [];
        foreach ($items as $line) {
            if (! is_array($line)) {
                continue;
            }
            $quantity = max(0, (int) ($line['quantity'] ?? 0));
            $variantId = (int) ($line['variant_id'] ?? 0);
            if ($quantity <= 0 || $variantId <= 0) {
                continue;
            }
            $price = max(0, (int) ($line['price'] ?? 0));
            $line['variant_id'] = $variantId;
            $line['quantity'] = $quantity;
            $line['price'] = $price;
            $line['line_price'] = $quantity * $price;
            $normalized[] = $line;
        }

        return array_values($normalized);
    }

    private function forgetLegacySession(): void
    {
        $request = request();
        if ($request->hasSession()) {
            $request->session()->forget(self::LEGACY_SESSION_KEY);
        }
    }

    private function limit(string $value, int $max): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return mb_strlen($value) > $max ? mb_substr($value, 0, $max) : $value;
    }
}
