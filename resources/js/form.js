import Vue from "vue";
import Laraform from "laraform";
import { createInertiaApp } from "@inertiajs/inertia-vue";
import { InertiaProgress } from "@inertiajs/progress";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

import "../css/form.scss";

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),
    setup({ el, App, props, plugin }) {
        Vue.use(plugin);
        Vue.use(Laraform);
        new Vue({
            render: (h) => h(App, props),
        }).$mount(el);
    },
});

InertiaProgress.init({ color: "#2563EB" });
