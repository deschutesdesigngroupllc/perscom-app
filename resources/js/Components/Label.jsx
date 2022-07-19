import React from "react";

export function Label({ forInput, value, className, children }) {
    return (
        <label htmlFor={forInput} className={`text-gray-700 block text-sm font-medium ` + className}>
            {value ? value : children}
        </label>
    );
}
