<?php
/**
 * Template: Applicant Email
 *
 * Override this template in your theme by copying it to yourtheme/matador/email-applicant-content.php.
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs
 * @subpackage  Templates
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   (c) 2017 Jeremy Scott, Paul Bearne
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

namespace matador;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Build the message
$message = '';
$message .= ( ! empty( $fullname ) ) ? '<p>' . esc_html__( 'Your Name', 'matador-jobs' ) . ': ' . $fullname . '</p>' . PHP_EOL : '';
$message .= ( ! empty( $email ) ) ? '<p>' . esc_html__( 'Your Email', 'matador-jobs' ) . ': ' . $email . '</p>' . PHP_EOL : '';
$message .= ( ! empty( $phone ) ) ? '<p>' . esc_html__( 'Your Phone', 'matador-jobs' ) . ': ' . $phone . '</p>' . PHP_EOL : '';
$message .= ( ! empty( $address ) ) ? '<p>' . esc_html__( 'Your Address', 'matador-jobs' ) . ': ' . '<br />' . $address . '</p>' . PHP_EOL : '';
if(  ! empty( $user_message )  ) {
	/**
	 * Matador Submit Candidate Notes Message Prefix
	 *
	 * Modify the label for the candidate message that prepends it before being saved as a note.
	 *
	 * @since 3.4.0
	 *
	 * @param string $label the text that comes before the "Message" field on a form response.
	 */
	$label = apply_filters( 'matador_submit_candidate_notes_message_label', __( 'Message: ', 'matador-jobs' ) );
	$user_message = str_replace( $label, '', $user_message );

	$message .= '<p>' . esc_html__( 'Your Message: ', 'matador-jobs' ) . ': ' . $user_message . '</p>' . PHP_EOL;
}

$message .= ( ! empty( $titles_html ) ) ? '<p>' . esc_html__( 'Jobs applied for', 'matador-jobs' ) . ': ' . '</p>' . PHP_EOL . $titles_html : '';

$contact_type = ( ! empty( $contact_type ) ) ? $contact_type : null;
$type = ( ! empty( $type ) ) ? $type : null;

do_action( 'matador_email_applicant_content_before', $type, $contact_type );

echo apply_filters( 'matador_email_applicant_content', $message, array(
	'fullname'     => $fullname,
	'email'        => $email,
	'phone'        => $phone,
	'address'      => $address,
	'user_message' => $user_message,
	'titles_html'  => $titles_html,
	'contact_type'  => $contact_type,
	'type'  => $type,
	'local_post_data'    => $local_post_data,
)  );

do_action( 'matador_email_applicant_content_after', $type, $contact_type );
