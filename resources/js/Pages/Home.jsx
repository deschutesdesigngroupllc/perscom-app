import React from "react";

import { Header } from "@/Components/Header";
import { Hero } from "@/Components/Hero";
import { PrimaryFeatures } from "@/Components/PrimaryFeatures";
import { SecondaryFeatures } from "@/Components/SecondaryFeatures";
import { CallToAction } from "@/Components/CallToAction";
import { Footer } from "@/Components/Footer";
import { Testimonials } from "@/Components/Testimonials";
import { Pricing } from "@/Components/Pricing";
import { OpenBetaBanner } from "@/Components/OpenBetaBanner";

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
    );
}
