import React from "react";

export function Input({ id, label, type = "text", append, ...props }) {
    return (
        <div>
            {label && (
                <label htmlFor={id} className="text-gray-700 mb-3 block text-sm font-medium">
                    {label}
                </label>
            )}
            {append ? (
                <div className="mt-1 flex rounded-md shadow-sm">
                    <input
                        id={id}
                        type={type}
                        {...props}
                        className="border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 block w-full appearance-none rounded-none rounded-l-md border px-3 py-2 focus:outline-none sm:text-sm"
                    />
                    <span className="border-gray-200 bg-gray-50 text-gray-500 inline-flex items-center rounded-r-md border border-l-0 px-3 sm:text-sm">
                        {append}
                    </span>
                </div>
            ) : (
                <input
                    id={id}
                    type={type}
                    {...props}
                    className="border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 block w-full appearance-none rounded-md border px-3 py-2 focus:outline-none sm:text-sm"
                />
            )}
        </div>
    );
}
