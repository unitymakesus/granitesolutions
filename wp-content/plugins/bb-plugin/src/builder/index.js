import React from 'react'
import ReactDOM from 'react-dom'
import ErrorBoundary from './error-boundary'
import UI from './ui'
import './style.scss'

ReactDOM.render( (
	<ErrorBoundary>
		<UI />
	</ErrorBoundary>
), document.getElementById( 'fl-ui-root' ) )
