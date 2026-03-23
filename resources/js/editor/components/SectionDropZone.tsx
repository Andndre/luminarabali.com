import { useDrop } from "react-dnd";
import { useTemplateStore } from "../stores/templateStore";
import { componentSchemas } from "../services/componentSchemas";
import { TemplateSection } from "../types";
import ElementWrapper from "./ElementWrapper";

interface Props {
    section: TemplateSection;
    columnIndex: number;
}

export default function SectionDropZone({ section, columnIndex }: Props) {
    const { selectedSection, addElement } = useTemplateStore();

    const [{ isOver, canDrop }, drop] = useDrop(() => ({
        accept: "ELEMENT",
        drop: (item: { componentType: string }) => {
            // Pass columnIndex to addElement
            addElement(item.componentType as any, section.id, columnIndex);
        },
        canDrop: () => {
            const schema = componentSchemas[section.section_type];
            return schema?.type === "section";
        },
        collect: (monitor) => ({
            isOver: monitor.isOver(),
            canDrop: monitor.canDrop(),
        }),
    }));

    // Filter children by column_index in props
    const children =
        section.children?.filter((c) => (c.props?.column_index ?? 0) === columnIndex) || [];

    return (
        <div
            ref={drop as any}
            data-column-index={columnIndex}
            className={`section-drop-zone min-h-12.5 p-2 rounded transition ${
                isOver && canDrop
                    ? "bg-yellow-50 border-2 border-dashed border-yellow-300"
                    : selectedSection?.id === section.id &&
                        children.length === 0
                      ? "bg-yellow-50 border-2 border-dashed border-yellow-300"
                      : "border border-dashed border-gray-200"
            }`}
        >
            {children.length === 0 && !isOver && (
                <div className="text-center text-gray-400 text-sm py-4">
                    Drop elements here
                </div>
            )}
            {children.map((child) => (
                <ElementWrapper
                    key={child.id}
                    element={child}
                    parent={section}
                />
            ))}
        </div>
    );
}
