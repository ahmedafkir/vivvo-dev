<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
 *
 * Vivvo CMS v4.5.2r (build 6082)
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


require_once (dirname(__FILE__) . '/admin_include.php');
require_once (VIVVO_FS_INSTALL_ROOT . 'lib/backup/mysql_backup.php');

defined('VIVVO_MAINTENANCE_BACKUP_MAX_ROWS_PER_RUN') or define('VIVVO_MAINTENANCE_BACKUP_MAX_ROWS_PER_RUN', 5000);
defined('VIVVO_MAINTENANCE_RELATE_MAX_ROWS_PER_RUN') or define('VIVVO_MAINTENANCE_RELATE_MAX_ROWS_PER_RUN', 100);

if (!$sm->user->is_admin()){
	header('Location: index.php');
	exit;
}

$lang = vivvo_lang::get_instance();

set_time_limit(20*60*60);
$start_time = microtime();
ignore_user_abort();

$restoreDropTable = false;

/**
 * Compress file on disk
 *
 * @param	string	$source
 * @param	int		$level
 * @return	string|false
 */
function gzcompressfile($source, $level = false) {

	if (file_exists($source)) {

		$dest = $source . '.gz';
		$mode = 'wb' . $level;
		$error = false;

		if (($fp_out = gzopen($dest, $mode))) {
			if (($fp_in = fopen($source, 'rb'))) {
				while (!feof($fp_in)) {
					gzwrite($fp_out, fread($fp_in, 4096));
				}
				fclose($fp_in);
			} else {
				$error = true;
			}
			gzclose($fp_out);
		} else {
			$error = true;
		}

		if ($error) {
			return false;
		}
		return $dest;
	}
	return false;
}

$messages = array();
$scripts = array();

// Requests

if (isset($_REQUEST['database_maintenance'])) {

	$action = 'database_maintenance';

	// repair
	$messages[] = array(
		'message' => '- Tables repair started:',
		'type' => 'title'
	);

	$tables = mysql_list_tables(VIVVO_DB_DATABASE);
	$table_names = array();

	while ($tabs = mysql_fetch_row($tables)) {

		if (substr($tabs[0], 0, strlen(VIVVO_DB_PREFIX)) == VIVVO_DB_PREFIX) {

			$table_names[] = $tabs[0];

			if (mysql_query("REPAIR TABLE `$tabs[0]`")) {
				$messages[] = array(
					'message' => $tabs[0] . ' ' . $lang->get_value('LNG_ADMIN_REPAIRED'),
					'type' => 'action',
					'result' => 'success'
				);
			} else {
				$messages[] = array(
					'message' => $tabs[0] . ' ' . $lang->get_value('LNG_ADMIN_NOT_REPAIRED'),
					'type' => 'action',
					'result' => 'failed'
				);
			}
		}
	}

	// optimize
	$messages[] = array(
		'message' => '- Tables optimization started:',
		'type' => 'title'
	);

	foreach ($table_names as $table) {

		if (mysql_query("OPTIMIZE TABLE `$table`")) {
			$messages[] = array(
				'message' => $table . ' ' . $lang->get_value('LNG_ADMIN_OPTIMIZED'),
				'type' => 'action',
				'result' => 'success'
			);
		} else {
			$messages[] = array(
				'message' => $table . ' ' . $lang->get_value('LNG_ADMIN_NOT_OPTIMIZED'),
				'type' => 'action',
				'result' => 'failed'
			);
		}
	}

} elseif (isset($_REQUEST['clean_cache'])) {

	$action = 'clean_cache';

	$dir = VIVVO_FS_ROOT . 'cache/';
	$success = true;
	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != ".." && $filename != ".htaccess" && !is_dir($dir . $filename)) {
				$success = $success && @unlink($dir . $filename);
			}
		}
	} else {
		$success = false;
	}

	if ($success) {
		$messages[] = array(
			'message' => ' - Clean cache folder',
			'type' => 'action',
			'result' => 'success'
		);
	} else {
		$messages[] = array(
			'message' => ' - Clean cache folder',
			'type' => 'action',
			'result' => 'failed'
		);
	}

	$dir = VIVVO_FS_ROOT . 'cache/feed/';
	$success = true;
	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != ".." && !is_dir($dir . $filename)) {
				$success = $success && @unlink($dir . $filename);
			}
		}
	} else {
		$success = false;
	}

	if ($success) {
		$messages[] = array(
			'message' => ' - Clean feed cache folder',
			'type' => 'action',
			'result' => 'success'
		);
	} else {
		$messages[] = array(
			'message' => ' - Clean feed cache folder',
			'type' => 'action',
			'result' => 'failed'
		);
	}

	$dir = VIVVO_FS_ROOT . 'cache/thumbnail/';
	$success = true;
	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename[0] != "." && !is_dir($dir . $filename)) {
				$success = $success && @unlink($dir . $filename);
			} elseif ($filename[0] != "." && is_dir($dir . $filename)) {
				$sub_dir = $dir . $filename . '/';
				if ($sub_handle = opendir($sub_dir)) {
					while (false !== ($sfilename = readdir($sub_handle))) {
						if ($sfilename[0] != "." && !is_dir($sub_dir . $sfilename)) {
							$success = $success && @unlink($sub_dir . $sfilename);
						}
					}
				} else {
					$success = false;
				}
			}
		}
	} else {
		$success = false;
	}
	if ($success) {
		$messages[] = array(
			'message' => ' - Clean thumbnail cache folder',
			'type' => 'action',
			'result' => 'success'
		);
	} else {
		$messages[] = array(
			'message' => ' - Clean thumbnail cache folder',
			'type' => 'action',
			'result' => 'failed'
		);
	}

	$sm->drop_db_cache();
	$messages[] = array(
		'message' => ' - Clean database cache',
		'type' => 'action',
		'result' => 'success'
	);

} elseif(isset($_REQUEST['delete'])) {

	$action = 'delete';

	$file = VIVVO_FS_ROOT . 'backup/' . $_REQUEST['file'];

	if (file_exists($file)) {
		if (is_writable($file)) {
			if (unlink ($file)) {
				$messages[] = array(
					'message' => $lang->get_value('LNG_INFO_FILE_DELETE_SUCCESS'),
					'type' => 'action',
					'result' => 'success'
				);
				$scripts[] = '<script type="text/javascript">parent.reload_backup_files();</script>';
			} else {
				$messages[] = array(
					'message' => $lang->get_value('LNG_ERROR_5001'),
					'type' => 'error'
				);
			}
		} else {
			$messages[] = array(
				'message' => $lang->get_value('LNG_ERROR_5002'),
				'type' => 'error'
			);
		}
	} else {
		$messages[] = array(
			'message' => $lang->get_value('LNG_ERROR_5003'),
			'type' => 'error'
		);
	}
} elseif (isset($_REQUEST['backup'])) {

	$action = 'backup';

	$restart = empty($_GET['restart']) ? 0 : (int)$_GET['restart'];
	$file_name = VIVVO_FS_ROOT . 'backup/backup_details.csv';
	$max_rows = VIVVO_MAINTENANCE_BACKUP_MAX_ROWS_PER_RUN;

	$folder_name = VIVVO_FS_ROOT . 'backup/';

	$backup_obj = new BackupMySQL();

	if (!isset($_GET['f'])) {
		$basename = $backup_obj->database . date('-Ymd-Hi-') . md5(uniqid(VIVVO_URL, true)) . '.sql';
	} else {
		$basename = $_GET['f'];
	}

	$filename = $folder_name.$basename;

	if ($restart != -1) {
		if ($restart == 1) {

			$table_details = $backup_obj->GetTables($database);

			$fp = fopen($file_name, 'w');

			for($count = 0; $count < count($table_details); $count++) {
				fwrite($fp, $table_details[$count] . ":0\r\n");
			}

			fclose($fp);

			$fp = fopen($filename, 'w');
			fclose($fp);

			@unlink($filename."gz");
		}

		$file_contents = file($file_name);

		$total_rows = 0;
		$table_count = count($file_contents);
		for ($count = 0; $count < $table_count; $count++) {

			list($table_name, $row_count, $start) = explode(':', str_replace("\r", "", str_replace("\n", "", $file_contents[$count])));

			if ($start < $row_count || $row_count == 0) {

				if (($start + $max_rows) > $row_count) {
					$end = $row_count - $start;
				} else {
					$end = $max_rows;
				}

				$str = $table_name . ":" . $row_count . ":" . ($start + $end) . "\r\n";
				$file_contents[$count] = $str;

				if (!$backup_obj->Execute($filename, $database, $table_name, $start, $end, $row_count)) {
					$messages[] = array(
						'message' => $lang->get_value('LNG_ADMIN_ERROR_BACKING_UP_TABLE') . $table_name . '. ' . $lang->get_value('LNG_ADMIN_DETAILS'). ' : ' . $backup_obj->error,
						'type' => 'action',
						'result' => 'failed'
					);
				} else {
					$total_rows += $end;
					if ($end != 0) {
						$messages[] = array(
							'message' => $lang->get_value('LNG_ADMIN_ROWS') . ' ' . $start . '-' . ($start+$end-1) . ' ' . $lang->get_value('LNG_ADMIN_OF_TABLE') . ' ' . $table_name . ' ' . $lang->get_value('LNG_ADMIN_WERE_SUCCESSFULY_BACKED_UP'),
							'type' => 'action',
							'result' => 'success'
						);
					} else {
						$messages[] = array(
							'message' => $lang->get_value('LNG_ADMIN_ROWS') . ' 0-0 '. $lang->get_value('LNG_ADMIN_OF_TABLE') .' ' . $table_name . ' ' . $lang->get_value('LNG_ADMIN_WERE_SUCCESSFULY_BACKED_UP'),
							'type' => 'action',
							'result' => 'success'
						);
					}

					$fp = fopen($file_name, 'w');

					for ($counter = 0; $counter < count($file_contents); $counter++) {
						fwrite($fp, $file_contents[$counter]);
						if ($total_rows >= $max_rows) {
							$messages[] = array(
								'message' => $file_contents[$counter],
								'type' => 'info'
							);
						}
					}
					fclose($fp);
				}

				if ($total_rows >= $max_rows) {
					break;
				}
			}
		}
	} else {
		if (gzcompressfile($filename, 9) == false) {
			$messages[] = array(
				'message' => $lang->get_value('LNG_ADMIN_GZIPPED_FILE_COULD_NOT_BE_CREATED'),
				'type' => 'action',
				'result' => 'failed'
			);
		} else {
			$messages[] = array(
				'message' => $lang->get_value('LNG_ADMIN_GZIPPED_FILE_WAS_SUCCESSFULLY_CREATED'),
				'type' => 'action',
				'result' => 'success'
			);
			$scripts[] = '<script type="text/javascript">parent.backup_completed();</script>';
			@unlink($filename);
		}
	}

	if ($restart !=-1 && $count < $table_count) {
		$scripts[] = '<script>location.href="' . VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'db_maintence.php?backup&restart=0&backup&f=' . $basename . '"</script>';
	} elseif ($restart != -1) {
		$scripts[] = '<script>location.href="' . VIVVO_URL . VIVVO_FS_ADMIN_DIR.'db_maintence.php?backup&restart=-1&backup&f=' . $basename . '"</script>';
	}

} elseif (isset($_REQUEST['restore'])) {

	$action = 'restore';

	if (isset($_REQUEST['file']) && file_exists(VIVVO_FS_ROOT . 'backup/' . $_REQUEST['file'])) {

		$link_id = mysql_connect(VIVVO_DB_HOST, VIVVO_DB_USER, VIVVO_DB_PASSWORD);
		$value = mysql_select_db(VIVVO_DB_DATABASE, $link_id);

		@mysql_query("SET NAMES 'utf8'");

		$rows = array();

		if ($gz = (substr($_REQUEST['file'], -2) == 'gz')) {
			$file = gzopen(VIVVO_FS_ROOT . 'backup/' . $_REQUEST['file'], "r");
		} else {
			$file = fopen(VIVVO_FS_ROOT . 'backup/' . $_REQUEST['file'], "r");
		}

		$qc = 0;
		$success_no = 0;
		$queries_no = 0;

		$break_point = 0;
		$read_line = 0;
		$queries = '';
		$debug_mode = true;

		do {
			if ($gz) {
				$buffer = gzgets($file, 4096);
			} else {
				$buffer = fgets($file, 4096);
			}
			$read_line++;

			if (strlen($buffer) > 0 and (substr($buffer, 0, 1) <> '#') or substr($buffer, 0, 2) == '##') {
				$queries .= $buffer;
				if (substr(trim($buffer),-19) == "##VIVVO_END_OF_LINE") {
					$queries = str_replace("##VIVVO_END_OF_LINE", "", $queries);
					$queries_no++;
					if (!mysql_query($queries)) {
						if ($debug_mode){
							$messages[] = array(
								'message' => mysql_error() . "($queries)",
								'type' => 'error'
							);
						}
					} else {
						$success_no++;
					}
					$queries = '';
					$break_point = $read_line;
				}
			}
		} while (!feof($file) && ((microtime() - $start_time) < 20 * 59 * 1000));

		if ($queries_no > 1 or $success_no > 1) {

			$messages[] = array(
				'message' => $lang->get_value('LNG_ADMIN_BACKUP_RESTORED') . ' - ' . $success_no . ' / ' . $queries_no . ' ' . $lang->get_value('LNG_ADMIN_INSTRUCTIONS'),
				'type' => 'action',
				'result' => 'success'
			);

			vivvo_cache::get_instance()->delete_all();

		} else {

			$messages[] = array(
				'message' => $lang->get_value('LNG_ADMIN_NOTHING_TO_DO'),
				'type' => 'action',
				'result' => 'success'
			);
		}
	}
} elseif (isset($_REQUEST['relate_articles'])) {

	$action = 'relate_articles';

	if (!VIVVO_ARTICLE_RELATED_CATEGORY and !VIVVO_ARTICLE_RELATED_TAGS and !VIVVO_ARTICLE_RELATED_TOPIC) {

		$messages[] = array(
			'message' => $lang->get_value('LNG_ADMIN_NOTHING_TO_DO'),
			'type' => 'action',
			'result' => 'success'
		);

	} else {

		$start = empty($_GET['restart']) ? 0 : (int)$_GET['restart'];
		$datetime = empty($_GET['dt']) ? date('Y-m-d 23:59:00') : $_GET['dt'];
		$count = empty($_GET['cnt']) ? -1 : (int)$_GET['cnt'];

		$db = $sm->get_db();

		do {

			if ($count < 0) {

				$res = $db->query('SELECT COUNT(*) FROM ' . VIVVO_DB_PREFIX . "articles WHERE created < '$datetime' AND status > 0");

				if (!PEAR::isError($res)) {
					$count = $res->fetchOne();
					$res->free();
				} else {
					$messages[] = array(
						'message' => $res->getMessage(),
						'type' => 'error'
					);
					break;
				}
			}

			if ($start >= $count) {
				$messages[] = array(
					'message' => str_replace('{1}', 100, $lang->get_value('LNG_ADMIN_RELATED_ARTICLES_PERCENT')),
					'type' => 'info'
				);
	            $messages[] = array(
					'message' => $lang->get_value('LNG_ADMIN_DONE'),
					'type' => 'action',
					'result' => 'success'
				);
				break;
	        }

			if ($start == 0) {
				$db->exec('TRUNCATE TABLE ' . VIVVO_DB_PREFIX . 'related');
			}

			$limit = VIVVO_MAINTENANCE_RELATE_MAX_ROWS_PER_RUN;

			$res = $db->query('SELECT id, category_id FROM ' . VIVVO_DB_PREFIX . "articles WHERE created <= '$datetime' AND status > 0 LIMIT $start,$limit");

			if (PEAR::isError($res)) {
				$messages[] = array(
					'message' => $res->getMessage(),
					'type' => 'error'
				);
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
					$messages[] = array(
						'message' => $related->getMessage(),
						'type' => 'error'
					);
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

			$start = min($start + $limit, $count);

			$messages[] = array(
				'message' => str_replace('{1}', ceil($start / $count * 100), $lang->get_value('LNG_ADMIN_RELATED_ARTICLES_PERCENT')),
				'type' => 'info'
			);

			if ($start < $count) {
				$scripts[] = '<script>location.href="' . VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'db_maintence.php?relate_articles&restart=' . $start . '&dt=' . $datetime . '&cnt=' . $count . '";</script>';
			}

		} while (0);
	}
}

$template = $sm->get_template();
$template->set_template_file(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/maintenance/iframe.xml');
$template->assign('action', $action);
$template->assign('messages', $messages);
$template->assign('scripts', $scripts);

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
echo $template->get_output();

ob_end_flush();
?>