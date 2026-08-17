<?php

namespace App\Support;

use App\Enum\AppConstant;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class StorefrontInventory
{
    /**
     * Chuẩn hóa mọi ID từ UI (local variant, Sapo variant hoặc product)
     * về một biến thể local duy nhất.
     *
     * @return array{
     *   product: Product,
     *   variant: ProductVariant,
     *   product_id: int,
     *   variant_id: int,
     *   sapo_variant_id: int|null,
     *   title: string,
     *   variant_title: string,
     *   handle: string,
     *   price: int,
     *   stock: int
     * }|null
     */
    public function resolve(int $identifier, bool $lockForUpdate = false): ?array
    {
        if ($identifier <= 0) {
            return null;
        }

        $variantQuery = ProductVariant::query()
            ->where(function (Builder $query) use ($identifier): void {
                $query->where('ID', $identifier)
                    ->orWhere('SAPO_VARIANT_ID', $identifier);
            })
            ->where('STATUS', AppConstant::STATUS_USING)
            ->where('IS_ACTIVE', true)
            ->orderByRaw('ID = ? DESC', [$identifier]);
        if ($lockForUpdate) {
            $variantQuery->lockForUpdate();
        }
        $variant = $variantQuery->first();

        $product = null;
        if ($variant) {
            $product = Product::query()
                ->whereKey($variant->PRODUCT_ID)
                ->where('STATUS', AppConstant::STATUS_USING)
                ->where('IS_ACTIVE', true)
                ->first();
        } else {
            $product = Product::query()
                ->where(function (Builder $query) use ($identifier): void {
                    $query->where('ID', $identifier)
                        ->orWhere('SAPO_ID', $identifier)
                        ->orWhere('ATTR1', (string) $identifier);
                })
                ->where('STATUS', AppConstant::STATUS_USING)
                ->where('IS_ACTIVE', true)
                ->first();

            if ($product) {
                $variantQuery = ProductVariant::query()
                    ->where('PRODUCT_ID', $product->ID)
                    ->where('STATUS', AppConstant::STATUS_USING)
                    ->where('IS_ACTIVE', true)
                    ->when(
                        (int) $product->ATTR1 > 0,
                        fn (Builder $query) => $query->orderByRaw(
                            'SAPO_VARIANT_ID = ? DESC',
                            [(int) $product->ATTR1]
                        )
                    )
                    ->orderBy('ID');
                if ($lockForUpdate) {
                    $variantQuery->lockForUpdate();
                }
                $variant = $variantQuery->first();
            }
        }

        if (! $product || ! $variant) {
            return null;
        }

        return [
            'product' => $product,
            'variant' => $variant,
            'product_id' => (int) $product->ID,
            'variant_id' => (int) $variant->ID,
            'sapo_variant_id' => (int) $variant->SAPO_VARIANT_ID ?: null,
            'title' => (string) $product->NAME,
            'variant_title' => (string) (
                $variant->ATTR4
                ?: (is_array($variant->OPTION_VALUES) ? implode(' / ', $variant->OPTION_VALUES) : '')
                ?: 'Mặc định'
            ),
            'handle' => Str::slug((string) $product->NAME),
            'price' => max(0, (int) round((float) $variant->PRODUCT_PRICE)),
            'stock' => max(0, (int) $variant->INVENTORY_QUANTITY),
        ];
    }

    /**
     * Đồng bộ giá, ID và số lượng của giỏ cũ theo tồn kho hiện tại.
     *
     * @param  array<int, array<string, mixed>>  $lines
     * @return array{items: array<int, array<string, mixed>>, changed: bool, notices: array<int, string>}
     */
    public function reconcileCart(array $lines): array
    {
        $items = [];
        $notices = [];
        $changed = false;

        foreach ($lines as $line) {
            $resolved = $this->resolve((int) ($line['variant_id'] ?? 0));
            if (! $resolved || $resolved['stock'] <= 0) {
                $changed = true;
                $notices[] = (($line['title'] ?? 'Sản phẩm').' đã hết hàng và được xóa khỏi giỏ.');
                continue;
            }

            $quantity = max(1, (int) ($line['quantity'] ?? 1));
            if ($quantity > $resolved['stock']) {
                $quantity = $resolved['stock'];
                $changed = true;
                $notices[] = $resolved['title'].' chỉ còn '.$resolved['stock'].' sản phẩm.';
            }

            $canonicalId = $resolved['variant_id'];
            $existingIndex = collect($items)->search(
                fn (array $item): bool => (int) $item['variant_id'] === $canonicalId
            );
            if ($existingIndex !== false) {
                $mergedQuantity = min(
                    $resolved['stock'],
                    (int) $items[$existingIndex]['quantity'] + $quantity
                );
                $items[$existingIndex]['quantity'] = $mergedQuantity;
                $items[$existingIndex]['line_price'] = $mergedQuantity * $resolved['price'];
                $items[$existingIndex]['stock'] = $resolved['stock'];
                $changed = true;
                continue;
            }

            $updated = $line;
            $updated['variant_id'] = $canonicalId;
            $updated['product_id'] = $resolved['product_id'];
            $updated['sapo_variant_id'] = $resolved['sapo_variant_id'];
            $updated['title'] = $resolved['title'];
            $updated['variant_title'] = $resolved['variant_title'];
            $updated['handle'] = $resolved['handle'];
            $updated['price'] = $resolved['price'];
            $updated['quantity'] = $quantity;
            $updated['line_price'] = $quantity * $resolved['price'];
            $updated['stock'] = $resolved['stock'];
            if ($updated !== $line) {
                $changed = true;
            }
            $items[] = $updated;
        }

        return compact('items', 'changed', 'notices');
    }
}
