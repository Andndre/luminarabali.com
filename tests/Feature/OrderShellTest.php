<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShellTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->create(['division' => 'customer']);
    }

    private function orderFor(User $user, string $status = Order::STATUS_PENDING): Order
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Mawar Senja', 'slug' => 'd'.uniqid(), 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);

        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $user->id,
            'invitation_template_id' => $template->id,
            'price' => 500000,
            'status' => $status,
        ]);
    }

    public function test_orders_index_uses_dashboard_shell_without_cdn(): void
    {
        $me = $this->customer();
        $order = $this->orderFor($me);

        $res = $this->actingAs($me)->get(route('orders.index'));

        $res->assertOk();
        $res->assertSee($order->order_number);
        // Shell dashboard: sidebar hadir, Tailwind CDN tidak.
        $res->assertSee('dash-sidebar', false);
        $res->assertDontSee('cdn.tailwindcss.com');
    }

    public function test_orders_show_uses_dashboard_shell_without_cdn(): void
    {
        $me = $this->customer();
        $order = $this->orderFor($me);
        BankAccount::create([
            'bank_name' => 'BCA', 'account_number' => '1234567890',
            'account_holder' => 'PT Luminara', 'is_active' => true,
        ]);

        $res = $this->actingAs($me)->get(route('orders.show', $order));

        $res->assertOk();
        $res->assertSee($order->order_number);
        $res->assertSee('Mawar Senja');
        $res->assertSee('BCA');            // rekening aktif tetap tampil
        $res->assertSee('1234567890');
        $res->assertSee('dash-sidebar', false);
        $res->assertDontSee('cdn.tailwindcss.com');
    }

    public function test_upload_form_hidden_once_paid(): void
    {
        $me = $this->customer();
        $paid = $this->orderFor($me, Order::STATUS_PAID);

        $this->actingAs($me)->get(route('orders.show', $paid))
            ->assertOk()
            ->assertDontSee('Kirim Bukti');
    }

    public function test_upload_form_visible_when_pending(): void
    {
        $me = $this->customer();
        $pending = $this->orderFor($me, Order::STATUS_PENDING);

        $this->actingAs($me)->get(route('orders.show', $pending))
            ->assertOk()
            ->assertSee('Kirim Bukti');
    }
}
