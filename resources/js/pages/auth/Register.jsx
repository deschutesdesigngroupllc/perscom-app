import React, { useEffect } from 'react'
import { AuthLayout } from '../../layouts/Auth'
import { Button } from '../../components/Button'
import { Input } from '../../components/Input'
import { Label } from '../../components/Label'
import { ValidationErrors } from '../../components/ValidationErrors'
import { Head, Link, useForm } from '@inertiajs/inertia-react'

export default function Register() {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })

  useEffect(() => {
    return () => {
      reset('password', 'password_confirmation')
    }
  }, [])

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.type === 'checkbox' ? event.target.checked : event.target.value)
  }

  const submit = (e) => {
    e.preventDefault()
    post(route('register'))
  }

  return (
    <AuthLayout>
      <Head title='Register' />
      <ValidationErrors errors={errors} />
      <form onSubmit={submit}>
        <div>
          <Label forInput='name' value='Name' />
          <Input
            type='text'
            name='name'
            value={data.name}
            className='mt-1 block w-full'
            autoComplete='name'
            onChange={onHandleChange}
            required
          />
        </div>
        <div className='mt-4'>
          <Label forInput='email' value='Email' />
          <Input
            type='email'
            name='email'
            value={data.email}
            className='mt-1 block w-full'
            autoComplete='username'
            onChange={onHandleChange}
            required
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
            required
          />
        </div>
        <div className='mt-4'>
          <Label forInput='password_confirmation' value='Confirm Password' />
          <Input
            type='password'
            name='password_confirmation'
            value={data.password_confirmation}
            className='mt-1 block w-full'
            onChange={onHandleChange}
            required
          />
        </div>
        <div className='mt-4 flex items-center justify-end'>
          <Link href={route('login')} className='text-sm text-gray-600 underline hover:text-gray-800'>
            Already registered?
          </Link>

          <Button className='ml-4' processing={processing} color='blue'>
            Register
          </Button>
        </div>
      </form>
    </AuthLayout>
  )
}
