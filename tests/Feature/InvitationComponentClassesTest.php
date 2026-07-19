<?php

namespace Tests\Feature;

use Tests\TestCase;

class InvitationComponentClassesTest extends TestCase
{
    public function test_every_component_type_belongs_to_exactly_one_class(): void
    {
        $classes = config('invitation_component_classes');
        $classified = array_merge($classes['feature'], $classes['container'], $classes['basic']);
        $types = array_keys(config('invitation_components'));

        $this->assertSame([], array_diff($types, $classified),
            'Ada tipe komponen tanpa kelas — tambahkan ke invitation_component_classes.');
        $this->assertSame([], array_diff($classified, $types),
            'Ada kelas menyebut tipe yang tak ada di invitation_components.');
        $this->assertSame(count($classified), count(array_unique($classified)),
            'Ada tipe yang masuk lebih dari satu kelas.');
    }

    public function test_every_container_declares_its_column_count(): void
    {
        $classes = config('invitation_component_classes');

        foreach ($classes['container'] as $type) {
            $this->assertArrayHasKey($type, $classes['container_columns'],
                "Container '{$type}' tak punya entri container_columns.");
            $this->assertGreaterThan(0, $classes['container_columns'][$type]);
        }
    }

    public function test_treatment_applies_to_feature_and_container_only(): void
    {
        $classes = config('invitation_component_classes');

        foreach ($classes['basic'] as $type) {
            $keys = collect(config("invitation_components.{$type}"))->pluck('key');
            $this->assertFalse($keys->contains('bg_image'),
                "Basic '{$type}' tak boleh dapat treatment latar (guideline §9).");
        }

        foreach (array_merge($classes['feature'], $classes['container']) as $type) {
            $keys = collect(config("invitation_components.{$type}"))->pluck('key');
            $this->assertTrue($keys->contains('treatment'),
                "Section '{$type}' seharusnya punya field treatment.");
        }
    }
}
