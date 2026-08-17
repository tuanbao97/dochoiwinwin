<?php

namespace Tests\Unit;

use App\Jobs\SyncSapoOrderJob;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Service\SapoService;
use App\Service\impl\SapoServiceImpl;
use App\Support\SapoOrderPuller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use ReflectionMethod;
use Tests\TestCase;

class SapoOrderSyncTest extends TestCase
{
    public function test_order_payload_maps_sapo_variant_customer_and_local_reference(): void
    {
        $transaction = new Transaction([
            'ID' => 123,
            'USER_BUY_EMAIL' => 'buyer@example.com',
            'USER_BUY_FULLNAME' => 'Nguyễn Văn An',
            'USER_BUY_PHONE' => '0909000111',
            'USER_BUY_ADDRESS' => '12 Nguyễn Trãi, TP.HCM',
            'USER_BUY_MESSAGE' => 'Giao giờ hành chính',
        ]);
        $transaction->ID = 123;

        $item = new OrderItem([
            'PRODUCT_ID' => 10,
            'SAPO_VARIANT_ID' => 84440001,
            'QUANTITY' => 2,
        ]);
        $transaction->setRelation('orderItems', collect([$item]));

        $method = new ReflectionMethod(SyncSapoOrderJob::class, 'payload');
        $method->setAccessible(true);
        $payload = $method->invoke(new SyncSapoOrderJob(123), $transaction);
        $order = $payload['order'];

        $this->assertSame('pending', $order['financial_status']);
        $this->assertSame('buyer@example.com', $order['email']);
        $this->assertSame('buyer@example.com', $order['customer']['email']);
        $this->assertSame('+84909000111', $order['phone']);
        $this->assertSame('+84909000111', $order['customer']['phone']);
        $this->assertSame(
            [['variant_id' => 84440001, 'quantity' => 2]],
            $order['line_items']
        );
        $this->assertSame('+84909000111', $order['shipping_address']['phone']);
        $this->assertSame(
            ['name' => 'local_transaction_id', 'value' => '123'],
            $order['note_attributes'][0]
        );
    }

    public function test_order_payload_omits_empty_email_but_keeps_phone_contact(): void
    {
        $transaction = new Transaction([
            'ID' => 124,
            'USER_BUY_EMAIL' => '',
            'USER_BUY_FULLNAME' => 'Khách',
            'USER_BUY_PHONE' => '0909000111',
            'USER_BUY_ADDRESS' => '12 Nguyễn Trãi, TP.HCM',
        ]);
        $transaction->ID = 124;
        $transaction->setRelation('orderItems', collect([
            new OrderItem(['PRODUCT_ID' => 10, 'SAPO_VARIANT_ID' => 84440001, 'QUANTITY' => 1]),
        ]));

        $method = new ReflectionMethod(SyncSapoOrderJob::class, 'payload');
        $method->setAccessible(true);
        $payload = $method->invoke(new SyncSapoOrderJob(124), $transaction);
        $order = $payload['order'];

        $this->assertArrayNotHasKey('email', $order);
        $this->assertArrayNotHasKey('email', $order['customer']);
        $this->assertSame('+84909000111', $order['phone']);
        $this->assertSame('+84909000111', $order['customer']['phone']);
    }

    public function test_sapo_order_window_is_sent_in_utc(): void
    {
        $sapo = $this->createMock(SapoService::class);
        $sapo->expects($this->once())
            ->method('get')
            ->willReturnCallback(function (string $path, array $query): array {
                $this->assertSame('/admin/orders.json', $path);
                $this->assertSame('2026-08-17T07:00:00+00:00', $query['modified_on_min']);
                $this->assertSame('2026-08-17T07:30:00+00:00', $query['modified_on_max']);
                $this->assertSame(1, $query['page']);
                $this->assertSame(250, $query['limit']);

                return ['orders' => []];
            });

        $method = new ReflectionMethod(SapoOrderPuller::class, 'fetchWindow');
        $method->setAccessible(true);
        $orders = $method->invoke(
            new SapoOrderPuller($sapo),
            Carbon::parse('2026-08-17T14:00:00+07:00'),
            Carbon::parse('2026-08-17T14:30:00+07:00')
        );

        $this->assertSame([], $orders);
    }

    /**
     * @dataProvider sapoOrderStatuses
     *
     * @param  array<string, mixed>  $order
     */
    public function test_sapo_order_status_maps_to_local_transaction_status(array $order, string $expected): void
    {
        $method = new ReflectionMethod(SapoOrderPuller::class, 'mapStatus');
        $method->setAccessible(true);

        $this->assertSame(
            $expected,
            $method->invoke(new SapoOrderPuller($this->createMock(SapoService::class)), $order)->value
        );
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string}>
     */
    public static function sapoOrderStatuses(): array
    {
        return [
            'new order' => [['status' => 'open', 'financial_status' => 'pending'], 'PENDING'],
            'paid order' => [['status' => 'open', 'financial_status' => 'paid'], 'CONFIRMED'],
            'confirmed order' => [['status' => 'open', 'confirmed_on' => '2026-08-17T06:49:49Z'], 'CONFIRMED'],
            'shipping order' => [['status' => 'open', 'fulfillment_status' => 'partial'], 'SHIPPING'],
            'fulfilled order' => [['status' => 'closed', 'fulfillment_status' => 'fulfilled'], 'COMPLETED'],
            'cancelled order' => [
                ['status' => 'cancelled', 'cancelled_on' => '2026-08-17T06:40:32Z', 'financial_status' => 'paid'],
                'CANCELLED',
            ],
        ];
    }

    public function test_sapo_service_posts_json_with_existing_credentials(): void
    {
        config()->set('services.sapo', [
            'enabled' => true,
            'store' => 'example.mysapo.net',
            'api_key' => 'key',
            'api_secret' => 'secret',
            'timeout' => 10,
            'access_token' => null,
            'product_type' => 'Đồ chơi',
        ]);

        Http::fake([
            'https://example.mysapo.net/admin/orders.json' => Http::response([
                'order' => ['id' => 999],
            ], 201),
        ]);

        $response = (new SapoServiceImpl())->post('/admin/orders.json', [
            'order' => ['line_items' => [['variant_id' => 1, 'quantity' => 1]]],
        ]);

        $this->assertSame(999, $response['order']['id']);
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->url() === 'https://example.mysapo.net/admin/orders.json'
            && $request['order']['line_items'][0]['variant_id'] === 1);
    }
}
