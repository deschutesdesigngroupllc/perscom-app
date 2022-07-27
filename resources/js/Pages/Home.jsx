import React from "react";

import {Header} from "@/Components/Header";
import {Hero} from "@/Components/Hero";
import {PrimaryFeatures} from "@/Components/PrimaryFeatures";
import {SecondaryFeatures} from "@/Components/SecondaryFeatures";
import {Footer} from "@/Components/Footer";
import {Pricing} from "@/Components/Pricing";
import {OpenBetaBanner} from "@/Components/OpenBetaBanner";

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
    );
}
