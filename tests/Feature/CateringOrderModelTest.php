<?php

namespace Tests\Feature;

use App\Models\CateringOrder;
use App\Models\CateringOrderDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CateringOrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_unique_order_number_when_created(): void
    {
        $order1 = CateringOrder::factory()->create();
        $order2 = CateringOrder::factory()->create();

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{4}$/', $order1->order_number);
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{4}$/', $order2->order_number);
    }

    public function test_it_auto_generates_order_number_via_model_creating_event_if_omitted(): void
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();

        $order = CateringOrder::create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'order_date' => now()->toDateString(),
            'delivery_date' => now()->addDays(2)->toDateString(),
            'status' => 'pending',
            'total_amount' => 150000.00,
        ]);

        $this->assertNotEmpty($order->order_number);
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{4}$/', $order->order_number);
    }

    public function test_it_preserves_custom_order_number_if_provided(): void
    {
        $order = CateringOrder::factory()->create([
            'order_number' => 'ORD-20261015-9999',
        ]);

        $this->assertEquals('ORD-20261015-9999', $order->order_number);
    }

    public function test_it_has_relations_to_customer_and_creator(): void
    {
        $order = CateringOrder::factory()->create();

        $this->assertInstanceOf(Customer::class, $order->customer);
        $this->assertInstanceOf(User::class, $order->creator);
    }

    public function test_it_can_have_multiple_order_details(): void
    {
        $order = CateringOrder::factory()->create();
        CateringOrderDetail::factory()->count(3)->create(['catering_order_id' => $order->id]);

        $this->assertCount(3, $order->details);
        foreach ($order->details as $detail) {
            $this->assertEquals($order->id, $detail->catering_order_id);
        }
    }

    public function test_order_detail_belongs_to_order_and_product(): void
    {
        $detail = CateringOrderDetail::factory()->create();

        $this->assertInstanceOf(CateringOrder::class, $detail->cateringOrder);
        $this->assertInstanceOf(Product::class, $detail->product);
    }
}
