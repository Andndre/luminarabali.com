@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Kisah Kami';
    $subheading = $props['subheading'] ?? '';
    $variant = $props['variant'] ?? 'marginalia';
    $stories = $props['stories'] ?? [];

    $resolvePath = function ($src) {
        if (empty($src)) return null;
        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : '/storage/' . ltrim($src, '/');
    };

    // Satu foto untuk seluruh section, di atas daftar. Opsional.
    $image = $resolvePath($props['image'] ?? null);
@endphp

<section class="story story--{{ $variant }}" style="padding: var(--section-y, 64px) 16px;">
  <div class="container mx-auto max-w-xl">
    <div class="text-center">
      <h2 class="section-heading" data-editable="heading">{{ $heading }}</h2>
      @if($subheading)<p class="section-subheading">{{ $subheading }}</p>@endif
    </div>

    @if($image)
      <div class="story-cover">
        <img src="{{ $image }}" alt="{{ $heading }}" loading="lazy">
      </div>
    @endif

    <div class="story-list">
      @foreach($stories as $story)
        <div class="story-item">
          @if(!empty($story['year']))<p class="story-year">{{ $story['year'] }}</p>@endif
          @if(!empty($story['title']))<h3 class="story-title">{{ $story['title'] }}</h3>@endif
          @if(!empty($story['story']))<p class="story-text">{{ $story['story'] }}</p>@endif
        </div>
      @endforeach
    </div>
  </div>
</section>
