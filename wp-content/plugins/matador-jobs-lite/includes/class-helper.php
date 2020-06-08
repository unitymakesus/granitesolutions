<?php
/**
 * Matador Helper
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

// Exit if accessed directly or if parent class doesn't exist.
if ( ! defined( 'ABSPATH' ) || class_exists( 'Logger' ) ) {
	exit;
}

/*
 *
 */

final class Helper {


	/**
	 * Workout the file type for uploaded files
	 *
	 * @param string $file_path a path to the file
	 *
	 * @return boolean|array
	 */
	public static function get_file_type( $file_path = null ) {

		if ( null === $file_path ) {

			return false;
		}
		// Get file extension
		$mine_types = wp_get_mime_types();
		unset( $mine_types['swf'], $mine_types['exe'], $mine_types['htm|html'] );

		if ( ! isset( $file_path ) || empty( $file_path ) || is_array( $file_path ) ) {
			Logger::add( 2, __( 'No file path provided in get_filetype().', 'matador-jobs' ) );

			return false;
		}

		$file_type = wp_check_filetype_and_ext( $file_path, basename( $file_path ), $mine_types );

		switch ( strtolower( $file_type['type'] ) ) {
			case 'text/plain':
				$format = 'TEXT';
				break;
			case 'application/msword':
				$format = 'DOC';
				break;
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
				$format = 'DOCX';
				break;
			case 'application/pdf':
				$format = 'PDF';
				break;
			case 'text/rtf':
				$format = 'RTF';
				break;
			case 'text/html':
				$format = 'HTML';
				break;
			default:
				return false;
		}

		return array( $file_type['ext'], $format );
	}

	/**
	 * Time From UTC to Local
	 *
	 * @param object $datetime
	 *
	 * @return object DateTime with TimeZone
	 */
	public static function utc_to_local( $datetime ) {
		$zone = get_option( 'timezone_string' ) ?: 'UTC';

		return $datetime->setTimezone( new \DateTimeZone( $zone ) );
	}

	/**
	 * Format Date
	 *
	 * Takes a time object and formats it to the 8601 standard
	 *
	 * @since 1.0.0
	 *
	 * @param string $datetime
	 *
	 * @return string
	 */
	public static function format_datetime_to_8601( $datetime ) {
		$datetime = self::utc_to_local( $datetime );

		return $datetime->format( 'c' );
	}

	/**
	 * Format Date : MYSQL
	 *
	 * Takes a time object and formats it to the 8601 standard
	 *
	 * @since 1.0.0
	 *
	 * @param string $
	 *
	 * @return object DateTime
	 */
	public static function format_datetime_to_mysql( $datetime ) {
		$datetime = self::utc_to_local( $datetime );

		return $datetime->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Get Post by Bullhorn ID
	 *
	 * Searches the local jobs for a job with the _matador_source_id postmeta where _matador_source is Bullhorn and
	 * returns the job object
	 *
	 * @access private
	 *
	 * @param integer $id id of Bullhorn Job to search
	 *
	 * @return object|boolean WP_Post|false
	 *
	 * @since 3.0.0
	 * @since 3.5.0 Updated to use new standard post meta _matador_source and _matador_source_id
	 */
	public static function get_post_by_bullhorn_id( $id ) {
		$args = array(
			'post_type'  => Matador::variable( 'post_type_key_job_listing' ),
			'number'     => 1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_matador_source',
					'value'   => 'bullhorn',
					'compare' => '=',
				),
				array(
					'key'     => '_matador_source_id',
					'value'   => absint( $id ),
					'compare' => '=',
				),
			),
		);

		$job_post = get_posts( $args );

		if ( empty( $job_post ) ) {
			return false;
		}

		return $job_post[0];
	}

	public static function resume_or_cv() {
		$setting = Matador::setting( 'resume_or_cv' );
		switch ( $setting ) {
			case 'cv':
				return esc_html__( 'Cirriculum Vitae', 'matador-jobs' );
			case 'cv_abbr':
				return esc_html__( 'CV', 'matador-jobs' );
			case 'resume':
			default:
				return esc_html__( 'Resume', 'matador-jobs' );
		}
	}

	/**
	 * Get Bullhorn ID from WPID
	 *
	 * Fetches the bullhorn ID of the job or returns false.
	 *
	 * @since  3.0.0
	 * @since  3.5.0 Now verifies source is 'bullhorn' from _matador_source prior to returning id
	 *
	 * @param  array $id ID of job (optional)
	 *
	 * @return int|bool    Job ID or false.
	 */
	public static function the_job_bullhorn_id( $id = null ) {
		$id = is_int( $id ) ? $id : get_the_id();

		if ( 'bullhorn' !== get_post_meta( $id, '_matador_source', true ) ) {

			return false;
		}

		return get_post_meta( $id, '_matador_source_id', true );
	}

	private static $categories;

	/**
	 *
	 *
	 * @return array|null|string
	 */
	public static function get_categories( $id = null ) {
		if ( null === $id ) {
			$id = get_the_id();
		}
		if ( null === self::$categories || ! isset( self::$categories[ $id ] ) ) {
			$names      = $slug = array();
			$taxonomies = Matador::variable( 'job_taxonomies' );
			$categories = wp_get_post_terms( $id, $taxonomies['category']['key'] );

			if ( $categories && ! is_wp_error( $categories ) ) {
				foreach ( $categories as $category ) {
					$names[] = ucwords( $category->name );
					$slug[]  = $taxonomies['category']['key'] . '-' . $category->slug;
				}
			}

			self::$categories[ $id ] = array( $names, $slug );
		}

		return self::$categories[ $id ];
	}


	private static $locations;

	/**
	 *
	 *
	 * @return array|null|string
	 */
	public static function get_locations( $id = null ) {
		if ( null === $id ) {
			$id = get_the_id();
		}
		if ( null === self::$locations || ! isset( self::$locations[ $id ] ) ) {
			$names      = $slug = array();
			$taxonomies = Matador::variable( 'job_taxonomies' );
			$locations  = wp_get_post_terms( $id, $taxonomies['location']['key'] );

			if ( $locations && ! is_wp_error( $locations ) ) {
				foreach ( $locations as $location ) {
					$names[] = ucwords( $location->name );
					$slug[]  = $taxonomies['location']['key'] . '-' . $location->slug;
				}
			}
			self::$locations[ $id ] = array( $names, $slug );
		}

		return self::$locations[ $id ];
	}

	public static function get_nopaging_url() {
		$current_url = $_SERVER['REQUEST_URI'];

		$pattern      = '/page\\/[0-9]+\\//i';
		$nopaging_url = preg_replace( $pattern, '', $current_url );

		// we should never pass the matador-apply on in the URL as this is a one time call
		$nopaging_url = remove_query_arg( 'matador-apply', $nopaging_url );

		return $nopaging_url;
	}

	/**
	 * @param array $array
	 * @param int|string $position
	 * @param mixed $insert
	 *
	 * @return array
	 */
	public static function array_insert( $array, $position, $insert ) {

		$position = self::array_position( $array, $position );

		$insert = is_array( $insert ) ? $insert : array( $insert );

		// array_splice will index the array.
		array_splice( $array, $position + 1, 0, $insert );

		return $array;

	}

	/**
	 * @param array $array
	 * @param int|string $position
	 *
	 * @return array
	 * @todo make it so it reindex only on numeric arrays
	 */
	public static function array_remove( $array, $position ) {

		$position = self::array_position( $array, $position );

		if ( array_key_exists( $position, $array ) ) {

			unset( $array[ $position ] );

		} else {

			foreach ( array_keys( $array, $position, true ) as $key ) {

				unset( $array[ $key ] );
			}
		}

		return array_values( $array );

	}

	/**
	 * @param array $array
	 * @param int|string $position
	 * @param mixed $insert
	 *
	 * @return array
	 */
	public static function array_replace( $array, $position, $insert ) {

		$position = self::array_position( $array, $position );

		$array = self::array_insert( $array, $position, $insert );

		$array = self::array_remove( $array, $position );

		return $array;

	}

	public static function array_position( $array, $position ) {
		if ( ! is_int( $position ) ) {

			if ( array_key_exists( $position, $array ) ) {

				$position = array_search( $position, array_keys( $array ), true );

			} else {

				$position = array_search( $position, $array, true );

			}
		}

		return $position;
	}

	public static function build_attributes( $attributes_array ) {

		$attributes = array();

		if ( empty( $attributes_array ) || ! is_array( $attributes_array ) ) {
			return '';
		}

		foreach ( $attributes_array as $attribute => $value ) {
			if ( is_bool( $value ) || 'true' === $value ) {
				$attributes[] = $attribute;
			} else {
				$attributes[] = sprintf( '%1$s="%2$s"', $attribute, $value );
			}
		}

		return implode( ' ', $attributes );
	}

	public static function form_field_args( $args = null, $field = null ) {

		if ( empty( $args ) || empty( $field ) ) {
			return false;
		}

		// Add all potential indexes to the array
		// to prevent undefined index errors.
		$args = wp_parse_args( $args, array(
			'type'        => null,
			'template'    => null,
			'name'        => null,
			'default'     => null,
			'label'       => null,
			'sublabel'    => null,
			'description' => null,
			'options'     => null,
			'attributes'  => array(),
			'class'       => array(),
			'value'       => null,
			'sanitize'    => null,
		) );

		// We need to pass the key to the template
		$args['name'] = $field;

		// Determine Form Template (Default, or Check for Specific)
		$template = ! empty( $args['template'] ) ? $args['template'] : $args['type'];

		if ( ! is_array( $args['class'] ) ) {
			$args['class'] = (array) $args['class'];
		}

		// Add Classes
		$args['class'] = array_merge( array(
			'matador-field-group',
			'matador-field-' . $field,
			'matador-field-template-' . $template,
			'matador-field-type-' . $args['type'],
		), $args['class'] );

		// Clean Up a Bit
		unset( $args['template'], $args['sanitize'], $args['supports'] );

		return array( $args, $template );
	}

	public static function comma_separated_string_to_escaped_array( $string ) {
		$array = array_map( 'trim', explode( ',', $string ) );

		return self::array_values_escaped( $array );
	}

	public static function array_values_escaped( $array ) {
		foreach ( $array as &$item ) {
			$item = esc_attr( $item );
		}

		return $array;
	}


	/**
	 *
	 *
	 * @return mixed
	 */
	public static function get_client_cluster_url() {
		$bullhorn_server_url = get_transient( 'bullhorn_server_url' );

		if ( empty( $bullhorn_server_url ) ) {

			return false;
		}

		return $bullhorn_server_url;
	}

	public static function jobs_fields_string_to_array( $array ) {
		if ( ! is_string( $array ) ) {
			return $array;
		}

		$array  = array_map( 'trim', explode( ',', $array ) );
		$return = array();

		foreach ( $array as $component ) {
			$parts = explode( '|', $component );

			if ( 2 <= count( $parts ) ) {

				$return[ esc_attr( $parts[0] ) ] = esc_html( $parts[1] );
			} else {

				// convert camelcase to words with spaces
				$matches = array();
				preg_match_all( '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $component, $matches );
				$ret = $matches[0];
				foreach ( $ret as &$match ) {
					$match = ( strtoupper( $match ) === $match ) ? strtolower( $match ) : lcfirst( $match );
				}
				$title = implode( ' ', $ret );

				// convert dashes to spaces
				$title = str_replace( '-', ' ', $title );

				// convert underscores to spaces
				$title = str_replace( '_', ' ', $title );

				// make words uppercase
				$title = ucwords( $title );

				// assign title as the key to field
				$return[ esc_attr( $component ) ] = esc_html( $title );
			}
		}

		return $return;
	}

	/**
	 * Matador Deprecated Notice
	 *
	 * Helper to produce the text for a Deprecated Notice
	 *
	 * @param string $type
	 * @param string $old
	 * @param string $new
	 */
	public static function deprecated_notice( $type = '', $old = '', $new = '' ) {

		if ( ! ( $type && $old && $new ) ) {
			return;
		}

		if ( $new ) {
			// Translators: placeholder 1 is the 'type' of deprecated item, ie: a function. Placeholder 2 is the old
			// Translators: item name. Placeholder 3 is its replacement, if necessary.
			$notice = __(
				'Your theme or extension is using the deprecated "%2$s" %1$s. Please update the %1$s to "%3$s". We will remove this %1$s in a future version.',
				'matador-jobs'
			);
		} else {
			// Translators: placeholder 1 is the 'type' of deprecated item, ie: a function. Placeholder 2 is the old
			// Translators: item name.
			$notice = __(
				'Your theme or extension is using the deprecated "%2$s" %1$s. Please consider an alternative solution. We will remove this %1$s in a future version.',
				'matador-jobs'
			);
		}

		if ( current_user_can( 'edit_posts' ) ) {
			echo '<div class="matador-deprecated-notice"><h4>'
				. esc_html__( 'Deprecation Notice', 'matador-jobs' ) . '</h4><p>'
				. esc_html( sprintf( $notice, $type, $old, $new ) ) . '</p><p>'
				. esc_html__( 'Only you, a logged in user who can edit content, can see this notice.', 'matador-jobs' )
				. '</p></div>';
		}

		new Event_Log( "matador-deprecated-{$type}-notice", sprintf( $notice, $type, $old, $new ) );
	}

	/**
	 * Escape Lucene Search Parameter
	 *
	 * Helper escaped reserved characters out of a Lucene Search Query parameter.
	 *
	 * @access public
	 * @static
	 * @since 3.4.0
	 *
	 * @param string $string
	 * @return string
	 */
	public static function escape_lucene_string( $string ) {

		$string = self::convert_quote_marks( $string );

		$reserved = '/\+|-|&|\||!|\(|\)|\{|\}|\[|\]|\^|"|~|\*|\?|\:|\\\/';

		$escaped = preg_replace( $reserved, '\\\\\\0', $string );

		if ( preg_match( '/ /', $escaped ) || strlen( $string ) != strlen( $escaped ) ) {
			return $escaped;
		}

		return $string;
	}

	/**
	 * Convert Quote Marks to Single or Double Quote
	 *
	 * Helper to convert various forms of single and double quotes in UTF-8 to the Unicode ' or " characters. Used in
	 * various string sanitization calls.
	 *
	 * Thanks to Walter Tross: https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
	 *
	 * @access public
	 * @static
	 * @since 3.4.0
	 *
	 * @param string $string
	 * @return string
	 */
	public static function convert_quote_marks( $string ) {
		$chr_map = array(
			// Windows codepage 1252
			"\xC2\x82"     => "'", // U+0082⇒U+201A single low-9 quotation mark
			"\xC2\x84"     => '"', // U+0084⇒U+201E double low-9 quotation mark
			"\xC2\x8B"     => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
			"\xC2\x91"     => "'", // U+0091⇒U+2018 left single quotation mark
			"\xC2\x92"     => "'", // U+0092⇒U+2019 right single quotation mark
			"\xC2\x93"     => '"', // U+0093⇒U+201C left double quotation mark
			"\xC2\x94"     => '"', // U+0094⇒U+201D right double quotation mark
			"\xC2\x9B"     => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

			// Regular Unicode     // U+0022 quotation mark (")
			// U+0027 apostrophe     (')
			"\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
			"\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
			"\xE2\x80\x98" => "'", // U+2018 left single quotation mark
			"\xE2\x80\x99" => "'", // U+2019 right single quotation mark
			"\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
			"\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
			"\xE2\x80\x9C" => '"', // U+201C left double quotation mark
			"\xE2\x80\x9D" => '"', // U+201D right double quotation mark
			"\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
			"\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
			"\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
			"\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
		);
		$chr     = array_keys( $chr_map ); // but: for efficiency you should
		$rpl     = array_values( $chr_map ); // pre-calculate these two arrays

		return str_replace( $chr, $rpl, html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
	}

	/**
	 * Get User IP
	 *
	 * Returns the IP address of the current visitor
	 *
	 * Credit: Easy Digital Downloads edd_get_ip() https://easydigitaldownloads.com/
	 *
	 * @access public
	 * @static
	 * @since 3.4.0
	 *
	 * @return string $ip User's IP address
	 */
	public static function get_user_ip() {

		$ip = '127.0.0.1';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// to check ip is pass from proxy
			// can include more than 1 ip, first is the public one
			$ip = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
			$ip = trim( $ip[0] );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		// Fix potential CSV returned from $_SERVER variables
		$ip_array = explode( ',', $ip );
		$ip_array = array_map( 'trim', $ip_array );

		return apply_filters( 'matador_get_user_ip', $ip_array[0] );
	}

}
