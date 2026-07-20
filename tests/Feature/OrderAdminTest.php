<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAdminTest extends TestCase
{
    use RefreshDatabase;

    private function order(string $status): Order
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $customer = User::factory()->create(['division' => 'customer']);
        $t = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't'.uniqid(), 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);

        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $customer->id, 'invitation_template_id' => $t->id,
            'price' => 500000, 'status' => $status,
        ]);
    }

    private function staff(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    public function test_staff_confirms_awaiting_order(): void
    {
        $order = $this->order(Order::STATUS_AWAITING);
        $staff = $this->staff();

        $this->actingAs($staff)->post(route('admin.orders.confirm', $order))->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_PAID, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame($staff->id, $order->confirmed_by);
    }

    public function test_staff_confirms_pending_order(): void
    {
        $order = $this->order(Order::STATUS_PENDING);
        $this->actingAs($this->staff())->post(route('admin.orders.confirm', $order))->assertRedirect();
        $this->assertSame(Order::STATUS_PAID, $order->refresh()->status);
    }

    public function test_cannot_confirm_paid_or_cancelled(): void
    {
        foreach ([Order::STATUS_PAID, Order::STATUS_CANCELLED] as $status) {
            $order = $this->order($status);
            $this->actingAs($this->staff())->post(route('admin.orders.confirm', $order))->assertForbidden();
        }
    }

    public function test_staff_cancels_pending(): void
    {
        $order = $this->order(Order::STATUS_PENDING);
        $this->actingAs($this->staff())->post(route('admin.orders.cancel', $order), ['notes' => 'Duplikat'])->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_CANCELLED, $order->status);
        $this->assertSame('Duplikat', $order->notes);
    }

    public function test_cannot_cancel_paid(): void
    {
        $order = $this->order(Order::STATUS_PAID);
        $this->actingAs($this->staff())->post(route('admin.orders.cancel', $order))->assertForbidden();
    }

    public function test_customer_blocked_from_admin_orders(): void
    {
        $customer = User::factory()->create(['division' => 'customer']);
        // Middleware staff mengalihkan customer ke dashboard, bukan 403.
        $this->actingAs($customer)->get(route('admin.orders.index'))->assertRedirect(route('dashboard'));
    }

    public function test_admin_index_lists_orders(): void
    {
        $order = $this->order(Order::STATUS_AWAITING);
        $this->actingAs($this->staff())->get(route('admin.orders.index'))
            ->assertOk()->assertSee($order->order_number);
    }
}
