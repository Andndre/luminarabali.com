@extends('layouts.editor')

@section('title', 'Blade Code Editor')



@section('content')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <style>
        /* Custom scrollbar for visual canvas */
        #visual-workspace::-webkit-scrollbar {
            width: 8px;
        }

        #visual-workspace::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #visual-workspace::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 4px;
        }

        #visual-workspace::-webkit-scrollbar-thumb:hover {
            background: #c9a227;
        }

        /* Global custom css from template */
        {!! $template->global_custom_css !!}
    </style>

    <div class="flex h-screen w-full overflow-hidden bg-[#1e1e1e]" x-data="editorApp()">

        <!-- Aside Navigation -->
        <div class="z-50 flex w-14 shrink-0 flex-col items-center border-r border-gray-800 bg-[#1e1e1e] py-4">
            <button type="button" @click="toggleView('visual')"
                :class="panels.visual ? 'text-blue-500 bg-gray-800' : 'text-gray-400 hover:text-white'"
                class="mb-2 rounded-xl p-3 transition" title="Visual Mode">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
            </button>
            <button type="button" @click="toggleView('code')"
                :class="panels.code ? 'text-blue-500 bg-gray-800' : 'text-gray-400 hover:text-white'"
                class="mb-2 rounded-xl p-3 transition" title="Code Mode">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
            </button>
            <button type="button" @click="toggleView('properties')"
                :class="panels.properties ? 'text-blue-500 bg-gray-800' : 'text-gray-400 hover:text-white'"
                class="mb-2 rounded-xl p-3 transition" title="Properties">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
            <button type="button" @click="toggleView('library')"
                :class="panels.library ? 'text-blue-500 bg-gray-800' : 'text-gray-400 hover:text-white'"
                class="mb-2 rounded-xl p-3 transition" title="Library">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
            </button>

            <div class="flex-1"></div>

            <a href="{{ route('admin.templates.index') }}" class="rounded-xl p-3 text-gray-400 transition hover:text-white"
                title="Kembali">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
        </div>

        <!-- Side Panel Component Library -->
        <div x-data="templateLibrary" :class="panels.library ? 'w-[320px] opacity-100' : 'w-0 border-none opacity-0'"
            class="flex shrink-0 flex-col overflow-hidden border-r border-gray-200 bg-white shadow-xl transition-all duration-300 ease-in-out"
            x-cloak>

            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 p-4">
                <div>
                    <h2 class="font-bold text-gray-900">Library</h2>
                    <p class="text-xs text-gray-500">Components & Sections</p>
                </div>
                <button type="button" @click="toggleView('library')"
                    class="rounded p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Filters -->
            <div class="z-10 space-y-3 border-b border-gray-100 bg-white p-4 shadow-sm">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" x-model="search" placeholder="Cari komponen..."
                        class="w-full rounded border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500">
                </div>
                <select x-model="selectedCategory"
                    class="w-full rounded border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    <option value="cover">Cover</option>
                    <option value="hero">Hero</option>
                    <option value="text">Text & Typography</option>
                    <option value="event">Event Details</option>
                    <option value="gallery">Gallery</option>
                    <option value="countdown">Countdown</option>
                    <option value="rsvp">RSVP</option>
                    <option value="section">Full Section</option>
                </select>
            </div>

            <!-- Component List -->
            <div class="relative flex-1 space-y-4 overflow-y-auto bg-gray-50/50 p-4">
                <div x-show="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/80">
                    <svg class="h-6 w-6 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>

                <div id="component-library-list" class="space-y-4">
                    <template x-for="item in filteredComponents" :key="item.id">
                        <div @click="insertComponent(item.id)" :data-id="item.id"
                            class="library-item group cursor-grab overflow-hidden rounded border border-gray-200 bg-white transition hover:border-blue-500 hover:shadow-md">
                            <div class="relative aspect-video bg-gray-100">
                                <template x-if="item.thumbnail">
                                    <img :src="item.thumbnail ? '/' + item.thumbnail : ''" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!item.thumbnail">
                                    <div class="flex h-full w-full items-center justify-center text-gray-400">
                                        <svg class="h-6 w-6 opacity-50" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                </template>

                                <!-- Hover overlay -->
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 backdrop-blur-[1px] transition-opacity group-hover:opacity-100">
                                    <span
                                        class="flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-xs font-medium text-white shadow-sm">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Insert
                                    </span>
                                </div>
                            </div>
                            <div class="p-3">
                                <div class="mb-1.5 flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                                        x-text="item.category"></span>
                                    <span x-show="item.type === 'section'"
                                        class="rounded border border-blue-100 bg-blue-50 px-1.5 py-0.5 text-[9px] text-blue-600">Section</span>
                                </div>
                                <h3 class="text-sm font-medium leading-tight text-gray-900 transition group-hover:text-blue-600"
                                    x-text="item.name"></h3>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="!loading && filteredComponents.length === 0" class="py-10 text-center text-sm text-gray-500">
                    Tidak ada komponen ditemukan.
                </div>
            </div>
        </div>

        <!-- Main Workspace (Code & Visual) -->
        <div class="relative flex h-full min-w-0 flex-1 flex-col transition-all duration-300 ease-in-out">

            <!-- Header Top Bar -->
            <div class="z-10 flex items-center justify-between border-b border-gray-800 bg-[#1e1e1e] p-3 text-gray-300">
                <div class="flex items-center gap-3">
                    <h1 class="text-sm font-medium">{{ $template->name }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openMediaLibrary()"
                        class="flex items-center gap-1 rounded border border-gray-700 bg-[#2d2d2d] px-3 py-1.5 text-xs text-gray-300 transition hover:bg-[#3d3d3d]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Media
                    </button>
                    <button form="editorForm" type="submit" @click="preSaveSync"
                        class="rounded border border-blue-500 bg-blue-600 px-4 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-blue-500">
                        Simpan
                    </button>
                </div>
            </div>

            <!-- Split View Container -->
            <div class="flex flex-1 overflow-hidden relative">
                <!-- CODE MODE -->
                <div x-show="panels.code" class="order-2 flex h-full flex-1 flex-col border-l border-gray-800 bg-[#1e1e1e] min-w-[400px]">
                <!-- Minimal Tabs Navigation -->
                <div class="flex shrink-0 border-b border-gray-800 bg-[#1e1e1e] text-xs text-gray-500">
                    <button type="button" onclick="switchTab('cover')" id="tab-cover"
                        class="border-b-2 border-transparent px-4 py-2 transition hover:text-gray-300">Cover Page</button>
                    <button type="button" onclick="switchTab('html')" id="tab-html"
                        class="border-b-2 border-blue-500 px-4 py-2 text-white transition">Main Content</button>
                    <button type="button" onclick="switchTab('css')" id="tab-css"
                        class="border-b-2 border-transparent px-4 py-2 transition hover:text-gray-300">Global CSS</button>
                </div>

                <!-- Monaco Container -->
                <div id="monaco-container" class="h-full w-full flex-1"></div>
            </div>

                <!-- VISUAL MODE -->
                <div x-show="panels.visual" class="order-1 h-full flex-1 overflow-y-auto bg-gray-100 min-w-[350px]" id="visual-workspace">
                <div class="relative mx-auto my-4 min-h-screen max-w-[480px] bg-white font-[Lato] shadow-2xl"
                    @mouseleave="hoverMenuVisible = false">
                    <x-invitation.layout class="bg-gray-50" :skip-cover="true">
                        <x-invitation.audio :src="''" />
                        <div x-show="isOpen" class="w-full" style="display:block;">
                            <div id="visual-canvas" class="@container min-h-[500px] w-full"
                                @click="inspectElement($event)" @mousemove.throttle.50ms="trackHover($event)">
                                {!! $template->html_content !!}
                            </div>
                        </div>
                    </x-invitation.layout>

                    <!-- Floating Hover Menu -->
                    <div x-show="hoverMenuVisible"
                        class="pointer-events-none absolute z-40 border-2 border-blue-400 transition-all duration-75 ease-linear"
                        :style="`top: ${hoverMenuPos.top}; left: ${hoverMenuPos.left}; width: ${hoverMenuPos.width}; height: ${hoverMenuPos.height};`"
                        style="display: none;">
                        <div
                            class="pointer-events-auto absolute -left-3 -top-4 z-50 flex gap-1 overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
                            <button @click.stop="moveNodeUp()" class="p-1.5 text-gray-600 transition hover:bg-gray-100"
                                title="Move Up">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                            <div class="w-px bg-gray-200"></div>
                            <button @click.stop="moveNodeDown()" class="p-1.5 text-gray-600 transition hover:bg-gray-100"
                                title="Move Down">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="pointer-events-auto absolute -right-3 -top-3 z-50 flex gap-1">
                            <button @click.stop="duplicateHoveredNode()"
                                class="rounded-full bg-blue-500 p-1.5 text-white shadow-md transition hover:scale-110 hover:bg-blue-600"
                                title="Duplicate Block">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </button>
                            <button @click.stop="deleteHoveredNode()"
                                class="rounded-full bg-red-500 p-1.5 text-white shadow-md transition hover:scale-110 hover:bg-red-600"
                                title="Delete Block">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>

                        <div class="pointer-events-auto absolute -bottom-3 left-1/2 z-50 -translate-x-1/2 transform">
                            <button @click.stop="prepareInsertBelow()"
                                class="rounded-full bg-blue-600 p-1.5 text-white shadow-md transition hover:scale-110 hover:bg-blue-700"
                                title="Add Section Below">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Active Node Inspector (Properties Sidebar) -->
                    <div x-show="isPropertiesPanelOpen" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="fixed bottom-0 right-0 top-0 z-[60] flex w-80 flex-col border-l border-gray-200 bg-white font-sans shadow-2xl"
                        style="display: none;">

                        <!-- Header -->
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

                        <!-- DOM Breadcrumbs Navigator -->
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

                            <!-- Inner Text Input -->
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

                            <!-- Link URL Input -->
                            <div x-show="nodeData.tagName === 'A'">
                                <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Link
                                    URL</label>
                                <input type="text" x-model="nodeData.href"
                                    @input="updateNodeProperty('href', $event.target.value)" placeholder="https://"
                                    class="w-full rounded border border-gray-300 bg-white p-2 text-sm outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>

                            <!-- Image Source Input -->
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

                            <!-- Text Alignment GUI -->
                            <div>
                                <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Text
                                    Alignment</label>
                                <div class="flex rounded border border-gray-200 bg-gray-100 p-1">
                                    <button
                                        @click="toggleTailwindClass('text-left', ['text-center', 'text-right', 'text-justify'])"
                                        :class="nodeData.classes.includes('text-left') ? 'bg-white shadow text-blue-600' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex flex-1 justify-center rounded py-1.5 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h10M4 18h16"></path>
                                        </svg>
                                    </button>
                                    <button
                                        @click="toggleTailwindClass('text-center', ['text-left', 'text-right', 'text-justify'])"
                                        :class="nodeData.classes.includes('text-center') ? 'bg-white shadow text-blue-600' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex flex-1 justify-center rounded py-1.5 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M7 12h10M4 18h16"></path>
                                        </svg>
                                    </button>
                                    <button
                                        @click="toggleTailwindClass('text-right', ['text-left', 'text-center', 'text-justify'])"
                                        :class="nodeData.classes.includes('text-right') ? 'bg-white shadow text-blue-600' :
                                            'text-gray-500 hover:text-gray-700'"
                                        class="flex flex-1 justify-center rounded py-1.5 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M10 12h10M4 18h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Raw Tailwind Classes -->
                            <div class="flex min-h-[150px] flex-1 flex-col">
                                <label
                                    class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">Tailwind
                                    Classes</label>
                                <textarea x-model="nodeData.classes" @input="updateNodeProperty('classes', $event.target.value)"
                                    class="w-full flex-1 resize-y rounded border border-gray-300 bg-gray-50 p-2 font-mono text-sm outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                    spellcheck="false"></textarea>
                            </div>

                            <!-- Quick Actions -->
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
                </div>
            </div>

            <!-- PROPERTIES MODE -->
            <div x-show="panels.properties" class="order-3 flex h-full w-[400px] shrink-0 flex-col overflow-y-auto border-l border-gray-800 bg-[#1e1e1e]"
                x-data="propertiesForm({{ json_encode($template->meta_data ?: ['bg_music' => '', 'rsvp_enabled' => true]) }})">
                <div class="mx-auto max-w-3xl p-8 text-gray-300">
                    <div class="mb-8 border-b border-gray-800 pb-4">
                        <h2 class="text-xl font-medium text-white">Template Properties</h2>
                        <p class="mt-1 text-sm text-gray-500">Konfigurasi dasar dari template ini.</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Background Music -->
                        <div class="rounded border border-gray-800 bg-[#252526] p-6">
                            <label class="mb-2 block text-sm font-medium text-gray-300">Background Music (MP3 URL)</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="formData.bg_music" @input="updateJson"
                                    id="bg_music_input_field"
                                    class="flex-1 rounded border border-gray-700 bg-[#1e1e1e] px-3 py-2 text-gray-300 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                                    placeholder="https://example.com/audio.mp3">
                                <button type="button" onclick="openMediaLibrary('audio')"
                                    class="rounded border border-gray-700 bg-[#2d2d2d] px-4 py-2 text-sm text-gray-300 transition hover:bg-[#3d3d3d]">Browse</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            </div> <!-- End Split View Container -->

            <form id="editorForm" action="{{ route('api.templates.sections.save') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="template_id" value="{{ $template->id }}">
                <input type="hidden" name="cover_content" id="cover_content_input">
                <input type="hidden" name="html_content" id="html_content_input">
                <input type="hidden" name="global_custom_css" id="global_custom_css_input">
                <input type="hidden" name="meta_data" id="meta_data_input">
            </form>
        </div>

        <!-- Remove backdrop completely -->

        
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Hidden Textareas to safely pass data to Monaco without PHP addslashes issues -->
    <textarea id="raw_cover_content" autocomplete="off" style="display:none;">{{ $template->cover_content ?? '' }}</textarea>
    <textarea id="raw_html_content" autocomplete="off" style="display:none;">{{ $template->html_content ?? '' }}</textarea>
    <textarea id="raw_custom_css" autocomplete="off" style="display:none;">{{ $template->global_custom_css ?? '' }}</textarea>

    <!-- Load Monaco Editor synchronously before usage -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>
    <script>
        window.isSyncing = false;
        window.typingTimer = null;

        window.syncToMonaco = function() {
            if (window.isSyncing) return;
            window.isSyncing = true;
            
            const canvas = document.getElementById('visual-canvas');
            if (!canvas || typeof htmlModel === 'undefined') {
                window.isSyncing = false;
                return;
            }

            const clone = canvas.cloneNode(true);
            const textElements = clone.querySelectorAll('[contenteditable]');
            textElements.forEach(el => {
                el.removeAttribute('contenteditable');
                el.classList.remove('hover:outline', 'hover:outline-1', 'hover:outline-blue-400',
                    'focus:outline-2', 'focus:outline-blue-500', 'transition-all');
                if (el.getAttribute('class') === '') el.removeAttribute('class');
            });

            // Remove visual indicators for dynamic variables
            const dynamicElements = clone.querySelectorAll('[x-text]');
            dynamicElements.forEach(el => {
                el.classList.remove('border-b', 'border-dashed', 'border-blue-400', 'cursor-not-allowed');
                el.removeAttribute('title');
                if (el.getAttribute('class') === '') el.removeAttribute('class');
            });

            // Convert container queries back to standard Tailwind breakpoints (@md: -> md:)
            // and clean up Sortable/Alpine temporary attributes
            const allEls = clone.querySelectorAll('*');
            allEls.forEach(el => {
                el.removeAttribute('draggable');
                if (el.getAttribute('style') === '') {
                    el.removeAttribute('style');
                }

                if (el.className && typeof el.className === 'string') {
                    el.className = el.className.replace(/@(sm|md|lg|xl|2xl):/g, '$1:');
                    el.className = el.className.replace(
                        /\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '').replace(/\s+/g,
                        ' ').trim();

                    if (el.className.trim() === '') {
                        el.removeAttribute('class');
                    }
                }
            });

            // Strip any Sortable classes if they leaked
            const sortables = clone.querySelectorAll('.sortable-ghost, .sortable-chosen, .sortable-drag');
            sortables.forEach(el => {
                el.classList.remove('sortable-ghost', 'sortable-chosen', 'sortable-drag');
                if (el.getAttribute('class') === '') el.removeAttribute('class');
            });

            // Update Monaco Model
            let cleanHTML = clone.innerHTML.trim();
            if (window.globalEditor) {
                const fullRange = htmlModel.getFullModelRange();
                window.globalEditor.executeEdits('visual-canvas', [{
                    range: fullRange,
                    text: cleanHTML
                }]);
            } else {
                htmlModel.setValue(cleanHTML);
            }
            
            setTimeout(() => { window.isSyncing = false; }, 50);
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('editorApp', () => ({
                panels: {
                    library: true,
                    visual: true,
                    code: false,
                    properties: false
                },
                isSyncing: false,
                typingTimer: null,
                toggleView(panelName) {
                    this.panels[panelName] = !this.panels[panelName];
                    if (panelName === 'code' && this.panels.code && window.globalEditor) {
                        setTimeout(() => window.globalEditor.layout(), 350);
                    }
                },
                insertTargetNode: null,
                preSaveSync() {
                    window.syncToMonaco();
                },
                
                // --- INVITATION EDITOR STATE MERGED ---
                // Fake data for rendering x-text variables in Editor
                groom_name: 'Romeo',
                bride_name: 'Juliet',
                event_date: '2026-12-12T08:00:00',

                // Node Inspector State
                isPropertiesPanelOpen: false,
                selectedNode: null,
                nodeData: {
                    tagName: '',
                    text: '',
                    classes: '',
                    href: '',
                    src: '',
                    isDynamic: false
                },

                // Hover Block Control State
                hoveredNode: null,
                hoverMenuVisible: false,
                hoverMenuPos: {
                    top: '0px',
                    left: '0px',
                    width: '0px',
                    height: '0px'
                },
                breadcrumbs: [],

                trackHover(event) {
                    // Ignore if we are dragging
                    if (event.buttons > 0) return;

                    const el = event.target;
                    if (!el || el.id === 'visual-canvas') return;

                    // Find closest block
                    const block = el.closest(
                        'section, header, footer, div.flex, div.grid, div.container, [class*="section"]'
                    );
                    if (!block || block.id === 'visual-canvas') {
                        // Don't instantly hide when hitting gaps, allow menu to persist
                        // until they hover a new block or leave the editor entirely
                        return;
                    }

                    this.hoveredNode = block;

                    // Position relative to max-w-[480px] parent wrapper which is relative
                    this.hoverMenuPos = {
                        top: block.offsetTop + 'px',
                        left: block.offsetLeft + 'px',
                        width: block.offsetWidth + 'px',
                        height: block.offsetHeight + 'px'
                    };

                    this.hoverMenuVisible = true;
                },

                duplicateHoveredNode() {
                    if (this.hoveredNode) {
                        const clone = this.hoveredNode.cloneNode(true);

                        // Remove highlight classes if any child has them (e.g. if selectedNode is inside it)
                        const highlighted = clone.querySelector('.ring-blue-500');
                        if (highlighted) {
                            highlighted.classList.remove('ring-2', 'ring-blue-500', 'ring-inset',
                                'outline-none');
                            let cls = highlighted.getAttribute('class') || '';
                            if (cls.trim() === '') {
                                highlighted.removeAttribute('class');
                            }
                        }
                        if (clone.classList.contains('ring-blue-500')) {
                            clone.classList.remove('ring-2', 'ring-blue-500', 'ring-inset',
                                'outline-none');
                            let cls = clone.getAttribute('class') || '';
                            if (cls.trim() === '') clone.removeAttribute('class');
                        }

                        // Insert as HTML string so Alpine initializes it as a fresh component
                        this.hoveredNode.insertAdjacentHTML('afterend', clone.outerHTML);

                        setTimeout(() => {
                            this.initEditable();
                            window.syncToMonaco();
                        }, 50);
                    }
                },

                deleteHoveredNode() {
                    if (this.hoveredNode) {
                        if (this.selectedNode === this.hoveredNode || this.hoveredNode.contains(this
                                .selectedNode)) {
                            this.closeInspector();
                        }
                        this.hoveredNode.remove();
                        this.hoverMenuVisible = false;
                        this.hoveredNode = null;
                        window.syncToMonaco();
                    }
                },

                prepareInsertBelow() {
                    if (this.hoveredNode) {
                        // Set parent's insertTargetNode
                        this.insertTargetNode = this.hoveredNode;
                        // Open library panel
                        this.togglePanel('library');
                    }
                },

                moveNodeUp() {
                    if (this.hoveredNode && this.hoveredNode.previousElementSibling) {
                        this.hoveredNode.parentNode.insertBefore(this.hoveredNode, this.hoveredNode
                            .previousElementSibling);

                        // Update visual position
                        this.hoverMenuPos = {
                            top: this.hoveredNode.offsetTop + 'px',
                            left: this.hoveredNode.offsetLeft + 'px',
                            width: this.hoveredNode.offsetWidth + 'px',
                            height: this.hoveredNode.offsetHeight + 'px'
                        };
                        window.syncToMonaco();
                    }
                },

                moveNodeDown() {
                    if (this.hoveredNode && this.hoveredNode.nextElementSibling) {
                        this.hoveredNode.parentNode.insertBefore(this.hoveredNode.nextElementSibling,
                            this.hoveredNode);

                        // Update visual position
                        this.hoverMenuPos = {
                            top: this.hoveredNode.offsetTop + 'px',
                            left: this.hoveredNode.offsetLeft + 'px',
                            width: this.hoveredNode.offsetWidth + 'px',
                            height: this.hoveredNode.offsetHeight + 'px'
                        };
                        window.syncToMonaco();
                    }
                },

                inspectElement(event) {
                    // Ignore clicks on the visual-canvas wrapper itself
                    if (event.target.id === 'visual-canvas' || event.target.tagName.toLowerCase() ===
                        'body') return;

                    // Prevent following links during editing
                    const aTag = event.target.closest('a');
                    if (aTag) {
                        event.preventDefault();
                    }

                    // Resolve selection target
                    let targetNode = event.target;

                    // If clicking inside an SVG, select the parent SVG
                    if (targetNode.closest('svg')) {
                        targetNode = targetNode.closest('svg');
                    }

                    // SMART FALLBACK: If clicking a purely structural/empty absolute overlay, bubble up to the closest macro-block
                    if (targetNode.tagName === 'DIV' && !targetNode.isContentEditable) {
                        if ((targetNode.classList.contains('absolute') || targetNode.classList.contains(
                                'fixed')) && targetNode.textContent.trim() === '') {
                            const macro = targetNode.closest(
                                'section, header, footer, [class*="section"]');
                            if (macro) targetNode = macro;
                        }
                    }

                    // Hand over to the unified selection method
                    this.selectNode(targetNode);
                },

                selectNode(node) {
                    if (!node) return;

                    this.removeHighlight();
                    this.selectedNode = node;
                    this.selectedNode.classList.add('ring-2', 'ring-blue-500', 'ring-inset',
                        'outline-none');

                    this.nodeData.tagName = this.selectedNode.tagName.toUpperCase();
                    this.nodeData.isDynamic = this.selectedNode.hasAttribute('x-text') || this
                        .selectedNode.closest('[x-text]') !== null;

                    // Only pull text if it's a relatively simple element (leaf node) to avoid nested HTML text extraction
                    if (!this.nodeData.isDynamic && this.selectedNode.children.length === 0) {
                        this.nodeData.text = this.selectedNode.textContent;
                    } else {
                        this.nodeData.text = ''; // Clear text if it has children or is dynamic
                    }

                    // Clean up classes by removing temporary highlight classes from the string
                    let cleanClasses = this.selectedNode.getAttribute('class') || '';
                    cleanClasses = cleanClasses.replace(
                            /\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '')
                        .replace(/\s+/g, ' ').trim();

                    this.nodeData.classes = cleanClasses;
                    this.nodeData.href = this.selectedNode.getAttribute('href') || '';
                    this.nodeData.src = this.selectedNode.getAttribute('src') || '';

                    this.updateBreadcrumbs();
                    this.isPropertiesPanelOpen = true;
                },

                updateBreadcrumbs() {
                    this.breadcrumbs = [];
                    let current = this.selectedNode;

                    while (current && current.id !== 'visual-canvas' && current.tagName
                    .toLowerCase() !== 'body') {
                        let clsStr = '';
                        let cls = current.getAttribute('class');
                        if (cls) {
                            cls = cls.replace(
                                /\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b|\bis-visible\b/g,
                                '').replace(/\s+/g, ' ').trim();
                            const classes = cls.split(' ').filter(c => c.length > 0).slice(0, 2);
                            if (classes.length > 0) {
                                clsStr = '.' + classes.join('.');
                            }
                        }

                        this.breadcrumbs.unshift({
                            tagName: current.tagName.toLowerCase(),
                            signature: clsStr,
                            node: current
                        });

                        current = current.parentElement;
                    }
                },

                removeHighlight() {
                    if (this.selectedNode) {
                        this.selectedNode.classList.remove('ring-2', 'ring-blue-500', 'ring-inset',
                            'outline-none');
                        let cls = this.selectedNode.getAttribute('class') || '';
                        if (cls.trim() === '') {
                            this.selectedNode.removeAttribute('class');
                        }
                    }
                },

                closeInspector() {
                    this.removeHighlight();
                    this.selectedNode = null;
                    this.isPropertiesPanelOpen = false;
                },

                selectParentNode() {
                    if (!this.selectedNode || !this.selectedNode.parentElement) return;

                    const parent = this.selectedNode.parentElement;
                    if (parent.id === 'visual-canvas' || parent.tagName.toLowerCase() === 'body')
                return;

                    this.selectNode(parent);
                },

                updateNodeProperty(property, value) {
                    if (!this.selectedNode) return;

                    if (property === 'text' && !this.nodeData.isDynamic) {
                        this.selectedNode.textContent = value;
                    } else if (property === 'classes') {
                        // Temporarily remove highlight before applying classes to ensure clean class string is set
                        this.removeHighlight();
                        if (value.trim() === '') {
                            this.selectedNode.removeAttribute('class');
                        } else {
                            this.selectedNode.setAttribute('class', value);
                        }
                        // Re-apply highlight
                        this.selectedNode.classList.add('ring-2', 'ring-blue-500', 'ring-inset',
                            'outline-none');
                    } else if (property === 'href') {
                        if (value) this.selectedNode.setAttribute('href', value);
                        else this.selectedNode.removeAttribute('href');
                    } else if (property === 'src') {
                        if (value) this.selectedNode.setAttribute('src', value);
                        else this.selectedNode.removeAttribute('src');
                    }

                    window.syncToMonaco();
                },

                duplicateSelectedNode() {
                    if (!this.selectedNode) return;

                    try {
                        // Clone the DOM node deeply
                        const clone = this.selectedNode.cloneNode(true);

                        // Remove highlight classes if any child has them (e.g. if selectedNode is inside it)
                        const highlighted = clone.querySelector('.ring-blue-500');
                        if (highlighted) {
                            highlighted.classList.remove('ring-2', 'ring-blue-500', 'ring-inset',
                                'outline-none');
                            let cls = highlighted.getAttribute('class') || '';
                            if (cls.trim() === '') {
                                highlighted.removeAttribute('class');
                            }
                        }
                        if (clone.classList.contains('ring-blue-500')) {
                            clone.classList.remove('ring-2', 'ring-blue-500', 'ring-inset',
                                'outline-none');
                            let cls = clone.getAttribute('class') || '';
                            if (cls.trim() === '') clone.removeAttribute('class');
                        }

                        // Insert as HTML string so Alpine initializes it as a fresh element
                        this.selectedNode.insertAdjacentHTML('afterend', clone.outerHTML);

                        // Re-initialize contenteditable on the clone if needed
                        setTimeout(() => {
                            this.initEditable();
                            window.syncToMonaco();
                        }, 50);
                    } catch (e) {
                        console.error("[DUPLICATE] ERROR CAUGHT:", e);
                    }
                },

                deleteSelectedNode() {
                    if (!this.selectedNode) return;

                    this.selectedNode.remove();
                    this.closeInspector();
                    window.syncToMonaco();
                },

                toggleTailwindClass(classToAdd, classesToRemove = []) {
                    if (!this.selectedNode) return;

                    const classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !==
                        '');

                    // Remove conflicting classes
                    const newClasses = classes.filter(c => !classesToRemove.includes(c));

                    // Toggle logic
                    const idx = newClasses.indexOf(classToAdd);
                    if (idx > -1) {
                        newClasses.splice(idx, 1);
                    } else {
                        newClasses.push(classToAdd);
                    }

                    this.nodeData.classes = newClasses.join(' ');
                    this.updateNodeProperty('classes', this.nodeData.classes);
                },

                init() {
                    const canvas = document.getElementById('visual-canvas');
                    if (canvas) {
                        // Automatically convert standard Tailwind breakpoints to Container Queries (@md, @lg, etc)
                        const els = canvas.querySelectorAll('*');
                        els.forEach(el => {
                            if (el.className && typeof el.className === 'string') {
                                el.className = el.className.replace(/\b(sm|md|lg|xl|2xl):/g,
                                    '@$1:');
                            }
                        });

                        new Sortable(canvas, {
                            group: 'shared',
                            animation: 150,
                            ghostClass: 'bg-blue-50',
                            onEnd: function(evt) {
                                window.syncToMonaco();
                            },
                            onAdd: async function(evt) {
                                if (evt.item.classList.contains('library-item')) {
                                    const id = evt.item.dataset.id;
                                    
                                    // Synchronously clean the dropped element to prevent Alpine from evaluating it
                                    evt.item.innerHTML = '<div class="flex flex-col items-center justify-center gap-2 rounded border border-blue-200 bg-blue-50 p-6 text-sm text-blue-600"><svg class="h-6 w-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuat komponen...</div>';
                                    const attrs = Array.from(evt.item.attributes);
                                    attrs.forEach(attr => {
                                        if (attr.name.startsWith('x-') || attr.name.startsWith('@') || attr.name.startsWith(':')) {
                                            evt.item.removeAttribute(attr.name);
                                        }
                                    });

                                    try {
                                        const response = await fetch(`/admin/api/component-library/${id}`);
                                        const component = await response.json();
                                        
                                        let code = component.code;
                                        if (component.variables && component.variables.length > 0) {
                                            component.variables.forEach(v => {
                                                const regex = new RegExp(`\\{\\{\\s*\\$${v.key}\\s*\\}\\}`, 'g');
                                                code = code.replace(regex, v.default || `[${v.label}]`);
                                            });
                                        }
                                        evt.item.outerHTML = code;
                                        setTimeout(() => {
                                            const editorContainer = document.querySelector('[x-data="editorApp()"]');
                                            if (editorContainer) {
                                                const editorData = Alpine.$data(editorContainer);
                                                if (editorData && typeof editorData.initEditable === 'function') {
                                                    editorData.initEditable();
                                                }
                                            }
                                            window.syncToMonaco();
                                        }, 100);
                                    } catch (e) {
                                        console.error('Failed to load component via drop', e);
                                    }
                                } else {
                                    window.syncToMonaco();
                                }
                            }
                        });
                    }
                    this.initEditable();
                    this.initMediaEditable();
                },
                initEditable() {
                    const textElements = this.$el.querySelectorAll(
                        'h1:not([x-text]), h2:not([x-text]), p:not([x-text]), span:not([x-text])');
                    textElements.forEach(el => {
                        el.setAttribute('contenteditable', 'true');
                        el.classList.add('hover:outline', 'hover:outline-1',
                            'hover:outline-blue-400', 'focus:outline-2',
                            'focus:outline-blue-500', 'transition-all');

                        // Prevent enter from creating new block elements if unwanted
                        el.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                document.execCommand('insertLineBreak');
                            }
                        });

                        // On blur, sync to Monaco
                        el.addEventListener('blur', () => {
                            window.syncToMonaco();
                        });
                    });

                    // Add visual distinction and disable editing for dynamic variables
                    const dynamicElements = this.$el.querySelectorAll('[x-text]');
                    dynamicElements.forEach(el => {
                        el.setAttribute('contenteditable', 'false');
                        el.classList.add('border-b', 'border-dashed', 'border-blue-400',
                            'cursor-not-allowed', 'select-none');
                        el.setAttribute('title', 'Dynamic Variable (Editing Disabled)');
                    });
                },
                initMediaEditable() {
                    const mediaElements = this.$el.querySelectorAll('img, section, div');
                    mediaElements.forEach(el => {
                        const isImg = el.tagName === 'IMG';
                        const hasBg = el.style.backgroundImage !== '';

                        if (isImg || hasBg) {
                            el.addEventListener('dblclick', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                window.currentMediaTarget = el;
                                openMediaLibrary('visual');
                            });

                            // Add hover effect to indicate editability
                            el.classList.add('cursor-pointer', 'transition-opacity');
                            el.setAttribute('title', 'Double-click to change media');
                        }
                    });
                }
            }));

            Alpine.data('propertiesForm', (initialData) => ({
                formData: initialData,
                init() {
                    this.updateJson();
                },
                updateJson() {
                    document.getElementById('meta_data_input').value = JSON.stringify(this.formData);
                }
            }));

            Alpine.data('templateLibrary', () => ({
                components: [],
                search: '',
                selectedCategory: '',
                loading: true,

                init() {
                    this.fetchComponents();
                    setTimeout(() => this.initSortable(), 500);
                },

                initSortable() {
                    const container = document.getElementById('component-library-list');
                    if (container) {
                        new Sortable(container, {
                            group: {
                                name: 'shared',
                                pull: 'clone',
                                put: false
                            },
                            sort: false,
                            animation: 150,
                            ghostClass: 'opacity-50'
                        });
                    }
                },

                async fetchComponents() {
                    this.loading = true;
                    try {
                        const response = await fetch('/admin/api/component-library');
                        this.components = await response.json();
                    } catch (error) {
                        console.error('Failed to fetch components', error);
                    } finally {
                        this.loading = false;
                    }
                },

                get filteredComponents() {
                    return this.components.filter(c => {
                        const matchSearch = c.name.toLowerCase().includes(this.search
                                .toLowerCase()) ||
                            (c.description && c.description.toLowerCase().includes(this
                                .search.toLowerCase()));
                        const matchCategory = this.selectedCategory === '' || c.category ===
                            this.selectedCategory;
                        return matchSearch && matchCategory;
                    });
                },

                async insertComponent(id) {
                    try {
                        const response = await fetch(`/admin/api/component-library/${id}`);
                        const component = await response.json();

                        // Default values for variables
                        let code = component.code;
                        if (component.variables && component.variables.length > 0) {
                            component.variables.forEach(v => {
                                const regex = new RegExp(`\\{\\{\\s*\\$${v.key}\\s*\\}\\}`,
                                    'g');
                                code = code.replace(regex, v.default || `[${v.label}]`);
                            });
                        }

                        const editorAppContainer = document.querySelector('[x-data="editorApp()"]');
                        const editorApp = editorAppContainer ? Alpine.$data(editorAppContainer) : null;
                        if (!editorApp) {
                            console.error('editorApp not found');
                            return;
                        }
                        
                        if (editorApp.mode === 'visual') {
                            const canvas = document.getElementById('visual-canvas');
                            if (canvas) {
                                if (editorApp.insertTargetNode) {
                                    editorApp.insertTargetNode.insertAdjacentHTML('afterend', code);
                                    editorApp.insertTargetNode = null;
                                } else {
                                    canvas.insertAdjacentHTML('beforeend', code);
                                }

                                // Let Alpine initialize bindings on new html
                                // and then attach editable events
                                setTimeout(() => {
                                    const container = document.querySelector(
                                        '[x-data="editorApp()"]');
                                    if (container) {
                                        const editorData = Alpine.$data(container);
                                        if (editorData && typeof editorData.initEditable ===
                                            'function') {
                                            editorData.initEditable();
                                        }
                                    }
                                    window.syncToMonaco();
                                }, 50);
                            }
                        } else {
                            if (!globalEditor) return;

                            // Insert at cursor
                            const position = globalEditor.getPosition();
                            globalEditor.executeEdits("library-insert", [{
                                range: new monaco.Range(position.lineNumber, position
                                    .column, position.lineNumber, position.column),
                                text: code + '\n',
                                forceMoveMarkers: true
                            }]);
                            globalEditor.focus();
                        }

                        // Close panel
                        editorApp.activePanel = null;

                        // Flash notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Component inserted',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Trigger save & refresh if not in visual mode (visual mode uses auto-sync)
                        if (this.mode === 'code' && typeof handleSave === 'function') {
                            handleSave();
                        }

                    } catch (error) {
                        console.error('Failed to insert component', error);
                        Swal.fire('Error', 'Gagal memuat komponen', 'error');
                    }
                }
            }));
        });

        var globalEditor = null;
        var coverModel, htmlModel, cssModel;
        var mediaTarget = 'editor'; // 'editor' or 'audio'

        // Inisialisasi Monaco
        require.config({
            paths: {
                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs'
            }
        });
        require(['vs/editor/editor.main'], function() {

            const rawCover = document.getElementById('raw_cover_content').value;
            const rawHtml = document.getElementById('raw_html_content').value;
            const rawCss = document.getElementById('raw_custom_css').value;

            coverModel = monaco.editor.createModel(rawCover, "html");
            htmlModel = monaco.editor.createModel(rawHtml, "html");
            cssModel = monaco.editor.createModel(rawCss, "css");

            // Initialize Editor
            globalEditor = monaco.editor.create(document.getElementById('monaco-container'), {
                model: htmlModel,
                theme: 'vs-dark',
                automaticLayout: true,
                wordWrap: 'on',
                minimap: {
                    enabled: false
                },
                fontSize: 14,
                lineHeight: 24,
                padding: {
                    top: 16
                }
            });

            // Add ResizeObserver for robust layout updating when flex panels animate
            const resizeObserver = new ResizeObserver(() => {
                if (window.globalEditor) {
                    window.globalEditor.layout();
                }
            });
            resizeObserver.observe(document.getElementById('monaco-container'));

            // Real-Time 2-Way Sync (Code -> Visual)
            htmlModel.onDidChangeContent(() => {
                if (window.isSyncing) return;
                
                clearTimeout(window.typingTimer);
                window.typingTimer = setTimeout(() => {
                    window.isSyncing = true;
                    
                    const rawHTML = htmlModel.getValue();
                    const canvas = document.getElementById('visual-canvas');
                    if (canvas) {
                        canvas.innerHTML = rawHTML;
                        
                        // Re-bind Alpine controls
                        const container = document.querySelector('[x-data="editorApp()"]');
                        if (container && Alpine.$data(container) && typeof Alpine.$data(container).initEditable === 'function') {
                            Alpine.$data(container).initEditable();
                        }
                    }
                    
                    setTimeout(() => { window.isSyncing = false; }, 50);
                }, 500); // 500ms debounce
            });

            function handleSave(e) {
                if (e) e.preventDefault();

                document.getElementById('cover_content_input').value = coverModel.getValue();
                document.getElementById('html_content_input').value = htmlModel.getValue();
                document.getElementById('global_custom_css_input').value = cssModel.getValue();

                const form = document.getElementById('editorForm');
                const formData = new FormData(form);

                const saveBtn = document.querySelector('button[form="editorForm"]');
                const originalText = saveBtn.innerText;
                saveBtn.innerText = 'Menyimpan...';

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        saveBtn.innerText = 'Tersimpan ✓';
                        setTimeout(() => saveBtn.innerText = originalText, 2000);
                    })
                    .catch(err => {
                        console.error(err);
                        saveBtn.innerText = 'Gagal Menyimpan!';
                        setTimeout(() => saveBtn.innerText = originalText, 2000);
                    });
            }

            // Expose globally
            window.handleSave = handleSave;

            // Sync data ke hidden input saat form disubmit
            document.getElementById('editorForm').addEventListener('submit', handleSave);

            // Shortcut Ctrl+S (Global) untuk Save
            window.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    handleSave();
                }
            });

            // Shortcut Ctrl+S (Monaco scope) fallback
            globalEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, function() {
                handleSave();
            });
        });

        // Tab Logic
        window.switchTab = function(tab) {
            document.querySelectorAll('[id^="tab-"]').forEach(el => {
                el.classList.remove('border-blue-500', 'text-white');
                el.classList.add('border-transparent');
            });
            document.getElementById('tab-' + tab).classList.remove('border-transparent');
            document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-white');

            if (tab === 'cover') globalEditor.setModel(coverModel);
            else if (tab === 'html') globalEditor.setModel(htmlModel);
            else if (tab === 'css') globalEditor.setModel(cssModel);
        };

        function openMediaLibrary(target = 'editor') {
            mediaTarget = target;
            const modal = document.getElementById('mediaModal');
            const iframe = document.getElementById('mediaIframe');

            // Cek src aslinya untuk mencegah reload berulang kali
            if (!iframe.getAttribute('src')) {
                iframe.setAttribute('src', "{{ route('admin.assets.index') }}?modal=1");
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeMediaLibrary() {
            const modal = document.getElementById('mediaModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // Opsional: Jika kita ingin iframe juga menutup modal setelah asset dipilih
            // bisa dikirim via postMessage dari dalam iframe.
        }

        function insertMedia(url) {
            if (mediaTarget === 'visual' && window.currentMediaTarget) {
                const el = window.currentMediaTarget;
                if (el.tagName === 'IMG') {
                    el.src = url;
                } else {
                    el.style.backgroundImage = `url('${url}')`;
                }
                // Strip the helper attributes before syncing
                el.removeAttribute('title');

                window.syncToMonaco();
                window.currentMediaTarget = null;
            } else if (mediaTarget === 'editor') {
                if (!globalEditor) return;
                const imgTag = `<img src="${url}" alt="image" class="w-full h-auto">`;

                const position = globalEditor.getPosition();
                globalEditor.executeEdits("media-insert", [{
                    range: new monaco.Range(position.lineNumber, position.column, position.lineNumber, position
                        .column),
                    text: imgTag,
                    forceMoveMarkers: true
                }]);
                globalEditor.focus();
            } else if (mediaTarget === 'audio') {
                const audioField = document.getElementById('bg_music_input_field');
                if (audioField) {
                    audioField.value = url;
                    audioField.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            }

            closeMediaLibrary();
        }
    </script>

    <!-- Modal Media Library -->
    <div id="mediaModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="flex h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b bg-gray-50 p-4">
                <h3 class="font-bold text-gray-800">Media Library</h3>
                <button onclick="closeMediaLibrary()" class="text-gray-500 transition hover:text-red-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="relative flex-1 overflow-hidden">
                <iframe id="mediaIframe" src="" class="h-full w-full border-0"></iframe>
            </div>
        </div>
    </div>

    <script>
        // Listen for asset selection from iframe
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'assetSelected') {
                const asset = event.data.asset;
                const url = '/storage/' + asset.file_path;

                // Re-use insertMedia function from editor-native
                insertMedia(url);
            }
        });
    </script>
@endsection
