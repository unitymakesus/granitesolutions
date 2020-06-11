<?php

namespace App;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
  // Enqueue files for child theme (which include the core assets as imports)
  wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);
  wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);

  // Set array of theme customizations for JS
  wp_localize_script( 'sage/main.js', 'simple_options', array('fonts' => get_theme_mod('theme_fonts'), 'colors' => get_theme_mod('theme_color')) );
}, 100);

/**
 * REMOVE WP EMOJI
 */
 remove_action('wp_head', 'print_emoji_detection_script', 7);
 remove_action('wp_print_styles', 'print_emoji_styles');
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Enable plugins to manage the document title
 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
 */
add_theme_support('title-tag');

/**
 * Register navigation menus
 * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
 */
register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'sage'),
    'social_links' => __('Social Links', 'sage')
]);

/**
 * Enable post thumbnails
 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
 */
add_theme_support('post-thumbnails');

/**
 * Enable HTML5 markup support
 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
 */
add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

/**
 * Enable selective refresh for widgets in customizer
 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
 */
add_theme_support('customize-selective-refresh-widgets');

/**
* Add support for Gutenberg.
*
* @link https://wordpress.org/gutenberg/handbook/reference/theme-support/
*/
add_theme_support( 'align-wide' );
add_theme_support( 'disable-custom-colors' );
add_theme_support( 'wp-block-styles' );

/**
 * Enqueue editor styles for Gutenberg
 */
// function simple_editor_styles() {
//   wp_enqueue_style( 'simple-gutenberg-style', asset_path('styles/main.css') );
// }
// add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\simple_editor_styles' );

/**
 * Add image quality
 */
add_filter('jpeg_quality', function($arg){return 100;});

/**
 * Enable logo uploader in customizer
 */
add_image_size('simple-logo', 200, 200, false);
add_image_size('simple-logo-2x', 400, 400, false);
add_theme_support('custom-logo', array(
  'size' => 'simple-logo-2x'
));

/**
 * Set image sizes
 */
update_option( 'thumbnail_size_w', 300 );
update_option( 'thumbnail_size_h', 300 );
update_option( 'thumbnail_crop', 1 );
update_option( 'medium_size_w', 600 );
update_option( 'medium_size_h', 600 );
add_image_size('tiny-thumbnail', 80, 80, true);
add_image_size('small-thumbnail', 150, 150, true);
add_image_size('medium-square-thumbnail', 400, 400, true);


add_filter( 'image_size_names_choose', function( $sizes ) {
  return array_merge( $sizes, array(
    'tiny-thumbnail' => __( 'Tiny Thumbnail' ),
    'small-thumbnail' => __( 'Small Thumbnail' ),
    'medium-square-thumbnail' => __( 'Medium Square Thumbnail' ),
  ) );
} );

/**
 * Register sidebars
 */
// add_action('widgets_init', function () {
//   $config = [
//     'before_widget' => '<section class="widget %1$s %2$s">',
//     'after_widget'  => '</section>',
//     'before_title'  => '<h3>',
//     'after_title'   => '</h3>'
//   ];
//   register_sidebar([
//     'name'          => __('Footer-Social-Left', 'sage'),
//     'id'            => 'footer-social-left'
//   ] + $config);
//   register_sidebar([
//     'name'          => __('Footer-Social-Right', 'sage'),
//     'id'            => 'footer-social-right'
//   ] + $config);
//   register_sidebar([
//     'name'          => __('Footer-Utility-Left', 'sage'),
//     'id'            => 'footer-utility-left'
//   ] + $config);
//   register_sidebar([
//     'name'          => __('Footer-Utility-Right', 'sage'),
//     'id'            => 'footer-utility-right'
//   ] + $config);
// });


 /**
  * Case Studies Post Type
  */
  function case_studies_post() {
  $argsCase = array(
    'labels' => array(
				'name' => 'Case Studies',
				'singular_name' => 'Case Study',
				'add_new' => 'Add New',
				'add_new_item' => 'Add New Case Study',
				'edit' => 'Edit',
				'edit_item' => 'Edit Case Study',
				'new_item' => 'New Case Study',
				'view_item' => 'View Case Study',
				'search_items' => 'Search Case Studies',
				'not_found' =>  'Nothing found in the Database.',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
    ),
    'public' => true,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_nav_menus' => false,
    'menu_position' => 20,
    'menu_icon' => 'dashicons-media-text',
    'capability_type' => 'page',
    'hierarchical' => false,
    'show_in_rest' => true,
    'supports' => array(
      'title',
      'editor',
      'revisions',
      'page-attributes',
      'thumbnail'
    ),
    'has_archive' => false,
    'rewrite' => array(
      'slug' => 'case-studies',
      'with_front' => false
    )
  );
  register_post_type( 'case-studies', $argsCase );
}
add_action( 'init', __NAMESPACE__.'\\case_studies_post' );

function case_studies_tax() {

	$argsCaseStudies = array(
		'labels' => array(
			'name' => __( 'Types' ),
			'singular_name' => __( 'Type' )
		),
		'publicly_queryable' => true,
		'show_ui' => true,
    'show_admin_column' => true,
		'show_in_nav_menus' => false,
		'hierarchical' => true,
		'rewrite' => false
	);
	register_taxonomy('case-studies-category', 'case-studies', $argsCaseStudies);

}
add_action( 'init', __NAMESPACE__.'\\case_studies_tax' );



/**
 * Resources Post Type
 */
 function resources_post() {
 $argsResources = array(
   'labels' => array(
       'name' => 'Resources',
       'singular_name' => 'Resource',
       'add_new' => 'Add New',
       'add_new_item' => 'Add New Resource',
       'edit' => 'Edit',
       'edit_item' => 'Edit Resource',
       'new_item' => 'New Resource',
       'view_item' => 'View Resource',
       'search_items' => 'Search Resources',
       'not_found' =>  'Nothing found in the Database.',
       'not_found_in_trash' => 'Nothing found in Trash',
       'parent_item_colon' => ''
   ),
   'public' => true,
   'exclude_from_search' => false,
   'publicly_queryable' => true,
   'show_ui' => true,
   'show_in_nav_menus' => false,
   'menu_position' => 20,
   'menu_icon' => 'dashicons-category',
   'capability_type' => 'page',
   'hierarchical' => false,
   'show_in_rest' => true,
   'supports' => array(
     'title',
     'editor',
     'revisions',
     'page-attributes',
     'thumbnail'
   ),
   'has_archive' => false,
   'rewrite' => array(
     'slug' => 'resources',
     'with_front' => false
   )
 );
 register_post_type( 'resources', $argsResources );
}
add_action( 'init', __NAMESPACE__.'\\resources_post' );

function resources_tax() {

 $argsResources = array(
   'labels' => array(
     'name' => __( 'Types' ),
     'singular_name' => __( 'Type' )
   ),
   'publicly_queryable' => true,
   'show_ui' => true,
   'show_admin_column' => true,
   'show_in_nav_menus' => false,
   'hierarchical' => true,
   'rewrite' => false
 );
 register_taxonomy('resources-category', 'resources', $argsResources);

}
add_action( 'init', __NAMESPACE__.'\\resources_tax' );

/**
 * ACF Local JSON
 * @source https://www.advancedcustomfields.com/resources/local-json/
 */
add_filter('acf/settings/save_json', function() {
    return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function($paths) {
    if (is_child_theme()) {
        $paths[] = get_template_directory() . '/acf-json';
    }

    return $paths;
});

/**
 * ACF Theme Options
 */
add_action('acf/init', function() {
    if (function_exists('acf_add_options_sub_page')) {
        acf_add_options_sub_page([
            'page_title' 	=> __('Custom Matador Jobs Settings', 'sage'),
            'menu_title'	=> __('Custom Settings', 'sage'),
            'parent_slug' 	=> 'edit.php?post_type=matador-job-listings',
            'capability'	=> 'edit_posts',
            'redirect'		=> false,
        ]);
    }
});

/**
 * Override link text in Matador Jobs navigation.
 */
add_filter('matador_template_the_job_navigation_buttons', function($html) {
    if (isset($html['jobs'])) {
        $html['jobs'] = esc_html__('View all opportunities', 'sage');
    }

    return $html;
}, 99, 1);

/**
 * Provide query argument for Job Application form field dynamic population.
 */
add_filter('matador_template_the_job_apply_link', function($url, $id, $context) {
    $job_id = get_post_meta($id, 'bullhorn_job_id', true);
    $job_apply_page = get_field('job_application_page', 'option');

    if (!empty($job_id) && !empty($job_apply_page)) {
        $url = add_query_arg('job_id', $job_id, get_the_permalink($job_apply_page));
    }

    return $url;
}, 10, 3);

/**
 * Load in all Matador Jobs to Job Application Gravity Form (the job_id query arg will pre-select the choice user made).
 */
function gform_populate_matador_jobs($form) {
    foreach ($form['fields'] as &$field) {
        // We only want to populate the job listing select field.
        if ($field->id != 6) {
            continue;
        }

        $jobs = get_posts([
            'post_type'   => 'matador-job-listings',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields'      => 'ids',
        ]);

        $choices = [];

        foreach ($jobs as $job) {
            $label = get_the_title($job);

            if ($job_location = get_post_meta($job, 'job_general_location', true)) {
                $label .= " (${job_location})";
            }

            $choices[] = [
                'text'  => $label,
                'value' => get_post_meta($job, 'bullhorn_job_id', true),
            ];
        }

        $field->placeholder = __('Select a job opportunity', 'sage');
        $field->choices = $choices;
    }

    return $form;
}
add_filter('gform_pre_render_1', __NAMESPACE__.'\\gform_populate_matador_jobs');
add_filter('gform_pre_validation_1', __NAMESPACE__.'\\gform_populate_matador_jobs');
add_filter('gform_pre_submission_filter_1', __NAMESPACE__.'\\gform_populate_matador_jobs');
add_filter('gform_admin_pre_render_1', __NAMESPACE__.'\\gform_populate_matador_jobs');
