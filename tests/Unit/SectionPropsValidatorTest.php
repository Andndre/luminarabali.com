<?php

namespace Tests\Unit;

use App\Services\SectionPropsValidator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SectionPropsValidatorTest extends TestCase
{
    public function test_valid_props_pass_through_unchanged(): void
    {
        $validator = new SectionPropsValidator();

        $result = $validator->validate('text', [
            'content' => 'Hello',
            'align' => 'center',
            'color' => '#3b2f2f',
            'line_height' => 1.5,
        ]);

        $this->assertSame('Hello', $result['content']);
        $this->assertSame('center', $result['align']);
        $this->assertSame('#3b2f2f', $result['color']);
        $this->assertSame(1.5, $result['line_height']);
    }

    public function test_unknown_keys_are_silently_dropped(): void
    {
        $validator = new SectionPropsValidator();

        $result = $validator->validate('text', [
            'content' => 'Hello',
            'not_a_real_field' => 'should be dropped',
        ]);

        $this->assertArrayHasKey('content', $result);
        $this->assertArrayNotHasKey('not_a_real_field', $result);
    }

    public function test_invalid_color_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);

        (new SectionPropsValidator())->validate('text', ['color' => 'not-a-hex-color']);
    }

    public function test_invalid_select_option_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);

        (new SectionPropsValidator())->validate('text', ['align' => 'diagonal']);
    }

    public function test_invalid_boolean_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);

        (new SectionPropsValidator())->validate('gallery', ['lightbox' => 'yes']);
    }

    public function test_invalid_number_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);

        (new SectionPropsValidator())->validate('gallery', ['columns' => 'three']);
    }

    public function test_unknown_section_type_returns_empty_array(): void
    {
        $result = (new SectionPropsValidator())->validate('not_a_real_section_type', ['anything' => 'here']);

        $this->assertSame([], $result);
    }

    public function test_only_group_filter_drops_design_props(): void
    {
        $validator = new SectionPropsValidator();

        $result = $validator->validate('text', [
            'content' => 'Hello',        // group: content
            'color' => '#ff0000',        // group: design -> harus dibuang
            'custom_css' => 'color:red', // group: design -> harus dibuang
        ], 'content');

        $this->assertSame(['content' => 'Hello'], $result);
    }

    public function test_only_group_filter_still_validates_types(): void
    {
        $validator = new SectionPropsValidator();

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $validator->validate('cover', ['title' => ['not-a-string']], 'content');
    }

    public function test_without_group_filter_behavior_is_unchanged(): void
    {
        $validator = new SectionPropsValidator();

        $result = $validator->validate('text', ['content' => 'Hi', 'color' => '#ff0000']);

        $this->assertSame(['content' => 'Hi', 'color' => '#ff0000'], $result);
    }

    public function test_whatsapp_phone_with_script_breaking_characters_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);

        (new SectionPropsValidator())->validate('rsvp', [
            'whatsapp_phone' => "not a valid <script>alert(1)</script>` phone",
        ], 'content');
    }

    public function test_whatsapp_phone_with_valid_formats_passes_through_unchanged(): void
    {
        $validator = new SectionPropsValidator();

        $result1 = $validator->validate('rsvp', ['whatsapp_phone' => '+62812345678'], 'content');
        $result2 = $validator->validate('rsvp', ['whatsapp_phone' => '0812-3456-789'], 'content');

        $this->assertSame('+62812345678', $result1['whatsapp_phone']);
        $this->assertSame('0812-3456-789', $result2['whatsapp_phone']);
    }
}
