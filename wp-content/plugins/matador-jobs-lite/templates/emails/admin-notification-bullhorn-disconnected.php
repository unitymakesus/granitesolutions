<?php
/**
 * Template: Admin Notification for Bullhorn Disconnected Email
 *
 * @link        http://matadorjobs.com/
 * @since       3.2.0
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
 * Defined before include:
 * @var string $error
 */

// @todo all this needs to be made translatable

echo
'Hi,

Your website and the Matador Jobs plugin needs your help.

We got this this error:' . esc_html( $error ) . '

We are sending you this notice because we believe you will need to manually reconnect your
site to Bullhorn in order to restore functionality. Follow these steps:

* Log into your WordPress admin.
* Click on or hover "Matador Jobs" in the sidebar, then click on "Settings" under it.
* Click on "Connection Assistant"
* Click on "Reauthorize Site"

This can be caused by a few things, including: Bullhorn servers being down, the Username and/or
Password being changed, your Client ID or Client Secret being changed, your Bullhorn account
being paused/suspended, or other causes.

If you keep getting these errors and need help, know that Matador Jobs Pro support is available
for Matador Jobs Pro and All-Access customers. http://matadorjobs.com/support

Thank You!

Matador Team
';
