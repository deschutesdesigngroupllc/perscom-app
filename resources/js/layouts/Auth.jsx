import React from 'react'
import PropTypes from 'prop-types'
import { Logo } from '../components/Logo'
import { Link } from '@inertiajs/react'

export function AuthLayout({ children }) {
  return (
    <div className='flex min-h-screen flex-col items-center bg-white pt-6 sm:justify-center sm:bg-gray-100 sm:pt-0'>
      <div className='hidden sm:flex'>
        <Link href='/'>
          <Logo className='h-24' />
        </Link>
      </div>
      <div className='mt-6 min-h-screen w-full overflow-hidden bg-white px-6 py-4 shadow-none sm:min-h-min sm:max-w-md sm:rounded-lg sm:shadow-md'>
        <div className='flex items-center justify-center sm:hidden'>
          <Link href='/'>
            <Logo className='h-24' />
          </Link>
        </div>
        {children}
      </div>
    </div>
  )
}

AuthLayout.propTypes = {
  children: PropTypes.array
}

export default AuthLayout
