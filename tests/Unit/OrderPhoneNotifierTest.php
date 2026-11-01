<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Support\OrderPhoneNotifier;
use Tests\TestCase;

class OrderPhoneNotifierTest extends TestCase
{
    public function test_new_order_payload_names_the_buyer_quantity_and_total(): void
    {
        $order = new Transaction;
        $order->ID = 14;
        $order->SAPO_ORDER_NAME = '#5121';
        $order->USER_BUY_FULLNAME = 'Lan';
        $order->TOTAL_QUANTITY = 4;
        $order->TOTAL_PRICE = 486000;

        $payload = OrderPhoneNotifier::payloadFor($order);

        $this->assertSame('Đơn mới #5121', $payload['title']);
        $this->assertSame('Lan · 4 sản phẩm · 486.000 ₫', $payload['body']);
        $this->assertStringEndsWith('/admin/don-hang/chi-tiet/dh-14', $payload['url']);
        $this->assertSame('ww-order-14', $payload['tag']);
        $this->assertStringContainsString('favicon-win-win.png', $payload['icon']);
    }
}
