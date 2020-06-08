<?php
/**
 * Matador / Admin Notices
 *
 * Manages admin notices.
 *
 * @link        http://matadorjobs.com/
 * @since       1.0.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Admin
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott, Paul Bearne
 *
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 */

namespace matador;

if ( ! defined( 'ABSPATH' ) || class_exists( 'Admin_Notices' ) ) {
	exit;
}

class Admin_Notices {

	/*
	 *
	 */
	private static $transient_key = 'matador_admin_notices';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   3.0.0 pre
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * @param $message string
	 * @param $type string
	 * @param $name string
	 */
	public static function add( $message, $type = 'info', $name = null ) {

		$possible_types = array( 'success', 'info', 'warning', 'error' );
		if ( ! in_array( $type, $possible_types, true ) ) {
			$type = 'info';
		}

		$notices = get_transient( self::$transient_key ) ?: array();

		if ( self::is_not_duplicate( $notices, $name ) ) {
			$notices[] = array(
				'message' => $message,
				'type'    => $type,
				'name'    => $name,
			);
			Logger::add( $type, $name, $message );
		}

		set_transient( self::$transient_key, $notices );
	}

	/**
	 * @param $name
	 */
	public static function remove( $name ) {

		// Notify the Admin of Successful Save
		$notices = get_transient( self::$transient_key ) ?: array();
		if ( ! empty( $notices ) ) {
			foreach ( $notices as $key => $notice ) {
				if ( array_key_exists( 'name', $notice ) && $name === $notice['name'] ) {
					unset( $notices[ $key ] );
				}
			}
			if ( ! empty( $notices ) ) {
				set_transient( self::$transient_key, $notices );
			} else {
				delete_transient( self::$transient_key );
			}
		}
	}

	/**
	 * Admin Notices
	 *
	 * @since   3.0.0
	 */
	public function admin_notices() {
		$matador_admin_notices = get_transient( self::$transient_key );

		if ( get_transient( Matador::variable( 'doing_sync', 'transients' ) ) ) {
			printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
				esc_attr( 'info' ),
				wp_kses_post( __( 'Matador is currently syncing jobs and applications. Reload your admin page for updates.', 'matador-jobs' ) )
			);
		}
		if ( get_transient( Matador::variable( 'doing_app_sync', 'transients' ) ) ) {
			printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
				esc_attr( 'info' ),
				wp_kses_post( __( 'Matador is currently syncing applications. Reload your application page for updates.', 'matador-jobs' ) )
			);
		}

		if ( $matador_admin_notices ) {
			foreach ( $matador_admin_notices as $notice ) {
				printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
					esc_attr( $notice['type'] ),
					wp_kses_post( $notice['message'] )
				);
			}
			delete_transient( self::$transient_key );
		}
	}

	private static function is_not_duplicate( $notices = array(), $name = null ) {

		if ( ! $name || empty( $notices ) ) {
			return true;
		}

		foreach ( $notices as $notice ) {
			if ( array_key_exists( 'name', $notice ) && $name === $notice['name'] ) {
				return false;
			}
		}

		return true;
	}
}
