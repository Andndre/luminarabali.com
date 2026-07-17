@props(['props' => [], 'section' => null, 'page' => null])

@php
$src = $props['src'] ?? '';
$autoplay = $props['autoplay'] ?? true;
$loop = $props['loop'] ?? true;
$showControls = $props['show_controls'] ?? true;
@endphp

<!-- Hidden Audio Element -->
<audio
    id="bg-music-{{ $section->id ?? 'default' }}"
    src="{{ $src }}"
    @if($autoplay) autoplay @endif
    @if($loop) loop @endif
    @if(!$showControls) style="display: none;" @endif
></audio>

@if($showControls)
    <!-- Floating Music Control Button — teleport ke body: container-type di .invite-content
         menjebak position:fixed di dalamnya (containment), FAB harus lepas dari situ. -->
    <template x-teleport="body">
    <div class="fixed bottom-6 right-6 z-40" x-data="{ playing: {{ $autoplay ? 'true' : 'false' }} }">
        <button
            @click="playing = !playing; document.getElementById('bg-music-{{ $section->id ?? 'default' }}').{{ $autoplay ? 'pause' : 'play' }}()"
            class="w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition transform hover:scale-110"
            style="background: {{ $props['button_color'] ?? '#d4af37' }};"
        >
            <svg x-show="!playing" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
            <svg x-show="playing" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
            </svg>
        </button>

        <!-- Music Info Tooltip -->
        <div x-show="playing" x-transition class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg whitespace-nowrap">
            <div class="flex items-center gap-2">
                <div class="flex gap-1">
                    <span class="w-1 h-3 bg-green-400 animate-pulse"></span>
                    <span class="w-1 h-4 bg-green-400 animate-pulse" style="animation-delay: 0.1s;"></span>
                    <span class="w-1 h-2 bg-green-400 animate-pulse" style="animation-delay: 0.2s;"></span>
                </div>
                <span>Now Playing</span>
            </div>
        </div>
    </div>
    </template>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const audio = document.getElementById('bg-music-{{ $section->id ?? 'default' }}');

            // Set initial volume
            audio.volume = 0.5;

            // Handle autoplay restrictions
            {{ $autoplay ? '' : "audio.pause();" }}

            // Show play/pause indicator
            audio.addEventListener('play', function() {
                if (window.Alpine) {
                    const component = document.querySelector('[x-data*="playing"]');
                    if (component && component.__x) {
                        component.__x.$data.playing = true;
                    }
                }
            });

            audio.addEventListener('pause', function() {
                if (window.Alpine) {
                    const component = document.querySelector('[x-data*="playing"]');
                    if (component && component.__x) {
                        component.__x.$data.playing = false;
                    }
                }
            });
        });
    </script>
    @endpush
@endif
