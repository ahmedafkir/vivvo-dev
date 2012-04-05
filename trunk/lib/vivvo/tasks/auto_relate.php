<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
 *
 * Vivvo CMS v4.6 (build 6082)
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
	 * Relate articles (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function auto_relate($sm) {

		if (!VIVVO_ARTICLE_RELATED_CATEGORY and !VIVVO_ARTICLE_RELATED_TAGS and !VIVVO_ARTICLE_RELATED_TOPIC) {
			$result = 'Nothing to do.';
		} else {

			$datetime = date('Y-m-d 23:59:00');

			$db = $sm->get_db();

			do {

				$res = $db->query('SELECT COUNT(*) FROM ' . VIVVO_DB_PREFIX . "articles WHERE created < '$datetime' AND status > 0");

				if (!PEAR::isError($res)) {
					$count = $res->fetchOne();
					$res->free();
				} else {
					$result = 'Failed to run.';
					break;
				}

				$db->exec('TRUNCATE TABLE ' . VIVVO_DB_PREFIX . 'related');

				$res = $db->query('SELECT id, category_id FROM ' . VIVVO_DB_PREFIX . "articles WHERE created <= '$datetime' AND status > 0");

				if (PEAR::isError($res)) {
					$result = 'Failed to select articles.';
					break;
				}

				$max = VIVVO_ARTICLE_RELATED_CATEGORY + VIVVO_ARTICLE_RELATED_TOPIC + VIVVO_ARTICLE_RELATED_TAGS;
				$rel_category = VIVVO_ARTICLE_RELATED_CATEGORY;
				$rel_parent = VIVVO_ARTICLE_RELATED_CATEGORY / 2;

				$categories = $sm->get_categories()->list;

				while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {

					$category_id = $row['category_id'];
					$parent_id = $categories[$category_id]->get_parent_cat();

					$sum = array();
					$join = '';

					if (VIVVO_ARTICLE_RELATED_CATEGORY >= 0) {
						$sum[] = "IF(a.category_id = $category_id, $rel_category, IF(a.category_id = $parent_id, $rel_parent, 0))";
					}

					if (VIVVO_ARTICLE_RELATED_TOPIC >= 0) {

						$topics_res = $db->query('SELECT DISTINCT tags_groups_id FROM ' . VIVVO_DB_PREFIX . "articles_tags WHERE article_id = $row[id]");

						if (PEAR::isError($topics_res)) {
							$topics = array();
						} else {
							$topics = $topics_res->fetchCol();
							$topics_res->free();
						}

						if (!empty($topics)) {
							$join .= ' INNER JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at1 ON (at1.article_id = a.id AND at1.tags_group_id IN (' . implode(',', $topics) . '))';
							$sum[] = 'COUNT(DISTINCT at1.tags_groups_id) * ' . (VIVVO_ARTICLE_RELATED_TOPIC / count($topics));
						}
					}

					if (VIVVO_ARTICLE_RELATED_TAGS >= 0) {

						$tags_res = $db->query('SELECT DISTINCT tag_id FROM ' . VIVVO_DB_PREFIX . "articles_tags WHERE article_id = $row[id]");

						if (PEAR::isError($tags_res)) {
							$tags = array();
						} else {
							$tags = $tags_res->fetchCol();
							$tags_res->free();
						}

						if (!empty($tags)) {
							$join .= ' INNER JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at2 ON (at2.article_id = a.id AND at2.tag_id IN (' . implode(',', $tags) . '))';
							$sum[] = 'COUNT(DISTINCT at2.tag_id) * ' . (VIVVO_ARTICLE_RELATED_TAGS / count($tags));
						}
					}

					$sql = 'SELECT a.id, (' . implode('+', $sum) . ') AS score
							FROM ' . VIVVO_DB_PREFIX . "articles AS a
							$join
							WHERE created <= '$datetime' AND status > 0 AND a.id != $row[id]
							GROUP BY a.id
							HAVING score > 0
							ORDER BY score DESC
							LIMIT 5";

					$related = $db->query($sql);

					if (PEAR::isError($related)) {
						$result = 'Failed to fetch related articles.';
						break 2;
					}

					$values = array();

					while (($related_row = $related->fetchRow(MDB2_FETCHMODE_ASSOC))) {
						$relevance = floor($related_row['score'] / $max * 100);
						$values[] = "($row[id],$related_row[id],$relevance)";
					}

					$related->free();

					$db->exec('INSERT INTO ' . VIVVO_DB_PREFIX . 'related VALUES ' . implode(',', $values));
				}

				$result = 'Executed successfully.';

			} while (0);
		}

		if (defined('VIVVO_CRONJOB_MODE')) {
			echo 'auto_relate: ' . $result . PHP_EOL;
		} else {
			admin_log('(Cron task: Auto Relate)', $result);
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Re-relates articles.';

#EOF