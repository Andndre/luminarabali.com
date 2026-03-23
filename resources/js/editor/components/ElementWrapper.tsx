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
                isSelected ? "ring-2 ring-yellow-500" : ""
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
    const elementId = props.element_id
        ? ` id="${escapeAttr(String(props.element_id))}"`
        : "";
    const customClass = props.custom_class
        ? ` ${escapeAttr(String(props.custom_class))}`
        : "";
    const customCss = props.custom_css ? String(props.custom_css) : "";
    const shadowMap: Record<string, string> = {
        sm: "0 1px 2px rgba(0, 0, 0, 0.08)",
        md: "0 8px 24px rgba(0, 0, 0, 0.12)",
        lg: "0 14px 34px rgba(0, 0, 0, 0.16)",
    };

    switch (type) {
        case "text":
            const content = props.content || "Tulis teks anda di sini...";
            const allowedTags = ["h1", "h2", "h3", "h4", "h5", "h6", "p"];
            const tag = allowedTags.includes(props.tag) ? props.tag : "p";
            const align = props.align || "left";
            const color = props.color || "#000000";
            const fontSize = props.font_size || 16;
            const marginBottom = props.margin_bottom ?? 0;
            const fontFamily = props.font_family || "lato";
            const lineHeight = props.line_height ?? 1.5;
            const letterSpacing = props.letter_spacing ?? 0;
            const fontFamilyMap: Record<string, string> = {
                lato: "'Lato', sans-serif",
                montserrat: "'Montserrat', sans-serif",
                "playfair-display": "'Playfair Display', serif",
                "great-vibes": "'Great Vibes', cursive",
                "open-sans": "'Open Sans', sans-serif",
            };
            const fontFamilyValue =
                fontFamilyMap[fontFamily] || "'Lato', sans-serif";
            return `<${tag}${elementId} class="${customClass.trim()}" style="font-family: ${fontFamilyValue}; text-align: ${align}; color: ${color}; font-size: ${fontSize}px; margin-bottom: ${marginBottom}px; line-height: ${lineHeight}; letter-spacing: ${letterSpacing}px; ${customCss}">${escapeHtml(
                content,
            )}</${tag}>`;

        case "image":
            const src = props.src || "https://via.placeholder.com/300x200";
            const alt = props.alt || "";
            const width = props.width || 100;
            const borderRadius = props.border_radius ?? 0;
            const borderWidth = props.border_width ?? 0;
            const borderColor = props.border_color || "#e5e7eb";
            const imgMarginBottom = props.margin_bottom ?? 0;
            const imgMarginTop = props.margin_top ?? 0;
            const shadowCss =
                props.shadow && props.shadow !== "none"
                    ? `box-shadow: ${shadowMap[props.shadow] || shadowMap.md};`
                    : "";
            return `<img${elementId} class="inline-block ${customClass.trim()}" src="${src}" alt="${escapeHtml(
                alt,
            )}" style="width: ${width}%; border-radius: ${borderRadius}px; border: ${borderWidth}px solid ${borderColor}; margin-top: ${imgMarginTop}px; margin-bottom: ${imgMarginBottom}px; ${shadowCss} ${customCss}" />`;

        case "button":
            const text = props.text || "Click Me";
            const url = props.url || "#";
            const variant = props.variant || "primary";
            const size = props.size || "medium";
            const btnAlign = props.align || "center";
            const backgroundColor = props.background_color || "#d4af37";
            const textColor = props.text_color || "#ffffff";
            const borderRadiusBtn = props.border_radius ?? 8;
            const btnBorderWidth = props.border_width ?? 0;
            const btnBorderColor = props.border_color || backgroundColor;
            const btnShadowCss =
                props.shadow && props.shadow !== "none"
                    ? `box-shadow: ${shadowMap[props.shadow] || shadowMap.md};`
                    : "";
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
            )}"${elementId} class="${customClass.trim()}" style="${buttonStyles[variant]} ${sizeStyles[size]} background-color: ${backgroundColor}; color: ${textColor}; text-decoration: none; display: inline-block; border-radius: ${borderRadiusBtn}px; border: ${btnBorderWidth}px solid ${btnBorderColor}; ${btnShadowCss} ${customCss}">${escapeHtml(
                text,
            )}</a></div>`;

        case "divider":
            const height = props.height || 1;
            const dividerColor = props.color || "#e5e7eb";
            const marginTop = props.margin_top ?? 24;
            const divMarginBottom = props.margin_bottom ?? 24;
            return `<div${elementId} class="${customClass.trim()}" style="height: ${height}px; background-color: ${dividerColor}; border: none; margin-top: ${marginTop}px; margin-bottom: ${divMarginBottom}px; ${customCss}"></div>`;

        case "spacer":
            const spacerHeight = props.height || 50;
            return `<div${elementId} class="${customClass.trim()}" style="height: ${spacerHeight}px; ${customCss}"></div>`;

        default:
            return `<p class="text-gray-500">${section.section_type}</p>`;
    }
}

function escapeHtml(text: string): string {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function escapeAttr(text: string): string {
    return text.replace(/"/g, "&quot;").replace(/</g, "&lt;");
}
