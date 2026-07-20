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

    public function test_heading_rule_ornament_emits_var_only_for_safe_local_paths(): void
    {
        $renderer = new InvitationRenderer();

        $style = $renderer->themeStyle($this->pageWithTheme(['ornaments' => ['heading_rule' => 'ornaments/leaf.svg']], null));
        // URL absolut wajib: url() di custom property diselesaikan relatif ke stylesheet
        // yang MEMAKAI var, dan di dev itu disajikan Vite dari host lain.
        $this->assertStringContainsString("--heading-rule: url('".asset('storage/ornaments/leaf.svg')."')", $style);
        $this->assertStringContainsString('http', $style);
        $this->assertStringContainsString('--heading-rule-w: 80%', $style);

        // Lebar dijepit ke 10..100 supaya nilai liar tidak merusak layout judul.
        $wide = $renderer->themeStyle($this->pageWithTheme(
            ['ornaments' => ['heading_rule' => 'ornaments/leaf.svg', 'heading_rule_width' => 999]], null
        ));
        $this->assertStringContainsString('--heading-rule-w: 100%', $wide);

        // Ornamen atas berdiri sendiri: kalau hanya sisi atas yang diisi, var sisi bawah
        // tidak boleh ikut keluar — kalau tidak, batang lurus bawaan berubah jadi
        // kotak aksen setinggi rasio 7:1.
        $topOnly = $renderer->themeStyle($this->pageWithTheme(
            ['ornaments' => ['heading_rule_top' => 'ornaments/crown.svg']], null
        ));
        $this->assertStringContainsString('--heading-rule-top: ', $topOnly);
        $this->assertStringContainsString('--heading-rule-top-d: block', $topOnly);

        // Lebar tiap sisi berdiri sendiri.
        $both = $renderer->themeStyle($this->pageWithTheme(['ornaments' => [
            'heading_rule_top' => 'ornaments/crown.svg', 'heading_rule_top_width' => 30,
            'heading_rule' => 'ornaments/leaf.svg', 'heading_rule_width' => 90,
        ]], null));
        $this->assertStringContainsString('--heading-rule-top-w: 30%', $both);
        $this->assertStringContainsString('--heading-rule-w: 90%', $both);
        $this->assertStringNotContainsString('--heading-rule-ar', $topOnly);
        $this->assertStringNotContainsString('--heading-rule-h', $topOnly);

        // Path masuk ke url() di dalam <style>: kutip harus ditolak, bukan di-escape,
        // supaya tidak bisa menutup url() dan menyuntik deklarasi lain.
        $evil = $renderer->themeStyle($this->pageWithTheme(
            ['ornaments' => ['heading_rule' => "x.svg'); background: url('//evil"]], null
        ));
        $this->assertStringNotContainsString('--heading-rule:', $evil);
        $this->assertStringNotContainsString('evil', $evil);

        // Tanpa ornamen: tidak ada var url sama sekali, CSS jatuh ke batang lurus bawaan
        // di bawah judul dan tidak apa-apa di atasnya. (--heading-rule-gap selalu ada:
        // ia juga mengatur jarak batang lurus itu.)
        $none = $renderer->themeStyle($this->pageWithTheme(null, null));
        $this->assertStringNotContainsString('--heading-rule:', $none);
        $this->assertStringNotContainsString('--heading-rule-top:', $none);
        $this->assertStringContainsString('--heading-rule-gap: 14px', $none);
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

    public function test_google_font_builds_its_own_link_from_the_family_name(): void
    {
        $page = $this->pageWithTheme([
            'fonts' => ['heading' => ['source' => 'google', 'family' => 'Cormorant Garamond']],
        ], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString("--font-heading: 'Cormorant Garamond'", $style);
        $this->assertStringContainsString('family=Cormorant+Garamond', $style);
    }

    public function test_uploaded_font_emits_a_font_face_instead_of_a_link(): void
    {
        $page = $this->pageWithTheme([
            'fonts' => ['heading' => ['source' => 'upload', 'family' => 'Gentium Plus', 'path' => 'invitations/gentium.woff2']],
        ], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringContainsString("@font-face{font-family:'Gentium Plus'", $style);
        $this->assertStringContainsString("url('".asset('storage/invitations/gentium.woff2')."') format('woff2')", $style);
        $this->assertStringContainsString("--font-heading: 'Gentium Plus'", $style);
        $this->assertStringNotContainsString('fonts.googleapis.com/css2?family=Gentium', $style);
    }

    /**
     * Nama keluarga dipakai mentah di font-family, jadi yang tidak lolos pola harus
     * DIBUANG — bukan di-escape, bukan sebagian lolos.
     */
    public function test_font_family_that_could_escape_the_css_rule_is_dropped(): void
    {
        $page = $this->pageWithTheme([
            'fonts' => ['heading' => ['source' => 'google', 'family' => "X'; } body { display:none } .y{font-family:'Z"]],
        ], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringNotContainsString('display:none', $style);
        $this->assertStringNotContainsString('--font-heading', $style);
    }

    public function test_uploaded_font_with_unsupported_extension_is_dropped(): void
    {
        $page = $this->pageWithTheme([
            'fonts' => ['heading' => ['source' => 'upload', 'family' => 'Sneaky', 'path' => 'invitations/font.svg']],
        ], null);

        $style = (new InvitationRenderer())->themeStyle($page);

        $this->assertStringNotContainsString('@font-face', $style);
        $this->assertStringNotContainsString('Sneaky', $style);
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
