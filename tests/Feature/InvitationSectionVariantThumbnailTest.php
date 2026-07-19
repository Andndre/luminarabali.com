<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
