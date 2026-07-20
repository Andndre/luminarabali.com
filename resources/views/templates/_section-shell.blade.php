{{-- Satu-satunya penghasil wrapper [data-section-id]: dipakai section-tree (publik + studio)
     dan render-section (fragment swap). Shell "berat" (relative + ornamen + CSS scoped +
     atribut animasi) hanya dirender bila perlu; selain itu wrapper display:contents lama. --}}
@props(['section' => null, 'page' => null, 'elements' => []])

@php
    $viewPath = "templates.components.{$section->section_type}";
    $props = $section->props ?? [];

    $animation = $props['animation'] ?? 'none';
    $animationDelay = (int) ($props['animation_delay'] ?? 0);
    $customCss = trim((string) ($section->custom_css ?? ''));

    $resolveOrnament = function ($src) {
        if (empty($src)) return null;
        return \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : '/storage/' . ltrim($src, '/');
    };
    $isSvg = fn ($src) => $src && \Illuminate\Support\Str::endsWith(strtolower($src), '.svg');

    // Bangun list ornamen per slot; fallback ke field tunggal lama (back-compat).
    $ornList = function (string $listKey, string $legacyKey) use ($props) {
        $list = is_array($props[$listKey] ?? null) ? $props[$listKey] : [];
        if (empty($list) && !empty($props[$legacyKey])) {
            $legacyPos = $props[$legacyKey.'_position'] ?? null;
            $pos = match ($legacyPos) {
                'corner-tr', 'corner-br' => 'right',
                'center' => 'center',
                'full-width' => 'full-width',
                default => 'left',
            };
            $list = [[
                'src' => $props[$legacyKey],
                'position' => $pos,
                'scale' => $props[$legacyKey.'_scale'] ?? 100,
                'color' => $props[$legacyKey.'_color'] ?? null,
                'flip_h' => false, 'flip_v' => false,
            ]];
        }
        return $list;
    };
    $ornamentsTop = $ornList('ornaments_top', 'ornament_top');
    $ornamentsBottom = $ornList('ornaments_bottom', 'ornament_bottom');
    $hasOrnaments = !empty($ornamentsTop) || !empty($ornamentsBottom);

    // Style satu item. Flip via transform (+translateX untuk center). Mask bila svg+color.
    $ornItemStyle = function (array $it, string $edge, bool $mask) {
        $pos = in_array($it['position'] ?? null, ['left', 'right', 'center', 'full-width'], true) ? $it['position'] : 'left';
        $scale = is_numeric($it['scale'] ?? null) ? (float) $it['scale'] : 100;
        $tf = [];
        if ($pos === 'center') $tf[] = 'translateX(-50%)';
        if (!empty($it['flip_h'])) $tf[] = 'scaleX(-1)';
        if (!empty($it['flip_v'])) $tf[] = 'scaleY(-1)';
        $transform = $tf ? 'transform:'.implode(' ', $tf).';' : '';
        $box = match ($pos) {
            'full-width' => 'left:0;right:0;width:100%;',
            'center' => "left:50%;width:{$scale}%;",
            'right' => "right:0;width:{$scale}%;",
            default => "left:0;width:{$scale}%;",
        };
        $style = "position:absolute;{$edge}:0;pointer-events:none;z-index:10;".$box.$transform;
        if ($mask) {
            $aspect = match ($pos) {
                'full-width' => '6 / 1',
                'center' => '4 / 1',
                default => '1 / 1',
            };
            $style .= "aspect-ratio:{$aspect};";
        }
        return $style;
    };

    // Cover punya sistem visual sendiri (gate position:fixed + layar sticky) dan
    // background_image sendiri. Treatment shell menimpa positioning anak-anaknya
    // (.sec-treat--image/pinned > :not(.sec-bg) { position: relative }) sehingga
    // gate berhenti full-viewport dan terklip overflow:hidden — jadi cover
    // dikecualikan dari treatment/bg_effect sepenuhnya.
    $ownsVisual = $section->section_type === 'cover';

    // Pilihan medianya milik BackgroundMedia — komponen cover memakai keputusan yang sama
    // dengan markup yang berbeda, jadi logikanya tidak boleh hidup di salah satu view.
    $media = $ownsVisual ? null : \App\Services\BackgroundMedia::resolve($props);
    $bgImage = $media['image'] ?? null;
    $bgPoster = $media['poster'] ?? null;
    $bgSlides = $media['slides'] ?? [];
    $bgVideo = $media['video'] ?? null;
    $bgYoutube = $media['youtube'] ?? null;
    $mediaType = $media['type'] ?? 'image';
    $hasBgMedia = (bool) ($media['has'] ?? false);

    $treatment = $ownsVisual ? 'surface' : ($props['treatment'] ?? 'surface');
    // image tanpa media = teks terang di atas latar terang (tak terbaca) → jatuhkan ke surface.
    if ($treatment === 'image' && ! $hasBgMedia) {
        $treatment = 'surface';
    }
    $bgOverlay = max(0, min(100, (int) ($props['bg_overlay'] ?? 45)));
    // Efek berlaku untuk ketiga jenis media, termasuk pinned pada latar YouTube: saat
    // pinned, ukuran bingkai iframe diturunkan dari --pin-h (tinggi kartu), bukan dari
    // tinggi section — lihat .sec-treat--pinned .sec-bg-ytwrap di invitation.css.
    $bgEffect = $ownsVisual || $treatment !== 'image' ? 'none' : ($props['bg_effect'] ?? 'none');
    $bgStrength = max(100, min(200, (int) ($props['bg_effect_strength'] ?? 130)));
    $slideSeconds = $media['slideSeconds'] ?? 5;
    $slideKeyframes = $media['keyframes'] ?? '';
    $hasTreatment = $treatment !== 'surface' || $hasBgMedia;

    $needsShell = $hasOrnaments || $animation !== 'none' || $customCss !== '' || $hasTreatment;
@endphp

@if (!view()->exists($viewPath))
    @php(\Illuminate\Support\Facades\Log::warning("Invitation component view not found: {$section->section_type}", ['section_id' => $section->id]))
    <!-- Component {{ $section->section_type }} not found -->
@elseif (!$needsShell)
    <div style="display: contents" data-section-id="{{ $section->id }}">
        @include($viewPath, [
            'props' => $props,
            'section' => $section,
            'page' => $page,
            'elements' => $elements,
        ])
    </div>
@else
    <div class="sec-treat sec-treat--{{ $treatment }}{{ $bgEffect === 'pinned' ? ' sec-treat--pinned' : '' }}" data-section-id="{{ $section->id }}"
        @if ($animation !== 'none') data-animate="{{ $animation }}" data-animate-delay="{{ $animationDelay }}" @endif>
        @if ($hasTreatment && $treatment === 'image' && $hasBgMedia)
            @if ($slideKeyframes)
                {{-- Crossfade murni CSS: tiap slide tampil 1/n siklus dengan jendela redup
                     yang beririsan dengan slide berikutnya. Persentasenya bergantung jumlah
                     slide, jadi keyframe digenerate per-n. Namanya memakai n saja: dua
                     section dengan jumlah slide sama menghasilkan definisi identik, jadi
                     duplikatnya tidak berbahaya. --}}
                <style>{!! $slideKeyframes !!}</style>
            @endif
            <div class="sec-bg{{ $bgSlides ? ' sec-bg--slideshow' : '' }}{{ $bgYoutube ? ' sec-bg--youtube' : '' }}" aria-hidden="true"
                @if ($bgEffect !== 'none') data-effect="{{ $bgEffect }}" data-strength="{{ $bgStrength }}" @endif
                @if ($bgSlides) style="background-image:url('{{ $bgSlides[0] }}');--slide-dur:{{ $slideSeconds }}s;--slide-n:{{ count($bgSlides) }};--slide-fade:sec-bgslide-{{ count($bgSlides) }}"
                {{-- Latar YouTube selalu punya foto di depannya: iframe baru ditampilkan
                     beberapa detik setelah pemutaran mulai, dan tanpa foto jeda itu jadi
                     kotak kosong. Kalau poster belum diisi, pakai thumbnail video itu
                     sendiri — dua lapis, karena maxres tidak tersedia untuk semua video
                     dan kegagalannya diam; lapis di bawahnya selalu ada. --}}
                @elseif ($bgYoutube) style="background-image:@if ($bgPoster)url('{{ $bgPoster }}')@else {!! \App\Services\BackgroundMedia::youtubePosterCss($bgYoutube, null) !!}@endif" @endif>
                @if ($bgYoutube)
                    {{-- Pembungkus yang diukur dan diberi efek; iframe di dalamnya cuma
                         mengisi. Dipisah karena keduanya butuh slot animation sendiri —
                         efek latar di pembungkus, tunda-tampil di iframe.
                         Ukurannya: 16:9 dipaksa menutupi kotak section lewat unit container
                         (cqh) lalu dipusatkan, sisi berlebih terpotong seperti object-fit
                         cover. pointer-events dimatikan di CSS supaya iframe tidak
                         menangkap sentuhan tamu.
                         nocookie: penonton tidak ditandai cookie iklan hanya karena
                         membuka undangan. loop butuh playlist berisi ID yang sama. --}}
                    <div class="sec-bg-ytwrap">
                        <iframe class="sec-bg-yt" title="" tabindex="-1" frameborder="0" allow="autoplay"
                            src="https://www.youtube-nocookie.com/embed/{{ $bgYoutube }}?autoplay=1&amp;mute=1&amp;loop=1&amp;playlist={{ $bgYoutube }}&amp;controls=0&amp;disablekb=1&amp;fs=0&amp;modestbranding=1&amp;rel=0&amp;iv_load_policy=3&amp;playsinline=1&amp;enablejsapi=1"></iframe>
                    </div>
                @elseif ($bgVideo)
                    {{-- muted+playsinline wajib supaya autoplay tidak diblokir browser mobile.
                         poster menutup jeda sebelum frame pertama termuat. --}}
                    <video class="sec-bg-video" src="{{ $bgVideo }}" autoplay muted loop playsinline
                        preload="metadata" @if ($bgPoster) poster="{{ $bgPoster }}" @endif></video>
                @elseif ($bgSlides)
                    {{-- Hanya urutan slide yang inline. Nama keyframe dan durasinya lewat
                         variabel di wadah, supaya CSS bisa menambahkan animasi kedua
                         (kenburns) tanpa ditimpa gaya inline. --}}
                    @foreach ($bgSlides as $i => $slide)
                        <div class="sec-bg-img sec-bg-slide" style="background-image:url('{{ $slide }}');--slide-i:{{ $i }}"></div>
                    @endforeach
                @else
                    <div class="sec-bg-img" style="background-image:url('{{ $bgImage }}')"></div>
                @endif
                <div class="sec-bg-overlay" style="opacity:{{ rtrim(rtrim(number_format($bgOverlay/100, 2, '.', ''), '0'), '.') }}"></div>
            </div>
        @endif
        @if ($customCss !== '')
            {{-- Scoping via CSS nesting native — server yang membungkus (proposal §5.4);
                 updateSection menolak payload dengan <> sehingga tag tidak bisa ditutup. --}}
            <style>[data-section-id="{{ $section->id }}"] { {!! $customCss !!} }</style>
        @endif
        @foreach ($ornamentsTop as $it)
            @php($src = $resolveOrnament($it['src'] ?? null))
            @if ($src)
                @if ($isSvg($src) && !empty($it['color']))
                    <div aria-hidden="true"
                        style="{{ $ornItemStyle($it, 'top', true) }}-webkit-mask-image:url('{{ $src }}');-webkit-mask-repeat:no-repeat;-webkit-mask-position:center;-webkit-mask-size:contain;mask-image:url('{{ $src }}');mask-repeat:no-repeat;mask-position:center;mask-size:contain;background-color:{{ $it['color'] }};"></div>
                @else
                    <img src="{{ $src }}" alt="" style="{{ $ornItemStyle($it, 'top', false) }}">
                @endif
            @endif
        @endforeach
        @include($viewPath, [
            'props' => $props,
            'section' => $section,
            'page' => $page,
            'elements' => $elements,
        ])
        @foreach ($ornamentsBottom as $it)
            @php($src = $resolveOrnament($it['src'] ?? null))
            @if ($src)
                @if ($isSvg($src) && !empty($it['color']))
                    <div aria-hidden="true"
                        style="{{ $ornItemStyle($it, 'bottom', true) }}-webkit-mask-image:url('{{ $src }}');-webkit-mask-repeat:no-repeat;-webkit-mask-position:center;-webkit-mask-size:contain;mask-image:url('{{ $src }}');mask-repeat:no-repeat;mask-position:center;mask-size:contain;background-color:{{ $it['color'] }};"></div>
                @else
                    <img src="{{ $src }}" alt="" style="{{ $ornItemStyle($it, 'bottom', false) }}">
                @endif
            @endif
        @endforeach
    </div>
@endif
