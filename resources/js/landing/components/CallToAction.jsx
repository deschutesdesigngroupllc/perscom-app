import backgroundImage from '../../../images/landing/background-call-to-action.jpg'
import { ButtonLink } from './Button'
import { Container } from './Container'

export function CallToAction() {
  return (
    <section id='call-to-action' className='relative overflow-hidden bg-blue-600 py-20 sm:py-32'>
      <div className='-trangray-x-[50%] -trangray-y-[50%] absolute top-1/2 left-1/2'>
        <img src={backgroundImage} alt='' width={2347} height={1244} aria-hidden='true' />
      </div>
      <Container className='relative'>
        <div className='mx-auto max-w-xl text-center'>
          <h2 className='text-3xl font-bold tracking-tight text-white sm:text-4xl'>Get started today.</h2>
          <p className='mt-4 text-base text-white'>
            Ready to streamline your operations and take control of your workflows? Sign up now and experience the power of PERSCOM.io.
          </p>
          <ButtonLink href={route('web.register.index')} color='white' className='mt-10'>
            {' '}
            Get 1 week free{' '}
          </ButtonLink>
        </div>
      </Container>
    </section>
  )
}
