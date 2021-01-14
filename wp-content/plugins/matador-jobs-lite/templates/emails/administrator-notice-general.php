<?php
/**
 * Template: Administrator Notice (General) Message
 *
 * @link        http://matadorjobs.com/
 * @since       3.6.0
 *
 * @package     Matador Jobs
 * @subpackage  Templates / Emails
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2018 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Mustache Templating
 *
 * This template is post-processed with Mustache templating engine.
 *
 * The following variables are available to Mustache:
 *
 * - sitename
 * - error
 *
 * ** Extensions may provide additional fields. **
 *
 * To use Mustache templating:
 *
 * - Include variable inline with text in double curly braces for HTML-escaped text, eg: "Hello {{name}}!"
 * - Include variable inline with text in triple curly braces for unescaped text, eg: "Hello {{{name}}}!"
 * - Include variable array with a dot to read the array's key's value, eg: "City: {{address.city}}, {{address.state}}.
 *
 * For more help, @see https://github.com/bobthecow/mustache.php/wiki/Mustache-Tags
 */

/**
 * These PHP variables are also available to this template, defined before includes:
 *
 * @var string $sitename
 * @var string $error
 *
 * ** Extensions may provide additional fields. **
 */
?>

<p><?php esc_html_e( 'Hello Administrator', 'matador-jobs' ); ?>,</p>

<p><?php esc_html_e( 'Matador Jobs Plugin on is experiencing a connection issue on your site that require your intervention.', 'matador-jobs' ); ?></p>

<p><?php esc_html_e( 'Matador Jobs sends this email to administrators after three consecutive connection failures, and once daily thereafter.', 'matador-jobs' ); ?></p>

<p><?php esc_html_e( 'Below is a copy of the error message, which may be helpful in resolving the issue.', 'matador-jobs' ); ?></p>

<p><em>{{error}}</em></p>

<p>
	<?php esc_html_e( 'For advice on resolving connection issues, review our help documentation.', 'matador-jobs' ); ?>
	<a href="https://matadorjobs.com/support/documentation"><?php esc_html_e( 'Go to help docs.', 'matador-jobs' ); ?></a>
</p>
