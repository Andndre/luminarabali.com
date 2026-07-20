{{--
    Live-frame: iframe undangan asli yang di-mount lazy (lihat
    catalog/_partials/scripts.blade.php) di atas poster.

    Mode:
    - default      : frame penuh, boleh auto-scroll (dipakai device tengah hero).
    - cover-only   : hanya bagian cover. Iframe terkunci di posisi atas, tak
                     pernah auto-scroll, wadahnya overflow:hidden dan memotong
                     tepat di area cover. Dipakai kartu katalog.
--}}
@props(['src', 'poster' => null, 'autoscroll' => false, 'coverOnly' => false])

@php
    // cover-only dan autoscroll saling meniadakan: cover harus diam di atas.
    $wantsScroll = $autoscroll && ! $coverOnly;
@endphp

<div {{ $attributes->merge(['class' => 'catalog-liveframe'.($coverOnly ? ' catalog-liveframe--cover' : '')]) }}
     data-liveframe
     data-src="{{ $src }}"
     @if ($wantsScroll) data-autoscroll="1" @endif>
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
