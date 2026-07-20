{{-- Panel kanan: Inspector — form digerakkan skema config/invitation_components.php --}}
<div class="w-80 shrink-0 border-l border-[var(--ui-line-2)] bg-[var(--ui-panel)] flex flex-col min-h-0">
    <template x-if="!selected">
        <div class="flex-1 flex flex-col items-center justify-center gap-2 p-5 text-center text-[var(--ui-text-4)]">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
            <p class="text-sm">Pilih section di panel kiri untuk mengedit.</p>
        </div>
    </template>
    <template x-if="selected">
        <div class="flex-1 flex flex-col min-h-0">
            <div class="p-4 border-b border-[var(--ui-line-2)]">
                <h2 class="text-sm font-bold text-[var(--ui-text)] uppercase tracking-wide"
                    x-text="typeLabel(selected.section_type)"></h2>
                <p class="text-xs text-[var(--ui-text-4)] mt-1" x-text="'Section #' + selected.id"></p>
                <div class="flex gap-1 mt-3 rounded-lg bg-[var(--ui-hover)] p-0.5">
                    <template x-for="tab in availableTabs" :key="tab.id">
                        <button @click="inspectorTab = tab.id"
                            class="flex-1 text-xs font-semibold rounded-md px-2 py-1.5"
                            :class="inspectorTab === tab.id ? 'bg-[var(--ui-active)] shadow text-[var(--ui-text)]' : 'text-[var(--ui-text-3)] hover:text-[var(--ui-text)]'"
                            x-text="tab.label"></button>
                    </template>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                {{-- availableTabs bisa kosong (mis. section tanpa field content saat Preview
                     sebagai Customer) — tampilkan status jelas, jangan biarkan panel kosong. --}}
                <p x-show="availableTabs.length === 0"
                    class="rounded-lg bg-[var(--ui-raised)] border border-[var(--ui-line)] px-3 py-2 text-xs text-[var(--ui-text-3)] text-center">
                    Section ini tidak punya field yang bisa diedit customer.
                </p>
                <p x-show="availableTabs.length > 0 && inspectorTab === 'content'"
                    class="rounded-lg bg-[#16243a] border border-[#26405f] px-3 py-2 text-xs text-[#9dc0f0]">
                    Field di tab ini bisa diedit pembeli lewat Customizer.
                </p>
                {{-- Field tanpa panel tampil datar. Field dari blok bersama config
                     (spacing/radius/treatment/ornamen/animasi) punya `panel` dan
                     dikelompokkan ke <details> di bawah — tanpa itu tab Desain satu
                     feature section jadi daftar 15-20 field (guideline §10). --}}
                <template x-for="field in fieldsFor(selected.section_type, inspectorTab).filter(f => !f.panel)"
                    :key="selected.id + ':' + field.key">
                    @include('admin.templates.studio._field')
                </template>

                {{-- <details> asli, bukan state Alpine: buka/tutup, keyboard, dan
                     aksesibilitasnya sudah gratis dari browser. Tertutup by default. --}}
                <template x-for="panel in panelsFor(selected.section_type, inspectorTab)" :key="selected.id + ':panel:' + panel">
                    <details class="rounded-lg border border-[var(--ui-line-2)] overflow-hidden">
                        <summary class="cursor-pointer select-none px-3 py-2 text-xs font-semibold text-[var(--ui-text-2)] bg-[var(--ui-raised)] hover:text-[var(--ui-text)]">
                            <span x-text="panelLabel(panel)"></span>
                        </summary>
                        <div class="p-3 space-y-4 border-t border-[var(--ui-line-2)]">
                            <template x-for="field in fieldsFor(selected.section_type, inspectorTab).filter(f => f.panel === panel)"
                                :key="selected.id + ':' + field.key">
                                @include('admin.templates.studio._field')
                            </template>
                        </div>
                    </details>
                </template>

                {{-- CSS kustom per section (kolom custom_css — discope server via [data-section-id]) --}}
                <template x-if="inspectorTab === 'advanced'">
                    <div>
                        <label class="block text-xs font-medium text-[var(--ui-text-3)] mb-1">CSS Kustom Section</label>
                        <textarea rows="5" spellcheck="false" :value="selected.custom_css ?? ''"
                            @input="setCustomCss($event.target.value)"
                            placeholder="color: red;&#10;.judul { font-size: 2rem; }"
                            class="w-full rounded-lg border-[var(--ui-line-2)] text-xs font-mono px-3 py-2 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]"></textarea>
                        <p class="text-[10px] text-[var(--ui-text-4)] mt-1">Berlaku hanya untuk section ini. Tulis rule CSS polos, tanpa tag.</p>
                        <p x-show="cssError" x-text="cssError" class="text-xs text-[#e08a8a] mt-1"></p>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
