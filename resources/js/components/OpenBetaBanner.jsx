import React from 'react'
import {XIcon} from '@heroicons/react/outline'

export function OpenBetaBanner() {
    return (
        <div className="relative bg-blue-600">
            <div className="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
                <div className="pr-16 sm:text-center sm:px-16">
                    <p className="font-medium text-white">
                        <span className="md:hidden">Sign up for our Open Beta!</span>
                        <span className="hidden md:inline">Big news! We're excited to announce our Open Beta has opened for public use. Get signed up today.</span>
                        <span className="block sm:ml-2 sm:inline-block">
                          <a href="/register" className="text-white font-bold underline">
                            {' '} Sign up <span aria-hidden="true">&rarr;</span>
                          </a>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    )
}