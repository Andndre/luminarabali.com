@props(['props' => [], 'section' => null, 'page' => null])

@php
$targetDate = $page && $page->event_date ? $page->event_date->toIso8601String() : null;
$variant = $props['variant'] ?? 'cards';
$title = $props['title'] ?? 'Counting Down To';
$paddingTop = $props['padding_top'] ?? 64;
$paddingBottom = $props['padding_bottom'] ?? 64;
$passedText = $props['passed_text'] ?? 'Hari bahagia telah tiba';
$units = ['d' => 'Hari', 'h' => 'Jam', 'm' => 'Menit', 's' => 'Detik'];
@endphp

<section class="countdown countdown--{{ $variant }}" style="padding: {{ $paddingTop }}px 20px {{ $paddingBottom }}px;">
  <div class="container mx-auto text-center">
    @if($title)
      <h2 class="countdown-title" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 1.5rem);">
        {{ $title }}
      </h2>
    @endif

    @if($targetDate)
      {{-- Alpine, bukan @push('scripts'): HTML yang disuntik lewat innerHTML (preview Studio)
           tidak pernah menjalankan <script>, jadi hitungannya beku di 00. --}}
      <div x-data="{
             target: new Date('{{ $targetDate }}').getTime(),
             passed: false,
             d: '00', h: '00', m: '00', s: '00',
             start() {
               this.tick();
               this.timer = setInterval(() => this.tick(), 1000);
             },
             tick() {
               // Preview Studio mengganti section lewat outerHTML tanpa memanggil
               // lifecycle Alpine, jadi interval lama harus berhenti sendiri.
               if (!this.$el.isConnected) return clearInterval(this.timer);
               const diff = this.target - Date.now();
               this.passed = diff <= 0;
               const left = Math.max(0, diff);
               const pad = n => String(Math.floor(n)).padStart(2, '0');
               this.d = pad(left / 86400000);
               this.h = pad(left / 3600000 % 24);
               this.m = pad(left / 60000 % 60);
               this.s = pad(left / 1000 % 60);
             },
           }"
           x-init="start()">
        <div class="countdown-grid">
          @foreach($units as $key => $label)
            <div class="countdown-item">
              @if($variant === 'ring')
                <div class="countdown-ring"><span class="countdown-number" x-text="{{ $key }}">00</span></div>
              @else
                <span class="countdown-number" x-text="{{ $key }}">00</span>
              @endif
              <span class="countdown-label">{{ $label }}</span>
            </div>
          @endforeach
        </div>

        @if($passedText)
          <p class="countdown-passed" x-show="passed" x-cloak>{{ $passedText }}</p>
        @endif
      </div>
    @endif
  </div>
</section>
