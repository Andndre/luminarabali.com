<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationShellTest extends TestCase
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

    public function test_public_page_renders_new_shell(): void
    {
        $page = $this->makePublishedPage();

        $res = $this->get('/invitation/'.$page->slug);
        $res->assertOk();
        $html = $res->getContent();
        $this->assertStringContainsString('invite-shell', $html);
        $this->assertStringContainsString('invite-hero', $html);
        $this->assertStringContainsString('invite-card', $html);
        $this->assertStringContainsString('invite-content', $html);
        $this->assertStringContainsString('invite-preloader', $html);
    }

    public function test_cover_image_resolves_storage_path(): void
    {
        $renderer = new \App\Services\InvitationRenderer();
        // Cover kini memakai bg_image (field media bersama), bukan background_image.
        $sections = collect([new \App\Models\InvitationSection([
            'section_type' => 'cover', 'props' => ['bg_image' => 'invitations/x.webp'],
        ])]);
        $this->assertSame('/storage/invitations/x.webp', $renderer->coverImage($sections));
        $this->assertNull($renderer->coverImage(collect()));

        // Tanpa foto tunggal, still-nya jatuh ke slide pertama slideshow.
        $slideshow = collect([new \App\Models\InvitationSection([
            'section_type' => 'cover',
            'props' => ['bg_media_type' => 'slideshow', 'bg_images' => [['url' => '/storage/a.webp'], ['url' => '/storage/b.webp']]],
        ])]);
        $this->assertSame('/storage/a.webp', $renderer->coverImage($slideshow));
    }

    public function test_cover_renders_gate_and_sticky_screen(): void
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
            'page_id' => $page->id, 'section_type' => 'cover', 'order_index' => 0,
            'is_visible' => true, 'props' => [],
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'quote', 'order_index' => 1,
            'is_visible' => true, 'props' => ['content' => 'Halo'],
        ]);

        $html = $this->get('/invitation/'.$page->slug)->getContent();
        $this->assertStringContainsString('invite-gate', $html);
        $this->assertStringContainsString('invite-cover-sticky', $html);
        $this->assertStringNotContainsString('cover-active', $html);
    }

    public function test_cover_renders_slideshow_and_video_backgrounds(): void
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
            'page_id' => $page->id, 'section_type' => 'cover', 'order_index' => 0, 'is_visible' => true,
            'props' => [
                'bg_media_type' => 'slideshow',
                'bg_images' => [['url' => '/storage/a.webp'], ['url' => '/storage/b.webp'], ['url' => '/storage/c.webp']],
            ],
        ]);

        $html = $this->get('/invitation/'.$page->slug)->getContent();

        // Gate DAN layar sticky masing-masing punya lapisan slide-nya sendiri: 3 slide × 2.
        $this->assertSame(6, substr_count($html, 'sec-bg-img sec-bg-slide'));
        $this->assertStringContainsString('@keyframes sec-bgslide-3', $html);

        // Ganti ke video YouTube pada section yang sama.
        $page->sections()->where('section_type', 'cover')->update(['props' => json_encode([
            'bg_media_type' => 'video', 'bg_video' => 'https://youtu.be/dQw4w9WgXcQ',
        ])]);

        $html = $this->get('/invitation/'.$page->slug)->getContent();
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $html);
        $this->assertStringContainsString('i.ytimg.com/vi/dQw4w9WgXcQ', $html); // poster thumbnail
    }

    public function test_page_without_cover_renders_card_without_gate(): void
    {
        $page = $this->makePublishedPage(); // hanya section 'text', tanpa cover

        $res = $this->get('/invitation/'.$page->slug);
        $res->assertOk();
        $html = $res->getContent();
        // JS init layout memuat selector '.invite-gate' — assert bentuk markup-nya saja.
        $this->assertStringNotContainsString('class="invite-gate', $html);
        $this->assertStringContainsString('invite-card', $html);
    }

    public function test_shell_x_data_attribute_survives_html_parsing(): void
    {
        // Regresi: kutip ganda di dalam x-data="..." memotong atribut saat browser parse
        // (string test biasa tidak menangkapnya karena HTML mentah terlihat "benar").
        $page = $this->makePublishedPage();
        $html = $this->get('/invitation/'.$page->slug)->getContent();

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $shell = $xpath->query('//*[contains(@class, "invite-shell")]')->item(0);

        $this->assertNotNull($shell, 'invite-shell tidak ditemukan setelah parsing DOM');
        $xData = $shell->getAttribute('x-data');
        $this->assertStringContainsString('openInvitation', $xData);
        // Sentinelnya harus sesuatu di ujung x-data: kalau atributnya terpotong oleh kutip
        // ganda mentah, bagian akhir inilah yang hilang lebih dulu.
        $this->assertStringContainsString('invitation-opened', $xData, 'atribut x-data terpotong — ada kutip ganda mentah di dalamnya?');
    }

    public function test_invite_content_inherits_theme_text_color_token(): void
    {
        $css = file_get_contents(resource_path('css/invitation.css'));
        $this->assertStringContainsString('--fg-body: var(--color-text, #2b2b2b);', $css);
    }

    public function test_studio_preview_skips_preloader(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Halo'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $res = $this->get("/admin/templates/{$template->id}/studio/preview");
        $res->assertOk();
        $html = $res->getContent();
        $this->assertStringContainsString('invite-shell', $html);
        $this->assertStringNotContainsString('invite-preloader', $html);
    }
}
