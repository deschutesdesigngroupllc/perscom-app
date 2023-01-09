import React from "react";
import { Logo } from "../Components/Logo";
import { Link } from "@inertiajs/inertia-react";

export function Auth({ children }) {
    return (
        <div className="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            <div>
                <Link href="/">
                    <Logo className="h-24" />
                </Link>
            </div>
            <div className="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}
