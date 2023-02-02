import React from "react";

import {Container} from "../components/Container";
import {Header} from "@/Pages/Pages/Header";

export function Tenant({ children }) {
    return (
        <>
            <Header />
            <main>
                <Container>
                    <div dangerouslySetInnerHTML={{ __html: children }} />
                </Container>
            </main>
        </>
    );
}
