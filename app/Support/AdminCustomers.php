<?php

namespace App\Support;

use App\Enum\TransactionStatusEnum;

class AdminCustomers
{
    /**
     * @param  list<array{id: int, user_id: int, name: string, phone: string, email: string, address: string, total: int, status: string, created_at: string, code: string, quantity: int}>  $orders
     * @param  list<array{id: int, name: string, phone: string, email: string, address: string, created_at: string}>  $accounts
     * @return list<array<string, mixed>>
     */
    public static function summarize(array $orders, array $accounts = []): array
    {
        $groups = [];

        foreach ($orders as $order) {
            $key = self::identity($order);
            if (! isset($groups[$key])) {
                $groups[$key] = self::blank($key);
            }

            $group = &$groups[$key];
            $group['name'] = self::firstFilled($group['name'], $order['name'] ?? '');
            $group['phone'] = self::firstFilled($group['phone'], $order['phone'] ?? '');
            $group['email'] = self::firstFilled($group['email'], $order['email'] ?? '');
            $group['address'] = self::firstFilled($group['address'], $order['address'] ?? '');
            $group['order_count']++;

            $status = (string) ($order['status'] ?? '');
            $total = (int) ($order['total'] ?? 0);
            if ($status === TransactionStatusEnum::CANCELLED->value) {
                $group['cancelled_count']++;
            } else {
                $group['spend'] += $total;
                $group['active_count']++;
                if ($status === TransactionStatusEnum::COMPLETED->value) {
                    $group['completed_count']++;
                    $group['completed_spend'] += $total;
                }
            }

            $createdAt = (string) ($order['created_at'] ?? '');
            if ($group['last_at'] === '' || strcmp($createdAt, $group['last_at']) > 0) {
                $group['last_at'] = $createdAt;
            }
            if ($group['first_at'] === '' || ($createdAt !== '' && strcmp($createdAt, $group['first_at']) < 0)) {
                $group['first_at'] = $createdAt;
            }

            $group['orders'][] = [
                'id' => (int) ($order['id'] ?? 0),
                'code' => (string) ($order['code'] ?? ''),
                'status' => $status,
                'total' => $total,
                'quantity' => (int) ($order['quantity'] ?? 0),
                'created_at' => $createdAt,
            ];
            unset($group);
        }

        foreach ($accounts as $account) {
            self::attachAccount($groups, $account);
        }

        $rows = array_values($groups);
        usort($rows, static function (array $a, array $b): int {
            $byDate = strcmp((string) $b['last_at'], (string) $a['last_at']);
            if ($byDate !== 0) {
                return $byDate;
            }

            return $b['spend'] <=> $a['spend'];
        });

        foreach ($rows as &$row) {
            usort($row['orders'], static fn (array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));
        }
        unset($row);

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public static function filter(array $rows, ?string $keyword): array
    {
        $needle = self::lower(trim((string) $keyword));
        if ($needle === '') {
            return $rows;
        }

        $digits = preg_replace('/\D+/', '', $needle) ?? '';

        return array_values(array_filter($rows, static function (array $row) use ($needle, $digits): bool {
            $name = self::lower((string) ($row['name'] ?? ''));
            $email = self::lower((string) ($row['email'] ?? ''));
            $phone = self::phoneDigits((string) ($row['phone'] ?? ''));
            if (str_contains($name, $needle) || str_contains($email, $needle)) {
                return true;
            }

            return $digits !== '' && str_contains($phone, $digits);
        }));
    }

    public static function find(array $rows, string $token): ?array
    {
        $raw = self::decodeKey($token);
        if ($raw === null) {
            return null;
        }

        foreach ($rows as $row) {
            if (($row['key'] ?? '') === $raw) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function present(array $row, bool $withOrders = false): array
    {
        $activeCount = (int) $row['active_count'];
        $payload = [
            'ID' => self::encodeKey((string) $row['key']),
            'HO_TEN' => trim((string) $row['name']) !== '' ? trim((string) $row['name']) : 'Khách lẻ',
            'SO_DIEN_THOAI' => (string) $row['phone'],
            'EMAIL' => (string) $row['email'],
            'DIA_CHI' => (string) $row['address'],
            'SO_DON' => (int) $row['order_count'],
            'SO_DON_CON_HIEU_LUC' => $activeCount,
            'TONG_CHI_TIEU' => (int) $row['spend'],
            'DON_TRUNG_BINH' => $activeCount > 0 ? (int) round($row['spend'] / $activeCount) : 0,
            'SO_DON_HOAN_THANH' => (int) $row['completed_count'],
            'TONG_HOAN_THANH' => (int) $row['completed_spend'],
            'SO_DON_DA_HUY' => (int) $row['cancelled_count'],
            'DON_GAN_NHAT' => self::formatDate((string) $row['last_at']),
            'MUA_TU' => self::formatDate((string) $row['first_at']),
            'CO_TAI_KHOAN' => (bool) $row['has_account'],
            'NGAY_TAO_TAI_KHOAN' => self::formatDate((string) $row['account_since']),
        ];

        if ($withOrders) {
            $payload['DON_HANG'] = array_map(static function (array $order): array {
                $status = (string) $order['status'];

                return [
                    'ID' => (int) $order['id'],
                    'MA_DON' => trim((string) $order['code']) !== '' ? (string) $order['code'] : '#'.$order['id'],
                    'TRANG_THAI' => $status,
                    'TRANG_THAI_TEXT' => self::statusText($status),
                    'TONG_TIEN' => (int) $order['total'],
                    'SO_LUONG' => (int) $order['quantity'],
                    'NGAY_TAO' => self::formatDate((string) $order['created_at']),
                ];
            }, $row['orders'] ?? []);
        }

        return $payload;
    }

    public static function encodeKey(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    public static function decodeKey(string $token): ?string
    {
        $b64 = strtr($token, '-_', '+/');
        $pad = strlen($b64) % 4;
        if ($pad > 0) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        $raw = base64_decode($b64, true);
        if (! is_string($raw) || ! preg_match('/^(p|u|e|g):.+$/', $raw)) {
            return null;
        }

        return $raw;
    }

    public static function phoneDigits(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if (str_starts_with($digits, '84') && strlen($digits) >= 11) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    /**
     * @param  array{id?: int, user_id?: int, phone?: string, email?: string}  $order
     */
    public static function identity(array $order): string
    {
        $phone = self::phoneDigits($order['phone'] ?? null);
        if ($phone !== '') {
            return 'p:'.$phone;
        }

        $userId = (int) ($order['user_id'] ?? 0);
        if ($userId > 0) {
            return 'u:'.$userId;
        }

        $email = self::lower(trim((string) ($order['email'] ?? '')));
        if ($email !== '') {
            return 'e:'.$email;
        }

        return 'g:'.(int) ($order['id'] ?? 0);
    }

    /**
     * @return array<string, mixed>
     */
    private static function blank(string $key): array
    {
        return [
            'key' => $key,
            'name' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'order_count' => 0,
            'active_count' => 0,
            'spend' => 0,
            'completed_count' => 0,
            'completed_spend' => 0,
            'cancelled_count' => 0,
            'last_at' => '',
            'first_at' => '',
            'has_account' => false,
            'account_since' => '',
            'orders' => [],
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $groups
     * @param  array{id: int, name: string, phone: string, email: string, address: string, created_at: string}  $account
     */
    private static function attachAccount(array &$groups, array $account): void
    {
        $userId = (int) ($account['id'] ?? 0);
        if ($userId <= 0) {
            return;
        }

        $phone = self::phoneDigits($account['phone'] ?? null);
        $email = self::lower(trim((string) ($account['email'] ?? '')));
        $candidates = array_filter([
            $phone !== '' ? 'p:'.$phone : null,
            'u:'.$userId,
            $email !== '' ? 'e:'.$email : null,
        ]);

        $target = null;
        foreach ($candidates as $candidate) {
            if (isset($groups[$candidate])) {
                $target = $candidate;
                break;
            }
        }

        if ($target === null) {
            $target = $phone !== '' ? 'p:'.$phone : 'u:'.$userId;
            $groups[$target] = self::blank($target);
        }

        $group = &$groups[$target];
        $group['has_account'] = true;
        $group['name'] = self::firstFilled($group['name'], $account['name'] ?? '');
        $group['phone'] = self::firstFilled($group['phone'], $account['phone'] ?? '');
        $group['email'] = self::firstFilled($group['email'], $account['email'] ?? '');
        $group['address'] = self::firstFilled($group['address'], $account['address'] ?? '');
        $group['account_since'] = (string) ($account['created_at'] ?? '');
        unset($group);
    }

    private static function firstFilled(string $current, string $incoming): string
    {
        if (trim($current) !== '') {
            return $current;
        }

        return trim($incoming);
    }

    private static function lower(string $value): string
    {
        return mb_strtolower($value);
    }

    private static function formatDate(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)
                ->timezone('Asia/Ho_Chi_Minh')
                ->format('d/m/Y H:i');
        } catch (\Throwable) {
            return $value;
        }
    }

    private static function statusText(string $status): string
    {
        $enum = TransactionStatusEnum::tryFrom($status);

        return $enum ? $enum->description() : ($status !== '' ? $status : '—');
    }
}
