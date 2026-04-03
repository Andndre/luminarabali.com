@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daftar Link</h1>
            <p class="mt-0.5 text-gray-500">Kelola link yang akan ditampilkan di halaman publik.</p>
        </div>
        <a href="{{ route('admin.links.create') }}"
            class="flex items-center gap-2 rounded-lg bg-black px-5 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-gray-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Link
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full border-collapse text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Thumbnail</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Judul</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">URL</th>
                        @if(auth()->user()->division === 'super_admin')
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Division</th>
                        @endif
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Order</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($links as $link)
                        <tr class="group transition hover:bg-gray-50/70">
                            <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-400">{{ $loop->iteration + ($links->currentPage() - 1) * $links->perPage() }}</td>
                            <td class="px-5 py-3.5">
                                @if($link->thumbnail)
                                    <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                        class="h-10 w-10 rounded-lg object-cover border border-gray-200">
                                @elseif($link->icon)
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200">
                                        <img src="{{ asset('images/icons/' . $link->icon . '.svg') }}"
                                            class="h-5 w-5 object-contain" alt="{{ $link->title }}">
                                    </div>
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200">
                                        <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <div class="text-sm font-semibold text-gray-900">{{ $link->title }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ $link->url }}" target="_blank" class="text-sm text-blue-500 hover:text-blue-700 hover:underline">
                                    {{ Str::limit($link->url, 35) }}
                                </a>
                            </td>
                            @if(auth()->user()->division === 'super_admin')
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-bold uppercase tracking-wide text-gray-600">
                                    {{ $link->business_unit }}
                                </span>
                            </td>
                            @endif
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <span class="text-sm font-medium text-gray-700">{{ $link->order }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <form action="{{ route('admin.links.update', $link->id) }}" method="POST" id="active-form-{{ $link->id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="title" value="{{ $link->title }}">
                                    <input type="hidden" name="url" value="{{ $link->url }}">
                                    <input type="hidden" name="order" value="{{ $link->order }}">
                                    <input type="hidden" name="is_active" value="{{ $link->is_active ? '0' : '1' }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold transition {{ $link->is_active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $link->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                        {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-70 transition group-hover:opacity-100">
                                    <a href="{{ route('admin.links.edit', $link->id) }}"
                                        class="rounded-lg p-1.5 text-blue-500 transition hover:bg-blue-50 hover:text-blue-700"
                                        title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.links.destroy', $link->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)"
                                            class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600"
                                            title="Hapus">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->division === 'super_admin' ? 8 : 7 }}" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
                                        <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-700">Belum ada link</p>
                                        <p class="mt-0.5 text-sm text-gray-400">Tambahkan link baru untuk ditampilkan di halaman publik.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="block divide-y divide-gray-100 md:hidden">
            @forelse($links as $link)
                <div class="space-y-3 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($link->thumbnail)
                                <img src="{{ asset('storage/' . $link->thumbnail) }}" alt="{{ $link->title }}"
                                    class="h-10 w-10 rounded-lg object-cover border border-gray-200 shrink-0">
                            @elseif($link->icon)
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200 shrink-0">
                                    <img src="{{ asset('images/icons/' . $link->icon . '.svg') }}"
                                        class="h-5 w-5 object-contain" alt="{{ $link->title }}">
                                </div>
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200 shrink-0">
                                    <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate">{{ $link->title }}</div>
                                <a href="{{ $link->url }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate block max-w-[180px]">{{ Str::limit($link->url, 30) }}</a>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $link->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-50 pt-2">
                        <span class="text-xs text-gray-400">Order: {{ $link->order }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.links.edit', $link->id) }}" class="text-blue-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.links.destroy', $link->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="text-red-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400">Belum ada data link.</div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($links->hasPages())
        <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
            <div class="hidden md:block">
                Menampilkan {{ $links->firstItem() ?? 0 }}–{{ $links->lastItem() ?? 0 }} dari {{ $links->total() }} link
            </div>
            <div class="hidden md:block">
                <div class="flex gap-1">
                    @foreach($links->getUrlRange(max(1, $links->currentPage() - 2), min($links->lastPage(), $links->currentPage() + 2)) as $page => $url)
                        @if($page == $links->currentPage())
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-black text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="block md:hidden">
                {{ $links->links() }}
            </div>
        </div>
        <div class="hidden md:block">
            {{ $links->links() }}
        </div>
    @endif

    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Hapus Link?',
                text: "Link yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) button.closest('form').submit();
            })
        }
    </script>
@endsection
