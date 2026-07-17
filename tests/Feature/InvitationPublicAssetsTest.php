<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPublicAssetsTest extends TestCase
{
    use RefreshDatabase;

    private function makePublishedPage(): InvitationPage
    {
        $user = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'published', 'created_by' => $user->id,
        ]);
        $page = InvitationPage::create([
            'title' => 'P', 'slug' => 'p-'.uniqid(), 'published_status' => 'published',
            'template_id' => $template->id, 'created_by' => $user->id,
            'groom_name' => 'Romeo', 'bride_name' => 'Juliet', 'event_date' => now()->addMonth(),
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 1,
            'is_visible' => true, 'props' => ['content' => 'Halo'],
        ]);
        return $page;
    }

    public function test_public_page_uses_vite_bundle_not_dev_cdns(): void
    {
        $res = $this->get('/invitation/'.$this->makePublishedPage()->slug);
        $res->assertOk();
        $html = $res->getContent();

        // Verify dev CDNs are removed
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $html);
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html);
        $this->assertStringNotContainsString('sweetalert2', $html);

        // Verify Vite bundle is used (check for hashed asset references).
        // Rollup content hashes are base64url, so they may legitimately contain
        // "-" and "_" alongside alphanumerics — the charset class must allow both.
        $this->assertStringContainsString('/build/assets/invitation-', $html);
        $this->assertTrue((bool) preg_match('/\/build\/assets\/invitation-[\w-]+\.css/', $html), 'Invitation CSS asset should be present');
        $this->assertTrue((bool) preg_match('/\/build\/assets\/invitation-[\w-]+\.js/', $html), 'Invitation JS asset should be present');

        // Verify Google Fonts stays
        $this->assertStringContainsString('fonts.googleapis.com', $html);
    }

    public function test_masonry_gallery_renders_inline_columns_for_arbitrary_counts(): void
    {
        $page = $this->makePublishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'gallery', 'order_index' => 2,
            'is_visible' => true, 'props' => ['layout' => 'masonry', 'columns' => 4, 'images' => [], 'gap' => 16],
        ]);

        $res = $this->get('/invitation/'.$page->slug);
        $res->assertOk();
        $html = $res->getContent();

        $this->assertStringContainsString('columns: 4', $html);
    }
}
