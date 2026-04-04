<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'Editor') - Luminara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google Fonts for template components -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- Component Schemas -->
    <script src="{{ asset('js/component-schemas.js') }}"></script>
    @stack('styles')
</head>
<body class="bg-gray-100 overflow-hidden">
    <div class="h-screen flex flex-col">
        <!-- Top Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ request()->routeIs('admin.templates.*') ? route('admin.templates.index') : route('admin.invitations.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span class="text-sm font-medium">Kembali</span>
                </a>
                <div class="h-6 w-px bg-gray-300"></div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">@yield('title', 'Editor')</h1>
                    @if(isset($template))
                        <p class="text-sm text-gray-500">{{ $template->name }}</p>
                    @elseif(isset($invitation))
                        <p class="text-sm text-gray-500">{{ $invitation->title }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Viewport Switcher -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <button onclick="setViewport('desktop')" class="viewport-btn px-3 py-1.5 rounded text-sm font-medium transition" data-viewport="desktop">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </button>
                    <button onclick="setViewport('tablet')" class="viewport-btn px-3 py-1.5 rounded text-sm font-medium transition" data-viewport="tablet">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </button>
                    <button onclick="setViewport('mobile')" class="viewport-btn px-3 py-1.5 rounded text-sm font-medium transition" data-viewport="mobile">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    @stack('header-actions')

                    <button onclick="openPreview()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview
                    </button>
                    <button onclick="saveAll()" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-black rounded-lg hover:bg-yellow-600 transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span id="save-text">Simpan</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Editor Content -->
        <main class="flex-1 overflow-hidden">
            @yield('content')
        </main>
    </div>

    <script>
        // Viewport switcher
        function setViewport(size) {
            const canvas = document.getElementById('editor-canvas');
            const widths = {
                desktop: '100%',
                tablet: '768px',
                mobile: '375px'
            };

            canvas.style.maxWidth = widths[size];
            canvas.style.margin = '0 auto';

            // Update button states
            document.querySelectorAll('.viewport-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
                btn.classList.add('text-gray-600');
            });
            document.querySelector(`[data-viewport="${size}"]`).classList.add('bg-white', 'shadow-sm', 'text-gray-900');
            document.querySelector(`[data-viewport="${size}"]`).classList.remove('text-gray-600');

            // Store current viewport
            window.currentViewport = size;
        }

        // Initialize desktop viewport
        document.addEventListener('DOMContentLoaded', function() {
            setViewport('desktop');
        });

        // Preview function
        function openPreview() {
            const isTemplate = '{{ request()->routeIs('admin.templates.*') ? 'true' : 'false' }}' === 'true';
            const slug = '{{ $invitation->slug ?? '' }}';
            const templateId = '{{ $template->id ?? '' }}';

            if (isTemplate) {
                // For templates, open preview in new tab with template ID
                if (templateId) {
                    window.open(`/admin/templates/${templateId}/preview`, '_blank');
                } else {
                    Swal.fire('Info', 'Simpan template dulu untuk preview', 'info');
                }
            } else {
                // For invitations, use slug-based preview
                if (slug) {
                    window.open(`/invitation/${slug}?preview=true`, '_blank');
                } else {
                    Swal.fire('Info', 'Simpan undangan dulu untuk preview', 'info');
                }
            }
        }

        // Save function - dispatches event for Alpine component to handle
        function saveAll() {
            // Show saving indicator
            const saveText = document.getElementById('save-text');
            if (saveText) {
                saveText.textContent = 'Menyimpan...';
            }

            // Dispatch event
            window.dispatchEvent(new CustomEvent('editor-save'));

            // Reset text after delay (Alpine component will update this)
            setTimeout(() => {
                if (saveText) {
                    saveText.textContent = 'Simpan';
                }
            }, 2000);
        }
    </script>

    @stack('scripts')
</body>
</html>
