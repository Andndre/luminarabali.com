<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioTemplatePreviewPageTest extends TestCase
{
    use RefreshDatabase;

    private function template(User $admin): InvitationTemplate
    {
        return InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
            'theme' => ['colors' => ['primary' => '#4a5d43'], 'fonts' => ['heading' => 'Lora']],
        ]);
    }

    public function test_preview_renders_template_sections_with_placeholder_names_and_theme(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = $this->template($admin);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->get("/admin/templates/{$template->id}/studio/preview");

        $response->assertOk();
        $response->assertSee('Romeo');
        $response->assertSee('Juliet');
        $response->assertSee('--color-primary: #4a5d43;', false);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_non_admin_cannot_open_the_preview(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $template = $this->template($admin);

        $this->actingAs($staff);

        $this->get("/admin/templates/{$template->id}/studio/preview")->assertForbidden();
    }
}
