<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;

class ShipAddressTest extends TestCase
{
    public function test_split_composed_address_into_street_ward_and_province(): void
    {
        $parts = wwSplitShipAddress('12 Lê Duẩn, Phường Hải Châu, Thành phố Đà Nẵng');

        $this->assertSame('12 Lê Duẩn', $parts['street']);
        $this->assertSame('Phường Hải Châu', $parts['ward']);
        $this->assertSame('Thành phố Đà Nẵng', $parts['province']);
    }

    public function test_split_keeps_commas_inside_the_street(): void
    {
        $parts = wwSplitShipAddress('12, hẻm 3, Phường Hội An, Thành phố Đà Nẵng');

        $this->assertSame('12, hẻm 3', $parts['street']);
        $this->assertSame('Phường Hội An', $parts['ward']);
        $this->assertSame('Thành phố Đà Nẵng', $parts['province']);
    }

    public function test_split_accepts_short_province_name_and_ward_inside_the_street_line(): void
    {
        $parts = wwSplitShipAddress('Số 5 Đường DT605 Xã Hòa Tiến, Đà Nẵng');

        $this->assertSame('Số 5 Đường DT605', $parts['street']);
        $this->assertSame('Xã Hòa Tiến', $parts['ward']);
        $this->assertSame('Thành phố Đà Nẵng', $parts['province']);
    }

    public function test_store_pickup_text_is_not_reused_as_a_delivery_address(): void
    {
        $parts = wwSplitShipAddress("Nhận tại cửa hàng: Đồ Chơi Win Win, Đường DT605, xã Hòa Tiến, Đà Nẵng");

        $this->assertSame('', $parts['street']);
        $this->assertSame('', $parts['ward']);
        $this->assertSame('', $parts['province']);
    }

    public function test_session_choice_is_used_when_the_profile_has_no_newer_address(): void
    {
        session([
            'storefront.ship_address' => [
                'street' => '9 Trần Phú',
                'ward' => 'Phường Hải Châu',
                'province' => 'Thành phố Đà Nẵng',
                'saved_at' => time(),
            ],
        ]);

        $resolved = wwResolveCheckoutShipAddress('chỉ ghi số nhà', Carbon::now()->subDay());

        $this->assertSame('9 Trần Phú', $resolved['street']);
        $this->assertSame('Phường Hải Châu', $resolved['ward']);
    }

    public function test_newer_profile_address_wins_over_the_session(): void
    {
        session([
            'storefront.ship_address' => [
                'street' => '9 Trần Phú',
                'ward' => 'Phường Hải Châu',
                'province' => 'Thành phố Đà Nẵng',
                'saved_at' => time() - 3600,
            ],
        ]);

        $resolved = wwResolveCheckoutShipAddress(
            '15 Nguyễn Huệ, Phường Hội An, Thành phố Đà Nẵng',
            Carbon::now()
        );

        $this->assertSame('15 Nguyễn Huệ', $resolved['street']);
        $this->assertSame('Phường Hội An', $resolved['ward']);
        $this->assertSame('Thành phố Đà Nẵng', $resolved['province']);
    }
}
