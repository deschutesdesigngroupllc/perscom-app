import clsx from 'clsx'
import PropTypes from 'prop-types'

export function Checkbox({ name, value, onChange, className }) {
  return (
    <input
      type='checkbox'
      name={name}
      value={value}
      className={clsx(
        'focus:ring-opacity-50 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200',
        className
      )}
      onChange={(e) => onChange(e)}
    />
  )
}

Checkbox.propTypes = {
  name: PropTypes.string,
  value: PropTypes.oneOfType([PropTypes.bool, PropTypes.string]),
  onChange: PropTypes.func
}

export default Checkbox
