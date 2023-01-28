import React from "react";
import {render} from "react-dom";
import {createInertiaApp} from "@inertiajs/inertia-react";
import {InertiaProgress} from "@inertiajs/progress";
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";

import "../css/app.css";

import.meta.glob(["../svg/**", "../images/**"]);

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob("./Pages/**/*.jsx")),
    setup({ el, App, props }) {
        render(React.createElement(App, props), el);
    },
});

InertiaProgress.init({ color: "#2563EB" });
