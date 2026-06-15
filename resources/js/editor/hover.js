export default function EditorHover() {
    return {
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
            }
    };
}
