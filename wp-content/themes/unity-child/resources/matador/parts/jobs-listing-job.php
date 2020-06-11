<?php
/**
 * Template: Job Partial for Jobs Listing
 *
 * Override this theme by copying it to /wp-content/themes/{yourtheme}/matador/parts/jobs-listings-list-each.php.
 *
 * @link        http://matadorjobs.com/
 * @since       3.4.0
 *
 * @package     Matador Jobs
 * @subpackage  Templates/Parts
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2018 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Defined before include:
 * @var $index
 * @var $args
 */

$context = 'listing';

/**
 * Matador Job Before (Action)
 *
 * @since 3.0.0
 * @since 3.4.0 added params
 *
 * @param mixed  $content_length
 * @param int    $index
 */
do_action( 'matador_job_before', $context, $index );

$classes = array(
	'matador-job',
	'matador-job-' . $context,
	'matador-job-' . get_the_ID(),
	'matador-job-nth-' . $index,
	0 === $index % 2 ? 'matador-job-even' : 'matador-job-odd',
);
?>

<article class="<?php matador_build_classes( $classes ); ?>">

	<?php
	/**
	 * Matador Job Before Content
	 *
	 * After the jobs' wrapper, before the template content.
	 *
	 * @since 3.0.0
	 *
	 * @param string $context
	 */
	do_action( 'matador_job_before_content', $context );
	?>

	<header class="matador-job-header">

		<?php if ( isset( $fields['title'] ) && $fields['title'] ) : ?>
			<h2 class="entry-title matador-job-title">
				<?php matador_the_job_title_link( null, $context ); ?>
			</h2>
        <?php endif; ?>

        <?php
            $post_id = get_the_ID();
            $general_location = get_post_meta($post_id, 'job_general_location', true);
            $employment_type = get_post_meta($post_id, 'employmentType', true);
            $category = get_the_term_list($post_id, 'matador-categories', '', ', ');
        ?>

        <?php if (!empty($category) && !is_wp_error($category)) : ?>
            <div>
                <span class="screen-reader-text"><?php echo __('Job Category:', 'sage'); ?></span>
                <strong><?php echo strip_tags($category); ?></strong>
            </div>
        <?php endif; ?>

        <?php if ($general_location) : ?>
            <em><span class="screen-reader-text"><?php echo __('Job Location:', 'sage'); ?></span><?php echo $general_location; ?></em>
        <?php endif; ?>
        <?php if ($general_location && $employment_type) : ?>
            <span aria-hidden="true" style="margin: 0 0.25rem;">|</span>
        <?php endif; ?>
        <?php if ($employment_type) : ?>
            <em><span class="screen-reader-text"><?php echo __('Employment Type:', 'sage'); ?></span><?php echo $employment_type; ?></em>
        <?php endif; ?>

		<?php if ( isset( $fields['info'] ) && $fields['info'] ) : ?>
			<?php matador_the_job_info( array(), array(), $context ); ?>
		<?php else : ?>
			<?php matador_the_job_info( $fields, array(), $context ); ?>
		<?php endif; ?>

		<?php
		/**
		 * Matador Job Header
		 *
		 * After the title and the Meta Header, before </header>
		 *
		 * @since 3.4.0
		 *
		 * @param string $context
		 */
		do_action( 'matador_job_header', $context );
		?>

	</header>

	<div class="matador-job-description">
		<?php
		/**
		 * Matador Job Content
		 *
		 * After the content, if included, and before </div class='matador-job-description'>
		 *
		 * @since 3.4.0
		 *
		 * @param string $context
		 */
		do_action( 'matador_job_content', $context );
		?>

	</div>

	<footer class="matador-job-footer">
		<?php
		/**
		 * Matador Job Footer
		 *
		 * After the job navigation, if included, and before </footer>
		 *
		 * @since 3.4.0
		 *
		 * @param string $context
		 */
		do_action( 'matador_job_footer', $context );
		?>

	</footer>

	<?php
	/**
	 * Matador Job After Content
	 *
	 * @since 3.0.0
	 * @since 3.4.0 added param $context
	 *
	 * @param string $context
	 */
	do_action( 'matador_job_after_content', $context );
	?>

</article>

<?php
/**
 * Matador Job After (Action)
 *
 * @since 3.0.0
 * @since 3.4.0 added params
 *
 * @param int    $index
 * @param string $context
 */
do_action( 'matador_job_after', $context, $index );
