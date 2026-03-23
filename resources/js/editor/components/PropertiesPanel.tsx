import { useState, useEffect } from "react";
import { useTemplateStore } from "../stores/templateStore";
import { componentSchemas } from "../services/componentSchemas";
import FieldRenderer from "./FieldRenderer";
import AccordionGroup from "./AccordionGroup";
import { TabSchema } from "../types";

export default function PropertiesPanel() {
    const {
        editorMode,
        template,
        selectedSection,
        currentTab,
        setCurrentTab,
        setTemplate,
        setHasUnsavedChanges,
        updateSection,
    } = useTemplateStore();
    const [localProps, setLocalProps] = useState<Record<string, any>>({});
    const [globalCustomCss, setGlobalCustomCss] = useState("");

    const schema = selectedSection
        ? componentSchemas[selectedSection.section_type]
        : null;
    const isSection = schema?.type === "section";
    const isElement = schema?.type === "element";

    // Update local props when selection changes
    useEffect(() => {
        if (selectedSection) {
            setLocalProps(selectedSection.props || {});
        }
    }, [selectedSection]);

    useEffect(() => {
        setGlobalCustomCss(template?.global_custom_css || "");
    }, [template]);

    const handlePropChange = (key: string, value: any) => {
        const newProps = { ...localProps, [key]: value };
        setLocalProps(newProps);
        if (selectedSection) {
            updateSection(selectedSection.id, { props: newProps });
        }
    };

    const handleGlobalCustomCssChange = (value: string) => {
        setGlobalCustomCss(value);
        if (template) {
            setTemplate({
                ...template,
                global_custom_css: value,
            });
            setHasUnsavedChanges(true);
        }
    };

    // Get tabs - support new structure or backward compatible flat fields
    const getTabs = (): Record<string, TabSchema> => {
        if (schema?.tabs) {
            return schema.tabs;
        }
        // Backward compatibility: if old schema.fields exists, wrap it in default tab
        if (schema?.fields) {
            return {
                settings: {
                    label: "Settings",
                    fields: schema.fields,
                },
            };
        }
        return {};
    };

    const tabs = getTabs();
    const tabIds = Object.keys(tabs);
    const activeTab =
        currentTab && tabIds.includes(currentTab) ? currentTab : tabIds[0];

    const renderTabContent = (tab: TabSchema) => {
        // If tab has groups, render accordion groups
        if (tab.groups) {
            return (
                <div>
                    {Object.entries(tab.groups).map(([groupId, group]) => (
                        <AccordionGroup
                            key={groupId}
                            group={group}
                            groupId={groupId}
                            localProps={localProps}
                            onPropChange={handlePropChange}
                        />
                    ))}
                </div>
            );
        }

        // Otherwise render flat fields
        if (tab.fields) {
            return (
                <div className="space-y-4">
                    {Object.entries(tab.fields).map(([fieldKey, field]) => (
                        <FieldRenderer
                            key={fieldKey}
                            fieldKey={fieldKey}
                            field={field}
                            value={localProps[fieldKey] ?? field.default}
                            onChange={(value) =>
                                handlePropChange(fieldKey, value)
                            }
                        />
                    ))}
                </div>
            );
        }

        return (
            <div className="text-sm text-gray-500 text-center py-4">
                No properties in this tab.
            </div>
        );
    };

    return (
        <div className="w-80 bg-white border-l overflow-y-auto">
            {/* No Selection - Editor Properties */}
            {!selectedSection && (
                <div className="p-6">
                    <h3 className="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg
                            className="w-5 h-5"
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
                        {editorMode === "invitation"
                            ? "Invitation Settings"
                            : "Template Settings"}
                    </h3>
                    <div className="space-y-4">
                        <p className="text-sm text-gray-500">
                            Click on canvas background or select a
                            section/element to see its properties.
                        </p>

                        <div className="border rounded-lg p-3 bg-gray-50">
                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                Global Custom CSS
                            </label>
                            <textarea
                                value={globalCustomCss}
                                onChange={(e) =>
                                    handleGlobalCustomCssChange(e.target.value)
                                }
                                rows={8}
                                placeholder={
                                    "Contoh:\n.text-highlight { color: #f59e0b; }\n#hero-title { letter-spacing: 2px; }"
                                }
                                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent resize-y"
                            />
                            <p className="mt-2 text-xs text-gray-500">
                                Applied to all sections/elements in preview and
                                public invitation.
                            </p>
                        </div>

                        <div className="border-t pt-4 mt-4">
                            <h4 className="text-sm font-medium text-gray-900 mb-3">
                                {editorMode === "invitation"
                                    ? "Invitation Information"
                                    : "Template Information"}
                            </h4>
                            <div className="text-sm text-gray-600 space-y-1">
                                <p>
                                    <strong>Name:</strong>{" "}
                                    {template?.name || "-"}
                                </p>
                                <p>
                                    <strong>Status:</strong>{" "}
                                    {template?.is_active ? (
                                        <span className="text-green-600">
                                            {editorMode === "invitation"
                                                ? "Published"
                                                : "Active"}
                                        </span>
                                    ) : (
                                        <span className="text-gray-500">
                                            Draft
                                        </span>
                                    )}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Section or Element Selected */}
            {selectedSection && schema && (
                <>
                    {/* Header with type indicator */}
                    <div className="px-4 py-3 border-b bg-gray-50">
                        <div className="flex items-center justify-between">
                            <h3 className="font-semibold text-gray-900">
                                {schema.name}
                            </h3>
                            <span
                                className={`text-xs px-2 py-1 rounded ${
                                    isSection
                                        ? "bg-blue-100 text-blue-700"
                                        : isElement
                                          ? "bg-green-100 text-green-700"
                                          : "bg-gray-100 text-gray-700"
                                }`}
                            >
                                {isSection
                                    ? "Section"
                                    : isElement
                                      ? "Element"
                                      : "Unknown"}
                            </span>
                        </div>
                    </div>

                    {/* Tabs */}
                    <div className="flex border-b sticky top-0 bg-white z-10 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300">
                        {tabIds.map((tabId) => (
                            <button
                                key={tabId}
                                onClick={() => setCurrentTab(tabId)}
                                className={`property-tab px-4 py-3 text-sm font-medium border-b-2 transition-colors flex-shrink-0 whitespace-nowrap ${
                                    activeTab === tabId
                                        ? "border-yellow-500 text-yellow-600"
                                        : "border-transparent text-gray-500 hover:text-gray-700"
                                }`}
                            >
                                {tabs[tabId].label}
                            </button>
                        ))}
                    </div>

                    {/* Tab Content */}
                    <div className="p-4">
                        {activeTab && tabs[activeTab]
                            ? renderTabContent(tabs[activeTab])
                            : null}
                    </div>
                </>
            )}

            {/* Unknown Component Type */}
            {selectedSection && !schema && (
                <div className="p-6 text-center">
                    <svg
                        className="w-12 h-12 mx-auto text-gray-400 mb-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <h3 className="font-medium text-gray-900 mb-2">
                        Unknown Component
                    </h3>
                    <p className="text-sm text-gray-500">
                        Component type{" "}
                        <code>{selectedSection.section_type}</code> is not
                        defined in schemas.
                    </p>
                </div>
            )}
        </div>
    );
}
