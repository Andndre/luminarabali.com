<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomizerPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_renders_draft_page_with_sections(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'preview-draft',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Draft preview content'], 'is_visible' => true,
        ]);

        $response = $this->actingAs($admin)->get("/admin/invitations/{$page->id}/preview");

        $response->assertOk();
        $response->assertSee('Draft preview content');
        $response->assertSee(':root{', false);
    }

    public function test_preview_requires_super_admin(): void
    {
        $user = User::factory()->create(['division' => 'photobooth']);
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'preview-403',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->actingAs($user)->get("/admin/invitations/{$page->id}/preview")->assertForbidden();
    }

    public function test_preview_of_empty_page_shows_not_ready(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'preview-empty',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get("/admin/invitations/{$page->id}/preview");

        $response->assertOk();
        $response->assertSee('Undangan belum siap');
    }
}
