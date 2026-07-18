@props(['props' => [], 'section' => null, 'page' => null])

@php
$title = $props['title'] ?? 'The Wedding Of';
$groomName = $page->groom_name ?? 'Groom';
$brideName = $page->bride_name ?? 'Bride';
$eventDate = $page->event_date ?? null;
$buttonText = $props['button_text'] ?? 'Buka Undangan';
$buttonColor = $props['button_color'] ?? 'var(--color-accent, #d4af37)';
$fontFamily = isset($props['font_family']) ? "'{$props['font_family']}', serif" : "var(--font-heading, 'Playfair Display'), serif";
$textColor = $props['text_color'] ?? 'var(--color-surface, #ffffff)';
$overlayEnabled = filter_var($props['overlay_enabled'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
$bgValue = $props['background_image'] ?? null;
$bgUrl = $bgValue
    ? (\Illuminate\Support\Str::startsWith($bgValue, ['http://', 'https://', '/']) ? $bgValue : \Illuminate\Support\Facades\Storage::url($bgValue))
    : null;
$targetName = request()->query('to');
$dateText = $eventDate ? \Illuminate\Support\Str::ucfirst(\Carbon\Carbon::parse($eventDate)->translatedFormat('d F Y')) : null;
$sid = $section->id ?? 'default';
$variant = $props['variant'] ?? 'fullscreen';
@endphp

<style>
  .cover-visual-{{ $sid }} {
    background-color: #1a1a1a;
    @if($bgUrl) background-image: url('{{ $bgUrl }}'); @endif
    background-size: cover; background-position: center;
  }
  .cover-visual-{{ $sid }}::before {
    content: ''; position: absolute; inset: 0;
    @if($overlayEnabled) background: rgba(0, 0, 0, 0.45); @endif
  }
  .cover-text-{{ $sid }} { font-family: {{ $fontFamily }}; color: {{ $textColor }}; }
</style>

{{-- 1. Gate full-viewport (sebelum dibuka) --}}
<div x-show="!isOpen"
     x-transition:leave="transition ease-in-out duration-1000"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0 -translate-y-6"
     class="invite-gate cover--{{ $variant }} cover-visual-{{ $sid }}">
  <div class="invite-gate-content cover-text-{{ $sid }}">
    <p class="invite-gate-kicker" data-editable="title">{{ $title }}</p>
    <h1 class="invite-gate-names">{{ $groomName }} &amp; {{ $brideName }}</h1>
    @if($dateText)<p class="invite-gate-date">{{ $dateText }}</p>@endif
    @if($targetName)
      <div class="invite-gate-guest">
        <p>Kepada Yth.</p>
        <p class="invite-gate-guest-name">{{ $targetName }}</p>
      </div>
    @endif
    <button @click="openInvitation()" class="invite-gate-button" style="background: {{ $buttonColor }};">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" /></svg>
      {{ $buttonText }}
    </button>
  </div>
</div>

{{-- 2. Layar sticky di dalam card (tertutup konten saat scroll) --}}
<div class="invite-cover-sticky cover--{{ $variant }} cover-visual-{{ $sid }}">
  <div class="invite-cover-sticky-content cover-text-{{ $sid }}">
    <p class="invite-gate-kicker">{{ $title }}</p>
    <h2 class="invite-gate-names">{{ $groomName }} &amp; {{ $brideName }}</h2>
    @if($dateText)<p class="invite-gate-date">{{ $dateText }}</p>@endif
  </div>
</div>
