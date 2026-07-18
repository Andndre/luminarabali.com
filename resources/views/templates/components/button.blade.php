@props(['props' => [], 'section' => null, 'page' => null])

@php
    $text = $props['text'] ?? 'Click Me';
    $url = $props['url'] ?? '#';
    $variant = $props['variant'] ?? ($props['style'] ?? 'primary');
    $size = $props['size'] ?? 'medium';
    $alignment = $props['align'] ?? ($props['alignment'] ?? 'center');
    $borderRadius = $props['border_radius'] ?? 8;
    $borderWidth = $props['border_width'] ?? 0;
    $shadow = $props['shadow'] ?? 'none';
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

    $variantClasses = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'outline' => 'btn-outline',
        'ghost' => 'btn-ghost',
    ];

    $shadowMap = [
        'none' => 'none',
        'sm' => '0 1px 2px rgba(0,0,0,0.08)',
        'md' => '0 8px 24px rgba(0,0,0,0.12)',
        'lg' => '0 14px 34px rgba(0,0,0,0.16)',
    ];
    $boxShadow = $shadowMap[$shadow] ?? 'none';
    // border_width > 0 = override eksplisit ketebalan bawaan varian.
    $borderOverride = $borderWidth > 0 ? "border-width: {$borderWidth}px; border-style: solid;" : '';
@endphp

<section class="button-section-{{ $section->id }}"
    style="margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;">
    <div class="container mx-auto px-4">
        <div class="text-{{ $alignment }}">
            <a @if ($elementId) id="{{ $elementId }}" @endif href="{{ $url }}"
                class="{{ $sizeClasses[$size] ?? $sizeClasses['medium'] }} {{ $variantClasses[$variant] ?? $variantClasses['primary'] }} inline-block font-semibold transition hover:opacity-90"
                style="border-radius: {{ $borderRadius }}px; box-shadow: {{ $boxShadow }}; {{ $borderOverride }} {{ $customCss }}">
                {{ $text }}
            </a>
        </div>
    </div>
</section>
