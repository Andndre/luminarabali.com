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
$alignment = $props['alignment'] ?? 'center';
$paddingTop = $props['padding_top'] ?? 120;
$paddingBottom = $props['padding_bottom'] ?? 120;
$variant = $props['variant'] ?? 'fullscreen';
@endphp

<style>
  /* Hanya data per-section di sini. Layout (display/tinggi/perataan) milik kelas
     .hero--{variant} di invitation.css — kalau ditulis ulang di sini, blok <style>
     ini menang atas varian (spesifisitas sama, urutan belakangan) dan varian mati. */
  .hero-section-{{ $section->id ?? 'default' }} {
    background-image: url('{{ $heroBg }}');
    background-size: cover;
    background-position: center;
@if($variant !== 'split')
    /* split: panel menempel rapat ke bawah, jadi padding section akan merusaknya —
       panelnya punya padding sendiri di .hero--split .hero-content. */
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
@endif
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
</style>

<section class="hero-section hero-section-{{ $section->id ?? 'default' }} hero--{{ $variant }}">
  <div class="hero-content text-{{ $alignment }} px-4">
    @if($title)
      <p class="hero-title mb-4" data-editable="title">{{ $title }}</p>
    @endif

    <h1 class="hero-names mb-4">
      {{ $groomName }} &amp; {{ $brideName }}
    </h1>

    @if($eventDate)
      <p class="hero-date">
        {{ \Illuminate\Support\Str::ucfirst(\Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y')) }}
      </p>
    @endif
  </div>
</section>
