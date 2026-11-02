<?php

namespace Tests\Unit;

use App\Support\AdminCustomers;
use PHPUnit\Framework\TestCase;

class AdminCustomersTest extends TestCase
{
    public function test_same_phone_is_one_customer_and_cancelled_orders_are_left_out_of_spend(): void
    {
        $rows = AdminCustomers::summarize([
            $this->order(2, '0901 234 567', 'COMPLETED', 200000, '2026-03-02 10:00:00', 'Lan'),
            $this->order(1, '+84 901-234-567', 'CANCELLED', 50000, '2026-03-01 10:00:00', 'Lan Nguyen'),
            $this->order(3, '0901234567', 'SHIPPING', 80000, '2026-03-03 10:00:00', ''),
        ]);

        $this->assertCount(1, $rows);
        $this->assertSame(3, $rows[0]['order_count']);
        $this->assertSame(280000, $rows[0]['spend']);
        $this->assertSame(1, $rows[0]['cancelled_count']);
        $this->assertSame(1, $rows[0]['completed_count']);
        $this->assertSame('Lan', $rows[0]['name']);
        $this->assertSame('2026-03-03 10:00:00', $rows[0]['last_at']);
    }

    public function test_account_without_orders_is_listed_and_phone_account_merges_into_orders(): void
    {
        $rows = AdminCustomers::summarize(
            [$this->order(9, '0909000111', 'COMPLETED', 150000, '2026-04-01 08:00:00', 'Be Na')],
            [[
                'id' => 15,
                'name' => 'Bé Na',
                'phone' => '0909000111',
                'email' => 'na@example.com',
                'address' => 'Quận 1',
                'created_at' => '2026-01-01 00:00:00',
            ], [
                'id' => 16,
                'name' => 'Khách mới',
                'phone' => '',
                'email' => 'moi@example.com',
                'address' => '',
                'created_at' => '2026-02-01 00:00:00',
            ]]
        );

        $this->assertCount(2, $rows);
        $buyer = AdminCustomers::present($rows[0], true);
        $this->assertSame('Be Na', $buyer['HO_TEN']);
        $this->assertSame('na@example.com', $buyer['EMAIL']);
        $this->assertTrue($buyer['CO_TAI_KHOAN']);
        $this->assertSame(150000, $buyer['TONG_CHI_TIEU']);
        $this->assertCount(1, $buyer['DON_HANG']);

        $fresh = AdminCustomers::present($rows[1]);
        $this->assertSame('Khách mới', $fresh['HO_TEN']);
        $this->assertSame(0, $fresh['TONG_CHI_TIEU']);
        $this->assertSame(0, $fresh['SO_DON']);
    }

    public function test_keyword_matches_name_and_phone(): void
    {
        $rows = AdminCustomers::summarize([
            $this->order(1, '0901111222', 'COMPLETED', 10000, '2026-01-01 00:00:00', 'An'),
            $this->order(2, '0903333444', 'COMPLETED', 20000, '2026-01-02 00:00:00', 'Binh'),
        ]);

        $this->assertCount(1, AdminCustomers::filter($rows, 'binh'));
        $this->assertCount(1, AdminCustomers::filter($rows, '0901 111 222'));
    }

    /**
     * @return array{id: int, user_id: int, name: string, phone: string, email: string, address: string, total: int, quantity: int, status: string, code: string, created_at: string}
     */
    private function order(int $id, string $phone, string $status, int $total, string $createdAt, string $name): array
    {
        return [
            'id' => $id,
            'user_id' => 0,
            'name' => $name,
            'phone' => $phone,
            'email' => '',
            'address' => '',
            'total' => $total,
            'quantity' => 1,
            'status' => $status,
            'code' => '#'.$id,
            'created_at' => $createdAt,
        ];
    }
}
