@props(['props' => [], 'section' => null, 'page' => null, 'elements' => []])

@include('templates._container-box', [
    'props' => $props, 'section' => $section, 'page' => $page, 'elements' => $elements,
    'widths' => [100],
])
