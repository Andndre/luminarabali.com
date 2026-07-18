{{-- Satu-satunya penghasil wrapper [data-section-id]: dipakai section-tree (publik + studio)
     dan render-section (fragment swap). Shell "berat" (relative + ornamen + CSS scoped +
     atribut animasi) hanya dirender bila perlu; selain itu wrapper display:contents lama. --}}
@props(['section' => null, 'page' => null, 'elements' => []])

@php
    $viewPath = "templates.components.{$section->section_type}";
    $props = $section->props ?? [];

    $animation = $props['animation'] ?? 'none';
    $animationDelay = (int) ($props['animation_delay'] ?? 0);
    $customCss = trim((string) ($section->custom_css ?? ''));

    $resolveOrnament = function ($src) {
        if (empty($src)) return null;
        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : '/storage/' . ltrim($src, '/');
    };
    $ornamentTop = $resolveOrnament($props['ornament_top'] ?? null);
    $ornamentBottom = $resolveOrnament($props['ornament_bottom'] ?? null);

    // Cover punya sistem visual sendiri (gate position:fixed + layar sticky) dan
    // background_image sendiri. Treatment shell menimpa positioning anak-anaknya
    // (.sec-treat--image/pinned > :not(.sec-bg) { position: relative }) sehingga
    // gate berhenti full-viewport dan terklip overflow:hidden — jadi cover
    // dikecualikan dari treatment/bg_effect sepenuhnya.
    $ownsVisual = $section->section_type === 'cover';

    $treatment = $ownsVisual ? 'surface' : ($props['treatment'] ?? 'surface');
    $bgImage = $ownsVisual ? null : $resolveOrnament($props['bg_image'] ?? null); // reuse resolver path
    $bgOverlay = max(0, min(100, (int) ($props['bg_overlay'] ?? 45)));
    $bgEffect = $ownsVisual ? 'none' : ($props['bg_effect'] ?? 'none');
    $bgStrength = max(100, min(200, (int) ($props['bg_effect_strength'] ?? 130)));
    $hasTreatment = $treatment !== 'surface' || $bgImage;

    $ornamentStyle = function (string $position, $scale, string $edge) {
        $width = is_numeric($scale) ? (float) $scale : 100;
        $style = "position:absolute;{$edge}:0;pointer-events:none;z-index:10;";
        return match ($position) {
            'full-width' => $style . 'left:0;right:0;width:100%;',
            'center' => $style . "left:50%;transform:translateX(-50%);width:{$width}%;",
            'corner-tr', 'corner-br' => $style . "right:0;width:{$width}%;",
            default => $style . "left:0;width:{$width}%;", // corner-tl / corner-bl
        };
    };

    $needsShell = $ornamentTop || $ornamentBottom || $animation !== 'none' || $customCss !== '' || $hasTreatment;
@endphp

@if (!view()->exists($viewPath))
    @php(\Illuminate\Support\Facades\Log::warning("Invitation component view not found: {$section->section_type}", ['section_id' => $section->id]))
    <!-- Component {{ $section->section_type }} not found -->
@elseif (!$needsShell)
    <div style="display: contents" data-section-id="{{ $section->id }}">
        @include($viewPath, [
            'props' => $props,
            'section' => $section,
            'page' => $page,
            'elements' => $elements,
        ])
    </div>
@else
    <div class="sec-treat sec-treat--{{ $treatment }}{{ $bgEffect === 'pinned' ? ' sec-treat--pinned' : '' }}" style="position: relative; overflow: hidden" data-section-id="{{ $section->id }}"
        @if ($animation !== 'none') data-animate="{{ $animation }}" data-animate-delay="{{ $animationDelay }}" @endif>
        @if ($hasTreatment && $treatment === 'image' && $bgImage)
            <div class="sec-bg" aria-hidden="true"
                @if ($bgEffect !== 'none') data-effect="{{ $bgEffect }}" data-strength="{{ $bgStrength }}" @endif>
                <div class="sec-bg-img" style="background-image:url('{{ $bgImage }}')"></div>
                <div class="sec-bg-overlay" style="opacity:{{ rtrim(rtrim(number_format($bgOverlay/100, 2, '.', ''), '0'), '.') }}"></div>
            </div>
        @endif
        @if ($customCss !== '')
            {{-- Scoping via CSS nesting native — server yang membungkus (proposal §5.4);
                 updateSection menolak payload dengan <> sehingga tag tidak bisa ditutup. --}}
            <style>[data-section-id="{{ $section->id }}"] { {!! $customCss !!} }</style>
        @endif
        @if ($ornamentTop)
            <img src="{{ $ornamentTop }}" alt=""
                style="{{ $ornamentStyle($props['ornament_top_position'] ?? 'corner-tl', $props['ornament_top_scale'] ?? 100, 'top') }}">
        @endif
        @include($viewPath, [
            'props' => $props,
            'section' => $section,
            'page' => $page,
            'elements' => $elements,
        ])
        @if ($ornamentBottom)
            <img src="{{ $ornamentBottom }}" alt=""
                style="{{ $ornamentStyle($props['ornament_bottom_position'] ?? 'corner-bl', $props['ornament_bottom_scale'] ?? 100, 'bottom') }}">
        @endif
    </div>
@endif
