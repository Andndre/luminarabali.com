@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Kisah Kami';
    $stories = $props['stories'] ?? [];
    $accentColor = $props['accent_color'] ?? 'var(--color-accent, #b5654d)';
    $textColor = $props['text_color'] ?? 'var(--color-text, #212529)';

    $resolvePath = function ($src) {
        if (empty($src)) return null;
        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : '/storage/' . ltrim($src, '/');
    };
@endphp

<section style="color: {{ $textColor }}; padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-2xl">
    <h2 class="text-2xl md:text-3xl font-bold mb-10 text-center" style="font-family: var(--font-heading, serif); color: {{ $accentColor }};"
      data-editable="heading">
      {{ $heading }}
    </h2>
    <div class="space-y-8">
      @foreach($stories as $story)
        @php($photo = $resolvePath($story['photo'] ?? null))
        <div class="flex gap-4 items-start">
          @if($photo)
            <img src="{{ $photo }}" alt="{{ $story['title'] ?? '' }}"
                class="w-20 h-20 rounded-lg object-cover shrink-0" style="border: 2px solid {{ $accentColor }};">
          @endif
          <div class="flex-1">
            @if(!empty($story['year']))
              <span class="text-sm font-semibold" style="color: {{ $accentColor }};">{{ $story['year'] }}</span>
            @endif
            @if(!empty($story['title']))
              <h3 class="text-lg font-semibold" style="font-family: var(--font-heading, serif);">{{ $story['title'] }}</h3>
            @endif
            @if(!empty($story['story']))
              <p class="mt-1 text-sm opacity-80">{{ $story['story'] }}</p>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
