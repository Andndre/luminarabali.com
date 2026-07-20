<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class InvitationCacheBustTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    public function test_saving_a_template_busts_cache_for_every_page_using_it(): void
    {
        $admin = $this->superAdmin();
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic', 'status' => 'published', 'created_by' => $admin->id,
        ]);
        $page = InvitationPage::create([
            'template_id' => $template->id, 'title' => 'A & B', 'slug' => 'a-and-b',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);

        Cache::put("invitation:{$page->slug}", 'stale-cached-value', 3600);
        $this->assertTrue(Cache::has("invitation:{$page->slug}"));

        $this->actingAs($admin)->postJson('/admin/api/templates/sections', [
            'template_id' => $template->id,
            'global_custom_css' => 'body { color: red; }',
        ])->assertOk();

        $this->assertFalse(Cache::has("invitation:{$page->slug}"));
    }

    public function test_updating_invitation_metadata_busts_cache_for_old_and_new_slug(): void
    {
        $admin = $this->superAdmin();
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'old-slug',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);

        Cache::put('invitation:old-slug', 'stale-old', 3600);

        $this->actingAs($admin)->put("/admin/invitations/{$page->id}", [
            'title' => 'A & B', 'slug' => 'new-slug',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth()->toDateString(),
            'published_status' => 'published',
        ])->assertRedirect();

        $this->assertFalse(Cache::has('invitation:old-slug'));
        $this->assertFalse(Cache::has('invitation:new-slug'));
    }

    public function test_saving_the_section_tree_busts_cache_for_that_page(): void
    {
        $admin = $this->superAdmin();
        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'section-tree-page',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);

        Cache::put('invitation:section-tree-page', 'stale-cached-value', 3600);

        $this->actingAs($admin)->post('/admin/api/sections', [
            'page_id' => $page->id,
            'global_custom_css' => '',
            'sections' => [],
        ])->assertRedirect();

        $this->assertFalse(Cache::has('invitation:section-tree-page'));
    }
}
