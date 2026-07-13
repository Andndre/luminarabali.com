@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Rangkaian Acara';
    $events = $props['events'] ?? [];
    $backgroundColor = $props['background_color'] ?? 'var(--color-surface, #ffffff)';
    $accentColor = $props['accent_color'] ?? 'var(--color-accent, #d4af37)';
    $textColor = $props['text_color'] ?? 'var(--color-text, #212529)';
@endphp

<section style="background: {{ $backgroundColor }}; color: {{ $textColor }}; padding: 64px 16px;">
  <div class="container mx-auto max-w-3xl text-center">
    <h2 class="text-2xl md:text-3xl font-bold mb-10" style="font-family: var(--font-heading, serif); color: {{ $accentColor }};">
      {{ $heading }}
    </h2>
    <div class="grid gap-6 {{ count($events) > 1 ? 'md:grid-cols-2' : '' }}">
      @foreach($events as $event)
        <div class="rounded-xl p-6" style="border: 1px solid {{ $accentColor }};">
          <h3 class="text-lg font-semibold" style="font-family: var(--font-heading, serif); color: {{ $accentColor }};">
            {{ $event['name'] ?? '' }}
          </h3>
          @if(!empty($event['date_text']))
            <p class="mt-3 text-sm">{{ $event['date_text'] }}</p>
          @endif
          @if(!empty($event['time_text']))
            <p class="mt-1 text-sm">{{ $event['time_text'] }}</p>
          @endif
          @if(!empty($event['venue']))
            <p class="mt-3 font-medium">{{ $event['venue'] }}</p>
          @endif
          @if(!empty($event['address']))
            <p class="mt-1 text-sm opacity-80">{{ $event['address'] }}</p>
          @endif
          @if(!empty($event['maps_url']))
            <a href="{{ $event['maps_url'] }}" target="_blank" rel="noopener"
                class="inline-block mt-4 rounded-full px-5 py-2 text-sm font-semibold"
                style="background: {{ $accentColor }}; color: {{ $backgroundColor }};">
              Lihat Lokasi
            </a>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
