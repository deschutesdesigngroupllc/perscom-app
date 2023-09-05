import React from 'react'
import PropTypes from 'prop-types'
import { Head, useForm } from '@inertiajs/react'
import { AuthLayout } from '../../layouts/Auth'
import { ValidationErrors } from '../../components/ValidationErrors'
import { Button, ButtonLink } from '../../components/Button'
import { Input } from '../../components/Input'

function FindMyOrganization({ url, tenant }) {
  const { data, setData, post, processing, errors } = useForm({
    email: ''
  })

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value)
  }

  const submit = (e) => {
    e.preventDefault()
    post(route('web.find-my-organization.store'))
  }

  return (
    <AuthLayout>
      <Head title='Find My Organization' />

      {url ? (
        <>
          <div className='mb-4 text-sm leading-normal'>We were able to find your organization&apos;s info:</div>
          <div>
            <span className='prose text-sm font-bold'>Organization: </span>
            <span className='prose text-sm'>{tenant}</span>
          </div>
          <div className='mb-2'>
            <span className='prose text-sm font-bold'>Dashboard URL: </span>
            <a className='prose text-sm underline' href={url}>
              {url}
            </a>
          </div>
          <ButtonLink color='blue' className='mt-4 w-full' href={url}>
            Go to Dashboard <span aria-hidden='true'>&nbsp;&rarr;</span>
          </ButtonLink>
        </>
      ) : (
        <>
          <div className='prose mb-4 text-sm leading-normal'>
            Don&apos;t remember your organization info? No problem. Just let us know the account email address on file and we will look up
            the organization&apos;s info.
          </div>

          <ValidationErrors errors={errors} />
          <form onSubmit={submit}>
            <Input type='text' name='email' value={data.email} className='mt-1 block w-full' onChange={onHandleChange} />
            <div className='mt-4 flex items-center justify-end'>
              <Button className='ml-4' processing={processing} color='blue'>
                {' '}
                Find my organization{' '}
              </Button>
            </div>
          </form>
        </>
      )}
    </AuthLayout>
  )
}

FindMyOrganization.propTypes = {
  url: PropTypes.string,
  tenant: PropTypes.string
}

export default FindMyOrganization
