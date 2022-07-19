import React from "react";
import { Logo } from "../Components/Logo";
import { Link } from "@inertiajs/inertia-react";

export function Guest({ children }) {
    return (
        <div className="bg-gray-100 flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0">
            <div>
                <Link href="/">
                    <Logo className="h-24" />
                </Link>
            </div>
            <div className="bg-white mt-6 w-full overflow-hidden px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}
