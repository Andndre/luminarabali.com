@props(['props' => [], 'section' => null, 'page' => null, 'elements' => []])

@php
    $paddingTop = $props['padding_top'] ?? 60;
    $paddingBottom = $props['padding_bottom'] ?? 60;
    $paddingLeft = $props['padding_left'] ?? 20;
    $paddingRight = $props['padding_right'] ?? 20;
    $maxWidth = $props['max_width'] ?? 1200;
    $backgroundColor = $props['background_color'] ?? '#ffffff';
    $marginTop = $props['margin_top'] ?? 0;
    $marginBottom = $props['margin_bottom'] ?? 0;
    $marginLeft = $props['margin_left'] ?? 0;
    $marginRight = $props['margin_right'] ?? 0;
    $marginLeftMode = $props['margin_left_mode'] ?? 'px';
    $marginRightMode = $props['margin_right_mode'] ?? 'px';
    $borderWidth = $props['border_width'] ?? 0;
    $borderColor = $props['border_color'] ?? '#e5e7eb';
    $borderRadius = $props['border_radius'] ?? 0;
    $shadow = $props['shadow'] ?? 'none';

    $shadowMap = [
        'none' => 'none',
        'sm' => '0 1px 2px rgba(0,0,0,0.08)',
        'md' => '0 8px 24px rgba(0,0,0,0.12)',
        'lg' => '0 14px 34px rgba(0,0,0,0.16)',
    ];
    $boxShadow = $shadowMap[$shadow] ?? 'none';

    $marginLeftValue = $marginLeftMode === 'auto' ? 'auto' : $marginLeft . 'px';
    $marginRightValue = $marginRightMode === 'auto' ? 'auto' : $marginRight . 'px';
@endphp

<div class="section-one-col"
    style="
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
    padding-left: {{ $paddingLeft }}px;
    padding-right: {{ $paddingRight }}px;
    max-width: {{ $maxWidth }}px;
    margin-left: {{ $marginLeftValue }};
    margin-right: {{ $marginRightValue }};
    margin-top: {{ $marginTop }}px;
    margin-bottom: {{ $marginBottom }}px;
    background-color: {{ $backgroundColor }};
    border: {{ $borderWidth }}px solid {{ $borderColor }};
    border-radius: {{ $borderRadius }}px;
    box-shadow: {{ $boxShadow }};
">
    @foreach ($elements as $element)
        @if (file_exists(resource_path("views/templates/components/{$element->section_type}.blade.php")))
            @include("templates.components.{$element->section_type}", [
                'props' => $element->props ?? [],
                'section' => $element,
                'page' => $page,
            ])
        @endif
    @endforeach
</div>
