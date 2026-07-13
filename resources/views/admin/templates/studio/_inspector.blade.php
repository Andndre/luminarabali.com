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
                <p x-show="inspectorTab === 'content'"
                    class="rounded-lg bg-blue-50 border border-blue-100 px-3 py-2 text-xs text-blue-700">
                    Field di tab ini bisa diedit pembeli lewat Customizer.
                </p>
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

                        {{-- image: thumbnail + upload + URL manual --}}
                        <template x-if="field.type === 'image'">
                            <div class="space-y-2">
                                <img x-show="val(field)" :src="mediaUrl(val(field))"
                                    class="w-full h-24 object-cover rounded border border-gray-200">
                                <div class="flex gap-2">
                                    <label class="flex-1 text-center text-xs border border-gray-300 rounded-lg px-2 py-1.5 cursor-pointer hover:border-black">
                                        Upload
                                        <input type="file" accept="image/*" class="hidden" @change="uploadToProp(field, $event)">
                                    </label>
                                    <button type="button" x-show="hasOverride(field.key)" @click="resetProp(field)"
                                        class="text-xs text-gray-400 hover:text-red-600">Hapus</button>
                                </div>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full border rounded-lg px-2 py-1.5 text-xs">
                            </div>
                        </template>

                        {{-- image_list (gallery.images): item {url, alt} --}}
                        <template x-if="field.type === 'image_list'">
                            <div class="space-y-2">
                                <template x-for="(img, i) in (val(field) ?? [])" :key="i">
                                    <div class="flex items-center gap-2">
                                        <img :src="img.url" class="w-10 h-10 object-cover rounded border border-gray-200 shrink-0">
                                        <span class="flex-1 text-xs text-gray-500 truncate" x-text="img.alt || img.url"></span>
                                        <button type="button" @click="moveListItem(field, i, -1)" :disabled="i === 0"
                                            class="text-gray-400 hover:text-gray-900 disabled:opacity-30">↑</button>
                                        <button type="button" @click="moveListItem(field, i, 1)"
                                            :disabled="i === (val(field) ?? []).length - 1"
                                            class="text-gray-400 hover:text-gray-900 disabled:opacity-30">↓</button>
                                        <button type="button" @click="removeListItem(field, i)"
                                            class="text-gray-400 hover:text-red-600">✕</button>
                                    </div>
                                </template>
                                <label class="block text-center text-xs border border-dashed border-gray-300 rounded-lg px-2 py-1.5 cursor-pointer hover:border-black">
                                    + Upload foto
                                    <input type="file" accept="image/*" class="hidden" @change="appendListItem(field, $event)">
                                </label>
                            </div>
                        </template>

                        {{-- code: HTML mentah super admin — bypass sistem props (escape hatch level 3) --}}
                        <template x-if="field.type === 'code'">
                            <div class="space-y-2">
                                <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-700">
                                    HTML mentah — hanya super admin, bypass sistem props. Salah tulis bisa merusak tampilan.
                                </div>
                                <textarea rows="10" spellcheck="false" :value="val(field) ?? ''"
                                    @input="setProp(field, $event.target.value)"
                                    class="w-full border rounded-lg px-3 py-2 text-xs font-mono"></textarea>
                            </div>
                        </template>

                        {{-- ornament: grid thumbnail dari media library (collection=ornament) --}}
                        <template x-if="field.type === 'ornament'">
                            <div class="space-y-2">
                                <img x-show="val(field)" :src="mediaUrl(val(field))"
                                    class="w-full h-16 object-contain rounded border border-gray-200 bg-gray-50">
                                <div class="grid grid-cols-4 gap-1" x-show="ornaments.length > 0">
                                    <template x-for="o in ornaments" :key="o.id">
                                        <button type="button" @click="setProp(field, o.file_path)" :title="o.asset_name"
                                            class="border rounded p-0.5 hover:border-black"
                                            :class="val(field) === o.file_path ? 'border-black' : 'border-gray-200'">
                                            <img :src="mediaUrl(o.file_path)" class="w-full h-10 object-contain">
                                        </button>
                                    </template>
                                </div>
                                <button type="button" x-show="hasOverride(field.key)" @click="resetProp(field)"
                                    class="text-xs text-gray-400 hover:text-red-600">✕ Tanpa ornamen</button>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full border rounded-lg px-2 py-1.5 text-xs">
                            </div>
                        </template>

                        {{-- repeater: daftar item terstruktur (events, accounts, stories, …) --}}
                        <template x-if="field.type === 'repeater'">
                            <div class="space-y-3">
                                <template x-for="(item, i) in (val(field) ?? [])" :key="selected.id + ':' + field.key + ':' + i">
                                    <div class="border border-gray-200 rounded-lg p-2 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-semibold text-gray-400" x-text="'Item ' + (i + 1)"></span>
                                            <div class="flex items-center gap-1 text-gray-400">
                                                <button type="button" @click="moveRepItem(field, i, -1)" :disabled="i === 0"
                                                    class="hover:text-gray-900 disabled:opacity-30">↑</button>
                                                <button type="button" @click="moveRepItem(field, i, 1)"
                                                    :disabled="i === (val(field) ?? []).length - 1"
                                                    class="hover:text-gray-900 disabled:opacity-30">↓</button>
                                                <button type="button" @click="removeRepItem(field, i)"
                                                    class="hover:text-red-600">✕</button>
                                            </div>
                                        </div>
                                        <template x-for="sub in field.fields" :key="sub.key">
                                            <div>
                                                <label class="block text-[10px] text-gray-500 mb-0.5" x-text="sub.label"></label>
                                                <template x-if="sub.type === 'text' && ['content', 'story', 'address', 'message'].includes(sub.key)">
                                                    <textarea rows="2" :value="item[sub.key] ?? ''"
                                                        @input="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full border rounded px-2 py-1 text-xs"></textarea>
                                                </template>
                                                <template x-if="sub.type === 'text' && !['content', 'story', 'address', 'message'].includes(sub.key)">
                                                    <input type="text" :value="item[sub.key] ?? ''"
                                                        @input="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full border rounded px-2 py-1 text-xs">
                                                </template>
                                                <template x-if="sub.type === 'url'">
                                                    <input type="url" placeholder="https://…" :value="item[sub.key] ?? ''"
                                                        @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full border rounded px-2 py-1 text-xs">
                                                </template>
                                                <template x-if="sub.type === 'number'">
                                                    <input type="number" step="any" :value="item[sub.key] ?? ''"
                                                        @input="setRepItem(field, i, sub.key, $event.target.value === '' ? null : Number($event.target.value))"
                                                        class="w-full border rounded px-2 py-1 text-xs">
                                                </template>
                                                <template x-if="sub.type === 'select'">
                                                    <select :value="item[sub.key]" @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full border rounded px-2 py-1 text-xs">
                                                        <template x-for="opt in sub.options" :key="opt">
                                                            <option :value="opt" x-text="opt" :selected="opt === item[sub.key]"></option>
                                                        </template>
                                                    </select>
                                                </template>
                                                <template x-if="sub.type === 'image'">
                                                    <div class="space-y-1">
                                                        <img x-show="item[sub.key]" :src="mediaUrl(item[sub.key])"
                                                            class="w-full h-16 object-cover rounded border border-gray-200">
                                                        <label class="block text-center text-[10px] border border-gray-300 rounded px-1 py-1 cursor-pointer hover:border-black">
                                                            Upload
                                                            <input type="file" accept="image/*" class="hidden"
                                                                @change="uploadRepImage(field, i, sub.key, $event)">
                                                        </label>
                                                        <input type="text" placeholder="path/URL…" :value="item[sub.key] ?? ''"
                                                            @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                            class="w-full border rounded px-1.5 py-1 text-[10px]">
                                                    </div>
                                                </template>
                                                <p x-show="fieldErrors[field.key + '.' + i + '.' + sub.key]"
                                                    x-text="fieldErrors[field.key + '.' + i + '.' + sub.key]"
                                                    class="text-[10px] text-red-600 mt-0.5"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <button type="button" @click="addRepItem(field)"
                                    class="w-full text-center text-xs border border-dashed border-gray-300 rounded-lg px-2 py-1.5 text-gray-600 hover:border-black hover:text-black">
                                    + Tambah item
                                </button>
                            </div>
                        </template>

                        {{-- audio / video: upload + URL + preview player native --}}
                        <template x-if="field.type === 'audio'">
                            <div class="space-y-2">
                                <audio x-show="val(field)" controls :src="mediaUrl(val(field))" class="w-full h-8"></audio>
                                <label class="block text-center text-xs border border-gray-300 rounded-lg px-2 py-1.5 cursor-pointer hover:border-black">
                                    Upload audio
                                    <input type="file" accept="audio/*" class="hidden" @change="uploadToProp(field, $event)">
                                </label>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full border rounded-lg px-2 py-1.5 text-xs">
                            </div>
                        </template>
                        <template x-if="field.type === 'video'">
                            <div class="space-y-2">
                                <video x-show="val(field)" controls :src="mediaUrl(val(field))" class="w-full rounded border border-gray-200"></video>
                                <label class="block text-center text-xs border border-gray-300 rounded-lg px-2 py-1.5 cursor-pointer hover:border-black">
                                    Upload video
                                    <input type="file" accept="video/*" class="hidden" @change="uploadToProp(field, $event)">
                                </label>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full border rounded-lg px-2 py-1.5 text-xs">
                            </div>
                        </template>

                        <p x-show="fieldErrors[field.key]" x-text="fieldErrors[field.key]"
                            class="text-xs text-red-600 mt-1"></p>
                    </div>
                </template>

                {{-- CSS kustom per section (kolom custom_css — discope server via [data-section-id]) --}}
                <template x-if="inspectorTab === 'advanced'">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">CSS Kustom Section</label>
                        <textarea rows="5" spellcheck="false" :value="selected.custom_css ?? ''"
                            @input="setCustomCss($event.target.value)"
                            placeholder="color: red;&#10;.judul { font-size: 2rem; }"
                            class="w-full border rounded-lg px-3 py-2 text-xs font-mono"></textarea>
                        <p class="text-[10px] text-gray-400 mt-1">Berlaku hanya untuk section ini. Tulis rule CSS polos, tanpa tag.</p>
                        <p x-show="cssError" x-text="cssError" class="text-xs text-red-600 mt-1"></p>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
