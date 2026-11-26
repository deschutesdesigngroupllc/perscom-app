import { useForm } from '@inertiajs/react'
import { Turnstile } from '@marsidev/react-turnstile'
import PropTypes from 'prop-types'
import { Button } from '../components/Button'
import Checkbox from '../components/Checkbox'
import { Input } from '../components/Input'
import { Logo } from '../components/Logo'
import { ValidationErrors } from '../components/ValidationErrors'
import { RegisterLayout } from '../layouts/Register'

const turnstileSiteKey = import.meta.env.VITE_CLOUDFLARE_TURNSTILE_SITE_KEY

export default function Register({ organizationTypes = [], baseDomain = '' }) {
  const { data, setData, post, processing, errors } = useForm({
    token: '',
    organization: '',
    name: '',
    email: '',
    subdomain: '',
    organization_type: ''
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
        <p className='mt-2 text-sm'>No upfront costs or credit card requirements. Cancel anytime, no questions asked.</p>
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
            <label htmlFor='organization_type' className='mb-1.5 block text-sm font-medium'>
              Type of Organization
            </label>
            <div className='relative'>
              <select
                id='organization_type'
                name='organization_type'
                value={data.organization_type}
                onChange={onHandleChange}
                className='block w-full rounded-md border-0 bg-white px-3 py-2 text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset transition-shadow placeholder:text-gray-400 hover:ring-gray-400 focus:ring-2 focus:ring-brand-600 focus:ring-inset focus:outline-none sm:text-sm appearance-none pr-10'
              >
                <option value=''>Choose one (optional)</option>
                {organizationTypes.map((type) => (
                  <option key={type.value} value={type.value}>
                    {type.label}
                  </option>
                ))}
              </select>
              <svg
                aria-hidden='true'
                viewBox='0 0 20 20'
                fill='currentColor'
                className='pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-gray-500'
              >
                <path
                  fillRule='evenodd'
                  d='M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z'
                  clipRule='evenodd'
                />
              </svg>
            </div>
            <p className='mt-1 text-xs text-gray-500'>We&apos;ll start you with a matching structure of ranks, units and positions.</p>
          </div>
          <div>
            <Input
              label='Your PERSCOM URL'
              id='subdomain'
              name='subdomain'
              type='text'
              autoComplete='off'
              placeholder='your-unit'
              append={baseDomain ? `.${baseDomain}` : null}
              value={data.subdomain}
              onChange={(event) => setData('subdomain', event.target.value.toLowerCase().replace(/[^a-z0-9-]/g, ''))}
            />
            <p className='mt-1 text-xs text-gray-500'>Optional. Letters, numbers and dashes. You can change it later.</p>
          </div>
          <div>
            <Input
              label='Your Name'
              id='name'
              name='name'
              type='text'
              autoComplete='name'
              required
              value={data.name}
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
            Create My Organization <span aria-hidden='true'>&nbsp;&rarr;</span>
          </Button>
        </form>
      </div>
    </RegisterLayout>
  )
}

Register.propTypes = {
  organizationTypes: PropTypes.arrayOf(PropTypes.shape({ value: PropTypes.string, label: PropTypes.string })),
  baseDomain: PropTypes.string
}
