import React from 'react'
import { render } from 'react-dom'
import { createInertiaApp } from '@inertiajs/inertia-react'

createInertiaApp({
    resolve: name => require(`./pages/${name}`),
    setup({ el, App, props }) {
        render(<App {...props} />, el)
    },
})