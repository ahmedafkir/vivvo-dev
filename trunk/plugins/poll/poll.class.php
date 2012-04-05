<?php
/* =============================================================================
 * $Revision: 4834 $
 * $Date: 2010-03-30 11:39:23 +0200 (Tue, 30 Mar 2010) $
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
 * @see  '/../../lib/vivvo/framework/vivvo_framework.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_framework.php');

	/**
	 * vivvo_poll_question
	 * Manipulate
	 *
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
	class vivvo_poll_question extends vivvo_post_object  {

  		/**
  		 * Poll id
  		 *
  		 * @var	integer
  		 */
		var $id;

		/**
		 * Poll name
		 *
		 * @var	string
		 */
		var $name;

		/**
		 * Poll question
		 *
		 * @var	string
		 */
		var $question;

		/**
		 * Start date
		 *
		 * @var	date
		 */
		var $start_date;

		/**
		 * End date
		 *
		 * @var	date
		 */
		var $end_date;

		/**
		 * Poll status (0 for deactive or 1 for active)
		 *
		 * @var integer
		 */
		var $status;

		/**
		 * Name of sql table
		 *
		 * @var	string
		 */
		var $_sql_table = 'poll_questions';

		/**
		 * Vivvo_poll_answer_list object
		 *
		 * @var	vivvo_poll_answer_list
		 */
		var $answer;


		/**
		 * Sets {@link $id}
		 *
		 * @param	integer	$id
		 * @return	boolean
		 * @access	public
		 */
		function set_id($id) {
			$id = (int) $id;
			$this->id = $id;
			return true;

		}

		/**
		 * Sets {@link $name}
		 *
		 * @param	string	$name
		 * @access	public
		 */
		function set_name($name) {
			if ($name != ''){
				$this->name = $name;
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Sets {@link $question}
		 *
		 * @param	string	$question
		 * @access	public
		 */
		function set_question($question) {
			if($question != ''){
				$this->question = $question;
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Sets {@link $start_date}
		 *
		 * @param	date	$start_date
		 * @access	public
		 */
		function set_start_date($start_date = '') {
			if ($start_date == '' || !(preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date))){
				$this->start_date = date('Y-m-d');
			}else {
				$this->start_date = $start_date;
			}
			return true;
		}

		/**
		 * Sets {@link $end_date}
		 *
		 * @param	date	$end_date
		 * @access	public
		 */
		function set_end_date($end_date = '') {
			if ($end_date == '' || !(preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date))){
				$this->end_date = date('Y-m-d', time()+7*24*60*60);
			}else {
				$this->end_date = $end_date;
			}
			return true;
		}

		function set_status($status) {
			if ($status == '1'){
				$this->status = '1';
			}else{
				$this->status = '0';
			}
			return true;
		}

		function get_href(){
			//if (VIVVO_FRIENDY_URL == 1){
				return 'app/poll/'.$this->id;
			//}else
			//	return 'index.php?plugin=poll&amp;search_pid='.$this->id;
		}

		/**
		 * Sets object properties from array uses populate from vivvo_framework
		 *
		 * @param	array	$row
		 */
		function populate ($row, $dump = NULL){
			$status = parent::populate($row, $dump);
			$this->answer =& new vivvo_poll_answer_list(vivvo_lite_site::get_instance());
			$this->answer->get_answer($this->id);
			return $status;
		}


		function on_delete($post_master){
			$poll_answer_list =& new vivvo_poll_answer_list(vivvo_lite_site::get_instance());
			$poll_answer_list->get_answers_by_poll_id($this->id);
			$poll_answer_list->sql_delete_list($post_master);
		}

		function is_voted(){
			if (isset ($_SESSION['vivvo']) && isset($_SESSION['vivvo']['poll']) && $_SESSION['vivvo']['poll'][$this->id]){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * vivvo_poll_answer
	 * Manipulate
	 *
	 *@license		http://www.vivvo.net/license.php
	 *@copyright	SpoonLabs
	 *@package		Vivvo
	 *@subpackage	plugin
	 *@version		0.1
	 *@category		CMS
	 *@author		Slobodan Utvic <utvara@spoonlabs.com>
	 *@author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 *@see			vivvo_post_object
	 *
	 */
	class vivvo_poll_answer extends vivvo_post_object  {

  		/**
  		 * Answer id
  		 *
  		 * @var integer
  		 */
		var $id;

		/**
		 * Poll id
		 *
		 * @var integer
		 */
		var $poll_id;

		/**
		 * Answer
		 *
		 * @var	string
		 */
		var $answer;

		/**
		 * Number of votes for this answer
		 *
		 * @var integer
		 */
		var $vote;

		/**
		 * Name of sql table
		 *
		 * @var string
		 */
		var $_sql_table = 'poll_answers';

		/**
		 * Percent of votes for this answer
		 *
		 * @var	real
		 */
		var $percent;

		/**
		 * Sum of all votes
		 *
		 * @var integer
		 */
		var $all_votes;




		/**
		 * Sets {@link $id}
		 *
		 * @param	integer	$id
		 * @return	boolean
		 * @access	public
		 */
		function set_id($id) {
			$id = (int) $id;
			$this->id = $id;
			return true;
		}

		/**
		 * Sets {@link $poll_id}
		 *
		 * @param	integer	$poll_id
		 * @return	boolean
		 */
		function set_poll_id($poll_id) {
			$poll_id = (int) $poll_id;
			if ($poll_id > 0){
				$this->poll_id = $poll_id;
				return true;
			}else {
				return false;
			}
		}

		/**
		 * Sets {@link $answer}
		 *
		 * @param	string	$answer
		 * @access	public
		 */
		function set_answer($answer) {
			if ($answer != ''){
				$this->answer = $answer;
				return true;
			}else{
				return false;
			}
		}

		function set_percent($percent) {
			$this->percent = $percent;
			return true;
		}

		function set_all_votes($all_votes) {
			$this->all_votes = $all_votes;
			return true;
		}
		/**
		 * Sets {@link $vote}
		 *
		 * @param	integer	$vote
		 * @return	boolean
		 * @access	public
		 */
		function set_vote($vote) {
			$vote = (int) $vote;
			if ($vote > 0){
				$this->vote = $vote;
			}else {
				$this->vote = 0;
			}
			return true;
		}

		function get_percent($multiply = 1){
			return $this->percent * $multiply;
		}

	}

	/**
	 * Creates list of questions
	 *
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
	class vivvo_poll_question_list extends vivvo_db_paged_list  {

		/**
		 * Name of object
		 *
		 * @var string
		 */
		var $post_object_type = 'vivvo_poll_question';

		/**
		 * Name of sql table
		 *
		 * @var	string
		 */
		var $_sql_table = 'poll_questions';

		/**
		 * Creates defult query
		 *
		 * @access	public
		 */
		function _default_query(){
			$this->_query->set_from(VIVVO_DB_PREFIX . 'poll_questions');
			$this->_query->add_fields('id');
			$this->_query->add_fields('name');
			$this->_query->add_fields('question');
			$this->_query->add_fields('start_date');
			$this->_query->add_fields('end_date');
			$this->_query->add_fields('status');
		}

		/**
		 * Secure filter for SELECT query
		 *
		 * @param	string	$type
		 * @param	string	$condition
		 */
		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where('id = ' . $condition);
					break;
				case 'status':
					$this->_query->add_where("status  = '".$condition."'");
					break;
			}
		}

		function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
			//search_query

			if (isset($params['search_pid'])){
				$this->add_filter('id' , intval($params['search_pid']));
				$this->add_generator_option('search_pid', $params['search_pid']);
			}


			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'name':
					$this->_query->add_order('name' . $search_direction);
					break;
				case 'question':
					$this->_query->add_order('question' . $search_direction);
					break;
				case 'start_date':
					$this->_query->add_order('start_date' . $search_direction);
					break;
				case 'end_date':
					$this->_query->add_order('end_date' . $search_direction);
					break;
				case 'status':
					$this->_query->add_order('status' . $search_direction);
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
			$this->_default_query();

			if ($set_list){
				$this->set_list();
				return $this->list;
			}
		}


		/**
		 * Creates list of question by id
		 *
		 * @param	integer	$id
		 * @return	mixed	vivvo_poll_question on success, a false on fail
		 * @access	public
		 */
		function &get_question($id){
			$id = (int) $id;
			$this->_default_query();
			$this->add_filter('id' , $id);
			$this->set_list();
			if (!empty($this->list)){
				return  $this->list[$id];
			}else{
				return false;
			}
		}

		function &get_question_by_status($status){
			$this->_query->reset_query();
			$this->_default_query();
			$this->add_filter('status' , $status);
			$this->set_list();
			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}
		function &get_last_question(){
			$this->_query->reset_query();
			$this->_default_query();
			$this->_query->add_order("id desc");
			$this->set_list();
			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}

		/**
		 * Creates list of all question
		 *
		 * @return	mixed	list on success, a false on fail
		 * @access	public
		 */
		function &get_all_question(){
			$this->_default_query();
			$this->_query->add_order('id ASC');
			$this->set_list();
			if (!empty($this->list)){
				return  $this->list;
			}else{
				return false;
			}
		}

		function get_xml(){
			$xml = '<v3:poll_question_list>';
			if (is_array($this->list) && !empty($this->list)){
				foreach ($this->list as $k=>$v){
					$xml .= $v->get_xml();
				}
			}
			$xml .= "</v3:poll_question_list>";
			return $xml;
		}

	}

	/**
	 * Creates list of answers
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @see			vivvo_db_list
	 */
	class vivvo_poll_answer_list extends vivvo_db_paged_list  {

		/**
		 * Name of object
		 *
		 * @var string
		 */
		var $post_object_type = 'vivvo_poll_answer';

		/**
		 * Name of sql table
		 *
		 * @var	string
		 */
		var $_sql_table = 'poll_answers';

		/**
		 * Percent of vote
		 *
		 * @var real
		 */
		var $percent;

		/**
		 * Sum of all votes
		 *
		 * @var integer
		 */
		var $all_votes;

		/**
		 * Creates defult query
		 *
		 */
		function _default_query(){
			$this->_query->set_from(VIVVO_DB_PREFIX . 'poll_answers');
			$this->_query->add_fields('poll_id');
			$this->_query->add_fields('id');
			$this->_query->add_fields('answer');
			$this->_query->add_fields('vote');
		}

		/**
		 * Secure filter for SELECT query
		 *
		 * @param	string	$type
		 * @param	string	$condition
		 */
		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where("id  = $condition");
					break;
				case 'poll_id':
					$this->_query->add_where("poll_id  = $condition");
					break;
			}
		}

		function &get_answer_by_id($aid){
			$aid = (int) $aid;
			$this->_default_query();
			$this->add_filter('id' , $aid);
			$this->set_list();
			if (!empty($this->list)){
				return  $this->list[$aid];
			}else{
				return false;
			}
		}

		function &get_answers_by_poll_id($pid){
			$pid = (int) $pid;
			$this->_default_query();
			$this->add_filter('poll_id' , $pid);
			$this->set_list();
			if (!empty($this->list)){
				return  $this->list;
			}else{
				return false;
			}
		}


		/**
		 * Creates list of answers with percent of votes
		 *
		 * @param	integer	$pid
		 * @return	mixed	list on success, a false on fail
		 * @access	public
		 */
		function &get_answer($pid){
			$pid = (int) $pid;
			$this->_default_query();
			$this->add_filter('poll_id' , $pid);
			$this->_query->add_order('id ' . 'DESC');
			$this->set_list();

			//Sum votes of all answer
			foreach ($this->list as $k => $v){
				$all_votes += $this->list[$k]->vote;
			}
			//echo $all_votes;


			//Sets percent of vote for single answer
			foreach ($this->list as $k => $v){
				if ($this->list[$k]->vote != 0){
					$this->list[$k]->percent = round($this->list[$k]->vote / $all_votes * 100, 2);
					$this->list[$k]->all_votes = $all_votes;
				}else{
					$this->list[$k]->percent = 0;
					$this->list[$k]->all_votes = $all_votes;
				}
			}

			if (!empty($this->list)){
				return $this->list;
			}else{
				return false;
			}
		}

		function get_xml(){
			$xml = '<v3:poll_answer_list>';
			if (is_array($this->list) && !empty($this->list)){
				foreach ($this->list as $k=>$v){
					$xml .= $v->get_xml();
				}
			}
			$xml .= "</v3:poll_answer_list>";
			return $xml;
		}
	}


?>