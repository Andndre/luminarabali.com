import { useState } from "react";
import { sectionSchemas, elementSchemas } from "../services/componentSchemas";
import { useTemplateStore } from "../stores/templateStore";
import DraggableComponent from "./DraggableComponent";
import { ComponentSchema } from "../types";

export default function Sidebar() {
    const [sectionsOpen, setSectionsOpen] = useState(true);
    const [elementsOpen, setElementsOpen] = useState(true);
    const { selectedSection, addSection, addElement } = useTemplateStore();

    const handleComponentClick = (type: string) => {
        const sectionSchema =
            sectionSchemas[type as keyof typeof sectionSchemas];
        const elementSchema =
            elementSchemas[type as keyof typeof elementSchemas];
        const schema = sectionSchema || elementSchema;

        if (!schema) return;

        const schemaType = (schema as ComponentSchema).type;

        if (schemaType === "section") {
            // Add section at top level
            addSection(type as any, null);
        } else if (schemaType === "element") {
            if (!selectedSection) {
                alert(
                    "Please select a section first before adding an element.",
                );
                return;
            }
            // Check if selected section is actually a section container
            const selectedSchema =
                sectionSchemas[
                    selectedSection.section_type as keyof typeof sectionSchemas
                ];
            if (!selectedSchema) {
                alert(
                    "Please select a section container before adding an element.",
                );
                return;
            }
            // Add element to the selected section (column 0 by default)
            addElement(type as any, selectedSection.id, 0);
        }
    };

    return (
        <div className="w-64 bg-white border-r overflow-y-auto">
            <div className="p-4 border-b sticky top-0 bg-white z-10">
                <h2 className="font-semibold text-gray-900">Components</h2>
                <p className="text-xs text-gray-500 mt-1">
                    Click to add or drag to canvas
                </p>
            </div>

            <div className="p-3 space-y-2">
                {/* Sections Category */}
                <div className="border border-gray-200 rounded-lg overflow-hidden">
                    <button
                        onClick={() => setSectionsOpen(!sectionsOpen)}
                        className="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition"
                    >
                        <div className="flex items-center gap-2">
                            <svg
                                className="w-4 h-4 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"
                                />
                            </svg>
                            <span className="font-medium text-sm text-gray-900">
                                Sections
                            </span>
                        </div>
                        <svg
                            className={`w-4 h-4 text-gray-600 transition-transform ${sectionsOpen ? "rotate-180" : ""}`}
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    {sectionsOpen && (
                        <div className="p-2 space-y-1">
                            {Object.entries(sectionSchemas).map(
                                ([type, schema]) => (
                                    <DraggableComponent
                                        key={type}
                                        type={type}
                                        schema={schema as ComponentSchema}
                                        onClick={() =>
                                            handleComponentClick(type)
                                        }
                                    />
                                ),
                            )}
                        </div>
                    )}
                </div>

                {/* Elements Category */}
                <div className="border border-gray-200 rounded-lg overflow-hidden">
                    <button
                        onClick={() => setElementsOpen(!elementsOpen)}
                        className="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition"
                    >
                        <div className="flex items-center gap-2">
                            <svg
                                className="w-4 h-4 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M4 6h16M4 12h16M4 18h7"
                                />
                            </svg>
                            <span className="font-medium text-sm text-gray-900">
                                Elements
                            </span>
                        </div>
                        <svg
                            className={`w-4 h-4 text-gray-600 transition-transform ${elementsOpen ? "rotate-180" : ""}`}
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    {elementsOpen && (
                        <div className="p-2 space-y-1">
                            {Object.entries(elementSchemas).map(
                                ([type, schema]) => (
                                    <DraggableComponent
                                        key={type}
                                        type={type}
                                        schema={schema as ComponentSchema}
                                        onClick={() =>
                                            handleComponentClick(type)
                                        }
                                    />
                                ),
                            )}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
