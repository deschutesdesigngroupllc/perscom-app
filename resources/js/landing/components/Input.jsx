import PropTypes from 'prop-types'

export function Input({ id, label, type = 'text', append, ...props }) {
  return (
    <div>
      {label && (
        <label htmlFor={id} className='mb-1.5 block text-sm font-medium'>
          {label}
        </label>
      )}
      {append ? (
        <div className='flex rounded-md bg-white shadow-xs ring-1 ring-gray-300 ring-inset transition-shadow hover:ring-gray-400 focus-within:ring-2 focus-within:ring-brand-600 focus-within:hover:ring-brand-600'>
          <input
            id={id}
            type={type}
            {...props}
            className='block min-w-0 flex-1 rounded-l-md border-0 bg-transparent px-3 py-2 text-gray-900 placeholder:text-gray-400 focus:ring-0 focus:outline-none sm:text-sm'
          />
          <span className='inline-flex shrink-0 items-center pr-3 text-gray-500 select-none sm:text-sm'>{append}</span>
        </div>
      ) : (
        <input
          id={id}
          type={type}
          {...props}
          className='block w-full rounded-md border-0 bg-white px-3 py-2 text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset transition-shadow placeholder:text-gray-400 hover:ring-gray-400 focus:ring-2 focus:ring-brand-600 focus:ring-inset focus:outline-none sm:text-sm'
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
