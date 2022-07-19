import React from "react";

import { Button } from "@/Components/Button";
import { Guest } from "@/Layouts/Guest";
import { Head, Link, useForm } from "@inertiajs/inertia-react";

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm();

    const submit = (e) => {
        e.preventDefault();
        post(route("verification.send"));
    };

    return (
        <Guest>
            <Head title="Email Verification" />

            <div className="text-gray-600 mb-4 text-sm">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                link we just emailed to you? If you didn&apos;t receive the email, we will gladly send you another.
            </div>

            {status === "verification-link-sent" && (
                <div className="text-green-600 mb-4 text-sm font-medium">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            )}

            <form onSubmit={submit}>
                <div className="mt-4 flex items-center justify-between">
                    <Button processing={processing} color="blue">
                        Resend verification email
                    </Button>

                    <Link
                        href={route("logout")}
                        method="post"
                        as="button"
                        className="text-gray-600 hover:text-gray-900 text-sm underline"
                    >
                        {" "}
                        Log out{" "}
                    </Link>
                </div>
            </form>
        </Guest>
    );
}
