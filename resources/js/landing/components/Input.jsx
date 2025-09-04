import React from 'react'
import PropTypes from 'prop-types'

export function Input({ id, label, type = 'text', append, ...props }) {
  return (
    <div>
      {label && (
        <label htmlFor={id} className='mb-3 block text-sm font-medium'>
          {label}
        </label>
      )}
      {append ? (
        <div className='mt-1 flex rounded-md shadow-sm'>
          <input
            id={id}
            type={type}
            {...props}
            className='block w-full appearance-none rounded-none rounded-l-md border border-gray-200 bg-gray-50 px-3 py-2 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 focus:outline-none sm:text-sm'
          />
          <span className='inline-flex items-center rounded-r-md border border-l-0 border-gray-200 bg-gray-50 px-3 sm:text-sm'>
            {append}
          </span>
        </div>
      ) : (
        <input
          id={id}
          type={type}
          {...props}
          className='block w-full appearance-none rounded-md border border-gray-200 bg-gray-50 px-3 py-2 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 focus:outline-none sm:text-sm'
        />
      )}
    </div>
  )
}

Input.propTypes = {
  id: PropTypes.string,
  label: PropTypes.string,
  type: PropTypes.string,
  append: PropTypes.string
}

export default Input
