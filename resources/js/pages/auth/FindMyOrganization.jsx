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
          <div className='mb-4 text-sm leading-normal text-gray-600'>We were able to find your organization&apos;s info:</div>
          <div className='text-sm'>
            <span className='font-bold'>Organization: </span>
            <span className='text-gray-600'>{tenant}</span>
          </div>
          <div className='mb-2 text-sm'>
            <span className='font-bold'>Dashboard URL: </span>
            <a className='text-gray-600 underline' href={url}>
              {url}
            </a>
          </div>
          <ButtonLink color='blue' className='mt-4 w-full' href={url}>
            Go to Dashboard <span aria-hidden='true'>&nbsp;&rarr;</span>
          </ButtonLink>
        </>
      ) : (
        <>
          <div className='mb-4 text-sm leading-normal text-gray-600'>
            Don&apos;t remember your oganization info? No problem. Just let us know the account email address on file and we will look up
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
