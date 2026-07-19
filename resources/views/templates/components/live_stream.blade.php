@props(['props' => [], 'section' => null, 'page' => null])

@php
    $heading = $props['heading'] ?? 'Live Streaming';
    $youtubeUrl = $props['youtube_url'] ?? '';
    $scheduleText = $props['schedule_text'] ?? '';
    $buttonText = $props['button_text'] ?? 'Tonton';
    $variant = $props['variant'] ?? 'player';

    $youtubeVideoId = null;
    if ($youtubeUrl && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $youtubeUrl, $m)) {
        $youtubeVideoId = $m[1];
    }

    // Tanpa video ID tidak ada yang bisa disematkan. Dulu hasilnya tombol telanjang
    // tanpa bingkai apa pun; sekarang jatuh ke bentuk card yang tetap punya judul.
    if (!$youtubeVideoId) {
        $variant = 'card';
    }

    $showFrame = $youtubeVideoId && $variant !== 'card';
    // hqdefault selalu ada untuk video publik mana pun; maxresdefault tidak.
    $thumb = $youtubeVideoId ? "https://i.ytimg.com/vi/{$youtubeVideoId}/hqdefault.jpg" : null;
@endphp

<section class="stream stream--{{ $variant }}">
    {{-- marquee menaruh judul di dalam bingkai; varian lain di atasnya. --}}
    @if($variant !== 'marquee')
        <div class="stream-body">
            <h2 class="section-heading stream-heading">{{ $heading }}</h2>
            @if($scheduleText)
                <p class="stream-schedule">{{ $scheduleText }}</p>
            @endif

            @if(!$showFrame && $youtubeUrl)
                <div class="stream-action">
                    <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                        {{ $buttonText }}
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if($showFrame)
        {{-- iframe baru dibuat setelah tombol putar ditekan: tamu yang tidak menonton
             tidak ikut memuat skrip dan cookie pihak ketiga YouTube. --}}
        <div class="stream-frame" x-data="{ playing: false }">
            <template x-if="playing">
                <iframe
                    src="https://www.youtube.com/embed/{{ $youtubeVideoId }}?autoplay=1"
                    title="{{ $heading }}"
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </template>

            <button type="button"
                    class="stream-facade"
                    x-show="!playing"
                    @click="playing = true"
                    aria-label="{{ $buttonText }}">
                <img src="{{ $thumb }}" alt="" loading="lazy">
                <span class="stream-play" aria-hidden="true"></span>
            </button>

            @if($variant === 'marquee')
                <div class="stream-overlay">
                    <h2 class="section-heading stream-heading">{{ $heading }}</h2>
                    @if($scheduleText)
                        <p class="stream-schedule">{{ $scheduleText }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif
</section>
