import React from 'react'
import PropTypes from 'prop-types'
import SwaggerUI from 'swagger-ui-react'
import 'swagger-ui-react/swagger-ui.css'

export function App({ url }) {
  return <SwaggerUI url={url} />
}

App.propTypes = {
  url: PropTypes.string
}

export default App
