<?php
/**
 * Matador Logger
 *
 * @link        http://matadorjobs.com/
 * @since       1.0.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Bullhorn API
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott, Paul Bearne
 *
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

namespace matador;

/*
 *
 */
final class Logger {

	/**
	 *
	 * 1 = Info. 2 = 3 = 4 = Tell us. 5 = We need to know NOW. ET PHONE HOME!
	 *
	 * @param string $level
	 * @param string $code name of log item
	 * @param string $message
	 *
	 * @return bool
	 */
	public static function add( $level, $code = '', $message = 'An Error Occurred.' ) {

		new Event_Log( $code, $message );

		do_action( 'matador_log', $level, $message, $code );
		__return_true();
	}

	private static function log_to_file() {



	}

	private static function log_to_admin() {

	}

	private static function email_to_admin() {

	}

	private static function email_to_developer() {

	}

}
