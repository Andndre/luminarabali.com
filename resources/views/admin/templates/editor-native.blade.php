@extends('layouts.admin')

@section('title', 'Blade Code Editor')



@section('content')
<div class="h-[calc(100vh-64px)] flex overflow-hidden">
    <!-- Left Panel: Monaco Code Editor -->
    <div class="w-1/2 h-full bg-[#1e1e1e] border-r border-gray-800 flex flex-col relative">
        <div class="p-3 bg-[#252526] text-white flex justify-between items-center border-b border-gray-800 z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.templates.index') }}" class="text-xs text-gray-400 hover:text-white transition">&larr; Kembali</a>
                <h1 class="text-sm font-semibold">{{ $template->name }} <span class="text-gray-500 font-normal">| blade_content</span></h1>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openMediaLibrary()" class="bg-indigo-600 hover:bg-indigo-500 px-3 py-1.5 rounded text-xs transition flex items-center gap-1 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Media Library
                </button>
                <button type="button" onclick="document.getElementById('previewFrame').contentWindow.location.reload()" class="bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded text-xs transition flex items-center gap-1 text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh Preview
                </button>
                <button form="editorForm" type="submit" class="bg-blue-600 hover:bg-blue-500 px-4 py-1.5 rounded text-xs font-medium transition text-white">
                    Simpan (Ctrl+S)
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-800 text-xs text-gray-400 bg-[#1e1e1e]">
            <button type="button" onclick="switchTab('cover')" id="tab-cover" class="px-4 py-2 border-b-2 border-transparent hover:text-white hover:bg-[#2d2d2d] transition">Cover Page (Blade)</button>
            <button type="button" onclick="switchTab('html')" id="tab-html" class="px-4 py-2 border-b-2 border-blue-500 text-white hover:bg-[#2d2d2d] transition">Main Content (Blade)</button>
            <button type="button" onclick="switchTab('css')" id="tab-css" class="px-4 py-2 border-b-2 border-transparent hover:text-white hover:bg-[#2d2d2d] transition">Global CSS</button>
            <button type="button" onclick="switchTab('json')" id="tab-json" class="px-4 py-2 border-b-2 border-transparent hover:text-white hover:bg-[#2d2d2d] transition">Properties (JSON)</button>
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

        <!-- Properties Form UI (Alpine.js) -->
        <div id="properties-ui" class="flex-1 w-full h-full bg-[#FAF8F5] overflow-y-auto hidden"
             x-data="propertiesForm({{ json_encode($template->meta_data ?: ['bg_music' => '', 'rsvp_enabled' => true, 'theme' => 'forest']) }})">
            <div class="p-8 max-w-3xl mx-auto text-gray-800">
                <div class="mb-8 border-b border-gray-200 pb-4">
                    <h2 class="text-2xl font-serif text-gray-900">Template Properties</h2>
                    <p class="text-sm text-gray-500 mt-1">Konfigurasi dasar dari template ini (dapat dioverride oleh tiap klien).</p>
                </div>

                <div class="space-y-6">
                    <!-- Background Music -->
                    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Background Music (MP3 URL)</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="formData.bg_music" @input="updateJson" id="bg_music_input_field" class="flex-1 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2" placeholder="https://example.com/audio.mp3">
                            <button type="button" onclick="openMediaLibrary('audio')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition border border-gray-200">Browse</button>
                        </div>
                    </div>

<!-- Removed Theme Selector as requested -->

                    <!-- RSVP Toggle -->
                    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Aktifkan Form RSVP</label>
                            <p class="text-xs text-gray-500 mt-1">Menampilkan form konfirmasi kehadiran.</p>
                        </div>
                        <button type="button" @click="formData.rsvp_enabled = !formData.rsvp_enabled; updateJson()"
                                :class="formData.rsvp_enabled ? 'bg-indigo-600' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span aria-hidden="true" :class="formData.rsvp_enabled ? 'translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Live Preview Iframe -->
    <div class="w-1/2 h-full bg-gray-100 relative">
        <div class="absolute top-4 right-4 z-10 flex gap-2">
            <a href="{{ route('admin.templates.preview', $template->id) }}" target="_blank" class="bg-white/80 backdrop-blur px-3 py-2 rounded shadow text-xs font-medium hover:bg-white flex items-center gap-2 border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Buka Tab Baru
            </a>
        </div>
        
        <iframe 
            id="previewFrame"
            src="{{ route('admin.templates.preview', $template->id) }}" 
            class="w-full h-full border-0 bg-white shadow-inner"
        ></iframe>
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
