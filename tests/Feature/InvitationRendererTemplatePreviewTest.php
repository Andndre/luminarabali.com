<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\InvitationRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationRendererTemplatePreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_render_template_renders_visible_sections_with_placeholder_couple_names(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'hero', 'order_index' => 1,
            'props' => [], 'is_visible' => false,
        ]);

        $html = (new InvitationRenderer())->renderTemplate($template->fresh(['sections']));

        $this->assertStringContainsString('Romeo', $html);
        $this->assertStringContainsString('Juliet', $html);
        // The hidden hero section must not render.
        // Note: the cover partial emits the "cover-section-{id}" class name multiple
        // times (once on the element, plus several times in scoped inline CSS
        // selectors), so counting raw substring occurrences would overcount. We
        // instead count how many section elements actually carry the class, which
        // accurately reflects "exactly one cover section rendered".
        $this->assertEquals(1, substr_count($html, 'class="cover-section-'));
    }

    public function test_template_theme_style_uses_the_templates_own_theme_not_a_pages(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Sage', 'slug' => 'sage-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
            'theme' => ['colors' => ['primary' => '#4a5d43'], 'fonts' => ['heading' => 'Lora']],
        ]);

        $style = (new InvitationRenderer())->templateThemeStyle($template);

        $this->assertStringContainsString('--color-primary: #4a5d43;', $style);
        $this->assertStringContainsString("--font-heading: 'Lora';", $style);
    }
}
