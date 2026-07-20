{{-- Lapisan latar cover. Dipakai dua kali: gate (sebelum dibuka) dan layar sticky.
     Keputusan medianya dari BackgroundMedia, sama seperti section lain; yang beda cuma
     pembungkusnya — di sini .invite-gate-bg, bukan .sec-bg. Kelas slide/video/iframe
     sengaja kelas yang sama supaya aturan CSS-nya tidak ada dua versi.
     $media, $sid, dan $mediaKey (pembeda id antara gate dan sticky) datang dari pemanggil. --}}
@if ($media['slides'])
    <div class="invite-gate-bg cover-media-slides" aria-hidden="true"
        style="background-image:url('{{ $media['slides'][0] }}');--slide-dur:{{ $media['slideSeconds'] }}s;--slide-n:{{ count($media['slides']) }};--slide-fade:sec-bgslide-{{ count($media['slides']) }}">
        @foreach ($media['slides'] as $i => $slide)
            <div class="sec-bg-img sec-bg-slide" style="background-image:url('{{ $slide }}');--slide-i:{{ $i }}"></div>
        @endforeach
    </div>
@elseif ($media['youtube'])
    <div class="invite-gate-bg sec-bg--youtube" aria-hidden="true"
        style="background-image:{!! \App\Services\BackgroundMedia::youtubePosterCss($media['youtube'], $media['poster']) !!}">
        <div class="sec-bg-ytwrap">
            <iframe class="sec-bg-yt" title="" tabindex="-1" frameborder="0" allow="autoplay"
                src="https://www.youtube-nocookie.com/embed/{{ $media['youtube'] }}?autoplay=1&amp;mute=1&amp;loop=1&amp;playlist={{ $media['youtube'] }}&amp;controls=0&amp;disablekb=1&amp;fs=0&amp;modestbranding=1&amp;rel=0&amp;iv_load_policy=3&amp;playsinline=1&amp;enablejsapi=1"></iframe>
        </div>
    </div>
@elseif ($media['video'])
    <video class="invite-gate-bg sec-bg-video" src="{{ $media['video'] }}" autoplay muted loop playsinline
        preload="metadata" aria-hidden="true" @if ($media['poster']) poster="{{ $media['poster'] }}" @endif></video>
@else
    <div class="invite-gate-bg cover-photo-{{ $sid }}" aria-hidden="true"></div>
@endif
