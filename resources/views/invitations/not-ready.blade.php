<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
               font-family: Georgia, serif; background: #fffaf3; color: #3b2f2f; text-align: center; }
        .wrap { padding: 2rem; }
        h1 { font-size: 1.5rem; margin-bottom: .5rem; }
        p { color: #6b5f5f; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Undangan belum siap</h1>
        <p>Undangan untuk {{ $page->groom_name }} &amp; {{ $page->bride_name }} sedang dipersiapkan. Silakan kembali lagi nanti.</p>
    </div>
</body>
</html>
