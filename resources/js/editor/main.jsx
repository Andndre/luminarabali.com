import '../../css/app.css';
import { StrictMode } from 'react';
import ReactDOM from "react-dom/client";
import { DndProvider } from "react-dnd";
import { HTML5Backend } from "react-dnd-html5-backend";
import EditorRoot from "./EditorRoot";

// Mount the React app
const container = document.getElementById("template-editor-root");
if (container) {
    ReactDOM.createRoot(container).render(
        <StrictMode>
            <DndProvider backend={HTML5Backend}>
                <EditorRoot />
            </DndProvider>
        </StrictMode>,
    );
}
