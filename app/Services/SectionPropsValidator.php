<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class SectionPropsValidator
{
    public function validate(string $sectionType, array $props, ?string $onlyGroup = null): array
    {
        $schema = config("invitation_components.{$sectionType}", []);

        if ($onlyGroup !== null) {
            $schema = array_values(array_filter(
                $schema,
                fn ($field) => ($field['group'] ?? null) === $onlyGroup
            ));
        }

        $validated = [];
        $errors = [];

        foreach ($schema as $field) {
            $key = $field['key'];

            if (!array_key_exists($key, $props)) {
                continue;
            }

            $value = $props[$key];

            $this->validateField($field, "props.{$key}", $value, $errors);

            $validated[$key] = $value;
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    protected function validateField(array $field, string $errorKey, mixed $value, array &$errors): void
    {
        $key = $field['key'];

        match ($field['type']) {
            'color' => $this->validateColor($errorKey, $value, $errors),
            'number' => $this->validateNumber($errorKey, $value, $errors),
            'boolean' => $this->validateBoolean($errorKey, $value, $errors),
            'select' => $this->validateSelect($errorKey, $value, $field['options'] ?? [], $errors),
            'url' => $this->validateUrl($errorKey, $value, $errors),
            'text' => str_ends_with($key, '_phone')
                ? $this->validatePhone($errorKey, $value, $errors)
                : $this->validateText($errorKey, $value, $errors),
            'image', 'ornament' => $this->validateImage($errorKey, $value, $errors),
            'code' => $this->validateCode($errorKey, $value, $errors),
            'repeater' => $this->validateRepeater($field, $errorKey, $value, $errors),
            default => null,
        };
    }

    protected function validateRepeater(array $field, string $errorKey, mixed $value, array &$errors): void
    {
        if ($value === null) {
            return;
        }

        if (!is_array($value) || !array_is_list($value)) {
            $errors[$errorKey] = ["{$field['key']} harus berupa daftar item."];

            return;
        }

        foreach ($value as $i => $item) {
            if (!is_array($item)) {
                $errors["{$errorKey}.{$i}"] = ['Item harus berupa objek.'];
                continue;
            }

            foreach ($field['fields'] ?? [] as $subField) {
                if ($subField['type'] === 'repeater') {
                    throw new \LogicException('Repeater bersarang tidak didukung (kesalahan skema).');
                }

                $subKey = $subField['key'];
                if (!array_key_exists($subKey, $item)) {
                    continue;
                }

                $this->validateField($subField, "{$errorKey}.{$i}.{$subKey}", $item[$subKey], $errors);
            }
        }
    }

    // $errorKey berbentuk "props.color" atau nested "props.events.0.name";
    // pesan memakai segmen terakhir supaya identik dengan pesan lama untuk field datar.
    protected function shortKey(string $errorKey): string
    {
        return substr(strrchr($errorKey, '.'), 1);
    }

    protected function validateColor(string $errorKey, mixed $value, array &$errors): void
    {
        if (!is_string($value) || !preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus berupa kode warna hex yang valid.'];
        }
    }

    protected function validateNumber(string $errorKey, mixed $value, array &$errors): void
    {
        if ($value !== null && !is_numeric($value)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus berupa angka.'];
        }
    }

    protected function validateBoolean(string $errorKey, mixed $value, array &$errors): void
    {
        if (!is_bool($value)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus berupa true/false.'];
        }
    }

    protected function validateSelect(string $errorKey, mixed $value, array $options, array &$errors): void
    {
        if (!in_array($value, $options, true)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus salah satu dari: '.implode(', ', $options).'.'];
        }
    }

    protected function validateUrl(string $errorKey, mixed $value, array &$errors): void
    {
        if ($value !== null && $value !== '' && $value !== '#'
            && (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL))) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus berupa URL yang valid.'];
        }
    }

    protected function validateText(string $errorKey, mixed $value, array &$errors): void
    {
        if ($value !== null && !is_string($value)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus berupa teks.'];
        }
    }

    protected function validatePhone(string $errorKey, mixed $value, array &$errors): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value) || !preg_match('/^[0-9+\-() ]*$/', $value)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' hanya boleh berisi angka, spasi, +, -, ( dan ).'];
        }
    }

    protected function validateImage(string $errorKey, mixed $value, array &$errors): void
    {
        if ($value !== null && !is_string($value)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus berupa path gambar (teks).'];
        }
    }

    protected function validateCode(string $errorKey, mixed $value, array &$errors): void
    {
        if ($value !== null && !is_string($value)) {
            $errors[$errorKey] = [$this->shortKey($errorKey).' harus berupa teks HTML.'];
        }
    }
}
