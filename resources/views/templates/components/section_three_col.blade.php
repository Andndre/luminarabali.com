@props(['props' => [], 'section' => null, 'page' => null, 'elements' => []])

@php
    $paddingTop = $props['padding_top'] ?? 60;
    $paddingBottom = $props['padding_bottom'] ?? 60;
    $paddingLeft = $props['padding_left'] ?? 20;
    $paddingRight = $props['padding_right'] ?? 20;
    $maxWidth = $props['max_width'] ?? 1200;
    $columnGap = $props['column_gap'] ?? 20;
    $backgroundColor = $props['background_color'] ?? '#ffffff';
    $marginTop = $props['margin_top'] ?? 0;
    $marginBottom = $props['margin_bottom'] ?? 0;
    $verticalAlign = $props['vertical_align'] ?? 'top';
    $alignItems = $verticalAlign === 'center' ? 'center' : ($verticalAlign === 'bottom' ? 'flex-end' : 'flex-start');

    // Map elements to columns by props.column_index (editor source of truth), fallback to order_index
    $column1Elements = collect($elements)->filter(function ($element) {
        return (int) data_get($element->props ?? [], 'column_index', $element->order_index ?? 0) === 0;
    });
    $column2Elements = collect($elements)->filter(function ($element) {
        return (int) data_get($element->props ?? [], 'column_index', $element->order_index ?? 0) === 1;
    });
    $column3Elements = collect($elements)->filter(function ($element) {
        return (int) data_get($element->props ?? [], 'column_index', $element->order_index ?? 0) === 2;
    });
@endphp

<div class="section-three-col"
    style="
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
    padding-left: {{ $paddingLeft }}px;
    padding-right: {{ $paddingRight }}px;
    max-width: {{ $maxWidth }}px;
    margin-left: auto;
    margin-right: auto;
    margin-top: {{ $marginTop }}px;
    margin-bottom: {{ $marginBottom }}px;
    background-color: {{ $backgroundColor }};; align-items: {{ $alignItems }}
">
    <div class="flex" style="gap: {{ $columnGap }}px">
        <!-- Column 1 -->
        <div style="width: 33.33%">
            @foreach ($column1Elements as $element)
                @if (file_exists(resource_path("views/templates/components/{$element->section_type}.blade.php")))
                    @include("templates.components.{$element->section_type}", [
                        'props' => $element->props ?? [],
                        'section' => $element,
                        'page' => $page,
                    ])
                @endif
            @endforeach
        </div>

        <!-- Column 2 -->
        <div style="width: 33.33%">
            @foreach ($column2Elements as $element)
                @if (file_exists(resource_path("views/templates/components/{$element->section_type}.blade.php")))
                    @include("templates.components.{$element->section_type}", [
                        'props' => $element->props ?? [],
                        'section' => $element,
                        'page' => $page,
                    ])
                @endif
            @endforeach
        </div>

        <!-- Column 3 -->
        <div style="width: 33.33%">
            @foreach ($column3Elements as $element)
                @if (file_exists(resource_path("views/templates/components/{$element->section_type}.blade.php")))
                    @include("templates.components.{$element->section_type}", [
                        'props' => $element->props ?? [],
                        'section' => $element,
                        'page' => $page,
                    ])
                @endif
            @endforeach
        </div>
    </div>
</div>
