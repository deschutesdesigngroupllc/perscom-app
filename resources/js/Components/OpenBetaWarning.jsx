import React from 'react'
import { XIcon } from '@heroicons/react/outline'

export function OpenBetaWarning() {
    return (
        <div className="relative bg-blue-600">
            <div className="mx-auto max-w-7xl py-3 px-3 sm:px-6 lg:px-8">
                <div className="pr-16 sm:px-16 sm:text-center">
                    <p className="flex flex-col font-medium text-white">
                        <span className="inline">
                            By signing up for our Open Beta, you agree to our Terms and Services.
                        </span>
                        <span className="mt-1 inline text-xs text-gray-200">
                            Be aware, data could be deleted without prior warning.
                        </span>
                    </p>
                </div>
            </div>
        </div>
    )
}
