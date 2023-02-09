import React from 'react'

import { Container } from './/Container'
import { Logo } from './/Logo'

export function Footer() {
  return (
    <footer className='bg-slate-50'>
      <Container>
        <div className='flex flex-col items-center py-16'>
          <Logo className='mx-auto h-10 w-auto' />
          <nav className='mt-10 text-sm' aria-label='quick links'>
            <ul className='-my-1 flex flex-wrap justify-center space-x-6'>
              <li>
                <a href={route('web.landing.home')} className='rounded-lg px-2 py-1 text-slate-700 hover:bg-slate-100 hover:text-slate-900'>
                  {' '}
                  Home{' '}
                </a>
              </li>
              <li>
                <a href='#features' className='rounded-lg px-2 py-1 text-slate-700 hover:bg-slate-100 hover:text-slate-900'>
                  {' '}
                  Features{' '}
                </a>
              </li>
              <li>
                <a href='#pricing' className='rounded-lg px-2 py-1 text-slate-700 hover:bg-slate-100 hover:text-slate-900'>
                  {' '}
                  Pricing{' '}
                </a>
              </li>
              <li>
                <a
                  href='https://docs.perscom.io'
                  target='_blank'
                  className='rounded-lg px-2 py-1 text-slate-700 hover:bg-slate-100 hover:text-slate-900'
                  rel='noreferrer'
                >
                  {' '}
                  Documentation{' '}
                </a>
              </li>
              <li>
                <a
                  href={route('web.find-my-organization.index')}
                  target='_blank'
                  className='rounded-lg px-2 py-1 text-slate-700 hover:bg-slate-100 hover:text-slate-900'
                  rel='noreferrer'
                >
                  {' '}
                  Find My Organization{' '}
                </a>
              </li>
              <li>
                <a
                  href={route('web.privacy-policy.index')}
                  className='rounded-lg px-2 py-1 text-slate-700 hover:bg-slate-100 hover:text-slate-900'
                >
                  {' '}
                  Privacy Policy{' '}
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <div className='flex flex-col items-center border-t border-slate-400/10 py-10'>
          <p className='mt-6 text-center text-sm text-slate-500 sm:mt-0'>
            Copyright &copy; {new Date().getFullYear()} Deschutes Design Group LLC. All rights reserved. reserved.
          </p>
        </div>
      </Container>
    </footer>
  )
}
