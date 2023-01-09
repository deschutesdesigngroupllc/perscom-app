/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.{js,vue}',
    ],
    prefix: 'dashboard-actions-',
    corePlugins: {
        preflight: false,
    }
}
