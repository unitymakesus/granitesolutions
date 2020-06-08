<?php
/**
 * Matador / Shortcodes
 *
 * Registers Shortcodes for use in WP Editor.
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs
 * @subpackage  core
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2017 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

namespace matador;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Shortcodes
 *
 * @final
 * @since 3.0.0
 */
final class Shortcodes {

	/**
	 * Class Constructor
	 *
	 * Adds shortcodes to WP.
	 *
	 * @access public
	 * @since  3.0.0
	 */
	public function __construct() {
		add_shortcode( 'matador_job', array( __CLASS__, 'matador_job_shortcode' ) );
		add_shortcode( 'matador_job_field', array( __CLASS__, 'matador_job_field_shortcode' ) );
		add_shortcode( 'matador_jobs', array( __CLASS__, 'matador_jobs_shortcode' ) );
		add_shortcode( 'matador_jobs_list', array( __CLASS__, 'matador_jobs_list_shortcode' ) );
		add_shortcode( 'matador_jobs_table', array( __CLASS__, 'matador_jobs_table_shortcode' ) );
		add_shortcode( 'matador_jobs_listing', array( __CLASS__, 'matador_jobs_listing_shortcode' ) );
		add_shortcode( 'matador_taxonomy', array( __CLASS__, 'matador_taxonomy_shortcode' ) );
		add_shortcode( 'matador_categories', array( __CLASS__, 'matador_category_shortcode' ) );
		add_shortcode( 'matador_types', array( __CLASS__, 'matador_types_shortcode' ) );
		add_shortcode( 'matador_locations', array( __CLASS__, 'matador_location_shortcode' ) );
		add_shortcode( 'matador_search', array( __CLASS__, 'matador_search_form_shortcode' ) );
		add_shortcode( 'matador_application', array( __CLASS__, 'matador_application_shortcode' ) );
	}

	/**
	 * Single Job Shortcode
	 *
	 * Retrieves a single job, useful for inclusion as part of a
	 * blog post highlighting a position or for use in a widget area.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 * @since  3.4.0 added 'fields' attribute to support new Job Meta and Job Navigation
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_job_shortcode( $atts ) {

		$pairs = array(
			'show'          => 'newest', // (string) newest, (string) random, (int) Post ID
			'fields'        => 'title,content,link',
			'class'         => '',
			'content_limit' => 160,
		);

		if ( Matador::setting( 'show_job_meta' ) ) {
			$pairs['fields'] = 'title,info,content,link';
		}

		$atts = shortcode_atts( $pairs, $atts, 'matador_job' );

		if ( $atts['fields'] === $pairs['fields'] ) {
			$atts['class'] .= ' matador-job-shortcode-default';
		}

		return Template_Support::get_job( $atts );
	}

	/**
	 * Job Field Shortcode
	 *
	 * Retrieve data from a specific job field.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 * @since  3.4.0 added 'job' attribute
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_job_field_shortcode( $atts ) {

		if ( ! is_array( $atts ) || ! array_key_exists( 'name', $atts ) ) {
			if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
				_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot use the [matador_job_field] shortcode without a "field" argument.', 'matador-jobs' ), '[matador_job_field]' ), '3.0.0' );
			}
			return false;
		}

		$atts = shortcode_atts( array(
			'name'   => null,
			'id'     => null,
			'job'    => null,
			'before' => '',
			'after'  => '',
			'class'  => '',
		), $atts, 'matador_job_field' );

		return Template_Support::the_job_field( $atts['name'], $atts['id'], $atts, 'shortcode' );
	}

	/**
	 * Jobs Shortcode
	 *
	 * Retrieves many jobs, formatted based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 * @since  3.5.0 add 'paginate', 'jobs_to_show', 'backfill' parameters
	 * @since  3.5.0 deprecated 'limit' and 'minimum' parameters
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_jobs_shortcode( $atts ) {

		// Default Pairs
		$pairs = array(
			'as'            => 'listing',
			'fields'        => 'title,content,link',
			'content_limit' => null,
			'jobs_to_show'  => null,
			'paginate'      => true,
			'backfill'      => null,
			'search'        => null,
			'selected'      => null,
			'multi'         => null,
			'id'            => null,
			'class'         => 'matador-jobs-shortcode',
			'limit'         => null, // Deprecated 3.5.0, use 'jobs_to_show'
			'minimum'       => null, // Deprecated 3.5.0, use 'backfill'
		);

		if ( Matador::setting( 'show_job_meta' ) ) {
			$pairs['fields'] = 'title,info,content,link';
		}

		if ( isset( $atts['as'] ) && 'table' === $atts['as'] ) {
			$pairs['fields'] = 'title|' . __( 'Job Title', 'matador-jobs' ) . ',link|';
		}

		// Dynamically Support Taxonomy Args
		foreach ( array_keys( (array) Matador::variable( 'job_taxonomies' ) ) as $taxonomy ) {
			$pairs[ $taxonomy ] = null;
		}

		// Parse User Input with Default Pairs
		$atts = shortcode_atts( $pairs, $atts, 'matador_jobs' );

		if ( $atts['fields'] === $pairs['fields'] ) {
			$atts['class'] .= ' matador-jobs-listing-default';
		}

		return Template_Support::get_jobs( $atts );
	}

	/**
	 * Jobs List Shortcode
	 *
	 * Retrieves many jobs, formatted as a list and based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.4.0
	 * @since  3.5.0 add 'paginate', 'jobs_to_show', 'backfill' parameters
	 * @since  3.5.0 deprecated 'limit' and 'minimum' parameters
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_jobs_list_shortcode( $atts ) {

		// Default Pairs
		$pairs = array(
			'content_limit' => null,
			'jobs_to_show'  => null,
			'paginate'      => true,
			'backfill'      => null,
			'search'        => null,
			'selected'      => null,
			'multi'         => null,
			'id'            => null,
			'class'         => 'matador-jobs-shortcode',
			'limit'         => null, // Deprecated 3.5.0, use 'jobs_to_show'
			'minimum'       => null, // Deprecated 3.5.0, use 'backfill'
		);

		// Dynamically Support Taxonomy Args
		foreach ( array_keys( (array) Matador::variable( 'job_taxonomies' ) ) as $taxonomy ) {
			$pairs[ $taxonomy ] = null;
		}

		// Parse User Input with Default Pairs
		$atts = shortcode_atts( $pairs, $atts, 'matador_jobs_list' );

		$atts['as'] = 'list';

		return Template_Support::get_jobs( $atts );
	}

	/**
	 * Jobs Table Shortcode
	 *
	 * Retrieves many jobs, formatted in a table based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.4.0
	 * @since  3.5.0 add 'paginate', 'jobs_to_show', 'backfill' parameters
	 * @since  3.5.0 deprecated 'limit' and 'minimum' parameters
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_jobs_table_shortcode( $atts ) {

		// Default Pairs
		$pairs = array(
			'fields'        => 'title,content,link',
			'content_limit' => null,
			'jobs_to_show'  => null,
			'paginate'      => true,
			'backfill'      => null,
			'search'        => null,
			'selected'      => null,
			'multi'         => null,
			'id'            => null,
			'class'         => 'matador-jobs-shortcode',
			'limit'         => null, // Deprecated 3.5.0, use 'jobs_to_show'
			'minimum'       => null, // Deprecated 3.5.0, use 'backfill'
		);

		// Dynamically Support Taxonomy Args
		foreach ( array_keys( (array) Matador::variable( 'job_taxonomies' ) ) as $taxonomy ) {
			$pairs[ $taxonomy ] = null;
		}

		// Parse User Input with Default Pairs
		$atts = shortcode_atts( $pairs, $atts, 'matador_jobs_table' );

		$atts['as'] = 'table';

		if ( $atts['fields'] === $pairs['fields'] ) {
			$atts['class'] .= ' matador-jobs-listing-default';
		}

		return Template_Support::get_jobs( $atts );
	}

	/**
	 * Jobs Listing Shortcode
	 *
	 * Retrieves many jobs, formatted in a listing based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.4.0
	 * @since  3.5.0 add 'paginate', 'jobs_to_show', 'backfill' parameters
	 * @since  3.5.0 deprecated 'limit' and 'minimum' parameters
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_jobs_listing_shortcode( $atts ) {

		// Default Pairs
		$pairs = array(
			'fields'        => 'title|' . __( 'Job Title', 'matador-jobs' ) . ',link|',
			'content_limit' => null,
			'jobs_to_show'  => null,
			'paginate'      => true,
			'backfill'      => null,
			'search'        => null,
			'selected'      => null,
			'multi'         => null,
			'id'            => null,
			'class'         => 'matador-jobs-shortcode',
			'limit'         => null, // Deprecated 3.5.0, use 'jobs_to_show'
			'minimum'       => null, // Deprecated 3.5.0, use 'backfill'
		);

		if ( Matador::setting( 'show_job_meta' ) ) {
			$pairs['fields'] = 'title,info,content,link';
		}

		// Dynamically Support Taxonomy Args
		foreach ( array_keys( (array) Matador::variable( 'job_taxonomies' ) ) as $taxonomy ) {
			$pairs[ $taxonomy ] = null;
		}

		// Parse User Input with Default Pairs
		$atts = shortcode_atts( $pairs, $atts, 'matador_jobs_listing' );

		$atts['as'] = 'listing';

		if ( $atts['fields'] === $pairs['fields'] ) {
			$atts['class'] .= ' matador-jobs-listing-default';
		}

		return Template_Support::get_jobs( $atts );
	}

	/**
	 * Taxonomy Shortcode
	 *
	 * Retrieves jobs locations, formatting based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 * @since  3.3.0 added 'hide_empty' and 'orderby' parameters
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_taxonomy_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'tax'             => null,
			'taxonomy'        => null,
			'as'              => 'list',
			'method'          => 'link',
			'multi'           => false,
			'show_all_option' => false,
			'orderby'         => 'name',
			'hide_empty'      => true,
			'class'           => null,
		), $atts );

		return Template_Support::taxonomy( $atts );
	}

	/**
	 * Categories Shortcode
	 *
	 * Shortcut for [matador_taxonomy tax=category] that retrieves jobs categories terms, formatted based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 *
	 * @param  array $atts
	 *
	 * @return string formatted HTML jobs
	 */
	public static function matador_category_shortcode( $atts ) {
		if ( ! in_array( 'category', array_keys( (array) Matador::variable( 'job_taxonomies' ) ), true ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot use the [matador_categories] shortcode because the Category taxonomy is not available.', 'matador-jobs' ), '[matador_categories]' ), '3.0.0' );

			return false;
		}

		return self::matador_taxonomy_shortcode( $atts );
	}

	/**
	 * Locations Shortcode
	 *
	 * Shortcut for [matador_taxonomy tax=location] that retrieves jobs locations terms, formatting based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_location_shortcode( $atts ) {
		if ( ! in_array( 'type', array_keys( (array) Matador::variable( 'job_taxonomies' ) ), true ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot use the [matador_locations] shortcode because the Location taxonomy is not available.', 'matador-jobs' ), '[matador_locations]' ), '3.0.0' );

			return false;
		}

		$atts['tax'] = 'location';

		return self::matador_taxonomy_shortcode( $atts );
	}

	/**
	 * Types Shortcode
	 *
	 * Shortcut for [matador_taxonomy tax=type] that retrieves jobs types terms, formatting based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_types_shortcode( $atts ) {
		if ( ! in_array( 'type', array_keys( (array) Matador::variable( 'job_taxonomies' ) ), true ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot use the [matador_types] shortcode because the Type taxonomy is not available.', 'matador-jobs' ), '[matador_types]' ), '3.0.0' );

			return false;
		}

		$atts['tax'] = 'type';

		return self::matador_taxonomy_shortcode( $atts );
	}

	/**
	 * Search Form Shortcode
	 *
	 * Retrieves a search form, fields and formatting based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_search_form_shortcode( $atts = array() ) {
		$atts = shortcode_atts( array(
			'fields' => 'keyword',
			'class'  => null,
		), $atts );

		return Template_Support::search( $atts );
	}

	/**
	 * Application Form Shortcode
	 *
	 * Retrieves an application form, fields and formatting based on arguments.
	 *
	 * @access public
	 * @static
	 * @since  3.0.0
	 *
	 * @param  array $atts
	 *
	 * @return string formatted html
	 */
	public static function matador_application_shortcode( $atts = array() ) {
		if ( ! (bool) Matador::setting( 'applications_accept' ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot use the [matador_application] shortcode if you\'ve disabled Application Processing.', 'matador-jobs' ), '[matador_application]' ), '3.0.0' );

			return false;
		}

		$atts = shortcode_atts( array(
			'fields'    => null,
			'require'   => null,
			'wpid'      => null,
			'bhid'      => null,
			'class'     => null,
			'shortcode' => true,
		), $atts );

		return Template_Support::application( $atts );
	}
}
