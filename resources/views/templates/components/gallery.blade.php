@props(['props' => [], 'section' => null, 'page' => null])

@php
$layout = $props['layout'] ?? 'grid';
$columns = $props['columns'] ?? 3;
$gap = $props['gap'] ?? 16;
$lightbox = $props['lightbox'] ?? true;
$images = $props['images'] ?? [];
@endphp

<section class="gallery-section py-12" style="background: {{ $props['background_color'] ?? 'var(--color-surface, #ffffff)' }};">
    <div class="container mx-auto px-4">
        @if($layout === 'grid')
            <div class="grid gap-4" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr));">
                @foreach($images as $image)
                    <div class="gallery-item relative overflow-hidden rounded-lg cursor-pointer hover:opacity-90 transition bg-gray-100" @if($lightbox) data-lightbox="gallery-{{ $section->id ?? 'default' }}" @endif>
                        <img src="{{ $image['url'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}" loading="lazy" class="block w-full h-64 object-cover">
                        @if($lightbox)
                            <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="gallery-item mb-4 break-inside-avoid rounded-lg overflow-hidden cursor-pointer hover:opacity-90 transition bg-gray-100" @if($lightbox) data-lightbox="gallery-{{ $section->id ?? 'default' }}" @endif>
                        <img src="{{ $image['url'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}" loading="lazy" class="block w-full">
                    </div>
                @endforeach
            </div>
        @elseif($layout === 'slider')
            <div class="gallery-slider relative overflow-hidden rounded-lg">
                <div class="flex transition-transform duration-300" id="slider-{{ $section->id ?? 'default' }}">
                    @foreach($images as $image)
                        <div class="flex-shrink-0 w-full">
                            <img src="{{ $image['url'] ?? '' }}" alt="{{ $image['alt'] ?? '' }}" class="w-full h-96 object-cover">
                        </div>
                    @endforeach
                </div>
                @if(count($images) > 1)
                    <button onclick="prevSlide('{{ $section->id ?? 'default' }}')" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 shadow-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button onclick="nextSlide('{{ $section->id ?? 'default' }}', {{ count($images) }})" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 shadow-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif
    </div>
</section>

@if($lightbox && $layout !== 'slider')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lightboxTriggers = document.querySelectorAll('[data-lightbox="gallery-{{ $section->id ?? 'default' }}"]');
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox-{{ $section->id ?? 'default' }}';
    lightbox.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center';
    lightbox.innerHTML = `
        <button class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300">&times;</button>
        <img src="" alt="" class="max-w-full max-h-full object-contain">
    `;
    document.body.appendChild(lightbox);

    const lightboxImg = lightbox.querySelector('img');
    const closeBtn = lightbox.querySelector('button');

    lightboxTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const img = this.querySelector('img');
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        });
    });

    closeBtn.addEventListener('click', function() {
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
    });

    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
        }
    });
});
</script>
@endpush
@endif

@if($layout === 'slider')
@push('scripts')
<script>
let currentSlide_{{ $section->id ?? 'default' }} = 0;

function prevSlide(id) {
    const slider = document.getElementById('slider-' + id);
    const slideCount = slider.children.length;
    currentSlide_{{ $section->id ?? 'default' }} = (currentSlide_{{ $section->id ?? 'default' }} - 1 + slideCount) % slideCount;
    slider.style.transform = 'translateX(-' + (currentSlide_{{ $section->id ?? 'default' }} * 100) + '%)';
}

function nextSlide(id, count) {
    const slider = document.getElementById('slider-' + id);
    currentSlide_{{ $section->id ?? 'default' }} = (currentSlide_{{ $section->id ?? 'default' }} + 1) % count;
    slider.style.transform = 'translateX(-' + (currentSlide_{{ $section->id ?? 'default' }} * 100) + '%)';
}

// Auto slide every 5 seconds
setInterval(() => {
    nextSlide('{{ $section->id ?? 'default' }}', {{ count($images) }});
}, 5000);
</script>
@endpush
@endif
