import EditorCore from './core';
import EditorHover from './hover';
import EditorInspector from './inspector';
import EditorBoxModel from './box-model';
import EditorInit from './init';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('editorApp', () => {
        return {
            ...EditorCore(),
            ...EditorHover(),
            ...EditorInspector(),
            ...EditorBoxModel(),
            ...EditorInit(),

            init() {
                if (this.coreInit) this.coreInit();
                if (this.hoverInit) this.hoverInit();
                if (this.inspectorInit) this.inspectorInit();
                if (this.boxModelInit) this.boxModelInit();
                if (this.setupInit) this.setupInit();
            }
        };
    });

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

                    let code = component.code;

                    const editorAppContainer = document.querySelector('[x-data="editorApp()"]');
                    const editorApp = editorAppContainer ? Alpine.$data(editorAppContainer) : null;
                    if (!editorApp) {
                        console.error('editorApp not found');
                        return;
                    }
                    
                    // Always insert into visual canvas
                    const canvas = document.getElementById('visual-canvas');
                    if (canvas) {
                        if (editorApp.insertTargetNode) {
                            editorApp.insertTargetNode.insertAdjacentHTML('afterend', code);
                            editorApp.insertTargetNode = null;
                        } else {
                            canvas.insertAdjacentHTML('beforeend', code);
                        }

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

                    // Flash notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Component inserted',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Failed to insert component', error);
                    Swal.fire('Error', 'Gagal memuat komponen', 'error');
                }
            }
        }));

Alpine.start();

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
        window.syncToCanvas = function(model) {
            if (window.isSyncing) return;
            
            clearTimeout(window.typingTimer);
            window.typingTimer = setTimeout(() => {
                window.isSyncing = true;
                
                const rawHTML = model.getValue();
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
        };

        htmlModel.onDidChangeContent(() => {
            if (window.activeTab === 'html') window.syncToCanvas(htmlModel);
        });
        coverModel.onDidChangeContent(() => {
            if (window.activeTab === 'cover') window.syncToCanvas(coverModel);
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
        window.activeTab = tab;
        
        document.querySelectorAll('[id^="tab-"]').forEach(el => {
            el.classList.remove('border-blue-500', 'text-white');
            el.classList.add('border-transparent');
        });
        document.getElementById('tab-' + tab).classList.remove('border-transparent');
        document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-white');

        if (tab === 'cover') {
            globalEditor.setModel(coverModel);
            if (typeof window.syncToCanvas === 'function') window.syncToCanvas(coverModel);
        } else if (tab === 'html') {
            globalEditor.setModel(htmlModel);
            if (typeof window.syncToCanvas === 'function') window.syncToCanvas(htmlModel);
        } else if (tab === 'css') {
            globalEditor.setModel(cssModel);
        }
        
        window.dispatchEvent(new CustomEvent('tab-changed', { detail: tab }));
    };

    function openMediaLibrary(target = 'editor') {
        mediaTarget = target;
        const modal = document.getElementById('mediaModal');
        const iframe = document.getElementById('mediaIframe');

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
    }

    function insertMedia(url) {
        if (mediaTarget === 'visual' && window.currentMediaTarget) {
            const el = window.currentMediaTarget;
            if (el.tagName === 'IMG') {
                el.src = url;
            } else {
                el.style.backgroundImage = `url('${url}')`;
            }
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
