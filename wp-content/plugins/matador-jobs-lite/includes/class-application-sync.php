<?php
/**
 * Matador Submit Candidate
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Core
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott, Paul Bearne
 *
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 */

namespace matador;

use stdClass;
use DateTime;

class Application_Sync {

	/**
	 * Application ID
	 *
	 * ID of the WordPress Application Custom Post Type post.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	private $application_id;

	/**
	 * Application Data
	 *
	 * The application data object from the application.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $application_data;

	/**
	 * Application Sync Status
	 *
	 * The status of the application status.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private $candidate_sync_status;

	/**
	 * Application Sync Step
	 *
	 * The step of the application sync for the current application status
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private $candidate_sync_step;

	/**
	 * Candidate Bullhorn ID
	 *
	 * The ID of the Bullhorn Candidate Entity
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $candidate_bhid;

	/**
	 * Candidate Data
	 *
	 * The candidate data object from Bullhorn or created by this class
	 *
	 * @since 3.0.0
	 *
	 * @var stdClass
	 */
	private $candidate_data;

	/**
	 * Candidate Resume
	 *
	 * The parsed resume object returned from Bullhorn's resume parser
	 *
	 * @since 3.0.0
	 *
	 * @var stdClass
	 */
	private $candidate_resume;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 3.0.0
	 *
	 * @param int $application_id
	 */
	public function __construct( $application_id = null ) {
		if ( null !== $application_id && is_int( $application_id ) && get_post_status( intval( $application_id ) ) ) {
			$this->sync( $application_id );
		}
	}

	/**
	 * Sync Application
	 *
	 * Begins the sync of the application.
	 *
	 * @access public
	 * @since 3.0.0
	 *
	 * @param int $application_id
	 *
	 * @return bool
	 */
	public function sync( $application_id ) {

		$this->application_id  = $application_id;
		$application_post_data = get_post_meta( $this->application_id, Matador::variable( 'application_data' ), true );

		if ( false !== $application_post_data && ! empty( $application_post_data ) ) {
			$this->application_data = (array) $application_post_data;
		} else {
			update_post_meta( $application_id, Matador::variable( 'candidate_sync_status' ), '3' );
			return false;
		}

		$this->candidate_sync_status = (array) get_post_meta( $this->application_id, Matador::variable( 'candidate_sync_status' ), true );
		$this->candidate_sync_step   = (array) get_post_meta( $this->application_id, Matador::variable( 'candidate_sync_step' ), true );

		return $this->add_bullhorn_candidate();
	}

	/**
	 * Clear Too Many Values
	 *
	 * @access private
	 * @static
	 *
	 * @param array     $application
	 * @param \stdClass $candidate
	 * @param bool      $update
	 *
	 * @return \stdClass $candidate
	 */
	private static function clear_too_many_values( $application, stdClass $candidate, $update = false ) {
		// Skill List
		// @todo should not exist in core
		if ( isset( $application['skillList'] ) && ! empty( $application['skillList'] ) ) {

			foreach ( $application['skillList'] as $key => $value ) {

				$application['skillList'][ $key ] = esc_html( $value );
			}

			if ( ! isset( $candidate->skillList ) || ! is_array( $candidate->skillList ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				$candidate->skillList = $application['skillList']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			} else {
				$candidate->skillList = array_merge( $candidate->skillList, $application['skillList'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		}
		unset( $candidate->candidate->skillList );

		// Categories List
		// @todo should not exist in core
		if ( isset( $application['categories'] ) && ! empty( $application['categories'] ) ) {

			foreach ( $application['categories'] as $key => $value ) {

				$application['categories'][ $key ] = esc_html( $value );
			}

			if ( ! isset( $candidate->categories ) || ! is_array( $candidate->categories ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName

				$candidate->categories = $application['categories']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			} else {
				$candidate->categories = array_merge( $candidate->categories, $application['categories'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}

			if ( ! $update && ! empty( $candidate->categories ) && ! isset( $candidate->candidate->category ) ) {
				$candidate->candidate->category     = new stdClass();
				$candidate->candidate->category->id = array_shift( $candidate->categories );
			}
		}
		unset( $candidate->candidate->categories );

		// businessSectors List
		// @todo should not exist in core
		if ( isset( $application['businessSectors'] ) && ! empty( $application['businessSectors'] ) ) {

			foreach ( $application['businessSectors'] as $key => $value ) {

				$application['businessSectors'][ $key ] = esc_html( $value );
			}

			if ( ! isset( $candidate->businessSectors ) || ! is_array( $candidate->businessSectors ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				$candidate->categories = $application['businessSectors']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			} else {
				$candidate->businessSectors = array_merge( $candidate->businessSectors, $application['businessSectors'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		}
		unset( $candidate->candidate->businessSectors );

		// specialties List
		// @todo should not exist in core
		if ( isset( $application['specialties'] ) && ! empty( $application['specialties'] ) ) {

			foreach ( $application['specialties'] as $key => $value ) {

				$application['specialties'][ $key ] = esc_html( $value );
			}

			if ( ! isset( $candidate->specialties ) || ! is_array( $candidate->specialties ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				$candidate->specialties = $application['specialties']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			} else {
				$candidate->specialties = array_merge( $candidate->specialties, $application['specialties'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		}
		unset( $candidate->candidate->specialties );

		// primarySkills List
		// @todo should not exist in core
		if ( isset( $application['primarySkills'] ) && ! empty( $application['primarySkills'] ) ) {

			foreach ( $application['primarySkills'] as $key => $value ) {

				$application['primarySkills'][ $key ] = esc_html( $value );
			}

			if ( ! isset( $candidate->primarySkills ) || ! is_array( $candidate->primarySkills ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				$candidate->primarySkills = $application['primarySkills']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			} else {
				$candidate->primarySkills = array_merge( $candidate->primarySkills, $application['primarySkills'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		}
		unset( $candidate->candidate->primarySkills );

		// secondarySkills List
		// @todo should not exist in core
		if ( isset( $application['secondarySkills'] ) && ! empty( $application['secondarySkills'] ) ) {

			foreach ( $application['secondarySkills'] as $key => $value ) {

				$application['secondarySkills'][ $key ] = esc_html( $value );
			}

			if ( ! isset( $candidate->secondarySkills ) || ! is_array( $candidate->secondarySkills ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				$candidate->secondarySkills = $application['secondarySkills']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			} else {
				$candidate->secondarySkills = array_merge( $candidate->secondarySkills, $application['secondarySkills'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		}
		unset( $candidate->candidate->secondarySkills );

		return $candidate;
	}

	/**
	 * Add Candidate from Resume
	 *
	 * This acts as an alternate constructor. Takes a file path to a resume/cv and
	 * sets up an alternate instance of the class to then run a candidate sync/submit routine.
	 *
	 * @param string $file_path
	 * @param int    $bhid
	 *
	 * @return stdClass
	 * @todo: this function needs to be removed after jobboard is refactored to have its applicant/candidate
	 * @todo: post type work like Matador's and WPJM's
	 *
	 * @access public
	 * @since  3.5.0
	 *
	 */
	public function add_candidate_from_cv( $file_path, $bhid = null ) {
		$application['files']['resume']['path'] = $file_path;

		$this->application_data = (array) $application;

		if ( null !== $bhid ) {
			$this->candidate_bhid = $bhid;
		}

		$this->add_bullhorn_candidate();

		return $this->candidate_data;
	}

	/**
	 * Add Bullhorn Candidate
	 *
	 * Takes a candidate application and creates and submits a Bullhorn candidate
	 *
	 * @access public
	 * @since 3.0.0
	 */
	public function add_bullhorn_candidate() {

		add_action( 'matador_log', array( $this, 'add_to_log' ), 10, 2 );

		try {

			Logger::add( 'info', 'matador-app-sync-start', esc_html__( 'Starting application sync for local candidate', 'matador-jobs' ) . ' ' . $this->application_id );

			$bullhorn = new Bullhorn_Candidate();

			// Create Resume Object from Application Data
			$this->candidate_sync_step = 'get-resume';
			$this->candidate_resume    = self::create_resume( $bullhorn, $this->application_data );

			$this->candidate_sync_step = 'check-can-sync';
			if ( self::can_application_sync() ) {

				/**
				 * Filter : Matador Submit Candidate Check for Existing
				 *
				 * True/false filter to check if the Submit Candidate process should check for an existing candidate or
				 * not, which could result in duplicate candidates.
				 *
				 * @since 3.0.0
				 *
				 * @param string|bool
				 */
				if ( ! empty( $this->candidate_sync_step ) && apply_filters( 'matador_submit_candidate_check_for_existing', Matador::setting( 'applications_sync_check_for_existing' ) ) ) {
					// Check if this candidate already exists, get its Bullhorn Candidate ID
					if ( isset( $this->application_data['email'] ) && isset( $this->application_data['name']['lastName'] ) ) {
						$this->candidate_bhid = $bullhorn->find_candidate( $this->application_data['email'], $this->application_data['name']['lastName'] );
					}
				}

				if ( ! empty( $this->candidate_bhid ) ) {

					if ( 'Private' === $this->candidate_bhid ) {

						$this->candidate_sync_step   = null;
						$this->candidate_sync_status = '6';
						$this->save_data();
						Logger::add( 'info', 'matador-app-sync-private_candidate', esc_html__( 'We found a candidate in bullhorn but they where marked as private.', 'matador-jobs' ) . ' ' . $this->application_id );
						Email::admin_error_notification( esc_html__( 'We found a candidate in bullhorn but they where marked as private. So could not update then in Bullhorn.', 'matador-jobs' ) . get_edit_post_link( $this->application_id, 'email' ) );

					} elseif ( is_int( $this->candidate_bhid ) ) {

						// Start Updating Existing Candidate
						$this->candidate_sync_step = 'existing-start';
						Logger::add( 'info', 'matador-app-existing-found', esc_html__( 'Found and updating existing remote candidate', 'matador-jobs' ) . ' ' . $this->candidate_bhid );

						// Fetch Existing Candidate Data
						$this->candidate_sync_step = 'existing-fetch';
						$this->candidate_data      = $bullhorn->get_candidate( $this->candidate_bhid );

						// Update Candidate from Submitted Data
						$this->candidate_sync_step = 'existing-update';
						$this->candidate_data      = self::update_candidate( $this->candidate_data, $this->application_data, $this->candidate_resume );

						// Add Last Updated IP Address to Candidate
						$this->candidate_data = self::candidate_ip( $this->candidate_data, $this->application_data );
						$this->candidate_data = self::candidate_privacy_policy( $this->candidate_data, $this->application_id );

						// remove Last Modified from data returned data
						unset( $this->candidate_data->candidate->dateLastModified );

						// Save Updated Candidate
						$this->candidate_sync_step = 'existing-candidate-save';

						$success = $bullhorn->save_candidate( $this->candidate_data );

						if ( false === $success ) {
							if ( $this->candidate_data ) {
								Logger::add( 'info', 'update-candidate-failed-but-recovered', esc_html__( 'An existing remote candidate was not updated due to a previously logged error. Will continue without update.', 'matador-jobs' ) );
							} else {
								throw new Exception( 'error', 'update-candidate-failed', esc_html__( 'An existing remote candidate update failed and Matador is unable to continue.', 'matador-jobs' ) );
							}
						}

						$this->candidate_sync_step = 'existing-candidate-complete';

					} else {
						throw new Exception( 'error', 'update-candidate-failed', esc_html__( 'Candidate update failed because a non-integer and unexpected value was returned.', 'matador-jobs' ) );
					}
				} else {

					// Start Creating a Candidate
					$this->candidate_sync_step = 'creating-start';
					Logger::add( 'info', 'matador-app-existing-not-found', esc_html__( 'An existing remote candidate was not found. Will create a new remote candidate', 'matador-jobs' ) );

					// Create Candidate Object from Resume and Application Data
					$this->candidate_sync_step = 'creating-candidate-object';
					$this->candidate_data      = self::create_candidate( $this->candidate_resume, $this->application_data );

					// Bullhorn Bug Fix
					if ( isset( $this->candidate_data->candidate->nameSuffix ) && ! empty( $this->candidate_data->candidate->nameSuffix ) ) {
						unset( $this->candidate_data->candidate->nameSuffix );
					}

					// Add Last Updated IP Address to Candidate
					$this->candidate_data = self::candidate_ip( $this->candidate_data, $this->application_data, true );
					$this->candidate_data = self::candidate_privacy_policy( $this->candidate_data, $this->application_id, true );

					// Save Candidate to Bullhorn
					$this->candidate_sync_step = 'creating-candidate-save';

					$success = $bullhorn->save_candidate( $this->candidate_data );

					if ( false === $success ) {
						throw new Exception( 'error', 'create-candaite', esc_html__( 'Failed to create candidate', 'matador-jobs' ) );
					} else {
						$this->candidate_data = $success;
					}

					if ( isset( $this->candidate_data->id ) ) {

						Logger::add( 'info', 'matador-app-new-created', esc_html__( 'A new remote candidate was created as', 'matador-jobs' ) . ' ' . $this->candidate_data->id );
					}

					$this->candidate_sync_step = 'creating-candidate-complete';
				}

				// Add/update Candidate Education
				$this->candidate_sync_step = 'creating-candidate-education';
				$bullhorn->save_candidate_education( $this->candidate_data );

				// Add/update Candidate Work History
				$this->candidate_sync_step = 'creating-candidate-work-history';
				$bullhorn->save_candidate_work_history( $this->candidate_data );

				$this->candidate_sync_step = 'creating-candidate-categories';
				$bullhorn->save_candidate_categories( $this->candidate_data );

				// Add/update Candidate Skills
				$this->candidate_sync_step = 'creating-candidate-primary_skills';
				$bullhorn->save_candidate_primary_skills( $this->candidate_data );

				// Add/update Candidate Categories
				$this->candidate_sync_step = 'creating-candidate-secondary_skills';
				$bullhorn->save_candidate_secondary_skills( $this->candidate_data );

				// Add/update Candidate Categories
				$this->candidate_sync_step = 'creating-candidate-specialties';
				$bullhorn->save_candidate_specialties( $this->candidate_data );

				// Add/update Candidate Categories
				$this->candidate_sync_step = 'creating-candidate-business_sectors';
				$bullhorn->save_candidate_business_sectors( $this->candidate_data );

				// Save Message, if any
				$this->candidate_sync_step = 'save-message';
				if ( isset( $this->application_data['message'] ) ) {

					$bullhorn->save_candidate_note( $this->candidate_data, $this->application_data['message'] );
				}

				// Save Files, if any
				$this->candidate_sync_step = 'save-files';
				$this->save_candidate_files( $this->candidate_data, $this->application_data, $bullhorn );

				// Save Jobs Applied To, if any
				$this->candidate_sync_step = 'save-jobs';
				$this->save_candidate_jobs( $this->candidate_data, $this->application_data, $bullhorn );

				// Do Custom Actions
				$this->candidate_sync_step = 'do-custom-actions';
				do_action( 'matador_bullhorn_candidate', $this->application_id, $this->application_data, $this->candidate_data, $this->candidate_resume, $bullhorn );

				// If we got this far, clear the steps, update the status, and save the meta
				$this->candidate_sync_step   = null;
				$this->candidate_sync_status = '1';
				$this->save_data();
				Logger::add( 'info', 'matador-app-sync-complete', esc_html__( 'Completed application sync for local candidate', 'matador-jobs' ) . ' ' . $this->application_id );

				if ( Matador::setting( 'application_delete_local_on_sync' ) ) {
					Logger::add( 'info', 'matador-app-sync-complete', esc_html__( 'Now will delete local candidate due to Privacy and Data Storage settings. Removing local candidate ', 'matador-jobs' ) . ' ' . $this->application_id );
					wp_delete_post( $this->application_id, true );
				}
			} else {

				$this->candidate_sync_step   = null;
				$this->candidate_sync_status = '5';
				$this->save_data();
				Logger::add( 'info', 'matador-app-sync-insufficient', esc_html__( 'Application cannot sync due to too little data.', 'matador-jobs' ) . ' ' . $this->application_id );

			}
		} catch ( Exception $e ) {

			$this->candidate_sync_status = '3'; // @todo: This status is not clear. Can we determine if the failure is recoverable?
			$this->save_data();
			Logger::add( 'error', $e->getName(), $e->getMessage() );
			Logger::add(
				'info',
				'matador-app-sync-fail',
				esc_html( sprintf(
					// Translators: Placeholder 1 is for the WPID of the Application.
					__(
						'Application sync failed for local candidate %1$s. Data must be manually submitted to Bullhorn.',
						'matador-jobs'
					),
					$this->application_id
				) )
			);
		}

		remove_action( 'matador_log', array( $this, 'add_to_log' ), 10 );
		return true;
	}

	/**
	 * Save Application Data
	 *
	 * Updates the Application post type post with updated post meta from this process.
	 *
	 * @access private
	 * @since 3.0.0
	 */
	private function save_data() {
		foreach ( array( 'application_data', 'candidate_bhid', 'candidate_data', 'candidate_resume', 'candidate_sync_status', 'candidate_sync_step' ) as $saveable ) {
			if ( isset( $this->{$saveable} ) && ! empty( $this->{$saveable} ) ) {
				update_post_meta( $this->application_id, Matador::variable( $saveable ), $this->{$saveable} );
			} else {
				delete_post_meta( $this->application_id, Matador::variable( $saveable ) );
			}
		}
	}

	/**
	 * Can Application Sync
	 *
	 * An application needs at least a last name and email. If a resume exists, check that the resume
	 * has those two fields. If not, we'll check if that information was submitted with the form.
	 *
	 * @access private
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	private function can_application_sync() {
		// Check the Resume
		if (
			! empty( $this->candidate_resume )
			&& ! empty( $this->candidate_resume->candidate->lastName )
			&& ! empty( $this->candidate_resume->candidate->email )
		) {
			return true;
		}
		// Check the Application
		if (
			! empty( $this->application_data )
			&& ( ! empty( $this->application_data['name'] ) || ! empty( $this->application_data['lastName'] ) )
			&& ! empty( $this->application_data['email'] )
		) {
			return true;
		}
		// Got this far, the answer is no.
		return false;
	}

	/**
	 * Save Candidate Files
	 *
	 * Saves all the files in an Application array to Bullhorn.
	 *
	 * @access private
	 * @since 3.0.0
	 *
	 * @param stdClass $candidate
	 * @param array $application
	 * @param Bullhorn_Candidate $bullhorn
	 *
	 * @throws Exception
	 *
	 * @todo handle Exception
	 */
	private function save_candidate_files( $candidate, $application, $bullhorn ) {
		if ( isset( $application['files'] ) ) {
			foreach ( $application['files'] as $index => $file ) {
				if ( ! empty( $file['path'] ) && empty( $file['synced'] ) ) {
					if ( $bullhorn->save_candidate_file( $candidate, $file['path'] ) ) {
						$this->application_data['files'][ $index ]['synced'] = 1;
					}
				}
			}
		}
	}

	/**
	 * Submit Candidate to Jobs
	 *
	 * Loops through all jobs in the Application
	 *
	 * @access private
	 * @since 3.0.0
	 *
	 * @param stdClass $candidate
	 * @param array $application
	 * @param Bullhorn_Candidate $bullhorn
	 *
	 * @throws Exception
	 *
	 * @todo handle Exception
	 */
	private function save_candidate_jobs( $candidate, $application, $bullhorn ) {
		if ( isset( $application['jobs'] ) ) {
			foreach ( $application['jobs'] as $key => $job ) {
				if ( isset( $job['bhid'] ) && is_numeric( $job['bhid'] ) && ! empty( $job['synced'] ) ) {
					$success = $bullhorn->submit_candidate_to_job( $candidate, (int) $job['bhid'], $this->application_data );
					if ( false !== $success ) {
						Logger::add( 'info', 'matador-app-sync-application_linked', esc_html__( 'Linked candidate to an application with the Bullhorn ID of', 'matador-jobs' ) . ' ' . $job['bhid'] );
						$application['jobs'][ $key ]['synced'] = 1;
					}
				}
			}
		} elseif ( Matador::setting( 'applications_backup_job' ) ) {
			$success = $bullhorn->submit_candidate_to_job( $candidate, (int) Matador::setting( 'applications_backup_job' ), $this->application_data );
			if ( false !== $success ) {
				Logger::add( 'info', 'matador-app-sync-application_linked', esc_html__( 'Linked candidate to Default application with Bullhorn ID', 'matador-jobs' ) . ' ' . Matador::setting( 'applications_backup_job' ) );
			}
		}
	}

	/**
	 * Create Resume
	 *
	 * Sends a file to Bullhorn for resume parsing, ideally returning a parsed JSON object.
	 *
	 * @access public
	 * @since 3.0.0
	 *
	 * @param Bullhorn_Candidate $bullhorn
	 * @param array $application
	 *
	 * @return stdClass
	 *
	 * @throws Exception
	 *
	 * @todo Handle Exception
	 */
	public static function create_resume( $bullhorn, $application ) {

		$resume = false;

		if ( Matador::setting( 'bullhorn_process_resumes' ) ) {

			if ( ! empty( $application['files']['resume'] ) && is_array( $application['files']['resume'] ) ) {

				$file = $application['files']['resume'];

				if ( ! empty( $file['path'] ) ) {

					$resume = $bullhorn->parse_resume( $file['path'] );

				}
			}

			if ( ! $resume && ! empty( $application['resume'] ) ) {
				$resume = $bullhorn->parse_resume( null, $application['resume'] );
			}

			if ( ! $resume ) {
				$text_resume = apply_filters( 'matador_submit_candidate_text_resume', '', $application );
				if ( ! empty( $reumse ) ) {
					$resume = $bullhorn->parse_resume( null, $text_resume );
				}
			}

			if ( ! is_object( $resume ) || ! $resume ) {
				Logger::add( 'error', 'bullhorn-application-processing-error', __( 'Error on resume process from Bullhorn: ', 'matador-jobs' ) . $resume['error'] );
				$resume = false;
			}
		}

		return $resume;
	}

	/**
	 * Create Candidate
	 *
	 * Creates a candidate object from the resume results (if any) and the application data
	 *
	 * @access public
	 * @since 3.0.0
	 *
	 * @param stdClass $resume
	 * @param array $application
	 *
	 * @return stdClass|bool
	 */
	public static function create_candidate( $resume = null, $application = null ) {

		if ( ! is_array( $application ) ) {
			return false;
		}

		$candidate = ! empty( $resume ) ? $resume : new stdClass();

		$candidate->candidate = ! empty( $candidate->candidate ) ? $candidate->candidate : new stdClass(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->candidateWorkHistory = ! empty( $candidate->candidateWorkHistory ) ? $candidate->candidateWorkHistory : array(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->candidateEducation = ! empty( $candidate->candidateEducation ) ? $candidate->candidateEducation : array();  // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->skillList = ! empty( $candidate->skillList ) ? $candidate->skillList : array(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->categories = ! empty( $candidate->categories ) ? $candidate->categories : array(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->businessSectors = ! empty( $candidate->businessSectors ) ? $candidate->businessSectors : array(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->specialties = ! empty( $candidate->specialties ) ? $candidate->specialties : array(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->primarySkills = ! empty( $candidate->primarySkills ) ? $candidate->primarySkills : array(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->secondarySkills = ! empty( $candidate->secondarySkills ) ? $candidate->secondarySkills : array(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		$candidate->candidate = self::candidate_name( $candidate->candidate, $application );

		$candidate->candidate = self::candidate_email( $candidate->candidate, $application );

		$candidate->candidate = self::candidate_phone( $candidate->candidate, $application );

		$candidate->candidate = self::candidate_address( $candidate->candidate, $application );

		$candidate->candidate = self::candidate_comments( $candidate->candidate, $application );

		unset( $candidate->candidate->editHistoryValue ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		unset( $candidate->candidate->smsOptIn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName

		/**
		 * Matador Data Source Description
		 *
		 * Adjusts the text description for the source of the job submission. Default is "{Site Name} Website", ie:
		 * "ACME Staffing Website". Use the $entity argument to narrow the modification to certain entities.
		 *
		 * @since 3.1.1
		 * @since 3.4.0 added $data parameter
		 * @since 3.5.0 added $submission parameter
		 *
		 * @var string   $source     The value for Source. Limit of 200 characters for Candidates, 100 for
		 *                           JobSubmissions. Default is the value of the WordPress "website name" setting.
		 * @var string   $context    Limit scope of filter in filtering function
		 * @var stdClass $data.      The associated data with the $context. Should not be used without $context first.
		 * @var array    $submission The associated data with the $context's submission.
		 *
		 * @return string The modified value for Source. Warning! Limit of 200 characters for Candidates, 100 for JobSubmissions.
		 */
		$candidate->candidate->source = substr( apply_filters( 'matador_data_source_description', get_bloginfo( 'name' ), 'candidate', $candidate->candidate, $application ), 0, 200 );

		$status = 'New Lead';

		$mark_application_as = Matador::setting( 'bullhorn_mark_application_as' );
		if ( ! empty( $mark_application_as ) ) {
			switch ( $mark_application_as ) {
				case 'submitted':
					$status = 'Submitted';
					break;
				case 'lead':
				default:
					$status = 'New Lead';
					break;
			}
		}
		/**
		 * Matador Data Status Description
		 *
		 * Adjusts the value of the status for the Bullhorn data item. IE: "New Lead"
		 *
		 * @since 3.5.1
		 *
		 * @var string    $status     The value of status. Set initially by default or by settings.
		 * @var string    $entity     Limit scope of filter in to an entity
		 * @var \stdClass $data.      The associated data with the $context. Should not be used without $context first.
		 * @var array     $submission The associated data with the $context's submission.
		 *
		 * @return string             The filtered value of status.
		 */
		$candidate->candidate->status = apply_filters( 'matador_data_source_status', $status, 'candidate', $candidate->candidate, $application );

		/**
		 * Matador Submit Candidate Candidate Data Filter
		 *
		 * Modify the Candidate Object following parsing.
		 *
		 * @since 3.4.0
		 *
		 * @param stdClass $candidate
		 * @param array $application
		 * @param string $action 'create' or 'update' if you want to limit to certain changes
		 */
		$candidate->candidate = apply_filters( 'matador_submit_candidate_candidate_data', $candidate->candidate, $application, 'create' );

		$candidate = self::clear_too_many_values( $application, $candidate );

		return $candidate;
	}

	/**
	 * Update Candidate
	 *
	 * Updates the retrieved Candidate object with information from the application.
	 *
	 * @access public
	 * @since 3.0.0
	 *
	 * @param stdClass $candidate
	 * @param array $application
	 * @param stdClass $resume
	 *
	 * @return stdClass
	 */
	private static function update_candidate( $candidate = null, $application = null, $resume = null ) {

		if ( ! $candidate || ! $application || ! is_array( $application ) ) {
			return $candidate;
		}

		$candidate->candidate = self::candidate_name( $candidate->candidate, $application );
		$candidate->candidate = self::candidate_email( $candidate->candidate, $application );
		$candidate->candidate = self::candidate_phone( $candidate->candidate, $application );
		$candidate->candidate = self::candidate_address( $candidate->candidate, $application );
		$candidate->candidate = self::candidate_comments( $candidate->candidate, $application );

		if ( $resume && isset( $resume->candidate->description ) && ! empty( $resume->candidate->description ) ) {
			$candidate->candidate->description = $resume->candidate->description;
		}

		/**
		 * Matador Submit Candidate Candidate Data Filter
		 *
		 * Modify the Candidate Object following parsing.
		 *
		 * @since 3.4.0
		 *
		 * @param stdClass $candidate
		 * @param array $application
		 * @param string $action 'create' or 'update' if you want to limit to certain changes
		 */
		$candidate->candidate = apply_filters( 'matador_submit_candidate_candidate_data', $candidate->candidate, $application, 'update' );

		$candidate = self::clear_too_many_values( $application, $candidate, true );

		return $candidate;
	}

	/**
	 * Candidate Name
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 *
	 * @param stdClass $person
	 * @param array $application
	 *
	 * @return stdClass
	 */
	private static function candidate_name( $person = null, $application = null ) {
		if ( $person && is_array( $application ) ) {

			if ( isset( $application['name'] ) && ! empty( $application['name'] ) ) {

				$name = $application['name'];

				if ( isset( $name['namePrefix'] ) ) {

					$person->namePrefix = $name['namePrefix']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				}
				if ( isset( $name['firstName'] ) ) {

					$person->firstName = $name['firstName']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				}
				if ( isset( $name['lastName'] ) ) {

					$person->lastName = $name['lastName']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				}
				if ( isset( $name['middleName'] ) ) {
					$person->middleName = $name['middleName']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				}
				if ( isset( $name['suffix'] ) ) {
					$person->nameSuffix = $name['suffix']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				}
				if ( isset( $name['fullName'] ) ) {
					$person->name = $name['fullName'];
				}
			}
		}

		return $person;
	}

	/**
	 * Candidate Email
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 * @since 3.5.2 Check that the value returned from sanitize is valid
	 *
	 * @param stdClass $person
	 * @param array $application
	 *
	 * @return stdClass
	 */
	private static function candidate_email( $person = null, $application = null ) {

		if ( ! $person ) {

			return $person;
		}

		if ( empty( $application ) || ! is_array( $application ) ) {

			return $person;
		}

		if ( empty( $application['email'] ) || ! is_string( $application['email'] ) ) {

			return $person;
		}

		$proposed = sanitize_email( $application['email'] );

		if ( empty( $proposed ) ) {

			return $person;
		}

		$existing1 = isset( $person->email ) ? sanitize_email( $person->email ) : null;
		$existing2 = isset( $person->email2 ) ? sanitize_email( $person->email2 ) : null;

		if ( $existing1 && $existing2 ) {
			if ( $existing2 === $proposed ) {
				$person->email2 = $person->email;
				$person->email  = $proposed;
			} elseif ( $existing1 !== $proposed ) {
				$person->email3 = $person->email2;
				$person->email2 = $person->email;
				$person->email  = $proposed;
			}
		} else {
			if ( $existing1 && $existing1 !== $proposed ) {
				$person->email2 = $person->email;
				$person->email  = $proposed;
			} else {
				$person->email = $proposed;
			}
		}

		return $person;
	}

	/**
	 * Candidate Phone
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 *
	 * @param stdClass $person
	 * @param array $application
	 *
	 * @return stdClass
	 */
	private static function candidate_phone( $person = null, $application = null ) {

		if ( $person && is_array( $application ) ) {

			if ( isset( $application['phone'] ) && ! empty( $application['phone'] ) ) {

				$existing1 = isset( $person->phone ) ? preg_replace( '~\D~', '', $person->phone ) : null;
				$existing2 = isset( $person->phone2 ) ? preg_replace( '~\D~', '', $person->phone2 ) : null;
				$proposed  = preg_replace( '~\D~', '', $application['phone'] );

				if ( $existing1 && $existing2 ) {
					if ( $existing2 === $proposed ) {
						$person->phone2 = $person->phone;
						$person->phone  = esc_attr( $application['phone'] );
					} elseif ( $existing1 !== $proposed ) {
						$person->phone3 = $person->phone2;
						$person->phone2 = $person->phone;
						$person->phone  = esc_attr( $application['phone'] );
					}
				} else {
					if ( $existing1 && $existing1 !== $proposed ) {
						$person->phone2 = $person->phone;
						$person->phone  = esc_attr( $application['phone'] );
					} else {
						$person->phone = esc_attr( $application['phone'] );
					}
				}
			}

			if ( isset( $application['work_phone'] ) ) {

				$person->workPhone = esc_attr( $application['work_phone'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}

			if ( isset( $application['mobile_phone'] ) ) {

				$person->mobile = esc_attr( $application['mobile_phone'] );
			}
		}

		return $person;
	}

	/**
	 * Candidate Address
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 *
	 * @param stdClass $person
	 * @param array $application
	 *
	 * @return stdClass
	 */
	private static function candidate_address( $person, $application ) {
		if ( $person && is_array( $application ) ) {

			// Address fields and field limits
			$address_fields = array(
				// field => character limit
				'address1'    => 40,
				'address2'    => 40,
				'city'        => 40,
				'state'       => 30,
				'zip'         => 15,
				'countryName' => 99,
			);

			// Checks if a user inputted address field exists, and sets our test to true.
			foreach ( $address_fields as $key => $unused ) {
				if ( isset( $application[ $key ] ) && ! empty( $application[ $key ] ) ) {
					$application_has_address = true;
				}
			}

			// We have user inputted address
			if ( isset( $application_has_address ) ) {

				if ( isset( $person->address ) && ! empty( $person->address ) ) {

					$person->secondaryAddress = $person->address; // phpcs:ignore WordPress.NamingConventions.ValidVariableName

				} else {

					$person->address            = new stdClass();
					$person->address->countryID = 1;

				}

				foreach ( $address_fields as $key => $length ) {
					if ( isset( $application[ $key ] ) && ! empty( $application[ $key ] ) ) {
						$person->address->{$key} = $application[ $key ];
					} else {
						unset( $person->address->{$key} );
					}
				}
			}

			// Truncate address values.
			foreach ( $address_fields as $key => $length ) {
				if ( isset( $person->address->$key ) ) {
					$person->address->$key = substr( $person->address->$key, 0, $length );
				}
				if ( isset( $person->secondaryAddress->$key ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
					$person->secondaryAddress->$key = substr( $person->secondaryAddress->$key, 0, $length ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				}
			}

			// Countries MUST have a countryID.
			// 0 will literally break Bullhorn!
			// Null won't process.
			if ( empty( $person->address->countryID ) ) {
				if ( ! isset( $person->address ) || ! is_object( $person->address ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
					$person->address = new stdClass();
				}
				$person->address->countryID = 1;
			}
			if ( isset( $person->secondaryAddress ) && empty( $person->secondaryAddress->countryID ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				if ( ! isset( $person->secondaryAddress ) || ! is_object( $person->secondaryAddress ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
					$person->secondaryAddress = new stdClass(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName
				}
				$person->secondaryAddress->countryID = 1; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
			}
		}

		return $person;
	}

	/**
	 * Candidate Comments
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 *
	 * @param stdClass $person
	 * @param array $application
	 *
	 * @return stdClass
	 */
	private static function candidate_comments( $person = null, $application = null ) {
		if ( $person && $application ) {

			$comments = '';

			if ( isset( $application['message'] ) && ! empty( $application['message'] ) ) {

				/**
				 * Matador Submit Candidate Notes Message Prefix
				 *
				 * Modify the label for the candidate message that prepends it before being saved as a note.
				 *
				 * @since 3.4.0
				 *
				 * @param string $label the text that comes before the "Message" field on a form response.
				 */
				$label = apply_filters( 'matador_submit_candidate_notes_message_label', __( 'Message: ', 'matador-jobs' ) );

				$comments .= esc_html( PHP_EOL . $label . $application['message'] );
			}

			if ( isset( $application['job']['title'] ) && ! empty( $application['job']['title'] ) ) {

				/**
				 * Matador Submit Candidate Notes Message Prefix
				 *
				 * Modify the label for the candidate jobs that prepends it before being saved as a note.
				 *
				 * @since 3.4.0
				 *
				 * @param string $label the text that comes before the "Job" field on a form response.
				 */
				$label = apply_filters( 'matador_submit_candidate_notes_job_label', __( 'Applied via the website for this position: ', 'matador-jobs' ) );

				$value = $application['job']['title'];

				if ( isset( $application['job']['bhid'] ) && is_numeric( $application['job']['bhid'] ) ) {
					$value .= sprintf( ' (Bullhorn Job ID: %s)', $application['job']['bhid'] );
				}

				$comments .= esc_html( $label . $value . PHP_EOL );
			}

			if ( ! empty( $comments ) ) {

				if ( isset( $person->comments ) && ! empty( $person->comments ) ) {

					$person->comments .= $comments;

				} else {

					$person->comments = $comments;

				}
			}
		}

		return $person;
	}

	/**
	 * Add to Application Sync Log
	 *
	 * @access public
	 * @since 3.0.0
	 *
	 * @param string $level
	 * @param string $message
	 */
	public function add_to_log( $level, $message ) {

		unset( $level ); // until PHPCS 3.4+

		if ( null === $this->application_id ) {
			$this->application_id = get_the_ID() ?: intval( $_GET['sync'] );
		}

		$log = get_post_meta( $this->application_id, Matador::variable( 'candidate_sync_log' ), true );

		$now = new DateTime();

		$append = PHP_EOL . $now->format( 'Y-m-d H:i:s: ' ) . $message;

		$updated = $log . $append;

		update_post_meta( $this->application_id, Matador::variable( 'candidate_sync_log' ), $updated );
	}

	/**
	 * Save Candidate Submission IP Address Field
	 *
	 * @access private
	 * @since 3.4.0
	 *
	 * @param stdClass $candidate
	 * @param array $application
	 * @param bool $new
	 *
	 * @return stdClass $candidate
	 */
	private function candidate_ip( $candidate, $application, $new = false ) {

		/**
		 * Filter: Save User IP Fields
		 *
		 * Defines whether the Submit Candidate process should skip tracking of user IP of responses.
		 *
		 * @since 3.4.0
		 *
		 * @param bool
		 */
		if ( ! apply_filters( 'matador_submit_candidate_save_ip_fields', true ) ) {
			return $candidate;
		}

		/**
		 * Filter: User IP field on create.
		 *
		 * Which field to save initial user ip address to.
		 *
		 * @since 3.4.0
		 *
		 * @param bool|string
		 */
		$on_create = apply_filters( 'matador_submit_candidate_ip_field_on_create', false );

		/**
		 * Filter: User IP field on update.
		 *
		 * Which field to save user ip address to on updates.
		 *
		 * @since 3.4.0
		 *
		 * @param bool|string
		 */
		$on_update = apply_filters( 'matador_submit_candidate_ip_field_on_update', false );

		if ( $new && is_string( $on_create ) ) {
			$candidate->candidate->$on_create = $application['ip'];
		}

		if ( is_string( $on_update ) ) {
			$candidate->candidate->$on_update = $application['ip'];
		}

		return $candidate;
	}

	/**
	 * Save Candidate Privacy Policy Record Fields
	 *
	 * Allows a site to add data to the candidate around their acceptance of the Privacy Policy field in a Matador
	 * Application. Users can set a customTextXX or customDateX field. If they use a customTextXX field, they can
	 * specify date format using a filter.
	 *
	 * @access private
	 * @since 3.4.0
	 *
	 * @param stdClass $candidate
	 * @param int $app_id
	 * @param bool $new
	 *
	 * @return stdClass $candidate
	 */
	private function candidate_privacy_policy( $candidate, $app_id, $new = false ) {

		if ( ! Matador::setting( 'application_privacy_field' ) ) {

			return $candidate;
		}

		/**
		 * Filter: Save Privacy Policy Fields
		 *
		 * Defines whether the Submit Candidate process should skip simple tracking of Privacy Policy responses.
		 *
		 * @since 3.4.0
		 *
		 * @param bool
		 *
		 * @return bool
		 */
		if ( ! apply_filters( 'matador_submit_candidate_save_privacy_policy_fields', true ) ) {

			return $candidate;
		}

		/**
		 * Filter: Privacy Policy date field format
		 *
		 * When a customTextXX field is provided, what should the DateTime string be set to. Use PHP date() formats. If
		 * a customDateXX field is provided, the date is formatted per the settings.
		 * @see http://php.net/manual/en/function.date.php
		 *
		 * @since 3.4.0
		 *
		 * @param string
		 *
		 * @return string
		 */
		$format = apply_filters( 'matador_submit_candidate_privacy_policy_field_format', 'c' );

		/**
		 * Filter: Privacy Policy date field on create.
		 *
		 * Which field to save initial privacy policy date/time to.
		 *
		 * @since 3.4.0
		 *
		 * @param bool|string
		 *
		 * @return string
		 */
		$on_create = apply_filters( 'matador_submit_candidate_privacy_policy_field_on_create', false );

		if ( $new && is_string( $on_create ) ) {
			if ( strpos( $on_create, 'customDate' ) !== false ) {
				// Send Epoch Microtime to a Bullhorn customDate
				$value_create = get_post_time( 'u', true, $app_id );
			} else {
				$value_create = get_post_time( $format, false, $app_id );
			}

			/**
			 * Filter: Privacy Policy Field value on create.
			 *
			 * Modify the value for the privacy policy field on candidate create. Warning, if you are sending a
			 * customDateXXX you must return a DateTime string formatted in microtime.
			 *
			 * @since 3.5.0
			 *
			 * @param DateTime|string $value The value of the field.
			 * @param string          $field The name of the field.
			 *
			 * @return string
			 */
			$value_create = apply_filters( 'matador_submit_candidate_privacy_policy_field_value_on_create', $value_create, $on_create );

			/**
			 * Filter: Privacy Policy Field value
			 *
			 * Modify the value for the privacy policy field. Warning, if you are sending a customDateXXX you must
			 * return a DateTime string formatted in microtime.
			 *
			 * @since 3.5.0
			 *
			 * @param DateTime|string $value The value of the field.
			 * @param string          $field The name of the field.
			 *
			 * @return string
			 */
			$candidate->candidate->$on_create = apply_filters( 'matador_submit_candidate_privacy_policy_field_value', $value_create, $on_create );
		}

		/**
		 * Filter: Privacy Policy date field on update
		 *
		 * Which field to save privacy policy date/time on candidate update to.
		 *
		 * @since 3.4.0
		 *
		 * @param bool|string
		 *
		 * @return string
		 */
		$on_update = apply_filters( 'matador_submit_candidate_privacy_policy_field_on_update', false );

		if ( is_string( $on_update ) ) {
			if ( strpos( $on_update, 'customDate' ) !== false ) {
				// Send Epoch Microtime to a Bullhorn customDate
				$value_update = get_post_time( 'u', true, $app_id );
			} else {
				$value_update = get_post_time( $format, false, $app_id );
			}

			/**
			 * Filter: Privacy Policy Field value on update.
			 *
			 * Modify the value for the privacy policy field on candidate update. Warning, if you are sending a
			 * customDateXXX you must return a DateTime string formatted in microtime.
			 *
			 * @since 3.5.0
			 *
			 * @param DateTime|string $value The value of the field.
			 * @param string          $field The name of the field.
			 *
			 * @return string
			 */
			$value_update = apply_filters( 'matador_submit_candidate_privacy_policy_field_value_on_update', $value_update, $on_update );

			/**
			 * Filter: Privacy Policy Field value
			 *
			 * Modify the value for the privacy policy field. Warning, if you are sending a customDateXXX you must
			 * return a DateTime string formatted in microtime.
			 *
			 * @since 3.5.0
			 *
			 * @param DateTime|string $value The value of the field.
			 * @param string          $field The name of the field.
			 *
			 * @return string
			 */
			$candidate->candidate->$on_update = apply_filters( 'matador_submit_candidate_privacy_policy_field_value', $value_update, $on_update );
		}

		return $candidate;
	}
}
