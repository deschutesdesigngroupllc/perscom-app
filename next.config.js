/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  distDir: '../../public/landing',
  images: {
    loader: 'imgix',
  },
}

module.exports = nextConfig