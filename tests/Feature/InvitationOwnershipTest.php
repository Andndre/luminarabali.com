<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_customer_reflects_division(): void
    {
        $this->assertTrue(User::factory()->create(['division' => 'customer'])->isCustomer());
        $this->assertFalse(User::factory()->create(['division' => 'super_admin'])->isCustomer());
    }

    public function test_owner_id_is_fillable_and_relation_resolves(): void
    {
        $mitra = User::factory()->create(['division' => 'designer']);
        $customer = User::factory()->create(['division' => 'customer']);

        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'own-1',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $mitra->id, 'owner_id' => $customer->id,
        ]);

        $this->assertTrue($page->owner->is($customer));
        $this->assertTrue($page->creator->is($mitra));
    }

    public function test_owner_id_is_nullable_for_legacy_pages(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'own-2',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->assertNull($page->owner_id);
        $this->assertNull($page->owner);
    }
}
