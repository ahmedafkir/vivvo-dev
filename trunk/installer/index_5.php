<?php
/* =============================================================================
 * $Revision: 5442 $
 * $Date: 2010-06-04 10:21:14 +0200 (Fri, 04 Jun 2010) $
 *
 * Vivvo CMS v4.5 (build 5661)
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

defined('VIVVO_FS_FILES_DIR') or define('VIVVO_FS_FILES_DIR', 'files/');

try {
    define('SYSTEM_DEFAULT_TIMEZONE', date_default_timezone_get());
}catch (Exception $e) {
    define('SYSTEM_DEFAULT_TIMEZONE', 'GMT');
}

/**
 * Checks directories and files read/write flags.
 *
 * @param string $dir
 * @return array
 */
function check_permission($dir) {
	$check = array();
	if (file_exists(VIVVO_FS_ROOT . $dir . '/') && is_dir (VIVVO_FS_ROOT . $dir)){
		if (!is_readable(VIVVO_FS_ROOT . $dir) || !is_writable(VIVVO_FS_ROOT . $dir) || !@file_exists(VIVVO_FS_ROOT . $dir . '/.')){
			$check[] = $dir;
		}
		if ($handle = opendir(VIVVO_FS_ROOT . $dir)) {
			while (false !== ($filename = readdir($handle))) {
        		if ($filename[0] != '.' && is_dir(VIVVO_FS_ROOT . $dir . '/' . $filename)) {
        			$check = array_merge($check, check_permission($dir . '/' . $filename));
        		} elseif ($filename[0] != '.') {
	        		if (!is_readable(VIVVO_FS_ROOT . $dir . '/' . $filename) || !is_writable(VIVVO_FS_ROOT . $dir . '/' . $filename)){
						$check[] = $dir . '/' . $filename;
					}
        		}
        	}
        }
	}
	return $check;
}

/**
 * Step 1, installer start screen, installer language selection.
 *
 * @param template $template
 * @param dump_manager $dm
 * @param vivvo_lang $lang
 * @return void
 */
function step1 (&$template, &$dm, &$lang) {
	$file = array();
	if ($handle = opendir(VIVVO_FS_ROOT . 'installer/lang')) {
		while (false !== ($filename = readdir($handle))) {
       		if ($filename[0] != '.' && !is_dir(VIVVO_FS_ROOT . $dir . $filename)) {
       			$file[] = str_replace('.php','', $filename);
       		}
       	}
	}
	asort($file);
	$template->assign('file_list', $file);
	$template->assign('step', strval(1));
}

/**
 * Step 2, checks various server requirements and shows what is available.
 *
 * @param template $template
 * @param dump_manager $dm
 * @param vivvo_lang $lang
 * @return void
 */
function step2 (&$template, &$dm, &$lang) {
	// PHP Version
	$template->assign('php_version', phpversion());
	$template->assign('php_check', version_compare(PHP_VERSION, '5.2.0', 'ge'));

	// MySQL version
	$mysql_version = mysql_get_client_info();
	$template->assign('mysql_version', substr($mysql_version, 0, 6));
	$template->assign('mysql_check', version_compare($mysql_version, '4.1', 'ge'));

	// ZLib
	$template->assign('zlib_check', intval(ini_get('zlib.output_compression') or function_exists('ob_gzhandler')));

	// Remote communication (fsockopen, file_get_contents, curl)

	// Mail
	$template->assign('mail_check', intval(@mail('example@example.com', 'Subject', 'Body')));

	// Mod rewrite module
	$mod_rw_check = 0;
	if (function_exists('apache_get_modules')) {
		$apache_modules = apache_get_modules();
		if (array_search('mod_rewrite', $apache_modules)){
			$mod_rw_check = 1;
		}
		else $mod_rw_check = 0;
	}
	else $mod_rw_check = @fopen(VIVVO_URL . 'check/mod_rewrite/', 'r') ? 1 : 2;
	$template->assign('mod_rw_check', $mod_rw_check);

	// File folder writable
	$file_created = 0;
	$fc = @fopen(dirname(__FILE__) . '/../' . VIVVO_FS_FILES_DIR . 'test_file_create.php', 'w');
	if ($fc !== false) {
		$file_created = 1;
		@fclose($fc);
		unlink(dirname(__FILE__) . '/../' . VIVVO_FS_FILES_DIR . 'test_file_create.php');
	}
	$template->assign('file_created', $file_created);

	// GD library
	$gd_check= 0;
	if (function_exists('gd_info')) {
		$gd_info = gd_info();
		$gd_check = 1;
		$template->assign('gd_info', $gd_info);
	}
	$template->assign('gd_check', $gd_check);

	// Files permissions
	$file_perm = array();

	if (!is_readable(VIVVO_FS_ROOT . 'conf.php') || !is_writable(VIVVO_FS_ROOT . 'conf.php')) {
		$file_perm[] = 'conf.php';
	}
	$file_perm = array_merge($file_perm, check_permission('templates'));
	$file_perm = array_merge($file_perm, check_permission('themes'));
	$file_perm = array_merge($file_perm, check_permission('files'));
	$file_perm = array_merge($file_perm, check_permission('backup'));
	$file_perm = array_merge($file_perm, check_permission('cache'));
	$file_perm = array_merge($file_perm, check_permission('modifiers'));

	if (empty($file_perm)) {
		$file_check = 1;
	} else {
		$file_check = 0;
		$template->assign('file_fix', $file_perm);
	}
	$template->assign('file_check', $file_check);

	$template->assign('step', strval(2));
}

/**
 * Step 3, validates server requirements from step 2 and shows licence screen.
 *
 * @param template $template
 * @param dump_manager $dm
 * @param vivvo_lang $lang
 * @return void
 */
function step3 (&$template, &$dm, &$lang) {
	$template->assign('step', strval(3));
}

/**
 * Step 4, validates licence agreement and shows database configuration screen.
 *
 * @param template $template
 * @param dump_manager $dm
 * @param vivvo_lang $lang
 * @return void
 */
function step4 (&$template, &$dm, &$lang) {
	if (!isset($_POST['agree']) || $_POST['agree'] != 1) {
		$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_YOU_MUST_ACCEPT'));
		$template->assign('step', strval(3));
	} else {
		$template->assign('step', strval(4));
	}
}

/**
 * Step 5, validates database configuration, shows common properties screen,
 * creates conf.php, validates MySQL version, runs scheme/data sql scripts
 * and installs default plugins.
 *
 * @param template $template
 * @param dump_manager $dm
 * @param vivvo_lang $lang
 * @return void
 */
function step5 (&$template, &$dm, &$lang) {
	$host = $_REQUEST['MYSQL_host'];
	$database = $_REQUEST['MYSQL_database'];
	$user = $_REQUEST['MYSQL_user'];
	$password = $_REQUEST['MYSQL_password'];
	$url = $_REQUEST['URL'];
	$tbl_prefix = $_REQUEST['MYSQL_prefix'];
	$tbl_prefix = preg_replace('/[^a-zA-Z\d\_]/i', '_', $tbl_prefix);

	if ($host == '' or $user == '' or $password == '') {
		$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_MISSING_DATA'));
		$template->assign('step', strval(4));
		$template->assign('MYSQL_DBNAME', $database);
		$template->assign('MYSQL_DBUSER', $user);
		$template->assign('MYSQL_DBPASS', $password);
		$template->assign('MYSQL_TABLE_PREFIX', $tbl_prefix);
	} else {
		if ($db = @mysql_connect("$host", "$user", "$password")) {
			if (mysql_select_db($database, $db)) {
				@mysql_query('SET NAMES utf8', $db);
				$conf_file = dirname(__FILE__) . '/../conf.php';
		        $fp = @fopen($conf_file, 'w');
		        if ($fp !== false) {
			        $body = "<?php\n";
			        $body.= "define('VIVVO_URL', '" . $url . "');" . "\n";
					$body.= "define('VIVVO_DB_HOST', '" . $host . "');" . "\n";
					$body.= "define('VIVVO_DB_USER', '" . $user . "');" . "\n";
					$body.= "define('VIVVO_DB_PASSWORD', '" . $password . "');" . "\n";
					$body.= "define('VIVVO_DB_DATABASE', '" . $database . "');" . "\n";
					$body.= "define('VIVVO_DB_PREFIX', '" . $tbl_prefix . "');" . "\n";
					$body.= "define('VIVVO_DB_TYPE', 'mysql');" . "\n";
					$body.= "define('VIVVO_DOMAIN', 'localhost');" . "\n";
					$body.= "define('VIVVO_FS_ROOT', str_replace('\\\\', '/', realpath(dirname(__FILE__))) . \"/\");" . "\n";
					$body.= "define('VIVVO_FS_INSTALL_ROOT', VIVVO_FS_ROOT);" . "\n";
					$body.= "define('VIVVO_FS_PLUGIN_ROOT', VIVVO_FS_ROOT);" . "\n";
					$body.= "define('VIVVO_FS_LANG_ROOT', VIVVO_FS_ROOT);" . "\n";
					$body.= "define('VIVVO_FS_TEMPLATE_ROOT', VIVVO_FS_ROOT);" . "\n";
					$body.= "define('VIVVO_FS_ADMIN_ROOT', VIVVO_FS_ROOT);" . "\n";
					$body.= "define('VIVVO_FS_ADMIN_DIR', 'admin/');" . "\n";
			        $body.= "#EOF\n";
			        fwrite($fp, $body);
			        @fclose($fp);

			        define('VIVVO_DB_PREFIX', $tbl_prefix);
			        define('VIVVO_DB_DATABASE', $database);

			        // mod_rewrite
		        	$mod_rw_check = 0;
					if (function_exists('apache_get_modules')) {
						$apache_modules = apache_get_modules();
						if (array_search('mod_rewrite', $apache_modules)) {
							$mod_rw_check = 1;
						}
					} else {
						$fp = @fopen(VIVVO_URL . 'check/mod_rewrite/', 'r');
						$mod_rw_check = ($fp !== false) ? 1 : 0;
					}
					define('MOD_RW_CHECK', $mod_rw_check);

					// MySql
					if (version_compare(mysql_get_client_info(), '4.1', 'ge')) {
						require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/sqlrunner.class.php';

						// load sql runner
						$sqlRunner = new SQLRunner(array('db' => $db, 'stop_on_error' => true));

						// Execute scheme.sql
						if ($sqlRunner->load(dirname(__FILE__) . '/db/scheme.sql') > 0) {
							try {
								$scheme = $sqlRunner->run(null, false);
							} catch (SQLRunnerException $e) {
								$dm->add_dump('error', 0, $lang->get_value('LNG_INSTALLER_ERROR_EXECUTING_SQL'), $e->getMessage());
								$template->assign('step', strval(4));
								return;
							}
						} else {
							$dm->add_dump('error', 0, $lang->get_value('LNG_INSTALLER_INVALID_SQL_FILE') . 'scheme.sql');
							$template->assign('step', strval(4));
							return;
						}



						// Execute data.sql
						if ($sqlRunner->load(dirname(__FILE__) . '/db/data.sql') > 0) {
							try {
								$data = $sqlRunner->run();
							} catch (SQLRunnerException $e) {
								$dm->add_dump('error', 0, $lang->get_value('LNG_INSTALLER_ERROR_EXECUTING_SQL'), $e->getMessage());
								$template->assign('step', strval(4));
								return;
							}
						} else {
							$dm->add_dump('error', 0, $lang->get_value('LNG_INSTALLER_INVALID_SQL_FILE') . 'data.sql)');
							$template->assign('step', strval(4));
							return;
						}

						$template->assign('query_list', array_merge($scheme, $data));
					} else {
						$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_INVALID_MYSQL_VERSION') . mysql_get_client_info());
						$template->assign('step', strval(4));
						return;
					}

					// Install plugins
					ob_start();

					require_once (VIVVO_FS_ROOT . 'conf.php');

					$sm = vivvo_lite_site::get_instance();
					$sm->define_global_param();
					$sm->set_template_dir();

					// Video Box
		        	if (file_exists(VIVVO_FS_ROOT . 'plugins/video_box/video_box_definition.class.php')){
						require_once(VIVVO_FS_ROOT . 'plugins/video_box/video_box_definition.class.php');
						$video_box_definition = new video_box_definition($sm);
						$video_box_definition->install($sm, 2);
					}

		        	// Image Gallery
		        	if (file_exists(VIVVO_FS_ROOT . 'plugins/image_gallery/image_gallery_definition.class.php')){
						require_once (VIVVO_FS_ROOT . 'plugins/image_gallery/image_gallery_definition.class.php');
						$image_gallery_definition = new image_gallery_definition($sm);
						$image_gallery_definition->install($sm, 2);
					}

					// Multiple Attachments
		        	if (file_exists(VIVVO_FS_ROOT . 'plugins/multiple_attachments/multiple_attachments_definition.class.php')){
						require_once (VIVVO_FS_ROOT . 'plugins/multiple_attachments/multiple_attachments_definition.class.php');
						$multiple_attachments_definition = new multiple_attachments_definition($sm);
						$multiple_attachments_definition->install($sm, 2);
					}
					
					// Form builder install (if present)
					if (file_exists(VIVVO_FS_ROOT . 'plugins/form_builder/form_builder_definition.class.php')){
                        require_once (VIVVO_FS_ROOT . 'plugins/form_builder/form_builder_definition.class.php');
                        $form_builder_definition = new form_builder_definition($sm);
                        $form_builder_definition->install($sm, 2);
                    }

					ob_end_clean();
					$template->assign('step', strval(5));
				} else {
					$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_CANT_CREATE_CONFIG_FILE'));
					$template->assign('step', strval(4));
				}
			} else {
				$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_CANT_SELECT_DATABASE'));
				$template->assign('step', strval(4));
			}
		} else {
			$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_CANT_CONNECT_TO_DATABASE'));
			$template->assign('step', strval(4));
		}
	}
}

/**
 * Step 6, sets administration parameters in database and creates welcome email.
 * At this step installation is finished and user can choose further action.
 *
 * @param template $template
 * @param dump_manager $dm
 * @param vivvo_lang $lang
 * @return void
 */
function step6 (&$template, &$dm, &$lang) {
	require_once(dirname(__FILE__) . '/../conf.php');
	if ($db = @mysql_connect(VIVVO_DB_HOST, VIVVO_DB_USER, VIVVO_DB_PASSWORD)) {
		if (mysql_select_db(VIVVO_DB_DATABASE, $db)) {
			@mysql_query('SET NAMES utf8', $db);
			$web_site_title = $_REQUEST['web_site_title'];
			$administrator_email = $_REQUEST['administrator_email'];
			$admin_username = $_REQUEST['admin_username'];
			$admin_password = $_REQUEST['admin_password'];
			$admin_password_retype = $_REQUEST['admin_password_retype'];

			if (!preg_match("/^[-_a-zA-Z0-9]+(\.[-_a-zA-Z0-9]+)*@[-a-zA-Z0-9]+(\.[-a-zA-Z0-9]+)*\.[a-zA-Z]{2,6}$/", $administrator_email)){
				$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_WRONG_EMAIL'));
				$template->assign('step', strval(5));
			} elseif ($admin_username == ''){
				$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_WRONG_USERNAME'));
				$template->assign('step', strval(5));
			} elseif (strlen($admin_password) < 6){
				$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_PASSWORD_MINIMUM_6_CHAR'));
				$template->assign('step', strval(5));
			} elseif ($admin_password != $admin_password_retype){
				$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_PASSWORD_AND_RETYPE_PASSWORD_MUST_BE_SAME'));
				$template->assign('step', strval(5));
			} else {
				define('VIVVO_WEBSITE_TITLE', $web_site_title);
				define('VIVVO_ADMINISTRATORS_EMAIL', $administrator_email);
				define('VIVVO_EMAIL_SEND_FROM', $administrator_email);
				define('VIVVO_ADMIN_USERNAME', $admin_username);
				define('VIVVO_ADMIN_PASSWORD', $admin_password);

				require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/sqlrunner.class.php';

				// load sql runner
				$sqlRunner = new SQLRunner(array('db' => $db, 'stop_on_error' => true));

				// Execute scheme.sql
				if ($sqlRunner->load(dirname(__FILE__) . '/db/post-install.sql') > 0) {
					try {
						$scheme = $sqlRunner->run();
					} catch (SQLRunnerException $e) {
						$dm->add_dump('error', 0, $lang->get_value('LNG_INSTALLER_ERROR_EXECUTING_SQL'), $e->getMessage());
						$template->assign('step', strval(4));
						return;
					}
				} else {
					$dm->add_dump('error', 0, $lang->get_value('LNG_INSTALLER_INVALID_SQL_FILE') . 'post-install.sql');
					$template->assign('step', strval(4));
					return;
				}

				$template->assign('step', strval(6));

				// Send information email
				$header = "From: Vivvo CMS Installation Wizard <" . $administrator_email . ">\r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-type: text/html; charset=utf-8\r\n";

				$subject = "=?UTF-8?B?" . base64_encode('Installation Complete') . "?=";

				$email_template = new template(null, $template);
				$email_template->set_template_file(VIVVO_FS_ROOT . 'installer/templates/email.xml');
				$body = $email_template->get_output();

				@mail($administrator_email, $subject, $body, $header);
			}
		} else {
			$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_CANT_SELECT_DATABASE'));
			$template->assign('step', strval(5));
		}
	} else {
		$dm->add_dump('info', 0, $lang->get_value('LNG_INSTALLER_CANT_CONNECT_TO_DATABASE'));
		$template->assign('step', strval(5));
	}
}

/**
 *
 * Start installation steps
 *
 */
error_reporting(E_ERROR);
set_time_limit(300);
function_exists('xml_parser_create') or die('Your server does not meet the requirements. XML parser support is missing!');
define('VIVVO_START_TIME', time());

$sub_path = $_SERVER['PHP_SELF']; // /installer/index.php
$sub_path = substr($sub_path, 0, strrpos($sub_path, '/'));
$sub_path = substr($sub_path, 0, strrpos($sub_path, '/') + 1);
$url = 'http://' . $_SERVER['HTTP_HOST'] . $sub_path;

define('VIVVO_INSTALLER', 1);
define('VIVVO_URL', $url);
define('VIVVO_DOMAIN', 'localhost');
define('VIVVO_DB_TYPE', 'mysql');
define('VIVVO_FS_ROOT', realpath(dirname(__FILE__). '/../') . '/');
define('VIVVO_FS_INSTALL_ROOT', VIVVO_FS_ROOT);
define('VIVVO_FS_PLUGIN_ROOT', VIVVO_FS_ROOT);
define('VIVVO_FS_LANG_ROOT', VIVVO_FS_ROOT);
define('VIVVO_FS_TEMPLATE_ROOT', VIVVO_FS_ROOT);
define('VIVVO_FS_ADMIN_ROOT', VIVVO_FS_ROOT);
define('VIVVO_FS_ADMIN_DIR', 'admin/');

require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php');
$sm = vivvo_lite_site::get_instance();
$dm = $sm->get_dump_manager();
$template = $sm->get_template();
$template->set_template_file(VIVVO_FS_ROOT . 'installer/templates/frame.xml');
$installer_lang = $sm->get_lang();

if (isset($_POST['installer_lang'])){
	setcookie('installer_lang', $_POST['installer_lang']);
	$_COOKIE['installer_lang'] = $_POST['installer_lang'];
}

if (isset($_COOKIE['installer_lang'])){
	$installer_lang->load_lang($_COOKIE['installer_lang'], 'installer/lang/', 'installer');
} else {
	$installer_lang->load_lang($_COOKIE['installer_lang'] = 'english', 'installer/lang/', 'installer');
}

define('VIVVO_INSTALLER_LANG', $_COOKIE['installer_lang']);

$um = $sm->get_url_manager();
$lang = $sm->get_lang();

if ($um->get_param('step') == 2) {
	step2($template, $dm, $lang);
} elseif ($um->get_param('step') == 3) {
	step3($template, $dm, $lang);
} elseif ($um->get_param('step') == 4) {
	step4($template, $dm, $lang);
} elseif ($um->get_param('step') == 5) {
	step5($template, $dm, $lang);
} elseif ($um->get_param('step') == 6) {
	step6($template, $dm, $lang);
	vivvo_cache::get_instance()->delete_all();
} else {
	step1($template, $dm, $lang);
}

$template->assign('DUMP_MESSAGES', $dm->_dump_pool);

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
echo $sm->get_output();
?>