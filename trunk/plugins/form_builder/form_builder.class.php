<?php
/* =============================================================================
 * $Revision: 5403 $
 * $Date: 2010-5-27 18:28:26 +0100 (Wed, 27 May 2010) $
 *
 * Vivvo CMS 4.5
 * Copyright 2005-10 SpoonLabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * ============================================================================
 */

/**
 * @see  '/../../lib/vivvo/framework/vivvo_framework.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_framework.php');


/**
 * FormBuilderFields object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @package		vivvo_plugins
 * @subpackage	form_builder
 * @see			vivvo_post_object
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */
class FormBuilderFields extends vivvo_post_object {

	/**
	 * Name of table without tblprefix
	 *
	 * @var   string
	 */
	var $_sql_table = 'form_builder_fields';

	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * type
	 * Database field type:	varchar(255)
	 * Null status:		NO
	 *
	 * @var	string	$type
	 */
	var $type;

	/**
	 * form_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$form_id
	 */
	var $form_id;

	/**
	 * order_number
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$order_number
	 */
	var $order_number;

	/**
	 * name
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$name
	 */
	var $name;

	/**
	 * label
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$label
	 */
	var $label;

	/**
	 * required
	 * Database field type:	tinyint(4)
	 * Null status:		YES
	 *
	 * @var	integer	$required
	 */
	var $required;

	/**
	 * size
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$size
	 */
	var $size;

	/**
	 * max_size
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$max_size
	 */
	var $max_size;

	/**
	 * reg_exp
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$reg_exp
	 */
	var $reg_exp;

	/**
	 * description
	 * Database field type:	longtext
	 * Null status:		YES
	 *
	 * @var	string	$description
	 */
	var $description;

	/**
	 * options
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$options
	 */
	var $options;

	/**
	 * selected
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$selected
	 */
	var $selected;

	/**
	 * error_message
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$error_message
	 */
	var $error_message;



	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = $id;
	}

	/**
	 * Sets {@link $type}
	 *
	 * @param	string	$type
	 */
	function set_type($type){
		$this->type = $type;
	}

	/**
	 * Sets {@link $form_id}
	 *
	 * @param	integer	$form_id
	 */
	function set_form_id($form_id){
		$this->form_id = $form_id;
	}

	/**
	 * Sets {@link $order_number}
	 *
	 * @param	integer	$order_number
	 */
	function set_order_number($order_number){
		$this->order_number = $order_number;
	}

	/**
	 * Sets {@link $name}
	 *
	 * @param	string	$name
	 */
	function set_name($name){
		$this->name = $name;
	}

	/**
	 * Sets {@link $label}
	 *
	 * @param	string	$label
	 */
	function set_label($label){
		$this->label = $label;
	}

	/**
	 * Sets {@link $required}
	 *
	 * @param	integer	$required
	 */
	function set_required($required){
		$this->required = $required;
	}

	/**
	 * Sets {@link $size}
	 *
	 * @param	integer	$size
	 */
	function set_size($size){
		$this->size = $size;
	}

	/**
	 * Sets {@link $max_size}
	 *
	 * @param	integer	$max_size
	 */
	function set_max_size($max_size){
		$this->max_size = $max_size;
	}

	/**
	 * Sets {@link $reg_exp}
	 *
	 * @param	string	$reg_exp
	 */
	function set_reg_exp($reg_exp){
		$this->reg_exp = $reg_exp;
	}

	/**
	 * Sets {@link $description}
	 *
	 * @param	string	$description
	 */
	function set_description($description){
		$this->description = $description;
	}

	/**
	 * Sets {@link $options}
	 *
	 * @param	string	$options
	 */
	function set_options($options){
		$this->options = $options;
	}

	/**
	 * Sets {@link $selected}
	 *
	 * @param	string	$selected
	 */
	function set_selected($selected){
		$this->selected = $selected;
	}

	/**
	 * Sets {@link $error_message}
	 *
	 * @param	string	$error_message
	 */
	function set_error_message($error_message){
		$this->error_message = $error_message;
	}



	/**
	 * Gets $id
	 *
	 * @return	integer
	 */
	function get_id(){
		return $this->id;
	}
	/**
	 * Gets $type
	 *
	 * @return	string
	 */
	function get_type(){
		return $this->type;
	}
	/**
	 * Gets $form_id
	 *
	 * @return	integer
	 */
	function get_form_id(){
		return $this->form_id;
	}
	/**
	 * Gets $order_number
	 *
	 * @return	integer
	 */
	function get_order_number(){
		return $this->order_number;
	}
	/**
	 * Gets $name
	 *
	 * @return	string
	 */
	function get_name(){
		return $this->name;
	}
	/**
	 * Gets $label
	 *
	 * @return	string
	 */
	function get_label(){
		return $this->label;
	}
	/**
	 * Gets $required
	 *
	 * @return	integer
	 */
	function get_required(){
		return $this->required;
	}
	/**
	 * Gets $size
	 *
	 * @return	integer
	 */
	function get_size(){
		return $this->size;
	}
	/**
	 * Gets $max_size
	 *
	 * @return	integer
	 */
	function get_max_size(){
		return $this->max_size;
	}
	/**
	 * Gets $reg_exp
	 *
	 * @return	string
	 */
	function get_reg_exp(){
		return $this->reg_exp;
	}
	/**
	 * Gets $description
	 *
	 * @return	string
	 */
	function get_description(){
		return $this->description;
	}
	/**
	 * Gets $options
	 *
	 * @return	string
	 */
	function get_options(){
		if ($this->type == 'drop_down' || $this->type == 'radio'){
			$a = explode("\n", $this->options);
			return array_map("trim", $a);
		}
		return $this->options;
	}
	/**
	 * Gets $selected
	 *
	 * @return	string
	 */
	function get_selected(){
		return $this->selected;
	}
	/**
	 * Gets $error_message
	 *
	 * @return	string
	 */
	function get_error_message(){
		return $this->error_message;
	}
}

/**
 * FormBuilderFields list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @package		vivvo_plugins
 * @subpackage	form_builder
 * @see			vivvo_db_paged_list
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */
class FormBuilderFields_list extends vivvo_db_paged_list {
	var $_sql_table = 'form_builder_fields';
	var $post_object_type = 'FormBuilderFields';

	function _default_query(){
		$this->_query->set_from(
								VIVVO_DB_PREFIX . 'form_builder_fields ');
		$this->_query->add_fields('*');
	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
				$this->_query->add_where('(id = \'' . $condition . '\')');
			break;
			case 'type':
				$this->_query->add_where('(type = \'' . $condition . '\')');
			break;
			case 'form_id':
				$this->_query->add_where('(form_id = \'' . $condition . '\')');
			break;
			case 'order_number':
				$this->_query->add_where('(order_number = \'' . $condition . '\')');
			break;
			case 'name':
				$this->_query->add_where('(name = \'' . $condition . '\')');
			break;
			case 'label':
				$this->_query->add_where('(label = \'' . $condition . '\')');
			break;
			case 'required':
				$this->_query->add_where('(required = \'' . $condition . '\')');
			break;
			case 'size':
				$this->_query->add_where('(size = \'' . $condition . '\')');
			break;
			case 'max_size':
				$this->_query->add_where('(max_size = \'' . $condition . '\')');
			break;
			case 'reg_exp':
				$this->_query->add_where('(reg_exp = \'' . $condition . '\')');
			break;
			case 'description':
				$this->_query->add_where('(description = \'' . $condition . '\')');
			break;
			case 'options':
				$this->_query->add_where('(options = \'' . $condition . '\')');
			break;
			case 'selected':
				$this->_query->add_where('(selected = \'' . $condition . '\')');
			break;
			case 'error_message':
				$this->_query->add_where('(error_message = \'' . $condition . '\')');
			break;

		}
	}

	/**
	 * Advaced search list generator
	 *
	 * @param	array	$params	Search parameters
	 * @param	string	$order	Order parameters
	 * @param	integer	$limit	Limit
	 * @param	integer	$offset	Offset
	 * @return	array	Array of content_items
	 */
	function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0){
		//search_query
		if (isset($params['search_id'])){
			$this->add_filter('id',$params['search_id']);
			$this->add_generator_option('search_id', $params['search_id']);
		}
		if (isset($params['search_type'])){
			$this->add_filter('type',$params['search_type']);
			$this->add_generator_option('search_type', $params['search_type']);
		}
		if (isset($params['search_form_id'])){
			$this->add_filter('form_id',$params['search_form_id']);
			$this->add_generator_option('search_form_id', $params['search_form_id']);
		}
		if (isset($params['search_order_number'])){
			$this->add_filter('order_number',$params['search_order_number']);
			$this->add_generator_option('search_order_number', $params['search_order_number']);
		}
		if (isset($params['search_name'])){
			$this->add_filter('name',$params['search_name']);
			$this->add_generator_option('search_name', $params['search_name']);
		}
		if (isset($params['search_label'])){
			$this->add_filter('label',$params['search_label']);
			$this->add_generator_option('search_label', $params['search_label']);
		}
		if (isset($params['search_required'])){
			$this->add_filter('required',$params['search_required']);
			$this->add_generator_option('search_required', $params['search_required']);
		}
		if (isset($params['search_size'])){
			$this->add_filter('size',$params['search_size']);
			$this->add_generator_option('search_size', $params['search_size']);
		}
		if (isset($params['search_max_size'])){
			$this->add_filter('max_size',$params['search_max_size']);
			$this->add_generator_option('search_max_size', $params['search_max_size']);
		}
		if (isset($params['search_reg_exp'])){
			$this->add_filter('reg_exp',$params['search_reg_exp']);
			$this->add_generator_option('search_reg_exp', $params['search_reg_exp']);
		}
		if (isset($params['search_description'])){
			$this->add_filter('description',$params['search_description']);
			$this->add_generator_option('search_description', $params['search_description']);
		}
		if (isset($params['search_options'])){
			$this->add_filter('options',$params['search_options']);
			$this->add_generator_option('search_options', $params['search_options']);
		}
		if (isset($params['search_selected'])){
			$this->add_filter('selected',$params['search_selected']);
			$this->add_generator_option('search_selected', $params['search_selected']);
		}
		if (isset($params['search_error_message'])){
			$this->add_filter('error_message',$params['search_error_message']);
			$this->add_generator_option('search_error_message', $params['search_error_message']);
		}


		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'id':
				$this->_query->add_order('id' . $search_direction);
				break;
			case 'type':
				$this->_query->add_order('type' . $search_direction);
				break;
			case 'form_id':
				$this->_query->add_order('form_id' . $search_direction);
				break;
			case 'order_number':
				$this->_query->add_order('order_number' . $search_direction);
				break;
			case 'name':
				$this->_query->add_order('name' . $search_direction);
				break;
			case 'label':
				$this->_query->add_order('label' . $search_direction);
				break;
			case 'required':
				$this->_query->add_order('required' . $search_direction);
				break;
			case 'size':
				$this->_query->add_order('size' . $search_direction);
				break;
			case 'max_size':
				$this->_query->add_order('max_size' . $search_direction);
				break;
			case 'reg_exp':
				$this->_query->add_order('reg_exp' . $search_direction);
				break;
			case 'description':
				$this->_query->add_order('description' . $search_direction);
				break;
			case 'options':
				$this->_query->add_order('options' . $search_direction);
				break;
			case 'selected':
				$this->_query->add_order('selected' . $search_direction);
				break;
			case 'error_message':
				$this->_query->add_order('error_message' . $search_direction);
				break;

			default:
				$order = 'id';
				$this->_query->add_order('id' . ' DESC');
				break;
		}
			$this->add_order_option($order, $direction);

			$limit = (int) $limit;
			$this->_query->set_limit($limit);
			$offset = (int) $offset;
			$this->_query->set_offset($offset);
			$this->_default_query(true);

			$this->set_list();
			return $this->list;
	}

	function &get_elements_by_form_id($id){
		$id = (int) $id;
		$this->search(array('search_form_id'=>$id), 'order_number', 'ascending', 200);

		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

}


/**
 * FormBuilderForms object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see		vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class FormBuilderForms extends vivvo_post_object {

	/**
	 * Name of table without tblprefix
	 *
	 * @var   string
	 */
	var $_sql_table = 'form_builder_forms';
	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * title
	 * Database field type:	varchar(255)
	 * Null status:		NO
	 *
	 * @var	string	$title
	 */
	var $title;

	/**
	 * email
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$email
	 */
	var $email;

	/**
	 * status
	 * Database field type:	tinyint(4)
	 * Null status:		NO
	 *
	 * @var	integer	$status
	 */
	var $status;

	/**
	 * message
	 * Database field type:	text
	 * Null status:		YES
	 *
	 * @var	string	$message
	 */
	var $message;

	/**
	 * user_status
	 * Database field type:	tinyint(4)
	 * Null status:		NO
	 *
	 * @var	integer	$user_status
	 */
	var $user_status;

	/**
	 * message_url
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$message_url
	 */
	var $message_url;

	/**
	 * action
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$action
	 */
	var $action;

	/**
	 * cmd
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$cmd
	 */
	var $cmd;

	/**
	 * url
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$url
	 */
	var $url;



	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = $id;
	}

	/**
	 * Sets {@link $title}
	 *
	 * @param	string	$title
	 */
	function set_title($title){
		$this->title = $title;
	}

	/**
	 * Sets {@link $email}
	 *
	 * @param	string	$email
	 */
	function set_email($email){
		$this->email = $email;
	}

	/**
	 * Sets {@link $status}
	 *
	 * @param	integer	$status
	 */
	function set_status($status){
		$this->status = $status;
	}

	/**
	 * Sets {@link $message}
	 *
	 * @param	string	$message
	 */
	function set_message($message){
		$this->message = $message;
	}

	/**
	 * Sets {@link $user_status}
	 *
	 * @param	integer	$user_status
	 */
	function set_user_status($user_status){
		$this->user_status = $user_status;
	}

	/**
	 * Sets {@link $message_url}
	 *
	 * @param	string	$message_url
	 */
	function set_message_url($message_url){
		$this->message_url = $message_url;
	}

	/**
	 * Sets {@link $action}
	 *
	 * @param	string	$action
	 */
	function set_action($action){
		$this->action = $action;
	}

	/**
	 * Sets {@link $cmd}
	 *
	 * @param	string	$cmd
	 */
	function set_cmd($cmd){
		$this->cmd = $cmd;
	}

	/**
	 * Sets {@link $url}
	 *
	 * @param	string	$url
	 */
	function set_url($url){
		$this->url = $url;
	}



	/**
	 * Gets $id
	 *
	 * @return	integer
	 */
	function get_id(){
		return $this->id;
	}
	/**
	 * Gets $title
	 *
	 * @return	string
	 */
	function get_title(){
		return $this->title;
	}
	/**
	 * Gets $email
	 *
	 * @return	string
	 */
	function get_email(){
		return $this->email;
	}
	/**
	 * Gets $status
	 *
	 * @return	integer
	 */
	function get_status(){
		return $this->status;
	}
	/**
	 * Gets $message
	 *
	 * @return	string
	 */
	function get_message(){
		return $this->message;
	}
	/**
	 * Gets $user_status
	 *
	 * @return	integer
	 */
	function get_user_status(){
		return $this->user_status;
	}
	/**
	 * Gets $message_url
	 *
	 * @return	string
	 */
	function get_message_url(){
		return $this->message_url;
	}
	/**
	 * Gets $action
	 *
	 * @return	string
	 */
	function get_action(){
		return $this->action;
	}
	/**
	 * Gets $cmd
	 *
	 * @return	string
	 */
	function get_cmd(){
		return $this->cmd;
	}
	/**
	 * Gets $url
	 *
	 * @return	string
	 */
	function get_url(){
		return $this->url;
	}
}

/**
 * FormBuilderForms list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class FormBuilderForms_list extends vivvo_db_paged_list {
	var $_sql_table = 'form_builder_forms';
	var $post_object_type = 'FormBuilderForms';

	function _default_query(){
		$this->_query->set_from(
								VIVVO_DB_PREFIX . 'form_builder_forms ');
		$this->_query->add_fields('*');
	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
				$this->_query->add_where('(id = \'' . $condition . '\')');
			break;
			case 'title':
				$this->_query->add_where('(title = \'' . $condition . '\')');
			break;
			case 'email':
				$this->_query->add_where('(email = \'' . $condition . '\')');
			break;
			case 'status':
				$this->_query->add_where('(status = \'' . $condition . '\')');
			break;
			case 'message':
				$this->_query->add_where('(message = \'' . $condition . '\')');
			break;
			case 'user_status':
				$this->_query->add_where('(user_status = \'' . $condition . '\')');
			break;
			case 'message_url':
				$this->_query->add_where('(message_url = \'' . $condition . '\')');
			break;
			case 'action':
				$this->_query->add_where('(action = \'' . $condition . '\')');
			break;
			case 'cmd':
				$this->_query->add_where('(cmd = \'' . $condition . '\')');
			break;
			case 'url':
				$this->_query->add_where('(url = \'' . $condition . '\')');
			break;

		}
	}

	/**
	 * Advaced search list generator
	 *
	 * @param	array	$params	Search parameters
	 * @param	string	$order	Order parameters
	 * @param	integer	$limit	Limit
	 * @param	integer	$offset	Offset
	 * @return	array	Array of content_items
	 */
	function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0){
		//search_query
		if (isset($params['search_id'])){
			$this->add_filter('id',$params['search_id']);
			$this->add_generator_option('search_id', $params['search_id']);
		}
		if (isset($params['search_title'])){
			$this->add_filter('title',$params['search_title']);
			$this->add_generator_option('search_title', $params['search_title']);
		}
		if (isset($params['search_email'])){
			$this->add_filter('email',$params['search_email']);
			$this->add_generator_option('search_email', $params['search_email']);
		}
		if (isset($params['search_status'])){
			$this->add_filter('status',$params['search_status']);
			$this->add_generator_option('search_status', $params['search_status']);
		}
		if (isset($params['search_message'])){
			$this->add_filter('message',$params['search_message']);
			$this->add_generator_option('search_message', $params['search_message']);
		}
		if (isset($params['search_user_status'])){
			$this->add_filter('user_status',$params['search_user_status']);
			$this->add_generator_option('search_user_status', $params['search_user_status']);
		}
		if (isset($params['search_message_url'])){
			$this->add_filter('message_url',$params['search_message_url']);
			$this->add_generator_option('search_message_url', $params['search_message_url']);
		}
		if (isset($params['search_action'])){
			$this->add_filter('action',$params['search_action']);
			$this->add_generator_option('search_action', $params['search_action']);
		}
		if (isset($params['search_cmd'])){
			$this->add_filter('cmd',$params['search_cmd']);
			$this->add_generator_option('search_cmd', $params['search_cmd']);
		}
		if (isset($params['search_url'])){
			$this->add_filter('url',$params['search_url']);
			$this->add_generator_option('search_url', $params['search_url']);
		}


		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'id':
				$this->_query->add_order('id' . $search_direction);
				break;
			case 'title':
				$this->_query->add_order('title' . $search_direction);
				break;
			case 'email':
				$this->_query->add_order('email' . $search_direction);
				break;
			case 'status':
				$this->_query->add_order('status' . $search_direction);
				break;
			case 'message':
				$this->_query->add_order('message' . $search_direction);
				break;
			case 'user_status':
				$this->_query->add_order('user_status' . $search_direction);
				break;
			case 'message_url':
				$this->_query->add_order('message_url' . $search_direction);
				break;
			case 'action':
				$this->_query->add_order('action' . $search_direction);
				break;
			case 'cmd':
				$this->_query->add_order('cmd' . $search_direction);
				break;
			case 'url':
				$this->_query->add_order('url' . $search_direction);
				break;

			default:
				$order = 'id';
				$this->_query->add_order('id' . ' DESC');
				break;
		}
		$this->add_order_option($order, $direction);

			$limit = (int) $limit;
			$this->_query->set_limit($limit);
			$offset = (int) $offset;
			$this->_query->set_offset($offset);
			$this->_default_query(true);

			$this->set_list();
			return $this->list;
	}

	function &get_all_forms(){
		$this->search(array(), 'title', 'ascending', 200);

		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

	function &get_latest_form(){
		$this->search(array(), 'id', 'descending', 1);

		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}

	function &get_form_by_id($id){
		$id = (int) $id;
		$this->search(array('search_id'=>$id));

		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}

	function &get_active_form_by_id ($id){
		$id = (int) $id;
		$this->search(array('search_id'=>$id, 'search_status' => 1));

		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}

	function &get_form_by_url ($url){
		$this->search(array('search_url'=>$url, 'search_status' => 1));

		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}
}

#EOF