@props(['props' => [], 'section' => null, 'page' => null])

@php
    $type = $props['type'] ?? 'line';
    $height = $props['height'] ?? 1;
    $color = $props['color'] ?? '#e5e7eb';
    $lineStyle = $props['style'] ?? 'solid';
    $width = $props['width'] ?? 100;
    $marginTop = $props['margin_top'] ?? 24;
    $marginBottom = $props['margin_bottom'] ?? 24;
    $elementId = $props['element_id'] ?? null;
    $customClass = $props['custom_class'] ?? '';
    $customCss = $props['custom_css'] ?? '';
@endphp

@if ($type === 'line')
    <section class="divider-section-{{ $section->id }}"
        style="margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;">
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
                <div @if ($elementId) id="{{ $elementId }}" @endif
                    @if ($customClass) class="{{ $customClass }}" @endif
                    style="height: {{ $height }}px; width: {{ $width }}%; background-color: {{ $color }}; border-bottom: {{ $lineStyle }} 0 transparent; {{ $customCss }}">
                </div>
            </div>
        </div>
    </section>
@else
    <section @if ($elementId) id="{{ $elementId }}" @endif
        @if ($customClass) class="{{ $customClass }}" @endif
        style="height: {{ $height }}px; {{ $customCss }}">
    </section>
@endif
