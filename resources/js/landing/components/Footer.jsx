import React from 'react'
import { Logo } from './Logo'
import Container from '@/landing/components/Container.jsx'

const navigation = {
  perscom: [
    { name: 'Home', href: '/' },
    { name: 'Features', href: '#features' },
    { name: 'Pricing', href: '#pricing' }
  ],
  support: [
    { name: 'Join Slack Discussion', href: 'https://perscom.io/slack' },
    { name: 'Documentation', href: 'https://docs.perscom.io' },
    { name: 'API Reference', href: 'https://docs.perscom.io/api-reference/introduction' }
  ],
  references: [
    { name: 'Sign Up', href: route('web.register.index') },
    { name: 'Find My Organization', href: route('web.find-my-organization.index') },
    { name: 'Demo', href: 'https://demo.perscom.io' }
  ],
  legal: [
    { name: 'Acceptable Use Policy', href: route('web.acceptable-use-policy') },
    { name: 'Cookie Policy', href: route('web.cookie-policy') },
    { name: 'Privacy Policy', href: route('web.privacy-policy') },
    { name: 'Terms of Service', href: route('web.terms-of-service') }
  ],
  social: [
    {
      name: 'GitHub',
      href: 'https://github.com/deschutesdesigngroupllc',
      icon: (props) => (
        <svg fill='currentColor' viewBox='0 0 24 24' {...props}>
          <path
            fillRule='evenodd'
            d='M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z'
            clipRule='evenodd'
          />
        </svg>
      )
    },
    {
      name: 'Slack',
      href: 'https://perscom.io/slack',
      icon: (props) => (
        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' {...props}>
          <path d='M94.1 315.1c0 25.9-21.2 47.1-47.1 47.1S0 341 0 315.1c0-25.9 21.2-47.1 47.1-47.1h47.1v47.1zm23.7 0c0-25.9 21.2-47.1 47.1-47.1s47.1 21.2 47.1 47.1v117.8c0 25.9-21.2 47.1-47.1 47.1s-47.1-21.2-47.1-47.1V315.1zm47.1-189c-25.9 0-47.1-21.2-47.1-47.1S139 32 164.9 32s47.1 21.2 47.1 47.1v47.1H164.9zm0 23.7c25.9 0 47.1 21.2 47.1 47.1s-21.2 47.1-47.1 47.1H47.1C21.2 244 0 222.8 0 196.9s21.2-47.1 47.1-47.1H164.9zm189 47.1c0-25.9 21.2-47.1 47.1-47.1 25.9 0 47.1 21.2 47.1 47.1s-21.2 47.1-47.1 47.1h-47.1V196.9zm-23.7 0c0 25.9-21.2 47.1-47.1 47.1-25.9 0-47.1-21.2-47.1-47.1V79.1c0-25.9 21.2-47.1 47.1-47.1 25.9 0 47.1 21.2 47.1 47.1V196.9zM283.1 385.9c25.9 0 47.1 21.2 47.1 47.1 0 25.9-21.2 47.1-47.1 47.1-25.9 0-47.1-21.2-47.1-47.1v-47.1h47.1zm0-23.7c-25.9 0-47.1-21.2-47.1-47.1 0-25.9 21.2-47.1 47.1-47.1h117.8c25.9 0 47.1 21.2 47.1 47.1 0 25.9-21.2 47.1-47.1 47.1H283.1z' />
        </svg>
      )
    }
  ]
}

export function Footer() {
  return (
    <footer className='bg-white'>
      <Container className='pb-8 pt-16 sm:pt-24 lg:px-8 lg:pt-32'>
        <div className='xl:grid xl:grid-cols-3 xl:gap-8'>
          <div className='space-y-8'>
            <Logo />
            <p className='text-balance text-sm/6'>Personnel management made easy for high-performing, results-driven organizations.</p>
            <div className='flex gap-x-6'>
              {navigation.social.map((item) => (
                <a key={item.name} href={item.href} className='hover:text-gray-800'>
                  <span className='sr-only'>{item.name}</span>
                  <item.icon aria-hidden='true' className='size-6' />
                </a>
              ))}
            </div>
          </div>
          <div className='mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0'>
            <div className='md:grid md:grid-cols-2 md:gap-8'>
              <div>
                <h3 className='text-sm/6 font-semibold'>PERSCOM</h3>
                <ul role='list' className='mt-6 space-y-4'>
                  {navigation.perscom.map((item) => (
                    <li key={item.name}>
                      <a href={item.href} className='text-sm/6 hover:text-gray-900'>
                        {item.name}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
              <div className='mt-10 md:mt-0'>
                <h3 className='text-sm/6 font-semibold'>Support</h3>
                <ul role='list' className='mt-6 space-y-4'>
                  {navigation.support.map((item) => (
                    <li key={item.name}>
                      <a href={item.href} className='text-sm/6 hover:text-gray-900'>
                        {item.name}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
            <div className='md:grid md:grid-cols-2 md:gap-8'>
              <div>
                <h3 className='text-sm/6 font-semibold'>Get Started</h3>
                <ul role='list' className='mt-6 space-y-4'>
                  {navigation.references.map((item) => (
                    <li key={item.name}>
                      <a href={item.href} className='text-sm/6 hover:text-gray-900'>
                        {item.name}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
              <div className='mt-10 md:mt-0'>
                <h3 className='text-sm/6 font-semibold'>Legal</h3>
                <ul role='list' className='mt-6 space-y-4'>
                  {navigation.legal.map((item) => (
                    <li key={item.name}>
                      <a href={item.href} className='text-sm/6 hover:text-gray-900'>
                        {item.name}
                      </a>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div className='mt-16 border-t border-gray-900/10 pt-8 sm:mt-20 lg:mt-24'>
          <p className='text-sm/6'>&copy; {new Date().getFullYear()} Deschutes Design Group LLC. All rights reserved.</p>
        </div>
      </Container>
    </footer>
  )
}
