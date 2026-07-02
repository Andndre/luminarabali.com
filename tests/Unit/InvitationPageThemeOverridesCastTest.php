<?php

namespace Tests\Unit;

use App\Models\InvitationPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPageThemeOverridesCastTest extends TestCase
{
    use RefreshDatabase;

    public function test_theme_overrides_is_cast_to_array_and_round_trips(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'a-and-b',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
            'theme_overrides' => ['colors' => ['primary' => '#b5654d']],
        ]);

        $fresh = InvitationPage::find($page->id);

        $this->assertIsArray($fresh->theme_overrides);
        $this->assertSame('#b5654d', $fresh->theme_overrides['colors']['primary']);
    }

    public function test_theme_overrides_defaults_to_null_when_not_set(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        $page = InvitationPage::create([
            'title' => 'A & B', 'slug' => 'no-overrides',
            'groom_name' => 'A', 'bride_name' => 'B', 'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => $admin->id,
        ]);

        $this->assertNull($page->fresh()->theme_overrides);
    }
}
