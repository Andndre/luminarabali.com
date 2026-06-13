@extends('layouts.admin')

@section('title', 'Edit Undangan')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.invitations.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Edit Undangan</h1>
        <p class="text-gray-600 mt-1">Ubah data undangan</p>
    </div>

    <form action="{{ route('admin.invitations.update', $invitation->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Invitation Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Undangan</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Undangan *</label>
                    <input type="text" name="title" value="{{ old('title', $invitation->title) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pria *</label>
                        <input type="text" name="groom_name" value="{{ old('groom_name', $invitation->groom_name) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        @error('groom_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wanita *</label>
                        <input type="text" name="bride_name" value="{{ old('bride_name', $invitation->bride_name) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        @error('bride_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Acara *</label>
                    <input type="date" name="event_date" value="{{ old('event_date', $invitation->event_date?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    @error('event_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" value="{{ $invitation->slug }}" readonly
                               class="w-full px-4 py-2 border rounded-lg bg-gray-50 text-gray-600">
                        <p class="mt-1 text-xs text-gray-500">URL untuk undangan</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="px-4 py-2 border rounded-lg bg-gray-50">
                            @if($invitation->published_status === 'published')
                                <span class="text-green-800 font-medium">Published</span>
                            @elseif($invitation->published_status === 'draft')
                                <span class="text-yellow-800 font-medium">Draft</span>
                            @else
                                <span class="text-gray-800 font-medium">Archived</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Data (JSON Override)</label>
                    <textarea name="meta_data" rows="5"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent font-mono text-sm"
                              placeholder="{'bg_music': '...', 'custom_image': '...'}">{{ old('meta_data', $invitation->meta_data ? json_encode($invitation->meta_data, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Opsional. Gunakan format JSON untuk menimpa properti spesifik halaman ini (misal: bg_music atau image_url).</p>
                    @error('meta_data')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
            <a href="{{ route('invitation.show', $invitation->slug) }}" target="_blank" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition flex items-center text-blue-600 border-blue-200 bg-blue-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Undangan (Live)
            </a>

            <div class="flex gap-3">
                <a href="{{ route('admin.invitations.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
