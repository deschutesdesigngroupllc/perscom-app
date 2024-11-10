import React, { useEffect, useState } from 'react'
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/react'
import clsx from 'clsx'

import { Container } from './Container'
import backgroundImage from '../../../images/landing/background-features.jpg'
import featureDashboard from '../../../images/landing/features/feature-1.png'
import featurePersonnelFiles from '../../../images/landing/features/feature-2.png'
import featureForms from '../../../images/landing/features/feature-3.png'
import featureLogs from '../../../images/landing/features/feature-4.png'

const features = [
  {
    title: 'Personnel management',
    description: "PERSCOM.io streamlines your organization's personnel management, allowing you to focus on what really matters.",
    image: featureDashboard
  },
  {
    title: 'Fully customizable',
    description:
      "PERSCOM.io's powerful features are fully customizable and scalable to meet the unique needs of your organization, while maintaining the highest levels of security.",
    image: featurePersonnelFiles
  },
  {
    title: 'Centralized data management',
    description:
      'With PERSCOM.io, all of your personnel data is stored in a centralized location, making it easy to access, update, and manage.',
    image: featureForms
  },
  {
    title: 'Seamless integrations',
    description:
      "PERSCOM.io's API, SDK's, Command Line Interface (CLI), OAuth 2.0, OpenID Connect (OIDC), Webhooks, and Widget integration options make it easy to connect with other tools and systems, while the platform's built-in communication features keep everyone on the same page.",
    image: featureLogs
  }
]

export function PrimaryFeatures() {
  let [tabOrientation, setTabOrientation] = useState('horizontal')

  useEffect(() => {
    let lgMediaQuery = window.matchMedia('(min-width: 1024px)')

    function onMediaQueryChange({ matches }) {
      setTabOrientation(matches ? 'vertical' : 'horizontal')
    }

    onMediaQueryChange(lgMediaQuery)
    lgMediaQuery.addEventListener('change', onMediaQueryChange)

    return () => {
      lgMediaQuery.removeEventListener('change', onMediaQueryChange)
    }
  }, [])

  return (
    <section id='features' aria-labelledby='features-title' className='relative overflow-hidden bg-blue-600 py-20 sm:py-32'>
      <img
        className='absolute left-1/2 top-1/2 max-w-none translate-x-[-44%] translate-y-[-42%]'
        src={backgroundImage}
        alt=''
        width={2245}
        height={1636}
      />
      <Container className='relative'>
        <div className='max-w-2xl md:mx-auto md:text-center xl:max-w-none'>
          <h2 className='text-3xl font-bold tracking-tight text-white sm:text-4xl md:text-5xl'>
            Everything you need to manage your personnel.
          </h2>
          <p className='mt-6 text-lg tracking-tight text-blue-100'>Packed with powerful features and backed by years of experience.</p>
        </div>
        <TabGroup
          className='mt-16 grid grid-cols-1 items-center gap-y-2 pt-10 sm:gap-y-6 md:mt-20 lg:grid-cols-12 lg:pt-0'
          vertical={tabOrientation === 'vertical'}
        >
          {({ selectedIndex }) => (
            <>
              <div className='-mx-4 flex overflow-x-auto pb-4 sm:mx-0 sm:overflow-visible sm:pb-0 lg:col-span-5'>
                <TabList className='relative z-10 flex gap-x-4 whitespace-nowrap px-4 sm:mx-auto sm:px-0 lg:mx-0 lg:block lg:gap-x-0 lg:gap-y-1 lg:whitespace-normal'>
                  {features.map((feature, featureIndex) => (
                    <div
                      key={feature.title}
                      className={clsx(
                        'group relative rounded-full px-4 py-1 font-medium lg:rounded-l-xl lg:rounded-r-none lg:p-6',
                        selectedIndex === featureIndex
                          ? 'bg-white lg:bg-white/10 lg:ring-1 lg:ring-inset lg:ring-white/10'
                          : 'hover:bg-white/10 lg:hover:bg-white/5'
                      )}
                    >
                      <h3>
                        <Tab
                          className={clsx(
                            'font-display ui-not-focus-visible:outline-none text-lg',
                            selectedIndex === featureIndex ? 'text-blue-600 lg:text-white' : 'text-blue-100 hover:text-white lg:text-white'
                          )}
                        >
                          <span className='absolute inset-0 rounded-full lg:rounded-l-xl lg:rounded-r-none' />
                          {feature.title}
                        </Tab>
                      </h3>
                      <p
                        className={clsx(
                          'mt-2 hidden text-sm lg:block',
                          selectedIndex === featureIndex ? 'text-white' : 'text-blue-100 group-hover:text-white'
                        )}
                      >
                        {feature.description}
                      </p>
                    </div>
                  ))}
                </TabList>
              </div>
              <TabPanels className='lg:col-span-7'>
                {features.map((feature) => (
                  <TabPanel key={feature.title} unmount={false}>
                    <div className='relative sm:px-6 lg:hidden'>
                      <div className='absolute -inset-x-4 bottom-[-4.25rem] top-[-6.5rem] bg-white/10 ring-1 ring-inset ring-white/10 sm:inset-x-0 sm:rounded-t-xl' />
                      <p className='relative mx-auto max-w-2xl text-base text-white sm:text-center'>{feature.description}</p>
                    </div>
                    <div className='mt-10 w-[45rem] overflow-hidden rounded-xl bg-slate-50 shadow-xl shadow-blue-900/20 sm:w-auto lg:mt-0 lg:w-[67.8125rem]'>
                      <img
                        className='w-full'
                        src={feature.image}
                        alt=''
                        sizes='(min-width: 1024px) 67.8125rem, (min-width: 640px) 100vw, 45rem'
                      />
                    </div>
                  </TabPanel>
                ))}
              </TabPanels>
            </>
          )}
        </TabGroup>
      </Container>
    </section>
  )
}
