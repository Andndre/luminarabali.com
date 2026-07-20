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
<body class="bg-gray-50 overflow-hidden">
    @yield('content')
    @stack('scripts')
</body>
</html>
