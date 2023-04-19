import React, {useEffect, useState} from 'react'
import {Tab} from '@headlessui/react'
import clsx from 'clsx'

import {Container} from './/Container'
import backgroundImage from '../../images/background-features.jpg'
import featureDashboard from '../../images/features/feature1.png'
import featurePersonnelFiles from '../../images/features/feature2.png'
import featureForms from '../../images/features/feature3.png'
import featureLogs from '../../images/features/feature4.png'

const features = [
  {
    title: 'Efficient Personnel Management Made Easy',
    description: "PERSCOM.io streamlines your organization's personnel management, allowing you to focus on what really matters.",
    image: featureDashboard
  },
  {
    title: 'Customizable, Scalable, and Secure',
    description:
      "PERSCOM.io's powerful features are fully customizable and scalable to meet the unique needs of your organization, while maintaining the highest levels of security.",
    image: featurePersonnelFiles
  },
  {
    title: 'Centralized Data Management',
    description:
      'With PERSCOM.io, all of your personnel data is stored in a centralized location, making it easy to access, update, and manage.',
    image: featureForms
  },
  {
    title: 'Seamless Integration and Communication',
    description:
      "PERSCOM.io's API, OAuth 2.0, and Widget integration options make it easy to connect with other tools and systems, while the platform's built-in communication features keep everyone on the same page.",
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
      <div className='absolute top-1/2 left-1/2 -trangray-x-[44%] -trangray-y-[42%]'>
        <img src={backgroundImage} alt='' width={2245} height={1636} />
      </div>
      <Container className='relative'>
        <div className='max-w-2xl md:mx-auto md:text-center xl:max-w-none'>
          <h2 id='features-title' className='font-display text-3xl font-bold tracking-tight text-white sm:text-4xl md:text-5xl'>
            Everything you need to manage your personnel.
          </h2>
          <p className='mt-6 text-base text-blue-100'>Packed with powerful features and backed by years of experience.</p>
        </div>
        <Tab.Group
          as='div'
          className='mt-16 grid grid-cols-1 items-center gap-y-2 pt-10 sm:gap-y-6 md:mt-20 lg:grid-cols-12 lg:pt-0'
          vertical={tabOrientation === 'vertical'}
        >
          {({ selectedIndex }) => (
            <>
              <div className='-mx-4 flex overflow-x-auto pb-4 sm:mx-0 sm:overflow-visible sm:pb-0 lg:col-span-5'>
                <Tab.List className='relative z-10 flex space-x-4 whitespace-nowrap px-4 sm:mx-auto sm:px-0 lg:mx-0 lg:block lg:space-y-1 lg:space-x-0 lg:whitespace-normal'>
                  {features.map((feature, featureIndex) => (
                    <div
                      key={feature.title}
                      className={clsx('group relative rounded-full py-1 px-4 lg:rounded-r-none lg:rounded-l-xl lg:p-6', {
                        'bg-white lg:bg-white/10 lg:ring-1 lg:ring-inset lg:ring-white/10': selectedIndex === featureIndex,
                        'hover:bg-white/10 lg:hover:bg-white/5': selectedIndex !== featureIndex
                      })}
                    >
                      <h3>
                        <Tab
                          className={clsx('font-display text-lg [&:not(:focus-visible)]:focus:outline-none', {
                            'text-blue-600 lg:text-white': selectedIndex === featureIndex,
                            'text-blue-100 hover:text-white lg:text-white': selectedIndex !== featureIndex
                          })}
                        >
                          <span className='absolute inset-0 rounded-full lg:rounded-r-none lg:rounded-l-xl' />
                          {feature.title}
                        </Tab>
                      </h3>
                      <p
                        className={clsx('mt-2 hidden text-sm lg:block', {
                          'text-white': selectedIndex === featureIndex,
                          'text-blue-100 group-hover:text-white': selectedIndex !== featureIndex
                        })}
                      >
                        {feature.description}
                      </p>
                    </div>
                  ))}
                </Tab.List>
              </div>
              <Tab.Panels className='lg:col-span-7'>
                {features.map((feature) => (
                  <Tab.Panel key={feature.title} unmount={false}>
                    <div className='relative sm:px-6 lg:hidden'>
                      <div className='absolute -inset-x-4 -top-[6.5rem] -bottom-[4.25rem] bg-white/10 ring-1 ring-inset ring-white/10 sm:inset-x-0 sm:rounded-t-xl' />
                      <p className='relative mx-auto max-w-2xl text-base text-white sm:text-center'>{feature.description}</p>
                    </div>
                    <div className='relative mt-10 aspect-[1085/590] w-[45rem] overflow-hidden rounded-xl bg-gray-50 shadow-xl shadow-blue-900/20 sm:w-auto lg:mt-0 lg:w-[67.8125rem]'>
                      <img src={feature.image} alt='' sizes='(min-width: 1024px) 67.8125rem, (min-width: 640px) 100vw, 45rem' />
                    </div>
                  </Tab.Panel>
                ))}
              </Tab.Panels>
            </>
          )}
        </Tab.Group>
      </Container>
    </section>
  )
}
