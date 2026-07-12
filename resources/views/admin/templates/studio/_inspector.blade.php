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

                        {{-- color / media dirender oleh blok Task 5 & 6 --}}

                        <p x-show="fieldErrors[field.key]" x-text="fieldErrors[field.key]"
                            class="text-xs text-red-600 mt-1"></p>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
