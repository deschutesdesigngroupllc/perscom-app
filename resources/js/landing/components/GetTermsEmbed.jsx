import React from 'react'
import useGetTermsEmbed from '../hooks/useGetTermsEmbed.jsx'

const GetTermsEmbed = ({ policyId, policyName }) => {
  const { loading, embedProps } = useGetTermsEmbed({ policyId, policyName })

  return (
    <div className='flex flex-col items-center justify-center'>
      {loading && (
        <div className='flex flex-col items-center pt-16'>
          <div className='h-10 w-10 animate-spin rounded-full border-4 border-gray-300 border-t-blue-600'></div>
          <p className='mt-2 text-sm font-semibold text-gray-600'>Loading document...</p>
        </div>
      )}
      <div {...embedProps} />
    </div>
  )
}

export default GetTermsEmbed
