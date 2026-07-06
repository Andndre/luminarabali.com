@extends('layouts.admin')

@section('title', 'Studio — ' . $template->name)

@section('content')
<style>[x-cloak]{display:none!important}</style>
<div x-data="studioApp()" x-init="init()" class="h-[calc(100vh-4rem)] flex">

    {{-- Panel kiri: Struktur --}}
    <div class="w-72 shrink-0 flex flex-col border-r border-gray-200 bg-white">
        <div class="p-4 border-b border-gray-200 space-y-2">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.templates.index') }}" class="text-sm text-gray-500 hover:text-gray-900">&larr; Kembali</a>
                <span class="text-xs font-semibold rounded-full px-2 py-1 capitalize"
                    :class="{
                        draft: 'bg-yellow-100 text-yellow-800',
                        published: 'bg-green-100 text-green-800',
                        archived: 'bg-gray-100 text-gray-800',
                    }[status]"
                    x-text="status"></span>
            </div>
            <h1 class="font-bold text-gray-900 truncate" title="{{ $template->name }}">{{ $template->name }}</h1>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-1" x-ref="sectionList">
            <template x-for="s in sections" :key="s.id">
                <div :data-id="s.id" @click="selectedId = s.id"
                    class="group flex items-center gap-2 rounded-lg border px-3 py-2 cursor-pointer select-none"
                    :class="selectedId === s.id ? 'border-black bg-gray-50' : 'border-gray-200 hover:bg-gray-50'">
                    <span class="drag-handle cursor-grab text-gray-300 group-hover:text-gray-400">⠿</span>
                    <span class="flex-1 text-sm truncate" :class="{ 'opacity-40': !s.is_visible }"
                        x-text="typeLabel(s.section_type)"></span>
                </div>
            </template>
            <p x-show="loaded && sections.length === 0" class="text-sm text-gray-400 px-2 py-4 text-center">
                Belum ada section.
            </p>
        </div>
    </div>

    {{-- Panel tengah: Preview --}}
    <div class="flex-1 bg-gray-100 flex flex-col min-w-0">
        <div class="p-2 flex justify-center gap-1">
            <button @click="device = 'mobile'"
                class="text-sm px-3 py-1.5 rounded-lg"
                :class="device === 'mobile' ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-200'">📱 Mobile</button>
            <button @click="device = 'desktop'"
                class="text-sm px-3 py-1.5 rounded-lg"
                :class="device === 'desktop' ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-200'">🖥 Desktop</button>
        </div>
        <div class="flex-1 px-4 pb-4 flex justify-center min-h-0">
            <iframe x-ref="preview" src="{{ route('admin.templates.studio.preview', $template->id) }}"
                class="h-full rounded-xl border border-gray-300 bg-white transition-all duration-300"
                :class="device === 'mobile' ? 'w-[375px]' : 'w-full'"></iframe>
        </div>
    </div>

    {{-- Panel kanan: Inspector (placeholder — form skema hadir di Fase A2c) --}}
    <div class="w-80 shrink-0 border-l border-gray-200 bg-white p-5 overflow-y-auto">
        <template x-if="!selected">
            <p class="text-sm text-gray-400">Pilih section di panel kiri untuk mengedit.</p>
        </template>
        <template x-if="selected">
            <div>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide"
                    x-text="typeLabel(selected.section_type)"></h2>
                <p class="text-xs text-gray-400 mt-1" x-text="'Section #' + selected.id"></p>
                <p class="text-sm text-gray-500 mt-4">Form properti section hadir di Fase A2c.</p>
            </div>
        </template>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.7/Sortable.min.js"></script>
<script>
function studioApp() {
    return {
        status: @json($template->status),
        sections: [],
        selectedId: null,
        device: 'mobile',
        loaded: false,
        typeLabels: @json($sectionTypes),

        async init() {
            const res = await fetch(`/admin/api/templates/{{ $template->id }}/load`);
            const data = await res.json();
            this.sections = data.sections.filter(s => !s.parent_id);
            this.loaded = true;
        },

        get selected() {
            return this.sections.find(s => s.id === this.selectedId) ?? null;
        },

        typeLabel(type) {
            return this.typeLabels[type] ?? type;
        },

        reloadPreview() {
            this.$refs.preview.contentWindow.location.reload();
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
    };
}
</script>
@endsection
