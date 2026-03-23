import { useEffect } from "react";
import { useTemplateStore } from "./stores/templateStore";
import { templateEditorService } from "./services/api";
import Sidebar from "./components/Sidebar";
import Canvas from "./components/Canvas";
import PropertiesPanel from "./components/PropertiesPanel";
import Header from "./components/Header";

export default function App() {
    const {
        templateId,
        editorMode,
        loading,
        setTemplate,
        setAllSections,
        buildSectionTree,
        setLoading,
        setSaving,
        setHasUnsavedChanges,
    } = useTemplateStore();

    // Load template data on mount
    useEffect(() => {
        const loadTemplate = async () => {
            try {
                setLoading(true);
                const data = await templateEditorService.loadTemplate(
                    templateId,
                    editorMode,
                );
                setTemplate(data.template);
                setAllSections(data.sections);
                buildSectionTree();
                setSaving(false);
                setHasUnsavedChanges(false);
            } catch (error) {
                console.error("Failed to load template:", error);
                // TODO: Show error toast
            } finally {
                setLoading(false);
            }
        };

        if (templateId > 0) {
            loadTemplate();
        }
    }, [templateId, editorMode, setHasUnsavedChanges, setLoading, setSaving]);

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="text-center">
                    <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-500"></div>
                    <p className="mt-4 text-gray-600">Loading editor...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="h-full flex flex-col">
            <Header />
            <div className="flex flex-1 overflow-hidden">
                <Sidebar />
                <Canvas />
                <PropertiesPanel />
            </div>
        </div>
    );
}
