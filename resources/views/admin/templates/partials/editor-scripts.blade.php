{{-- Editor JavaScript: Sync Logic, Alpine Components, Monaco, Tabs, Media Library --}}
<script>
    window.isSyncing = false;
    window.typingTimer = null;
    window.activeTab = 'html';

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
        const activeModel = window.activeTab === 'cover' ? coverModel : htmlModel;
        
        if (window.globalEditor) {
            const fullRange = activeModel.getFullModelRange();
            window.globalEditor.executeEdits('visual-canvas', [{
                range: fullRange,
                text: cleanHTML
            }]);
        } else {
            activeModel.setValue(cleanHTML);
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
            isInspectorOpen: false, // Element Inspector (separate from Page Properties)
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
            guest_name: 'Budi (Tamu VIP)',

            // Node Inspector State
            selectedNode: null,
            nodeData: {
                tagName: '',
                text: '',
                classes: '',
                href: '',
                src: '',
                isDynamic: false,
                textColor: '#000000',
                bgColor: '#ffffff'
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

                    // Remove highlight classes if any child has them
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

                // Only pull text if it's a relatively simple element (leaf node)
                if (!this.nodeData.isDynamic && this.selectedNode.children.length === 0) {
                    this.nodeData.text = this.selectedNode.textContent;
                } else {
                    this.nodeData.text = '';
                }

                // Clean up classes by removing temporary highlight classes from the string
                let cleanClasses = this.selectedNode.getAttribute('class') || '';
                cleanClasses = cleanClasses.replace(
                        /\bring-2\b|\bring-blue-500\b|\bring-inset\b|\boutline-none\b/g, '')
                    .replace(/\s+/g, ' ').trim();

                this.nodeData.classes = cleanClasses;
                this.nodeData.href = this.selectedNode.getAttribute('href') || '';
                this.nodeData.src = this.selectedNode.getAttribute('src') || '';

                // Extract text color
                const textMatch = cleanClasses.match(/text-\[\s*(#[0-9a-fA-F]{3,8})\s*\]/);
                this.nodeData.textColor = textMatch ? textMatch[1] : '#000000';

                // Extract bg color
                const bgMatch = cleanClasses.match(/bg-\[\s*(#[0-9a-fA-F]{3,8})\s*\]/);
                this.nodeData.bgColor = bgMatch ? bgMatch[1] : '#ffffff';

                this.updateBreadcrumbs();
                this.isInspectorOpen = true; // Open the Element Inspector drawer
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
                this.isInspectorOpen = false; // Close the Element Inspector drawer
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
                    this.removeHighlight();
                    if (value.trim() === '') {
                        this.selectedNode.removeAttribute('class');
                    } else {
                        this.selectedNode.setAttribute('class', value);
                    }
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
                    const clone = this.selectedNode.cloneNode(true);

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

                    this.selectedNode.insertAdjacentHTML('afterend', clone.outerHTML);

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

                const newClasses = classes.filter(c => !classesToRemove.includes(c));

                const idx = newClasses.indexOf(classToAdd);
                if (idx > -1) {
                    newClasses.splice(idx, 1);
                } else {
                    newClasses.push(classToAdd);
                }

                this.nodeData.classes = newClasses.join(' ');
                this.updateNodeProperty('classes', this.nodeData.classes);
            },

            updateArbitraryColor(prefix, hex) {
                if (!this.selectedNode) return;
                let classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !== '');
                
                const regex = new RegExp('^' + prefix + '-\\\\[#[0-9a-fA-F]{3,8}\\\\]$');
                const staticColors = ['white', 'black', 'transparent'];
                
                classes = classes.filter(c => {
                    const base = c.replace(prefix + '-', '');
                    return !regex.test(c) && !staticColors.includes(base);
                });
                
                classes.push(prefix + '-[' + hex + ']');
                
                this.nodeData.classes = classes.join(' ');
                this.updateNodeProperty('classes', this.nodeData.classes);
            },

            updateDirectionalClass(prefix, newValue) {
                if (!this.selectedNode) return;
                let classes = (this.nodeData.classes || '').split(' ').filter(c => c.trim() !== '');

                const regex = new RegExp('^-?' + prefix + '(?:-|$)');

                classes = classes.filter(c => !regex.test(c));

                if (newValue && newValue.trim() !== '') {
                    classes.push(newValue);
                }

                this.nodeData.classes = classes.join(' ');
                this.updateNodeProperty('classes', this.nodeData.classes);
            },

            init() {
                const canvas = document.getElementById('visual-canvas');
                if (canvas) {
                    // Automatically convert standard Tailwind breakpoints to Container Queries
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

                    el.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            document.execCommand('insertLineBreak');
                        }
                    });

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
</script>
