<?php
/* =============================================================================
 * $Revision: 5065 $
 * $Date: 2010-04-22 14:04:27 +0200 (Thu, 22 Apr 2010) $
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
 * Users object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @see			vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class Users extends vivvo_user {

	var $id_key = 'userid';
	var $_sql_table = 'users';
	/**
	 * userid
	 * Database field type:	int(9)
	 * Null status:
	 *
	 * @var	integer	$userid
	 */
	var $userid;

	/**
	 * first_name
	 * Database field type:	varchar(25)
	 * Null status:
	 *
	 * @var	string	$first_name
	 */
	var $first_name;

	/**
	 * last_name
	 * Database field type:	varchar(25)
	 * Null status:		YES
	 *
	 * @var	string	$last_name
	 */
	var $last_name;

	/**
	 * email_address
	 * Database field type:	varchar(35)
	 * Null status:		YES
	 *
	 * @var	string	$email_address
	 */
	var $email_address;

	/**
	 * username
	 * Database field type:	varchar(25)
	 * Null status:
	 *
	 * @var	string	$username
	 */
	var $username;

	/**
	 * password
	 * Database field type:	varchar(255)
	 * Null status:
	 *
	 * @var	string	$password
	 */
	var $password;

	/**
	 * activated
	 * Database field type:	enum('-1','0','1','2')
	 * Null status:
	 *
	 * @var	string	$activated
	 */
	var $activated;

	/**
	 * picture
	 * Database field type:	varchar(100)
	 * Null status:		YES
	 *
	 * @var	string	$picture
	 */
	var $picture;

	/**
	 * bio
	 * Database field type:	text
	 * Null status:		YES
	 *
	 * @var	string	$bio
	 */
	var $bio;

	/**
	 * www
	 * Database field type:	varchar(100)
	 * Null status:		YES
	 *
	 * @var	string	$www
	 */
	var $www;

	/**
	 * logins
	 * Database field type:	int(9)
	 * Null status:
	 *
	 * @var	integer	$logins
	 */
	var $logins;

	/**
	 * last_login
	 * Database field type:	datetime
	 * Null status:
	 *
	 * @var	string	$last_login
	 */
	var $last_login;

	/**
	 * session_id
	 * Database field type:	varchar(32)
	 * Null status:		YES
	 *
	 * @var	string	$session_id
	 */
	var $session_id;

	var $created;

	/**
	 * Sets {@link $userid}
	 *
	 * @param	integer	$userid
	 */
	function set_id($userid){
		$this->userid = $userid;
		return true;
	}

	/**
	 * Sets {@link $userid}
	 *
	 * @param	integer	$userid
	 */
	function set_userid($userid){
		$this->userid = $userid;
		return true;
	}

	/**
	 * Sets {@link $first_name}
	 *
	 * @param	string	$first_name
	 */
	function set_first_name($first_name){
		$this->first_name = $first_name;
		return true;
	}

	/**
	 * Sets {@link $last_name}
	 *
	 * @param	string	$last_name
	 */
	function set_last_name($last_name){
		$this->last_name = $last_name;
		return true;
	}

	/**
	 * Sets {@link $email_address}
	 *
	 * @param	string	$email_address
	 */
	function set_email_address($email_address){
		$email_address = strtolower($email_address);
		if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $email_address)){
			$this->email_address = $email_address;
			return false;
		}else{
			$this->email_address = $email_address;
			return true;
		}
	}

	/**
	 * Sets {@link $username}
	 *
	 * @param	string	$username
	 */
	function set_username($username){
		if ($username != ''){
			$this->username = $username;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $password}
	 *
	 * @param	string	$password
	 */
	function set_password($password){
		if ($password != ''){
			$this->password = $password;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $activated}
	 *
	 * @param	string	$activated
	 */
	function set_activated($activated){
		if ($activated != ''){
			$this->activated = $activated;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $picture}
	 *
	 * @param	string	$picture
	 */
	function set_picture($picture){
		if (file_exists(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $picture)){
			$this->picture = $picture;
		}else{
			$this->picture = '';
		}
		return true;
	}

	/**
	 * Sets {@link $bio}
	 *
	 * @param	string	$bio
	 */
	function set_bio($bio){
		$this->bio = $bio;
		return true;
	}

	/**
	 * Sets {@link $www}
	 *
	 * @param	string	$www
	 */
	function set_www($www){
		$this->www = $www;
		return true;
	}

	/**
	 * Sets {@link $logins}
	 *
	 * @param	integer	$logins
	 */
	function set_logins($logins){
		if ($logins != ''){
			$this->logins = $logins;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $last_login}
	 *
	 * @param	string	$last_login
	 */
	function set_last_login($last_login){
		if ($last_login != ''){
			$this->last_login = $last_login;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $session_id}
	 *
	 * @param	string	$session_id
	 */
	function set_session_id($session_id){
		$this->session_id = $session_id;
		return true;
	}

	/**
	 * Sets {@link $created}
	 *
	 * @param	string	$created
	 */
	function set_created($created){
		if ($created != ''){
			$this->created = $created;
			return true;
		}else{
			return false;
		}
	}

	function set_privileges(){
		//load gourp privilegies
		$gul = new group_user_list();
		$group_privilegies = $gul->get_user_groups($this->get_id());
		$group_privilegies = array_unique($group_privilegies);

		$gl = new group_list();
		$this->groups =& $gl->get_groups($group_privilegies);

		$this->privileges = $this->_merge_privileges($this->get_user_privileges(), $gl->merge_privileges($group_privilegies));

		if (isset($this->privileges['EDITOR']['Categories']) && is_array($this->privileges['EDITOR']['Categories']) && !empty($this->privileges['EDITOR']['Categories'])){
			//merge EDITOR and WRITE
			if (isset($this->privileges['WRITE']['Categories']) && is_array($this->privileges['WRITE']['Categories'])){
				$this->privileges['WRITE']['Categories'] = array_merge($this->privileges['WRITE']['Categories'], $this->privileges['EDITOR']['Categories']);
			}

			//merge EDITOR and READ
			if (isset($this->privileges['READ']['Categories']) && is_array($this->privileges['READ']['Categories'])){
				$this->privileges['READ']['Categories'] = array_merge($this->privileges['READ']['Categories'], $this->privileges['EDITOR']['Categories']);
			}
		}
	}

	/**
	 * @var	array	Array of user group ids (used only when adding/editing user)
	 */
	private $group_ids = null;

	/**
	 * Helper method for preserving selected user groups when add/edit action fails
	 *
	 * @param	array	$groups
	 */
	public function set_groups($groups) {
		$this->group_ids = (array)$groups;
	}

	/**
	 * Ovveride of in_group method, used when add/edit user action fails
	 *
	 * @param	int		$group_id
	 * @return	bool
	 */
	public function in_group($group_id) {
		if (is_array($this->group_ids)) {
			return in_array($group_id, $this->group_ids);
		}
		return parent::in_group($group_id);
	}


	/**
	 * Gets $userid
	 *
	 * @return	integer
	 */
	function get_id(){
		return $this->userid;
	}

	/**
	 * Gets $userid
	 *
	 * @return	integer
	 */
	function get_userid(){
		return $this->userid;
	}
	/**
	 * Gets $first_name
	 *
	 * @return	string
	 */
	function get_first_name(){
		return $this->first_name;
	}
	/**
	 * Gets $last_name
	 *
	 * @return	string
	 */
	function get_last_name(){
		return $this->last_name;
	}
	/**
	 * Gets $email_address
	 *
	 * @return	string
	 */
	function get_email_address(){
		return $this->email_address;
	}
	/**
	 * Gets $username
	 *
	 * @return	string
	 */
	function get_username(){
		return $this->username;
	}
	/**
	 * Gets $password
	 *
	 * @return	string
	 */
	function get_password(){
		return $this->password;
	}
	/**
	 * Gets $activated
	 *
	 * @return	string
	 */
	function get_activated(){
		return $this->activated;
	}

	/**
	 * Gets $picture
	 *
	 * @return	string
	 */
	function get_picture(){
		return $this->picture;
	}

	/**
	 * Returns path to user's picture
	 *
	 * @param	string	$size
	 * @return	string
	 */
	public function get_picture_href($size = 'summary_large') {
		return VIVVO_STATIC_URL . 'thumbnail.php?file=' .  $this->picture . '&size=' . $size;
	}

	/**
	 * Gets $bio
	 *
	 * @return	string
	 */
	function get_bio(){
		return $this->bio;
	}
	/**
	 * Gets $www
	 *
	 * @return	string
	 */
	function get_www(){
		return $this->www;
	}
	/**
	 * Gets $logins
	 *
	 * @return	integer
	 */
	function get_logins(){
		return $this->logins;
	}
	/**
	 * Gets $last_login
	 *
	 * @return	string
	 */
	function get_last_login(){
		return $this->last_login;
	}
	/**
	 * Gets $session_id
	 *
	 * @return	string
	 */
	function get_session_id(){
		return $this->session_id;
	}

	/**
	 * Gets $created
	 *
	 * @return	string
	 */
	function get_created(){
		return $this->created;
	}

	function get_name(){
		if ($this->first_name != ''){
			return $this->first_name . ' ' . $this->last_name;
		}else{
			return $this->username;
		}
	}

	function get_admin_logout_url(){
		return 'index.php?login&amp;action=login&amp;cmd=logout';
	}

	function on_delete(){
		$fm = vivvo_lite_site::get_instance()->get_file_manager();
		if ($this->get_picture() != ''){
			$fm->delete_fs(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $this->get_picture());
		}
	}

	/**
	 * Resets user hash string and sets cookie (or clears it)
	 *
	 * @param	int		$expires	If less than or equal to 0 cookie will be cleared
	 */
	public function reset_user_hash($expires = 0) {

		$this->session_id = md5(md5($this->password . $this->username) . mt_rand(100000, 999999) . VIVVO_START_TIME);

		$hash = $expires > 0 ? md5($this->session_id) : null;
		$expires = $expires > 0 ? VIVVO_START_TIME + $expires : 1;

		defined('VIVVO_COOKIE_PREFIX') or define('VIVVO_COOKIE_PREFIX', 'Vivvo');

		setcookie(VIVVO_COOKIE_PREFIX . 'UserHash', $hash, $expires, VIVVO_COOKIE_PATH,  VIVVO_COOKIE_DOMAIN);
	}
}

/**
 * Users list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class Users_list extends vivvo_users_list {
	var $id_key = 'userid';
	var $_sql_table = 'users';
	var $post_object_type = 'Users';

	function _default_query(){
		$this->_query->set_from(
								VIVVO_DB_PREFIX . 'users AS u');
		if (is_array($this->_fields) && !empty($this->_fields)){
			foreach ($this->_fields as $field){
				$this->_query->add_fields($field);
			}
		}else{
			$this->_query->add_fields('u.*');
		}

	}


	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'userid':
				$condition = secure_sql_in($condition);
				$this->_query->add_where('(u.userid IN (' . $condition . '))');
			break;
			case 'first_name':
				$this->_query->add_where('(u.first_name = \'' . $condition . '\')');
			break;
			case 'last_name':
				$this->_query->add_where('(u.last_name = \'' . $condition . '\')');
			break;
			case 'name':
				$condition = str_replace('%', '\%', $condition);
				$this->_query->add_where('(u.last_name LIKE \'%' . $condition . '%\') OR (u.first_name LIKE \'%' . $condition . '%\') OR (u.username LIKE \'%' . $condition . '%\')');
			break;
			case 'email_address':
				$this->_query->add_where('(u.email_address = \'' . $condition . '\')');
			break;
			case 'username':
				$this->_query->add_where('(u.username = \'' . $condition . '\')');
			break;
			case 'password':
                 $this->_query->add_where('(u.password = \'' . $condition . '\')');
            break;
			case 'session_id':
                $this->_query->add_where("u.session_id = '$condition'");
            break;
			case 'user_hash':
                $this->_query->add_where("MD5(u.session_id) = '$condition'");
            break;
			case 'activated':
				$this->_query->add_where('(u.activated = \'' . $condition . '\')');
			break;
			case 'group':
				$condition = secure_sql_in($condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'group_user AS gu ON gu.user_id = u.userid ','gu');
				$this->_query->add_where('(gu.group_id IN (' . $condition . '))');
				$this->_query->add_group_by('u.userid');
			break;
			case 'concat_first_last':
				$this->_query->add_where('CONCAT(u.first_name, "-", u.last_name ) = \'' . $condition .'\'');
			break;
			case 'created_before':
				$this->_query->add_where('(u.created < (DATE_SUB(NOW(), INTERVAL ' . $condition . '  DAY)))');
			break;
			case 'created_after':
				$this->_query->add_where('(u.created > (DATE_SUB(NOW(), INTERVAL ' . $condition . '  DAY)))');
			break;
			case 'md5':
				$this->_query->add_where("md5(concat( u.`email_address` , u.`username` , u.`created` ) ) = '$condition'");
			break;
			case 'forgot':
				$this->_query->add_where("md5( concat( u.`username` , u.`email_address`, u.`password` ) ) = '$condition'");
			break;
		}
	}

	function set_order($order, $search_direction){
		switch ($order){
			case 'id':
				$this->_query->add_order('u.userid' . $search_direction);
				break;
			case 'first_name':
				$this->_query->add_order('u.first_name' . $search_direction);
				break;
			case 'last_name':
				$this->_query->add_order('u.last_name' . $search_direction);
				break;
			case 'email_address':
				$this->_query->add_order('u.email_address' . $search_direction);
				break;
			case 'username':
				$this->_query->add_order('u.username' . $search_direction);
				break;
			case 'name':
				$this->_query->add_order('u.first_name' . $search_direction);
				$this->_query->add_order('u.last_name' . $search_direction);
				break;
			case 'random':
				$this->_query->add_order('rand( )' . $search_direction);
				break;
			default:
				$order = 'id';
				$this->_query->add_order('u.userid' . ' DESC');
				break;
		}
	}

	function get_user_from_session() {

		defined('VIVVO_COOKIE_PREFIX') or define('VIVVO_COOKIE_PREFIX', 'Vivvo');

		$userhash_cookie = VIVVO_COOKIE_PREFIX . 'UserHash';

		if (!empty($_SESSION['vivvo']) and !empty($_SESSION['vivvo']['user_id'])) {

			return $this->get_user_by_id($_SESSION['vivvo']['user_id']);

		} elseif (!empty($_COOKIE[$userhash_cookie])) {	// remember me cookie

			$this->_query->reset_query();
			$this->_default_query();
			$this->add_filter('user_hash', $_COOKIE[$userhash_cookie]);
			$this->set_list();

			if (!empty($this->list)) {
				return current($this->list);
			}
		}

		return false;
	}

	function get_login_tempalte(){
		return 'box/login.tpl';
	}
}

?>