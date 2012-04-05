<?php
/* =============================================================================
 * $Revision: 5365 $
 * $Date: 2010-05-21 15:16:26 +0200 (Fri, 21 May 2010) $
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

	/**
 	 * Process mail queue (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function mail_queue($sm) {

		require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/PEAR/Mail/Queue.php';

		$container_options = array(
			'type' => 'mdb2',
			'dsn' => VIVVO_DB_TYPE . '://' . VIVVO_DB_USER . ':' . VIVVO_DB_PASSWORD . '@' . VIVVO_DB_HOST . '/' . VIVVO_DB_DATABASE,
			'mail_table' => VIVVO_DB_PREFIX . 'mail_queue'
		);


		if (VIVVO_EMAIL_SMTP_PHP == 1) {
			$mail_options = array('driver' => 'mail');
		} else {

			$mail_options = array(
				'driver' => 'smtp',
				'host' => VIVVO_EMAIL_SMTP_HOST,
				'port' => VIVVO_EMAIL_SMTP_PORT,
				'localhost' => 'localhost'
			);

			if (VIVVO_EMAIL_SMTP_PASSWORD != '' and VIVVO_EMAIL_SMTP_USERNAME != '') {
				$mail_options['auth'] = true;
				$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
				$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
			} else {
				$mail_options['auth'] = false;
				$mail_options['username']  = '';
				$mail_options['password']  = '';
			}
		}

		$mail_queue = new Mail_Queue($container_options, $mail_options);
		$mail_queue->sendMailsInQueue(VIVVO_PLUGIN_NEWSLETTER_NUMBER_OF_MAILS);

		if (defined('VIVVO_CRONJOB_MODE')) {
			echo 'mail_queue: Finished.' . PHP_EOL;
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Sends mails from mail queue.';

#EOF