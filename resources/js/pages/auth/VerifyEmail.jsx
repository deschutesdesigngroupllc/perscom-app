import React from 'react'
import PropTypes from 'prop-types'
import { Button } from '../../components/Button'
import { AuthLayout } from '../../layouts/Auth'
import { Head, Link, useForm } from '@inertiajs/inertia-react'

export function VerifyEmail({ status }) {
  const { post, processing } = useForm()

  const submit = (e) => {
    e.preventDefault()
    post(route('verification.send'))
  }

  return (
    <AuthLayout>
      <Head title='Email Verification' />

      <div className='mb-4 text-sm text-gray-600'>
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        If you didn&apos;t receive the email, we will gladly send you another.
      </div>

      {status === 'verification-link-sent' && (
        <div className='mb-4 text-sm font-medium text-green-600'>
          A new verification link has been sent to the email address you provided during registration.
        </div>
      )}

      <form onSubmit={submit}>
        <div className='mt-4 flex items-center justify-between'>
          <Button processing={processing} color='blue'>
            Resend verification email
          </Button>

          <Link href={route('logout')} method='post' as='button' className='text-sm text-gray-600 underline hover:text-gray-800'>
            {' '}
            Log out{' '}
          </Link>
        </div>
      </form>
    </AuthLayout>
  )
}

VerifyEmail.propTypes = {
  status: PropTypes.string
}

export default VerifyEmail
