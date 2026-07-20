<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPagePolicyTest extends TestCase
{
    use RefreshDatabase;

    private function page(int $createdBy, ?int $ownerId): InvitationPage
    {
        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'pol-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $createdBy, 'owner_id' => $ownerId,
        ]);
    }

    public function test_owner_customer_can_view_and_update(): void
    {
        $customer = User::factory()->create(['division' => 'customer']);
        $page = $this->page($customer->id, $customer->id);

        $this->assertTrue($customer->can('view', $page));
        $this->assertTrue($customer->can('update', $page));
    }

    public function test_non_owner_customer_denied(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $intruder = User::factory()->create(['division' => 'customer']);
        $page = $this->page($owner->id, $owner->id);

        $this->assertFalse($intruder->can('view', $page));
        $this->assertFalse($intruder->can('update', $page));
    }

    public function test_designer_creator_can_update(): void
    {
        $mitra = User::factory()->create(['division' => 'designer']);
        $customer = User::factory()->create(['division' => 'customer']);
        $page = $this->page($mitra->id, $customer->id);

        $this->assertTrue($mitra->can('update', $page));
    }

    public function test_designer_non_creator_denied(): void
    {
        $mitra = User::factory()->create(['division' => 'designer']);
        $otherMitra = User::factory()->create(['division' => 'designer']);
        $customer = User::factory()->create(['division' => 'customer']);
        $page = $this->page($mitra->id, $customer->id);

        $this->assertFalse($otherMitra->can('update', $page));
    }

    public function test_super_admin_bypasses(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $customer = User::factory()->create(['division' => 'customer']);
        $page = $this->page($customer->id, $customer->id);

        $this->assertTrue($admin->can('view', $page));
        $this->assertTrue($admin->can('update', $page));
    }
}
