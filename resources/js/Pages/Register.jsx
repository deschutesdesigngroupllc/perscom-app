import React, { useState } from 'react'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-react'

import { AuthLayout } from '../Components/AuthLayout'
import { Input } from '../Components/Input'
import { Logo } from '../Components/Logo'

export default function Register() {
    const { errors } = usePage().props

    const [values, setValues] = useState({
        organization: '',
        email: '',
        domain: '',
    })

    function handleChange(e) {
        const key = e.target.id
        const value = e.target.value
        setValues((values) => ({
            ...values,
            [key]: value,
        }))
    }

    function handleSubmit(e) {
        e.preventDefault()
        Inertia.post('/register', values)
    }

    return (
        <AuthLayout position="justify-start">
            <div className="flex flex-col items-start justify-start">
                <div className="flex w-full items-center justify-center">
                    <a href={route('landing.home')}>
                        <Logo className="sm:h-18 mb-2 h-16 w-auto md:h-20" />
                    </a>
                </div>
                <h1 className="mt-10 text-xl font-bold tracking-tight text-gray-900">
                    Get started for free.
                </h1>
                <p className="mt-2 text-sm text-gray-700">
                    No upfront costs or credit card requirements. Cancel at
                    anytime with no questions asked.
                </p>
            </div>
            <div className="mt-5">
                <form
                    action="#"
                    method=""
                    onSubmit={handleSubmit}
                    className="space-y-4"
                >
                    <div>
                        <Input
                            label="Organization"
                            id="organization"
                            name="organization"
                            type="text"
                            required
                            value={values.organization}
                            onChange={handleChange}
                        />
                        {errors.organization && (
                            <p
                                className="mt-2 text-sm text-red-600"
                                id="organization-error"
                            >
                                {errors.organization}
                            </p>
                        )}
                    </div>
                    <div>
                        <Input
                            label="Email Address"
                            id="email"
                            name="email"
                            type="email"
                            autoComplete="email"
                            required
                            value={values.email}
                            onChange={handleChange}
                        />
                        {errors.email && (
                            <p
                                className="mt-2 text-sm text-red-600"
                                id="email-error"
                            >
                                {errors.email}
                            </p>
                        )}
                    </div>
                    <div>
                        <Input
                            label="Domain"
                            id="domain"
                            append=".perscom.io"
                            name="domain"
                            type="text"
                            value={values.domain}
                            onChange={handleChange}
                        />
                        {errors.domain && (
                            <p
                                className="mt-2 text-sm text-red-600"
                                id="domain-error"
                            >
                                {errors.domain}
                            </p>
                        )}
                    </div>
                    <div className="pt-5">
                        <button
                            type="submit"
                            className="w-full rounded-full border border-transparent bg-blue-600 py-2 px-4 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Start free trial{' '}
                            <span aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </form>
            </div>
        </AuthLayout>
    )
}
