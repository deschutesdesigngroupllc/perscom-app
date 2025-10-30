import clsx from 'clsx'
import PropTypes from 'prop-types'

export function Container({ className, ...props }) {
  return <div className={clsx('mx-auto max-w-7xl px-4 sm:px-6 lg:px-8', className)} {...props} />
}

Container.propTypes = {
  className: PropTypes.string
}

export default Container
