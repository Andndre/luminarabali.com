<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPublishStatusTest extends TestCase
{
    use RefreshDatabase;

    private function draftPage(User $admin): InvitationPage
    {
        return InvitationPage::create([
            'title' => 'A & B', 'slug' => 'status-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);
    }

    private function updatePayload(InvitationPage $page, array $overrides = []): array
    {
        return array_merge([
            'title' => $page->title,
            'slug' => $page->slug,
            'groom_name' => $page->groom_name,
            'bride_name' => $page->bride_name,
            'event_date' => $page->event_date->toDateString(),
        ], $overrides);
    }

    public function test_edit_form_shows_a_status_select_with_current_value_chosen(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = $this->draftPage($admin);

        $response = $this->actingAs($admin)->get("/admin/invitations/{$page->id}/edit");

        $response->assertOk();
        $response->assertSee('name="published_status"', false);
        $response->assertSee('<option value="draft" selected', false);
    }

    public function test_update_can_change_status_from_draft_to_published(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = $this->draftPage($admin);

        $this->actingAs($admin)
            ->put("/admin/invitations/{$page->id}", $this->updatePayload($page, ['published_status' => 'published']))
            ->assertRedirect();

        $this->assertSame('published', $page->refresh()->published_status);
    }

    public function test_update_rejects_an_invalid_status_value(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = $this->draftPage($admin);

        $this->actingAs($admin)
            ->put("/admin/invitations/{$page->id}", $this->updatePayload($page, ['published_status' => 'not-a-real-status']))
            ->assertSessionHasErrors('published_status');

        $this->assertSame('draft', $page->refresh()->published_status);
    }
}
