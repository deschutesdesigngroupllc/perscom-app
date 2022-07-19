import React from "react";

export default function Login() {
    return (
        <div
            className="min-h-full bg-cover bg-top sm:bg-top"
            style={{
                backgroundImage:
                    'url("https://images.unsplash.com/photo-1545972154-9bb223aac798?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=3050&q=80&exp=8&con=-15&sat=-75")',
            }}
        >
            <div className="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 sm:py-24 lg:px-8 lg:py-48">
                <p className="text-black text-sm font-semibold uppercase tracking-wide text-opacity-50">404 error</p>
                <h1 className="text-white mt-2 text-4xl font-extrabold tracking-tight sm:text-5xl">
                    Uh oh! I think you’re lost.
                </h1>
                <p className="text-black mt-2 text-lg font-medium text-opacity-50">
                    We cannot find an account linked with the domain you entered.
                </p>
                <div className="mt-6">
                    <a
                        href={route("landing.home")}
                        className="border-transparent bg-white text-black inline-flex items-center rounded-md border bg-opacity-75 px-4 py-2 text-sm font-medium text-opacity-75 sm:bg-opacity-25 sm:hover:bg-opacity-50"
                    >
                        {" "}
                        Go back home{" "}
                    </a>
                </div>
            </div>
        </div>
    );
}
