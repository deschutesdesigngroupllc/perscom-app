import React from 'react'
import clsx from 'clsx'
import PropTypes from 'prop-types'

export function Banner({ banner }) {
  return (
    <div
      style={{ backgroundColor: banner.background_color }}
      className={clsx('flex items-center justify-center gap-x-6 px-6 py-2.5 sm:px-3.5', {
        '!bg-blue-600': !banner.background_color
      })}
    >
      <p
        style={{ color: banner.text_color }}
        className={clsx('text-sm leading-6', {
          '!text-white': !banner.text_color
        })}
      >
        <a href={banner.link_url}>
          <strong className='font-semibold'>{banner.link_text}</strong>
          <svg viewBox='0 0 2 2' className='mx-2 inline h-0.5 w-0.5 fill-current' aria-hidden='true'>
            <circle cx={1} cy={1} r={1} />
          </svg>
          {banner.message}&nbsp;<span aria-hidden='true'>&rarr;</span>
        </a>
      </p>
    </div>
  )
}

Banner.propTypes = {
  banner: PropTypes.object.isRequired
}
