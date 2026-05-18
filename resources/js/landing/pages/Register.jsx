import { useForm } from '@inertiajs/react'
import { Turnstile } from '@marsidev/react-turnstile'
import { Button } from '../components/Button'
import Checkbox from '../components/Checkbox'
import { Input } from '../components/Input'
import { Logo } from '../components/Logo'
import { ValidationErrors } from '../components/ValidationErrors'
import { RegisterLayout } from '../layouts/Register'

const turnstileSiteKey = import.meta.env.VITE_CLOUDFLARE_TURNSTILE_SITE_KEY

export default function Register() {
  const { data, setData, post, processing, errors } = useForm({
    token: '',
    organization: '',
    email: '',
    domain: ''
  })

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value)
  }

  const onTurnstileSuccess = (token) => {
    setData('token', token)
  }

  const submit = (e) => {
    e.preventDefault()
    post(route('web.register.store'))
  }

  return (
    <RegisterLayout position='justify-start'>
      <div className='flex flex-col items-start justify-start'>
        <div className='flex w-full items-center justify-center'>
          <a href={route('filament.app.pages.dashboard')}>
            <Logo className='mb-2 h-16 w-auto sm:h-18 md:h-20' />
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
                  <a className='font-semibold' target='_blank' rel='noreferrer' href='https://perscom.io/legal/acceptable-use'>
                    Acceptable Use Policy
                  </a>
                  ,{' '}
                  <a className='font-semibold' target='_blank' rel='noreferrer' href='https://perscom.io/legal/cookies'>
                    Cookie Policy
                  </a>
                  ,{' '}
                  <a className='font-semibold' target='_blank' rel='noreferrer' href='https://perscom.io/legal/privacy'>
                    Privacy Policy
                  </a>{' '}
                  and{' '}
                  <a className='font-semibold' target='_blank' rel='noreferrer' href='https://perscom.io/legal/terms'>
                    Terms of Service
                  </a>
                  .
                </span>
              </label>
            </div>
          </div>
          {turnstileSiteKey && <Turnstile siteKey={turnstileSiteKey} onSuccess={onTurnstileSuccess} />}
          <Button type='submit' processing={processing} color='blue' className='w-full'>
            Continue <span aria-hidden='true'>&nbsp;&rarr;</span>
          </Button>
        </form>
      </div>
    </RegisterLayout>
  )
}
