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
@endphp

<div class="section-one-col" style="
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
    padding-left: {{ $paddingLeft }}px;
    padding-right: {{ $paddingRight }}px;
    max-width: {{ $maxWidth }}px;
    margin-left: auto;
    margin-right: auto;
    margin-top: {{ $marginTop }}px;
    margin-bottom: {{ $marginBottom }}px;
    background-color: {{ $backgroundColor }};
">
    @foreach($elements as $element)
        @if(file_exists(resource_path("views/templates/components/{$element->section_type}.blade.php")))
            @include("templates.components.{$element->section_type}", [
                'props' => $element->props ?? [],
                'section' => $element,
                'page' => $page
            ])
        @endif
    @endforeach
</div>
