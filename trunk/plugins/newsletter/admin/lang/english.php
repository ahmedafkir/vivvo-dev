<?php
/* =============================================================================
 * $Revision: 5448 $
 * $Date: 2010-06-04 13:09:44 +0200 (Fri, 04 Jun 2010) $
 *
 * Vivvo CMS v4.5.2r (build 6084)
 *
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */

$lang = array(

	'LNG_PLUGIN_NEWSLETTER_ADMIN_SUBSCRIBERS' => 'Subscribers',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_CONFIRM' => 'Confirm',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_LATEST_FIRST' => 'Latest first',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_OLDEST_FIRST' => 'Oldest first',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_EMAIL_ASC' => 'Email Asc',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_EMAIL_DESC' => 'Email Desc',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_ADD_NEW_NEWSLETTER' => 'Add new newsletter',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_TEST_ONLY' => 'Test only',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_TEST_INFO' => 'Yes - Send test email on test email / No - Send out newsletter',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_TEST_EMAIL' => 'Test e-mail',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_SUBJECT' => 'Subject',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PARSE_BODY' => 'Use VTE in email body',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PARSE_BODY_INFO' => 'VTE template',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_SEND_TO_GROUPS' => 'Send to groups',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_SEND_TO_GROUPS_INFO' => 'Ctrl for multiply',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_SEND' => 'Send',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_QUICK_LINK' => 'Quick link',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PREFERENCES' => 'Preferences',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_OLD_NEWSLETTERS' => 'Old Newsletters',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_IMPORT_EXPORT_CSV' => 'Import/Export Subscribers',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_IMPORT_FILE' => 'Import file',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_IMPORT' => 'Import',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_EXPORT_FILE' => 'Export file',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_EXPORT' => 'Export',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_ALL' => 'All',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_CONFIRMED' => 'Confirmed',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_UNCONFIRMED' => 'Unconfirmed',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PREFERENCES_NO_OF_EMAILS' => 'Number of emails in a batch',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PREFERENCES_SEND_INTERVAL' => 'Send interval',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PREFERENCES_SEND_INTERVAL_TIME' => 'minute(s)',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PREFERENCES_USUBSCRIBE_MESSAGE' => 'Unsubscribe message',
	'LNG_PLUGIN_NEWSLETTER_ADMIN_PREFERENCES_SUBSCRIBE_SUBJECT' => 'Subscribe subject',

//INFO

	'LNG_INFO_PLUGIN_NEWSLETTER_DELETE_SUBSCRIBER_SUCCESS' => 'Subscriber is deleted successfully',
	'LNG_INFO_PLUGIN_NEWSLETTER_EDIT_FIELD_SUCCESS' => 'Edit success',
	'LNG_INFO_PLUGIN_NEWSLETTER_ADD_SUCCESS' => 'Newsletter is added successfully',
	'LNG_INFO_PLUGIN_NEWSLETTER_EDIT_SUCCESS' => 'Newsletter is edited successfully',
	'LNG_INFO_PLUGIN_NEWSLETTER_DELETE_SUCCESS' => 'Newsletter is deleted successfully',
	'LNG_INFO_PLUGIN_NEWSLETTER_SEND_TEST_MAIL_SUCCESS' => 'Test email is sent successfully',
	'LNG_INFO_PLUGIN_NEWSLETTER_PREPARE_MAILS_FOR_SEND' => 'Preparing to send out newsletter. The emails are in the queue now.',
	'LNG_INFO_PLUGIN_NEWSLETTER_PREFERENCES_SUCCESS' => 'Preferences edit success',
	'LNG_INFO_PLUGIN_NEWSLETTER_IMPORT_MAILS_SUCCESS' => 'Import success. Total <NUM_OF_EMAILS> email(s) imported',

//SERVICE ERRORS

	'LNG_ERROR_10201' => 'Can`t execute this action',
	'LNG_ERROR_10202' => 'You don`t have sufficient privileges for this action',
	'LNG_ERROR_10203' => 'You must be logged in for this action',
	'LNG_ERROR_10204' => 'Can`t execute this action',
	'LNG_ERROR_10205' => 'You don`t have sufficient privileges for this action',
	'LNG_ERROR_10206' => 'You must be logged in for this action',
	'LNG_ERROR_10207' => 'Invalid email address',
	'LNG_ERROR_10208' => 'Test email field is empty',
	'LNG_ERROR_10209' => 'Invalid email address',
	'LNG_ERROR_10210' => 'You need to choose group or groups',
	'LNG_ERROR_10211' => 'Can`t insert newsletter in database',
	'LNG_ERROR_10212' => 'You don`t have sufficient privileges for adding newsletter',
	'LNG_ERROR_10213' => 'You must be logged in for adding newsletter',
	'LNG_ERROR_10214' => 'Invalid email address',
	'LNG_ERROR_10215' => 'Test email field is empty',
	'LNG_ERROR_10216' => 'Invalid email address',
	'LNG_ERROR_10217' => 'You need to choose group or groups',
	'LNG_ERROR_10218' => 'Can`t update newsletter',
	'LNG_ERROR_10219' => 'Newsletter doesn`t exist',
	'LNG_ERROR_10220' => 'You don`t have sufficient privileges for editing newsletter',
	'LNG_ERROR_10221' => 'You must be logged in for editing newsletter',
	'LNG_ERROR_10222' => 'Can`t delete newsletter. Try again',
	'LNG_ERROR_10223' => 'You don`t have sufficient privileges for deleting newsletter',
	'LNG_ERROR_10224' => 'You must be logged in for deleting newsletter',
	'LNG_ERROR_10225' => 'Newsletter doesn`t exist',
	'LNG_ERROR_10226' => 'You don`t have sufficient privileges for sending newsletter',
	'LNG_ERROR_10227' => 'You must be logged in for sending newsletter',
	'LNG_ERROR_10228' => 'Can`t update preference',
	'LNG_ERROR_10229' => 'Preference doesn`t exist.',
	'LNG_ERROR_10230' => 'You don`t have sufficient privileges for sending newsletter',
	'LNG_ERROR_10231' => 'You don`t have sufficient privileges for exporting maillist',
	'LNG_ERROR_10232' => 'File upload failed',
	'LNG_ERROR_10233' => 'You don`t have sufficient privileges for importing emails',

//DATABASE FIELD

	'LNG_DB_newsletter_subject' => 'Subject',
	'LNG_DB_newsletter_body' => 'Body'
);
?>