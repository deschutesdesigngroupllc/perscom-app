import React from 'react'

import { Container } from './Container'
import { Logo } from './Logo'

export function Footer() {
  return (
    <footer>
      <Container>
        <div className='flex flex-col items-center py-16'>
          <Logo className='mx-auto h-10 w-auto' />
          <nav className='mt-10' aria-label='quick links'>
            <ul className='-my-1 flex flex-wrap justify-center space-x-6'>
              <li>
                <a href={route('web.landing.home')} className='rounded-lg p-2.5 text-sm hover:bg-gray-200'>
                  {' '}
                  Home{' '}
                </a>
              </li>
              <li>
                <a href='https://docs.perscom.io' target='_blank' className='rounded-lg p-2.5 text-sm hover:bg-gray-200' rel='noreferrer'>
                  {' '}
                  Documentation{' '}
                </a>
              </li>
              <li>
                <a href={route('web.landing.home') + '#features'} className='rounded-lg p-2.5 text-sm hover:bg-gray-200'>
                  {' '}
                  Features{' '}
                </a>
              </li>
              <li>
                <a
                  href={route('web.find-my-organization.index')}
                  target='_blank'
                  className='rounded-lg p-2.5 text-sm hover:bg-gray-200'
                  rel='noreferrer'
                >
                  {' '}
                  Find My Organization{' '}
                </a>
              </li>
              <li>
                <a href={route('web.landing.home') + '#pricing'} className='rounded-lg p-2.5 text-sm hover:bg-gray-200'>
                  {' '}
                  Pricing{' '}
                </a>
              </li>
              <li>
                <a href={route('web.privacy-policy.index')} className='rounded-lg p-2.5 text-sm hover:bg-gray-200'>
                  {' '}
                  Privacy Policy{' '}
                </a>
              </li>
              <li>
                <a href={route('web.slack')} className='rounded-lg p-2.5 text-sm hover:bg-gray-200'>
                  {' '}
                  Support{' '}
                </a>
              </li>
              <li>
                <a
                  href='https://feedback.perscom.io/roadmap'
                  target='_blank'
                  className='rounded-lg p-2.5 text-sm hover:bg-gray-200'
                  rel='noreferrer'
                >
                  {' '}
                  Roadmap{' '}
                </a>
              </li>
              <li>
                <a href='https://status.perscom.io' target='_blank' className='rounded-lg p-2.5 text-sm hover:bg-gray-200' rel='noreferrer'>
                  {' '}
                  Status{' '}
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <div className='flex flex-col items-center border-t border-gray-400/10 py-10'>
          <p className='mt-6 text-center text-sm sm:mt-0'>
            Copyright &copy; {new Date().getFullYear()} Deschutes Design Group LLC. All rights reserved.
          </p>
        </div>
      </Container>
    </footer>
  )
}
