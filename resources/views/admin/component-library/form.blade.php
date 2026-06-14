@extends('layouts.editor')

@section('content')
@php
$defaultCode = '';
$defaultVariables = '[]';
if (!isset($component) && !old('code') && isset($category)) {
    switch($category) {
        case 'cover':
            $defaultCode = '<div id="invitation-cover" 
     x-show="!isOpen" 
     x-transition.opacity.duration.1000ms
     class="fixed inset-0 z-100 flex items-center justify-center bg-gray-900 transition-transform duration-1000 ease-in-out">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop" 
             alt="Cover Image" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 text-center px-6 max-w-lg mx-auto transform transition-all duration-1000 translate-y-0">
        <p class="invitation-accent text-sm tracking-[0.3em] uppercase mb-6 text-gray-300">The Wedding Of</p>
        
        <h1 class="invitation-title text-6xl md:text-8xl font-serif mb-8 text-white">
            {{ $groom_name }}<br>
            <span class="invitation-accent text-4xl italic">&amp;</span><br>
            {{ $bride_name }}
        </h1>
        
        <div class="mt-12 mb-12">
            <p class="text-sm text-gray-400 uppercase tracking-widest mb-2">Kepada Yth.</p>
            <p class="text-xl font-serif text-white">{{ $guest_name }}</p>
        </div>
        
        <button @click="openInvitation()" 
                class="invitation-button group relative px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white tracking-[0.2em] text-xs uppercase transition-all duration-500 overflow-hidden">
            <span class="relative z-10 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                Buka Undangan
            </span>
        </button>
    </div>
</div>';
            $defaultVariables = json_encode([
                ['key' => 'bride_name', 'label' => 'Bride Name', 'type' => 'text', 'default' => 'Juliet', 'description' => 'Nama mempelai wanita'],
                ['key' => 'groom_name', 'label' => 'Groom Name', 'type' => 'text', 'default' => 'Romeo', 'description' => 'Nama mempelai pria'],
                ['key' => 'guest_name', 'label' => 'Guest Name', 'type' => 'search_param', 'default' => 'to', 'description' => 'Parameter URL pencarian nama tamu (misal: ?to=Nama)']
            ]);
            break;
        case 'hero':
            $defaultCode = '<!-- Hero Section -->
<section class="invitation-hero relative min-h-[100dvh] flex flex-col items-center justify-center text-center p-8 overflow-hidden bg-white">
    <div class="absolute inset-0 bg-cover bg-center opacity-30 fixed" style="background-image: url(\'https://images.unsplash.com/photo-1520854221256-17451cc331bf?q=80&w=2000&auto=format&fit=crop\')"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white"></div>
    
    <div class="relative z-10 max-w-2xl mx-auto pt-32">
        <p class="invitation-accent uppercase tracking-[0.3em] text-sm mb-6 reveal-on-scroll">We Are Getting Married</p>
        <h2 class="invitation-title text-6xl md:text-8xl font-serif text-gray-900 mb-8 reveal-on-scroll" style="transition-delay: 200ms;">
            {{ $groom_name ?? \'Romeo\' }} <br> <span class="invitation-accent text-4xl italic">&amp;</span> <br> {{ $bride_name ?? \'Juliet\' }}
        </h2>
        <div class="invitation-line w-px h-32 mx-auto mt-12 animate-float reveal-on-scroll bg-gray-400" style="transition-delay: 400ms;"></div>
    </div>
</section>';
            $defaultVariables = json_encode([
                ['key' => 'bride_name', 'label' => 'Bride Name', 'type' => 'text', 'default' => 'Juliet', 'description' => 'Nama mempelai wanita'],
                ['key' => 'groom_name', 'label' => 'Groom Name', 'type' => 'text', 'default' => 'Romeo', 'description' => 'Nama mempelai pria']
            ]);
            break;
        case 'text':
            $defaultCode = '<div class="max-w-2xl mx-auto p-8 text-center">
    <h3 class="text-2xl font-bold mb-3 text-gray-800">{{ $title }}</h3>
    <p class="text-gray-600 leading-relaxed">{{ $content }}</p>
</div>';
            $defaultVariables = json_encode([
                ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Judul Text', 'description' => ''],
                ['key' => 'content', 'label' => 'Content', 'type' => 'textarea', 'default' => 'Lorem ipsum dolor sit amet.', 'description' => '']
            ]);
            break;
        case 'button':
            $defaultCode = '<div class="text-center p-4">
    <button class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-full transition-colors shadow-md">
        {{ $button_text }}
    </button>
</div>';
            $defaultVariables = json_encode([
                ['key' => 'button_text', 'label' => 'Button Text', 'type' => 'text', 'default' => 'Click Me', 'description' => '']
            ]);
            break;
        case 'rsvp':
            $defaultCode = '<div class="max-w-xl mx-auto p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
    <h3 class="text-2xl font-bold text-center mb-6 text-gray-800">RSVP</h3>
    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ketik nama Anda...">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kehadiran</label>
            <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="1">Ya, Saya akan hadir</option>
                <option value="0">Maaf, tidak bisa hadir</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pesan & Doa (Opsional)</label>
            <textarea class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Tuliskan ucapan..."></textarea>
        </div>
        <button type="button" class="w-full bg-gray-900 text-white font-medium py-3 rounded-xl hover:bg-gray-800 transition">Kirim RSVP</button>
    </form>
</div>';
            break;
        case 'event':
            $defaultCode = '<div class="max-w-3xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
    <div class="bg-gray-50 p-6 rounded-2xl text-center">
        <h4 class="font-bold text-xl mb-2">Akad Nikah</h4>
        <p class="text-gray-600 mb-4">Minggu, 12 Desember 2026<br>08.00 - Selesai</p>
        <p class="text-sm text-gray-500">Masjid Agung Bali</p>
    </div>
    <div class="bg-gray-50 p-6 rounded-2xl text-center">
        <h4 class="font-bold text-xl mb-2">Resepsi</h4>
        <p class="text-gray-600 mb-4">Minggu, 12 Desember 2026<br>11.00 - 14.00</p>
        <p class="text-sm text-gray-500">Hotel Aston Bali</p>
    </div>
</div>';
            break;
        case 'countdown':
            $defaultCode = '<div class="flex justify-center gap-4 p-8">
    <div class="text-center"><div class="w-16 h-16 bg-gray-900 text-white rounded-xl flex items-center justify-center text-2xl font-bold mb-2">12</div><span class="text-xs text-gray-500 uppercase">Hari</span></div>
    <div class="text-center"><div class="w-16 h-16 bg-gray-900 text-white rounded-xl flex items-center justify-center text-2xl font-bold mb-2">08</div><span class="text-xs text-gray-500 uppercase">Jam</span></div>
    <div class="text-center"><div class="w-16 h-16 bg-gray-900 text-white rounded-xl flex items-center justify-center text-2xl font-bold mb-2">45</div><span class="text-xs text-gray-500 uppercase">Menit</span></div>
</div>';
            break;
        case 'gallery':
            $defaultCode = '<div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4 max-w-4xl mx-auto">
    <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden"><img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover"></div>
    <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden"><img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover"></div>
    <div class="aspect-square bg-gray-200 rounded-xl overflow-hidden"><img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover"></div>
</div>';
            break;
        case 'map':
            $defaultCode = '<div class="w-full max-w-3xl mx-auto p-4">
    <div class="w-full aspect-video bg-gray-100 rounded-2xl overflow-hidden border border-gray-200 flex items-center justify-center text-gray-400">
        <!-- Embed iframe gmaps disini -->
        [Google Maps Iframe]
    </div>
    <div class="mt-4 text-center">
        <a href="#" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-900 text-white rounded-full text-sm font-medium hover:bg-gray-800">Buka di Google Maps</a>
    </div>
</div>';
            break;
        case 'divider':
            $defaultCode = '<div class="w-full py-8 flex justify-center">
    <div class="w-24 h-px bg-gray-300 relative">
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 rounded-full border border-gray-300 bg-white"></div>
    </div>
</div>';
            break;
        case 'footer':
            $defaultCode = '<footer class="w-full py-8 bg-gray-900 text-center text-gray-400">
    <p class="text-sm">Made with love by Luminara</p>
</footer>';
            break;
        case 'video':
            $defaultCode = '<div class="w-full max-w-3xl mx-auto p-4">
    <div class="aspect-video bg-gray-900 rounded-2xl overflow-hidden flex items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm cursor-pointer hover:bg-white/30 transition">
            <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6z"></path></svg>
        </div>
    </div>
</div>';
            break;
        case 'section':
            $defaultCode = '<section class="w-full py-16 bg-white">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">Judul Section</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Kolom 1 -->
            <div>
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80&w=600" class="w-full rounded-2xl shadow-lg">
            </div>
            <!-- Kolom 2 -->
            <div class="flex flex-col justify-center">
                <h3 class="text-2xl font-semibold mb-4">Sub Judul</h3>
                <p class="text-gray-600 mb-6">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <button class="px-6 py-3 bg-black text-white rounded-xl self-start">Selengkapnya</button>
            </div>
        </div>
    </div>
</section>';
            break;
        default:
            $defaultCode = '<div class="p-4 text-center">
    <p class="text-gray-500">Tulis komponen Anda disini</p>
</div>';
            break;
    }
}
@endphp
<div x-data="componentEditor()" class="flex-1 flex flex-col w-full h-full bg-white relative">
    
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.component-library.index') }}" class="text-gray-500 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ isset($component) ? 'Edit Component: ' . $component->name : 'Create Component' }}</h1>
                <p class="text-xs text-gray-500 mt-1">Define reusable code snippet and its variables.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <span x-show="isSaving" class="text-sm text-gray-500" x-cloak>Menyimpan...</span>
            <button @click="isDrawerOpen = true" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
            </button>
            <button @click="save()" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Save
            </button>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="flex-1 flex overflow-hidden relative w-full">
        
        <!-- Left Panel: Monaco Editor -->
        <div class="w-1/2 flex flex-col border-r border-gray-800 bg-[#1e1e1e] h-full overflow-hidden shrink-0">
            <div class="bg-gray-900 text-gray-400 text-[11px] uppercase tracking-wider font-semibold px-4 py-2 border-b border-gray-800 flex justify-between font-mono-code shrink-0">
                <span>Code Editor (Blade/HTML)</span>
                <span>Use @{{ $variable_name }} for placeholders</span>
            </div>
            <div id="monaco-editor" x-ignore class="flex-1 w-full relative"></div>
        </div>
        
        <!-- Right Panel: Realtime Preview -->
        <div class="flex-1 flex flex-col bg-white h-full overflow-hidden relative">
            <div class="bg-gray-100 text-gray-600 text-[11px] uppercase tracking-wider font-semibold px-4 py-2 border-b border-gray-200 flex justify-between font-sans shrink-0">
                <span>Realtime Preview</span>
                <span class="text-xs text-gray-400 font-sans">Updates dynamically as you type</span>
            </div>
            <div class="flex-1 w-full relative bg-slate-50">
                <iframe id="preview-iframe" x-ignore class="w-full h-full border-0"></iframe>
            </div>
        </div>
        
        <!-- Drawer Overlay -->
        <div x-show="isDrawerOpen" class="fixed inset-0 bg-gray-900/20 z-40 backdrop-blur-sm transition-opacity" @click="isDrawerOpen = false" x-transition.opacity x-cloak></div>

        <!-- Drawer Panel -->
        <div class="fixed inset-y-0 right-0 w-[420px] bg-gray-50 border-l border-gray-200 shadow-2xl z-50 transform transition-transform duration-300 ease-in-out flex flex-col" :class="isDrawerOpen ? 'translate-x-0' : 'translate-x-full'" x-cloak>
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex items-center justify-between shrink-0 shadow-sm z-10">
                <h2 class="font-bold text-gray-900 font-sans">Settings & Variables</h2>
                <button @click="isDrawerOpen = false" class="p-1 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- General Settings -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4 border-b border-gray-100 pb-2.5 font-sans">General Settings</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5 font-sans">Name</label>
                            <input type="text" x-model="form.name" @input="generateSlug" class="w-full bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-2 rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-sans">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5 font-sans">Slug</label>
                            <input type="text" x-model="form.slug" class="w-full bg-gray-50 border border-gray-200 text-gray-500 px-3 py-2 rounded-lg text-sm cursor-not-allowed font-mono-code" readonly>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5 font-sans">Category</label>
                            <div class="relative">
                                <select x-model="form.category" class="w-full appearance-none bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-2 pr-10 rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer font-sans">
                                    <option value="cover">Cover</option>
                                    <option value="hero">Hero</option>
                                    <option value="text">Text &amp; Typography</option>
                                    <option value="event">Event Details</option>
                                    <option value="gallery">Gallery</option>
                                    <option value="countdown">Countdown</option>
                                    <option value="rsvp">RSVP</option>
                                    <option value="map">Map</option>
                                    <option value="video">Video</option>
                                    <option value="button">Button / CTA</option>
                                    <option value="divider">Divider &amp; Spacer</option>
                                    <option value="footer">Footer</option>
                                    <option value="section">Complete Section (Composition)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5 font-sans">Type</label>
                            <div class="relative">
                                <select x-model="form.type" class="w-full appearance-none bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-2 pr-10 rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer font-sans">
                                    <option value="component">Component (Single Block)</option>
                                    <option value="section">Section (Multiple Blocks)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5 font-sans">Description</label>
                        <textarea x-model="form.description" rows="2" class="w-full bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-2 rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-sans"></textarea>
                    </div>

                    <div class="flex items-center gap-6 pt-3 border-t border-gray-100">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" x-model="form.is_public" class="rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer h-4 w-4">
                            <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors font-sans">Public (Visible to other creators)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" x-model="form.is_active" class="rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer h-4 w-4">
                            <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors font-sans">Active</span>
                        </label>
                    </div>
                </div>

                <!-- Variable Builder -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2.5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 font-sans">Variables</h3>
                        <button @click="addVariable()" class="px-3 py-1.5 text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg font-semibold flex items-center gap-1.5 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Variable
                        </button>
                    </div>
                    
                    <div x-show="form.variables.length === 0" class="text-center py-8 text-gray-400 text-sm italic border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                        No variables defined yet. Add variables to make your component dynamic.
                    </div>
                    
                    <div class="space-y-4">
                        <template x-for="(variable, index) in form.variables" :key="index">
                            <div class="border-l-4 border-l-indigo-600 border border-gray-200 rounded-xl p-5 bg-white shadow-sm relative group transition-all hover:shadow-md">
                                <button @click="removeVariable(index)" class="absolute top-3 right-3 text-gray-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-all p-1 hover:bg-red-50 rounded-lg cursor-pointer" title="Delete Variable">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                
                                <div class="grid grid-cols-12 gap-3">
                                    <div class="col-span-4">
                                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 font-sans">Key ($name)</label>
                                        <input type="text" x-model="variable.key" class="w-full bg-white border border-gray-200 hover:border-gray-300 text-indigo-700 px-3 py-1.5 rounded-lg text-sm font-mono-code focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                    </div>
                                    <div class="col-span-4">
                                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 font-sans">Label (UI)</label>
                                        <input type="text" x-model="variable.label" class="w-full bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-1.5 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                    </div>
                                    <div class="col-span-4">
                                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 font-sans">Type</label>
                                        <div class="relative">
                                            <select x-model="variable.type" class="w-full appearance-none bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-1.5 pr-8 rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                                                <option value="text">Text</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="image">Image</option>
                                                <option value="image_list">Image List</option>
                                                <option value="boolean">Boolean</option>
                                                <option value="select">Select</option>
                                                <option value="color">Color</option>
                                                <option value="range">Range Slider</option>
                                                <option value="search_param">Search Param (URL)</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5">
                                                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-6">
                                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 font-sans">Default Value</label>
                                        <input type="text" x-model="variable.default" class="w-full bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-1.5 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                    </div>
                                    <div class="col-span-6">
                                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1 font-sans">Help Text</label>
                                        <input type="text" x-model="variable.description" placeholder="Optional explanation" class="w-full bg-white border border-gray-200 hover:border-gray-300 text-gray-900 px-3 py-1.5 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Live Preview / Thumbnail Capture Area -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4 border-b border-gray-100 pb-2.5 font-sans">Thumbnail</h3>
                    
                    <div class="flex gap-4 items-start">
                        <div class="w-1/2 aspect-video bg-white rounded-lg overflow-hidden border border-dashed border-gray-200 flex items-center justify-center relative">
                            <template x-if="thumbnailUrl">
                                <img :src="thumbnailUrl" class="w-full h-full object-contain p-4">
                            </template>
                            <template x-if="!thumbnailUrl">
                                <span class="text-sm text-gray-400 font-sans">No thumbnail</span>
                            </template>
                        </div>
                        
                        <div class="flex-1 space-y-3">
                            <p class="text-xs text-gray-500 font-sans">Thumbnail generated automatically via html2canvas when you save. You can also force generate it below.</p>
                            
                            <button @click="generateThumbnail()" class="px-4 py-2 border border-gray-200 bg-white text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all w-full cursor-pointer font-sans">
                                Capture Thumbnail Now
                            </button>
                        </div>
                    </div>
                    <textarea id="raw_code" class="hidden">{!! old('code', $component->code ?? $defaultCode) !!}</textarea>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- html-to-image for pixel-perfect thumbnail generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>
<!-- Monaco Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>

<script>
let monacoEditorInstance = null;

document.addEventListener('alpine:init', () => {
    Alpine.data('componentEditor', () => ({
        isDrawerOpen: false,
        isSaving: false,
        previewInitialized: false,
        previewReady: false,
        thumbnailUrl: '{{ isset($component) && $component->thumbnail ? asset($component->thumbnail) : '' }}',
        thumbnailBlob: null,
        
        form: {
            id: {{ isset($component) ? $component->id : 'null' }},
            name: @json(isset($component) ? $component->name : ''),
            slug: @json(isset($component) ? $component->slug : ''),
            category: '{{ old('category', $component->category ?? ($category ?? 'cover')) }}',
            type: @json(isset($component) ? $component->type : 'component'),
            description: @json(isset($component) ? $component->description : ''),
            is_public: {{ isset($component) ? ($component->is_public ? 'true' : 'false') : 'true' }},
            is_active: {{ isset($component) ? ($component->is_active ? 'true' : 'false') : 'true' }},
            variables: {!! isset($component) ? json_encode($component->variables) : (isset($defaultVariables) ? $defaultVariables : '[]') !!},
            code: document.getElementById('raw_code').value
        },

        previewTimeout: null,

        init() {
            this.initPreviewIframe();
            this.initMonaco();
            this.$watch('form.variables', () => {
                this.updatePreviewDebounced();
            }, { deep: true });
        },

        generateSlug() {
            if(!this.form.id) { // Only auto-generate on create
                this.form.slug = this.form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            }
        },

        addVariable() {
            this.form.variables.push({
                key: 'var_' + (this.form.variables.length + 1),
                label: 'New Variable',
                type: 'text',
                default: '',
                description: ''
            });
            this.updatePreviewDebounced();
        },

        removeVariable(index) {
            this.form.variables.splice(index, 1);
            this.updatePreviewDebounced();
        },

        initMonaco() {
            if (monacoEditorInstance) return;

            const container = document.getElementById('monaco-editor');
            if (!container) return;

            const createEditor = () => {
                monacoEditorInstance = monaco.editor.create(container, {
                    value: this.form.code,
                    language: 'html',
                    theme: 'vs-dark',
                    automaticLayout: true,
                    minimap: { enabled: false },
                    wordWrap: 'on',
                    fontSize: 14,
                    padding: { top: 16 }
                });

                // Update preview on changes
                monacoEditorInstance.onDidChangeModelContent(() => {
                    this.updatePreviewDebounced();
                });

                // Ctrl+S to save
                monacoEditorInstance.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => {
                    this.save();
                });

                // Populate preview initially once editor is ready
                this.updatePreviewDebounced();
            };

            if (window.monaco) {
                createEditor();
            } else {
                require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
                require(['vs/editor/editor.main'], () => {
                    createEditor();
                });
            }
        },

        updatePreviewDebounced() {
            if (this.previewTimeout) {
                clearTimeout(this.previewTimeout);
            }
            this.previewTimeout = setTimeout(() => {
                this.updatePreview();
            }, 400);
        },

        initPreviewIframe() {
            const iframe = document.getElementById('preview-iframe');
            if (!iframe || this.previewInitialized) return;

            // Set up iframe once using srcdoc — prevents browser middleware from injecting into JS strings
            const initialHtml = [
                '<!DOCTYPE html><html><head>',
                '<meta charset="utf-8">',
                '<script src="https://cdn.tailwindcss.com"><\/script>',
                '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet" crossorigin="anonymous">',
                '<style>',
                'body { font-family: "Plus Jakarta Sans", sans-serif; margin: 0; background-color: #f8fafc; transform: translateZ(0); height: 100vh; overflow-y: auto; overflow-x: hidden; }',
                '#preview-container { padding: 20px; min-height: 100%; }',
                '<\/style>',
                '<\/head><body>',
                '<div id="preview-container"><\/div>',
                '<\/body><\/html>'
            ].join('');

            iframe.srcdoc = initialHtml;
            this.previewInitialized = true;

            // Safely poll for container availability instead of using flakier load events
            let checkCount = 0;
            const checkInterval = setInterval(() => {
                checkCount++;
                if (checkCount > 50) { // Timeout after 5 seconds
                    clearInterval(checkInterval);
                    return;
                }

                try {
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    if (doc) {
                        const container = doc.getElementById('preview-container');
                        if (container) {
                            clearInterval(checkInterval);
                            this.previewReady = true;
                            this.updatePreview();
                        }
                    }
                } catch (e) {
                    // Suppress transient cross-origin or loading exceptions
                }
            }, 100);
        },

        updatePreview() {
            if (!this.previewReady || !monacoEditorInstance) return;

            try {
                const iframe = document.getElementById('preview-iframe');
                if (!iframe) return;

                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (!doc) return;

                const container = doc.getElementById('preview-container');
                if (!container) return;

                let html = monacoEditorInstance.getValue();

                // Process/replace variables
                if (this.form.variables && this.form.variables.length > 0) {
                    this.form.variables.forEach(v => {
                        if (!v.key) return;
                        const escapedKey = v.key.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                        const pattern = '\\{' + '\\{\\s*\\$' + escapedKey + '\\s*\\}' + '\\}';
                        const regex = new RegExp(pattern, 'g');
                        
                        let replacement = v.default || ('[' + v.label + ']');
                        if (v.type === 'search_param') {
                            replacement = new URLSearchParams(window.location.search).get(v.default) || 'Tamu Spesial';
                        }
                        
                        html = html.replace(regex, replacement);
                    });
                }

                container.innerHTML = html;
            } catch (error) {
                console.warn('Failed to update preview:', error);
            }
        },

        async generateThumbnail() {
            const iframe = document.getElementById('preview-iframe');
            if (!iframe) return;

            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (!doc) return;

            const container = doc.getElementById('preview-container');
            if (!container) return;

            // Wait for all fonts inside the iframe to load fully
            await doc.fonts.ready;
            await new Promise(r => setTimeout(r, 200));

            // Temporarily change container style to shrink-wrap the component exactly
            const originalStyle = container.getAttribute('style') || '';
            container.style.display = 'inline-block';
            container.style.padding = '0px';
            container.style.margin = '0px';
            container.style.border = 'none';
            container.style.background = 'transparent';

            try {
                // Use html-to-image for pixel-perfect SVG-based rendering
                const blob = await window.htmlToImage.toBlob(container, {
                    pixelRatio: 2,
                    skipFonts: false
                });

                // Restore original styling of container in the live preview iframe
                container.setAttribute('style', originalStyle);

                this.thumbnailBlob = blob;
                this.thumbnailUrl = URL.createObjectURL(blob);
                return blob;
            } catch (err) {
                // Restore original style in case of failure
                container.setAttribute('style', originalStyle);
                console.error('Failed to generate thumbnail', err);
            }
        },

        async save() {
            if(this.isSaving) return;
            
            // Validate
            if(!this.form.name || !this.form.slug) {
                Swal.fire('Error', 'Name and Slug are required.', 'error');
                return;
            }

            this.isSaving = true;
            this.form.code = monacoEditorInstance.getValue();
            
            // Try generating thumbnail if we don't have one
            await this.generateThumbnail();

            const isUpdate = !!this.form.id;
            const url = isUpdate 
                ? `/admin/component-library/${this.form.id}` 
                : `/admin/component-library`;
                
            const method = isUpdate ? 'PUT' : 'POST';
            
            // Prepare data
            const data = {
                ...this.form,
                variables: JSON.stringify(this.form.variables),
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if(result.success) {
                    this.form.id = result.component.id;
                    
                    // Upload thumbnail if we generated one
                    if(this.thumbnailBlob) {
                        const formData = new FormData();
                        formData.append('thumbnail', this.thumbnailBlob, 'thumb.jpg');
                        formData.append('_token', data._token);
                        
                        await fetch(`/admin/api/component-library/${this.form.id}/thumbnail`, {
                            method: 'POST',
                            body: formData
                        });
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    if(!isUpdate) {
                        // Update URL without reload to change to edit mode
                        window.history.replaceState({}, '', `/admin/component-library/${this.form.id}/edit`);
                    }
                } else {
                    throw new Error(result.message || 'Validation failed');
                }
            } catch (error) {
                Swal.fire('Error', 'Failed to save component. ' + error.message, 'error');
            } finally {
                this.isSaving = false;
            }
        }
    }));
});
</script>
@endsection
