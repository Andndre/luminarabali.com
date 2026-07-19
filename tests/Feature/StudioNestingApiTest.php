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

    public function test_update_cannot_change_a_nested_child_column_index(): void
    {
        $parent = $this->section('section_two_col');
        $child = $this->section('text', $parent->id, ['column_index' => 0]);

        $this->putJson("/admin/api/templates/sections/{$child->id}", [
            'props' => ['column_index' => 5],
        ])->assertOk();

        $this->assertSame(0, $child->fresh()->props['column_index']);
    }

    public function test_reorder_moves_a_child_between_columns(): void
    {
        $parent = $this->section('section_two_col');
        $child = $this->section('text', $parent->id, ['column_index' => 0]);

        $this->postJson('/admin/api/templates/sections/reorder', [
            'sections' => [
                ['id' => $child->id, 'order_index' => 0, 'parent_id' => $parent->id, 'column_index' => 1],
            ],
        ])->assertOk();

        $child->refresh();
        $this->assertSame($parent->id, $child->parent_id);
        $this->assertSame(1, $child->props['column_index']);
    }

    public function test_reorder_rejects_a_column_index_outside_the_container(): void
    {
        $parent = $this->section('section_two_col');
        $child = $this->section('text', $parent->id, ['column_index' => 0]);

        $this->postJson('/admin/api/templates/sections/reorder', [
            'sections' => [
                ['id' => $child->id, 'order_index' => 0, 'parent_id' => $parent->id, 'column_index' => 5],
            ],
        ])->assertStatus(422);
    }

    public function test_reorder_rejects_promoting_a_basic_under_a_non_container(): void
    {
        $feature = $this->section('couple');
        $child = $this->section('text');

        $this->postJson('/admin/api/templates/sections/reorder', [
            'sections' => [
                ['id' => $child->id, 'order_index' => 0, 'parent_id' => $feature->id, 'column_index' => 0],
            ],
        ])->assertStatus(422);
    }

    public function test_reorder_without_column_index_defaults_to_zero_not_stale_value(): void
    {
        $threeCol = $this->section('section_three_col');
        $twoCol = $this->section('section_two_col');
        $child = $this->section('text', $threeCol->id, ['column_index' => 2]);

        // Payload memindahkan anak ke container 2-kolom TANPA mengirim column_index sama
        // sekali — validasi menganggap key hilang berarti 0, jadi penulisan pun harus
        // menulis 0, bukan membiarkan nilai lama (2) yang di luar jangkauan container baru.
        $this->postJson('/admin/api/templates/sections/reorder', [
            'sections' => [
                ['id' => $child->id, 'order_index' => 0, 'parent_id' => $twoCol->id],
            ],
        ])->assertOk();

        $child->refresh();
        $this->assertSame($twoCol->id, $child->parent_id);
        $this->assertSame(0, $child->props['column_index']);
    }

    public function test_promoting_a_child_to_top_level_drops_its_column_index(): void
    {
        $parent = $this->section('section_two_col');
        $child = $this->section('text', $parent->id, ['column_index' => 1]);

        $this->postJson('/admin/api/templates/sections/reorder', [
            'sections' => [
                ['id' => $child->id, 'order_index' => 0, 'parent_id' => null],
            ],
        ])->assertOk();

        $child->refresh();
        $this->assertNull($child->parent_id);
        $this->assertArrayNotHasKey('column_index', $child->props);
    }
}
