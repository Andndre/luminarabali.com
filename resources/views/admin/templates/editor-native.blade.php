@extends('layouts.editor')

@section('title', 'Blade Code Editor')



@section('content')
<div class="h-screen flex overflow-hidden w-full bg-[#1e1e1e]" x-data="templateLibrary()">
    
    <!-- Left Pane: Monaco Code Editor & Properties -->
    <div class="flex-1 h-full flex flex-col relative min-w-0 border-r border-gray-800">
        <!-- Minimal Flat Header -->
        <div class="p-3 bg-[#1e1e1e] text-gray-300 flex justify-between items-center border-b border-gray-800 z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.templates.index') }}" class="text-xs text-gray-500 hover:text-white transition">&larr; Kembali</a>
                <h1 class="text-sm font-medium">{{ $template->name }} <span class="text-gray-600 font-normal">| source</span></h1>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openMediaLibrary()" class="bg-[#2d2d2d] hover:bg-[#3d3d3d] px-3 py-1.5 rounded text-xs transition flex items-center gap-1 text-gray-300 border border-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Media
                </button>
                <button type="button" onclick="document.getElementById('previewFrame').contentWindow.location.reload()" class="bg-[#2d2d2d] hover:bg-[#3d3d3d] px-3 py-1.5 rounded text-xs transition flex items-center gap-1 text-gray-300 border border-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh
                </button>
                <button form="editorForm" type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-1.5 rounded text-xs font-medium transition text-white shadow-sm border border-blue-500">
                    Simpan
                </button>
            </div>
        </div>

        <!-- Minimal Tabs Navigation -->
        <div class="flex border-b border-gray-800 text-xs text-gray-500 bg-[#1e1e1e]">
            <button type="button" onclick="switchTab('cover')" id="tab-cover" class="px-4 py-2 border-b-2 border-transparent hover:text-gray-300 transition">Cover Page</button>
            <button type="button" onclick="switchTab('html')" id="tab-html" class="px-4 py-2 border-b-2 border-blue-500 text-white transition">Main Content</button>
            <button type="button" onclick="switchTab('css')" id="tab-css" class="px-4 py-2 border-b-2 border-transparent hover:text-gray-300 transition">Global CSS</button>
            <button type="button" onclick="switchTab('json')" id="tab-json" class="px-4 py-2 border-b-2 border-transparent hover:text-gray-300 transition">Properties</button>
        </div>

        <form id="editorForm" action="{{ route('api.templates.sections.save') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="template_id" value="{{ $template->id }}">
            <input type="hidden" name="cover_content" id="cover_content_input">
            <input type="hidden" name="blade_content" id="blade_content_input">
            <input type="hidden" name="global_custom_css" id="global_custom_css_input">
            <input type="hidden" name="meta_data" id="meta_data_input">
        </form>

        <!-- Monaco Container -->
        <div id="monaco-container" class="flex-1 w-full h-full"></div>

        <!-- Properties Form UI -->
        <div id="properties-ui" class="flex-1 w-full h-full bg-[#1e1e1e] overflow-y-auto hidden"
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

        <!-- Floating Action Button (FAB) for Library Drawer -->
        <button type="button" @click="drawerOpen = true" class="absolute bottom-6 right-6 z-20 bg-blue-600 hover:bg-blue-500 text-white rounded-full p-4 shadow-lg hover:shadow-blue-500/25 transition-all transform hover:scale-105 flex items-center justify-center group" title="Open Component Library">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>
    </div>

    <!-- Right Pane: Live Preview Iframe (Visual Canvas) -->
    <div class="flex-1 h-full bg-gray-100 relative min-w-0">
        <div class="absolute top-4 right-4 z-10 flex gap-2">
            <a href="{{ route('admin.templates.preview', $template->id) }}" target="_blank" class="bg-white/90 backdrop-blur px-3 py-2 rounded shadow-sm text-xs font-medium hover:bg-white flex items-center gap-2 border border-gray-200 text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Buka Tab Baru
            </a>
        </div>
        
        <iframe 
            id="previewFrame"
            src="{{ route('admin.templates.preview', $template->id) }}" 
            class="w-full h-full border-0 bg-white"
        ></iframe>
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
<textarea id="raw_blade_content" autocomplete="off" style="display:none;">{{ $template->blade_content ?? '' }}</textarea>
<textarea id="raw_custom_css" autocomplete="off" style="display:none;">{{ $template->global_custom_css ?? '' }}</textarea>

<!-- Load Monaco Editor synchronously before usage -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
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
            drawerOpen: false,
            
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
                    
                    if(!globalEditor) return;
                    
                    // Default values for variables
                    let code = component.code;
                    if(component.variables && component.variables.length > 0) {
                        component.variables.forEach(v => {
                            const regex = new RegExp(`\\{\\{\\s*\\$${v.key}\\s*\\}\\}`, 'g');
                            code = code.replace(regex, v.default || `[${v.label}]`);
                        });
                    }
                    
                    // Insert at cursor
                    const position = globalEditor.getPosition();
                    
                    // Fallback to EOF if position is 1,1 and model is not focused? 
                    // Actually Monaco maintains position. If they never focused, it might be 1,1.
                    // Let's just use the current position, it's the most reliable native behavior.
                    globalEditor.executeEdits("library-insert", [{
                        range: new monaco.Range(position.lineNumber, position.column, position.lineNumber, position.column),
                        text: code + '\n',
                        forceMoveMarkers: true
                    }]);
                    globalEditor.focus();
                    
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

                    // Trigger save & refresh
                    if (typeof handleSave === 'function') {
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
        const rawBlade = document.getElementById('raw_blade_content').value;
        const rawCss = document.getElementById('raw_custom_css').value;

        coverModel = monaco.editor.createModel(rawCover, "html");
        htmlModel = monaco.editor.createModel(rawBlade, "html");
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
            document.getElementById('blade_content_input').value = htmlModel.getValue();
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
                
                // Reload preview with skip_cover if editing Main Content
                const previewFrame = document.getElementById('previewFrame');
                const isHtmlTab = document.getElementById('tab-html').classList.contains('border-blue-500');
                const baseUrl = "{{ route('admin.templates.preview', $template->id) }}";
                
                previewFrame.src = baseUrl + (isHtmlTab ? '?skip_cover=1' : '');
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

        // Shortcut Ctrl+S untuk Save
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

        const monacoContainer = document.getElementById('monaco-container');
        const propertiesUi = document.getElementById('properties-ui');

        if(tab === 'json') {
            monacoContainer.classList.add('hidden');
            propertiesUi.classList.remove('hidden');
        } else {
            monacoContainer.classList.remove('hidden');
            propertiesUi.classList.add('hidden');
            
            if(tab === 'cover') globalEditor.setModel(coverModel);
            if(tab === 'html') globalEditor.setModel(htmlModel);
            if(tab === 'css') globalEditor.setModel(cssModel);
        }
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
        if(mediaTarget === 'editor') {
            if(!globalEditor) return;
            const imgTag = `<img src="${url}" alt="image" class="w-full h-auto">`;
            
            // Insert at cursor
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
                // Trigger alpine model update
                audioField.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
        
        closeMediaLibrary();
    }
</script>

<!-- Modal Media Library -->
<div id="mediaModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm items-center justify-center p-4">
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
