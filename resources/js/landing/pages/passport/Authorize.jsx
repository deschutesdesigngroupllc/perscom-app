import React from 'react'
import PropTypes from 'prop-types'
import { Button } from '../../components/Button'
import { AuthLayout } from '../../layouts/Auth'
import { ValidationErrors } from '../../components/ValidationErrors'
import { Head, useForm } from '@inertiajs/react'

export function Authorize({ client, description, image, name, scopes, state, authToken, tenant }) {
  const { post, processing, errors } = useForm({
    state: state,
    client_id: client,
    auth_token: authToken
  })

  const approve = (e) => {
    e.preventDefault()

    post(
      route('passport.authorizations.approve', {
        tenant: tenant
      })
    )
  }

  const deny = (e) => {
    e.preventDefault()

    post(
      route('passport.authorizations.deny', {
        _method: 'delete',
        tenant: tenant
      })
    )
  }

  const scopeList = []
  scopes.forEach((data) => {
    scopeList.push(<li key={data['id']}>{data['description']}</li>)
  })

  return (
    <AuthLayout image={image}>
      <Head title='Authorization' />
      <ValidationErrors errors={errors} />
      <div className='mb-4'>
        <span className='font-bold'>{name}</span> is requesting permission to access your account.
      </div>
      {description && (
        <div className='border-y py-2 text-center'>
          <div className='text-sm'>{description}</div>
        </div>
      )}
      {scopeList.length > 0 && <ul className='my-4 list-inside list-disc'>{scopeList}</ul>}
      <div className='flex flex-col items-center justify-center space-y-2 pt-4'>
        <form onSubmit={approve} className='w-full'>
          <Button className='w-full' processing={processing} color='blue'>
            Authorize
          </Button>
        </form>

        <form onSubmit={deny} className='w-full'>
          <Button className='w-full' processing={processing} color='slate'>
            Deny
          </Button>
        </form>
      </div>
    </AuthLayout>
  )
}

Authorize.propTypes = {
  client: PropTypes.string,
  name: PropTypes.string,
  scopes: PropTypes.array,
  state: PropTypes.string,
  authToken: PropTypes.string
}

export default Authorize
