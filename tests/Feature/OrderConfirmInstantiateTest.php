<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderConfirmInstantiateTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    private function orderWithSections(string $status = Order::STATUS_AWAITING): array
    {
        $admin = $this->admin();
        $customer = User::factory()->create(['division' => 'customer', 'name' => 'Sari Wulandari']);
        $template = InvitationTemplate::create([
            'name' => 'Mawar', 'slug' => 'mawar-'.uniqid(), 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'page_id' => null, 'parent_id' => null,
            'section_type' => 'cover', 'order_index' => 0, 'props' => [], 'is_visible' => true,
        ]);
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $customer->id, 'invitation_template_id' => $template->id,
            'price' => 500000, 'status' => $status,
        ]);

        return [$admin, $customer, $template, $order];
    }

    public function test_confirm_creates_invitation_page_owned_by_customer(): void
    {
        [$admin, $customer, $template, $order] = $this->orderWithSections();

        $this->actingAs($admin)->post(route('admin.orders.confirm', $order))->assertRedirect();

        $order->refresh();
        $this->assertNotNull($order->invitation_page_id);

        $page = $order->invitationPage;
        $this->assertSame($customer->id, $page->owner_id);
        $this->assertSame($admin->id, $page->created_by);
        $this->assertSame('draft', $page->published_status);
        $this->assertSame($template->id, $page->template_id);
        $this->assertSame(Str::slug($order->order_number), $page->slug);
    }

    public function test_confirm_copies_template_sections_to_page(): void
    {
        [, , , $order] = $this->orderWithSections();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.orders.confirm', $order));

        $page = $order->fresh()->invitationPage;
        $this->assertSame(1, $page->sections()->count());
    }

    public function test_confirm_twice_via_http_is_rejected_and_creates_only_one_page(): void
    {
        [, , , $order] = $this->orderWithSections();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.orders.confirm', $order));
        $this->actingAs($admin)->post(route('admin.orders.confirm', $order))->assertForbidden();

        $this->assertSame(1, InvitationPage::count());
    }

    public function test_order_already_linked_does_not_get_second_page_on_reconfirm_attempt(): void
    {
        // Lapis kedua idempoten: guard DATA, bukan cuma guard HTTP status.
        // Simulasikan order yang sudah punya invitation_page_id tapi (secara
        // hipotetis) confirm() terpanggil lagi — mis. dari pemanggil lain
        // di masa depan yang lupa cek status HTTP.
        [$admin, , , $order] = $this->orderWithSections();
        $this->actingAs($admin)->post(route('admin.orders.confirm', $order));
        $order->refresh();
        $firstPageId = $order->invitation_page_id;

        // Order dipaksa balik ke awaiting supaya guard HTTP status tak menghalangi,
        // isolasi murni pada guard data invitation_page_id.
        $order->update(['status' => Order::STATUS_AWAITING]);
        $this->actingAs($admin)->post(route('admin.orders.confirm', $order));

        $order->refresh();
        $this->assertSame($firstPageId, $order->invitation_page_id);
        $this->assertSame(1, InvitationPage::count());
    }

    public function test_confirm_without_template_still_marks_paid_without_error(): void
    {
        [$admin, $customer, $template, $order] = $this->orderWithSections();
        $template->delete();

        $this->actingAs($admin)->post(route('admin.orders.confirm', $order))->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_PAID, $order->status);
        $this->assertNull($order->invitation_page_id);
    }

    public function test_confirm_rejected_from_final_status_creates_no_page(): void
    {
        [$admin, , , $order] = $this->orderWithSections(Order::STATUS_CANCELLED);

        $this->actingAs($admin)->post(route('admin.orders.confirm', $order))->assertForbidden();

        $this->assertSame(0, InvitationPage::count());
    }
}
