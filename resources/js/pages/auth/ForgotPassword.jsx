import React from 'react'
import PropTypes from 'prop-types'
import { Button } from '../../components/Button'
import { Input } from '../../components/Input'
import { ValidationErrors } from '../../components/ValidationErrors'
import { Head, useForm } from '@inertiajs/inertia-react'
import { AuthLayout } from '../../layouts/Auth'

function ForgotPassword({ status }) {
  const { data, setData, post, processing, errors } = useForm({
    email: ''
  })

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value)
  }

  const submit = (e) => {
    e.preventDefault()
    post(route('password.email'))
  }

  return (
    <AuthLayout>
      <Head title='Forgot Password' />
      <div className='mb-4 text-sm leading-normal text-gray-600'>
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow
        you to choose a new one.
      </div>

      {status && <div className='mb-4 text-sm font-medium text-green-600'>{status}</div>}

      <ValidationErrors errors={errors} />
      <form onSubmit={submit}>
        <Input type='text' name='email' value={data.email} className='mt-1 block w-full' onChange={onHandleChange} />
        <div className='mt-4 flex items-center justify-end'>
          <Button className='ml-4' processing={processing} color='blue'>
            {' '}
            Email password reset link{' '}
          </Button>
        </div>
      </form>
    </AuthLayout>
  )
}

ForgotPassword.propTypes = {
  status: PropTypes.string
}

export default ForgotPassword
