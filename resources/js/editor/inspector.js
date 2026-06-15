export default function EditorInspector() {
    return {
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
            }
    };
}
