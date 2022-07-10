import React from 'react'
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-react'

import { AuthLayout } from '../Components/AuthLayout'
import { Button } from '../Components/Button'
import { Input } from '../Components/Input'
import { Logo } from '../Components/Logo'
import { ValidationErrors } from '@/Components/ValidationErrors'

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        organization: '',
        email: '',
        domain: '',
    })

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value)
    }

    const submit = (e) => {
        e.preventDefault()
        post(route('register.store'))
    }

    return (
        <AuthLayout position="justify-start">
            <div className="flex flex-col items-start justify-start">
                <div className="flex w-full items-center justify-center">
                    <a href={route('landing.home')}>
                        <Logo className="sm:h-18 mb-2 h-16 w-auto md:h-20" />
                    </a>
                </div>
                <h1 className="mt-10 text-xl font-bold tracking-tight text-gray-900">Get started for free.</h1>
                <p className="mt-2 text-sm text-gray-700">
                    No upfront costs or credit card requirements. Cancel at anytime with no questions asked.
                </p>
            </div>
            <div className="mt-5">
                <ValidationErrors errors={errors} />
                <form action="#" method="" onSubmit={submit} className="space-y-4">
                    <div>
                        <Input
                            label="Organization"
                            id="organization"
                            name="organization"
                            type="text"
                            required
                            value={data.organization}
                            onChange={onHandleChange}
                        />
                    </div>
                    <div>
                        <Input
                            label="Email Address"
                            id="email"
                            name="email"
                            type="email"
                            autoComplete="email"
                            required
                            value={data.email}
                            onChange={onHandleChange}
                        />
                    </div>
                    <div>
                        <Input
                            label="Domain"
                            id="domain"
                            append=".perscom.io"
                            name="domain"
                            type="text"
                            value={data.domain}
                            onChange={onHandleChange}
                        />
                    </div>
                    <div className="pt-5">
                        <Button type="submit" processing={processing} color="blue" className="w-full">
                            Start free trial <span aria-hidden="true">&rarr;</span>
                        </Button>
                    </div>
                </form>
            </div>
        </AuthLayout>
    )
}
