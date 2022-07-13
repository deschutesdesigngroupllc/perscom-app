import React from 'react'

import {ButtonLink} from '../Components/Button'
import {Container} from '../Components/Container'
import backgroundImage
    from '../../images/background-call-to-action.jpg'

export function CallToAction() {
    return (
        <section id="get-started-today" className="relative overflow-hidden bg-blue-600 py-32">
            <div className="absolute top-1/2 left-1/2 -translate-x-[50%] -translate-y-[50%]">
                <img src={backgroundImage} alt="" width={2347} height={1244} />
            </div>
            <Container className="relative">
                <div className="mx-auto max-w-lg text-center">
                    <h2 className="font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Get started today.
                    </h2>
                    <p className="mt-4 text-lg tracking-tight text-white">
                        Our platform requires no setup and is ready-to-use as soon as you&apos;re ready to start.
                    </p>
                    <ButtonLink href={route('register.index')} color="white" className="mt-10">
                        {' '}
                        Get 1 week free{' '}
                    </ButtonLink>
                </div>
            </Container>
        </section>
    )
}
