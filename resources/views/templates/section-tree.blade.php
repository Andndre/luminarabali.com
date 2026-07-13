@foreach ($sections as $section)
    @include('templates._section-shell', [
        'section' => $section,
        'page' => $page,
        'elements' => $byParent->get($section->id, collect())->all(),
    ])
@endforeach
