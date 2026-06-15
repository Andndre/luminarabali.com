export default function EditorInit() {
    return {
        setupInit() {
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
    };
}
