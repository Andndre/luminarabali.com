@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Mempelai';
    $groomParents = $props['groom_parents'] ?? '';
    $brideParents = $props['bride_parents'] ?? '';
    $groomInstagram = $props['groom_instagram'] ?? null;
    $brideInstagram = $props['bride_instagram'] ?? null;
    $backgroundColor = $props['background_color'] ?? 'var(--color-surface, #ffffff)';
    $textColor = $props['text_color'] ?? 'var(--color-text, #212529)';
    $accentColor = $props['accent_color'] ?? 'var(--color-accent, #d4af37)';

    $resolvePath = function ($src) {
        if (empty($src)) return null;
        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : '/storage/' . ltrim($src, '/');
    };
    $groomPhoto = $resolvePath($props['groom_photo'] ?? null);
    $bridePhoto = $resolvePath($props['bride_photo'] ?? null);

    $people = [
        ['name' => $page->groom_name ?? 'Mempelai Pria', 'photo' => $groomPhoto, 'parents' => $groomParents, 'instagram' => $groomInstagram],
        ['name' => $page->bride_name ?? 'Mempelai Wanita', 'photo' => $bridePhoto, 'parents' => $brideParents, 'instagram' => $brideInstagram],
    ];
@endphp

<section style="background: {{ $backgroundColor }}; color: {{ $textColor }}; padding: 64px 16px;">
  <div class="container mx-auto max-w-4xl text-center">
    <h2 class="text-2xl md:text-3xl font-bold mb-10" style="font-family: var(--font-heading, serif); color: {{ $accentColor }};"
      data-editable="heading">
      {{ $heading }}
    </h2>
    <div class="grid gap-10 @md:grid-cols-2">
      @foreach($people as $person)
        <div>
          @if($person['photo'])
            <img src="{{ $person['photo'] }}" alt="{{ $person['name'] }}"
                class="w-40 h-40 mx-auto rounded-full object-cover mb-4"
                style="border: 3px solid {{ $accentColor }};">
          @endif
          <h3 class="text-xl font-semibold" style="font-family: var(--font-heading, serif);">{{ $person['name'] }}</h3>
          @if($person['parents'])
            <p class="mt-2 text-sm opacity-80">{{ $person['parents'] }}</p>
          @endif
          @if($person['instagram'])
            <a href="{{ $person['instagram'] }}" target="_blank" rel="noopener"
                class="inline-block mt-2 text-sm underline" style="color: {{ $accentColor }};">Instagram</a>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
