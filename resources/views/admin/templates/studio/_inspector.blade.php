{{-- Panel kanan: Inspector — form digerakkan skema config/invitation_components.php --}}
<div class="w-80 shrink-0 border-l border-gray-200 bg-white flex flex-col min-h-0">
    <template x-if="!selected">
        <p class="p-5 text-sm text-gray-400">Pilih section di panel kiri untuk mengedit.</p>
    </template>
    <template x-if="selected">
        <div class="flex-1 flex flex-col min-h-0">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide"
                    x-text="typeLabel(selected.section_type)"></h2>
                <p class="text-xs text-gray-400 mt-1" x-text="'Section #' + selected.id"></p>
                <div class="flex gap-1 mt-3">
                    <template x-for="tab in availableTabs" :key="tab.id">
                        <button @click="inspectorTab = tab.id"
                            class="flex-1 text-xs font-semibold rounded-lg px-2 py-1.5"
                            :class="inspectorTab === tab.id ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            x-text="tab.label"></button>
                    </template>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <template x-for="field in fieldsFor(selected.section_type, inspectorTab)" :key="selected.id + ':' + field.key">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" x-text="field.label"></label>

                        {{-- text: textarea khusus key 'content', input untuk sisanya --}}
                        <template x-if="field.type === 'text' && field.key === 'content'">
                            <textarea rows="3" :value="val(field) ?? ''"
                                @input="setProp(field, $event.target.value)"
                                class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                        </template>
                        <template x-if="field.type === 'text' && field.key !== 'content'">
                            <input type="text" :value="val(field) ?? ''"
                                @input="setProp(field, $event.target.value)"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </template>

                        <template x-if="field.type === 'number'">
                            <input type="number" step="any" :value="val(field) ?? ''"
                                @input="setProp(field, $event.target.value === '' ? null : Number($event.target.value))"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </template>

                        <template x-if="field.type === 'boolean'">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :checked="val(field)"
                                    @change="setProp(field, $event.target.checked)"
                                    class="rounded border-gray-300">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                        </template>

                        <template x-if="field.type === 'select'">
                            <select :value="val(field)" @change="setProp(field, $event.target.value)"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                                <template x-for="opt in field.options" :key="opt">
                                    <option :value="opt" x-text="opt" :selected="opt === val(field)"></option>
                                </template>
                            </select>
                        </template>

                        <template x-if="field.type === 'url'">
                            <input type="url" :value="val(field) ?? ''" placeholder="https://…"
                                @change="setProp(field, $event.target.value || null)"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </template>

                        {{-- color dengan token: chip dua mode (Theme ↔ Custom) — spec §4.6 --}}
                        <template x-if="field.type === 'color' && field.token">
                            <div>
                                <template x-if="!hasOverride(field.key)">
                                    <button type="button" @click="setProp(field, theme.colors[field.token])"
                                        title="Klik untuk override manual"
                                        class="w-full flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 text-sm text-left hover:border-black">
                                        <span class="w-5 h-5 rounded border border-gray-300 shrink-0"
                                            :style="`background:${theme.colors[field.token]}`"></span>
                                        <span class="flex-1 text-gray-700">Theme — <span class="capitalize" x-text="field.token"></span></span>
                                        <span class="text-xs text-gray-400">ubah…</span>
                                    </button>
                                </template>
                                <template x-if="hasOverride(field.key)">
                                    <div class="flex items-center gap-2">
                                        <input type="color" :value="val(field)"
                                            @input="setProp(field, $event.target.value)"
                                            class="h-9 w-14 border rounded cursor-pointer">
                                        <span class="text-[10px] font-semibold uppercase bg-yellow-100 text-yellow-800 rounded px-1.5 py-0.5">override</span>
                                        <button type="button" @click="resetProp(field)" title="Reset ke theme"
                                            class="ml-auto text-gray-400 hover:text-gray-900">⟲</button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- color tanpa token: picker polos (partial-nya tidak punya fallback token) --}}
                        <template x-if="field.type === 'color' && !field.token">
                            <input type="color" :value="val(field) ?? '#000000'"
                                @input="setProp(field, $event.target.value)"
                                class="h-9 w-14 border rounded cursor-pointer">
                        </template>

                        <p x-show="fieldErrors[field.key]" x-text="fieldErrors[field.key]"
                            class="text-xs text-red-600 mt-1"></p>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
