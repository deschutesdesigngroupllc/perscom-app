import React from 'react'
import PropTypes from 'prop-types'
import {Logo} from '../components/Logo'
import {useForm} from '@inertiajs/react'

export default function Error({ status, title = null, message = null, back = null, showLink = true, showLogout = false }) {
  const { post } = useForm({})
  function logout(e) {
    e.preventDefault()
    post('/logout')
  }

  const header =
    title ??
    {
      401: 'Unauthorized.',
      402: 'Subscription Required.',
      403: 'Forbidden.',
      404: 'Not Found.',
      405: 'Method Not Allowed',
      419: 'Page Expired.',
      429: 'Too Many Requests.',
      500: 'Server Error.',
      503: 'Service Unavailable.'
    }[status] ??
    'Unknown Error'

  const description =
    message ??
    {
      401: 'Please try logging in to access this page.',
      402: 'The account requires a subscription to continue. Please contact your account administrator.',
      403: 'Sorry, the page you are trying to access is off limits.',
      404: 'Sorry, the page you are looking for could not be found.',
      419: 'The page you are trying to access is no longer available.',
      429: 'Woah, slow down there. Please wait a minute before accessing this page again.',
      500: 'Whoops, something went wrong on our servers.',
      503: 'Sorry, we are doing some maintenance. Please check back soon.'
    }[status] ??
    'We received an unknown error in the last request.'

  return (
    <div className='min-h-full pt-16 pb-12 flex flex-col bg-gray-100'>
      <main className='flex-grow flex flex-col justify-center max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8'>
        <div className='flex-shrink-0 flex justify-center'>
          <a href='/' className='inline-flex'>
            <span className='sr-only'>PERSCOM</span>
            <Logo className='h-12 w-auto' />
          </a>
        </div>
        <div className='py-16'>
          <div className='text-center'>
            <p className='text-sm font-semibold text-blue-600 uppercase tracking-wide'>{status} error</p>
            <h1 className='mt-2 text-4xl font-extrabold prose text-black tracking-tight sm:text-5xl'>{header}</h1>
            <p className='mt-2 text-base mx-auto prose'>{description}</p>
            {showLink && (
              <div className='mt-6'>
                <a href={back ?? route('web.landing.home')} className='text-base font-medium text-blue-600 hover:text-blue-600'>
                  Go back<span aria-hidden='true'> &rarr;</span>
                </a>
              </div>
            )}
          </div>
        </div>
      </main>
      <footer className='flex-shrink-0 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8'>
        <nav className='flex flex-col sm:flex-row justify-center items-center space-x-4'>
          <a
            href='https://community.deschutesdesigngroup.com/'
            className='rounded-lg px-2 py-1 prose text-sm hover:bg-gray-200'
            target='_blank'
            rel='noreferrer'
          >
            Community Forums
          </a>
          <span className='inline-block border-l border-gray-300' aria-hidden='true'></span>
          <a
            href='https://docs.perscom.io'
            className='rounded-lg px-2 py-1 prose text-sm hover:bg-gray-200'
            target='_blank'
            rel='noreferrer'
          >
            Documentation
          </a>
          <span className='inline-block border-l border-gray-300' aria-hidden='true'></span>
          <a
            href='https://support.deschutesdesigngroup.com/'
            className='rounded-lg px-2 py-1 prose text-sm hover:bg-gray-200'
            target='_blank'
            rel='noreferrer'
          >
            Help Desk
          </a>
          <span className='inline-block border-l border-gray-300' aria-hidden='true'></span>
          <a
            href='https://support.deschutesdesigngroup.com/hc/en-us/requests/new'
            className='rounded-lg px-2 py-1 prose text-sm hover:bg-gray-200'
            target='_blank'
            rel='noreferrer'
          >
            Submit A Ticket
          </a>
          {showLogout && (
            <>
              <span className='inline-block border-l border-gray-300' aria-hidden='true'></span>
              <form onSubmit={logout}>
                <button
                  type='submit'
                  className='rounded-lg prose text-sm px-2 py-1 text-red-700 hover:bg-red-100 hover:text-red-800'
                  target='_blank'
                >
                  Logout
                </button>
              </form>
            </>
          )}
        </nav>
      </footer>
    </div>
  )
}

Error.propTypes = {
  status: PropTypes.number,
  title: PropTypes.string,
  message: PropTypes.string,
  back: PropTypes.string,
  showLink: PropTypes.bool,
  showLogout: PropTypes.bool
}
