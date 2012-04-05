<?php
/* =============================================================================
 * $Revision: 5913 $
 * $Date: 2010-11-01 14:29:03 +0100 (Mon, 01 Nov 2010) $
 *
 * Vivvo CMS v4.7 (build 6082)
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
 * Articles object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @see			vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class Articles extends vivvo_post_object {

	/**
	 * id
	 * Database field type:	int(6)
	 * Null status:
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * category_id
	 * Database field type:	int(6)
	 * Null status:
	 *
	 * @var	integer	$category_id
	 */
	var $category_id;

	/**
	 * user_id
	 * Database field type:	int(6)
	 * Null status:
	 *
	 * @var	integer	$user_id
	 */
	var $user_id;

	/**
	 * user_domain
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$user_domain
	 */
	var $user_domain;

	/**
	 * author
	 * Database field type:	varchar(30)
	 * Null status:
	 *
	 * @var	string	$author
	 */
	var $author;

	/**
	 * title
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$title
	 */
	var $title;

	/**
	 * image
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$image
	 */
	var $image;

	/**
	 * created
	 * Database field type:	datetime
	 * Null status:		YES
	 *
	 * @var	string	$created
	 */
	var $created;

	/**
	 * last_edited
	 * Database field type:	datetime
	 * Null status:		YES
	 *
	 * @var	string	$last_edited
	 */
	var $last_edited;

	/**
	 * body
	 * Database field type:	text
	 * Null status:
	 *
	 * @var	string	$body
	 */
	var $body;

	/**
	 * last_read
	 * Database field type:	datetime
	 * Null status:		YES
	 *
	 * @var	string	$last_read
	 */
	var $last_read;

	/**
	 * times_read
	 * Database field type:	int(6)
	 * Null status:
	 *
	 * @var	integer	$times_read
	 */
	var $times_read;

	/**
	 * today_read
	 * Database field type:	int(6)
	 * Null status:
	 *
	 * @var	integer	$today_read
	 */
	var $today_read;

	/**
	 * status
	 * Database field type:	int(3)
	 * Null status:		YES
	 *
	 * @var	integer	$status
	 */
	var $status;


	/**
	 * sefriendly
	 * Database field type:	varchar(64)
	 * Null status:		YES
	 *
	 * @var	string	$sefriendly
	 */
	var $sefriendly;

	/**
	 * link
	 * Database field type:	varchar(255)
	 * Null status:
	 *
	 * @var	string	$link
	 */
	var $link;

	/**
	 * order_num
	 * Database field type:	int(6)
	 * Null status:		YES
	 *
	 * @var	integer	$order_num
	 */
	var $order_num;

	/**
	 * document
	 * Database field type:	varchar(50)
	 * Null status:		YES
	 *
	 * @var	string	$document
	 */
	var $document;

	/**
	 * show_poll
	 * Database field type:	enum('0','1')
	 * Null status:		YES
	 *
	 * @var	string	$show_poll
	 */
	var $show_poll;

	/**
	 * show_comment
	 * Database field type:	enum('0','1')
	 * Null status:		YES
	 *
	 * @var	string	$show_comment
	 */
	var $show_comment;

	/**
	 * rss_fee
	 * Database field type:	enum('0','1')
	 * Null status:		YES
	 *
	 * @var	string	$show_comment
	 */
	var $rss_feed;

	/**
	 * keywords
	 * Database field type:	varchar(100)
	 * Null status:		YES
	 *
	 * @var	string	$keywords
	 */
	var $keywords;

	/**
	 * description
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$description
	 */
	var $description;


	/**
	 * emailed
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$emailed
	 */
	var $emailed;

	/**
	 * image_caption
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	integer	$image_caption
	 */
	var $image_caption;

	/**
	 * vote_num
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$vote_num
	 */
	var $vote_num;

	/**
	 * vote_sum
	 * Database field type:	int(11)
	 * Null status:		YES
	 *
	 * @var	integer	$vote_sum
	 */
	var $vote_sum;

	/**
	 * abstract
	 * Database field type:	text
	 * Null status:		YES
	 *
	 * @var	string	$abstract
	 */
	var $abstract;


	var $author_obj;

	var $_sql_table = 'articles';

	/*
	 * Number of comments
	 */
	var $number_of_comments = false;

	/*
	 * Number of tags
	 */
	var $number_of_tags = false;

	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = $id;
	}

	/**
	 * Sets {@link $category_id}
	 *
	 * @param	integer	$category_id
	 */
	function set_category_id($category_id){
		if (( (int) ($category_id) <= 0) or ($category_id == '')){
			return false;
		}else{
			$this->category_id = (int) $category_id;
		}
		return true;
	}

	/**
	 * Sets {@link $user_id}
	 *
	 * @param	integer	$user_id
	 */
	function set_user_id($user_id){
		if (( (int) ($user_id) <= 0) or ($user_id == '')){
			return false;
		}else{
			$this->user_id = (int) $user_id;
			$this->author_obj = vivvo_lite_site::get_instance()->_user_manager->get_user_by_id($this->user_id);
			return true;
		}

	}

	/**
	 * Sets {@link $user_domain}
	 *
	 * @param	string	$user_domain
	 */
	function set_user_domain($user_domain){
		$this->user_domain = $user_domain;
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
	 * Sets {@link $title}
	 *
	 * @param	string	$title
	 */
	function set_title($title){
		if ($title == ''){
			return false;
		}else{
			$this->title = $title;
			return true;
		}
	}

	/**
	 * Sets {@link $image}
	 *
	 * @param	string	$image
	 */
	function set_image($image){
		if (file_exists(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $image)){
			$this->image = $image;
		}else{
			$this->image = '';
		}
		return true;
	}

	/**
	 * Sets {@link $created}
	 *
	 * @param	string	$created
	 */
	function set_created($created){
		if(preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $created)){
			$this->created = $created;
		}else{
			$this->created = date('Y-m-d H:i:s');
		}
		return true;
	}

	/**
	 * Sets {@link $last_edited}
	 *
	 * @param	string	$last_edited
	 */
	function set_last_edited($last_edited){
		if(preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $last_edited)){
			$this->last_edited = $last_edited;
		}else{
			$this->last_edited = date('Y-m-d H:i:s');
		}
		return true;
	}



	/**
	 * Sets {@link $body}
	 *
	 * @param	string	$body
	 */
	function set_body($body){
		$this->body = $body;
		return true;
	}

	/**
	 * Sets {@link $last_read}
	 *
	 * @param	string	$last_read
	 */
	function set_last_read($last_read){
		if(preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $last_read)){
			$this->last_read = $last_read;
		}else{
			$this->last_read = date('Y-m-d H:i:s');
		}
		return true;
	}

	/**
	 * Sets {@link $times_read}
	 *
	 * @param	integer	$times_read
	 */
	function set_times_read($times_read){
		if (( (int) ($times_read) < 0) or ($times_read == '')){
			$this->times_read = 0;
		}else{
			$this->times_read = (int) $times_read;
		}
		return true;
	}

	/**
	 * Sets {@link $today_read}
	 *
	 * @param	integer	$today_read
	 */
	function set_today_read($today_read){
		if (( (int) ($today_read) < 0) or ($today_read == '')){
			$this->today_read = 0;
		}else{
			$this->today_read = (int) $today_read;
		}
		return true;
	}

	/**
	 * Sets {@link $status}
	 *
	 * @param	integer	$status
	 */
	function set_status($status){
		if ($status == ''){
			$this->status = 0;
		}else{
			$this->status = (int) $status;
		}
		return true;

	}

	/**
	 * Sets {@link $sefriendly}
	 *
	 * @param	string	$sefriendly
	 */
	function set_sefriendly($sefriendly){
		$this->sefriendly = $sefriendly;
		return true;
	}

	/**
	 * Sets {@link $link}
	 *
	 * @param	string	$link
	 */
	function set_link($link){
		$this->link = $link;
		return true;
	}

	/**
	 * Sets {@link $order_num}
	 *
	 * @param	integer	$order_num
	 */
	function set_order_num($order_num){
		$this->order_num = $order_num;
		return true;
	}

	/**
	 * Sets {@link $document}
	 *
	 * @param	string	$document
	 */
	function set_document($document){
		$this->document = $document;
		return true;
	}

	/**
	 * Sets {@link $show_poll}
	 *
	 * @param	integer	$show_poll
	 */
	function set_show_poll($show_poll){
		if ($show_poll == '1'){
			$this->show_poll = '1';
		}else{
			$this->show_poll = '0';
		}
		return true;
	}

	/**
	 * Sets {@link $show_comment}
	 *
	 * @param	integer	$show_comment
	 */
	function set_show_comment($show_comment){
		if ($show_comment == '1'){
			$this->show_comment = '1';
		}else{
			$this->show_comment = '0';
		}
		return true;
	}

	/**
	 * Sets {@link $rss_feed}
	 *
	 * @param	integer	$rss_feed
	 */
	function set_rss_feed($rss_feed){
		if ($rss_feed == '1'){
			$this->rss_feed = '1';
		}else{
			$this->rss_feed = '0';
		}
		return true;
	}

	/**
	 * Sets {@link $keywords}
	 *
	 * @param	string	$keywords
	 */
	function set_keywords($keywords){
		$this->keywords = $keywords;
		return true;
	}

	/**
	 * Sets {@link $description}
	 *
	 * @param	string	$description
	 */
	function set_description($description){
		$this->description = $description;
		return true;
	}

	/**
	 * Sets {@link $emailed}
	 *
	 * @param	integer	$emailed
	 */
	function set_emailed($emailed){
		if ($emailed == ''){
			$this->emailed = 0;
		}else{
			$this->emailed = $emailed;
		}
	}

	/**
	 * Sets {@link $image_caption}
	 *
	 * @param	string	$image_caption
	 */
	function set_image_caption($image_caption){
		$this->image_caption = $image_caption;
	}

	/**
	 * Sets {@link $vote_num}
	 *
	 * @param	integer	$vote_num
	 */
	function set_vote_num($vote_num){
		if ((((int) $vote_num) == 0 )or ($vote_num  == '')){
			$this->vote_num = 0;
		}else{
			$this->vote_num = (int) $vote_num;
		}
	}

	/**
	 * Sets {@link $vote_sum}
	 *
	 * @param	integer	$vote_sum
	 */
	function set_vote_sum($vote_sum){
		if ((((int) $vote_sum) == 0 ) or ($vote_sum  == '')){
			$this->vote_sum = 0;
		}else{
			$this->vote_sum = $vote_sum;
		}
	}

	/**
	 * Sets {@link $abstract}
	 *
	 * @param	string	$abstract
	 */
	function set_abstract($abstract){
		$this->abstract = $abstract;
		return true;
	}

	/**
	 * Sets {@link $number_of_comments}
	 *
	 * @param	integer	$num
	 */
	function set_number_of_comments($num = false){
		if ($num === false || !is_numeric($num)){
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Comments.class.php');
			$cl = new Comments_list();
			$params = array('search_article_id' => $this->id);
			defined('VIVVO_ADMIN_MODE') or $params['search_status'] = 1;
			$cl->search($params, '', '', 0, 0, false);
			$this->number_of_comments = $cl->get_total_count();
		}elseif (is_numeric($num)){
			$this->number_of_comments = $num;
		}else{
			$this->number_of_comments = false;
		}
		return true;
	}

	/**
	 * Sets {@link $number_of_tags}
	 *
	 * @param	integer	$num
	 */
	function set_number_of_tags($num = false){
		if ($num === false || !is_numeric($num)){
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesTags.class.php');
			$atl = new ArticlesTags_list();
			$atl->search(array('search_article_id' => $this->id), '', '', 0, 0, false);
			$atl->_query->add_group_by('at.article_id');
			$this->number_of_tags = $atl->get_total_count();
		}elseif (is_numeric($num)){
			$this->number_of_tags = $num;
		}else{
			$this->number_of_tags = false;
		}
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
	 * Gets $category_id
	 *
	 * @return	integer
	 */
	function get_category_id(){
		return $this->category_id;
	}

	function get_category() {
		$cat = vivvo_lite_site::get_instance()->get_categories();
		if ($cat->list[$this->category_id]){
			return $cat->list[$this->category_id];
		}
		return false;
	}

	/**
	 * Gets $user_id
	 *
	 * @return	integer
	 */
	function get_user_id(){
		return $this->user_id;
	}

	/**
	 * Gets $user_domain
	 *
	 * @return	string
	 */
	function get_user_domain(){
		return $this->user_domain;
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
	 * Gets $title
	 *
	 * @return	string
	 */
	function get_title(){
		return $this->title;
	}
	/**
	 * Gets $image
	 *
	 * @return	string
	 */
	function get_image(){
		return $this->image;
	}

	/**
	 * Gets $created
	 *
	 * @return	string
	 */
	function get_created($format = false){
		return format_date($this->created, $format);
	}

	/**
	 * Gets $last_edited
	 *
	 * @return	string
	 */
	function get_last_edited($format = false){
		return format_date($this->last_edited, $format);
	}


	/**
	 * Gets $body
	 *
	 * @return	string
	 */
	function get_body($word_number = null, $elipsis = '') {

		if ($word_number !== null) {

			class_exists('XHTMLChop') or require VIVVO_FS_INSTALL_ROOT . 'lib/xhtml_chop/xhtml_chop.php';

			return XHTMLChop::chop($this->body, $word_number, XHTML_CHOP_WORDS | XHTML_CHOP_UTF8, $elipsis);
		}

		return $this->body;
	}
	/**
	 * Gets $last_read
	 *
	 * @return	string
	 */
	function get_last_read(){
		return $this->last_read;
	}
	/**
	 * Gets $times_read
	 *
	 * @return	integer
	 */
	function get_times_read(){
		return $this->times_read;
	}
	/**
	 * Gets $today_read
	 *
	 * @return	integer
	 */
	function get_today_read(){
		return $this->today_read;
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
	 * Gets $sefriendly
	 *
	 * @return	string
	 */
	function get_sefriendly(){
		return $this->sefriendly;
	}

	/**
	 * Gets $link
	 *
	 * @return	string
	 */
	function get_link(){
		return $this->link;
	}

	/**
	 * Gets $order_num
	 *
	 * @return	integer
	 */
	function get_order_num(){
		return $this->order_num;
	}

	/**
	 * Gets $document
	 *
	 * @deprecated
	 * @return	string
	 */
	function get_document(){
		return '';
	}

	/**
	 * @deprecated
	 */
	function get_document_href(){
		return '';
	}

	/**
	 * Gets $show_poll
	 *
	 * @return	integer
	 */
	function get_show_poll(){
		return $this->show_poll;
	}

	/**
	 * Gets $show_comment
	 *
	 * @return	integer
	 */
	function get_show_comment(){
		return $this->show_comment;
	}

	/**
	 * Gets $show_comment
	 *
	 * @return	integer
	 */
	function get_rss_feed(){
		return $this->rss_feed;
	}

	/**
	 * Gets $keywords
	 *
	 * @return	string
	 */
	function get_keywords(){
		if ($this->keywords){
			return $this->keywords;
		}else{
			$title = explode(' ', $this->title);
			$title = implode(',', $title);
			return $this->get_category_name() . ',' . $this->title . ',' . $title;
		}
	}
	/**
	 * Gets $description
	 *
	 * @return	string
	 */
	function get_description(){
		if ($this->description){
			return $this->description;
		}else{
			return $this->title;
		}
	}

	/**
	 * Gets $emailed
	 *
	 * @return	integer
	 */
	function get_emailed(){
		return $this->emailed;
	}

	/**
	 * Gets $image_caption
	 *
	 * @return	string
	 */
	function get_image_caption(){
		return $this->image_caption;
	}

	/**
	 * Gets $vote_num
	 *
	 * @return	integer
	 */
	function get_vote_num(){
		return $this->vote_num;
	}

	/**
	 * Gets $vote_sum
	 *
	 * @return	integer
	 */
	function get_vote_sum(){
		return $this->vote_sum;
	}

	/**
	 * Gets vote average
	 *
	 * @return	integer
	 */
	function get_vote_average($dec = 0){
		if (intval($this->vote_num) > 0){
			$dec = intval($dec);
			return number_format(intval($this->vote_sum) / intval($this->vote_num), $dec);
		}
		return 0;
	}

	/**
	 * Gets is voted
	 *
	 * @return	mixed integer on success or false on faild
	 */
	function is_voted (){
		$sm = vivvo_lite_site::get_instance();
		if ($sm->user && !$sm->user->can('ARTICLE_VOTE')){
			return true;
		}elseif (!$sm->user && $sm->guest_group && !$sm->guest_group->can('ARTICLE_VOTE')){
			return true;
		}
		if (isset ($_SESSION['vivvo']) && isset($_SESSION['vivvo']['article_poll']) && $_SESSION['vivvo']['article_poll'][$this->id]){
			return $_SESSION['vivvo']['article_poll'][$this->id];
		}else{
			return false;
		}
	}

	/**
	 * Gets $abstract
	 *
	 * @return	string
	 */
	function get_abstract(){
		return $this->abstract;
	}


	/**
	 * Gets author name
	 *
	 * @return	string
	 */
	function get_author_name(){
		if ($this->author != ''){
			return $this->author;
		}elseif (is_object($this->author_obj)){
			return $this->author_obj->get_name();
		}else{
			return '';
		}
	}

	/**
	 * Gets author href
	 *
	 * @return	string
	 */
	function get_author_href(){
		if (is_object($this->author_obj)){
			return $this->author_obj->get_href();
		}else{
			return '';
		}
	}

	function get_absolute_href() {
		return make_absolute_url($this->get_href(), false);
	}

	/**
	 * Gets article href
	 *
	 * @return	string
	 */
	function get_href(){
		$sm = vivvo_lite_site::get_instance();
		if (VIVVO_URL_FORMAT != 0 && $sm->is_registered_url('article' . VIVVO_URL_FORMAT) && !function_exists('format_article_url' . VIVVO_URL_FORMAT)){
			$sm->load_url_handler('article' . VIVVO_URL_FORMAT);
		}
		if (VIVVO_URL_FORMAT != 0 && $sm->is_registered_url('article' . VIVVO_URL_FORMAT) && function_exists('format_article_url' . VIVVO_URL_FORMAT)){
			return call_user_func('format_article_url' . VIVVO_URL_FORMAT, $this);
		}else{
			return $this->format_href($sm, $this->id, $this->category_id, '');
		}
	}

	function format_href($sm, $id, $category_id, $sefriendly) {

		if ($sefriendly != '') {
			$href = $sefriendly;
		} else {
			$href = $id;
		}

		$cat = $sm->get_categories();

		if ($cat->list[$category_id]) {
			$breadcrumbs = $cat->list[$category_id]->get_breadcrumb_href();
		}

		if ($breadcrumbs) {
			return make_proxied_url($breadcrumbs . $href . '.html');
		}

		return 'index.php?news=' . $id;
	}

	/**
	 * Gets article print href
	 *
	 * @return	string
	 */
	function get_print_href(){
		return $this->get_href() . '?print';
	}

	/**
	 * Gets article  breadcrumb
	 *
	 * @return	string
	 */
	function get_breadcrumb() {
		$cat = vivvo_lite_site::get_instance()->get_categories();
		if ($cat->list[$this->category_id]){
			$breadcrumbs = $cat->list[$this->category_id]->get_breadcrumb();
			if ($breadcrumbs) {
				return $breadcrumbs;
			}
		}
	}

	/**
	 * Gets article category name
	 *
	 * @return	string
	 */
	function get_category_name() {
		$cat = vivvo_lite_site::get_instance()->get_categories();
		if ($cat->list[$this->category_id]) {
			return $cat->list[$this->category_id]->category_name;
		}
	}

	/**
	 * Gets article category href
	 *
	 * @return	string
	 */
	function get_category_href() {
		$cat = vivvo_lite_site::get_instance()->get_categories();
		if ($cat->list[$this->category_id]){
			return $cat->list[$this->category_id]->get_href();
		}
	}

	/**
	 * Gets article summary
	 *
	 * @return	string
	 */
	function get_summary($word_number = null) {

		if (!empty($this->abstract)) {
			if ($word_number != null) {
				$text = $this->abstract;
			} else {
				return $this->abstract;
			}
		} else {
			$text = strip_tags($this->body);
		}

		if ($word_number < 1) {
			$word_number = 25;
		}

		return implode(' ', array_slice(explode(' ', trim($text)), 0, $word_number));
	}

	/**
	 * Gets number of comments for this article
	 *
	 * @return	integer
	 */
	function get_number_of_comments(){
		if ($this->number_of_comments === false) {
			$this->set_number_of_comments();
		}
		return $this->number_of_comments;
	}

	/**
	 * Gets number of tags for this article
	 *
	 * @return	integer
	 */
	function get_number_of_tags() {
		if ($this->number_of_tags === false) {
			$this->set_number_of_tags();
		}
		return $this->number_of_tags;
	}


	/**
	 * @var	array
	 */
	private $applied_tags = false;

	/**
	 * Returns array of applied topic/tag pairs
	 *
	 * @return	array
	 */
	public function get_applied_tags() {

		if ($this->applied_tags === false) {

			$res = vivvo_lite_site::get_instance()->get_db()->query(
				"SELECT at.tag_id, at.tags_group_id AS topic_id,
				 t.name AS tag_name, tg.name AS topic_name
				 FROM " . VIVVO_DB_PREFIX . 'articles_tags AS at
				 INNER JOIN ' . VIVVO_DB_PREFIX . 'tags AS t ON at.tag_id = t.id
				 INNER JOIN ' . VIVVO_DB_PREFIX . 'tags_groups AS tg ON at.tags_group_id = tg.id
				 WHERE at.article_id = ' . $this->id
			);

			if (!PEAR::isError($res)) {

				$this->applied_tags = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
				$res->free();

				foreach ($this->applied_tags as &$link) {
					$link['topic_id'] = intval($link['topic_id']);
					$link['tag_id'] = intval($link['tag_id']);
                    $link['tag_name'] =html_entity_decode($link['tag_name'],ENT_QUOTES,'UTF-8');
				}
				unset($link);
			}
		}

		return $this->applied_tags;
	}

	public $tags;

	/**
	 * Sets list of applied tags (from form)
	 *
	 * @param	mixed	$tags
	 */
	public function set_tags($article_tags) {

		is_array($article_tags) or $article_tags = explode(',', $article_tags);

		$topics = array();
		$tags = array();

		class_exists('tag_service') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/service/Tags.service.php';

		$tag_service = new tag_service();

		foreach ($article_tags as &$pair) {

			if (!preg_match('/^\d+:\d+$/', $pair)) {

				$name = trim(preg_replace('/(^\[|\]$)/', '', $pair));

				if (!$name) {
					$pair = false;
					continue;
				}

				$topic_id = 0;
				$tag_id = $tag_service->add_tag($name, make_sefriendly($name));

			} else {

				list($topic_id, $tag_id) = explode(':', $pair, 2);
			}

			if (!$tag_id) {
				$pair = false;
				continue;
			}

			$topics[$topic_id] = 1;
			$tags[$tag_id] = 1;

			$pair = array('tag_id' => $tag_id, 'topic_id' => $topic_id);
		}
		unset($pair);

		$topic_ids = secure_sql_in(array_keys($topics));
		$tag_ids = secure_sql_in(array_keys($tags));

		$db = vivvo_lite_site::get_instance()->get_db();

		$res = $db->query(
			'SELECT id, name FROM ' . VIVVO_DB_PREFIX . "tags WHERE id IN ($tag_ids)"
		);

		if (PEAR::isError($res)) {
			return;
		}

		$tags = $res->fetchAll(MDB2_FETCHMODE_ASSOC, true);

		$res = $db->query(
			'SELECT id, name FROM ' . VIVVO_DB_PREFIX . "tags_groups WHERE id IN ($topic_ids)"
		);

		if (PEAR::isError($res)) {
			return;
		}

		$topics = $res->fetchAll(MDB2_FETCHMODE_ASSOC, true);

		$this->applied_tags = array();

		foreach ($article_tags as $pair) {
			if ($pair and isset($tags[$pair['tag_id']]) and isset($topics[$pair['topic_id']])) {
				$this->applied_tags[] = array_merge($pair, array(
					'tag_name' => $tags[$pair['tag_id']],
					'topic_name' => $topics[$pair['topic_id']]
				));
			}
		}
	}


	/**
	 * Generate captcha code
	 *
	 * @return	string
	 */
	function generate_captcha(){
		if (VIVVO_COMMENTS_CAPTHA != 0) {
			$enc = rand(1000,100000) . $this->id;
		    $enc = substr(md5('icemelondawg' . $enc), 2,8);
		    if (!isset($_SESSION['vivvo'])) $_SESSION['vivvo'] = array();
		    if (!isset($_SESSION['vivvo']['comment_captcha'])) $_SESSION['vivvo']['comment_captcha'] = array();
		    $_SESSION['vivvo']['comment_captcha'][$this->id] = $enc;
		}
		return '';
	}

	/**
	 * Returns array of schedules for this article
	 *
	 * @return	array
	 */
	public function get_schedules() {

		static $schedules;

		if ( !isset($schedules) ) {

			require_once(VIVVO_FS_ROOT . 'lib/vivvo/core/ArticlesSchedule.class.php');

			$schedule_list = new ArticlesSchedule_list( $this->get_site_manager() );

			if ( ($schedules = $schedule_list->get_schedules_by_article_id($this->id) ) === false ) {
				$schedules = array();
			}
		}

		return $schedules;
	}

	/**
	 * @var array	List of applied tags
	 */
	private $tag_links = false;

	/**
	 * @var array	List of topics with applied tags
	 */
	private $topics = false;

	/**
	 * Returns list of all tags applied to this article
	 *
	 * @return	array
	 */
	public function get_tag_links() {

		if ($this->tag_links === false) {

			$this->tag_links = array();

			class_exists('TagsGroups') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php';
			class_exists('Tags') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php';

			$this->topics = TagsGroups_list::factory()->get_groups_by_article_id($this->id, 1);
			$tag_list = Tags_list::factory();

			foreach ($this->topics as $topic) {
				$topic->set_article_tags($tags = $tag_list->get_tags_by_article_topic($this, $topic, true));
				$this->tag_links = array_merge($this->tag_links, $tags);
			}
		}

		return $this->tag_links;
	}

	/**
	 * Returns list of topic objects
	 *
	 * @return	array
	 */
	public function get_topics() {

		if ($this->tag_links === false) {
			$this->get_tag_links();
		}

		return $this->topics;
	}

	/**
	 * Update $last_read and $times_read on display article
	 */
	function on_display() {

		$t = getdate();

		$today = "$t[year]-$t[mon]-$t[mday]";
		$last_read = "$t[year]-$t[mon]-$t[mday] $t[hours]:$t[minutes]:$t[seconds]";

		if (strtotime($this->last_read) < strtotime($today)) {
			$this->today_read = 1;
		} else {
			$this->today_read++;
		}

        $this->times_read++;

		$sm = vivvo_lite_site::get_instance();
        $db = $sm->get_db();

		$sql = "INSERT INTO ". VIVVO_DB_PREFIX ."articles_stats
				(`article_id`, `last_read`, `times_read`, `today_read`, `updated`, `created`)
				VALUES ($this->id, '$last_read', ".
					intval($this->times_read) .", ".
					intval($this->today_read) .", 1, '$this->created')
				ON DUPLICATE KEY UPDATE
					`today_read`=IF(`last_read`<'$today', 1, `today_read`+1),
					`last_read`='$last_read',
					`times_read`=`times_read`+1,
					`updated`=1,
					`created`='$this->created'";
		$db->exec($sql);

		$sm->debug_push("sql:", $sql);
	}

	/**
	 * Cascade delete
	 */
	function on_delete($post_master) {

		$post_master->sql_delete_list('articles_tags', "article_id=$this->id");
		$post_master->sql_delete_list('articles_schedule', "article_id=$this->id");
		$post_master->sql_delete_list('articles_revisions', "article_id=$this->id");
		$post_master->sql_delete_list('articles_stats', "article_id=$this->id");
		$post_master->sql_delete_list('comments', "article_id=$this->id");
		$post_master->sql_delete_list('article_attachments', "article_id=$this->id");
		$post_master->sql_delete_list('article_images', "article_id=$this->id");

		admin_log(vivvo_lite_site::get_instance()->user->get_username(), 'Deleted article #' . $this->id);
	}

	function __destruct () {
		parent::__destruct();
	}
}

/**
 * Articles list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class Articles_list extends vivvo_db_paged_list {
	var $_sql_table = 'articles';
	var $post_object_type = 'Articles';

	function _default_query(){
		$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table .' AS a');
		if (is_array($this->_fields) && !empty($this->_fields)){
			foreach ($this->_fields as $field){
				$this->_query->add_fields('a.' . $field);
			}
		}else{
			$this->_query->add_fields('a.*');
		}
	}

	public static function factory() {
		return new self();
	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){

			case 'a.id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("a.id IN ($condition)");
			break;
			case '!a.id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("a.id NOT IN ($condition)");
			break;
			case 'a.category_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("a.category_id IN ($condition)");
			break;
			case '!a.category_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("a.category_id NOT IN ($condition)");
			break;
			case 'a.user_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("a.user_id IN ($condition)");
				$this->_query->add_where("a.user_domain = '" . VIVVO_USER_SOURCE . "'");
			break;
			case 'a.user_domain':
				$this->_query->add_where("a.user_domain = '$condition'");
			break;
			case 'a.author_exact_name':
				$this->_query->add_where("a.author = '$condition'");
			break;
			case 'a.author':
				$condition = escape_sql_like($condition);
				$this->_query->add_where("a.author LIKE '%$condition%'");
			break;
			case 'a.title':
				$condition = escape_sql_like($condition);
				$this->_query->add_where("a.title LIKE '%$condition%'");
			break;
			case 'a.image':
				$this->_query->add_where("a.image = '$condition'");
			break;
			case 'a.created_month':
				$this->_query->add_where("MONTH(a.created) = '$condition'");
			break;
			case 'a.created_year':
				$this->_query->add_where("YEAR(a.created) = '$condition'");
			break;
			case 'a.created_day':
				$this->_query->add_where("DAY(a.created) = '$condition'");
			break;
			case 'a.created_before':
				$current_time = date('Y-m-d H:i:00', VIVVO_START_TIME);
				$this->_query->add_where("a.created < (DATE_SUB('$current_time', INTERVAL $condition  DAY))");
			break;
			case 'a.created_after':
				$current_time = date('Y-m-d H:i:00', VIVVO_START_TIME);
				$this->_query->add_where("a.created > (DATE_SUB('$current_time', INTERVAL $condition DAY))");
			break;
			case 'a.created_filter':
				$current_time = date('Y-m-d H:i:00', VIVVO_START_TIME);
				$this->_query->add_where("a.created < '$current_time'");
			break;
			case 'a.body':
				$this->_query->add_where("MATCH (title,body,abstract) AGAINST ('$condition' IN BOOLEAN MODE)");
			break;
			case 'a.last_read':
				$this->_query->add_where("a.last_read = '$condition'");
			break;
			case 'a.times_read':
				$this->_query->add_where("a.times_read = '$condition'");
			break;
			case 'a.today_read':
				$this->_query->add_where("a.today_read = '$condition'");
			break;
			case 'a.status':
				$this->_query->add_where("a.status = '$condition'");
			break;
			case 'a.not_status':
				$this->_query->add_where("a.status != '$condition'");
			break;
			case 'a.status_limit':
				$this->_query->add_where('a.status > 0');
			break;
			case 'a.sefriendly':
				$this->_query->add_where("a.sefriendly = '$condition'");
			break;
			case 'a.link':
				$this->_query->add_where("a.link = '$condition'");
			break;
			case 'a.order_num':
				$this->_query->add_where("a.order_num = '$condition'");
			break;
			case 'a.show_poll':
				$this->_query->add_where("a.show_poll = '$condition'");
			break;
			case 'a.rss_feed':
				$this->_query->add_where("a.rss_feed = '$condition'");
			break;
			case 'a.show_comment':
				$this->_query->add_where("a.show_comment = '$condition'");
			break;
			case 'a.keywords':
				$this->_query->add_where("a.keywords = '$condition'");
			break;
			case 'a.description':
				$this->_query->add_where("a.description = '$condition'");
			break;
			case 'a.emailed':
				$this->_query->add_where("a.emailed = '$condition'");
			break;
			case 'a.vote_num':
				$this->_query->add_where("a.vote_num = '$condition'");
			break;
			case 'a.vote_sum':
				$this->_query->add_where("a.vote_sum = '$condition'");
			break;
			case 'a.abstract':
				$this->_query->add_where("a.abstract = '$condition'");
			break;

			case 'related':
	            $this->_query->add_join(' INNER JOIN ' . VIVVO_DB_PREFIX . 'related AS r ON r.related_article_id = a.id ', 'r');
	            $this->_query->add_order('r.relevance DESC');
	            $this->_query->add_where('r.article_id = ' . (int)$condition);
	        break;

			case 'tag':
				$condition = secure_sql_in($condition, false);
				$this->_query->add_join(' INNER JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ','at');
				$this->_query->add_join(' INNER JOIN ' . VIVVO_DB_PREFIX . 'tags as t ON t.id = at.tag_id ','t');
				$this->_query->add_where("t.name IN ($condition)");
				$this->_query->add_group_by('a.id');
			break;

			case 'tag_matches':
				$condition = escape_sql_like($condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ','at');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'tags as t ON t.id = at.tag_id ','t');
				$this->_query->add_where("t.name LIKE '%$condition%'");
				$this->_query->add_group_by('a.id');
			break;

			case 'tag_id':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ', 'at');
				$condition = secure_sql_in($condition);
				$this->_query->add_where("at.tag_id IN ($condition)");
				$this->_query->add_group_by('a.id');
			break;

			case 'all_tag_ids':
				is_array($condition) or $condition = explode(',', $condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ', 'at');
				$value = (int)array_shift($condition);
				$this->_query->add_where("at.tag_id = $value");
				$this->_query->add_group_by('a.id');
				$tag_ids = array();
				foreach($condition as $value) {
					$tag_ids[] = (int)$value;
				}
				if (!empty($tag_ids)) {
					$tag_ids = implode(',', $tag_ids);
					$this->_query->add_where('a.id IN (SELECT article_id FROM ' . VIVVO_DB_PREFIX . "articles_tags WHERE tag_id IN ($tag_ids))");
				}
			break;

			case 'tags_group_id':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ', 'at');
				$condition = secure_sql_in($condition);
				$this->_query->add_where("at.tags_group_id IN ($condition)");
				$this->_query->add_group_by('a.id');
			break;

			case 'user_group_id':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ', 'at');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'group_user AS gu ON at.user_id = gu.user_id ', 'gu');
				$condition = secure_sql_in($condition);
				$this->_query->add_where("gu.group_id IN ($condition)");
				$this->_query->add_group_by('a.id');
			break;

			case 'not_user_group_id':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ', 'at');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'group_user AS gu ON at.user_id = gu.user_id ', 'gu');
				$condition = secure_sql_in($condition);
				$this->_query->add_where("gu.group_id NOT IN ($condition)");
				$this->_query->add_group_by('a.id');
			break;

			case 'tags_group_name':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.article_id = a.id ', 'at');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'tags_groups as tg ON at.tags_group_id = tg.id ', 'tg');
				$condition = explode(',', $condition);
				foreach ($condition as &$topic) {
					$topic = "'" . secure_sql($topic) . "'";
				}
				unset($topic);
				$condition = implode(',', $condition);
				$this->_query->add_where("tg.name IN ($condition)");
				$this->_query->add_group_by('a.id');
			break;

			case 'sc.id':
			case 'sc.duration':
			case 'sc.status':
			case 'sc.year':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = (int)$condition;
				$this->_query->add_where("$type = $condition");
			break;

			case 'sc.minute':
			case 'sc.hour':
			case 'sc.dom':
			case 'sc.month':
			case 'sc.dow':
				require_once(VIVVO_FS_ROOT . 'lib/vivvo/core/ArticlesSchedule.class.php');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = ArticlesSchedule::getHexMask( $condition, substr($type, 3) );
				$this->_query->add_where("$type & $condition");
			break;

			case 'sc.date':
				if ( !is_array($condition) ) {
					$parts = explode( ',', date('i,G,j,n,w,Y', $condition) );
					$condition = array(
						'minute' => (int)($parts[0]),
						'hour' => $parts[1],
						'dom' => $parts[2],
						'month' => $parts[3],
						'dow' => $parts[4] + 1,
						'year' => $parts[5]
					);
				}
				require_once(VIVVO_FS_ROOT . 'lib/vivvo/core/ArticlesSchedule.class.php');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = ArticlesSchedule::getHexMask( $condition );
				foreach ($condition as $name => $value) {
					if ($name == 'year') {
						$value = (int)$value;
						$this->_query->add_where("sc.year = $value");
					} else {
						$this->_query->add_where("sc.$name & $value");
					}
				}

			break;

			case 'sc.id_in':
			case 'sc.duration_in':
			case 'sc.year_in':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = secure_sql_in($condition);
				$type = substr($type, 0, -3);
				$this->_query->add_where("$type IN ($condition)");
			break;

			case 'sc.id_not_in':
			case 'sc.duration_not_in':
			case 'sc.year_not_in':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = secure_sql_in($condition);
				$type = substr($type, 0, -7);
				$this->_query->add_where("$type NOT IN ($condition)");
			break;

			case 'sc.duration_lt':
			case 'sc.year_lt':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = (int)$condition;
				$type = substr($type, 0, -3);
				$this->_query->add_where("$type < $condition");
			break;

			case 'sc.duration_lte':
			case 'sc.year_lte':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = (int)$condition;
				$type = substr($type, 0, -4);
				$this->_query->add_where("$type <= $condition");
			break;

			case 'sc.duration_gt':
			case 'sc.year_lg':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = (int)$condition;
				$type = substr($type, 0, -3);
				$this->_query->add_where("$type > $condition");
			break;

			case 'sc.duration_gte':
			case 'sc.year_gte':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$condition = (int)$condition;
				$type = substr($type, 0, -4);
				$this->_query->add_where("$type >= $condition");
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
	function search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
		//search_created_filter

		if (!empty($params['search_created_filter']) and intval($params['search_created_filter']) !== 0){
			$this->add_filter('a.created_filter', 1);
		}

		//search_query
		if (isset($params['search_id'])){
			if (is_array($params['search_id'])){
				if (!in_array(0, $params['search_id'])){
					$params['search_id'] = implode(',', $params['search_id']);
					$this->add_filter('a.id',$params['search_id']);
				}
			}else{
				if ($params['search_id'] != 0){
					$this->add_filter('a.id',$params['search_id']);
				}
			}
		}

		if (isset($params['search_exclude_id'])){
			if (is_array($params['search_exclude_id'])){
				if (!empty($params['search_exclude_id'])){
					$params['search_exclude_id'] = implode(',', $params['search_exclude_id']);
					$this->add_filter('!a.id',$params['search_exclude_id']);
				}
			}else{
				if ($params['search_exclude_id'] != 0){
					$this->add_filter('!a.id',$params['search_exclude_id']);
				}
			}
		}

		if (isset($params['search_user_id'])){
			if (is_array($params['search_user_id'])){
				if (!in_array(0, $params['search_user_id'])){
					$params['search_user_id'] = implode(',', $params['search_user_id']);
					$this->add_filter('a.user_id',$params['search_user_id']);
				}
			}else{
				if ($params['search_user_id'] != 0){
					$this->add_filter('a.user_id',$params['search_user_id']);
				}
			}
		}

		if (isset($params['search_cid'])){
			if (is_array($params['search_cid'])){
				if (!in_array(0, $params['search_cid'])){
					$params['search_cid'] = implode(',', $params['search_cid']);
					$this->add_filter('a.category_id',$params['search_cid']);
				}
			}else{
				if ($params['search_cid'] != 0){
					$this->add_filter('a.category_id',$params['search_cid']);
				}
			}
		}

		if (isset($params['search_exclude_cid'])) {
			$this->add_filter('!a.category_id', $params['search_exclude_cid']);
		}

		if (isset($params['search_author'])){
			if (isset($params['search_author_exact_name']) && $params['search_author_exact_name']){
					$this->add_filter('a.author_exact_name',$params['search_author']);
			}else{
					$this->add_filter('a.author',$params['search_author']);

			}
		}

		if (isset($params['search_query'])){
			if ($params['search_title_only']){
				$this->add_filter('a.title', $params['search_query']);
			}else{
				$this->add_filter('a.body', $params['search_query']);
				$this->_query->add_fields("(MATCH (body,title,abstract) AGAINST ('" . secure_sql($params['search_query']) . "' IN BOOLEAN MODE)) as relevance");
			}
		}

		//search_search_date
		if (isset($params['search_search_date']) && intval($params['search_search_date']) !== 0){
			$this->add_filter((($params['search_before_after'] === '1') ? 'a.created_after' : 'a.created_before'), $params['search_search_date']);
		}

		if (isset($params['search_by_date']) && intval($params['search_by_date']) !== 0){
			$date = strtotime($params['search_date']);
			$this->add_filter('a.created_year', date('Y', $date));
			$this->add_filter('a.created_month', date('m', $date));
			$this->add_filter('a.created_day', date('d', $date));
		}

		if (isset($params['search_by_year']) && intval($params['search_by_year']) !== 0){
			$this->add_filter('a.created_year',$params['search_by_year']);
		}

		if (isset($params['search_by_month']) && intval($params['search_by_month']) !== 0){
			$this->add_filter('a.created_month', $params['search_by_month']);
		}

		if (isset($params['search_by_day']) && intval($params['search_by_day']) !== 0){
			$this->add_filter('a.created_day', $params['search_by_day']);
		}

		if (isset($params['search_image'])){
			$this->add_filter('a.image',$params['search_image']);
		}

		if (isset($params['search_body'])){
			$this->add_filter('a.body',$params['search_body']);
		}

		if (isset($params['search_last_read'])){
			$this->add_filter('a.last_read',$params['search_last_read']);
		}

		if (isset($params['search_times_read'])){
			$this->add_filter('a.times_read',$params['search_times_read']);
		}

		if (isset($params['search_today_read'])){
			$this->add_filter('a.today_read',$params['search_today_read']);
		}

		if (isset($params['search_status']) && $params['search_status'] !== ''){
			$this->add_filter('a.status', $params['search_status']);
		} elseif (!isset($params['search_id']) and !isset($params['search_status_limit'])) {
			$this->add_filter('a.not_status', -2); // exclude soft-deleted
		}

		if (isset($params['search_status_limit']) && $params['search_status_limit'] == 1){
			$this->add_filter('a.status_limit',$params['search_status_limit']);
		}

		if (isset($params['search_sefriendly'])){
			$this->add_filter('a.sefriendly',$params['search_sefriendly']);
		}
		if (isset($params['search_type'])){
			$this->add_filter('a.type',$params['search_type']);
		}
		if (isset($params['search_order_num'])){
			$this->add_filter('a.order_num',$params['search_order_num']);
		}
		if (isset($params['search_show_poll'])){
			$this->add_filter('a.show_poll',$params['search_show_poll']);
		}
		if (isset($params['search_show_comment'])){
			$this->add_filter('a.show_comment',$params['search_show_comment']);
		}
		if (isset($params['search_rss_feed'])){
			$this->add_filter('a.rss_feed',$params['search_rss_feed']);
		}
		if (isset($params['search_keywords'])){
			$this->add_filter('a.keywords',$params['search_keywords']);
		}
		if (isset($params['search_description'])){
			$this->add_filter('a.description',$params['search_description']);
		}
		if (isset($params['search_emailed'])){
			$this->add_filter('a.emailed',$params['search_emailed']);
		}
		if (isset($params['search_vote_num'])){
			$this->add_filter('a.vote_num',$params['search_vote_num']);
		}
		if (isset($params['search_vote_sum'])){
			$this->add_filter('a.vote_sum',$params['search_vote_sum']);
		}

		if (isset($params['search_abstract'])){
			$this->add_filter('a.abstract',$params['search_abstract']);
		}

		if (isset($params['search_tag'])){
			$this->add_filter('tag',$params['search_tag']);
		}

		if (isset($params['search_tag_name']) and defined('VIVVO_ADMIN_MODE')) {
			$this->add_filter('tag_matches', $params['search_tag_name']);
		}

		if (isset($params['search_tag_id'])){
			$this->add_filter('tag_id',$params['search_tag_id']);
		}

		if (isset($params['search_all_tag_ids'])) {
			$this->add_filter('all_tag_ids', $params['search_all_tag_ids']);
		}

		if (isset($params['search_tags_group_id'])){
			$this->add_filter('tags_group_id',$params['search_tags_group_id']);
		}

		if (isset($params['search_topic_id'])){
			$this->add_filter('tags_group_id',$params['search_topic_id']);
		}

		if (isset($params['search_related'])){
			$this->add_filter('related',$params['search_related']);
		}

		if (isset($params['search_topic'])){
			$this->add_filter('tags_group_name',$params['search_topic']);
		}

		if (isset($params['search_user_group_id'])){
			$this->add_filter('user_group_id',$params['search_user_group_id']);
		}
		if (isset($params['search_not_user_group_id'])){
			$this->add_filter('not_user_group_id',$params['search_not_user_group_id']);
		}

		if (defined('VIVVO_FORCE_CATEGORY_RESTRICTION') && VIVVO_FORCE_CATEGORY_RESTRICTION != ''){
			$this->add_filter('a.category_id', VIVVO_FORCE_CATEGORY_RESTRICTION);
		}

		if (isset($params['search_schedule_id'])){
			$this->add_filter('sc.id', $params['search_schedule_id']);
		}
		if (isset($params['search_schedule_duration'])){
			$this->add_filter('sc.duration', $params['search_schedule_duration']);
		}
		if (isset($params['search_schedule_year'])){
			$this->add_filter('sc.year', $params['search_schedule_year']);
		}
		if (isset($params['search_schedule_id_in'])){
			$this->add_filter('sc.id_in', $params['search_schedule_id_in']);
		}
		if (isset($params['search_schedule_duration_in'])){
			$this->add_filter('sc.duration_in', $params['search_schedule_duration_in']);
		}
		if (isset($params['search_schedule_year_in'])){
			$this->add_filter('sc.year_in', $params['search_schedule_year_in']);
		}
		if (isset($params['search_schedule_id_not_in'])){
			$this->add_filter('sc.id_not_in', $params['search_schedule_id_not_in']);
		}
		if (isset($params['search_schedule_duration_not_in'])){
			$this->add_filter('sc.duration_not_in', $params['search_schedule_duration_not_in']);
		}
		if (isset($params['search_schedule_year_not_in'])){
			$this->add_filter('sc.year_not_in', $params['search_schedule_year_not_in']);
		}
		if (isset($params['search_schedule_duration_lt'])){
			$this->add_filter('sc.duration_lt', $params['search_schedule_duration_lt']);
		}
		if (isset($params['search_schedule_year_lt'])){
			$this->add_filter('sc.year_lt', $params['search_schedule_year_lt']);
		}
		if (isset($params['search_schedule_duration_lte'])){
			$this->add_filter('sc.duration_lte', $params['search_schedule_duration_lte']);
		}
		if (isset($params['search_schedule_year_lte'])){
			$this->add_filter('sc.year_lte', $params['search_schedule_year_lte']);
		}
		if (isset($params['search_schedule_duration_gt'])){
			$this->add_filter('sc.duration_gt', $params['search_schedule_duration_gt']);
		}
		if (isset($params['search_schedule_year_gt'])){
			$this->add_filter('sc.year_gt', $params['search_schedule_year_gt']);
		}
		if (isset($params['search_schedule_duration_gte'])){
			$this->add_filter('sc.duration_gte', $params['search_schedule_duration_gte']);
		}
		if (isset($params['search_schedule_year_gte'])){
			$this->add_filter('sc.year_gte', $params['search_schedule_year_gte']);
		}
		if (isset($params['search_schedule_minute'])){
			$this->add_filter('sc.minute', $params['search_schedule_minute']);
		}
		if (isset($params['search_schedule_hour'])){
			$this->add_filter('sc.hour', $params['search_schedule_hour']);
		}
		if (isset($params['search_schedule_dom'])){
			$this->add_filter('sc.dom', $params['search_schedule_dom']);
		}
		if (isset($params['search_schedule_month'])){
			$this->add_filter('sc.month', $params['search_schedule_month']);
		}
		if (isset($params['search_schedule_dow'])){
			$this->add_filter('sc.dow', $params['search_schedule_dow']);
		}
		if (isset($params['search_schedule_date'])){
			$this->add_filter('sc.date', $params['search_schedule_date']);
		}

		if (defined('VIVVO_CUSTOM_FIELD_SEARCH') && VIVVO_CUSTOM_FIELD_SEARCH == 1){
			if (isset($params['generic_search']) && $params['generic_search'] !== false){
				$this->generic_add_filter($params['generic_search'], 'a.');
			}
		}


		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'most_popular':
				if (VIVVO_MODULES_MOST_POPULAR_COUNTER == 0){
					$this->_query->add_order('a.today_read' . $search_direction);
				}else{
					$this->_query->add_order('a.times_read' . $search_direction);
				}
				break;
			case 'most_commented':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'comments as c ON c.article_id = a.id ','c');
				$this->_query->add_fields(' COUNT(DISTINCT c.id) AS number_of_comments');
				defined('VIVVO_ADMIN_MODE') or $this->_query->add_where("c.status = '1'");
				$this->_query->add_order('number_of_comments' . $search_direction);
				$this->_query->add_group_by('a.id');
				break;
			case 'most_emailed':
				$this->_query->add_order('a.emailed' . $search_direction);
				break;

			case 'id':
				$this->_query->add_order('a.id' . $search_direction);
				break;
			case 'category_id':
				$this->_query->add_order('a.category_id' . $search_direction);
				break;
			case 'user_id':
				$this->_query->add_order('a.user_id' . $search_direction);
				break;
			case 'author':
				if (VIVVO_USE_COLLATE){
					$this->_query->add_order('a.author COLLATE ' . VIVVO_DB_COLLATION . ' ' . $search_direction);
				}else{
					$this->_query->add_order('a.author ' . $search_direction);
				}
				break;
			case 'title':
				if (VIVVO_USE_COLLATE){
					$this->_query->add_order('a.title COLLATE ' . VIVVO_DB_COLLATION . ' ' . $search_direction);
				}else{
					$this->_query->add_order('a.title ' . $search_direction);
				}
				break;
			case 'image':
				$this->_query->add_order('a.image' . $search_direction);
				break;
			case 'created':
				$this->_query->add_order('a.created' . $search_direction);
				break;
			case 'last_edited':
				$this->_query->add_order('a.last_edited' . $search_direction);
				break;
			case 'body':
				$this->_query->add_order('a.body' . $search_direction);
				break;
			case 'last_read':
				$this->_query->add_order('a.last_read' . $search_direction);
				break;
			case 'times_read':
				$this->_query->add_order('a.times_read' . $search_direction);
				break;
			case 'today_read':
				$this->_query->add_order('a.today_read' . $search_direction);
				break;
			case 'status':
				$this->_query->add_order('a.status' . $search_direction);
				break;
			case 'sefriendly':
				$this->_query->add_order('a.sefriendly' . $search_direction);
				break;
			case 'link':
				$this->_query->add_order('a.link' . $search_direction);
				break;
			case 'order_num':
				$this->_query->add_order('a.order_num' . $search_direction);
				break;
			case 'document':
				$this->_query->add_order('a.document' . $search_direction);
				break;
			case 'show_poll':
				$this->_query->add_order('a.show_poll' . $search_direction);
				break;
			case 'show_comment':
				$this->_query->add_order('a.show_comment' . $search_direction);
				break;
			case 'rss_feed':
				$this->_query->add_order('a.rss_feed' . $search_direction);
				break;
			case 'keywords':
				$this->_query->add_order('a.keywords' . $search_direction);
				break;
			case 'description':
				$this->_query->add_order('a.description' . $search_direction);
				break;
			case 'emailed':
				$this->_query->add_order('a.emailed' . $search_direction);
				break;
			case 'vote_num':
				$this->_query->add_order('a.vote_num' . $search_direction);
				break;
			case 'vote_sum':
				$this->_query->add_order('a.vote_sum' . $search_direction);
				break;
			case 'vote_avg':
				$this->_query->add_order('(a.vote_sum / a.vote_num) DESC');
			break;
			case 'random':
				$this->_query->add_order('rand( )' . $search_direction);
			break;
			case 'abstract':
				$this->_query->add_order('a.abstract' . $search_direction);
				break;
			case 'relevance':
				if (isset($params['search_query']) && $params['search_title_only'] == 1){
					$this->_query->add_order('relevance' . $search_direction);
				}else{
					$this->_query->add_order('a.order_num' . $search_direction);
				}
				break;
			case 'schedule_duration':
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_schedule AS sc ON sc.article_id = a.id ', 'sc');
				$this->_query->add_order("sc.duration $search_direction");
				break;
			break;
			default:
				if ($order != '' && defined('VIVVO_CUSTOM_FIELD_SORT') && VIVVO_CUSTOM_FIELD_SORT == 1){
					if (!$this->generic_sort('a.', $order, $search_direction)){
						$order = 'a.id';
						$this->_query->add_order('a.id' . ' DESC');
					}
				}else{
					$order = 'a.id';
					$this->_query->add_order('a.id' . ' DESC');
				}
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

	function get_article_by_id($article_id, $cat_id = 0){

		if ($article_id <= 0) return false;

		if ($cat_id){
			$this->search(array('search_id'=>$article_id, 'search_cid' => $cat_id));
		}else{
			$this->search(array('search_id'=>$article_id));
		}

		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}

	function get_articles_by_category_id($category_id){
		$category_id = (int) $category_id;
		$this->search(array('search_cid'=>$category_id));
		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

	function get_articles_by_tags_group_id($group_id) {
		$this->search(array('search_tags_group_id' => $group_id));
		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

	function get_articles_by_tag_id($tag_id) {
		$this->search(array('search_tag_id' => $tag_id));
		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

	function get_articles_by_ids($article_id){
		$val = trim(implode(',', $article_id));
		if (empty($val)){
			return false;
		}
		$this->search(array('search_id'=>$article_id));
		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

	function get_user_articles_by_ids($article_id,$user_id, $cat_restriction = 0){
		if (is_array($article_id)){
			$val = trim(implode(',', $article_id));
		}else{
			$val = (int) $article_id;
		}
		if (empty($val)){
			return false;
		}
		$this->search(array('search_id'=>$article_id, 'search_user_id'=>$user_id, 'search_cid' => $cat_restriction));
		if (empty($this->list)){
			return false;
		}else{
			return $this->list;
		}
	}

	function get_article_by_sefriendly($sefriendly, $cat_id){
		$cat_id = (int) $cat_id;
		$this->search(array('search_sefriendly' => $sefriendly, 'search_cid' => $cat_id));
		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}

	/**
	 * gets articles by schedule date
	 *
	 * @param	mixed	$time	int (unix timestamp) or array
	 * @return	mixed	array or false
	 */
   public function &get_articles_by_schedule_date($time) {

		if ( !is_array($time) ) {
			$parts = explode( ',', date('i,G,j,n,w,Y', $time) );
			$time = array(
				'minute' => (int)($parts[0]),
				'hour' => $parts[1],
				'dom' => $parts[2],
				'month' => $parts[3],
				'dow' => $parts[4] + 1,
				'year' => $parts[5]
			);
		}

		$this->_query->reset_query();
		$this->_default_query();

		foreach ($time as $name => $value) {
			$this->add_filter('sc.'.$name, $value);
		}

		$this->set_list();

		$result = false;

		if ( !empty($this->list) ) {
			$result =& $this->list;
		}

		return $result;
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

		if (defined('VIVVO_ADMIN_MODE')){
			if (isset($in_params['search_status']) && $in_params['search_status'] != '') $params['search_options']['search_status'] = $in_params['search_status'];
		}else{
			$params['search_options']['search_created_filter'] = 1;
			if (isset($in_params['search_status']) && $in_params['search_status'] != 0){
				$params['search_options']['search_status'] = $in_params['search_status'];
			}else{
				$params['search_options']['search_status_limit'] = 1;
			}
		}

		if (!empty($in_params['search_id'])) $params['search_options']['search_id'] = $in_params['search_id'];
		if (!empty($in_params['search_cid'])) $params['search_options']['search_cid'] = $in_params['search_cid'];
		if (!empty($in_params['search_exclude_cid'])) $params['search_options']['search_exclude_cid'] = $in_params['search_exclude_cid'];
		if (!empty($in_params['search_author'])) $params['search_options']['search_author'] = $in_params['search_author'];
		if (isset($in_params['search_author_exact_name']) && $in_params['search_author_exact_name'] == 1) $params['search_options']['search_author_exact_name'] = $in_params['search_author_exact_name'];
		if (!empty($in_params['search_query'])) $params['search_options']['search_query'] = $in_params['search_query'];
		if (isset($in_params['search_title_only']) && $in_params['search_title_only'] == 1) $params['search_options']['search_title_only'] = $in_params['search_title_only'];
		if (!empty($in_params['search_search_date'])) $params['search_options']['search_search_date'] = $in_params['search_search_date'];
		if (isset($in_params['search_before_after']) && $in_params['search_before_after'] == 1) $params['search_options']['search_before_after'] = $in_params['search_before_after'];
		if (!empty($in_params['search_by_date'])) $params['search_options']['search_by_date'] = $in_params['search_by_date'];
		if (!empty($in_params['search_by_year'])) $params['search_options']['search_by_year'] = $in_params['search_by_year'];
		if (!empty($in_params['search_by_month'])) $params['search_options']['search_by_month'] = $in_params['search_by_month'];
		if (!empty($in_params['search_by_day'])) $params['search_options']['search_by_day'] = $in_params['search_by_day'];
		if (!empty($in_params['search_tag'])) $params['search_options']['search_tag'] = $in_params['search_tag'];
		if (!empty($in_params['search_tag_name'])) $params['search_options']['search_tag_name'] = $in_params['search_tag_name'];
		if (!empty($in_params['search_tag_id'])) $params['search_options']['search_tag_id'] = $in_params['search_tag_id'];
		if (!empty($in_params['search_all_tag_ids'])) $params['search_options']['search_all_tag_ids'] = $in_params['search_all_tag_ids'];
		if (isset($in_params['search_tags_group_id'])) $params['search_options']['search_tags_group_id'] = $in_params['search_tags_group_id'];
		if (isset($in_params['search_topic_id'])) $params['search_options']['search_tags_group_id'] = $in_params['search_topic_id'];
		if (!empty($in_params['search_topic'])) $params['search_options']['search_topic'] = $in_params['search_topic'];
		if (!empty($in_params['search_user_id'])) $params['search_options']['search_user_id'] = $in_params['search_user_id'];
		if (!empty($in_params['search_related'])) $params['search_options']['search_related'] = $in_params['search_related'];
		if (!empty($in_params['search_rss_feed'])) $params['search_options']['search_rss_feed'] = $in_params['search_rss_feed'];

		if (!empty($in_params['search_user_group_id'])) $params['search_options']['search_user_group_id'] = $in_params['search_user_group_id'];
		if (!empty($in_params['search_not_user_group_id'])) $params['search_options']['search_not_user_group_id'] = $in_params['search_not_user_group_id'];

		if (!empty($in_params['search_schedule_id'])) $params['search_options']['search_schedule_id'] = $in_params['search_schedule_id'];
		if (!empty($in_params['search_schedule_duration'])) $params['search_options']['search_schedule_duration'] = $in_params['search_schedule_duration'];
		if (!empty($in_params['search_schedule_year'])) $params['search_options']['search_schedule_year'] = $in_params['search_schedule_year'];
		if (!empty($in_params['search_schedule_id_in'])) $params['search_options']['search_schedule_id_in'] = $in_params['search_schedule_id_in'];
		if (!empty($in_params['search_schedule_duration_in'])) $params['search_options']['search_schedule_duration_in'] = $in_params['search_schedule_duration_in'];
		if (!empty($in_params['search_schedule_year_in'])) $params['search_options']['search_schedule_year_in'] = $in_params['search_schedule_year_in'];
		if (!empty($in_params['search_schedule_id_not_in'])) $params['search_options']['search_schedule_id_not_in'] = $in_params['search_schedule_id_not_in'];
		if (!empty($in_params['search_schedule_duration_not_in'])) $params['search_options']['search_schedule_duration_not_in'] = $in_params['search_schedule_duration_not_in'];
		if (!empty($in_params['search_schedule_year_not_in'])) $params['search_options']['search_schedule_year_not_in'] = $in_params['search_schedule_year_not_in'];
		if (!empty($in_params['search_schedule_duration_lt'])) $params['search_options']['search_schedule_duration_lt'] = $in_params['search_schedule_duration_lt'];
		if (!empty($in_params['search_schedule_duration_lte'])) $params['search_options']['search_schedule_duration_lte'] = $in_params['search_schedule_duration_lte'];
		if (!empty($in_params['search_schedule_duration_gt'])) $params['search_options']['search_schedule_duration_gt'] = $in_params['search_schedule_duration_gt'];
		if (!empty($in_params['search_schedule_duration_gte'])) $params['search_options']['search_schedule_duration_gte'] = $in_params['search_schedule_duration_gte'];
		if (!empty($in_params['search_schedule_year_lt'])) $params['search_options']['search_schedule_year_lt'] = $in_params['search_schedule_year_lt'];
		if (!empty($in_params['search_schedule_year_lte'])) $params['search_options']['search_schedule_year_lte'] = $in_params['search_schedule_year_lte'];
		if (!empty($in_params['search_schedule_year_gt'])) $params['search_options']['search_schedule_year_gt'] = $in_params['search_schedule_year_gt'];
		if (!empty($in_params['search_schedule_year_gte'])) $params['search_options']['search_schedule_year_gte'] = $in_params['search_schedule_year_gte'];
		if (!empty($in_params['search_schedule_minute'])) $params['search_options']['search_schedule_minute'] = $in_params['search_schedule_minute'];
		if (!empty($in_params['search_schedule_hour'])) $params['search_options']['search_schedule_hour'] = $in_params['search_schedule_hour'];
		if (!empty($in_params['search_schedule_dom'])) $params['search_options']['search_schedule_dom'] = $in_params['search_schedule_dom'];
		if (!empty($in_params['search_schedule_month'])) $params['search_options']['search_schedule_month'] = $in_params['search_schedule_month'];
		if (!empty($in_params['search_schedule_dow'])) $params['search_options']['search_schedule_dow'] = $in_params['search_schedule_dow'];
		if (!empty($in_params['search_schedule_date'])) $params['search_options']['search_schedule_date'] = $in_params['search_schedule_date'];

		if (!empty($in_params['search_exclude_id'])) $params['search_options']['search_exclude_id'] = $in_params['search_exclude_id'];

		if (!empty($in_params['search_sort_by'])){
			$params['search_sort_by'] = $in_params['search_sort_by'];
		}else{
			$params['search_sort_by'] = 'order_num';
		}

		if (isset($in_params['search_order']) && !empty($in_params['search_order'])){
			$params['search_order'] = $in_params['search_order'];
		}else{
			$params['search_order'] = 'descending';
		}


		if (isset($this) && is_a($this, 'Articles_list')){
			if (!isset($in_params['search_options']) || !is_array($in_params['search_options']) || empty($in_params['search_options'])){
				$params['search_options']['generic_search'] = $this->generic_get_search_params($sm, VIVVO_DB_PREFIX . $this->_sql_table,  $in_params);
			}
		}else{
			if (!isset($in_params['search_options']) || !is_array($in_params['search_options']) || empty($in_params['search_options'])){
				$params['search_options']['generic_search'] = Articles_list::generic_get_search_params($sm, VIVVO_DB_PREFIX . 'articles',  $in_params);
			}
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

	function get_search_params_from_url($sm){
		$um = $sm->get_url_manager();
		$params = Articles_list::get_search_params($sm, $um->list);
		return $params;
	}

}

#EOF