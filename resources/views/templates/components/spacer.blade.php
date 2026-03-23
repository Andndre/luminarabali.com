@props(['props' => [], 'section' => null, 'page' => null])

@php
    $height = $props['height'] ?? 50;
    $elementId = $props['element_id'] ?? null;
    $customCss = $props['custom_css'] ?? '';
@endphp

<section @if ($elementId) id="{{ $elementId }}" @endif
    class="spacer-section-{{ $section->id ?? 'default' }}" style="height: {{ $height }}px; {{ $customCss }}">
</section>
