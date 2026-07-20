<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Luminara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: system-ui, sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-gray-500 mt-2">Dashboard undangan Anda akan segera hadir.</p>
        <form action="{{ route('logout') }}" method="POST" class="mt-6">
            @csrf
            <button class="text-sm text-gray-600 hover:text-black">Keluar</button>
        </form>
    </div>
</body>
</html>
