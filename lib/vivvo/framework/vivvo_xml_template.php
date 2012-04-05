<?php
/* =============================================================================
 * $Revision: 5473 $
 * $Date: 2010-06-08 14:47:53 +0200 (Tue, 08 Jun 2010) $
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
 * Vivvo CMS
 *
 * LICENSE:
 *
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * @copyright	SpoonLabs
 * @package		vivvo_framework
 * @subpackage	framework
 * @version		$Revision: 5473 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */


	/**
	 * require the parser
	 */
	require_once VIVVO_FS_FRAMEWORK . 'PEAR/Parser.php';

	/**
	 * xml_template_node class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	template
	 * @version		$Revision: 5473 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Mihail Binev
	 */
	class xml_template_node
	{
		/**
		 * Node type
		 *
		 * @var string values if foreach literal module variable string
		 */
		var $_type;
		/**
		 * Template
		 *
		 * @var template
		 */
		var $_template;
		var $_value;
		var $attributes = array();
		var $_nodes = array();
		var $parent;


		function &chain_value($chain, $variable = null, $instruction_param = false){
			if (is_null($variable)){
				if (preg_match('/^(\w+)((\[\w+\])+)$/', $chain[0], $var)){
					$arr =& $this->_template->get_value($var[1]);
					//$variable = $arr[$var[1]]
					$arr_keys = preg_split( "/\[([^\]]+)\]/", $var[2], -1, PREG_SPLIT_DELIM_CAPTURE );

					foreach ($arr_keys as $key){
						if ($key !== ''){
							if (is_array($arr)){
								if (array_key_exists($key, $arr)){
									$arr =& $arr[$key];
								}else{
									return  '';
								}
							}else{
								return '';
							}
						}
					}
					$variable =& $arr;
				}else{
					$variable =& $this->_template->get_value($chain[0]);
				}

				array_shift($chain);

				if (!empty($chain)){
					return $this->chain_value($chain, $variable, $instruction_param);
				}else{
					return $variable;
				}
			}else{
				if (is_object($variable)){
					$property = $chain[0];

					array_shift($chain);

					if (preg_match('/^(\w+)((\[\w+\])+)?$/', $property, $var)){
						$property = $var[1];
					}

					//method
					if (method_exists($variable, $property)){
						if (!empty ($instruction_param) && empty($chain)){
							$result =& call_user_func_array(array($variable, $property), $instruction_param);
						}
						else $result =& call_user_func(array($variable, $property));

					// property
					}else {

						if (vivvo_property_exists(get_class($variable), $property) or $variable instanceof StdClass){
							$result = $variable->$property;
						//overload
						}
						else $result = $variable->__get($property);
					}

					if (isset($var[2])){
						$arr_keys = preg_split( "/\[([^\]]+)\]/", $var[2], -1, PREG_SPLIT_DELIM_CAPTURE );

						foreach ($arr_keys as $key){
							if ($key !== ''){
								if (is_array($result)){
									if (array_key_exists($key, $result)){
										$result =& $result[$key];
									}
									else return  '';
								}
								else return '';
							}
						}
					}

					if (!empty($chain)){
						return $this->chain_value($chain, $result, $instruction_param);
					}else{
						return $result;
					}
				}else{
					$value = '';
					return  $value;
				}
			}
		}

		function &param_value($param){
			if (substr($param,0,1) == '{'){
				$param = trim ($param, '{}');
				@list($variable_name, $instuction_str) = explode ('|',$param);

				// Instruction
				if (!empty ($instuction_str)){
					if (strpos($instuction_str,':') !== false && preg_match('/^\w+:.*$/', $instuction_str)){
						list($instruction_function, $instruction_param) = explode (':', $instuction_str, 2);
					}else{
						$instruction_function = $instuction_str;
					}

					if (!function_exists($instruction_function)){
						$instruction_param = $instruction_function;
						$instruction_function = null;
					}

					// Instruction params
					$split_params = preg_split( "/[\s,]*('[^']+')[\s,]*|[\s,]+/", $instruction_param, -1, PREG_SPLIT_DELIM_CAPTURE );
					$instruction_params = array();
					foreach ($split_params as $v){
						if ($v == ''){
							// do nothing
						} elseif (preg_match('/^\'[^\']+\'$/', $v)){
							$instruction_params[] = trim($v, '\'');
						} elseif (preg_match('/^\d+(\.\d+)?$/', $v)){
							$instruction_params[] = $v;
						} elseif ($v == 'true'){
							$instruction_params[] = true;
						} elseif ($v == 'false'){
							$instruction_params[] = false;
						}
						else $instruction_params[] = $this->param_value('{' . $v . '}');
					}
				}else{
					$instruction_function = null;
					$instruction_param = array();
				}

				// A variable
				if (!preg_match('/[\.\[\]]/', $variable_name)){
					$result =& $this->_template->get_value($variable_name);
				}elseif (preg_match('/[\w\.\[\]]+/', $variable_name)){
					$chain = explode('.', $variable_name);
					if (is_null($instruction_function) && !empty($instruction_params)){
						$result =& $this->chain_value($chain, null, $instruction_params);
					}else{
						$result =& $this->chain_value($chain);
					}
				}

				if (!empty ($instruction_function)){
					array_push($instruction_params, $result);
					$new_result = call_user_func_array($instruction_function, $instruction_params);
					return $new_result;
				}
				return $result;
			}else{
				if (substr($param,0,1) == '"'){
					$value = trim($param, '"');
				}elseif	(substr($param,0,1) == "'"){
					$value = trim($param, "'");
				}else {
					$value = $param;
				}
			}
			return $value;
		}

		function &get_child_by_tag ($tag, $index = 0) {
			$child = false;
			$i = 0;
			if (is_array($this->_nodes)){
				foreach ($this->_nodes as $k => $node){
					if ($this->_nodes[$k]->_type == $tag){
						if ($index == $i){
							return $this->_nodes[$k];
						}
					}
				}
			}
			return $child;
		}

		function &get_children_by_tag ($tag) {
			$array = array();
			if (is_array($this->_nodes)){
				foreach ($this->_nodes as $k => $node){
					if ($this->_nodes[$k]->_type == $tag){
						$array[] =& $this->_nodes[$k];
					}
				}
			}
			return $array;
		}

		function xmlentities ( $string ) {
			return str_replace ( array ('&', '<', '>', '"'), array ('&amp;', '&lt;', '&gt;', '&#34;' ), $string );
		}

		function xmlentities_decode ( $string ) {
			return str_replace ( array ('&amp;', '&lt;', '&gt;', '&#34;', '&quot;' ), array ('&', '<', '>', '"', '"'), $string );
		}

		function get_literal_output() {
			$output = '';
			if (is_array($this->_nodes) && !empty($this->_nodes)){
				$output = '<' . $this->_type . $this->get_attribute_literal_output() . '>';
				foreach ($this->_nodes as $k => $node) {
					$output .= $this->_nodes[$k]->get_literal_output();
				}
				$output .= '</' . $this->_type . '>';
			}elseif ($this->_type == "CDATA"){
				$output .= $this->_value;
			}elseif ($this->_type == "comment"){
				$output .= $this->_value;
			}else{
				$output = '<' . $this->_type . $this->get_attribute_literal_output() . ' />';
			}
			return $output;
		}

		function nodes_output(){
			$output = '';
			if (is_array($this->_nodes)){
				foreach ($this->_nodes as $k => $node) {
					$output .= $this->_nodes[$k]->get_output();
				}
			}
			return $output;
		}

		function get_attribute_output($attribute_name) {
			$output = false;
			if (!empty($this->attributes)){
				if (is_array($this->attributes) && key_exists($attribute_name, $this->attributes)){
					if (preg_match('/\{/', $this->attributes[$attribute_name])){
						$split_tpl = preg_split('/({.*?})/', $this->attributes[$attribute_name],-1,PREG_SPLIT_DELIM_CAPTURE);
						if (is_array ($split_tpl) && !empty($split_tpl)){
							foreach ($split_tpl as $v){
								if (!$v) continue;
								if (preg_match('/\{/', $v)){
									if ($output !== false) {
										$output .= $this->param_value($v);
									} else {
										$output = $this->param_value($v);
									}
								}else{
									if ($output !== false) {
										$output .= $v;
									} else {
										$output = $v;
									}
								}
							}
						}
					}else{
						$output = $this->attributes[$attribute_name];
					}
				}
			}
			return $output;
		}

		function get_attributes_output() {
			$output = '';
			if (!empty($this->attributes)){
				if (is_array($this->attributes)){
					foreach ($this->attributes as $key => $val){
						$output .= ' ' . $key . '="' . $this->get_attribute_output($key) . '"';
					}
				}
			}
			return $output;
		}

		function get_attribute_literal_output() {
			$output = '';
			if (!empty($this->attributes)){
				if (is_array($this->attributes)){
					foreach ($this->attributes as $key => $val){
						$output .= ' ' . $key . '="' . $val . '"';
					}
				}
			}
			return $output;
		}

		/**
		 * Creates string output for every VTE template element, and common html tag,
		 * that need to be parsed for the final HTML content.
		 *
		 * @return string    Parsed HTML string
		 */
		function get_output(){
			$output = '';

			if ($this->_type == 'CDATA' || $this->_type == 'after_schock')
				return $this->_value;

			// non-vte tags
			if (substr($this->_type, 0, 4) != 'vte:' and $this->_type != 'comment') {
				return $this->get_output_default();
			}

			if ($this->_type == 'comment') {
				$output = '';
				if (preg_match('/\{/', $this->_value)){
					$split_tpl = preg_split('/({.*?})/', $this->_value, -1, PREG_SPLIT_DELIM_CAPTURE);
					if (is_array ($split_tpl) && !empty($split_tpl)){
						foreach ($split_tpl as $v){
							$output .= (preg_match('/\{/', trim($v))) ? $this->param_value($v) : $v;
						}
						unset($v);
					}
				}
				else $output .= $this->_value;
				return $output;
			}



			switch ($this->_type){
				case 'vte:else':
					return '';

				case 'vte:if':
					$countinue = false;
					if (!empty($this->attributes['isset'])) {
						$countinue = $this->_template->has_value($this->attributes['isset']);
					} else {
						if (preg_match('/(\'[^\']*\'|\"[^"]*\"|-?\d+|true|false|\{[\w\.\[\]\|:,\-]+\})\s*(=|==|!=|&lt;|&gt;|&lt;=|&gt;=|lt|lte|gt|gte|eq|neq|in)\s*(\'[^\']*\'|\"[^"]*\"|-?\d+|true|false|\{[\w\.\[\]\|:,\-]+\})/i',$this->attributes['test'], $if_statment)){
							$variable =& $this->param_value($if_statment[1]);
							$value =& $this->param_value($if_statment[3]);

							switch ($if_statment[2]){

								case 'eq':
								case '=':
								case '==':
									if ($variable == $value) $countinue = true;
								break;

								case 'neq':
								case '!=':
									if ($variable != $value) $countinue = true;
								break;

								case 'lt':
								case '&lt;':
									if ($variable < $value) $countinue = true;
								break;

								case 'lte':
								case '&lt;=':
									if ($variable <= $value) $countinue = true;
								break;

								case 'gt':
								case '&gt;':
									if ($variable > $value) $countinue = true;
								break;

								case 'gte':
								case '&gt;=':
									if ($variable >= $value) $countinue = true;
								break;

								case 'in':
									if (((is_string($variable) or (is_numeric($variable)) or (is_bool($variable))))){
										if(is_string($value) and $value != ''){
											$value = explode(',',$value);
											if (in_array($variable,$value)){
												$countinue = true;
											}
										}elseif(is_array($value) and !empty($value)){
											if (in_array($variable,$value)){
												$countinue = true;
											}
										}
									}
									break;
							}
						} elseif (preg_match('/^!(\{[\*\w\.\[\]\|:,\-\']+\})$/i',$this->attributes['test'], $if_statment)){
							$variable =& $this->param_value($if_statment[1]);
							if (empty($variable)){
								$countinue = true;
							}
						} elseif (preg_match('/^(\{[\*\w\.\[\]\|:,\-\']+\})$/i',$this->attributes['test'], $if_statment)){
							$variable =& $this->param_value($if_statment[1]);
							if (!empty($variable)){
								$countinue = true;
							}
						}
					}

					if ($countinue){
						//$output .= $this->nodes_output();
						if (is_array($this->_nodes)){
							foreach ($this->_nodes as $k => $node) {
								$output .= $this->_nodes[$k]->get_output();
							}
						}
					} else {
						$else_node =& $this->get_child_by_tag('vte:else');
						if ($else_node){
							//$output = $else_node->nodes_output();
							$output = '';
							if (is_array($else_node->_nodes)){
								foreach ($else_node->_nodes as $k => $node) {
									$output .= $else_node->_nodes[$k]->get_output();
								}
							}
						}
					}
					return $output;

				case 'vte:for':
					$output = '';
					if (isset($this->attributes['from'])){
						$array = $this->param_value($this->attributes['from']);
					}
					else $array = false;

					$key = false;
					if (isset($this->attributes['key'])) $key = $this->attributes['key'];
					$start = (isset ($this->attributes['start'])) ? intval($this->get_attribute_output('start')) : 1;
					$step = (isset($this->attributes['step'])) ? intval($this->get_attribute_output('step')) : 1;

					if ($step == 0) $step = 1;
					if (is_array($array) && !empty($array)){
						$count = count($array);
						$loop = (isset ($this->attributes['loop'])) ? intval($this->get_attribute_output('loop')) + $start : $count;
					}
					else $loop = (isset ($this->attributes['loop'])) ? intval($this->get_attribute_output('loop')) + $start : 0;

					if ($key !== false && $loop != 0){
						for ($j = $start; $j <= $loop; $j = $j + $step){
							$this->_template->assign($key, $j);
							$this->_template->assign($key . '_count', $loop);
							if (is_array($this->_nodes)){
								foreach ($this->_nodes as $k => $node) {
									$output .= $this->_nodes[$k]->get_output();
								}
							}
						}
					}
					return $output;

				case 'vte:foreach':
					if (isset($this->attributes['from'])){
						$array = $this->param_value($this->attributes['from']);
					}
					else $array = false;

					$key = false;
					if (isset($this->attributes['key'])) $key = $this->attributes['key'];

					if (is_array($array) && !empty($array)){
						$item_name = $this->attributes['item'];
						$count = count($array);
						$start = (isset ($this->attributes['start'])) ? intval($this->get_attribute_output('start')): 1;
						$loop = (isset ($this->attributes['loop'])) ? intval($this->get_attribute_output('loop')) + $start - 1 : $count;
						$step = (isset($this->attributes['step'])) ? intval($this->get_attribute_output('step')) : 1;

						if ($start > $count) return '';
						$index = 0;
						$internal_index = $start;

						reset($array);
						do {
							$current_key = key ($array);
							if (($index >= $start - 1) && ($index < $loop)){
								if ($key){
									$this->_template->assign($key, $internal_index);
									$this->_template->assign($key . '_count', $loop);
								}
								$this->_template->assign($item_name, $array[$current_key]);
								//$output .= $this->nodes_output();
								if (is_array($this->_nodes)){
									foreach ($this->_nodes as $k => $node) {
										$output .= $this->_nodes[$k]->get_output();
									}
								}
								$internal_index += $step;
							}
							$index++;

							for ($i=1; $i< $step; $i++){
								if (!next($array)) break;
								$index++;
							}
						}
						while (next($array));
					}
					else {
						$else_node = $this->get_child_by_tag('vte:else');
						if ($else_node){
							$output = '';
							if (is_array($else_node->_nodes)){
								foreach ($else_node->_nodes as $k => $node) {
									$output .= $else_node->_nodes[$k]->get_output();
								}
							}
						}
					}
					return $output;

				case 'vte:literal':
					$output = '';
					if (is_array($this->_nodes) && !empty($this->_nodes)){
						foreach ($this->_nodes as $k => $node) {
							$output .= $this->_nodes[$k]->get_literal_output();
						}
					}
					return $output;

				case 'vte:variable':
					$output  = '';
					$content = '';
					if (isset($this->attributes['name']) && !empty($this->attributes['name'])){
						$literal = isset($this->attributes['literal']) && ($this->attributes['literal'] == 1);
						if (isset($this->attributes['value'])){
							$content = $literal ? $this->attributes['value'] : $this->get_attribute_output('value');
						}elseif (is_array($this->_nodes) && !empty($this->_nodes)){
							foreach ($this->_nodes as $k => $node) {
								$content .= $literal ? $this->_nodes[$k]->get_literal_output() : $this->_nodes[$k]->get_output();
							}
						}

						if (isset($this->attributes['json']) && $this->attributes['json'] == 1){
							$content = json_decode($content, true);
						}

						if (isset($this->attributes['global']) && $this->attributes['global'] == 1) {
							vivvo_lite_site::get_instance()->get_template()->assign($this->attributes['name'], $content);
						} else {
							$this->_template->assign($this->attributes['name'], $content);
						}
					}
					return $output;

				case 'vte:header':
					$output = '';
					if (isset($this->attributes['type'])){
						$sm =& $this->_template->get_site_manager();
						$header =& $sm->get_header_manager();

						if ($this->attributes['type'] == 'script'){
							if (isset($this->attributes['href'])){
								$header->add_script($this->get_attribute_output('href'));
							}
						}elseif ($this->attributes['type'] == 'css'){
							if (isset($this->attributes['href'])){
								$media = 'screen';
								if (isset($this->attributes['media'])) $media = $this->attributes['media'];
								$header->add_css($this->get_attribute_output('href'), $media);
							}
						}elseif ($this->attributes['type'] == 'description'){
							$header->add_description($this->get_attribute_output('value'));
						}elseif ($this->attributes['type'] == 'keyword'){
							$header->add_keyword($this->get_attribute_output('value'));
						}elseif ($this->attributes['type'] == 'feed'){
							if (isset($this->attributes['href'])){
								$title = '';
								if (isset($this->attributes['title'])) $title = $this->get_attribute_output('title');
								$header->add_rss($this->get_attribute_output('href'), $title);
							}
						}
					}
					return $output;

				case 'vte:lang':
					$output = '';
					if (isset($this->attributes['src']) && !empty($this->attributes['src'])){
						$sm =& $this->_template->get_site_manager();
						$lang =& $sm->get_lang();
						if (defined('VIVVO_ADMIN_LANG')){
							$lang->load_lang(VIVVO_ADMIN_LANG, $this->attributes['src']);
						}
						else $lang->load_lang(VIVVO_DEFAULT_LANG, $this->attributes['src']);
					}
					return $output;

				case 'vte:include':
					$output = '';
					if (isset($this->attributes['file'])){
						$cache_output = '';

						$href = $this->get_attribute_output('file');

						if (isset($this->attributes['nocache']) && $this->attributes['nocache'] == 1){
							$cache_output = '<!--NoCache ' . substr($href, strlen(VIVVO_TEMPLATE_DIR)) . '-->';
						}

						if (is_file(VIVVO_FS_TEMPLATE_ROOT . $href)){
							$used_file = VIVVO_FS_TEMPLATE_ROOT . $href;
							$output = file_get_contents(VIVVO_FS_TEMPLATE_ROOT . $href);
						}

						if (!isset($this->attributes['parse']) || $this->attributes['parse'] != 'off'){
							$sm =& $this->_template->get_site_manager();
							$tmp_tpl =& new template($sm, $this->_template);
							if (isset($used_file)){
								$tmp_tpl->set_template_file($used_file);
								$sm->debug_push("tpl:", $used_file);
							}
							$tmp_tpl->set_string_template($output);
							if (!empty($this->attributes)){
								foreach ($this->attributes as $k => $v){
									if (preg_match('/^\{[^\}]*\}$/', $v)){
										$tmp_tpl->assign($k, $this->param_value($v));
									}
									else $tmp_tpl->assign($k, $this->get_attribute_output($k));
								}
							}
							$output = $tmp_tpl->get_output();
							$tmp_tpl->__destruct();
						}
						$cache_output .= $output;

						if (isset($this->attributes['nocache']) && $this->attributes['nocache'] == 1){
							$cache_output .= '<!--NoCache-->';
						}
						$output = $cache_output;
					}
					elseif (isset($this->attributes['href'])){
						//include url
						$output = @file_get_contents($this->attributes['href']);
					}
					return $output;

				case 'vte:param':
					return '';

				case 'vte:attribute':
					if (is_a ($this->parent, 'xml_template_node')){
						$value = trim($this->nodes_output());

						$name =  $this->attributes['name'];
						if (!empty($value) && !empty ($name)){
							$this->parent->attributes[$name] = $value;
						}
						else unset($this->parent->attributes[$name]);
					}
					return '';

				case 'vte:cdata':
					return '<![CDATA[' . $this->nodes_output() . ']]>';

				case 'vte:params':
					$params = array ();
					if (is_array($this->_nodes) && !empty($this->_nodes)){
						foreach ($this->_nodes as $k => $v){
							if ($this->_nodes[$k]->_type == 'vte:param'){
								if (preg_match('/^\{[^\}]*\}$/', $this->_nodes[$k]->attributes['value'])){
									$params[$this->_nodes[$k]->attributes['name']] =& $this->param_value($this->_nodes[$k]->attributes['value']);
								}
								else $params[$this->_nodes[$k]->attributes['name']] = $this->xmlentities_decode($this->_nodes[$k]->get_attribute_output('value'));
							}
						}
					}
					return $params;

				case 'vte:load':
					$module_name = $this->attributes['module'];
					if (isset($module_name) && trim($module_name) != ''){
						$params = array();
						if (isset ($this->attributes['params'])){
							$params = $this->param_value($this->attributes['params']);
						}elseif (!empty($this->attributes)){
							foreach ($this->attributes as $k => $v){
								if (preg_match('/^\{[^\}]*\}$/', $v)){
									$params[$k] =& $this->param_value($v);
								}
								else $params[$k] = $this->xmlentities_decode($this->get_attribute_output($k));
							}
						}
						$site_manager =& $this->_template->get_site_manager();
						return $site_manager->get_module_output($module_name, $params, $this->_template);
					}
					return '';

				case 'vte:box':
					$module_name = $this->attributes['module'];
					if (isset($module_name) && trim($module_name) != ''){
						if (isset ($this->attributes['params'])){
							$params = $this->param_value($this->attributes['params']);
						}

						$params_node = $this->get_child_by_tag('vte:params');

						if (empty($params) || !is_array($params)) $params = array();

						if ($params_node){
							$params = array_merge($params, $params_node->get_output());
						}

						$site_manager =& $this->_template->get_site_manager();

						$template_node = $this->get_child_by_tag('vte:template');
						if ($this->_type == 'vte:box' && $template_node){
							$params['template_string'] =  $template_node->get_literal_output();
						}
						return $site_manager->get_module_output($module_name, $params, $this->_template);
					}
					return '';

				case 'vte:template':
					$output = '';
					if (!empty($this->attributes['template_string'])) {

						$template = new template(null, $this);
						$template->set_string_template($this->param_value($this->attributes['template_string']));

						foreach ($this->attributes as $name => $value) {
							$template->assign($name, $this->param_value($value));
						}

						$output = $template->get_output();

					} else {

						if (!empty($this->attributes['recursive'])) {
							$literal = '';
							if (is_array($this->_nodes) && !empty($this->_nodes)) {
								foreach ($this->_nodes as $k => $node) {
									$literal .= $this->_nodes[$k]->get_literal_output();
								}
							}
							$this->_template->assign('template_string', $literal);
						}

						if (is_array($this->_nodes)){
							foreach ($this->_nodes as $k => $node) {
								$output .= $this->_nodes[$k]->get_output();
							}
						}
					}
					return $output;

				case 'vte:value':
					return $this->param_value($this->attributes['select']);

				default:
					return $this->get_output_default();
			}
		}

		public static $self_closing = array('img', 'input', 'br', 'hr', 'link', 'base', 'meta', 'area', 'basefont', 'param', 'frame', 'col', 'embed');

		/**
		 * Returns default string output for non-vte nodes.
		 *
		 * @return string    Parsed HTML tag
		 */
		protected function get_output_default() {
			if (empty($this->_nodes)) {
				if (in_array($this->_type, self::$self_closing)) {
					return '<' . $this->_type . $this->get_attributes_output() . ' />';
				} else {
					return '<' . $this->_type . $this->get_attributes_output() . ' ></' . $this->_type . '>';
				}
			}

			$output = '';
			if (is_array($this->_nodes)){
				foreach ($this->_nodes as $k => $node) {
					$output .= $this->_nodes[$k]->get_output();
				}
				unset($k, $node);
			}

			if (strlen(trim($output)) or !in_array($this->_type, self::$self_closing)) {
				$output .= '</' . $this->_type . '>';
			}

			return '<' . $this->_type . $this->get_attributes_output() . '>' . $output;
		}

		function xml_template_node(&$template, $type, $attributes = '', $value = ''){
			$this->attributes = $attributes;
			$this->_template =& $template;
			$this->_type = $type;
			$this->_value = $value;
		}
	}

	/**
	 * vivvo_xml_template_parser class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	template
	 * @version		$Revision: 5473 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author      Mihail Binev
	 */
	class vivvo_xml_template_parser extends XML_Parser
	{
		var $_template;
		var $folding = false;
		var $nodes = array();
		var	$parent_stack = array(-1);
		var $parent_count = 0;


		function defaultHandler($xp, $cdata)
	    {
	    	$current_index = count($this->nodes);
	    	$parent_index  = $this->parent_stack[count($this->parent_stack) - 1];
	    	$this->nodes[$current_index] =& new xml_template_node($this->_template, 'comment', array(), $cdata);
	    	if ($parent_index != -1){
	    		$this->nodes[$current_index]->parent =& $this->nodes[$parent_index];
	    		$this->nodes[$parent_index]->_nodes[] =& $this->nodes[$current_index];
	    	}
	    	unset($parent_index);
	    }

		function piHandler($xp, $cdata) {

	    	$current_index = count($this->nodes);
	    	$parent_index = $this->parent_stack[count($this->parent_stack) - 1];

			$this->nodes[$current_index] =& new xml_template_node($this->_template, 'CDATA', array(), "<?{$cdata} {$data}?>");

	    	if ($parent_index != -1) {
	    		$this->nodes[$current_index]->parent =& $this->nodes[$parent_index];
	    		$this->nodes[$parent_index]->_nodes[] =& $this->nodes[$current_index];
	    	}
	    	unset($parent_index);
	    }

	    function unparsedHandler($xp, $cdata) {}
	    function notationHandler($xp, $cdata) {}
	    function entityrefHandler($xp, $openEntityNames, $base, $systemId,$publicId) {}

		function startHandler($xp, $name, $attribs)
	    {
	    	$current_index = count($this->nodes);
	    	$parent_index = $this->parent_stack[count($this->parent_stack) - 1];

	    	$this->nodes[$current_index] =& new xml_template_node($this->_template, $name, $attribs, '');

	    	if ($parent_index != -1){
	    		$this->nodes[$current_index]->parent =& $this->nodes[$parent_index];
	    		$this->nodes[$parent_index]->_nodes[] =& $this->nodes[$current_index];
	    	}

	    	array_push($this->parent_stack, $current_index);
	    	unset($parent_index);
	    }

		function endHandler($xp, $name) {
	    	array_pop($this->parent_stack);
	    }

		function cdataHandler($xp, $cdata)
		{
			$current_index = count($this->nodes);
	    	$parent_index = $this->parent_stack[count($this->parent_stack) - 1];

	    	$this->nodes[$current_index] =& new xml_template_node($this->_template, 'CDATA', array(), $cdata);

	    	if ($parent_index != -1){
	    		$this->nodes[$current_index]->parent =& $this->nodes[$parent_index];
	    		$this->nodes[$parent_index]->_nodes[] =& $this->nodes[$current_index];
	    	}
		}

		function get_output(){
			if (isset($this->nodes[0]))
				return $this->nodes[0]->get_output();
		}

		function set__template (&$template) {
			$this->_template =& $template;
		}
	}


	/**
	 * Template_node
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	template
	 * @version		$Revision: 5473 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class template extends vivvo_object
	{

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
		 * Patterns
		 *
		 * @var array
		 */
		var $_replacments = array();

		/**
		 * Nessted templates
		 *
		 * @var array
		 */
		var $_nessted = array();

		/**
		 * Template file name
		 *
		 * @var string
		 */
		var $_template_file = '';

		/**
		 * String template for parsing,
		 * if template file is set and string is not empty,
		 * this template will be used
		 *
		 * @var string
		 */
		var $_string_template = '';

		/**
		 * Push sub_template, pattern assignment
		 *
		 * @param object $value
		 * @param strng $pattern
		 */
		function assign_template($pattern, &$value){
			if ($pattern != ''){
				if (is_object($value) && is_a($value, 'template')){
					$this->_nessted[$pattern] =& $value;
				}
			}
		}

		/**
		 * Push value, pattern assignment
		 *
		 * @param misc $value
		 * @param string $pattern
		 */
		function assign($pattern, $value){
			if ($pattern != '') {
				$this->_replacments[$pattern] = $value;
			}
		}

		/**
		 * Push value, pattern assignment (by reference)
		 *
		 * @param misc $value
		 * @param string $pattern
		 */
		function assign_ref($pattern, &$value){
			if ($pattern != '') {
				if (is_array($value) or is_object($value)) {
					$this->_replacments[$pattern] =& $value;
				} else {
					$this->_replacments[$pattern] = $value;
				}
			}
		}

		private static $lang = null;

		function &get_value($pattern){

			$value = '';
			is_null(self::$lang) and self::$lang = vivvo_lang::get_instance();

			if (is_array($this->_replacments) && key_exists($pattern, $this->_replacments)){
				return $this->_replacments[$pattern];
			}elseif (is_array($this->_nessted) && key_exists($pattern, $this->_nessted)){
				if (is_object($this->_nessted[$pattern]) && is_a($this->_nessted[$pattern], 'template')){
					return $this->_nessted[$pattern]->get_output();
				}
			}elseif (($value = self::$lang->get_value($pattern)) !== false){
				//
			}elseif ($this->_parent_template){
				return $this->_parent_template->get_value($pattern);
			}elseif (defined($pattern)){
				$value = constant($pattern);
			}
			return $value;
		}

		public function has_value($name) {

			is_null(self::$lang) and self::$lang = vivvo_lang::get_instance();

			if (is_array($this->_replacments) && key_exists($name, $this->_replacments)) {
				return true;
			} elseif (is_array($this->_nessted) && key_exists($name, $this->_nessted)) {
				return true;
			} elseif (self::$lang->get_value($pattern) !== false) {
				return true;
			} elseif ($this->_parent_template) {
				return $this->_parent_template->has_value($name);
			}

			return defined($name);
		}

		function eval_string($string) {

			$output = '';

			if (preg_match('/\{/', $string)) {

				$split_tpl = preg_split('/({.*?})/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);

				if (is_array($split_tpl) && !empty($split_tpl)) {
					foreach($split_tpl as $v) {

						if (preg_match('/\{/', $v)) {
							$output .= $this->rootNode->param_value($v);
						} else {
							$output .= $v;
						}
					}
				}
			} else {
				$output = $string;
			}

			return $output;
		}

		/**
		 * Sets template file name.
		 *
		 * @param string $file
		 */
		function set_template_file($file){
			$this->_template_file = $file;
		}

		function get_tempalte_file(){
			if (!empty($this->_template_file)){
				return $this->_template_file;
			}
			elseif ($this->_parent_template){
				return $this->_parent_template->get_tempalte_file();
			}
			else return 'No template file';
		}

		/**
		 * Get template file content
		 *
		 */
		function load_template(){
			if (file_exists($this->_template_file)) {
				$this->_string_template = file_get_contents($this->_template_file);
			}
		}

		function xmlEntities($s){
			//build first an assoc. array with the entities we want to match
			$table1 = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
			//now build another assoc. array with the entities we want to replace (numeric entities)
			foreach ($table1 as $k=>$v){
			  $table1[$k] = "$v";
			  $c = htmlentities($k,ENT_QUOTES,"UTF-8");
			  $table2[$c] = "&#".ord($k).";";
			}

			//now perform a replacement using preg_replace
			//each matched value in array 1 will be replaced with the corresponding value in array 2
			$s = str_replace($table1,$table2,$s);
			return $s;
		}

		var $rootNode = null;

		/**
		 * Parse template file
		 *
		 */
		function parse(){
			$p = &new vivvo_xml_template_parser(null, 'event');
			$p->set__template($this);
			$this->_string_template =  str_replace ("&", "&amp;",$this->_string_template);
			$result = $p->setInputString($this->_string_template);

			$sm = vivvo_lite_site::get_instance();

			if (PEAR::isError($result))
				$sm->debug_push("Parser:", $result->getMessage(), 1);

			$result = $p->parse();
			if (PEAR::isError($result)) {
				$sm->debug_push("tpl:", $this->get_tempalte_file(), 1);
				$sm->debug_push("Parser:", $result->getMessage(), 1);
			}
			$this->output = $p->get_output();
		}

		/**
		 * Set template string.
		 *
		 * @param string $str_template
		 */
		function set_string_template($str_template) {
			$this->_string_template = $str_template;
		}

		/**
		 * Get output.
		 *
		 * @return string
		 */
		function get_output(){
			if ($this->_string_template == ''){
				$this->load_template();
			}
			$this->parse();
			return $this->output;
		}

		function template($site_manager = null, &$parent = null) {
			if ($parent && $parent instanceof template){
				$this->_parent_template =& $parent;
			}
//			$this->set_site_manager($site_manager);
			$this->rootNode = new xml_template_node($this, 'vte:else');
		}

		function __destruct () {
			parent::__destruct();
			unset($this->_replacments);
			unset($this->_nessted);
			unset($this->_parent_template);
			unset($this->output);
		}

		public static function loadModifiers() {
			$dir_name = VIVVO_FS_ROOT . 'modifiers/';
		    $filename_pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*\.modifier\.php$/';
	        if (is_dir($dir_name) && ($dir = opendir($dir_name))) {
	            while (($file = readdir($dir)) !== false) {
	                if (preg_match($filename_pattern, $file)) {
	                    @include $dir_name . $file;
	                }
	            }
	            closedir($dir);
	        }
		}
	}

	template::loadModifiers();

#EOF