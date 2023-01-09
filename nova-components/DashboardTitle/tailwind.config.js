/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.{js,vue}',
    ],
    prefix: 'dashboard-title-',
    corePlugins: {
        preflight: false,
    }
}
