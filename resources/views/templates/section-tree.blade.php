@php $coverFirst = $sections->first()?->section_type === 'cover'; @endphp
@if ($coverFirst)
    @include('templates._section-shell', ['section' => $sections->first(), 'page' => $page, 'elements' => $byParent->get($sections->first()->id, collect())->all()])
    <div class="invite-content">
        @foreach ($sections->slice(1) as $section)
            @include('templates._section-shell', ['section' => $section, 'page' => $page, 'elements' => $byParent->get($section->id, collect())->all()])
        @endforeach
    </div>
@else
    <div class="invite-content">
        @foreach ($sections as $section)
            @include('templates._section-shell', ['section' => $section, 'page' => $page, 'elements' => $byParent->get($section->id, collect())->all()])
        @endforeach
    </div>
@endif
