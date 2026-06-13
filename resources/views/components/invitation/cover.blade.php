@props([
    'groom' => 'Romeo', 
    'bride' => 'Juliet', 
    'guest' => 'Tamu Spesial',
    'image' => ''
])

<div x-show="!isOpen" 
     x-transition:leave="transition ease-in-out duration-1500"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-full"
     class="invitation-cover fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden bg-white">
     
    <!-- Animated Background Image -->
    <div class="invitation-cover-bg absolute inset-0 bg-cover bg-center animate-zoom opacity-60 mix-blend-overlay" style="background-image: url('{{ $image }}');"></div>
    <div class="invitation-cover-gradient absolute inset-0 bg-gradient-to-t from-white to-white/80 via-transparent"></div>
    
    <!-- Content Frame -->
    <div class="invitation-cover-frame relative z-10 w-11/12 max-w-md md:max-w-xl h-[85vh] border border-gray-200 p-8 flex flex-col justify-between items-center text-center rounded-t-full rounded-b-lg">
        
        <div class="animate-fade-up w-full" style="animation-delay: 0.3s; opacity:0;">
            <p class="invitation-cover-subtitle tracking-[0.3em] uppercase text-xs font-semibold mb-2 text-gray-500">The Wedding Of</p>
            <div class="invitation-cover-line w-12 h-px mx-auto bg-gray-400 mb-8"></div>
        </div>

        <div class="animate-fade-up w-full" style="animation-delay: 0.6s; opacity:0;">
            <h1 class="invitation-cover-title text-6xl md:text-7xl font-serif text-gray-900 mb-4 leading-tight">
                {{ $groom }}
                <span class="block text-4xl italic my-2 text-gray-500">&amp;</span>
                {{ $bride }}
            </h1>
        </div>
        
        <div class="animate-fade-up w-full flex flex-col items-center" style="animation-delay: 0.9s; opacity:0;">
            <div class="invitation-cover-guest-box px-6 py-6 rounded-lg border border-gray-200 shadow-2xl backdrop-blur-xl mb-8 w-full bg-white/80">
                <p class="text-xs uppercase tracking-widest mb-2 text-gray-500">Kepada Yth.</p>
                <p class="invitation-cover-guest-name font-serif text-2xl text-gray-900">{{ $guest }}</p>
            </div>

            <button @click="openInvitation" class="invitation-cover-button group relative px-10 py-4 bg-transparent border border-gray-900 text-gray-900 uppercase tracking-[0.2em] text-xs font-bold transition-all duration-500 overflow-hidden hover:text-white">
                <span class="relative z-10">Buka Undangan</span>
                <div class="absolute inset-0 h-full w-0 transition-all duration-500 ease-out group-hover:w-full z-0 bg-gray-900"></div>
            </button>
        </div>
    </div>
</div>
