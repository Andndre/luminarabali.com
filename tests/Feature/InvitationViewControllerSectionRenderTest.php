<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationViewControllerSectionRenderTest extends TestCase
{
    use RefreshDatabase;

    private function publishedPage(): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'published', 'created_by' => $admin->id,
        ]);

        return InvitationPage::create([
            'template_id' => $template->id, 'title' => 'A & B', 'slug' => 'a-and-b-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);
    }

    public function test_page_without_sections_shows_not_ready(): void
    {
        $page = $this->publishedPage();

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Undangan belum siap');
    }

    public function test_page_with_sections_renders_section_tree(): void
    {
        $page = $this->publishedPage();

        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Hello from section tree'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Hello from section tree');
    }

    public function test_nested_sections_render_inside_their_parent_container(): void
    {
        $page = $this->publishedPage();

        $container = InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'section_one_col', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'parent_id' => $container->id, 'section_type' => 'text',
            'order_index' => 0, 'props' => ['content' => 'Nested child text'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $html = $response->getContent();
        $containerPos = strpos($html, 'section-one-col');
        $childPos = strpos($html, 'Nested child text');
        $this->assertNotFalse($containerPos);
        $this->assertNotFalse($childPos);
        $this->assertGreaterThan($containerPos, $childPos, 'Nested child text must render inside the parent container.');
    }

    public function test_nested_child_goes_through_the_section_shell(): void
    {
        $page = $this->publishedPage();

        $container = InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'section_two_col', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        $child = InvitationSection::create([
            'page_id' => $page->id, 'parent_id' => $container->id, 'section_type' => 'text',
            'order_index' => 0, 'is_visible' => true,
            'props' => ['content' => 'Nested animated', 'column_index' => 1, 'animation' => 'fade-up'],
            'custom_css' => 'letter-spacing: 3px;',
        ]);

        $html = $this->get("/invitation/{$page->slug}")->assertOk()->getContent();

        // Tanpa shell, dua hal ini tidak pernah dirender untuk blok anak: atribut animasi
        // dan blok custom_css yang di-scope ke data-section-id.
        $this->assertStringContainsString('data-animate="fade-up"', $html);
        $this->assertStringContainsString('[data-section-id="'.$child->id.'"]', $html);
    }

    public function test_container_background_is_transparent_by_default(): void
    {
        $page = $this->publishedPage();

        // Container adalah section: treatment-nya dilukis shell. Latar opak bawaan akan
        // menutupinya, jadi 'dark' harus tetap terlihat.
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'section_one_col', 'order_index' => 0,
            'props' => ['treatment' => 'dark'], 'is_visible' => true,
        ]);

        $html = $this->get("/invitation/{$page->slug}")->assertOk()->getContent();

        $this->assertStringContainsString('sec-treat--dark', $html);
        $this->assertStringContainsString('background-color:transparent', $html);
    }

    public function test_image_radius_can_be_set_per_corner(): void
    {
        $page = $this->publishedPage();

        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'image', 'order_index' => 0, 'is_visible' => true,
            'props' => [
                'src' => 'foto.jpg', 'radius_per_corner' => true,
                'radius_tl' => 4, 'radius_tr' => 8, 'radius_br' => 12, 'radius_bl' => 16,
            ],
        ]);

        $html = $this->get("/invitation/{$page->slug}")->assertOk()->getContent();

        // Urutan CSS: kiri-atas, kanan-atas, kanan-bawah, kiri-bawah.
        $this->assertStringContainsString('border-radius: 4px 8px 12px 16px;', $html);
    }

    public function test_image_falls_back_to_single_radius_when_per_corner_is_off(): void
    {
        $page = $this->publishedPage();

        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'image', 'order_index' => 0, 'is_visible' => true,
            // Nilai pojok tetap tersimpan tapi harus diabaikan selama centangnya mati.
            'props' => ['src' => 'foto.jpg', 'border_radius' => 6, 'radius_tl' => 99],
        ]);

        $html = $this->get("/invitation/{$page->slug}")->assertOk()->getContent();

        $this->assertStringContainsString('border-radius: 6px;', $html);
        $this->assertStringNotContainsString('99px', $html);
    }

    public function test_theme_style_block_is_present_when_page_uses_sections(): void
    {
        $page = $this->publishedPage();
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'x'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee(':root{', false);
    }
}
