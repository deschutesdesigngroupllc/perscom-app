import React from 'react'
import clsx from 'clsx'
import PropTypes from 'prop-types'

const baseStyles = {
  solid:
    'transition duration-75 group inline-flex items-center justify-center rounded-md py-1.5 px-5 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2',
  outline: 'transition duration-75 group inline-flex ring-1 items-center justify-center rounded-md py-1.5 px-5 text-sm focus:outline-none'
}

const variantStyles = {
  solid: {
    slate:
      'bg-slate-700 text-white hover:bg-slate-800 hover:text-gray-100 active:bg-slate-900 active:text-gray-300 focus-visible:outline-gray-900',
    blue: 'bg-blue-600 text-white hover:text-gray-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600',
    white: 'bg-white text-gray-800 hover:bg-blue-50 active:bg-blue-200 active:text-gray-600 focus-visible:outline-white',
    gray: 'bg-gray-100 text-gray-800 hover:bg-gray-50 active:bg-gray-200 active:text-gray-600 focus-visible:outline-white'
  },
  outline: {
    slate:
      'ring-gray-200 text-slate-700 hover:text-gray-800 hover:ring-gray-300 active:bg-gray-100 active:text-gray-600 focus-visible:outline-blue-600 focus-visible:ring-gray-300',
    white: 'ring-gray-700 text-white hover:ring-gray-500 active:ring-gray-700 active:text-gray-400 focus-visible:outline-white'
  }
}

export function Button({ variant = 'solid', color = 'slate', className, processing = false, ...props }) {
  return (
    <button
      className={clsx(baseStyles[variant], variantStyles[variant][color], className, processing && 'opacity-25')}
      disabled={processing}
      {...props}
    />
  )
}

Button.propTypes = {
  variant: PropTypes.string,
  color: PropTypes.string,
  className: PropTypes.string,
  processing: PropTypes.bool
}

export function ButtonLink({ variant = 'solid', color = 'slate', href, className, ...props }) {
  return <a href={href} className={clsx(baseStyles[variant], variantStyles[variant][color], className)} {...props} />
}

ButtonLink.propTypes = {
  variant: PropTypes.string,
  color: PropTypes.string,
  className: PropTypes.string,
  href: PropTypes.string
}
