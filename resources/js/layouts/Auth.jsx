import React from 'react'
import PropTypes from 'prop-types'
import { Logo } from '../components/Logo'
import { Link } from '@inertiajs/react'

export function AuthLayout({ children }) {
  return (
    <div className='flex min-h-screen flex-col items-center bg-white sm:bg-gray-100 pt-6 sm:justify-center sm:pt-0'>
      <div className='hidden sm:flex'>
        <Link href='/'>
          <Logo className='h-24' />
        </Link>
      </div>
      <div className='min-h-screen sm:min-h-min mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-none sm:shadow-md sm:max-w-md sm:rounded-lg'>
        <div className='sm:hidden flex justify-center items-center'>
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
