import { ComponentSchema } from "../types";

export const componentSchemas: Record<string, ComponentSchema> = {
    // ============================================
    // SECTIONS (Container Components)
    // ============================================

    section_one_col: {
        name: "Section (1 Column)",
        type: "section",
        icon: "M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z",
        tabs: {
            spacing: {
                label: "Spacing",
                groups: {
                    padding: {
                        label: "Padding",
                        collapsed: false,
                        fields: {
                            padding_top: {
                                type: "slider",
                                label: "Top",
                                min: 0,
                                max: 200,
                                default: 60,
                                unit: "px",
                            },
                            padding_bottom: {
                                type: "slider",
                                label: "Bottom",
                                min: 0,
                                max: 200,
                                default: 60,
                                unit: "px",
                            },
                            padding_left: {
                                type: "slider",
                                label: "Left",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                            padding_right: {
                                type: "slider",
                                label: "Right",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                        },
                    },
                    margin: {
                        label: "Margin",
                        collapsed: true,
                        fields: {
                            margin_top: {
                                type: "slider",
                                label: "Top",
                                min: 0,
                                max: 100,
                                default: 0,
                                unit: "px",
                            },
                            margin_bottom: {
                                type: "slider",
                                label: "Bottom",
                                min: 0,
                                max: 100,
                                default: 0,
                                unit: "px",
                            },
                            margin_left: {
                                type: "slider",
                                label: "Left (px)",
                                min: 0,
                                max: 200,
                                default: 0,
                                unit: "px",
                            },
                            margin_left_mode: {
                                type: "select",
                                label: "Left Mode",
                                options: [
                                    { value: "px", label: "PX" },
                                    { value: "auto", label: "Auto" },
                                ],
                                default: "px",
                            },
                            margin_right: {
                                type: "slider",
                                label: "Right (px)",
                                min: 0,
                                max: 200,
                                default: 0,
                                unit: "px",
                            },
                            margin_right_mode: {
                                type: "select",
                                label: "Right Mode",
                                options: [
                                    { value: "px", label: "PX" },
                                    { value: "auto", label: "Auto" },
                                ],
                                default: "px",
                            },
                        },
                    },
                },
            },
            layout: {
                label: "Layout",
                fields: {
                    max_width: {
                        type: "slider",
                        label: "Max Width",
                        min: 300,
                        max: 1600,
                        default: 1200,
                        unit: "px",
                    },
                },
            },
            appearance: {
                label: "Appearance",
                fields: {
                    background_color: {
                        type: "color",
                        label: "Background Color",
                        default: "#ffffff",
                    },
                    border_width: {
                        type: "slider",
                        label: "Border Width",
                        min: 0,
                        max: 12,
                        default: 0,
                        unit: "px",
                    },
                    border_color: {
                        type: "color",
                        label: "Border Color",
                        default: "#e5e7eb",
                    },
                    border_radius: {
                        type: "slider",
                        label: "Border Radius",
                        min: 0,
                        max: 60,
                        default: 0,
                        unit: "px",
                    },
                    shadow: {
                        type: "select",
                        label: "Shadow",
                        options: [
                            { value: "none", label: "None" },
                            { value: "sm", label: "Small" },
                            { value: "md", label: "Medium" },
                            { value: "lg", label: "Large" },
                        ],
                        default: "none",
                    },
                },
            },
        },
    },

    section_two_col: {
        name: "Section (2 Columns)",
        type: "section",
        icon: "M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7",
        tabs: {
            spacing: {
                label: "Spacing",
                groups: {
                    padding: {
                        label: "Padding",
                        collapsed: false,
                        fields: {
                            padding_top: {
                                type: "slider",
                                label: "Top",
                                min: 0,
                                max: 200,
                                default: 60,
                                unit: "px",
                            },
                            padding_bottom: {
                                type: "slider",
                                label: "Bottom",
                                min: 0,
                                max: 200,
                                default: 60,
                                unit: "px",
                            },
                            padding_left: {
                                type: "slider",
                                label: "Left",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                            padding_right: {
                                type: "slider",
                                label: "Right",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                        },
                    },
                    margin: {
                        label: "Margin",
                        collapsed: true,
                        fields: {
                            margin_top: {
                                type: "slider",
                                label: "Top",
                                min: 0,
                                max: 100,
                                default: 0,
                                unit: "px",
                            },
                            margin_bottom: {
                                type: "slider",
                                label: "Bottom",
                                min: 0,
                                max: 100,
                                default: 0,
                                unit: "px",
                            },
                            margin_left: {
                                type: "slider",
                                label: "Left (px)",
                                min: 0,
                                max: 200,
                                default: 0,
                                unit: "px",
                            },
                            margin_left_mode: {
                                type: "select",
                                label: "Left Mode",
                                options: [
                                    { value: "px", label: "PX" },
                                    { value: "auto", label: "Auto" },
                                ],
                                default: "px",
                            },
                            margin_right: {
                                type: "slider",
                                label: "Right (px)",
                                min: 0,
                                max: 200,
                                default: 0,
                                unit: "px",
                            },
                            margin_right_mode: {
                                type: "select",
                                label: "Right Mode",
                                options: [
                                    { value: "px", label: "PX" },
                                    { value: "auto", label: "Auto" },
                                ],
                                default: "px",
                            },
                        },
                    },
                },
            },
            layout: {
                label: "Layout",
                groups: {
                    columns: {
                        label: "Columns",
                        collapsed: false,
                        fields: {
                            column_ratio: {
                                type: "select",
                                label: "Column Ratio",
                                options: [
                                    { value: "50-50", label: "50% - 50%" },
                                    { value: "33-67", label: "33% - 67%" },
                                    { value: "67-33", label: "67% - 33%" },
                                    { value: "40-60", label: "40% - 60%" },
                                    { value: "60-40", label: "60% - 40%" },
                                ],
                                default: "50-50",
                            },
                            column_gap: {
                                type: "slider",
                                label: "Gap",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                            vertical_align: {
                                type: "select",
                                label: "Vertical Align",
                                options: ["top", "center", "bottom"],
                                default: "top",
                            },
                        },
                    },
                    size: {
                        label: "Size",
                        collapsed: false,
                        fields: {
                            max_width: {
                                type: "slider",
                                label: "Max Width",
                                min: 300,
                                max: 1600,
                                default: 1200,
                                unit: "px",
                            },
                        },
                    },
                },
            },
            appearance: {
                label: "Appearance",
                fields: {
                    background_color: {
                        type: "color",
                        label: "Background Color",
                        default: "#ffffff",
                    },
                    border_width: {
                        type: "slider",
                        label: "Border Width",
                        min: 0,
                        max: 12,
                        default: 0,
                        unit: "px",
                    },
                    border_color: {
                        type: "color",
                        label: "Border Color",
                        default: "#e5e7eb",
                    },
                    border_radius: {
                        type: "slider",
                        label: "Border Radius",
                        min: 0,
                        max: 60,
                        default: 0,
                        unit: "px",
                    },
                    shadow: {
                        type: "select",
                        label: "Shadow",
                        options: [
                            { value: "none", label: "None" },
                            { value: "sm", label: "Small" },
                            { value: "md", label: "Medium" },
                            { value: "lg", label: "Large" },
                        ],
                        default: "none",
                    },
                },
            },
        },
    },

    section_three_col: {
        name: "Section (3 Columns)",
        type: "section",
        icon: "M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2m0 0V7a2 2 0 012-2h2a2 2 0 012 2",
        tabs: {
            spacing: {
                label: "Spacing",
                groups: {
                    padding: {
                        label: "Padding",
                        collapsed: false,
                        fields: {
                            padding_top: {
                                type: "slider",
                                label: "Top",
                                min: 0,
                                max: 200,
                                default: 60,
                                unit: "px",
                            },
                            padding_bottom: {
                                type: "slider",
                                label: "Bottom",
                                min: 0,
                                max: 200,
                                default: 60,
                                unit: "px",
                            },
                            padding_left: {
                                type: "slider",
                                label: "Left",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                            padding_right: {
                                type: "slider",
                                label: "Right",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                        },
                    },
                    margin: {
                        label: "Margin",
                        collapsed: true,
                        fields: {
                            margin_top: {
                                type: "slider",
                                label: "Top",
                                min: 0,
                                max: 100,
                                default: 0,
                                unit: "px",
                            },
                            margin_bottom: {
                                type: "slider",
                                label: "Bottom",
                                min: 0,
                                max: 100,
                                default: 0,
                                unit: "px",
                            },
                            margin_left: {
                                type: "slider",
                                label: "Left (px)",
                                min: 0,
                                max: 200,
                                default: 0,
                                unit: "px",
                            },
                            margin_left_mode: {
                                type: "select",
                                label: "Left Mode",
                                options: [
                                    { value: "px", label: "PX" },
                                    { value: "auto", label: "Auto" },
                                ],
                                default: "px",
                            },
                            margin_right: {
                                type: "slider",
                                label: "Right (px)",
                                min: 0,
                                max: 200,
                                default: 0,
                                unit: "px",
                            },
                            margin_right_mode: {
                                type: "select",
                                label: "Right Mode",
                                options: [
                                    { value: "px", label: "PX" },
                                    { value: "auto", label: "Auto" },
                                ],
                                default: "px",
                            },
                        },
                    },
                },
            },
            layout: {
                label: "Layout",
                groups: {
                    columns: {
                        label: "Columns",
                        collapsed: false,
                        fields: {
                            column_gap: {
                                type: "slider",
                                label: "Gap",
                                min: 0,
                                max: 100,
                                default: 20,
                                unit: "px",
                            },
                            vertical_align: {
                                type: "select",
                                label: "Vertical Align",
                                options: ["top", "center", "bottom"],
                                default: "top",
                            },
                        },
                    },
                    size: {
                        label: "Size",
                        collapsed: false,
                        fields: {
                            max_width: {
                                type: "slider",
                                label: "Max Width",
                                min: 300,
                                max: 1600,
                                default: 1200,
                                unit: "px",
                            },
                        },
                    },
                },
            },
            appearance: {
                label: "Appearance",
                fields: {
                    background_color: {
                        type: "color",
                        label: "Background Color",
                        default: "#ffffff",
                    },
                    border_width: {
                        type: "slider",
                        label: "Border Width",
                        min: 0,
                        max: 12,
                        default: 0,
                        unit: "px",
                    },
                    border_color: {
                        type: "color",
                        label: "Border Color",
                        default: "#e5e7eb",
                    },
                    border_radius: {
                        type: "slider",
                        label: "Border Radius",
                        min: 0,
                        max: 60,
                        default: 0,
                        unit: "px",
                    },
                    shadow: {
                        type: "select",
                        label: "Shadow",
                        options: [
                            { value: "none", label: "None" },
                            { value: "sm", label: "Small" },
                            { value: "md", label: "Medium" },
                            { value: "lg", label: "Large" },
                        ],
                        default: "none",
                    },
                },
            },
        },
    },

    // ============================================
    // ELEMENTS (Inside Sections)
    // ============================================

    text: {
        name: "Text Block",
        type: "element",
        icon: "M4 6h16M4 12h16M4 18h7",
        tabs: {
            content: {
                label: "Content",
                fields: {
                    content: {
                        type: "textarea",
                        label: "Text Content",
                        default: "Tulis teks anda di sini...",
                    },
                    tag: {
                        type: "select",
                        label: "HTML Tag",
                        options: [
                            { value: "p", label: "Paragraph" },
                            { value: "h1", label: "Heading 1" },
                            { value: "h2", label: "Heading 2" },
                            { value: "h3", label: "Heading 3" },
                            { value: "h4", label: "Heading 4" },
                            { value: "h5", label: "Heading 5" },
                            { value: "h6", label: "Heading 6" },
                        ],
                        default: "p",
                    },
                },
            },
            typography: {
                label: "Typography",
                groups: {
                    text: {
                        label: "Text",
                        collapsed: false,
                        fields: {
                            font_family: {
                                type: "select",
                                label: "Font Family",
                                options: [
                                    { value: "lato", label: "Lato (default)" },
                                    {
                                        value: "montserrat",
                                        label: "Montserrat",
                                    },
                                    {
                                        value: "playfair-display",
                                        label: "Playfair Display",
                                    },
                                    {
                                        value: "great-vibes",
                                        label: "Great Vibes",
                                    },
                                    { value: "open-sans", label: "Open Sans" },
                                ],
                                default: "lato",
                            },
                            color: {
                                type: "color",
                                label: "Color",
                                default: "#000000",
                            },
                            font_size: {
                                type: "slider",
                                label: "Font Size",
                                min: 12,
                                max: 72,
                                default: 16,
                                unit: "px",
                            },
                            line_height: {
                                type: "slider",
                                label: "Line Height",
                                min: 1,
                                max: 3,
                                default: 1.5,
                                unit: "",
                            },
                            letter_spacing: {
                                type: "slider",
                                label: "Letter Spacing",
                                min: -2,
                                max: 5,
                                default: 0,
                                unit: "px",
                            },
                            align: {
                                type: "select",
                                label: "Text Align",
                                options: ["left", "center", "right"],
                                default: "left",
                            },
                        },
                    },
                },
            },
            spacing: {
                label: "Spacing",
                fields: {
                    margin_bottom: {
                        type: "slider",
                        label: "Margin Bottom",
                        min: 0,
                        max: 100,
                        default: 16,
                        unit: "px",
                    },
                },
            },
            advanced: {
                label: "Advanced",
                fields: {
                    element_id: {
                        type: "text",
                        label: "Element ID",
                        default: "",
                    },
                    custom_class: {
                        type: "text",
                        label: "Custom Class",
                        default: "",
                    },
                    custom_css: {
                        type: "textarea",
                        label: "Custom CSS",
                        default: "",
                    },
                },
            },
        },
    },

    image: {
        name: "Image",
        type: "element",
        icon: "M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z",
        tabs: {
            content: {
                label: "Content",
                fields: {
                    src: {
                        type: "text",
                        label: "Image URL",
                        default: "",
                    },
                    alt: {
                        type: "text",
                        label: "Alt Text",
                        default: "",
                    },
                },
            },
            styling: {
                label: "Styling",
                groups: {
                    sizing: {
                        label: "Sizing",
                        collapsed: false,
                        fields: {
                            width: {
                                type: "slider",
                                label: "Width",
                                min: 100,
                                max: 100,
                                default: 100,
                                unit: "%",
                            },
                        },
                    },
                    corners: {
                        label: "Corners",
                        collapsed: true,
                        fields: {
                            border_radius: {
                                type: "slider",
                                label: "Border Radius",
                                min: 0,
                                max: 50,
                                default: 0,
                                unit: "px",
                            },
                            border_width: {
                                type: "slider",
                                label: "Border Width",
                                min: 0,
                                max: 12,
                                default: 0,
                                unit: "px",
                            },
                            border_color: {
                                type: "color",
                                label: "Border Color",
                                default: "#e5e7eb",
                            },
                            shadow: {
                                type: "select",
                                label: "Shadow",
                                options: [
                                    { value: "none", label: "None" },
                                    { value: "sm", label: "Small" },
                                    { value: "md", label: "Medium" },
                                    { value: "lg", label: "Large" },
                                ],
                                default: "none",
                            },
                        },
                    },
                },
            },
            spacing: {
                label: "Spacing",
                fields: {
                    margin_bottom: {
                        type: "slider",
                        label: "Margin Bottom",
                        min: 0,
                        max: 100,
                        default: 16,
                        unit: "px",
                    },
                },
            },
            advanced: {
                label: "Advanced",
                fields: {
                    element_id: {
                        type: "text",
                        label: "Element ID",
                        default: "",
                    },
                    custom_class: {
                        type: "text",
                        label: "Custom Class",
                        default: "",
                    },
                    custom_css: {
                        type: "textarea",
                        label: "Custom CSS",
                        default: "",
                    },
                },
            },
        },
    },

    button: {
        name: "Button",
        type: "element",
        icon: "M14 5l7 7m0 0l-7 7m7-7H3",
        tabs: {
            content: {
                label: "Content",
                fields: {
                    text: {
                        type: "text",
                        label: "Button Text",
                        default: "Click Me",
                    },
                    url: {
                        type: "text",
                        label: "Link URL",
                        default: "#",
                    },
                },
            },
            styling: {
                label: "Styling",
                groups: {
                    appearance: {
                        label: "Appearance",
                        collapsed: false,
                        fields: {
                            variant: {
                                type: "select",
                                label: "Style",
                                options: [
                                    { value: "primary", label: "Primary" },
                                    { value: "secondary", label: "Secondary" },
                                    { value: "outline", label: "Outline" },
                                ],
                                default: "primary",
                            },
                            size: {
                                type: "select",
                                label: "Size",
                                options: [
                                    { value: "small", label: "Small" },
                                    { value: "medium", label: "Medium" },
                                    { value: "large", label: "Large" },
                                ],
                                default: "medium",
                            },
                            background_color: {
                                type: "color",
                                label: "Background Color",
                                default: "#d4af37",
                            },
                            text_color: {
                                type: "color",
                                label: "Text Color",
                                default: "#ffffff",
                            },
                            border_radius: {
                                type: "slider",
                                label: "Border Radius",
                                min: 0,
                                max: 60,
                                default: 8,
                                unit: "px",
                            },
                            border_width: {
                                type: "slider",
                                label: "Border Width",
                                min: 0,
                                max: 12,
                                default: 0,
                                unit: "px",
                            },
                            border_color: {
                                type: "color",
                                label: "Border Color",
                                default: "#d4af37",
                            },
                            shadow: {
                                type: "select",
                                label: "Shadow",
                                options: [
                                    { value: "none", label: "None" },
                                    { value: "sm", label: "Small" },
                                    { value: "md", label: "Medium" },
                                    { value: "lg", label: "Large" },
                                ],
                                default: "none",
                            },
                        },
                    },
                    layout: {
                        label: "Layout",
                        collapsed: true,
                        fields: {
                            align: {
                                type: "select",
                                label: "Alignment",
                                options: [
                                    { value: "left", label: "Left" },
                                    { value: "center", label: "Center" },
                                    { value: "right", label: "Right" },
                                ],
                                default: "center",
                            },
                        },
                    },
                },
            },
            advanced: {
                label: "Advanced",
                fields: {
                    element_id: {
                        type: "text",
                        label: "Element ID",
                        default: "",
                    },
                    custom_class: {
                        type: "text",
                        label: "Custom Class",
                        default: "",
                    },
                    custom_css: {
                        type: "textarea",
                        label: "Custom CSS",
                        default: "",
                    },
                },
            },
        },
    },

    divider: {
        name: "Divider",
        type: "element",
        icon: "M4 12h16",
        tabs: {
            styling: {
                label: "Styling",
                groups: {
                    appearance: {
                        label: "Appearance",
                        collapsed: false,
                        fields: {
                            height: {
                                type: "slider",
                                label: "Height",
                                min: 1,
                                max: 10,
                                default: 1,
                                unit: "px",
                            },
                            color: {
                                type: "color",
                                label: "Color",
                                default: "#e5e7eb",
                            },
                        },
                    },
                },
            },
            spacing: {
                label: "Spacing",
                groups: {
                    margins: {
                        label: "Margins",
                        collapsed: false,
                        fields: {
                            margin_top: {
                                type: "slider",
                                label: "Top",
                                min: 0,
                                max: 100,
                                default: 24,
                                unit: "px",
                            },
                            margin_bottom: {
                                type: "slider",
                                label: "Bottom",
                                min: 0,
                                max: 100,
                                default: 24,
                                unit: "px",
                            },
                        },
                    },
                },
            },
            advanced: {
                label: "Advanced",
                fields: {
                    element_id: {
                        type: "text",
                        label: "Element ID",
                        default: "",
                    },
                    custom_class: {
                        type: "text",
                        label: "Custom Class",
                        default: "",
                    },
                    custom_css: {
                        type: "textarea",
                        label: "Custom CSS",
                        default: "",
                    },
                },
            },
        },
    },

    spacer: {
        name: "Spacer",
        type: "element",
        icon: "M4 12h16",
        tabs: {
            sizing: {
                label: "Sizing",
                fields: {
                    height: {
                        type: "slider",
                        label: "Height",
                        min: 10,
                        max: 200,
                        default: 50,
                        unit: "px",
                    },
                },
            },
            advanced: {
                label: "Advanced",
                fields: {
                    element_id: {
                        type: "text",
                        label: "Element ID",
                        default: "",
                    },
                    custom_class: {
                        type: "text",
                        label: "Custom Class",
                        default: "",
                    },
                    custom_css: {
                        type: "textarea",
                        label: "Custom CSS",
                        default: "",
                    },
                },
            },
        },
    },
};

// Get all sections (components with type: 'section')
export const sectionSchemas = Object.entries(componentSchemas)
    .filter(([_, schema]) => schema.type === "section")
    .reduce((acc, [key, value]) => ({ ...acc, [key]: value }), {});

// Get all elements (components with type: 'element')
export const elementSchemas = Object.entries(componentSchemas)
    .filter(([_, schema]) => schema.type === "element")
    .reduce((acc, [key, value]) => ({ ...acc, [key]: value }), {});
