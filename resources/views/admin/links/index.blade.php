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

    {{-- Division Filter Tabs (super_admin only) --}}
    @if(auth()->user()->division === 'super_admin')
        <div class="mb-4 flex gap-2">
            @foreach(['photobooth' => 'Photobooth', 'visual' => 'Visual'] as $value => $label)
                <a href="{{ route('admin.links.index', ['division' => $value]) }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ ($divisionFilter ?? '') === $value ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
            <a href="{{ route('admin.links.index') }}"
               class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ !$divisionFilter ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </a>
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm" x-data="linksTable()">
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full border-collapse text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-5 py-3.5 w-10"></th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Thumbnail</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Judul</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">URL</th>
                        @if(auth()->user()->division === 'super_admin')
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Division</th>
                        @endif
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 links-desktop-tbody">
                    <template x-for="(link, idx) in links" :key="link.id">
                        <tr class="group transition hover:bg-gray-50/70" :data-link-id="link.id">
                            {{-- Drag handle --}}
                            <td class="px-5 py-3.5 text-center cursor-move drag-handle">
                                <svg class="h-4 w-4 text-gray-300 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </td>
                            {{-- # --}}
                            <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-400"
                                x-text="idx + 1{{ $paginated ? ' + (' . ($paginated->currentPage() - 1) . ' * ' . $paginated->perPage() . ')' : '' }}"></td>
                            {{-- Thumbnail --}}
                            <td class="px-5 py-3.5">
                                <template x-if="link.thumbnail">
                                    <img :src="'/storage/' + link.thumbnail" class="h-10 w-10 rounded-lg object-cover border border-gray-200">
                                </template>
                                <template x-if="!link.thumbnail && link.icon">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200">
                                        <img :src="'/images/icons/' + link.icon + '.svg'" class="h-5 w-5 object-contain">
                                    </div>
                                </template>
                                <template x-if="!link.thumbnail && !link.icon">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200">
                                        <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                    </div>
                                </template>
                            </td>
                            {{-- Title --}}
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <div class="text-sm font-semibold text-gray-900" x-text="link.title"></div>
                            </td>
                            {{-- URL --}}
                            <td class="px-5 py-3.5">
                                <a :href="link.url" target="_blank" class="text-sm text-blue-500 hover:text-blue-700 hover:underline"
                                   x-text="link.url.length > 35 ? link.url.substring(0, 35) + '...' : link.url"></a>
                            </td>
                            {{-- Division (super_admin only) --}}
                            @if(auth()->user()->division === 'super_admin')
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-bold uppercase tracking-wide text-gray-600"
                                      x-text="link.business_unit"></span>
                            </td>
                            @endif
                            {{-- Status toggle --}}
                            <td class="whitespace-nowrap px-5 py-3.5">
                                <form :action="'{{ route('admin.links.index') }}/' + link.id" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="title" :value="link.title">
                                    <input type="hidden" name="url" :value="link.url">
                                    <input type="hidden" name="is_active" :value="link.is_active ? '0' : '1'">
                                    <input type="hidden" name="business_unit" :value="link.business_unit">
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold transition"
                                            :class="link.is_active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'">
                                        <span class="h-1.5 w-1.5 rounded-full" :class="link.is_active ? 'bg-green-500' : 'bg-gray-400'"></span>
                                        <span x-text="link.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                    </button>
                                </form>
                            </td>
                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-70 transition group-hover:opacity-100">
                                    <a :href="'{{ route('admin.links.index') }}/' + link.id + '/edit'"
                                       class="rounded-lg p-1.5 text-blue-500 transition hover:bg-blue-50 hover:text-blue-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form :action="'{{ route('admin.links.index') }}/' + link.id" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)"
                                                class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                    {{-- Empty state --}}
                    <template x-if="links.length === 0">
                        <tr>
                            <td colspan="{{ auth()->user()->division === 'super_admin' ? 8 : 7 }}" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
                                        <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-700">Belum ada link</p>
                                        <p class="mt-0.5 text-sm text-gray-400">Tambahkan link baru untuk ditampilkan di halaman publik.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="block divide-y divide-gray-100 md:hidden links-mobile-container">
            <template x-for="(link, idx) in links" :key="link.id">
                <div class="flex items-start gap-3 p-4" :data-link-id="link.id">
                    {{-- Drag handle --}}
                    <div class="flex items-center pt-1 cursor-move shrink-0 drag-handle">
                        <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="link.thumbnail">
                                    <img :src="'/storage/' + link.thumbnail" class="h-10 w-10 rounded-lg object-cover border border-gray-200 shrink-0">
                                </template>
                                <template x-if="!link.thumbnail && link.icon">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200 shrink-0">
                                        <img :src="'/images/icons/' + link.icon + '.svg'" class="h-5 w-5 object-contain">
                                    </div>
                                </template>
                                <template x-if="!link.thumbnail && !link.icon">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 border border-gray-200 shrink-0">
                                        <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate" x-text="link.title"></div>
                                    <a :href="link.url" target="_blank" class="text-xs text-blue-500 hover:underline truncate block max-w-45" x-text="link.url.length > 30 ? link.url.substring(0, 30) + '...' : link.url"></a>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold"
                                  :class="link.is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500'"
                                  x-text="link.is_active ? 'Aktif' : 'Nonaktif'"></span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-50 pt-2">
                            <span class="text-xs text-gray-400" x-text="'Order: ' + (idx + 1)"></span>
                            <div class="flex gap-2">
                                <a :href="'{{ route('admin.links.index') }}/' + link.id + '/edit'" class="text-blue-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form :action="'{{ route('admin.links.index') }}/' + link.id" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="text-red-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="links.length === 0">
                <div class="p-8 text-center text-gray-400">Belum ada data link.</div>
            </template>
        </div>
    </div>

    {{-- Pagination (only shown when not filtered by division) --}}
    @if($paginated && $paginated->hasPages())
        <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
            <div class="hidden md:block">
                Menampilkan {{ $paginated->firstItem() ?? 0 }}–{{ $paginated->lastItem() ?? 0 }} dari {{ $paginated->total() }} link
            </div>
            <div class="hidden md:block">
                <div class="flex gap-1">
                    @foreach($paginated->getUrlRange(max(1, $paginated->currentPage() - 2), min($paginated->lastPage(), $paginated->currentPage() + 2)) as $page => $url)
                        @if($page == $paginated->currentPage())
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-black text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="block md:hidden">
                {{ $paginated->links() }}
            </div>
        </div>
        <div class="hidden md:block">
            {{ $paginated->links() }}
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        window.linksData = {!! json_encode($links->map(fn($l) => ['id' => $l->id, 'title' => $l->title, 'thumbnail' => $l->thumbnail, 'icon' => $l->icon, 'url' => $l->url, 'order' => $l->order, 'is_active' => $l->is_active, 'business_unit' => $l->business_unit])->values()) !!};
        window.linksReorderUrl = "{{ route('admin.links.reorder') }}";
    </script>
    <script>
        window.linksTable = function () {
            return {
                links: window.linksData || [],
                sortableDesktop: null,
                sortableMobile: null,
                init() {
                    var self = this;
                    this.$nextTick(function () {
                        var desktopTbody = document.querySelector('.links-desktop-tbody');
                        var mobileContainer = document.querySelector('.links-mobile-container');
                        if (desktopTbody) {
                            self.sortableDesktop = new Sortable(desktopTbody, {
                                animation: 200,
                                handle: '.drag-handle',
                                onEnd: function () { self._syncFromDom('.links-desktop-tbody', 'tr[data-link-id]'); }
                            });
                        }
                        if (mobileContainer) {
                            self.sortableMobile = new Sortable(mobileContainer, {
                                animation: 200,
                                handle: '.drag-handle',
                                onEnd: function () { self._syncFromDom('.links-mobile-container', '[data-link-id]'); }
                            });
                        }
                    });
                },
                _syncFromDom: function (containerSelector, itemSelector) {
                    var ids = [].slice.call(document.querySelectorAll(containerSelector + ' ' + itemSelector)).map(function (el) {
                        return parseInt(el.getAttribute('data-link-id'), 10);
                    });
                    var reordered = ids.map(function (id) {
                        return this.links.find(function (l) { return l.id === id; });
                    }.bind(this)).filter(Boolean);
                    if (reordered.length === this.links.length) {
                        this.links = reordered;
                        this._saveOrder();
                    }
                },
                _saveOrder: function () {
                    var self = this;
                    fetch(window.linksReorderUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ order: this.links.map(function (l) { return l.id; }) }),
                    }).then(function () { window.location.reload(); });
                }
            };
        };
    </script>
@endsection
