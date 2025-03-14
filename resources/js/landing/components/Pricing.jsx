import React from 'react'
import clsx from 'clsx'
import { ButtonLink } from './Button'
import { Container } from './Container'
import PropTypes from 'prop-types'

import { CheckIcon } from '@heroicons/react/20/solid'

const includedFeatures = [
  'Private forum access',
  'Member resources',
  'Entry to annual conference',
  'Official member t-shirt',
]

export function Pricing() {
  return (
    <div className="bg-gray-900 py-24 sm:py-32">
      <div className="mx-auto max-w-7xl px-6 lg:px-8">
        <div className="mx-auto max-w-4xl sm:text-center">
          <h2 className="text-pretty text-5xl font-semibold tracking-tight text-white sm:text-balance sm:text-6xl">
            <span className='relative whitespace-nowrap'>
               <svg
                            aria-hidden='true'
                            viewBox='0 0 281 40'
                            className='absolute left-0 top-1/2 h-[1em] w-full fill-blue-400'
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
          <p className="mx-auto mt-12 max-w-2xl text-pretty text-lg font-medium text-gray-500 sm:text-xl/8">
            Distinctio et nulla eum soluta et neque labore quibusdam. Saepe et quasi iusto modi velit ut non voluptas
            in. Explicabo id ut laborum.
          </p>
        </div>
        <div className="mx-auto mt-16 max-w-2xl rounded-3xl ring-1 ring-gray-200 sm:mt-20 lg:mx-0 lg:flex lg:max-w-none bg-white">
          <div className="p-8 sm:p-10 lg:flex-auto">
            <h3 className="text-3xl font-semibold tracking-tight text-gray-900">Simple membership</h3>
            <p className="mt-6 text-base/7 text-gray-600">
              Lorem ipsum dolor sit amet consect etur adipisicing elit. Itaque amet indis perferendis blanditiis
              repellendus etur quidem assumenda.
            </p>
            <div className="mt-10 flex items-center gap-x-4">
              <h4 className="flex-none text-sm/6 font-semibold text-blue-600">What’s included</h4>
              <div className="h-px flex-auto bg-gray-100" />
            </div>
            <ul role="list" className="mt-8 grid grid-cols-1 gap-4 text-sm/6 text-gray-600 sm:grid-cols-2 sm:gap-6">
              {includedFeatures.map((feature) => (
                <li key={feature} className="flex gap-x-3">
                  <CheckIcon aria-hidden="true" className="h-6 w-5 flex-none text-blue-600" />
                  {feature}
                </li>
              ))}
            </ul>
          </div>
          <div className="-mt-2 p-2 lg:mt-0 lg:w-full lg:max-w-md lg:shrink-0">
            <div className="rounded-2xl bg-gray-50 py-10 text-center ring-1 ring-inset ring-gray-900/5 lg:flex lg:flex-col lg:justify-center lg:py-16">
              <div className="mx-auto max-w-xs px-8">
                <p className="text-base font-semibold text-gray-600">Pay once, own it forever</p>
                <p className="mt-6 flex items-baseline justify-center gap-x-2">
                  <span className="text-5xl font-semibold tracking-tight text-gray-900">$20</span>
                  <span className="text-sm/6 font-semibold tracking-wide text-gray-600">USD</span>
                </p>
                <a
                  href="#"
                  className="mt-10 block w-full rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                  Get access
                </a>
                <p className="mt-6 text-xs/5 text-gray-600">
                  Invoices and receipts available for easy company reimbursement
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}


// function Plan({ name, price, description, href, features, featured = false }) {
//   return (
//     <section
//       className={clsx('flex flex-col rounded-3xl px-6 sm:px-8', {
//         'order-first bg-blue-600 py-8 lg:order-none': featured,
//         'lg:py-8': !featured
//       })}
//     >
//       <h3 className='mt-5 text-lg text-white'>{name}</h3>
//       <p
//         className={clsx('mt-2 text-base', {
//           'text-white': featured,
//           'text-gray-400': !featured
//         })}
//       >
//         {description}
//       </p>
//       <p className='order-first text-5xl font-light tracking-tight text-white'>{price}</p>
//       <ul
//         className={clsx('order-last mt-10 space-y-3', {
//           'text-white': featured,
//           'text-gray-200': !featured
//         })}
//       >
//         {features.map((feature) => (
//           <li key={feature} className='flex'>
//             <svg
//               aria-hidden='true'
//               className={clsx('h-6 w-6 flex-none', {
//                 'fill-white stroke-white': featured,
//                 'fill-gray-400 stroke-gray-400': !featured
//               })}
//             >
//               <path
//                 d='M9.307 12.248a.75.75 0 1 0-1.114 1.004l1.114-1.004ZM11 15.25l-.557.502a.75.75 0 0 0 1.15-.043L11 15.25Zm4.844-5.041a.75.75 0 0 0-1.188-.918l1.188.918Zm-7.651 3.043 2.25 2.5 1.114-1.004-2.25-2.5-1.114 1.004Zm3.4 2.457 4.25-5.5-1.187-.918-4.25 5.5 1.188.918Z'
//                 strokeWidth={0}
//               />
//               <circle cx={12} cy={12} r={8.25} fill='none' strokeWidth={1.5} strokeLinecap='round' strokeLinejoin='round' />
//             </svg>
//             <span className='ml-4 text-sm text-white'>{feature}</span>
//           </li>
//         ))}
//       </ul>
//       <ButtonLink
//         href={href}
//         variant={featured ? 'solid' : 'outline'}
//         color='white'
//         className='mt-8'
//         aria-label={`Get started with ${name} plan for ${price}`}
//       >
//         {' '}
//         Get started{' '}
//       </ButtonLink>
//     </section>
//   )
// }
//
// Plan.propTypes = {
//   name: PropTypes.string,
//   price: PropTypes.string,
//   description: PropTypes.string,
//   href: PropTypes.string,
//   features: PropTypes.array,
//   featured: PropTypes.bool
// }
//
// export function Pricing() {
//   return (
//     <section id='pricing' aria-labelledby='pricing-title' className='bg-gray-900 py-20 sm:py-32'>
//       <Container className='relative'>
//         <div className='md:text-center'>
//           <h2 className='text-3xl font-bold tracking-tight text-white sm:text-4xl'>
//             <span className='relative whitespace-nowrap'>
//               <svg
//                 aria-hidden='true'
//                 viewBox='0 0 281 40'
//                 className='absolute left-0 top-1/2 h-[1em] w-full fill-blue-400'
//                 preserveAspectRatio='none'
//               >
//                 <path
//                   fillRule='evenodd'
//                   clipRule='evenodd'
//                   d='M240.172 22.994c-8.007 1.246-15.477 2.23-31.26 4.114-18.506 2.21-26.323 2.977-34.487 3.386-2.971.149-3.727.324-6.566 1.523-15.124 6.388-43.775 9.404-69.425 7.31-26.207-2.14-50.986-7.103-78-15.624C10.912 20.7.988 16.143.734 14.657c-.066-.381.043-.344 1.324.456 10.423 6.506 49.649 16.322 77.8 19.468 23.708 2.65 38.249 2.95 55.821 1.156 9.407-.962 24.451-3.773 25.101-4.692.074-.104.053-.155-.058-.135-1.062.195-13.863-.271-18.848-.687-16.681-1.389-28.722-4.345-38.142-9.364-15.294-8.15-7.298-19.232 14.802-20.514 16.095-.934 32.793 1.517 47.423 6.96 13.524 5.033 17.942 12.326 11.463 18.922l-.859.874.697-.006c2.681-.026 15.304-1.302 29.208-2.953 25.845-3.07 35.659-4.519 54.027-7.978 9.863-1.858 11.021-2.048 13.055-2.145a61.901 61.901 0 0 0 4.506-.417c1.891-.259 2.151-.267 1.543-.047-.402.145-2.33.913-4.285 1.707-4.635 1.882-5.202 2.07-8.736 2.903-3.414.805-19.773 3.797-26.404 4.829Zm40.321-9.93c.1-.066.231-.085.29-.041.059.043-.024.096-.183.119-.177.024-.219-.007-.107-.079ZM172.299 26.22c9.364-6.058 5.161-12.039-12.304-17.51-11.656-3.653-23.145-5.47-35.243-5.576-22.552-.198-33.577 7.462-21.321 14.814 12.012 7.205 32.994 10.557 61.531 9.831 4.563-.116 5.372-.288 7.337-1.559Z'
//                 />
//               </svg>
//               <span className='relative'>Simple pricing,</span>
//             </span>{' '}
//             for everyone.
//           </h2>
//           <p className='mx-auto mt-4 text-base text-gray-400'>
//             We have pricing options for all organizations. Scale up and down as your needs change.
//           </p>
//         </div>
//         <div className='-mx-4 mt-16 grid max-w-2xl grid-cols-1 gap-y-10 sm:mx-auto lg:-mx-8 lg:max-w-none lg:grid-cols-3 xl:mx-0 xl:gap-x-8'>
//           <Plan
//             featured
//             name='Pro'
//             price='$20'
//             description='For growing organizations who wish to integrate their personnel data into third-party services without enterprise level complexity.'
//             href={route('web.register.index')}
//             features={[
//               'Access to powerful API',
//               'Optional paid premium features',
//               'Widgets and website integration',
//               'Custom subdomain',
//               'Ticket and email support'
//             ]}
//           />
//         </div>
//       </Container>
//     </section>
//   )
// }
