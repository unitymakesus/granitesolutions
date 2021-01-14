=== Matador Jobs Lite ===

- Contributors: jeremyescott, pbearne
- Donate Link: https://matadorjobs.com
- Tags: Bullhorn, job board, matador, google jobs search, career portal, OSCP
- Requires at least: 4.9.6
- Tested up to: 5.5.0
- Stable tag: 3.6.4
- Version: 3.6.4
- Requires PHP: 5.6
- License: GPLv3 or later
- License URI: https://www.gnu.org/licenses/gpl-3.0.html

Connect your WordPress site with your Bullhorn account. Cache job data locally and display it with style inside your WordPress theme.

== Description ==

Connect your Bullhorn Account with your WordPress site and display your valuable jobs on your new self-hosted job board. Matador makes this as easy as it sounds, and lets you seamlessly integrate a powerful job board--a major marketing tool for your business--directly into your WordPress site. Everything that is great about WordPress is extended to Matador: great out-of-the-box SEO, easy templating/theming, endless customization options, and more. Matador goes further by listing your jobs with incredible job-specific SEO customization (optimized for Google Jobs Search), and more.

Use Matador's powerful settings to connect our "Apply Now" button for jobs to a page that will collect applications, or look into purchasing Matador Jobs Pro to accept applications from Matador and see them turned into candidates submitted to jobs directly in your Bullhorn Account!

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the '/wp-content/plugins/matador-jobs' directory, or install the plugin through the
   WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Configure the plugin by going to Matador Jobs > Settings.
1. Connect your site to Bullhorn by clicking on 'Bullhorn Connection Assistant' on the Settings Page, and following the.
   prompts.

== Frequently Asked Questions ==

= Does this Require a Bullhorn Account? =

You must have an active Bullhorn Account to use Matador's Bullhorn Import/Export features. It technically will function as a stand-alone jobs board without a Bullhorn Account, but there are better options out there for that.

= How Do I Get Bullhorn API Credentials? =

You must submit a ticket to Bullhorn support. Merely informing them you will be using Matador should give them all the info they need to help you, as we are now Bullhorn Marketplace Developer Partners and they know what a new Matador user needs. That said, we recommend first installing the plugin, activating it, and starting the Bullhorn Connection Assistant before you do this. Follow the prompts in the easy-to-use assistant and Matador will generate a copy-and-paste email you can send to Bullhorn Support to get you started.

= So Matador downloads jobs from Bullhorn. Does it accept applications too? =

Yes, if your a user of Matador Jobs Pro or All-Access. Once you've connected to Bullhorn and synced your first jobs, your visitors can apply to the jobs. Based on settings, the applications will be sent to your Bullhorn either immediately or in the next regularly scheduled sync with Bullhorn.

If you are only right now a user of the free Matador Jobs Lite, not yet. Matador Jobs lite allows you to designate a destination page for the "Apply" button, but you will need to handle your own applications, perhaps with a contact form plugin.

If you'd like more information on Matador Jobs Pro or All-Access, visit <https://matadorjobs.com/>.

= How Can I Customize the Look of Matador? =

Our documentation site <https://matadorjobs.com/support/documentation/> explains how to use our template system, theme functions, shortcodes, and actions and filters to make your site look amazing. You can also watch out for occasional client showcases on our where we feature creative and amazing looking implementations of Matador.

= How Can I Customize the Function of Matador? =

Matador is built by WordPress users for WordPress users. We included hundreds of Actions and Filters to help you customize how it works just like WordPress core. Some of those are documented at <https://matadorjobs.com/support/documentation/> while others can be discovered with a quick code review.

But that requires a developer and hours of work! If you haven't already, check out our many official extensions that are viewable at <https://matadorjobs.com/products/>. These extend Matador's core functionality in ways that can make each site feel unique! You can use an unlimited number of these All-Access Add-Ons with any Matador Jobs All-Access plan.

If you need something and you don't see an add-on, feel free to write us. Leave a comment in the Support Forum or with our Pro support system <http://www.matadorjobs.com/support/> (requires Matador Jobs Pro or All-Access). Simple modifications might already be documented and we can point you to them. And if you have a more complex modification, we may be able to take your input and idea and turn it into another All-Access Add-On.

= Where can I get support? =

Users of Matador Jobs Lite should use the plugin's WordPress.org support forum. Users of Matador Jobs Pro and All-Access annual or lifetime plans can use our support ticket system at <http://www.matadorjobs.com/support/>.

== Upgrade Notice ==

Upgrading from 'Bullhorn 2 WordPress' 2.4 or 2.5 to Matador Jobs Lite/Pro 3.0 or later includes some breaking updates. This will cause some sites to disconnect or look differently. Back up your site and perform the upgrade on a staging server if possible. We have made every effort to make this smooth, but be warned, it will require extra work to make your site function the same again.

== Screenshots ==

1. Options page - Bullhorn Import settings
2. CV/resume upload form
3. Notifications options page
4. Jobs listings in the admin

== Changelog ==

= 3.6.4 =

Features:

- Extending a feature added in 3.6.0, if a job ID is accessed directly via either external ID scheme (ie:  /jobs-page/1234 or /jobs-page/?xid=1234) and the job does not exist on the site, a sync to Bullhorn will be fired off. This is to address issues related to Bullhorn-based real-time XML feeds that present a Matador URL prior to the resource being created in Matador. In some cases, either a user, but more commonly a 3rd party aggregator, would try to access the resource, but be returned a 404 Error: Not Found. Now, if a job posting is not found, this will trigger a sync and ideally will result in the resource becoming available.
- Added filter 'matador_the_jobs_description_allowed_protocols' to allow the permitted HTTP protocols in imported job descriptions to be extended beyond WordPress's core allowed HTTP protocols. This can be used to expose your site to exploitation and should be used with absolute caution, however can be used to allow data:// and svg:// protocols in descriptions.

Bugfixes:

- Fixes issue causing employmentType in Structured Data (used by Google for Jobs Search) to not present properly in certain cases, especially when the Bullhorn data is customized. Thanks to Nishi B. from Beach Head for the bug report.
- Fixed a backward compatibility issue affecting some users of PHP 5.6.x. We apologize for the issue. Please note, PHP 5.6.x is no longer receiving security updates and is 50% slower than PHP 7.2, the current minimum supported version of PHP. Matador will continue to support PHP 5.6.x but new features will not load for users of less than PHP 7.2.
- Fixed issue affecting iOS Safari during Matador Campaign tracking load. In iOS Safari, window.external is not defined, which threw a Javascript error
- Fix issue where some API calls on certain server configurations would fail due to an extraneous forward-slash.

= 3.6.3 =

Features:

- Adds automatic detection of the "ALLOW_PRIVATE" entitlement on the candidate object. Application syncs that encounter a Private candidate will check if the API user can modify them, if not falling back to old Private Candidate routines, if so allowing the sync to carry on as normal.
- The "Sync this Job" button is automatically removed for sites that utilize developer filters that disable adding/updating jobs on a sync.

Bugfixes:

- Improved the behavior of "Sync This Job" button. Will now always sync/overwrite job data regardless of whether job was recently updated at Bullhorn.
- Improvements to existing Private Candidate application handling (when "ALLOW_PRIVATE" is false). Thank you to Nishi B for the heads up.
- Fixed an issue affecting some web hosts, including Windows-based web hosts, when loading a 3rd-party code library. Thank you Tony S for the tip.
- Fixed issue where the consent object's "name" wasn't being properly auto-detected on some user's sites. Thank you Nishi B for bringing this to our attention.
- Fixed two uncommon issues encountered when importing options for checkboxes or select fields from a Bullhorn data source (generally when using Advanced Applications Extension). These were caused when type mismatches were created during conversion of data to/from arrays in PHP/Matador and JSON/Bullhorn.
- Fixed a "typo" in a settings option. A form setting description referencing a Bullhorn job order field name was incorrect even though the setting label and setting values were as intended.

= 3.6.2 =

Bugfixes:

- Fixed an issue where the Matador Application form may not have always denied a form submission when Google reCAPTCHA was not filled out, resulting in an on-screen error message for applicants. Thank you to Doug B for the help finding this one.
- Fixed a backward compatibility handler for versions 3.0.0 and 3.4.0. A filter introduced in those versions for developers to use to customize emails was deprecated in 3.6.0's big email-related code rewrite, but was given special handling for backward-compatibility into the 3.6.0 release. It was, unfortunately, not working as intended, resulting in some sites using the older developer filters to fail. Thank you to Nishi B for pointing this out.
- Fixed an issue where the content-type header for email was not being unset after a Matador email was sent. Though this was very unlikely to have caused any issues for users, its possible some email sent by non-Matador plugins that would've sent after a Matador email would've been affected. Now the content-type filter will be removed after a Matador email sends.
- Fixed an issue where Matador's Javascript wouldn't load in some rare cases. To ensure the lightest impact to load times, Matador Javascript is only loaded when needed. A rare case was discovered where the Javascript would not be loaded for taxonomy (i.e.: category, location) drop-down menus if the page also didn't use the [matador_jobs] shortcode or function or didn't have an application. Now the javascript will load during any appearance of the drop-down.

Internationalization:

- Fixed misspelled text domain for a translatable string, meaning it is now able to translated.

Inclusive Language:

Some phrases and terms used by developers have roots in speech that was originally oppressive or exclusive in nature. Not only are these phrases rooted in world in which we, Jeremy & Paul, do not support, but in fact they are less descriptive for a layperson than using more accurate phrases. In an effort to do our small part in righting these historical wrongs and improving the clarity in which we communicate, we are working to remove instances of this language from our projects. The following changes made it into this release:

- All uses of the term "API Redirect Whitelist" (as a noun) is now to "Allowed API Redirect List"
- All uses of the term "whitelist" (as a verb) is now "register".
- You willl now see phrases like "you need to register your uri to your Allowed API Redirect List" instead of "you need to whitelist your redirect URI" or "your redirect URI is not on the whitelist."

Note 1: We'd like to thank our partners at Bullhorn for supporting this change as they undergo similar updates to their own documentation.
Note 2: Our efforts to replace insensitive language in our project with inclusive alternatives is a work in progress. We will continue to make these changes in upcoming releases.

= 3.6.1 =

Bugfixes:

- Fixed issue on PHP 7.3 & 7.4 where Matador would cause a NOTICE error, which if WP_DEBUG was set to true, would print an error to screen and interrupt Matador. Please make sure your WP_DEBUG is set to false in production (live) as a general best practice and apply updates in a staging environment. Thank you to Samantha S for the tip.
- Removed duplicated code files in Matador Jobs Lite due to build process error.

= 3.6.0 =

Features:

- Added support for Bullhorn's "Consent" Tab (customObject). If "Require Applicant to Agree to Privacy Policy" setting is on and Matador can detect the presence of the Consent custom object on the Candidate object, Matador will add consent data during Candidate create, update, and submission. In most cases, you will need to make no changes to your site to take advantage of this feature provided the object has been configured on your Bullhorn account. (Note: due to a bug, this feature may not have worked fully until 3.6.3)
- Added Schema/JSON+LD support for "Work from Home" jobs. Basic support is enabled automatically when "onSite" is set to "No Preference" or "Off-Site". If your company uses other terms to refer to "Work From Home" in your Bullhorn, you can identify these via the filter 'matador_bullhorn_import_telecommute_types'. Advanced support requires further configuration. Schema/JSON+LD support for "Work From Home" will result in job searches for the same country (or state) of the hiring organization (employer). If you'd like to narrow or widen this, you can assign custom fields in your Bullhorn Job Listing and Matador will import these. They are "type" and "value", where type may be, "COUNTRY" and value will be "USA". Use filter 'matador_bullhorn_import_location_requirements_fields' to assign these.
- Added alternate URL schemes to support other integrations. Jobs can now be found by appending the remote job id (ie: Bullhorn ID) to the jobs base URL. IE: if jobs exist at your-site.com/gigs/ and you have a job ID on Bullhorn of 1234, then your-site.com/gigs/1234 will redirect to its human-friendly URL, eg: your-site.com/gigs/job-name-1234. This is to better support 3rd party integrations, eg: with Indeed. Jobs can now also be found by appending a query string to the jobs base URL with the argument of 'xid'. IE: if jobs exist on your-site.com/gigs/ and you have a job ID on Bullhorn of 1234, then your-site.com/gigs/?xid=1234 will redirect to its human-friendly URL, eg: your-site.com/gigs/job-name-1234. For forward compatability with future support of additional job boards, you can include an additional argument of 'xsource' to verify the external source, ie: your-site.com/gigs/?xid=1234&xsource=bullhorn.
- Completely new Email-related codebase. Settings allowing you to set the default "from name" and "from email" for Matador-generated emails. New "Default Recruiter" email setting. Allows you to determine a recipient for the recruiter email when the job has no assigned users and/or the application isn't tied to a job. Mustache Templating added to email templates, to simplify templates.
- A number of improvements to the sync routine of Matador Jobs. Jobs are now not updated unless they've been updated since the last sync, improving performance by up to 70%!
- Matador's JSON+LD support is now properly merged with Yoast SEO's (WPSEO) plugin's use of the single graph.
- Added "Single Job Refresh" action to the job. Visit the job page in WordPress Admin and click on the button to refresh it immediately from its Bullhorn record without requiring a full job sync.
- Added the whitelist URI to the Connection Assistant tool, so it can be referenced without doing a reset.

Bugfixes:

- In the default template to output a select or multiselect field (via shortcode or function) for a taxonomy (eg: category, location) the field was named improperly when the multiselect option was set, resulting in the field values not being formatted properly and search results from the subsequent form submission were incorrect. This is fixed so that when a multiselect flag is set, the selected options will now properly pass values to the POST or GET request. Thank you to Emily B. of Integrity Locums for the bug report.
- Fixed an issue in Matador on PHP 7.3 where a matador_jobs()/[matador_jobs] function/shortcode argument was handled during processing in such a way that an error was raised PHP 7.3.
- The matador_application()/[matador_application] function/shortcode would not recognize Bullhorn or WordPress id passed via querystring. This is an edge case implementation of Matador designed for third-party form systems, but we made sure we can support it as well.
- In a rare instance, a custom theme's style rules could make the hidden file input field still display but with graphical error. To ensure this doesn't happen, additional CSS rules were added to make this less likely to occur, but note that custom theme CSS can always override ours, and sometimes things unexpected occur.
- When certain SMTP or other mail sending plugins were used, they may have encountered errors with duplicates of the same email being in the "to" field. The email generating code will now remove duplicate email addresses in any given field (to, cc, bcc, etc).
- Fixed issue where certain PHP versions (7.3, 7.4) could throw an error during application processing and incorrectly reject an application form submission.

Developer:

- Add support for select option groups in custom form fields by nesting arrays of options within the options array. Only supported via customized fields, as Bullhorn does not provide data in this structure.
- Added template for hidden type application form fields.
- Added filter matador_bullhorn_candidate_get_candidate_fields to modify fields retrieved during an existing candidate edit. Please see documentation block for notes and warnings.
- Added action matador_import_before_save_job_location and replaced matador_save_job_address with matador_import_after_save_job_location to assist users in creating custom experiences with location data.

Internationalization:

- Added French (Canadian) Translations File

= 3.5.7 & 3.5.8 =

- No changes in this version, but a version number increment was created to correct an error we made in uploading the wrong version of Matador Jobs Lite to the WordPress.org Plugin Repository.

= 3.5.6 =

Bugfixes:

- Added 'Content-Type:application/json' headers to all Bullhorn calls, thus modifying our API calls to match the changes made in the Bullhorn API specification in mid December 2019. This fully resolves the issue discussed in the 3.5.5 release.
- Fixed an issue causing unintended printing of the Job Info or Job Navigation blocks when using matador_get_the_description() and related functions. Unfortunately, some current users may rely on the unintended behavior that was fixed, so therefore the changes will not be applied until users complete an upgrade confirmation and/or when version 3.7.0 releases.
- Fixed an issue where the query results and pagination navigation for the shortcode and matador_get_jobs() function was not working when deployed onto the front page of a site. This was due, in part, to how WordPress handles pagination in general on the front page.
- Fixed an issue where a developer's public function (from template-functions.php) required an argument even though documentation said it was optional. Argument is now optional.
- Fixed an issue created in 3.5.0 where the 'backfill' argument of the [matador_jobs] shortcode/matador_get_jobs() function would not work as intended.

= 3.5.5 =

Bugfixes:

- Fixed an issue where application form fields of the select multiple type were not getting validation attributes during render, and thus being skipped by client-side validation.
- Fixed another issue around "skills" being parsed by the resume parser were still causing sync failures (see 3.5.4 release notes for earlier handling).
- Improved handling of errors around the Candidate update routine for when an application is submitted and Matador can find a matching candidate in the Bullhorn list.

Note:

- An issue of unknown origin, but possibly originating from the mid-December Bullhorn update, is causing all API calls that updating existing candidate records to fail. Matador is currently unable to update an existing candidate. This update contains improvements, however, to our error handling of errors of this type, as well as it gives Matador permission to skip the candidate update and finish the job/web submission and file uploads for the application.

= 3.5.4 =

Security:

- Improved sanitization for search query terms. No issue or exploit was found or used with malicious intent, but we felt we could be stronger.
- "Message" field in the application was once allowed to submit HTML, but now will strip HTML. This was previously permitted under the assumption that users would want to use the field for an HTML-resume. We properly escaped unsafe HTML, and no exploit was used by a hacker to break into a site. That said, this field was overall not being used for HTML resumes while spammers were able submit content with clickable URLs pointing to unsafe destinations, representing a security risk to recruiters who might click those links. So all HTML tags will be removed, and should a spammer submit an unsafe URL, the full url will be presented, making it easier to spot unsafe submissions. Should you need an HTML-accepting field, use a custom application fields to accomplish this, and make sure to use proper HTML escaping for your security. As always, be careful with URLs submitted by users.

Features:

- Added two new filters and replaced one via deprecation.

Bugfixes:

- Fixed an issue related to application processing where the setting for background processing was not working as intended. No users encountered any problems with this, however.
- Fixed an issue where a name submitted with multiple white spaces in the compound name input field could cause an issue with processing an application and fail during save and/or fail to find a valid existing record (thus creating a duplicate). Matador will now remove all instances of multiple whitespace and convert all to a single non-breaking space, which allows for our compound name processor to work as intended.
- Fixed an issue where an error would interrupt candidate save when certain resumes parsed by Bullhorn returned an unexpectedly formatted skills object. The skills object is now handled for all its possible combinations, and the candidate save can complete.
- Fixed an issue where security escaping was too strong for certain configurations of the search form, resulting in those forms not working as intended.
- WordPress Job Manager integration did not use Matador's JSON+LD integration, yet settings for it were still being offered to users of it. These are now removed to limit any confusion.
- WordPress Job Manager integration did not have access to new settings added in 3.5.0 around Matador's email notifications. They were added for those users.

Misc:

- Removed the WEST USA 50 cluster option from the datacenter options. Truth is, you can log in via any cluster, though some are indeed faster. Meanwhile, every attempt to log into West USA 50 failed. Since no users appear to be on West USA 50, we've never been able to test why. We are still investigating this and will restore the option when an appropriate fix is identified.
- Improved description of the settings options around Application Processing.
- Improved documentation blocks for the template-functions.php file, which is most used by theme and integration developers.

= 3.5.3 =

Bugfixes:

- In version 3.4.0, we included code to allow a user to build a custom form that let candidates select options from checkboxes or select/multiselect fields with values from one of Bullhorn's "to many" associations, eg: "category" or "skills". No users expressed interest in using the feature until recently. Upon finally having a real-world use case, we were able to work with our new users to fix some recently discovered bugs around this feature, and included them in this update.
- We recently learned that a Bullhorn user can set an existing candidate to a "private" status, which prevents Matador from updating the record or creating new Job Submissions. While we cannot override this protection granted by a "private status", we were able to create new error handling to properly notify a user when an applicant with a private status tried to apply to a position.
- A Matador application form generated dynamically (ie: inside a pop-up modal) requires a Javascript insertion of a hidden form field in order to associate the job the candidate is applying to. Previously, this hidden form field required the job's WordPress ID. Now, the association can also be made with the job's Bullhorn ID.
- We discovered and fixed and error that could result in recruiter notification emails failing to send when an "additional/backup email address" was not set.
- We were notified of an error where multiple comma-delimited taxonomy terms passed into the $$taxonomy argument for the [matador_jobs] shortcode or matador_jobs() function would not properly query the multiple taxonomy terms. Thanks to Eric C. of Visual Notion for bringing this to our attention.

Internationalization:

- We discovered and fixed an issue causing two text strings that should be translatable to be not translatable.
- We discovered and made translatable two text strings previously untranslatable.
- We updated the Netherlands Dutch translation file, following feedback from our users.

= 3.5.2 =

Bugfixes:

- Fixed javascript issue where a site that loads jQuery.validate() extensions will conflict with Matador's implementation of that library. Conflicts were resolved by name spacing the functions. Thank you to Lee H for the report.
- Fixed an issue where DOM manipulation could allow an application form to submit without the required acceptance of the Privacy Policy notice and fire an unrecoverable error during processing. In order to fix this, the offending user will now be returned to the form page to re-try. Thank you to Lee R for the report.
- Fixed an issue where an invalid email submitted in an application will be saved as an empty value to Bullhorn after a security cleans it.
- Fixed bug where admin notices reporting a sync failure would not be removed when a successful sync completes, and would stack up. Now only one will show if needed, and all will be removed upon a successful sync.
- Fixed an issue with the Matador Jobs shortcode and function where, in certain instances, an array key would be unset even though the function requires it later on. This was by design, so we added a check for the unset variable before we try to reference it.

Other:

- Fixed a spelling error in a form field label.

= 3.5.1 =

Features:

- New filter matador_data_source_status to allow a user to control the 'status' value of new candidates and job submissions. Settings currently allow you to set these as "New Lead" or "Job Submission", but using the filter you can use any number of values.

Bugfixes:

- Fixed a bug where a globally namespaced function was being called within a namespaced class causing an error in certain PHP versions.
- Fixed a bug where the Leads object (controlled by Leads All-Access Add-On) was not receiving data from the Candidate Source Tracking tool.
- Fixed a bug where Recruiter emails that had no post content were failing.
- Fixed a bug where CSS flex was being applied to the search form container and search form form object due to both having the same class name. Changed the class name of the Search Form enclosing <div> from 'matador-search-form' to 'matador-search-form-container'. The class name 'matador-search-form' was being used on both the <form> and wrapping <div> tag going back to release 3.0.0. Our new CSS in 3.5.0 for flex display of the search form was breaking on some sites. We closely considered how to fix this, as there is no fix without causing a "breaking" change. This is our solution, but please check your sites in case your CSS needs an update.

= 3.5.0 =

Tested Up To/Minimum Requirements Updates:

- Tested up to WordPress version 5.2.3
- Tested up to PHP version 7.2.18
- Now requires PHP version 7.0+

Major New Feature: Candidate Traffic Source Tracking

- Matador Jobs Pro now attempts to track and pass along campaign (traffic) data into the 'source' field of the Candidate and JobSubmission entities.
- When a user visits the site, Matador will attempt to create a cookie named matador_traffic that will store data from the referral or utm_* query string values.
- When referrer data is used, Matador will compare the referrer against a list of known social networks and search engines and designate the source as "social" or "organic" if appropriate.
- Additionally, when Matador builds a Job Application form, it will perform a similar set of checks and pass generate hidden form fields with the values.
- When saving an application, Matador will look for the presence of a cookie first, or hidden data from the form related to traffic second, and save in the application database record campaign data.
- When syncing an application to Bullhorn, Matador will append the 'source' field with campaign information.
  - Up until this point, your source would be "[WEBSITE NAME] Website". It will now look like "[WEBSITE NAME] (Google Jobs Apply/Organic/Google Jobs Apply)" or "[WEBSITE NAME] (Bing/Organic)" or "[WEBSITE NAME] (Email/April Newsletter)"
  - Bullhorn limits the source field to 200 characters for Candidates and 100 characters for JobSubmissions. To prevent syncing issues, Matador will truncate your source to that limit. If you use especially long campaign names/values, you could reach the limit easily.
  - Therefore, to give you more space to work with, Matador no longer appends ' Website' following your name.
  - If your website name is very long and causing your campaign data to be truncated often, you have two options:
    - Return true to 'matador_campaign_tracking_source_reset' to fully omit your website name, ie: "(Google Jobs Apply/Organic/Google Jobs Apply)".
    - Shorten your website name via the filter 'matador_data_source_description', ie: pass 'ACME' to the filter to change 'ACME Staffing and Recruiting' to 'ACME', a savings of 24 characters--almost 25% extra space for the JobSubmission source.
- Because UTM_* values can be set by anyone with limited knowledge of the internet, you may optionally create whitelist and check values against the whitelist at the time an application is saved so only pre-determined values can be passed to Bullhorn.
  - Return true to the 'matador_campaign_tracking_check_{$field}_against_whitelist' filter to turn on whitelisting for the field. Replace {$field} with the field you'd like to whitelist, ie: 'campaign'. You must also return an array of values to the 'matador_campaign_tracking_{$field}_whitelist' filter.
- A developer can further access the campaign data and include behaviors to save the items to customXY fields for a Candidate, add notes for the Candidate, or add comments to the JobSubmission. This is advanced and you should check with Pro Support for assistance.
- You may turn off this new feature by passing false to the filter 'matador_load_module_save_campaign_data_to_bullhorn'
- Because this feature does not add new cookies but does increase tracking of certain data of your users, you should update your Privacy Policy as is appropriate prior to updating Matador.

Features - Job & Job Imports

- Added the 'job_general_location' meta field as a new option for the 'matador_import_location_taxonomy_allowed_fields' which determines the value used in the Job Location taxonomy.
- Client Corporation data is now available for inclusion on job listings via a get_post_meta() call.
- Choose whether to determine the job category from the 'categories' in the Job Order (now intended by Bullhorn to be for internal use only) or the new 'published category' set during job publishing. Includes filters to override setting.
- Matador now adds two small bits of data to your job: Source and Source ID. One is a future looking feature, meant for a day that you can use other platforms in addition to Bullhorn on your Matador-powered website. The other is a copy of the job's remote source ID. Now, you can use filters to safely modify the Job ID's shown to the public (ie: adding 'BH' in front of them) without affecting Matador's ability to run. These are created with the job during import, so it will also help to prevent duplicate jobs issues caused when some job meta saves fail.
- Optimized an aspect of the import routine to reduce overall system memory use.
- Added a new routine to remove duplicate entries when they occur, often due to an error. Monitor your logs for notice of duplicates being found and removed, as this can be a sign of an issue elsewhere.
- Added arguments $wpid and $field to filter 'matador_import_meta_to_taxonomy_value' to extended its usefulness and functionality.

Features - Applications & Application Sync

- Application form validation now checks that uploaded files are under 1 mb / 1024 kb and are a valid extension.
- Applications from existing candidates can now trigger changes to the candidate's first name, middle name, name prefix or name suffix. This may occur if the candidate existed previously but a resume provided additional information.
- Added filters to support customizing the value saved Privacy Policy Acceptance fields, introduced in 3.4.0. By default, the values are Date/Time strings, but can now be updated to any string value, i.e.: "Yes", "True", or "1", using 'matador_submit_candidate_privacy_policy_field_value_on_create', 'matador_submit_candidate_privacy_policy_field_value_on_update', or 'matador_submit_candidate_privacy_policy_field_value'. Props to Scott R. for the feature request.
- Added 'Published Contact' as an option to the "Send Recruiter Email" setting.

Features - Template

- Added 'paginate' parameter to matador_jobs() and [matador_jobs] (and [matador_jobs_*]) shortcodes. It defaults to true to support backward compatibility. Pass "false", "off", or "no" to turn off page navigation when the job query can produce more results. (This may be considered a bugfix to some, depending on how you used the original shortcode.)
- To provide more intuitive parameters for our [matador_jobs_*] shortcodes, we renamed 'limit' to 'jobs_to_show' (to 'jobs_per_page' for the function) and 'minimum' to 'backfill'. Backward compatibility is in place to make sure old implementations will still work.
- Matador Search form fields are now wrapped in a <div>. Previously, taxonomy drop-downs were wrapped while buttons and text fields were not. This was incidental, a result of how taxonomy drop-downs were included. Now, all fields will be wrapped in a div allowing developers/designers greater control via CSS.

Bugfixes:

- Fixed an error where the Jobs Structured Data would not honor the setting '"Hiring Company" Data Source' in certain cases, resulting in the Hiring Company Name, and not the Agency Name, being included in the structured data in certain cases. Props to Phil V. for the bug report.
- Fixed issue where the new [matador_jobs_listing] and [matador_jobs_table] shortcut shortcodes were doing each other's behavior.
- Fixed an issue where the Bullhorn API would return a non-breaking warning when Matador updated an existing candidate in Bullhorn, which generally happens when an applicant applies for more than one job in the same 2-year period. This was not causing any issue at this time, but could in the future.
- Fixed issue where taxonomy drop-down menus with method set as "link" would fail to trigger page reload automatically.
- Fixed an issue where applications that accept data via a select or multi select, the email (and notes) on the submission displayed the form value, not the human-friendly form label.
- Fixed an issue where large resume file sizes (larger than 1mb) would cause a Bullhorn Candidate/Submission sync to fail. Now, sync will continue, but may fail completely later on if not enough user data was required by the form.
- Fixed an issue where job description would not properly display when using the [matador_jobs] shortcode or the matador_jobs() template function outside of a standard loop.
- Fixed an issue and generally refactored how uploads are stored on the server to fix issues when a user hosted on a Windows IIS server environment.
- Fixed an issue affecting the frequency of which local application data was automatically deleted.
- Fixed an issue causing some intended HTML to be escaped, causing a visual error, when using specific settings with some Matador Shortcodes.

Other:

- Added support for new Bullhorn regional data centers.
- Added styles for Matador Job Alerts Extension updates.

= 3.4.1 =

This release is a maintenance release to support final features included in WordPress 5.0, 5.0.1 and 5.0.2, as well as fixes bugs.

- Adjusted a few calls where wp_kses_post() was used when it wasn't necessary. Backward incompatible changes to WordPress 5.0.1 required this.
- Added filters matador_application_note_line_label and matador_application_note_line_item to modify how "note" lines are saved in the application.
- Moved JSON_LD injection from above the Job markup and into the <head>. May also have fixed some JSON LD reading/saving issues with Google.
- Added setting to allow user to define a page (ideally using shortcodes) as the jobs home page. Note: you cannot set the "Home" page as the jobs home page at this time without a possible issue around search results.
- Added tools to support handling issues some of our users were experiencing with enabling Google Indexing API.
- Update "tested up to" to 5.0.2

= 3.4.0 =

Big New Features:

Matador Jobs 3.4.0 is the biggest update to Matador yet. Its patch notes will be huge and confusing and incomplete. For this reason, please follow our blog and our help files to learn how to use the updated Matador! Here are the big deal updates:

- We now support the Google Indexing API. This matters, because it allows your site to directly notify Google whenever a new job is added or a job is removed. This is a partial support for this feature, as we'd like to soon support updates to jobs in this notification as well. To turn on Google Indexing for your jobs, you will need to get a Google Indexing API keyfile. Look in Settings -> Job Listings (tab) -> Jobs Structured Data (section) for the new settings, and a link to our help website on how to get this.
- Applications now can save the IP address of the applicant. Further, the IP as well as the date/time of the submission, can now be submitted to the candidate's record in Bullhorn as part of the candidate save. This is useful to track the most recent acceptance of terms of service and privacy policies, for example, as well as provide data points for internal analytics. See the more detailed patch notes on how to use this.
- Jobs now have a default "information" header, aka the "meta header" after the title and before the description. This will present (by default) the Job's location, type of job (ie: contract, permanent), and the Bullhorn Job ID. This is easy to customize with a little code, but styles are only applied for default setting. Existing users will need to enable this feature by turning on a setting in their settings.
- Jobs also now have a default set of navigation buttons at their end, after description. These are contextual, so will change based on your settings and depending on which page you're on. These are added to all jobs by default, so check your site to see you like the styles.
- A majority of the templates, template actions and hooks, and more have been simplified. Actions and hooks now have a second parameter called "context", which can be used to limit their scope. Where in the past, for example, the "jobs listing" template had two actions for 'before_jobs', 'matador_before_jobs' and 'matador_before_jobs_listing',now it just has one 'matador_before_jobs' with the passed 'context' of 'listing'. The action is the same for all templates, but it can be limited using the 'context' parameter. This will make customizing templates easier, but may cause issue with current custom templates and actions.
- Many, many new template functions were added to the global namespace for theme developers to access, making the customization much easier!
- New shortcut shortcodes `[matador_jobs_listing]`, `[matador_jobs_table]`, and `[matador_jobs_list]` to make it simpler to use them and provide more intuitive default settings.

Additional Features/Enhancements:

- When redirected to a job page after a successful application where a confirmation is shown (as opposed to a Thank You page), the application form will no longer show, which helps avoid confusion for the user.
- Applications now save the IP address of the computer that submitted the form.
- The IP address of the computer that submitted the form can now be submitted to the corresponding new or existing candidate record following a Candidate Submit routine by Matador. IP addresses are saved to both a "on submit" and "on update" field, and to tell Matador which custom fields on the Candidate record to use, assign them with the matador_submit_candidate_ip_field_on_create and matador_submit_candidate_ip_field_on_update filters, introduced in this version. While we recommend you use these filters to prevent any issues caused by changing these values accidentally, a free Extension was created to make an admin setting for this also if you are unable or uncomfortable writing a filter.
- If a site has the "Require Applicant to Agree to Privacy Policy" setting turned on, when Matador sends the application to Bullhorn, you can now designate a text or date custom field to save the date and time of that submission in order to track the initial and most recent acceptance of your Privacy Policy to their candidate record. Assign which fields to use with the matador_submit_candidate_privacy_policy_field_on_create and matador_submit_candidate_privacy_policy_field_on_update filters, introduced in this version. While we recommend you use these filters to prevent any issues caused by changing these values accidentally, a free Extension was created to make an admin setting for this also if you are unable or uncomfortable writing a filter. You may additionally customize the date format by using the matador_submit_candidate_privacy_policy_field_format, but this only applies when you are using a custom text, and not a custom date, type field.
- Form validation error messages can now be customized by a developer new matador_application_validation_error_{$error} filter. See `includes/class-scripts.php` for a list of errors that can be filtered.
- Option to modify the URL slug format of each job. Options are limited to three possibilities, but a filter was added to provide users methods to make more complex URL slugs. Paired with an additional field import, a user can even set the URL slug to be generated from a job custom field, among other things.
- Previously, jobs without an end date in Bullhorn would result the in the 'validThrough' field in the structured data--what Google for Jobs uses--to be blank. We found that this causes Google for Jobs to make that job a lower priority. Matador will now set a date one year from the 'job posted' for the 'validThrough' field. Note that if your firm offers ongoing positions, it is important you set an impossibly long job end date in Bullhorn to make sure your job always has a 'validThrough' date in the future.
- Added $data parameter to the 'matador_data_source_description' filter. This allows developers to access the object and any custom fields to use to modify the source. This object is different in each context, so filter functions should check for context prior to assuming object structure.
- Adds new field to job, called `job_general_location` which is customizable with the new `matador_import_job_general_location` filter. This is the field used by the default meta header.
- When an Application is deleted, either manually or automatically, prior to its delete, associated files (resumes, etc) are now also deleted. This should free up storage space on your server and close a possible privacy or security risk should server settings allow those files be available to the internet.
- Adds empty index.php files in Matador uploads folders to provide security for files when a server has Apache directory indexes turned on.
- Adds new actions 'matador_add_job', 'matador_update_job', 'matador_save_job', 'matador_transition_job', and 'matador_delete_job' to give developers easier access to changes in job posts to hook into.

Bugfixes:

- Fixed an issue where an appended Application Confirmation message would not show properly after a job application.
- Fixed an issue where pagination was not presented for shortcode-based jobs lists.
- Fixed an issue where an error was printed to screen, instead of logged to the logs, during a rare login-related issue with Bullhorn.
- Disabled "toggle" type admin settings will now show styles that communicate its disabled status.
- The deprecation notices were not showing properly for logged in users. They now will. Pay attention to them! Update you site accordingly.

Localization:

- Extended Localization options to all form validation error messages.

Deprecation:

- The pre-3.0.0 deprecated shortcodes will be removed from Matador in our next major version in January to March 2019. Please make sure you've fully migrated to the [matador_*] shortcodes.

= 3.3.7 =

Features:

- Added a bulk Application Sync button to the applications view. Will re-try all failed applications from the issue
  addressed in 3.3.6 and any failed applications for the last two weeks.
- Added a filter matador_application_batch_sync_allowed_statuses to allow you to extend the statuses included in a batch
  sync. This may come in handy in the future when we expand the statuses assigned to applications that fail.
- Added a filter matador_application_batch_sync_duration to allow you to extend of duration applied in a batch sync. If,
  for example, you want to apply a batch sync to jobs older than two weeks, you can do this via this filter.

Bugfixes:

- Fixed spelling/grammar error in the application processing overlay.
- Added escaping functions to prevent errors encountered during an application sync when Matador checks for existing
  candidates. Candidates with names that included a single quotation mark/apostrophe caused the search to be badly
  formatted and thus present an error, which in turn caused the application sync to fail altogether.

= 3.3.6 =

Features:

- Enabled sync re-try for applications with the status of "Unable to sync".
- Updated messaging around reasons that an application sync may fail.

Bugfixes:

- Updated the way a Bullhorn request was being made that was causing it to fail after the September 2018 Bullhorn ATS
  software upgrades.
- We are observing an issue on some Bullhorn users' accounts where a Bullhorn resume parse may return a badly structured
  candidate object that later results in a failure when Matador tries to create a candidate with that object. We have
  included a temporary work-around until we can help our partners at Bullhorn resolve this issue.
- Fixed an issue where a "Re-try Sync" routine on an application would result in an HTTP 430 error under certain caching
  circumstances.

Localization:

- Updated included Dutch (Netherlands) localization files.

= 3.3.5 =

Features:

- Added filters matador_import_job_description and matador_import_job_title to potentially override, append, or prepend
  a job title as it is imported from Bullhorn.
- Added filters matador_import_job_description_field and matador_import_job_title_field to use a non-standard field for
  job title or description. Standard fields are "title" and "description" or "public description".
- Updated the HTML tag filter on import so that job descriptions with images and videos will now import properly.

Bugfixes:

- Various changes were made around user-initiated syncs that fixed a few issues. Now, only one sync can occur at a time,
  which prevents a rare problem caused by two syncs running concurrently and creating duplicate jobs that Matador is not
  able to automatically expire. Also, now, each sync is performed fully in the background, which both allows an admin to
  continue other work on their site while the sync processes and also makes it so they're never presented with a browser
  timeout. Various notifications were added to explain these features so admins understand what is going on.
- Various display issues were fixed in the Application CSS to provide a better, more reliable base user experience.
- Fixed an issue that caused links to application pages, when linking to a custom page, to not work as intended (most
  often used in Lite installations).

UI:

- Added a toggle-type on/off switch to the UI for settings. Changed several settings where it was appropriate to this
  new style with the hope that it will make the experience better for users.

= 3.3.4 =

Bugfixes:

- Fixed an issue where the an anti-spam honeypot related function was named a protected function name causing issues in
  some versions of PHP, resulting in a failure when it should be valid.
- Improved Matador's error handling for invalid candidate object submissions to Bullhorn. At this time, there appears to
  be an error with Bullhorn's resume processor returning invalid candidate objects, which caused Matador to log an
  error. Unfortunately, Matador determined this was a recoverable error, which meant that it retried a failing process
  on each sync, a waste of system resources.
- Fixed a minor issue where the site name wasn't being accessed properly in Application email builder, which was used to
  generate Email Subjects when applicants applied in a form with no job assigned.
- Fixed an issue where a data attribute on the Apply button was being filtered by security features.
- Fixed an issue where an undefined privacy policy in WP 4.9.6 would result in a link being generated that had no url.
  Now the link isn't included if a privacy policy page is not set on WP 4.9.6+.
- Fixed an issue where rewrite rules were not being refreshed on upgrade/install of Matador Jobs.
- Fixed a minor issue where a hidden field in the application had two fields with the same ID attribute, which is
  improper HTML syntax, which may have caused some custom JS to function incorrectly.
- Fixed an issue where the the privacy_policy_opt_in was being appended to the "message" of the applicant confirmation
  email and in the "notes" of the Bullhorn Candidate record.

Developer:

- Filter 'matador_recruiter_email_header', introduced in 3.0.0, was deprecated and replaced with
  'matador_application_confirmation_recruiter_from' to best match the new naming conventions.
- Filter 'matador_recruiter_email_recipients', introduced in 3.0.0, was deprecated and replaced with
  'matador_application_confirmation_recruiter_recipients', to best match the new naming conventions and now accepts
  additional variable, $local_post_data.
- Filters 'matador_recruiter_email_subject' and 'matador_recruiter_email_subject_no_title', introduced in 3.0.0, were
  deprecated and replaced with 'matador_application_confirmation_recruiter_subject', to best match the new naming
  conventions and now accepts three inputs, $subject, $local_post_data, & $job_title.
- Filter 'matador_applicant_email_header', introduced in 3.0.0, was deprecated and replaced with
  'matador_application_confirmation_candidate_from' to best match the new naming conventions.
- Filter 'matador_applicant_email_recipients', introduced in 3.0.0, was deprecated and replaced with
  'matador_application_confirmation_candidate_recipients' to best match the new naming conventions and now accepts
  additional variable, $local_post_data.
- Filters 'matador_applicant_email_subject' and 'matador_applicant_email_subject_no_title', introduced in 3.0.0, were
  deprecated and replaced with 'matador_application_confirmation_candidate_subject', to best match the new naming
  conventions and now accepts three inputs, $subject, $local_post_data, & $job_title.

= 3.3.3 =

Bugfixes:

- Fixed an issue where the 'Sync Now' button on the Admin Job Listings Page didn't show if there were no jobs in the
  database, which is when we want and need that button the most.
- Fixed an issue where [matador_search] shortcode and matador_search() functions that included taxonomy fields were not
  working because the all option was passing a value of '_all' when the args were not set to ignore them.

Developer:

- Settings Sanitizer 'number_list' extracted from the Import by Client Extension and added to core.

= 3.3.2 =

Bugfixes:

- Fixed an issue where [matador_taxonomy] shortcode (and its shortcuts) or matador_taxonomy() function with the
  parameter 'method' is set to 'list' (which is the default) had poorly generated links that did not work.
- Fixed an issue where multiple versions of Matador, ie: Lite & Pro, would conflict with each other when both active in
  the same WordPress instance. Now, all versions can be activated, but whichever loads first in WordPress will be loaded
  until unneeded versions are deactivated.

= 3.3.1 =

Bugfixes:

- Fixed an issue where a site sending application notifications without having "AssignedUsers" or "Owners" checked could
  have applicants presented with an error.

= 3.3.0 =

Features:

- Added an anti-spam behavior to the Application form. You may need to update your settings by visiting Matador Jobs >
  Settings in your WordPress Admin, clicking on the Applications tab, scrolling down to "Use Anti-Spam Honey Pot", and
  setting it to "On".
- Added two features to prevent duplicate Application forms submissions.
  - First, applications will now have their submit button disabled after a user clicks the button and the client-side
    validation passes, allowing only one submission per click.
  - An overlay with a loading "spinner" will also be added over the form to give a strong visual indication to the user
    the form is processing. This may be styled with CSS.
- Added a 'hide_empty' and 'orderby' parameter for the [matador_taxonomy] shortcode and matador_taxonomy() function. Now
  you can choose to list categories, locations, etc even if they don't have jobs and you can choose how to order them,
  like, our favorite, by number of jobs in the taxonomy. This option now works on the alias versions of shortcode too:
  [matador_categories], [matador_locations], and [matador_types].
- Added a new allowed value for the [matador_search] shortcode and matador_search() function parameter 'fields'. It now
  accepts 'reset', and when passed, will add a Reset Search button to the output.

Bugfixes:

- Fixed an issue where the "show_all_option" parameter of the [matador_taxonomies] and matador_taxonomies() function was
  not always resulting in an "All Categories" like link added to the list.
- Fixed an issue where translatable strings were used in dynamic filter names and filter arguments in class
  Job_Taxonomies, which may have caused some sites using non-US English difficulty in customizing output.
- Fixed issue where templates moved/renamed in 3.2.0 were not given proper backward compatibility handling concerns that
  should've been in place before 3.2.0 was released. Apologies to all affected sites, and thanks to Andre, who reported
  the bug.
- Fixed an issue where if a site chose to leave the "Additional Emails to Notify" option blank for Application
  Notifications, the site Admin Email would be included. In the past, this behavior was default and correct, but not
  since 3.2.0's recruiter-based email notifications feature was released. Thanks to Jason and Rich for reporting the
  bug.
- Added a class .matador-screen-reader-text in CSS and applied it to all places where we add previously used the global
  class .screen-reader-text. This wasn't a per se 'bug', but it caused a lot of issues for sites whose themes did not
  implement the WordPress recommended class in the theme, resulting in sites that had extra text in awkward places and
  confused users trying to figure out how to remove it. That said, your theme should implement and use classes like
  .screen-reader-text to help make your sites more welcoming to users who are blind or hard of hearing, just from now
  on, we'll assume you don't.

UI:

- Matador developers are developers, so sometimes we don't explain things very well. We got feedback that the new
  settings around structured data in 3.1.0 were confusing and didn't make sense. We changed the order of the settings,
  revised or rewrote their descriptions, and hopefully made everything much easier to understand.

Accessibility:

- Added Screen Reader content to the [matador_search] and [matador_taxonomy] shortcodes and the matador_search() and
  matador_taxonomy() functions to improve readability.

Developer:

- Added a filter named 'matador_rewrites_taxonomy_has_front', which when returned false will disable the inclusion of
  the jobs slug before the taxonomy slug.
- Added filters named 'matador_rewrites_taxonomy' and 'matador_rewrites_taxonomy_$key', which allows modification of the
  'rewrites' array in the taxonomy declaration. Replaces deprecated filter 'matador_taxonomies_rewrites_$key'.
- Added a filter named 'matador_taxonomy_labels', which allows for manipulation of the 'labels' array in a taxonomy
  declaration. Does not replace 'matador_taxonomy_labels_$key'.
- Added a filter named 'matador_taxonomy_args', which allows modification of the whole $args array in a taxonomy
  declaration. Does not replace 'matador_taxonomy_args_$key'.
- Filter 'matador_bullhorn_source_description', introduced in 3.2.1, was renamed to 'matador_data_source_description'
  and is now applied to three variables, up from the original 1. A 2nd argument for the filter, which is optional,
  was clarified in documentation as the 'entity'. Warning: There is no deprecation handling for this change.
- Added an action and filter that run before the Application Handler begins processing raw data. The action,
  'matador_application_handler_start' fires immediately after nonce is verified and can be used to do further before
  processing verifications, like check a captcha or an anti-spam honeypot. The new filter,
  'matador_application_handler_start_ignored_fields' lets developers add fields that might have been added to the form
  for processing in the the aforementioned action be added to the ignored fields list, which ensures the processor's
  catch-all at the end of the form processor doesn't pick them up and include them in the job submission as a 'note'.
- Added a filter named 'matador_get_template_print_name_comment', which, when passed a true value, prints an HTML
  comment before the output of the template with the template's path. This will help developers determine which template
  is being loaded by Matador for easier template overrides.
- Added a filter named 'matador_locate_template_additional_directories'. This allows extension developers to add their
  template directories to be checked by Matador's template loader. The template loader checks these directories after it
  failed to find a template in core, so this won't override a core template.
- Added a filter named 'matador_locate_template_replace_default'. This allows extension developers to replace a core
  template with their own without taking away the important ability to override a template in the user's theme.
- Rewrote and simplified actions, filters, and the templates for the [matador_taxonomy] shortcode and matador_taxonomy()
  function. Templates were broken into parts for easier use and customization. Some actions/filters were deprecated in
  favor of new, more robust options. Deprecation handling was added for sites that implemented these old actions and
  filters.
- Rewrote and simplified actions, filters, and the templates for the [matador_search] shortcode and matador_search()
  function. Template was broken into parts for easier use and customization. Actions/filters were added to allow for
  easier customization.

Misc:

- Based on feedback acquired from a Bullhorn support ticket, modified the text of the Bullhorn Support email generator
  in the connection assistant "Callback URI" step to now include the ClientID. This is in case a Bullhorn account has
  more than one set of API credentials.

= 3.2.1 =

Features:

- Modified how "Notes" items are labeled when saved from an Application. Now should use the registered field's label
  instead of a label generated from the form field key.

Bugfixes:

- Fixed an issue discovered that caused "Messages" and custom fields to not be saved to the Bullhorn "Notes" for the
  candidate.

= 3.2.0 =

Features:

- New Template Helper matador_get_job_terms for getting a array of terms from 1 or all Matador taxonomies.
- New Template Helper matador_get_job_terms_list for getting a formatted string of terms from a job's taxonomy in
  various formats. Uses a new template 'job-terms-list.php' and introduces 6 new actions (matador_job_terms_list_before,
  matador_job_terms_list_before_terms, matador_job_terms_list_before_each, matador_job_terms_list_after_each,
  matador_job_terms_list_after_terms, and matador_job_terms_list_after) plus one new filter
  (matador_job_terms_list_separator ) to support customization.
- Added filter 'matador_bullhorn_source_description' to allow users to customize how Matador lists the "Source" of the
  job submission.
- Added filter 'matador_recruiter_email_subject_no_title' to allow for adjusting of subject sent to recruiter if not
  linked to a job.
- Improved the behavior of matador_is_current_term() to also return true when on a taxonomy archive page.
- New Template file for Admin Notifications when Bullhorn Connection needs user intervention.

Bugfixes:

- Fixed a major issue where certain candidate and recruiter notifications were not being sent when they should've.
- Added code to prevent conflicts when a vendor library is loaded by another plugin or theme after Matador loads. This
  changes makes existing conflict prevention methods more robust and fool-proof.
- Fixed bug where, when called directly, matador_the_job_field could throw an error if certain optional arguments were
  not passed.
- Fixed a bug where, in the default jobs-taxonomies-lists.php template, a class for the term markup was being made with
  the title and not slug, creating invalid HTML and not useful classes.
- Fixed a bug where, in Settings, a checkbox type settings wouldn't save when all checkboxes are unchecked.
- Fixed a bug where, when both a Candidate and Recruiter email were being sent the Recruiter email listed the job twice.

UI:

- Added the Bullhorn Bull icon to the "Manual Sync" button in settings, providing a visual cue to users that this action
  communicates with Bullhorn.
- Removed check marks next to checkboxes. Because that didn't make any sense.
- Revised wording around the "Classify Applicants" setting and added a long description explaining its impact on a
  Candidate and Job Submission, hoping to add further clarity to how the setting affects the workflow of various users'
  firms.

Misc:

- Added a (hopefully temporary) advisory to the Authorize step of the Connection Assistant to warn users of a known
  issue when logging in for the first time. In the future, we hope to resolve this issue with our Bullhorn partners.
- Moved templates for emails from /templates/ into /templates/emails. Renamed the templates. Some clients with
  customized email templates may want to double check if this impacted them, but given these templates were only
  available for all for less than two weeks, we feel safe making this adjustment now.
- Refined error logging messages during certain Bullhorn login attempts.
- Fixed spelling error on an Application Form Field
- Fixed spelling/grammar errors & formatting in Readme

= 3.1.0 =

Notice:

- This update will require a manual install. All Plus and Pro subscriptions and Pro Lifetime purchasers will receive a
  free manual upgrade by either Paul or Jeremy. The reason for this is that our plugin folder name will change to
  reflect a change-of-name for Matador Jobs Lite on WordPress.org. The folder name for "Lite" will now be 'matador-jobs'
  and the folder name for Matador Jobs Pro will be 'matador-jobs-pro'.

Features:

- Added new settings to support GDPR compliance, specifically the ability to require an acknowledgement of a Privacy
  Statement upon application, and the ability for admins to force the erasure of local candidate data. We will defer to
  Bullhorn's GDPR compliance tools to assist with final export of user data and erasure/anonymization.
- Jobs import now includes the email address of the "Job Owner" and "Assigned Users", and site admins can designate
  email notifications be sent to both the owner and/or the assigned users.
- Added numerous improvements and features to the Bullhorn API Assistant Wizard, including: a Client ID validator, a
  better Redirect URI validator, a fix to an issue caused by site transfers (like from staging to live) where the site
  wouldn't check for a new whitelisted Redirect URI until a cache expired up to 24 hours later, a check that determines
  if "Pretty Permalinks" were set, which is required for Authorization and caused some users confusion on fresh
  installs, a "skip assistant" option for advanced users which skips the Wizard and goes straight to the summary, and
  more.
- Added more interactive buttons in many UIs. A button now activates sync from the jobs listing page, a button now links
  to the Bullhorn job (for logged in users) from the job page, and a button links to the Bullhorn candidate (for logged
  in users) from the applicant page after the applicant is synced (and provided they are not deleted immediately).
- Added options surrounding structured data. Specifically, allows a site operator to choose whether to show base pay or
  not, and whether to use company data from Bullhorn or website data (name/url) for the Hiring Organization. The site
  operator may also now disable structured data, for instance in the case they want to run a low-profile board for
  internal uses.
- Added an admin notice when Matador cannot write its uploads folders (which prevents saving of uploaded files from
  applicants and data logging).
- Various performance improvements, including a rewrite of our settings page that speeds up Matador-related page loads
  by up to 3x faster.
- Added filter to allow for better messaging when an application sync status is null (possible in outside integrations
  like WPJM)
- Added filter 'matador_bullhorn_doing_jobs_sync' to allow for safe targeting of local and automatic post saves.
- Added action 'matador_bullhorn_import_save_new_job' to allow for actions based only on initial import of job.
- Added actions 'matador_bullhorn_before_import' and 'matador_bullhorn_after_import' to trigger behaviors around the
  import function.
- Various security hardening improvements.

Bug Fixes:

- Fixed a stubborn bug that was introduced in 3.0.3 that prevented automatic updates of Matador Jobs Pro.
- Improvements to Application Sync to prevent "Re-try" messages.
- Adjusted structured data (JSON+LD) to better match the spec.
- Fixed issue introduced in 3.0.4 where structured data (JSON+LD) saved during local changes (ie: in WordPress) was
  causing an error during remote change routines.

UI:

- A 're-check' button on Redirect URI step in Connection Assistant to reload the page and re-run the check, where
  formally users needed to reload the page.

= 3.0.5 =

Features:

- Ability to disable SSL Verify for sites with self-signed SSL.
- Added filter to modify the "WHERE" clause in Bullhorn Job Queries
- Better Errors and Logging around auto reconnect.
- Better Logging Messages around Application Sync.
- Enhancement: Resume fields, when included in the shortcode or via the default form defined in the settings will now be
  a required field with client-side validation.
- All application form fields defined in the 'matador_application_fields_structure' filter given an attribute of
  'required' will be required by client-side validation.
- Refactored the Jobs Output function into two parts to allow for alternate processing possibilities.
- Matador now processes Resumes into HTML to support new Bullhorn 'NOVO' UI

Bugfixes:

- Fixed incorrectly named actions in default Matador Taxonomies template.
- Fixed an issue for users that upgraded from a 2.x.x version of the lite plugin regarding ClientCorporation IDs.

= 3.0.4 =

UI:

- Fixed text color of status labels in connection assistant

= 3.0.3 =

Features:

- Added more allowed html tags from the job description import, including: <h1> through <h6>, and the list item tags.
- Added basic sorting functionality to the job listings, including new settings options, extendable via filters.

Bugfixes:

- Fixed an issue where the "Published - Submitted" option in the import settings wasn't working
- Fixed a persistent issue related to a 3.0.2 bugfix for template helpers related to job meta.

Internationalization:

- A partial translation of Matador to Netherlands Dutch was added to the languages directory.

= 3.0.2 =

Features:

- Added new filters into taxonomies-list
- Added argument to filter call in taxonomy labels
- Added an explicit timeout to API requests

Bugfixes:

- Matador Application form's <form> element had an empty class attribute
- Shortcodes and Template Helpers for job meta fields were not working as intended
- Fixed an issue where custom additional taxonomies formatted incorrectly caused the load to fail completely.
- Fixed issue causing some sites to fail upgrade from 2.X versions of Matador Jobs Lite
- Fixed issue where, when using the main query's template, matador search terms would not be applied to search results.
- Fixed issue where case sensitive field names in the [matador_application] shortcode caused fields to not display.
  Allow the fields attribute to be more forgiving in general to formatting.
- Fixed issue where the "show_all_option" for the taxonomy shortcode wouldn't properly work before a list.

UI:

- Improved the text label for "No Taxonomy Entries Found"

= 3.0.1 =

Features:

- Add a feature to prevent loading of deprecated shortcodes if main shortcodes are not loaded.
- Revised load order of the plugin for better extension development.

Bugfixes:

- Users exiting the connection assistant from the "Prepare" step initial screen will no longer be advanced to the next
  step upon return.
- Fixed issue where Matador Jobs Lite/Just Jobs would fail when looking for the premium versions' updater class.
- Fixed issue where All-Access and Pro extensions were not properly getting their update information.
- Fixed issue where Bullhorn Import failed if a standard taxonomy is unset by a developer (is in WPJM extension).

UI:

- Improved text prompts in Connection Assistant.

= 3.0.0 =

- While '3.0.0' in name, this update is a completely new version that rewrote the entire plugin. See matadorjobs.com
  for a full list of features. This will be a breaking change for users of the former WordPress.org plugin.

= 2.5.0 =

- Added warning for users of plugin, informing them of the 3.0+ plus release being available now for download on
  matadorjobs.com and warning that the release is breaking change and to not allow updates. Our intent is to leave this
  warning in place for a minimum of 60 days.

= 2.4.0 =
Added retries to the CV upload to re-attempt CV parsing failures (a known Bullhorn issue - "Convert failed, resume mirror status: -1 - Rex has not been initialized").
Increased the timeout for CV parsing to 2 mins
Added error email option
Added lots of extra checks for bad data back to Bullhorn API
Enabled a copy of the CV to be inserted into the description as HTML.
Numerous other bug-fixes and extra filters.

= 2.3.0 =
Added option to disable auto syncing.
Added option to not filter the jobs by isPublic location setting.
Other small fixes.
More short code support.

= 2.2.2 =
Some fixes for PHP errors.
Added more content to job post meta.
Added option select which field to show in CV form.
More short code support.

= 2.2.0 =
Fixed an error in the options name that was breaking the CV upload redirect to the thank you page.
Fixed calls to non static functions.
Added shortcodes for "b2wp_resume_form", "b2wp_application", "b2wp_shortapp" for compatibility.

= 2.1.4 =
Fixed typo.

= 2.1.2 =
Improved the messages on the settings page.
Protected the country code.

= 2.1.1 =
Fixed a bug when syncing.

= 2.1 =
Fixed the country fetch.
Handle running the plugin without being linked to bullhorn.

= 2.0 =
Merged CV upload.
Removed 3rd party file upload code and replaced with native WordPress version.
Added support to CV upload to parse skill list.
Added support to CV upload to link to a joblisting.
Added translations to all strings I could find.
Added filter to allow settings override.
Added option to select to use the CV upload form or just link to a page with an application form.
Added Microdata to the job detail pages to help with SEO ranking.
Lots of code tidy and refactoring to get the code close the WordPress Standards.
Added support for multiple URL's to one Bullhorn Account.

= 1.x.x =
As forked