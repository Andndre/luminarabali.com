<?php

namespace Tests\Feature;

use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioNestingApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private InvitationTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['division' => 'super_admin']);
        $this->template = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't-'.uniqid(), 'status' => 'draft', 'created_by' => $this->admin->id,
        ]);
        $this->actingAs($this->admin);
    }

    private function section(string $type, ?int $parentId = null, array $props = []): InvitationSection
    {
        return InvitationSection::create([
            'template_id' => $this->template->id,
            'parent_id' => $parentId,
            'section_type' => $type,
            'order_index' => 0,
            'props' => $props,
            'is_visible' => true,
        ]);
    }

    private function store(array $body)
    {
        return $this->postJson("/admin/api/studio/templates/{$this->template->id}/sections", $body);
    }

    public function test_container_section_can_be_added_again(): void
    {
        $this->store(['section_type' => 'section_two_col'])
            ->assertCreated()
            ->assertJsonPath('section.section_type', 'section_two_col');
    }

    public function test_basic_can_be_nested_into_a_container_column(): void
    {
        $parent = $this->section('section_two_col');

        $this->store(['section_type' => 'text', 'parent_id' => $parent->id, 'column_index' => 1])
            ->assertCreated()
            ->assertJsonPath('section.parent_id', $parent->id)
            ->assertJsonPath('section.props.column_index', 1);
    }

    public function test_feature_section_cannot_be_nested(): void
    {
        $parent = $this->section('section_two_col');

        $this->store(['section_type' => 'couple', 'parent_id' => $parent->id, 'column_index' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent_id');
    }

    public function test_parent_must_be_a_container(): void
    {
        $parent = $this->section('couple');

        $this->store(['section_type' => 'text', 'parent_id' => $parent->id, 'column_index' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent_id');
    }

    public function test_column_index_must_fit_the_container(): void
    {
        $parent = $this->section('section_two_col');

        $this->store(['section_type' => 'text', 'parent_id' => $parent->id, 'column_index' => 2])
            ->assertStatus(422)
            ->assertJsonValidationErrors('column_index');
    }

    public function test_child_order_index_counts_siblings_not_the_whole_template(): void
    {
        $parent = $this->section('section_two_col');
        $this->section('quote'); // top-level lain, order_index tinggi tak boleh mempengaruhi anak
        InvitationSection::where('template_id', $this->template->id)
            ->where('section_type', 'quote')->update(['order_index' => 9]);

        $first = $this->store(['section_type' => 'text', 'parent_id' => $parent->id, 'column_index' => 0])
            ->json('section');
        $second = $this->store(['section_type' => 'image', 'parent_id' => $parent->id, 'column_index' => 0])
            ->json('section');

        $this->assertSame(0, $first['order_index']);
        $this->assertSame(1, $second['order_index']);
    }
}
