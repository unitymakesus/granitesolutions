<?php
/**
 * Scripts
 *
 * @package     Bullpen
 * @subpackage  Functions
 * @copyright   Copyright (c) 2016, Jeremy Scott
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
 */

namespace matador;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Scripts {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'matador_register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'matador_register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'matador_register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'matador_register_admin_scripts' ) );
	}

	/**
	 * Register Styles
	 *
	 * Checks the styles option and hooks the required filter.
	 *
	 * @since 2.1
	 * @return void
	 */
	public static function matador_register_styles() {

		// @to-do, setting so users can turn off css output
		// if ( setting -> css == false )
		// return

		wp_register_style( 'matador-styles', Matador::$path . 'assets/css/matador.css', array(), self::matador_scripts_styles_version(), 'all' );

		wp_enqueue_style( 'matador-styles' );

	}


	/**
	 * Register Scripts
	 *
	 * Checks the styles option and hooks the required filter.
	 *
	 * @since 1.0
	 * @return void
	 * @todo: workout how to know if we want to load
	 */
	public static function matador_register_scripts() {

		if ( is_admin() ) {
			return;
		}

		wp_register_script( 'jquery_validate', Matador::$path . 'assets/scripts/vendor/jquery.validate.min.js', array( 'jquery-core' ), '1.17.0', true );
		wp_register_script( 'jquery_validate_localization', Matador::$path . 'assets/scripts/vendor/jquery.validate.localization.js', array( 'jquery-core', 'jquery_validate' ), '1.17.0', true );

		wp_localize_script( 'jquery_validate_localization', 'jquery_validate_localization', self::jquery_validate_l10n() );

		wp_register_script( 'matador_javascript', Matador::$path . 'assets/scripts/matador.js', array( 'jquery_validate_localization' ), self::matador_scripts_styles_version(), true );
	}


	/**
	 * Add admin scripts and CSS
	 */
	public static function matador_register_admin_styles() {
		wp_register_style( 'matador_admin_styles', Matador::$path . 'assets/css/matador-admin.css', array(), self::matador_scripts_styles_version() );
		wp_enqueue_style( 'matador_admin_styles' );
	}

	/**
	 * Add admin scripts and CSS
	 */
	public static function matador_register_admin_scripts() {
		wp_register_script( 'matador_admin_scripts', Matador::$path . 'assets/scripts/matador-admin.js', array( 'jquery' ), self::matador_scripts_styles_version(), true );
		wp_enqueue_script( 'matador_admin_scripts' );
	}


	/**
	 * Localize jQuery Validate
	 *
	 * Checks the styles option and hooks the required filter.
	 *
	 * @access public
	 * @since 3.4.0
	 *
	 * @return array of localized strings.
	 */
	public static function jquery_validate_l10n() {

		$filtered = array();

		$strings = array(
			'required'    => __( 'This field is required.', 'matador-jobs' ),
			'remote'      => __( 'Please fix this field.', 'matador-jobs' ),
			'email'       => __( 'Please enter a valid email address.', 'matador-jobs' ),
			'url'         => __( 'Please enter a valid URL.', 'matador-jobs' ),
			'date'        => __( 'Please enter a valid date.', 'matador-jobs' ),
			'dateISO'     => __( 'Please enter a valid date (ISO).', 'matador-jobs' ),
			'number'      => __( 'Please enter a valid number.', 'matador-jobs' ),
			'digits'      => __( 'Please enter only digits.', 'matador-jobs' ),
			'equalTo'     => __( 'Please enter the same value again.', 'matador-jobs' ),
			'maxlength'   => __( 'Please enter no more than {0} characters.', 'matador-jobs' ),
			'minlength'   => __( 'Please enter at least {0} characters.', 'matador-jobs' ),
			'rangelength' => __( 'Please enter a value between {0} and {1} characters long.', 'matador-jobs' ),
			'range'       => __( 'Please enter a value between {0} and {1}.', 'matador-jobs' ),
			'max'         => __( 'Please enter a value less than or equal to {0}.', 'matador-jobs' ),
			'min'         => __( 'Please enter a value greater than or equal to {0}.', 'matador-jobs' ),
			'maxsize'     => __( 'A submitted file must not exceed {0} bytes.', 'matador-jobs' ),
			'extension'   => __( 'Please submit a file with a file extension from the list.', 'matador-jobs' ),
		);

		foreach ( $strings as $key => $value ) {
			/**
			 * Dynamic Filter: Matador Application Validation Error {Error}
			 *
			 * Change the string used in the validation to a custom line.
			 *
			 * @since 3.4.0
			 *
			 * @param string
			 * @return string
			 */
			$filtered[ $key ] = apply_filters( "matador_application_validator_error_{ $key }", $value );
		}

		return $filtered;

	}

	public static function matador_scripts_styles_version() {
		if ( defined( 'WP_DEBUG' ) && ( true === WP_DEBUG ) ) {
			return strtotime( 'now' );
		} else {
			return Matador::VERSION;
		}
	}

}
