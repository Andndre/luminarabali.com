@props(['props' => [], 'section' => null, 'page' => null])

@php
$title = $props['title'] ?? 'The Wedding Of';
$groomName = $page->groom_name ?? 'Groom';
$brideName = $page->bride_name ?? 'Bride';
$eventDate = $page->event_date ?? null;
$buttonText = $props['button_text'] ?? 'Buka Undangan';
// Media latar memakai keputusan yang sama dengan section lain (BackgroundMedia), hanya
// markupnya yang beda: cover punya gate position:fixed, layar sticky, lapisan blur, dan
// jendela arch — semuanya di luar sistem treatment.
$media = \App\Services\BackgroundMedia::resolve($props);
$bgOverlay = max(0, min(100, (int) ($props['bg_overlay'] ?? 45)));
// Scrim cover memakai gradien tetap (0.22..0.66); opasitasnya diskalakan bg_overlay
// dengan 45 (bawaan) = 1.0, jadi nilai bawaan tampil persis seperti sebelum overlay
// bisa diatur. Di atas 45 memang tak bertambah gelap — gradiennya sudah penuh.
$scrimOpacity = rtrim(rtrim(number_format(min(1, $bgOverlay / 45), 2, '.', ''), '0'), '.');

// Lapisan blur dan jendela arch sengaja memakai SATU foto diam, bukan ikut slideshow atau
// video: keduanya hanya dekorasi di belakang/di dalam panel, dan menggandakan pemutaran
// video di sana memakan baterai tanpa terlihat jelas.
$stillUrl = $media['image']
    ?: ($media['slides'][0] ?? null)
    ?: $media['poster'];
$targetName = request()->query('to');
$dateText = $eventDate ? \Illuminate\Support\Str::ucfirst(\Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y')) : null;
$sid = $section->id ?? 'default';
$variant = in_array($props['variant'] ?? null, ['fullscreen', 'split', 'minimal', 'arch'], true)
    ? $props['variant'] : 'fullscreen';
@endphp

<style>
  /* Hanya foto — overlay & blur ditangani lapisan CSS terpisah (.invite-gate-scrim /
     .invite-gate-blur), jadi kelas ini bisa dipakai ulang di window arch tanpa ikut gelap. */
  .cover-photo-{{ $sid }} {
    background-color: var(--color-ink, #20302a);
    @if($stillUrl) background-image: url('{{ $stillUrl }}'); @endif
    background-size: cover; background-position: center;
  }
  @if($media['keyframes']) {!! $media['keyframes'] !!} @endif
</style>

{{-- 1. Gate full-viewport (sebelum dibuka) --}}
<div x-show="!isOpen"
     x-transition:leave="transition ease-in-out duration-1000"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0 -translate-y-6"
     class="invite-gate cover--{{ $variant }}">
  @include('templates._cover-media', ['media' => $media, 'sid' => $sid])
  <div class="invite-gate-blur cover-photo-{{ $sid }}" aria-hidden="true"></div>
  @if($bgOverlay > 0)<div class="invite-gate-scrim" style="opacity:{{ $scrimOpacity }}" aria-hidden="true"></div>@endif
  <div class="cover-panel invite-gate-content">
    <div class="invite-gate-window cover-photo-{{ $sid }}" aria-hidden="true">
      <span class="invite-gate-window-cap">{{ $title }}</span>
    </div>
    <p class="invite-gate-kicker" data-editable="title">{{ $title }}</p>
    <h1 class="invite-gate-names">{{ $groomName }} <span class="invite-gate-amp">&amp;</span> {{ $brideName }}</h1>
    <div class="invite-gate-flourish" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M12 3c0 4-3 6-3 9a3 3 0 006 0c0-3-3-5-3-9z" fill="currentColor" fill-opacity=".18"/><path d="M12 12c-2.5 0-4.5 1.5-5.5 3.5M12 12c2.5 0 4.5 1.5 5.5 3.5M12 12v8" stroke-linecap="round"/></svg>
    </div>
    @if($dateText)<p class="invite-gate-date">{{ $dateText }}</p>@endif
    @if($targetName)
      <div class="invite-gate-guest">
        <p class="invite-gate-guest-label">Kepada Yth.</p>
        <p class="invite-gate-guest-name">{{ $targetName }}</p>
      </div>
    @endif
    <button @click="openInvitation()" class="invite-gate-button">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" /></svg>
      {{ $buttonText }}
    </button>
  </div>
</div>

{{-- 2. Layar sticky di dalam card (tertutup konten saat scroll) --}}
<div class="invite-cover-sticky cover--{{ $variant }}">
  @include('templates._cover-media', ['media' => $media, 'sid' => $sid])
  <div class="invite-gate-blur cover-photo-{{ $sid }}" aria-hidden="true"></div>
  @if($bgOverlay > 0)<div class="invite-gate-scrim" style="opacity:{{ $scrimOpacity }}" aria-hidden="true"></div>@endif
  <div class="cover-panel invite-cover-sticky-content">
    <div class="invite-gate-window cover-photo-{{ $sid }}" aria-hidden="true">
      <span class="invite-gate-window-cap">{{ $title }}</span>
    </div>
    <p class="invite-gate-kicker">{{ $title }}</p>
    <h2 class="invite-gate-names">{{ $groomName }} <span class="invite-gate-amp">&amp;</span> {{ $brideName }}</h2>
    <div class="invite-gate-flourish" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M12 3c0 4-3 6-3 9a3 3 0 006 0c0-3-3-5-3-9z" fill="currentColor" fill-opacity=".18"/><path d="M12 12c-2.5 0-4.5 1.5-5.5 3.5M12 12c2.5 0 4.5 1.5 5.5 3.5M12 12v8" stroke-linecap="round"/></svg>
    </div>
    @if($dateText)<p class="invite-gate-date">{{ $dateText }}</p>@endif
  </div>
</div>
