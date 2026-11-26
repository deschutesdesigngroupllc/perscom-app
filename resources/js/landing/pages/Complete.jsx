import { Logo } from '../components/Logo'
import { RegisterLayout } from '../layouts/Register'

export default function Complete() {
  return (
    <RegisterLayout position='justify-start'>
      <div className='flex flex-col items-start justify-start'>
        <div className='flex w-full items-center justify-center'>
          <Logo className='mb-2 h-16 w-auto text-gray-900 sm:h-18 md:h-20' />
        </div>
        <h1 className='mt-10 text-xl font-bold tracking-tight text-gray-800'>Building Your Organization</h1>
        <p className='mt-2 text-sm'>
          Your email is verified and setup has started. It usually takes under a minute. We&apos;ll email you a link to set your password
          and sign in as soon as it&apos;s ready.
        </p>
      </div>
    </RegisterLayout>
  )
}
