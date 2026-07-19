<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvitationSectionVariantThumbnailTest extends TestCase
{
    use RefreshDatabase;

    public function test_variant_thumbnails_column_casts_to_array(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        $section = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'couple', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
            'variant_thumbnails' => ['portrait-overlay' => 'section-thumbs/1/portrait-overlay.png'],
        ]);

        $this->assertSame(
            ['portrait-overlay' => 'section-thumbs/1/portrait-overlay.png'],
            $section->fresh()->variant_thumbnails
        );
    }

    public function test_load_payload_includes_variant_thumbnails(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'couple', 'order_index' => 0,
            'props' => [], 'is_visible' => true,
            'variant_thumbnails' => ['centered-stacked' => 'section-thumbs/x.png'],
        ]);

        $this->actingAs($admin);
        $this->getJson("/admin/api/templates/{$template->id}/load")
            ->assertOk()
            ->assertJsonPath('sections.0.variant_thumbnails.centered-stacked', 'section-thumbs/x.png');
    }

    public function test_non_super_admin_cannot_store_a_variant_thumbnail(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $staff = User::factory()->create(['division' => 'photobooth']);
        $template = InvitationTemplate::create(['name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id]);
        $section = InvitationSection::create(['template_id' => $template->id, 'section_type' => 'couple', 'order_index' => 0, 'props' => [], 'is_visible' => true]);

        $this->actingAs($staff);
        $this->post("/admin/api/studio/sections/{$section->id}/variant-thumbnail", [
            'variant' => 'portrait-overlay', 'image' => UploadedFile::fake()->image('t.png', 400, 300),
        ])->assertForbidden();
    }

    public function test_storing_a_thumbnail_for_an_invalid_variant_is_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create(['name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id]);
        $section = InvitationSection::create(['template_id' => $template->id, 'section_type' => 'couple', 'order_index' => 0, 'props' => [], 'is_visible' => true]);

        $this->actingAs($admin);
        // postJson (not post): plain post() omits the Accept:application/json
        // header, so Laravel's validate() redirects (302) on failure instead
        // of returning a 422 JSON response — matches convention used by every
        // sibling 422-assertion test in this codebase (e.g. StudioCreateSectionTest).
        $this->postJson("/admin/api/studio/sections/{$section->id}/variant-thumbnail", [
            'variant' => 'tidak-ada', 'image' => UploadedFile::fake()->image('t.png', 400, 300),
        ])->assertStatus(422);
    }

    public function test_storing_a_valid_thumbnail_downscales_saves_and_records_path(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create(['name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $admin->id]);
        $section = InvitationSection::create(['template_id' => $template->id, 'section_type' => 'couple', 'order_index' => 0, 'props' => [], 'is_visible' => true]);

        $this->actingAs($admin);
        $path = "section-thumbs/{$section->id}/portrait-overlay.png";
        $this->post("/admin/api/studio/sections/{$section->id}/variant-thumbnail", [
            'variant' => 'portrait-overlay', 'image' => UploadedFile::fake()->image('t.png', 800, 600),
        ])->assertOk()->assertJsonPath('path', $path);

        Storage::disk('public')->assertExists($path);
        $this->assertSame($path, $section->fresh()->variant_thumbnails['portrait-overlay']);
    }
}
