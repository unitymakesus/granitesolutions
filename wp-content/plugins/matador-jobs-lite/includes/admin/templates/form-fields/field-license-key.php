<?php
/**
 * Admin Template Part : Field License Key
 *
 * Template for the special settings field called 'license_key'. This template can not overridden.
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
 * @var string $name
 * @var string $label
 * @var string $sublabel
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

		<?php

		printf( '<input type="text" id="%1$s" name="%2$s" value="%3$s" %4$s />', esc_attr( $name ), esc_attr( $name ), esc_attr( $value ), esc_attr( matador_build_attributes( $attributes ) ) );

		$status_value = matador\Matador::setting( str_replace( ']', '_status', str_replace( 'matador-settings[', '', $args['name'] ) ) );
		$status = ( false !== $status_value && 'valid' === $status_value );

		?>

		<div class="matador-field-description">

			<?php if ( $status ) : ?>

				<div class="callout callout-success">
					<p>
						<?php esc_html_e( 'Your site has a valid, active Matador Jobs Pro subscription.', 'matador-jobs' ); ?>
					</p>
				</div>

			<?php elseif ( isset( $error ) ) : ?>

				<div class="callout callout-error">
					<p>
						<?php esc_html_e( 'You provided an invalid license key, or your license is expired.', 'matador-jobs' ); ?>
					</p>
				</div>

			<?php else : ?>

				<div class="callout callout-warning">
					<p>
						<?php esc_html_e( 'Enter your license key to access automatic updates and add-ons.', 'matador-jobs' ); ?>
					</p>
				</div>

			<?php endif; ?>

		</div>

	</div>

</div>
