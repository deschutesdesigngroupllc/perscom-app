import React from "react";

export function OpenBetaWarning() {
    return (
        <div className="bg-blue-600 relative">
            <div className="mx-auto max-w-7xl py-3 px-3 sm:px-6 lg:px-8">
                <div className="pr-16 sm:px-16 sm:text-center">
                    <p className="text-white flex flex-col font-medium">
                        <span className="inline">
                            By signing up for our Open Beta, you agree to our Terms and Services.
                        </span>
                        <span className="text-gray-200 mt-1 inline text-xs">
                            Be aware, data could be deleted without prior warning.
                        </span>
                    </p>
                </div>
            </div>
        </div>
    );
}
