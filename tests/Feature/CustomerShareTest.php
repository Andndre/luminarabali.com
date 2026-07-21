<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerShareTest extends TestCase
{
    use RefreshDatabase;

    private function pageFor(User $owner, string $status = 'published'): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'share-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => $status, 'owner_id' => $owner->id, 'created_by' => $admin->id,
        ]);
    }

    public function test_owner_sees_public_link_when_published(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner, 'published');

        $this->actingAs($owner)->get("/undangan-saya/{$page->id}/bagikan")
            ->assertOk()
            ->assertSee("/invitation/{$page->slug}", false);
    }

    public function test_draft_shows_not_published_message_instead_of_link(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner, 'draft');

        $this->actingAs($owner)->get("/undangan-saya/{$page->id}/bagikan")
            ->assertOk()
            ->assertDontSee("/invitation/{$page->slug}", false);
    }

    public function test_non_owner_forbidden(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $stranger = User::factory()->create(['division' => 'customer']);
        $page = $this->pageFor($owner);

        $this->actingAs($stranger)->get("/undangan-saya/{$page->id}/bagikan")->assertForbidden();
    }
}
