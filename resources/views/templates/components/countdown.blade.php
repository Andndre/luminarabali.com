@props(['props' => [], 'section' => null, 'page' => null])

@php
$targetDate = $page && $page->event_date ? $page->event_date->toIso8601String() : null;
$title = $props['title'] ?? 'Counting Down To';
$paddingTop = $props['padding_top'] ?? 64;
$paddingBottom = $props['padding_bottom'] ?? 64;
@endphp

<style>
  .countdown-section-{{ $section->id }} {
    padding-top: {{ $paddingTop }}px;
    padding-bottom: {{ $paddingBottom }}px;
  }

  /* grid 4 kolom sama lebar: kartu undangan hanya ~430px, jadi min-width tetap +
     breakpoint viewport (md:) meluber — lebar ikut kontainer, bukan layar. */
  .countdown-grid-{{ $section->id }} {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: clamp(.25rem, 2cqi, 1rem);
    max-width: 28rem;
    margin: 0 auto;
  }

  .countdown-item-{{ $section->id }} {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .countdown-number-{{ $section->id }} {
    font-size: var(--step-3xl, 2.25rem);
    font-weight: 700;
    color: var(--color-accent, #b5654d);
    line-height: 1;
    font-variant-numeric: tabular-nums;
  }

  .countdown-label-{{ $section->id }} {
    font-size: var(--step-sm, .8125rem);
    opacity: .75;
    margin-top: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }
</style>

<section class="countdown-section-{{ $section->id }}">
  <div class="container mx-auto px-4 text-center">
    @if($title)
      <h2 class="font-bold mb-8" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 1.5rem);">
        {{ $title }}
      </h2>
    @endif

    @if($targetDate)
      <div id="countdown-{{ $section->id }}" class="countdown-grid-{{ $section->id }}">
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
