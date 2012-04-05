<?php
/* =============================================================================
 * $Revision: 5348 $
 * $Date: 2010-05-18 18:02:19 +0200 (Tue, 18 May 2010) $
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
	 * Resets number of today's reads (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function auto_reset_today_read($sm) {

		require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

		$article_list = new Articles_list($sm);
		$article_list->search(array(), '', 'ascending', 0, 0, false);

		require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
		$pm = new vivvo_post_master($sm);
		$article_list->sql_update_list($pm, array('today_read' => '0'), NULL, true);

		if (defined('VIVVO_CRONJOB_MODE')) {
			echo 'auto_reset_today_read: Finished.' . PHP_EOL;
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Resets today read numbers.';

#EOF