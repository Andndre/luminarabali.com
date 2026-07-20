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
    </style>
</head>
<body class="bg-[var(--ui-bg)] overflow-hidden">
    @yield('content')
    @stack('scripts')
</body>
</html>
