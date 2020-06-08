<?php
/**
 * Admin Template Part : Field Hidden
 *
 * Admin override hidden type settings field. This template can not overridden.
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
 * @var mixed  $value
 * @var array  $attributes
 */

printf( '<input type="hidden" id="%1$s" name="%1$s" value="%2$s" %3$s />', esc_attr( $name ), esc_attr( $value ), esc_attr( matador_build_attributes( $attributes ) ) );
