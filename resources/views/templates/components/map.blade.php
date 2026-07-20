@props(['props' => [], 'section' => null, 'page' => null])

@php
$title = $props['title'] ?? null;
$address = $props['address'] ?? '';
$venueLabel = $props['venue_label'] ?? '';
$latitude = $props['latitude'] ?? '';
$longitude = $props['longitude'] ?? '';
$zoom = $props['zoom'] ?? 15;
$height = $props['height'] ?? 400;
$showButton = $props['show_button'] ?? true;
$variant = $props['variant'] ?? 'framed';

// Koordinat kosong → pakai alamat sebagai query supaya peta zoom ke lokasi,
// bukan fallback ke peta dunia (q=,).
$hasCoords = $latitude !== '' && $longitude !== '';
$mapQuery = $hasCoords ? "{$latitude},{$longitude}" : $address;
$dirDestination = $hasCoords ? "{$latitude},{$longitude}" : $address;

// Varian bar memberi panel ini latar sendiri — kalau isinya kosong, yang tersisa
// cuma pita berwarna tanpa isi.
$showAddressBelow = $variant !== 'address-first' && $address !== '';
$showAction = $showButton && $dirDestination !== '';

// no-embed sengaja tidak memuat iframe Google sama sekali: tak ada request pihak
// ketiga, tak ada chrome "Buka di Maps" yang digambar Google di dalam petanya, dan
// tak ada beban muat di kartu yang isinya cuma satu alamat. Tombol arah tetap
// membawa tamu ke aplikasi peta mereka sendiri.
$showEmbed = $variant !== 'no-embed' && $mapQuery !== '';
@endphp

<section class="map map--{{ $variant }}">
    @if($title)
        <h2 class="section-heading map-heading">{{ $title }}</h2>
    @endif

    {{-- address-first membaca alamat lebih dulu, jadi ia juga harus lebih dulu
         di DOM — bukan cuma dipindah lewat CSS. --}}
    @if($variant === 'address-first' && $address)
        <p class="map-address">
            @if($venueLabel)<span class="map-venue">{{ $venueLabel }}</span>@endif
            {{ $address }}
        </p>
    @endif

    @if($showEmbed)
        <div class="map-stage">
            <div class="map-frame" style="height: {{ (int) $height }}px;">
                <iframe
                    width="100%"
                    height="100%"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    title="{{ $address ?: 'Peta lokasi' }}"
                    src="https://maps.google.com/maps?q={{ urlencode($mapQuery) }}&z={{ (int) $zoom }}&output=embed">
                </iframe>
            </div>
        </div>
    @endif

    @if($showAddressBelow || $showAction)
    <div class="map-panel">
        @if($showAddressBelow)
            <p class="map-address">
                @if($venueLabel)<span class="map-venue">{{ $venueLabel }}</span>@endif
                {{ $address }}
            </p>
        @endif

        @if($showAction)
            <div class="map-action">
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($dirDestination) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn-primary">
                    {{ $props['button_text'] ?? 'Petunjuk Arah' }}
                </a>
            </div>
        @endif
    </div>
    @endif
</section>
