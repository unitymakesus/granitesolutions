<?php
/**
 * Admin Template Part : Field Bullhorn API Connect
 *
 * Template for the special settings field called 'bullhorn_api_connect'. This template can not overridden.
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs
 * @subpackage  Admin/Templates/Parts
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2017 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

/**
 * Defined before include:
 * @var string $label
 * @var string $sublabel
 * @var string $name
 * @var mixed  $value
 * @var array  $attributes
 */
?>

<div class="<?php matador_build_classes( $class ); ?>">

	<div class="matador-label">

		<?php if ( $label ) : ?>
			<h5 class="matador-field-label">
				<label>
					<?php echo esc_html( $label ); ?>
				</label>
			</h5>
		<?php endif; ?>
		<?php if ( $sublabel ) : ?>
			<h6 class="matador-field-sublabel"><label>
					<?php echo esc_html( $sublabel ); ?>
				</label></h6>
		<?php endif; ?>

	</div>
	<div class="matador-field">

		<div class="matador-field-description">

			<?php $is_connected = matador\Matador::setting( 'bullhorn_api_is_connected' ) ?: false; ?>

			<?php if ( $is_connected ) : ?>

				<div class="callout callout-success">
					<p>
						<?php
						esc_html_e( '
						Your site is connected to Bullhorn!
						', 'matador-jobs' );
						?>
					</p>
				</div>

			<?php else : ?>

				<div class="callout callout-error">
					<p>
						<?php
						esc_html_e( '
						Your site is not connected to Bullhorn. Use the Connection Assistant to connect.
						', 'matador-jobs' );
						?>
					</p>
				</div>

			<?php endif; ?>

			<input id="matador_action" type="hidden" name="matador_action" value="" />

			<?php

			$format = '<button type="button" id="%1$s" class="%2$s"> %3$s</button> ';

			printf( $format, 'connect_to_bullhorn', 'button button-primary', esc_html__( 'Connection Assistant', 'matador-jobs' ) );

			if ( $is_connected ) {
			    $format = '<button type="button" id="%1$s" class="%2$s" style="display: inline-block; position: relative; padding-left: 26px;"><img src="https://app.bullhornstaffing.com/assets/images/circle-bull.png" height="16px" style="position:absolute; top: 5px; left: 4px;  height: 16px" /> %3$s</button> ';
				printf( $format, 'sync', 'sync button', esc_html__( 'Manual Sync', 'matador-jobs' ) );
				if ( isset( $_GET['adv-sync'] ) ) {
					printf( $format, 'sync-tax', 'sync', esc_html__( 'Sync Just Tax', 'matador-jobs' ) );
					printf( $format, 'sync-jobs', 'sync', esc_html__( 'Sync Just Jobs', 'matador-jobs' ) );
				}
			}

			?>

		</div>

	</div>

</div>

