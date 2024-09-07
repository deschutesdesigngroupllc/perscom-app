import React from 'react'
import PropTypes from 'prop-types'
import { Button, ButtonLink } from '../../components/Button'
import { AuthLayout } from '../../layouts/Auth'
import { ValidationErrors } from '../../components/ValidationErrors'
import { Head, Link, useForm } from '@inertiajs/react'

export function Authorize({ client, description, image, name, scopes, state, authToken, csrfToken, tenant }) {
  const { post, processing, errors } = useForm({
    state: state,
    client_id: client,
    auth_token: authToken,
    _token: csrfToken
  })

  const submit = (e) => {
    e.preventDefault()
    post(
      route('passport.authorizations.approve', {
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
      <form onSubmit={submit}>
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
          <Button className='w-full' processing={processing} color='blue'>
            {' '}
            Authorize{' '}
          </Button>
          <ButtonLink
            href={route('passport.authorizations.deny', {
              tenant: tenant
            })}
            method='delete'
            color='slate'
            className='w-full'
          >
            {' '}
            Deny{' '}
          </ButtonLink>
        </div>
      </form>
    </AuthLayout>
  )
}

Authorize.propTypes = {
  client: PropTypes.string,
  name: PropTypes.string,
  scopes: PropTypes.array,
  state: PropTypes.string,
  authToken: PropTypes.string,
  csrfToken: PropTypes.string
}

export default Authorize
