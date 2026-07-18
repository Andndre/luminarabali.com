@props(['props' => [], 'section' => null, 'page' => null])

@php
$targetDate = $page && $page->event_date ? $page->event_date->toIso8601String() : null;
$variant = $props['variant'] ?? 'cards';
$title = $props['title'] ?? 'Counting Down To';
$paddingTop = $props['padding_top'] ?? 64;
$paddingBottom = $props['padding_bottom'] ?? 64;
@endphp

<section class="countdown countdown--{{ $variant }}" style="padding: {{ $paddingTop }}px 20px {{ $paddingBottom }}px;">
  <div class="container mx-auto text-center">
    @if($title)
      <h2 class="countdown-title" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 1.5rem);">
        {{ $title }}
      </h2>
    @endif

    @if($targetDate)
      <div id="countdown-{{ $section->id }}" class="countdown-grid">
        @if($variant === 'ring')
          <div class="countdown-item">
            <div class="countdown-ring"><span class="countdown-number" id="days-{{ $section->id }}">00</span></div>
            <span class="countdown-label">Hari</span>
          </div>
          <div class="countdown-item">
            <div class="countdown-ring"><span class="countdown-number" id="hours-{{ $section->id }}">00</span></div>
            <span class="countdown-label">Jam</span>
          </div>
          <div class="countdown-item">
            <div class="countdown-ring"><span class="countdown-number" id="minutes-{{ $section->id }}">00</span></div>
            <span class="countdown-label">Menit</span>
          </div>
          <div class="countdown-item">
            <div class="countdown-ring"><span class="countdown-number" id="seconds-{{ $section->id }}">00</span></div>
            <span class="countdown-label">Detik</span>
          </div>
        @else
          <div class="countdown-item">
            <span class="countdown-number" id="days-{{ $section->id }}">00</span>
            <span class="countdown-label">Hari</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number" id="hours-{{ $section->id }}">00</span>
            <span class="countdown-label">Jam</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number" id="minutes-{{ $section->id }}">00</span>
            <span class="countdown-label">Menit</span>
          </div>
          <div class="countdown-item">
            <span class="countdown-number" id="seconds-{{ $section->id }}">00</span>
            <span class="countdown-label">Detik</span>
          </div>
        @endif
      </div>
    @endif
  </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const targetDate = new Date('{{ $targetDate }}').getTime();
  const sectionId = '{{ $section->id }}';

  setInterval(function() {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance > 0) {
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      const daysEl = document.getElementById('days-' + sectionId);
      const hoursEl = document.getElementById('hours-' + sectionId);
      const minutesEl = document.getElementById('minutes-' + sectionId);
      const secondsEl = document.getElementById('seconds-' + sectionId);

      if (daysEl) daysEl.textContent = days;
      if (hoursEl) hoursEl.textContent = hours;
      if (minutesEl) minutesEl.textContent = minutes;
      if (secondsEl) secondsEl.textContent = seconds;
    }
  }, 1000);
});
</script>
@endpush
