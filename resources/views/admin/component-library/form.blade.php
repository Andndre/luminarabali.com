@extends('layouts.editor')

@section('content')
    @php
        $defaultCode = '';
        $defaultVariables = '[]';
        if (!isset($component) && !old('code') && isset($category)) {
            switch ($category) {
                case 'cover':
                    $defaultCode = '<!-- Cover Section -->
<div class="invitation-cover relative flex min-h-[100dvh] flex-col items-center justify-center overflow-hidden bg-[#2C1E16] p-8 text-center" x-show="!isOpen">
    <div class="fixed absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url(\'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop\')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-[#2C1E16] via-transparent to-transparent"></div>
    
    <div class="relative z-10 flex flex-col items-center justify-center">
        <p class="invitation-accent mb-4 text-xs uppercase tracking-[0.3em] text-white/80">The Wedding Of</p>
        <h1 class="invitation-title mb-8 font-serif text-5xl text-white md:text-7xl">
            <span x-text="groom_name">Romeo</span> <br> <span class="invitation-accent my-4 block text-3xl italic text-[#C5A059]">&amp;</span> <span x-text="bride_name">Juliet</span>
        </h1>
        
        <div class="mb-12 mt-12">
            <p class="mb-2 text-sm uppercase tracking-widest text-gray-400">Kepada Yth.</p>
            <p class="font-serif text-xl text-white" x-text="guest_name">Tamu Undangan</p>
        </div>
        
        <button @click="openInvitation()" 
                class="invitation-button group relative overflow-hidden border border-white/30 bg-white/10 px-8 py-4 text-xs uppercase tracking-[0.2em] text-white backdrop-blur-md transition-all duration-500 hover:bg-white/20">
            <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                Buka Undangan
            </span>
        </button>
    </div>
</div>';
                    $defaultVariables = '[]';
                    break;
                case 'hero':
                    $defaultCode = '<!-- Hero Section -->
<section class="invitation-hero relative flex min-h-[100dvh] flex-col items-center justify-center overflow-hidden bg-white p-8 text-center">
    <div class="fixed absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url(\'https://images.unsplash.com/photo-1520854221256-17451cc331bf?q=80&w=2000&auto=format&fit=crop\')"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white"></div>
    
    <div class="relative z-10 mx-auto max-w-2xl pt-32">
        <p class="invitation-accent mb-6 text-sm uppercase tracking-[0.3em]" data-reveal="up">We Are Getting Married</p>
        <h2 class="invitation-title mb-8 font-serif text-6xl text-gray-900 md:text-8xl" data-reveal="up">
            <span x-text="groom_name">Romeo</span> <br> <span class="invitation-accent text-4xl italic">&amp;</span> <br> <span x-text="bride_name">Juliet</span>
        </h2>
        <div class="invitation-line animate-float mx-auto mt-12 h-32 w-px bg-gray-400" data-reveal="up"></div>
    </div>
</section>';
                    $defaultVariables = '[]';
                    break;
                case 'text':
                    $defaultCode = '<div class="mx-auto max-w-2xl p-8 text-center" data-reveal="up">
    <h3 class="mb-3 text-2xl font-bold text-gray-800">Judul Text</h3>
    <p class="leading-relaxed text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
</div>';
                    $defaultVariables = '[]';
                    break;
                case 'button':
                    $defaultCode = '<div class="p-4 text-center" data-reveal="up">
    <button class="rounded-full bg-gray-900 px-6 py-3 font-medium text-white shadow-md transition-colors hover:bg-gray-800">
        Click Me
    </button>
</div>';
                    $defaultVariables = '[]';
                    break;
                case 'rsvp':
                    $defaultCode = '<div class="mx-auto max-w-xl rounded-2xl border border-gray-100 bg-white p-6 shadow-sm" x-data="rsvpForm()" data-reveal="up">
    <h3 class="mb-6 text-center text-2xl font-bold text-gray-800">RSVP</h3>
    <form class="space-y-4" @submit.prevent="submitRsvp" x-show="!isSuccess">
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Nama Lengkap</label>
            <input type="text" x-model="formData.guest_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ketik nama Anda...">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Kehadiran</label>
            <select x-model="formData.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="Hadir">Ya, Saya akan hadir</option>
                <option value="Tidak Hadir">Maaf, tidak bisa hadir</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Pesan & Doa (Opsional)</label>
            <textarea x-model="formData.comments" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" placeholder="Tuliskan ucapan..."></textarea>
        </div>
        <button type="submit" :disabled="isSubmitting" class="w-full flex items-center justify-center rounded-xl bg-gray-900 py-3 font-medium text-white transition hover:bg-gray-800">
            <span x-show="!isSubmitting">Kirim RSVP</span>
            <span x-show="isSubmitting">Mengirim...</span>
        </button>
    </form>
    <div x-show="isSuccess" class="text-center py-8" style="display: none;">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Terima Kasih!</h3>
        <p class="text-gray-600">Kehadiran Anda sangat berarti bagi kami.</p>
    </div>
</div>';
                    $defaultVariables = '[]';
                    break;
                case 'event':
                    $defaultCode = '<div class="mx-auto grid max-w-3xl grid-cols-1 gap-6 p-6 md:grid-cols-2">
    <div class="rounded-2xl bg-gray-50 p-6 text-center">
        <h4 class="mb-2 text-xl font-bold">Akad Nikah</h4>
        <p class="mb-4 text-gray-600">Minggu, 12 Desember 2026<br>08.00 - Selesai</p>
        <p class="text-sm text-gray-500">Masjid Agung Bali</p>
    </div>
    <div class="rounded-2xl bg-gray-50 p-6 text-center">
        <h4 class="mb-2 text-xl font-bold">Resepsi</h4>
        <p class="mb-4 text-gray-600">Minggu, 12 Desember 2026<br>11.00 - 14.00</p>
        <p class="text-sm text-gray-500">Hotel Aston Bali</p>
    </div>
</div>';
                    break;
                case 'countdown':
                    $defaultCode = '<div class="flex justify-center gap-4 p-8">
    <div class="text-center"><div class="mb-2 flex h-16 w-16 items-center justify-center rounded-xl bg-gray-900 text-2xl font-bold text-white">12</div><span class="text-xs uppercase text-gray-500">Hari</span></div>
    <div class="text-center"><div class="mb-2 flex h-16 w-16 items-center justify-center rounded-xl bg-gray-900 text-2xl font-bold text-white">08</div><span class="text-xs uppercase text-gray-500">Jam</span></div>
    <div class="text-center"><div class="mb-2 flex h-16 w-16 items-center justify-center rounded-xl bg-gray-900 text-2xl font-bold text-white">45</div><span class="text-xs uppercase text-gray-500">Menit</span></div>
</div>';
                    break;
                case 'gallery':
                    $defaultCode = '<div class="mx-auto grid max-w-4xl grid-cols-2 gap-4 p-4 md:grid-cols-3">
    <div class="aspect-square overflow-hidden rounded-xl bg-gray-200"><img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&q=80&w=400" class="h-full w-full object-cover"></div>
    <div class="aspect-square overflow-hidden rounded-xl bg-gray-200"><img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&q=80&w=400" class="h-full w-full object-cover"></div>
    <div class="aspect-square overflow-hidden rounded-xl bg-gray-200"><img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80&w=400" class="h-full w-full object-cover"></div>
</div>';
                    break;
                case 'map':
                    $defaultCode = '<div class="mx-auto w-full max-w-3xl p-4">
    <div class="flex aspect-video w-full items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 text-gray-400">
        <!-- Embed iframe gmaps disini -->
        [Google Maps Iframe]
    </div>
    <div class="mt-4 text-center">
        <a href="#" class="inline-flex items-center gap-2 rounded-full bg-gray-900 px-6 py-2 text-sm font-medium text-white hover:bg-gray-800">Buka di Google Maps</a>
    </div>
</div>';
                    break;
                case 'divider':
                    $defaultCode = '<div class="flex w-full justify-center py-8">
    <div class="relative h-px w-24 bg-gray-300">
        <div class="absolute left-1/2 top-1/2 h-2 w-2 -translate-x-1/2 -translate-y-1/2 rounded-full border border-gray-300 bg-white"></div>
    </div>
</div>';
                    break;
                case 'footer':
                    $defaultCode = '<footer class="w-full bg-gray-900 py-8 text-center text-gray-400">
    <p class="text-sm">Made with love by Luminara</p>
</footer>';
                    break;
                case 'video':
                    $defaultCode = '<div class="mx-auto w-full max-w-3xl p-4">
    <div class="flex aspect-video items-center justify-center overflow-hidden rounded-2xl bg-gray-900">
        <div class="flex h-16 w-16 cursor-pointer items-center justify-center rounded-full bg-white/20 backdrop-blur-sm transition hover:bg-white/30">
            <svg class="ml-1 h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6z"></path></svg>
        </div>
    </div>
</div>';
                    break;
                case 'section':
                    $defaultCode = '<section class="w-full bg-white py-16">
    <div class="mx-auto max-w-4xl px-6">
        <h2 class="mb-12 text-center text-3xl font-bold">Judul Section</h2>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <!-- Kolom 1 -->
            <div>
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&q=80&w=600" class="w-full rounded-2xl shadow-lg">
            </div>
            <!-- Kolom 2 -->
            <div class="flex flex-col justify-center">
                <h3 class="mb-4 text-2xl font-semibold">Sub Judul</h3>
                <p class="mb-6 text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <button class="self-start rounded-xl bg-black px-6 py-3 text-white">Selengkapnya</button>
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
    <div x-data="componentEditor()" class="relative flex h-full w-full flex-1 flex-col bg-white">

        <!-- Header -->
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.component-library.index') }}" class="text-gray-500 hover:text-gray-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ isset($component) ? 'Edit Component: ' . $component->name : 'Create Component' }}</h1>
                    <p class="mt-1 text-xs text-gray-500">Define reusable code snippet and its variables.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span x-show="isSaving" class="text-sm text-gray-500" x-cloak>Menyimpan...</span>
                <button @click="isDrawerOpen = true"
                    class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </button>
                <button @click="save()"
                    class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2 font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                        </path>
                    </svg>
                    Save
                </button>
            </div>
        </div>

        <!-- Main Content Split -->
        <div class="relative flex w-full flex-1 overflow-hidden">

            <!-- Left Panel: Monaco Editor -->
            <div class="flex h-full w-1/2 shrink-0 flex-col overflow-hidden border-r border-gray-800 bg-[#1e1e1e]">
                <div
                    class="font-mono-code flex shrink-0 justify-between border-b border-gray-800 bg-gray-900 px-4 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                    <span>Code Editor (Blade/HTML)</span>
                    <span>Use @{{ $variable_name }} for placeholders</span>
                </div>
                <div id="monaco-editor" x-ignore class="relative w-full flex-1"></div>
            </div>

            <!-- Right Panel: Realtime Preview -->
            <div class="relative flex h-full flex-1 flex-col overflow-hidden bg-white">
                <div
                    class="flex shrink-0 justify-between border-b border-gray-200 bg-gray-100 px-4 py-2 font-sans text-[11px] font-semibold uppercase tracking-wider text-gray-600">
                    <span>Realtime Preview</span>
                    <span class="font-sans text-xs text-gray-400">Updates dynamically as you type</span>
                </div>
                <div class="relative w-full flex-1 bg-slate-50">
                    <iframe id="preview-iframe" x-ignore class="h-full w-full border-0"></iframe>
                </div>
            </div>

            <!-- Drawer Overlay -->
            <div x-show="isDrawerOpen" class="fixed inset-0 z-40 bg-gray-900/20 backdrop-blur-sm transition-opacity"
                @click="isDrawerOpen = false" x-transition.opacity x-cloak></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 right-0 z-50 flex w-[420px] transform flex-col border-l border-gray-200 bg-gray-50 shadow-2xl transition-transform duration-300 ease-in-out"
                :class="isDrawerOpen ? 'translate-x-0' : 'translate-x-full'" x-cloak>
                <div
                    class="z-10 flex shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6 py-4 shadow-sm">
                    <h2 class="font-sans font-bold text-gray-900">Settings & Variables</h2>
                    <button @click="isDrawerOpen = false"
                        class="cursor-pointer rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-900">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 space-y-6 overflow-y-auto p-6">
                    <!-- General Settings -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3
                            class="mb-4 border-b border-gray-100 pb-2.5 font-sans text-xs font-bold uppercase tracking-wider text-gray-500">
                            General Settings</h3>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="mb-1.5 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Name</label>
                                <input type="text" x-ref="nameInput" x-model="form.name" @input="generateSlug"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 font-sans text-sm text-gray-900 shadow-sm transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Slug</label>
                                <input type="text" x-model="form.slug"
                                    class="font-mono-code w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500"
                                    readonly>
                            </div>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="mb-1.5 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Category</label>
                                <div class="relative">
                                    <select x-model="form.category"
                                        class="w-full cursor-pointer appearance-none rounded-lg border border-gray-200 bg-white px-3 py-2 pr-10 font-sans text-sm text-gray-900 shadow-sm transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
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
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Type</label>
                                <div class="relative">
                                    <select x-model="form.type"
                                        class="w-full cursor-pointer appearance-none rounded-lg border border-gray-200 bg-white px-3 py-2 pr-10 font-sans text-sm text-gray-900 shadow-sm transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        <option value="component">Component (Single Block)</option>
                                        <option value="section">Section (Multiple Blocks)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label
                                class="mb-1.5 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Description</label>
                            <textarea x-model="form.description" rows="2"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 font-sans text-sm text-gray-900 shadow-sm transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                        </div>

                        <div class="flex items-center gap-6 border-t border-gray-100 pt-3">
                            <label class="group flex cursor-pointer items-center gap-2">
                                <input type="checkbox" x-model="form.is_public"
                                    class="h-4 w-4 cursor-pointer rounded border-gray-300 text-indigo-600 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <span
                                    class="font-sans text-sm font-medium text-gray-600 transition-colors group-hover:text-gray-900">Public
                                    (Visible to other creators)</span>
                            </label>
                            <label class="group flex cursor-pointer items-center gap-2">
                                <input type="checkbox" x-model="form.is_active"
                                    class="h-4 w-4 cursor-pointer rounded border-gray-300 text-indigo-600 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <span
                                    class="font-sans text-sm font-medium text-gray-600 transition-colors group-hover:text-gray-900">Active</span>
                            </label>
                        </div>
                    </div>

                    <!-- Variable Builder -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <h3 class="font-sans text-xs font-bold uppercase tracking-wider text-gray-500">Variables</h3>
                            <button @click="addVariable()"
                                class="flex cursor-pointer items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition-all hover:bg-indigo-100">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Variable
                            </button>
                        </div>

                        <div x-show="form.variables.length === 0"
                            class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 py-8 text-center text-sm italic text-gray-400">
                            No variables defined yet. Add variables to make your component dynamic.
                        </div>

                        <div class="space-y-4">
                            <template x-for="(variable, index) in form.variables" :key="index">
                                <div
                                    class="group relative rounded-xl border border-l-4 border-gray-200 border-l-indigo-600 bg-white p-5 shadow-sm transition-all hover:shadow-md">
                                    <button @click="removeVariable(index)"
                                        class="absolute right-3 top-3 cursor-pointer rounded-lg p-1 text-gray-400 opacity-0 transition-all hover:bg-red-50 hover:text-red-600 group-hover:opacity-100"
                                        title="Delete Variable">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>

                                    <div class="grid grid-cols-12 gap-3">
                                        <div class="col-span-4">
                                            <label
                                                class="mb-1 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Key
                                                ($name)</label>
                                            <input type="text" x-model="variable.key"
                                                class="font-mono-code w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-indigo-700 transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        </div>
                                        <div class="col-span-4">
                                            <label
                                                class="mb-1 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Label
                                                (UI)</label>
                                            <input type="text" x-model="variable.label"
                                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-900 transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        </div>
                                        <div class="col-span-4">
                                            <label
                                                class="mb-1 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Type</label>
                                            <div class="relative">
                                                <select x-model="variable.type"
                                                    class="w-full cursor-pointer appearance-none rounded-lg border border-gray-200 bg-white px-3 py-1.5 pr-8 text-sm text-gray-900 shadow-sm transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
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
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5">
                                                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-span-6">
                                            <label
                                                class="mb-1 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Default
                                                Value</label>
                                            <input type="text" x-model="variable.default"
                                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-900 transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        </div>
                                        <div class="col-span-6">
                                            <label
                                                class="mb-1 block font-sans text-xs font-semibold uppercase tracking-wider text-gray-500">Help
                                                Text</label>
                                            <input type="text" x-model="variable.description"
                                                placeholder="Optional explanation"
                                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-900 transition-all hover:border-gray-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Live Preview / Thumbnail Capture Area -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3
                            class="mb-4 border-b border-gray-100 pb-2.5 font-sans text-xs font-bold uppercase tracking-wider text-gray-500">
                            Thumbnail</h3>

                        <div class="flex items-start gap-4">
                            <div
                                class="relative flex aspect-video w-1/2 items-center justify-center overflow-hidden rounded-lg border border-dashed border-gray-200 bg-white">
                                <template x-if="thumbnailUrl">
                                    <img :src="thumbnailUrl" class="h-full w-full object-contain p-4">
                                </template>
                                <template x-if="!thumbnailUrl">
                                    <span class="font-sans text-sm text-gray-400">No thumbnail</span>
                                </template>
                            </div>

                            <div class="flex-1 space-y-3">
                                <p class="font-sans text-xs text-gray-500">Thumbnail generated automatically via
                                    html2canvas when you save. You can also force generate it below.</p>

                                <button @click="generateThumbnail()"
                                    class="w-full cursor-pointer rounded-lg border border-gray-200 bg-white px-4 py-2 font-sans text-sm font-semibold text-gray-700 transition-all hover:bg-gray-50 hover:text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                    Capture Thumbnail Now
                                </button>
                            </div>
                        </div>
                        <textarea id="raw_code" class="hidden">{{ old('code', $component->code ?? $defaultCode) }}</textarea>
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
                isDrawerOpen: {{ !isset($component) ? 'true' : 'false' }},
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
                    variables: {!! isset($component)
                        ? json_encode($component->variables)
                        : (isset($defaultVariables)
                            ? $defaultVariables
                            : '[]') !!},
                    code: document.getElementById('raw_code').value
                },

                previewTimeout: null,

                init() {
                    this.initPreviewIframe();
                    this.initMonaco();
                    
                    if (this.isDrawerOpen) {
                        setTimeout(() => {
                            if (this.$refs.nameInput) {
                                this.$refs.nameInput.focus();
                            }
                        }, 400);
                    }
                    this.$watch('form.variables', () => {
                        this.updatePreviewDebounced();
                    }, {
                        deep: true
                    });
                },

                generateSlug() {
                    if (!this.form.id) { // Only auto-generate on create
                        this.form.slug = this.form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-')
                            .replace(/(^-|-$)+/g, '');
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
                            minimap: {
                                enabled: false
                            },
                            wordWrap: 'on',
                            fontSize: 14,
                            padding: {
                                top: 16
                            }
                        });

                        // Update preview on changes
                        monacoEditorInstance.onDidChangeModelContent(() => {
                            this.updatePreviewDebounced();
                        });

                        // Ctrl+S to save
                        monacoEditorInstance.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS,
                            () => {
                                this.save();
                            });

                        // Populate preview initially once editor is ready
                        this.updatePreviewDebounced();
                    };

                    if (window.monaco) {
                        createEditor();
                    } else {
                        require.config({
                            paths: {
                                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs'
                            }
                        });
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
                        '<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"><\/script>',
                        '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet" crossorigin="anonymous">',
                        '<style>',
                        'body { font-family: "Plus Jakarta Sans", sans-serif; margin: 0; background-color: #f8fafc; transform: translateZ(0); height: 100vh; overflow-y: auto; overflow-x: hidden; }',
                        '#preview-container { padding: 20px; min-height: 100%; }',
                        '<\/style>',
                        '<script>',
                        'window.event_date = "2026-08-24T12:00:00";',
                        'document.addEventListener("alpine:init", () => {',
                        '    Alpine.data("countdown", (target) => ({ days: "12", hours: "08", minutes: "45", seconds: "00", init() {} }));',
                        '    Alpine.data("rsvpForm", () => ({',
                        '        formData: { guest_name: "", status: "Hadir", comments: "" },',
                        '        isSubmitting: false, isSuccess: false, errorMessage: "",',
                        '        async submitRsvp() { this.isSubmitting = true; setTimeout(() => { this.isSubmitting = false; this.isSuccess = true; }, 1000); }',
                        '    }));',
                        '});',
                        '<\/script>',
                        '<\/head><body x-data=\'{ groom_name: "Romeo", bride_name: "Juliet", event_date: "2026-08-24T12:00:00", guest_name: "Tamu Spesial" }\'>',
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

                        // 1. Collect all variables into a mockData object for Alpine
                        let mockData = {
                            isOpen: false,
                            isPlaying: false,
                            openInvitation: function() { this.isOpen = true; },
                            toggleAudio: function() { this.isPlaying = !this.isPlaying; }
                        };
                        if (this.form.variables && this.form.variables.length > 0) {
                            this.form.variables.forEach(v => {
                                if (!v.key) return;
                                
                                if (v.type === 'search_param') {
                                    mockData[v.key] = new URLSearchParams(window.location.search).get(v.default) || 'Tamu Spesial';
                                } else {
                                    mockData[v.key] = v.default || ('[' + v.label + ']');
                                }
                            });
                        }

                        // 2. Convert to JSON string and escape double quotes
                        // We must serialize functions manually since JSON.stringify strips them
                        let xDataString = '{';
                        xDataString += 'isOpen: false, isPlaying: false, ';
                        xDataString += 'openInvitation() { this.isOpen = true; }, ';
                        xDataString += 'toggleAudio() { this.isPlaying = !this.isPlaying; }';
                        
                        // Add the rest of the dynamic variables
                        const dynamicVars = {};
                        Object.keys(mockData).forEach(k => {
                            if (typeof mockData[k] !== 'function' && k !== 'isOpen' && k !== 'isPlaying') {
                                dynamicVars[k] = mockData[k];
                            }
                        });
                        
                        if (Object.keys(dynamicVars).length > 0) {
                            xDataString += ', ' + JSON.stringify(dynamicVars).slice(1, -1);
                        }
                        xDataString += '}';
                        
                        xDataString = xDataString.replace(/"/g, '&quot;');

                        // 3. Wrap HTML with x-data state
                        container.innerHTML = '<div x-data="' + xDataString + '" class="w-full h-full preview-wrapper">' + html + '</div>';

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
                    if (this.isSaving) return;

                    // Validate
                    if (!this.form.name || !this.form.slug) {
                        Swal.fire('Error', 'Name and Slug are required.', 'error');
                        return;
                    }

                    this.isSaving = true;
                    this.form.code = monacoEditorInstance.getValue();

                    // Try generating thumbnail if we don't have one
                    await this.generateThumbnail();

                    const isUpdate = !!this.form.id;
                    const url = isUpdate ?
                        `/admin/component-library/${this.form.id}` :
                        `/admin/component-library`;

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

                        if (result.success) {
                            this.form.id = result.component.id;

                            // Upload thumbnail if we generated one
                            if (this.thumbnailBlob) {
                                const formData = new FormData();
                                formData.append('thumbnail', this.thumbnailBlob, 'thumb.jpg');
                                formData.append('_token', data._token);

                                await fetch(
                                    `/admin/api/component-library/${this.form.id}/thumbnail`, {
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

                            if (!isUpdate) {
                                // Update URL without reload to change to edit mode
                                window.history.replaceState({}, '',
                                    `/admin/component-library/${this.form.id}/edit`);
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
