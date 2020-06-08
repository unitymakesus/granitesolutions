<?php
/**
 * Matador / Emailer
 *
 * @link        http://matadorjobs.com/
 * @since       3.0.0
 *
 * @package     Matador Jobs Board
 * @subpackage  Core
 * @author      Jeremy Scott, Paul Bearne
 * @copyright   Copyright (c) 2017, Jeremy Scott
 *
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

namespace matador;

if ( ! defined( 'ABSPATH' ) || class_exists( 'Matador_Email' ) ) {
	exit;
}

class Email {

	/**
	 * Stores the email object.
	 *
	 * @var array
	 */
	public $mail = array();

	public function __construct() {
		$this->headers();
	}

	public function headers( $headers = array() ) {
		if ( empty( $headers ) ) {
			$headers = array( sprintf( 'From: %s < %s >', apply_filters( 'matador_email_from_name', get_bloginfo( 'name' ) ), apply_filters( 'matador_email_from_email', get_option( 'admin_email' ) ) ) );
		}
		$this->mail['headers'] = $headers;
	}

	public function subject( $subject ) {
		$this->mail['subject'] = $subject;
	}

	public function recipients( $recipients ) {
		$this->mail['to'] = $recipients;
	}

	public function message( $message ) {
		$this->mail['message'] = $message;
	}

	public function attachment( $attachment ) {
		$this->mail['attachments'][] = $attachment;
	}

	public function send() {
		$success = null;
		if ( ! isset( $this->mail['attachments'] ) ) {
			$this->mail['attachments'] = array();
		}

		if ( ! empty( $this->mail['subject'] ) && ! empty( $this->mail['message'] ) ) {
			add_filter( 'wp_mail_content_type', array( __CLASS__, 'set_content_type_html' ), 22 );

			$success = wp_mail( $this->mail['to'], $this->mail['subject'], $this->mail['message'], $this->mail['headers'], $this->mail['attachments'] );

			remove_filter( 'wp_mail_content_type', array( __CLASS__, 'set_content_type_html' ) );
		}

		return $success;
	}

	/**
	 * Application Notification
	 *
	 * @static
	 *
	 * @param $wp_id
	 * @param $local_post_data @todo rename $local_post_data to $application, because that is what it is
	 *
	 * @todo move out of this function
	 */
	public static function application_notification( $wp_id, $local_post_data ) {

		if ( ! $wp_id ) {
			return;
		}

		// get the info from the form
		$address_fields = array( 'address1', 'address2', 'city', 'state', 'zip' );

		$fullname     = ( isset( $local_post_data['name']['fullName'] ) ) ? trim( sanitize_text_field( wp_unslash( $local_post_data['name']['fullName'] ) ) ) : null;
		$email        = ( isset( $local_post_data['email'] ) ) ? trim( sanitize_email( wp_unslash( $local_post_data['email'] ) ) ) : null;
		$phone        = ( isset( $local_post_data['phone'] ) ) ? trim( sanitize_text_field( wp_unslash( $local_post_data['phone'] ) ) ) : null;
		$jobs         = ( isset( $local_post_data['jobs'] ) ) ? $local_post_data['jobs'] : null;
		$user_message = ( isset( $local_post_data['message'] ) ) ? trim( sanitize_post( wp_unslash( $local_post_data['message'] ) ) ) : null;
		$job_title    = '';
		$titles_html  = '';
		$address      = '';

		foreach ( $address_fields as $field ) {
			$address .= ( isset( $local_post_data[ $field ] ) && ! empty( $local_post_data[ $field ] ) ) ? trim( sanitize_text_field( wp_unslash( $local_post_data[ $field ] ) ) ) . '<br />' : '';
		}

		if ( null !== $jobs ) {
			foreach ( $jobs as $job ) {
				$positions[] = $job['title'];
			}
		}

		if ( null !== $jobs && ! empty( $jobs ) ) {
			$job_title    = isset( $jobs[0]['title'] ) ? $jobs[0]['title'] . ' (bhid:' . absint( $jobs[0]['bhid'] ) . ')' : '';
			$titles_html .= '<ul>';
			foreach ( $jobs as $t ) {
				$titles_html .= '<li>' . esc_html( $t['title'] ) . ' (bhid:' . absint( $t['bhid'] ) . ')</li>';
			}
			$titles_html .= '</ul>';
		}

		if ( '1' === Matador::setting( 'notify_recruiter' ) ) {
			$mailer = new Email();

			/**
			 * Filter: Matador Application Confirmation Recruiter From
			 *
			 * Modify the "From" name and email address for the Application Confirmation for Recruiters email.
			 *
			 * @since 3.4.0, replaces 'matador_recruiter_email_header'
			 *
			 * @var string $from
			 * @var array  $local_post_data
			 */
			$mailer->headers( apply_filters( 'matador_application_confirmation_recruiter_from',
				array( sprintf( 'From: New Submission on the %s Website < %s >', get_bloginfo( 'name' ), get_bloginfo( 'admin_email' ) ) ),
				$local_post_data
			) );

			// Who are we going to send this form too
			if ( Matador::setting( 'recruiter_email' ) ) {
				$recipients = Matador::setting( 'recruiter_email' );
			} else {
				$recipients = '';
			}

			$notify_type = Matador::setting( 'notify_type' );
			// loop the jobs and look for assigned user meta and get email if set
			if ( is_array( $notify_type ) ) {
				foreach ( $jobs as $job ) {
					if ( in_array( 'assignedUsers', $notify_type, true ) ) {
						$assigned_users = get_post_meta( $job['wpid'], 'assignedUsers', true );
						if ( ! empty( $assigned_users->data ) ) {
							foreach ( $assigned_users->data as $assigned_user ) {
								if ( is_email( $assigned_user->email ) ) {
									if ( $recipients ) {
										$recipients .= ', ';
									}
									$recipients .= $assigned_user->email;
								}
							}
						}
					}
					if ( in_array( 'responseUser', $notify_type, true ) ) {
						$owner = get_post_meta( $job['wpid'], 'responseUser', true );
						if ( ! empty( $owner ) ) {
							if ( is_email( $owner->email ) ) {
								if ( $recipients ) {
									$recipients .= ', ';
								}
								$recipients .= $owner->email;
							}
						}
					}
					if ( in_array( 'owner', $notify_type, true ) ) {
						$owner = get_post_meta( $job['wpid'], 'owner', true );
						if ( ! empty( $owner ) ) {
							if ( is_email( $owner->email ) ) {
								if ( $recipients ) {
									$recipients .= ', ';
								}
								$recipients .= $owner->email;
							}
						}
					}
				}
			}

			/**
			 * Filter: Matador Application Confirmation Recruiter Recipients
			 *
			 * Modify the string of recipients for the Application Confirmation for Recruiters email.
			 *
			 * @since 3.4.0, replaces 'matador_recruiter_email_recipients'
			 *
			 * @var array $recipients
			 * @var array $local_post_data
			 */
			$mailer->recipients( apply_filters( 'matador_application_confirmation_recruiter_recipients', $recipients, $local_post_data ) );

			// The email subject
			if ( '' === $job_title ) {
				$subject = sprintf(
					// Translators: %1$s is the term used for CV, %2$s is Site Title
					__( 'New %1$s submitted to the %2$s Website', 'matador-jobs' ),
					Helper::resume_or_cv(),
					get_bloginfo( 'name' )
				);
			} else {
				// Translators: %1$s is Job Title
				$subject = sprintf( __( 'Submission Notification for %1$s', 'matador-jobs' ), $job_title );
			}

			/**
			 * Filter: Matador Application Confirmation Recruiter Subject
			 *
			 * Modify the email subject for the Application Confirmation for Recruiters email.
			 *
			 * @since 3.4.0, replaces 'matador_recruiter_email_subject_no_title' & 'matador_recruiter_email_subject'
			 *
			 * @var string $subject
			 * @var array  $local_post_data
			 * @var string $job_title
			 *
			 * @return string
			 */
			$mailer->subject( apply_filters( 'matador_application_confirmation_recruiter_subject', $subject, $local_post_data, $job_title ) );

			$message = matador_get_template( 'application-confirmation-for-recruiter.php', array(
				'fullname'        => $fullname,
				'email'           => $email,
				'phone'           => $phone,
				'address'         => $address,
				'user_message'    => $user_message,
				'titles_html'     => $titles_html,
				'local_post_data' => $local_post_data,
				'post_content'    => get_post_field( 'post_content', $wp_id ),
			), 'emails' );

			// Add the message
			$mailer->message( $message );

			// Attachments
			if ( isset( $local_post_data['files'] ) && ! empty( $local_post_data['files'] ) ) {

				foreach ( $local_post_data['files'] as $file ) {

					if ( file_exists( $file['path'] ) ) {
						$mailer->attachment( $file['path'] );
					}
				}
			}

			switch ( $mailer->send() ) {
				case null:
					new Event_Log( 'email-send-missing-subject-or-message-recruiter', __( 'Email to recruiter failed: missing subject or message', 'matador-jobs' ) );
					break;
				case false:
					new Event_Log( 'email-send-failed-recruiter', __( 'Email to recruiter failed:', 'matador-jobs' ) );
					break;
				case true:
					new Event_Log( 'email-send-success-recruiter', __( 'Email sent to recruiters: ', 'matador-jobs' ) . $recipients );
					break;
			}

			unset( $mailer );
		}

		if ( '1' === Matador::setting( 'notify_applicant' ) ) {
			$mailer = new Email();

			/**
			 * Filter: Matador Application Confirmation Candidate From
			 *
			 * Modify the "From" name and email address for the Application Confirmation for Candidates email.
			 *
			 * @since 3.4.0, replaces 'matador_applicant_email_header'
			 *
			 * @var string $from
			 * @var array  $local_post_data
			 */
			$mailer->headers( apply_filters( 'matador_application_confirmation_candidate_from',
				array( 'From: ' . get_bloginfo( 'name' ) . ' Website <' . get_bloginfo( 'admin_email' ) . '>' ),
				$local_post_data
			) );

			/**
			 * Filter: Matador Application Confirmation Candidate Recipients
			 *
			 * Modify the string of recipients for the Application Confirmation for candidate email.
			 *
			 * @since 3.4.0, replaces 'matador_applicant_email_recipients'
			 *
			 * @var array $recipients
			 * @var array $local_post_data
			 */
			$mailer->recipients( apply_filters( 'matador_applicant_email_recipients', $email, $local_post_data ) );

			if ( null !== $jobs && ! empty( $jobs ) ) {
				$job_title   = isset( $jobs[0]['title'] ) ? $jobs[0]['title'] : '';
				$titles_html = '<ul>';
				foreach ( $jobs as $t ) {
					$titles_html .= '<li>' . esc_html( $t['title'] ) . '</li>';
				}
				$titles_html .= '</ul>';
			}

			// The email subject
			if ( '' === $job_title ) {
				$subject = sprintf(
					// Translators: %1$s is the term used for CV, %2$s is Site Title
					__( '%2$s, Thank you for your %1$s', 'matador-jobs' ),
					Helper::resume_or_cv(),
					$local_post_data['name']['fullName']
				);
			} else {
				// Translators: %1$s is Job Title
				$subject = sprintf( __( 'Copy of your application for %1$s', 'matador-jobs' ), $job_title );
			}

			/**
			 * Filter: Matador Application Confirmation Candidate Subject
			 *
			 * Modify the email subject for the Application Confirmation for Candidates email.
			 *
			 * @since 3.4.0, replaces 'matador_applicant_email_subject_no_title' & 'matador_applicant_email_subject'
			 *
			 * @var string $subject
			 * @var array  $local_post_data
			 * @var string $job_title
			 *
			 * @return string
			 */
			$mailer->subject( apply_filters( 'matador_application_confirmation_candidate_subject', $subject, $local_post_data, $job_title ) );

			$template_args = array(
				'fullname'        => $fullname,
				'email'           => $email,
				'phone'           => $phone,
				'address'         => $address,
				'user_message'    => $user_message,
				'titles_html'     => $titles_html,
				'local_post_data' => $local_post_data,
			);

			$message = Template_Support::get_template( 'application-confirmation-for-candidate.php', $template_args, 'emails' );

			// Add the message
			$mailer->message( $message );

			switch ( $mailer->send() ) {
				case null:
					new Event_Log( 'applicant-email-send-missing-subject-or-message', __( 'Applicant Email failed: missing subject or message', 'matador-jobs' ) );
					break;
				case false:
					new Event_Log( 'applicant-email-send-failed', __( 'Applicant Email failed:', 'matador-jobs' ) );
					break;
				case true:
					new Event_Log( 'applicant-email-send-success', __( 'Applicant Email sent', 'matador-jobs' ) );
					break;
			}
		} // End if().

		unset( $mailer );

	}

	public static function set_content_type_html() {
		return 'text/html';
	}

	/**
	 * sends an email with error to site admin when we are broken
	 *
	 * @static
	 *
	 * @param $error
	 * @todo move out of this function
	 */
	public static function admin_error_notification( $error ) {

		if ( '0' === Matador::setting( 'notify_admin' ) ) {

			new Event_Log( 'email-send-error-notification-diablied', __( 'Email to Admin with error is disabled but called:', 'matador-jobs' ) );

			return;
		}

		$throttled = get_transient( Matador::variable( 'admin_email_timeout', 'transients' ) );

		if ( false !== $throttled ) {

			new Event_Log( 'email-send-error-notification-throttled', __( 'Email to Admin with error is throttled but called:', 'matador-jobs' ) );

			return;
		}

		$mail = new Email();

		//set the form headers
		$mail->headers( apply_filters( 'matador_error_notification_email_header', array( 'From: ' . get_bloginfo( 'name' ) . ' Website <' . get_bloginfo( 'admin_email' ) . '>' ) ) );

		if ( Matador::setting( 'admin_email' ) ) {
			$recipients = Matador::setting( 'admin_email' );
		} else {
			$recipients = get_bloginfo( 'admin_email' );
		}

		$mail->recipients( apply_filters( 'matador_error_notification_email_recipients', $recipients ) );

		$message = Template_Support::get_template( 'admin-notification-bullhorn-disconnected.php', array( 'error' => $error ), 'email' );

		// Add the message
		$mail->message( wpautop( $message ) );

		// The email subject
		$site_name = get_bloginfo( 'name' );
		// Translators: %1$s is Site Name
		$mail->subject( sprintf( apply_filters( 'matador_error_notification_email_subject', __( 'Matador Jobs Plugin on %s has an error ', 'matador-jobs' ), $site_name ), $site_name, $error ) );

		switch ( $mail->send() ) {
			case null:
				new Event_Log( 'email-send-missing-subject-or-message-error-notification', __( 'Email to Admin with error failed: missing subject or message', 'matador-jobs' ) );
				break;
			case false:
				new Event_Log( 'email-send-failed-error-notification', __( 'Email to Admin with error failed:', 'matador-jobs' ) );
				break;
			case true:
				new Event_Log( 'email-send-success-error-notification', __( 'Email sent to Admin with error', 'matador-jobs' ) );
				set_transient( Matador::variable( 'admin_email_timeout', 'transients' ), date( 'U' ), HOUR_IN_SECONDS * 6 ); // throttle to 1 per 6 hours
				break;
		}
	}
}
