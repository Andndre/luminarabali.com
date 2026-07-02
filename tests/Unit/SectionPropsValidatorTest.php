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
}
