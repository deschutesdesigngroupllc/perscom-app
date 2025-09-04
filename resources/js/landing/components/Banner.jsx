import React from 'react'
import clsx from 'clsx'
import PropTypes from 'prop-types'

export function Banner({ banner }) {
  return (
    <div
      style={{ backgroundColor: banner.background_color }}
      className={clsx('flex items-center justify-center gap-x-6 bg-gray-900 px-4 py-2.5 text-center sm:px-3.5 dark:bg-gray-800', {
        'sm:before:flex-1': banner.link_url,
        'bg-blue-600!': !banner.background_color
      })}
    >
      <div
        style={{ color: banner.text_color }}
        className={clsx('text-sm leading-6', {
          'text-white!': !banner.text_color
        })}
      >
        <strong className='font-semibold'>{banner.title}</strong>
        <svg viewBox='0 0 2 2' className='mx-2 inline h-0.5 w-0.5 fill-current' aria-hidden='true'>
          <circle cx='1' cy='1' r='1' />
        </svg>
        {banner.message}
      </div>
      {banner.link_url && (
        <div className='flex flex-1 justify-end'>
          <a href={banner.link_url} target='_blank' className='-m-3 flex items-center space-x-1 p-3 focus-visible:-outline-offset-4'>
            <strong
              style={{ color: banner.text_color }}
              className={clsx('text-sm leading-6 font-semibold', {
                'text-white!': !banner.text_color
              })}
            >
              {banner.link_text}
            </strong>
            <svg
              xmlns='http://www.w3.org/2000/svg'
              fill='none'
              viewBox='0 0 24 24'
              strokeWidth='1.5'
              stroke='currentColor'
              className='h-4 stroke-white'
            >
              <path strokeLinecap='round' strokeLinejoin='round' d='M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3' />
            </svg>
          </a>
        </div>
      )}
    </div>
  )
}

Banner.propTypes = {
  banner: PropTypes.object.isRequired
}
