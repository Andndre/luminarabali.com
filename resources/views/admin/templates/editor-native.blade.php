@extends('layouts.editor')

@section('title', 'Blade Code Editor')

@section('content')
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    {{-- SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    @include('admin.templates.partials.editor-styles')

    <div class="flex h-screen w-full overflow-hidden bg-[#1e1e1e]" x-data="editorApp()">

        @include('admin.templates.partials.editor-aside')

        @include('admin.templates.partials.editor-library')

        {{-- Main Workspace (Code & Visual) --}}
        <div class="relative flex h-full min-w-0 flex-1 flex-col transition-all duration-300 ease-in-out">

            {{-- Header Top Bar --}}
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

            {{-- Split View Container --}}
            <div class="flex flex-1 overflow-hidden relative">
                {{-- CODE MODE --}}
                <div x-show="panels.code" class="order-2 flex h-full flex-1 flex-col border-l border-gray-800 bg-[#1e1e1e] min-w-[400px]">
                    {{-- Minimal Tabs Navigation --}}
                    <div class="flex shrink-0 border-b border-gray-800 bg-[#1e1e1e] text-xs text-gray-500">
                        <button type="button" onclick="switchTab('cover')" id="tab-cover"
                            class="border-b-2 border-transparent px-4 py-2 transition hover:text-gray-300">Cover Page</button>
                        <button type="button" onclick="switchTab('html')" id="tab-html"
                            class="border-b-2 border-blue-500 px-4 py-2 text-white transition">Main Content</button>
                        <button type="button" onclick="switchTab('css')" id="tab-css"
                            class="border-b-2 border-transparent px-4 py-2 transition hover:text-gray-300">Global CSS</button>
                    </div>

                    {{-- Monaco Container --}}
                    <div id="monaco-container" class="h-full w-full flex-1"></div>
                </div>

                @include('admin.templates.partials.editor-visual')

                {{-- Element Inspector Drawer (floats over visual canvas) --}}
                @include('admin.templates.partials.editor-inspector')

                @include('admin.templates.partials.editor-page-properties')
            
            </div> {{-- End Split View Container --}}

            <form id="editorForm" action="{{ route('api.templates.sections.save') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="template_id" value="{{ $template->id }}">
                <input type="hidden" name="cover_content" id="cover_content_input">
                <input type="hidden" name="html_content" id="html_content_input">
                <input type="hidden" name="global_custom_css" id="global_custom_css_input">
                <input type="hidden" name="meta_data" id="meta_data_input">
            </form>
        </div>
    </div>

    @php $skip_alpine = true; @endphp

    {{-- Hidden Textareas to safely pass data to Monaco without PHP addslashes issues --}}
    <textarea id="raw_cover_content" autocomplete="off" style="display:none;">{{ $template->cover_content ?? '' }}</textarea>
    <textarea id="raw_html_content" autocomplete="off" style="display:none;">{{ $template->html_content ?? '' }}</textarea>
    <textarea id="raw_custom_css" autocomplete="off" style="display:none;">{{ $template->global_custom_css ?? '' }}</textarea>

    {{-- Load Monaco Editor synchronously before usage --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>
    
    @include('admin.templates.partials.editor-scripts')

    @include('admin.templates.partials.editor-media-modal')
@endsection
