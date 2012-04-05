<?php
/* =============================================================================
 * $Revision: 5772 $
 * $Date: 2010-09-15 15:57:30 +0200 (Wed, 15 Sep 2010) $
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
 * Comments object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see			vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class Comments extends vivvo_post_object {

	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * article_id
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$article_id
	 */
	var $article_id;

	/**
	 * user_id
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$user_id
	 */
	public $user_id;

	/**
	 * description
	 * Database field type:	text
	 * Null status:		YES
	 *
	 * @var	string	$description
	 */
	var $description;

	/**
	 * description_src
	 * Database field type:	text
	 * Null status:		YES
	 *
	 * @var	string	$description_src
	 */
	var $description_src;

	/**
	 * create_dt
	 * Database field type:	timestamp
	 * Null status:		YES
	 *
	 * @var	string	$create_dt
	 */
	var $create_dt;

	/**
	 * author
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$author
	 */
	var $author;

	/**
	 * email
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$email
	 */
	var $email;

	/**
	 * ip
	 * Database field type:	varchar(20)
	 * Null status:
	 *
	 * @var	string	$ip
	 */
	var $ip;

	/**
	 * status
	 * Database field type:	enum('0','1')
	 * Null status:
	 *
	 * @var	string	$status
	 */
	var $status;

	/**
	 * www
	 * Database field type:	varchar(255)
	 * Null status:	YES
	 *
	 * @var	string	$www
	 */
	var $www;

	/**
	 * vote
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$vote
	 */
	var $vote;

	/**
	 * reply_to
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	int	$reply_to
	 */
	public $reply_to;

	/**
	 * root_comment
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	int	$root_comment
	 */
	public $root_comment;

	/**
	 * @var	string	URL of user's avatar image
	 */
	protected $avatar_url = false;


	var $_sql_table = 'comments';

	var $article_obj;

	/**
	 * @var	string	URL to author page
	 */
	private $author_href = '';

	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id) {
		$this->id = $id;
		return true;
	}

	/**
	 * Sets {@link $article_id}
	 *
	 * @param	integer	$article_id
	 */
	function set_article_id($article_id){
		$this->article_id = $article_id;
		return true;
	}

	/**
	 * Sets {@link $user_id}
	 *
	 * @param	int	$user_id
	 */
	public function set_user_id($user_id) {
		$this->user_id = $user_id;
	}

	/**
	 * Sets {@link $description}
	 *
	 * @param	string	$description
	 */
	function set_description($description) {
		$this->description = $description;
		return true;
	}

	/**
	 * Sets {@link $description_src}
	 *
	 * @param	string	$description_src
	 */
	function set_description_src($description_src) {
		$this->description_src = $description_src;
		return true;
	}

	/**
	 * Sets {@link $create_dt}
	 *
	 * @param	string	$create_dt
	 */
	function set_create_dt($create_dt){
		$this->create_dt = $create_dt;
		return true;
	}

	/**
	 * Sets {@link $author}
	 *
	 * @param	string	$author
	 */
	function set_author($author){
		$this->author = $author;
		return true;
	}

	/**
	 * Sets {@link $email}
	 *
	 * @param	string	$email
	 */
	function set_email($email){
		$this->email = $email;
		return true;
	}

	/**
	 * Sets {@link $ip}
	 *
	 * @param	string	$ip
	 */
	function set_ip($ip){
		if ($ip == ''){
			return false;
		}else{
			$this->ip = $ip;
			return true;
		}

	}

	/**
	 * Sets {@link $status}
	 *
	 * @param	string	$status
	 */
	function set_status($status){
		if ($status == ''){
			return false;
		}else{
			$this->status = $status;
			return true;
		}

	}

	/**
	 * Sets {@link $www}
	 *
	 * @param	string	$www
	 */
	function set_www($www){
		$this->www = $www;
	}

	/**
	 * Sets {@link $vote}
	 *
	 * @param	integer	$vote
	 */
	function set_vote($vote){
		$this->vote = $vote;
	}

	/**
	 * Sets {@link $reply_to}
	 *
	 * @param	int	$reply_to
	 */
	function set_reply_to($reply_to) {
		$this->reply_to = $reply_to;
		return true;
	}

	/**
	 * Sets {@link $root_comment}
	 *
	 * @param	int	$root_comment
	 */
	function set_root_comment($root_comment) {
		$this->root_comment = $root_comment;
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
	 * Gets $article_id
	 *
	 * @return	integer
	 */
	function get_article_id(){
		return $this->article_id;
	}

	/**
	 * Gets $user_id
	 *
	 * @return	int
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	function get_article_title() {

		if (!$this->article_obj) {
			require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$al = new Articles_list(null, 'title,sefriendly,category_id');
			$this->article_obj = $al->get_article_by_id($this->article_id);
		}

		if ($this->article_obj) {
			return $this->article_obj->get_title();
		}
	}

	function get_article_href(){
		if (!$this->article_obj){
			require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$al = new Articles_list(null, 'title,sefriendly,category_id');
			$this->article_obj = $al->get_article_by_id($this->article_id);
		}
		if ($this->article_obj){
			return $this->article_obj->get_href();
		}
	}

	function get_article_absolute_href(){
		if (!$this->article_obj){
			require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$al = new Articles_list(null, 'title,sefriendly,category_id');
			$this->article_obj = $al->get_article_by_id($this->article_id);
		}
		if ($this->article_obj){
			return $this->article_obj->get_absolute_href();
		}
	}

	/**
	 * Gets $description
	 *
	 * @return	string
	 */
	function get_description() {
		return nl2br($this->description);
	}

	/**
	 * Gets $description_src
	 *
	 * @return	string
	 */
	function get_description_src() {
		return $this->description_src;
	}

	/**
	 * Gets $create_dt
	 *
	 * @return	string
	 */
	function get_create_dt(){
		return format_date($this->create_dt);
	}
	/**
	 * Gets $author
	 *
	 * @return	string
	 */
	function get_author(){
		return $this->author;
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
	 * Gets $ip
	 *
	 * @return	string
	 */
	function get_ip(){
		return $this->ip;
	}
	/**
	 * Gets $status
	 *
	 * @return	string
	 */
	function get_status(){
		return $this->status;
	}

	function get_www(){
		return $this->www;
	}

	/**
	 * Gets $vote
	 *
	 * @return	integer
	 */
	function get_vote(){
		return $this->vote;
	}

	/**
	 * Gets $reply_to
	 *
	 * @return	int
	 */
	function get_reply_to() {
		return $this->reply_to;
	}

	/**
	 * Gets $root_comment
	 *
	 * @return	int
	 */
	function get_root_comment() {
		return (int)$this->root_comment;
	}

	function get_gravatar($size=40){
		return $this->get_avatar($size);
	}

	/**
	 * Returns user avatar image
	 *
	 * @param		int	$size	Used only with gravatar urls (if gravatar usage is enabled)
	 * @deprecated	$size
	 * @return		string
	 */
	public function get_avatar($size = 40) {

		$default = VIVVO_THEME . 'img/avatar.gif';

		if (!$this->avatar_url) {
			if (VIVVO_COMMENTS_ENABLE_GRAVATAR and $this->email) {
				$this->avatar_url = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($this->email) . '&default=' . urlencode($default) . '&size=' . $size;
			} else {
				$this->avatar_url = $default;
			}
		}

		return $this->avatar_url;
	}

	function get_author_name (){
		if (!empty($this->author)){
			return $this->author;
		}else{
			return $this->email;
		}
	}

	function get_summary($word_number = 25) {

		$text = trim($this->description);
		$output = explode(' ', $text);

		if ($word_number < 1) {
			$word_number = 25;
		}

		for ($i = 0; $i < min($word_number, sizeof($output)); $i++) {
			$article_short .= $output[$i] . ' ';
		}

		if ($i<sizeof($output)) {
			$article_short .= '...';
		}

		return $article_short;
	}

	/**
	 * Returns comment summary with HTML tags stripped
	 *
	 * @return	string
	 */
	public function get_plain_summary($word_number = 25) {
		$words = preg_split('/\s+/', strip_tags($this->description));
		return implode(' ', array_slice($words, 0, $word_number)) . (count($words) > $word_number ? '...' : '');
	}

	/**
	 * Returns comment body, if BBCode is enabled and comment source is defined source will be returned
	 *
	 * @return	string
	 */
	public function get_body() {

		if (VIVVO_COMMENTS_ENABLE_BBCODE and $this->description_src) {
			return $this->description_src;
		}

		return strip_tags($this->description);
	}

	/**
	 * Gets $author_href
	 *
	 * @return	string
	 */
	public function get_author_href() {
		return $this->author_href;
	}

	/**
	 * @var	array	Responses posted to this comment
	 */
	protected $respones = array();

	/**
	 * Adds response to this comment
	 *
	 * @param	Comments	$comment
	 */
	public function add_response($comment) {
		$this->respones[$comment->get_id()] = $comment;
	}

	/**
	 * Returns list of all responses
	 *
	 * @return	array
	 */
	public function get_responses() {
		return $this->respones;
	}

	/**
	 * Renders responses
	 *
	 * @param	string	$template_file
	 * @return	string
	 */
	public function render_responses($template_file) {

		$template = new template(null, vivvo_lite_site::get_instance()->get_template());
		$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $template_file);
		$template->assign('comment_list', $this->respones);

		return $template->get_output();
	}

	/**
	 * Populate object properties
	 *
	 * @param	array	$data
	 * @param	bool	$dump
	 * @return	bool
	 */
	public function populate($data, $dump = null) {

		if ($status = parent::populate($data, $dump) and $this->user_id) {

			$user = vivvo_lite_site::get_instance()->get_user_manager()->get_user_by_id($this->user_id);

			if ($user) {
				$this->set_author($user->get_name());
				$this->set_www($user->get_www());
				$this->set_email($user->get_email_address());
				$this->author_href = $user->get_href();
				$this->avatar_url = $user->get_picture();
				if ($this->avatar_url and !preg_match('/^[^:\/\.\?]+:/', $this->avatar_url)) {
					$this->avatar_url = VIVVO_STATIC_URL . 'thumbnail.php?size=avatar&file=' . $this->avatar_url;
				}
			}
		}

		return $status;
	}

	/**
	 * @var	array	Default BBCode tags
	 */
	public static $bbcode_tags = array(
		'b' => array('span', 'bold'),
		'i' => array('span', 'italic'),
		'u' => array('span', 'underlined'),
		's' => array('span', 'striketrough'),
		'quote' => array('div', 'quote'),
		'url' => array('a', array('value' => 'href'))
	);

	/**
	 * Parses BBCode tags to HTML
	 *
	 * @param	string	$source
	 * @param	array	$tags
	 * @return	string
	 */
	public static function parse_bbcode($source, array $tags = array()) {

		$tags = array_merge(self::$bbcode_tags, $tags);

        $find = array(); $re_find = array();
        $replace = array(); $re_replace = array();

        foreach ($tags as $tag => $data) {

            list($elem, $class) = $data;

            if (is_array($class)) {
                $className = isset($class['class']) ? ' class="' . $class['class'] . '"' : '';
                if (isset($class['find'])) {
                    $re_find[] = $class['find'];
                    $attr = $class['tag'];
                    $p = $class['prefix'];
                    $s = $class['suffix'];
                    if (isset($class['content'])) {
                        if (!$class['content']) {
                            $re_replace[] = "<$elem{$className} $attr=\"$p$1$s\" />";
                        } else {
                            $attr2 = $class['content'];
                            $re_replace[] = "<$elem{$className} $attr=\"$p$1$s\" $attr2=\"$2\" />";
                        }
                    } else {
                        $re_replace[] = "<$elem{$className} $attr=\"$p$1$s\" />";
                    }
                } elseif (isset($class['value'])) {
                    $re_find[] = "|\[$tag=([^\]]+)\]|";
                    $attr = $class['value'];
                    $re_replace[] = "<$elem{$className} $attr=\"$1\">";
                    $find[] = "[/$tag]"; $replace[] = "</$elem>";
                } elseif (isset($class['content'])) {
                    $attr = $class['content'];
                    $re_find[] = "|\[$tag\](.+?)\[/$tag\]|";
                    $re_replace[] = "<$elem{$className} $attr=\"$1\" />";
                }
            } else {
                $find[] = "[$tag]";  $replace[] = "<$elem class=\"$class\">";
                $find[] = "[/$tag]"; $replace[] = "</$elem>";
            }
        }

        return self::fix_html(str_replace($find, $replace, preg_replace($re_find, $re_replace, $source)));
    }

	/**
	 * Takes possibly invalid HTML string (with mismatched/unclosed tags) and fixes it
	 *
	 * @param	string	$html
	 * @return	string
	 */
    private static function fix_html($html) {

        $stack = array();
        $ret = '';

        while ($html) {

            if (!preg_match('|<(/?\w+)[^>]*>|', $html, $matches,  PREG_OFFSET_CAPTURE)) {
                $ret .= $html;
                break;
            }

            list($tag, $tag_start) = $matches[0];

            $ret .= substr($html, 0, $tag_start);

            if ( $matches[1][0][0] != '/' ) {
                $stack[] = $matches[1][0];
            } else {
                if (empty($stack)) {
                    $html = substr($html, $tag_start + strlen($tag));
                    continue;
                }

                while ($close_tag = array_pop($stack)) {
                    if (strtolower($close_tag) == strtolower(substr($matches[1][0], 1))) {
                        break;
                    } else {
                        $ret .= "</$close_tag>";
                    }
                }
            }

            $ret.= $tag;

            $html = substr($html, $tag_start + strlen($tag));
        }

        while ($tag = array_pop($stack)) {
            if (!in_array(strtolower($tag), array('img', 'input', 'br', 'hr', 'link', 'base', 'meta'))) {
                $ret .= "</$tag>";
            }
        }

        return $ret;
	}
}

/**
 * Comments list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class Comments_list extends vivvo_db_paged_list {
	var $_sql_table = 'comments';
	var $post_object_type = 'Comments';


	function _default_query(){
		$this->_query->set_from(VIVVO_DB_PREFIX . 'comments AS cm ');
		if (is_array($this->_fields) && !empty($this->_fields)){
			foreach ($this->_fields as $field){
				$this->_query->add_fields('cm.' . $field);
			}
		}else{
			$this->_query->add_fields('cm.*');
		}

	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'cm.id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where('(cm.id IN (' . $condition . '))');
			break;
			case 'cm.article_id':
				$this->_query->add_where('(cm.article_id = \'' . $condition . '\')');
			break;
			case 'cm.user_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where('cm.user_id IN (' . $condition . ')');
			break;
			case 'cm.description':
				$condition = str_replace('%', '\%', $condition);
				$this->_query->add_where('(cm.description LIKE \'%' . $condition . '%\')');
			break;
			case 'cm.create_dt':
				$this->_query->add_where('(cm.create_dt = \'' . $condition . '\')');
			break;
			case 'cm.author':
				$condition = str_replace('%', '\%', $condition);
				$this->_query->add_where('(cm.author LIKE \'%' . $condition . '%\')');
			break;
			case 'cm.author_name':
				$this->_query->add_where("cm.author = '$condition'");
			break;
			case 'cm.email':
				$condition = str_replace('%', '\%', $condition);
				$condition = str_replace('*', '%', $condition);
				$condition = str_replace('?', '_', $condition);
				$this->_query->add_where('(cm.email LIKE \'' . $condition . '\')');
			break;
			case 'cm.email_exact':
				$this->_query->add_where('(cm.email = \'' . $condition . '\')');
			break;
			case 'cm.ip':
				$condition = str_replace('%', '\%', $condition);
				$condition = str_replace('*', '%', $condition);
				$condition = str_replace('?', '_', $condition);
				$this->_query->add_where('(cm.ip LIKE \'' . $condition . '\')');
			break;
			case 'cm.status':
				$this->_query->add_where('(cm.status = \'' . $condition . '\')');
			break;
			case 'cm.created_before':
				$this->_query->add_where('(cm.create_dt < (DATE_SUB(NOW(), INTERVAL ' . $condition . '  DAY)))');
				break;
			case 'cm.created_after':
				$this->_query->add_where('(cm.create_dt > (DATE_SUB(NOW(), INTERVAL ' . $condition . '  DAY)))');
				break;
			case 'cm.vote':
				$this->_query->add_where('(cm.vote = \'' . $condition . '\')');
			break;
			case 'cm.reply_to':
				if ($condition == 0) {
					$this->_query->add_where('cm.reply_to IS NULL');
				} else {
					$condition = secure_sql_in($condition);
					$this->_query->add_where("cm.reply_to IN ($condition)");
				}
			break;
			case 'cm.not_reply_to':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("cm.reply_to NOT IN ($condition)");
			break;
			case 'cm.root_comment':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("cm.root_comment IN ($condition)");
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
	 * @return	array	Array of articles
	 */
	function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
		//search_query

		if (isset($params['search_id'])){
			if (is_array($params['search_id'])){
				if (!in_array(0, $params['search_id'])){
					$params['search_id'] = implode(',', $params['search_id']);
					$this->add_filter('cm.id',$params['search_id']);
				}
			}else{
				if ($params['search_id'] != 0){
					$this->add_filter('cm.id',$params['search_id']);
				}
			}
		}

		if (isset($params['search_article_id'])){
			$this->add_filter('cm.article_id', $params['search_article_id']);
		}
		if (isset($params['search_user_id'])){
			$this->add_filter('cm.user_id', $params['search_user_id']);
		}
		if (isset($params['search_description']) && $params['search_description'] != ''){
			$this->add_filter('cm.description', $params['search_description']);
		}
		if (isset($params['search_create_dt'])){
			$this->add_filter('cm.create_dt', $params['search_create_dt']);
		}
		if (isset($params['search_author']) && $params['search_author'] != ''){
			$this->add_filter('cm.author', $params['search_author']);
		}
		if (isset($params['search_author_name']) && $params['search_author_name'] != ''){
			$this->add_filter('cm.author_name', $params['search_author_name']);
		}
		if (isset($params['search_email'])){
			$this->add_filter('cm.email', $params['search_email']);
		}
		if (isset($params['search_email_exact'])){
			$this->add_filter('cm.email_exact', $params['search_email_exact']);
		}
		if (isset($params['search_ip'])){
			$this->add_filter('cm.ip', $params['search_ip']);
		}
		if (isset($params['search_status']) && $params['search_status'] !== ''){
			$this->add_filter('cm.status', $params['search_status']);
		}

		if (intval($params['search_search_date']) !== 0){
			$this->add_filter((($params['search_before_after'] === '1') ? 'cm.created_before' : 'cm.created_after'), $params['search_search_date']);
		}

		if (isset($params['search_vote']) && $params['search_vote'] !== ''){
			$this->add_filter('cm.vote', $params['search_vote']);
		}

		if (!empty($params['search_reply_to'])) {
			$this->add_filter('cm.reply_to', $params['search_reply_to']);
		}
		if (!empty($params['search_not_reply_to'])) {
			$this->add_filter('cm.not_reply_to', $params['search_reply_to']);
		}
		if (!empty($params['search_root_comment'])) {
			$this->add_filter('cm.root_comment', $params['search_root_comment']);
		}

		$threaded = VIVVO_COMMENTS_ENABLE_THREADED;
		if ($threaded and (!empty($params['threaded']) and $params['threaded'] == 1) and empty($params['search_reply_to']) and empty($params['search_not_reply_to'])) {
			$this->add_filter('cm.reply_to', 0);
		} else {
			$threaded = false;
		}

		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){

			case 'id':
				$this->_query->add_order('cm.id' . $search_direction);
				break;
			case 'article_id':
				$this->_query->add_order('cm.article_id' . $search_direction);
				break;
			case 'user_id':
				$this->_query->add_order('cm.user_id' . $search_direction);
				break;
			case 'description':
				$this->_query->add_order('cm.description' . $search_direction);
				break;
			case 'create_dt':
				$this->_query->add_order('cm.create_dt' . $search_direction);
				break;
			case 'created':
				$this->_query->add_order('cm.create_dt' . $search_direction);
				break;
			case 'author':
				$this->_query->add_order('cm.author' . $search_direction);
				break;
			case 'email':
				$this->_query->add_order('cm.email' . $search_direction);
				break;
			case 'ip':
				$this->_query->add_order('ip' . $search_direction);
				break;
			case 'status':
				$this->_query->add_order('cm.status' . $search_direction);
				break;
			case 'vote':
				$this->_query->add_order('cm.vote' . $search_direction);
				break;

			default:
				$order = 'cm.id';
				$this->_query->add_order('cm.id' . ' DESC');
				break;
		}

		$limit = (int) $limit;
		$this->_query->set_limit($limit);
		$offset = (int) $offset;
		$this->_query->set_offset($offset);
		$this->_default_query(true);

		if ($set_list) {

			$this->set_list();

			if ($threaded && !empty($this->list)) {

				$tmp_list = new Comments_list();
				$list = $tmp_list->search(array('search_root_comment' => array_keys($this->list), 'search_status' => 1));

				foreach ($list as $id => $comment) {
					if (($reply_to = $comment->get_reply_to()) > 0) {
						if (isset($this->list[$reply_to])) {
							$this->list[$reply_to]->add_response($comment);
						} elseif (isset($list[$reply_to])) {
							$list[$reply_to]->add_response($comment);
						}
					}
				}
			}

			return $this->list;
		}
	}
	function get_comments_by_article_id($article_id){
		$article_id = (int)$article_id;
		$this->search(array('search_article_id'=>$article_id));
		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}
	function get_comments_by_id($id){
		$id = (int)$id;
		$this->search(array('search_id'=>$id));
		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}

	function get_comments_by_ids($ids){
		$val = trim(implode(',', $ids));
		if (empty($val)){
			return false;
		}
		$this->search(array('search_id'=>$ids));
		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

	function get_search_params($sm, $in_params){
		$params = array ();

		if (!empty($in_params['search_limit'])){
			$params['search_limit'] = $in_params['search_limit'];
		}else{
			$params['search_limit'] = 10;
		}

		$params['search_options'] = array();

		if (isset($in_params['search_options']) && is_array($in_params['search_options']) && !empty($in_params['search_options'])) $params['search_options'] = $in_params['search_options'];

		if (!empty($in_params['search_article_id'])) $params['search_options']['search_article_id'] = $in_params['search_article_id'];
		if (!empty($in_params['search_user_id'])) $params['search_options']['search_user_id'] = $in_params['search_user_id'];
		if (!empty($in_params['threaded'])) $params['search_options']['threaded'] = $in_params['threaded'];
		if (!empty($in_params['search_reply_to'])) $params['search_options']['search_reply_to'] = $in_params['search_reply_to'];
		if (!empty($in_params['search_not_reply_to'])) $params['search_options']['search_not_reply_to'] = $in_params['search_not_reply_to'];
		if (defined('VIVVO_ADMIN_MODE')){
			if (isset($in_params['search_status'])) $params['search_options']['search_status'] = $in_params['search_status'];
			if (!empty($in_params['search_ip'])) $params['search_options']['search_ip'] = $in_params['search_ip'];
			if (!empty($in_params['search_email'])) $params['search_options']['search_email'] = $in_params['search_email'];
			if (!empty($in_params['search_author'])) $params['search_options']['search_author'] = $in_params['search_author'];
			if (!empty($in_params['search_author_name'])) $params['search_options']['search_author_name'] = $in_params['search_author_name'];
			if (!empty($in_params['search_description'])) $params['search_options']['search_description'] = $in_params['search_description'];
			if (!empty($in_params['search_search_date'])) $params['search_options']['search_search_date'] = $in_params['search_search_date'];
			if (!empty($in_params['search_before_after'])) $params['search_options']['search_before_after'] = $in_params['search_before_after'];
		}else{
			$params['search_options']['search_status'] = 1;
		}

		if (!empty($in_params['search_email_exact'])) $params['search_options']['search_email_exact'] = $in_params['search_email_exact'];
		if (!empty($in_params['search_id'])) $params['search_options']['search_id'] = $in_params['search_id'];

		if (!empty($in_params['search_sort_by'])){
			$params['search_sort_by'] = $in_params['search_sort_by'];
		}else{
			$params['search_sort_by'] = 'created';
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
		$params = Comments_list::get_search_params($sm, $um->list);
		return $params;
	}
}

?>