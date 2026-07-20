{{-- Satu field inspector. Dipakai dua kali oleh _inspector: sekali untuk field
     tanpa panel (datar di atas), sekali di dalam tiap panel colaps. Diekstrak
     supaya markup ~400 baris ini tidak digandakan. Mengharapkan variabel Alpine
     `field` dari x-for pemanggil. --}}
                <div x-show="showField(field)">
                    <div class="flex items-center gap-1.5 justify-between mb-1">
                        <label class="block text-xs font-medium text-[var(--ui-text-3)]" x-text="field.label"></label>
                        <button x-show="field.group === 'content' && !asCustomer" type="button"
                            @click="toggleLock(field.key)"
                            :title="isLocked(field.key) ? 'Buka kunci — customer bisa mengubah' : 'Kunci — sembunyikan dari customer'"
                            class="shrink-0 p-0.5 rounded"
                            :class="isLocked(field.key) ? 'text-[var(--ui-text)]' : 'text-[var(--ui-text-4)] hover:text-[var(--ui-text-2)]'">
                            <svg x-show="isLocked(field.key)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            <svg x-show="!isLocked(field.key)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </button>
                    </div>

                    {{-- text: textarea khusus key 'content', input untuk sisanya --}}
                    <template x-if="field.type === 'text' && field.key === 'content'">
                        <textarea rows="3" :value="val(field) ?? ''"
                            @input="setProp(field, $event.target.value)"
                            class="w-full rounded-lg border-[var(--ui-line-2)] text-sm px-3 py-2 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]"></textarea>
                    </template>
                    <template x-if="field.type === 'text' && field.key !== 'content'">
                        <input type="text" :value="val(field) ?? ''"
                            @input="setProp(field, $event.target.value)"
                            class="w-full rounded-lg border-[var(--ui-line-2)] text-sm px-3 py-2 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                    </template>

                    <template x-if="field.type === 'number'">
                        <input type="number" step="any" :value="val(field) ?? ''"
                            @input="setProp(field, $event.target.value === '' ? null : Number($event.target.value))"
                            class="w-full rounded-lg border-[var(--ui-line-2)] text-sm px-3 py-2 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                    </template>

                    <template x-if="field.type === 'boolean'">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" :checked="val(field)"
                                @change="setProp(field, $event.target.checked)"
                                class="rounded border-[var(--ui-line-2)] text-[var(--ui-text)] focus:ring-[var(--ui-accent)]">
                            <span class="text-sm text-[var(--ui-text-2)]">Aktif</span>
                        </label>
                    </template>

                    <template x-if="field.type === 'select'">
                        <select :value="val(field)" @change="setProp(field, $event.target.value)"
                            class="w-full rounded-lg border-[var(--ui-line-2)] text-sm px-3 py-2 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                            <template x-for="opt in field.options" :key="opt">
                                <option :value="opt" x-text="opt" :selected="opt === val(field)"></option>
                            </template>
                        </select>
                    </template>

                    {{-- variant (type khusus): grid kartu skematik SVG (guideline §6/§10) --}}
                    <template x-if="field.type === 'variant'">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="opt in field.options" :key="opt">
                                <button type="button" @click="setProp(field, opt)" :title="variantLabel(opt)"
                                    class="flex flex-col items-center gap-1 rounded-lg border p-1.5 transition overflow-hidden"
                                    :class="opt === val(field) ? 'border-[var(--ui-accent)] ring-1 ring-[var(--ui-accent)] bg-[var(--ui-raised)] text-[var(--ui-text)]' : 'border-[var(--ui-line-2)] text-[var(--ui-text-4)] hover:border-[var(--ui-line-3)] hover:text-[var(--ui-text-2)]'">
                                    <span class="w-full" x-html="variantSchematic(opt)"></span>
                                    <span class="text-[10px] font-medium leading-tight text-center" x-text="variantLabel(opt)"></span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="field.type === 'url'">
                        <input type="url" :value="val(field) ?? ''" placeholder="https://…"
                            @change="setProp(field, $event.target.value || null)"
                            class="w-full rounded-lg border-[var(--ui-line-2)] text-sm px-3 py-2 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                    </template>

                    {{-- color dengan token: chip dua mode (Theme ↔ Custom) — spec §4.6 --}}
                    <template x-if="field.type === 'color' && field.token">
                        <div>
                            <template x-if="!hasOverride(field.key)">
                                <button type="button" @click="setProp(field, theme.colors[field.token])"
                                    title="Klik untuk override manual"
                                    class="w-full flex items-center gap-2 border border-[var(--ui-line-2)] rounded-lg px-3 py-2 text-sm text-left hover:border-[var(--ui-accent)]">
                                    <span class="w-5 h-5 rounded border border-[var(--ui-line-2)] shrink-0"
                                        :style="`background:${theme.colors[field.token]}`"></span>
                                    <span class="flex-1 text-[var(--ui-text-2)]">Theme — <span class="capitalize" x-text="field.token"></span></span>
                                    <span class="text-xs text-[var(--ui-text-4)]">ubah…</span>
                                </button>
                            </template>
                            <template x-if="hasOverride(field.key)">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex items-center gap-1.5 border border-[var(--ui-line-2)] rounded-lg px-1.5 py-1 flex-1">
                                            <input type="color" :value="val(field)"
                                                @input="setProp(field, $event.target.value)"
                                                class="h-7 w-7 shrink-0 rounded cursor-pointer border-0 bg-transparent p-0" title="Pilih warna">
                                            <input type="text" class="w-full text-xs font-mono uppercase border-0 focus:ring-0 p-0"
                                                maxlength="9" :value="val(field)"
                                                @change="(() => { const h = normalizeHex($event.target.value); if (h) setProp(field, h); else $event.target.value = val(field); })()">
                                        </div>
                                        <span class="text-[10px] font-semibold uppercase bg-[#3a2f12] text-[#e6c96a] rounded px-1.5 py-0.5">override</span>
                                        <button type="button" @click="resetProp(field)" title="Reset ke theme"
                                            class="p-1 rounded text-[#e08a8a] hover:bg-[#2a1618]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                        </button>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="(hex, tk) in theme.colors" :key="tk">
                                            <button type="button" @click="setProp(field, hex)" :title="tk"
                                                class="w-5 h-5 rounded border border-[var(--ui-line-2)] hover:scale-110 transition"
                                                :style="`background:${hex}`"></button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- color tanpa token: hex + swatch (bukan native telanjang) --}}
                    <template x-if="field.type === 'color' && !field.token">
                        <div class="flex items-center gap-1.5 border border-[var(--ui-line-2)] rounded-lg px-1.5 py-1">
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
                                class="w-full h-24 object-cover rounded border border-[var(--ui-line-2)]">
                            <div class="flex gap-2">
                                <label class="flex-1 text-center text-xs border border-[var(--ui-line-2)] rounded-lg px-2 py-1.5 cursor-pointer hover:border-[var(--ui-accent)]">
                                    Upload
                                    <input type="file" accept="image/*" class="hidden" @change="uploadToProp(field, $event)">
                                </label>
                                <button type="button" x-show="hasOverride(field.key)" @click="resetProp(field)"
                                    class="text-xs text-[#e08a8a] hover:bg-[#2a1618] rounded-lg px-2 py-1.5">Hapus</button>
                            </div>
                            <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                @change="setProp(field, $event.target.value || null)"
                                class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1.5 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                        </div>
                    </template>

                    {{-- image_list (gallery.images): item {url, alt} --}}
                    <template x-if="field.type === 'image_list'">
                        <div class="space-y-2">
                            <template x-for="(img, i) in (val(field) ?? [])" :key="i">
                                <div class="flex items-center gap-2">
                                    <img :src="img.url" class="w-10 h-10 object-cover rounded border border-[var(--ui-line-2)] shrink-0">
                                    <span class="flex-1 text-xs text-[var(--ui-text-3)] truncate" x-text="img.alt || img.url"></span>
                                    <button type="button" @click="moveListItem(field, i, -1)" :disabled="i === 0"
                                        class="text-[var(--ui-text-4)] hover:text-[var(--ui-text)] disabled:opacity-30">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                    </button>
                                    <button type="button" @click="moveListItem(field, i, 1)"
                                        :disabled="i === (val(field) ?? []).length - 1"
                                        class="text-[var(--ui-text-4)] hover:text-[var(--ui-text)] disabled:opacity-30">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                    </button>
                                    <button type="button" @click="removeListItem(field, i)"
                                        class="p-1 rounded text-[var(--ui-text-4)] hover:bg-[#2a1618] hover:text-[#e08a8a]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="upload">
                                <div class="border border-[var(--ui-line-2)] rounded-lg px-3 py-2 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs text-[var(--ui-text-3)]">
                                        <span x-text="`Mengupload ${upload.current}/${upload.total}`"></span>
                                        <span class="tabular-nums" x-text="upload.percent + '%'"></span>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-[var(--ui-line)] overflow-hidden">
                                        <div class="h-full bg-[var(--ui-accent)] rounded-full transition-all duration-150"
                                            :style="`width: ${upload.percent}%`"></div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!upload">
                                <label x-data="{ over: false }"
                                    @dragover.prevent="over = true" @dragleave.prevent="over = false"
                                    @drop.prevent="over = false; uploadFilesToList(field, $event.dataTransfer.files)"
                                    :class="over ? 'border-[var(--ui-accent)] bg-[var(--ui-raised)]' : 'border-[var(--ui-line-2)]'"
                                    class="block text-center text-xs border border-dashed rounded-lg px-2 py-3 cursor-pointer hover:border-[var(--ui-accent)] transition">
                                    <span x-text="over ? 'Lepas untuk upload' : 'Tarik foto ke sini atau klik (bisa beberapa)'"></span>
                                    <input type="file" accept="image/*" multiple class="hidden" @change="appendListItem(field, $event)">
                                </label>
                            </template>
                        </div>
                    </template>

                    {{-- code: HTML mentah super admin — bypass sistem props (escape hatch level 3) --}}
                    <template x-if="field.type === 'code'">
                        <div class="space-y-2">
                            <div class="rounded-lg bg-[#2a1618] border border-[#4a2226] px-3 py-2 text-xs text-[#f0a5a5]">
                                HTML mentah — hanya super admin, bypass sistem props. Salah tulis bisa merusak tampilan.
                            </div>
                            {{-- Textarea transparan di atas <pre> berwarna: keduanya harus punya
                                 font, ukuran, padding, dan pembungkusan baris yang identik, kalau
                                 tidak kursornya melenceng dari teks yang terlihat. --}}
                            <div class="code-editor">
                                <pre x-ref="hl" aria-hidden="true" class="code-editor-hl" x-html="highlightHtml(val(field) ?? '')"></pre>
                                <textarea rows="12" spellcheck="false" autocapitalize="off" autocomplete="off"
                                    class="code-editor-input" :value="val(field) ?? ''"
                                    @input="setProp(field, $event.target.value)"
                                    @scroll="$refs.hl.scrollTop = $event.target.scrollTop"
                                    @keydown.tab.prevent="insertTab($event, field)"></textarea>
                            </div>
                            {{-- Peringatan heuristik, bukan pengaman: pola di bawah cuma menandai
                                 yang paling umum. Yang menahan HTML mentah ini tetap
                                 authorizeSuperAdmin(), bukan daftar ini. --}}
                            <template x-if="codeWarnings(val(field) ?? '').length">
                                <div class="rounded-lg bg-[#2a2113] border border-[#4d3a17] px-3 py-2 text-xs text-[#e6bd7a]">
                                    <p class="font-semibold">Perlu diperiksa ulang:</p>
                                    <ul class="mt-1 ml-4 list-disc space-y-0.5">
                                        <template x-for="w in codeWarnings(val(field) ?? '')" :key="w">
                                            <li x-text="w"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- ornament_list: daftar item ornamen — tambah/hapus/urut, picker per item, posisi/skala/flip/warna --}}
                    <template x-if="field.type === 'ornament_list'">
                        <div class="space-y-2">
                            <template x-for="(it, i) in ornItems(field)" :key="i">
                                <div class="border border-[var(--ui-line-2)] rounded-lg p-2 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 shrink-0 rounded border border-[var(--ui-line-2)] bg-[var(--ui-raised)] flex items-center justify-center overflow-hidden">
                                            <img x-show="it.src" :src="mediaUrl(it.src)" class="w-full h-full object-contain">
                                        </div>
                                        <button type="button" @click="openOrnamentPickerItem(field.key, i)"
                                            class="flex-1 text-xs border border-[var(--ui-line-2)] rounded-lg px-2 py-1.5 text-left hover:border-[var(--ui-accent)]">
                                            <span x-text="it.src ? 'Ganti…' : 'Pilih ornamen…'"></span>
                                        </button>
                                        <button type="button" @click="moveOrnItem(field, i, -1)" :disabled="i === 0"
                                            class="text-[var(--ui-text-4)] hover:text-[var(--ui-text)] disabled:opacity-30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                        </button>
                                        <button type="button" @click="moveOrnItem(field, i, 1)" :disabled="i === ornItems(field).length - 1"
                                            class="text-[var(--ui-text-4)] hover:text-[var(--ui-text)] disabled:opacity-30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                        </button>
                                        <button type="button" @click="removeOrnItem(field, i)"
                                            class="p-1 rounded text-[var(--ui-text-4)] hover:bg-[#2a1618] hover:text-[#e08a8a]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select :value="it.position" @change="setOrnItem(field, i, 'position', $event.target.value)"
                                            class="rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1">
                                            <template x-for="p in ['left', 'right', 'center', 'full-width']" :key="p">
                                                <option :value="p" x-text="p"></option>
                                            </template>
                                        </select>
                                        <input type="number" min="10" max="300" :value="it.scale" placeholder="Skala %"
                                            @change="setOrnItem(field, i, 'scale', Number($event.target.value))"
                                            class="rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="setOrnItem(field, i, 'flip_h', !it.flip_h)"
                                            class="flex-1 text-xs rounded-lg border px-2 py-1"
                                            :class="it.flip_h ? 'border-[var(--ui-accent)] bg-[var(--ui-accent)] text-[var(--ui-panel)]' : 'border-[var(--ui-line-2)] text-[var(--ui-text-2)] hover:border-[var(--ui-accent)]'">
                                            Flip ↔
                                        </button>
                                        <button type="button" @click="setOrnItem(field, i, 'flip_v', !it.flip_v)"
                                            class="flex-1 text-xs rounded-lg border px-2 py-1"
                                            :class="it.flip_v ? 'border-[var(--ui-accent)] bg-[var(--ui-accent)] text-[var(--ui-panel)]' : 'border-[var(--ui-line-2)] text-[var(--ui-text-2)] hover:border-[var(--ui-accent)]'">
                                            Flip ↕
                                        </button>
                                    </div>
                                    <template x-if="isSvgPath(it.src)">
                                        <div class="flex items-center gap-1.5 border border-[var(--ui-line-2)] rounded-lg px-1.5 py-1">
                                            <span class="text-[10px] text-[var(--ui-text-3)] shrink-0">Warna SVG</span>
                                            <input type="color" :value="it.color || '#000000'"
                                                @input="setOrnItem(field, i, 'color', $event.target.value)"
                                                class="h-6 w-6 shrink-0 rounded cursor-pointer border-0 bg-transparent p-0">
                                            <input type="text" class="w-full text-xs font-mono uppercase border-0 focus:ring-0 p-0"
                                                maxlength="9" :value="it.color ?? ''" placeholder="asli"
                                                @change="(() => { const v = $event.target.value.trim(); if (v === '') { setOrnItem(field, i, 'color', null); return; } const h = normalizeHex(v); if (h) setOrnItem(field, i, 'color', h); else $event.target.value = it.color ?? ''; })()">
                                            <button type="button" x-show="it.color" @click="setOrnItem(field, i, 'color', null)" title="Warna asli"
                                                class="p-1 rounded text-[var(--ui-text-4)] hover:bg-[var(--ui-hover)] shrink-0">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="addOrnItem(field)"
                                class="w-full text-center text-xs border border-dashed border-[var(--ui-line-2)] rounded-lg px-2 py-1.5 text-[var(--ui-text-2)] hover:border-[var(--ui-accent)] hover:text-[var(--ui-text)]">
                                + Tambah ornamen
                            </button>
                        </div>
                    </template>

                    {{-- repeater: daftar item terstruktur (events, accounts, stories, …) --}}
                    <template x-if="field.type === 'repeater'">
                        <div class="space-y-3">
                            <template x-for="(item, i) in (val(field) ?? [])" :key="selected.id + ':' + field.key + ':' + i">
                                <div class="border border-[var(--ui-line-2)] rounded-lg p-2 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-semibold text-[var(--ui-text-4)]" x-text="'Item ' + (i + 1)"></span>
                                        <div class="flex items-center gap-1 text-[var(--ui-text-4)]">
                                            <button type="button" @click="moveRepItem(field, i, -1)" :disabled="i === 0"
                                                class="hover:text-[var(--ui-text)] disabled:opacity-30">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
                                            </button>
                                            <button type="button" @click="moveRepItem(field, i, 1)"
                                                :disabled="i === (val(field) ?? []).length - 1"
                                                class="hover:text-[var(--ui-text)] disabled:opacity-30">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                            </button>
                                            <button type="button" @click="removeRepItem(field, i)"
                                                class="p-0.5 rounded hover:bg-[#2a1618] hover:text-[#e08a8a]">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <template x-for="sub in field.fields" :key="sub.key">
                                        <div>
                                            <label class="block text-[10px] font-medium text-[var(--ui-text-3)] mb-0.5" x-text="sub.label"></label>
                                            <template x-if="sub.type === 'text' && ['content', 'story', 'address', 'message'].includes(sub.key)">
                                                <textarea rows="2" :value="item[sub.key] ?? ''"
                                                    @input="setRepItem(field, i, sub.key, $event.target.value)"
                                                    class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]"></textarea>
                                            </template>
                                            <template x-if="sub.type === 'text' && !['content', 'story', 'address', 'message'].includes(sub.key)">
                                                <input type="text" :value="item[sub.key] ?? ''"
                                                    @input="setRepItem(field, i, sub.key, $event.target.value)"
                                                    class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                                            </template>
                                            <template x-if="sub.type === 'url'">
                                                <input type="url" placeholder="https://…" :value="item[sub.key] ?? ''"
                                                    @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                    class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                                            </template>
                                            <template x-if="sub.type === 'number'">
                                                <input type="number" step="any" :value="item[sub.key] ?? ''"
                                                    @input="setRepItem(field, i, sub.key, $event.target.value === '' ? null : Number($event.target.value))"
                                                    class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                                            </template>
                                            <template x-if="sub.type === 'select'">
                                                <select :value="item[sub.key]" @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                    class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                                                    <template x-for="opt in sub.options" :key="opt">
                                                        <option :value="opt" x-text="opt" :selected="opt === item[sub.key]"></option>
                                                    </template>
                                                </select>
                                            </template>
                                            <template x-if="sub.type === 'image'">
                                                <div class="space-y-1">
                                                    <img x-show="item[sub.key]" :src="mediaUrl(item[sub.key])"
                                                        class="w-full h-16 object-cover rounded border border-[var(--ui-line-2)]">
                                                    <label class="block text-center text-[10px] border border-[var(--ui-line-2)] rounded px-1 py-1 cursor-pointer hover:border-[var(--ui-accent)]">
                                                        Upload
                                                        <input type="file" accept="image/*" class="hidden"
                                                            @change="uploadRepImage(field, i, sub.key, $event)">
                                                    </label>
                                                    <input type="text" placeholder="path/URL…" :value="item[sub.key] ?? ''"
                                                        @change="setRepItem(field, i, sub.key, $event.target.value)"
                                                        class="w-full rounded-lg border-[var(--ui-line-2)] text-[10px] px-1.5 py-1 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                                                </div>
                                            </template>
                                            <p x-show="fieldErrors[field.key + '.' + i + '.' + sub.key]"
                                                x-text="fieldErrors[field.key + '.' + i + '.' + sub.key]"
                                                class="text-[10px] text-[#e08a8a] mt-0.5"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <button type="button" @click="addRepItem(field)"
                                class="w-full text-center text-xs border border-dashed border-[var(--ui-line-2)] rounded-lg px-2 py-1.5 text-[var(--ui-text-2)] hover:border-[var(--ui-accent)] hover:text-[var(--ui-text)]">
                                + Tambah item
                            </button>
                        </div>
                    </template>

                    {{-- audio / video: upload + URL + preview player native --}}
                    <template x-if="field.type === 'audio'">
                        <div class="space-y-2">
                            <audio x-show="val(field)" controls :src="mediaUrl(val(field))" class="w-full h-8"></audio>
                            <label class="block text-center text-xs border border-[var(--ui-line-2)] rounded-lg px-2 py-1.5 cursor-pointer hover:border-[var(--ui-accent)]">
                                Upload audio
                                <input type="file" accept="audio/*" class="hidden" @change="uploadToProp(field, $event)">
                            </label>
                            <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                @change="setProp(field, $event.target.value || null)"
                                class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1.5 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                        </div>
                    </template>
                    <template x-if="field.type === 'video'">
                        <div class="space-y-2">
                            <video x-show="val(field)" controls :src="mediaUrl(val(field))" class="w-full rounded border border-[var(--ui-line-2)]"></video>
                            <label class="block text-center text-xs border border-[var(--ui-line-2)] rounded-lg px-2 py-1.5 cursor-pointer hover:border-[var(--ui-accent)]">
                                Upload video
                                <input type="file" accept="video/*" class="hidden" @change="uploadToProp(field, $event)">
                            </label>
                            <input type="text" placeholder="atau path/URL manual…" :value="val(field) ?? ''"
                                @change="setProp(field, $event.target.value || null)"
                                class="w-full rounded-lg border-[var(--ui-line-2)] text-xs px-2 py-1.5 focus:border-[var(--ui-accent)] focus:ring-[var(--ui-accent)]">
                        </div>
                    </template>

                    <p x-show="fieldErrors[field.key]" x-text="fieldErrors[field.key]"
                        class="text-xs text-[#e08a8a] mt-1"></p>
                </div>
