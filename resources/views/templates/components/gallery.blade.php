@props(['props' => [], 'section' => null, 'page' => null])

@php
$layout = $props['variant'] ?? $props['layout'] ?? 'grid';
$columns = $props['columns'] ?? 2;
$gap = $props['gap'] ?? 16;
$lightbox = $props['lightbox'] ?? true;
$images = $props['images'] ?? [];
$heading = $props['heading'] ?? '';
$subheading = $props['subheading'] ?? '';

// video_url: hanya YouTube didukung (ekstrak ID 11-char, embed via URL tetap — aman dari injeksi)
$videoUrl = $props['video_url'] ?? '';
$youtubeVideoId = null;
if ($videoUrl && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $videoUrl, $m)) {
    $youtubeVideoId = $m[1];
}
@endphp

@php $useLightbox = $lightbox && $layout !== 'slider'; @endphp

<section class="gallery-section gallery--{{ $layout }}" style="padding: var(--section-y, 64px) 16px;"
    @if($useLightbox) x-data="{ open: false, src: '', alt: '' }" @endif>
    <div class="container mx-auto px-4">
        @if($heading || $subheading)
            <div class="gallery-header">
                @if($heading)<h2 class="section-heading gallery-heading">{{ $heading }}</h2>@endif
                @if($subheading)<p class="section-subheading">{{ $subheading }}</p>@endif
            </div>
        @endif

        @if($youtubeVideoId)
            <div class="gallery-video" style="border-radius: var(--radius, 12px);">
                <iframe src="https://www.youtube.com/embed/{{ $youtubeVideoId }}"
                        title="Video"
                        frameborder="0"
                        allow="encrypted-media; picture-in-picture"
                        allowfullscreen loading="lazy"></iframe>
            </div>
        @endif

        @if($layout === 'grid')
            <div class="gallery-grid grid" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr)); gap: {{ $gap }}px;">
                @foreach($images as $image)
                    <div class="gallery-item group relative overflow-hidden cursor-pointer"
                        style="border-radius: var(--radius, 12px); background: var(--color-surface_alt, #f3ece1); box-shadow: var(--shadow, 0 4px 14px rgba(0,0,0,.08));"
                        @if($useLightbox) @click="src = $el.querySelector('img').src; alt = $el.querySelector('img').alt; open = true" @endif>
                        <img src="{{ $image['url'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}" loading="lazy" class="block w-full h-64 object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                        @if($useLightbox)
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif($layout === 'masonry')
            <div style="columns: {{ max(1, (int) $columns) }}; column-gap: {{ $gap }}px;">
                @foreach($images as $image)
                    <div class="gallery-item group break-inside-avoid overflow-hidden cursor-pointer"
                        style="margin-bottom: {{ $gap }}px; border-radius: var(--radius, 12px); background: var(--color-surface_alt, #f3ece1); box-shadow: var(--shadow, 0 4px 14px rgba(0,0,0,.08));"
                        @if($useLightbox) @click="src = $el.querySelector('img').src; alt = $el.querySelector('img').alt; open = true" @endif>
                        <img src="{{ $image['url'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}" loading="lazy" class="block w-full transition-transform duration-500 ease-out group-hover:scale-105">
                    </div>
                @endforeach
            </div>
        @elseif($layout === 'slider')
            {{-- Alpine self-contained: jalan walau HTML di-inject via innerHTML (preview) --}}
            <div class="gallery-slider relative overflow-hidden" style="border-radius: var(--radius, 12px);"
                 x-data="{ i: 0, n: {{ count($images) }} }"
                 x-init="if (n > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(() => i = (i + 1) % n, 5000)">
                <div class="flex transition-transform duration-300" :style="`transform: translateX(-${i * 100}%)`">
                    @foreach($images as $image)
                        <div class="shrink-0 w-full">
                            <img src="{{ $image['url'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}" class="w-full h-96 object-cover">
                        </div>
                    @endforeach
                </div>
                @if(count($images) > 1)
                    <button type="button" @click="i = (i - 1 + n) % n" aria-label="Sebelumnya" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 transition" style="box-shadow: 0 8px 24px rgba(0,0,0,.12);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button type="button" @click="i = (i + 1) % n" aria-label="Berikutnya" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 transition" style="box-shadow: 0 8px 24px rgba(0,0,0,.12);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif
    </div>

    @if($useLightbox)
        {{-- teleport ke body: fixed harus lepas dari .invite-card yang ber-transform --}}
        <template x-teleport="body">
            <div x-show="open" x-transition.opacity style="display: none"
                 @click.self="open = false" @keydown.escape.window="open = false"
                 class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4">
                <button type="button" @click="open = false" aria-label="Tutup"
                        class="absolute top-4 right-4 text-white text-4xl leading-none hover:opacity-70 transition">&times;</button>
                <img :src="src" :alt="alt" class="max-w-full max-h-full object-contain">
            </div>
        </template>
    @endif
</section>

