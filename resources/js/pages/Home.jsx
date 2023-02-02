import React from 'react'

import { Header } from '../components/Header'
import { Hero } from '../components/Hero'
import { PrimaryFeatures } from '../components/PrimaryFeatures'
import { SecondaryFeatures } from '../components/SecondaryFeatures'
import { Footer } from '../components/Footer'
import { Pricing } from '../components/Pricing'
import { OpenBetaBanner } from '../components/OpenBetaBanner'

export default function Home() {
  return (
    <>
      <OpenBetaBanner />
      <Header />
      <main>
        <Hero />
        <PrimaryFeatures />
        <SecondaryFeatures />
        <Pricing />
      </main>
      <Footer />
    </>
  )
}
