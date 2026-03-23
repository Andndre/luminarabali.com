import { useDrop } from "react-dnd";
import { useTemplateStore } from "../stores/templateStore";
import SectionWrapper from "./SectionWrapper";

export default function Canvas() {
    const {
        sections,
        template,
        currentViewport,
        setSelectedSection,
        addSection,
        setDraggedComponent,
    } = useTemplateStore();

    const [{ isOver }, drop] = useDrop(() => ({
        accept: ["SECTION"],
        drop: (item: { componentType: string; schemaType: string }) => {
            // Only accept sections at canvas level
            if (item.schemaType === "section") {
                addSection(item.componentType as any, null);
            }
            setDraggedComponent(null);
        },
        collect: (monitor) => ({
            isOver: monitor.isOver(),
        }),
    }));

    const getViewportClass = () => {
        switch (currentViewport) {
            case "mobile":
                return "w-[375px]";
            case "tablet":
                return "w-[768px]";
            case "desktop":
                return "w-full max-w-6xl";
            default:
                return "w-full";
        }
    };

    return (
        <div className="flex-1 bg-gray-200 overflow-y-auto p-8">
            <div className="flex justify-center">
                <div
                    ref={drop as any}
                    onClick={() => setSelectedSection(null)}
                    className={`${getViewportClass()} min-h-screen bg-white shadow-lg transition-colors duration-200 ${
                        isOver ? "bg-yellow-50" : ""
                    }`}
                >
                    {template?.global_custom_css ? (
                        <style>{template.global_custom_css}</style>
                    ) : null}

                    {sections.length === 0 ? (
                        <div className="flex flex-col items-center justify-center min-h-[400px] text-gray-400">
                            <svg
                                className="w-16 h-16 mb-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                />
                            </svg>
                            <p className="text-lg font-medium">
                                Canvas is ready
                            </p>
                            <p className="text-sm">
                                Drag & drop components from the sidebar
                            </p>
                        </div>
                    ) : (
                        sections.map((section) => (
                            <SectionWrapper
                                key={section.id}
                                section={section}
                            />
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
