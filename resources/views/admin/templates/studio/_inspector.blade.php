{{-- Panel kanan: Inspector — form digerakkan skema config/invitation_components.php --}}
<div class="w-80 shrink-0 border-l border-gray-200 bg-white flex flex-col min-h-0">
    <template x-if="!selected">
        <div class="flex-1 flex flex-col items-center justify-center gap-2 p-5 text-center text-gray-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
            <p class="text-sm">Pilih section di panel kiri untuk mengedit.</p>
        </div>
    </template>
    <template x-if="selected">
        <div class="flex-1 flex flex-col min-h-0">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide"
                    x-text="typeLabel(selected.section_type)"></h2>
                <p class="text-xs text-gray-400 mt-1" x-text="'Section #' + selected.id"></p>
                <div class="flex gap-1 mt-3 rounded-lg bg-gray-100 p-0.5">
                    <template x-for="tab in availableTabs" :key="tab.id">
                        <button @click="inspectorTab = tab.id"
                            class="flex-1 text-xs font-semibold rounded-md px-2 py-1.5"
                            :class="inspectorTab === tab.id ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-900'"
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
                        <label class="block text-xs font-medium text-gray-500 mb-1" x-text="field.label"></label>

                        {{-- text: textarea khusus key 'content', input untuk sisanya --}}
                        <template x-if="field.type === 'text' && field.key === 'content'">
                            <textarea rows="3" :value="val(field) ?? ''"
                                @input="setProp(field, $event.target.value)"
                                class="w-full rounded-lg border-gray-200 text-sm px-3 py-2 focus:border-black focus:ring-black"></textarea>
                        </template>
                        <template x-if="field.type === 'text' && field.key !== 'content'">
                            <input type="text" :value="val(field) ?? ''"
                                @input="setProp(field, $event.target.value)"
                                class="w-full rounded-lg border-gray-200 text-sm px-3 py-2 focus:border-black focus:ring-black">
                        </template>

                        <template x-if="field.type === 'number'">
                            <input type="number" step="any" :value="val(field) ?? ''"
                                @input="setProp(field, $event.target.value === '' ? null : Number($event.target.value))"
                                class="w-full rounded-lg border-gray-200 text-sm px-3 py-2 focus:border-black focus:ring-black">
                        </template>

                        <template x-if="field.type === 'boolean'">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :checked="val(field)"
                                    @change="setProp(field, $event.target.checked)"
                                    class="rounded border-gray-300 text-black focus:ring-black">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                        </template>

                        <template x-if="field.type === 'select'">
                            <select :value="val(field)" @change="setProp(field, $event.target.value)"
                                class="w-full rounded-lg border-gray-200 text-sm px-3 py-2 focus:border-black focus:ring-black">
                                <template x-for="opt in field.options" :key="opt">
                                    <option :value="opt" x-text="opt" :selected="opt === val(field)"></option>
                                </template>
                            </select>
                        </template>

                        {{-- variant (type khusus): grid kartu — thumbnail asli kalau ada, else skematik (guideline §6/§10) --}}
                        <template x-if="field.type === 'variant'">
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="opt in field.options" :key="opt">
                                    <button type="button" @click="setProp(field, opt)" :title="variantLabel(opt)"
                                        class="flex flex-col items-center gap-1 rounded-lg border p-1.5 transition overflow-hidden"
                                        :class="opt === val(field) ? 'border-black ring-1 ring-black bg-gray-50 text-gray-900' : 'border-gray-200 text-gray-400 hover:border-gray-400 hover:text-gray-600'">
                                        <template x-if="selected.variant_thumbnails && selected.variant_thumbnails[opt]">
                                            <img :src="mediaUrl(selected.variant_thumbnails[opt])" class="w-full h-14 object-cover rounded">
                                        </template>
                                        <template x-if="!(selected.variant_thumbnails && selected.variant_thumbnails[opt])">
                                            <span class="w-full" x-html="variantSchematic(opt)"></span>
                                        </template>
                                        <span class="text-[10px] font-medium leading-tight text-center" x-text="variantLabel(opt)"></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-if="field.type === 'url'">
                            <input type="url" :value="val(field) ?? ''" placeholder="https://…"
                                @change="setProp(field, $event.target.value || null)"
                                class="w-full rounded-lg border-gray-200 text-sm px-3 py-2 focus:border-black focus:ring-black">
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
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-1.5 py-1 flex-1">
                                                <input type="color" :value="val(field)"
                                                    @input="setProp(field, $event.target.value)"
                                                    class="h-7 w-7 shrink-0 rounded cursor-pointer border-0 bg-transparent p-0" title="Pilih warna">
                                                <input type="text" class="w-full text-xs font-mono uppercase border-0 focus:ring-0 p-0"
                                                    maxlength="9" :value="val(field)"
                                                    @change="(() => { const h = normalizeHex($event.target.value); if (h) setProp(field, h); else $event.target.value = val(field); })()">
                                            </div>
                                            <span class="text-[10px] font-semibold uppercase bg-yellow-100 text-yellow-800 rounded px-1.5 py-0.5">override</span>
                                            <button type="button" @click="resetProp(field)" title="Reset ke theme"
                                                class="p-1 rounded text-red-600 hover:bg-red-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                            </button>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="(hex, tk) in theme.colors" :key="tk">
                                                <button type="button" @click="setProp(field, hex)" :title="tk"
                                                    class="w-5 h-5 rounded border border-gray-300 hover:scale-110 transition"
                                                    :style="`background:${hex}`"></button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- color tanpa token: hex + swatch (bukan native telanjang) --}}
                        <template x-if="field.type === 'color' && !field.token">
                            <div class="flex items-center gap-1.5 border border-gray-200 rounded-lg px-1.5 py-1">
                                <input type="color" :value="val(field) ?? '#000000'"
                                    @input="setProp(field, $event.target.value)"
                                    class="h-7 w-7 shrink-0 rounded cursor-pointer border-0 bg-transparent p-0" title="Pilih warna">
                                <input type="text" class="w-full text-xs font-mono uppercase border-0 focus:ring-0 p-0"
                                    maxlength="9" :value="val(field) ?? ''"
                                    @change="(() => { const h = normalizeHex($event.target.value); if (h) setProp(field, h); else $event.target.value = val(field) ?? ''; })()">
                            </div>
                        </template>

                        {{-- image: thumbnail + upload + URL manual --}}
                        <template x-if="field.type === 'image'">
                            <div class="space-y-2">
                                <img x-show="val(field)" :src="mediaUrl(val(field))"
                                    class="w-full h-24 object-cover rounded border border-gray-200">
                                <div class="flex gap-2">
                                    <label class="flex-1 text-center text-xs border border-gray-200 rounded-lg px-2 py-1.5 cursor-pointer hover:border-black">
                                        Upload
                                        <input type="file" accept="image/*" class="hidden" @change="uploadToProp(field, $event)">
                                    </label>
                                    <button type="button" x-show="hasOverride(field.key)" @click="resetProp(field)"
                                        class="text-xs text-red-600 hover:bg-red-50 rounded-lg px-2 py-1.5">Hapus</button>
                                </div>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full rounded-lg border-gray-200 text-xs px-2 py-1.5 focus:border-black focus:ring-black">
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
                                            class="text-gray-400 hover:text-gray-900 disabled:opacity-30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                        </button>
                                        <button type="button" @click="moveListItem(field, i, 1)"
                                            :disabled="i === (val(field) ?? []).length - 1"
                                            class="text-gray-400 hover:text-gray-900 disabled:opacity-30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                        </button>
                                        <button type="button" @click="removeListItem(field, i)"
                                            class="p-1 rounded text-gray-400 hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
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
                                    class="w-full rounded-lg border-gray-200 text-xs font-mono px-3 py-2 focus:border-black focus:ring-black"></textarea>
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
                                    class="inline-flex items-center gap-1 text-xs text-red-600 hover:bg-red-50 rounded px-1.5 py-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tanpa ornamen
                                </button>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full rounded-lg border-gray-200 text-xs px-2 py-1.5 focus:border-black focus:ring-black">
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
                                                    class="hover:text-gray-900 disabled:opacity-30">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                                </button>
                                                <button type="button" @click="moveRepItem(field, i, 1)"
                                                    :disabled="i === (val(field) ?? []).length - 1"
                                                    class="hover:text-gray-900 disabled:opacity-30">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                                </button>
                                                <button type="button" @click="removeRepItem(field, i)"
                                                    class="p-0.5 rounded hover:bg-red-50 hover:text-red-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <template x-for="sub in field.fields" :key="sub.key">
                                            <div>
                                                <label class="block text-[10px] font-medium text-gray-500 mb-0.5" x-text="sub.label"></label>
                                                <template x-if="sub.type === 'text' && ['content', 'story', 'address', 'message'].includes(sub.key)">
                                                    <textarea rows="2" :value="item[sub.key] ?? ''"
                                                        @input="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full rounded-lg border-gray-200 text-xs px-2 py-1 focus:border-black focus:ring-black"></textarea>
                                                </template>
                                                <template x-if="sub.type === 'text' && !['content', 'story', 'address', 'message'].includes(sub.key)">
                                                    <input type="text" :value="item[sub.key] ?? ''"
                                                        @input="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full rounded-lg border-gray-200 text-xs px-2 py-1 focus:border-black focus:ring-black">
                                                </template>
                                                <template x-if="sub.type === 'url'">
                                                    <input type="url" placeholder="https://…" :value="item[sub.key] ?? ''"
                                                        @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full rounded-lg border-gray-200 text-xs px-2 py-1 focus:border-black focus:ring-black">
                                                </template>
                                                <template x-if="sub.type === 'number'">
                                                    <input type="number" step="any" :value="item[sub.key] ?? ''"
                                                        @input="setRepItem(field, i, sub.key, $event.target.value === '' ? null : Number($event.target.value))"
                                                        class="w-full rounded-lg border-gray-200 text-xs px-2 py-1 focus:border-black focus:ring-black">
                                                </template>
                                                <template x-if="sub.type === 'select'">
                                                    <select :value="item[sub.key]" @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full rounded-lg border-gray-200 text-xs px-2 py-1 focus:border-black focus:ring-black">
                                                        <template x-for="opt in sub.options" :key="opt">
                                                            <option :value="opt" x-text="opt" :selected="opt === item[sub.key]"></option>
                                                        </template>
                                                    </select>
                                                </template>
                                                <template x-if="sub.type === 'image'">
                                                    <div class="space-y-1">
                                                        <img x-show="item[sub.key]" :src="mediaUrl(item[sub.key])"
                                                            class="w-full h-16 object-cover rounded border border-gray-200">
                                                        <label class="block text-center text-[10px] border border-gray-200 rounded px-1 py-1 cursor-pointer hover:border-black">
                                                            Upload
                                                            <input type="file" accept="image/*" class="hidden"
                                                                @change="uploadRepImage(field, i, sub.key, $event)">
                                                        </label>
                                                        <input type="text" placeholder="path/URL…" :value="item[sub.key] ?? ''"
                                                            @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                            class="w-full rounded-lg border-gray-200 text-[10px] px-1.5 py-1 focus:border-black focus:ring-black">
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
                                <label class="block text-center text-xs border border-gray-200 rounded-lg px-2 py-1.5 cursor-pointer hover:border-black">
                                    Upload audio
                                    <input type="file" accept="audio/*" class="hidden" @change="uploadToProp(field, $event)">
                                </label>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full rounded-lg border-gray-200 text-xs px-2 py-1.5 focus:border-black focus:ring-black">
                            </div>
                        </template>
                        <template x-if="field.type === 'video'">
                            <div class="space-y-2">
                                <video x-show="val(field)" controls :src="mediaUrl(val(field))" class="w-full rounded border border-gray-200"></video>
                                <label class="block text-center text-xs border border-gray-200 rounded-lg px-2 py-1.5 cursor-pointer hover:border-black">
                                    Upload video
                                    <input type="file" accept="video/*" class="hidden" @change="uploadToProp(field, $event)">
                                </label>
                                <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                    @change="setProp(field, $event.target.value || null)"
                                    class="w-full rounded-lg border-gray-200 text-xs px-2 py-1.5 focus:border-black focus:ring-black">
                            </div>
                        </template>

                        <p x-show="fieldErrors[field.key]" x-text="fieldErrors[field.key]"
                            class="text-xs text-red-600 mt-1"></p>
                    </div>
                </template>

                {{-- CSS kustom per section (kolom custom_css — discope server via [data-section-id]) --}}
                <template x-if="inspectorTab === 'advanced'">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">CSS Kustom Section</label>
                        <textarea rows="5" spellcheck="false" :value="selected.custom_css ?? ''"
                            @input="setCustomCss($event.target.value)"
                            placeholder="color: red;&#10;.judul { font-size: 2rem; }"
                            class="w-full rounded-lg border-gray-200 text-xs font-mono px-3 py-2 focus:border-black focus:ring-black"></textarea>
                        <p class="text-[10px] text-gray-400 mt-1">Berlaku hanya untuk section ini. Tulis rule CSS polos, tanpa tag.</p>
                        <p x-show="cssError" x-text="cssError" class="text-xs text-red-600 mt-1"></p>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>
