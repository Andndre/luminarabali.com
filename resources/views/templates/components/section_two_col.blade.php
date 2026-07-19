@props(['props' => [], 'section' => null, 'page' => null, 'elements' => []])

@php
    // Rasio tak dikenal jatuh ke 50-50, bukan ke lebar kosong.
    $ratio = in_array($props['column_ratio'] ?? null, ['50-50', '60-40', '40-60', '70-30', '30-70'], true)
        ? $props['column_ratio'] : '50-50';
@endphp

@include('templates._container-box', [
    'props' => $props, 'section' => $section, 'page' => $page, 'elements' => $elements,
    'widths' => array_map('intval', explode('-', $ratio)),
])
