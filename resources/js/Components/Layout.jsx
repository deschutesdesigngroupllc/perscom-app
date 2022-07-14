import React from "react";
import "focus-visible";
import "../styles/tailwind.css";

export default function Layout({ children }) {
    return (
        <html className="h-full scroll-smooth bg-white antialiased [font-feature-settings:'ss01']" lang="en">
            <head>
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
                <link
                    rel="stylesheet"
                    href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lexend:wght@400;500&display=swap"
                />
            </head>
            <body className="flex h-full flex-col">{children}</body>
        </html>
    );
}
