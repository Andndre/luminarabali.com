@extends('layouts.admin')

@section('title', 'Customizer — ' . $page->title)

@section('content')
<div x-data="customizerApp()" x-init="init()" class="h-[calc(100vh-4rem)] flex">

    {{-- Panel kiri: form --}}
    <div class="w-96 shrink-0 overflow-y-auto border-r border-gray-200 bg-white p-5 space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.invitations.index') }}" class="text-sm text-gray-500 hover:text-gray-900">&larr; Kembali</a>
            <button @click="save()" :disabled="saving"
                class="bg-black text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-800 disabled:opacity-50">
                <span x-text="saving ? 'Menyimpan…' : 'Simpan'"></span>
            </button>
        </div>

        <template x-if="loaded">
            <div class="space-y-6">
                {{-- Fakta inti --}}
                <section>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Data Acara</h2>
                    <div class="space-y-3">
                        <label class="block">
                            <span class="text-xs text-gray-600">Judul</span>
                            <input type="text" x-model="form.title" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                        </label>
                        <label class="block">
                            <span class="text-xs text-gray-600">Nama Pria</span>
                            <input type="text" x-model="form.groom_name" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                        </label>
                        <label class="block">
                            <span class="text-xs text-gray-600">Nama Wanita</span>
                            <input type="text" x-model="form.bride_name" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                        </label>
                        <label class="block">
                            <span class="text-xs text-gray-600">Tanggal Acara</span>
                            <input type="date" x-model="form.event_date" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                        </label>
                    </div>
                </section>

                {{-- Tema: warna --}}
                <section>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Warna</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="(base, key) in themeBase.colors" :key="key">
                            <label class="block">
                                <span class="text-xs text-gray-600 capitalize" x-text="key"></span>
                                <input type="color" :value="colorValue(key)"
                                    @input="setColor(key, $event.target.value)"
                                    class="mt-1 w-full h-9 border rounded cursor-pointer">
                            </label>
                        </template>
                    </div>
                </section>

                {{-- Tema: font --}}
                <section>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Font</h2>
                    <div class="space-y-3">
                        <template x-for="(base, key) in themeBase.fonts" :key="key">
                            <label class="block">
                                <span class="text-xs text-gray-600 capitalize" x-text="key"></span>
                                <select :value="fontValue(key)" @change="setFont(key, $event.target.value)"
                                    class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                                    <template x-for="font in fonts" :key="font">
                                        <option :value="font" x-text="font"></option>
                                    </template>
                                </select>
                            </label>
                        </template>
                    </div>
                </section>

                {{-- Konten per section --}}
                <section>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Konten</h2>
                    <div class="space-y-4">
                        <template x-for="section in sections" :key="section.id">
                            <div class="border rounded-xl p-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2" x-text="section.section_type"></p>
                                <div class="space-y-2">
                                    <template x-for="field in section.fields" :key="field.key">
                                        <div>
                                            <span class="text-xs text-gray-600" x-text="field.label"></span>
                                            <template x-if="field.type === 'text'">
                                                <input type="text" x-model="field.value"
                                                    class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                                            </template>
                                            <template x-if="field.type === 'image'">
                                                <div class="mt-1 flex items-center gap-2">
                                                    <img x-show="field.value" :src="'/storage/' + field.value"
                                                        class="w-12 h-12 object-cover rounded border">
                                                    <button @click="openPicker(section.id, field.key)"
                                                        class="text-sm border rounded-lg px-3 py-1.5 hover:bg-gray-50">
                                                        Pilih Foto
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>
            </div>
        </template>
    </div>

    {{-- Panel kanan: preview --}}
    <div class="flex-1 bg-gray-100 p-4">
        <iframe x-ref="preview" src="{{ route('admin.invitations.customizer-preview', $page->id) }}"
            class="w-full h-full rounded-xl border bg-white"></iframe>
    </div>

    {{-- Modal picker aset --}}
    <div x-show="pickerOpen" x-cloak
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-8"
        @click.self="pickerOpen = false">
        <div class="bg-white rounded-2xl w-full max-w-5xl h-[80vh] overflow-hidden">
            <iframe x-show="pickerOpen" src="{{ route('admin.assets.index') }}?page_id={{ $page->id }}"
                class="w-full h-full"></iframe>
        </div>
    </div>
</div>

<script>
function customizerApp() {
    return {
        loaded: false,
        saving: false,
        pickerOpen: false,
        pickerTarget: null, // { sectionId, key }
        form: { title: '', groom_name: '', bride_name: '', event_date: '' },
        themeBase: { colors: {}, fonts: {} },
        overrides: { colors: {}, fonts: {} },
        fonts: [],
        sections: [],

        async init() {
            const res = await fetch(`/admin/api/invitations/{{ $page->id }}/customizer`);
            const data = await res.json();
            this.form = data.page;
            this.themeBase = data.theme_base;
            this.overrides = {
                colors: (data.theme_overrides && data.theme_overrides.colors) || {},
                fonts: (data.theme_overrides && data.theme_overrides.fonts) || {},
            };
            this.fonts = data.fonts;
            this.sections = data.sections;
            this.loaded = true;

            // Terima pilihan aset dari picker (verifikasi origin — spec §10).
            window.addEventListener('message', (event) => {
                if (event.origin !== window.location.origin) return;
                if (event.data?.type !== 'assetSelected' || !this.pickerTarget) return;
                const section = this.sections.find(s => s.id === this.pickerTarget.sectionId);
                const field = section?.fields.find(f => f.key === this.pickerTarget.key);
                if (field) field.value = event.data.asset.file_path;
                this.pickerOpen = false;
                this.pickerTarget = null;
            });
        },

        colorValue(key) { return this.overrides.colors[key] || this.themeBase.colors[key]; },
        fontValue(key) { return this.overrides.fonts[key] || this.themeBase.fonts[key]; },

        setColor(key, value) {
            this.overrides.colors[key] = value;
            // Live restyle tanpa round-trip (spec §4).
            this.$refs.preview.contentWindow?.document?.documentElement
                ?.style.setProperty(`--color-${key}`, value);
        },

        setFont(key, value) {
            this.overrides.fonts[key] = value;
            this.$refs.preview.contentWindow?.document?.documentElement
                ?.style.setProperty(`--font-${key}`, `'${value}'`);
        },

        openPicker(sectionId, key) {
            this.pickerTarget = { sectionId, key };
            this.pickerOpen = true;
        },

        async save() {
            this.saving = true;
            try {
                const res = await fetch(`/admin/api/invitations/{{ $page->id }}/customizer`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        ...this.form,
                        theme_overrides: this.overrides,
                        sections: this.sections.map(s => ({
                            id: s.id,
                            props: Object.fromEntries(s.fields.map(f => [f.key, f.value])),
                        })),
                    }),
                });

                if (!res.ok) {
                    const err = await res.json();
                    const firstError = Object.values(err.errors || {})[0]?.[0] || 'Gagal menyimpan.';
                    Swal.fire('Validasi gagal', firstError, 'error');
                    return;
                }

                this.$refs.preview.contentWindow.location.reload();
                Swal.fire({ icon: 'success', title: 'Tersimpan', timer: 1200, showConfirmButton: false });
            } finally {
                this.saving = false;
            }
        },
    };
}
</script>
@endsection
