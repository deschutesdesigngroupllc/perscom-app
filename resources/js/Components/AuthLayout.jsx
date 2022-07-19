import React from "react";
import clsx from "clsx";

import backgroundImage from "../../images/register1.jpeg";
import { OpenBetaWarning } from "../Components/OpenBetaWarning";

export function AuthLayout({ children, position = "justify-center" }) {
    return (
        <>
            <OpenBetaWarning />
            <div className={clsx("relative flex min-h-full md:px-12 lg:px-0", position)}>
                <div
                    className={clsx(
                        "bg-white relative z-10 flex max-h-full flex-1 flex-col py-16 px-4 shadow-2xl md:flex-none md:px-28",
                        position
                    )}
                >
                    <div className="mx-auto w-full max-w-md sm:px-4 md:w-96 md:max-w-sm md:px-0">{children}</div>
                </div>
                <div className="absolute inset-0 hidden w-full flex-1 sm:block lg:relative lg:w-0">
                    <span className="absolute inset-0 m-0 box-border block overflow-hidden border-none p-0 opacity-100">
                        <img
                            src={backgroundImage}
                            alt=""
                            className="w-100 absolute inset-0 m-auto box-border block h-0 max-h-full min-h-full w-0 min-w-full max-w-full border-none object-cover p-0"
                        />
                    </span>
                </div>
            </div>
        </>
    );
}
