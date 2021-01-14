<?php
/**
 * Template: Application Confirmation for Recruiter (Email)
 *
 * Override this theme by copying it to yourtheme/matador/application-confirmation-for-recruiter.php.
 *
 * Use this template to inject HTML and structure or override the email content.
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0 as 'email-recruiter-content'
 * @since       3.2.0 as 'application-confirmation-for-recruiter'
 *
 * @package     Matador Jobs
 * @subpackage  Templates
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2017-2020 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Mustache
 *
 * This template is post-processed with Mustache templating engine.
 *
 * The following variables are available to Mustache:
 *
 * - firstname
 * - lastname
 * - fullname
 * - email
 * - phone
 * - message
 * - sitename
 * - address (as array, use Mustache dot notation)
 * - application (as array, use Mustache dot notation)
 * - content (HTML formatted human-readable application with human-relevant fields, eg: name, not ip address)
 * - applied_jobs (HTML formatted list or applied job(s))
 *
 * ** Extensions may provide additional fields. **
 *
 * To use Mustache templating:
 *
 * - Include variable inline with text in double curly braces for HTML-escaped text, eg: "Hello {{name}}!"
 * - Include variable inline with text in triple curle braces for unescaped text, eg: "Hello {{{name}}}!"
 * - Include variable array with a dot to read the array's key's value, eg: "City: {{address.city}}, {{address.state}}.
 *
 * For more help, @see https://github.com/bobthecow/mustache.php/wiki/Mustache-Tags
 */

/**
 * These variables are available to this template via PHP, defined before includes:
 *
 * @var string $firstname
 * @var string $lastname
 * @var string $fullname
 * @var string $email
 * @var string $phone
 * @var string $message
 * @var string $post_content
 * @var string $sitename
 * @var array $address
 * @var array $application full Application Object
 *
 * ** Extensions may provide additional fields. **
 */
?>

<p><?php esc_html_e( 'Hello!', 'matador-jobs' ); ?></p>

{{#application.jobs}} <!-- If this applicant applied to a job. -->
<p>
	<?php esc_html_e( 'A new application was submitted on your website for the following role(s):', 'matador-jobs' ); ?>
</p>
{{{applied_jobs}}}
{{/application.jobs}}

{{^application.jobs}}
<p>
	<?php esc_html_e( 'A new general application was submitted on your website.', 'matador-jobs' ); ?>
</p>
{{/application.jobs}}

<p><?php esc_html_e( "Below are the applicant's application responses:", 'matador-jobs' ); ?></p>

{{{post_content}}}
