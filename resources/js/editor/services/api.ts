import {
    EditorMode,
    TemplateData,
    SaveSectionRequest,
    SaveSectionResponse,
} from "../types";

const API_BASE = "/admin/api";

class TemplateEditorService {
    /**
     * Load editor data by mode
     */
    async loadTemplate(
        resourceId: number,
        mode: EditorMode,
    ): Promise<TemplateData> {
        const endpoint =
            mode === "invitation"
                ? `${API_BASE}/invitations/${resourceId}/load`
                : `${API_BASE}/templates/${resourceId}/load`;

        const response = await fetch(endpoint);
        if (!response.ok) {
            throw new Error("Failed to load editor data");
        }
        return response.json();
    }

    /**
     * Save sections
     */
    async saveSections(
        resourceId: number,
        sections: any[],
        globalCustomCss: string,
        mode: EditorMode,
    ): Promise<SaveSectionResponse> {
        const payload: SaveSectionRequest =
            mode === "invitation"
                ? {
                      page_id: resourceId,
                      global_custom_css: globalCustomCss,
                      sections,
                  }
                : {
                      template_id: resourceId,
                      global_custom_css: globalCustomCss,
                      sections,
                  };

        const endpoint =
            mode === "invitation"
                ? `${API_BASE}/sections`
                : `${API_BASE}/templates/sections`;

        const response = await fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": this.getCsrfToken(),
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            throw new Error("Failed to save sections");
        }

        return response.json();
    }

    /**
     * Delete section
     */
    async deleteSection(sectionId: string, mode: EditorMode): Promise<void> {
        const endpoint =
            mode === "invitation"
                ? `${API_BASE}/sections/${sectionId}`
                : `${API_BASE}/templates/sections/${sectionId}`;

        const response = await fetch(endpoint, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": this.getCsrfToken(),
            },
        });

        if (!response.ok) {
            throw new Error("Failed to delete section");
        }
    }

    /**
     * Publish template
     */
    async publishTemplate(resourceId: number, mode: EditorMode): Promise<void> {
        const endpoint =
            mode === "invitation"
                ? `/admin/invitations/${resourceId}/publish`
                : `/admin/templates/${resourceId}/publish`;

        const response = await fetch(endpoint, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": this.getCsrfToken(),
            },
        });

        if (!response.ok) {
            throw new Error("Failed to publish");
        }
    }

    /**
     * Get CSRF token from meta tag
     */
    private getCsrfToken(): string {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        return token || "";
    }
}

export const templateEditorService = new TemplateEditorService();
