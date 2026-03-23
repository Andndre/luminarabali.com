@extends('layouts.admin')

@section('title', 'Templates')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Templates</h1>
                <p class="mt-1 text-gray-600">Kelola template undangan digital</p>
            </div>
            <a href="{{ route('admin.templates.create') }}"
                class="flex items-center rounded-xl bg-black px-6 py-2 font-semibold text-white shadow-lg transition hover:bg-gray-800">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Template
            </a>
        </div>

        <!-- Templates Grid -->
        @if ($templates->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($templates as $template)
                    <div
                        class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                        <!-- Thumbnail -->
                        <div class="aspect-[3/4] bg-gray-100">
                            @if ($template->thumbnail)
                                <img src="{{ Storage::url($template->thumbnail) }}" alt="{{ $template->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-yellow-100 to-yellow-200">
                                    <svg class="h-16 w-16 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <div class="mb-4 flex items-start justify-between">
                                <div class="min-w-0 flex-1 pr-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                    @if ($template->category)
                                        <p class="text-sm text-gray-500">{{ $template->category }}</p>
                                    @endif
                                </div>
                                @if ($template->is_active)
                                    <span
                                        class="whitespace-nowrap rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Active</span>
                                @else
                                    <span
                                        class="whitespace-nowrap rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">Inactive</span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="space-y-2">
                                <a href="{{ route('admin.templates.editor-react', $template->id) }}"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-yellow-500 px-4 py-2.5 font-medium text-black transition hover:bg-yellow-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit with Visual Editor
                                </a>

                                <div class="flex gap-2">
                                    <a href="{{ route('admin.templates.edit', $template->id) }}"
                                        class="flex flex-1 items-center justify-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium transition hover:bg-gray-50">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="MM11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-3m0 0V5a2 2 0 012-2h6a2 2 0 012 2v14a2 2 0 002 2H8a2 2 0 01-2-2V9a2 2 0 012-2z" />
                                        </svg>
                                        Edit Info
                                    </a>

                                    <form action="{{ route('admin.templates.duplicate', $template->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        <button type="submit"
                                            class="flex w-full items-center justify-center gap-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium transition hover:bg-gray-50">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Duplicate
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST"
                                        class="flex-1" onsubmit="return confirm('Hapus template ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex w-full items-center justify-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-100">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-gray-100 bg-white py-12 text-center shadow-sm">
                <svg class="mx-auto mb-4 h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mb-2 text-lg font-medium text-gray-900">Belum ada template</h3>
                <p class="mb-4 text-gray-600">Buat template undangan pertama</p>
                <a href="{{ route('admin.templates.create') }}"
                    class="inline-block rounded-xl bg-black px-6 py-2 font-semibold text-white transition hover:bg-gray-800">
                    Buat Template
                </a>
            </div>
        @endif
    </div>
@endsection
