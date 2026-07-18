@props(['props' => [], 'section' => null, 'page' => null])

@php
$targetDate = $page && $page->event_date ? $page->event_date->toIso8601String() : null;
$title = $props['title'] ?? 'Counting Down To';
$textColor = $props['text_color'] ?? 'var(--color-text, #212529)';
$titleColor = $props['title_color'] ?? 'var(--color-primary, #212529)';
$accentColor = $props['accent_color'] ?? 'var(--color-accent, #b5654d)';
$paddingTop = $props['padding_top'] ?? 64;
$paddingBottom = $props['padding_bottom'] ?? 64;
@endphp

<style>
  .countdown-section-{{ $section->id }} {
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
  }

  .countdown-item-{{ $section->id }} {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 80px;
  }

  .countdown-number-{{ $section->id }} {
    font-size: 2.5rem;
    font-weight: 700;
    color: {{ $accentColor }};
    line-height: 1;
  }

  .countdown-label-{{ $section->id }} {
    font-size: 0.875rem;
    color: {{ $textColor }};
    margin-top: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
</style>

<section class="countdown-section-{{ $section->id }}">
  <div class="container mx-auto px-4 text-center">
    @if($title)
      <h2 class="text-2xl md:text-3xl font-bold mb-8" style="font-family: var(--font-heading, serif); color: {{ $titleColor }};">
        {{ $title }}
      </h2>
    @endif

    @if($targetDate)
      <div id="countdown-{{ $section->id }}" class="flex justify-center gap-6 md:gap-12">
        <div class="countdown-item-{{ $section->id }}">
          <span class="countdown-number-{{ $section->id }}" id="days-{{ $section->id }}">00</span>
          <span class="countdown-label-{{ $section->id }}">Hari</span>
        </div>
        <div class="countdown-item-{{ $section->id }}">
          <span class="countdown-number-{{ $section->id }}" id="hours-{{ $section->id }}">00</span>
          <span class="countdown-label-{{ $section->id }}">Jam</span>
        </div>
        <div class="countdown-item-{{ $section->id }}">
          <span class="countdown-number-{{ $section->id }}" id="minutes-{{ $section->id }}">00</span>
          <span class="countdown-label-{{ $section->id }}">Menit</span>
        </div>
        <div class="countdown-item-{{ $section->id }}">
          <span class="countdown-number-{{ $section->id }}" id="seconds-{{ $section->id }}">00</span>
          <span class="countdown-label-{{ $section->id }}">Detik</span>
        </div>
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
