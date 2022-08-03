import React, { useEffect } from "react";

import { Button } from "@/Components/Button";
import { Checkbox } from "@/Components/Checkbox";
import { Guest } from "@/Layouts/Guest";
import { Input } from "@/Components/Input";
import { Label } from "@/Components/Label";
import { ValidationErrors } from "@/Components/ValidationErrors";
import { Head, Link, useForm } from "@inertiajs/inertia-react";

export default function Login({ status, canResetPassword, demoMode }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: "",
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === "checkbox" ? event.target.checked : event.target.value);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route("login"));
    };

    return (
        <Guest>
            <Head title="Log in" />

            {status && <div className="mb-4 text-sm font-medium text-green-600">{status}</div>}

            {demoMode && (
                <div class="mb-4">
                    <div className="text-lg font-bold leading-7 text-gray-600">Welcome to the PERSCOM Demo</div>
                    <div className="text-sm text-gray-500">Use the login information below to get started.</div>
                </div>
            )}

            <ValidationErrors errors={errors} />
            <form onSubmit={submit}>
                <div>
                    <Label forInput="email" value="Email" />
                    <Input
                        type="text"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        onChange={onHandleChange}
                        placeholder={demoMode && "Demo Email: demo@perscom.io"}
                    />
                </div>
                <div className="mt-4">
                    <Label forInput="password" value="Password" />
                    <Input
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={onHandleChange}
                        placeholder={demoMode && "Demo Password: password"}
                    />
                </div>
                <div className="mt-4 block">
                    <label className="flex items-center">
                        <Checkbox name="remember" value={data.remember} onChange={onHandleChange} />
                        <span className="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>
                <div className="mt-4 flex items-center justify-end">
                    {canResetPassword && (
                        <Link
                            href={route("password.request")}
                            className="text-sm text-gray-600 underline hover:text-gray-900"
                        >
                            {" "}
                            Forgot your password?{" "}
                        </Link>
                    )}
                    <Button className="ml-4" processing={processing} color="blue">
                        {" "}
                        Log in{" "}
                    </Button>
                </div>
            </form>
        </Guest>
    );
}
