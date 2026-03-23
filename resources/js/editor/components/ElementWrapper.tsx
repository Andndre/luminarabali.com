import { useDrag } from "react-dnd";
import { useTemplateStore } from "../stores/templateStore";
import { TemplateSection } from "../types";

interface Props {
    element: TemplateSection;
    parent: TemplateSection;
}

export default function ElementWrapper({ element, parent }: Props) {
    const { selectedSection, setSelectedSection, deleteElement } =
        useTemplateStore();
    const isSelected = selectedSection?.id === element.id;

    const [{ isDragging }, drag] = useDrag(() => ({
        type: "EXISTING_ELEMENT",
        item: { elementId: element.id, parentId: parent.id },
        collect: (monitor) => ({
            isDragging: monitor.isDragging(),
        }),
    }));

    const handleClick = (e: React.MouseEvent) => {
        e.stopPropagation();
        setSelectedSection(element);
    };

    const handleDelete = () => {
        if (confirm("Delete this element?")) {
            deleteElement(parent.id, element.id);
        }
    };

    return (
        <div
            ref={drag as any}
            data-element-id={element.id}
            className={`element-wrapper relative p-2 mb-2 transition cursor-pointer group ${
                isSelected
                    ? "ring-2 ring-yellow-500"
                    : ""
            } ${isDragging ? "opacity-50" : ""}`}
            onClick={handleClick}
        >
            {/* Element content */}
            <div
                dangerouslySetInnerHTML={{
                    __html: renderElementContent(element),
                }}
            />

            {/* Delete button when selected */}
            <div
                className={`absolute top-1 right-1 flex gap-1 transition-opacity ${
                    isSelected ? "opacity-100" : "opacity-0 hover:opacity-100"
                }`}
            >
                <button
                    onClick={(e) => {
                        e.stopPropagation();
                        handleDelete();
                    }}
                    className="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                    title="Delete"
                >
                    <svg
                        className="w-3 h-3"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    );
}

// Helper function to render element content
function renderElementContent(section: TemplateSection): string {
    const props = section.props || {};
    const type = section.section_type;

    switch (type) {
        case "text":
            const content = props.content || "Tulis teks anda di sini...";
            const tag = props.tag || "p";
            const align = props.align || "left";
            const color = props.color || "#000000";
            const fontSize = props.font_size || 16;
            const marginBottom = props.margin_bottom || 16;
            return `<${tag} style="text-align: ${align}; color: ${color}; font-size: ${fontSize}px; margin-bottom: ${marginBottom}px;">${escapeHtml(
                content,
            )}</${tag}>`;

        case "image":
            const src = props.src || "https://via.placeholder.com/300x200";
            const alt = props.alt || "";
            const width = props.width || 100;
            const borderRadius = props.borderRadius || 0;
            const imgMarginBottom = props.margin_bottom || 16;
            return `<img src="${src}" alt="${escapeHtml(
                alt,
            )}" style="width: ${width}%; border-radius: ${borderRadius}px; margin-bottom: ${imgMarginBottom}px;" />`;

        case "button":
            const text = props.text || "Click Me";
            const url = props.url || "#";
            const variant = props.variant || "primary";
            const size = props.size || "medium";
            const btnAlign = props.align || "center";
            const buttonStyles: Record<string, string> = {
                primary:
                    "background-color: #000; color: #fff; padding: 10px 20px;",
                secondary:
                    "background-color: #6b7280; color: #fff; padding: 10px 20px;",
                outline:
                    "background-color: transparent; color: #000; border: 1px solid #000; padding: 10px 20px;",
            };
            const sizeStyles: Record<string, string> = {
                small: "font-size: 12px; padding: 6px 12px;",
                medium: "",
                large: "font-size: 16px; padding: 14px 28px;",
            };
            return `<div style="text-align: ${btnAlign};"><a href="${escapeHtml(
                url,
            )}" style="${buttonStyles[variant]} ${sizeStyles[size]} text-decoration: none; display: inline-block; border-radius: 4px;">${escapeHtml(
                text,
            )}</a></div>`;

        case "divider":
            const height = props.height || 1;
            const dividerColor = props.color || "#e5e7eb";
            const marginTop = props.margin_top || 24;
            const divMarginBottom = props.margin_bottom || 24;
            return `<hr style="height: ${height}px; background-color: ${dividerColor}; border: none; margin-top: ${marginTop}px; margin-bottom: ${divMarginBottom}px;" />`;

        case "spacer":
            const spacerHeight = props.height || 50;
            return `<div style="height: ${spacerHeight}px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center;"><span class="text-xs text-gray-400">Spacer: ${spacerHeight}px</span></div>`;

        default:
            return `<p class="text-gray-500">${section.section_type}</p>`;
    }
}

function escapeHtml(text: string): string {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}
