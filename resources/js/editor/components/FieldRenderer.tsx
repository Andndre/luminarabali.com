import { FieldSchema } from "../types";

interface Props {
    fieldKey: string;
    field: FieldSchema;
    value: any;
    onChange: (value: any) => void;
}

export default function FieldRenderer({
    fieldKey,
    field,
    value,
    onChange,
}: Props) {
    const label = field.label || fieldKey;

    const renderField = () => {
        switch (field.type) {
            case "text":
                return (
                    <input
                        type="text"
                        value={value || ""}
                        onChange={(e) => onChange(e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    />
                );

            case "textarea":
                return (
                    <textarea
                        rows={4}
                        value={value || ""}
                        onChange={(e) => onChange(e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent resize-y"
                    />
                );

            case "select":
                return (
                    <select
                        value={value ?? field.default}
                        onChange={(e) => onChange(e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    >
                        {(field.options || []).map((option) => {
                            const optValue =
                                typeof option === "string"
                                    ? option
                                    : option.value;
                            const optLabel =
                                typeof option === "string"
                                    ? option
                                    : option.label;
                            return (
                                <option key={optValue} value={optValue}>
                                    {optLabel}
                                </option>
                            );
                        })}
                    </select>
                );

            case "number":
                return (
                    <input
                        type="number"
                        value={value ?? field.default}
                        onChange={(e) =>
                            onChange(parseInt(e.target.value) || 0)
                        }
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    />
                );

            case "slider":
                const min = field.min ?? 0;
                const max = field.max ?? 100;
                return (
                    <div className="space-y-2">
                        <input
                            type="range"
                            min={min}
                            max={max}
                            value={value ?? field.default ?? 0}
                            onChange={(e) => onChange(parseInt(e.target.value))}
                            className="w-full"
                        />
                        <div className="flex justify-between text-xs text-gray-500">
                            <span>
                                {value ?? field.default ?? 0}
                                {field.unit || ""}
                            </span>
                            <span>
                                {max}
                                {field.unit || ""}
                            </span>
                        </div>
                    </div>
                );

            case "color":
                return (
                    <div className="flex items-center gap-2">
                        <input
                            type="color"
                            value={value ?? field.default ?? "#000000"}
                            onChange={(e) => onChange(e.target.value)}
                            className="w-12 h-10 px-1 py-1 border border-gray-300 rounded-lg cursor-pointer"
                        />
                        <input
                            type="text"
                            value={value ?? field.default ?? "#000000"}
                            onChange={(e) => onChange(e.target.value)}
                            className="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono"
                        />
                    </div>
                );

            case "image":
                return (
                    <div className="space-y-2">
                        <input
                            type="text"
                            value={value || ""}
                            onChange={(e) => onChange(e.target.value)}
                            placeholder="Image URL"
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        />
                        {value && (
                            <img
                                src={value}
                                alt="Preview"
                                className="w-full h-32 object-cover rounded-lg"
                            />
                        )}
                    </div>
                );

            default:
                return (
                    <input
                        type="text"
                        value={value || ""}
                        onChange={(e) => onChange(e.target.value)}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                    />
                );
        }
    };

    return (
        <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
                {label}
            </label>
            {renderField()}
        </div>
    );
}
