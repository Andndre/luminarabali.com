import { useTemplateStore } from "../stores/templateStore";
import { templateEditorService } from "../services/api";

export default function Header() {
    const {
        editorMode,
        templateId,
        template,
        setTemplate,
        hasUnsavedChanges,
        saving,
        lastSaved,
        currentViewport,
        setCurrentViewport,
        saveSections,
    } = useTemplateStore();

    const formatTime = (date: Date | null) => {
        if (!date) return "";
        return new Intl.DateTimeFormat("id-ID", {
            hour: "2-digit",
            minute: "2-digit",
        }).format(date);
    };

    const handleViewportChange = (
        viewport: "mobile" | "tablet" | "desktop",
    ) => {
        setCurrentViewport(viewport);
    };

    const handleSave = async () => {
        try {
            await saveSections();
        } catch (error) {
            console.error("Failed to save:", error);
            alert("Failed to save. Please try again.");
        }
    };

    const handlePreview = () => {
        if (template?.id) {
            const previewUrl =
                editorMode === "invitation"
                    ? `/invitation/${template.slug}`
                    : `/admin/templates/${template.id}/preview`;
            window.open(previewUrl, "_blank");
        }
    };

    const handlePublish = async () => {
        if (!templateId) return;

        try {
            await templateEditorService.publishTemplate(templateId, editorMode);
            if (template) {
                setTemplate({
                    ...template,
                    is_active: true,
                    published_status: "published",
                });
            }
        } catch (error) {
            console.error("Failed to publish:", error);
            alert("Failed to publish. Please try again.");
        }
    };

    return (
        <header className="bg-white border-b px-6 py-3 flex items-center justify-between">
            <div className="flex items-center gap-4">
                <h1 className="text-lg font-semibold text-gray-900">
                    {template?.name ||
                        (editorMode === "invitation"
                            ? "Invitation Editor"
                            : "Template Editor")}
                </h1>

                {/* Viewport Switcher */}
                <div className="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button
                        onClick={() => handleViewportChange("mobile")}
                        className={`p-2 rounded transition ${
                            currentViewport === "mobile"
                                ? "bg-white shadow text-yellow-600"
                                : "text-gray-500 hover:text-gray-700"
                        }`}
                        title="Mobile View"
                    >
                        <svg
                            className="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"
                            />
                        </svg>
                    </button>
                    <button
                        onClick={() => handleViewportChange("tablet")}
                        className={`p-2 rounded transition ${
                            currentViewport === "tablet"
                                ? "bg-white shadow text-yellow-600"
                                : "text-gray-500 hover:text-gray-700"
                        }`}
                        title="Tablet View"
                    >
                        <svg
                            className="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                            />
                        </svg>
                    </button>
                    <button
                        onClick={() => handleViewportChange("desktop")}
                        className={`p-2 rounded transition ${
                            currentViewport === "desktop"
                                ? "bg-white shadow text-yellow-600"
                                : "text-gray-500 hover:text-gray-700"
                        }`}
                        title="Desktop View"
                    >
                        <svg
                            className="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div className="flex items-center gap-4">
                {/* Unsaved Changes Indicator */}
                {hasUnsavedChanges && !saving && (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium">
                        <svg
                            className="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                        <span>Unsaved changes</span>
                    </span>
                )}

                {/* Saving Indicator */}
                {saving && (
                    <span className="inline-flex items-center gap-2 text-sm text-gray-500">
                        <svg
                            className="w-4 h-4 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                className="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                strokeWidth="4"
                            />
                            <path
                                className="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            />
                        </svg>
                        Saving...
                    </span>
                )}

                {/* Last Saved */}
                {lastSaved && !saving && !hasUnsavedChanges && (
                    <span className="text-sm text-gray-500">
                        Last saved: {formatTime(lastSaved)}
                    </span>
                )}

                {/* Save Button */}
                <button
                    onClick={handleSave}
                    disabled={saving || !hasUnsavedChanges}
                    className={`inline-flex items-center gap-2 px-4 py-2 rounded-lg transition text-sm font-medium ${
                        saving || !hasUnsavedChanges
                            ? "bg-gray-200 text-gray-400 cursor-not-allowed"
                            : "bg-yellow-500 text-white hover:bg-yellow-600"
                    }`}
                >
                    {saving ? "Saving..." : "Save"}
                </button>

                {/* Preview Button */}
                <button
                    onClick={handlePreview}
                    className="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200"
                    title="Open preview in new tab"
                >
                    <svg
                        className="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                    </svg>
                    Preview
                </button>

                {/* Publish Button */}
                <button
                    onClick={handlePublish}
                    disabled={template?.is_active}
                    className={`inline-flex items-center gap-2 px-4 py-2 rounded-lg transition text-sm font-medium ${
                        template?.is_active
                            ? "bg-green-500 text-white cursor-default"
                            : "bg-black text-white hover:bg-gray-800"
                    }`}
                >
                    {template?.is_active ? "Published" : "Publish"}
                </button>
            </div>
        </header>
    );
}
