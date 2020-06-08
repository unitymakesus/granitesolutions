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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bullhorn_Connection {

	/**
	 * Property: Logged In
	 *
	 * Stores a boolean with the result of the login attempt.
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	public $logged_in = false;

	/**
	 * Property: API Credentials
	 *
	 * Stores the authorized API credentials we use to log into Bullhorn.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $credentials;

	/**
	 * Property: Session
	 *
	 * Stores the session ID we use to make subsequent requests to Bullhorn.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	protected $session;

	/**
	 * Property: URL
	 *
	 * Nicely holds the formatted URL we make requests to.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	protected $url;

	/**
	 * Constructor
	 *
	 * Class constructor sets up some variables.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// This creates a "variable" in the variables array for us that isn't otherwise defined.
		add_filter( 'matador_variable_bullhorn_api_credentials_key', array( __CLASS__, 'define_credentials_key' ) );

		// Fetch API Credentials
		$this->get_credentials();
	}

	/**
	 * Define Credentials Key
	 *
	 * Allows us to use a filter to set a variable to the Matador::$variable object without
	 * having it pre-set in the variables defaults for security reasons.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public static function define_credentials_key() {

		return 'bullhorn_api_credentials';
	}

	/**
	 * Get Regional Datacenter URL
	 *
	 * Checks if a setting exists for a Bullhorn Data Center,
	 * then translates the setting into the URL fron for the
	 * associated regional datacenter.
	 *
	 * @since 3.0.0
	 * @since 3.5.0 updated datacenters
	 * @return string
	 */
	private function get_data_center() {
		switch ( Matador::setting( 'bullhorn_api_center' ) ) {
			case 'apac':
				$url = 'https://rest-apac.bullhornstaffing.com/';
				break;
			case 'eur-uk':
				$url = 'https://rest-emea.bullhornstaffing.com/';
				break;
			case 'eur-ger':
				$url = 'https://rest-ger.bullhornstaffing.com/';
				break;
			case 'west-usa':
			case 'w50-usa': // until USA 50 is fixed
				$url = 'https://rest-west.bullhornstaffing.com/';
				break;
			// case 'w50-usa':
			//	$url = 'https://rest-west50.bullhornstaffing.com/';
			//	break;
			case 'atl-usa':
				$url = 'https://rest-east.bullhornstaffing.com/';
				break;
			case 'east-usa':
			default:
				$url = 'https://rest.bullhornstaffing.com/';
				break;
		}
		return esc_url( apply_filters( 'matador_bullhorn_data_center_url', $url, Matador::setting( 'bullhorn_api_center' ) ) );
	}

	/**
	 * Get API Credentials
	 *
	 * Gets the stored API Credentials from the WordPress
	 * database and assigns it to the variable.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function get_credentials() {
		$credentials = get_option( Matador::variable( 'bullhorn_api_credentials_key' ), array() );
		if ( is_array( $credentials )
			&& array_key_exists( 'refresh_token', $credentials )
			&& array_key_exists( 'access_token', $credentials ) ) {

			$this->credentials = $credentials;
		} else {

			$this->credentials = array();
		}
	}

	/**
	 * Get API Credential
	 *
	 * Checks if a credential exists and returns it.
	 *
	 * @since 3.0.0
	 *
	 * @param (string) $key the name of the setting.
	 *
	 * @return string|null
	 */
	private function get_credential( $key ) {
		if ( array_key_exists( $key, $this->credentials ) ) {

			return $this->credentials[ $key ];
		} else {

			return null;
		}
	}

	/**
	 * Update API Credentials
	 *
	 * Takes an array of credentials, ads an expiry time,
	 * and updates the class variable and database option.
	 *
	 * The credentials request gives a value 'expires_in'
	 * time in seconds for the access token. Take a timestamp
	 * of the  time right now and add the expiration in second,
	 * then, to be safe, subtract 30 seconds to make sure all
	 * our future requests are made with plenty of time to spare.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials array of credentials
	 *
	 * @return void
	 */
	private function update_credentials( $credentials = array() ) {
		if ( ! empty( $credentials ) ) {

			// Validate all four expected values came through the authorization request
			foreach ( array( 'token_type', 'access_token', 'expires_in', 'refresh_token' ) as $key ) {
				if ( ! array_key_exists( $key, $credentials ) ) {
					Logger::add( 'critical', 'bullhorn-update-credentials-invalid-data', __( 'Invalid credentials were provided to credentials update.', 'matador-jobs' ) );
					return;
				}
			}

			// Validate the token_type is "bearer"
			if ( 'Bearer' !== $credentials['token_type'] ) {
				Logger::add( 'critical', 'bullhorn-update-credentials-invalid-token-type', __( 'An invalid token type was provided to a credentials update.', 'matador-jobs' ) );
				return;
			}

			// Sanitize the values and toss unneeded ones
			$credentials['access_token']  = esc_attr( $credentials['access_token'] );
			$credentials['refresh_token'] = esc_attr( $credentials['refresh_token'] );
			unset( $credentials['token_type'] );
			unset( $credentials['expires_in'] );

			$this->credentials = $credentials;
			update_option( Matador::variable( 'bullhorn_api_credentials_key' ), $credentials );
		}
	}

	/**
	 * Destroy API Credentials
	 *
	 * Deletes existing API Credentials and unsets the class variable.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	private function destroy_credentials() {
		$this->credentials = array();
		delete_option( Matador::variable( 'bullhorn_api_credentials_key' ) );
	}

	/**
	 * Is Client ID Valid
	 *
	 * As an aid to our users, Matador can checks that the ClientID is valid.
	 * This check attempts a behind-the-scenes call to Bullhorn as if we
	 * were attempting to authorize the site with credentials. If the result
	 * is an error page with the text "Invalid ClientID", the method returns
	 * false. The method also returns false if the connection attempt fails.
	 * Otherwise, the method assumes the ClientID is valid and returns true.
	 *
	 * @since 3.1.0
	 *
	 * @param string
	 * @return boolean
	 */
	public function is_client_id_valid( $client = null ) {

		Logger::add( 'notice', 'bullhorn-client-id-check', __( 'Checking Bullhorn Client ID.', 'matador-jobs' ) );

		$client = ! empty( $client ) ? $client : Matador::setting( 'bullhorn_api_client' );

		if ( ! $client ) {
			return false;
		}

		$url = 'https://auth.bullhornstaffing.com/oauth/authorize';

		$params = array(
			'client_id'     => $client,
			'response_type' => 'code',
		);

		$request = wp_remote_get( $url . '?' . http_build_query( $params ), array( 'timeout' => 30 ) );

		if ( $request && ! is_wp_error( $request ) ) {

			Logger::add( 'notice', 'bullhorn-client-id-response', __( 'Bullhorn Client ID Response Received' ) );

			// Strip the response of HTML tags
			$response = strip_tags( $request['body'] );

			// Replace all whitespace with single spaces.
			$response = preg_replace( '!\s+!', ' ', $response );

			// Ready an array to collect results of regex examination.
			$matches = array();

			// Examine response with regex, output results to $matches array
			preg_match( '/.*(\bInvalid Client Id\b).*/i', $response, $matches );

			// If the array has more than zero contents, it found the error that signifies
			// an invalid redirect URI
			if ( count( $matches ) > 0 ) {
				Logger::add( 'error', 'bullhorn-client-id-invalid', 'Bullhorn Client ID is Invalid' );

				return false;
			}
			Logger::add( 'notice', 'bullhorn-client-id-valid', __( 'Bullhorn Client ID Check returned no error. Try again.' ) );

			return true;
		} else {
			Logger::add( 'notice', 'bullhorn-client-id-response', __( 'Bullhorn Client ID Check was unable to connect. Try again.' ) );

			return false;
		}

	}

	/**
	 * Is Redirect URI Invalid
	 *
	 * For security purposes, Matador checks that the domain has a valid
	 * redirect URI. While Bullhorn doesn't require a valid redirect URI
	 * to permit API calls, the workflow of authorizing a site requires an
	 * expert knowledge of both Matador and Bullhorn to ensure credentials
	 * are properly recorded.
	 *
	 * This check attempts a behind-the-scenes call to Bullhorn as if we
	 * were attempting to authorize the site with credentials and the
	 * redirect URI created by Matador. If the result is an error page
	 * with the text "Invalid Redirect URI", the method returns true. If
	 * the method finds credentials are not yet saved and/or the site
	 * operator overrode the redirect URI to null, or if the attempt
	 * returns a log in page, the method returns false. False does not
	 * mean the Redirect URI is valid, only that it is not invalid.
	 *
	 * @since 3.0.0
	 *
	 * @return boolean|null
	 */
	public function is_redirect_uri_invalid() {

		$redirect_uri = Matador::variable( 'api_redirect_uri' );

		if ( ! $redirect_uri ) {

			return null;
		}

		$client       = Matador::setting( 'bullhorn_api_client' );
		$client_valid = Matador::setting( 'bullhorn_api_client_is_valid' );
		$secret       = Matador::setting( 'bullhorn_api_secret' );

		if ( ! $client || ! $client_valid || ! $secret ) {
			new Event_Log( 'bullhorn-is-redirect-valid-missing-settings', __( 'A redirect URI check was indeterminate because of a missing valid Client ID or Client Secret.', 'matador-jobs' ) );

			return null;
		}

		$url = 'https://auth.bullhornstaffing.com/oauth/authorize';

		$params = array(
			'client_id'     => $client,
			'response_type' => 'code',
			'redirect_uri'  => $redirect_uri,
		);

		$request = wp_remote_get( $url . '?' . http_build_query( $params ) );

		if ( $request && ! is_wp_error( $request ) ) {

			// Strip the response of HTML tags
			$response = strip_tags( $request['body'] );

			// Replace all whitespace with single spaces.
			$response = preg_replace( '!\s+!', ' ', $response );

			// Ready an array to collect results of regex examination.
			$matches = array();

			// Examine response with regex, output results to $matches array
			preg_match( '/.*(\bInvalid Client Id\b).*/i', $response, $matches );

			// If the array has more than zero contents, it found the error that signifies
			// an invalid client ID. Return null to be handled as inderminate.
			if ( count( $matches ) > 0 ) {
				new Event_Log( 'bullhorn-is-redirect-valid-bad-client-id', __( 'A redirect URI check yielded a bad Client ID', 'matador-jobs' ) );

				return null;
			}

			// Examine response with regex, output results to $matches array
			preg_match( '/.*(\bInvalid Redirect URI\b).*/', $response, $matches );

			// If the array has more than zero contents, it found the error that signifies
			// an invalid redirect URI. Return true.
			if ( count( $matches ) > 0 ) {
				new Event_Log( 'bullhorn-is-redirect-valid-true', __( 'A redirect URI check yielded a valid Redirect URI.', 'matador-jobs' ) . print_r( $request['body'], true ) );

				return true;
			}
		} else {

			if( is_wp_error( $request ) ){
				new Event_Log( 'bullhorn-is-redirect-valid-wp_error', __( 'A redirect URI check yielded a WP error', 'matador-jobs' ) . print_r( $request, true ) );

			} else {

				new Event_Log( 'bullhorn-is-redirect-valid-null', __( 'A redirect URI check yielded an Error.', 'matador-jobs' ) . print_r( $request, true ) );
			}



			return null;
		}

		// If we made it this far, the redirect must be valid, so return false.
		return false;
	}

	/**
	 * Is Authorized
	 *
	 * Checks if Bullhorn credentials exist. If they do not, we can assume we cannot login.
	 * Does not determine if the credentials are valid.
	 *
	 * @since 3.0.0
	 *
	 * @param (string) $code the Bullhorn provided authorization code.
	 *
	 * @return bool whether we have existing authorization credentials.
	 */
	public function is_authorized() {
		if (
			! empty( $this->credentials )
			&& array_key_exists( 'access_token', $this->credentials )
			&& array_key_exists( 'refresh_token', $this->credentials )
		) {

			return true;
		}

		return false;
	}

	/**
	 * Authorize
	 *
	 * The first step to authorize a Bullhorn App is to request an authorization code.
	 * The authorization process can be done two ways.
	 *
	 * The first way, or the basic authorization, a user may send a request that redirects
	 * them to the Bullhorn Login screen where they must then enter their Bullhorn API
	 * user and password, after which they will be redirected back with an authorization code.
	 * This process must also be used if the API user has not accepted the terms and
	 * conditions.
	 *
	 * A second way, or advanced authorization, allows a user to send a plain-text username
	 * and password over HTTPS and Bullhorn will automatically redirect the user back with
	 * an authorization code.
	 *
	 * Note: the fast way requires an HTTPS site, and to check, we use WordPress's is_ssl()
	 * function. This has been known to not be accurate on load-balanced sites. See the link
	 * on a plugin for sites that are running SSL but load balanced servers are causing
	 * is_ssl() to return false.
	 *
	 * @since 3.0.0
	 * @param bool $advanced whether to attempt an advanced authorization
	 * @throws Exception
	 * @return void
	 */
	public function authorize( $advanced = true ) {

		Logger::add( 'notice', 'bullhorn-authorize-start', esc_html__( 'User initiated an authorization for Bullhorn.', 'matador-jobs' ) );

		// API Action URL
		$url = 'https://auth.bullhornstaffing.com/oauth/authorize';

		$redirect_uri   = Matador::variable( 'api_redirect_uri' );
		$client         = Matador::setting( 'bullhorn_api_client' );
		$secret         = Matador::setting( 'bullhorn_api_secret' );
		$user           = Matador::setting( 'bullhorn_api_user' );
		$pass           = Matador::setting( 'bullhorn_api_pass' );
		$has_authorized = Matador::setting( 'bullhorn_api_has_authorized' );

		if ( $client && $secret ) {

			$params = array(
				'client_id'     => $client,
				'response_type' => 'code',
			);

			// Skilled site operators may choose to use
			// a filter to set redirect URI to null.
			// This is not recommended in production.
			if ( $redirect_uri ) {
				$params['redirect_uri'] = $redirect_uri;
			}

			// An advanced authorization can be prevented by
			// passing the $advanced variable as false.
			// First-time log-ins to Bullhorn require the user to
			// accept terms and conditions, so $has_authorized
			// must be true.
			if ( $user && $pass && $advanced && $has_authorized ) {
				$params['username'] = $user;
				$params['password'] = $pass;
				$params['action']   = 'Login';
			}

			if ( ! isset( $params['action'] ) ) {
				$message = esc_html__( 'A manual authorization is required. User will be redirected.', 'matador-jobs' );
			} else {
				$message = esc_html__( 'An complete authorization is being sent to Bullhorn.', 'matador-jobs' );
			}

			Logger::add( 'notice', 'bullhorn-authorize-send', $message );

			$redirect = $url . '?' . http_build_query( $params );

		} else {

			$error = esc_html__( 'An authorization was attempted with missing or unsaved API credentials. At least Client ID and Client Secret are required.', 'matador-jobs' );
			throw new Exception( 'error', 'bullhorn-authorize-missing-credentials', $error );
		}

		wp_redirect( $redirect, 302 );
		die();
	}

	/**
	 * Reauthorize
	 *
	 * Occasionally, long after a site is authorized, especially during downtime at Bullhorn,
	 * a site may lose connection when a refresh_token is consumed but before a new refresh_token
	 * is granted. On unsecure sites and sites that do not provide a username and password in the
	 * settings, to reauthorize a site, user intervention is required. However, on secure sites
	 * that provide API user and passwords, we can attempt an automatic reauthorize.
	 *
	 * This should only be called when an API function fails. This will not run if a previous
	 * authorization does not exist, if the site is not secure, if the site is running in a no
	 * redirect uri mode, or if any of the required settings are not set.
	 *
	 * @since 3.0.0
	 * @throws Exception
	 */
	public function reauthorize() {

		Logger::add( 'notice', 'bullhorn-reauthorize-start', esc_html__( 'System initiated an automatic authorization attempt.', 'matador-jobs' ) );

		$redirect_uri = Matador::variable( 'api_redirect_uri' );
		$client       = Matador::setting( 'bullhorn_api_client' );
		$secret       = Matador::setting( 'bullhorn_api_secret' );
		$user         = Matador::setting( 'bullhorn_api_user' );
		$pass         = Matador::setting( 'bullhorn_api_pass' );

		if ( $redirect_uri && $client && $secret && $user && $pass && $this->is_authorized() && ! $this->is_redirect_uri_invalid() ) {

			Logger::add( 'notice', 'bullhorn-reauthorize-allowed', esc_html__( 'System determined site is able to support automatic authorization attempt.', 'matador-jobs' ) );

			set_transient( Matador::variable( 'bullhorn-auto-reauth', 'transients' ), true, 15 );

			$url = 'https://auth.bullhornstaffing.com/oauth/authorize';

			$params = array(
				'client_id'     => $client,
				'response_type' => 'code',
				'redirect_uri'  => $redirect_uri,
				'username'      => $user,
				'password'      => $pass,
				'action'        => 'Login',
			);

			$request_args = array(
				'timeout' => 10,
				// Because many sites are now using self-signed SSL, which doesn't verify
				// as easily, and given the nature of this action doing a multi-redirect loop
				// back to the host, we may want to disable SSL verify on this call.
				'sslverify' => apply_filters( 'matador_reauthorize_verify_ssl', true ),
			);

			$request = wp_remote_get( $url . '?' . http_build_query( $params ), $request_args );

			if ( is_wp_error( $request ) ) {

				throw new Exception( 'error', 'bullhorn-reauthorize-error', esc_html__( 'Automatic authorization attempt failed with this message: ', 'matador-jobs' ) . $request->get_error_message() );

			} else {

				return true;

			}
		} else {

			throw new Exception( 'notice', 'bullhorn-reauthorize-not-allowed', esc_html__( 'System determined settings are incomplete and we cannot support an automatic authorization attempt.', 'matador-jobs' ) );

		}

	}

	/**
	 * Deauthorize
	 *
	 * In the event a site owner wishes to disconnect their site, we'll remove credentials
	 * for them with this function.
	 *
	 * @since 3.0.0
	 */
	public function deauthorize() {
		$this->destroy_credentials();
		Matador::setting( 'bullhorn_api_is_connected', false );
	}

	/**
	 * Get Access Token with Authorization Code
	 *
	 * The second step to authorize a Bullhorn App is to take the authorization code returned
	 * in step one and use it to authorize the site. This function will attempt to authorize the
	 * site, and if it can, will save the recieved credentials for future use.
	 *
	 * @since 3.0.0
	 *
	 * @param string $code the Bullhorn provided authorization code.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function request_access_token( $code ) {

		// API Action URL
		$url = 'https://auth.bullhornstaffing.com/oauth/token';

		// Get info we need for this action
		$client       = Matador::setting( 'bullhorn_api_client' );
		$secret       = Matador::setting( 'bullhorn_api_secret' );
		$redirect_uri = Matador::variable( 'api_redirect_uri' );

		// Check if we have what we need
		if ( empty( $client ) || empty( $secret ) || empty( $code ) ) {
			throw new Exception( 'error', 'bullhorn-request-token-missing-credentials', esc_html__( 'Cannot authorize without credentials.', 'matador-jobs' ) );
		}

		// Send the request.
		$params = array(
			'grant_type'    => 'authorization_code',
			'code'          => $code,
			'client_id'     => $client,
			'client_secret' => $secret,
		);

		// Request Args
		$args = array(
			'timeout' => 15,
		);

		if ( $redirect_uri ) {
			$params['redirect_uri'] = $redirect_uri;
		}

		$request = wp_remote_post( add_query_arg( $params, $url ), $args );

		// Did the request itself work
		if ( $request && ! is_wp_error( $request ) ) {

			$body = json_decode( $request['body'] );

			// Okay, lets see if the content of the response is what we want
			if ( 200 === $request['response']['code'] && isset( $body->access_token ) ) {

				Matador::setting( 'bullhorn_api_has_authorized', true );
				Matador::setting( 'bullhorn_api_is_connected', true );

				$this->update_credentials( (array) $body );

			} elseif ( 400 === $request['response']['code'] ) {
				// Error 400 means we made a 'bad request', when really we provided invalid login
				// credentials. Of course, code 400 is totally and completely the wrong HTTP
				// code to use for a request with invalid credentials. (Should be 401). It means
				// we tried to send a used refresh token or a mismatched redirect URL. In the former,
				// it means we failed to update our state when we got a new code (unlikely) or a prior
				// request failed (very likely) and we didn't handle that request's error.

				$this->destroy_credentials();
				throw new Exception( 'error', 'bullhorn-authorization-bad-request', esc_html( __( 'Bullhorn could not authorize your site due to a bad request: ', 'matador-jobs' ) . $body->error_description ) );
			}
		} else {

			throw new Exception( 'error', 'bullhorn-authorization-timeout', esc_html( __( 'Authorization failed due to a timeout when your site was accessing Bullhorn', 'matador-jobs' ) . print_r( $request, true ) ) );
		}

	}

	/**
	 * Refresh Access Token
	 *
	 * The third step to authorize a Bullhorn app is once per session, the login.
	 * Login requires a valid Access Token. The Bullhorn-provided API Credentials
	 * we got in step two include an access token which expires in 600 seconds.
	 * Login actions after that time limit need to first refresh the token, which
	 * this call does by re-authorizing the app using the refresh token provided
	 * by Bullhorn's most recent authorization.
	 *
	 * @since 3.0.0
	 * @return boolean
	 * @throws Exception
	 */
	private function refresh_access_token() {

		Logger::add( 'info', 'bullhorn-refresh-token-start', esc_html__( 'Starting Bullhorn Refresh Token.', 'matador-jobs' ) );

		// API Action URL
		$url = 'https://auth.bullhornstaffing.com/oauth/token';

		// Get info we need for this action
		$refresh_token = $this->get_credential( 'refresh_token' );
		$client        = Matador::setting( 'bullhorn_api_client' );
		$secret        = Matador::setting( 'bullhorn_api_secret' );

		// Are we missing something we need?
		if ( ! ( $refresh_token || $client || $secret ) ) {
			if ( ! $refresh_token ) {
				// Not possible unless function is called improperly.
				throw new Exception( 'error', 'bullhorn-refresh-token-missing-credentials', esc_html__( 'Refreshing a token requires existing credentials. User intervention required.', 'matador-jobs' ) );
			} elseif ( ! $client ) {
				throw new Exception( 'error', 'bullhorn-refresh-token-missing-credentials', esc_html__( 'Refreshing a token requires API Client ID. User intervention required.', 'matador-jobs' ) );
			} elseif ( ! $secret ) {
				throw new Exception( 'error', 'bullhorn-refresh-token-missing-credentials', esc_html__( 'Refreshing a token requires API Secret. User intervention required.', 'matador-jobs' ) );
			}
		}

		// Send the request
		$params = array(
			'grant_type'    => 'refresh_token',
			'refresh_token' => $refresh_token,
			'client_id'     => $client,
			'client_secret' => $secret,
		);

		$args = array(
			'timeout' => 15,
		);

		$request = wp_remote_post( add_query_arg( $params, $url ), $args );

		// Check if the request worked.
		if ( $request && ! is_wp_error( $request ) ) {
			$response = $request['response'];
			$body     = json_decode( $request['body'] );
		} else {
			throw new Exception( 'error', 'bullhorn-refresh-token-remote-error', esc_html__( 'Request to refresh token was rejected or timed out. Bullhorn may be down. Error: ', 'matador-jobs' ) . $request->get_error_message() );
		}

		// Check if the returned content is as expected.
		if ( 200 === $response['code'] && isset( $body->access_token ) ) {

			$this->update_credentials( (array) $body );
			// Translators: Placeholder for Token ID
			Logger::add( 'info', 'bullhorn-refresh-token-success', esc_html__( 'Bullhorn Refresh Token complete.', 'matador-jobs' ) );

		} elseif ( 400 === $response['code'] ) {
			// Error 400 means we made a 'bad request', when really we provided invalid login
			// credentials. Of course, code 400 is totally and completely the wrong HTTP
			// code to use for a request with invalid credentials. (Should be 401). It means
			// we tried to send a used refresh token. Either we failed to update our state when
			// we got a new one (unlikely) or a prior request failed (very likely) and we didn't
			// handle that request's error.
			//
			// The $body->error_description has error details. (often "Invalid Grant" )

			Logger::add( 'error', 'bullhorn-refresh-token-failed', esc_html__( 'Bullhorn refresh token failed with error: ', 'matador-jobs' ) . $body->error_description );

			// Here we are going to run reauthorize, which attempts a reauthorization request.
			try {

				$this->reauthorize();
				sleep( 1 );

			} catch ( Exception $e ) {

				Logger::add( $e->getLevel(), $e->getName(), $e->getMessage() );
				Admin_Notices::add( __( 'You are disconnected from Bullhorn. We were unable to refresh the token.', 'matador-jobs' ), 'error', 'bullhorn-refresh-token-disconnected' );
				Matador::setting( 'bullhorn_api_is_connected', false );
				$this->destroy_credentials();
				// Needs to throw error so the Class::login fails/stops
			}
		}

		// In late 2015, the server handling access tokens
		// and the regional servers were not syncing very
		// fast. We wait a quarter second and to give it
		// a moment to catch up.
		usleep( 250000 );

		return true;
	}

	/**
	 * Login to Bullhorn REST API
	 *
	 * If we've done everything right to this point, we are ready to
	 * log in and begin making calls to the API. The first thing we do
	 * is check our access token. If its expired, we'll request a new
	 * one.
	 *
	 * @since 3.0.0
	 * @throws Exception
	 * @return boolean
	 */
	public function login() {

		Logger::add( 'info', 'bullhorn-login-start', esc_html__( 'Logging into Bullhorn.', 'matador-jobs' ) );
		//remove any old admin notices
		Admin_Notices::remove( 'bullhorn-login-exception' );

		// Before we attempt login, check that we have authorized the site.
		if ( ! $this->is_authorized() ) {
			Logger::add( 'error', 'bullhorn-login-not-authorized', esc_html__( 'Site is not authorized to connect with Bullhorn. User intervention is required.', 'matador-jobs' ) );
			Email::admin_error_notification( esc_html__( 'Site is not authorized to connect with Bullhorn. User intervention is required.', 'matador-jobs' ) );

			return false;
		}

		// Before we attempt login, we need a refreshed token.
		// Despite what BH documentation may suggest, a token can be used only once.
		$this->refresh_access_token();

		// Send the request
		$url = $this->get_data_center() . 'rest-services/login';

		$params = array(
			'version'      => '*',
			'access_token' => $this->get_credential( 'access_token' ),
			'ttl'          => 600,
		);

		$request = wp_remote_get( add_query_arg( $params, $url ), array( 'timeout' => 15 ) );

		// Check if the request worked
		if ( $request && ! is_wp_error( $request ) ) {
			$body = json_decode( $request['body'] );
		} else {
			throw new Exception( 'error', 'bullhorn-login-timeout', esc_html__( 'Login failed due to timeout.', 'matador-jobs' ) );
		}

		// Review response from Bullhorn
		if ( isset( $body->BhRestToken ) ) { // @codingStandardsIgnoreLine (SnakeCase)
			$this->session   = $body->BhRestToken; // @codingStandardsIgnoreLine (SnakeCase)
			$this->url       = $body->restUrl; // @codingStandardsIgnoreLine (SnakeCase)

			preg_match( '/rest(.*)rest-services.*/U', $body->restUrl, $matches );
			if( isset( $matches[1] ) ) {
				set_transient( 'bullhorn_server_url', 'https://cls' . $matches[1] );
			}

			$this->logged_in = true;
			Logger::add( 'info', 'bullhorn-login-success', esc_html__( 'Successfully logged into Bullhorn.', 'matador-jobs' ) );
		} else {
			if ( empty( $body->errorMessage ) ) { // @codingStandardsIgnoreLine (SnakeCase)
				$error = esc_html__( 'Error unknown', 'matador-jobs' );
			} else {
				$error = esc_html( $body->errorMessage ); // @codingStandardsIgnoreLine (SnakeCase)
			}

			throw new Exception( 'error', 'bullhorn-authorization-login-error', esc_html__( 'Login failed to Bullhorn error: ', 'matador-jobs' ) . $error );
		}

		return true;
	}

	/**
	 * API Request
	 *
	 * WHEW! We did it. We are logged into Bullhorn. This function handles our API
	 * calls and wraps wp_remote_request().
	 *
	 * @access public
	 *
	 * @param string $api_method string Bullhorn API method, default null
	 * @param array $params array of API request parameters
	 * @param string $http_method http verb for request
	 * @param array|object $body data to be sent with request as JSON
	 * @param array $request_args array of arguments for wp_remote_request() function
	 *
	 * @uses wp_remote_request()
	 * @throws Exception
	 * @since 3.0.0
	 * @return bool|object of content from API
	 */
	public function request( $api_method = null, $params = array(), $http_method = 'GET', $body = null, $request_args = null ) {

		if ( is_null( $this->url ) ) {
			throw new Exception( 'error', 'bullhorn-request-not-logged-in', esc_html__( 'Bullhorn requests require a logged in instance.', 'matador-jobs' ) );
		}
		if ( is_null( $api_method ) || is_null( $params ) ) {
			throw new Exception( 'error', 'bullhorn-request-no-method', esc_html__( 'Bullhorn requests require a method and not null params array.', 'matador-jobs' ) );
		}
		if ( ! in_array( strtoupper( $http_method ), array( 'GET', 'POST', 'PUT' ), true ) ) {
			throw new Exception( 'error', 'bullhorn-request-invalid-http-method', esc_html__( 'Bullhorn requests require a valid HTTP method.', 'matador-jobs' ) );
		}

		// Translators: Placeholder is for API Method call.
		Logger::add( 'info', 'bullhorn-request-start', sprintf( esc_html__( 'Starting Bullhorn request to endpoint %s.', 'matador-jobs' ), $api_method ) );

		if ( is_array( $body ) || is_object( $body ) ) {
			$body = wp_json_encode( $body );
		}

		$params['BhRestToken'] = $this->session;

		$default_request_args = array(
			'method'      => strtoupper( $http_method ),
			'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'        => $body,
			'data_format' => 'body',
			'timeout'     => 45,
		);

		$args = ! empty( $request_args ) ? array_merge( $default_request_args, $request_args ) : $default_request_args;

		$request = wp_remote_request( add_query_arg( $params, $this->url . $api_method ), $args );

		// Logger::add( 'info', 'bullhorn-request-url', sprintf( esc_html__( 'Bullhorn Request to URL %s.', 'matador-jobs' ), add_query_arg( $params, $this->url . $api_method ) ) );

		// Check if the request worked
		if ( $request && ! is_wp_error( $request ) ) {

			// Translators: Placeholder is for API Method call.
			Logger::add( 'info', 'bullhorn-request-success', sprintf( esc_html__( 'Completed Bullhorn request to endpoint %s.', 'matador-jobs' ), $api_method ) );

			return json_decode( $request['body'] );
		} else {
			throw new Exception( 'error', 'bullhorn-request-timed-out', esc_html__( 'Bullhorn request timed out.', 'matador-jobs' ) );
		}
	}

	/**
	 * Wrapper for submitting files around request().
	 *
	 * @access public
	 * @access public
	 *
	 * @param string $api_method string Bullhorn API call name
	 * @param array $params array of URL parameters
	 * @param string $http_method verb for request
	 * @param string $file path_to_file
	 *
	 * @return bool|array
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	protected function request_with_payload( $api_method = null, $params = array(), $http_method = 'POST', $file = null ) {

		// Check we got the right stuff
		if ( ! is_string( $api_method ) || ! is_array( $params ) || ! is_string( $http_method ) || ! is_string( $file ) || ! in_array( strtoupper( $http_method ), array( 'POST', 'PUT' ), true ) ) {
			$error_name = 'method-invalid-parameters';
			Logger::add( 'warning', $error_name, esc_html__( 'Method called with invalid parameters.', 'matador-jobs' ) );

			return array( 'error' => $error_name );
		}

		if( ! is_array( $file ) ) {
			// Check the file exists
			if ( ! file_exists( $file ) ) {
				$error_name = 'file-does-not-exist';
				Logger::add( 'warning', $error_name, esc_html__( 'The file path was not set or invalid.', 'matador-jobs' ) );

				return array( 'error' => $error_name );
			}

			// Get the file type and format.
			list( $ext, $format ) = Helper::get_file_type( $file );

			// Get file contents without requiring get_file_contents or the URL to run wp_remote_get
			$contents = implode( '', file( $file ) );
			// Name to send.
			$name = substr( basename( $file ), 0, strrpos( basename( $file ), '.' ) ) . '.' . $ext;
		} else{
			if ( ! isset( $file['contents'] ) ) {
				$error_name = 'file-content-missing';
				Logger::add( '2', $error_name, esc_html__( 'The content is missing', 'matador-jobs' ) );

				return array( 'error' => $error_name );
			}
			// Get the file type and format.
			list( $ext, $format ) = Helper::get_file_type( $file['file'] );
			$name = substr( basename( $file ), 0, strrpos( basename( $file['file']  ), '.' ) ) . '.' . $ext;
			$contents = $file['contents'];
		}

		// Check the format is allowed.
		if ( ! $ext || ! $format ) {
			$error_name = 'file-format-invalid';
			Logger::add( '2', $error_name, esc_html__( 'The file type was invalid.', 'matador-jobs' ) );

			return array( 'error' => $error_name );
		}

		// Add to the params array file format;
		$params['format'] = $format;



		// Create a boundary. We'll need it as we build the payload.
		$boundary = md5( time() . $ext );



		// End of Line
		$eol = "\r\n";

		// Construct the payload in multipart/form-data format
		$payload  = '';
		$payload .= '--' . $boundary;
		$payload .= $eol;
		$payload .= 'Content-Disposition: form-data; name="submitted_file"; filename="' . $name . '"' . $eol;
		$payload .= 'Content-Type: ' . $format . $eol;
		$payload .= 'Content-Transfer-Encoding: binary' . $eol;
		$payload .= $eol;
		$payload .= $contents;
		$payload .= $eol;
		$payload .= '--' . $boundary . '--';
		$payload .= $eol . $eol;

		// Create args for wp_remote_request
		$args = array(
			'headers' => array(
				'accept'       => 'application/json',
				'content-type' => 'multipart/form-data;boundary=' . $boundary,
			),
		);

		// Call the standard request function and return it.
		return $this->request( $api_method, $params, $http_method, $payload, $args );
	}
}
