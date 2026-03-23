@props(['props' => [], 'section' => null, 'page' => null])

@php
    $text = $props['text'] ?? 'Click Me';
    $linkType = $props['link_type'] ?? 'url';
    $url = $props['url'] ?? '#';
    $style = $props['variant'] ?? ($props['style'] ?? 'primary');
    $size = $props['size'] ?? 'medium';
    $alignment = $props['align'] ?? ($props['alignment'] ?? 'center');
    $backgroundColor = $props['background_color'] ?? '#d4af37';
    $textColor = $props['text_color'] ?? '#ffffff';
    $borderRadius = $props['border_radius'] ?? 8;
    $marginTop = $props['margin_top'] ?? 0;
    $marginBottom = $props['margin_bottom'] ?? 24;
    $elementId = $props['element_id'] ?? null;
    $customCss = $props['custom_css'] ?? '';
@endphp

@php
    $sizeClasses = [
        'small' => 'px-4 py-2 text-sm',
        'medium' => 'px-6 py-3 text-base',
        'large' => 'px-8 py-4 text-lg',
    ];

    $styleClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700',
        'outline' => 'border-2 border-current hover:bg-gray-100',
        'ghost' => 'hover:bg-gray-100',
    ];
@endphp

<section class="button-section-{{ $section->id }}"
    style="margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;">
    <div class="container mx-auto px-4">
        <div class="text-{{ $alignment }}">
            <a @if ($elementId) id="{{ $elementId }}" @endif href="{{ $url }}"
                class="{{ $sizeClasses[$size] ?? $sizeClasses['medium'] }} inline-block rounded-lg font-semibold transition"
                style="background-color: {{ $backgroundColor }}; color: {{ $textColor }}; border-radius: {{ $borderRadius }}px; {{ $customCss }}">
                {{ $text }}
            </a>
        </div>
    </div>
</section>
