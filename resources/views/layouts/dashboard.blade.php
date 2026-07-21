<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | Luminara</title>
    @vite(['resources/css/app.css'])
    @stack('scripts')
</head>
<body class="dash">

<div class="dash-shell">
    <div class="dash-scrim" data-dash-scrim></div>

    {{-- id dipakai aria-controls tombol hamburger. --}}
    <aside class="dash-sidebar" id="dash-sidebar" data-dash-sidebar>
        <div class="dash-brand">Luminara</div>
        {{-- aria-current="page" menandai halaman aktif ke pembaca layar; kelas
             is-active hanya menyampaikannya secara visual. --}}
        <nav class="dash-nav" aria-label="Menu utama">
            @php $onDashboard = request()->routeIs('dashboard'); @endphp
            <a href="{{ route('dashboard') }}"
               @if ($onDashboard) aria-current="page" @endif
               class="dash-nav__link {{ $onDashboard ? 'is-active' : '' }}">Ringkasan</a>
            @php $onInvitations = request()->routeIs('invitations.*'); @endphp
            <a href="{{ route('invitations.index') }}"
               @if ($onInvitations) aria-current="page" @endif
               class="dash-nav__link {{ $onInvitations ? 'is-active' : '' }}">Undangan</a>
            @php $onOrders = request()->routeIs('orders.*'); @endphp
            <a href="{{ route('orders.index') }}"
               @if ($onOrders) aria-current="page" @endif
               class="dash-nav__link {{ $onOrders ? 'is-active' : '' }}">Pesanan</a>
        </nav>
        <div class="dash-sidebar__foot">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dash-btn dash-btn--ghost" style="width: 100%">Keluar</button>
            </form>
        </div>
    </aside>

    <main class="dash-main">
        <div class="dash-topbar">
            <button type="button" class="dash-burger" data-dash-burger
                    aria-label="Buka menu" aria-controls="dash-sidebar" aria-expanded="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/>
                </svg>
            </button>
            <span style="font-weight: 600">@yield('title', 'Dashboard')</span>
        </div>

        <div class="dash-content">
            @if (session('success'))
                <div class="dash-flash dash-flash--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-flash dash-flash--error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
(() => {
    // Drawer sidebar mobile. Vanilla: dashboard tak memuat Alpine.
    const sidebar = document.querySelector('[data-dash-sidebar]');
    const scrim = document.querySelector('[data-dash-scrim]');
    const burger = document.querySelector('[data-dash-burger]');
    const toggle = (open) => {
        sidebar.classList.toggle('is-open', open);
        scrim.classList.toggle('is-open', open);
        // Status harus ikut berubah, bukan cuma atribut statis di markup.
        burger?.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    burger?.addEventListener('click', () => toggle(true));
    scrim?.addEventListener('click', () => toggle(false));
    // Esc menutup drawer: keyboard tak boleh terjebak di menu yang terbuka.
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar?.classList.contains('is-open')) toggle(false);
    });
})();
</script>
</body>
</html>
