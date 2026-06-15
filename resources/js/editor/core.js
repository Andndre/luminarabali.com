export default function EditorCore() {
    return {
        panels: {
                library: true,
                visual: true,
                code: false,
                properties: false
            },
            isSyncing: false,
            typingTimer: null,
            isInspectorOpen: false, // Element Inspector (separate from Page Properties)
            toggleView(panelName) {
                this.panels[panelName] = !this.panels[panelName];
                if (panelName === 'code' && this.panels.code && window.globalEditor) {
                    setTimeout(() => window.globalEditor.layout(), 350);
                }
            },
            insertTargetNode: null,
            preSaveSync() {
                window.syncToMonaco();
            },
            
            // --- INVITATION EDITOR STATE MERGED ---
            // Fake data for rendering x-text variables in Editor
            groom_name: 'Romeo',
            bride_name: 'Juliet',
            event_date: '2026-12-12T08:00:00',
            guest_name: 'Budi (Tamu VIP)',

            // Node Inspector State
            selectedNode: null,
            nodeData: {
                tagName: '',
                text: '',
                classes: '',
                href: '',
                src: '',
                isDynamic: false,
                textColor: '#000000',
                bgColor: '#ffffff'
            },
            
            constraints: {
                paddingGlobal: false,
                marginGlobal: false,
                radiusGlobal: false,
                borderGlobal: false
            },

            // Hover Block Control State
    };
}
