import { Logo } from '../components/Logo'
import { RegisterLayout } from '../layouts/Register'

export default function Verify() {
  return (
    <RegisterLayout position='justify-start'>
      <div className='flex flex-col items-start justify-start'>
        <div className='flex w-full items-center justify-center'>
          <Logo className='mb-2 h-16 w-auto text-gray-900 sm:h-18 md:h-20' />
        </div>
        <h1 className='mt-10 text-xl font-bold tracking-tight text-gray-800'>Check Your Inbox</h1>
        <p className='mt-2 text-sm'>We sent you a verification link. Open it within 24 hours to create your organization.</p>
        <p className='mt-4 text-sm text-gray-600'>
          Don&apos;t see it? Check your spam or promotions folder. It can take a minute or two to arrive.
        </p>
      </div>
    </RegisterLayout>
  )
}
