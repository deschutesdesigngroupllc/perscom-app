import React, {Fragment} from 'react'
import {Popover, Transition} from '@headlessui/react'
import clsx from 'clsx'

import {ButtonLink} from './/Button'
import {Container} from './/Container'
import {Logo} from './/Logo'

function MobileNavigation() {
  return (
    <Popover>
      {({ open, close }) => (
        <>
          <Popover.Button className='relative z-10 flex h-8 w-8 items-center justify-center [&:not(:focus-visible)]:focus:outline-none'>
            <span className='sr-only'>Toggle Navigation</span>
            <svg
              aria-hidden='true'
              className='h-3.5 w-3.5 overflow-visible stroke-gray-700'
              fill='none'
              strokeWidth={2}
              strokeLinecap='round'
            >
              <path
                d='M0 1H14M0 7H14M0 13H14'
                className={clsx('origin-center transition', {
                  'scale-90 opacity-0': open
                })}
              />
              <path
                d='M2 2L12 12M12 2L2 12'
                className={clsx('origin-center transition', {
                  'scale-90 opacity-0': !open
                })}
              />
            </svg>
          </Popover.Button>
          <Transition.Root>
            <Transition.Child
              as={Fragment}
              enter='duration-150 ease-out'
              enterFrom='opacity-0'
              enterTo='opacity-100'
              leave='duration-150 ease-in'
              leaveFrom='opacity-100'
              leaveTo='opacity-0'
            >
              <Popover.Overlay className='fixed inset-0 bg-gray-300/50' />
            </Transition.Child>
            <Transition.Child
              as={Fragment}
              enter='duration-150 ease-out'
              enterFrom='opacity-0 scale-95'
              enterTo='opacity-100 scale-100'
              leave='duration-100 ease-in'
              leaveFrom='opacity-100 scale-100'
              leaveTo='opacity-0 scale-95'
            >
              <Popover.Panel
                as='ul'
                className='absolute inset-x-0 top-full mt-4 origin-top space-y-4 rounded-2xl bg-white p-6 text-lg tracking-tight shadow-xl ring-1 ring-gray-900/5'
              >
                <li>
                  <a href={route('web.landing.home')} className='prose block w-full hover:text-gray-500' onClick={() => close()}>
                    Home
                  </a>
                </li>
                <li>
                  <a
                    href='https://docs.perscom.io'
                    target='_blank'
                    className='prose block w-full hover:text-gray-500'
                    onClick={() => close()}
                    rel='noreferrer'
                  >
                    Documentation
                  </a>
                </li>
                <li>
                  <a href='#features' className='prose block w-full hover:text-gray-500' onClick={() => close()}>
                    Features
                  </a>
                </li>
                <li>
                  <a href='#pricing' className='prose block w-full hover:text-gray-500' onClick={() => close()}>
                    Pricing
                  </a>
                </li>
                <li>
                  <a
                    href={route('web.find-my-organization.index')}
                    target='_blank'
                    className='prose block w-full hover:text-gray-500'
                    onClick={() => close()}
                    rel='noreferrer'
                  >
                    Find My Organization
                  </a>
                </li>
              </Popover.Panel>
            </Transition.Child>
          </Transition.Root>
        </>
      )}
    </Popover>
  )
}

export function Header() {
  return (
    <header className='py-4'>
      <Container>
        <nav className='relative z-50'>
          <ul className='flex items-center'>
            <li>
              <a href={route('web.landing.home')}>
                <span className='sr-only'>Home</span>
                <Logo className='h-8 w-auto sm:h-10 md:h-12' />
              </a>
            </li>
            <li className='ml-6 hidden lg:block'>
              <a href={route('web.landing.home')} className='rounded-lg py-1 px-2 text-sm prose hover:bg-gray-200'>
                Home
              </a>
            </li>
            <li className='ml-6 hidden lg:block'>
              <a
                href='https://docs.perscom.io'
                target='_blank'
                className='rounded-lg py-1 px-2 text-sm prose hover:bg-gray-200'
                rel='noreferrer'
              >
                Documentation
              </a>
            </li>
            <li className='ml-6 hidden lg:block'>
              <a href='#features' className='rounded-lg py-1 px-2 text-sm prose hover:bg-gray-200'>
                Features
              </a>
            </li>
            <li className='ml-6 hidden lg:block'>
              <a href='#pricing' className='rounded-lg py-1 px-2 text-sm prose hover:bg-gray-200'>
                Pricing
              </a>
            </li>
            <li className='ml-6 hidden lg:block ml-auto'>
              <a
                href={route('web.find-my-organization.index')}
                target='_blank'
                className='rounded-lg py-1 px-2 text-sm prose hover:bg-gray-200'
                rel='noreferrer'
              >
                Find My Organization
              </a>
            </li>
            <li className='lg:ml-6 ml-auto'>
              <ButtonLink href={route('web.register.index')} color='blue'>
                <span>
                  Get started
                  <span className='hidden lg:inline'> today</span>
                </span>
              </ButtonLink>
            </li>
            <li className='ml-5 -mr-1 lg:hidden'>
              <MobileNavigation />
            </li>
          </ul>
        </nav>
      </Container>
    </header>
  )
}
