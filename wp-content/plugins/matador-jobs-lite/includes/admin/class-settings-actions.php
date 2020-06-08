<?php
/**
 * Matador / Settings
 *
 * This contains the settings structure and provides functions to manipulate saved settings.
 * This class is extended to create and validate field input on the settings page.
 *
 * @link        http://matadorjobs.com/
 * @since       3.1.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Admin
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2018, Jeremy Scott, Paul Bearne
 *
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 */

namespace matador;

final class Settings_Actions {

	/**
	 * Magic Method: Constructor
	 *
	 * Class constructor prepares 'key' and 'data' variables.
	 *
	 * @since 3.1.0
	 */
	public function __construct() {
		add_action( 'matador_options_after_set', array( __CLASS__, 'trigger_rewrite_flush' ), 10, 2 );
		add_action( 'matador_options_after_set_license_core', array( __CLASS__, 'activate_license' ), 10, 2 );
		add_action( 'matador_options_after_unset_license_core', array( __CLASS__, 'deactivate_license' ), 10, 1 );
	}

	/**
	 * Trigger Rewrites Flush
	 *
	 * Determines if a field assigned as a rewrite is valid, and if so,
	 * sets a transient to flush rewrite rules.
	 *
	 * @access public
	 * @static
	 * @since 3.1.0
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return void
	 */
	public static function trigger_rewrite_flush( $key, $value ) {

		if ( ! $value && ! $key ) {
			return;
		}

		$triggers = array( 'post_type_slug_job_listing' );

		foreach ( Matador::variable( 'job_taxonomies' ) as $name => $taxonomy ) {
			$triggers[] = strtolower( 'taxonomy_slug_' . $name );
		}

		/**
		 * Filter: Rewrite Triggers
		 *
		 * Allows us to add settings keys to the list of triggers, so that
		 * when the setting is changed, a flush_rewrite_rules will be triggered
		 * on the next admin page load.
		 *
		 * @since 3.1.0
		 */
		$triggers = apply_filters( 'matador_options_rewrite_triggers', $triggers );

		if ( in_array( strtolower( $key ), $triggers, true ) ) {
			new Event_Log( 'options-trigger-rewrite', __( 'A setting that affects rewrites was changed. Rewrites will be flushed on next admin load.', 'matador-jobs' ) );
			set_transient( Matador::variable( 'flush_rewrite_rules', 'transients' ), true, 30 );
		}
	}

	/**
	 * Activate Licenses
	 *
	 * Runs the license activator upon submission of a new or changed
	 * license key.
	 *
	 * @access public
	 * @static
	 * @since 3.0.0
	 * @since 3.1.0 underwent drastic refactoring
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return void
	 */
	public static function activate_license( $key = null, $value = null ) {

		if ( ! $key ) {
			return;
		}

		new Event_Log( 'options-update-license', __( 'The Matador Jobs Pro license key was updated. Running an activation check.', 'matador-jobs' ) );

		$license           = trim( $value );
		$license_data      = null;
		$activation_status = null;

		$params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_id'    => Matador::ID,
			'url'        => home_url(),
		);

		$response = wp_remote_post( Matador::LICENSES_HOST, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $params,
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			$message = ( is_wp_error( $response ) ) ? $response->get_error_message() : esc_html__( 'An error occurred, please try again.', 'matador-jobs' );

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						// translators: Placeholder contains date license expired.
						$message = sprintf( esc_html__( 'Your license key expired on %s.', 'matador-jobs' ), date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) );
						break;
					case 'revoked':
						$message = esc_html__( 'Your license key has been disabled.', 'matador-jobs' );
						break;
					case 'missing':
						$message = esc_html__( 'You provided an invalid license key. Check to make sure you entered the correct key.', 'matador-jobs' );
						break;
					case 'invalid':
					case 'site_inactive':
						$message = esc_html__( 'Your license is not active for this URL.', 'matador-jobs' );
						break;
					case 'item_name_mismatch':
						$message = esc_html__( 'This appears to be an invalid license key.', 'matador-jobs' );
						break;
					case 'no_activations_left':
						$message = esc_html__( 'Your license key has reached its activation limit.', 'matador-jobs' );
						break;
					default:
						$message = esc_html__( 'An error occurred, please try again.', 'matador-jobs' );
						break;
				}
			}
		}

		if ( ! empty( $message ) ) {
			new Event_Log( 'options-license-activation-failed', $message );
			Settings::field_error( $key, $message );
		} elseif ( isset( $license_data->license ) ) {
			$activation_status = $license_data->license;
			new Event_Log( 'options-license-activation-success', __( 'Matador Jobs Pro license was validated.', 'matador-jobs' ) );
		}

		add_filter( 'matador_options_set_only_public', '__return_false' );
		Matador::$settings->{$key . '_status'} = $activation_status;
		remove_filter( 'matador_options_set_only_public', '__return_false' );
	}

	/**
	 * Deactivate Licenses
	 *
	 * Removes the activation status for the license.
	 *
	 * @access public
	 * @static
	 * @since 3.1.0
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public static function deactivate_license( $key = null ) {
		add_filter( 'matador_options_set_only_public', '__return_false' );
		Matador::$settings->{$key . '_status'} = null;
		remove_filter( 'matador_options_set_only_public', '__return_false' );
	}

}
