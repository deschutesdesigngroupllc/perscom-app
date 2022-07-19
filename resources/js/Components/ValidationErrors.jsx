import React from "react";

export function ValidationErrors({ errors }) {
    return (
        Object.keys(errors).length > 0 && (
            <div className="mb-4">
                <div className="text-red-600 font-medium">Whoops! Something went wrong.</div>

                <ul className="text-red-600 mt-3 list-inside list-disc text-sm">
                    {Object.keys(errors).map(function (key, index) {
                        return <li key={index}>{errors[key]}</li>;
                    })}
                </ul>
            </div>
        )
    );
}
