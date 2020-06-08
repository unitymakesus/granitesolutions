<?php
/**
 * Matador / Bullhorn API / Candidate Submission
 *
 * Extends Bullhorn_Connection and submits candidates for jobs.
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs Board
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott, Paul Bearne
 *
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 */

namespace matador;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use stdClass;

/**
 * This class is an extension of Bullhorn_Connection.  Its purpose
 * is to allow for resume and candidate posting
 *
 * Class Bullhorn_Candidate_Processor
 */
class Bullhorn_Candidate extends Bullhorn_Connection {

	/**
	 * Class Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		parent::__construct();
		try {
			$this->login();
		} catch ( Exception $e ) {

			new Event_Log( $e->getName(), $e->getMessage() );
			Admin_Notices::add( esc_html__( 'Login into Bullhorn failed see log for more info.', 'matador-jobs' ), 'warning', 'bullhorn-login-exception' );
		}
	}

	/**
	 * Find Candidate
	 *
	 * Looks up submitted email address and last name for matching entries
	 * in the candidates database.
	 *
	 * @param string $email
	 * @param string $last_name
	 *
	 * @return integer|boolean
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function find_candidate( $email, $last_name ) {
		if ( ! $email ) {

			return false;
		}

		// API Method
		$method = '/search/Candidate';

		// API Params
		$params = array(
			'count'  => '1',
			'query'  => 'email: "' . Helper::escape_lucene_string( $email )
				. '" AND lastName: "' . Helper::escape_lucene_string( $last_name )
				. '" AND isDeleted:0',
			'fields' => 'id,lastName,email,isDeleted,status',
		);

		$request = $this->request( $method, $params, 'GET' );

		if (
			! is_wp_error( $request )
			&& is_object( $request )
			&& ! isset( $request->errorMessage ) // @codingStandardsIgnoreLine (SnakeCase)
			&& 0 < $request->count
		) {
			if ( ! empty( $request->data[0]->status ) && 'Private' === $request->data[0]->status ) {

				return 'Private';
			}

			return (int) $request->data[0]->id;
		} else {

			return false;
		}
	}

	/**
	 * Search by Email Address
	 *
	 * Looks up submitted email address for matching entries
	 * in the candidates database.
	 *
	 * @param integer $bhid
	 *
	 * @return stdClass|boolean
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_candidate( $bhid = null ) {

		if ( ! is_integer( $bhid ) ) {
			return false;
		}

		// API Method
		$method = 'entity/Candidate/' . $bhid;

		// API Params
		$params = array(
			'fields' => 'id,name,nickName,firstName,middleName,lastName,address,secondaryAddress,email,email2,email3,mobile,phone,phone2,phone3,description,status,dateLastModified',
		);

		// API Request
		$response = $this->request( $method, $params, 'GET' );

		if ( is_object( $response ) && isset( $response->data ) && isset( $response->data->id ) && $response->data->id === $bhid ) {

			$return = new stdClass();

			$return->candidate = $response->data;
		} else {
			$return = false;
		}

		return $return;
	}

	/**
	 * Save Candidate
	 *
	 * @param stdClass $candidate
	 *
	 * @return stdClass|boolean
	 *
	 * @throws Exception
	 * @since 3.0
	 */
	public function save_candidate( $candidate = null ) {

		if ( ! $candidate->candidate || ! is_object( $candidate->candidate ) ) {
			Logger::add( 'error', 'matador-error-bad-candidate data', esc_html__( 'We passed bad data to the save candidate function the data was: ', 'matador-jobs' ) . ' ' . print_r( $candidate, true ) );

			return false;
		}

		// API Method
		if ( isset( $candidate->candidate->id ) ) {
			$method = 'entity/Candidate/' . $candidate->candidate->id;
			// API Request
			$response = $this->request( $method, array(), 'POST', $candidate->candidate );
		} else {
			$method = 'entity/Candidate';
			// API Request
			$response = $this->request( $method, array(), 'PUT', $candidate->candidate );
		}

		if ( is_object( $response ) && isset( $response->changedEntityId ) ) { // @codingStandardsIgnoreLine (SnakeCase)
			$candidate->candidate->id = $response->changedEntityId; // @codingStandardsIgnoreLine (SnakeCase)

			return $candidate;
		} else {

			if ( isset( $candidate->candidate->id ) ) {
				Logger::add( 'error', 'matador-error-updating-candidate', esc_html__( 'We got an error when updating a remote candidate the error was: ', 'matador-jobs' ) . ' ' . print_r( $response, true ) );
			} else {
				Logger::add( 'error', 'matador-error-creating-candidate', esc_html__( 'We got an error when creating a remote candidate the error was: ', 'matador-jobs' ) . ' ' . print_r( $response, true ) );
			}

			return false;
		}

	}

	/**
	 * Save Candidate Education
	 *
	 * @param stdClass|array $candidate
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function save_candidate_education( $candidate = null ) {

		if ( ! $candidate ) {
			return false;
		}

		// API Method
		$method = 'entity/CandidateEducation';

		// API Params
		$params = array();

		// HTTP Action
		$http = 'PUT';

		if ( isset( $candidate->candidateEducation ) && is_array( $candidate->candidateEducation ) ) { // @codingStandardsIgnoreLine (SnakeCase)

			$return = array();

			foreach ( $candidate->candidateEducation as $education ) { // @codingStandardsIgnoreLine (SnakeCase)

				$education->candidate     = new stdClass();
				$education->candidate->id = $candidate->candidate->id;

				// API Call
				$return[] = $this->request( $method, $params, $http, $education );

			}
		}

		return isset( $return ) ? true : false;
	}

	/**
	 * Save Candidate Work History
	 *
	 * @param stdClass|array $candidate
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function save_candidate_work_history( $candidate = null ) {

		if ( empty( $candidate ) ) {
			return false;
		}

		// API Method
		$method = 'entity/CandidateWorkHistory';

		// API Params
		$params = array();

		// HTTP Action
		$http = 'PUT';

		if ( isset( $candidate->candidateWorkHistory ) && is_array( $candidate->candidateWorkHistory ) ) { // @codingStandardsIgnoreLine (SnakeCase)

			// Return Array
			$return = array();

			foreach ( $candidate->candidateWorkHistory as $job ) { // @codingStandardsIgnoreLine (SnakeCase)

				$job->candidate     = new stdClass();
				$job->candidate->id = $candidate->candidate->id;

				// API Call
				$return[] = $this->request( $method, $params, $http, $job );

			}
		}

		return ! empty( $return ) ? true : false;
	}

	/**
	 * Save Candidate primary
	 *
	 * @param stdClass $candidate
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function save_candidate_primary_skills( $candidate = null ) {

		if ( ! $candidate || empty( $candidate->primarySkills ) ) { // @codingStandardsIgnoreLine (SnakeCase)
			return false;
		}

		$bullhorn_skills      = $this->get_skills_list();
		$candidate_skills     = $candidate->primarySkills; // @codingStandardsIgnoreLine (SnakeCase)
		$candidate_skills_ids = array();

		if ( ! empty( $bullhorn_skills ) ) {
			foreach ( $candidate_skills as $skill ) {
				if ( isset( $skill->id ) ) {
					if ( array_key_exists( $skill->id, $bullhorn_skills ) ) {
						$candidate_skills_ids[] = $skill->id;
					}
				} elseif ( isset( $skill->name ) ) {
					$key = array_search( strtolower( $skill->name ), $bullhorn_skills, true );
					if ( $key ) {
						$candidate_skills_ids[] = $key;
					}
				} else {
					$key = array_search( strtolower( $skill ), $bullhorn_skills, true );
					if ( $key ) {
						$candidate_skills_ids[] = $key;
					} elseif ( array_key_exists( $skill, $bullhorn_skills ) ) {
						$candidate_skills_ids[] = $skill;
					} else {
						Logger::add( 'info', 'matador-skill-missing', esc_html__( 'We didn\'t find the id passed to primarySkills', 'matador-jobs' ) . ' - ' . print_r( $skill, true ) );
					}
				}
			}
			$candidate_skills_ids = array_unique( $candidate_skills_ids );
		}

		// API Method
		$method = 'entity/Candidate/' . $candidate->candidate->id . '/primarySkills/' . implode( ',', $candidate_skills_ids );

		// API Params
		$params = array();

		// HTTP Action
		$http = 'PUT';

		// Return Array
		$return = $this->request( $method, $params, $http );

		// Send a Boolean response
		return ! empty( $return ) ? true : false;
	}

	/**
	 * Save Candidate secondary Skills
	 *
	 * @param stdClass $candidate
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function save_candidate_secondary_skills( $candidate = null ) {

		if ( ! $candidate || empty( $candidate->secondarySkills ) ) { // @codingStandardsIgnoreLine (SnakeCase)
			return false;
		}

		$bullhorn_skills      = $this->get_skills_list();
		$candidate_skills     = $candidate->secondarySkills; // @codingStandardsIgnoreLine (SnakeCase)
		$candidate_skills_ids = array();

		if ( ! empty( $bullhorn_skills ) ) {
			foreach ( $candidate_skills as $skill ) {
				$key = array_search( strtolower( $skill ), $bullhorn_skills, true );
				if ( $key ) {
					$candidate_skills_ids[] = $key;
				} elseif ( array_key_exists( $skill, $bullhorn_skills ) ) {
					$candidate_skills_ids[] = $skill;
				} else {
					Logger::add( 'info', 'matador-secondary-skill-missing', esc_html__( 'We didn\'t find the id passed to secondarySkills', 'matador-jobs' ) . ' - ' . $skill );
				}
			}
			$candidate_skills_ids = array_unique( $candidate_skills_ids );
		}

		// API Method
		$method = 'entity/Candidate/' . $candidate->candidate->id . '/secondarySkills/' . implode( ',', $candidate_skills_ids );

		// API Params
		$params = array();

		// HTTP Action
		$http = 'PUT';

		// Return Array
		$return = $this->request( $method, $params, $http );

		// Send a Boolean response
		return ! empty( $return ) ? true : false;
	}

	/**
	 * Save Candidate Categories
	 *
	 * @param stdClass $candidate
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function save_candidate_categories( $candidate = null ) {

		if ( ! $candidate || empty( $candidate->categories ) ) { // @codingStandardsIgnoreLine (SnakeCase)

			return false;
		}

		$bullhorn_categories      = $this->get_categories_list();
		$candidate_categories     = $candidate->categories; // @codingStandardsIgnoreLine (SnakeCase)
		$candidate_categories_ids = array();

		if ( ! empty( $bullhorn_categories ) ) {
			foreach ( $candidate_categories as $category ) {
				$key = array_search( strtolower( $category ), $bullhorn_categories, true );
				if ( $key ) {
					$candidate_categories_ids[] = $key;
				} elseif ( array_key_exists( $category, $bullhorn_categories ) ) {
					$candidate_categories_ids[] = $category;
				} else {
					Logger::add( 'info', 'matador-category-missing', esc_html__( 'We didn\'t find the id passed to categories', 'matador-jobs' ) . ' - ' . $category );
				}
			}
			$candidate_categories_ids = array_unique( $candidate_categories_ids );
		}

		if ( empty( $candidate_categories_ids ) ) {

			return false;
		}

		// API Method
		$method = 'entity/Candidate/' . $candidate->candidate->id . '/categories/' . implode( ',', $candidate_categories_ids );

		// API Params
		$params = array();

		// HTTP Action
		$http = 'PUT';

		// Return Array
		$return = $this->request( $method, $params, $http );

		// Send a Boolean response
		return ! empty( $return ) ? true : false;
	}

	/**
	 * Save Candidate Categories
	 *
	 * @param stdClass $candidate
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function save_candidate_specialties( $candidate = null ) {

		if ( ! $candidate || empty( $candidate->specialties ) ) { // @codingStandardsIgnoreLine (SnakeCase)

			return false;
		}

		$bullhorn_specialties  = $this->get_specialties_list();
		$candidate_specialties = $candidate->specialties; // @codingStandardsIgnoreLine (SnakeCase)

		$candidate_specialties_ids = array();

		if ( ! empty( $bullhorn_specialties ) ) {
			foreach ( $candidate_specialties as $specialty ) {
				$key = array_search( strtolower( $specialty ), $bullhorn_specialties, true );
				if ( $key ) {
					$candidate_specialties_ids[] = $key;
				} elseif ( array_key_exists( $specialty, $bullhorn_specialties ) ) {
					$candidate_specialties_ids[] = $specialty;
				} else {
					Logger::add( 'info', 'matador-specialty-missing', esc_html__( 'We didn\'t find the id passed to specialties', 'matador-jobs' ) . ' - ' . $specialty );
				}
			}
			$candidate_specialties_ids = array_unique( $candidate_specialties_ids );
		}

		if ( empty( $candidate_specialties_ids ) ) {

			return false;
		}
		// API Method
		$method = 'entity/Candidate/' . $candidate->candidate->id . '/specialties/' . implode( ',', $candidate_specialties_ids );

		// API Params
		$params = array();

		// HTTP Action
		$http = 'PUT';

		// Return Array
		$return = $this->request( $method, $params, $http );

		// Send a Boolean response
		return ! empty( $return ) ? true : false;
	}


	/**
	 * Save Candidate Categories
	 *
	 * @param stdClass $candidate
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function save_candidate_business_sectors( $candidate = null ) {

		if ( ! $candidate || empty( $candidate->businessSectors ) ) { // @codingStandardsIgnoreLine (SnakeCase)

			return false;
		}

		$bullhorn_business_sectors  = $this->get_business_sectors_list();
		$candidate_business_sectors = $candidate->businessSectors; // @codingStandardsIgnoreLine (SnakeCase)

		$candidate_business_sectors_ids = array();

		if ( ! empty( $bullhorn_business_sectors ) ) {
			foreach ( $candidate_business_sectors as $sector ) {
				$key = array_search( strtolower( $sector ), $bullhorn_business_sectors, true );
				if ( $key ) {
					$candidate_business_sectors_ids[] = $key;
				} elseif ( array_key_exists( $sector, $bullhorn_business_sectors ) ) {
					$candidate_business_sectors_ids[] = $sector;
				} else {
					Logger::add( 'info', 'matador-business-sectors-missing', esc_html__( 'We didn\'t find the id passed to businessSectors', 'matador-jobs' ) . ' - ' . $sector );
				}
			}
			$candidate_business_sectors_ids = array_unique( $candidate_business_sectors_ids );
		}

		if ( empty( $candidate_business_sectors_ids ) ) {

			return false;
		}
		// API Method
		$method = 'entity/Candidate/' . $candidate->candidate->id . '/businessSectors/' . implode( ',', $candidate_business_sectors_ids );

		// API Params
		$params = array();

		// HTTP Action
		$http = 'PUT';

		// Return Array
		$return = $this->request( $method, $params, $http );

		// Send a Boolean response
		return ! empty( $return ) ? true : false;
	}


	/**
	 * Attach Note to a candidate
	 *
	 * @param stdClass $candidate
	 * @param string   $note
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function save_candidate_note( $candidate = null, $note = null ) {

		if ( ! $candidate && ! $note ) {
			return false;
		}

		$body = array(
			'personReference' => array( 'id' => $candidate->candidate->id ),
			'comments'        => $note,
		);

		// API Method
		$method = 'entity/Note';

		// API Params
		$params = array();

		// Request
		$response = $this->request( $method, $params, 'PUT', $body );

		return $response ? true : false;
	}

	/**
	 * Attach Resume to a candidate.
	 *
	 * @param stdClass $candidate
	 * @param string   $file      path/to/file
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function save_candidate_file( $candidate = null, $file = null ) {

		if ( ! $candidate || ! $file ) {
			return false;
		}

		// API Method
		$method = '/file/Candidate/' . $candidate->candidate->id . '/raw';

		// API Params
		$params = array(
			'externalID' => 'Portfolio', // PER BULLHORN
			'fileType'   => 'SAMPLE', // PER BULLHORN
		);

		// API Request
		$request = $this->request_with_payload( $method, $params, 'PUT', $file );

		if ( ! $request ) {

			return false;
		}

		return $request ? true : false;
	}

	/**
	 * Get Candidate Job Submissions
	 *
	 * For a given Candidate ID, get the history of job submissions for the candidate.
	 *
	 * @access public
	 *
	 * @param int $candidate_id
	 *
	 * @return array|bool
	 *
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function get_candidate_submissions( $candidate_id = null ) {

		if ( empty( $candidate_id ) || ! is_integer( $candidate_id ) ) {

			return false;
		}

		$transient = 'JobSubmission_' . $candidate_id;

		$applications = get_transient( $transient );

		if ( false === $applications ) {

			$method   = 'search/JobSubmission?query=candidate.id:' . $candidate_id;
			$params   = array( 'fields' => 'id,status' );
			$response = $this->request( $method, $params, 'GET' );

			$applications = array();

			foreach ( $response->data as $application ) {

				$applications[ $application->id ] = $application->status;
			}

			set_transient( $transient, $applications, HOUR_IN_SECONDS );
		}

		return $applications;
	}

	/**
	 * Get Job Submission History
	 *
	 * For a given job submission, get the history of actions on the submission.
	 *
	 * @param int $submission_id the ID of the Job Submission
	 *
	 * @return array|bool
	 * @throws Exception
	 * @since 3.5.0
	 *
	 * @todo rename to get_job_submission_history
	 *
	 * @access public
	 */
	public function get_job_application_status( $submission_id = null ) {
		if ( empty( $candidate_id ) || ! is_integer( $submission_id ) ) {

			return false;
		}

		$transient    = 'job_submission_status_' . $submission_id;
		$applications = get_transient( $transient );

		if ( false === $applications ) {

			$method = 'entity/JobSubmissionHistory/' . $submission_id;
			$params = array( 'fields' => 'id,comments,dateAdded,jobSubmission,status,transactionID' );

			$response = $this->request( $method, $params, 'GET' );

			$applications = $response->data;

			set_transient( $transient, $applications, HOUR_IN_SECONDS );
		}

		return $applications;
	}

	/**
	 * Attach Found Candidate to Job
	 *
	 * Looks up submitted email address for matching entries
	 * in the candidates database.
	 *
	 * @param stdClass $candidate
	 * @param integer  $job_id
	 * @param array    $application
	 *
	 * @return array|bool
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function submit_candidate_to_job( $candidate = null, $job_id = null, $application = [] ) {

		if ( ! is_object( $candidate ) && ! is_int( $job_id ) ) {
			return false;
		}

		$status              = 'New Lead';
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

		// API Method
		$method = 'entity/JobSubmission';

		// Request Body
		$body = array(
			'candidate'       => array( 'id' => $candidate->candidate->id ),
			'jobOrder'        => array( 'id' => $job_id ),
			/**
			 * Matador Data Source Description
			 *
			 * Adjusts the text description for the source of the job submission. Default is "{Site Name} Website", ie:
			 * "ACME Staffing Website". Use the $entity argument to narrow the modification to certain entities.
			 *
			 * @return string The modified value for Source. Warning! Limit of 200 characters for Candidates, 100 for JobSubmissions.
			 * @since 3.4.0 added $data parameter
			 * @since 3.5.0 added $submission parameter
			 *
			 * @var string    $source The value for Source. Limit of 200 characters for Candidates, 100 for
			 *                            JobSubmissions. Default is the value of the WordPress "website name" setting.
			 * @var string    $context    Limit scope of filter in filtering function
			 * @var stdClass  $data       The associated data with the $context. Should not be used without $context first.
			 * @var array     $submission The associated data with the $context's submission.
			 *
			 * @since 3.1.1
			 */
			'source'          => substr( apply_filters( 'matador_data_source_description', get_bloginfo( 'name' ), 'submission', $candidate->candidate, $application ), 0, 100 ),
			/**
			 * Matador Data Status Description
			 *
			 * Adjusts the value of the status for the Bullhorn data item. IE: "New Lead"
			 *
			 * @var string    $status     The value of status. Set initially by default or by settings.
			 * @var string    $entity     Limit scope of filter in to an entity
			 * @var stdClass  $data       The associated data with the $context. Should not be used without $context first.
			 * @var array     $submission The associated data with the $context's submission.
			 *
			 * @since 3.5.1
			 */
			'status'          => apply_filters( 'matador_data_source_status', $status, 'submission', $candidate->candidate, $application ),
			'dateWebResponse' => (int) ( microtime( true ) * 1000 ),
		);

		$response = $this->request( $method, array(), 'PUT', $body );

		/**
		 * Action After Submit Candidate to Job
		 *
		 * @var stdClass $candidate a Candidate object
		 * @var int      $job_id
		 * @var stdClass $response from the Bullhorn entity/JobSubmission API call
		 *
		 * @since 3.5.0
		 */
		do_action( 'matador_bullhorn_after_submit_candidate_to_job', $candidate, $job_id, $response );

		return is_object( $response ) ? $response->changedEntityId : false;
	}

	/**
	 * Parse Resume
	 *
	 * Takes an application data array, checks for the file info.
	 *
	 * @param string $file Path to file for resume.
	 * @param string $content Text-based resume content.
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 * @since 3.0.0
	 * @since 3.4.0 added $context parameter
	 *
	 * @access public
	 *
	 */
	public function parse_resume( $file = null, $content = null ) {

		if ( ! $file && ! $content ) {
			throw new Exception( 'warning', 'bullhorn-parse-resume-no-file', esc_html__( 'Parse resume cannot be called without a file.', 'matador-jobs' ) );
		}

		// API Method
		if ( ! $file && $content ) {
			$method = '/resume/parseToCandidateViaJson';
		} else {

			$file_size = filesize( $file ) / pow( 1024, 2 );

			Logger::add( 'info', 'bullhorn-cv-size', __( 'Resume/CV File Size: ', 'matador-jobs' ) . $file_size . 'mb' );

			/**
			 * File size limit for the Bullhorn Resume Parser
			 *
			 * Adjusts the max size that we attempt to send to Bullhorn. Use the matador_variable_accepted_file_size_limit
			 * to set this globally, as this filter only changes what will be rejected prior to Bullhorn submission and
			 * will not prevent submission of a file by a user.
			 *
			 * @since 3.5.0
			 *
			 * @var int File size in Mb. Default is set in Variables
			 */
			$file_size_limit = apply_filters( 'matador_bullhorn_file_size_limit', Matador::variable( 'accepted_file_size_limit' ) );

			if ( $file_size < $file_size_limit ) {

				$method = 'resume/parseToCandidate';

			} else {

				// Translators: 1. Submitted file size in mb. 2. Max allowed file size in mb.
				$error = __( 'Resume/CV file size exceeds Bullhorn limit of %2$smb. Will not submit resume file to Bullhorn for processing.', 'matador-jobs' );

				Logger::add( 'info', 'bullhorn-file-size-exceeds-limit', sprintf( $error, $file_size_limit ) );

				if ( $content ) {
					$method = '/resume/parseToCandidateViaJson';
				} else {
					return false;
				}
			}
		}

		// API Params
		$params = array(
			'populateDescription' => apply_filters( 'matador_bullhorn_candidate_parse_resume_description_format', 'html' ),
		);

		// while ( true ) is ambiguous, but the loop is broken upon a return, which occurs by the fifth cycle.
		while ( true ) {

			$count = isset( $count ) ? ++ $count : 1;

			if ( '/resume/parseToCandidateViaJson' === $method ) {
				$body = array(
					'resume' => $content,
				);

				$request_args = array(
					'headers' => array( 'Content-Type' => 'application/json' ),
				);

				if ( strip_tags( $content ) !== $content ) {
					$params['format'] = 'html';
				} else {
					$params['format'] = 'text';
				}

				$return = $this->request( $method, $params, 'POST', $body, $request_args );

			} else {

				$return = $this->request_with_payload( $method, $params, 'POST', $file );
			}

			if ( isset( $return->errorMessage ) ) {

				if (
					isset( $return->errorMessageKey ) &&
					substr( $return->errorMessageKey, 0, 'errors.resumeParser' ) === 'errors.resumeParser'
				) {
					Logger::add( 'error', 'bullhorn-resume-file-error', print_r( $return->errorMessage, true ) );
				} else {
					Logger::add( 'error', 'bullhorn-resume-error', print_r( $return->errorMessage, true ) );
				}

				return false;
			}

			// Success condition
			if ( ! isset( $return->errorMessage ) ) { // @codingStandardsIgnoreLine (SnakeCase)
				return $return;
			}

			// Try Again Condition
			if ( $count >= 5 ) {
				return array( 'error' => 'attempted-five-and-failed' );
			}
		}

		return false;
	}

	/**
	 * Get Skills List
	 *
	 * Gets all the Skills terms.
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function get_skills_list() {

		$transient = Matador::variable( 'bullhorn_skills_cache', 'transients' );
		$skills    = get_transient( $transient );

		if ( ! $skills ) {
			// Things we need
			$limit      = 300;
			$offset     = isset( $offset ) ? $offset : 0;
			$new_skills = array();

			// API Method
			$method = 'options/Skill';

			// HTTP Action
			$http = 'GET';

			while ( true ) {

				// Return Array
				$request = $this->request( $method, array(
					'count' => $limit,
					'start' => $offset,
				), $http );

				if ( isset( $request->data ) ) {

					foreach ( $request->data as $skill ) {
						$new_skills[ $skill->value ] = strtolower( trim( $skill->label ) );
					}

					if ( count( $request->data ) < $limit ) {
						// If the size of the result is less than the results per page
						// we got all the jobs, so end the loop
						break;
					} else {
						// Otherwise, increment the offset by the results per page, and re-run the loop.
						$offset += $limit;
					}
				} else {

					break;
				}
			}// while
			$skills = array_unique( $new_skills );
			set_transient( $transient, $skills, HOUR_IN_SECONDS * 6 );
		}

		return $skills;
	}


	/**
	 * Get Categories List
	 *
	 * Gets all the Categories terms.
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function get_categories_list() {

		$transient  = Matador::variable( 'bullhorn_categories_cache', 'transients' );
		$categories = get_transient( $transient );

		if ( ! $categories ) {

			// Things we need
			$limit          = 300;
			$offset         = isset( $offset ) ? $offset : 0;
			$new_categories = array();
			// API Method
			$method = 'options/Category';

			// HTTP Action
			$http = 'GET';

			while ( true ) {
				// Return Array
				$request = $this->request( $method, array(
					'count' => $limit,
					'start' => $offset,
				), $http );

				if ( isset( $request->data ) ) {

					foreach ( $request->data as $category ) {
						$new_categories[ $category->value ] = strtolower( trim( $category->label ) );
					}
					if ( count( $request->data ) < $limit ) {
						// If the size of the result is less than the results per page
						// we got all the jobs, so end the loop
						break;
					} else {
						// Otherwise, increment the offset by the results per page, and re-run the loop.
						$offset += $limit;
					}
				} else {

					break;
				}
			}
			$categories = array_unique( $new_categories );
			set_transient( $transient, $categories, HOUR_IN_SECONDS * 6 );
		}

		return $categories;
	}

	/**
	 * Get Categories List
	 *
	 * Gets all the Categories terms.
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function get_specialties_list() {

		$transient   = Matador::variable( 'bullhorn_specialties_cache', 'transients' );
		$specialties = get_transient( $transient );

		if ( ! $specialties ) {
			// Things we need
			$limit           = 300;
			$offset          = isset( $offset ) ? $offset : 0;
			$new_specialties = array();
			// API Method
			$method = 'options/Specialty';

			// HTTP Action
			$http = 'GET';

			while ( true ) {

				// Return Array
				$request = $this->request( $method, array(
					'count' => $limit,
					'start' => $offset,
				), $http );

				if ( isset( $request->data ) ) {

					foreach ( $request->data as $specialty ) {
						$new_specialties[ $specialty->value ] = strtolower( trim( $specialty->label ) );
					}

					if ( count( $request->data ) < $limit ) {
						// If the size of the result is less than the results per page
						// we got all the jobs, so end the loop
						break;
					} else {
						// Otherwise, increment the offset by the results per page, and re-run the loop.
						$offset += $limit;
					}
				} else {

					break;
				}
			}// while
			$specialties = array_unique( $new_specialties );
			set_transient( $transient, $specialties, HOUR_IN_SECONDS * 6 );
		}

		return $specialties;
	}


	/**
	 * Get Categories List
	 *
	 * Gets all the Categories terms.
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 3.5.0
	 */
	public function get_business_sectors_list() {

		$transient        = Matador::variable( 'bullhorn_business_sectors_cache', 'transients' );
		$business_sectors = get_transient( $transient );

		if ( ! $business_sectors ) {
			// Things we need
			$limit                = 300;
			$offset               = isset( $offset ) ? $offset : 0;
			$new_business_sectors = array();

			// API Method
			$method = 'options/BusinessSector';

			// HTTP Action
			$http = 'GET';

			while ( true ) {

				// Return Array
				$request = $this->request( $method, array(
					'count' => $limit,
					'start' => $offset,
				), $http );

				if ( isset( $request->data ) ) {

					foreach ( $request->data as $business_sector ) {
						$new_business_sectors[ $business_sector->value ] = strtolower( trim( $business_sector->label ) );
					}

					if ( count( $request->data ) < $limit ) {
						// If the size of the result is less than the results per page
						// we got all the jobs, so end the loop
						break;
					} else {
						// Otherwise, increment the offset by the results per page, and re-run the loop.
						$offset += $limit;
					}
				} else {
					// we got all the jobs, so end the loop
					break;
				}
			}// while
			$business_sectors = array_unique( $new_business_sectors );
			set_transient( $transient, $business_sectors, HOUR_IN_SECONDS * 6 );
		}

		return $business_sectors;
	}


	/**
	 * @param stdClass $candidate
	 *
	 * @return mixed
	 */
	public function delete_candidate(
		$candidate
	) {
		//TODO: add call to do this in bullhorn
		return $candidate;
	}

}
