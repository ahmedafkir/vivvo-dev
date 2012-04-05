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
 	 * Archive articles (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function auto_archive($sm) {

		require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php';

		if (VIVVO_ARTICLE_AUTO_ARCHIVE > 0) {

			$article_list = new Articles_list;
			$search_params = array(
				'search_cid' => VIVVO_ARTICLE_AUTO_ARCHIVE_ITEMS,
				'search_before_after' => 0,
				'search_search_date' => VIVVO_ARTICLE_AUTO_ARCHIVE
			);

			$article_list->search($search_params, '', 'ascending', 0, 0, false);

			require_once VIVVO_FS_FRAMEWORK . 'vivvo_post.php';

			$article_list->sql_update_list(new vivvo_post_master, array('status' => '-1'), null, true);

			if (defined('VIVVO_CRONJOB_MODE')) {
				echo 'auto_archive: Finished.' . PHP_EOL;
			} else {
				admin_log('(Cron task: Auto Archive)', 'Finished.');
			}
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Archives outdated articles.';

#EOF