@props(['props' => [], 'section' => null, 'page' => null])

@php
    $variant = $props['variant'] ?? 'bordered-cards';
    $heading = $props['heading'] ?? 'Rangkaian Acara';
    $events = $props['events'] ?? [];
@endphp

<section class="events events--{{ $variant }}" style="padding: var(--section-y, 64px) 16px;">
  <div class="events-inner">
    <h2 class="events-heading" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);">
      {{ $heading }}
    </h2>
    <div class="events-list">
      @foreach ($events as $event)
        <div class="event-item">
          <h3 style="font-family: var(--font-heading, serif); font-size: var(--step-lg, 20px);">{{ $event['name'] ?? '' }}</h3>
          @if (!empty($event['date_text']))<p class="event-line">{{ $event['date_text'] }}</p>@endif
          @if (!empty($event['time_text']))<p class="event-line">{{ $event['time_text'] }}</p>@endif
          @if (!empty($event['venue']))<p class="event-venue">{{ $event['venue'] }}</p>@endif
          @if (!empty($event['address']))<p class="event-addr">{{ $event['address'] }}</p>@endif
          @if (!empty($event['maps_url']))
            <a class="event-btn" href="{{ $event['maps_url'] }}" target="_blank" rel="noopener"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>{{ $props['maps_label'] ?? 'Lihat Lokasi' }}</a>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
