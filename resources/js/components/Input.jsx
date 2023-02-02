import React from 'react'

export function Input({ id, label, type = 'text', append, ...props }) {
  return (
    <div>
      {label && (
        <label htmlFor={id} className='mb-3 block text-sm font-medium text-gray-700'>
          {label}
        </label>
      )}
      {append ? (
        <div className='mt-1 flex rounded-md shadow-sm'>
          <input
            id={id}
            type={type}
            {...props}
            className='block w-full appearance-none rounded-none rounded-l-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-blue-500 sm:text-sm'
          />
          <span className='inline-flex items-center rounded-r-md border border-l-0 border-gray-200 bg-gray-50 px-3 text-gray-500 sm:text-sm'>
            {append}
          </span>
        </div>
      ) : (
        <input
          id={id}
          type={type}
          {...props}
          className='block w-full appearance-none rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-blue-500 sm:text-sm'
        />
      )}
    </div>
  )
}
