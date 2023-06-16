import React, {useEffect} from 'react'
import PropTypes from 'prop-types'
import {Button} from '../../components/Button'
import {AuthLayout} from '../../layouts/Auth'
import {Input} from '../../components/Input'
import {Label} from '../../components/Label'
import {ValidationErrors} from '../../components/ValidationErrors'
import {Head, useForm, usePage} from '@inertiajs/inertia-react'

export function ResetPassword({ token, email }) {
  const { flash } = usePage().props

  const { data, setData, post, processing, errors, reset } = useForm({
    token: token,
    email: email,
    password: '',
    password_confirmation: ''
  })

  useEffect(() => {
    return () => {
      reset('password', 'password_confirmation')
    }
  }, [])

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value)
  }

  const submit = (e) => {
    e.preventDefault()
    post(route('password.update'))
  }

  return (
    <AuthLayout>
      <Head title='Reset Password' />

      {flash.status && <div className='mb-4 text-sm font-medium text-green-600'>{flash.status}</div>}

      <ValidationErrors errors={errors} />

      <form onSubmit={submit}>
        <div>
          <Label forInput='email' value='Email' />
          <Input
            type='email'
            name='email'
            value={data.email}
            className='mt-1 block w-full'
            autoComplete='username'
            onChange={onHandleChange}
          />
        </div>
        <div className='mt-4'>
          <Label forInput='password' value='Password' />
          <Input
            type='password'
            name='password'
            value={data.password}
            className='mt-1 block w-full'
            autoComplete='new-password'
            onChange={onHandleChange}
          />
        </div>
        <div className='mt-4'>
          <Label forInput='password_confirmation' value='Confirm Password' />
          <Input
            type='password'
            name='password_confirmation'
            value={data.password_confirmation}
            className='mt-1 block w-full'
            autoComplete='new-password'
            onChange={onHandleChange}
          />
        </div>
        <div className='mt-4 flex items-center justify-end'>
          <Button className='ml-4' processing={processing} color='blue'>
            {' '}
            Reset password{' '}
          </Button>
        </div>
      </form>
    </AuthLayout>
  )
}

ResetPassword.propTypes = {
  token: PropTypes.string,
  email: PropTypes.string
}

export default ResetPassword
