import React from 'react'
import { Tab } from '@headlessui/react'
import clsx from 'clsx'
import {
    SearchIcon,
    CollectionIcon,
    PencilAltIcon,
} from '@heroicons/react/outline'

import { Container } from '../Components/Container'
import customFieldsImage from '../../images/features/secondary1.png'
import recordsImage from '../../images/features/secondary2.png'
import searchImage from '../../images/features/secondary3.png'

const features = [
    {
        name: 'Custom Fields',
        summary: 'Advanced customazability without the headache.',
        description:
            'Nearly every module allows for the implementation of Custom Fields which enables you to collect the data you need.',
        image: customFieldsImage,
        icon: function Icon() {
            return <PencilAltIcon className="h-8 w-8" />
        },
    },
    {
        name: 'Records Management',
        summary:
            'Keep track of every action and update that happens to your people.',
        description:
            'The advanced RMS system provides a historical timeline of every update applied to a personnel file keeping everyone on the same page.',
        image: recordsImage,
        icon: function Icon() {
            return <CollectionIcon className="h-8 w-8" />
        },
    },
    {
        name: 'Advanced Searching',
        summary: 'Backed by Algolia, the leader in AI-Powered searching.',
        description:
            "We've gone the extra mile to make sure access to your data is quick and accurate.",
        image: searchImage,
        icon: function Icon() {
            return <SearchIcon className="h-8 w-8" />
        },
    },
]

function Feature({ feature, isActive, className, ...props }) {
    return (
        <div
            className={clsx(className, {
                'opacity-75 hover:opacity-100': !isActive,
            })}
            {...props}
        >
            <div
                className={clsx(className, {
                    'text-blue-600': isActive,
                    'text-slate-600': !isActive,
                })}
            >
                <feature.icon />
            </div>
            <h3
                className={clsx('mt-2 text-sm font-medium', {
                    'text-blue-600': isActive,
                    'text-slate-600': !isActive,
                })}
            >
                {feature.name}
            </h3>
            <p className="mt-2 font-display text-xl text-slate-900">
                {feature.summary}
            </p>
            <p className="mt-4 text-sm text-slate-600">{feature.description}</p>
        </div>
    )
}

function FeaturesMobile() {
    return (
        <div className="-mx-4 mt-20 space-y-10 overflow-hidden px-4 sm:-mx-6 sm:px-6 lg:hidden">
            {features.map((feature) => (
                <div key={feature.name}>
                    <Feature
                        feature={feature}
                        className="mx-auto max-w-2xl"
                        isActive
                    />
                    <div className="relative mt-10 pb-10">
                        <div className="absolute -inset-x-4 bottom-0 top-8 bg-slate-200 sm:-inset-x-6" />
                        <div className="relative mx-auto aspect-[844/428] w-[52.75rem] overflow-hidden rounded-xl bg-white shadow-lg shadow-slate-900/5 ring-1 ring-slate-500/10">
                            <img src={feature.image} alt="" sizes="52.75rem" />
                        </div>
                    </div>
                </div>
            ))}
        </div>
    )
}

function FeaturesDesktop() {
    return (
        <Tab.Group as="div" className="hidden lg:mt-20 lg:block">
            {({ selectedIndex }) => (
                <>
                    <Tab.List className="grid grid-cols-3 gap-x-8">
                        {features.map((feature, featureIndex) => (
                            <Feature
                                key={feature.name}
                                feature={{
                                    ...feature,
                                    name: (
                                        <Tab className="[&:not(:focus-visible)]:focus:outline-none">
                                            <span className="absolute inset-0" />{' '}
                                            {feature.name}
                                        </Tab>
                                    ),
                                }}
                                isActive={featureIndex === selectedIndex}
                                className="relative"
                            />
                        ))}
                    </Tab.List>
                    <Tab.Panels className="relative mt-20 overflow-hidden rounded-4xl bg-slate-200 px-14 py-16 xl:px-16">
                        <div className="-mx-5 flex">
                            {features.map((feature, featureIndex) => (
                                <Tab.Panel
                                    static
                                    key={feature.name}
                                    className={clsx(
                                        'px-5 transition duration-500 ease-in-out [&:not(:focus-visible)]:focus:outline-none',
                                        {
                                            'opacity-60':
                                                featureIndex !== selectedIndex,
                                        }
                                    )}
                                    style={{
                                        transform: `translateX(-${
                                            selectedIndex * 100
                                        }%)`,
                                    }}
                                    aria-hidden={featureIndex !== selectedIndex}
                                >
                                    <div className="relative aspect-[844/428] w-[52.75rem] overflow-hidden rounded-xl bg-white shadow-lg shadow-slate-900/5 ring-1 ring-slate-500/10">
                                        <img
                                            src={feature.image}
                                            alt=""
                                            sizes="52.75rem"
                                        />
                                    </div>
                                </Tab.Panel>
                            ))}
                        </div>
                        <div className="pointer-events-none absolute inset-0 rounded-4xl ring-1 ring-inset ring-slate-900/10" />
                    </Tab.Panels>
                </>
            )}
        </Tab.Group>
    )
}

export function SecondaryFeatures() {
    return (
        <section
            id="secondary-features"
            aria-labelledby="secondary-features-title"
            className="pt-20 pb-14 sm:pb-20 sm:pt-32 lg:pb-32"
        >
            <Container>
                <div className="mx-auto max-w-2xl md:text-center">
                    <h2
                        id="secondary-features-title"
                        className="font-display font-bold text-3xl tracking-tight text-slate-900 sm:text-4xl"
                    >
                        Simplify your organization's tasks.
                    </h2>
                    <p className="mt-4 text-lg tracking-tight text-slate-700">
                        We've focused on making your life easier. Let our
                        software show you how.
                    </p>
                </div>
                <FeaturesMobile /> <FeaturesDesktop />
            </Container>
        </section>
    )
}
