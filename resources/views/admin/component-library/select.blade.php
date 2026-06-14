@extends('layouts.admin')

@section('title', 'Pilih Kategori Komponen')

@section('content')
<div class="max-w-7xl mx-auto p-6 lg:p-8 relative">
    
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.component-library.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 font-medium flex items-center gap-1 mb-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Library
            </a>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Pilih Jenis Komponen</h1>
            <p class="text-gray-500 mt-1">Pilih kategori komponen yang ingin Anda buat. Kami akan menyiapkan struktur dasarnya untuk Anda.</p>
        </div>
    </div>

    <!-- Category Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        
        <!-- Cover -->
        <a href="{{ route('admin.component-library.create', ['category' => 'cover']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4 relative overflow-hidden">
                <div class="w-24 h-full bg-indigo-100/50 rounded flex flex-col items-center justify-center gap-2 group-hover:scale-105 transition-transform duration-500 relative">
                    <div class="w-12 h-1 bg-indigo-300 rounded-full"></div>
                    <div class="w-16 h-1.5 bg-indigo-400 rounded-full"></div>
                    <div class="w-10 h-1 bg-indigo-200 rounded-full mt-2"></div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Cover Page</h3>
                <p class="text-xs text-gray-500 mt-0.5">Layar pertama undangan</p>
            </div>
        </a>

        <!-- Hero -->
        <a href="{{ route('admin.component-library.create', ['category' => 'hero']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4 relative overflow-hidden">
                <div class="w-full h-16 bg-blue-100/50 rounded flex items-center justify-center gap-2 relative overflow-hidden group-hover:shadow-inner transition-all">
                    <!-- Image icon wireframe -->
                    <svg class="w-6 h-6 text-blue-300 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Hero Section</h3>
                <p class="text-xs text-gray-500 mt-0.5">Gambar utama / Header</p>
            </div>
        </a>

        <!-- Text -->
        <a href="{{ route('admin.component-library.create', ['category' => 'text']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4">
                <div class="w-full max-w-[120px] flex flex-col gap-2 relative">
                    <div class="w-3/4 h-2 bg-gray-300 rounded-full group-hover:w-full transition-all duration-500"></div>
                    <div class="w-full h-1.5 bg-gray-200 rounded-full"></div>
                    <div class="w-full h-1.5 bg-gray-200 rounded-full"></div>
                    <div class="w-2/3 h-1.5 bg-gray-200 rounded-full"></div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Text & Typography</h3>
                <p class="text-xs text-gray-500 mt-0.5">Paragraf, kutipan, judul</p>
            </div>
        </a>

        <!-- Event Details -->
        <a href="{{ route('admin.component-library.create', ['category' => 'event']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4">
                <div class="w-24 h-20 bg-white shadow-sm border border-gray-100 rounded-lg p-2 flex flex-col gap-2 group-hover:-translate-y-1 transition-transform">
                    <div class="w-full h-3 bg-red-100 rounded flex justify-between px-1 items-center">
                        <div class="w-1.5 h-1.5 bg-red-300 rounded-full"></div>
                        <div class="w-1.5 h-1.5 bg-red-300 rounded-full"></div>
                    </div>
                    <div class="w-full flex-1 flex flex-col items-center justify-center gap-1">
                        <div class="w-8 h-1.5 bg-gray-300 rounded-full"></div>
                        <div class="w-12 h-1 bg-gray-200 rounded-full"></div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Event Details</h3>
                <p class="text-xs text-gray-500 mt-0.5">Informasi acara & waktu</p>
            </div>
        </a>

        <!-- Gallery -->
        <a href="{{ route('admin.component-library.create', ['category' => 'gallery']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4">
                <div class="grid grid-cols-2 gap-1.5 w-24 h-20 group-hover:scale-105 transition-transform">
                    <div class="bg-indigo-100 rounded"></div>
                    <div class="bg-indigo-200 rounded"></div>
                    <div class="bg-indigo-200 rounded"></div>
                    <div class="bg-indigo-100 rounded"></div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Gallery</h3>
                <p class="text-xs text-gray-500 mt-0.5">Grid foto atau slider</p>
            </div>
        </a>

        <!-- Countdown -->
        <a href="{{ route('admin.component-library.create', ['category' => 'countdown']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4">
                <div class="flex gap-1.5 group-hover:gap-2 transition-all">
                    <div class="w-6 h-8 bg-gray-800 rounded flex flex-col items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-x-0 top-1/2 h-px bg-gray-900"></div>
                        <div class="w-3 h-0.5 bg-gray-600 rounded"></div>
                    </div>
                    <div class="w-6 h-8 bg-gray-800 rounded flex flex-col items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-x-0 top-1/2 h-px bg-gray-900"></div>
                        <div class="w-3 h-0.5 bg-gray-600 rounded"></div>
                    </div>
                    <div class="text-gray-400 font-bold -translate-y-0.5">:</div>
                    <div class="w-6 h-8 bg-gray-800 rounded flex flex-col items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-x-0 top-1/2 h-px bg-gray-900"></div>
                        <div class="w-3 h-0.5 bg-gray-600 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Countdown</h3>
                <p class="text-xs text-gray-500 mt-0.5">Penghitung waktu mundur</p>
            </div>
        </a>

        <!-- RSVP -->
        <a href="{{ route('admin.component-library.create', ['category' => 'rsvp']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4">
                <div class="w-24 bg-white p-2.5 rounded-lg shadow-sm border border-gray-100 flex flex-col gap-1.5 group-hover:-translate-y-1 transition-transform">
                    <div class="w-1/2 h-1 bg-gray-300 rounded"></div>
                    <div class="w-full h-3 bg-gray-100 rounded border border-gray-200 mt-1 group-hover:border-indigo-300 transition-colors"></div>
                    <div class="w-full h-3 bg-gray-100 rounded border border-gray-200 group-hover:border-indigo-300 transition-colors"></div>
                    <div class="w-full h-4 bg-indigo-500 rounded mt-1 opacity-80 group-hover:opacity-100 transition-opacity"></div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">RSVP Form</h3>
                <p class="text-xs text-gray-500 mt-0.5">Formulir kehadiran tamu</p>
            </div>
        </a>

        <!-- Map -->
        <a href="{{ route('admin.component-library.create', ['category' => 'map']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4 relative overflow-hidden">
                <!-- Map pattern background -->
                <div class="absolute inset-0 opacity-10 flex flex-col justify-between">
                    <div class="w-full h-px bg-gray-900"></div><div class="w-full h-px bg-gray-900"></div><div class="w-full h-px bg-gray-900"></div><div class="w-full h-px bg-gray-900"></div>
                </div>
                <div class="absolute inset-0 opacity-10 flex justify-between">
                    <div class="w-px h-full bg-gray-900"></div><div class="w-px h-full bg-gray-900"></div><div class="w-px h-full bg-gray-900"></div><div class="w-px h-full bg-gray-900"></div>
                </div>
                <svg class="w-8 h-8 text-red-500 z-10 group-hover:-translate-y-2 group-hover:scale-110 transition-transform duration-300 drop-shadow-md" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Map Location</h3>
                <p class="text-xs text-gray-500 mt-0.5">Peta Google Maps</p>
            </div>
        </a>

        <!-- Button -->
        <a href="{{ route('admin.component-library.create', ['category' => 'button']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
            <div class="h-32 bg-gray-50 flex items-center justify-center p-4">
                <div class="px-5 py-2 bg-gray-900 text-white text-xs font-medium rounded-full flex items-center gap-2 group-hover:bg-indigo-600 group-hover:scale-105 transition-all shadow-md">
                    <span>Click Me</span>
                    <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Button / CTA</h3>
                <p class="text-xs text-gray-500 mt-0.5">Tombol aksi interaktif</p>
            </div>
        </a>

        <!-- Section -->
        <a href="{{ route('admin.component-library.create', ['category' => 'section']) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-purple-500 hover:shadow-lg hover:ring-1 hover:ring-purple-500 transition-all duration-300 md:col-span-2 lg:col-span-3">
            <div class="h-32 bg-purple-50/50 flex items-center justify-center p-4">
                <div class="w-full max-w-sm h-full flex gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                    <!-- Sidebar wireframe -->
                    <div class="w-1/3 bg-white rounded-lg shadow-sm border border-purple-100 flex flex-col gap-2 p-2">
                        <div class="w-full h-1/2 bg-purple-100 rounded"></div>
                        <div class="w-3/4 h-2 bg-gray-200 rounded-full mt-1"></div>
                        <div class="w-1/2 h-2 bg-gray-200 rounded-full"></div>
                    </div>
                    <!-- Main content wireframe -->
                    <div class="w-2/3 bg-white rounded-lg shadow-sm border border-purple-100 flex flex-col gap-2 p-2 relative overflow-hidden">
                        <div class="w-1/2 h-2 bg-gray-300 rounded-full mt-1"></div>
                        <div class="w-full h-1.5 bg-gray-200 rounded-full mt-1"></div>
                        <div class="w-full h-1.5 bg-gray-200 rounded-full"></div>
                        <div class="w-4/5 h-1.5 bg-gray-200 rounded-full"></div>
                        
                        <div class="absolute bottom-2 right-2 w-8 h-3 bg-purple-500 rounded"></div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-purple-50/30">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-900 group-hover:text-purple-700 transition-colors">Complete Section</h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">ADVANCED</span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Komposisi lengkap gabungan beberapa elemen sekaligus.</p>
            </div>
        </a>

    </div>
</div>
@endsection
