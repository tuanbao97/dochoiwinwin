<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Service\SapoService;
use App\Support\SapoOrderPuller;
use App\Support\SapoOrderStatusPusher;
use RuntimeException;
use Tests\TestCase;

class SapoOrderStatusPushTest extends TestCase
{
    public function test_confirmed_order_records_a_sale_on_sapo(): void
    {
        $calls = [];
        $sapo = $this->sapo($this->openOrder(), $calls);
        $transaction = $this->transaction('CONFIRMED');

        $this->assertTrue((new SapoOrderStatusPusher($sapo, new SapoOrderPuller($sapo)))->push($transaction));
        $this->assertSame('put', $calls[0][0]);
        $this->assertSame('/admin/orders/55.json', $calls[0][1]);
        $this->assertSame(
            [['name' => 'local_transaction_id', 'value' => '14']],
            $calls[0][2]['order']['note_attributes']
        );
        $this->assertSame('post', $calls[1][0]);
        $this->assertSame('/admin/orders/55/transactions.json', $calls[1][1]);
        $this->assertSame('sale', $calls[1][2]['transaction']['kind']);
        $this->assertSame('success', $calls[1][2]['transaction']['status']);
        $this->assertSame(159000.0, $calls[1][2]['transaction']['amount']);
        $this->assertSame('VND', $calls[1][2]['transaction']['currency']);
    }

    public function test_shipping_marks_the_order_then_creates_a_fulfillment(): void
    {
        $calls = [];
        $sapo = $this->sapo($this->openOrder(), $calls);
        $transaction = $this->transaction('SHIPPING');

        (new SapoOrderStatusPusher($sapo, new SapoOrderPuller($sapo)))->push($transaction);

        $this->assertSame('/admin/orders/55.json', $calls[0][1]);
        $this->assertSame('SHIPPING', $calls[0][2]['order']['note_attributes'][1]['value']);
        $this->assertSame('local_transaction_id', $calls[0][2]['order']['note_attributes'][0]['name']);
        $this->assertSame('/admin/orders/55/fulfillments.json', $calls[1][1]);
        $this->assertFalse($calls[1][2]['fulfillment']['send_notification_email']);
        $this->assertSame([['id' => 9, 'quantity' => 1]], $calls[1][2]['fulfillment']['line_items']);
    }

    public function test_completed_order_is_closed_on_sapo(): void
    {
        $calls = [];
        $sapo = $this->sapo($this->openOrder(), $calls);

        (new SapoOrderStatusPusher($sapo, new SapoOrderPuller($sapo)))->push($this->transaction('COMPLETED'));

        $this->assertSame('/admin/orders/55/close.json', $calls[0][1]);
        $this->assertSame('post', $calls[0][0]);
    }

    public function test_cancelled_order_is_cancelled_without_email_and_restocks(): void
    {
        $calls = [];
        $sapo = $this->sapo($this->openOrder(), $calls);

        (new SapoOrderStatusPusher($sapo, new SapoOrderPuller($sapo)))->push($this->transaction('CANCELLED'));

        $this->assertSame([[
            'post',
            '/admin/orders/55/cancel.json',
            ['reason' => 'other', 'email' => false, 'restock' => true],
        ]], $calls);
    }

    public function test_matching_sapo_status_does_not_call_again(): void
    {
        $calls = [];
        $order = $this->openOrder();
        $order['financial_status'] = 'paid';
        $sapo = $this->sapo($order, $calls);

        $this->assertTrue(
            (new SapoOrderStatusPusher($sapo, new SapoOrderPuller($sapo)))->push($this->transaction('CONFIRMED'))
        );
        $this->assertSame([], $calls);
    }

    public function test_paid_order_cannot_return_to_pending(): void
    {
        $order = $this->openOrder();
        $order['financial_status'] = 'paid';
        $calls = [];
        $sapo = $this->sapo($order, $calls);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Sapo đã xác nhận đơn');
        (new SapoOrderStatusPusher($sapo, new SapoOrderPuller($sapo)))->push($this->transaction('PENDING'));
    }

    public function test_disabled_sapo_leaves_the_local_status_alone(): void
    {
        $sapo = $this->createMock(SapoService::class);
        $sapo->method('isEnabled')->willReturn(false);
        $sapo->expects($this->never())->method('get');

        $this->assertFalse(
            (new SapoOrderStatusPusher($sapo, new SapoOrderPuller($sapo)))->push($this->transaction('CONFIRMED'))
        );
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: array<string, mixed>}>  $calls
     */
    private function sapo(array $order, ?array &$calls = null): SapoService
    {
        $calls = [];
        $sapo = $this->createMock(SapoService::class);
        $sapo->method('isEnabled')->willReturn(true);
        $sapo->method('get')->willReturn(['order' => $order]);
        $sapo->method('post')->willReturnCallback(function (string $path, array $payload) use (&$calls): array {
            $calls[] = ['post', $path, $payload];

            return [];
        });
        $sapo->method('put')->willReturnCallback(function (string $path, array $payload) use (&$calls): array {
            $calls[] = ['put', $path, $payload];

            return [];
        });

        return $sapo;
    }

    /**
     * @return array<string, mixed>
     */
    private function openOrder(): array
    {
        return [
            'id' => 55,
            'status' => 'open',
            'financial_status' => 'pending',
            'fulfillment_status' => null,
            'total_price' => '159000.00',
            'currency' => 'VND',
            'note_attributes' => [
                ['name' => 'local_transaction_id', 'value' => '14'],
            ],
            'line_items' => [
                ['id' => 9, 'quantity' => 1, 'fulfillable_quantity' => 1],
            ],
            'fulfillments' => [],
        ];
    }

    private function transaction(string $status): Transaction
    {
        $transaction = new Transaction;
        $transaction->SAPO_ORDER_ID = 55;
        $transaction->TRANSACTION_STATUS = $status;

        return $transaction;
    }
}
