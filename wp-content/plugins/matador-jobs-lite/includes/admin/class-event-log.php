<?php
/**
 * Matador / Admin Event Log
 *
 * This powers the Matador Logger
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

final class Event_Log {

	/**
	 * Variable: File
	 *
	 * Stores the file name of the log file
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Variable: Now
	 *
	 * Stores the time now
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $now;

	/**
	 * Variable: Pointer
	 *
	 * Keeps the position in the current file
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var resource
	 */
	private $pointer;

	/**
	 * Class Constructor
	 *
	 * Instantiates the class.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var string $code
	 * @var string $message
	 *
	 */
	public function __construct( $code = null, $message = '' ) {
		if ( $this->is_enabled() ) {
			if ( null === $code ) {
				$this->delete_logs();
			} else {
				$this->now  = current_time( 'mysql' );
				$this->file = Matador::variable( 'log_file_path' ) . current_time( 'Y-m-d' ) . '-matador-log-' . $this->get_hash() . '.txt';
				$this->write( $code, $message );
			}
		}
	}

	/**
	 * Write File
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $code
	 * @param null $message
	 *
	 * @return void
	 */
	private function write( $code = 'matador-meta-code-not-passed', $message = null ) {

		$message = empty( $message ) ? esc_html__( 'Unknown Error.', 'matador-jobs' ) : $message;

		/**
		 * Action: Matador Event Log Before Write
		 *
		 * Action fires before an Event Log Write is completed.
		 *
		 * @since 3.5.0
		 *
		 * @param string $code
		 * @param string $message
		 */
		do_action( 'matador_event_log_before_write', $code, $message );

		$this->open();

		$line = sprintf( '%1$s: %2$s (%3$s)', $this->now, $message, $code ) . "\n";

		fwrite( $this->pointer, $line ); // @codingStandardsIgnoreLine

		$this->close();
	}

	/**
	 * Open File
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function open() {
		$this->pointer = fopen( $this->file, 'a+' ); // @codingStandardsIgnoreLine
	}

	/**
	 * Close File
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function close() {
		fclose( $this->pointer ); // @codingStandardsIgnoreLine
	}

	/**
	 * Is Enabled
	 *
	 * Checks that logging is enabled in the plugin settings.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @return bool
	 */
	private function is_enabled() {
		return (bool) ( 0 < Matador::setting( 'logging' ) );
	}

	/**
	 * Get Hash
	 *
	 * Appends a hash to the file names to obfuscate
	 * log file names for security purposes.
	 *
	 * @return string
	 */
	private function get_hash() {

		return md5( Matador::variable( 'log_file_path' ) );
	}

	/**
	 * List Logs
	 *
	 * @since 3.0.0
	 *
	 * @access static
	 *
	 * @return string
	 */
	public static function list_logs() {
		$files    = array_reverse( glob( Matador::variable( 'log_file_path' ) . '*-matador-log*.txt' ) );
		$out      = '';
		$base_url = Matador::variable( 'uploads_base_url' );

		foreach ( $files as $file ) {
			$fullpath = explode( wp_normalize_path( DIRECTORY_SEPARATOR ), $file );
			$filename = end( $fullpath );
			$out     .= sprintf( '<br /><a href="%1$s" target="_blank">%2$s</a>',
				esc_url( $base_url . $filename ),
				$filename
			);
		}

		if ( absint( Matador::setting( 'logging' ) ) < count( $files ) || '-1' === Matador::setting( 'logging' ) ) {
			// delete old files
			new Event_Log( null );
		}

		return $out;
	}

	/**
	 * Delete Log Files
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	public function delete_logs() {

		$files       = glob( Matador::variable( 'log_file_path' ) . '*-matador-log*.txt' );
		$date_offset = Matador::setting( 'logging' );

		foreach ( $files as $file ) {
			$fullpath = explode( DIRECTORY_SEPARATOR, $file );
			$filename = end( $fullpath );
			$date     = str_replace( '-matador-log.txt', '', str_replace( '-matador-log-' . $this->get_hash() . '.txt', '', $filename ) );

			if ( strtotime( $date ) ) {
				$current_date      = new \DateTime( $date );
				$date_file_created = new \DateTime( $this->now );
				$age_of_file       = $current_date->diff( $date_file_created );

				if ( absint( $date_offset ) < $age_of_file->days && file_exists( $file ) ) {
					unlink( $file );
					new Event_Log( 'matador-logger-delete-old-log', __( 'Expired log removed. File: ', 'matador-jobs' ) . $file );
				}
			} else {
				unlink( $file );
				new Event_Log( 'matador-logger-delete-bad-date-log', __( 'Bad hashed log file removed. File: ', 'matador-jobs' ) . $file );
			}
		}
	}
}