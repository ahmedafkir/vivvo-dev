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
 	 * Delete articles (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function auto_delete($sm) {

		require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

		if (VIVVO_ARTICLE_AUTO_DELETE > 0) {

			$al = new Articles_list($sm);
			$al->_query->add_where('created < ( DATE_SUB( \'' . date('Y-m-d H:i:s') . '\', INTERVAL ' .  VIVVO_ARTICLE_AUTO_DELETE .' DAY ))');
			$al->_query->set_from('`'.VIVVO_DB_PREFIX . 'articles`');
			$al->_query->add_fields('*');

			$article_params['search_options']['search_cid'] = VIVVO_ARTICLE_AUTO_DELETE_ITEMS;
			$al->search($article_params['search_options'], '', 'ascending', 0, 0, false);

			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

			$pm = new vivvo_post_master($sm);
			$al->sql_delete_list($pm, NULL, true);

			if (defined('VIVVO_CRONJOB_MODE')) {
				echo 'auto_delete: Finished.' . PHP_EOL;
			} else {
				admin_log('(Cron task: Auto Delete)', 'Finished.');
			}
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Deletes outdated articles.';

#EOF