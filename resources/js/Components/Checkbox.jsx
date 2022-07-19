import React from "react";

export function Checkbox({ name, value, onChange }) {
    return (
        <input
            type="checkbox"
            name={name}
            value={value}
            className="border-gray-300 rounded text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            onChange={(e) => onChange(e)}
        />
    );
}
