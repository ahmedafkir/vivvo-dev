<?php
/* =============================================================================
 * $Revision: 5409 $
 * $Date: 2010-05-27 17:57:42 +0200 (Thu, 27 May 2010) $
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

	defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time());

	/**
 	 * Import articles (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function auto_import_feeds($sm) {

		require_once dirname(__FILE__) . '/../core/Feeds.class.php';
		require_once VIVVO_FS_FRAMEWORK . 'vivvo_post.php';

		$db = $sm->get_db();

		if (VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE > 0) {
			$datetime = date('Y-m-d H:i:59', VIVVO_START_TIME - VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE * 86400);
			if (VIVVO_PLUGIN_FEED_IMPORTER_TRASH) {
				$db->exec('UPDATE ' . VIVVO_DB_PREFIX . "articles SET status = -2 WHERE created < '$datetime' AND feed_item_id IS NOT NULL AND status > -2");
			} else {
				$db->exec('DELETE FROM ' . VIVVO_DB_PREFIX . "articles WHERE created < '$datetime' AND feed_item_id IS NOT NULL");
			}
		}

		if (VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE > 0) {
			$datetime = date('Y-m-d H:i:59', VIVVO_START_TIME - VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE * 86400);
			$db->exec('UPDATE ' . VIVVO_DB_PREFIX . "articles SET status = -1 WHERE created < '$datetime' AND feed_item_id IS NOT NULL");
		}

		$feed_list = Feeds_list::factory();
		$feed_list->search(array());

		$post_master = new vivvo_post_master($sm);

		$total_added = 0;
		$total_updated = 0;

		foreach($feed_list->list as $feed) {
			$counts = $feed->import_articles();
			$total_added += $counts[0];
			$total_updated += $counts[1];
			$post_master->set_data_object($feed);
			$post_master->sql_update();
		}

		if (defined('VIVVO_CRONJOB_MODE')) {
			$feed_count = count($feed_list->list);
			echo "auto_import_feeds: Imported $total_added and updated $total_updated article(s) from $feed_count feed(s)." . PHP_EOL;
		} elseif (defined('VIVVO_FEED_IMPORTER_ADMIN_VIEW')) {
			echo json_encode(array(
				'feeds' => count($feed_list->list),
				'added' => $total_added,
				'updated' => $total_updated
			));
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Imports articles from web feeds.';

#EOF