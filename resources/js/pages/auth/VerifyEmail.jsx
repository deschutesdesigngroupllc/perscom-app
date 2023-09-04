import React from 'react'
import {Button} from '../../components/Button'
import {AuthLayout} from '../../layouts/Auth'
import {Head, Link, useForm, usePage} from '@inertiajs/react'

export function VerifyEmail() {
  const { flash } = usePage().props

  const { post, processing } = useForm()

  const submit = (e) => {
    e.preventDefault()
    post(route('verification.send'))
  }

  return (
    <AuthLayout>
      <Head title='Email Verification' />

      <div className='mb-4 text-sm prose'>
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        If you didn&apos;t receive the email, we will gladly send you another.
      </div>

      {flash.status === 'verification-link-sent' && (
        <div className='mb-4 text-sm font-medium text-green-600'>
          A new verification link has been sent to the email address you provided during registration.
        </div>
      )}

      <form onSubmit={submit}>
        <div className='mt-4 flex items-center justify-between'>
          <Button processing={processing} color='blue'>
            Resend verification email
          </Button>

          <Link href={route('logout')} method='post' as='button' className='text-sm prose underline'>
            {' '}
            Log out{' '}
          </Link>
        </div>
      </form>
    </AuthLayout>
  )
}

export default VerifyEmail
