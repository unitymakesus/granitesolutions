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
 * @subpackage  Bullhorn API
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott
 *
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

namespace matador;

// since 1.0

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class is an extension of Bullhorn_Connection.  Its purpose
 * is to allow for resume and candidate posting
 *
 * Class Bullhorn_Candidate_Processor
 */
class Bullhorn_Lead extends Bullhorn_Connection {

	/**
	 * Class Constructor
	 *
	 * Since 1.0
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
	 * @return integer|boolean
	 *
	 * @since 1.0.0
	 */
	public function find_lead( $email, $last_name ) {
		if ( ! $email ) {

			return false;
		}

		// API Method
		$method = '/search/Lead';

		// API Params
		$params = array(
			'count' => '1',
		//	'query' => sprintf( 'email: "%s" AND lastName: "%s" AND isDeleted:0', $email, $last_name ),
				'query' => 'isDeleted:1',
//			'fields' => 'id,lastName,email,isDeleted',

			'fields' => '*',
		);

		$request = $this->request( $method, $params, 'GET' );

		if ( ! is_wp_error( $request ) && is_object( $request ) && ! isset( $request->errorMessage ) && 0 < $request->count ) {
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
	 * @param $lead_id
	 *
	 * @return bool|object
	 * @internal param int $bhid
	 * @since 1.0.0
	 */
	public function get_lead( $lead_id ) {


		// API Method
		$method = 'entity/Lead/' . $lead_id;

		// API Params
		$params = array(
			'fields'=> '*'
//			'fields' => 'id,name,nickName,firstName,middleName,lastName,address,secondaryAddress,email,email2,email3,mobile,phone,phone2,phone3,description,status,dateLastModified',
		);

		// API Request
		$response = $this->request( $method, $params, 'GET' );

		if ( is_object( $response ) && isset( $response->data ) ) {
			$return = new \stdClass;
			$return->data = $response->data;
		} else {
			$return = false;
		}

		return $return;

	}

	public function get_settings_user_id() {

		if( is_null( $this->url ) ) {

			return false;
		}
		$tran_key = 'matador_default_user_id';
		$matador_default_user_id = get_transient( $tran_key );

		if( false !== $matador_default_user_id ){

			return $matador_default_user_id;
		}

		// API Method
		$method = 'settings/userId';

		// API Params
		$params = array();

		// API Request
		$response = $this->request( $method, $params, 'GET' );

		if ( is_object( $response ) && isset( $response->userId ) ) {
			$return = absint( $response->userId );
			set_transient( $tran_key, $return , HOUR_IN_SECONDS );
		} else {
			$return = false;
		}

		return $return;

	}

	/**
	 * Check is the current account has leads enabled
	 * returns false if not enabled
	 *
	 * @return bool|null
	 */
	public function is_lead_enabled() {

		if( ! $this->is_authorized() ){

			return null;
		}

		$tran_key = 'matador_is_lead_enabled';
		$matador_is_lead_enabled = get_transient( $tran_key );

		if( false !== $matador_is_lead_enabled ){

			return (bool) $matador_is_lead_enabled;
		}

		// API Method
		$method = 'settings/leadAndOpportunityEnabled';

		// API Params
		$params = array();

		// API Request
		$response = $this->request( $method, $params, 'GET' );

		if ( is_object( $response ) && isset( $response->leadAndOpportunityEnabled ) ) {
			$return = $response->leadAndOpportunityEnabled;
			// if leads are enabled then check once a day otherwise recheck in 10 minutes as might get tuned on
			$tran_expiration = ( $response->leadAndOpportunityEnabled ) ? DAY_IN_SECONDS : MINUTE_IN_SECONDS * 10;
			set_transient( $tran_key, $return , $tran_expiration );
		} else {
			$return = false;
		}

		return $return;

	}

	/*
	 * gets all the settings for an account
	 *
	 */
	public function list_settings() {
		// API Method
		$method = 'settings';

		// API Params
		$params = array();

		// API Request
		$response = $this->request( $method, $params, 'GET' );
		if ( is_object( $response ) && isset( $response->data ) ) {
			$return = new \stdClass;
			$return->data = $response->data;
		} else {
			$return = false;
		}

		return $return;

	}


	/**
	 * Save Lead
	 *
	 * @param object $candidate
	 *
	 * @return object|boolean
	 * Since 1.0
	 */
	public function save_lead( $info = null ) {

		if( is_null( $this->url ) ) {

			return false;
		}
		$owner = new \stdClass();
		$owner->id =  self::get_settings_user_id();

		$default = [
			'owner' => $owner,
			/**
			 * Matador Lead Status
			 *
			 * @since unknown
			 *
			 * @var string
			 *
			 * @todo rename and deprecate filter. Should use underscores for naming convention.
			 */
			'status' => apply_filters( 'matador-lead-status', 'New Lead', $info ),
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
			 * @var string   $entity     Limit scope of filter in filtering function
			 * @var stdClass $data.      The associated data with the $context. Should not be used without $context first.
			 * @var array    $submission The associated data with the $context's submission.
			 *
			 * @return string The modified value for Source. Warning! Limit of 200 characters for Candidates, 100 for JobSubmissions & Leads.
			 */
			'leadSource' => substr( apply_filters( 'matador_data_source_description', get_bloginfo( 'name' ) . ' Website', 'lead', null, $info ), 0, 100 ),
			/**
			 * Matador Lead Comment Format
			 *
			 * @since unknown
			 *
			 * @var string
			 *
			 * @todo rename and deprecate filter. Should use underscores for naming convention.
			 */
			'comments'   => sprintf( apply_filters( 'matador-lead-comment-format', __( 'This is a lead from %1$s Website', 'matador-jobs' ), $info ), get_bloginfo('blogname' ) ),
		];
		$data = array_merge($info,$default);

		if( isset( $info['description'] ) ) {
			$data['comments'] .= PHP_EOL . ' Message: ' . esc_html( $info['description'] );
		}

		// API Method
		if ( isset( $data->id ) ) {
			$method = 'entity/Lead/' . $data->id;
		} else {
			$method = 'entity/Lead';
		}

		// API Request
		$response = $this->request( $method, array(), 'PUT', $data );

		if ( is_object( $response ) && isset( $response->changedEntityId ) ) {
			$response->id = $response->changedEntityId;
			return $response;
		} else {
			return false;
		}

	}


	/**
	 * Attach Note to a lead
	 *
	 * @param object $candidate
	 *
	 * @return mixed response body or false
	 */
	public function save_lead_note( $lead = null, $note = null ) {

		if ( ! $lead && ! $note ) {
			return false;
		}

		// @todo make sure the notes are like this:
		//
		$body = array(
			'personReference' => array( 'id' => $lead->id ),
			'comments' => $note,
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
	 * @param object $lead
	 *
	 * @return bool
	 */
	public function delete_lead( $lead ) {

		return false;
	}

}
