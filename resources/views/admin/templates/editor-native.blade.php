@extends('layouts.editor')

@section('title', 'Blade Code Editor')



@section('content')
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<style>
    /* Custom scrollbar for visual canvas */
    #visual-workspace::-webkit-scrollbar { width: 8px; }
    #visual-workspace::-webkit-scrollbar-track { background: #f1f1f1; }
    #visual-workspace::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 4px; }
    #visual-workspace::-webkit-scrollbar-thumb:hover { background: #c9a227; }
    
    /* Global custom css from template */
    {!! $template->global_custom_css !!}
</style>

<div class="h-screen flex overflow-hidden w-full bg-[#1e1e1e]" x-data="editorApp()">
    
    <!-- Aside Navigation -->
    <div class="w-14 bg-[#1e1e1e] flex flex-col items-center py-4 border-r border-gray-800 z-50 shrink-0">
        <button type="button" @click="mode = 'visual'" :class="mode === 'visual' ? 'text-blue-500 bg-gray-800' : 'text-gray-400 hover:text-white'" class="p-3 rounded-xl transition mb-2" title="Visual Mode">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        </button>
        <button type="button" @click="mode = 'code'" :class="mode === 'code' ? 'text-blue-500 bg-gray-800' : 'text-gray-400 hover:text-white'" class="p-3 rounded-xl transition mb-2" title="Code Mode">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
        </button>
        <button type="button" @click="mode = 'properties'" :class="mode === 'properties' ? 'text-blue-500 bg-gray-800' : 'text-gray-400 hover:text-white'" class="p-3 rounded-xl transition mb-2" title="Properties">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </button>
        <button type="button" @click="drawerOpen = true" class="p-3 text-gray-400 hover:text-white rounded-xl transition" title="Library">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
        </button>

        <div class="flex-1"></div>

        <a href="{{ route('admin.templates.index') }}" class="p-3 text-gray-400 hover:text-white rounded-xl transition" title="Kembali">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
    </div>

    <!-- Main Workspace (Code & Visual) -->
    <div class="flex-1 h-full flex flex-col relative min-w-0">
        
        <!-- Header Top Bar -->
        <div class="p-3 bg-[#1e1e1e] text-gray-300 flex justify-between items-center border-b border-gray-800 z-10">
            <div class="flex items-center gap-3">
                <h1 class="text-sm font-medium">{{ $template->name }} <span class="text-gray-600 font-normal" x-text="'| ' + mode + ' mode'"></span></h1>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openMediaLibrary()" class="bg-[#2d2d2d] hover:bg-[#3d3d3d] px-3 py-1.5 rounded text-xs transition flex items-center gap-1 text-gray-300 border border-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Media
                </button>
                <button form="editorForm" type="submit" @click="preSaveSync" class="bg-blue-600 hover:bg-blue-500 px-4 py-1.5 rounded text-xs font-medium transition text-white shadow-sm border border-blue-500">
                    Simpan
                </button>
            </div>
        </div>

        <!-- CODE MODE -->
        <div x-show="mode === 'code'" class="flex-1 flex flex-col h-full bg-[#1e1e1e]">
            <!-- Minimal Tabs Navigation -->
            <div class="flex border-b border-gray-800 text-xs text-gray-500 bg-[#1e1e1e] shrink-0">
                <button type="button" onclick="switchTab('cover')" id="tab-cover" class="px-4 py-2 border-b-2 border-transparent hover:text-gray-300 transition">Cover Page</button>
                <button type="button" onclick="switchTab('html')" id="tab-html" class="px-4 py-2 border-b-2 border-blue-500 text-white transition">Main Content</button>
                <button type="button" onclick="switchTab('css')" id="tab-css" class="px-4 py-2 border-b-2 border-transparent hover:text-gray-300 transition">Global CSS</button>
            </div>

            <!-- Monaco Container -->
            <div id="monaco-container" class="flex-1 w-full h-full"></div>
        </div>

        <!-- VISUAL MODE -->
        <div x-show="mode === 'visual'" class="flex-1 h-full bg-gray-100 overflow-y-auto" id="visual-workspace">
            <div class="max-w-[480px] mx-auto min-h-screen bg-white shadow-2xl my-4 relative font-[Lato]" x-data="invitationEditor()" @mouseleave="hoverMenuVisible = false">
                <x-invitation.layout class="bg-gray-50" :skip-cover="true">
                    <x-invitation.audio :src="''" />
                    <div x-show="isOpen" class="w-full" style="display:block;">
                        <div id="visual-canvas" class="w-full min-h-[500px] @container" @click="inspectElement($event)" @mousemove.throttle.50ms="trackHover($event)">
                            {!! $template->html_content !!}
                        </div>
                    </div>
                </x-invitation.layout>

                <!-- Floating Hover Menu -->
                <div 
                    x-show="hoverMenuVisible"
                    class="absolute z-40 border-2 border-blue-400 pointer-events-none transition-all duration-75 ease-linear"
                    :style="`top: ${hoverMenuPos.top}; left: ${hoverMenuPos.left}; width: ${hoverMenuPos.width}; height: ${hoverMenuPos.height};`"
                    style="display: none;"
                >
                    <div class="absolute -top-4 -left-3 flex gap-1 pointer-events-auto bg-white border border-gray-200 rounded shadow-sm overflow-hidden z-50">
                        <button @click.stop="moveNodeUp()" class="hover:bg-gray-100 p-1.5 transition text-gray-600" title="Move Up">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        </button>
                        <div class="w-px bg-gray-200"></div>
                        <button @click.stop="moveNodeDown()" class="hover:bg-gray-100 p-1.5 transition text-gray-600" title="Move Down">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>

                    <div class="absolute -top-3 -right-3 flex gap-1 pointer-events-auto z-50">
                        <button @click.stop="deleteHoveredNode()" class="bg-red-500 text-white p-1.5 rounded-full shadow-md hover:bg-red-600 hover:scale-110 transition" title="Delete Block">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>

                    <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 pointer-events-auto z-50">
                        <button @click.stop="prepareInsertBelow()" class="bg-blue-600 text-white p-1.5 rounded-full shadow-md hover:bg-blue-700 hover:scale-110 transition" title="Add Section Below">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Active Node Inspector (Properties Sidebar) -->
                <div x-show="isPropertiesPanelOpen" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="fixed top-0 right-0 bottom-0 w-80 bg-white border-l border-gray-200 shadow-2xl z-[60] flex flex-col font-sans"
                     style="display: none;">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gray-50">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold font-mono">
                                <span x-text="nodeData.tagName"></span>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-800">Properties</h3>
                        </div>
                        <button @click="closeInspector()" class="p-1 text-gray-400 hover:text-gray-600 rounded transition" title="Close Inspector">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-5 space-y-6 text-left">
                        
                        <!-- Inner Text Input -->
                        <div x-show="['H1','H2','H3','H4','H5','H6','P','SPAN','A','BUTTON','DIV'].includes(nodeData.tagName)">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2 flex justify-between">
                                <span>Text Content</span>
                                <span x-show="nodeData.isDynamic" class="text-orange-500 text-[10px]">Dynamic (Locked)</span>
                            </label>
                            <textarea 
                                x-model="nodeData.text" 
                                @input="updateNodeProperty('text', $event.target.value)"
                                :disabled="nodeData.isDynamic"
                                :class="nodeData.isDynamic ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200' : 'bg-white border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500'"
                                rows="3" 
                                class="w-full text-sm border rounded p-2 outline-none transition resize-y"></textarea>
                        </div>

                        <!-- Link URL Input -->
                        <div x-show="nodeData.tagName === 'A'">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Link URL</label>
                            <input type="text" 
                                x-model="nodeData.href" 
                                @input="updateNodeProperty('href', $event.target.value)"
                                placeholder="https://"
                                class="w-full text-sm bg-white border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded p-2 outline-none transition">
                        </div>

                        <!-- Image Source Input -->
                        <div x-show="nodeData.tagName === 'IMG' || nodeData.classes.includes('bg-[url')">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Image Source</label>
                            <div class="flex gap-2">
                                <input type="text" 
                                    x-model="nodeData.src" 
                                    @input="updateNodeProperty('src', $event.target.value)"
                                    id="visual_image_src_input"
                                    placeholder="/storage/..."
                                    class="flex-1 text-sm bg-white border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded p-2 outline-none transition">
                                <button @click="openMediaLibrary('visual')" type="button" class="px-3 bg-gray-100 border border-gray-300 text-gray-600 rounded hover:bg-gray-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Text Alignment GUI -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Text Alignment</label>
                            <div class="flex bg-gray-100 p-1 rounded border border-gray-200">
                                <button @click="toggleTailwindClass('text-left', ['text-center', 'text-right', 'text-justify'])" 
                                        :class="nodeData.classes.includes('text-left') ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 py-1.5 flex justify-center rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"></path></svg>
                                </button>
                                <button @click="toggleTailwindClass('text-center', ['text-left', 'text-right', 'text-justify'])" 
                                        :class="nodeData.classes.includes('text-center') ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 py-1.5 flex justify-center rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M4 18h16"></path></svg>
                                </button>
                                <button @click="toggleTailwindClass('text-right', ['text-left', 'text-center', 'text-justify'])" 
                                        :class="nodeData.classes.includes('text-right') ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                        class="flex-1 py-1.5 flex justify-center rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M4 18h16"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Raw Tailwind Classes -->
                        <div class="flex-1 flex flex-col min-h-[150px]">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Tailwind Classes</label>
                            <textarea 
                                x-model="nodeData.classes" 
                                @input="updateNodeProperty('classes', $event.target.value)"
                                class="w-full flex-1 text-sm font-mono bg-gray-50 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 rounded p-2 outline-none transition resize-y"
                                spellcheck="false"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROPERTIES MODE -->
        <div x-show="mode === 'properties'" class="flex-1 w-full h-full bg-[#1e1e1e] overflow-y-auto"
             x-data="propertiesForm({{ json_encode($template->meta_data ?: ['bg_music' => '', 'rsvp_enabled' => true]) }})">
            <div class="p-8 max-w-3xl mx-auto text-gray-300">
                <div class="mb-8 border-b border-gray-800 pb-4">
                    <h2 class="text-xl font-medium text-white">Template Properties</h2>
                    <p class="text-sm text-gray-500 mt-1">Konfigurasi dasar dari template ini.</p>
                </div>

                <div class="space-y-6">
                    <!-- Background Music -->
                    <div class="bg-[#252526] p-6 rounded border border-gray-800">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Background Music (MP3 URL)</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="formData.bg_music" @input="updateJson" id="bg_music_input_field" class="flex-1 bg-[#1e1e1e] border border-gray-700 rounded text-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm px-3 py-2 outline-none" placeholder="https://example.com/audio.mp3">
                            <button type="button" onclick="openMediaLibrary('audio')" class="px-4 py-2 bg-[#2d2d2d] text-gray-300 rounded hover:bg-[#3d3d3d] text-sm transition border border-gray-700">Browse</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="editorForm" action="{{ route('api.templates.sections.save') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="template_id" value="{{ $template->id }}">
            <input type="hidden" name="cover_content" id="cover_content_input">
            <input type="hidden" name="html_content" id="html_content_input">
            <input type="hidden" name="global_custom_css" id="global_custom_css_input">
            <input type="hidden" name="meta_data" id="meta_data_input">
        </form>
    </div>

    <!-- Backdrop for Drawer -->
    <div 
        x-show="drawerOpen" 
        x-transition.opacity
        @click="drawerOpen = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
        x-cloak>
    </div>

    <!-- Drawer Component Library -->
    <div 
        x-data="templateLibrary"
        x-show="drawerOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-[320px] bg-white shadow-2xl z-50 flex flex-col border-r border-gray-200"
        x-cloak>
        
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div>
                <h2 class="font-bold text-gray-900">Library</h2>
                <p class="text-xs text-gray-500">Components & Sections</p>
            </div>
            <button type="button" @click="drawerOpen = false" class="p-2 text-gray-400 hover:text-red-500 rounded hover:bg-red-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Filters -->
        <div class="p-4 border-b border-gray-100 space-y-3 bg-white shadow-sm z-10">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" x-model="search" placeholder="Cari komponen..." class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 rounded outline-none transition">
            </div>
            <select x-model="selectedCategory" class="w-full py-2 px-3 text-sm bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 rounded outline-none transition">
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
        <div class="flex-1 overflow-y-auto p-4 space-y-4 relative bg-gray-50/50">
            <div x-show="loading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>

            <template x-for="item in filteredComponents" :key="item.id">
                <div @click="insertComponent(item.id)" class="group bg-white border border-gray-200 rounded overflow-hidden cursor-pointer hover:border-blue-500 hover:shadow-md transition">
                    <div class="aspect-video bg-gray-100 relative">
                        <template x-if="item.thumbnail">
                            <img :src="item.thumbnail ? '/' + item.thumbnail : ''" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!item.thumbnail">
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </template>
                        
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[1px]">
                            <span class="bg-blue-600 text-white text-xs font-medium px-4 py-2 rounded shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Insert
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400" x-text="item.category"></span>
                            <span x-show="item.type === 'section'" class="text-[9px] px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded border border-blue-100">Section</span>
                        </div>
                        <h3 class="font-medium text-gray-900 text-sm leading-tight group-hover:text-blue-600 transition" x-text="item.name"></h3>
                    </div>
                </div>
            </template>
            
            <div x-show="!loading && filteredComponents.length === 0" class="text-center py-10 text-gray-500 text-sm">
                Tidak ada komponen ditemukan.
            </div>
        </div>
    </div>
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
    window.syncToMonaco = function() {
        const canvas = document.getElementById('visual-canvas');
        if(!canvas || typeof htmlModel === 'undefined') return;
        
        const clone = canvas.cloneNode(true);
        const textElements = clone.querySelectorAll('[contenteditable]');
        textElements.forEach(el => {
            el.removeAttribute('contenteditable');
            el.classList.remove('hover:outline', 'hover:outline-1', 'hover:outline-blue-400', 'focus:outline-2', 'focus:outline-blue-500', 'transition-all');
            if(el.getAttribute('class') === '') el.removeAttribute('class');
        });
        
        // Remove visual indicators for dynamic variables
        const dynamicElements = clone.querySelectorAll('[x-text]');
        dynamicElements.forEach(el => {
            el.classList.remove('border-b', 'border-dashed', 'border-blue-400', 'cursor-not-allowed');
            el.removeAttribute('title');
            if(el.getAttribute('class') === '') el.removeAttribute('class');
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
                el.className = el.className.replace(/\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '').replace(/\s+/g, ' ').trim();
                
                if (el.className.trim() === '') {
                    el.removeAttribute('class');
                }
            }
        });

        // Strip any Sortable classes if they leaked
        const sortables = clone.querySelectorAll('.sortable-ghost, .sortable-chosen, .sortable-drag');
        sortables.forEach(el => {
            el.classList.remove('sortable-ghost', 'sortable-chosen', 'sortable-drag');
            if(el.getAttribute('class') === '') el.removeAttribute('class');
        });

        // Update Monaco Model
        htmlModel.setValue(clone.innerHTML.trim());
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('editorApp', () => ({
            mode: 'visual',
            drawerOpen: false,
            insertTargetNode: null,
            init() {
                this.$watch('mode', value => {
                    if (value === 'visual') {
                        // Sync from Monaco to Canvas
                        const canvas = document.getElementById('visual-canvas');
                        if (canvas && typeof htmlModel !== 'undefined') {
                            canvas.innerHTML = htmlModel.getValue();
                            
                            // Let Alpine initialize bindings and then re-apply editable logic
                            setTimeout(() => {
                                const container = document.querySelector('[x-data="invitationEditor()"]');
                                if(container) {
                                    const editorData = Alpine.$data(container);
                                    if(editorData && typeof editorData.init === 'function') {
                                        editorData.init();
                                    }
                                }
                            }, 50);
                        }
                    } else if (value === 'code') {
                        // Sync from Canvas to Monaco
                        window.syncToMonaco();
                    }
                });
            },
            preSaveSync() {
                if(this.mode === 'visual') {
                    window.syncToMonaco();
                }
            }
        }));

        Alpine.data('invitationEditor', () => ({
            // Fake data for rendering x-text variables in Editor
            groom_name: 'Romeo',
            bride_name: 'Juliet',
            event_date: '2026-12-12T08:00:00',
            
            // Node Inspector State
            isPropertiesPanelOpen: false,
            selectedNode: null,
            nodeData: { tagName: '', text: '', classes: '', href: '', src: '', isDynamic: false },
            
            // Hover Block Control State
            hoveredNode: null,
            hoverMenuVisible: false,
            hoverMenuPos: { top: '0px', left: '0px', width: '0px', height: '0px' },

            trackHover(event) {
                // Ignore if we are dragging
                if (event.buttons > 0) return;
                
                const el = event.target;
                if (!el || el.id === 'visual-canvas') return;
                
                // Find closest block
                const block = el.closest('section, header, footer, div.flex, div.grid, div.container, [class*="section"]');
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

            deleteHoveredNode() {
                if (this.hoveredNode) {
                    if (this.selectedNode === this.hoveredNode || this.hoveredNode.contains(this.selectedNode)) {
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
                    // Open drawer
                    this.drawerOpen = true;
                }
            },

            moveNodeUp() {
                if (this.hoveredNode && this.hoveredNode.previousElementSibling) {
                    this.hoveredNode.parentNode.insertBefore(this.hoveredNode, this.hoveredNode.previousElementSibling);
                    
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
                    this.hoveredNode.parentNode.insertBefore(this.hoveredNode.nextElementSibling, this.hoveredNode);
                    
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
                if (event.target.id === 'visual-canvas') return;
                
                // Prevent following links during editing
                const aTag = event.target.closest('a');
                if (aTag) {
                    event.preventDefault();
                }

                let el = event.target;
                
                // If they clicked a child of an SVG, target the SVG instead
                if (el.closest('svg')) {
                    el = el.closest('svg');
                }
                
                // Remove highlight from previous node
                this.removeHighlight();

                this.selectedNode = el;
                this.nodeData.tagName = el.tagName.toUpperCase();
                this.nodeData.isDynamic = el.hasAttribute('x-text') || el.closest('[x-text]') !== null;
                
                // Only pull text if it's a relatively simple element (leaf node) to avoid nested HTML text extraction
                if (!this.nodeData.isDynamic && el.children.length === 0) {
                    this.nodeData.text = el.textContent;
                } else {
                    this.nodeData.text = ''; // Clear text if it has children or is dynamic
                }
                
                // Clean up classes by removing temporary highlight classes from the string
                let cleanClasses = el.getAttribute('class') || '';
                cleanClasses = cleanClasses.replace(/\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '').replace(/\s+/g, ' ').trim();
                
                this.nodeData.classes = cleanClasses;
                this.nodeData.href = el.getAttribute('href') || '';
                this.nodeData.src = el.getAttribute('src') || '';
                
                // Add highlight
                el.classList.add('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                
                this.isPropertiesPanelOpen = true;
            },
            
            removeHighlight() {
                if (this.selectedNode) {
                    this.selectedNode.classList.remove('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
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
            
            updateNodeProperty(property, value) {
                if (!this.selectedNode) return;
                
                if (property === 'text' && !this.nodeData.isDynamic) {
                    this.selectedNode.textContent = value;
                } else if (property === 'classes') {
                    // Temporarily remove highlight before applying classes to ensure clean class string is set
                    this.removeHighlight();
                    if(value.trim() === '') {
                        this.selectedNode.removeAttribute('class');
                    } else {
                        this.selectedNode.setAttribute('class', value);
                    }
                    // Re-apply highlight
                    this.selectedNode.classList.add('ring-2', 'ring-blue-500', 'ring-inset', 'outline-none');
                } else if (property === 'href') {
                    if (value) this.selectedNode.setAttribute('href', value);
                    else this.selectedNode.removeAttribute('href');
                } else if (property === 'src') {
                    if (value) this.selectedNode.setAttribute('src', value);
                    else this.selectedNode.removeAttribute('src');
                }
                
                window.syncToMonaco();
            },
            
            toggleTailwindClass(classToAdd, classesToRemove = []) {
                if (!this.selectedNode) return;
                
                const classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !== '');
                
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
                            el.className = el.className.replace(/\b(sm|md|lg|xl|2xl):/g, '@$1:');
                        }
                    });

                    new Sortable(canvas, {
                        group: 'shared',
                        animation: 150,
                        ghostClass: 'bg-blue-50',
                        onEnd: function (evt) {
                            window.syncToMonaco();
                        },
                        onAdd: function (evt) {
                            window.syncToMonaco();
                        }
                    });
                }
                this.initEditable();
                this.initMediaEditable();
            },
            initEditable() {
                const textElements = this.$el.querySelectorAll('h1:not([x-text]), h2:not([x-text]), p:not([x-text]), span:not([x-text])');
                textElements.forEach(el => {
                    el.setAttribute('contenteditable', 'true');
                    el.classList.add('hover:outline', 'hover:outline-1', 'hover:outline-blue-400', 'focus:outline-2', 'focus:outline-blue-500', 'transition-all');
                    
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
                    el.classList.add('border-b', 'border-dashed', 'border-blue-400', 'cursor-not-allowed', 'select-none');
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
                    const matchSearch = c.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                      (c.description && c.description.toLowerCase().includes(this.search.toLowerCase()));
                    const matchCategory = this.selectedCategory === '' || c.category === this.selectedCategory;
                    return matchSearch && matchCategory;
                });
            },
            
            async insertComponent(id) {
                try {
                    const response = await fetch(`/admin/api/component-library/${id}`);
                    const component = await response.json();
                    
                    // Default values for variables
                    let code = component.code;
                    if(component.variables && component.variables.length > 0) {
                        component.variables.forEach(v => {
                            const regex = new RegExp(`\\{\\{\\s*\\$${v.key}\\s*\\}\\}`, 'g');
                            code = code.replace(regex, v.default || `[${v.label}]`);
                        });
                    }
                    
                    if (this.mode === 'visual') {
                        const canvas = document.getElementById('visual-canvas');
                        if (canvas) {
                            if (this.insertTargetNode) {
                                this.insertTargetNode.insertAdjacentHTML('afterend', code);
                                this.insertTargetNode = null;
                            } else {
                                canvas.insertAdjacentHTML('beforeend', code);
                            }
                            
                            // Let Alpine initialize bindings on new html
                            // and then attach editable events
                            setTimeout(() => {
                                const container = document.querySelector('[x-data="invitationEditor()"]');
                                if(container) {
                                    const editorData = Alpine.$data(container);
                                    if(editorData && typeof editorData.initEditable === 'function') {
                                        editorData.initEditable();
                                    }
                                }
                                window.syncToMonaco();
                            }, 50);
                        }
                    } else {
                        if(!globalEditor) return;
                        
                        // Insert at cursor
                        const position = globalEditor.getPosition();
                        globalEditor.executeEdits("library-insert", [{
                            range: new monaco.Range(position.lineNumber, position.column, position.lineNumber, position.column),
                            text: code + '\n',
                            forceMoveMarkers: true
                        }]);
                        globalEditor.focus();
                    }
                    
                    // Tutup laci
                    this.drawerOpen = false;

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
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        
        // Blade Snippets for HTML Language
        monaco.languages.registerCompletionItemProvider('html', {
            triggerCharacters: ['@', '{'],
            provideCompletionItems: function(model, position) {
                var word = model.getWordUntilPosition(position);
                var range = {
                    startLineNumber: position.lineNumber,
                    endLineNumber: position.lineNumber,
                    startColumn: word.startColumn,
                    endColumn: word.endColumn
                };
                
                // Cek apakah sedang mengetik sesuatu dengan prefix tertentu
                var textUntilPosition = model.getValueInRange({startLineNumber: 1, startColumn: 1, endLineNumber: position.lineNumber, endColumn: position.column});
                
                var suggestions = [
                    { label: '\\x40if', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40if(${1:condition})\n\t$0\n\\x40endif', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range, detail: 'Blade if statement' },
                    { label: '\\x40else', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40else', range: range },
                    { label: '\\x40elseif', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40elseif(${1:condition})', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40foreach', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40foreach($${1:array} as $${2:item})\n\t$0\n\\x40endforeach', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range, detail: 'Blade foreach loop' },
                    { label: '\\x40empty', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40empty\n\t$0', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40auth', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40auth\n\t$0\n\\x40endauth', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40guest', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40guest\n\t$0\n\\x40endguest', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40include', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40include(\'${1:view}\')', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40php', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40php\n\t$0\n\\x40endphp', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40isset', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40isset($${1:var})\n\t$0\n\\x40endisset', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40switch', kind: monaco.languages.CompletionItemKind.Keyword, insertText: '\\x40switch($${1:var})\n\t\\x40case(${2:1})\n\t\t$0\n\t\t\\x40break\n\\x40endswitch', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x40click (Alpine)', kind: monaco.languages.CompletionItemKind.Event, insertText: '\\x40click="${1:function}()"', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x7b\\x7b \\x7d\\x7d (Echo)', kind: monaco.languages.CompletionItemKind.Snippet, insertText: '\\x7b\\x7b $1 \\x7d\\x7d', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: '\\x7b!! !!\\x7d (Raw Echo)', kind: monaco.languages.CompletionItemKind.Snippet, insertText: '\\x7b!! $1 !!\\x7d', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: 'x-data (Alpine)', kind: monaco.languages.CompletionItemKind.Snippet, insertText: 'x-data="{ ${1:property}: ${2:value} }"', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: 'x-show (Alpine)', kind: monaco.languages.CompletionItemKind.Snippet, insertText: 'x-show="${1:condition}"', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                    { label: 'x-bind (Alpine)', kind: monaco.languages.CompletionItemKind.Snippet, insertText: 'x-bind:${1:class}="${2:condition}"', insertTextRules: monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet, range: range },
                ];
                
                return { suggestions: suggestions };
            }
        });

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
            minimap: { enabled: false },
            fontSize: 14,
            lineHeight: 24,
            padding: { top: 16 }
        });

        function handleSave(e) {
            if(e) e.preventDefault();
            
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
        
        if(tab === 'cover') globalEditor.setModel(coverModel);
        else if(tab === 'html') globalEditor.setModel(htmlModel);
        else if(tab === 'css') globalEditor.setModel(cssModel);
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
            if(!globalEditor) return;
            const imgTag = `<img src="${url}" alt="image" class="w-full h-auto">`;
            
            const position = globalEditor.getPosition();
            globalEditor.executeEdits("media-insert", [{
                range: new monaco.Range(position.lineNumber, position.column, position.lineNumber, position.column),
                text: imgTag,
                forceMoveMarkers: true
            }]);
            globalEditor.focus();
        } else if (mediaTarget === 'audio') {
            const audioField = document.getElementById('bg_music_input_field');
            if(audioField) {
                audioField.value = url;
                audioField.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
        
        closeMediaLibrary();
    }
</script>

<!-- Modal Media Library -->
<div id="mediaModal" class="hidden fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl h-[90vh] flex flex-col overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Media Library</h3>
            <button onclick="closeMediaLibrary()" class="text-gray-500 hover:text-red-500 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex-1 overflow-hidden relative">
            <iframe id="mediaIframe" src="" class="w-full h-full border-0"></iframe>
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
