import React from 'react'

import backgroundImage from '../../images/register1.jpeg'
import {OpenBetaWarning} from "@/components/OpenBetaWarning";

export function AuthLayout({children}) {
    return (
        <>
            <OpenBetaWarning/>
            <div className="relative flex min-h-full justify-center md:px-12 lg:px-0">
                <div className="relative z-10 flex flex-1 flex-col justify-center bg-white py-12 px-4 shadow-2xl md:flex-none md:px-28 max-h-full">
                    <div className="mx-auto w-full max-w-md sm:px-4 md:w-96 md:max-w-sm md:px-0">
                        {children}
                    </div>
                </div>
                <div className="absolute inset-0 hidden w-full flex-1 sm:block lg:relative lg:w-0">
                    <span className="box-border block overflow-hidden opacity-100 border-none m-0 p-0 absolute inset-0">
                        <img src={backgroundImage} alt="" className="absolute inset-0 p-0 box-border border-none m-auto block w-0 h-0 w-100 min-w-full max-w-full min-h-full max-h-full object-cover"/>
                    </span>
                </div>
            </div>
        </>
    )
}
