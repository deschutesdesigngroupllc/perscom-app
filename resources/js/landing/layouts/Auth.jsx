import PropTypes from 'prop-types'
import { Logo } from '../components/Logo'

export function AuthLayout({ children, image }) {
  return (
    <div className='flex min-h-screen flex-col items-center bg-white pt-6 sm:justify-center sm:bg-gray-100 sm:pt-0'>
      <div className='hidden sm:flex'>
        <a href={route('web.landing.home')}>{image ? <img className='h-24' src={image} /> : <Logo className='h-24' />}</a>
      </div>
      <div className='mt-6 min-h-screen w-full overflow-hidden bg-white p-6 shadow-none sm:min-h-min sm:max-w-lg sm:rounded-lg sm:shadow-md'>
        <div className='flex items-center justify-center sm:hidden'>
          <a href={route('web.landing.home')}>
            <Logo className='h-24' />
          </a>
        </div>
        {children}
      </div>
    </div>
  )
}

AuthLayout.propTypes = {
  children: PropTypes.array,
  image: PropTypes.string
}

export default AuthLayout
