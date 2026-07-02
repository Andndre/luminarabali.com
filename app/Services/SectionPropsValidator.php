<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class SectionPropsValidator
{
    public function validate(string $sectionType, array $props): array
    {
        $schema = config("invitation_components.{$sectionType}", []);
        $validated = [];
        $errors = [];

        foreach ($schema as $field) {
            $key = $field['key'];

            if (!array_key_exists($key, $props)) {
                continue;
            }

            $value = $props[$key];

            match ($field['type']) {
                'color' => $this->validateColor($key, $value, $errors),
                'number' => $this->validateNumber($key, $value, $errors),
                'boolean' => $this->validateBoolean($key, $value, $errors),
                'select' => $this->validateSelect($key, $value, $field['options'] ?? [], $errors),
                'url' => $this->validateUrl($key, $value, $errors),
                'text' => $this->validateText($key, $value, $errors),
                default => null,
            };

            $validated[$key] = $value;
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $validated;
    }

    protected function validateColor(string $key, mixed $value, array &$errors): void
    {
        if (!is_string($value) || !preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
            $errors["props.{$key}"] = ["{$key} harus berupa kode warna hex yang valid."];
        }
    }

    protected function validateNumber(string $key, mixed $value, array &$errors): void
    {
        if ($value !== null && !is_numeric($value)) {
            $errors["props.{$key}"] = ["{$key} harus berupa angka."];
        }
    }

    protected function validateBoolean(string $key, mixed $value, array &$errors): void
    {
        if (!is_bool($value)) {
            $errors["props.{$key}"] = ["{$key} harus berupa true/false."];
        }
    }

    protected function validateSelect(string $key, mixed $value, array $options, array &$errors): void
    {
        if (!in_array($value, $options, true)) {
            $errors["props.{$key}"] = ["{$key} harus salah satu dari: ".implode(', ', $options).'.'];
        }
    }

    protected function validateUrl(string $key, mixed $value, array &$errors): void
    {
        if ($value !== null && $value !== '' && $value !== '#'
            && (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL))) {
            $errors["props.{$key}"] = ["{$key} harus berupa URL yang valid."];
        }
    }

    protected function validateText(string $key, mixed $value, array &$errors): void
    {
        if ($value !== null && !is_string($value)) {
            $errors["props.{$key}"] = ["{$key} harus berupa teks."];
        }
    }
}
