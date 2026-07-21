<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCustomizerScreenTest extends TestCase
{
    use RefreshDatabase;

    private function pageFor(User $owner): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'customer-customizer-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'owner_id' => $owner->id, 'created_by' => $admin->id,
        ]);
    }

    public function test_owner_can_open_customer_customizer_screen(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $response = $this->actingAs($owner)->get("/undangan-saya/{$page->id}/customizer");

        $response->assertOk();
        $response->assertSee('customizerApp', false);
        $response->assertSee("/undangan-saya/{$page->id}/customizer/preview", false);
    }

    public function test_non_owner_customer_forbidden_from_customer_customizer_screen(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $stranger = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $this->actingAs($stranger)->get("/undangan-saya/{$page->id}/customizer")->assertForbidden();
    }

    public function test_admin_customizer_screen_still_uses_admin_shell(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $response = $this->actingAs($admin)->get("/admin/invitations/{$page->id}/customizer");

        $response->assertOk();
        $response->assertSee("/admin/invitations/{$page->id}/preview", false);
    }
}
