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
	function auto_update_article_stats($sm) {

        $db = $sm->get_db();

        //get statistics
        $res = $db->query("SELECT * FROM ". VIVVO_DB_PREFIX ."articles_stats WHERE updated=1");

        while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {

			// update articles table
			$db->exec("UPDATE " . VIVVO_DB_PREFIX . "articles
						SET last_read='$row[last_read]', times_read=$row[times_read], today_read=$row[today_read]
						WHERE id=$row[article_id]");

			// reset updated flag if no new reads in mean time
			$db->exec("UPDATE " . VIVVO_DB_PREFIX . "articles_stats
						SET updated=0
						WHERE article_id=$row[article_id] AND times_read=$row[times_read]");
        }

		if (defined('VIVVO_CRONJOB_MODE')) {
			echo 'auto_update_article_stats: Finished.' . PHP_EOL;
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Updates article statistics.';

#EOF