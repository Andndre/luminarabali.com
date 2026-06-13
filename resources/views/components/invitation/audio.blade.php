@props(['src'])

@if($src)
    <audio x-ref="bgAudio" src="{{ $src }}" loop></audio>
    
    <!-- Audio Control Button -->
    <button x-show="isOpen" @click="toggleAudio" 
            class="invitation-audio-fab fixed bottom-8 right-8 z-50 p-4 rounded-full shadow-2xl transition-all duration-500 bg-white/90 backdrop-blur-md text-gray-800 hover:bg-gray-100 border border-gray-200"
            x-transition:enter="transition ease-out duration-1000 delay-1000"
            x-transition:enter-start="opacity-0 translate-y-8 scale-50"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100">
        <svg x-show="isPlaying" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M8 5v14l-4-4H3V9h1l4-4z"></path></svg>
        <svg x-show="!isPlaying" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.807L5.586 15z"></path></svg>
    </button>
@endif
