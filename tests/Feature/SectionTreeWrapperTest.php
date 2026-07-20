<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTreeWrapperTest extends TestCase
{
    use RefreshDatabase;

    private function template(User $admin): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
    }

    public function test_studio_preview_wraps_each_top_level_section_with_a_section_id_marker(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Halo'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->get("/admin/templates/{$template->id}/studio/preview")
            ->assertOk()
            ->assertSee('data-section-id="'.$section->id.'"', false)
            ->assertSee('display: contents', false);
    }

    public function test_child_sections_are_wrapped_too(): void
    {
        // Dulu anak container di-@include langsung tanpa shell, jadi tidak punya wrapper —
        // konsekuensi tak disengaja yang mematikan animasi + custom_css di blok nested dan
        // memaksa swapSection() jatuh ke reload penuh karena wrapper-nya tak pernah ketemu.
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        $parent = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'section_one_col', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        $child = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'parent_id' => $parent->id, 'props' => ['content' => 'Anak'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $this->get("/admin/templates/{$template->id}/studio/preview")
            ->assertOk()
            ->assertSee('data-section-id="'.$parent->id.'"', false)
            ->assertSee('data-section-id="'.$child->id.'"', false);
    }
}
