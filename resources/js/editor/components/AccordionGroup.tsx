import { useState } from "react";
import { FieldGroup } from "../types";
import FieldRenderer from "./FieldRenderer";

interface Props {
    group: FieldGroup;
    groupId: string;
    localProps: Record<string, any>;
    onPropChange: (key: string, value: any) => void;
}

export default function AccordionGroup({
    group,
    groupId,
    localProps,
    onPropChange,
}: Props) {
    const [isCollapsed, setIsCollapsed] = useState(group.collapsed ?? false);

    return (
        <div className="border rounded-lg mb-4 overflow-hidden">
            {/* Group Header */}
            <button
                onClick={() => setIsCollapsed(!isCollapsed)}
                className="w-full px-4 py-3 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors"
            >
                <h4 className="text-sm font-semibold text-gray-900">
                    {group.label}
                </h4>
                {/* Chevron Icon */}
                <svg
                    className={`w-4 h-4 text-gray-500 transition-transform ${
                        isCollapsed ? "" : "rotate-180"
                    }`}
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M19 14l-7 7m0 0l-7-7m7 7V3"
                    />
                </svg>
            </button>

            {/* Group Content */}
            {!isCollapsed && (
                <div className="px-4 py-3 space-y-4 border-t bg-white">
                    {Object.entries(group.fields).map(([fieldKey, field]) => (
                        <FieldRenderer
                            key={fieldKey}
                            fieldKey={fieldKey}
                            field={field}
                            value={localProps[fieldKey] ?? field.default}
                            onChange={(value) => onPropChange(fieldKey, value)}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
