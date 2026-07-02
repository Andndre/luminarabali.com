@props(['props' => [], 'section' => null, 'page' => null])

@php
$props = $props ?? [];
$backgroundValue = $props['background_image'] ?? null;
$overlayEnabled = $props['overlay_enabled'] ?? false;
$overlayColor = $props['overlay_color'] ?? '#000000';
$overlayOpacity = ($props['overlay_opacity'] ?? 40) / 100;

$title = $props['title'] ?? 'The Wedding Of';
$groomName = $page->groom_name ?? 'Groom';
$brideName = $page->bride_name ?? 'Bride';
$eventDate = $page->event_date ?? null;
$fontFamily = $props['font_family'] ?? 'Playfair Display';
$textColor = $props['text_color'] ?? '#ffffff';
$alignment = $props['alignment'] ?? 'center';
$paddingTop = $props['padding_top'] ?? 120;
$paddingBottom = $props['padding_bottom'] ?? 120;
@endphp

<style>
  .hero-section-{{ $section->id ?? 'default' }} {
    background-image: url('{{ $backgroundValue ? '/storage/' . $backgroundValue : '' }}');
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
      background: {{ $overlayColor }};
      opacity: {{ $overlayOpacity }};
    @endif
  }

  .hero-section-{{ $section->id ?? 'default' }} .hero-content {
    position: relative;
    z-index: 10;
  }

  .hero-section-{{ $section->id ?? 'default' }} .hero-title {
    font-family: {{ $fontFamily }}, serif;
    color: {{ $textColor }};
  }

  .hero-section-{{ $section->id ?? 'default' }} .hero-names {
    font-family: {{ $fontFamily }}, serif;
    color: {{ $textColor }};
  }

  .hero-section-{{ $section->id ?? 'default' }} .hero-date {
    font-family: {{ $fontFamily }}, serif;
    color: {{ $textColor }};
  }
</style>

<section class="hero-section-{{ $section->id ?? 'default' }}">
  <div class="hero-content text-{{ $alignment }} px-4">
    @if($title)
      <p class="hero-title text-lg md:text-xl mb-4">{{ $title }}</p>
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
