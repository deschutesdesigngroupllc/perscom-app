import { Tab, TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/react'
import { CalendarDaysIcon, FolderMinusIcon, NewspaperIcon } from '@heroicons/react/20/solid'
import clsx from 'clsx'
import PropTypes from 'prop-types'
import customFieldsImage from '../../../images/landing/features/secondary-1.png'
import recordsImage from '../../../images/landing/features/secondary-2.png'
import searchImage from '../../../images/landing/features/secondary-3.png'
import { Container } from './Container'

const features = [
  {
    name: 'Realtime Messaging and Notifications',
    summary: 'Instantly reach your team across multiple channels.',
    description:
      "Keep your team informed and responsive with PERSCOM.io's Realtime Messaging & Notifications. Administrators can send critical updates, reminders, or alerts through a variety of channels, including email, SMS, Discord, and more—ensuring that your message is received no matter where your team is. With instant delivery and full control over your communication, you can improve efficiency, reduce delays, and keep everyone on the same page, all in real time.",
    image: customFieldsImage,
    icon: function Icon() {
      return <NewspaperIcon className='h-8 w-8' role='img' />
    }
  },
  {
    name: 'Customizable Form Design',
    summary: 'Tailor forms to capture the exact data you need.',
    description:
      "Every organization is unique, and your forms should be too. With our powerful form builder, you can easily create custom forms that capture the specific data your business requires. Whether it's for lead generation, feedback, or internal processes, our flexible design options ensure your forms are as unique as your needs. Streamline data collection and ensure you're always gathering the right information to drive your success.",
    image: recordsImage,
    icon: function Icon() {
      return <FolderMinusIcon className='h-8 w-8' role='img' />
    }
  },
  {
    name: 'Powerful Event Management',
    summary: 'Keep your team in sync with effortless scheduling.',
    description:
      "Stay on top of all your organizational events with PERSCOM.io’s powerful Calendars and Events features. Effortlessly manage and coordinate meetings, trainings, drills, and more, ensuring that everyone is up-to-date and in the know. Whether you're planning a small meeting or a large-scale event, our intuitive tools streamline scheduling, reduce conflicts, and improve communication across your team.",
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
      <h4 className='mt-2 text-lg font-medium'>{feature.summary}</h4>
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
    <div className='-mx-4 mt-20 flex flex-col gap-y-10 overflow-hidden px-4 sm:-mx-6 sm:px-6 lg:hidden'>
      {features.map((feature) => (
        <div key={feature.summary}>
          <Feature feature={feature} className='mx-auto max-w-2xl' isActive />
          <div className='relative mt-10 pb-10'>
            <div className='absolute -inset-x-4 top-8 bottom-0 bg-slate-200 sm:-inset-x-6' />
            <div className='relative mx-auto w-211 overflow-hidden rounded-xl bg-white shadow-lg ring-1 shadow-slate-900/5 ring-slate-500/10'>
              <img className='w-full' src={feature.image} alt='' sizes='52.75rem' />
            </div>
          </div>
        </div>
      ))}
    </div>
  )
}

function FeaturesDesktop() {
  return (
    <TabGroup className='hidden lg:mt-20 lg:block'>
      {({ selectedIndex }) => (
        <>
          <TabList className='grid grid-cols-3 gap-x-8'>
            {features.map((feature, featureIndex) => (
              <Feature
                key={feature.summary}
                feature={{
                  ...feature,
                  name: (
                    <Tab className='ui-not-focus-visible:outline-none'>
                      <span className='absolute inset-0' />
                      {feature.name}
                    </Tab>
                  )
                }}
                isActive={featureIndex === selectedIndex}
                className='relative'
              />
            ))}
          </TabList>
          <TabPanels className='relative mt-20 overflow-hidden rounded-4xl bg-slate-200 px-14 py-16 xl:px-16'>
            <div className='-mx-5 flex'>
              {features.map((feature, featureIndex) => (
                <TabPanel
                  static
                  key={feature.summary}
                  className={clsx(
                    'ui-not-focus-visible:outline-none px-5 transition duration-500 ease-in-out',
                    featureIndex !== selectedIndex && 'opacity-60'
                  )}
                  style={{ transform: `translateX(-${selectedIndex * 100}%)` }}
                  aria-hidden={featureIndex !== selectedIndex}
                >
                  <div className='w-211 overflow-hidden rounded-xl bg-white shadow-lg ring-1 shadow-slate-900/5 ring-slate-500/10'>
                    <img className='w-full' src={feature.image} alt='' sizes='52.75rem' />
                  </div>
                </TabPanel>
              ))}
            </div>
            <div className='pointer-events-none absolute inset-0 rounded-4xl ring-1 ring-slate-900/10 ring-inset' />
          </TabPanels>
        </>
      )}
    </TabGroup>
  )
}

export function SecondaryFeatures() {
  return (
    <section
      id='secondary-features'
      aria-label='Features for simplifying everyday organizational tasks'
      className='pt-20 pb-14 sm:pt-32 sm:pb-20 lg:pb-32'
    >
      <Container>
        <div className='mx-auto max-w-3xl md:text-center'>
          <h2 className='text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl'>Simplify your organization&apos;s tasks.</h2>
          <p className='mt-4 text-lg tracking-tight text-slate-700'>
            We&apos;ve focused on making your life easier. Let our software show you how.
          </p>
        </div>
        <FeaturesMobile />
        <FeaturesDesktop />
      </Container>
    </section>
  )
}
