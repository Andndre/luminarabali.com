<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioPublishLintTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_fails_when_template_has_no_cover_section(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'hi'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/templates/{$template->id}/publish");

        $response->assertStatus(422);
        $this->assertContains('Template harus memiliki section cover.', $response->json('errors'));
        $this->assertEquals('draft', $template->fresh()->status);
    }

    public function test_publish_fails_when_a_required_content_field_is_empty_with_no_default(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        // image.src has no default ('') and no value set — an empty required content field.
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'image', 'order_index' => 1,
            'props' => ['src' => ''], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/templates/{$template->id}/publish");

        $response->assertStatus(422);
        $this->assertEquals('draft', $template->fresh()->status);
    }

    public function test_publish_succeeds_when_lint_passes(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/templates/{$template->id}/publish");

        $response->assertOk();
        $this->assertEquals('published', $template->fresh()->status);
    }

    public function test_hidden_sections_are_not_lint_checked(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        // Hidden image with an empty src must NOT block publish.
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'image', 'order_index' => 1,
            'props' => ['src' => ''], 'is_visible' => false,
        ]);

        $this->actingAs($admin);

        $this->postJson("/admin/templates/{$template->id}/publish")->assertOk();
        $this->assertEquals('published', $template->fresh()->status);
    }

    public function test_publish_fails_when_an_image_section_has_empty_src_even_with_alt_set(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Rustic', 'slug' => 'rustic-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'cover', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
        ]);
        // image.src has no fallback rendering (unlike cover's background_image); an empty src
        // must still block publish even though alt is non-empty.
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'image', 'order_index' => 1,
            'props' => ['src' => '', 'alt' => 'something'], 'is_visible' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->postJson("/admin/templates/{$template->id}/publish");

        $response->assertStatus(422);
        $this->assertEquals('draft', $template->fresh()->status);
    }
}
