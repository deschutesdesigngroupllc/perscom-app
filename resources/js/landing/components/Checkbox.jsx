import clsx from 'clsx'
import PropTypes from 'prop-types'

export function Checkbox({ name, value, onChange, className }) {
  return (
    <input
      type='checkbox'
      name={name}
      value={value}
      className={clsx(
        'rounded border-gray-300 text-brand-600 shadow-sm focus:border-brand-300 focus:ring-2 focus:ring-brand-500/50',
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
