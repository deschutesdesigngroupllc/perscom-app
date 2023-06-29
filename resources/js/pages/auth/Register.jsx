import React, { useEffect } from 'react'
import { AuthLayout } from '../../layouts/Auth'
import { Button } from '../../components/Button'
import { Input } from '../../components/Input'
import { Label } from '../../components/Label'
import { ValidationErrors } from '../../components/ValidationErrors'
import { Head, Link, useForm } from '@inertiajs/inertia-react'
import PropTypes from 'prop-types'

export function Register({ status, enableSocialLogin, githubLogin, discordLogin }) {
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
          <Link href={route('login')} className='text-sm text-gray-600 underline hover:text-gray-800'>
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
            <div className='relative flex justify-center text-sm'>
              <span className='bg-white px-2 text-gray-600'>Or continue with</span>
            </div>
          </div>

          <div className='mt-6 grid grid-cols-2 gap-3'>
            <div>
              <a
                href={discordLogin}
                className='inline-flex w-full justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50'
              >
                <span className='sr-only'>Sign in with Discord</span>
                <svg className='h-5 w-5 text-gray-600' viewBox='0 0 71 55' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                  <path d='M60.1045 4.8978C55.5792 2.8214 50.7265 1.2916 45.6527 0.41542C45.5603 0.39851 45.468 0.440769 45.4204 0.525289C44.7963 1.6353 44.105 3.0834 43.6209 4.2216C38.1637 3.4046 32.7345 3.4046 27.3892 4.2216C26.905 3.0581 26.1886 1.6353 25.5617 0.525289C25.5141 0.443589 25.4218 0.40133 25.3294 0.41542C20.2584 1.2888 15.4057 2.8186 10.8776 4.8978C10.8384 4.9147 10.8048 4.9429 10.7825 4.9795C1.57795 18.7309 -0.943561 32.1443 0.293408 45.3914C0.299005 45.4562 0.335386 45.5182 0.385761 45.5576C6.45866 50.0174 12.3413 52.7249 18.1147 54.5195C18.2071 54.5477 18.305 54.5139 18.3638 54.4378C19.7295 52.5728 20.9469 50.6063 21.9907 48.5383C22.0523 48.4172 21.9935 48.2735 21.8676 48.2256C19.9366 47.4931 18.0979 46.6 16.3292 45.5858C16.1893 45.5041 16.1781 45.304 16.3068 45.2082C16.679 44.9293 17.0513 44.6391 17.4067 44.3461C17.471 44.2926 17.5606 44.2813 17.6362 44.3151C29.2558 49.6202 41.8354 49.6202 53.3179 44.3151C53.3935 44.2785 53.4831 44.2898 53.5502 44.3433C53.9057 44.6363 54.2779 44.9293 54.6529 45.2082C54.7816 45.304 54.7732 45.5041 54.6333 45.5858C52.8646 46.6197 51.0259 47.4931 49.0921 48.2228C48.9662 48.2707 48.9102 48.4172 48.9718 48.5383C50.038 50.6034 51.2554 52.5699 52.5959 54.435C52.6519 54.5139 52.7526 54.5477 52.845 54.5195C58.6464 52.7249 64.529 50.0174 70.6019 45.5576C70.6551 45.5182 70.6887 45.459 70.6943 45.3942C72.1747 30.0791 68.2147 16.7757 60.1968 4.9823C60.1772 4.9429 60.1437 4.9147 60.1045 4.8978ZM23.7259 37.3253C20.2276 37.3253 17.3451 34.1136 17.3451 30.1693C17.3451 26.225 20.1717 23.0133 23.7259 23.0133C27.308 23.0133 30.1626 26.2532 30.1066 30.1693C30.1066 34.1136 27.28 37.3253 23.7259 37.3253ZM47.3178 37.3253C43.8196 37.3253 40.9371 34.1136 40.9371 30.1693C40.9371 26.225 43.7636 23.0133 47.3178 23.0133C50.9 23.0133 53.7545 26.2532 53.6986 30.1693C53.6986 34.1136 50.9 37.3253 47.3178 37.3253Z' />
                </svg>
              </a>
            </div>

            <div>
              <a
                href={githubLogin}
                className='inline-flex w-full justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50'
              >
                <span className='sr-only'>Sign in with GitHub</span>
                <svg className='h-5 w-5' aria-hidden='true' fill='currentColor' viewBox='0 0 20 20'>
                  <path
                    fillRule='evenodd'
                    d='M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z'
                    clipRule='evenodd'
                  />
                </svg>
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
