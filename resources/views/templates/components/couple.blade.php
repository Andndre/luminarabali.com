@props(['props' => [], 'section' => null, 'page' => null])

@php
    $variant = $props['variant'] ?? 'centered-stacked';
    $heading = $props['heading'] ?? 'Mempelai';
    $groomParents = $props['groom_parents'] ?? '';
    $brideParents = $props['bride_parents'] ?? '';
    $groomInstagram = $props['groom_instagram'] ?? null;
    $brideInstagram = $props['bride_instagram'] ?? null;

    $resolvePath = function ($src) {
        if (empty($src)) return null;
        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src : '/storage/' . ltrim($src, '/');
    };

    $people = [
        ['name' => $page->groom_name ?? 'Mempelai Pria', 'photo' => $resolvePath($props['groom_photo'] ?? null), 'parents' => $groomParents, 'instagram' => $groomInstagram],
        ['name' => $page->bride_name ?? 'Mempelai Wanita', 'photo' => $resolvePath($props['bride_photo'] ?? null), 'parents' => $brideParents, 'instagram' => $brideInstagram],
    ];
@endphp

<section class="couple couple--{{ $variant }}" style="padding: var(--section-y, 64px) 20px;">
  <div class="couple-inner">
    <h2 class="couple-heading" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px); color: var(--color-accent, #b5654d);"
        data-editable="heading">{{ $heading }}</h2>

    @if ($variant === 'side-alternating')
      <div class="couple-list">
        @foreach ($people as $i => $person)
          <div class="couple-row {{ $i % 2 ? 'is-rev' : '' }}">
            @if ($person['photo'])
              <img class="couple-photo-rect" src="{{ $person['photo'] }}" alt="{{ $person['name'] }}">
            @endif
            <div class="couple-meta">
              <h3 style="font-family: var(--font-heading, serif); font-size: var(--step-xl, 26px);">{{ $person['name'] }}</h3>
              @if ($person['parents'])<p class="couple-sub">{{ $person['parents'] }}</p>@endif
              @if ($person['instagram'])<a class="couple-ig" href="{{ $person['instagram'] }}" target="_blank" rel="noopener" style="color: var(--color-accent, #b5654d);">Instagram</a>@endif
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="couple-grid">
        @foreach ($people as $person)
          <div class="couple-card">
            @if ($person['photo'])
              <img class="couple-photo-round" src="{{ $person['photo'] }}" alt="{{ $person['name'] }}"
                   style="border: 3px solid var(--color-accent, #b5654d);">
            @endif
            <h3 style="font-family: var(--font-heading, serif); font-size: var(--step-xl, 26px);">{{ $person['name'] }}</h3>
            @if ($person['parents'])<p class="couple-sub">{{ $person['parents'] }}</p>@endif
            @if ($person['instagram'])<a class="couple-ig" href="{{ $person['instagram'] }}" target="_blank" rel="noopener" style="color: var(--color-accent, #b5654d);">Instagram</a>@endif
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
