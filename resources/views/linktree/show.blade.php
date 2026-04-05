<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <x-seo title="Luminara {{ ucfirst(str_replace('Luminara', '', $division)) }}"
        description="Temukan semua link resmi Luminara {{ str_replace('Luminara', '', $division) }} - Photobooth dan Visual Documentation untuk acara spesial Anda."
        keywords="luminara {{ strtolower(str_replace('Luminara', '', $division)) }}, linktree luminara, {{ strtolower(str_replace('Luminara', '', $division)) }} bali"
        og_image="/images/logo.png" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-maroon {
            background-color: #450000;
        }

        .bg-maroon-dark {
            background-color: #491919;
        }
    </style>
</head>

<body class="bg-maroon-dark flex min-h-dvh flex-col items-center px-4 py-10 font-sans">

    {{-- Mobile outer frame with rounded edges --}}
    <div class="rounded-4xl bg-maroon w-full max-w-md px-5 py-6 shadow-2xl">

        {{-- Header share button (pojok kanan atas container) --}}
        <div class="mb-3 flex items-center justify-between">
            <div></div>
            <button
                id="btn-share-header"
                class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white transition hover:scale-110 hover:bg-white/30"
                title="Bagikan Linktree"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </button>
        </div>

        {{-- Header --}}
        <div class="mb-5 text-center">
            <div
                class="mx-auto mb-3 flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-white shadow-lg">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-full w-full object-contain">
            </div>
            <h1 class="text-xl font-bold tracking-wide text-white">
                Luminara {{ $division === 'LuminaraPhotobooth' ? 'Photobooth' : 'Visual' }}
            </h1>
            <p class="mt-1 text-sm text-white">Photobooth Bali Wedding | Event | Birhtday | etc. FREE TRANSPORT
                SELURUH BALI</p>
        </div>

        {{-- Pinned links strip (icon horizontal) --}}
        @php
            $pinnedLinks = $adminLinks->filter(fn($link) => $link->is_pinned);
        @endphp

        @if ($pinnedLinks->isNotEmpty())
            <div class="mb-4 flex items-center justify-center gap-3 overflow-x-auto px-1 py-2">
                @foreach ($pinnedLinks as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" title="{{ $link->title }}"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:scale-110 hover:bg-white/30">
                        @if ($link->icon)
                            <img src="{{ asset('images/icons/' . $link->icon . '.svg') }}" alt="{{ $link->title }}"
                                class="h-6 w-6 object-contain brightness-0 invert">
                        @else
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Links --}}
        <div class="space-y-3">

            {{-- Today's booking links --}}
            @foreach ($todayBookingLinks as $link)
                <div class="block overflow-hidden rounded-2xl bg-white shadow-sm transition-transform duration-200 hover:scale-[1.02] hover:shadow-md">
                    @if ($link->thumbnail)
                        <div class="relative h-36 w-full overflow-hidden bg-gray-100">
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="h-full w-full block">
                                <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                    class="h-full w-full object-cover">
                            </a>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 p-3">
                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50">
                            <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </a>

                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="min-w-0 flex-1">
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
                        </a>

                        <span class="shrink-0 rounded-full bg-pink-500 px-2.5 py-0.5 text-xs font-bold text-white">Hari Ini</span>

                        <button
                            class="btn-share flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                            data-url="{{ $link->url }}"
                            data-title="{{ $link->title }}"
                            title="Bagikan"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="1.5"/>
                                <circle cx="12" cy="12" r="1.5"/>
                                <circle cx="12" cy="19" r="1.5"/>
                            </svg>
                        </button>
                    </div>
                </div>
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
                <div class="block overflow-hidden rounded-2xl bg-white shadow-sm transition-transform duration-200 hover:scale-[1.02] hover:shadow-md">
                    @if ($link->thumbnail)
                        <div class="h-36 w-full overflow-hidden bg-gray-100">
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="h-full w-full block">
                                <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                    class="h-full w-full object-cover">
                            </a>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 p-3">
                        @if ($link->icon)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50">
                                <img src="{{ asset('images/icons/' . $link->icon . '.svg') }}"
                                    alt="{{ $link->title }}" class="h-6 w-6 object-contain">
                            </a>
                        @else
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </a>
                        @endif

                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="flex-1 truncate text-sm font-semibold text-gray-900">{{ $link->title }}</a>

                        <button
                            class="btn-share flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                            data-url="{{ $link->url }}"
                            data-title="{{ $link->title }}"
                            title="Bagikan"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="1.5"/>
                                <circle cx="12" cy="12" r="1.5"/>
                                <circle cx="12" cy="19" r="1.5"/>
                            </svg>
                        </button>
                    </div>
                </div>
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
                <div class="block overflow-hidden rounded-2xl bg-white shadow-sm transition-transform duration-200 hover:scale-[1.02] hover:shadow-md">
                    <div class="flex items-center gap-3 p-3">
                        @if ($link->thumbnail)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="h-10 w-10 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                    class="h-full w-full object-cover">
                            </a>
                        @else
                            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50">
                                <svg class="h-5 w-5 text-pink-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                        @endif

                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="min-w-0 flex-1">
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
                        </a>

                        <button
                            class="btn-share flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                            data-url="{{ $link->url }}"
                            data-title="{{ $link->title }}"
                            title="Bagikan"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="5" r="1.5"/>
                                <circle cx="12" cy="12" r="1.5"/>
                                <circle cx="12" cy="19" r="1.5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach

        </div>

    </div>

    {{-- Footer --}}
    <footer class="mb-4 mt-6 text-center text-sm font-medium text-white/50">
        &copy; {{ date('Y') }} Luminara Photobooth
    </footer>

    {{-- SHARE MODAL --}}
    <div id="share-modal" class="fixed inset-0 z-50 hidden items-end justify-center p-0 sm:items-center sm:p-4">
        {{-- Backdrop --}}
        <div id="share-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        {{-- Modal panel --}}
        <div class="relative z-10 w-full max-w-md rounded-t-3xl bg-white sm:rounded-2xl shadow-2xl">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <span class="font-bold text-gray-900">Bagikan</span>
                <button id="btn-close-share" class="rounded-full p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Share buttons grid --}}
            <div class="grid grid-cols-5 gap-4 p-5">
                <a id="share-whatsapp" href="#" target="_blank" rel="noopener noreferrer"
                   class="flex flex-col items-center gap-1.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#25D366]">
                        <img src="{{ asset('images/icons/whatsapp.svg') }}" alt="WhatsApp" class="h-6 w-6 object-contain brightness-0 invert">
                    </div>
                    <span class="text-xs text-gray-600">WhatsApp</span>
                </a>

                <a id="share-facebook" href="#" target="_blank" rel="noopener noreferrer"
                   class="flex flex-col items-center gap-1.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#1877F2]">
                        <img src="{{ asset('images/icons/facebook.svg') }}" alt="Facebook" class="h-6 w-6 object-contain brightness-0 invert">
                    </div>
                    <span class="text-xs text-gray-600">Facebook</span>
                </a>

                <a id="share-twitter" href="#" target="_blank" rel="noopener noreferrer"
                   class="flex flex-col items-center gap-1.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-black">
                        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 5.99zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-600">X</span>
                </a>

                <a id="share-telegram" href="#" target="_blank" rel="noopener noreferrer"
                   class="flex flex-col items-center gap-1.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#26A5E4]">
                        <img src="{{ asset('images/icons/telegram.svg') }}" alt="Telegram" class="h-6 w-6 object-contain brightness-0 invert">
                    </div>
                    <span class="text-xs text-gray-600">Telegram</span>
                </a>

                <a id="share-email" href="#"
                   class="flex flex-col items-center gap-1.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-500">
                        <img src="{{ asset('images/icons/email.svg') }}" alt="Email" class="h-6 w-6 object-contain brightness-0 invert">
                    </div>
                    <span class="text-xs text-gray-600">Email</span>
                </a>
            </div>

            {{-- Native share + Copy link --}}
            <div class="border-t border-gray-100 px-5 py-4 space-y-3">
                <button id="btn-native-share"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-maroon px-4 py-3 font-semibold text-white transition hover:opacity-90">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Bagikan via Perangkat
                </button>

                <button id="btn-copy-link"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-3 font-semibold text-gray-700 transition hover:bg-gray-50">
                    <img src="{{ asset('images/icons/link.svg') }}" alt="Copy" class="h-5 w-5">
                    Salin Link
                </button>
            </div>
        </div>
    </div>

    {{-- Toast notification --}}
    <div id="toast-copy-link"
        class="pointer-events-none fixed bottom-6 left-1/2 z-60 flex -translate-x-1/2 transform items-center gap-2 rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white opacity-0 shadow-lg transition-opacity duration-300">
        <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Link berhasil disalin!
    </div>

    <script>
        (function () {
            var modal = document.getElementById('share-modal');
            var backdrop = document.getElementById('share-backdrop');
            var btnClose = document.getElementById('btn-close-share');
            var btnCopy = document.getElementById('btn-copy-link');
            var btnNativeShare = document.getElementById('btn-native-share');
            var toast = document.getElementById('toast-copy-link');
            var btnHeaderShare = document.getElementById('btn-share-header');

            var currentUrl = '';
            var currentTitle = '';

            function openShareModal(url, title) {
                currentUrl = url;
                currentTitle = title;

                document.getElementById('share-whatsapp').href =
                    'https://wa.me/?text=' + encodeURIComponent(title + ' ' + url);
                document.getElementById('share-facebook').href =
                    'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
                document.getElementById('share-twitter').href =
                    'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
                document.getElementById('share-telegram').href =
                    'https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
                document.getElementById('share-email').href =
                    'mailto:?subject=' + encodeURIComponent(title) + '&body=' + encodeURIComponent(url);

                // Show/hide native share button
                btnNativeShare.style.display = navigator.share ? 'flex' : 'none';

                modal.style.display = 'flex';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeShareModal() {
                modal.style.display = 'none';
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function copyLink() {
                navigator.clipboard.writeText(currentUrl);
                toast.classList.remove('opacity-0');
                setTimeout(function () {
                    toast.classList.add('opacity-0');
                }, 2000);
                closeShareModal();
            }

            function nativeShare() {
                if (navigator.share) {
                    navigator.share({ title: currentTitle, url: currentUrl }).catch(function () {});
                }
            }

            // Header share button
            btnHeaderShare.addEventListener('click', function (e) {
                e.preventDefault();
                var division = {{ Js::from($division) }};
                var title = division === 'LuminaraPhotobooth' ? 'Luminara Photobooth' : 'Luminara Visual';
                openShareModal(window.location.href, title);
            });

            // Per-link share buttons
            document.querySelectorAll('.btn-share').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openShareModal(this.dataset.url, this.dataset.title);
                });
            });

            backdrop.addEventListener('click', closeShareModal);
            btnClose.addEventListener('click', closeShareModal);
            btnCopy.addEventListener('click', copyLink);
            btnNativeShare.addEventListener('click', nativeShare);

            // Prevent modal clicks from closing
            modal.querySelector('.relative.z-10').addEventListener('click', function (e) {
                e.stopPropagation();
            });
        })();
    </script>

</body>

</html>
