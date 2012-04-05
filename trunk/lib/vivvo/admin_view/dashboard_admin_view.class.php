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

/**
 * @see  'vivvo_plugin.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_admin_view.php');

	/**
	 * Plugin class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class dashboard_admin_view extends vivvo_admin_view{
		var $views = array('list_output', 'leftnav');

		var $_template_root = 'templates/home/';

		function handle_action() {
			vivvo_lite_site::get_instance()->execute_action();
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();
			$template = $sm->get_template();

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$articles_list = new Articles_list($sm);

			$articles_list->reset_list_query();
			$template->assign('website_articles_pending', strval($articles_list->get_count(array('search_status' => 0))));

			//Check installer
			if (file_exists(dirname(__FILE__) . '/../installer/index.php')) {
				$dm->add_dump('warning', 0 , vivvo_lang::get_instance()->get_value('LNG_ADMIN_INSTALLER_WARNING'));
			}

		}

		/**
		 * Load admin css/javascript
		 */
		public function load_admin_header () {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css');
			$header->add_script(array(
				VIVVO_URL . 'flash/amline/swfobject.js',
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/dashboard.js'
			));
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->_leftnav());
			return $template;
		}

		function _list_output() {
			$sm = vivvo_lite_site::get_instance();
			$content_template = $this->load_template($this->_template_root . 'content.xml');

			require_once(VIVVO_FS_FRAMEWORK . '/PEAR/Lite.php');

			$options = array(
				'cacheDir' => VIVVO_FS_ROOT . 'cache/',
				'lifeTime' => 600
			);

			$cache_manager = new Cache_Lite($options);

			$web_stat = $cache_manager->get('web_statistics', 'admin');
			if (empty($web_stat)) {
				$web_stat = $this->web_statistics();
				$cache_manager->save($web_stat, 'web_statistics', 'admin');
			}

			$system_stat = $cache_manager->get('system_statistics', 'admin');
			if (empty($system_stat)) {
				$system_stat = $this->system_statistics();
				$cache_manager->save($system_stat, 'system_statistics', 'admin');
			}

			$today_stat = $cache_manager->get('today_statistics', 'admin');
			if (empty($today_stat)) {
				$today_stat = $this->today_statistics();
				$cache_manager->save($today_stat, 'today_statistics', 'admin');
			}

			$signup_stat = $cache_manager->get('signup_statistics', 'admin');
			if (empty($signup_stat)) {
				$signup_stat = $this->signup_statistics();
				$cache_manager->save($signup_stat, 'signup_statistics', 'admin');
			}

			$content_template->assign('web_statistics', $web_stat);
			$content_template->assign('system_statistics', $system_stat);
			$content_template->assign('today_statistics', $today_stat);
			$content_template->assign('signup_statistics', $signup_stat);

			$log_file = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . 'logs/' . date('Y') . '-' . date('m') . '.txt';
			$handle = @fopen($log_file, "r");
			if ($handle) {
				$i = 0;
			    while (!feof($handle) && ($i<10)) {
			        $buffer .= str_replace(',', ', ', str_replace('"', '', fgets($handle, 4096)));
					$i++;
			    }
			    fclose($handle);
				$content_template->assign('activity_log', strval($buffer));
			} else {
				$content_template->assign('activity_log', strval(''));
			}
			$content_template->assign('activity_log_link', strval(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'download.php?file=' . VIVVO_FS_FILES_DIR . 'logs/' . date('Y') . '-' . date('m') . '.txt'));

			return $content_template;
		}

		function _leftnav() {

			$content_template = $this->load_template($this->_template_root . 'tabs.xml');

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Comments.class.php');
			$comments_list = new Comments_list();
			$content_template->assign('pending_comments', $comments_list->get_count(array('search_status' => 0)));

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Users.class.php');
			$users_list = new Users_list();
			$content_template->assign('pending_users', $users_list->get_count(array('search_activated' => 0)));


			return $content_template;
		}

		private function signup_statistics() {
			$template = $this->load_template($this->_template_root . 'signup_statistics.xml');
			$sm = vivvo_lite_site::get_instance();
			$db = $sm->get_db();
			$yesterday = date('Y-m-d', strtotime('yesterday'));
			$week = date('Y-m-d', strtotime('last Sunday + 1 day'));
			$this_month = date('Y-m-01');
			$last_month = date('Y-m-01', strtotime('last month'));

			$groups = $sm->get_user_manager()->get_groups_without_privilege('ACCESS_ADMIN');
			$groups = implode(',', array_keys($groups));

			$query = 'SELECT COUNT(*)
					  FROM ' . VIVVO_DB_PREFIX . 'users AS u
					  LEFT JOIN ' . VIVVO_DB_PREFIX ."group_user AS gu ON gu.user_id = u.userid
					  WHERE gu.group_id IN ($groups)";

			$res = $db->query($query);
			if (!PEAR::isError($res)) {
				$total_signups = $res->fetchOne();
				$res->free();
			} else {
				$total_signups = 0;
			}

			$res = $db->query($query . " AND CAST(`created` AS DATE) = '$yesterday'");
			if (!PEAR::isError($res)) {
				$yesterday_signups = $res->fetchOne();
				$res->free();
			} else {
				$yesterday_signups = 0;
			}

			$res = $db->query($query . " AND CAST(`created` AS DATE) >= '$week'");
			if (!PEAR::isError($res)) {
				$this_week_signups = $res->fetchOne();
				$res->free();
			} else {
				$this_week_signups = 0;
			}

			$res = $db->query($query . " AND CAST(`created` AS DATE) >= '$this_month'");
			if (!PEAR::isError($res)) {
				$this_month_signups = $res->fetchOne();
				$res->free();
			} else {
				$this_month_signups = 0;
			}

			$res = $db->query($query . " AND CAST(`created` AS DATE) >= '$last_month' AND CAST(`created` AS DATE) < '$this_month'");
			if (!PEAR::isError($res)) {
				$last_month_signups = $res->fetchOne();
				$res->free();
			} else {
				$last_month_signups = 0;
			}

			$template->assign('total_signups', $total_signups);
			$template->assign('yesterday_signups', $yesterday_signups);
			$template->assign('this_week_signups', $this_week_signups);
			$template->assign('this_month_signups', $this_month_signups);
			$template->assign('last_month_signups', $last_month_signups);

			return $template->get_output();
		}

		function today_statistics() {
			$content_template =& $this->load_template($this->_template_root . 'today_statistics.xml');
			$sm = vivvo_lite_site::get_instance();

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$articles_list = new Articles_list();

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Comments.class.php');
			$comments_list = new Comments_list();

			$db =& $sm->get_db();

			$currently_visitors = 0;

            $currently_visitors = $this->getOnlineUsers();

			$content_template->assign('currently_visitors', strval($currently_visitors));

			$res = $db->query('SELECT sum(today_read) as today_articles_view FROM '.VIVVO_DB_PREFIX.'articles_stats WHERE last_read >= \'' . date('Y-m-d') . '\'');

			if (!PEAR::isError($res)) {
				if ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
					$today_articles_view = $row['today_articles_view'];
				}
			}
			$content_template->assign('today_articles_view', intval($today_articles_view));

			$comments_list->reset_list_query();
			$content_template->assign('today_comments', strval($comments_list->get_count(array('search_search_date' => 1, 'search_before_after' => 1))));

			$user_mng = $sm->get_user_manager();
			$user_list = $user_mng->get_user_list();

			$user_list->reset_list_query();
			$content_template->assign('today_users', strval($user_list->get_count(array('search_search_date' => 1, 'search_before_after' => 1))));

			return $content_template->get_output();
		}
		
		private function getOnlineUsers(){ 
            $max_idle_time = 5; //idle in minutes
            
            $count = 0; 
            if ($directory_handle = opendir(session_save_path())) { 
                
                while (false !== ($file = readdir($directory_handle))) { 
                    if($file != '.' && $file != '..') {
                        // Comment the 'if(...){' and '}' lines if you get a significant amount of traffic 
                        //if (time()- fileatime(session_save_path() . '/' . $file) < $max_idle_time * 60) { 
                            $count++;
                        //} 
                    } 
                }
                closedir($directory_handle);
            } 
            return $count;
        } 

		function system_statistics() {
			$content_template = $this->load_template($this->_template_root . 'system_statistics.xml');
			$sm = vivvo_lite_site::get_instance();

			$user_mng = $sm->get_user_manager();
			$user_list = $user_mng->get_user_list();
			$content_template->assign('system_staff', strval($user_list->get_count(array('search_user_type' => 'staff'))));

			$fm = $sm->get_file_manager();

			$tpl_dir = new dir_list(null, VIVVO_TEMPLATE_DIR, '', VIVVO_FS_TEMPLATE_ROOT);
			$tpl_dir->allowed_ext = array('tpl');
			$content_template->assign('system_templates', strval($tpl_dir->get_total_number()));
			$content_template->assign('system_templates_size', number_format($tpl_dir->get_total_file_size() / (1024 * 1024), 2, '.', ' '));

			$tpl_dir->set__root_dir(VIVVO_FS_ROOT);
			$tpl_dir->set_dir(VIVVO_FS_FILES_DIR);
			$tpl_dir->allowed_ext = array();
			$content_template->assign('system_files', strval($tpl_dir->get_total_number()));
			$content_template->assign('system_files_size', number_format($tpl_dir->get_total_file_size() / (1024 * 1024), 2, '.', ' '));

			$tpl_dir->set__root_dir(VIVVO_FS_ROOT);
			$tpl_dir->set_dir('backup/');
			$tpl_dir->allowed_ext = array('sql', 'gz');
			$content_template->assign('system_backup', strval($tpl_dir->get_total_number()));
			$content_template->assign('system_backup_size', number_format($tpl_dir->get_total_file_size() / (1024 * 1024), 2, '.', ' '));

			$tpl_dir->set__root_dir(VIVVO_FS_ROOT);
			$tpl_dir->set_dir('cache/');
			$tpl_dir->allowed_ext = array();
			$content_template->assign('system_cache_size', number_format($tpl_dir->get_total_file_size() / (1024 * 1024), 2, '.', ' '));

			$db = $sm->get_db();
			$res = $db->query("SHOW TABLE STATUS");

			$db_size = 0;
			if (!PEAR::isError($res)) {
				while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)){
					$db_size += $row['data_length'] + $row['index_length'];
				}
			}

			$content_template->assign('system_database', number_format($db_size / (1024 * 1024), 2, '.', ' '));

			return $content_template->get_output();
		}

		function web_statistics () {
			$content_template = $this->load_template($this->_template_root . 'web_statistics.xml');
			$sm = vivvo_lite_site::get_instance();
            $db =& $sm->get_db();
            
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Categories.class.php');
			$cat_list = new Categories_list();
			$content_template->assign('website_categories', strval($cat_list->get_count()));

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');
			$tag_list = new Tags_list();
			$content_template->assign('website_tags', strval($tag_list->get_count()));

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$articles_list = new Articles_list();

			$content_template->assign('website_articles', strval($articles_list->get_count()));

			$content_template->assign('website_articles_active', strval($articles_list->get_count(array('search_status' => 1))));

			$res = $db->query('SELECT sum(times_read) as times_view FROM '.VIVVO_DB_PREFIX.'articles_stats');

            if (!PEAR::isError($res)) {
                if ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
                    $times_view = $row['times_view'];
                }
            }
            $content_template->assign('website_articles_view', intval($times_view));

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Comments.class.php');
			$comments_list = new Comments_list();
			$content_template->assign('website_comments', strval($comments_list->get_count()));

			$user_mng = $sm->get_user_manager();
			$user_list = $user_mng->get_user_list();
			$content_template->assign('system_staff', strval($user_list->get_count(array('search_user_type' => 'staff'))));

			return $content_template->get_output();
		}
	}
?>