import React, { useState } from 'react'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-react'

import { Header } from '../components/Header'
import { Footer } from '../components/Footer'
import { AuthLayout } from '../components/AuthLayout'
import { Input } from '../components/Input'
import { Logo } from '../components/Logo'

export default function Register() {
    const { errors } = usePage().props

    const [values, setValues] = useState({
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        organization: '',
        subdomain: '',
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
        <AuthLayout>
            <div className="flex flex-col items-start justify-start">
                <div className="flex w-full items-center justify-center">
                    <a href="/">
                        <Logo className="sm:h-18 mb-2 h-16 w-auto md:h-20" />
                    </a>
                </div>
                <h2 className="mt-10 text-lg font-semibold text-gray-900">
                    Get started for free.
                </h2>
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
                    <div className="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6">
                        <div>
                            <Input
                                label="First Name"
                                id="first_name"
                                name="first_name"
                                type="text"
                                autoComplete="given-name"
                                required
                                value={values.first_name}
                                onChange={handleChange}
                            />
                            {errors.first_name && (
                                <p
                                    className="mt-2 text-sm text-red-600"
                                    id="first-name-error"
                                >
                                    {errors.first_name}
                                </p>
                            )}
                        </div>
                        <div>
                            <Input
                                label="Last Name"
                                id="last_name"
                                name="last_name"
                                type="text"
                                autoComplete="family-name"
                                required
                                value={values.last_name}
                                onChange={handleChange}
                            />
                            {errors.last_name && (
                                <p
                                    className="mt-2 text-sm text-red-600"
                                    id="last-name-error"
                                >
                                    {errors.last_name}
                                </p>
                            )}
                        </div>
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
                            label="Password"
                            id="password"
                            name="password"
                            type="password"
                            autoComplete="new-password"
                            required
                            value={values.password}
                            onChange={handleChange}
                        />
                        {errors.password && (
                            <p
                                className="mt-2 text-sm text-red-600"
                                id="password-error"
                            >
                                {errors.password}
                            </p>
                        )}
                    </div>
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
                            label="Subdomain"
                            id="subdomain"
                            append=".perscom.io"
                            name="subdomain"
                            type="text"
                            required
                            value={values.subdomain}
                            onChange={handleChange}
                        />
                        {errors.subdomain && (
                            <p
                                className="mt-2 text-sm text-red-600"
                                id="subdomain-error"
                            >
                                {errors.subdomain}
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
