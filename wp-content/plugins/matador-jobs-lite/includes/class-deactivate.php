<?php
/**
 * Matador / Deactivator
 *
 * @link        http://matadorjobs.com/
 * @since       1.0.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Core
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott, Paul Bearne
 *
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 */

namespace matador;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Matador_Deactivator {

	/**
	 * Short Description.
	 *
	 * Long description.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'bullhorn_hourly_event' );
		flush_rewrite_rules();
	}

}