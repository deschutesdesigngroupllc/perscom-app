import React from 'react'
import PropTypes from 'prop-types'
import { Tab } from '@headlessui/react'
import clsx from 'clsx'
import { FolderMinusIcon, CalendarDaysIcon, NewspaperIcon } from '@heroicons/react/20/solid'
import { Container } from './Container'
import customFieldsImage from '../../images/features/secondary1.png'
import recordsImage from '../../images/features/secondary2.png'
import searchImage from '../../images/features/secondary3.png'

const features = [
  {
    name: 'Friendly Organizational Newsfeed',
    summary: 'Bring everyone in the organization together.',
    description:
      'The PERSCOM.io newsfeed serves as a central hub for essential organization-wide updates and information. Stay informed at a glance with real-time access to critical announcements, news, and key insights vital for efficient personnel management and decision-making.',
    image: customFieldsImage,
    icon: function Icon() {
      return <NewspaperIcon className='h-8 w-8' role='img' />
    }
  },
  {
    name: 'Customizable Form Design',
    summary: 'Build the forms to collect the data you need.',
    description:
      "We understand that every organization has unique needs, which is why we empower you to create custom forms and gather the data necessary for your organization's success. Your ability to build and customize forms ensures that your organization's specific requirements are met.",
    image: recordsImage,
    icon: function Icon() {
      return <FolderMinusIcon className='h-8 w-8' role='img' />
    }
  },
  {
    name: 'Powerful Event Management',
    summary: 'Keep everyone up-to-date and in the know.',
    description:
      "PERSCOM.io's Calendars and Events features provide a comprehensive solution for managing scheduling and events, allowing you to efficiently plan and coordinate meetings, trainings, drills, and more.",
    image: searchImage,
    icon: function Icon() {
      return <CalendarDaysIcon className='h-8 w-8' role='img' />
    }
  }
]

function Feature({ feature, isActive, className, ...props }) {
  return (
    <div
      className={clsx(className, {
        'opacity-75 hover:opacity-100': !isActive
      })}
      {...props}
    >
      <div
        className={clsx(className, {
          'text-blue-600': isActive,
          'text-gray-600': !isActive
        })}
      >
        <feature.icon />
      </div>
      <h3
        className={clsx('mt-2 text-sm font-medium', {
          'text-blue-600': isActive,
          'text-gray-600': !isActive
        })}
        role='heading'
      >
        {feature.name}
      </h3>
      <h4 className='font-medium mt-2 text-xl'>{feature.summary}</h4>
      <p className='mt-4 text-sm'>{feature.description}</p>
    </div>
  )
}

Feature.propTypes = {
  feature: PropTypes.object,
  isActive: PropTypes.bool,
  className: PropTypes.string
}

function FeaturesMobile() {
  return (
    <div className='-mx-4 mt-16 space-y-10 overflow-hidden px-4 sm:-mx-6 sm:px-6 lg:hidden'>
      {features.map((feature) => (
        <div key={feature.name}>
          <Feature feature={feature} className='mx-auto max-w-2xl' isActive />
          <div className='relative mt-10 pb-10'>
            <div className='absolute -inset-x-4 bottom-0 top-8 bg-gray-200 sm:-inset-x-6' />
            <div className='relative mx-auto aspect-[844/428] w-[52.75rem] overflow-hidden rounded-xl bg-white shadow-lg shadow-gray-900/5 ring-1 ring-gray-500/10'>
              <img src={feature.image} alt='' sizes='52.75rem' />
            </div>
          </div>
        </div>
      ))}
    </div>
  )
}

function FeaturesDesktop() {
  return (
    <Tab.Group as='div' className='hidden lg:mt-20 lg:block'>
      {({ selectedIndex }) => (
        <>
          <Tab.List className='grid grid-cols-3 gap-x-8'>
            {features.map((feature, featureIndex) => (
              <Tab className='text-left [&:not(:focus-visible)]:focus:outline-none' key={feature.name}>
                <Feature
                  feature={{
                    ...feature,
                    name: (
                      <div>
                        <span className='absolute inset-0' /> {feature.name}
                      </div>
                    )
                  }}
                  isActive={featureIndex === selectedIndex}
                  className='relative'
                />
              </Tab>
            ))}
          </Tab.List>
          <Tab.Panels className='relative mt-20 overflow-hidden rounded-4xl bg-gray-200 px-14 py-16 xl:px-16'>
            <div className='-mx-5 flex'>
              {features.map((feature, featureIndex) => (
                <Tab.Panel
                  static
                  key={feature.name}
                  className={clsx('px-5 transition duration-500 ease-in-out [&:not(:focus-visible)]:focus:outline-none', {
                    'opacity-60': featureIndex !== selectedIndex
                  })}
                  style={{
                    transform: `translateX(-${selectedIndex * 100}%)`
                  }}
                  aria-hidden={featureIndex !== selectedIndex}
                >
                  <div className='relative aspect-[844/428] w-[52.75rem] overflow-hidden rounded-xl bg-white shadow-lg shadow-gray-900/5 ring-1 ring-gray-500/10'>
                    <img src={feature.image} alt='' sizes='52.75rem' />
                  </div>
                </Tab.Panel>
              ))}
            </div>
            <div className='pointer-events-none absolute inset-0 rounded-4xl ring-1 ring-inset ring-gray-900/10' />
          </Tab.Panels>
        </>
      )}
    </Tab.Group>
  )
}

export function SecondaryFeatures() {
  return (
    <section id='secondary-features' aria-labelledby='secondary-features-title' className='py-20 sm:py-32'>
      <Container className='relative'>
        <div className='mx-auto max-w-2xl md:text-center'>
          <h2 className='text-gray-800 text-3xl font-bold tracking-tight sm:text-4xl'>Simplify your organization&apos;s tasks.</h2>
          <p className='mx-auto mt-4 max-w-3xl text-base'>
            We&apos;ve focused on making your life easier. Let our software show you how.
          </p>
        </div>
        <FeaturesMobile /> <FeaturesDesktop />
      </Container>
    </section>
  )
}
