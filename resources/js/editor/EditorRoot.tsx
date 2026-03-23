import { useEffect } from "react";
import { useTemplateStore } from "./stores/templateStore";
import App from "./App";
import { EditorMode } from "./types";

// Get template ID from window
declare global {
    interface Window {
        templateId: number;
        editorConfig?: {
            mode?: EditorMode;
            resourceId?: number;
        };
    }
}

export default function EditorRoot() {
    const setTemplateId = useTemplateStore((state) => state.setTemplateId);
    const setEditorMode = useTemplateStore((state) => state.setEditorMode);

    useEffect(() => {
        const mode = window.editorConfig?.mode ?? "template";
        const resourceId = window.editorConfig?.resourceId ?? window.templateId;

        setEditorMode(mode);
        if (resourceId) {
            setTemplateId(resourceId);
        }
    }, [setEditorMode, setTemplateId]);

    return <App />;
}
