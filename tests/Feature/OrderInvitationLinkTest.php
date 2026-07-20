<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderInvitationLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_link_to_invitation_page(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $customer = User::factory()->create(['division' => 'customer']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't1', 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);
        $page = InvitationPage::create([
            'template_id' => $template->id, 'title' => 'Undangan Test',
            'slug' => 'ord-test', 'groom_name' => 'A', 'bride_name' => 'B',
            'event_date' => now()->addMonths(6), 'published_status' => 'draft',
            'owner_id' => $customer->id, 'created_by' => $admin->id,
        ]);
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $customer->id, 'invitation_template_id' => $template->id,
            'price' => 500000, 'status' => Order::STATUS_PAID,
            'invitation_page_id' => $page->id,
        ]);

        $this->assertSame($page->id, $order->fresh()->invitationPage->id);
    }

    public function test_deleting_invitation_page_nulls_order_link(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $customer = User::factory()->create(['division' => 'customer']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't2', 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);
        $page = InvitationPage::create([
            'template_id' => $template->id, 'title' => 'Undangan Test',
            'slug' => 'ord-test-2', 'groom_name' => 'A', 'bride_name' => 'B',
            'event_date' => now()->addMonths(6), 'published_status' => 'draft',
            'owner_id' => $customer->id, 'created_by' => $admin->id,
        ]);
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $customer->id, 'invitation_template_id' => $template->id,
            'price' => 500000, 'status' => Order::STATUS_PAID,
            'invitation_page_id' => $page->id,
        ]);

        $page->delete();

        $this->assertNull($order->fresh()->invitation_page_id);
    }
}
