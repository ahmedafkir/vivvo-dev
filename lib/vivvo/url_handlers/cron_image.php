<?php
/* =============================================================================
 * $Revision: 5005 $
 * $Date: 2010-04-15 11:22:08 +0200 (Thu, 15 Apr 2010) $
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

function cron_image_url_handler(&$sm, $url_array) {

	header("Content-Type: image/gif");
	echo base64_decode("R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==");
	flush();

	$cron_flag = VIVVO_FS_ROOT . 'cache/.cron.lock';

	// call cron only if it's been at least a minute since the last
	if (file_exists($cron_flag) and filemtime($cron_flag) > VIVVO_START_TIME - 60) {
		exit;
	}

	// reset flag
	@unlink($cron_flag);
	touch($cron_flag);

	require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php';

	ignore_user_abort(true);
	$cm = new vivvo_cron_manager($sm);
	$cm->execute();
	exit;
}
?>