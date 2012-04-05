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


class Statistics extends vivvo_post_object {
	var $id;
	var $ip_address;
	var $impression;
	var $date;
	var $agent;
	var $uri;
	var $ref;
	var $session_id;

	function set_id($id){
		$this->id = $id;
	}
	function set_ip_address($ip_address){
		$this->ip_address = $ip_address;
	}
	function set_impression($impression){
		$this->impression = $impression;
	}
	function set_date($date){
		$this->date = $date;
	}
	function set_agent($agent){
		$this->agent = $agent;
	}
	function set_uri($uri){
		$this->uri = $uri;
	}
	function set_ref($ref){
		$this->ref = $ref;
	}
	function set_session_id($session_id){
		$this->session_id = $session_id;
	}


	function get_id(){
		return $this->id;
	}
	function get_ip_address(){
		return $this->ip_address;
	}
	function get_impression(){
		return $this->impression;
	}
	function get_date(){
		return $this->date;
	}
	function get_agent(){
		return $this->agent;
	}
	function get_uri(){
		return $this->uri;
	}
	function get_ref(){
		return $this->ref;
	}

}
class Statistics_list extends vivvo_db_paged_list {
	var $_sql_table = 'Statistics';
	var $post_object_type = 'Statistics';

	function _default_query(){
		$this->_query->set_from(VIVVO_DB_PREFIX . 'Statistics ');
			$this->_query->add_fields('*');
	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
				$this->_query->add_where('(id = \'' . $condition . '\')');
			break;
			case 'ip_address':
				$this->_query->add_where('(ip_address = \'' . $condition . '\')');
			break;
			case 'impression':
				$this->_query->add_where('(impression = \'' . $condition . '\')');
			break;
			case 'date':
				$this->_query->add_where('(date = \'' . $condition . '\')');
			break;
			case 'agent':
				$this->_query->add_where('(agent = \'' . $condition . '\')');
			break;
			case 'uri':
				$this->_query->add_where('(uri = \'' . $condition . '\')');
			break;
			case 'ref':
				$this->_query->add_where('(ref = \'' . $condition . '\')');
			break;
			case 'session_id':
				$this->_query->add_where('(session_id = md5(\'' . $condition . '\'))');
			break;
			case '':
			;
		}
	}

	function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
		//search_query

		if (isset($params['search_id'])){
			$this->add_filter('id',$params['search_id']);
		}
		if (isset($params['search_ip_address'])){
			$this->add_filter('ip_address',$params['search_ip_address']);
		}
		if (isset($params['search_impression'])){
			$this->add_filter('impression',$params['search_impression']);
		}
		if (isset($params['search_date'])){
			$this->add_filter('date',$params['search_date']);
		}
		if (isset($params['search_agent'])){
			$this->add_filter('agent',$params['search_agent']);
		}
		if (isset($params['search_uri'])){
			$this->add_filter('uri',$params['search_uri']);
		}
		if (isset($params['search_ref'])){
			$this->add_filter('ref',$params['search_ref']);
		}
		if (isset($params['session_id'])){
			$this->add_filter('session_id',$params['session_id']);
		}



		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'id':
				$this->_query->add_order('id' . $search_direction);
				break;
			case 'ip_address':
				$this->_query->add_order('ip_address' . $search_direction);
				break;
			case 'impression':
				$this->_query->add_order('impression' . $search_direction);
				break;
			case 'date':
				$this->_query->add_order('date' . $search_direction);
				break;
			case 'agent':
				$this->_query->add_order('agent' . $search_direction);
				break;
			case 'uri':
				$this->_query->add_order('uri' . $search_direction);
				break;
			case 'ref':
				$this->_query->add_order('ref' . $search_direction);
				break;
			case 'session_id':
				$this->_query->add_order('session_id' . $search_direction);
				break;


			default:
				$order = 'id';
				$this->_query->add_order('id' . ' DESC');
				break;
		}

		$limit = (int) $limit;
		$this->_query->set_limit($limit);
		$offset = (int) $offset;
		$this->_query->set_offset($offset);
		$this->_default_query(true);

		if ($set_list){
			$this->set_list();
			return $this->list;
		}
	}
}
?>