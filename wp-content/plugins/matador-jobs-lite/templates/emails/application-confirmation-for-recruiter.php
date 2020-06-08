<?php
/**
 * Template: Recruiter Email
 *
 * Override this theme by copying it to yourtheme/matador/email-recruiter-content.php.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$contact_type = ( ! empty( $contact_type ) ) ? $contact_type : null;
$type         = ( ! empty( $type ) ) ? $type : null;

// Build the message

if ( empty( $post_content ) ) {

	$message = '';
	$message .= ( ! empty( $referrer_fullname ) ) ? '<p>' . esc_html__( 'Referrer Name', 'matador-jobs' ) . ': ' . $referrer_fullname . ' </p>' . PHP_EOL : '';
	$message .= ( ! empty( $referrer_email ) ) ? '<p>' . esc_html__( 'Referrer Email', 'matador-jobs' ) . ': ' . $referrer_email . ' </p>' . PHP_EOL : '';
	$message .= ( ! empty( $fullname ) ) ? '<p>' . esc_html__( 'Name', 'matador-jobs' ) . ': ' . $fullname . ' </p>' . PHP_EOL : '';
	$message .= ( ! empty( $email ) ) ? '<p>' . esc_html__( 'Email', 'matador-jobs' ) . ': ' . $email . ' </p>' . PHP_EOL : '';
	$message .= ( ! empty( $phone ) ) ? '<p>' . esc_html__( 'Phone', 'matador-jobs' ) . ': ' . $phone . ' </p>' . PHP_EOL : '';
	$message .= ( ! empty( $address ) ) ? '<p>' . esc_html__( 'Address', 'matador-jobs' ) . ': ' . '<br />' . $address . ' </p>' . PHP_EOL : '';
	$message .= ( ! empty( $user_message ) ) ? '<p>' . $user_message . ' </p>' . PHP_EOL : '';
	if ( ! empty( $referrer_email ) ) {
		$message .= ( ! empty( $titles_html ) ) ? '<p>' . esc_html__( 'Jobs referred to', 'matador-jobs' ) . ': ' . '</p>' . PHP_EOL . $titles_html : '';
	} else {
		$message .= ( ! empty( $titles_html ) ) ? '<p>' . esc_html__( 'Jobs applied for', 'matador-jobs' ) . ': ' . '</p>' . PHP_EOL . $titles_html : '';
	}

	// Make sure we have a Post Content string (for filter)
	$post_content = '';
} else {
	$message = apply_filters( 'the_content', $post_content );
}

do_action( 'matador_email_recruiter_content_before', $type, $contact_type );

echo apply_filters( 'matador_email_recruiter_content', $message, array(
	'fullname'        => $fullname,
	'email'           => $email,
	'phone'           => $phone,
	'address'         => $address,
	'user_message'    => $user_message,
	'titles_html'     => $titles_html,
	'contact_type'    => $contact_type,
	'type'            => $type,
	'local_post_data' => $local_post_data,
	'post_content'    => $post_content,
) );

do_action( 'matador_email_recruiter_content_after', $type, $contact_type );