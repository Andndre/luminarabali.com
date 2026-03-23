// Template Editor Types

export type ComponentType = "section" | "element";

export type EditorMode = "template" | "invitation";

export type SectionType =
    | "section_one_col"
    | "section_two_col"
    | "section_three_col"
    | "hero"
    | "text"
    | "image"
    | "button"
    | "divider"
    | "spacer"
    | "countdown"
    | "gallery"
    | "map"
    | "music"
    | "rsvp"
    | "video";

export type FieldType =
    | "text"
    | "textarea"
    | "select"
    | "number"
    | "slider"
    | "color"
    | "image";

export interface FieldOption {
    value: string;
    label: string;
}

export interface FieldSchema {
    type: FieldType;
    label?: string;
    default?: any;
    min?: number;
    max?: number;
    unit?: string;
    options?: (FieldOption | string)[];
}

export interface FieldGroup {
    label: string;
    collapsed?: boolean; // default: false (open)
    fields: Record<string, FieldSchema>;
}

export interface TabSchema {
    label: string;
    // Either flat fields OR grouped fields with accordion
    fields?: Record<string, FieldSchema>;
    groups?: Record<string, FieldGroup>;
}

export interface ComponentSchema {
    name: string;
    type: ComponentType;
    icon?: string;
    // NEW: Tab-based structure with accordion groups support
    // Backward compatible: if only 'fields' is provided, will render in default tab
    fields?: Record<string, FieldSchema>; // Backward compatible
    tabs?: Record<string, TabSchema>; // NEW: Tab structure
}

export interface TemplateSection {
    id: string;
    template_id?: number;
    page_id?: number | null;
    parent_id?: number | string | null;
    section_type: SectionType;
    order_index: number;
    props: Record<string, any>;
    custom_css?: string;
    is_visible?: boolean;
    children?: TemplateSection[];
}

export interface Template {
    id: number;
    name: string;
    slug: string;
    thumbnail?: string;
    description?: string;
    category?: string;
    is_active: boolean;
    created_by?: number;
    published_status?: "draft" | "published" | "archived";
    created_at?: string;
    updated_at?: string;
}

export interface TemplateData {
    template: Template;
    sections: TemplateSection[];
}

export interface SaveSectionRequest {
    template_id?: number;
    page_id?: number;
    sections: TemplateSection[];
}

export interface SaveSectionResponse {
    success: boolean;
    message: string;
    sections: Array<{ temp_id: string; id: number }>;
}

export type ViewportSize = "mobile" | "tablet" | "desktop";

// Drag & Drop Types
export interface DragItem {
    type: SectionType;
    componentType: ComponentType;
}

export interface DropResult {
    sectionId: string | null;
    columnIndex?: number;
    index: number;
}
