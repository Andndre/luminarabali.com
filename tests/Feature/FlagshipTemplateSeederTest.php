<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\TemplateInstantiator;
use Database\Seeders\FlagshipTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlagshipTemplateSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_two_templates_with_theme_and_master_sections(): void
    {
        User::factory()->create(['division' => 'super_admin']);
        $this->seed(FlagshipTemplateSeeder::class);

        foreach (['terracotta-dawn', 'sage-garden'] as $slug) {
            $template = InvitationTemplate::where('slug', $slug)->firstOrFail();
            $this->assertIsArray($template->theme);
            $this->assertArrayHasKey('colors', $template->theme);
            $this->assertArrayHasKey('fonts', $template->theme);

            $masters = InvitationSection::where('template_id', $template->id)
                ->whereNull('page_id')->get();
            $this->assertGreaterThanOrEqual(4, $masters->count(), "{$slug} needs >= 4 sections");
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        User::factory()->create(['division' => 'super_admin']);
        $this->seed(FlagshipTemplateSeeder::class);
        $this->seed(FlagshipTemplateSeeder::class);

        $this->assertSame(1, InvitationTemplate::where('slug', 'terracotta-dawn')->count());
        $template = InvitationTemplate::where('slug', 'terracotta-dawn')->first();
        $this->assertSame(
            InvitationSection::where('template_id', $template->id)->count(),
            InvitationSection::where('template_id', $template->id)->distinct('order_index')->count('order_index')
        );
    }

    public function test_instantiated_flagship_page_renders_publicly(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $this->seed(FlagshipTemplateSeeder::class);
        $template = InvitationTemplate::where('slug', 'terracotta-dawn')->firstOrFail();

        $page = app(TemplateInstantiator::class)->instantiate($template, [
            'title' => 'Test Couple', 'slug' => 'flagship-render-test',
            'groom_name' => 'Rama', 'bride_name' => 'Sinta',
            'event_date' => now()->addMonth(),
            'published_status' => 'published', 'created_by' => $admin->id,
        ]);

        $response = $this->get("/invitation/{$page->slug}");

        $response->assertOk();
        $response->assertSee('Rama');
        $response->assertSee('Sinta');
        $response->assertSee(':root{', false);
        $response->assertSee('--color-primary', false);
    }
}
