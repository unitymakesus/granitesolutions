<?php
/**
 * Admin Template : Settings
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

<div class="wrap">

	<?php $settings_fields = Settings_Fields::instance()->get_settings_fields_with_structure( 'settings' ); ?>

	<h1 class="matador-settings-page-title">
		<?php echo esc_html( apply_filters( 'matador_settings_page_title', esc_html__( 'Matador Settings', 'matador-jobs' ) ) ); ?>
	</h1>

	<h2 class="matador-nav-tabs nav-tab-wrapper">

		<?php

		$tab_index = 1;

		foreach ( Settings_Fields::instance()->get_settings_tabs() as $tab => $label ) {

			/**
			 * Dynamic Filter: Modify the tab label.
			 *
			 * @since   3.0.0
			 *
			 * @param   string $label Tab Label
			 */
			$label = apply_filters( 'matador_settings_tab_label' . $tab, esc_attr( $label ) );

			$active = ( 1 === $tab_index ++ ) ? 'nav-tab-active' : '';

			echo wp_kses_post( '<a href="#matador-settings-tab-' . sanitize_key( $tab ) . '" class="nav-tab ' . $active . ' ">' . $label . '</a>' );
		}

		unset( $tab_index );

		?>

	</h2>

	<form method="post" id="general_options_form" class="matador-settings-form">

		<?php

		wp_nonce_field( Matador::variable( 'options', 'nonce' ) );

		$tab_index = 1;
		foreach ( $settings_fields as $tab => $sections ) {

			?>


			<div id="matador-settings-tab-<?php echo esc_attr( $tab ); ?>"
				class="matador-settings-tab tab-container"
				<?php echo ( 1 === $tab_index ++ ) ? '' : 'style="display:none"'; ?> >

				<?php
				/**
				 * Dynamic Action: Adds content before first tab sections.
				 *
				 * @since   3.0.0
				 */
				do_action( "matador_settings_before_tab_$tab" );

				foreach ( $sections[1] as $section => $fields ) {

					?>

					<div id="matador-settings-section-<?php echo esc_attr( $section ); ?>" class="matador-settings-section">


						<h4 class="matador-settings-section-title matador-settings-section-title-<?php echo esc_attr( $section ); ?>">
							<?php
							/**
							 * Dynamic Filter: Modify the title of the section.
							 *
							 * @since   3.0.0
							 *
							 * @param   string $fields first item in array is General Tab Title
							 */
							echo esc_html( apply_filters( 'matador_settings_section_title_' . $section, $fields[0] ) );
							?>
						</h4>

						<?php
						/**
						 * Dynamic Action: Add content before the fields of the section.
						 *
						 * @since   3.0.0
						 */
						do_action( "matador_settings_section_before_$section" );

						foreach ( $fields as $field => $args ) {

							if ( is_array( $args ) ) {

								list( $args, $template ) = Options::form_field_args( $args, $field );

								Template_Support::get_template_part( 'field', $template, $args, 'form-fields', true, true );

							}
						}

						/**
						 * Dynamic Action: Add content before the fields of the section.
						 *
						 * @since   3.0.0
						 */
						do_action( 'matador_settings_section_after_' . $section );

						?>

					</div>

					<?php

				} // End foreach().

				/**
				 * Dynamic Action: Adds content after last tab sections.
				 *
				 * @since   3.0.0
				 */
				do_action( 'matador_settings_after_tab_' . $tab );

				?>

			</div>

			<?php

		} // End foreach().

		unset( $i );

		?>

		<input type="hidden" value="1" name="admin_notices">
		<input type="submit" name="general_options_submit" id="general-options-form-submit" class="button button-primary" value="<?php echo esc_html__( 'Save Changes', 'matador-jobs' ); ?>">

	</form>

</div>
