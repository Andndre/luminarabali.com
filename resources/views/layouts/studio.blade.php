<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'Studio') - Luminara</title>
    {{-- app.css sudah memindai seluruh resources/views (@source di file itu), jadi
         utility Studio ikut terbangun tanpa entry Tailwind tersendiri. --}}
    @vite(['resources/css/app.css', 'resources/js/studio.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" crossorigin="anonymous">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Palet chrome Studio. Panel gelap dan diam supaya undangan di kanvas jadi
           satu-satunya benda terang di layar — panel terang di sekelilingnya menipu
           penilaian warna tema. Semua kelas di Blade menunjuk var ini lewat
           bg-[var(--ui-…)], jadi menyetel ulang skema cukup di blok ini.

           HANYA chrome editor. Isi iframe preview memakai token temanya sendiri
           (--color-*) dan tak terpengaruh apa pun di sini. */
        :root {
            --ui-bg: #1b1d22;       /* meja kerja: kanvas + body */
            --ui-panel: #16181d;    /* topbar, panel kiri, inspector */
            --ui-raised: #22252c;   /* kontrol & permukaan naik di atas panel */
            --ui-hover: #24272f;    /* baris hover, track pill */
            --ui-active: #3a3f4a;   /* pill terpilih — duduk di atas track, harus lebih terang */
            --ui-line: #2a2d35;     /* pemisah antar panel */
            --ui-line-2: #31353f;   /* garis kontrol */
            --ui-line-3: #454a56;   /* garis kontrol saat hover */
            --ui-text: #f2f4f7;
            --ui-text-2: #cfd3db;
            --ui-text-3: #9aa0ac;
            --ui-text-4: #7d838f;
            --ui-accent: #e8eaef;   /* aksi utama: dulu hitam-di-terang, kini terang-di-gelap */
        }

        /* Warna teks dasar. Banyak elemen (nama section, ikon berbasis currentColor)
           tak punya kelas warna sama sekali dan dulu mewarisi hitam bawaan browser —
           di panel gelap itu jadi teks hitam di atas gelap. Kelas text-* tetap menang
           karena ini cuma menetapkan warisan, bukan menimpa utility. */
        .studio-chrome { color: var(--ui-text-2); }

        /* Kontrol form tak punya kelas latar di Blade — tanpa aturan ini semuanya
           putih dengan teks terang di atas panel gelap. Selektor elemen, bukan
           utility, supaya tak perlu menyunting ~40 daftar kelas.
           Catatan: blok <style> ini tanpa layer, jadi ia MENGALAHKAN utility
           Tailwind. Kelas bg-* pada input tidak akan berpengaruh selama aturan
           ini ada. */
        .studio-chrome :is(input, select, textarea) {
            background-color: var(--ui-raised);
            color: var(--ui-text);
        }
        .studio-chrome :is(input, select, textarea)::placeholder { color: var(--ui-text-4); }
        .studio-chrome input[type="color"] { background: transparent; padding: 0; }
        .studio-chrome input[type="checkbox"] { background-color: var(--ui-raised); }
        .studio-chrome input[type="checkbox"]:checked { background-color: var(--ui-accent); }
        /* Panah <select> bawaan digambar gelap-di-gelap oleh sebagian browser. */
        .studio-chrome select { color-scheme: dark; }
        .studio-chrome summary::marker { color: var(--ui-text-4); }

        .studio-chrome ::-webkit-scrollbar { width: 10px; height: 10px; }
        .studio-chrome ::-webkit-scrollbar-thumb {
            background: var(--ui-line-2); border-radius: 6px;
            border: 3px solid transparent; background-clip: content-box;
        }
        .studio-chrome ::-webkit-scrollbar-thumb:hover { background: var(--ui-line-3); background-clip: content-box; }
        .studio-chrome ::-webkit-scrollbar-track { background: transparent; }

        /* Editor kode: textarea transparan menumpuk <pre> berwarna. Setiap properti yang
           memengaruhi posisi glyph harus sama persis di kedua lapis, kalau tidak kursor
           dan teks berwarna saling melenceng makin jauh tiap baris. */
        .code-editor { position: relative; }
        .code-editor-hl,
        .code-editor-input {
            margin: 0;
            padding: .5rem .75rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px;
            line-height: 1.6;
            tab-size: 2;
            white-space: pre-wrap;
            word-break: break-word;
            overflow-wrap: break-word;
            border: 0;
        }
        .code-editor-hl {
            position: absolute;
            inset: 0;
            overflow: auto;
            border-radius: .5rem;
            background: #1e1e23;
            color: #d6d3ce;
            pointer-events: none;
        }
        .code-editor-input {
            position: relative;
            display: block;
            width: 100%;
            min-height: 12rem;
            resize: vertical;
            background: transparent;
            color: transparent;
            caret-color: #f5f1e8;
            border-radius: .5rem;
            outline: 1px solid #e5e7eb;
        }
        .code-editor-input:focus { outline: 2px solid #000; }
        .code-editor-input::selection { background: rgba(120, 160, 255, .35); }
        .tok-tag { color: #7ab7f5; }
        .tok-attr { color: #c8a6f0; }
        .tok-str { color: #a9d98a; }
        .tok-cmt { color: #6f6a63; font-style: italic; }
        .tok-doctype { color: #e0996b; }

        /* Color picker in-app (ganti dialog OS). Semua warna dari palet --ui-*. Markup di
           studio/_color-picker.blade.php, dipicu tombol .cpick-trigger tiap field warna. */
        .cpick-trigger {
            display: flex; align-items: center; gap: 8px; width: 100%;
            border: 1px solid var(--ui-line-2); border-radius: 8px; padding: 5px 8px;
            background: var(--ui-raised); color: var(--ui-text); cursor: pointer;
            text-align: left; transition: border-color .12s;
        }
        .cpick-trigger:hover { border-color: var(--ui-line-3); }
        .cpick-trigger.open { border-color: var(--ui-active); }
        .cpick-sw { width: 24px; height: 24px; border-radius: 6px; flex: 0 0 auto; box-shadow: inset 0 0 0 1px rgba(255,255,255,.14); }
        .cpick-val {
            flex: 1; min-width: 0; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12.5px; text-transform: uppercase; letter-spacing: .04em;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        /* Popup melayang di atas field, bukan mendorongnya. Anchor relatif membatasi
           picker ke lebar field, jadi tak pernah keluar dari panel. */
        .cpick-anchor { position: relative; }
        .cpick {
            position: absolute; z-index: 40; top: calc(100% + 6px); left: 0;
            width: 264px; max-width: calc(100vw - 32px);
            background: var(--ui-raised); border: 1px solid var(--ui-line-3);
            border-radius: 10px; padding: 10px;
            box-shadow: 0 18px 40px -12px rgba(0,0,0,.6);
        }
        /* Panel Tema 2-kolom: cell kanan buka ke kiri supaya popup lebar (yang melampaui
           lebar cell) tetap dalam panel — panel overflow-y-auto mengklip yang lewat tepi.
           Kelas dipasang via indeks Alpine, bukan nth-child (Alpine menyisakan node
           <template> sebagai anak pertama, menggeser hitungan nth-child). */
        .cpick-open-left .cpick { left: auto; right: 0; }
        .cpick-sv { position: relative; height: 168px; border-radius: 7px; cursor: crosshair; touch-action: none; box-shadow: inset 0 0 0 1px rgba(255,255,255,.08); }
        .cpick-hue {
            position: relative; height: 12px; border-radius: 6px; margin-top: 10px; cursor: pointer; touch-action: none;
            background: linear-gradient(to right,#f00 0%,#ff0 17%,#0f0 33%,#0ff 50%,#00f 67%,#f0f 83%,#f00 100%);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
        }
        .cpick-thumb {
            position: absolute; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff;
            box-shadow: 0 0 0 1px rgba(0,0,0,.45), 0 1px 3px rgba(0,0,0,.5);
            transform: translate(-50%,-50%); pointer-events: none;
        }
        .cpick-hue-thumb { top: 50%; }
        .cpick-inputs { margin-top: 10px; }
        .cpick-hex { display: flex; align-items: center; gap: 6px; border: 1px solid var(--ui-line-2); border-radius: 7px; padding: 4px 8px; background: var(--ui-panel); }
        .cpick-hex span { color: var(--ui-text-4); font-family: ui-monospace, monospace; font-size: 12px; }
        .cpick-hex input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; color: var(--ui-text); font-family: ui-monospace, monospace; font-size: 12.5px; text-transform: uppercase; }
        .cpick-rgb { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-top: 7px; }
        .cpick-rgb label { display: flex; flex-direction: column; align-items: center; gap: 2px; min-width: 0; }
        .cpick-rgb span { font-size: 9px; color: var(--ui-text-4); text-transform: uppercase; letter-spacing: .08em; }
        .cpick-rgb input { width: 100%; min-width: 0; text-align: center; border: 1px solid var(--ui-line-2); border-radius: 6px; padding: 3px 0; color: var(--ui-text); font-family: ui-monospace, monospace; font-size: 12px; outline: 0; background: var(--ui-panel); }
        .cpick-tokens { margin-top: 10px; }
        .cpick-tok-h { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: var(--ui-text-4); }
        .cpick-tok-row { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 5px; }
        .cpick-tok { width: 20px; height: 20px; border-radius: 5px; cursor: pointer; box-shadow: inset 0 0 0 1px rgba(255,255,255,.14); transition: transform .1s; }
        .cpick-tok:hover { transform: scale(1.14); }

        /* Number stepper (ganti spinner native yang sempit). Markup di studio/_stepper.blade.php. */
        .stepper { display: flex; align-items: stretch; border: 1px solid var(--ui-line-2); border-radius: 8px; overflow: hidden; background: var(--ui-raised); transition: border-color .12s; }
        .stepper:focus-within { border-color: var(--ui-active); }
        .stepper input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; color: var(--ui-text); font-size: 14px; padding: 8px 0 8px 11px; border-radius: 0; -moz-appearance: textfield; }
        .stepper input::-webkit-outer-spin-button,
        .stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .stepper-btns { display: flex; flex-direction: column; width: 28px; flex: 0 0 auto; border-left: 1px solid var(--ui-line-2); }
        .stepper-btns button { flex: 1; background: transparent; border: 0; cursor: pointer; color: var(--ui-text-3); display: grid; place-items: center; padding: 0; transition: background .1s, color .1s; }
        .stepper-btns button:hover { background: var(--ui-hover); color: var(--ui-text); }
        .stepper-btns button:active { background: var(--ui-active); }
        .stepper-btns button:first-child { border-bottom: 1px solid var(--ui-line-2); }
        .stepper-btns svg { width: 11px; height: 11px; }
    </style>
</head>
<body class="bg-[var(--ui-bg)] overflow-hidden">
    @yield('content')
    @stack('scripts')
</body>
</html>
