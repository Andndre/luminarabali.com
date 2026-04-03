<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Luminara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 overflow-x-hidden">

    <div class="min-h-screen flex w-full">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full z-20 transition-transform transform md:translate-x-0 -translate-x-full" id="sidebar">
            <div class="h-16 flex flex-col items-center justify-center border-b border-gray-800">
                <span class="text-xl font-bold tracking-wider text-yellow-500 uppercase">LUMINARA</span>
                <span class="text-l font-bold tracking-wider text-yellow-500 uppercase">
                    {{ str_replace('_', ' ', auth()->user()->division) }}
                </span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.bookings.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Bookings
                </a>

                <a href="{{ route('admin.invoices.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.invoices.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Invoice
                </a>

                <a href="{{ route('admin.finance.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.finance.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Keuangan
                </a>

                <a href="{{ route('admin.packages.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.packages.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Paket & Harga
                </a>

                <a href="{{ route('admin.calendar.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.calendar.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Kalender / Block
                </a>

                <a href="{{ route('admin.galleries.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.galleries.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Galeri & Portfolio
                </a>

                <a href="{{ route('admin.links.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.links.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    Links
                </a>

                @if(auth()->user()->division == 'super_admin')
                <div class="pt-4 mt-4 border-t border-gray-800">
                    <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Undangan Digital</p>
                    <a href="{{ route('admin.templates.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.templates.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                        Templates
                    </a>
                    <a href="{{ route('admin.invitations.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.invitations.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Undangan
                    </a>
                    <a href="{{ route('admin.assets.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.assets.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Media Library
                    </a>
                </div>

                <div class="pt-4 mt-4 border-t border-gray-800">
                    <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Super Admin</p>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-yellow-500 text-black font-bold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Kelola Admin
                    </a>
                </div>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-red-400 hover:text-white hover:bg-red-900 rounded-lg transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay for mobile sidebar -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden md:hidden glass transition-opacity opacity-0"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:ml-64 transition-all min-w-0 w-full max-w-full">
            <!-- Mobile Header -->
            <header class="bg-white shadow-sm md:hidden h-16 flex items-center px-4 justify-between sticky top-0 z-10">
                <span class="font-bold text-lg">LUMINARA ADMIN</span>
                <button onclick="toggleSidebar()" class="text-gray-600 focus:outline-none p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <script>
                function toggleSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('sidebar-overlay');

                    if (sidebar.classList.contains('-translate-x-full')) {
                        // Open
                        sidebar.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                    } else {
                        // Close
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('opacity-0');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    }
                }
            </script>

            <main class="flex-1 p-4 md:p-8 overflow-y-auto">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                        <p class="font-bold">Berhasil</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                        <p class="font-bold">Terjadi Kesalahan</p>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
