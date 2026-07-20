<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;

    private function template(?int $price, string $status = 'published'): InvitationTemplate
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        return InvitationTemplate::create([
            'name' => 'Desain', 'slug' => 'desain-'.uniqid(), 'status' => $status,
            'price' => $price, 'created_by' => $admin->id,
        ]);
    }

    private function customer(): User
    {
        return User::factory()->create(['division' => 'customer']);
    }

    public function test_customer_creates_order_with_snapshot_price(): void
    {
        $t = $this->template(750000);
        $customer = $this->customer();

        $res = $this->actingAs($customer)->post("/undangan/{$t->slug}/pesan");

        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertSame(750000, $order->price);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $res->assertRedirect(route('orders.show', $order));
    }

    public function test_price_snapshot_frozen_against_later_template_change(): void
    {
        $t = $this->template(750000);
        $customer = $this->customer();

        $this->actingAs($customer)->post("/undangan/{$t->slug}/pesan");
        $t->update(['price' => 999000]);

        $this->assertSame(750000, Order::where('user_id', $customer->id)->first()->price);
    }

    public function test_null_price_template_cannot_be_ordered(): void
    {
        $t = $this->template(null);
        $customer = $this->customer();

        $this->actingAs($customer)->post("/undangan/{$t->slug}/pesan")
            ->assertRedirect(route('catalog.show', $t->slug));

        $this->assertSame(0, Order::count());
    }

    public function test_unpublished_template_404(): void
    {
        $t = $this->template(500000, 'draft');

        $this->actingAs($this->customer())->post("/undangan/{$t->slug}/pesan")->assertNotFound();
    }

    public function test_guest_redirected_to_login(): void
    {
        $t = $this->template(500000);

        $this->post("/undangan/{$t->slug}/pesan")->assertRedirect(route('login'));
    }

    public function test_duplicate_pending_order_redirects_to_existing(): void
    {
        $t = $this->template(500000);
        $customer = $this->customer();

        $this->actingAs($customer)->post("/undangan/{$t->slug}/pesan");
        $first = Order::where('user_id', $customer->id)->first();

        $this->actingAs($customer)->post("/undangan/{$t->slug}/pesan")
            ->assertRedirect(route('orders.show', $first));

        $this->assertSame(1, Order::count());
    }
}
