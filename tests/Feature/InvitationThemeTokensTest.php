<?php
namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\InvitationRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationThemeTokensTest extends TestCase
{
    use RefreshDatabase;

    private function pageWithTheme(array $theme): InvitationPage
    {
        $user = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'published',
            'created_by' => $user->id, 'theme' => $theme,
        ]);
        return InvitationPage::create([
            'title' => 'P', 'slug' => 'p-'.uniqid(), 'published_status' => 'published',
            'template_id' => $template->id, 'created_by' => $user->id,
            'groom_name' => 'R', 'bride_name' => 'J', 'event_date' => now()->addMonth(),
        ]);
    }

    public function test_default_scales_emitted_as_css_vars(): void
    {
        $page = $this->pageWithTheme([]);
        $css = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString('--step-base: 16px', $css);
        $this->assertStringContainsString('--radius: 12px', $css);
        $this->assertStringContainsString('--section-y: 64px', $css);
        $this->assertStringContainsString('--color-ink:', $css);
        $this->assertStringContainsString('--color-on_dark:', $css);
        $this->assertStringContainsString('--shadow:', $css);
    }

    public function test_type_scale_computed_from_base_and_ratio(): void
    {
        $page = $this->pageWithTheme(['scales' => ['type_base' => 20, 'type_ratio' => 1.5]]);
        $css = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString('--step-base: 20px', $css);
        $this->assertStringContainsString('--step-lg: 30px', $css);   // 20 * 1.5
        $this->assertStringContainsString('--step-xl: 45px', $css);   // 20 * 1.5^2
    }

    public function test_invalid_scale_falls_back_to_default(): void
    {
        $page = $this->pageWithTheme(['scales' => ['type_base' => 'evil; }']]);
        $css = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString('--step-base: 16px', $css); // default, bukan nilai jahat
        $this->assertStringNotContainsString('evil', $css);
    }
}
