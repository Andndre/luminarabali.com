{{-- Kartu template. Dipanggil lewat @each, jadi variabelnya $template (bukan @props). --}}
<a href="{{ route('catalog.show', $template->slug) }}" class="catalog-card catalog-reveal">
    {{--
        Preview hidup, TAMPILAN DEPAN SAJA: `cover-only` mengunci iframe di
        posisi atas dan wadahnya memotong tepat di area cover. Thumbnail
        template jadi poster sebelum iframe ter-mount sekaligus fallback bila
        preview gagal/belum dimuat. Mount tetap lazy (IntersectionObserver di
        catalog/_partials/scripts.blade.php), jadi kartu di bawah lipatan tak
        pernah membuat iframe sampai benar-benar masuk viewport.
    --}}
    <div class="catalog-card__thumb">
        {{-- Backdrop nama: dirender LEBIH DULU supaya live-frame melukis di atasnya. --}}
        @unless ($template->thumbnail)
            <div class="catalog-card__thumb-empty">{{ $template->name }}</div>
        @endunless
        <x-catalog.live-frame
            :src="route('catalog.preview', $template->slug)"
            :poster="$template->thumbnail ? asset('storage/'.$template->thumbnail) : null"
            cover-only />
    </div>
    <div class="catalog-card__body">
        @if ($template->category)
            <span class="catalog-eyebrow">{{ $template->category }}</span>
        @endif
        <h3 class="catalog-display" style="font-size: 1.35rem; margin-top: .35rem">{{ $template->name }}</h3>
        <p style="margin-top: .5rem; font-weight: 500; color: var(--cat-wine)">{{ $template->priceLabel() }}</p>
    </div>
</a>
