<?php

namespace Tests\Feature;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\TemplateInstantiator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateInstantiatorTest extends TestCase
{
    use RefreshDatabase;

    private function makeTemplate(): InvitationTemplate
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        return InvitationTemplate::create([
            'name' => 'Flagship', 'slug' => 'flagship-'.uniqid(),
            'status' => 'published', 'created_by' => $admin->id,
        ]);
    }

    private function pageAttributes(): array
    {
        return [
            'title' => 'A & B', 'slug' => 'a-b-'.uniqid(),
            'groom_name' => 'A', 'bride_name' => 'B',
            'event_date' => now()->addMonth(),
            'published_status' => 'draft', 'created_by' => 1,
        ];
    }

    public function test_instantiate_copies_master_sections_to_page(): void
    {
        $template = $this->makeTemplate();
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text',
            'order_index' => 0, 'props' => ['content' => 'Master text'], 'is_visible' => true,
        ]);

        $page = app(TemplateInstantiator::class)->instantiate($template, $this->pageAttributes());

        $this->assertCount(1, $page->sections);
        $copy = $page->sections->first();
        $this->assertSame('text', $copy->section_type);
        $this->assertSame(['content' => 'Master text'], $copy->props);
        $this->assertNull($copy->template_id);
    }

    public function test_editing_template_after_instantiation_does_not_change_page_copy(): void
    {
        $template = $this->makeTemplate();
        $master = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text',
            'order_index' => 0, 'props' => ['content' => 'Original'], 'is_visible' => true,
        ]);

        $page = app(TemplateInstantiator::class)->instantiate($template, $this->pageAttributes());
        $master->update(['props' => ['content' => 'Changed later']]);

        $this->assertSame('Original', $page->sections()->first()->props['content']);
    }

    public function test_parent_child_relationships_are_remapped(): void
    {
        $template = $this->makeTemplate();
        $parent = InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'section_one_col',
            'order_index' => 0, 'props' => [], 'is_visible' => true,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'parent_id' => $parent->id,
            'section_type' => 'text', 'order_index' => 0,
            'props' => ['content' => 'Child'], 'is_visible' => true,
        ]);

        $page = app(TemplateInstantiator::class)->instantiate($template, $this->pageAttributes());

        $copiedParent = $page->sections()->where('section_type', 'section_one_col')->first();
        $copiedChild = $page->sections()->where('section_type', 'text')->first();
        $this->assertSame($copiedParent->id, $copiedChild->parent_id);
        $this->assertNotSame($parent->id, $copiedParent->id);
    }

    public function test_failure_mid_copy_rolls_back_cleanly(): void
    {
        $template = $this->makeTemplate();
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text',
            'order_index' => 0, 'props' => ['content' => 'x'], 'is_visible' => true,
        ]);

        $attrs = $this->pageAttributes();
        unset($attrs['title']); // NOT NULL constraint -> exception di dalam transaksi

        try {
            app(TemplateInstantiator::class)->instantiate($template, $attrs);
            $this->fail('Expected exception was not thrown');
        } catch (\Throwable $e) {
            // expected
        }

        $this->assertSame(0, InvitationPage::count());
        $this->assertSame(0, InvitationSection::whereNotNull('page_id')->count());
    }

    public function test_blob_template_without_master_sections_copies_nothing(): void
    {
        $template = $this->makeTemplate();

        $page = app(TemplateInstantiator::class)->instantiate($template, $this->pageAttributes());

        $this->assertCount(0, $page->sections);
    }

    public function test_store_endpoint_instantiates_sections_from_template(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $template = InvitationTemplate::create([
            'name' => 'Flagship', 'slug' => 'flagship-store', 'status' => 'published', 'created_by' => $admin->id,
        ]);
        InvitationSection::create([
            'template_id' => $template->id, 'section_type' => 'text',
            'order_index' => 0, 'props' => ['content' => 'Seeded'], 'is_visible' => true,
        ]);

        $this->actingAs($admin)->post('/admin/invitations', [
            'title' => 'C & D', 'slug' => 'c-and-d',
            'groom_name' => 'C', 'bride_name' => 'D',
            'event_date' => now()->addMonth()->toDateString(),
            'template_id' => $template->id,
        ])->assertRedirect();

        $page = InvitationPage::where('slug', 'c-and-d')->firstOrFail();
        $this->assertCount(1, $page->sections);
        $this->assertSame('Seeded', $page->sections->first()->props['content']);
    }
}
