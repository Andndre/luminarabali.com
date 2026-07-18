<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTokenPassTest extends TestCase
{
    use RefreshDatabase;

    private function render(string $type, array $props): string
    {
        $user = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'published', 'created_by' => $user->id,
        ]);
        $page = InvitationPage::create([
            'title' => 'P', 'slug' => 'p-'.uniqid(), 'published_status' => 'published',
            'template_id' => $template->id, 'created_by' => $user->id,
            'groom_name' => 'R', 'bride_name' => 'J', 'event_date' => now()->addMonth(),
        ]);
        InvitationSection::create([
            'page_id' => $page->id, 'section_type' => $type, 'order_index' => 1,
            'is_visible' => true, 'props' => $props,
        ]);

        return $this->get('/invitation/'.$page->slug)->getContent();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('genericColorComponents')]
    public function test_component_has_no_generic_tailwind_colors(string $type, array $props): void
    {
        $html = $this->render($type, $props);
        foreach (['bg-blue-600', 'text-gray-700', 'text-gray-600', 'bg-blue-700'] as $banned) {
            $this->assertStringNotContainsString($banned, $html, "{$type} masih memakai {$banned}");
        }
    }

    public static function genericColorComponents(): array
    {
        return [
            'rsvp' => ['rsvp', []],
            'gift' => ['gift', []],
            'countdown' => ['countdown', ['target_date' => '2026-12-31']],
            'wishes' => ['wishes', []],
            'live_stream' => ['live_stream', []],
            'video' => ['video', []],
            'love_story' => ['love_story', []],
            'closing' => ['closing', []],
        ];
    }
}
