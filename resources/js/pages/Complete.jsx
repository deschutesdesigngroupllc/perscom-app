import React from 'react'
import PropTypes from 'prop-types'
import { RegisterLayout } from '../layouts/Register'
import { ButtonLink } from '../components/Button'
import { Logo } from '../components/Logo'

export function Complete({ url }) {
  return (
    <RegisterLayout position='justify-start'>
      <div className='flex flex-col items-start justify-start'>
        <div className='flex w-full items-center justify-center'>
          <a href={route('web.landing.home')}>
            <Logo className='sm:h-18 mb-2 h-16 w-auto md:h-20' />
          </a>
        </div>
        <h1 className='mt-10 text-xl font-bold tracking-tight text-gray-800'>Registration complete.</h1>
        <p className='mt-2 text-sm'>Plese check your email with instructions on how to access your account.</p>
        <ButtonLink color='blue' className='mt-4 w-full' href={url}>
          Go to Dashboard <span aria-hidden='true'>&nbsp;&rarr;</span>
        </ButtonLink>
      </div>
    </RegisterLayout>
  )
}

Complete.propTypes = {
  url: PropTypes.string
}

export default Complete
