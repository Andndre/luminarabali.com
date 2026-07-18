@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Kisah Kami';
    $stories = $props['stories'] ?? [];

    $resolvePath = function ($src) {
        if (empty($src)) return null;
        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : '/storage/' . ltrim($src, '/');
    };
@endphp

<section style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-2xl">
    <h2 class="mb-10 text-center" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);"
      data-editable="heading">
      {{ $heading }}
    </h2>
    <div class="space-y-8">
      @foreach($stories as $story)
        @php($photo = $resolvePath($story['photo'] ?? null))
        <div class="flex gap-4 items-start">
          @if($photo)
            <img src="{{ $photo }}" alt="{{ $story['title'] ?? '' }}"
                class="w-20 h-20 object-cover shrink-0"
                style="border-radius: var(--radius, 12px); border: 2px solid var(--color-accent, #b5654d);">
          @endif
          <div class="flex-1">
            @if(!empty($story['year']))
              <span class="text-sm font-semibold opacity-70">{{ $story['year'] }}</span>
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
