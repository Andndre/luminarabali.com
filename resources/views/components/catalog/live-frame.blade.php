@props(['src', 'poster' => null, 'autoscroll' => false])

<div {{ $attributes->merge(['class' => 'catalog-liveframe']) }}
     data-liveframe
     data-src="{{ $src }}"
     @if ($autoscroll) data-autoscroll="1" @endif>
    {{-- poster selalu tampil; iframe di-mount lazy DI ATAS poster --}}
    <div class="catalog-liveframe__device">
        <div class="catalog-liveframe__poster">
            @if ($poster)
                <img src="{{ $poster }}" alt="" loading="lazy">
            @else
                <span>Memuat preview…</span>
            @endif
        </div>
    </div>
</div>
