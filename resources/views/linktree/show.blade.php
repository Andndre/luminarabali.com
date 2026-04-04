<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luminara {{ $division === 'LuminaraPhotobooth' ? 'Photobooth' : 'Visual' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-maroon {
            background-color: #7c0d1e;
        }

        .bg-maroon-dark {
            background-color: #4a080f;
        }
    </style>
</head>

<body class="bg-maroon-dark flex min-h-dvh flex-col items-center px-4 py-10 font-sans">

    {{-- Mobile outer frame with rounded edges --}}
    <div class="rounded-4xl bg-maroon w-full max-w-md px-5 py-6 shadow-2xl">

        {{-- Header --}}
        <div class="mb-6 text-center">
            <div
                class="mx-auto mb-3 flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-white shadow-lg">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-full w-full object-contain">
            </div>
            <h1 class="text-xl font-bold tracking-wide text-white">
                Luminara {{ $division === 'LuminaraPhotobooth' ? 'Photobooth' : 'Visual' }}
            </h1>
            <p class="mt-1 text-sm text-white/70">Simplify your access</p>
        </div>

        {{-- Links --}}
        <div class="space-y-3">

            {{-- Today's booking links --}}
            @foreach ($todayBookingLinks as $link)
                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                    class="block overflow-hidden rounded-2xl bg-white shadow-sm transition-transform duration-200 hover:scale-[1.02] hover:shadow-md">

                    @if ($link->thumbnail)
                        <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                            <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                class="h-full w-full object-cover">
                        </div>
                    @endif

                    <div class="flex items-center gap-3 p-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50">
                            <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-gray-900">{{ $link->title }}</div>
                            @if ($link->event_date)
                                <div class="mt-0.5 flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($link->event_date)->format('d M Y') }}
                                </div>
                            @endif
                        </div>

                        <span
                            class="shrink-0 rounded-full bg-pink-500 px-2.5 py-0.5 text-xs font-bold text-white">Hari
                            Ini</span>

                        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </div>
                </a>
            @endforeach

            {{-- Divider: today → admin --}}
            @if ($todayBookingLinks->isNotEmpty() && $adminLinks->isNotEmpty())
                <div class="flex items-center gap-3 py-1">
                    <div class="h-px flex-1 bg-white/20"></div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-white">Lainnya</span>
                    <div class="h-px flex-1 bg-white/20"></div>
                </div>
            @endif

            {{-- Admin links --}}
            @forelse ($adminLinks as $link)
                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                    class="block overflow-hidden rounded-2xl bg-white shadow-sm transition-transform duration-200 hover:scale-[1.02] hover:shadow-md">

                    @if ($link->thumbnail)
                        <div class="h-36 w-full overflow-hidden bg-gray-100">
                            <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                class="h-full w-full object-cover">
                        </div>
                    @endif

                    <div class="flex items-center gap-3 p-3">
                        @if ($link->icon)
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50">
                                <img src="{{ asset('images/icons/' . $link->icon . '.svg') }}"
                                    alt="{{ $link->title }}" class="h-6 w-6 object-contain">
                            </div>
                        @else
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                        @endif

                        <span class="flex-1 truncate text-sm font-semibold text-gray-900">{{ $link->title }}</span>

                        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </div>
                </a>
            @empty
                @if ($todayBookingLinks->isEmpty() && $olderBookingLinks->isEmpty())
                    <div class="rounded-2xl border border-white/20 bg-white/10 p-6 text-center">
                        <p class="text-sm text-white">Belum ada link tersedia.</p>
                    </div>
                @endif
            @endforelse

            {{-- Divider: admin → older bookings --}}
            @if ($adminLinks->isNotEmpty() && $olderBookingLinks->isNotEmpty())
                <div class="flex items-center gap-3 py-1">
                    <div class="h-px flex-1 bg-white/20"></div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-white">Arsip</span>
                    <div class="h-px flex-1 bg-white/20"></div>
                </div>
            @endif

            {{-- Older booking links (2+ days ago) --}}
            @foreach ($olderBookingLinks as $link)
                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                    class="block overflow-hidden rounded-2xl bg-white shadow-sm transition-transform duration-200 hover:scale-[1.02] hover:shadow-md">

                    @if ($link->thumbnail)
                        <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                            <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                class="h-full w-full object-cover">
                        </div>
                    @endif

                    <div class="flex items-center gap-3 p-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50">
                            <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-gray-900">{{ $link->title }}</div>
                            @if ($link->event_date)
                                <div class="mt-0.5 flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($link->event_date)->format('d M Y') }}
                                </div>
                            @endif
                        </div>

                        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </div>
                </a>
            @endforeach

        </div>

    </div>

    {{-- Footer --}}
    <footer class="mb-4 mt-6 text-center text-sm font-medium text-white/50">
        &copy; {{ date('Y') }} Luminara Photobooth
    </footer>

</body>

</html>
