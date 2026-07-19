<?php

namespace Tests\Unit;

use Tests\TestCase;

class InvitationComponentsSchemaTest extends TestCase
{
    private const ALLOWED_SECTION_TYPES = [
        'cover', 'section_one_col', 'section_two_col', 'section_three_col',
        'hero', 'text', 'image', 'button', 'divider', 'spacer',
        'countdown', 'gallery', 'map', 'music', 'rsvp', 'video',
    ];

    private const VALID_TYPES = ['text', 'select', 'variant', 'color', 'number', 'boolean', 'image', 'audio', 'video', 'image_list', 'url', 'repeater', 'ornament', 'code'];
    private const VALID_GROUPS = ['content', 'design', 'advanced'];

    public function test_schema_has_an_entry_for_every_allowed_section_type(): void
    {
        $schema = config('invitation_components');

        foreach (self::ALLOWED_SECTION_TYPES as $type) {
            $this->assertArrayHasKey($type, $schema, "Missing schema for section type: {$type}");
        }
    }

    public function test_every_field_has_the_required_keys_and_valid_type_and_group(): void
    {
        foreach (config('invitation_components') as $sectionType => $fields) {
            $this->assertIsArray($fields, "{$sectionType} schema must be an array");

            foreach ($fields as $field) {
                $this->assertArrayHasKey('key', $field, "{$sectionType} field missing 'key'");
                $this->assertArrayHasKey('type', $field, "{$sectionType}.{$field['key']} missing 'type'");
                $this->assertArrayHasKey('label', $field, "{$sectionType}.{$field['key']} missing 'label'");
                $this->assertArrayHasKey('group', $field, "{$sectionType}.{$field['key']} missing 'group'");
                $this->assertArrayHasKey('default', $field, "{$sectionType}.{$field['key']} missing 'default'");

                $this->assertContains($field['type'], self::VALID_TYPES, "{$sectionType}.{$field['key']} has invalid type '{$field['type']}'");
                $this->assertContains($field['group'], self::VALID_GROUPS, "{$sectionType}.{$field['key']} has invalid group '{$field['group']}'");

                if (in_array($field['type'], ['select', 'variant'], true)) {
                    $this->assertArrayHasKey('options', $field, "{$sectionType}.{$field['key']} is type={$field['type']} but has no 'options'");
                    $this->assertNotEmpty($field['options']);
                }
            }
        }
    }

    public function test_no_duplicate_keys_within_a_single_section_type(): void
    {
        foreach (config('invitation_components') as $sectionType => $fields) {
            $keys = array_column($fields, 'key');
            $this->assertSame($keys, array_unique($keys), "{$sectionType} has duplicate field keys");
        }
    }
}
