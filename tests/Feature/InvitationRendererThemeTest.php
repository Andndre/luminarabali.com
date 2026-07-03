<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\InvitationRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationRendererThemeTest extends TestCase
{
    use RefreshDatabase;

    private function pageWithTheme(?array $templateTheme, ?array $pageOverrides): InvitationPage
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'published',
            'created_by' => $admin->id, 'theme' => $templateTheme,
        ]);

        return InvitationPage::create([
            'template_id' => $template->id, 'title' => 'A & B', 'slug' => 'a-and-b-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
            'theme_overrides' => $pageOverrides,
        ])->load('template');
    }

    public function test_falls_back_to_default_theme_when_template_and_page_have_none(): void
    {
        $page = $this->pageWithTheme(null, null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString('--color-primary: '.config('invitation.default_theme.colors.primary'), $style);
        $this->assertStringContainsString("--font-heading: '".config('invitation.default_theme.fonts.heading')."'", $style);
    }

    public function test_template_theme_overrides_default(): void
    {
        $page = $this->pageWithTheme(['colors' => ['primary' => '#111111']], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString('--color-primary: #111111', $style);
    }

    public function test_page_theme_overrides_win_over_template_theme(): void
    {
        $page = $this->pageWithTheme(
            ['colors' => ['primary' => '#111111']],
            ['colors' => ['primary' => '#222222']]
        );

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString('--color-primary: #222222', $style);
        $this->assertStringNotContainsString('#111111', $style);
    }

    public function test_invalid_color_value_is_dropped_not_injected(): void
    {
        $page = $this->pageWithTheme(['colors' => ['primary' => "red; } body { display:none"]], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringNotContainsString('display:none', $style);
        $this->assertStringNotContainsString('--color-primary: red', $style);
    }

    public function test_font_not_in_curated_list_is_dropped_and_no_link_tag_generated(): void
    {
        $page = $this->pageWithTheme(['fonts' => ['heading' => 'Comic Sans MS']], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringNotContainsString('Comic Sans', $style);
    }

    public function test_font_links_only_include_curated_fonts_actually_used(): void
    {
        $page = $this->pageWithTheme(['fonts' => ['heading' => 'Lora', 'body' => 'Lora']], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertSame(1, substr_count($style, 'family=Lora'));
    }

    public function test_malicious_color_key_is_dropped_not_injected(): void
    {
        $maliciousKey = 'primary; } body { display:none } .x{color';

        $page = $this->pageWithTheme(['colors' => [$maliciousKey => '#fff']], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringNotContainsString($maliciousKey, $style);
        $this->assertStringNotContainsString('display:none', $style);
    }
}
