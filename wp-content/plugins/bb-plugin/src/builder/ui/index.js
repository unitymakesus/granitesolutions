import React, { Component, Fragment } from 'react'
import InlineEditor from 'components/inline-editor'
import { NotificationsManager } from 'builder/notifications'
import { SVGSymbols } from './svg'

/**
 * Class for rendering all of the main
 * builder UI components.
 *
 * @since 2.1
 * @class UI
 */
class UI extends Component {
	render() {
		return (
			<Fragment>
				<InlineEditor />
				<NotificationsManager />
				<SVGSymbols />
			</Fragment>
		)
	}
}

export default UI
