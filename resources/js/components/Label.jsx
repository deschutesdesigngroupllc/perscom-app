import React from 'react'
import PropTypes from 'prop-types'

export function Label({ forInput, value, className, children }) {
  return (
    <label htmlFor={forInput} className={`prose block text-sm font-medium ` + className}>
      {value ? value : children}
    </label>
  )
}

Label.propTypes = {
  forInput: PropTypes.string,
  value: PropTypes.string,
  className: PropTypes.string,
  children: PropTypes.object
}

export default Label
