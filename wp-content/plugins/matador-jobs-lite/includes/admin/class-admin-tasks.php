<?php
/**
 * Matador / Admin / Admin Tasks
 *
 * This contains the settings structure and provides functions to manipulate saved settings.
 * This class is extended to create and validate field input on the settings page.
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Admin
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott, Paul Bearne
 *
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 */

namespace matador;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Tasks {

	public function __construct() {
		add_action( 'current_screen', array( __CLASS__, 'admin_tasks' ), 50 );
		add_action( 'init', array( __CLASS__, 'applications_sync_now' ), 8 );
	}

	/**
	 * Add Tasks to this Screen.
	 *
	 * @since  3.0.0
	 */
	public static function admin_tasks() {

		self::flush_rewrite_rules();

		if ( isset( $_POST['matador_action'] ) && isset( $_POST['_wpnonce'] ) && ( check_admin_referer( Matador::variable( 'options', 'nonce' ) ) ) ) {

			$action = isset( $_POST['matador_action'] ) ? strtolower( $_POST['matador_action'] ) : false;

			if ( in_array( $action, array( 'connect_to_bullhorn', 'sync', 'sync-tax', 'sync-jobs' ), true ) ) {

				switch ( $action ) {

					case 'connect_to_bullhorn':
						wp_safe_redirect( Bullhorn_Connection_Assistant::get_url() );
						die();

					case 'sync':
						self::import_sync_now();
						break;
				}
			}
		}
	}

	/**
	 * Jobs Sync Now
	 *
	 * This is triggered by an admin manually requesting a sync.
	 *
	 * @access public
	 * @static
	 * @since 3.1.0
	 * @since 3.4.0 $url param added
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public static function import_sync_now( $url = '' ) {
		if ( empty( $url ) ) {
			$url = Matador::variable( 'options_url' );
		}
		if ( ! get_transient( Matador::variable( 'doing_sync', 'transients' ) ) ) {
			wp_schedule_single_event( time(), 'matador_job_sync_now', array( 'manual' ) );
			wp_redirect( $url, 302 );
		} else {
			Admin_Notices::add( __( 'Cannot start a new manual sync while a sync is currently running.', 'matador-jobs' ), 'error', 'matador-sync-manual-rejected' );
		}
	}

	/**
	 * Applications Sync Now
	 *
	 * This is triggered by an admin manually requesting a sync.
	 *
	 * In version 3.1.0, the function was moved to these general admin tasks
	 * so that it could be used by the WPJM Extension and support Applications.
	 *
	 * @access public
	 * @static
	 * @since 3.0.0
	 * @since 3.1.0 function location was moved.
	 *
	 * @return void
	 */
	public static function applications_sync_now() {

		if (
			isset( $_REQUEST['application_sync'] ) &&
			wp_verify_nonce( $_REQUEST['application_sync'], 'application_sync' ) &&
			isset( $_REQUEST['sync'] ) &&
			isset( $_REQUEST['post_type'] ) &&
			Matador::variable( 'post_type_key_application' ) === $_REQUEST['post_type']
		) {
			if ( get_transient( Matador::variable( 'doing_app_sync', 'transients' ) ) ) {
				Admin_Notices::add( __( 'Cannot start a new batch application sync while a sync is currently running.', 'matador-jobs' ), 'error', 'matador-app-sync-manual-rejected' );
			} else {
				if ( is_numeric( $_REQUEST['sync'] ) ) {
					new Event_Log( 'application-manual-sync-single', esc_html( sprintf( __( 'An admin requested a manual sync for local application ', 'matador-jobs' ) . $_REQUEST['sync'] ) ) );
					new Application_Sync( intval( $_REQUEST['sync'] ) );
				} elseif ( 'all' === strtolower( $_REQUEST['sync'] ) ) {
					Scheduled_Events::application_sync();
				}
			}
			wp_safe_redirect( remove_query_arg( array(
				'sync',
				'application_sync',
			), $_SERVER['REQUEST_URI'] ) );
			exit;
		}
	}

	public static function flush_rewrite_rules() {

		if ( get_transient( Matador::variable( 'flush_rewrite_rules', 'transients' ) ) ) {

			delete_transient( Matador::variable( 'flush_rewrite_rules', 'transients' ) );

			Logger::add( 'success', 'flush-rewrite-rules', 'Matador flushed rewrite rules.' );

			flush_rewrite_rules();
		}
	}

	public static function is_uri_redirect_invalid() {

		if ( ! Matador::variable( 'api_redirect_uri' ) ) {

			return 'null_url';
		}

		$client = Matador::setting( 'bullhorn_api_client' );
		$valid  = Matador::setting( 'bullhorn_api_client_is_valid' );
		$secret = Matador::setting( 'bullhorn_api_secret' );

		// If we don't have a $client that is valid or a $secret
		// we aren't ready to test.
		if ( ! $client || ! $valid || ! $secret ) {

			return 'indeterminate';
		}

		$bullhorn   = new Bullhorn_Connection();
		$is_invalid = $bullhorn->is_redirect_uri_invalid();

		if ( null === $is_invalid ) {

			return 'indeterminate';
		} elseif ( true === $is_invalid ) {

			return 'invalid';
		} else {

			return 'valid';
		}
	}


	public static function bullhorn_authorize() {
		$bullhorn = new Bullhorn_Connection();
		try {
			$bullhorn->authorize();
			return true;
		} catch ( Exception $e ) {
			new Event_Log( $e->getName(), $e->getMessage() );
			return false;
		}
	}

	public static function bullhorn_deauthorize() {
		$bullhorn = new Bullhorn_Connection();
		$bullhorn->deauthorize();
	}

	public static function attempt_login() {
		$bullhorn = new Bullhorn_Connection();
		if ( $bullhorn->is_authorized() ) {
			try {
				$bullhorn->login();
			} catch ( Exception $e ) {
				new Event_Log( $e->getName(), $e->getMessage() );
				Admin_Notices::add( esc_html__( 'Login into Bullhorn failed see log for more info.', 'matador-jobs' ), 'warning', 'bullhorn-login-exception' );
			}
		} else {
			Admin_Notices::add( esc_html__( 'To attempt a login, you must have an authorized site.', 'matador-jobs' ), 'warning', 'bullhorn-test-reconnect' );
		}
	}

	public static function break_connection() {
		$credentials = get_option( 'bullhorn_api_credentials', array() );
		if ( array_key_exists( 'refresh_token', $credentials ) ) {
			$credentials['refresh_token'] = substr( $credentials['refresh_token'], -1 ) . substr( $credentials['refresh_token'], 0, -1 );
			update_option( 'bullhorn_api_credentials', $credentials );
		}
	}

	public static function reset_assistant() {
		// Deauthorize Bullhorn
		self::bullhorn_deauthorize();

		// Delete the 24-hour Transient on Redirect Checks
		delete_transient( Matador::variable( 'bullhorn_valid_redirect', 'transients' ) );

		// Unset all Bullhorn API Settings
		Matador::$settings->update( array(
			'bullhorn_api_has_authorized'  => null,
			'bullhorn_api_is_connected'    => null,
			'bullhorn_api_assistant'       => null,
			'bullhorn_api_center'          => null,
			'bullhorn_api_client'          => null,
			'bullhorn_api_client_is_valid' => null,
			'bullhorn_api_secret'          => null,
			'bullhorn_api_user'            => null,
			'bullhorn_api_pass'            => null,
		) );
	}

}
