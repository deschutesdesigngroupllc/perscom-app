import clsx from 'clsx'
import PropTypes from 'prop-types'
import { useState } from 'react'
import { ButtonLink } from './Button'
import { Container } from './Container'

function Plan({ name, price, description, href, features, featured = false }) {
  return (
    <section
      className={clsx('flex w-full flex-col rounded-3xl px-6 py-8 sm:px-8 lg:flex-row lg:px-12', {
        'bg-blue-600': featured,
        'bg-gray-800': !featured
      })}
    >
      <div className='lg:w-1/2 lg:pr-8'>
        <p className='text-5xl font-light tracking-tight text-white'>{price}</p>
        <h3 className='mt-5 text-lg font-semibold text-white'>{name}</h3>
        <p
          className={clsx('mt-2 text-base', {
            'text-white': featured,
            'text-gray-400': !featured
          })}
        >
          {description}
        </p>
        <ButtonLink
          href={href}
          variant={featured ? 'solid' : 'outline'}
          color='white'
          className='mt-8'
          aria-label={`Get started with ${name} plan for ${price}`}
        >
          Get started
        </ButtonLink>
      </div>
      <div className='mt-8 lg:mt-0 lg:flex lg:w-1/2 lg:items-center'>
        <ul
          className={clsx('grid w-full grid-cols-1 gap-3 sm:grid-cols-2', {
            'text-white': featured,
            'text-gray-200': !featured
          })}
        >
          {features.map((feature) => (
            <li key={feature} className='flex'>
              <svg
                aria-hidden='true'
                className={clsx('h-6 w-6 flex-none', {
                  'fill-white stroke-white': featured,
                  'fill-gray-400 stroke-gray-400': !featured
                })}
              >
                <path
                  d='M9.307 12.248a.75.75 0 1 0-1.114 1.004l1.114-1.004ZM11 15.25l-.557.502a.75.75 0 0 0 1.15-.043L11 15.25Zm4.844-5.041a.75.75 0 0 0-1.188-.918l1.188.918Zm-7.651 3.043 2.25 2.5 1.114-1.004-2.25-2.5-1.114 1.004Zm3.4 2.457 4.25-5.5-1.187-.918-4.25 5.5 1.188.918Z'
                  strokeWidth={0}
                />
                <circle cx={12} cy={12} r={8.25} fill='none' strokeWidth={1.5} strokeLinecap='round' strokeLinejoin='round' />
              </svg>
              <span className='ml-4 text-sm text-white'>{feature}</span>
            </li>
          ))}
        </ul>
      </div>
    </section>
  )
}

Plan.propTypes = {
  name: PropTypes.string,
  price: PropTypes.string,
  description: PropTypes.string,
  href: PropTypes.string,
  features: PropTypes.array,
  featured: PropTypes.bool
}

export function Pricing() {
  const [isYearly, setIsYearly] = useState(false)

  const allFeatures = [
    'Access to powerful API',
    'Automated account backups',
    'Custom subdomain',
    'Quality records management',
    'Real-time webhooks',
    'Single sign-on integration',
    'Social login support',
    'Widgets and website integration'
  ]

  return (
    <section id='pricing' aria-labelledby='pricing-title' className='bg-gray-900 py-20 sm:py-32'>
      <Container className='relative'>
        <div className='md:text-center'>
          <h2 className='text-3xl font-bold tracking-tight text-white sm:text-4xl'>
            <span className='relative whitespace-nowrap'>
              <svg
                aria-hidden='true'
                viewBox='0 0 281 40'
                className='absolute top-1/2 left-0 h-[1em] w-full fill-blue-400'
                preserveAspectRatio='none'
              >
                <path
                  fillRule='evenodd'
                  clipRule='evenodd'
                  d='M240.172 22.994c-8.007 1.246-15.477 2.23-31.26 4.114-18.506 2.21-26.323 2.977-34.487 3.386-2.971.149-3.727.324-6.566 1.523-15.124 6.388-43.775 9.404-69.425 7.31-26.207-2.14-50.986-7.103-78-15.624C10.912 20.7.988 16.143.734 14.657c-.066-.381.043-.344 1.324.456 10.423 6.506 49.649 16.322 77.8 19.468 23.708 2.65 38.249 2.95 55.821 1.156 9.407-.962 24.451-3.773 25.101-4.692.074-.104.053-.155-.058-.135-1.062.195-13.863-.271-18.848-.687-16.681-1.389-28.722-4.345-38.142-9.364-15.294-8.15-7.298-19.232 14.802-20.514 16.095-.934 32.793 1.517 47.423 6.96 13.524 5.033 17.942 12.326 11.463 18.922l-.859.874.697-.006c2.681-.026 15.304-1.302 29.208-2.953 25.845-3.07 35.659-4.519 54.027-7.978 9.863-1.858 11.021-2.048 13.055-2.145a61.901 61.901 0 0 0 4.506-.417c1.891-.259 2.151-.267 1.543-.047-.402.145-2.33.913-4.285 1.707-4.635 1.882-5.202 2.07-8.736 2.903-3.414.805-19.773 3.797-26.404 4.829Zm40.321-9.93c.1-.066.231-.085.29-.041.059.043-.024.096-.183.119-.177.024-.219-.007-.107-.079ZM172.299 26.22c9.364-6.058 5.161-12.039-12.304-17.51-11.656-3.653-23.145-5.47-35.243-5.576-22.552-.198-33.577 7.462-21.321 14.814 12.012 7.205 32.994 10.557 61.531 9.831 4.563-.116 5.372-.288 7.337-1.559Z'
                />
              </svg>
              <span className='relative'>Simple pricing,</span>
            </span>{' '}
            for everyone.
          </h2>
          <p className='mx-auto mt-4 text-base text-gray-400'>One plan with everything included. Choose monthly or yearly billing.</p>
        </div>

        <div className='mt-16 flex justify-center'>
          <div className='relative rounded-lg bg-gray-800 p-1'>
            <button
              className={clsx('relative w-32 py-2 text-sm font-medium transition-all', {
                'rounded-md bg-blue-600 text-white shadow-sm': !isYearly,
                'text-gray-400 hover:text-white': isYearly
              })}
              onClick={() => setIsYearly(false)}
            >
              Monthly
            </button>
            <button
              className={clsx('relative w-28 py-2 text-sm font-medium transition-all', {
                'rounded-md bg-blue-600 text-white shadow-sm': isYearly,
                'text-gray-400 hover:text-white': !isYearly
              })}
              onClick={() => setIsYearly(true)}
            >
              Yearly
              <span className='absolute -top-5 left-1/2 -translate-x-1/2 transform rounded bg-green-500 px-2 py-1 text-xs text-nowrap text-white'>
                Save 17%
              </span>
            </button>
          </div>
        </div>

        <div className='mt-16'>
          <Plan
            featured
            name='Professional'
            price={isYearly ? '$200/year' : '$20/month'}
            description='Complete personnel management solution with all features included. Perfect for organizations of any size.'
            href={route('web.register.index')}
            features={allFeatures}
          />
        </div>
      </Container>
    </section>
  )
}
