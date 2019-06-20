<?php

/**
 * Helper class for MailPoet.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceMailPoet extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'mailpoet';

	/**
	 * Test the API connection.
	 *
	 * @since 1.5.4
	 * @param array $fields
	 * @return array{
	 *      @type bool|string $error The error message or false if no error.
	 *      @type array $data An array of data used to make the connection.
	 * }
	 */
	public function connect( $fields = array() ) {
		$response = array(
			'error' => false,
			'data'  => array(),
		);

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.5.4
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		return '';
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.5.4
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$response = array(
			'error' => false,
			'html'  => '',
		);
		$lists    = array();

		try {
			// MailPoet 2+
			if ( class_exists( 'WYSIJA' ) ) {
				$list_model = WYSIJA::get( 'list', 'model' );
				$lists      = $list_model->get( array( 'name', 'list_id' ), array(
					'is_enabled' => 1,
				) );

				// MailPoet 3.0
			} elseif ( defined( 'MAILPOET_INITIALIZED' ) && true === MAILPOET_INITIALIZED ) {

				$listing      = new MailPoet\Listing\Handler();
				$listing_data = $listing->get( '\MailPoet\Models\Segment', array() );

				if ( isset( $listing_data['items'] ) ) {
					foreach ( $listing_data['items'] as $segment ) {
						$lists[] = array(
							'list_id' => $segment->id,
							'name'    => $segment->name,
						);
					}
				}
			}

			$response['html'] = self::render_list_field( $lists, $settings );
		} catch ( Exception $e ) {
			$response['error'] = __( 'There was an error retrieveing your lists.', 'fl-builder' );
		}

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.5.4
	 * @param array $lists List data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */
	private function render_list_field( $lists, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		foreach ( $lists as $list ) {
			$options[ $list['list_id'] ] = $list['name'];
		}

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'List', 'An email list from a third party provider.', 'fl-builder' ),
			'options'   => $options,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to MailPoet.
	 *
	 * @since 1.5.4
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */
	public function subscribe( $settings, $email, $name = false ) {
		$response = array(
			'error' => false,
		);
		$user     = array(
			'email' => $email,
		);

		if ( ! class_exists( 'WYSIJA' )
			&& ( ! defined( 'MAILPOET_INITIALIZED' ) || ( defined( 'MAILPOET_INITIALIZED' ) && false === MAILPOET_INITIALIZED ) )
			) {
			$response['error'] = __( 'There was an error subscribing. MailPoet is not installed.', 'fl-builder' );
		} else {

			if ( $name ) {
				$names = explode( ' ', $name );
			}

			// MailPoet 2+
			if ( class_exists( 'WYSIJA' ) ) {

				if ( $names && isset( $names[0] ) ) {
					$user['firstname'] = $names[0];
				}
				if ( $names && isset( $names[1] ) ) {
					$user['lastname'] = $names[1];
				}

				$helper = WYSIJA::get( 'user', 'helper' );

				$helper->addSubscriber( array(
					'user'      => $user,
					'user_list' => array(
						'list_ids' => array( $settings->list_id ),
					),
				));

				// MailPoet 3.0
			} elseif ( defined( 'MAILPOET_INITIALIZED' ) && true === MAILPOET_INITIALIZED ) {
				$subscriber = new MailPoet\Models\Subscriber();

				if ( $names && isset( $names[0] ) ) {
					$user['first_name'] = $names[0];
				}
				if ( $names && isset( $names[1] ) ) {
					$user['last_name'] = $names[1];
				}

				$subscribed = $subscriber::subscribe( $user, array( $settings->list_id ) );
				$errors     = $subscribed->getErrors();

				if ( false !== $errors ) {
					/* translators: %s: error */
					$response['error'] = sprintf( __( 'There was an error subscribing to MailPoet. %s', 'fl-builder' ), $errors[0] );
				}
			}
		}

		return $response;
	}
}
