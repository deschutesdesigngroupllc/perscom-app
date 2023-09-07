import React from 'react'

import { ButtonLink } from './Button'
import { Container } from './Container'
import backgroundImage from '../../images/background-call-to-action.jpg'

export function CallToAction() {
  return (
    <section id='call-to-action' className='relative overflow-hidden bg-blue-600 py-20 sm:py-32'>
      <div className='-trangray-x-[50%] -trangray-y-[50%] absolute left-1/2 top-1/2'>
        <img src={backgroundImage} alt='' width={2347} height={1244} aria-hidden='true' />
      </div>
      <Container className='relative'>
        <div className='mx-auto max-w-xl text-center'>
          <h2 className='text-3xl font-bold tracking-tight text-white sm:text-4xl'>Get started today.</h2>
          <p className='mt-4 text-base text-white'>
            Take the first step toward more efficient personnel management. Sign up for PERSCOM.io today.
          </p>
          <ButtonLink href={route('web.register.index')} color='white' className='mt-10'>
            {' '}
            Get 1 week free{' '}
          </ButtonLink>
        </div>
      </Container>
    </section>
  )
}
