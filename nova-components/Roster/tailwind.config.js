/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.{js,vue}',
    ],
    prefix: 'roster-',
    corePlugins: {
        preflight: false,
    }
}
