{{-- Side Panel Component Library --}}
<div x-data="templateLibrary" :class="panels.library ? 'w-[320px] opacity-100' : 'w-0 border-none opacity-0'"
    class="flex shrink-0 flex-col overflow-hidden border-r border-gray-200 bg-white shadow-xl transition-all duration-300 ease-in-out"
    x-cloak>

    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 p-4">
        <div>
            <h2 class="font-bold text-gray-900">Library</h2>
            <p class="text-xs text-gray-500">Components & Sections</p>
        </div>
        <button type="button" @click="toggleView('library')"
            class="rounded p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-500">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>
    </div>

    {{-- Filters --}}
    <div class="z-10 space-y-3 border-b border-gray-100 bg-white p-4 shadow-sm">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" x-model="search" placeholder="Cari komponen..."
                class="w-full rounded border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500">
        </div>
        <select x-model="selectedCategory"
            class="w-full rounded border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500">
            <option value="">Semua Kategori</option>
            <option value="cover">Cover</option>
            <option value="hero">Hero</option>
            <option value="text">Text & Typography</option>
            <option value="event">Event Details</option>
            <option value="gallery">Gallery</option>
            <option value="countdown">Countdown</option>
            <option value="rsvp">RSVP</option>
            <option value="section">Full Section</option>
        </select>
    </div>

    {{-- Component List --}}
    <div class="relative flex-1 space-y-4 overflow-y-auto bg-gray-50/50 p-4">
        <div x-show="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/80">
            <svg class="h-6 w-6 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                    stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>

        <div id="component-library-list" class="space-y-4">
            <template x-for="item in filteredComponents" :key="item.id">
                <div @click="insertComponent(item.id)" :data-id="item.id"
                    class="library-item group cursor-grab overflow-hidden rounded border border-gray-200 bg-white transition hover:border-blue-500 hover:shadow-md">
                    <div class="relative aspect-video bg-gray-100">
                        <template x-if="item.thumbnail">
                            <img :src="item.thumbnail ? '/' + item.thumbnail : ''" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!item.thumbnail">
                            <div class="flex h-full w-full items-center justify-center text-gray-400">
                                <svg class="h-6 w-6 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </template>

                        {{-- Hover overlay --}}
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 backdrop-blur-[1px] transition-opacity group-hover:opacity-100">
                            <span
                                class="flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-xs font-medium text-white shadow-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Insert
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="mb-1.5 flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                                x-text="item.category"></span>
                            <span x-show="item.type === 'section'"
                                class="rounded border border-blue-100 bg-blue-50 px-1.5 py-0.5 text-[9px] text-blue-600">Section</span>
                        </div>
                        <h3 class="text-sm font-medium leading-tight text-gray-900 transition group-hover:text-blue-600"
                            x-text="item.name"></h3>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="!loading && filteredComponents.length === 0" class="py-10 text-center text-sm text-gray-500">
            Tidak ada komponen ditemukan.
        </div>
    </div>
</div>
