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
            <div class="flex items-center gap-2">
                <h1 class="flex-1 font-bold text-gray-900 truncate" title="{{ $template->name }}">{{ $template->name }}</h1>
                <button @click="publish()" :disabled="publishing"
                    class="shrink-0 rounded-lg bg-black px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-800 disabled:opacity-50"
                    x-text="publishing ? '…' : (status === 'published' ? 'Publish Ulang' : 'Publish')"></button>
            </div>
            <div class="flex gap-1 pt-1">
                <button @click="panel = 'sections'"
                    class="flex-1 text-xs font-semibold rounded-lg px-2 py-1.5"
                    :class="panel === 'sections' ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    Struktur
                </button>
                <button @click="panel = 'theme'"
                    class="flex-1 text-xs font-semibold rounded-lg px-2 py-1.5"
                    :class="panel === 'theme' ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
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
                        <label class="block">
                            <span class="text-xs text-gray-600 capitalize" x-text="key"></span>
                            <input type="color" :value="theme.colors[key]"
                                @input="setColor(key, $event.target.value)"
                                class="mt-1 w-full h-9 border rounded cursor-pointer">
                        </label>
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
            <p class="text-xs text-gray-400">Perubahan tersimpan otomatis dan langsung terlihat di preview.</p>
        </div>

        <div x-show="panel === 'sections'" class="flex-1 overflow-y-auto p-3 space-y-1" x-ref="sectionList">
            <template x-for="s in sections" :key="s.id">
                <div :data-id="s.id" @click="selectedId = s.id"
                    class="group flex items-center gap-2 rounded-lg border px-3 py-2 cursor-pointer select-none"
                    :class="selectedId === s.id ? 'border-black bg-gray-50' : 'border-gray-200 hover:bg-gray-50'">
                    <span class="drag-handle cursor-grab text-gray-300 group-hover:text-gray-400">⠿</span>
                    <span class="flex-1 text-sm truncate" :class="{ 'opacity-40': !s.is_visible }"
                        x-text="typeLabel(s.section_type)"></span>
                    <div class="hidden group-hover:flex items-center gap-1 text-gray-400">
                        <button @click.stop="toggleVisible(s)" :title="s.is_visible ? 'Sembunyikan' : 'Tampilkan'"
                            class="hover:text-gray-900" x-text="s.is_visible ? '👁' : '🚫'"></button>
                        <button @click.stop="duplicateSection(s)" title="Duplikat" class="hover:text-gray-900">⧉</button>
                        <button @click.stop="removeSection(s)" title="Hapus" class="hover:text-red-600">✕</button>
                    </div>
                </div>
            </template>
            <p x-show="loaded && sections.length === 0" class="text-sm text-gray-400 px-2 py-4 text-center">
                Belum ada section.
            </p>
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
        @click.self="addOpen = false" @keydown.escape.window="addOpen = false">
        <div class="bg-white rounded-2xl w-full max-w-2xl p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Tambah Section</h2>
                <button @click="addOpen = false" class="text-gray-400 hover:text-gray-900">✕</button>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <template x-for="(label, type) in typeLabels" :key="type">
                    <button @click="addSection(type)"
                        class="border border-gray-200 rounded-xl p-4 text-sm text-left hover:border-black hover:bg-gray-50">
                        <span class="font-medium" x-text="label"></span>
                    </button>
                </template>
            </div>
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

    @include('admin.templates.studio._inspector')
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
        panel: 'sections',
        theme: @json($themeBase),
        fonts: @json($fonts),
        themeSaveTimer: null,
        fontsDirty: false,
        publishing: false,
        typeLabels: @json($sectionTypes),
        schema: @json($schema),
        inspectorTab: 'content',
        fieldErrors: {},
        hasChildren: {},
        propSaveTimer: null,

        async init() {
            const res = await fetch(`/admin/api/templates/{{ $template->id }}/load`);
            const data = await res.json();
            this.hasChildren = {};
            data.sections.forEach(s => {
                if (s.parent_id) this.hasChildren[String(s.parent_id)] = true;
            });
            this.sections = data.sections
                .filter(s => !s.parent_id)
                .map(s => ({ ...s, props: s.props ?? {} }));
            this.loaded = true;
            this.initSortable();
            this.$watch('selectedId', () => {
                this.fieldErrors = {};
                this.inspectorTab = this.availableTabs[0]?.id ?? 'design';
            });
        },

        get selected() {
            return this.sections.find(s => s.id === this.selectedId) ?? null;
        },

        typeLabel(type) {
            return this.typeLabels[type] ?? type;
        },

        get availableTabs() {
            if (!this.selected) return [];
            return [
                { id: 'content', label: 'Konten' },
                { id: 'design', label: 'Desain' },
                { id: 'advanced', label: 'Lanjutan' },
            ].filter(t => this.fieldsFor(this.selected.section_type, t.id).length > 0);
        },

        fieldsFor(type, group) {
            return (this.schema[type] ?? []).filter(f => f.group === group);
        },

        val(field) {
            return this.selected?.props?.[field.key] ?? field.default;
        },

        hasOverride(key) {
            const v = this.selected?.props?.[key];
            return v !== undefined && v !== null;
        },

        setProp(field, value) {
            this.selected.props[field.key] = value;
            this.queuePropSave();
        },

        resetProp(field) {
            this.selected.props[field.key] = null; // null = unset di server (kembali ke theme/default)
            this.queuePropSave();
        },

        queuePropSave() {
            clearTimeout(this.propSaveTimer);
            const s = this.selected; // tangkap sekarang — user bisa pindah section saat debounce
            this.propSaveTimer = setTimeout(() => this.saveProps(s), 300);
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
                wrapper.innerHTML = data.html;
            } catch {
                this.reloadPreview();
            }
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

        async addSection(type) {
            const data = await this.api('POST', `/admin/api/studio/templates/{{ $template->id }}/sections`, {
                section_type: type,
            });
            this.sections.push({ ...data.section, id: String(data.section.id) });
            this.addOpen = false;
            this.selectedId = String(data.section.id);
            this.reloadPreview();
        },

        async duplicateSection(s) {
            const data = await this.api('POST', `/admin/api/studio/sections/${s.id}/duplicate`);
            const index = this.sections.findIndex(x => x.id === s.id);
            this.sections.splice(index + 1, 0, { ...data.section, id: String(data.section.id) });
            this.reloadPreview();
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
            await this.api('DELETE', `/admin/api/templates/sections/${s.id}`);
            this.sections = this.sections.filter(x => x.id !== s.id);
            if (this.selectedId === s.id) this.selectedId = null;
            this.reloadPreview();
        },

        async toggleVisible(s) {
            s.is_visible = !s.is_visible;
            await this.api('PUT', `/admin/api/templates/sections/${s.id}`, { is_visible: s.is_visible });
            this.reloadPreview();
        },

        initSortable() {
            new Sortable(this.$refs.sectionList, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: () => this.persistOrder(),
            });
        },

        async persistOrder() {
            // Read the new order off the DOM, re-sync the Alpine array to it
            // (keyed x-for then leaves the DOM untouched), persist, re-render.
            const ids = [...this.$refs.sectionList.querySelectorAll('[data-id]')].map(el => el.dataset.id);
            this.sections = ids.map(id => this.sections.find(s => s.id === id));
            await this.api('POST', '/admin/api/templates/sections/reorder', {
                sections: this.sections.map((s, i) => ({ id: s.id, order_index: i })),
            });
            this.reloadPreview();
        },

        setColor(key, value) {
            this.theme.colors[key] = value;
            this.$refs.preview.contentWindow?.document?.documentElement
                ?.style.setProperty(`--color-${key}`, value);
            this.queueThemeSave();
        },

        setFont(key, value) {
            this.theme.fonts[key] = value;
            this.fontsDirty = true;
            this.$refs.preview.contentWindow?.document?.documentElement
                ?.style.setProperty(`--font-${key}`, `'${value}'`);
            this.queueThemeSave();
        },

        queueThemeSave() {
            clearTimeout(this.themeSaveTimer);
            this.themeSaveTimer = setTimeout(async () => {
                await this.api('PATCH', `/admin/api/studio/templates/{{ $template->id }}/theme`, this.theme);
                if (this.fontsDirty) {
                    // Font baru butuh <link> Google Fonts dari templateThemeStyle() — reload sekali.
                    this.fontsDirty = false;
                    this.reloadPreview();
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
                await this.api('POST', `/admin/templates/{{ $template->id }}/publish`);
                this.status = 'published';
                Swal.fire({ icon: 'success', title: 'Template dipublish', timer: 1500, showConfirmButton: false });
            } catch (err) {
                const items = (err.errors ?? ['Gagal mempublish template.'])
                    .map(e => `<li>${escapeHtml(e)}</li>`).join('');
                Swal.fire({
                    icon: 'error',
                    title: 'Belum bisa dipublish',
                    html: `<ul class="text-left text-sm list-disc pl-5">${items}</ul>`,
                });
            } finally {
                this.publishing = false;
            }
        },
    };
}
</script>
@endsection
