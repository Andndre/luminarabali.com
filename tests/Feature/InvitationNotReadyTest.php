<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationNotReadyTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_without_sections_shows_not_ready(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Empty', 'slug' => 'empty-t', 'status' => 'published', 'created_by' => $admin->id,
        ]);
        $page = InvitationPage::create([
            'template_id' => $template->id, 'title' => 'A & B', 'slug' => 'not-ready-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Undangan belum siap');
    }

    public function test_page_without_template_at_all_shows_not_ready(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'no-template-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Undangan belum siap');
    }
}
