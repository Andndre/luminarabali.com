// Component Schemas for Visual Invitation Editor
// Elementor-like structure: Sections (containers) → Elements

const componentSchemas = {
    // ============================================
    // SECTIONS (Container Components)
    // ============================================
    section_one_col: {
        name: 'Section (1 Column)',
        description: 'Single column section container',
        icon: 'square',
        type: 'section',  // Marks this as a container/section
        fields: {
            padding_top: {
                type: 'slider',
                label: 'Padding Top',
                min: 0,
                max: 200,
                unit: 'px',
                default: 60
            },
            padding_bottom: {
                type: 'slider',
                label: 'Padding Bottom',
                min: 0,
                max: 200,
                unit: 'px',
                default: 60
            },
            padding_left: {
                type: 'slider',
                label: 'Padding Left',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            padding_right: {
                type: 'slider',
                label: 'Padding Right',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            max_width: {
                type: 'slider',
                label: 'Max Width',
                min: 300,
                max: 1600,
                unit: 'px',
                default: 1200
            },
            background_color: {
                type: 'color',
                label: 'Background Color',
                default: '#ffffff'
            },
            margin_top: {
                type: 'slider',
                label: 'Margin Top',
                min: 0,
                max: 100,
                unit: 'px',
                default: 0
            },
            margin_bottom: {
                type: 'slider',
                label: 'Margin Bottom',
                min: 0,
                max: 100,
                unit: 'px',
                default: 0
            }
        }
    },

    section_two_col: {
        name: 'Section (2 Columns)',
        description: 'Two column section container',
        icon: 'columns-2',
        type: 'section',
        fields: {
            padding_top: {
                type: 'slider',
                label: 'Padding Top',
                min: 0,
                max: 200,
                unit: 'px',
                default: 60
            },
            padding_bottom: {
                type: 'slider',
                label: 'Padding Bottom',
                min: 0,
                max: 200,
                unit: 'px',
                default: 60
            },
            padding_left: {
                type: 'slider',
                label: 'Padding Left',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            padding_right: {
                type: 'slider',
                label: 'Padding Right',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            max_width: {
                type: 'slider',
                label: 'Max Width',
                min: 300,
                max: 1600,
                unit: 'px',
                default: 1200
            },
            column_gap: {
                type: 'slider',
                label: 'Column Gap',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            column_ratio: {
                type: 'select',
                label: 'Column Ratio',
                options: [
                    { value: '50-50', label: '50% - 50%' },
                    { value: '33-67', label: '33% - 67%' },
                    { value: '67-33', label: '67% - 33%' },
                    { value: '40-60', label: '40% - 60%' },
                    { value: '60-40', label: '60% - 40%' }
                ],
                default: '50-50'
            },
            vertical_align: {
                type: 'select',
                label: 'Vertical Align',
                options: [
                    { value: 'top', label: 'Top' },
                    { value: 'center', label: 'Center' },
                    { value: 'bottom', label: 'Bottom' }
                ],
                default: 'top'
            },
            background_color: {
                type: 'color',
                label: 'Background Color',
                default: '#ffffff'
            },
            margin_top: {
                type: 'slider',
                label: 'Margin Top',
                min: 0,
                max: 100,
                unit: 'px',
                default: 0
            },
            margin_bottom: {
                type: 'slider',
                label: 'Margin Bottom',
                min: 0,
                max: 100,
                unit: 'px',
                default: 0
            }
        }
    },

    section_three_col: {
        name: 'Section (3 Columns)',
        description: 'Three column section container',
        icon: 'columns-3',
        type: 'section',
        fields: {
            padding_top: {
                type: 'slider',
                label: 'Padding Top',
                min: 0,
                max: 200,
                unit: 'px',
                default: 60
            },
            padding_bottom: {
                type: 'slider',
                label: 'Padding Bottom',
                min: 0,
                max: 200,
                unit: 'px',
                default: 60
            },
            padding_left: {
                type: 'slider',
                label: 'Padding Left',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            padding_right: {
                type: 'slider',
                label: 'Padding Right',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            max_width: {
                type: 'slider',
                label: 'Max Width',
                min: 300,
                max: 1600,
                unit: 'px',
                default: 1200
            },
            column_gap: {
                type: 'slider',
                label: 'Column Gap',
                min: 0,
                max: 100,
                unit: 'px',
                default: 20
            },
            background_color: {
                type: 'color',
                label: 'Background Color',
                default: '#ffffff'
            },
            margin_top: {
                type: 'slider',
                label: 'Margin Top',
                min: 0,
                max: 100,
                unit: 'px',
                default: 0
            },
            margin_bottom: {
                type: 'slider',
                label: 'Margin Bottom',
                min: 0,
                max: 100,
                unit: 'px',
                default: 0
            }
        }
    },

    // ============================================
    // ELEMENTS (Inside Sections)
    // ============================================
    text: {
        name: 'Text Block',
        description: 'Heading or paragraph text',
        icon: 'type',
        type: 'element',  // Marks this as an element (goes inside sections)
        fields: {
            content: {
                type: 'textarea',
                label: 'Content',
                default: 'Tulis teks anda di sini...'
            },
            tag: {
                type: 'select',
                label: 'HTML Tag',
                options: [
                    { value: 'p', label: 'Paragraph' },
                    { value: 'h1', label: 'Heading 1' },
                    { value: 'h2', label: 'Heading 2' },
                    { value: 'h3', label: 'Heading 3' },
                    { value: 'h4', label: 'Heading 4' },
                    { value: 'h5', label: 'Heading 5' },
                    { value: 'h6', label: 'Heading 6' }
                ],
                default: 'p'
            },
            align: {
                type: 'select',
                label: 'Alignment',
                options: [
                    { value: 'left', label: 'Left' },
                    { value: 'center', label: 'Center' },
                    { value: 'right', label: 'Right' }
                ],
                default: 'left'
            },
            margin_bottom: {
                type: 'slider',
                label: 'Margin Bottom',
                min: 0,
                max: 100,
                unit: 'px',
                default: 16
            }
        }
    }
};

// Export to window for Alpine.js access
window.componentSchemas = componentSchemas;
