{{-- Kartu template. Dipanggil lewat @each, jadi variabelnya $template (bukan @props). --}}
<a href="{{ route('catalog.show', $template->slug) }}" class="catalog-card catalog-reveal">
    <div class="catalog-card__thumb">
        @if ($template->thumbnail)
            <img src="{{ asset('storage/'.$template->thumbnail) }}" alt="Pratinjau desain {{ $template->name }}" loading="lazy">
        @else
            <div class="catalog-card__thumb-empty">{{ $template->name }}</div>
        @endif
    </div>
    <div class="catalog-card__body">
        @if ($template->category)
            <span class="catalog-eyebrow">{{ $template->category }}</span>
        @endif
        <h3 class="catalog-display" style="font-size: 1.35rem; margin-top: .35rem">{{ $template->name }}</h3>
        <p style="margin-top: .5rem; font-weight: 500; color: var(--cat-wine)">{{ $template->priceLabel() }}</p>
    </div>
</a>
