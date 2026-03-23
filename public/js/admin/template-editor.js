// Template Editor Alpine.js Component
function templateEditor() {
    return {
        templateId: window.templateId,
        templateData: null,
        sections: [],  // Top-level sections only (parent_id === null)
        allSections: [],  // All sections (flat array from server)
        selectedSection: null,
        currentViewport: 'desktop',
        currentTab: 'Settings',
        loading: true,
        saving: false,
        lastSaved: null,
        hasUnsavedChanges: false,
        componentSchemas: window.componentSchemas || {},
        saveTimeout: null,
        currentMediaPickerProperty: null,
        _addingComponent: false,  // Guard flag to prevent double add
        draggedComponent: null,  // For drag & drop from sidebar
        draggedComponentType: null,  // 'section' or 'element'
        isDraggingOver: false,  // For visual feedback when dragging over canvas

        async init() {
            await this.loadTemplateData();
            this.initSortable();

            // Store reference to addComponent for global access
            window.templateEditorAddComponent = (type) => this.addComponent(type);

            // Store reference to drag start handler for global access
            window.templateEditorDragStart = (type, componentType, event) => {
                this.draggedComponent = type;
                this.draggedComponentType = componentType;
                event.dataTransfer.effectAllowed = 'copy';
                event.dataTransfer.setData('text/plain', type);
            };

            // Listen for save event from layout
            window.addEventListener('editor-save', () => {
                this.saveSections();
            });

            // Warn before leaving with unsaved changes
            window.addEventListener('beforeunload', (e) => {
                if (this.hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },

        async loadTemplateData() {
            try {
                const response = await fetch(`/admin/api/templates/${this.templateId}/load`);
                const data = await response.json();
                this.templateData = data.template;
                this.allSections = data.sections || [];
                this.buildSectionTree();
            } catch (error) {
                console.error('Error loading template:', error);
                Swal.fire('Error', 'Failed to load template data', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Build nested tree from flat array
        buildSectionTree() {
            const grouped = { 'root': [] };

            // Group by parent_id
            this.allSections.forEach(section => {
                const parentId = section.parent_id || 'root';
                if (!grouped[parentId]) {
                    grouped[parentId] = [];
                }
                grouped[parentId].push(section);
            });

            // Build tree recursively
            this.sections = (grouped['root'] || []).map(section => ({
                ...section,
                children: this.buildChildren(section.id, grouped)
            }));
        },

        // Recursively build children
        buildChildren(parentId, grouped) {
            const children = grouped[parentId] || [];
            return children.map(child => ({
                ...child,
                children: this.buildChildren(child.id, grouped)
            }));
        },

        // Find section in nested tree
        findSection(sections, id) {
            for (const section of sections) {
                if (section.id === id) return section;
                if (section.children && section.children.length > 0) {
                    const found = this.findSection(section.children, id);
                    if (found) return found;
                }
            }
            return null;
        },

        initSortable() {
            const canvas = document.getElementById('editor-canvas');
            if (!canvas) return;

            // Initialize top-level section sorting
            new Sortable(canvas, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                handle: '.section-wrapper', // Only drag by section-wrapper
                onEnd: (evt) => {
                    // Move item in array directly using SortableJS event data
                    const item = this.sections.splice(evt.oldIndex, 1)[0];
                    this.sections.splice(evt.newIndex, 0, item);

                    // Update order_index for all sections
                    this.sections.forEach((section, index) => {
                        section.order_index = index;
                    });

                    this.hasUnsavedChanges = true;
                }
            });

            // Initialize sortable for all section drop zones
            this.initSectionDropZones();
        },

        // Initialize sortable for section drop zones (elements within sections)
        initSectionDropZones() {
            // Use a mutation observer to watch for new sections being added
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                // Check if this node contains section-drop-zone elements
                                const dropZones = node.querySelectorAll?.('.section-drop-zone') || [];
                                dropZones.forEach((zone) => this.initDropZone(zone));

                                // Check if the node itself is a drop zone
                                if (node.classList?.contains('section-drop-zone')) {
                                    this.initDropZone(node);
                                }
                            }
                        });
                    }
                });
            });

            // Start observing the canvas for changes
            const canvas = document.getElementById('editor-canvas');
            if (canvas) {
                observer.observe(canvas, { childList: true, subtree: true });

                // Initialize existing drop zones
                const existingDropZones = canvas.querySelectorAll('.section-drop-zone');
                existingDropZones.forEach((zone) => this.initDropZone(zone));
            }
        },

        // Initialize SortableJS on a single drop zone
        initDropZone(dropZoneElement) {
            // Skip if already initialized
            if (dropZoneElement.classList.contains('sortable-initialized')) {
                return;
            }

            // Get the section ID from data attributes
            const sectionWrapper = dropZoneElement.closest('.section-wrapper');
            if (!sectionWrapper) return;

            const sectionId = sectionWrapper.getAttribute('data-section-id');
            if (!sectionId) return;

            // Get column index for multi-column sections
            const columnIndex = parseInt(dropZoneElement.getAttribute('data-column-index') || '0');

            new Sortable(dropZoneElement, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                group: 'elements', // Allow dragging between drop zones
                onEnd: (evt) => {
                    this.handleElementDrop(sectionId, columnIndex, evt);
                }
            });

            dropZoneElement.classList.add('sortable-initialized');
        },

        // Handle element drop within a section
        handleElementDrop(sectionId, columnIndex, evt) {
            const section = this.findSection(this.sections, sectionId);
            if (!section) return;

            const elementId = evt.item.getAttribute('data-element-id');
            if (!elementId) return;

            // Find the element in the section's children
            const elementIndex = section.children?.findIndex(c => c.id === elementId);
            if (elementIndex === -1) return;

            const element = section.children[elementIndex];

            // Check if element was moved to a different column
            if (section.section_type === 'section_two_col' || section.section_type === 'section_three_col') {
                // Update order_index to match the new column
                element.order_index = columnIndex;
            }

            // Reorder children array based on DOM order
            this.reorderSectionChildren(section);

            this.hasUnsavedChanges = true;
        },

        // Reorder section children based on DOM order
        reorderSectionChildren(section) {
            if (!section.children || section.children.length === 0) return;

            const sectionWrapper = document.querySelector(`[data-section-id="${section.id}"]`);
            if (!sectionWrapper) return;

            // Get all element wrappers in this section
            const elementWrappers = sectionWrapper.querySelectorAll('.element-wrapper');

            // Create new children array based on DOM order
            const newChildren = [];
            elementWrappers.forEach((wrapper) => {
                const elId = wrapper.getAttribute('data-element-id');
                const child = section.children.find(c => c.id === elId);
                if (child) {
                    newChildren.push(child);
                }
            });

            // Update order_index for each child
            newChildren.forEach((child, index) => {
                child.order_index = index;
            });

            // Replace the children array
            section.children = newChildren;
        },

        // Re-initialize drop zones after adding/removing elements
        refreshDropZones() {
            const canvas = document.getElementById('editor-canvas');
            if (!canvas) return;

            // Remove sortable-initialized class from all drop zones
            const dropZones = canvas.querySelectorAll('.section-drop-zone');
            dropZones.forEach((zone) => {
                zone.classList.remove('sortable-initialized');
            });

            // Re-initialize
            this.$nextTick(() => {
                this.initSectionDropZones();
            });
        },

        setViewport(size) {
            this.currentViewport = size;
        },

        async addComponent(type, parentSectionId = null) {
            // Prevent double-click/double add
            if (this._addingComponent) return;
            this._addingComponent = true;

            const schema = this.componentSchemas[type];
            if (!schema) {
                this._addingComponent = false;
                return;
            }

            const newSection = {
                id: 'temp-' + Date.now(),
                parent_id: parentSectionId,
                section_type: type,
                props: this.getDefaultProps(schema),
                order_index: 0,
                children: []
            };

            // Use $nextTick to ensure Alpine reactivity
            this.$nextTick(() => {
                if (parentSectionId) {
                    // Add as element inside section
                    const parent = this.findSection(this.sections, parentSectionId);
                    if (parent) {
                        if (!parent.children) parent.children = [];
                        newSection.order_index = parent.children.length;
                        parent.children.push(newSection);
                    }
                } else {
                    // Add as top-level section
                    newSection.order_index = this.sections.length;
                    this.sections.push(newSection);
                }

                this.selectedSection = newSection;
                this.hasUnsavedChanges = true;
                this._addingComponent = false;

                // Refresh drop zones to enable sortable on new elements
                this.refreshDropZones();
            });
        },

        getDefaultProps(schema) {
            const props = {};
            if (schema.fields) {
                for (const [key, field] of Object.entries(schema.fields)) {
                    if (field.default !== undefined) {
                        props[key] = field.default;
                    }
                }
            }
            return props;
        },

        selectSection(section) {
            this.selectedSection = section;
            const schema = this.componentSchemas[section.section_type];
            if (schema && schema.tabs) {
                this.currentTab = schema.tabs[0];
            }
        },

        selectTemplate() {
            // Click on canvas background → select template (no section selected)
            this.selectedSection = null;
        },

        updateProp(key, value) {
            if (!this.selectedSection) return;
            // Using $set to ensure Alpine reactivity
            this.selectedSection.props = {
                ...this.selectedSection.props,
                [key]: value
            };
            this.hasUnsavedChanges = true;
        },

        async confirmDeleteSection(section) {
            const result = await Swal.fire({
                title: 'Delete this section?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                this.deleteSection(section.id);
            }
        },

        async deleteSection(sectionId) {
            this.$nextTick(() => {
                const index = this.sections.findIndex(s => s.id === sectionId);
                if (index !== -1) {
                    this.sections.splice(index, 1);
                }
                if (this.selectedSection?.id === sectionId) {
                    this.selectedSection = null;
                }
                this.hasUnsavedChanges = true;
            });
        },

        async duplicateSection(section) {
            const duplicated = {
                ...section,
                id: 'temp-' + Date.now(),
                order_index: this.sections.length
            };
            this.sections.push(duplicated);
            this.selectedSection = duplicated;
            this.hasUnsavedChanges = true;
        },

        async moveSection(index, direction) {
            const newIndex = index + direction;
            if (newIndex < 0 || newIndex >= this.sections.length) return;

            const temp = this.sections[index];
            this.sections[index] = this.sections[newIndex];
            this.sections[newIndex] = temp;

            this.hasUnsavedChanges = true;
        },

        // Sync sections array with DOM order after drag-drop
        reorderSectionsArray() {
            const canvas = document.getElementById('editor-canvas');
            if (!canvas) return;

            const sectionElements = canvas.querySelectorAll('.section-wrapper');
            const newOrderIds = Array.from(sectionElements).map(el => {
                // Get section ID from Alpine data
                const AlpineData = Alpine.$data(el);
                return AlpineData.section.id;
            });

            // Reorder sections array based on DOM order
            const reorderedSections = [];
            newOrderIds.forEach(id => {
                const section = this.sections.find(s => s.id === id);
                if (section) {
                    reorderedSections.push(section);
                }
            });

            this.sections = reorderedSections;
        },

        async reorderSections() {
            const newOrder = this.sections.map((s, index) => ({
                id: s.id,
                order_index: index
            }));

            try {
                await fetch('/admin/api/templates/sections/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sections: newOrder })
                });
            } catch (error) {
                console.error('Error reordering:', error);
            }
        },

        // Flatten nested tree to array for saving
        flattenSections(sections, result = []) {
            sections.forEach(section => {
                result.push({
                    id: section.id,
                    parent_id: section.parent_id || null,
                    section_type: section.section_type,
                    order_index: section.order_index,
                    props: section.props
                });
                if (section.children && section.children.length > 0) {
                    this.flattenSections(section.children, result);
                }
            });
            return result;
        },

        async saveSections() {
            this.saving = true;
            const saveText = document.getElementById('save-text');
            if (saveText) saveText.textContent = 'Menyimpan...';

            try {
                const flatSections = this.flattenSections(this.sections);

                const response = await fetch('/admin/api/templates/sections', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        template_id: this.templateId,
                        sections: flatSections
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.lastSaved = new Date();
                    this.hasUnsavedChanges = false;
                    if (saveText) {
                        saveText.textContent = 'Tersimpan!';
                        setTimeout(() => {
                            if (saveText) saveText.textContent = 'Simpan';
                        }, 2000);
                    }

                    // Update temp IDs with real IDs from server
                    if (data.sections) {
                        data.sections.forEach((savedSection) => {
                            const index = this.sections.findIndex(s => s.id === savedSection.temp_id);
                            if (index !== -1) {
                                this.sections[index].id = savedSection.id;
                            }
                        });
                    }
                } else {
                    throw new Error(data.message || 'Save failed');
                }
            } catch (error) {
                console.error('Error saving:', error);
                Swal.fire('Error', 'Gagal menyimpan perubahan', 'error');
                if (saveText) {
                    saveText.textContent = 'Gagal';
                    setTimeout(() => {
                        if (saveText) saveText.textContent = 'Simpan';
                    }, 2000);
                }
            } finally {
                this.saving = false;
            }
        },

        async publish() {
            try {
                const response = await fetch(`/admin/templates/${this.templateId}/publish`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.templateData.is_active = true;
                    Swal.fire('Published!', 'Template is now active.', 'success');
                }
            } catch (error) {
                console.error('Error publishing:', error);
                Swal.fire('Error', 'Failed to publish template', 'error');
            }
        },

        async saveAndClose() {
            await this.saveSections();
            window.location.href = '/admin/templates';
        },

        // Get fields for the current tab
        getFieldsForTab(tabName) {
            if (!this.selectedSection) return {};

            const schema = this.componentSchemas[this.selectedSection.section_type];
            if (!schema || !schema.fields) return {};

            // If no tabs defined or tab is "Settings", return all fields
            if (!schema.tabs || tabName === 'Settings') {
                return schema.fields;
            }

            // Filter fields based on tab (currently simple implementation)
            // In future, fields could have a 'tab' property
            return schema.fields;
        },

        // Update a single property with debounced save
        updateProperty(key, value) {
            if (!this.selectedSection) return;

            // Ensure props object exists
            if (!this.selectedSection.props) {
                this.selectedSection.props = {};
            }

            // Update the property
            this.selectedSection.props[key] = value;

            // Trigger debounced save
            this.debouncedSave();
        },

        // Debounced save to avoid excessive API calls
        debouncedSave() {
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => {
                this.saveSections();
            }, 1000);
        },

        formatTime(date) {
            if (!date) return '';
            return new Date(date).toLocaleTimeString();
        },

        // ============================================
        // Helper Functions for Canvas Rendering
        // ============================================

        // Get inline styles for a section container
        getSectionStyle(section) {
            const props = section.props || {};
            const styles = [];

            if (props.background_color) {
                styles.push(`background-color: ${props.background_color}`);
            }
            if (props.margin_top) {
                styles.push(`margin-top: ${props.margin_top}px`);
            }
            if (props.margin_bottom) {
                styles.push(`margin-bottom: ${props.margin_bottom}px`);
            }

            return styles.join('; ');
        },

        // Get container-specific styles (padding, max-width, etc.)
        getSectionContainerStyle(section) {
            const props = section.props || {};
            const styles = [];

            if (props.padding_top) {
                styles.push(`padding-top: ${props.padding_top}px`);
            }
            if (props.padding_bottom) {
                styles.push(`padding-bottom: ${props.padding_bottom}px`);
            }
            if (props.padding_left) {
                styles.push(`padding-left: ${props.padding_left}px`);
            }
            if (props.padding_right) {
                styles.push(`padding-right: ${props.padding_right}px`);
            }
            if (props.max_width) {
                styles.push(`max-width: ${props.max_width}px`);
            }
            if (props.column_gap && section.section_type !== 'section_one_col') {
                styles.push(`gap: ${props.column_gap}px`);
            }

            return styles.join('; ');
        },

        // Get column ratio for 2-column section (as grid-template-columns)
        getSectionColumnRatio(section) {
            const props = section.props || {};
            const ratio = props.column_ratio || '50-50';
            const parts = ratio.split('-');
            return `${parts[0]}% ${parts[1]}%`;
        },

        // Render an element's HTML content
        renderElement(element) {
            const props = element.props || {};
            const type = element.section_type;

            if (type === 'text') {
                const content = props.content || 'Tulis teks anda di sini...';
                const tag = props.tag || 'p';
                const align = props.align || 'left';

                let html = '';
                if (tag === 'h1') {
                    html = `<h1 class="text-4xl font-bold text-${align}" style="font-family: 'Playfair Display', serif;">${this.escapeHtml(content)}</h1>`;
                } else if (tag === 'h2') {
                    html = `<h2 class="text-3xl font-bold text-${align}" style="font-family: 'Playfair Display', serif;">${this.escapeHtml(content)}</h2>`;
                } else if (tag === 'h3') {
                    html = `<h3 class="text-2xl font-semibold text-${align}" style="font-family: 'Playfair Display', serif;">${this.escapeHtml(content)}</h3>`;
                } else if (tag === 'h4') {
                    html = `<h4 class="text-xl font-semibold text-${align}">${this.escapeHtml(content)}</h4>`;
                } else if (tag === 'h5') {
                    html = `<h5 class="text-lg font-medium text-${align}">${this.escapeHtml(content)}</h5>`;
                } else if (tag === 'h6') {
                    html = `<h6 class="text-base font-medium text-${align}">${this.escapeHtml(content)}</h6>`;
                } else {
                    html = `<p class="text-base leading-relaxed text-${align}">${this.escapeHtml(content)}</p>`;
                }

                return html;
            }

            // Default: show component name
            return `<div class="p-4 text-center text-gray-500 border border-dashed">
                <p class="font-medium">${this.componentSchemas[type]?.name || type}</p>
                <p class="text-xs text-gray-400">(Preview not yet implemented)</p>
            </div>`;
        },

        // Escape HTML to prevent XSS
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        // Delete an element from a section
        async confirmDeleteElement(element, parentSection) {
            const result = await Swal.fire({
                title: 'Delete this element?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                this.deleteElement(parentSection, element.id);
            }
        },

        deleteElement(parentSection, elementId) {
            this.$nextTick(() => {
                const index = parentSection.children.findIndex(c => c.id === elementId);
                if (index !== -1) {
                    parentSection.children.splice(index, 1);
                }
                if (this.selectedSection?.id === elementId) {
                    this.selectedSection = parentSection;
                }
                this.hasUnsavedChanges = true;
            });
        },

        // ============================================
        // Drag & Drop Handlers
        // ============================================

        onCanvasDrop(event) {
            event.preventDefault();

            // Get dragged component type from dataTransfer
            const componentType = event.dataTransfer.getData('text/plain');
            if (!componentType) return;

            // Check if we have a dragged component stored
            if (!this.draggedComponent) return;

            // Check component type (section or element)
            const isSection = this.draggedComponentType === 'section';
            const isElement = this.draggedComponentType === 'element';

            // If it's a section, add to top level
            if (isSection) {
                this.addComponent(this.draggedComponent, null);
            }
            // If it's an element, add to selected section or show warning
            else if (isElement) {
                if (!this.selectedSection) {
                    Swal.fire({
                        title: 'No Section Selected',
                        text: 'Please select a section first before adding an element, or drag the element directly onto a section.',
                        icon: 'warning',
                        confirmButtonColor: '#d4af37'
                    });
                    return;
                }

                // Check if selected section is actually a section (not an element)
                const schema = this.componentSchemas[this.selectedSection.section_type];
                if (schema?.type !== 'section') {
                    Swal.fire({
                        title: 'Not a Section',
                        text: 'Please select a section container before adding an element.',
                        icon: 'warning',
                        confirmButtonColor: '#d4af37'
                    });
                    return;
                }

                // Add element to the selected section
                this.addComponent(this.draggedComponent, this.selectedSection.id);
            }

            // Reset drag state
            this.draggedComponent = null;
            this.draggedComponentType = null;
        }
    };
}
