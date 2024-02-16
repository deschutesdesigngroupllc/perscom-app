import React, { useEffect } from 'react'
import { AuthLayout } from '../../layouts/Auth'
import { Button } from '../../components/Button'
import { Input } from '../../components/Input'
import { Label } from '../../components/Label'
import { ValidationErrors } from '../../components/ValidationErrors'
import { Head, Link, useForm } from '@inertiajs/react'
import PropTypes from 'prop-types'

export function Register({ status, enableSocialLogin, googleLogin, discordLogin, githubLogin }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })

  useEffect(() => {
    return () => {
      reset('password', 'password_confirmation')
    }
  }, [])

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value)
  }

  const submit = (e) => {
    e.preventDefault()
    post(route('register'))
  }

  return (
    <AuthLayout>
      <Head title='Register' />

      {status && <div className='mb-4 text-sm font-medium text-green-600'>{status}</div>}

      <ValidationErrors errors={errors} />

      <form onSubmit={submit} className='space-y-4'>
        <div>
          <Label forInput='name' value='Name' />
          <Input
            type='text'
            name='name'
            value={data.name}
            className='mt-1 block w-full'
            autoComplete='name'
            onChange={onHandleChange}
            required
          />
        </div>
        <div>
          <Label forInput='email' value='Email' />
          <Input
            type='email'
            name='email'
            value={data.email}
            className='mt-1 block w-full'
            autoComplete='username'
            onChange={onHandleChange}
            required
          />
        </div>
        <div>
          <Label forInput='password' value='Password' />
          <Input
            type='password'
            name='password'
            value={data.password}
            className='mt-1 block w-full'
            autoComplete='new-password'
            onChange={onHandleChange}
            required
          />
        </div>
        <div>
          <Label forInput='password_confirmation' value='Confirm Password' />
          <Input
            type='password'
            name='password_confirmation'
            value={data.password_confirmation}
            className='mt-1 block w-full'
            onChange={onHandleChange}
            required
          />
        </div>
        <div className='flex justify-end'>
          <Link href={route('login')} className='text-sm underline'>
            Already registered?
          </Link>
        </div>
        <Button className='w-full' processing={processing} color='blue'>
          Register
        </Button>
      </form>

      {enableSocialLogin && (
        <div className='mt-6'>
          <div className='relative'>
            <div className='absolute inset-0 flex items-center'>
              <div className='w-full border-t border-gray-300' />
            </div>
            <div className='relative flex justify-center'>
              <span className='bg-white px-2 text-sm'>Or continue with</span>
            </div>
          </div>

          <div className='mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3'>
            <div>
              <a
                href={googleLogin}
                className='inline-flex w-full items-center justify-center space-x-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium shadow-sm hover:bg-gray-50'
              >
                <span className='sr-only'>Sign in with Google</span>
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 488 512' className='h-5 w-5 fill-gray-600'>
                  <path d='M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z' />
                </svg>
                <span className='block text-sm font-bold tracking-tight sm:hidden'>GOOGLE</span>
              </a>
            </div>

            <div>
              <a
                href={discordLogin}
                className='inline-flex w-full items-center justify-center space-x-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium shadow-sm hover:bg-gray-50'
              >
                <span className='sr-only'>Sign in with Discord</span>
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 512' className='h-5 w-5 fill-gray-600'>
                  <path d='M524.5 69.8a1.5 1.5 0 0 0 -.8-.7A485.1 485.1 0 0 0 404.1 32a1.8 1.8 0 0 0 -1.9 .9 337.5 337.5 0 0 0 -14.9 30.6 447.8 447.8 0 0 0 -134.4 0 309.5 309.5 0 0 0 -15.1-30.6 1.9 1.9 0 0 0 -1.9-.9A483.7 483.7 0 0 0 116.1 69.1a1.7 1.7 0 0 0 -.8 .7C39.1 183.7 18.2 294.7 28.4 404.4a2 2 0 0 0 .8 1.4A487.7 487.7 0 0 0 176 479.9a1.9 1.9 0 0 0 2.1-.7A348.2 348.2 0 0 0 208.1 430.4a1.9 1.9 0 0 0 -1-2.6 321.2 321.2 0 0 1 -45.9-21.9 1.9 1.9 0 0 1 -.2-3.1c3.1-2.3 6.2-4.7 9.1-7.1a1.8 1.8 0 0 1 1.9-.3c96.2 43.9 200.4 43.9 295.5 0a1.8 1.8 0 0 1 1.9 .2c2.9 2.4 6 4.9 9.1 7.2a1.9 1.9 0 0 1 -.2 3.1 301.4 301.4 0 0 1 -45.9 21.8 1.9 1.9 0 0 0 -1 2.6 391.1 391.1 0 0 0 30 48.8 1.9 1.9 0 0 0 2.1 .7A486 486 0 0 0 610.7 405.7a1.9 1.9 0 0 0 .8-1.4C623.7 277.6 590.9 167.5 524.5 69.8zM222.5 337.6c-29 0-52.8-26.6-52.8-59.2S193.1 219.1 222.5 219.1c29.7 0 53.3 26.8 52.8 59.2C275.3 311 251.9 337.6 222.5 337.6zm195.4 0c-29 0-52.8-26.6-52.8-59.2S388.4 219.1 417.9 219.1c29.7 0 53.3 26.8 52.8 59.2C470.7 311 447.5 337.6 417.9 337.6z' />
                </svg>
                <span className='block text-sm font-bold tracking-tight sm:hidden'>DISCORD</span>
              </a>
            </div>

            <div>
              <a
                href={githubLogin}
                className='inline-flex w-full items-center justify-center space-x-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium shadow-sm hover:bg-gray-50'
              >
                <span className='sr-only'>Sign in with GitHub</span>
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 496 512' className='h-5 w-5 fill-gray-600'>
                  <path d='M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3 .3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5 .3-6.2 2.3zm44.2-1.7c-2.9 .7-4.9 2.6-4.6 4.9 .3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3 .7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3 .3 2.9 2.3 3.9 1.6 1 3.6 .7 4.3-.7 .7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3 .7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3 .7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z' />
                </svg>
                <span className='block text-sm font-bold tracking-tight sm:hidden'>GITHUB</span>
              </a>
            </div>
          </div>
        </div>
      )}
    </AuthLayout>
  )
}

Register.propTypes = {
  status: PropTypes.string,
  enableSocialLogin: PropTypes.bool,
  githubLogin: PropTypes.string,
  discordLogin: PropTypes.string
}

export default Register
