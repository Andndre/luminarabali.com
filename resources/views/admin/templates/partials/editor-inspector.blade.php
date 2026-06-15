{{-- Element Inspector Panel (Rightmost Column) --}}
<div x-show="isInspectorOpen"
    class="order-4 flex h-full w-80 shrink-0 flex-col border-l border-gray-200 bg-white font-sans"
    x-cloak>

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 p-4 pb-3">
        <div class="flex items-center gap-2">
            <div
                class="flex h-6 min-w-6 items-center justify-center rounded bg-blue-100 px-2 font-mono text-xs font-bold text-blue-600">
                <span x-text="nodeData.tagName"></span>
            </div>
            <h3 class="text-sm font-semibold text-gray-800">Properties</h3>
        </div>
        <button @click="closeInspector()"
            class="rounded p-1 text-gray-400 transition hover:text-gray-600" title="Close Inspector">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- DOM Breadcrumbs Navigator --}}
    <div class="border-b border-gray-100 bg-gray-50 px-4 pb-3" x-show="breadcrumbs.length > 0">
        <div
            class="flex flex-wrap items-center gap-0.5 rounded border border-gray-200 bg-white p-1.5 font-mono text-[10px] text-gray-500 shadow-sm">
            <template x-for="(crumb, index) in breadcrumbs" :key="index">
                <div class="flex items-center">
                    <span x-show="index > 0" class="mx-0.5 text-gray-300">›</span>
                    <button type="button" @click="selectNode(crumb.node)"
                        class="max-w-[120px] truncate rounded px-1.5 py-0.5 tracking-wide transition"
                        :class="crumb.node === selectedNode ? 'bg-blue-500 text-white font-bold' :
                            'hover:bg-gray-100 hover:text-gray-800'"
                        :title="crumb.tagName + crumb.signature">
                        <span x-text="crumb.tagName"></span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <div class="flex-1 space-y-6 overflow-y-auto p-5 text-left">

        {{-- Inner Text Input --}}
        <div
            x-show="['H1','H2','H3','H4','H5','H6','P','SPAN','A','BUTTON','DIV'].includes(nodeData.tagName)">
            <label
                class="mb-2 block flex justify-between text-xs font-medium uppercase tracking-wider text-gray-500">
                <span>Text Content</span>
                <span x-show="nodeData.isDynamic" class="text-[10px] text-orange-500">Dynamic
                    (Locked)</span>
            </label>
            <textarea x-model="nodeData.text" @input="updateNodeProperty('text', $event.target.value)"
                :disabled="nodeData.isDynamic"
                :class="nodeData.isDynamic ?
                    'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200' :
                    'bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500'"
                rows="3" class="w-full resize-y rounded border p-2 text-sm outline-none transition"></textarea>
        </div>

        {{-- Link URL Input --}}
        <div x-show="nodeData.tagName === 'A'">
            <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Link
                URL</label>
            <input type="text" x-model="nodeData.href"
                @input="updateNodeProperty('href', $event.target.value)" placeholder="https://"
                class="w-full rounded border border-gray-300 bg-white p-2 text-sm outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        {{-- Image Source Input --}}
        <div x-show="nodeData.tagName === 'IMG' || nodeData.classes.includes('bg-[url')">
            <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Image
                Source</label>
            <div class="flex gap-2">
                <input type="text" x-model="nodeData.src"
                    @input="updateNodeProperty('src', $event.target.value)"
                    id="visual_image_src_input" placeholder="/storage/..."
                    class="flex-1 rounded border border-gray-300 bg-white p-2 text-sm outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <button @click="openMediaLibrary('visual')" type="button"
                    class="rounded border border-gray-300 bg-gray-100 px-3 text-gray-600 transition hover:bg-gray-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Text Alignment GUI --}}
        <div>
            <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Text
                Alignment</label>
            <div class="flex rounded border border-gray-200 bg-gray-100 p-1">
                <button
                    @click="toggleTailwindClass('text-left', ['text-center', 'text-right', 'text-justify'])"
                    :class="(nodeData.classes || '').split(' ').includes('text-left') ? 'bg-white shadow text-blue-600' :
                        'text-gray-500 hover:text-gray-700'"
                    class="flex flex-1 justify-center rounded py-1.5 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h10M4 18h16"></path>
                    </svg>
                </button>
                <button
                    @click="toggleTailwindClass('text-center', ['text-left', 'text-right', 'text-justify'])"
                    :class="(nodeData.classes || '').split(' ').includes('text-center') ? 'bg-white shadow text-blue-600' :
                        'text-gray-500 hover:text-gray-700'"
                    class="flex flex-1 justify-center rounded py-1.5 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M7 12h10M4 18h16"></path>
                    </svg>
                </button>
                <button
                    @click="toggleTailwindClass('text-right', ['text-left', 'text-center', 'text-justify'])"
                    :class="(nodeData.classes || '').split(' ').includes('text-right') ? 'bg-white shadow text-blue-600' :
                        'text-gray-500 hover:text-gray-700'"
                    class="flex flex-1 justify-center rounded py-1.5 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M10 12h10M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Font Weight Dropdown --}}
        <div>
            <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Font Weight</label>
            <select
                @change="toggleTailwindClass($event.target.value, ['font-thin', 'font-light', 'font-normal', 'font-medium', 'font-semibold', 'font-bold', 'font-extrabold', 'font-black'])"
                class="w-full rounded border border-gray-300 bg-white p-2 text-sm outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Default (Inherit)</option>
                <option value="font-thin" :selected="(nodeData.classes || '').split(' ').includes('font-thin')">Thin (100)</option>
                <option value="font-light" :selected="(nodeData.classes || '').split(' ').includes('font-light')">Light (300)</option>
                <option value="font-normal" :selected="(nodeData.classes || '').split(' ').includes('font-normal')">Normal (400)</option>
                <option value="font-medium" :selected="(nodeData.classes || '').split(' ').includes('font-medium')">Medium (500)</option>
                <option value="font-semibold" :selected="(nodeData.classes || '').split(' ').includes('font-semibold')">Semi Bold (600)</option>
                <option value="font-bold" :selected="(nodeData.classes || '').split(' ').includes('font-bold')">Bold (700)</option>
                <option value="font-extrabold" :selected="(nodeData.classes || '').split(' ').includes('font-extrabold')">Extra Bold (800)</option>
            </select>
        </div>

        {{-- Color Pickers --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Text Color</label>
                <div class="flex items-center gap-2 rounded border border-gray-300 bg-white p-1">
                    <input type="color" x-model="nodeData.textColor" @input="updateArbitraryColor('text', $event.target.value)"
                        class="h-7 w-8 cursor-pointer rounded border-0 bg-transparent p-0 outline-none">
                    <input type="text" x-model="nodeData.textColor" @change="updateArbitraryColor('text', $event.target.value)"
                        class="w-full border-none bg-transparent p-1 text-xs uppercase outline-none focus:ring-0">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Bg Color</label>
                <div class="flex items-center gap-2 rounded border border-gray-300 bg-white p-1">
                    <input type="color" x-model="nodeData.bgColor" @input="updateArbitraryColor('bg', $event.target.value)"
                        class="h-7 w-8 cursor-pointer rounded border-0 bg-transparent p-0 outline-none">
                    <input type="text" x-model="nodeData.bgColor" @change="updateArbitraryColor('bg', $event.target.value)"
                        class="w-full border-none bg-transparent p-1 text-xs uppercase outline-none focus:ring-0">
                </div>
            </div>
        </div>

        {{-- Spacing and Borders --}}
        <div class="mt-4 border-t border-gray-200 pt-4">
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">Spacing and Borders</label>
            <div class="space-y-4">
                {{-- Padding Grid --}}
                <div>
                    <label class="mb-1 block text-[10px] font-medium uppercase tracking-wider text-gray-400">Padding</label>
                    <div class="grid grid-cols-2 gap-1">
                        @foreach(['t' => 'pt', 'b' => 'pb', 'l' => 'pl', 'r' => 'pr'] as $side => $prefix)
                        <div class="flex items-center gap-1 rounded bg-gray-50 px-2 transition hover:bg-gray-100">
                            <span class="w-3 text-[10px] font-bold text-gray-400">{{ strtoupper($side) }}</span>
                            <select @change="updateDirectionalClass('{{ $prefix }}', $event.target.value)" class="w-full cursor-pointer appearance-none border-none bg-transparent p-1 text-[11px] text-gray-700 outline-none focus:ring-0">
                                <option value="">-</option>
                                <option value="{{ $prefix }}-0" :selected="getActiveBoxClass('p', '{{ $side }}') === '{{ $prefix }}-0'">0</option>
                                <option value="{{ $prefix }}-2" :selected="getActiveBoxClass('p', '{{ $side }}') === '{{ $prefix }}-2'">2</option>
                                <option value="{{ $prefix }}-4" :selected="getActiveBoxClass('p', '{{ $side }}') === '{{ $prefix }}-4'">4</option>
                                <option value="{{ $prefix }}-8" :selected="getActiveBoxClass('p', '{{ $side }}') === '{{ $prefix }}-8'">8</option>
                                <option value="{{ $prefix }}-12" :selected="getActiveBoxClass('p', '{{ $side }}') === '{{ $prefix }}-12'">12</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Margin Grid --}}
                <div>
                    <label class="mb-1 block text-[10px] font-medium uppercase tracking-wider text-gray-400">Margin</label>
                    <div class="grid grid-cols-2 gap-1">
                        @foreach(['t' => 'mt', 'b' => 'mb', 'l' => 'ml', 'r' => 'mr'] as $side => $prefix)
                        <div class="flex items-center gap-1 rounded bg-gray-50 px-2 transition hover:bg-gray-100">
                            <span class="w-3 text-[10px] font-bold text-gray-400">{{ strtoupper($side) }}</span>
                            <select @change="updateDirectionalClass('{{ $prefix }}', $event.target.value)" class="w-full cursor-pointer appearance-none border-none bg-transparent p-1 text-[11px] text-gray-700 outline-none focus:ring-0">
                                <option value="">-</option>
                                <option value="{{ $prefix }}-0" :selected="getActiveBoxClass('m', '{{ $side }}') === '{{ $prefix }}-0'">0</option>
                                <option value="{{ $prefix }}-2" :selected="getActiveBoxClass('m', '{{ $side }}') === '{{ $prefix }}-2'">2</option>
                                <option value="{{ $prefix }}-4" :selected="getActiveBoxClass('m', '{{ $side }}') === '{{ $prefix }}-4'">4</option>
                                <option value="{{ $prefix }}-8" :selected="getActiveBoxClass('m', '{{ $side }}') === '{{ $prefix }}-8'">8</option>
                                <option value="-{{ $prefix }}-4" :selected="getActiveBoxClass('m', '{{ $side }}') === '-{{ $prefix }}-4'">-4</option>
                                <option value="{{ $prefix }}-auto" :selected="getActiveBoxClass('m', '{{ $side }}') === '{{ $prefix }}-auto'">Auto</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Border Radius Grid --}}
                <div>
                    <label class="mb-1 block text-[10px] font-medium uppercase tracking-wider text-gray-400">Radius</label>
                    <div class="grid grid-cols-2 gap-1">
                        @foreach(['tl' => 'rounded-tl', 'tr' => 'rounded-tr', 'bl' => 'rounded-bl', 'br' => 'rounded-br'] as $side => $prefix)
                        <div class="flex items-center gap-1 rounded bg-gray-50 px-2 transition hover:bg-gray-100">
                            <span class="w-3 text-[10px] font-bold text-gray-400">{{ strtoupper($side) }}</span>
                            <select @change="updateDirectionalClass('{{ $prefix }}', $event.target.value)" class="w-full cursor-pointer appearance-none border-none bg-transparent p-1 text-[11px] text-gray-700 outline-none focus:ring-0">
                                <option value="">-</option>
                                <option value="{{ $prefix }}-none" :selected="getActiveBoxClass('rounded', '{{ $side }}') === 'rounded-{{ $side }}-none'">Sq</option>
                                <option value="{{ $prefix }}-md" :selected="getActiveBoxClass('rounded', '{{ $side }}') === 'rounded-{{ $side }}-md'">Md</option>
                                <option value="{{ $prefix }}-lg" :selected="getActiveBoxClass('rounded', '{{ $side }}') === 'rounded-{{ $side }}-lg'">Lg</option>
                                <option value="{{ $prefix }}-full" :selected="getActiveBoxClass('rounded', '{{ $side }}') === 'rounded-{{ $side }}-full'">Pill</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Raw Tailwind Classes --}}
        <div class="flex min-h-[150px] flex-1 flex-col mt-4">
            <label
                class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Tailwind
                Classes</label>
            <textarea x-model="nodeData.classes" @input="updateNodeProperty('classes', $event.target.value)"
                class="w-full flex-1 resize-y rounded border border-gray-300 bg-gray-50 p-2 font-mono text-sm outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                spellcheck="false"></textarea>
        </div>

        {{-- Quick Actions --}}
        <div class="mt-2 border-t border-gray-200 pt-4">
            <label
                class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">Quick
                Actions</label>
            <div class="flex gap-2">
                <button type="button" @click.stop.prevent="duplicateSelectedNode()"
                    class="flex flex-1 items-center justify-center gap-1 rounded bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    Duplicate
                </button>

                <button type="button" @click.stop.prevent="deleteSelectedNode()"
                    class="flex flex-1 items-center justify-center gap-1 rounded bg-red-50 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
