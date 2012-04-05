<?php
/* =============================================================================
 * $Revision: 5365 $
 * $Date: 2010-05-21 15:16:26 +0200 (Fri, 21 May 2010) $
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
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @see			vivvo_post_object
	 *
	 */
	class maillist extends vivvo_post_object  {

  		/**
  		 * Subscriber id
  		 *
  		 * @var integer
  		 */
		var $id;

		/**
		 * Subscriber email
		 *
		 * @var string
		 */
		var $email;

		/**
		 * Subscriber ip
		 *
		 * @var string
		 */
		var $ip;

		/**
		 * Time of first registration for subscriber
		 *
		 * @var string
		 */
		var $time;

		/**
		 * Confirm status (0 - unconfirmed / 1 - confirmed)
		 *
		 * @var	integer
		 */
		var $confirm;

		/**
		 * Domain id
		 *
		 * @var integer
		 */
		var $domain_id;

		/**
		 * Name of sql table for Maillist
		 *
		 * @var string
		 */
		var $_sql_table = 'maillist';

		/**
		 * Sets subscriber {@link $id}
		 *
		 * @param	integer	$id
		 */
		function set_id($id) {
			$this->id = $id;
			return true;
		}

		/**
		 * Sets subscriber {@link $email}
		 *
		 * @param	string	$email
		 * @access	public
		 */
		function set_email($email) {
			$this->email = $email;
			return true;
		}

		/**
		 * Sets subscriber {@link $ip}
		 *
		 * @param	string	$ip
		 * @access	public
		 */
		function set_ip($ip) {
			$this->ip = $ip;
			return true;
		}

		/**
		 * Sets {@link $time}
		 *
		 * @param	integer	$time
		 * @access	public
		 */
		function set_time($time) {
			$this->time = $time;
			return true;
		}

		/**
		 * Sets {@link $confirm} status
		 *
		 * @param	integer	$confirm
		 * @return	boolean
		 * @access	public
		 */
		function set_confirm($confirm) {
			if ($confirm == 1){
				$this->confirm = 1;
			}else {
				$this->confirm = 0;
			}
			return true;
		}

		/**
		 * Gets $id
		 *
		 * @return integer
		 */
		function get_id(){
			return $this->id;
		}

		/**
		 * Gets $email
		 *
		 * @return string
		 */
		function get_email(){
			return $this->email;
		}

		/**
		 * Gets $ip
		 *
		 * @return string
		 */
		function get_ip(){
			return $this->ip;
		}

		/**
		 * Gets $time
		 *
		 * @return integer
		 */
		function get_time(){
			return $this->time;
		}

		/**
		 * Gets $confirm
		 *
		 * @return integer
		 */
		function get_confirm(){
			return $this->confirm;
		}

		/**
		 * Gets $domain_id
		 *
		 * @return integer
		 */
		function get_domain_id(){
			return $this->domain_id;
		}

		/**
		 * Sets {@link $domain_id}
		 *
		 * @param	integer	$domain_id
		 * @access	public
		 */
		function set_domain_id($domain_id) {
			$this->domain_id = $domain_id;
		}

	}

	/**
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @see			vivvo_db_list
	 *
	 */
	class maillist_list extends vivvo_db_paged_list  {

		/**
		 * Name of sql table
		 *
		 * @var string
		 */
		var $_sql_table = 'maillist';

		/**
		 * Name of object
		 *
		 * @var string
		 */
		var $post_object_type = 'maillist';


		/**
		 * Creates default query
		 *
		 *	@access	public
		 */
		function _default_query(){
			$this->_query->set_from(VIVVO_DB_PREFIX . 'maillist');
			$this->_query->add_fields('*');
		}

		/**
		 * Secure filter
		 *
		 * @param	string	$type
		 * @param	mixed	$condition
		 */
		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where('(id IN (' . $condition . '))');
					break;
				case 'email':
					$this->_query->add_where("email  = '$condition'");
					break;
				case 'md5':
					$this->_query->add_where("md5( concat( `email` , `ip` , `time` ) ) = '$condition'");
					break;
				case 'md5unsubscribe':
					$this->_query->add_where("md5( concat( `ip` , `time` , `email` ) ) = '$condition'");
					break;
				case 'confirm':
					$this->_query->add_where('(confirm IN (' . $condition . '))');
					break;
			}
		}


		function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
			//search_query

			if (isset($params['search_id'])){
				if (is_array($params['search_id'])){
					if (!in_array(0, $params['search_id'])){
						$params['search_id'] = implode(',', $params['search_id']);
						$this->add_filter('id',$params['search_id']);
					}
				}else{
					if ($params['search_id'] != 0){
						$this->add_filter('id',$params['search_id']);
					}
				}
				$this->add_generator_option('search_id', $params['search_id']);
			}

			if (isset ($params['search_email']) && $params['search_email'] != ''){
				$this->add_filter('email' , $params['search_email']);
				$this->add_generator_option('search_email', $params['search_email']);
			}

			if(isset($params['search_confirm']) && $params['search_confirm'] != ''){
				$this->add_filter('confirm',$params['search_confirm']);
				$this->add_generator_option('search_confirm', $params['search_confirm']);
			}


			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'email':
					$this->_query->add_order('email' . $search_direction);
					break;
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				default:
					$this->_query->add_order('id' . ' DESC');
					break;
			}
			//$this->add_order_option($order, $direction);

			$limit = (int) $limit;
			$this->_query->set_limit($limit);
			$offset = (int) $offset;
			$this->_query->set_offset($offset);
			$this->_default_query();

			if ($set_list){
				$this->set_list();
				return $this->list;
			}
		}

		/**
		 * Gets subscriber by id
		 *
		 * @param	integer	$id
		 * @return	object
		 */
		function &get_subscriber_by_id($id){

			$this->_default_query();
			$this->add_filter('id' , $id);
			$this->set_list();
			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}

		function get_subscribers_by_ids($subscribers_ids){
			if (is_array($subscribers_ids)){
				$val = trim(implode(',', $subscribers_ids));
			}else{
				$val = (int) $subscribers_ids;
			}
			if (empty($val)){
				return false;
			}

			$this->search(array('search_id'=>$subscribers_ids));

			if (empty($this->list)){
				return false;
			}else{
				return $this->list;
			}
		}

		/**
		 * Gets subscriber by email
		 *
		 * @param	string	$email
		 * @return	mixed object on success, false on fail
		 */
		function &get_subscriber_by_email($email){
			$this->_default_query();
			$this->add_filter('email' , $email);
			$this->set_list();

			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}

		function &get_subscriber_by_md5($param){
			$this->_default_query();
			$this->add_filter('md5' , $param );

			$this->set_list();
			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}

		function &get_subscriber_by_md5_unsubscribe($param){
			$this->_default_query();
			$this->add_filter('md5unsubscribe' , $param );

			$this->set_list();
			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}

		function &get_maillist(){
			$this->_default_query();
			$this->_query->add_order('id DESC');
			$this->set_list();
			if (!empty($this->list)){
				return  $this->list;
			}else{
				return false;
			}
		}

		function get_search_params(&$sm, $in_params){
			$params = array ();

			if (!empty($in_params['search_limit'])){
				$params['search_limit'] = $in_params['search_limit'];
			}else{
				$params['search_limit'] = 10;
			}

			$params['search_options'] = array();
			if (isset($in_params['search_options']) && is_array($in_params['search_options']) && !empty($in_params['search_options'])) $params['search_options'] = $in_params['search_options'];

			if (isset($in_params['search_confirm'])) $params['search_options']['search_confirm'] = $in_params['search_confirm'];

			if (!empty($in_params['search_sort_by'])){
				$params['search_sort_by'] = $in_params['search_sort_by'];
			}else{
				$params['search_sort_by'] = 'id';
			}

			if (isset($in_params['search_order']) && !empty($in_params['search_order'])){
				$params['search_order'] = $in_params['search_order'];
			}else{
				$params['search_order'] = 'descending';
			}

			if (isset($in_params['pg'])){
				$cur_page = (int) $in_params['pg'];
			}
			if (empty($cur_page)) $cur_page=1;

			$params['pg'] = $cur_page;

			$params['offset'] = ($cur_page-1) * $params['search_limit'];
			if (empty($params['offset'])) $params['offset'] = 0;

			if (!empty($in_params['cache'])) $params['cache'] = $in_params['cache'];

			return $params;
		}

		function get_search_params_from_url(&$sm){
			$um =& $sm->get_url_manager();
			$params = maillist_list::get_search_params($sm, $um->list);
			return $params;
		}
	}


	/**
	 * newsletter object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see			vivvo_post_object
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class newsletter extends vivvo_post_object {

		/**
		 * id
		 * Database field type:	int(11)
		 * Null status:		NO
		 *
		 * @var	integer	$id
		 */
		var $id;

		/**
		 * subject
		 * Database field type:	varchar(255)
		 * Null status:		NO
		 *
		 * @var	string	$subject
		 */
		var $subject;

		/**
		 * body
		 * Database field type:	longtext
		 * Null status:		NO
		 *
		 * @var	string	$body
		 */
		var $body;

		/**
		 * groups
		 * Database field type:	varchar(255)
		 * Null status:		YES
		 *
		 * @var	string	$groups
		 */
		var $groups;

		/**
		 * vte_template
		 * Database field type:	tinyint(4)
		 * Null status:		YES
		 *
		 * @var	integer	$vte_template
		 */
		var $vte_template;

		/**
		 * test
		 * Database field type:	tinyint(4)
		 * Null status:		YES
		 *
		 * @var	integer	$test
		 */
		var $test;

		/**
		 * test_email
		 * Database field type:	varchar(255)
		 * Null status:		YES
		 *
		 * @var	string	$test_email
		 */
		var $test_email;

		/**
		 * Newsletter table
		 */
		var $_sql_table = 'newsletter';

		/**
		 * Sets {@link $id}
		 *
		 * @param	integer	$id
		 */
		function set_id($id){
			$this->id = $id;
			return true;
		}

		/**
		 * Sets {@link $subject}
		 *
		 * @param	string	$subject
		 */
		function set_subject($subject){
			if ($subject != ''){
				$this->subject = $subject;
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Sets {@link $body}
		 *
		 * @param	string	$body
		 */
		function set_body($body){
			if ($body != ''){
				$this->body = $body;
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Sets {@link $groups}
		 *
		 * @param	string	$groups
		 */
		function set_groups($groups){
			$this->groups = $groups;
			return true;
		}

		/**
		 * Sets {@link $vte_template}
		 *
		 * @param	integer	$vte_template
		 */
		function set_vte_template($vte_template){
			if ($vte_template == 1){
				$this->vte_template	= 1;
			}else{
				$this->vte_template	= 0;
			}
			return true;
		}

		/**
		 * Sets {@link $test}
		 *
		 * @param	integer	$test
		 */
		function set_test($test){
			if ($test == 1){
				$this->test	= 1;
			}else{
				$this->test	= 0;
			}
			return true;
		}


		/**
		 * Sets {@link $test_email}
		 *
		 * @param	string	$test_email
		 */
		function set_test_email($test_email){
			$this->test_email = $test_email;
			return true;
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
		 * Gets $subject
		 *
		 * @return	string
		 */
		function get_subject(){
			return $this->subject;
		}
		/**
		 * Gets $body
		 *
		 * @return	string
		 */
		function get_body(){
			return $this->body;
		}
		/**
		 * Gets $groups
		 *
		 * @return	string
		 */
		function get_groups(){
			return $this->groups;
		}
		/**
		 * Gets $vte_template
		 *
		 * @return	integer
		 */
		function get_vte_template(){
			return $this->vte_template;
		}

		/**
		 * Gets $test
		 *
		 * @return	integer
		 */
		function get_test(){
			return $this->test;
		}

		/**
		 * Gets $test_email
		 *
		 * @return	string
		 */
		function get_test_email(){
			return $this->test_email;
		}
	}

	/**
	 * newsletter list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @see			vivvo_db_paged_list
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class newsletter_list extends vivvo_db_paged_list {
		var $_sql_table = 'newsletter';
		var $post_object_type = 'newsletter';

		function _default_query(){
			$this->_query->set_from(
									VIVVO_DB_PREFIX . 'newsletter ');
			$this->_query->add_fields('*');
		}

		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where('(id = \'' . $condition . '\')');
				break;
				case 'subject':
					$this->_query->add_where('(subject = \'' . $condition . '\')');
				break;
				case 'body':
					$this->_query->add_where('(body = \'' . $condition . '\')');
				break;
				case 'groups':
					$this->_query->add_where('(groups = \'' . $condition . '\')');
				break;
				case 'vte_template':
					$this->_query->add_where('(vte_template = \'' . $condition . '\')');
				break;
				case 'test':
					$this->_query->add_where('(test = \'' . $condition . '\')');
				break;
				case 'test_email':
					$this->_query->add_where('(test_email = \'' . $condition . '\')');
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
			if (isset($params['search_subject'])){
				$this->add_filter('subject',$params['search_subject']);
				$this->add_generator_option('search_subject', $params['search_subject']);
			}
			if (isset($params['search_body'])){
				$this->add_filter('body',$params['search_body']);
				$this->add_generator_option('search_body', $params['search_body']);
			}
			if (isset($params['search_groups'])){
				$this->add_filter('groups',$params['search_groups']);
				$this->add_generator_option('search_groups', $params['search_groups']);
			}
			if (isset($params['search_vte_template'])){
				$this->add_filter('vte_template',$params['search_vte_template']);
				$this->add_generator_option('search_vte_template', $params['search_vte_template']);
			}

			if (isset($params['search_test'])){
				$this->add_filter('test',$params['search_test']);
				$this->add_generator_option('search_test', $params['search_test']);
			}

			if (isset($params['search_test_email'])){
				$this->add_filter('test_email',$params['search_test_email']);
				$this->add_generator_option('search_test_email', $params['search_test_email']);
			}


			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'subject':
					$this->_query->add_order('subject' . $search_direction);
					break;
				case 'body':
					$this->_query->add_order('body' . $search_direction);
					break;
				case 'groups':
					$this->_query->add_order('groups' . $search_direction);
					break;
				case 'vte_template':
					$this->_query->add_order('vte_template' . $search_direction);
					break;
				case 'test':
					$this->_query->add_order('test' . $search_direction);
					break;
				case 'test_email':
					$this->_query->add_order('test_email' . $search_direction);
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

		function &get_all_newsletter(){
			$this->_default_query();
			$this->_query->add_order('id DESC');
			$this->set_list();
			if (!empty($this->list)){
				return  $this->list;
			}else{
				return false;
			}
		}

		function &get_newsletter_by_id($id){
			$id = (int) $id;
			$this->_default_query();
			$this->add_filter('id', $id);
			$this->set_list();

			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}
	}

?>