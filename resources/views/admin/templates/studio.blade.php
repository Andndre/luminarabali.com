@extends('layouts.studio')

@section('title', 'Studio — ' . $template->name)

@section('content')
<div x-data="studioApp()" x-init="init()" class="h-screen flex flex-col">
    {{-- Toolbar --}}
    <header class="h-14 shrink-0 flex items-center gap-3 px-4 border-b border-gray-200 bg-white">
        <a href="{{ route('admin.templates.index') }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900" title="Kembali">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        </a>
        <h1 class="font-bold text-gray-900 truncate max-w-xs" title="{{ $template->name }}">{{ $template->name }}</h1>
        <span class="text-xs font-semibold rounded-full px-2 py-0.5 capitalize"
            :class="{ draft: 'bg-yellow-100 text-yellow-800', published: 'bg-green-100 text-green-800', archived: 'bg-gray-100 text-gray-800' }[status]"
            x-text="status"></span>

        <div class="flex-1"></div>

        {{-- Mode Lanjutan: buka container/Basic/CSS mentah (guideline §2.0) --}}
        <button @click="toggleAdvanced()" type="button"
            :title="advanced ? 'Matikan Mode Lanjutan' : 'Mode Lanjutan: container, blok dasar, CSS/HTML mentah'"
            class="flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold border"
            :class="advanced ? 'border-black bg-black text-white' : 'border-gray-200 text-gray-500 hover:text-gray-900'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Lanjutan
        </button>

        {{-- Device toggle (ikon, bukan emoji) --}}
        <div class="flex rounded-lg bg-gray-100 p-0.5">
            <button @click="device = 'mobile'" title="Preview mobile" class="p-1.5 rounded-md" :class="device === 'mobile' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
            </button>
            <button @click="device = 'desktop'" title="Preview desktop" class="p-1.5 rounded-md" :class="device === 'desktop' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
            </button>
        </div>

        <button @click="publish()" :disabled="publishing"
            class="rounded-lg bg-black px-4 py-1.5 text-xs font-semibold text-white hover:bg-gray-800 disabled:opacity-50"
            x-text="publishing ? '…' : (status === 'published' ? 'Publish Ulang' : 'Publish')"></button>
    </header>

    <div class="flex-1 flex min-h-0">

    {{-- Panel kiri: Struktur --}}
    <div class="w-72 shrink-0 flex flex-col border-r border-gray-200 bg-white">
        <div class="p-3 border-b border-gray-200">
            <div class="flex gap-1 rounded-lg bg-gray-100 p-0.5">
                <button @click="panel = 'sections'"
                    class="flex-1 text-xs font-semibold rounded-md px-2 py-1.5"
                    :class="panel === 'sections' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900'">
                    Struktur
                </button>
                <button @click="panel = 'theme'"
                    class="flex-1 text-xs font-semibold rounded-md px-2 py-1.5"
                    :class="panel === 'theme' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900'">
                    Theme
                </button>
            </div>
        </div>

        {{-- Panel Theme (brand kit) --}}
        <div x-show="panel === 'theme'" x-cloak class="flex-1 overflow-y-auto p-4 space-y-6">
            <section>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Warna</h2>
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="key in Object.keys(theme.colors)" :key="key">
                        <div>
                            <span class="text-xs text-gray-600 capitalize" x-text="key.replace('_', ' ')"></span>
                            <div class="mt-1 flex items-center gap-1.5 border rounded-lg px-1.5 py-1">
                                <input type="color" :value="theme.colors[key]"
                                    @input="setColor(key, $event.target.value)"
                                    class="h-7 w-7 shrink-0 rounded cursor-pointer border-0 bg-transparent p-0"
                                    title="Pilih warna">
                                <input type="text" class="theme-hex-input w-full text-xs font-mono uppercase border-0 focus:ring-0 p-0"
                                    maxlength="9" :value="theme.colors[key]"
                                    @change="(() => { const h = normalizeHex($event.target.value); if (h) setColor(key, h); else $event.target.value = theme.colors[key]; })()">
                            </div>
                        </div>
                    </template>
                </div>
            </section>
            <section>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Font</h2>
                <div class="space-y-3">
                    <template x-for="key in Object.keys(theme.fonts)" :key="key">
                        <label class="block">
                            <span class="text-xs text-gray-600 capitalize" x-text="key"></span>
                            <select :value="theme.fonts[key]" @change="setFont(key, $event.target.value)"
                                class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                                <template x-for="font in fonts" :key="font">
                                    <option :value="font" x-text="font"></option>
                                </template>
                            </select>
                        </label>
                    </template>
                </div>
            </section>
            <section>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Skala</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-xs text-gray-600">Ukuran Teks Dasar (px)</span>
                        <input type="number" min="8" max="40" step="1" :value="theme.scales.type_base"
                            @change="setScale('type_base', Number($event.target.value))"
                            class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-xs text-gray-600">Rasio Skala</span>
                        <input type="number" min="1" max="2" step="0.05" :value="theme.scales.type_ratio"
                            @change="setScale('type_ratio', Number($event.target.value))"
                            class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-xs text-gray-600">Radius (px)</span>
                        <input type="number" min="0" max="64" step="1" :value="theme.scales.radius"
                            @change="setScale('radius', Number($event.target.value))"
                            class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-xs text-gray-600">Jarak Section (px)</span>
                        <input type="number" min="0" max="200" step="1" :value="theme.scales.section_spacing"
                            @change="setScale('section_spacing', Number($event.target.value))"
                            class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </label>
                    <label class="block col-span-2">
                        <span class="text-xs text-gray-600">Bayangan</span>
                        <select :value="theme.scales.shadow_level" @change="setScale('shadow_level', $event.target.value)"
                            class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                            <template x-for="opt in ['none', 'sm', 'md', 'lg']" :key="opt">
                                <option :value="opt" x-text="opt"></option>
                            </template>
                        </select>
                    </label>
                </div>
            </section>
            <p class="text-xs text-gray-400">Perubahan tersimpan otomatis dan langsung terlihat di preview.</p>
        </div>

        <div x-show="panel === 'sections'" class="flex-1 overflow-y-auto p-3 space-y-1" x-ref="sectionList">
            <template x-if="!loaded">
                <div class="space-y-2 p-1">
                    <div class="h-9 rounded-lg bg-gray-100 animate-pulse"></div>
                    <div class="h-9 rounded-lg bg-gray-100 animate-pulse"></div>
                    <div class="h-9 rounded-lg bg-gray-100 animate-pulse"></div>
                </div>
            </template>
            <template x-for="s in sections" :key="s.id">
                <div :data-id="s.id">
                    <div @click="selectedId = s.id"
                        class="group flex items-center gap-2 rounded-lg border px-2.5 py-2 cursor-pointer select-none"
                        :class="selectedId === s.id ? 'border-black bg-gray-50' : 'border-gray-200 hover:bg-gray-50'">
                        <span class="drag-handle cursor-grab text-gray-300 group-hover:text-gray-400 shrink-0">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><circle cx="7" cy="5" r="1.5"/><circle cx="13" cy="5" r="1.5"/><circle cx="7" cy="10" r="1.5"/><circle cx="13" cy="10" r="1.5"/><circle cx="7" cy="15" r="1.5"/><circle cx="13" cy="15" r="1.5"/></svg>
                        </span>
                        <span class="shrink-0 text-gray-400" x-html="typeIcon(s.section_type)"></span>
                        <span class="flex-1 text-sm truncate" :class="{ 'opacity-40': !s.is_visible }" x-text="typeLabel(s.section_type)"></span>
                        <span x-show="s.custom_css" title="Section ini punya CSS kustom" class="text-[9px] font-semibold bg-purple-100 text-purple-700 rounded px-1">CSS</span>
                        <div class="hidden group-hover:flex items-center gap-0.5 text-gray-400">
                            <button @click.stop="toggleVisible(s)" :title="s.is_visible ? 'Sembunyikan' : 'Tampilkan'" class="p-1 rounded hover:bg-gray-200 hover:text-gray-900">
                                <svg x-show="s.is_visible" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg x-show="!s.is_visible" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                            <button @click.stop="duplicateSection(s)" title="Duplikat" class="p-1 rounded hover:bg-gray-200 hover:text-gray-900">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h2.25m8.25-8.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-8.25A2.25 2.25 0 017.5 18v-7.5a2.25 2.25 0 012.25-2.25h6.75z"/></svg>
                            </button>
                            <button @click.stop="savePreset(s)" title="Simpan sebagai preset" class="p-1 rounded hover:bg-gray-200 hover:text-gray-900">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            </button>
                            <button @click.stop="removeSection(s)" title="Hapus" class="p-1 rounded hover:bg-red-50 hover:text-red-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            </button>
                        </div>
                    </div>
                    <div x-show="advanced && classOf(s.section_type) === 'container'" x-cloak
                        class="mt-1 ml-3 pl-2 border-l border-dashed border-gray-200 space-y-1.5">
                        <template x-for="col in columnsOf(s.section_type)" :key="col">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 px-1 py-0.5"
                                    x-text="'Kolom ' + col"></p>
                                <div class="space-y-1" :data-column="col - 1" :data-parent="s.id"
                                    x-ref="col" x-init="$nextTick(() => initColumnSortable($el))">
                                    <template x-for="c in childrenOf(s.id, col - 1)" :key="c.id">
                                        <div :data-id="c.id" @click.stop="selectedId = c.id"
                                            class="group flex items-center gap-2 rounded-lg border px-2 py-1.5 cursor-pointer select-none bg-white"
                                            :class="selectedId === c.id ? 'border-black bg-gray-50' : 'border-gray-200 hover:bg-gray-50'">
                                            <span class="drag-handle cursor-grab text-gray-300 shrink-0">
                                                <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><circle cx="7" cy="5" r="1.5"/><circle cx="13" cy="5" r="1.5"/><circle cx="7" cy="10" r="1.5"/><circle cx="13" cy="10" r="1.5"/><circle cx="7" cy="15" r="1.5"/><circle cx="13" cy="15" r="1.5"/></svg>
                                            </span>
                                            <span class="shrink-0 text-gray-400" x-html="typeIcon(c.section_type)"></span>
                                            <span class="flex-1 text-xs truncate" :class="{ 'opacity-40': !c.is_visible }"
                                                x-text="typeLabel(c.section_type)"></span>
                                            <button @click.stop="removeSection(c)" title="Hapus"
                                                class="hidden group-hover:block p-0.5 rounded text-gray-400 hover:bg-red-50 hover:text-red-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <button @click.stop="openAddChild(s.id, col - 1)" type="button"
                                    class="mt-1 w-full rounded-lg border border-dashed border-gray-200 px-2 py-1 text-[11px] text-gray-400 hover:border-black hover:text-black">
                                    + Blok
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="loaded && sections.length === 0" class="flex flex-col items-center gap-2 text-center px-2 py-8 text-gray-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z"/></svg>
                <p class="text-sm">Belum ada section.</p>
            </div>
        </div>

        <div x-show="panel === 'sections'" class="p-3 border-t border-gray-200">
            <button @click="addOpen = true"
                class="w-full rounded-lg border border-dashed border-gray-300 px-3 py-2 text-sm text-gray-600 hover:border-black hover:text-black">
                + Tambah Section
            </button>
        </div>
    </div>

    {{-- Modal galeri tipe section --}}
    <div x-show="addOpen" x-cloak
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-8"
        @click.self="addOpen = false; addChildTarget = null" @keydown.escape.window="addOpen = false; addChildTarget = null">
        <div class="bg-white rounded-2xl w-full max-w-2xl p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900" x-text="addChildTarget ? 'Tambah Blok ke Kolom' : 'Tambah Section'"></h2>
                <button @click="addOpen = false; addChildTarget = null" class="p-1 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex gap-1 mb-4 rounded-lg bg-gray-100 p-0.5 w-fit">
                <button @click="addTab = 'types'"
                    class="text-xs font-semibold rounded-md px-3 py-1.5"
                    :class="addTab === 'types' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900'">
                    Tipe Section
                </button>
                <button @click="addTab = 'presets'"
                    class="text-xs font-semibold rounded-md px-3 py-1.5"
                    :class="addTab === 'presets' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900'">
                    Presets
                </button>
            </div>
            <div x-show="addTab === 'types'" class="space-y-5">
                <div x-show="!addChildTarget">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Section</p>
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="(label, type) in curatedTypes" :key="type">
                            <button @click="addSection(type)" class="flex flex-col items-start gap-2 border border-gray-200 rounded-xl p-4 text-sm text-left hover:border-black hover:bg-gray-50">
                                <span class="text-gray-400" x-html="typeIcon(type)"></span>
                                <span class="font-medium" x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <div x-show="advanced || addChildTarget" x-cloak>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-2">
                        Mode Lanjutan — kolom &amp; blok dasar
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="(label, type) in (addChildTarget ? basicTypes : advancedTypes)" :key="type">
                            <button @click="addChildTarget ? addSection(type, null, addChildTarget) : addSection(type)" class="flex flex-col items-start gap-2 border border-gray-200 rounded-xl p-4 text-sm text-left hover:border-black hover:bg-gray-50">
                                <span class="text-gray-400" x-html="typeIcon(type)"></span>
                                <span class="font-medium" x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            <div x-show="addTab === 'presets'" x-cloak>
                <div class="grid grid-cols-3 gap-3" x-show="presets.length > 0">
                    <template x-for="p in presets" :key="p.id">
                        <div class="group relative border border-gray-200 rounded-xl p-4 text-sm hover:border-black hover:bg-gray-50 cursor-pointer"
                            @click="addSection(p.section_type, p.props)">
                            <span class="block font-medium" x-text="p.name"></span>
                            <span class="block text-xs text-gray-400 mt-1"
                                x-text="(p.category ? p.category + ' · ' : '') + typeLabel(p.section_type)"></span>
                            <button @click.stop="deletePreset(p)" title="Hapus preset"
                                class="absolute top-2 right-2 hidden group-hover:block p-1 rounded text-gray-300 hover:bg-red-50 hover:text-red-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                <p x-show="presetsLoaded && presets.length === 0" class="text-sm text-gray-400 text-center py-6">
                    Belum ada preset. Simpan section sebagai preset lewat tombol simpan di panel struktur.
                </p>
            </div>
        </div>
    </div>

    {{-- Modal pustaka ornamen --}}
    <div x-show="ornamentPicker.open" x-cloak
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-8"
        @click.self="ornamentPicker.open = false" @keydown.escape.window="ornamentPicker.open = false">
        <div class="bg-white rounded-2xl w-full max-w-2xl p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Pustaka Ornamen</h2>
                <button @click="ornamentPicker.open = false" class="p-1 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <label class="block text-center text-sm border border-dashed border-gray-300 rounded-lg px-3 py-2 mb-4 cursor-pointer hover:border-black">
                + Upload ornamen baru
                <input type="file" accept="image/*,.svg" class="hidden"
                    @change="uploadOrnamentToItem($event)">
            </label>
            <template x-if="ornaments.length === 0">
                <p class="text-sm text-gray-400 text-center py-8">Belum ada ornamen. Upload di atas.</p>
            </template>
            <div class="grid grid-cols-5 gap-2">
                <template x-for="o in ornaments" :key="o.id">
                    <button type="button" @click="pickOrnament(o.file_path)" :title="o.asset_name"
                        class="border border-gray-200 rounded-lg p-1 hover:border-black bg-gray-50">
                        <img :src="mediaUrl(o.file_path)" class="w-full h-12 object-contain">
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Panel tengah: Preview --}}
    <div class="flex-1 bg-gray-100 flex flex-col min-w-0">
        <div class="flex-1 p-4 flex justify-center min-h-0">
            <iframe x-ref="preview" src="{{ route('admin.templates.studio.preview', $template->id) }}"
                class="h-full rounded-xl border border-gray-300 bg-white transition-all duration-300"
                :class="device === 'mobile' ? 'w-[375px]' : 'w-full'"></iframe>
        </div>
    </div>

    @include('admin.templates.studio._inspector')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.7/Sortable.min.js"></script>
<script>
function escapeHtml(s) {
    const div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
}

function studioApp() {
    return {
        status: @json($template->status),
        sections: [],
        selectedId: null,
        device: 'mobile',
        loaded: false,
        addOpen: false,
        addTab: 'types',
        presets: [],
        presetsLoaded: false,
        ornaments: [],
        ornamentPicker: { open: false, listKey: null, index: null },
        panel: 'sections',
        theme: @json($themeBase),
        fonts: @json($fonts),
        themeSaveTimer: null,
        fontsDirty: false,
        publishing: false,
        typeLabels: @json($sectionTypes),
        classes: @json($componentClasses),
        advanced: localStorage.getItem('luminara.studio.advanced') === '1',
        schema: @json($schema),
        inspectorTab: 'content',
        fieldErrors: {},
        hasChildren: {},
        children: [],
        addChildTarget: null, // { parentId, columnIndex } saat modal dibuka untuk isi kolom
        propSaveTimer: null,
        undoStack: [],
        redoStack: [],
        restoring: false,
        cssSaveTimer: null,
        cssError: null,

        async init() {
            const res = await fetch(`/admin/api/templates/{{ $template->id }}/load`);
            const data = await res.json();
            this.hasChildren = {};
            data.sections.forEach(s => {
                if (s.parent_id) this.hasChildren[String(s.parent_id)] = true;
            });
            this.children = data.sections
                .filter(s => s.parent_id)
                .map(s => ({ ...s, props: s.props ?? {} }));
            this.sections = data.sections
                .filter(s => !s.parent_id)
                .map(s => ({ ...s, props: s.props ?? {} }));
            this.loaded = true;
            this.initSortable();
            this.$watch('selectedId', () => {
                this.fieldErrors = {};
                this.cssError = null;
                this.inspectorTab = this.availableTabs[0]?.id ?? 'design';
            });
            this.$watch('addOpen', open => {
                if (open && !this.presetsLoaded) this.loadPresets();
            });
            // Galeri ornamen untuk kontrol tipe 'ornament' (gagal = kontrol tetap bisa path manual)
            fetch('/admin/api/assets?collection=ornament', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(d => { this.ornaments = d.data ?? []; })
                .catch(() => {});
            // Pesan dari iframe preview (mode studio): klik-pilih & inline edit.
            window.addEventListener('message', e => {
                if (e.origin !== window.location.origin || !e.data?.type) return;
                if (e.data.type === 'studio:select') {
                    this.panel = 'sections';
                    this.selectedId = String(e.data.id);
                }
                if (e.data.type === 'studio:edit') {
                    const s = this.sections.find(x => x.id === String(e.data.id));
                    if (!s) return;
                    this.pushUndo();
                    s.props[e.data.key] = e.data.value;
                    this.saveProps(s); // blur = final, tanpa debounce
                }
            });
            // Ctrl/Cmd+Z = undo, +Shift = redo (abaikan saat fokus di input)
            window.addEventListener('keydown', e => {
                if (!(e.ctrlKey || e.metaKey) || e.key.toLowerCase() !== 'z') return;
                const t = e.target;
                if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.isContentEditable)) return;
                e.preventDefault();
                e.shiftKey ? this.redo() : this.undo();
            });
        },

        get selected() {
            return this.selectedId ? this.sectionById(this.selectedId) : null;
        },

        typeLabel(type) {
            return this.typeLabels[type] ?? type;
        },

        typeIcon(type) {
            const p = {
                cover: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25z',
                hero: 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z',
                text: 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12',
                image: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z',
                countdown: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
                gallery: 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
                map: 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z',
                music: 'M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z',
                video: 'M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z',
                couple: 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
                event_details: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
                gift: 'M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H4.5a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
                quote: 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z',
                love_story: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
                live_stream: 'M9.348 14.651a3.75 3.75 0 010-5.303m5.304 0a3.75 3.75 0 010 5.303m-7.425 2.122a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.981 0 13.79M12 12h.008v.007H12V12z',
                wishes: 'M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155',
                rsvp: 'M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z',
                closing: 'M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5',
                code: 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
                divider: 'M19.5 12h-15',
                spacer: 'M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5',
                button: 'M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5',
                section_one_col: 'M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z',
            };
            p.section_two_col = p.section_one_col;
            p.section_three_col = p.section_one_col;
            const d = p[type] ?? p.gallery;
            return `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${d}"/></svg>`;
        },

        get availableTabs() {
            if (!this.selected) return [];
            return [
                { id: 'content', label: 'Konten' },
                { id: 'design', label: 'Desain' },
                { id: 'advanced', label: 'Lanjutan' },
            ].filter(t => t.id === 'advanced'
                // CSS/HTML mentah = perkakas Mode Lanjutan (guideline §2.0)
                ? this.advanced
                : this.fieldsFor(this.selected.section_type, t.id).length > 0);
        },

        toggleAdvanced() {
            this.advanced = !this.advanced;
            localStorage.setItem('luminara.studio.advanced', this.advanced ? '1' : '0');
            // Section yang sedang dipilih bisa jadi tak lagi punya tab aktif yang sah.
            this.inspectorTab = this.availableTabs[0]?.id ?? 'content';
        },

        classOf(type) {
            if (this.classes.container.includes(type)) return 'container';
            if (this.classes.basic.includes(type)) return 'basic';
            return 'feature';
        },

        columnsOf(type) {
            return this.classes.container_columns[type] ?? 0;
        },

        // WAJIB kembalikan salinan hasil filter (bukan array live) — konsisten dengan
        // helper list lain; mutasi di tempat merusak snapshot undo.
        childrenOf(parentId, columnIndex) {
            return this.children
                .filter(c => String(c.parent_id) === String(parentId)
                    && Number(c.props?.column_index ?? 0) === columnIndex)
                .sort((a, b) => a.order_index - b.order_index);
        },

        // Section (top-level atau anak) berdasarkan id — dipakai inspector & aksi baris.
        sectionById(id) {
            return this.sections.find(s => String(s.id) === String(id))
                ?? this.children.find(c => String(c.id) === String(id));
        },

        get curatedTypes() {
            return Object.fromEntries(Object.entries(this.typeLabels)
                .filter(([type]) => this.classOf(type) === 'feature'));
        },

        get advancedTypes() {
            return Object.fromEntries(Object.entries(this.typeLabels)
                .filter(([type]) => this.classOf(type) !== 'feature'));
        },

        get basicTypes() {
            return Object.fromEntries(Object.entries(this.typeLabels)
                .filter(([type]) => this.classOf(type) === 'basic'));
        },

        fieldsFor(type, group) {
            const schema = this.schema[type] ?? [];
            const variantField = schema.find(f => f.type === 'variant');
            const activeVariant = this.selected?.props?.variant ?? variantField?.default;
            return schema.filter(f => f.group === group && !f.hidden
                && (!f.variant || (activeVariant && f.variant.includes(activeVariant))));
        },

        variantLabel(v) {
            return String(v ?? '').split('-')
                .map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
        },

        variantSchematic(v) {
            // Skematik layout mini (viewBox 0 0 40 28), stroke currentColor. Fallback saat
            // thumbnail asli belum ada. Tiap varian = gambaran kasar bentuknya.
            const S = {
                'fullscreen': '<rect x="2" y="2" width="36" height="24" rx="2"/><rect x="12" y="12" width="16" height="2" fill="currentColor" stroke="none"/>',
                'split': '<rect x="2" y="2" width="36" height="24" rx="2"/><line x1="20" y1="2" x2="20" y2="26"/>',
                'minimal': '<rect x="2" y="2" width="36" height="24" rx="2" opacity=".3"/><rect x="13" y="13" width="14" height="2" fill="currentColor" stroke="none"/>',
                'centered-stacked': '<circle cx="20" cy="8" r="4"/><rect x="13" y="15" width="14" height="2" fill="currentColor" stroke="none"/><rect x="15" y="20" width="10" height="1.5" fill="currentColor" stroke="none"/>',
                'side-alternating': '<rect x="3" y="4" width="10" height="8" rx="1"/><line x1="16" y1="6" x2="30" y2="6"/><line x1="16" y1="9" x2="26" y2="9"/><rect x="27" y="16" width="10" height="8" rx="1"/><line x1="10" y1="18" x2="24" y2="18"/><line x1="14" y1="21" x2="24" y2="21"/>',
                'portrait-overlay': '<rect x="4" y="3" width="14" height="22" rx="1"/><rect x="22" y="3" width="14" height="22" rx="1"/><rect x="6" y="19" width="10" height="2" fill="currentColor" stroke="none"/><rect x="24" y="19" width="10" height="2" fill="currentColor" stroke="none"/>',
                'cards': '<rect x="3" y="9" width="7" height="10" rx="1"/><rect x="12" y="9" width="7" height="10" rx="1"/><rect x="21" y="9" width="7" height="10" rx="1"/><rect x="30" y="9" width="7" height="10" rx="1"/>',
                'minimal-line': '<rect x="4" y="12" width="4" height="4" fill="currentColor" stroke="none"/><line x1="12" y1="9" x2="12" y2="19"/><rect x="16" y="12" width="4" height="4" fill="currentColor" stroke="none"/><line x1="24" y1="9" x2="24" y2="19"/><rect x="28" y="12" width="4" height="4" fill="currentColor" stroke="none"/>',
                'ring': '<circle cx="9" cy="14" r="5"/><circle cx="20" cy="14" r="5"/><circle cx="31" cy="14" r="5"/>',
                'grid': '<rect x="3" y="4" width="10" height="8" rx="1"/><rect x="15" y="4" width="10" height="8" rx="1"/><rect x="27" y="4" width="10" height="8" rx="1"/><rect x="3" y="15" width="10" height="8" rx="1"/><rect x="15" y="15" width="10" height="8" rx="1"/><rect x="27" y="15" width="10" height="8" rx="1"/>',
                'masonry': '<rect x="3" y="4" width="10" height="12" rx="1"/><rect x="15" y="4" width="10" height="8" rx="1"/><rect x="27" y="4" width="10" height="14" rx="1"/><rect x="15" y="15" width="10" height="9" rx="1"/><rect x="3" y="19" width="10" height="5" rx="1"/>',
                'slider': '<rect x="9" y="6" width="22" height="16" rx="2"/><path d="M6 14l3-3M6 14l3 3"/><path d="M34 14l-3-3M34 14l-3 3"/>',
                'elevated': '<rect x="8" y="10" width="28" height="14" rx="2" opacity=".3"/><rect x="5" y="6" width="28" height="14" rx="2"/>',
                'custom-controls': '<rect x="3" y="3" width="34" height="22" rx="2"/><line x1="7" y1="9" x2="33" y2="9"/><rect x="7" y="16" width="9" height="5" rx="2.5"/><rect x="18" y="16" width="9" height="5" rx="2.5"/>',
                'underline': '<line x1="6" y1="9" x2="34" y2="9"/><line x1="6" y1="15" x2="34" y2="15"/><line x1="6" y1="21" x2="22" y2="21"/>',
                'bordered-cards': '<rect x="5" y="4" width="30" height="8" rx="1"/><rect x="5" y="15" width="30" height="8" rx="1"/>',
                'divider-list': '<line x1="6" y1="6" x2="30" y2="6"/><line x1="3" y1="11" x2="37" y2="11" opacity=".4"/><line x1="6" y1="16" x2="30" y2="16"/><line x1="3" y1="21" x2="37" y2="21" opacity=".4"/>',
            };
            const body = S[v] ?? '<rect x="4" y="6" width="32" height="16" rx="2" opacity=".5"/>';
            return `<svg viewBox="0 0 40 28" fill="none" stroke="currentColor" stroke-width="1.5" class="w-full h-8">${body}</svg>`;
        },

        val(field) {
            return this.selected?.props?.[field.key] ?? field.default;
        },

        hasOverride(key) {
            const v = this.selected?.props?.[key];
            return v !== undefined && v !== null;
        },

        setProp(field, value) {
            if (!this.propSaveTimer) this.pushUndo(); // snapshot pra-mutasi, sekali per burst
            this.selected.props[field.key] = value;
            this.queuePropSave();
        },

        resetProp(field) {
            if (!this.propSaveTimer) this.pushUndo();
            this.selected.props[field.key] = null; // null = unset di server (kembali ke theme/default)
            this.queuePropSave();
        },

        mediaUrl(v) {
            if (!v) return '';
            return /^(https?:)?\//.test(v) ? v : '/storage/' + v.replace(/^\/+/, '');
        },

        async uploadFile(file) {
            const fd = new FormData();
            fd.append('file', file);
            const res = await fetch('/admin/api/assets/upload', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: fd, // tanpa Content-Type manual — browser set boundary multipart sendiri
            });
            if (!res.ok) throw await res.json().catch(() => ({}));
            return (await res.json()).asset;
        },

        async uploadToProp(field, event) {
            const file = event.target.files[0];
            if (!file) return;
            try {
                this.setProp(field, (await this.uploadFile(file)).file_path);
            } catch {
                Swal.fire({ icon: 'error', title: 'Upload gagal', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
            }
            event.target.value = '';
        },

        isSvgPath(v) {
            return typeof v === 'string' && v.toLowerCase().endsWith('.svg');
        },

        ornItems(field) {
            const v = this.selected?.props?.[field.key];
            return Array.isArray(v) ? [...v] : [];
        },

        addOrnItem(field) {
            this.setProp(field, [...this.ornItems(field),
                { src: null, position: 'left', scale: 100, flip_h: false, flip_v: false, color: null }]);
        },

        setOrnItem(field, i, subkey, value) {
            const list = this.ornItems(field);
            list[i] = { ...list[i], [subkey]: value };
            this.setProp(field, list);
        },

        removeOrnItem(field, i) {
            const list = this.ornItems(field);
            list.splice(i, 1);
            this.setProp(field, list);
        },

        moveOrnItem(field, i, delta) {
            const list = this.ornItems(field);
            const [it] = list.splice(i, 1);
            list.splice(i + delta, 0, it);
            this.setProp(field, list);
        },

        openOrnamentPickerItem(listKey, index) {
            this.ornamentPicker = { open: true, listKey, index };
        },

        pickOrnament(path) {
            const { listKey, index } = this.ornamentPicker;
            if (listKey !== null && index !== null) {
                this.setOrnItem({ key: listKey }, index, 'src', path);
            }
            this.ornamentPicker.open = false;
        },

        async uploadOrnamentToItem(event) {
            const file = event.target.files[0];
            if (!file) return;
            try {
                const fd = new FormData();
                fd.append('file', file);
                fd.append('collection', 'ornament');
                const res = await fetch('/admin/api/assets/upload', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: fd,
                });
                if (!res.ok) throw await res.json().catch(() => ({}));
                const asset = (await res.json()).asset;
                this.ornaments = [asset, ...this.ornaments];
                this.pickOrnament(asset.file_path);
            } catch {
                Swal.fire({ icon: 'error', title: 'Upload ornamen gagal', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
            }
            event.target.value = '';
        },

        listOf(field) {
            return [...(this.selected.props[field.key] ?? [])];
        },

        async appendListItem(field, event) {
            const file = event.target.files[0];
            if (!file) return;
            try {
                const asset = await this.uploadFile(file);
                this.setProp(field, [...this.listOf(field), {
                    url: '/storage/' + asset.file_path,
                    alt: asset.asset_name ?? '',
                }]);
            } catch {
                Swal.fire({ icon: 'error', title: 'Upload gagal', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
            }
            event.target.value = '';
        },

        removeListItem(field, i) {
            const list = this.listOf(field);
            list.splice(i, 1);
            this.setProp(field, list);
        },

        moveListItem(field, i, delta) {
            const list = this.listOf(field);
            const [item] = list.splice(i, 1);
            list.splice(i + delta, 0, item);
            this.setProp(field, list);
        },

        // repeater: item = objek per sub-field (events, accounts, stories, …)
        repItems(field) {
            return (this.val(field) ?? []).map(item => ({ ...item }));
        },

        addRepItem(field) {
            const item = Object.fromEntries((field.fields ?? []).map(f => [f.key, f.default ?? '']));
            this.setProp(field, [...this.repItems(field), item]);
        },

        setRepItem(field, i, subkey, value) {
            const list = this.repItems(field);
            list[i] = { ...list[i], [subkey]: value };
            this.setProp(field, list);
        },

        removeRepItem(field, i) {
            const list = this.repItems(field);
            list.splice(i, 1);
            this.setProp(field, list);
        },

        moveRepItem(field, i, delta) {
            const list = this.repItems(field);
            const [item] = list.splice(i, 1);
            list.splice(i + delta, 0, item);
            this.setProp(field, list);
        },

        async uploadRepImage(field, i, subkey, event) {
            const file = event.target.files[0];
            if (!file) return;
            try {
                this.setRepItem(field, i, subkey, (await this.uploadFile(file)).file_path);
            } catch {
                this.toastError('Upload gagal');
            }
            event.target.value = '';
        },

        // ===== Undo/redo — scope: props + theme saja (add/delete/reorder tidak masuk stack) =====
        snapshot() {
            // props anak (this.children) ikut disimpan — selected sekarang bisa resolve
            // ke section top-level ATAU anak (lihat sectionById), jadi undo/redo harus
            // menutupi keduanya, bukan cuma this.sections.
            return JSON.parse(JSON.stringify({
                theme: this.theme,
                props: Object.fromEntries([...this.sections, ...this.children].map(s => [s.id, s.props])),
            }));
        },

        pushUndo() {
            if (this.restoring) return;
            this.undoStack.push(this.snapshot());
            if (this.undoStack.length > 50) this.undoStack.shift();
            this.redoStack = [];
        },

        undo() {
            const snap = this.undoStack.pop();
            if (!snap) return;
            this.redoStack.push(this.snapshot());
            this.applySnapshot(snap);
        },

        redo() {
            const snap = this.redoStack.pop();
            if (!snap) return;
            this.undoStack.push(this.snapshot());
            this.applySnapshot(snap);
        },

        async applySnapshot(snap) {
            this.restoring = true;
            try {
                if (JSON.stringify(snap.theme) !== JSON.stringify(this.theme)) {
                    this.theme = JSON.parse(JSON.stringify(snap.theme));
                    const doc = this.$refs.preview.contentWindow?.document?.documentElement;
                    if (doc) {
                        Object.entries(this.theme.colors).forEach(([k, v]) => doc.style.setProperty(`--color-${k}`, v));
                        Object.entries(this.theme.fonts).forEach(([k, v]) => doc.style.setProperty(`--font-${k}`, `'${v}'`));
                    }
                    // scales & font menurunkan --step-*/--radius/link font di server → reload
                    // sekali biar undo/redo perubahan skala/font terlihat, bukan cuma tersimpan.
                    this.fontsDirty = true;
                    this.queueThemeSave();
                }
                for (const s of [...this.sections, ...this.children]) {
                    const snapProps = snap.props[s.id];
                    if (snapProps === undefined) continue; // section baru pasca-snapshot — biarkan
                    if (JSON.stringify(snapProps) === JSON.stringify(s.props)) continue;
                    // payload restore: props snapshot + null untuk key yang harus di-unset
                    const payload = { ...snapProps };
                    for (const key of Object.keys(s.props)) {
                        if (!(key in snapProps)) payload[key] = null;
                    }
                    s.props = JSON.parse(JSON.stringify(snapProps));
                    try {
                        await this.api('PUT', `/admin/api/templates/sections/${s.id}`, { props: payload });
                        await this.swapSection(s);
                    } catch {
                        this.toastError('Gagal memulihkan section');
                    }
                }
            } finally {
                this.restoring = false;
            }
        },

        queuePropSave() {
            clearTimeout(this.propSaveTimer);
            const s = this.selected; // tangkap sekarang — user bisa pindah section saat debounce
            this.propSaveTimer = setTimeout(() => {
                this.propSaveTimer = null;
                this.saveProps(s);
            }, 300);
        },

        async saveProps(s) {
            try {
                const data = await this.api('PUT', `/admin/api/templates/sections/${s.id}`, { props: s.props });
                s.props = data.section.props ?? {}; // sinkron kanonik (key null sudah hilang)
                if (this.selectedId === s.id) this.fieldErrors = {};
                await this.swapSection(s);
            } catch (err) {
                if (this.selectedId !== s.id) return; // respons basi — user sudah pindah section
                if (err.errors) {
                    this.fieldErrors = Object.fromEntries(
                        Object.entries(err.errors).map(([k, v]) => [k.replace(/^props\./, ''), v[0]])
                    );
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal menyimpan', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
                }
            }
        },

        setCustomCss(value) {
            this.selected.custom_css = value;
            clearTimeout(this.cssSaveTimer);
            const s = this.selected;
            this.cssSaveTimer = setTimeout(async () => {
                try {
                    await this.api('PUT', `/admin/api/templates/sections/${s.id}`, { custom_css: s.custom_css || null });
                    if (this.selectedId === s.id) this.cssError = null;
                    await this.swapSection(s);
                } catch (err) {
                    if (this.selectedId !== s.id) return;
                    this.cssError = err.errors?.custom_css?.[0] ?? 'Gagal menyimpan CSS';
                }
            }, 500);
        },

        async swapSection(s) {
            // ponytail: section beranak fallback full reload — render-section me-render elements: []
            if (this.hasChildren[s.id]) return this.reloadPreview();
            try {
                const data = await this.api('POST', '/admin/api/studio/render-section', {
                    section_type: s.section_type, props: s.props, section_id: s.id,
                });
                const wrapper = this.$refs.preview.contentWindow?.document
                    ?.querySelector(`[data-section-id="${s.id}"]`);
                if (!wrapper) return this.reloadPreview(); // mis. section hidden → tidak dirender
                wrapper.outerHTML = data.html; // respons = wrapper lengkap dari _section-shell
            } catch {
                this.reloadPreview();
            }
        },

        reloadPreview() {
            this.$refs.preview.contentWindow.location.reload();
        },

        toastError(title = 'Terjadi kesalahan') {
            Swal.fire({ icon: 'error', title, toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
        },

        async api(method, url, body = null) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: body ? JSON.stringify(body) : null,
            });
            if (!res.ok) {
                throw await res.json().catch(() => ({}));
            }
            return res.json();
        },

        async addSection(type, props = null, parent = null) {
            if (type === 'code') {
                const ok = await Swal.fire({
                    title: 'Tambah section Kode?',
                    text: 'Section ini menyisipkan HTML mentah tanpa validasi. Gunakan hanya bila paham risikonya.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Tambah',
                    cancelButtonText: 'Batal',
                });
                if (!ok.isConfirmed) return;
            }
            try {
                const data = await this.api('POST', `/admin/api/studio/templates/{{ $template->id }}/sections`, {
                    section_type: type,
                    ...(props ? { props } : {}),
                    ...(parent ? { parent_id: parent.parentId, column_index: parent.columnIndex } : {}),
                });
                const created = { ...data.section, id: String(data.section.id), props: data.section.props ?? {} };
                if (parent) {
                    this.children.push(created);
                    this.hasChildren[String(parent.parentId)] = true;
                } else {
                    this.sections.push(created);
                }
                this.addOpen = false;
                this.addChildTarget = null;
                this.selectedId = created.id;
                this.reloadPreview();
            } catch {
                this.toastError('Gagal menambah section');
            }
        },

        openAddChild(parentId, columnIndex) {
            this.addChildTarget = { parentId, columnIndex };
            this.addTab = 'types';
            this.addOpen = true;
        },

        async loadPresets() {
            try {
                this.presets = (await this.api('GET', '/admin/api/studio/presets')).presets;
                this.presetsLoaded = true;
            } catch {
                this.toastError('Gagal memuat presets');
            }
        },

        async savePreset(s) {
            const res = await Swal.fire({
                title: 'Simpan sebagai preset',
                html: '<input id="preset-name" class="swal2-input" placeholder="Nama preset">' +
                    '<input id="preset-category" class="swal2-input" placeholder="Kategori (opsional)">',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const name = document.getElementById('preset-name').value.trim();
                    if (!name) {
                        Swal.showValidationMessage('Nama wajib diisi');
                        return false;
                    }
                    return { name, category: document.getElementById('preset-category').value.trim() || null };
                },
            });
            if (!res.isConfirmed) return;
            try {
                await this.api('POST', '/admin/api/studio/presets', {
                    ...res.value,
                    section_type: s.section_type,
                    props: s.props,
                });
                this.presetsLoaded = false; // fetch ulang saat modal dibuka lagi
                Swal.fire({ icon: 'success', title: 'Preset tersimpan', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
            } catch {
                this.toastError('Gagal menyimpan preset');
            }
        },

        async deletePreset(p) {
            try {
                await this.api('DELETE', `/admin/api/studio/presets/${p.id}`);
                this.presets = this.presets.filter(x => x.id !== p.id);
            } catch {
                this.toastError('Gagal menghapus preset');
            }
        },

        async duplicateSection(s) {
            try {
                const data = await this.api('POST', `/admin/api/studio/sections/${s.id}/duplicate`);
                const index = this.sections.findIndex(x => x.id === s.id);
                this.sections.splice(index + 1, 0, { ...data.section, id: String(data.section.id) });
                this.reloadPreview();
            } catch {
                this.toastError('Gagal menduplikat section');
            }
        },

        async removeSection(s) {
            const confirmed = await Swal.fire({
                title: 'Hapus section?',
                text: `Section ${this.typeLabel(s.section_type)} akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            });
            if (!confirmed.isConfirmed) return;
            try {
                await this.api('DELETE', `/admin/api/templates/sections/${s.id}`);
                this.sections = this.sections.filter(x => x.id !== s.id);
                this.children = this.children.filter(x => x.id !== s.id && String(x.parent_id) !== String(s.id));
                // hasChildren turunan dari this.children — derive ulang, kalau tidak
                // container yang baru saja kehilangan anak terakhirnya tetap dianggap
                // beranak dan swapSection() terus jatuh ke reloadPreview() penuh.
                this.hasChildren = {};
                this.children.forEach(c => { this.hasChildren[String(c.parent_id)] = true; });
                if (this.selectedId === s.id) this.selectedId = null;
                this.reloadPreview();
            } catch {
                this.toastError('Gagal menghapus section');
            }
        },

        async toggleVisible(s) {
            s.is_visible = !s.is_visible;
            try {
                await this.api('PUT', `/admin/api/templates/sections/${s.id}`, { is_visible: s.is_visible });
                this.reloadPreview();
            } catch {
                s.is_visible = !s.is_visible; // revert optimistic flip
                this.toastError('Gagal mengubah visibilitas');
            }
        },

        initSortable() {
            new Sortable(this.$refs.sectionList, {
                handle: '.drag-handle',
                animation: 150,
                draggable: '[data-id]', // hanya pembungkus section top-level
                onEnd: () => this.persistOrder(),
            });
        },

        initColumnSortable(el) {
            if (el.dataset.sortableReady) return; // x-init bisa jalan ulang saat list re-render
            el.dataset.sortableReady = '1';
            new Sortable(el, {
                group: 'studio-columns', // satu grup = boleh seret antar kolom & antar container
                handle: '.drag-handle',
                animation: 150,
                onEnd: () => this.persistColumnOrder(),
            });
        },

        async persistOrder() {
            // Read the new order off the DOM, re-sync the Alpine array to it
            // (keyed x-for then leaves the DOM untouched), persist, re-render.
            const ids = [...this.$refs.sectionList.children]
                .map(el => el.dataset.id)
                .filter(Boolean);
            this.sections = ids.map(id => this.sections.find(s => s.id === id));
            try {
                await this.api('POST', '/admin/api/templates/sections/reorder', {
                    sections: this.sections.map((s, i) => ({ id: s.id, order_index: i })),
                });
                this.reloadPreview();
            } catch {
                this.toastError('Gagal menyimpan urutan');
            }
        },

        async persistColumnOrder() {
            // Baca ulang seluruh kolom dari DOM: satu drag bisa mengubah dua kolom sekaligus.
            const rows = [];
            this.$el.querySelectorAll('[data-parent][data-column]').forEach(col => {
                [...col.querySelectorAll('[data-id]')].forEach((el, i) => {
                    rows.push({
                        id: el.dataset.id,
                        order_index: i,
                        parent_id: col.dataset.parent,
                        column_index: Number(col.dataset.column),
                    });
                });
            });
            if (rows.length === 0) return;

            try {
                await this.api('POST', '/admin/api/templates/sections/reorder', { sections: rows });
                rows.forEach(r => {
                    const child = this.children.find(c => String(c.id) === String(r.id));
                    if (!child) return;
                    child.parent_id = r.parent_id;
                    child.order_index = r.order_index;
                    child.props = { ...child.props, column_index: r.column_index };
                });
                this.reloadPreview();
            } catch {
                this.toastError('Gagal menyimpan urutan kolom');
            }
        },

        setColor(key, value) {
            if (!this.themeSaveTimer) this.pushUndo();
            this.theme.colors[key] = value;
            this.$refs.preview.contentWindow?.document?.documentElement
                ?.style.setProperty(`--color-${key}`, value);
            this.queueThemeSave();
        },

        normalizeHex(v) {
            let s = String(v ?? '').trim();
            if (s && s[0] !== '#') s = '#' + s;
            return /^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/.test(s) ? s : null;
        },

        setFont(key, value) {
            if (!this.themeSaveTimer) this.pushUndo();
            this.theme.fonts[key] = value;
            this.fontsDirty = true;
            this.$refs.preview.contentWindow?.document?.documentElement
                ?.style.setProperty(`--font-${key}`, `'${value}'`);
            this.queueThemeSave();
        },

        setScale(key, value) {
            if (!this.themeSaveTimer) this.pushUndo();
            this.theme.scales[key] = value;
            // type scale (type_base/type_ratio) menurunkan --step-* di server; reload biar akurat.
            // radius/section_spacing/shadow bisa langsung, tapi demi sederhana reload sekali via fontsDirty.
            this.fontsDirty = true;
            this.queueThemeSave();
        },

        queueThemeSave() {
            clearTimeout(this.themeSaveTimer);
            this.themeSaveTimer = setTimeout(async () => {
                this.themeSaveTimer = null;
                try {
                    await this.api('PATCH', `/admin/api/studio/templates/{{ $template->id }}/theme`, this.theme);
                    if (this.fontsDirty) {
                        // Font baru butuh <link> Google Fonts dari templateThemeStyle() — reload sekali.
                        this.fontsDirty = false;
                        this.reloadPreview();
                    }
                } catch {
                    this.toastError('Gagal menyimpan theme');
                }
            }, 600);
        },

        async publish() {
            if (this.status === 'published') {
                const confirmed = await Swal.fire({
                    title: 'Publish ulang?',
                    text: 'Perubahan akan langsung terlihat oleh pembeli yang meng-instantiate template ini.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Publish Ulang',
                    cancelButtonText: 'Batal',
                });
                if (!confirmed.isConfirmed) return;
            }
            this.publishing = true;
            try {
                await this.doPublish(false);
            } finally {
                this.publishing = false;
            }
        },

        async doPublish(force) {
            try {
                await this.api('POST', `/admin/templates/{{ $template->id }}/publish`, force ? { force: true } : null);
                this.status = 'published';
                Swal.fire({ icon: 'success', title: 'Template dipublish', timer: 1500, showConfirmButton: false });
            } catch (err) {
                // 409 → hanya warning: tawarkan publish paksa
                if (err.warnings) {
                    const items = err.warnings.map(w => `<li>${escapeHtml(w)}</li>`).join('');
                    const ok = await Swal.fire({
                        icon: 'warning',
                        title: 'Ada peringatan',
                        html: `<ul class="text-left text-sm list-disc pl-5">${items}</ul>`,
                        showCancelButton: true,
                        confirmButtonText: 'Publish Saja',
                        cancelButtonText: 'Perbaiki Dulu',
                    });
                    if (ok.isConfirmed) await this.doPublish(true);
                    return;
                }
                const items = (err.errors ?? ['Gagal mempublish template.'])
                    .map(e => `<li>${escapeHtml(e)}</li>`).join('');
                Swal.fire({
                    icon: 'error',
                    title: 'Belum bisa dipublish',
                    html: `<ul class="text-left text-sm list-disc pl-5">${items}</ul>`,
                });
            }
        },
    };
}
</script>
@endsection
