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

    $textAlignOptions = ['left', 'center', 'right'];
    $groomTextAlign = in_array($props['groom_text_align'] ?? null, $textAlignOptions, true) ? $props['groom_text_align'] : 'left';
    $brideTextAlign = in_array($props['bride_text_align'] ?? null, $textAlignOptions, true) ? $props['bride_text_align'] : 'right';
    $paddingTop = $props['padding_top'] ?? 64;
    $paddingBottom = $props['padding_bottom'] ?? 64;

    $people = [
        ['name' => $page->groom_name ?? 'Mempelai Pria', 'photo' => $resolvePath($props['groom_photo'] ?? null), 'parents' => $groomParents, 'instagram' => $groomInstagram, 'align' => $groomTextAlign, 'label' => $props['groom_label'] ?? 'Mempelai Pria', 'role' => 'Groom'],
        ['name' => $page->bride_name ?? 'Mempelai Wanita', 'photo' => $resolvePath($props['bride_photo'] ?? null), 'parents' => $brideParents, 'instagram' => $brideInstagram, 'align' => $brideTextAlign, 'label' => $props['bride_label'] ?? 'Mempelai Wanita', 'role' => 'Bride'],
    ];

    $igSvg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>';
@endphp

<section class="couple couple--{{ $variant }}" style="padding: {{ $paddingTop }}px 20px {{ $paddingBottom }}px;">
  <div class="couple-inner">
    @if ($heading)
      <h2 class="section-heading couple-heading"
          data-editable="heading">{{ $heading }}</h2>
    @endif

    @if ($variant === 'portrait-overlay')
      <div class="couple-portraits">
        @foreach ($people as $i => $person)
          <div class="couple-portrait couple-portrait--{{ $person['align'] }}">
            @if ($person['photo'])
              <img class="couple-portrait-img" src="{{ $person['photo'] }}" alt="{{ $person['name'] }}" loading="lazy">
            @endif
            <div class="couple-portrait-overlay"></div>
            <div class="couple-portrait-text">
              <p class="couple-portrait-eyebrow" data-reveal>{{ $person['label'] }}</p>
              <h3 class="couple-portrait-name" data-reveal style="font-family: var(--font-heading, serif);">{{ $person['name'] }}</h3>
              @if ($person['parents'])<p class="couple-portrait-sub" data-reveal>{{ $person['parents'] }}</p>@endif
              @if ($person['instagram'])
                <a class="couple-portrait-ig" data-reveal href="{{ $person['instagram'] }}" target="_blank" rel="noopener">{!! $igSvg !!} Instagram</a>
              @endif
            </div>
          </div>
        @endforeach
      </div>

    @elseif ($variant === 'side-alternating')
      <div class="couple-list">
        @foreach ($people as $i => $person)
          <div class="couple-row {{ $i % 2 ? 'is-rev' : '' }}">
            @if ($person['photo'])
              <img class="couple-photo-rect" src="{{ $person['photo'] }}" alt="{{ $person['name'] }}">
            @endif
            <div class="couple-meta">
              <p class="couple-eyebrow">{{ $person['label'] }}</p>
              <h3 class="couple-name" style="font-family: var(--font-heading, serif); font-size: var(--step-xl, 26px);">{{ $person['name'] }}</h3>
              @if ($person['parents'])<p class="couple-sub">{{ $person['parents'] }}</p>@endif
              @if ($person['instagram'])<a class="couple-ig" href="{{ $person['instagram'] }}" target="_blank" rel="noopener">{!! $igSvg !!}Instagram</a>@endif
            </div>
          </div>
        @endforeach
      </div>

    @elseif ($variant === 'arch-pair')
      <div class="couple-arches">
        @foreach ($people as $i => $person)
          <div class="couple-arch">
            @if ($person['photo'])
              <img class="couple-arch-img" src="{{ $person['photo'] }}" alt="{{ $person['name'] }}" loading="lazy">
            @endif
            <p class="couple-eyebrow">{{ $person['label'] }}</p>
            <h3 class="couple-name" style="font-family: var(--font-heading, serif); font-size: var(--step-xl, 26px);">{{ $person['name'] }}</h3>
            @if ($person['parents'])<p class="couple-sub">{{ $person['parents'] }}</p>@endif
            @if ($person['instagram'])<a class="couple-ig" href="{{ $person['instagram'] }}" target="_blank" rel="noopener">{!! $igSvg !!}Instagram</a>@endif
          </div>
        @endforeach
      </div>

    @elseif ($variant === 'label-vertical')
      <div class="couple-labels">
        @foreach ($people as $i => $person)
          <div class="couple-label-fig">
            <div class="couple-label-photo">
              @if ($person['photo'])
                <img class="couple-label-img" src="{{ $person['photo'] }}" alt="{{ $person['name'] }}" loading="lazy">
              @endif
              <div class="couple-label-side" aria-hidden="true"><span class="couple-label-the">The</span><span class="couple-label-role">{{ $person['role'] }}</span></div>
            </div>
            <div class="couple-label-info">
              <h3 class="couple-name couple-label-name" style="font-family: var(--font-heading, serif);">{{ $person['name'] }}</h3>
              <div class="couple-label-divide" aria-hidden="true"></div>
              @if ($person['parents'])<p class="couple-sub">{{ $person['parents'] }}</p>@endif
              @if ($person['instagram'])<a class="couple-ig" href="{{ $person['instagram'] }}" target="_blank" rel="noopener">{!! $igSvg !!}Instagram</a>@endif
            </div>
          </div>
        @endforeach
      </div>

    @else
      {{-- centered-stacked & centered-portrait: grid + "&"; bentuk foto (bulat / kapsul)
           dibedakan lewat CSS per varian, markupnya sama. --}}
      <div class="couple-grid">
        @foreach ($people as $i => $person)
          @if ($i > 0)<div class="couple-amp" aria-hidden="true" style="font-family: var(--font-heading, serif);">&amp;</div>@endif
          <div class="couple-card">
            @if ($person['photo'])
              <img class="couple-photo-round" src="{{ $person['photo'] }}" alt="{{ $person['name'] }}">
            @endif
            <p class="couple-eyebrow">{{ $person['label'] }}</p>
            <h3 class="couple-name" style="font-family: var(--font-heading, serif); font-size: var(--step-xl, 26px);">{{ $person['name'] }}</h3>
            @if ($person['parents'])<p class="couple-sub">{{ $person['parents'] }}</p>@endif
            @if ($person['instagram'])<a class="couple-ig" href="{{ $person['instagram'] }}" target="_blank" rel="noopener">{!! $igSvg !!}Instagram</a>@endif
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
