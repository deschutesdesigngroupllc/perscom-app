import React from 'react'
import { useForm } from '@inertiajs/react'
import { RegisterLayout } from '../layouts/Register'
import { Button } from '../components/Button'
import { Input } from '../components/Input'
import { Logo } from '../components/Logo'
import { ValidationErrors } from '../components/ValidationErrors'
import Checkbox from '../components/Checkbox'

export default function Register() {
  const { data, setData, post, processing, errors } = useForm({
    organization: '',
    email: '',
    domain: ''
  })

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value)
  }

  const submit = (e) => {
    e.preventDefault()
    post(route('web.register.store'))
  }

  return (
    <RegisterLayout position='justify-start'>
      <div className='flex flex-col items-start justify-start'>
        <div className='flex w-full items-center justify-center'>
          <a href={route('web.landing.home')}>
            <Logo className='sm:h-18 mb-2 h-16 w-auto md:h-20' />
          </a>
        </div>
        <h1 className='mt-10 text-xl font-bold tracking-tight text-gray-800'>Get started for free</h1>
        <p className='mt-2 text-sm'>No upfront costs or credit card requirements. Cancel at anytime with no questions asked.</p>
      </div>
      <div className='mt-5'>
        <ValidationErrors errors={errors} />
        <form action='#' method='' onSubmit={submit} className='space-y-4'>
          <div>
            <Input
              label='Organization'
              id='organization'
              name='organization'
              type='text'
              autoComplete='organization'
              required
              value={data.organization}
              onChange={onHandleChange}
            />
          </div>
          <div>
            <Input
              label='Email Address'
              id='email'
              name='email'
              type='email'
              autoComplete='email'
              required
              value={data.email}
              onChange={onHandleChange}
            />
          </div>
          <div className='flex items-center justify-between'>
            <div className='flex items-center'>
              <label className='flex items-start'>
                <Checkbox name='privacy' value={data.privacy} onChange={onHandleChange} className='mt-1' />
                <span className='ml-2 text-sm'>
                  I have read and agree to the{' '}
                  <a className='font-semibold' target='_blank' rel='noreferrer' href={route('web.acceptable-use-policy')}>
                    Acceptable Use Policy
                  </a>
                  ,{' '}
                  <a className='font-semibold' target='_blank' rel='noreferrer' href={route('web.cookie-policy')}>
                    Cookie Policy
                  </a>
                  ,{' '}
                  <a className='font-semibold' target='_blank' rel='noreferrer' href={route('web.privacy-policy')}>
                    Privacy Policy
                  </a>{' '}
                  and{' '}
                  <a className='font-semibold' target='_blank' rel='noreferrer' href={route('web.terms-of-service')}>
                    Terms of Service
                  </a>
                  .
                </span>
              </label>
            </div>
          </div>
          <div className='pt-2 text-sm'>
            Already have an account? Find it{' '}
            <a href={route('web.find-my-organization.index')} className='font-semibold'>
              here
            </a>
            .
          </div>
          <div className='pt-5'>
            <Button type='submit' processing={processing} color='blue' className='w-full'>
              Continue <span aria-hidden='true'>&nbsp;&rarr;</span>
            </Button>
          </div>
        </form>
      </div>
    </RegisterLayout>
  )
}
