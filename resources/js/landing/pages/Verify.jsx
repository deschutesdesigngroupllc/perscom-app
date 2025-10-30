import { ButtonLink } from '../components/Button'
import { Logo } from '../components/Logo'
import { RegisterLayout } from '../layouts/Register'

export default function Verify() {
  const handleOpenEmail = () => {
    window.location.href = 'mailto:'
  }

  return (
    <RegisterLayout position='justify-start'>
      <div className='flex flex-col items-start justify-start'>
        <div className='flex w-full items-center justify-center'>
          <a href={route('web.landing.home')}>
            <Logo className='mb-2 h-16 w-auto sm:h-18 md:h-20' />
          </a>
        </div>
        <h1 className='mt-10 text-xl font-bold tracking-tight text-gray-800'>You are almost there</h1>
        <p className='mt-2 text-sm'>Please check your email to complete the registration process.</p>
        <ButtonLink color='blue' className='mt-4 w-full' onClick={handleOpenEmail}>
          Open Email <span aria-hidden='true'>&nbsp;&rarr;</span>
        </ButtonLink>
      </div>
    </RegisterLayout>
  )
}
