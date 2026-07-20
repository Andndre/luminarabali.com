@props(['props' => [], 'section' => null, 'page' => null])

@php
$props = $props ?? [];
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
  /* Hanya padding per-section. Foto latar dan overlay milik sistem treatment
     (_section-shell: .sec-bg-img + .sec-bg-overlay) — hero tak menggambar latarnya
     sendiri lagi, jadi satu foto dan satu overlay diatur di satu tempat.
     Layout (display/tinggi/perataan) milik .hero--{variant} di invitation.css. */
  .hero-section-{{ $section->id ?? 'default' }} {
@if($variant !== 'split')
    /* split: panel menempel rapat ke bawah, jadi padding section akan merusaknya —
       panelnya punya padding sendiri di .hero--split .hero-content. */
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
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
