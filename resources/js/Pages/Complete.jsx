import React from "react";

import {AuthLayout} from "@/Components/AuthLayout";
import {ButtonLink} from "@/Components/Button";
import {Logo} from "@/Components/Logo";

export default function Complete({ url }) {
    return (
        <AuthLayout position="justify-start">
            <div className="flex flex-col items-start justify-start">
                <div className="flex w-full items-center justify-center">
                    <a href={route("landing.home")}>
                        <Logo className="sm:h-18 mb-2 h-16 w-auto md:h-20" />
                    </a>
                </div>
                <h2 className="mt-10 text-xl font-bold tracking-tight text-gray-900">Registration complete.</h2>
                <p className="mt-2 text-sm text-gray-700">
                    Plese check your email with instructions on how to access your account.
                </p>
                <ButtonLink color="blue" className="mt-4 w-full" href={url}>
                    Go to Dashboard <span aria-hidden="true">&nbsp;&rarr;</span>
                </ButtonLink>
            </div>
        </AuthLayout>
    );
}
