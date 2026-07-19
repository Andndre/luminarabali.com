@props(['props' => [], 'section' => null, 'page' => null])

@php
    $text = $props['text'] ?? 'Click Me';
    $url = $props['url'] ?? '#';
    $size = $props['size'] ?? 'medium';
    $alignment = $props['align'] ?? ($props['alignment'] ?? 'center');
    $borderWidth = $props['border_width'] ?? 0;
    $shadow = $props['shadow'] ?? 'none';
    $marginTop = $props['margin_top'] ?? 0;
    $marginBottom = $props['margin_bottom'] ?? 24;
    $elementId = $props['element_id'] ?? null;
    $customCss = $props['custom_css'] ?? '';

    // Nama varian lama masih tersimpan di baris yang dibuat sebelum set ini ada.
    $variant = $props['variant'] ?? ($props['style'] ?? 'solid');
    $variant = [
        'primary' => 'solid',
        'secondary' => 'soft',
        'ghost' => 'link',
    ][$variant] ?? $variant;
    $known = ['solid', 'soft', 'outline', 'round', 'link', 'rule'];
    if (! in_array($variant, $known, true)) {
        $variant = 'solid';
    }

    $sizeClasses = [
        'small' => 'px-4 py-2 text-sm',
        'medium' => 'px-6 py-3 text-base',
        'large' => 'px-8 py-4 text-lg',
    ];

    $boxShadow = [
        'none' => 'none',
        'sm' => '0 1px 2px rgba(0,0,0,0.08)',
        'md' => '0 8px 24px rgba(0,0,0,0.12)',
        'lg' => '0 14px 34px rgba(0,0,0,0.16)',
    ][$shadow] ?? 'none';

    // Bentuk round dan rule adalah identitas varian: radius manual tidak boleh mengubahnya
    // jadi kotak biasa atau justru membulatkan garis atas-bawah.
    $shapeIsFixed = in_array($variant, ['round', 'rule'], true);
    $radiusOverride = ! $shapeIsFixed && isset($props['border_radius'])
        ? 'border-radius: '.(int) $props['border_radius'].'px;'
        : '';

    // border_width > 0 = override eksplisit ketebalan bawaan varian. Varian tanpa kotak
    // dikecualikan — memberi mereka border penuh merusak bentuknya.
    $borderOverride = $borderWidth > 0 && ! in_array($variant, ['link', 'rule'], true)
        ? "border-width: {$borderWidth}px; border-style: solid;"
        : '';
@endphp

<section class="button-section-{{ $section->id }}"
    style="margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;">
    <div class="text-{{ $alignment }}">
        <a @if ($elementId) id="{{ $elementId }}" @endif href="{{ $url }}"
            class="btn btn-{{ $variant }} {{ $sizeClasses[$size] ?? $sizeClasses['medium'] }}"
            style="box-shadow: {{ $boxShadow }}; {{ $radiusOverride }} {{ $borderOverride }} {{ $customCss }}">
            {{ $text }}
        </a>
    </div>
</section>
