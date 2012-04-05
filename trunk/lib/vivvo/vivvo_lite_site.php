<?php
/* =============================================================================
 * $Revision: 5746 $
 * $Date: 2010-09-02 16:03:43 +0200 (Thu, 02 Sep 2010) $
 *
 * Vivvo CMS v4.7 (build 6084)
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
 * Vivvo CMS
 *
 * @package    	Vivvo
 * @subpackage 	framework
 * @author     	Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @copyright  	2007-2012 Spoonlabs d.o.o.
 * @license    	http://www.vivvo.net/license.php
 * @version    	4.7
 * @category   	CMS
 */

defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time());

error_reporting(E_ERROR);

require_once (dirname(__FILE__) . '/framework/vivvo_framework.php');
require_once VIVVO_FS_FRAMEWORK . 'vivvo_cache.php';
require_once VIVVO_FS_FRAMEWORK . 'vivvo_hooks.php';

/**#@+
 * Constant
 */

define('VIVVO_VERSION', "v4.7");
define('VIVVO_CODE_NAME', "MARTYR");
define('VIVVO_CODE_REVISION', "6084");
define('VIVVO_LICESE_TOKEN', "d6ae32fa49c9a2812d0d3b0ebe70f44a");
define('VIVVO_PRODUCT_COPYRIGHT', "© 2012 Spoonlabs d.o.o.");

define('VIVVO_HEADER_MERGE_JS', false);
define('VIVVO_HEADER_COMPRESS_JS', false);

set_include_path(VIVVO_FS_INSTALL_ROOT . 'lib' . PATH_SEPARATOR . get_include_path());

defined('VIVVO_STATIC_URL') or define('VIVVO_STATIC_URL', VIVVO_URL);

	/**
	 * Site manager ("lite")
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	framework
	 * @version		0.2
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	*/
	class vivvo_lite_site extends vivvo_site {

		/**
		 * Categories
		 *
		 * @var category_list
		 */
		var $categories;

		var $article;

		var $article_list_params = array();

		var $printed_articles = array();

		var $page_title = '';

		var $guest_group = false;

		/**
		 * Set main category list
		 *
		 * @param integer $root
		 * @return category_list
		 */
		function &set_categories($root = 0){
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Categories.class.php');
			$this->categories = new Categories_list($this, $root);
			return $this->categories;
		}

		/**
		 * Get main category list
		 *
		 * @return category_list
		 */
		function &get_categories(){
			if (!is_a($this->categories, 'Categories_list')){
				return $this->set_categories();
			}else {
				return $this->categories;
			}
		}

		function &set_url_manager(){
			$this->_url_manager = new vivvo_lite_url_manager($this);
			return $this->_url_manager;
		}

		function set_guest_group(){
			$this->guest_group = $this->_user_manager->get_group_by_id(7);
		}

		function set_template_dir(){
			if (!defined('VIVVO_TEMPLATE_DIR')) {
				if (defined ('VIVVO_TEMPLATE_DIR_OVERWRITE') && file_exists(VIVVO_FS_TEMPLATE_ROOT . 'templates/' . VIVVO_TEMPLATE_DIR_OVERWRITE) && is_dir(VIVVO_FS_TEMPLATE_ROOT . 'templates/' . VIVVO_TEMPLATE_DIR_OVERWRITE)){
					define ('VIVVO_TEMPLATE_DIR', 'templates/' . VIVVO_TEMPLATE_DIR_OVERWRITE . '/');
				}elseif (file_exists(VIVVO_FS_TEMPLATE_ROOT . 'templates/' . VIVVO_DEFAULT_TEMPLATE_DIR) && is_dir(VIVVO_FS_TEMPLATE_ROOT . 'templates/' . VIVVO_DEFAULT_TEMPLATE_DIR)){
					define ('VIVVO_TEMPLATE_DIR', 'templates/' . VIVVO_DEFAULT_TEMPLATE_DIR . '/');
				}else{
					define ('VIVVO_TEMPLATE_DIR', 'templates/default/');
				}
			}
		}

		/**
		 * Get url manager
		 *
		 * @return vivvo_url_manager
		 */
		function &get_url_manager(){
			if (!is_a($this->_url_manager, 'vivvo_lite_url_manager')){
				return $this->set_url_manager();
			}else {
				return $this->_url_manager;
			}
		}

		function set_content() {

			require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/box/vivvo_box.php');

			$header = $this->get_header_manager();
			$pm = $this->get_plugin_manager();
			$um = $this->get_url_manager();

			if ($um->get_param('404')) {
				$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/404.tpl');
				$this->_template->assign('URL', htmlspecialchars($um->get_param('url')));
			} elseif ($um->get_param('plugin')) {
				$this->set_theme();
				$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
				$content = $pm->get_content($um->get_param('plugin'));

				if ($content !== false){
					$this->_template->assign('PAGE_CONTENT', $content);
				}
			} elseif ($um->get_param('url_module')) {
				$this->call_url_content_handler($um->get_param('url_module'));
			} else {
				if ($um->get_param('search_id')) {
					$this->call_url_content_handler('article');
				} else {
					require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

					//Article list page

					$cur_page = (int) $um->get_param('pg');
					if (empty($cur_page)) $cur_page = 1;

					$this->article_list_params['search_options'] = array();

					if ($um->get_param('search_cid') && !$um->get_param('search_do_advanced')) {
						$this->call_url_content_handler('category');
					} else if ($um->get_param('search_do_advanced')) {
						if (VIVVO_MODULES_SEARCH == 1 || defined('VIVVO_ADMIN_MODE')) {
							//Search results
							$this->article_list_params = Articles_list::get_search_params_from_url($this);
							$this->_template->assign('PAGE_TITLE', strval($this->_lang->get_value('LNG_SEARCH_RESULTS')));
							if ($um->get_param('type') == 'rss') {
								define('VIVVO_RSS_FEED', true);
								$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/rss2.0.tpl');
							} elseif ($um->isset_param('force_template') && $this->user && $this->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'search_results/' . $um->get_param('force_template'))) {
								$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'search_results/' . $um->get_param('force_template'));
							} else {
								$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'search_results/' . VIVVO_SEARCH_RESULT_LAYOUT);
							}
						} else {
							go_404();
						}
					}

					if (empty($this->article_list_params['search_limit'])) $this->article_list_params['search_limit'] = 10;

					$this->article_list_params['pg'] = $cur_page;
					$this->article_list_params['offset'] = ($cur_page-1) * $this->article_list_params['search_limit'];

					if ($um->isset_param('search_sort_by')) {
						$this->article_list_params['search_sort_by'] = $um->get_param('search_sort_by');
					}

					if ($um->isset_param('search_order')) {
						$this->article_list_params['search_order'] = $um->get_param('search_order');
					}

					if (empty($this->article_list_params['search_sort_by'])) {
						$this->article_list_params['search_sort_by'] = 'order_num';
						$this->article_list_params['search_order'] = 'descending';
					} else {
						if (empty($this->article_list_params['search_order'])) {
							$this->article_list_params['search_order'] = 'ascending';
						}
					}

					if (!defined('VIVVO_ADMIN_MODE')) {
						if (defined('VIVVO_CACHE_ENABLE') && (VIVVO_CACHE_ENABLE == 1) && !isset($this->article_list_params['cache'])) $this->article_list_params['cache'] = 1;
					}

					$this->_template->assign('content_params', $this->article_list_params);
					if (isset($this->article_list_params['search_options'])) {
						$this->_template->assign('CONTENT_PARAMS_OPTIONS', $this->article_list_params['search_options']);
					}
				}
			}
		}

		function set_theme($theme = '', $print = false) {
			if (!defined('VIVVO_THEME')) {
				if (!defined('VIVVO_FS_THEME_ROOT')) {
					define ('VIVVO_FS_THEME_ROOT', VIVVO_FS_TEMPLATE_ROOT);
				}
				if ($this->_url_manager->isset_param('force_theme') && $this->user && $this->user->is_admin() && file_exists(VIVVO_FS_THEME_ROOT . 'themes/' . $this->_url_manager->get_param('force_theme'))){
					$theme = $this->_url_manager->get_param('force_theme');
					if (!preg_match('/\/$/', $theme)) $theme .= '/';
				}

				if ($theme == '') {
					$theme = VIVVO_DEFAULT_THEME;
				}

				define ('VIVVO_THEME', VIVVO_STATIC_URL . 'themes/' . $theme . '/');
				if (!$print) {
					$this->_header_manager->add_css(array(
						VIVVO_THEME . 'css/layout_default.css',
						VIVVO_THEME . 'css/style.css',
						VIVVO_THEME . 'css/forms.css'));

					$this->_header_manager->add_css(VIVVO_THEME . 'css/handheld.css', 'handheld');
				} else {
					$this->_header_manager->add_css(VIVVO_THEME . 'css/print.css');
				}
			}
		}

		function fb_debug_output() {
			require_once (VIVVO_FS_INSTALL_ROOT . 'lib/firePHP/fb.php');
			if (!empty($this->_debug_stack)) {
				foreach ($this->_debug_stack as $debug) {
					if ($debug[2] == 0){
						if (isset($_REQUEST['dump_info'])) fb($debug[0] . ' ' . $debug[1], FirePHP::LOG);
					} else {
						fb($debug[0] . ' ' .  $debug[1], FirePHP::ERROR);
					}
				}
				if (isset($_REQUEST['dump_info'])) {
					fb($this->_url_manager->list, 'URL manager', FirePHP::LOG);
					$_config = array(array('Name','Propery','Value', 'Module'));
					foreach ($this->_configuration->_config_data as $conf) {
						array_push($_config, array($conf['variable_name'], strval($conf['variable_property']), strval($conf['variable_value']), strval($conf['module'])));
					}
					fb(array('Vivvo Configuration', $_config), FirePHP::TABLE);
				}
			}
		}

		/**
		 * Get site output
		 *
		 * @return string
		 */
		function get_output() {
			$output = $this->_template->get_output();

			if (!defined('VIVVO_INSTALLER')) {
				$output = str_replace('{$css__}', $this->_header_manager->get_css_output(), $output);
				$output = str_replace('{$scripts__}', $this->_header_manager->get_script_output(), $output);
				$output = str_replace('{$rss__}', $this->_header_manager->get_rss_output(), $output);
				$output = str_replace('{$meta_names__}', $this->_header_manager->get_meta_output(), $output);

				if (!defined('VIVVO_ADMIN_MODE')){
							$add = false;

					if ($add){
						$output = str_replace('</body>', '<div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10px; margin-top: -13px;">Powered by Vivvo CMS ' . VIVVO_VERSION . '</div></body>', $output);
					}
				}
				if (defined('VIVVO_DEBUG_MODE')) {
					if (preg_match('/<\/body>/', $output)) {
						$output = str_replace('</body>', $this->format_debug_output(), $output);
					} else {
						print_r($this->_debug_stack);
					}
				}
				if ($this->user && $this->user->is_admin()) {
					$this->fb_debug_output();
				}
			}
			if ($this->_db) {
				$this->_db->disconnect();
			}
			return $output;
		}

		function &get_admin_view($view) {
			if (file_exists(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/admin_view/' . $view . '_admin_view.class.php')){
				require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/admin_view/' . $view . '_admin_view.class.php');

				$class = $view . '_admin_view';
				$view_obj =& new $class($this);
				if ($view_obj->check_privileges()) {
					return $view_obj;
				}
			}
			return false;
		}

		/**
		 * Static property for vivvo_lite_site singleton pattern implementation.
		 *
		 * @var vivvo_lite_site
		 */
		private static $instance;

		/**
		 * Returns singleton object instance of vivvo_lite_site, or
		 * creates an instance if not exist.
		 *
		 * @return vivvo_lite_site
		 */
		public static function &get_instance() {
			if (self::$instance === null)
				new self;
			return self::$instance;
		}

		/**
		 * vivvo_lite_site class constructor.
		 *
		 * @return void
		 */
		public function __construct() {
			self::$instance = $this;
			vivvo_cache::get_instance();

			if (!defined('VIVVO_INSTALLER')){
				$this->set_db();
				$this->start_session();
				$this->define_global_param();

				vivvo_hooks_manager::load(); //load a list of registered hook handlers

				$this->set_user_manager();
				$this->set_modules();
				$this->set_url_modules();
				$this->set_user();
				if (!$this->user)
					$this->set_guest_group();

				if (isset($_GET['kill_cache']) && $this->user && $this->user->is_admin())
					vivvo_cache::get_instance()->delete_all();
			}

			if (defined('VIVVO_FRIENDY_URL') && VIVVO_FRIENDY_URL == 0){
				if (!defined('VIVVO_SYSTEM_PROXY_URL')){
					define('VIVVO_SYSTEM_PROXY_URL', VIVVO_URL . 'index.php/');
				}
			}

			$this->set_url_manager();

			if (!defined('VIVVO_INSTALLER')){
				$this->set_header_manager();
				$this->set_dump_manager();
				$this->set_plugin_manager();

				if (defined('VIVVO_ROOT_CATEGORY')){
					$this->set_categories(VIVVO_ROOT_CATEGORY);
				}

				$this->define_global_param();

				vivvo_chart::init();

				$this->_url_manager->parse_url();
				$this->set_lang();
				$this->_plugin_manager->load_lang();
				$this->set_template_dir();
				$this->load_action_handlers();
			}

			$this->define_proxy_url();

			if (!defined('VIVVO_FS_THEME_ROOT')){
				define ('VIVVO_FS_THEME_ROOT', VIVVO_FS_TEMPLATE_ROOT);
			}
		}
	}

	class vivvo_lite_url_manager extends vivvo_url_manager {

		/**
		 * Parse argument
		 *
		 * @param	string	$arg
		 * @return	string
		 */
		function parse_request($arg){
			switch ($arg){
				case 'news':
					return 'search_id';
					break;
				case 'category':
					return 'search_cid';
					break;
				case 'author':
					return 'search_author_id';
					break;
				case 'tag':
					return 'search_tag_id';
					break;
				case 'page':
					return 'search_page';
					break;
				case 'archive':
					return 'search_archive';
					break;
				case 'year':
					return 'search_by_year';
					break;
				case 'month':
					return 'search_by_month';
					break;
				case 'day':
					return 'search_by_day';
					break;
				default:
					return $arg;
					break;
			}
		}

		function parse_url($force = false){
			//TODO srediti non friendly urls

			if (vivvo_hooks_manager::call('url_parse_pre', array(&$this, &$force)) == false) {
				return;
			}

			if (!$force && (defined('VIVVO_ADMIN_MODE') || defined('VIVVO_SKIP_URL_PARSING'))){
				return;
			}

			$sm = vivvo_lite_site::get_instance();
			$pm = $sm->get_plugin_manager();
			$user = $sm->user;

			empty($this->_url_array) and $this->_url_array[0] = 'index.php'; // default is index

			// Fix for #594 (plain text version on article preview)
			if ($user and $user->can('ACCESS_ADMIN') and count($this->_url_array) == 1 and $this->_url_array[0] == 'index.php' and !empty($_GET['news']) and !empty($this->list['output_type']) and $this->list['output_type'] != 'php') {
				$this->_url_array = array('feed', 'index.' . $this->list['output_type']);
			}

			if (preg_match('/[a-zA-Z0-9_]*\.php/', $this->_url_array[0])) {
				return;
			} elseif (strtolower($this->_url_array[0]) == '404.html') {
				$this->list['404'] = true;
			} elseif ($sm->is_registered_url($this->_url_array[0] . $this->list['output_type'])) {

				if (vivvo_hooks_manager::call('url_parse_handler_pre', array(&$this, $this->_url_array[0] . $this->list['output_type']))) {

					$res = $sm->call_url_handler($this->_url_array[0] . $this->list['output_type'], $this->_url_array);

					vivvo_hooks_manager::call('url_parse_handler_post', array(&$this, $this->_url_array[0] . $this->list['output_type'], &$res));

					if ($res === false) {
						go_404();
					} else {
						if (is_array($res) && !empty($res)) {
							$this->list = array_merge($this->list, $res);
						}
					}
				}
				return;
			} elseif ($sm->is_registered_url($this->_url_array[0])) {

				if (vivvo_hooks_manager::call('url_parse_handler_pre', array(&$this, $this->_url_array[0]))) {

					$res = $sm->call_url_handler($this->_url_array[0], $this->_url_array);

					vivvo_hooks_manager::call('url_parse_handler_post', array(&$this, $this->_url_array[0], &$res));

					if ($res === false) {
						go_404();
					} else {
						if (is_array($res) && !empty($res)) {
							$this->list = array_merge($this->list, $res);
						}
					}
				}
				return;
			} elseif (strtolower($this->_url_array[0]) == 'app') {
				if (isset($this->_url_array[1])) {
					$this->list['plugin'] = $this->_url_array[1];
					$plagin_name = $this->_url_array[1];
					array_shift($this->_url_array);
					array_shift($this->_url_array);
					$parsed_url = $pm->url_handler($plagin_name, $this->_url_array);
					if (is_array($parsed_url) && !empty ($parsed_url)) {
						foreach ($parsed_url as $k => $v) $this->list[$k] = $v;
					}
				}
			} elseif (count($this->_url_array) > 0) {

				$last_chunk = end($this->_url_array);
				$url_count = count ($this->_url_array);

				if ($url_count == 1) {
					//all news
					if (preg_match('/^index\.(\d+)?.*'. $this->list['output_type'].'?$/i',$last_chunk, $filename)) {
						if (!empty($filename[1])) {
							$this->list['pg'] = $filename[1];
						}
						array_pop($this->_url_array);
					} else {

						if (vivvo_hooks_manager::call('url_parse_default_pre', array(&$this))) {

							$res = $sm->call_url_handler('category', $this->_url_array);
							if ($res === false) {
								go_404();
							} else {
								if (is_array($res) && !empty($res)) {
									$this->list = array_merge($this->list, $res);
								}
								vivvo_hooks_manager::call('url_parse_default_post');
							}
						}
					}
				} else {

					if (vivvo_hooks_manager::call('url_parse_default_pre', array(&$this))) {

						$res = $sm->call_url_handler('category', $this->_url_array);
						if ($res === false) {
							go_404();
						} else {
							if (is_array($res) && !empty($res)) {
								$this->list = array_merge($this->list, $res);
							}
						}

						//article URL
						if (!empty($this->list['output_type'])) {
							if (isset($this->list['search_cid']) && !preg_match('/^index\.(\d+)?.*' . $this->list['output_type'].'?$/i', $last_chunk)) {
								if (VIVVO_URL_FORMAT != 0 && $sm->is_registered_url('article' . VIVVO_URL_FORMAT)) {
									$res = $sm->call_url_handler('article' . VIVVO_URL_FORMAT, $this->_url_array);
								} else {
									$res = $sm->call_url_handler('article', $this->_url_array);
								}
								if ($res === false) {
									go_404();
								} else {
									if (is_array($res) && !empty($res)) {
										$this->list = array_merge($this->list, $res);
									}
								}
							}
						}

						vivvo_hooks_manager::call('url_parse_default_post');
					}
				}
			}
		}

	}

	function mod($base, $number){
		return ($number % $base);
	}

	function switch_format($format, $url) {

		if (defined('VIVVO_PROXY_URL_OVERWRITE')) {
			$proxy = VIVVO_PROXY_URL_OVERWRITE;
		} elseif (defined('VIVVO_PROXY_URL')) {
			$proxy = VIVVO_PROXY_URL;
		} else {
			$proxy = '';
		}

		if ($proxy and substr($url, 0, $start = strlen($proxy)) == $proxy or substr($url, 0, $start = strlen(VIVVO_URL)) == VIVVO_URL) {
			$url = substr($url, $start);
		}

		if (substr($url, -5) == '.html') {
			$url = preg_replace('/\.html$/', '.' . $format, $url);
			if (substr($url, 0, 5) != 'feed/') {
				$url = 'feed/' . $url;
			}
		} elseif (strpos($url, '?') !== false) {
			$url = preg_replace('/[\?&]output_type=[^&]*/', '', $url) . '&output_type=' . $format;
		} else {
			if (substr($url, 0, 5) != 'feed/') {
				$url = 'feed/' . $url;
			}
			$url .= 'index.' . $format;
		}

		return make_proxied_url($url);
	}

	function convert2text ($text){
		require_once (VIVVO_FS_INSTALL_ROOT . 'lib/HtmlToPlaintext/HtmlToPlaintext.php');
		$h2t = new html2text($text);
		$h2t->width = 80;
		return $h2t->get_text();
	}

	if (!function_exists('file_put_contents')){
		define('FILE_APPEND', 1);
		function file_put_contents($n, $d, $flag = false) {
		    $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
		    $f = @fopen($n, $mode);
		    if ($f === false) {
		        return 0;
		    } else {
		        if (is_array($d)) $d = implode($d);
		        $bytes_written = fwrite($f, $d);
		        fclose($f);
		        return $bytes_written;
		    }
		}
	}

#EOF