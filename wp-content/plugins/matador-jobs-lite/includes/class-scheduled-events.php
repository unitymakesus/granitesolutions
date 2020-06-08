<?php
/**
 * Matador / Scheduled Events
 *
 * This sets up the scheduled events using the WP Cron.
 *
 * @link        http://matadorjobs.com/
 * @since       1.0.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Core
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott
 *
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

namespace matador;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Matador Scheduled Events Class
 *
 * This class handles scheduled events
 *
 * @since 1.0.0
 * @return void
 */
class Scheduled_Events {

	/**
	 * Default Recurrence Setting
	 */
	private static $recurrence = 'hourly';

	/**
	 * Register the Crons
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'schedule_application_sync' ) );
		add_action( 'init', array( __CLASS__, 'schedule_job_sync' ) );
		add_action( 'matador_job_sync', array( __CLASS__, 'jobs_sync' ) );
		add_action( 'matador_job_sync_now', array( __CLASS__, 'jobs_sync' ) );
		add_action( 'matador_application_sync', array( __CLASS__, 'application_sync' ), 10, 2 );
		add_action( 'matador_application_sync_now', array( __CLASS__, 'application_sync' ), 10, 2 );
	}

	/**
	 * Schedules the events
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function schedule_application_sync() {
		$recurrence = apply_filters( 'matador_scheduled_event_recurrence__all', self::$recurrence );
		$recurrence = apply_filters( 'matador_scheduled_event_recurrence_application_sync', $recurrence );
		$recurrence = self::validate_recurrence( $recurrence );
		if ( 0 !== (int) Matador::setting( 'applications_sync' ) ) {
			if ( false === wp_next_scheduled( 'matador_application_sync' ) ) {
				wp_schedule_event( current_time( 'timestamp' ), $recurrence, 'matador_application_sync' );
			}
		} else {
			$scheduled = wp_next_scheduled( 'matador_application_sync' );
			if ( false !== $scheduled ) {
				wp_unschedule_event( $scheduled, $recurrence, 'matador_application_sync' );
			}
		}
	}

	/**
	 * Hourly events
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	public static function schedule_job_sync() {
		$recurrence = apply_filters( 'matador_scheduled_event_recurrence__all', self::$recurrence );
		$recurrence = apply_filters( 'matador_scheduled_event_recurrence_job_sync', $recurrence );
		$recurrence = self::validate_recurrence( $recurrence );
		if ( false === wp_next_scheduled( 'matador_job_sync' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), $recurrence, 'matador_job_sync' );
		}
	}

	/**
	 *
	 * @param string $method
	 */
	public static function jobs_sync( $method = 'auto' ) {

		if ( 'manual' === $method ) {
			set_transient( Matador::variable( 'doing_sync', 'transients' ), true, 5 * MINUTE_IN_SECONDS );
			new Event_Log( 'jobs_sync_start_manual', __( 'Manual Sync Starting', 'matador-jobs' ) );
		} elseif ( (bool) Matador::setting( 'bullhorn_auto_sync' ) ) {
			new Event_Log( 'jobs_sync_start_auto', __( 'Automatic Sync Starting', 'matador-jobs' ) );
		} else {
			new Event_Log( 'jobs_sync_skip', __( 'An scheduled Automatic sync was set to start, but "Automatically Sync Jobs" is set to off, so it was not run.', 'matador-jobs' ) );
			return;
		}

		// check we have credentials before calling
		$credentials = get_option( 'bullhorn_api_credentials', array() );

		if ( ! empty( $credentials ) ) {
			$bullhorn = new Bullhorn_Import();
			$sync     = $bullhorn->sync();
			if ( true !== $sync ) {
				Admin_Notices::add( __( 'Bullhorn sync jobs failed', 'matador-jobs' ), 'bullhorn-sync-fail' );
			} else {
				Admin_Notices::remove( 'bullhorn-sync-fail' );
			}
		} else {
			new Event_Log( 'Error with sync. API Credentials do not exist.', print_r( $credentials ) );
		}
	}

	/**
	 *
	 *
	 */
	public static function application_sync( $id = null, $last = null ) {

		if ( ! get_transient( Matador::variable( 'doing_app_sync', 'transients' ) ) ) {
			set_transient( Matador::variable( 'doing_app_sync', 'transients' ), true, 5 * MINUTE_IN_SECONDS );
		}

		if ( ! $id ) {
			new Event_Log( 'application_sync_start_cron', __( 'Batch Application Sync Start', 'matador-jobs' ) );
			$application_query = array(
				'post_type'      => Matador::variable( 'post_type_key_application' ),
				'posts_per_page' => 1,
				'meta_query'     => array(
					array(
						'key'     => Matador::variable( 'candidate_sync_status' ),
						'compare' => 'IN',
						/**
						 * Filter: Application Batch Sync Statuses
						 *
						 * Allows you to extend the statuses found in an application batch sync.
						 *
						 * @since 3.3.7
						 *
						 * @param array
						 */
						'value'   => apply_filters( 'matador_application_batch_sync_allowed_statuses', array( '-1', '2', '3' ) ),
					),
				),
				'nopaging' => true,
				'date_query' => array(
					array(
						'inclusive' => false,
						/**
						 * Filter: Application Batch Sync Duration
						 *
						 * Allows you to set the time limit for applications to sync in a batch. Default is two weeks.
						 * requires a strtotime() valid string. IE: '2 Weeks Ago'.
						 *
						 * @since 3.3.7
						 *
						 * @param string
						 */
						'after'  => apply_filters( 'matador_application_batch_sync_allowed_duration', '2 weeks ago' ),
					),
				),
			);

			if ( $last ) {
				$application_query['date_query'][0]['before'] = $last;
			}

			$application = new \WP_Query( $application_query );

			if ( $application->have_posts() && ! is_wp_error( $application ) ) {
				$id = $application->posts[0]->ID;
				$last = $application->posts[0]->post_date;
			} else {
				delete_transient( Matador::variable( 'doing_app_sync', 'transients' ) );
				new Event_Log( 'application_sync_none', __( 'No Applications found to sync', 'matador-jobs' ) );
				return;
			}
		} else {
			new Event_Log( 'application_sync_start_manual', __( 'Manual Application Sync Start', 'matador-jobs' ) );
		}
		new Event_Log( 'bullhorn-application_sync', esc_html( sprintf( __( 'Batch Application Sync found an local application ', 'matador-jobs' ) . $id ) ) );
		new Application_Sync( $id );
		// create another cron task to run now do upload another application if needed
		wp_schedule_single_event( time(), 'matador_application_sync_now', array( null, $last ) );
	}

	private static function validate_recurrence( $reccurrence ) {
		$valid_recurrences = wp_get_schedules();
		if ( array_key_exists( strtolower( $reccurrence ), $valid_recurrences ) ) {
			return strtolower( $reccurrence );
		} else {
			return self::$recurrence;
		}
	}
}
