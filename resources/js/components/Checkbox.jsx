import React from 'react'

export function Checkbox({ name, value, onChange }) {
  return (
    <input
      type='checkbox'
      name={name}
      value={value}
      className='rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50'
      onChange={(e) => onChange(e)}
    />
  )
}
