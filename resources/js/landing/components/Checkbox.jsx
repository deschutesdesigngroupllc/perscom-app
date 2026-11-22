import clsx from 'clsx'
import PropTypes from 'prop-types'

export function Checkbox({ name, value, onChange, className }) {
  return (
    <input
      type='checkbox'
      name={name}
      value={value}
      className={clsx(
        'size-4 rounded accent-brand-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600',
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
