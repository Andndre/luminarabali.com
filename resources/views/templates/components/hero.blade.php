@props(['props' => [], 'section' => null, 'page' => null])

@php
$props = $props ?? [];
$backgroundValue = $props['background_image'] ?? null;
// http(s)/absolut dipakai apa adanya; path relatif → /storage/ (konsisten dgn cover/couple).
$heroBg = $backgroundValue
    ? (\Illuminate\Support\Str::startsWith($backgroundValue, ['http://', 'https://', '/']) ? $backgroundValue : '/storage/' . $backgroundValue)
    : '';
$overlayEnabled = $props['overlay_enabled'] ?? false;
$overlayOpacityPercent = is_numeric($props['overlay_opacity'] ?? null) ? (float) $props['overlay_opacity'] : 40.0;
$overlayOpacity = max(0, min(1, $overlayOpacityPercent / 100));

$title = $props['title'] ?? 'The Wedding Of';
$groomName = $page->groom_name ?? 'Groom';
$brideName = $page->bride_name ?? 'Bride';
$eventDate = $page->event_date ?? null;
$fontFamily = 'var(--font-heading, serif)';
$alignment = $props['alignment'] ?? 'center';
$paddingTop = $props['padding_top'] ?? 120;
$paddingBottom = $props['padding_bottom'] ?? 120;
$variant = $props['variant'] ?? 'fullscreen';
@endphp

<style>
  .hero-section-{{ $section->id ?? 'default' }} {
    background-image: url('{{ $heroBg }}');
    background-size: cover;
    background-position: center;
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .hero-section-{{ $section->id ?? 'default' }}::before {
    content: '';
    position: absolute;
    inset: 0;
    @if($overlayEnabled)
      background: var(--color-ink, #20302a);
      opacity: {{ $overlayOpacity }};
    @endif
  }

  .hero-section-{{ $section->id ?? 'default' }} .hero-content {
    position: relative;
    z-index: 10;
  }

  .hero-section-{{ $section->id ?? 'default' }} .hero-title,
  .hero-section-{{ $section->id ?? 'default' }} .hero-names,
  .hero-section-{{ $section->id ?? 'default' }} .hero-date {
    font-family: {{ $fontFamily }};
    color: var(--color-on_dark, #f5f1e8);
  }
</style>

<section class="hero-section-{{ $section->id ?? 'default' }} hero--{{ $variant }}">
  <div class="hero-content text-{{ $alignment }} px-4">
    @if($title)
      <p class="hero-title text-lg md:text-xl mb-4" data-editable="title">{{ $title }}</p>
    @endif

    <h1 class="hero-names text-4xl md:text-6xl lg:text-7xl font-bold mb-4">
      {{ $groomName }} & {{ $brideName }}
    </h1>

    @if($eventDate)
      <p class="hero-date text-xl md:text-2xl lg:text-3xl">
        {!! \Illuminate\Support\Str::ucfirst(\Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y')) !!}
      </p>
    @endif
  </div>
</section>
