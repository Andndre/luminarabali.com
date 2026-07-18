<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTemplateStylesTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_seeded_templates_differ_in_treatment_and_variant(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $templates = InvitationTemplate::all();
        $this->assertGreaterThanOrEqual(2, $templates->count(), 'butuh minimal 2 template contoh');

        // Kumpulkan sidik gaya (treatment couple + variant) tiap template.
        $fingerprints = $templates->map(function ($t) {
            $couple = InvitationSection::where('template_id', $t->id)->where('section_type', 'couple')->first();
            return ($couple->props['variant'] ?? 'centered-stacked').'|'.($couple->props['treatment'] ?? 'surface');
        })->unique();

        $this->assertGreaterThanOrEqual(2, $fingerprints->count(), 'dua template harus tampak berbeda (varian/treatment couple)');
    }
}
