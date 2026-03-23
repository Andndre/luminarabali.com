import { useDrag } from "react-dnd";
import { ComponentSchema } from "../types";

interface Props {
    type: string;
    schema: ComponentSchema;
    onClick: () => void;
}

export default function DraggableComponent({ type, schema, onClick }: Props) {
    const [{ isDragging }, drag] = useDrag(() => ({
        type: schema.type === "section" ? "SECTION" : "ELEMENT",
        item: { componentType: type, schemaType: schema.type },
        collect: (monitor) => ({
            isDragging: monitor.isDragging(),
        }),
    }));

    return (
        <div
            ref={drag as any}
            onClick={onClick}
            className={`flex items-center gap-3 p-2 rounded-lg transition cursor-grab active:cursor-grabbing ${
                isDragging
                    ? "opacity-50"
                    : "hover:bg-yellow-50 border border-transparent hover:border-yellow-400"
            }`}
        >
            <div className="w-8 h-8 rounded bg-yellow-50 flex items-center justify-center shrink-0">
                <svg
                    className="w-4 h-4 text-yellow-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    {schema.icon && (
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d={schema.icon}
                        />
                    )}
                </svg>
            </div>
            <div className="min-w-0 flex-1">
                <p className="font-medium text-sm text-gray-900 truncate">
                    {schema.name}
                </p>
            </div>
        </div>
    );
}
