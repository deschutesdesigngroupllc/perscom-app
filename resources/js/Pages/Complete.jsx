import React from "react";

import { AuthLayout } from "@/Components/AuthLayout";
import { Logo } from "@/Components/Logo";

export default function Complete() {
    return (
        <AuthLayout position="justify-start">
            <div className="flex flex-col items-start justify-start">
                <div className="flex w-full items-center justify-center">
                    <a href={route("landing.home")}>
                        <Logo className="sm:h-18 mb-2 h-16 w-auto md:h-20" />
                    </a>
                </div>
                <h2 className="text-gray-900 mt-10 text-xl font-bold tracking-tight">Registration complete.</h2>
                <p className="text-gray-700 mt-2 text-sm">
                    Plese check your email with instructions on how to access your account.
                </p>
            </div>
        </AuthLayout>
    );
}
