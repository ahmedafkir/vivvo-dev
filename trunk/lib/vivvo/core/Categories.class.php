<?php
/* =============================================================================
 * $Revision: 4896 $
 * $Date: 2010-04-07 11:05:12 +0200 (Wed, 07 Apr 2010) $
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
 * Categories object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see			vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class Categories extends vivvo_post_object {

	/**
	 * id
	 * Database field type:	int(6)
	 * Null status:
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * category_name
	 * Database field type:	varchar(255)
	 * Null status:
	 *
	 * @var	string	$category_name
	 */
	var $category_name;

	/**
	 * parent_cat
	 * Database field type:	int(6)
	 * Null status:
	 *
	 * @var	integer	$parent_cat
	 */
	var $parent_cat;

	/**
	 * order_num
	 * Database field type:	int(4)
	 * Null status:		YES
	 *
	 * @var	integer	$order_num
	 */
	var $order_num;

	/**
	 * article_num
	 * Database field type:	int(4)
	 * Null status:		YES
	 *
	 * @var	integer	$article_num
	 */
	var $article_num;

	/**
	 * template
	 * Database field type:	varchar(50)
	 * Null status:
	 *
	 * @var	string	$template
	 */
	var $template;

	/**
	 * CSS
	 * css
	 * Database field type:	varchar(50)
	 * Null status:
	 *
	 * @var	string	$css
	 */
	var $css;

	/**
	 * view_subcat
	 * Database field type:	int(1)
	 * Null status:		YES
	 *
	 * @var	integer	$view_subcat
	 */
	var $view_subcat;

	/**
	 * redirect
	 * Database field type:	varchar(255)
	 * Null status:
	 *
	 * @var	string	$redirect
	 * @deprecated	not used anymore
	 */
	var $redirect;

	/**
	 * image
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$image
	 */
	var $image;

	/**
	 * sefriendly
	 * Database field type:	varchar(255)
	 * Null status:		NO
	 *
	 * @var	string	$sefriendly
	 */
	var $sefriendly;

	/**
	 * article_template
	 * Database field type:	varchar(255)
	 * Null status:		YES
	 *
	 * @var	string	$article_template
	 */
	var $article_template;


	var $_sql_table = 'categories';
	var $subcategories;
	/**
	 * Category list
	 *
	 * @var category_list
	 */
	var $_category_list;

	function set__category_list ($cat_list){
			$this->_category_list =& $cat_list;
	}

	function add_subcategory(&$cat){
		if (is_a($cat, 'Categories')){
			if (!is_array($this->subcategories)) $this->subcategories = array();
			$this->subcategories[$cat->id] =& $cat;
		}
	}

	function get_template(){
		if ($this->template == ''){
			return VIVVO_CATEGORY_LAYOUT;
		}elseif ($this->template != 'inherit'){
			if (file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'category/' . $this->template)){
				return $this->template;
			}else{
				return VIVVO_CATEGORY_LAYOUT;
			}
		}

		if (isset($this->_category_list)){
			$category =& $this->_category_list->get_category($this->parent_cat);
		}

		if (is_a ($category, 'Categories') && $this->id != 0){
			return $category->get_template();
		}else{
			return VIVVO_CATEGORY_LAYOUT;
		}
	}


	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = $id;
	}

	/**
	 * Sets {@link $category_name}
	 *
	 * @param	string	$category_name
	 */

	function set_category_name($category_name){
		if ($category_name == ''){
			return false;
		}else{
			$this->category_name = $category_name;
			return true;
		}
	}

	/**
	 * Sets {@link $parent_cat}
	 *
	 * @param	integer	$parent_cat
	 */
	function set_parent_cat($parent_cat){
		$parent_cat = (int) $parent_cat;
		if ($parent_cat < 0){
			$this->parent_cat = 0;
		}else{
			$this->parent_cat = $parent_cat;
		}
		return true;
	}

	/**
	 * Sets {@link $order_num}
	 *
	 * @param	integer	$order_num
	 */
	function set_order_num($order_num){
		$order_num = (int) $order_num;
		if ($order_num < 0){
			$this->order_num = 0;
		}else{
			$this->order_num = $order_num;
		}
		return true;
	}

	/**
	 * Sets {@link $article_num}
	 *
	 * @param	integer	$article_num
	 */
	function set_article_num($article_num){
		$article_num = (int) $article_num;
		if (( (int) ($article_num) < 0) or ($article_num == '')){
			$this->article_num = 10;
		}else{
			$this->article_num = (int) $article_num;
		}
		return true;
	}

	/**
	 * Sets {@link $template}
	 *
	 * @param	string	$template
	 */
	function set_template($template){
		$this->template = $template;
		return true;
	}

	/**
	 * Sets {@link $css}
	 *
	 * @param	string	$css
	 */
	function set_css($css){
		$this->css = $css;
		return true;
	}

	/**
	 * Sets {@link $view_subcat}
	 *
	 * @param	integer	$view_subcat
	 */
	function set_view_subcat($view_subcat){
		$view_subcat = (int) $view_subcat;
		if ($view_subcat == 0){
			$this->view_subcat = 0;
		}else{
			$this->view_subcat = 1;
		}
		return true;
	}

	/**
	 * Sets {@link $redirect}
	 *
	 * @param	string	$redirect
	 */
	function set_redirect($redirect){
		$this->redirect = $redirect;
	}

	/**
	 * Sets {@link $image}
	 *
	 * @param	string	$image
	 */
	function set_image($image){
		if (file_exists(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $image)) {
			$this->image = $image;
		} else {
			$this->image = '';
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
	 * Sets {@link $article_template}
	 *
	 * @param	string	$article_template
	 */
	function set_article_template($article_template){
		$this->article_template = $article_template;
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
	 * Gets $category_name
	 *
	 * @return	string
	 */
	function get_category_name(){
		return $this->category_name;
	}
	/**
	 * Gets $parent_cat
	 *
	 * @return	integer
	 */
	function get_parent_cat(){
		return $this->parent_cat;
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
	 * Gets $article_num
	 *
	 * @return	integer
	 */
	function get_article_num(){
		return $this->article_num;
	}
	/**
	 * Gets $css
	 *
	 * @return	string
	 */
	function get_css(){
		if ($this->css == ''){
			return VIVVO_DEFAULT_THEME;
		}elseif ($this->css != 'inherit'){
			if (file_exists(VIVVO_FS_THEME_ROOT . 'themes/' . $this->css)){
				return $this->css;
			}else{
				return VIVVO_DEFAULT_THEME;
			}
		}

		if (isset($this->_category_list)){
			$category =& $this->_category_list->get_category($this->parent_cat);
		}

		if (is_a ($category, 'category') && $this->id != 0){
			return $category->get_css();
		}else{
			return VIVVO_DEFAULT_THEME;
		}
	}
	/**
	 * Gets $view_subcat
	 *
	 * @return	integer
	 */
	function get_view_subcat(){
		return $this->view_subcat;
	}
	/**
	 * Gets $redirect
	 *
	 * @return	string
	 */
	function get_redirect(){
		return $this->redirect;
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
	 * Gets $sefriendly
	 *
	 * @return	string
	 */
	function get_sefriendly(){
		return $this->sefriendly;
	}

	/**
	 * Gets $article_template
	 *
	 * @return	string
	 */
	function get_article_template(){

		if ($this->article_template == ''){
			return VIVVO_ARTICLE_LAYOUT;
		}elseif ($this->article_template != 'inherit'){
			if (file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'article/' . $this->article_template)){
				return $this->article_template;
			}else{
				return VIVVO_ARTICLE_LAYOUT;
			}
		}

		if (isset($this->_category_list)){
			$category = $this->_category_list->get_category($this->parent_cat);
		}

		if (is_a ($category, 'Categories') && $this->id != 0){
			return $category->get_article_template();
		}else{
			return VIVVO_ARTICLE_LAYOUT;
		}
	}

	function get_breadcrumb(){
		return $this->_category_list->get_breadcrumb($this->id);
	}

	function get_breadcrumb_href(){
		$href_text = '';

		$breadcrumb = $this->get_breadcrumb();

		if (is_array($breadcrumb)) {
			foreach ($breadcrumb as $crumb) {
				$href_text .= urlencode($crumb->sefriendly) . '/';
			}
		}

	    return $href_text;
	}

	function get_href($pg = 1, $type = 'html') {
		return $this->format_href(vivvo_lite_site::get_instance(), $this->id, $pg, $type);
	}

	/**
	 * Returns absolute URL of category page
	 *
	 * @param	int		$pg
	 * @param	string	$type
	 * @return	string
	 */
	public function get_absolute_href($pg = 1, $type = 'html') {
		return make_absolute_url($this->get_breadcrumb_href() . "index.$pg.$type");
	}

	function format_href(&$sm, $id, $pg = 1, $type = 'html') {
		return make_proxied_url($sm->get_categories()->list[$id]->get_breadcrumb_href() . "index.$pg.$type");
	}

	/**
	 * Gets $keywords
	 *
	 * @return	string
	 */
	function get_keywords(){
		return $this->category_name . ',' . VIVVO_WEBSITE_TITLE;
	}
	/**
	 * Gets $description
	 *
	 * @return	string
	 */
	function get_description(){
		return $this->category_name . ',' . VIVVO_WEBSITE_TITLE;
	}

	/**
	 * Gets ids of subcategories
	 *
	 * @return	array
	 */
	function get_subcategories_ids(){
		return array_keys($this->subcategories);
	}

	function get_descendent_ids(){
		if (!empty($this->subcategories)){
			$result = array();
			$keys = array_keys($this->subcategories);
			foreach ($keys as $k){
				$result = array_merge ($result, $this->subcategories[$k]->get_descendent_ids());
			}
			$result = array_merge ($result, $keys);
			$resutl = array_unique ($result);
			return $result;
		}
		return array();
	}

	/**
	 * On sql delte
	 *
	 * @param vivvo_post_master $post_master
	 */
	function on_delete($post_master){
		require_once(dirname(__FILE__) . '/Articles.class.php');

		$article_list = new Articles_list();
		$article_list->get_articles_by_category_id($this->id);
		$article_list->sql_delete_list($post_master);

		$sub_keys = array_keys($this->subcategories);
		$sub_count = count ($sub_keys);
		for ($i = 0 ; $i < $sub_count; $i++){
			$post_master->set_data_object($this->subcategories[$sub_keys[$i]]);
			$post_master->sql_delete();
		}

		$fm = vivvo_lite_site::get_instance()->get_file_manager();
		if ($this->get_image() != ''){
			$fm->delete_fs(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $this->get_image());
		}

		vivvo_cache::get_instance()->delete('categories');
	}

	function on_update($post_master) {
		vivvo_cache::get_instance()->delete('categories');
	}

	function on_insert($post_master) {
		vivvo_cache::get_instance()->delete('categories');
	}

	protected $article_count = false;

	public function __sleep() {
		$this->article_count = false;
		return array_keys(get_object_vars($this));
	}

	public function get_article_count() {

		if ($this->article_count === false) {

			class_exists('Articles_list') or require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php';

			$list = Articles_list::factory();
			$list->search(array('search_cid' => $this->id), '', '', 0, 0, false);
			$this->article_count = $list->get_total_count();
		}

		return $this->article_count;
	}

	/**
	 * @var	bool	True if this category is currently displaying
	 */
	private $selected = false;

	/**
	 * @var	bool	True if this category or one of it's descedants is currently displaying
	 */
	private $child_selected = false;

	/**
	 * Checks if this category is currently selected
	 *
	 * @return	bool
	 */
	public function is_selected() {
		return $this->selected;
	}

	/**
	 * Checks if this category or one of it's descedants is currently selected
	 *
	 * @return	bool
	 */
	public function is_child_selected() {
		return $this->child_selected;
	}

	/**
	 * Sets selected flags
	 *
	 * @param	bool	$self
	 * @param	bool	$child
	 */
	public function set_selected($self, $child) {
		$this->selected = $self;
		$this->child_selected = $child;
	}
}
/**
 * Categories list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class Categories_list extends vivvo_db_list {

	var $_sql_table = 'categories';
	var $post_object_type = 'Categories';
	var $root_category;

	/**
	 * Get category by id
	 *
	 * @param integer $id
	 * @return mixed category on SUCCES, a false on FAIL
	 */
	function &get_category($id){
		$id = (int) $id;
		if (key_exists($id, $this->list)){
			return $this->list[$id];
		}else{
			return false;
		}
	}

	function set_root_category($root = 0){
		if (is_array($this->list)){
			if ($root == 0 || !key_exists($root, $this->list)){
				$this->root_category =& $this->list[0];
			}else{
				$this->root_category =& $this->list[$root];
			}
		}
	}

	function get_breadcrumb($id){
		if (key_exists($id, $this->list)){
			$breadcrumb = array();
			$breadcrumb[] =& $this->list[$id];
			$id = $this->list[$id]->parent_cat;
			while (key_exists($id, $this->list)){
				if ($this->list[$id]->id != $this->root_category->id){
					$breadcrumb[] =& $this->list[$id];
					$id = $this->list[$id]->parent_cat;
				}else {
					return array_reverse($breadcrumb);
				}
			}
			return array_reverse($breadcrumb);
		}
	}

	function _default_query(){
		$this->_query->set_from(
								VIVVO_DB_PREFIX . 'categories ');
		if (is_array($this->_fields) && !empty($this->_fields)){
			foreach ($this->_fields as $field){
				$this->_query->add_fields($field);
			}
		}else{
			$this->_query->add_fields('*');
		}

	}

	function add_filter($type, $condition = ''){
		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
				$this->_query->add_where('(id = \'' . $condition . '\')');
			break;
			case '!id':
				$this->_query->add_where('(id != \'' . $condition . '\')');
			break;
			case 'category_name':
				$this->_query->add_where('(category_name = \'' . $condition . '\')');
			break;
			case 'parent_cat':
				$this->_query->add_where('(parent_cat = \'' . $condition . '\')');
			break;
			case 'order_num':
				$this->_query->add_where('(order_num = \'' . $condition . '\')');
			break;
			case 'article_num':
				$this->_query->add_where('(article_num = \'' . $condition . '\')');
			break;
			case 'template':
				$this->_query->add_where('(template = \'' . $condition . '\')');
			break;
			case 'css':
				$this->_query->add_where('(css = \'' . $condition . '\')');
			break;
			case 'view_subcat':
				$this->_query->add_where('(view_subcat = \'' . $condition . '\')');
			break;
			case 'image':
				$this->_query->add_where('(image = \'' . $condition . '\')');
			break;
			case 'sefriendly':
				$this->_query->add_where('(sefriendly = \'' . $condition . '\')');
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
			$this->add_filter('id',$params['search_id']);
		}
		if (isset($params['search_not_id'])){
			$this->add_filter('!id',$params['search_not_id']);
		}
		if (isset($params['search_category_name'])){
			$this->add_filter('category_name',$params['search_category_name']);
		}
		if (isset($params['search_parent_cat'])){
			$this->add_filter('parent_cat',$params['search_parent_cat']);
		}
		if (isset($params['search_order_num'])){
			$this->add_filter('order_num',$params['search_order_num']);
		}
		if (isset($params['search_article_num'])){
			$this->add_filter('article_num',$params['search_article_num']);
		}
		if (isset($params['search_template'])){
			$this->add_filter('template',$params['search_template']);
		}
		if (isset($params['search_css'])){
			$this->add_filter('css',$params['search_css']);
		}
		if (isset($params['search_view_subcat'])){
			$this->add_filter('view_subcat',$params['search_view_subcat']);
		}
		if (isset($params['search_image'])){
			$this->add_filter('image',$params['search_image']);
		}
		if (isset($params['search_sefriendly'])){
			$this->add_filter('sefriendly',$params['search_sefriendly']);
		}



		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'id':
				$this->_query->add_order('id' . $search_direction);
				break;
			case 'category_name':
				$this->_query->add_order('category_name' . $search_direction);
				break;
			case 'parent_cat':
				$this->_query->add_order('parent_cat' . $search_direction);
				break;
			case 'order_num':
				$this->_query->add_order('order_num' . $search_direction);
				break;
			case 'article_num':
				$this->_query->add_order('article_num' . $search_direction);
				break;
			case 'template':
				$this->_query->add_order('template' . $search_direction);
				break;
			case 'css':
				$this->_query->add_order('css' . $search_direction);
				break;
			case 'view_subcat':
				$this->_query->add_order('view_subcat' . $search_direction);
				break;
			case 'image':
				$this->_query->add_order('image' . $search_direction);
				break;
			case 'sefriendly':
				$this->_query->add_order('sefriendly' . $search_direction);
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

	function set_list() {
		if (($list = vivvo_cache::get_instance()->get('categories')) !== false) {
			$this->list = $list;
		} else {
			$sql ='SELECT * FROM '.VIVVO_DB_PREFIX.'categories ORDER BY order_num ASC';
			$res = vivvo_lite_site::get_instance()->get_db()->query($sql);
			while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {
				$this->list[$row['id']] = new Categories(null, $row);
				$this->list[$row['id']]->set__category_list ($this);
			}
			$res->free();
			$this->list[0] = new Categories();
			$this->list[0]->set__category_list ($this);
			$this->list[0]->id = 0;
			$this->sort_subcategories(0);
			vivvo_cache::get_instance()->put('categories', $this->list);
		}

		$current_url = preg_replace('|/index(\.\d+)?\.html$|', '/', CURRENT_URL);

		foreach ($this->list[0]->subcategories as $category) {
			$this->set_selected($category, $current_url);
		}
	}

	private function set_selected($category, $current_url) {

		if ($category->get_redirect()) {
			$url = preg_replace('|/index(\.\d+)?\.html$|', '/', make_absolute_url($category->get_redirect()));
		} else {
			$url = preg_replace('|/index(\.\d+)?\.html$|', '/', $category->get_absolute_href());
		}

		if (substr($current_url, 0, $url_len = strlen($url)) == $url) {
			$category->set_selected(strlen($current_url) == $url_len, true);
		}

		foreach ($category->subcategories as $subcategory) {
			$this->set_selected($subcategory, $current_url);
		}
	}

	function sort_subcategories($root){
		if (key_exists($root, $this->list)){
			foreach ($this->list as $key => $cat){
				if ($root == 0 && $cat->id == 0){
				}else{
					if ($cat->parent_cat == $root && $cat->parent_cat != $cat->id){
						$this->list[$root]->add_subcategory($this->list[$key]);
						$this->sort_subcategories($key);
					}
				}
			}
		}

		return false;
	}

	function Categories_list($site_manager = null, $root = 0){
			$this->vivvo_db_list($site_manager);
			$this->set_list();
			$this->set_root_category($root);
	}

	function get_number_of_category(){

		$sql ='SELECT max( order_num ) as max FROM '.VIVVO_DB_PREFIX.'categories';
		$res = vivvo_lite_site::get_instance()->get_db()->query($sql);

		if ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)){
			$res->free();
			return $row['max'] + 1;
		}else{
			return 1;
		}

	}

	function get_hrefs($ids){
		$hrefs = array();
		if (is_array($ids)){
			$ids = implode(',', $ids);
		}

		$sm = vivvo_lite_site::get_instance();

		$sql = 'SELECT id, category_name FROM '.VIVVO_DB_PREFIX. $this->_sql_table . ' WHERE id IN (' . secure_sql_in($ids) . ')';
		$res = $sm->get_db()->query($sql);

		if (!PEAR::isError($res)) {
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)){
				$hrefs[$row['id']] = array();
				$hrefs[$row['id']]['title'] = $row['category_name'];
				$hrefs[$row['id']]['href'] =  Categories::format_href($sm, $row['id']);
			}
		}

		return $hrefs;
	}
}

?>