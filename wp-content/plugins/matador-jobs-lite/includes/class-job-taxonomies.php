<?php
/**
 * Matador Job Taxonomies
 *
 * A class to contain functions and helpers that register taxonomies for the Job Listing.
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs
 * @subpackage  Core
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2017 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

namespace matador;

/**
 * Class Job Taxonomies
 *
 * @final
 *
 * @since  3.0.0
 */
final class Job_Taxonomies {

	/**
	 * Class Constructor
	 *
	 * @access public
	 * @since  3.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	/**
	 * Taxonomy Constructor
	 *
	 * @access public
	 * @since  3.0.0
	 */
	public function register_taxonomies() {
		foreach ( Matador::variable( 'job_taxonomies' ) as $key => $taxonomy ) {
			register_taxonomy( $taxonomy['key'], Matador::variable( 'post_type_key_job_listing' ), self::taxonomy_args( $key, $taxonomy ) );
		}
	}

	/**
	 * Taxonomy Args
	 *
	 * A taxonomy args are super important. While the key info for a taxonomy
	 * can be passed via the variable array (and filtered there), here is where
	 * we compare the variable array (if any) to the WP required defaults and
	 * build a complete new taxonomy. Before the array is passed back, it is
	 * filtered again, giving extension builders many ways to affect the construction
	 * of a taxonomy in Matador.
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 *
	 * @param string $key
	 * @param array $taxonomy
	 *
	 * @return array
	 */
	private static function taxonomy_args( $key, $taxonomy ) {

		if ( ! $taxonomy ) {
			die( 'Internal Error: Taxonomies::taxonomy_args called improperly without arguments.' );
			//throw new Exception( 'internal', 'Internal Error: Taxonomies::taxonomy_args called improperly without arguments.' );
		}

		$slug = isset( $taxonomy['slug'] ) ? $taxonomy['slug'] : $taxonomy['key'];

		$default_args = array(
			'labels'             => self::taxonomy_labels( $key, $taxonomy['single'], $taxonomy['plural'] ),
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'show_in_nav_menus'  => false,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'show_admin_column'  => false,
			'show_in_rest'       => true,
			'hierarchical'       => false,
			'rewrite'            => self::taxonomy_rewrites( $key, $slug ),
			'sort'               => 'None',
		);

		$variable_args = ( ! empty( $taxonomy['args'] ) ) ? $taxonomy['args'] : array();

		$args = wp_parse_args( $variable_args, $default_args );

		/**
		 * Dynamic Filter: Taxonomy Args
		 *
		 * @since 3.0.0
		 *
		 * @param array $args
		 *
		 * @return array
		 */
		$args = apply_filters( "matador_taxonomy_args_{$key}", $args );

		/**
		 * Filter: Taxonomy Args
		 *
		 * @since 3.3.0
		 *
		 * @param array $args
		 * @param string $key
		 *
		 * @return array
		 */
		$args = apply_filters( 'matador_taxonomy_args', $args, $key );

		return $args;
	}

	/**
	 * Taxonomy Labels
	 *
	 * Uses the passed args to generate default taxonomy labels.
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 *
	 * @param string $key
	 * @param string $singular
	 * @param string $plural
	 *
	 * @return array
	 */
	private static function taxonomy_labels( $key, $singular, $plural ) {
		if ( ! $singular || ! $plural ) {
			die( 'Internal Error: Taxonomies::taxonomy_labels called without arguments.' );
			//throw new Exception( 'internal', 'Internal Error: Taxonomies::taxonomy_labels called without arguments.' );
		}

		$labels = array(
			// Translators: Placeholder for Uppercased Taxonomy Name (Plural)
			'name'                       => esc_html( sprintf( _x( 'Job %1$s', 'Uppercased Taxonomy Name Plural, eg: "Job Categories"', 'matador-jobs' ), ucfirst( $plural ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Singular)
			'singular_name'              => esc_html( sprintf( _x( 'Job %1$s', 'Uppercased Taxonomy Name Singular, eg: "Job Category"', 'matador-jobs' ), ucfirst( $singular ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Plural)
			'menu_name'                  => esc_html( sprintf( __( 'Job %1$s', 'matador-jobs' ), ucfirst( $plural ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Plural)
			'all_items'                  => esc_html( sprintf( __( 'All %1$s', 'matador-jobs' ), ucfirst( $plural ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Singular)
			'edit_item'                  => esc_html( sprintf( __( 'Edit Job %1$s', 'matador-jobs' ), ucfirst( $singular ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Singular)
			'view_item'                  => esc_html( sprintf( __( 'View Job %1$s', 'matador-jobs' ), ucfirst( $singular ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Singular)
			'update_item'                => esc_html( sprintf( __( 'Update Job %1$s', 'matador-jobs' ), ucfirst( $singular ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Singular)
			'add_new_item'               => esc_html( sprintf( __( 'Add New %1$s', 'matador-jobs' ), ucfirst( $singular ) ) ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Singular)
			'new_item_name'              => esc_html( sprintf( __( 'New %1$s Name', 'matador-jobs' ), ucfirst( $singular ) ) ),
			'separate_items_with_commas' => esc_html__( 'Separate each with commas', 'matador-jobs' ),
			'add_or_remove_items'        => esc_html__( 'Add or Remove', 'matador-jobs' ),
			// Translators: Placeholder for Taxonomy Name (Plural)
			'choose_from_most_used'      => esc_html( sprintf( __( 'Choose from the most common %1$s', 'matador-jobs' ), $plural ) ),
			'popular_items'              => esc_html__( 'Popular', 'matador-jobs' ),
			'search_items'               => esc_html__( 'Search', 'matador-jobs' ),
			// Translators: Placeholder for Uppercased Taxonomy Name (Plural)
			'not_found'                  => esc_html( sprintf( __( 'No %1$s Found', 'matador-jobs' ), ucfirst( $plural ) ) ),
		);

		/**
		 * Dynamic Filter: Taxonomy Labels
		 *
		 * @since 3.0.0
		 *
		 * @param array $args
		 *
		 * @return array
		 */
		$labels = apply_filters( "matador_taxonomy_labels_{$key}", $labels );

		/**
		 * Filter: Taxonomy Labels
		 *
		 * @since 3.3.0
		 *
		 * @param array $args
		 * @param string $key
		 *
		 * @return array
		 */
		$labels = apply_filters( 'matador_taxonomy_labels', $labels, $key );

		return $labels;
	}

	/**
	 * Build Taxonomy Labels
	 *
	 * Uses the passed labels to generate all default taxonomy rewrite rules.
	 *
	 * @access private
	 * @static
	 * @since 3.0.0
	 *
	 * @param string $key
	 * @param $slug
	 *
	 * @return array
	 */
	private static function taxonomy_rewrites( $key, $slug ) {

		if ( ! $key || ! $slug ) {
			die( 'Internal Error: Taxonomies::taxonomy_labels called without arguments.' );
			//throw new Exception( 'internal', 'Internal Error: Taxonomies::taxonomy_labels called without arguments.' );
		}

		/**
		 * Filter: Rewrites Taxonomy "Has Front"
		 *
		 * Filters the "front" of the rewrite for the taxonomy. Default is {$job_slug}/
		 *
		 * @var bool
		 * @var string $key
		 *
		 * @since   3.3.0
		 */
		if ( apply_filters( 'matador_rewrites_taxonomy_has_front', true, $key ) ) {
			$front = Matador::variable( 'post_type_slug_job_listing' );
		}

		$front = isset( $front ) ? trailingslashit( $front ) : '';

		$rewrites = array(
			'slug'         => $front . $slug,
			'with_front'   => false,
			'hierarchical' => false,
		);

		/**
		 * Dynamic Filter: Rewrites Taxonomy Args
		 *
		 * Filters the array of args passed to the 'rewrite' argument of register_taxonomy()
		 *
		 * @since   3.3.0
		 *
		 * @var array
		 * @var string $key
		 */
		$rewrites = apply_filters( "matador_rewrites_taxonomy_{$key}", $rewrites );

		/**
		 * Filter: Rewrites Taxonomy Args
		 *
		 * Filters the array of args passed to the 'rewrite' argument of register_taxonomy()
		 *
		 * @since   3.3.0
		 *
		 * @var array
		 * @var string $key
		 */
		$rewrites = apply_filters( 'matador_rewrites_taxonomy', $rewrites, $key );

		return $rewrites;
	}

	/**
	 * @return array
	 */
	public static function registered_taxonomies() {
		return array_keys( Matador::variable( 'job_taxonomies' ) );
	}
}
