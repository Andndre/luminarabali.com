@props(['props' => [], 'section' => null, 'page' => null])

@php
$src = $props['src'] ?? '';
// Path relatif storage tanpa prefix diresolusi browser relatif ke URL halaman → 404 di Studio.
$audioSrc = $src && \Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
    ? $src
    : ($src ? '/storage/'.ltrim($src, '/') : '');
$autoplay = $props['autoplay'] ?? true;
$loop = $props['loop'] ?? true;
$showControls = $props['show_controls'] ?? true;
$variant = $props['variant'] ?? 'disc';
$audioId = 'bg-music-'.($section->id ?? 'default');
@endphp

{{-- Tanpa atribut autoplay: browser memblokir autoplay bersuara tanpa interaksi.
     Ketukan tamu di gate ("Buka Undangan") itulah interaksinya — layout mengirim
     event invitation-opened, dan di situ play() diizinkan. --}}
<audio id="{{ $audioId }}" src="{{ $audioSrc }}" preload="auto" @if($loop) loop @endif
    @if(!$showControls) style="display: none;" @endif></audio>

@if($showControls)
    {{-- Teleport ke body: container-type di .invite-content menjebak position:fixed
         di dalamnya (containment), FAB harus lepas dari situ. --}}
    <template x-teleport="body">
    <div class="music-fab music-fab--{{ $variant }}"
        @if($autoplay) @invitation-opened.window="audio?.play().catch(() => {})" @endif
        x-data="{
            playing: false,
            audio: null,
            init() {
                this.audio = document.getElementById(@js($audioId));
                if (!this.audio) return;
                this.audio.volume = .5;
                // playing mengikuti keadaan nyata elemen, bukan prop autoplay: play()
                // bisa ditolak browser, dan menebak bikin ikon terbalik.
                this.audio.addEventListener('play', () => this.playing = true);
                this.audio.addEventListener('pause', () => this.playing = false);
                this.playing = !this.audio.paused;
            },
            toggle() {
                if (!this.audio) return;
                this.playing ? this.audio.pause() : this.audio.play().catch(() => {});
            },
        }">
        <button type="button" class="music-btn" :class="playing && 'is-playing'" @click="toggle()"
                :aria-label="playing ? 'Jeda musik' : 'Putar musik'">
            @if($variant === 'pill')
                <span class="music-eq" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
            @endif

            <span class="music-icon" aria-hidden="true">
                <svg x-show="!playing" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                <svg x-show="playing" x-cloak viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
            </span>
        </button>
    </div>
    </template>
@endif
