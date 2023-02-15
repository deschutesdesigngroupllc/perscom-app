import React from 'react'

import { Header } from '../components/Header'
import { Hero } from '../components/Hero'
import { PrimaryFeatures } from '../components/PrimaryFeatures'
import { SecondaryFeatures } from '../components/SecondaryFeatures'
import { Footer } from '../components/Footer'
import { Pricing } from '../components/Pricing'
import { Faqs } from '../components/Faqs'
import { CallToAction } from '../components/CallToAction'

export default function Home() {
  return (
    <>
      <Header />
      <main>
        <Hero />
        <PrimaryFeatures />
        <SecondaryFeatures />
        <CallToAction />
        <Faqs />
        <Pricing />
      </main>
      <Footer />
    </>
  )
}
