<?php
/* =============================================================================
 * $Revision: 5999 $
 * $Date: 2010-11-29 16:19:59 +0100 (Mon, 29 Nov 2010) $
 *
 * Vivvo CMS v4.7 (build 6082)
 *
 * Copyright (c) 2012, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */

defined('VIVVO_RELATIVE_URL') or define('VIVVO_RELATIVE_URL', preg_replace('|^[^:]+://[^/]+|', '', rtrim(VIVVO_URL, '/') . '/'));

if (!defined('VIVVO_COOKIE_DOMAIN')){

    if (preg_match('/^\d+\.\d+\.\d+\.\d+$/', $_SERVER['SERVER_NAME'])){
        define ('VIVVO_COOKIE_DOMAIN', $_SERVER['SERVER_NAME']);
    }else if (preg_match('/^www\./', $_SERVER['SERVER_NAME'])){
        define ('VIVVO_COOKIE_DOMAIN', '.' . preg_replace('/^www\./','', $_SERVER['SERVER_NAME']));
    }else if (strpos($_SERVER['SERVER_NAME'], '.') === false){
        define ('VIVVO_COOKIE_DOMAIN', '');
    }else{
        define ('VIVVO_COOKIE_DOMAIN', '.' . $_SERVER['SERVER_NAME']);
    }
}
if (!defined('VIVVO_COOKIE_PATH')) {
	define('VIVVO_COOKIE_PATH', VIVVO_RELATIVE_URL);
}

	function full_page_cache_load() {

		list($url) = explode('?', CACHE_URL, 2);

		if (substr($url, -16) == '/cron_image.html' or substr($url, -15) == '/imagecode.html' or !defined('VIVVO_DB_HOST')) {
			return array('', array());
		}

		$connection = @mysql_connect(VIVVO_DB_HOST, VIVVO_DB_USER, VIVVO_DB_PASSWORD) or die("Can't connect! ".mysql_error());
		if (!mysql_select_db(VIVVO_DB_DATABASE, $connection)) die ("Error while connection to database. ".mysql_error());

		$res = mysql_query('SELECT * FROM ' . VIVVO_DB_PREFIX . "configuration WHERE variable_name LIKE 'VIVVO_CACHE_%'");
		while ($row = mysql_fetch_assoc($res)){
			@define($row['variable_name'], $row['variable_value']);
		}

        $kill_cache = isset($_GET['kill_cache']);
        if (!$kill_cache && isset($_COOKIE['kill_cache']) && $_COOKIE['kill_cache'] == '1'){
            $kill_cache = true;
            setcookie("kill_cache", false, 1, VIVVO_COOKIE_PATH, VIVVO_COOKIE_DOMAIN);
        }

		$output = '';
		$uncached_boxes = array();

		if ((VIVVO_CACHE_ENABLE > 1) && !$kill_cache && !isset($_REQUEST['template_output']) && !isset($_REQUEST['action']) && !isset($_REQUEST['search_options']) && !preg_match('/\?print$/', $_SERVER['REQUEST_URI'])){

			class_exists('vivvo_cache') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cache.php';

			$cache_manager = vivvo_cache::get_instance();

			if (!$cache_manager->exists(CACHE_URL)) {
				return array($output, $uncached_boxes);
			}

			$output = $cache_manager->get(CACHE_URL);

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";

			//<!--NoCache box/login.tpl-->
			if (VIVVO_CACHE_ENABLE == 2){
				preg_match_all('/<!--NoCache ([a-zA-Z0-9_\/\.]+)-->/', $output, $arr);
				$uncached_boxes = array_unique($arr[1]);
			} else {
				$output = str_replace('$generation_time$', '0s', $output);

				if (preg_match('!^' . preg_quote(VIVVO_URL, '!') . '.+?/(\d+)(-.+)?\.html!', CACHE_URL, $match)) {
					$id = (int)($match[1]);
					if ($article = mysql_fetch_assoc($result = mysql_query('SELECT times_read,today_read,last_read,created FROM '.VIVVO_DB_PREFIX."articles_stats WHERE `article_id`=$id"))) {
						mysql_free_result($result);

						$today = date('Y-m-d');
						$last_read = date('Y-m-d H:i:s');
						$times_read = (int)($article['times_read']);
						$today_read = (int)($article['today_read']);

						if (strtotime($article['last_read']) < strtotime($today)) {
							$today_read = 1;
						} else {
							$today_read++;
						}

						$times_read++;

						mysql_query(
							'INSERT INTO '. VIVVO_DB_PREFIX ."articles_stats
							(`article_id`, `last_read`, `times_read`, `today_read`, `updated`, `created`)
							VALUES ($id, '$last_read', $times_read, $today_read, 1, '$article[created]')
							ON DUPLICATE KEY UPDATE
								`today_read`=IF(`last_read`<'$today', 1, `today_read`+1),
								`last_read`='$last_read',
								`times_read`=`times_read`+1,
								`updated`=1,
								`created`='$article[created]'"
						);
					}
				}

				echo $output;
				exit;
			}
		}
		$result = array($output, $uncached_boxes);
		return $result;
	}

	function full_page_cache_replace_boxes($sm, $uncached_boxes){
		$sm != null or $sm = vivvo_lite_site::get_instance();
		if ((VIVVO_CACHE_ENABLE == 2) && !empty($uncached_boxes)){
			$output = $sm->output;
			$template = $sm->get_template();
			$box_output = array();
			foreach ($uncached_boxes as $box_key => $box){
				$sm->debug_push("Full page cache:", 'Render ' . $box);
				$template_file = preg_replace('/[^a-zA-Z0-9\_\-\/\.]/', '', $box);
				if (file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $template_file)){
					$box_template = new template($sm, $template);
					$box_template->set_template_file(VIVVO_TEMPLATE_DIR . $template_file);
					$output = preg_replace ('/<!--NoCache ' . preg_quote($box, '/') . '-->.*<!--NoCache-->/sU', $box_template->get_output(), $output);
				}
			}
			if (defined('VIVVO_DEBUG_MODE')){
				$output = str_replace('</body>', $sm->format_debug_output(), $output);
			}
			echo $output;
			exit;
		}
	}

	function full_page_cache_save($sm, $output){
		$sm != null or $sm = vivvo_lite_site::get_instance();

		if ((VIVVO_CACHE_ENABLE > 1) && isset($_GET['kill_cache'])){
			if ($sm->user && $sm->user->is_admin()){
				$sm->drop_db_cache();
			}
		}
		if ((VIVVO_CACHE_ENABLE > 1) && !isset($_REQUEST['action']) && !isset($_REQUEST['template_output']) && !isset($_REQUEST['search_options']) && !preg_match('/\?print$/', $_SERVER['REQUEST_URI'])){
			$sm->save_db_cache($output);
		}
	}

#EOF