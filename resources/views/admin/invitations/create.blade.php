@extends('layouts.admin')

@section('title', 'Buat Undangan Baru')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.invitations.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Buat Undangan Digital Baru</h1>
        <p class="text-gray-600 mt-1">Mulai dengan memilih template atau blank canvas</p>
    </div>

    <form action="{{ route('admin.invitations.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Step 1: Choose Template or Blank -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pilih Template</h2>

            @if($templates->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <label class="cursor-pointer">
                        <input type="radio" name="template_id" value="" checked class="sr-only peer">
                        <div class="border-2 rounded-xl p-4 text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition">
                            <div class="aspect-[3/4] bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <p class="font-medium text-gray-900">Blank Canvas</p>
                            <p class="text-sm text-gray-500">Mulai dari awal</p>
                        </div>
                    </label>

                    @foreach($templates as $template)
                        <label class="cursor-pointer">
                            <input type="radio" name="template_id" value="{{ $template->id }}" class="sr-only peer">
                            <div class="border-2 rounded-xl p-4 text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition">
                                @if($template->thumbnail)
                                    <div class="aspect-[3/4] bg-gray-100 rounded-lg mb-3 overflow-hidden">
                                        <img src="{{ Storage::url($template->thumbnail) }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="aspect-[3/4] bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-lg mb-3 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <p class="font-medium text-gray-900">{{ $template->name }}</p>
                                <p class="text-sm text-gray-500">{{ $template->category ?? 'Template' }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <p class="text-yellow-800">Belum ada template. Anda akan mulai dengan blank canvas.</p>
                </div>
            @endif
        </div>

        <!-- Step 2: Invitation Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="{
            groom: '{{ old('groom_name') }}',
            bride: '{{ old('bride_name') }}',
            slug: '{{ old('slug') }}',
            userEditedSlug: {{ old('slug') ? 'true' : 'false' }},
            updateSlug() {
                if (!this.userEditedSlug) {
                    this.slug = (this.groom + '-' + this.bride).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                }
            }
        }">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Undangan</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Undangan *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                           placeholder="Contoh: The Wedding Of Romeo & Juliet">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pria *</label>
                        <input type="text" name="groom_name" x-model="groom" @input="updateSlug" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                               placeholder="Nama lengkap pria">
                        @error('groom_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wanita *</label>
                        <input type="text" name="bride_name" x-model="bride" @input="updateSlug" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                               placeholder="Nama lengkap wanita">
                        @error('bride_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug / URL Undangan *</label>
                    <div class="flex rounded-lg shadow-sm">
                      <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                        {{ rtrim(url('/invitation/'), '/') }}/
                      </span>
                      <input type="text" name="slug" x-model="slug" @input="userEditedSlug = true" required
                             class="flex-1 min-w-0 block w-full px-4 py-2 rounded-none rounded-r-lg border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                             placeholder="romeo-juliet">
                    </div>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Acara *</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    @error('event_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.invitations.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                Buat & Edit Undangan
            </button>
        </div>
    </form>
</div>
@endsection
