@extends('layouts.admin')

@section('title', 'Edit Template')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.templates.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Edit Template</h1>
    </div>

    <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Template *</label>
                <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                <input type="text" name="slug" value="{{ old('slug', $template->slug) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    <option value="">Pilih Kategori</option>
                    <option value="rustic" {{ old('category', $template->category) === 'rustic' ? 'selected' : '' }}>Rustic</option>
                    <option value="modern" {{ old('category', $template->category) === 'modern' ? 'selected' : '' }}>Modern</option>
                    <option value="elegant" {{ old('category', $template->category) === 'elegant' ? 'selected' : '' }}>Elegant</option>
                    <option value="minimalist" {{ old('category', $template->category) === 'minimalist' ? 'selected' : '' }}>Minimalist</option>
                    <option value="floral" {{ old('category', $template->category) === 'floral' ? 'selected' : '' }}>Floral</option>
                    <option value="vintage" {{ old('category', $template->category) === 'vintage' ? 'selected' : '' }}>Vintage</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>

                @if($template->thumbnail)
                    <div class="mb-3">
                        <img src="{{ Storage::url($template->thumbnail) }}" alt="{{ $template->name }}" class="h-32 w-auto rounded-lg border">
                    </div>
                @endif

                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-yellow-500 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="thumbnail" class="relative cursor-pointer bg-white rounded-md font-medium text-yellow-600 hover:text-yellow-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-yellow-500">
                                <span>Change file</span>
                                <input id="thumbnail" name="thumbnail" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                    </div>
                </div>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">{{ old('description', $template->description) }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $template->is_active) ? 'checked' : '' }} class="w-4 h-4 text-yellow-500 rounded focus:ring-yellow-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Template Aktif</label>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.templates.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
