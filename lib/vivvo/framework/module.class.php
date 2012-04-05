<?php
/* =============================================================================
 * $Revision: 5385 $
 * $Date: 2010-05-25 11:51:09 +0200 (Tue, 25 May 2010) $
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
 * Module output cache lifetime period, if not defined set to 5 minutes
 */
defined('VIVVO_MODULE_CACHE_TIME') or define('VIVVO_MODULE_CACHE_TIME', 300);

	/**
	 * Module class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see			vivvo_object
	 */

	class module extends vivvo_object {
		/**
		 * Output
		 *
		 * @var string
		 */
		var $output;

		/**
		 * Parent template
		 *
		 * @var template
		 */
		var $_parent_template;

		/**
		 * Module identifyer
		 *
		 * @var string
		 */
		var $_name = '';

		/**
		 * Default template to process
		 *
		 * @var unknown_type
		 */
		var $_default_template_file = '';

		/**
		 * Module configuration params
		 *
		 * @var array
		 */
		var $_configuration = array();

		/**
		 * Local template parser
		 *
		 * @var template
		 */
		var $_template;

		/**
		 * Cache module output
		 *
		 * @var boolean
		 */
		var $cache = false;

		/**
		 * Cache output string
		 *
		 * @var string
		 */
		var $cache_output;

		/**
		 * Cache manager
		 *
		 * @var Cache_lite
		 */
		var $_cache_manager;

		/**
		 * Cache id MD5 hash of serialized params and class name
		 *
		 * @var string
		 */
		var $_cache_id;

		/**
		 * Create template parser
		 *
		 * @return template
		 */
		function &set_template($params = array()) {

			require_once(dirname(__FILE__) . '/vivvo_xml_template.php');

			$sm = vivvo_lite_site::get_instance();

			if (!$this->_parent_template) {
				$this->_parent_template = $sm->get_template();
			}

			$this->_template = new template(null, $this->_parent_template);

			if (!empty($params)) {
				if (!empty($params['template']) && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $params['template'])) {
					$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $params['template']);
					$sm->debug_push("tpl:", VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $params['template']);
				} elseif (!empty($params['template']) && file_exists(VIVVO_FS_TEMPLATE_ROOT . $params['template'])) {
					$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . $params['template']);
					$sm->debug_push("tpl:", VIVVO_FS_TEMPLATE_ROOT . $params['template']);
				} elseif(isset($params['template_string'])) {
					$this->_template->set_string_template($params['template_string']);
				} else {
					$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $this->_default_template_file);
					$sm->debug_push("tpl:", VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $this->_default_template_file);
				}
			} else {
				$this->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $this->_default_template_file);
				$sm->debug_push("tpl:", VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $this->_default_template_file);
			}

			return $this->_template;
		}

		/**
		 * Set parent template
		 *
		 * @param template $parent_template
		 */
		function set_parent_template($parent_template) {
			$this->_parent_template = $parent_template;
		}

		/**
		 * Save module preferences
		 *
		 * @param vivvo_site $site_manager
		 * @param vivvo_post_master $post_master
		 * @param integer $box_def_id
		 * @param array $prefs
		 * @return mixed on SUCCES true, on ERROR error code
		 */
		function save_preferences ($site_manager, $post_master, $box_def_id, $prefs) {
			$box_params_list = new box_def_list();
			$box = $box_params_list->get_box_by_id($box_def_id);
			if ($box) {
				$box->set_params('');

				if (!empty($prefs) && is_array($prefs)){
					$box->set_params($prefs);
				}
				$post_master->set_data_object($box);
				if (!$post_master->sql_update()){
					// DB insert fail
					return 801;
				}
				return true;
			}else{
				//TODO error code
				return 802;
			}
		}

		/**
		 * Set cache manager
		 * @deprecated
		 */
		function set_cache_manager(){
		}

		/**
		 * Set cache id
		 *
		 * @param array module load parameters
		 * @return string MD5 hash of serialized params and class name
		 */
		function set_cache_id($params) {
			return $this->_cache_id = md5(serialize($params));
		}

		function load_cache() {

			$cache_manager = vivvo_cache::get_instance();
			$class = get_class($this);

			if ($cache_manager->exists($cache_key = $this->_cache_id . '!' . $class)) {
				$output = $cache_manager->get($cache_key);
				vivvo_lite_site::get_instance()->debug_push('Box cache:', 'Render ' . $class);
				if ($output) {
					$this->cache_output = $output;
					return true;
				}
			}

			return false;
		}

		function _serialize_data(){
			$list = $this->_template->_replacments;
			$output = new stdClass();
			if (!empty($list)) {
				$keys = array_keys($list);
				foreach ($keys as $key){
					$output->$key = $this->sanitize_vivvo_object($list[$key]);
				}
			}
			return $output;
		}

		function _object_to_xml (&$object, &$xml, &$parentNode){
			$class_vars = get_object_vars($object);
			foreach ($class_vars as $name => $value)
			{
				if (is_object($value)){
					$element = $xml->createElement($name);
					$node = $parentNode->appendChild($element);
					$this->_object_to_xml($value, $xml, $node);
				}elseif (is_array($value)){
					foreach($value as $k => $v){
						$name = str_replace('_list', '', $name);
						$element = $xml->createElement($name);
						$node = $parentNode->appendChild($element);
						$this->_object_to_xml($v, $xml, $node);
					}
				}else{
					if (preg_match('/[&<>"]/', $value)){
						$element = $xml->createElement($name);
						$cdata = $xml->createCDATASection($value);
						$element->appendChild($cdata);
					}else{
						$element = $xml->createElement($name, $value);
					}
					$parentNode->appendChild($element);
				}
			}
		}

		function get_xml_output(){
			$object = $this->_serialize_data();

			//

			$xml = new DOMDocument("1.0", 'UTF-8');
			$elements = $xml->createElement(get_class($this));

			$rootNode = $xml->appendChild($elements);

			if ($object){
				$this->_object_to_xml($object, $xml, $rootNode);
			}

			return $xml->saveXML();
		}

		function get_json_output(){
			$output = $this->_serialize_data();
			return json_encode ($output);
		}

		function sanitize_vivvo_object($obj) {
			if(is_object($obj)){
				$res = new stdClass();

				unset($obj->_sql_table);
				unset($obj->id_key);
				unset($obj->dump);
				unset($obj->_dump_handler);
				unset($obj->dump_object);

				if (isset($obj->elem)){
					if (!empty($obj->elem)){
						foreach($obj->elem as $name => $value){
							$res->$name = $value;
						}
					}
					unset($obj->elem);
				}

				foreach(get_object_vars($obj) as $key=>$val){
					if ($key[0] == '_'){
						unset($obj->$key);
					}elseif(is_object($obj->$key) || is_array($obj->$key)){
						$res->$key = $this->sanitize_vivvo_object($obj->$key);
					}else{
						$res->$key = $obj->$key;
					}
				}
				return $res;
			}elseif(is_array($obj)){
				foreach($obj as $key=>$val){
					if ($key[0] == '_'){
						unset($obj[$key]);
					}elseif(is_object($obj[$key]) || is_array($obj[$key])){
						$res[$key] = $this->sanitize_vivvo_object($obj[$key]);
					}else{
						$res[$key] = $obj[$key];
					}
				}
				return $res;
			}else{
				return $obj;
			}
		}

		/**
		 * Reutrn module template output
		 *
		 * @return string
		 */
		function get_output(){
			if ($this->cache && $this->cache_output){
				return $this->cache_output;
			}else{
				if ($this->_template instanceof template){
					$output = $this->_template->get_output();
				}else {
					$output = $this->output;
				}
				if ($this->cache) {
					$class = get_class($this);
					vivvo_cache::get_instance()->put($this->_cache_id . '!' . $class, $output, array($class), VIVVO_MODULE_CACHE_TIME);
					vivvo_lite_site::get_instance()->debug_push('Box cache:', 'Save ' . $class);
				}
				return $output;
			}
		}

		/**
		 * Load settings for module
		 *
		 */
		function set_configuration(){
			$config = vivvo_lite_site::get_instance()->get_configuration();
			$this->_configuration = $config->get_configuration($this->_name);
		}

		function generate_output(){
			$this->output = '';
		}

		/**
		 * Default module params
		 *
		 * @return array default params
		 */
		function default_params(){
			return array();
		}

		/**
		 * Generic module counstructor
		 *
		 * @param vivvo_site $site_manager
		 * @param array $params
		 * @param boolean $cache 				force cache drop
		 * @return module
		 */
		function module($site_manager, $params, $parent_template = NULL, $cache = true, $output = true){

			if ($parent_template) $this->set_parent_template($parent_template);

			if (VIVVO_CACHE_ENABLE &&$this->cache && $cache){
				$this->set_cache_id($params);
				if (!$this->load_cache()){
					if ($output) $this->generate_output($params);
				}
			}elseif (VIVVO_CACHE_ENABLE && isset($params['cache']) && $params['cache'] == 1){
				$this->set_cache_id($params);
				$this->cache = true;
				if (!$this->load_cache()){
					if ($output) $this->generate_output($params);
				}
			}else{
				if ($output) $this->generate_output($params);
			}
		}

		function __destruct () {
			parent::__destruct ();
			if (!empty($this->_template)){
				$this->_template->__destruct();
				unset($this->_template);

			}
			unset($this->_configuration);
			if (!empty($this->_parent_template)) unset($this->_parent_template);
		}
	}

#EOF