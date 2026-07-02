@foreach ($sections as $section)
    @php($viewPath = "templates.components.{$section->section_type}")
    @if (view()->exists($viewPath))
        @include($viewPath, [
            'props' => $section->props ?? [],
            'section' => $section,
            'page' => $page,
            'elements' => $byParent->get($section->id, collect())->all(),
        ])
    @else
        @php(\Illuminate\Support\Facades\Log::warning("Invitation component view not found: {$section->section_type}", ['section_id' => $section->id]))
        <!-- Component {{ $section->section_type }} not found -->
    @endif
@endforeach
