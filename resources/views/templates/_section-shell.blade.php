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

    $bgImage = $ownsVisual ? null : $resolveOrnament($props['bg_image'] ?? null); // reuse resolver path

    // Latar boleh foto tunggal, slideshow, atau video. bg_image tetap dipakai ketiganya:
    // foto tunggal, cadangan slideshow saat animasi mati, dan poster video.
    $mediaType = in_array($props['bg_media_type'] ?? null, ['image', 'slideshow', 'video'], true)
        ? $props['bg_media_type'] : 'image';
    $bgSlides = [];
    $bgVideo = null;
    $bgYoutube = null;
    if (! $ownsVisual && $mediaType === 'slideshow') {
        foreach (is_array($props['bg_images'] ?? null) ? $props['bg_images'] : [] as $item) {
            $src = $resolveOrnament(is_array($item) ? ($item['url'] ?? null) : $item);
            if ($src) $bgSlides[] = $src;
        }
        // Satu foto bukan slideshow; jadikan foto tunggal supaya tidak ada animasi sia-sia.
        if (count($bgSlides) === 1) {
            $bgImage = $bgImage ?: $bgSlides[0];
            $bgSlides = [];
            $mediaType = 'image';
        }
    } elseif (! $ownsVisual && $mediaType === 'video') {
        // Satu kolom, dua sumber. Tautan YouTube dikenali dari isinya — dan harus dicek
        // lebih dulu: URL apa pun lolos $resolveOrnament apa adanya, jadi tanpa cek ini
        // tautan YouTube akan berakhir sebagai src <video> yang tidak bisa diputar.
        // Hanya ID-nya yang dipakai; sisa URL milik pengguna tak pernah ikut ke src.
        $bgYoutube = \App\Services\InvitationRenderer::youtubeId($props['bg_video'] ?? null);
        if (! $bgYoutube) {
            // Selain YouTube, hanya berkas video yang diterima. Tautan halaman (Vimeo dan
            // sejenisnya) bukan berkas: kalau diteruskan ke <video> ia gagal tanpa pesan,
            // jadi lebih baik jatuh ke foto yang memang terlihat.
            $candidate = $resolveOrnament($props['bg_video'] ?? null);
            $ext = $candidate ? strtolower(pathinfo(parse_url($candidate, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)) : '';
            $bgVideo = in_array($ext, ['mp4', 'webm', 'ogv', 'ogg', 'mov', 'm4v'], true) ? $candidate : null;
        }
        if (! $bgVideo && ! $bgYoutube) {
            $mediaType = 'image'; // video belum diisi → jatuh ke foto/poster-nya
        }
    }
    $hasBgMedia = (bool) ($bgImage || $bgSlides || $bgVideo || $bgYoutube);

    $treatment = $ownsVisual ? 'surface' : ($props['treatment'] ?? 'surface');
    // image tanpa media = teks terang di atas latar terang (tak terbaca) → jatuhkan ke surface.
    if ($treatment === 'image' && ! $hasBgMedia) {
        $treatment = 'surface';
    }
    $bgOverlay = max(0, min(100, (int) ($props['bg_overlay'] ?? 45)));
    // Efek latar menganimasikan .sec-bg-img lewat animation/transform — properti yang sama
    // dipakai crossfade slideshow, dan video bukan .sec-bg-img sama sekali. Daripada dua
    // aturan saling menimpa diam-diam, efek hanya berlaku untuk foto tunggal.
    $bgEffect = $ownsVisual || $treatment !== 'image' || $mediaType !== 'image'
        ? 'none' : ($props['bg_effect'] ?? 'none');
    $bgStrength = max(100, min(200, (int) ($props['bg_effect_strength'] ?? 130)));
    $slideSeconds = max(2, min(30, (int) ($props['bg_slide_seconds'] ?? 5)));

    // Keyframe crossfade dihitung di sini, bukan di badan view. Blade menyimpan blok PHP
    // dengan regex non-greedy, jadi blok kedua di berkas ini akan menelan bentuk sebaris
    // yang dipakai di bawah (Log::warning dan $resolveOrnament) menjadi satu blok mentah.
    $slideKeyframes = '';
    if ($bgSlides) {
        $fade = 6; // persen siklus untuk satu transisi
        $hold = 100 / count($bgSlides);
        $pct = fn (float $v) => rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');
        $slideKeyframes = '@keyframes sec-bgslide-'.count($bgSlides)
            .'{0%{opacity:0}'.$pct($fade).'%{opacity:1}'.$pct($hold).'%{opacity:1}'
            .$pct($hold + $fade).'%{opacity:0}100%{opacity:0}}';
    }
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
                @if ($bgSlides) style="background-image:url('{{ $bgImage ?: $bgSlides[0] }}');--slide-dur:{{ $slideSeconds }}s;--slide-n:{{ count($bgSlides) }}"
                @elseif ($bgYoutube && $bgImage) style="background-image:url('{{ $bgImage }}')" @endif>
                @if ($bgYoutube)
                    {{-- Iframe berformat 16:9 dipaksa menutupi kotak section lewat unit
                         container (cqh), lalu dipusatkan — sisi yang berlebih terpotong,
                         seperti object-fit:cover. pointer-events dimatikan di CSS supaya
                         iframe tidak menangkap sentuhan tamu.
                         nocookie: penonton tidak ditandai cookie iklan hanya karena
                         membuka undangan. loop butuh playlist berisi ID yang sama. --}}
                    <iframe class="sec-bg-yt" title="" tabindex="-1" frameborder="0" allow="autoplay"
                        src="https://www.youtube-nocookie.com/embed/{{ $bgYoutube }}?autoplay=1&amp;mute=1&amp;loop=1&amp;playlist={{ $bgYoutube }}&amp;controls=0&amp;disablekb=1&amp;fs=0&amp;modestbranding=1&amp;rel=0&amp;iv_load_policy=3&amp;playsinline=1"></iframe>
                @elseif ($bgVideo)
                    {{-- muted+playsinline wajib supaya autoplay tidak diblokir browser mobile.
                         poster menutup jeda sebelum frame pertama termuat. --}}
                    <video class="sec-bg-video" src="{{ $bgVideo }}" autoplay muted loop playsinline
                        preload="metadata" @if ($bgImage) poster="{{ $bgImage }}" @endif></video>
                @elseif ($bgSlides)
                    @foreach ($bgSlides as $i => $slide)
                        <div class="sec-bg-img sec-bg-slide" style="background-image:url('{{ $slide }}');animation-name:sec-bgslide-{{ count($bgSlides) }};animation-delay:calc(var(--slide-dur) * {{ $i }})"></div>
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
