@props(['props' => [], 'section' => null, 'page' => null, 'elements' => []])

@php
    $paddingTop = $props['padding_top'] ?? 60;
    $paddingBottom = $props['padding_bottom'] ?? 60;
    $paddingLeft = $props['padding_left'] ?? 20;
    $paddingRight = $props['padding_right'] ?? 20;
    $maxWidth = $props['max_width'] ?? 1200;
    $columnGap = $props['column_gap'] ?? 20;
    $columnRatio = $props['column_ratio'] ?? '50-50';
    $verticalAlign = $props['vertical_align'] ?? 'top';
    $alignItems = $verticalAlign === 'center' ? 'center' : ($verticalAlign === 'bottom' ? 'flex-end' : 'flex-start');
    $backgroundColor = $props['background_color'] ?? '#ffffff';
    $marginTop = $props['margin_top'] ?? 0;
    $marginBottom = $props['margin_bottom'] ?? 0;

    // Parse column ratio
    $ratioParts = explode('-', $columnRatio);
    $col1Width = $ratioParts[0] ?? 50;
    $col2Width = $ratioParts[1] ?? 50;

    // Map elements to columns by props.column_index (editor source of truth), fallback to order_index
    $column1Elements = collect($elements)->filter(function ($element) {
        return (int) data_get($element->props ?? [], 'column_index', $element->order_index ?? 0) === 0;
    });
    $column2Elements = collect($elements)->filter(function ($element) {
        return (int) data_get($element->props ?? [], 'column_index', $element->order_index ?? 0) === 1;
    });
@endphp

<div class="section-two-col"
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
    background-color: {{ $backgroundColor }};
">
    <div class="flex" style="gap: {{ $columnGap }}px; align-items: {{ $alignItems }}">
        <!-- Column 1 -->
        <div style="width: {{ $col1Width }}%">
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
        <div style="width: {{ $col2Width }}%">
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
    </div>
</div>
