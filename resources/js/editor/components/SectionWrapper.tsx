import { useTemplateStore } from "../stores/templateStore";
import { componentSchemas } from "../services/componentSchemas";
import { TemplateSection } from "../types";
import SectionDropZone from "./SectionDropZone";
import { CSSProperties } from "react";

interface Props {
    section: TemplateSection;
}

export default function SectionWrapper({ section }: Props) {
    const {
        selectedSection,
        setSelectedSection,
        deleteSection,
        duplicateSection,
        moveSection,
    } = useTemplateStore();

    const isSelected = selectedSection?.id === section.id;
    const schema = componentSchemas[section.section_type];
    const isSectionContainer = schema?.type === "section";

    const getSectionStyle = (): CSSProperties => {
        const props = section.props || {};
        const style: CSSProperties = {};

        if (props.background_color) {
            style.backgroundColor = props.background_color;
        }
        if (props.margin_top) {
            style.marginTop = `${props.margin_top}px`;
        } else {
            style.marginTop = 0;
        }
        if (props.margin_bottom) {
            style.marginBottom = `${props.margin_bottom}px`;
        } else {
            style.marginBottom = 0;
        }
        const marginLeftMode = props.margin_left_mode || "px";
        const marginRightMode = props.margin_right_mode || "px";

        style.marginLeft =
            marginLeftMode === "auto" ? "auto" : `${props.margin_left ?? 0}px`;
        style.marginRight =
            marginRightMode === "auto"
                ? "auto"
                : `${props.margin_right ?? 0}px`;

        const borderWidth = props.border_width ?? 0;
        if (borderWidth > 0) {
            style.border = `${borderWidth}px solid ${props.border_color || "#e5e7eb"}`;
        }

        if ((props.border_radius ?? 0) > 0) {
            style.borderRadius = `${props.border_radius}px`;
        }

        const shadowMap: Record<string, string> = {
            sm: "0 1px 2px rgba(0, 0, 0, 0.08)",
            md: "0 8px 24px rgba(0, 0, 0, 0.12)",
            lg: "0 14px 34px rgba(0, 0, 0, 0.16)",
        };

        if (props.shadow && props.shadow !== "none") {
            style.boxShadow = shadowMap[props.shadow] || shadowMap.md;
        }

        return style;
    };

    const getContainerStyle = (): CSSProperties => {
        const props = section.props || {};
        const style: CSSProperties = {};

        style.paddingTop = props.padding_top ? `${props.padding_top}px` : 0;
        style.paddingBottom = props.padding_bottom
            ? `${props.padding_bottom}px`
            : 0;
        style.paddingLeft = props.padding_left ? `${props.padding_left}px` : 0;
        style.paddingRight = props.padding_right
            ? `${props.padding_right}px`
            : 0;

        if (props.max_width) {
            style.maxWidth = `${props.max_width}px`;
            style.marginLeft = "auto";
            style.marginRight = "auto";
        }

        return style;
    };

    const getColumnRatio = () => {
        const props = section.props || {};
        const ratio = props.column_ratio || "50-50";
        const parts = ratio.split("-");
        return `${parts[0]}% ${parts[1]}%`;
    };

    const getColumnLayoutStyle = (columns: "two" | "three"): CSSProperties => {
        const props = section.props || {};
        const verticalAlign = props.vertical_align || "top";

        const style: CSSProperties = {
            gap: `${props.column_gap ?? 20}px`,
            alignItems:
                verticalAlign === "center"
                    ? "center"
                    : verticalAlign === "bottom"
                      ? "end"
                      : "start",
        };

        style.gridTemplateColumns =
            columns === "two" ? getColumnRatio() : "repeat(3, minmax(0, 1fr))";

        return style;
    };

    const handleDelete = () => {
        if (confirm("Are you sure you want to delete this section?")) {
            deleteSection(section.id);
        }
    };

    const handleMove = (direction: "up" | "down") => {
        moveSection(section.id, direction);
    };

    const index = useTemplateStore
        .getState()
        .sections.findIndex((s) => s.id === section.id);

    return (
        <div
            data-section-id={section.id}
            className="section-wrapper relative py-1"
            onClick={(e) => {
                e.stopPropagation();
                setSelectedSection(section);
            }}
        >
            {/* Section Actions - higher z-index */}
            <div
                className={`absolute -top-2 right-2 flex gap-1 z-20 transition-opacity ${
                    isSelected ? "opacity-100" : "opacity-0"
                }`}
            >
                {index > 0 && (
                    <button
                        onClick={(e) => {
                            e.stopPropagation();
                            handleMove("up");
                        }}
                        className="p-1.5 bg-white rounded shadow hover:bg-gray-50 border border-gray-200"
                        title="Move up"
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
                                d="M5 15l7-7 7 7"
                            />
                        </svg>
                    </button>
                )}
                {index < useTemplateStore.getState().sections.length - 1 && (
                    <button
                        onClick={(e) => {
                            e.stopPropagation();
                            handleMove("down");
                        }}
                        className="p-1.5 bg-white rounded shadow hover:bg-gray-50 border border-gray-200"
                        title="Move down"
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
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                )}
                <button
                    onClick={(e) => {
                        e.stopPropagation();
                        duplicateSection(section.id);
                    }}
                    className="p-1.5 bg-white rounded shadow hover:bg-gray-50 border border-gray-200"
                    title="Duplicate"
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
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                        />
                    </svg>
                </button>
                <button
                    onClick={(e) => {
                        e.stopPropagation();
                        handleDelete();
                    }}
                    className="p-1.5 bg-red-500 text-white rounded shadow hover:bg-red-600"
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
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                    </svg>
                </button>
            </div>

            {/* Section Preview */}
            <div
                className={`bg-white overflow-hidden transition relative ${
                    isSelected ? "border-2 border-yellow-500" : ""
                }`}
                style={getSectionStyle()}
            >
                {isSectionContainer ? (
                    <div
                        style={getContainerStyle()}
                        className="section-container"
                    >
                        {/* Section label when selected - floating badge style */}
                        {isSelected && (
                            <div className="absolute top-2 left-2 px-2 py-1 bg-yellow-500 text-white text-xs rounded shadow z-10 flex items-center gap-1">
                                <span className="font-medium">
                                    {schema?.name}
                                </span>
                            </div>
                        )}

                        {/* Render based on section type */}
                        {section.section_type === "section_one_col" && (
                            <SectionDropZone
                                section={section}
                                columnIndex={0}
                            />
                        )}

                        {section.section_type === "section_two_col" && (
                            <div
                                className="grid gap-2"
                                style={getColumnLayoutStyle("two")}
                            >
                                <SectionDropZone
                                    section={section}
                                    columnIndex={0}
                                />
                                <SectionDropZone
                                    section={section}
                                    columnIndex={1}
                                />
                            </div>
                        )}

                        {section.section_type === "section_three_col" && (
                            <div
                                className="grid gap-2"
                                style={getColumnLayoutStyle("three")}
                            >
                                <SectionDropZone
                                    section={section}
                                    columnIndex={0}
                                />
                                <SectionDropZone
                                    section={section}
                                    columnIndex={1}
                                />
                                <SectionDropZone
                                    section={section}
                                    columnIndex={2}
                                />
                            </div>
                        )}
                    </div>
                ) : (
                    // Direct element render
                    <div className="p-6">
                        <div
                            dangerouslySetInnerHTML={{
                                __html: renderElementContent(section),
                            }}
                        />
                    </div>
                )}

                {/* Unknown type */}
                {!schema && (
                    <div className="p-4 text-center text-gray-500">
                        <p className="font-medium">{section.section_type}</p>
                        <p className="text-sm">(Unknown component type)</p>
                    </div>
                )}
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
            const imgMarginTop = props.margin_top ?? 0;
            const imgMarginBottom = props.margin_bottom ?? 0;
            return `<img${elementId} class="inline-block ${customClass.trim()}" src="${src}" alt="${escapeHtml(
                alt,
            )}" style="width: ${width}%; border-radius: ${borderRadius}px; margin-top: ${imgMarginTop}px; margin-bottom: ${imgMarginBottom}px; ${customCss}" />`;

        case "button":
            const text = props.text || "Click Me";
            const url = props.url || "#";
            const variant = props.variant || "primary";
            const size = props.size || "medium";
            const btnAlign = props.align || "center";
            const backgroundColor = props.background_color || "#d4af37";
            const textColor = props.text_color || "#ffffff";
            const borderRadiusBtn = props.border_radius ?? 8;
            return `<div style="text-align: ${btnAlign};"><a href="${escapeHtml(
                url,
            )}"${elementId} class="btn btn-${variant} btn-${size} ${customClass.trim()}" style="background-color: ${backgroundColor}; color: ${textColor}; border-radius: ${borderRadiusBtn}px; ${customCss}">${escapeHtml(text)}</a></div>`;

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
            return `<p>${section.section_type}</p>`;
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
