<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function customer(string $name = 'Rani'): User
    {
        return User::factory()->create(['division' => 'customer', 'name' => $name]);
    }

    private function orderFor(User $user, string $status): Order
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Desain '.uniqid(), 'slug' => 'd'.uniqid(), 'status' => 'published',
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

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_dashboard_greets_user(): void
    {
        $this->actingAs($this->customer('Rani'))->get('/dashboard')
            ->assertOk()
            ->assertSee('Rani');
    }

    public function test_stats_count_only_own_orders(): void
    {
        $me = $this->customer();
        $this->orderFor($me, Order::STATUS_PENDING);
        $this->orderFor($me, Order::STATUS_AWAITING);
        $this->orderFor($me, Order::STATUS_PAID);
        $this->orderFor($this->customer('Orang Lain'), Order::STATUS_PAID);

        $res = $this->actingAs($me)->get('/dashboard');

        $res->assertOk();
        $res->assertViewHas('totalOrders', 3);
        $res->assertViewHas('pendingCount', 1);
        $res->assertViewHas('paidCount', 1);
    }

    public function test_recent_orders_only_own_and_capped_at_four(): void
    {
        $me = $this->customer();
        foreach (range(1, 6) as $i) {
            $this->orderFor($me, Order::STATUS_PENDING);
        }
        $theirs = $this->orderFor($this->customer('Lain'), Order::STATUS_PENDING);

        $res = $this->actingAs($me)->get('/dashboard');

        $res->assertOk();
        $res->assertViewHas('recentOrders', fn ($orders) => $orders->count() === 4
            && $orders->every(fn ($o) => $o->user_id === $me->id));
        $res->assertDontSee($theirs->order_number);
    }

    public function test_empty_state_points_to_catalog(): void
    {
        $res = $this->actingAs($this->customer())->get('/dashboard');

        $res->assertOk();
        $res->assertViewHas('totalOrders', 0);
        $res->assertSee(route('catalog.index'), false);
    }

    public function test_dashboard_does_not_use_tailwind_cdn(): void
    {
        // Dashboard adalah pilot fondasi Vite; CDN tak boleh menyelinap masuk.
        $this->actingAs($this->customer())->get('/dashboard')
            ->assertDontSee('cdn.tailwindcss.com');
    }

    public function test_active_nav_item_is_marked_for_screen_readers(): void
    {
        // Kelas is-active hanya visual; aria-current yang menyampaikannya ke
        // pembaca layar. Layout ini jadi cetakan admin, jadi dikunci di sini.
        $this->actingAs($this->customer())->get('/dashboard')
            ->assertOk()
            ->assertSee('aria-current="page"', false);
    }

    public function test_known_statuses_get_their_own_pill_variant(): void
    {
        $me = $this->customer();
        $this->orderFor($me, Order::STATUS_PENDING);
        $this->orderFor($me, Order::STATUS_PAID);

        $res = $this->actingAs($me)->get('/dashboard');

        $res->assertSee('dash-pill--pending', false);
        $res->assertSee('dash-pill--paid', false);
        // Status yang dikenal tak boleh jatuh ke varian "belum diklasifikasi".
        $res->assertDontSee('dash-pill--unknown', false);
    }

    public function test_unmapped_status_does_not_masquerade_as_cancelled(): void
    {
        $me = $this->customer();
        $order = $this->orderFor($me, Order::STATUS_PENDING);
        // Status di luar keempat yang dipetakan (mis. status baru di masa depan).
        $order->forceFill(['status' => 'refunded'])->save();

        $res = $this->actingAs($me)->get('/dashboard');

        $res->assertSee('dash-pill--unknown', false);
        $res->assertDontSee('dash-pill--cancelled', false);
    }
}
