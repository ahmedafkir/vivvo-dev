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
 * Box generic
 *
 * @version Release: @package_version@
 * @license http://www.vivvo.net/license.php
 * @copyright SpoonLabs
 * @package Vivvo
 * @subpackage box
 * @author Slobodan Utvic <utvara@spoonlabs.com>
 */
class box_generic extends module {
	var $_default_template_file = '';

	/**
		* Generate box output
		*
		* @param array $params Parameters
		*/
	function generate_output ($params){
		require_once(VIVVO_FS_FRAMEWORK . 'vivvo_generic.class.php');
		if ($params['table']){
			$this->set_template($params);

			if (isset($params['primary_key'])){
				$primary_key = $params['primary_key'];
			}else{
				$primary_key = 'id';
			}
			// database/dsn params might be broken now...
			if ($params['database']){
				$the_list = new vivvo_generic_list(null, $params['table'], $primary_key, $params['database']);
			}elseif ($params['dsn']){
				$the_list = new vivvo_generic_list(null, $params['table'], $primary_key, '', $params['dsn']);
			}else{
				$the_list = new vivvo_generic_list(null, $params['table'], $primary_key);
			}

			$search_params = $the_list->get_search_params($params);

			$the_list->search($search_params['search_options'], $search_params['search_sort_by'], $search_params['search_order'], $search_params['search_limit'], $search_params['offset']);
			if (!empty($the_list->list)){
				$this->_template->assign('list_object', $the_list);
				$this->_template->assign('list', $the_list->list);
			}else{
				$this->_template->assign('list_object', strval(0));
				$this->_template->assign('list', strval(0));
			}
		}
	}
}

class box_xml_grabber extends module {
	var $data;

	function object2array($object){
		$return = NULL;

		if(is_array($object)){
			foreach($object as $key => $value){
				$key = str_replace('@', '_', $key);
				$return[$key] = $this->object2array($value);
			}
		}else{
			$var = get_object_vars($object);
			if($var){
				foreach($var as $key => $value){
					$key = str_replace('@', '_', $key);
					$return[$key] = $this->object2array($value);
				}
			}else{
				return strval($object); // strval and everything is fine
			}
		}

		return $return;
	}

	/**
		* Generate box output
		*
		* @param array $params Parameters
		*/
	function generate_output($params){
		$this->set_template($params);
		if ($params['url'] != ''){
			$data = @simplexml_load_file($params['url']);
			if ($data){
				//var_dump(is_array($this->data->attributes());
				//exit;
				$this->data = $this->object2array($data);
				$this->_template->assign('xml_data', $this->data);
			}
		}else{
			$this->_template->assign('xml_data', strval(''));
		}
	}
}

#EOF