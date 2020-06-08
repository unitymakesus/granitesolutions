<?php
/**
 * Admin Template : Bullhorn Connection Assistant Doctor
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs
 * @subpackage  Admin/Templates
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2017 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

namespace matador;

?>

<header>
	<h4><?php esc_html_e( 'Bullhorn Connection Settings and Tools', 'matador-jobs' ); ?></h4>
</header>

<div>

	<?php // IS CONNECTED CHECK ?>

	<?php if ( Matador::setting( 'bullhorn_api_is_connected' ) ) : ?>

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
				Your site is not connected to Bullhorn. Re-connect by clicking on the "Authorize" button below. 
				', 'matador-jobs' );
				?>
			</p>
		</div>

	<?php endif; ?>

	<?php // IS WHITELISTED CHECK ?>

	<?php

	$api_redirect_uri = Admin_Tasks::is_uri_redirect_invalid();

	if ( 'null_url' === $api_redirect_uri ) :

		?>

		<div class="callout callout-warning">
			<p>
				<?php
				esc_html_e( '
				Your site is set to a null redirect URL, which is not recommended and useful only
				in developer mode for advanced users. Matador cannot check if your URI Redirect is
				set.
				', 'matador-jobs' );
				?>
			</p>
		</div>

	<?php elseif ( 'invalid' === $api_redirect_uri ) : ?>

		<div class="callout callout-error">
			<p>
				<?php esc_html_e( "Callback URI is not on Bullhorn's Redirect URI whitelist.", 'matador-jobs' ); ?>
				<a href="<?php esc_url( Bullhorn_Connection_Assistant::get_url( 'callback' ) ); ?>"><?php esc_html_e( 'Troubleshoot.', 'matador-jobs' ); ?></a>
			</p>
		</div>

	<?php elseif ( 'indeterminate' === $api_redirect_uri ) : ?>

		<div class="callout callout-warning">
			<p>
				<?php esc_html_e( 'Matador is unable to check for a valid Callback URI. This is sometimes caused by a missing or invalid Client ID or Client Secret. Other causes are logged in the Matador Event Log. This may or may not signify an issue with your site.', 'matador-jobs' ); ?>
				<a href="<?php esc_url( Bullhorn_Connection_Assistant::get_url( 'callback' ) ); ?>"><?php esc_html_e( 'Troubleshoot.', 'matador-jobs' ); ?></a>
			</p>
		</div>

	<?php endif; ?>

	<?php // IS ABLE TO AUTO RECONNECT CHECK ?>

	<?php if ( 'valid' === $api_redirect_uri && Matador::setting( 'bullhorn_api_user' ) && Matador::setting( 'bullhorn_api_pass' ) ) : ?>

		<div class="callout callout-success">
			<p>
				<?php
				esc_html_e( '
				Your site is set up for automatic reconnect attempts.
				', 'matador-jobs' );
				?>
			</p>
		</div>

	<?php else : ?>

		<div class="callout callout-warning">
			<p>
				<?php
				esc_html_e( '
				Your site is not able to attempt automatic reconnects. Automatic reconnects require an API User and Password and valid callback URI.
				', 'matador-jobs' );
				?>
			</p>
		</div>

	<?php endif; ?>

	<h4><?php esc_html_e( 'Bullhorn Connection Actions', 'matador-jobs' ); ?></h4>

	<?php
	$args = array(
		'name'       => 'matador-action',
		'value'      => 'authorize',
		'class'      => 'button-secondary',
		'label'      => __( 'Authorize Site', 'matador-jobs' ),
	);
	Template_Support::get_template_part( 'field', 'button', $args, 'form-fields', true, true );

	$args = array(
		'name'       => 'matador-action',
		'value'      => 'deauthorize',
		'class'      => 'button-secondary',
		'label'      => __( 'Deauthorize Site', 'matador-jobs' ),
	);
	Template_Support::get_template_part( 'field', 'button', $args, 'form-fields', true, true );

	$args = array(
		'name'       => 'matador-action',
		'value'      => 'test-reconnect',
		'class'      => 'button-secondary',
		'label'      => __( 'Test Auto Reconnect', 'matador-jobs' ),
	);
	Template_Support::get_template_part( 'field', 'button', $args, 'form-fields', true, true );

	$args = array(
		'name'       => 'matador-action',
		'value'      => 'reset-assistant',
		'class'      => 'button-secondary',
		'label'      => __( 'Reset Assistant', 'matador-jobs' ),
		'novalidate' => true,
	);
	Template_Support::get_template_part( 'field', 'button', $args, 'form-fields', true, true );

	?>

	<h4><?php esc_html_e( 'Edit Bullhorn Connection Settings', 'matador-jobs' ); ?></h4>

	<?php

	$fields = array( 'bullhorn_api_client', 'bullhorn_api_secret', 'bullhorn_api_user', 'bullhorn_api_pass', 'bullhorn_api_center' );

	foreach ( $fields as $field ) {

		$field_args = Settings_Fields::instance()->get_field( $field );

		if ( is_array( $field_args ) ) {

			list( $args, $template ) = Options::form_field_args( $field_args, $field );

			Template_Support::get_template_part( 'field', $template, $args, 'form-fields', true, true );

		}
	}

	?>


</div>

<footer>
	<button type="submit" class="button-primary">
		<?php esc_html_e( 'Save Changes', 'matador-jobs' ); ?>
	</button>
	<button type="submit" name="exit" class="button-primary exit-connection-assistant">
		<?php esc_html_e( 'Save & Exit', 'matador-jobs' ); ?>
	</button>
</footer>
