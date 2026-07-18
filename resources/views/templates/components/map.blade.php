@props(['props' => [], 'section' => null, 'page' => null])

@php
$address = $props['address'] ?? '';
$latitude = $props['latitude'] ?? '';
$longitude = $props['longitude'] ?? '';
$zoom = $props['zoom'] ?? 15;
$height = $props['height'] ?? 400;
$showButton = $props['show_button'] ?? true;

// Koordinat kosong → pakai alamat sebagai query supaya peta zoom ke lokasi,
// bukan fallback ke peta dunia (q=,).
$hasCoords = $latitude !== '' && $longitude !== '';
$mapQuery = $hasCoords ? "{$latitude},{$longitude}" : $address;
$dirDestination = $hasCoords ? "{$latitude},{$longitude}" : $address;
@endphp

<section style="padding: var(--section-y, 64px) 16px;">
    <div class="container mx-auto px-4">
        @if($props['title'] ?? false)
            <h2 class="text-center mb-8" style="font-family: var(--font-heading, serif); font-size: var(--step-2xl, 32px);">
                {{ $props['title'] }}
            </h2>
        @endif

        <div class="max-w-4xl mx-auto">
            <!-- Google Maps Embed -->
            @if($mapQuery)
            <div class="map-frame" style="height: {{ $height }}px;">
                <iframe
                    width="100%"
                    height="100%"
                    frameborder="0"
                    style="border:0"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q={{ urlencode($mapQuery) }}&z={{ $zoom }}&output=embed">
                </iframe>
            </div>
            @endif

            <!-- Address -->
            @if($address)
                <div class="mt-6 text-center opacity-80">
                    <p>{{ $address }}</p>
                </div>
            @endif

            <!-- Directions Button -->
            @if($showButton && $dirDestination)
                <div class="mt-6 text-center">
                    <a
                        href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($dirDestination) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-6 py-3 font-medium transition hover:opacity-90"
                        style="background: var(--color-accent, #b5654d); color: var(--color-surface, #ffffff); border-radius: var(--radius, 12px);"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        {{ $props['button_text'] ?? 'Petunjuk Arah' }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
