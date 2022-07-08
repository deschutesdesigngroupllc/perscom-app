import React from 'react'

import {Header} from "../components/Header";
import {Hero} from "../components/Hero";
import {PrimaryFeatures} from "../components/PrimaryFeatures";
import {SecondaryFeatures} from "../components/SecondaryFeatures";
import {CallToAction} from "../components/CallToAction";
import {Footer} from "../components/Footer";
import {Testimonials} from "../components/Testimonials";
import {Pricing} from "../components/Pricing";
import {Faqs} from "../components/Faqs";
import {OpenBetaBanner} from "../components/OpenBetaBanner";

export default function Home() {
    return (
        <>
            <OpenBetaBanner />
            <Header />
            <main>
                <Hero />
                <PrimaryFeatures />
                <SecondaryFeatures />
                <CallToAction />
                <Testimonials />
                <Pricing />
            </main>
            <Footer />
        </>
    )
}