import React from "react";

import {Button} from "@/Components/Button";
import {Guest} from "@/Layouts/Guest";
import {ValidationErrors} from "@/Components/ValidationErrors";
import {Head, Link, useForm} from "@inertiajs/inertia-react";

export default function Authorize({ client, name, scopes, state, authToken, csrfToken }) {
    const { post, processing, errors } = useForm({
        state: state,
        client_id: client,
        auth_token: authToken,
        _token: csrfToken,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("passport.authorizations.approve"));
    };

    const scopeList = [];
    scopes.forEach((data) => {
        scopeList.push(<li key={data["id"]}>{data["description"]}</li>);
    });

    return (
        <Guest>
            <Head title="Authorization" />
            <ValidationErrors errors={errors} />
            <form onSubmit={submit}>
                <div>
                    <span className="font-bold">{name}</span> is requesting permission to access your account.
                </div>
                {scopeList.length > 0 && <ul className="my-4 list-inside list-disc">{scopeList}</ul>}
                <div className="mt-4 flex items-center justify-end">
                    <Link
                        href={route("passport.authorizations.deny")}
                        method="delete"
                        as="button"
                        className="text-sm text-gray-600 underline hover:text-gray-900"
                    >
                        {" "}
                        Deny{" "}
                    </Link>
                    <Button className="ml-4" processing={processing} color="blue">
                        {" "}
                        Authorize{" "}
                    </Button>
                </div>
            </form>
        </Guest>
    );
}
