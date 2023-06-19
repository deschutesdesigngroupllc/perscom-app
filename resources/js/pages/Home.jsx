import React from 'react'
import {Banner} from '../components/Banner'
import {CallToAction} from '../components/CallToAction'
import {Faqs} from '../components/Faqs'
import {Footer} from '../components/Footer'
import {Header} from '../components/Header'
import {Hero} from '../components/Hero'
import {Pricing} from '../components/Pricing'
import {PrimaryFeatures} from '../components/PrimaryFeatures'
import {SecondaryFeatures} from '../components/SecondaryFeatures'
import PropTypes from 'prop-types'

export default function Home({ banners = null }) {
  return (
    <>
      {banners && !!banners.length && banners.map((banner) => <Banner key={banner.id} banner={banner} />)}
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

Home.propTypes = {
  banners: PropTypes.array
}
