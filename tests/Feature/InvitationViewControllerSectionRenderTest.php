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

    public function test_page_without_sections_falls_back_to_legacy_html_content(): void
    {
        $page = $this->publishedPage();
        $page->template->update(['html_content' => '<p id="legacy-marker">legacy blob</p>']);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('legacy-marker', false);
    }

    public function test_page_with_sections_renders_section_tree_instead_of_legacy_blob(): void
    {
        $page = $this->publishedPage();
        $page->template->update(['html_content' => '<p id="legacy-marker">should not appear</p>']);

        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Hello from section tree'], 'is_visible' => true,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Hello from section tree');
        $response->assertDontSee('legacy-marker', false);
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
