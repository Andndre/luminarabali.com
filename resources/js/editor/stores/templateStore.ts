import { create } from "zustand";
import { immer } from "zustand/middleware/immer";
import {
    EditorMode,
    Template,
    TemplateSection,
    SectionType,
    ComponentType,
    ViewportSize,
} from "../types";
import { templateEditorService } from "../services/api";

interface EditorState {
    // Template data
    templateId: number;
    editorMode: EditorMode;
    template: Template | null;
    sections: TemplateSection[];
    allSections: TemplateSection[]; // Flat array for saving

    // UI state
    selectedSection: TemplateSection | null;
    currentViewport: ViewportSize;
    loading: boolean;
    saving: boolean;
    hasUnsavedChanges: boolean;
    lastSaved: Date | null;
    currentTab: string;

    // Drag & drop state
    draggedComponent: {
        type: SectionType;
        componentType: ComponentType;
    } | null;

    // Actions
    setTemplateId: (id: number) => void;
    setEditorMode: (mode: EditorMode) => void;
    setTemplate: (template: Template) => void;
    setSections: (sections: TemplateSection[]) => void;
    setAllSections: (sections: TemplateSection[]) => void;
    setSelectedSection: (section: TemplateSection | null) => void;
    setCurrentViewport: (viewport: ViewportSize) => void;
    setLoading: (loading: boolean) => void;
    setSaving: (saving: boolean) => void;
    setHasUnsavedChanges: (hasChanges: boolean) => void;
    setLastSaved: (date: Date | null) => void;
    setCurrentTab: (tab: string) => void;
    setDraggedComponent: (
        dragged: { type: SectionType; componentType: ComponentType } | null,
    ) => void;

    // Section actions
    addSection: (type: SectionType, parentSectionId?: string | null) => void;
    updateSection: (
        sectionId: string,
        updates: Partial<TemplateSection>,
    ) => void;
    deleteSection: (sectionId: string) => void;
    duplicateSection: (sectionId: string) => void;
    moveSection: (sectionId: string, direction: "up" | "down") => void;
    moveSectionTo: (fromIndex: number, toIndex: number) => void;

    // Element actions (children of sections)
    addElement: (
        type: SectionType,
        parentSectionId: string,
        columnIndex?: number,
    ) => void;
    updateElement: (
        parentId: string,
        elementId: string,
        updates: Partial<TemplateSection>,
    ) => void;
    deleteElement: (parentId: string, elementId: string) => void;
    moveElement: (
        elementId: string,
        newParentId: string | null,
        newIndex: number,
    ) => void;

    // Tree helpers
    buildSectionTree: () => void;
    flattenSections: () => TemplateSection[];
    findSection: (
        sections: TemplateSection[],
        id: string,
    ) => TemplateSection | null;

    // Save
    saveSections: () => Promise<void>;
}

export const useTemplateStore = create<EditorState>()(
    immer((set, get) => ({
        // Initial state
        templateId: 0,
        editorMode: "template",
        template: null,
        sections: [],
        allSections: [],
        selectedSection: null,
        currentViewport: "desktop",
        loading: true,
        saving: false,
        hasUnsavedChanges: false,
        lastSaved: null,
        currentTab: "Settings",
        draggedComponent: null,

        // Setters
        setTemplateId: (id) => set({ templateId: id }),
        setEditorMode: (mode) => set({ editorMode: mode }),
        setTemplate: (template) => set({ template }),
        setSections: (sections) => set({ sections }),
        setAllSections: (allSections) => set({ allSections }),
        setSelectedSection: (section) => set({ selectedSection: section }),
        setCurrentViewport: (viewport) => set({ currentViewport: viewport }),
        setLoading: (loading) => set({ loading }),
        setSaving: (saving) => set({ saving }),
        setHasUnsavedChanges: (hasChanges) =>
            set({ hasUnsavedChanges: hasChanges }),
        setLastSaved: (date) => set({ lastSaved: date }),
        setCurrentTab: (tab) => set({ currentTab: tab }),
        setDraggedComponent: (dragged) => set({ draggedComponent: dragged }),

        // Build tree from flat array
        buildSectionTree: () => {
            const { allSections } = get();
            const grouped: Record<string, TemplateSection[]> = { root: [] };

            allSections.forEach((section) => {
                const parentId = section.parent_id || "root";
                if (!grouped[parentId]) {
                    grouped[parentId] = [];
                }
                grouped[parentId].push(section);
            });

            const buildChildren = (parentId: string): TemplateSection[] => {
                const children = grouped[parentId] || [];
                return children.map((child) => ({
                    ...child,
                    children: buildChildren(child.id),
                }));
            };

            set({ sections: buildChildren("root") });
        },

        // Flatten tree to array for saving
        flattenSections: () => {
            const result: TemplateSection[] = [];

            const flatten = (sections: TemplateSection[]) => {
                sections.forEach((section) => {
                    result.push({
                        id: section.id,
                        parent_id: section.parent_id || null,
                        section_type: section.section_type,
                        order_index: section.order_index,
                        props: section.props,
                        custom_css: section.custom_css,
                        is_visible: section.is_visible,
                    });
                    if (section.children && section.children.length > 0) {
                        flatten(section.children);
                    }
                });
            };

            flatten(get().sections);
            return result;
        },

        // Find section in tree
        findSection: (sections, id) => {
            for (const section of sections) {
                if (section.id === id) return section;
                if (section.children && section.children.length > 0) {
                    const found = get().findSection(section.children, id);
                    if (found) return found;
                }
            }
            return null;
        },

        // Add section
        addSection: (type, parentSectionId) => {
            const newSection: TemplateSection = {
                id: `temp-${Date.now()}`,
                section_type: type,
                order_index: 0,
                props: {},
                children: [],
            };

            set((state) => {
                if (parentSectionId) {
                    // Add as element inside section
                    const parent = get().findSection(
                        state.sections,
                        parentSectionId,
                    );
                    if (parent) {
                        if (!parent.children) parent.children = [];
                        newSection.order_index = parent.children.length;
                        newSection.parent_id = parentSectionId;
                        parent.children.push(newSection);
                    }
                } else {
                    // Add as top-level section
                    newSection.order_index = state.sections.length;
                    state.sections.push(newSection);
                }
                state.selectedSection = newSection;
                state.hasUnsavedChanges = true;
            });
        },

        // Update section
        updateSection: (sectionId, updates) => {
            set((state) => {
                const section = get().findSection(state.sections, sectionId);
                if (section) {
                    const nextProps =
                        updates.props !== undefined
                            ? JSON.stringify(updates.props)
                            : null;
                    const currentProps =
                        updates.props !== undefined
                            ? JSON.stringify(section.props)
                            : null;

                    const hasChanged = Object.entries(updates).some(
                        ([key, value]) => {
                            if (key === "props") {
                                return nextProps !== currentProps;
                            }

                            return (section as any)[key] !== value;
                        },
                    );

                    if (!hasChanged) {
                        return;
                    }

                    Object.assign(section, updates);
                    state.hasUnsavedChanges = true;
                }
            });
        },

        // Delete section
        deleteSection: (sectionId) => {
            set((state) => {
                const deleteFrom = (list: TemplateSection[]) => {
                    const index = list.findIndex((s) => s.id === sectionId);
                    if (index !== -1) {
                        list.splice(index, 1);
                        return true;
                    }
                    for (const section of list) {
                        if (section.children && section.children.length > 0) {
                            if (deleteFrom(section.children)) return true;
                        }
                    }
                    return false;
                };

                deleteFrom(state.sections);

                if (state.selectedSection?.id === sectionId) {
                    state.selectedSection = null;
                }
                state.hasUnsavedChanges = true;
            });
        },

        // Duplicate section
        duplicateSection: (sectionId) => {
            const { sections } = get();
            const section = get().findSection(sections, sectionId);
            if (!section) return;

            const duplicate: TemplateSection = {
                ...JSON.parse(JSON.stringify(section)),
                id: `temp-${Date.now()}`,
            };

            set((state) => {
                const parent = section.parent_id
                    ? get().findSection(
                          state.sections,
                          section.parent_id as string,
                      )
                    : null;

                if (parent && parent.children) {
                    const index = parent.children.findIndex(
                        (s) => s.id === sectionId,
                    );
                    parent.children.splice(index + 1, 0, duplicate);
                    // Update order_index
                    parent.children.forEach((s, i) => (s.order_index = i));
                } else {
                    const index = state.sections.findIndex(
                        (s) => s.id === sectionId,
                    );
                    state.sections.splice(index + 1, 0, duplicate);
                    // Update order_index
                    state.sections.forEach((s, i) => (s.order_index = i));
                }

                state.selectedSection = duplicate;
                state.hasUnsavedChanges = true;
            });
        },

        // Move section up/down
        moveSection: (sectionId, direction) => {
            set((state) => {
                const index = state.sections.findIndex(
                    (s) => s.id === sectionId,
                );
                if (index === -1) return;

                const newIndex = direction === "up" ? index - 1 : index + 1;
                if (newIndex < 0 || newIndex >= state.sections.length) return;

                const [removed] = state.sections.splice(index, 1);
                state.sections.splice(newIndex, 0, removed);

                state.sections.forEach((s, i) => (s.order_index = i));
                state.hasUnsavedChanges = true;
            });
        },

        // Move section to specific index (for drag & drop reordering)
        moveSectionTo: (fromIndex, toIndex) => {
            set((state) => {
                if (fromIndex < 0 || fromIndex >= state.sections.length) return;
                if (toIndex < 0 || toIndex >= state.sections.length) return;

                const [removed] = state.sections.splice(fromIndex, 1);
                state.sections.splice(toIndex, 0, removed);

                state.sections.forEach((s, i) => (s.order_index = i));
                state.hasUnsavedChanges = true;
            });
        },

        // Add element to section
        addElement: (type, parentSectionId, columnIndex = 0) => {
            const newSection: TemplateSection = {
                id: `temp-${Date.now()}`,
                section_type: type,
                order_index: 0,
                props: { column_index: columnIndex }, // Store column index in props
                children: [],
            };

            set((state) => {
                const parent = get().findSection(
                    state.sections,
                    parentSectionId,
                );
                if (parent) {
                    if (!parent.children) parent.children = [];
                    newSection.order_index = parent.children.length;
                    newSection.parent_id = parentSectionId;
                    parent.children.push(newSection);
                    state.selectedSection = newSection;
                    state.hasUnsavedChanges = true;
                }
            });
        },

        // Update element
        updateElement: (parentId, elementId, updates) => {
            const { sections } = get();
            const parent = get().findSection(sections, parentId);
            if (!parent || !parent.children) return;

            set((state) => {
                const p = get().findSection(state.sections, parentId);
                if (p && p.children) {
                    const element = p.children.find((c) => c.id === elementId);
                    if (element) {
                        const nextProps =
                            updates.props !== undefined
                                ? JSON.stringify(updates.props)
                                : null;
                        const currentProps =
                            updates.props !== undefined
                                ? JSON.stringify(element.props)
                                : null;

                        const hasChanged = Object.entries(updates).some(
                            ([key, value]) => {
                                if (key === "props") {
                                    return nextProps !== currentProps;
                                }

                                return (element as any)[key] !== value;
                            },
                        );

                        if (!hasChanged) {
                            return;
                        }

                        Object.assign(element, updates);
                        state.hasUnsavedChanges = true;
                    }
                }
            });
        },

        // Delete element
        deleteElement: (parentId, elementId) => {
            set((state) => {
                const parent = get().findSection(state.sections, parentId);
                if (parent && parent.children) {
                    const index = parent.children.findIndex(
                        (c) => c.id === elementId,
                    );
                    if (index !== -1) {
                        parent.children.splice(index, 1);
                        if (state.selectedSection?.id === elementId) {
                            state.selectedSection = parent;
                        }
                        state.hasUnsavedChanges = true;
                    }
                }
            });
        },

        // Move element between sections/columns
        moveElement: (elementId, newParentId, newIndex) => {
            // First, find and extract the element
            let extractedElement: TemplateSection | null = null;

            const extractElement = (list: TemplateSection[]): boolean => {
                for (const section of list) {
                    if (section.children) {
                        const elemIndex = section.children.findIndex(
                            (c) => c.id === elementId,
                        );
                        if (elemIndex !== -1) {
                            extractedElement = section.children.splice(
                                elemIndex,
                                1,
                            )[0];
                            return true;
                        }
                        if (extractElement(section.children)) return true;
                    }
                }
                return false;
            };

            // Get current sections to extract from
            const currentSections = get().sections;
            extractElement(currentSections);

            if (!extractedElement) return;

            // Now update the state with the element moved
            set((state) => {
                const element = extractedElement!;

                if (newParentId) {
                    const newParent = get().findSection(
                        state.sections,
                        newParentId,
                    );
                    if (newParent) {
                        if (!newParent.children) newParent.children = [];
                        (element as any).parent_id = newParentId;
                        (element as any).order_index = newIndex;
                        newParent.children.splice(newIndex, 0, element);
                    }
                } else {
                    (element as any).parent_id = null;
                    (element as any).order_index = newIndex;
                    state.sections.splice(newIndex, 0, element);
                }

                state.hasUnsavedChanges = true;
            });
        },

        // Save sections to backend
        saveSections: async () => {
            const { templateId, editorMode, flattenSections, template } = get();
            const sections = flattenSections();
            const globalCustomCss = template?.global_custom_css ?? "";

            set({ saving: true });

            try {
                const response = await templateEditorService.saveSections(
                    templateId,
                    sections,
                    globalCustomCss,
                    editorMode,
                );

                const idMapping = new Map(
                    (response.sections || []).map((item) => [
                        item.temp_id,
                        String(item.id),
                    ]),
                );

                if (idMapping.size > 0) {
                    const remapTree = (
                        nodes: TemplateSection[],
                    ): TemplateSection[] => {
                        return nodes.map((node) => {
                            const nextId = idMapping.get(node.id) || node.id;
                            const nextParent =
                                typeof node.parent_id === "string"
                                    ? idMapping.get(node.parent_id) ||
                                      node.parent_id
                                    : (node.parent_id ?? null);

                            return {
                                ...node,
                                id: nextId,
                                parent_id: nextParent,
                                children: node.children
                                    ? remapTree(node.children)
                                    : [],
                            };
                        });
                    };

                    set((state) => {
                        state.sections = remapTree(state.sections);

                        if (state.selectedSection) {
                            const selectedId =
                                idMapping.get(state.selectedSection.id) ||
                                state.selectedSection.id;
                            state.selectedSection = get().findSection(
                                state.sections,
                                selectedId,
                            );
                        }
                    });
                }

                set({
                    hasUnsavedChanges: false,
                    lastSaved: new Date(),
                });
            } catch (error) {
                console.error("Failed to save sections:", error);
                throw error;
            } finally {
                set({ saving: false });
            }
        },
    })),
);
