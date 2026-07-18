@props(['props' => [], 'section' => null, 'page' => null])

@php
    $src = $props['src'] ?? '';
    $alt = $props['alt'] ?? '';
    $width = $props['width'] ?? 100;
    $borderRadius = $props['border_radius'] ?? 0;
    $borderWidth = $props['border_width'] ?? 0;
    $shadow = $props['shadow'] ?? 'none';
    $alignment = $props['align'] ?? ($props['alignment'] ?? 'center');
    $marginTop = $props['margin_top'] ?? 0;
    $marginBottom = $props['margin_bottom'] ?? 24;
    $elementId = $props['element_id'] ?? null;
    $customCss = $props['custom_css'] ?? '';

    if (!empty($src) && !\Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])) {
        $src = '/storage/' . ltrim($src, '/');
    }

    $shadowMap = [
        'none' => 'none',
        'sm' => '0 1px 2px rgba(0,0,0,0.08)',
        'md' => '0 8px 24px rgba(0,0,0,0.12)',
        'lg' => '0 14px 34px rgba(0,0,0,0.16)',
    ];
    $boxShadow = $shadowMap[$shadow] ?? 'none';
    // Border tanpa warna sendiri: derivasi dari token teks, bukan hex bebas.
    $border = $borderWidth > 0
        ? "{$borderWidth}px solid color-mix(in srgb, var(--color-text, #2b2b2b) 15%, transparent)"
        : 'none';
@endphp

<section class="image-section-{{ $section->id }}"
    style="margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;">
    <div class="container mx-auto px-4">
        <div class="text-{{ $alignment }}">
            <img @if ($elementId) id="{{ $elementId }}" @endif src="{{ $src }}"
                alt="{{ $alt }}"
                style="width: {{ $width }}%; border-radius: {{ $borderRadius }}px; border: {{ $border }}; box-shadow: {{ $boxShadow }}; {{ $customCss }}"
                class="inline-block" loading="lazy">
        </div>
    </div>
</section>
