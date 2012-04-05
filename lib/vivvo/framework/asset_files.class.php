<?php
/* =============================================================================
 * $Revision: 5370 $
 * $Date: 2010-05-24 11:42:36 +0200 (Mon, 24 May 2010) $
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

defined('VIVVO_ASSETS_DIRTYPE_ID') or define('VIVVO_ASSETS_DIRTYPE_ID', 6);

	/**
	 * AssetFiles object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 * @see			vivvo_post_object
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class AssetFiles extends vivvo_post_object {

		var $_sql_table = 'asset_files';

		/**
		 * id
		 * @var	integer	$id
		 */
		var $id;
        /**
		 * file name
		 * @var	string	$filename
		 */
		var $name;
		/**
		 * file extension
		 * @var	string $extension
		 */
		var $extension;
		/**
		 * file path (directory)
		 * @var	string	$path
		 */
		var $path;
		/**
		 * MD5 hash of file path, for search in given dir
		 * @var	string	$path_md5
		 */
		var $path_md5;
		/**
		 * File size, in bytes
		 * @var	integer	$size
		 */
		var $size;
		/**
		 * Width, if applicable
		 * @var	integer	$width
		 */
		var $width;
		/**
		 * Height, if applicable
		 * @var	integer	$height
		 */
		var $height;
		/**
		 * File description (meta data)
		 * @var	text $info
		 */
		var $info;
		/**
		 * File description
		 * @var	text $info
		 */
		var $descript;
		/**
		 * File type (foreign key)
		 * @var	integer	$filetype_id
		 */
		var $filetype_id;
		/**
		 * Datetime of last modification
		 * @var	string $mtime
		 */
		var $mtime;
		/**
		 * Flag to know if file's metadata has been read
		 * @var	boolean $scanned
		 */
		var $scanned;

		var $keywords = false;



		var $_root_dir = VIVVO_FS_ROOT;

		function set_id($id) {
			$this->id = $id;
		}
		function set_name($name) {
			$this->name = $name;
		}
		function set_extension($ext) {
			$this->extension = $ext;
		}
		function set_path($path) {
			$this->path = $path;
		}
		function set_path_md5($path_md5) {
			$this->path_md5 = $path_md5;
		}
		function set_size($size) {
			$this->size = $size;
		}
		function set_width($width) {
			$this->width = $width;
		}
		function set_height($height) {
			$this->height = $height;
		}

		function set_dimensions($width, $height) {
			$this->set_width($width);
			$this->set_height($height);
		}

		function set_info($info) {
			$this->info = $info;
		}

		function set_descript($descript) {
			$this->descript = $descript;
		}

		function set_filetype_id($filetype_id) {
			$this->filetype_id = $filetype_id;
		}

		/**
		 * set filetype_id based on extension
		 * @param string $file
		 */
		function set_filetype($file) {
			require_once( dirname(__FILE__).'/asset_filetypes.class.php');

			$type_list = new AssetFileTypes_list();

			$parts = pathinfo($file);

			$type = $type_list->get_filetype_by_ext($parts['extension']);
			$type_id = $type? $type->get_id() : 0;
			$this->set_filetype_id($type_id);
		}

		/**
		 * set modification time
		 * @param string $date	Either unix timestamp or valid mysql datetime
		 */
		function set_mtime($date) {
			if(is_numeric($date) and strlen($date)>8 and strlen($date)<14) // if timestamp
				$this->mtime = date('Y-m-d G:i:s', $date);
			else
				$this->mtime = $date;
		}

		function set_scanned($flag=1) {
			$this->scanned = $flag;
		}

		function set_keywords($key) {
			$this->keywords = $key;
		}


		function get_id() {
			return $this->id;
		}
		function get_name() {
			return $this->name;
		}
		function get_extension() {
			return $this->extension;
		}
		/**
		 * get filename with extension
		 * @return string
		 */
		function get_filename() {
			return $this->name .'.'. $this->extension;
		}

		/**
		 * alias for get_filename()
		 */
		function filename() {
			return $this->name .'.'. $this->extension;
		}

		function get_path() {
			return $this->path;
		}
		/**
		 * alias for get_path()
		 */
		function get_dir() {
			return $this->path;
		}
		function dir() {
			return $this->path;
		}

		function get_fullname() {
			return $this->path . $this->name . '.' . $this->extension;

		}

		function get_path_md5() {
			return $this->path_md5;
		}
		function get_size() {
			return $this->size;
		}
		function get_smart_size() {
			$size = $this->size;
			if($size < 1024)
				return $size . ' B';

			$size /= 1024;
			if($size < 1024)
				return round($size, 2) . ' KB';

			$size /= 1024;
			return round($size, 2) . ' MB';

		}

		function get_width() {
			if($this->scanned)
				return $this->width;

			$this->read_info_from_file();
			return $this->width;
		}

		function get_height() {
			if($this->scanned)
				return $this->height;

			$this->read_info_from_file();
			return $this->height;
		}

		function get_info() {
			if($this->scanned)
				return $this->info;
			$this->read_info_from_file();
			return $this->info;
		}

		function get_descript() {
			return $this->descript;
		}
		function get_filetype_id() {
			return $this->filetype_id;
		}
		/**
		 * get modification time
		 * @return string	Mysql datetime
		 */
		function get_mtime() {
			return $this->mtime;
		}

		function get_scanned() {
			return $this->scanned;
		}

		/**
		 * get modification timestamp
		 * @return int unix timestamp
		 */
		function get_mtimestamp() {
			return strtotime($this->mtime);
		}


		/**
		 * get modification date
		 * @param string $format date format
		 * @return string
		 */
		function get_time($format='c') {
			return date($format, strtotime($this->mtime));
		}

		function get_keywords() {

			if ( $this->keywords !== false) {
				return $this->keywords;
			}

			require_once(VIVVO_FS_FRAMEWORK . '/asset_keywords.class.php');

			$keywords_list = new AssetKeywords_list();

			$keywords = $keywords_list->get_keywords_by_asset_id($this->id);

			$keys = array();
			if ($keywords) {
				foreach($keywords as $key) {
					$keys[] = $key->get_keyword();
				}
			}

			if ($keys) {
				return $this->keywords = implode(', ', $keys);
			}

			return false;
		}

		function get_thumb_href() {

			//if image, return thumb
			if (in_array(strtolower($this->extension), array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
				return VIVVO_STATIC_URL . 'thumbnail.php?file=' . preg_replace('|^' . VIVVO_FS_FILES_DIR . '|', '', $this->path) . $this->get_filename() . '&size=thumbview';
			}

			//if icon exists return path to icon
			if (file_exists(VIVVO_FS_ROOT . ($icon_file = VIVVO_FS_ADMIN_DIR . 'img/mime_' . strtolower($this->extension) . '.gif'))) {
				return VIVVO_URL . $icon_file;
			}
			//otherwise return default icon
			return VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'img/icon_file.gif';
		}

		function read_info_from_file($filepath=false) {

			if (!$filepath) {
				$filepath = $this->get_fullname();
			}

			$fullpath = VIVVO_FS_ROOT . $filepath;

			if ( !file_exists($fullpath) )
				return false;

			$classes_path = VIVVO_FS_ROOT . 'lib/vivvo/file/';

			$parts = pathinfo($filepath);
			if(!$this->extension)
				$this->extension = $parts['extension'];

			$ext = strtolower($this->extension);

			if( in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp')) ) {

				$dim = @getimagesize($fullpath);
				if($dim)
					$this->set_dimensions( $dim[0], $dim[1] );
			}

			if( in_array($ext, array('jpg', 'jpeg')) ) {

				require_once $classes_path .'image_info.class.php';
				$info = image_info::get_info($fullpath);

				if($info)
					$this->set_info($info);

			} elseif ( in_array($ext, array('avi', 'mpg', 'mpeg', 'flv')) ) {
				require_once $classes_path.'video_info.class.php';
				$info = video_info::get_info($fullpath);
				if($info)
					$this->set_info($info);

			} elseif ( $ext == 'mp3' ) {
				require_once $classes_path .'media_info.class.php';
				$info = media_info::get_info($fullpath);
				if($info)
					$this->set_info($info);

			} elseif ( $ext == 'pdf' ) {
				require_once $classes_path .'pdf_info.class.php';
				$info = pdf_info::get_info($fullpath);
				if($info)
					$this->set_info($info);

			} elseif ( $ext == 'zip' ) {
				require_once $classes_path .'archive_info.class.php';
				$info = archive_info::get_info($fullpath);
				if($info)
					$this->set_info($info);

			} else {

			}

			$this->scanned = 1;

			if( empty($this->id) )
				return true;

			//if we've got ID save the data
			$sql = 'UPDATE '. VIVVO_DB_PREFIX .'asset_files SET width='. intval($this->width).
						',height='. intval($this->height).
						",info='". trim(secure_sql( htmlspecialchars($this->info))).
						"',scanned=1 WHERE id=". intval($this->id);
			$res = vivvo_lite_site::get_instance()->get_db()->exec($sql);
			if(PEAR::isError($res))
				$this->scanned = 0;

			return true;
		}

		/**
		 * populate properties using vivvo file object
		 * @param &vivvo_file $file_obj
		 * @return boolean
		 */
		function populate_from_file($filepath, $is_update=false) {

			$fullpath = VIVVO_FS_ROOT . $filepath;

			if(!file_exists($fullpath))
				return false;

			if(!$is_update) { //on updates skip data that hasn't changed

				$parts = pathinfo($filepath);
				$name = substr( $parts['basename'], 0, strrpos($parts['basename'], '.'));
				$this->set_name($name);
				$this->set_extension($parts['extension']);
				$this->set_path($parts['dirname'] .'/');
				$this->set_path_md5( md5($this->path) );
				$this->set_filetype($parts['basename']);
			}

			$this->set_size( filesize($fullpath) );
			$this->set_mtime( filemtime($fullpath) );

			return true;
		}

		function get_href(){
			return VIVVO_URL . $this->path . $this->filename;
		}

		function get_content(){
			return file_get_contents($this->_root_dir . $this->dir . $this->name . '.' . $this->extension);
		}

		function get_html_content(){
			$content = file_get_contents($this->_root_dir . $this->dir . $this->name . '.' . $this->extension);
			return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
		}

		function on_delete(&$post_master){

			//delete directly
			$post_master->sql_delete_list('asset_keywords', "asset_id=$this->id");

		}


	}//end_class

	/**
	 * AssetFiles DB list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 * @see			vivvo_db_paged_list
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class AssetFiles_list extends vivvo_db_paged_list {
		var $_sql_table = 'asset_files';
		var $post_object_type = 'AssetFiles';

		function _default_query(){
			$this->_query->reset_query();

			$this->_query->set_from(VIVVO_DB_PREFIX . 'asset_files AS f');
			$this->_query->add_join('LEFT JOIN '. VIVVO_DB_PREFIX .'asset_file_types AS t ON (f.filetype_id=t.id)', 't');
			$this->_query->add_fields('f.*');
			$this->_query->add_fields('t.type');
			$this->_query->add_fields('t.extensions');
			$this->_query->add_fields('t.path_prefix');
		}

		function add_filter($type, $cond = ''){

			$condition = secure_sql($cond);
			switch ($type){
				case 'id':
					$condition = secure_sql_in($condition);
					$this->_query->add_where('(f.id IN (' . $condition . '))');
					break;
				case 'notid':
					$condition = secure_sql_in($condition);
					$this->_query->add_where('(f.id NOT IN (' . $condition . '))');
					break;
				case 'name':
					$this->_query->add_where("(f.name='$condition')");
					break;
				case 'name_starts_with':
					$this->_query->add_where("(f.name LIKE '$condition%')");
					break;
				case 'name_wildcard':
					$this->_query->add_where("(f.name LIKE '%$condition%')");
					break;
				case 'ext':
					$ext = join("','", explode(',', $condition));
					$this->_query->add_where("(f.extension IN ('$ext'))");
					break;
				case 'path':
					$cond = rtrim($cond, '/') . '/'; //make sure one and just one / at the end
					$this->_query->add_where("(f.path_md5='". md5($cond) ."')");
					break;
				case 'under_path':
					$this->_query->add_where("(f.path LIKE '$condition%')");
					break;
				case 'type_id':
					$this->_query->add_where("(f.filetype_id='$condition')");
					break;
				case 'not_type_id':
					$condition = (int)$condition;
					$this->_query->add_where("(f.filetype_id != $condition)");
					break;
				case 'type':
					$this->_query->add_where("(t.type='$condition')");
					break;
				case 'date_after':
					$this->_query->add_where("(f.mtime > '$condition')");
					break;
				case 'info':
					$this->_query->add_where("(MATCH (f.info) AGAINST ('$condition' IN BOOLEAN MODE))");
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
		function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
			$this->_default_query(true);

			//search_query
			if (!empty($params['search_id'])){
				if (is_array($params['search_id']))
					$params['search_id'] = implode(',', $params['search_id']);
				$this->add_filter('id', $params['search_id']);
			}

			if (isset($params['search_notid'])){
				if (is_array($params['search_notid']))
					$params['search_notid'] = implode(',', $params['search_notid']);
				$this->add_filter('notid', $params['search_notid']);
			}

			if (isset($params['search_name'])){
				$this->add_filter('name', $params['search_name']);
			}
			if (!empty($params['search_ext'])){
				$this->add_filter('ext', $params['search_ext']);
			}
			if (isset($params['search_dir'])){
				$this->add_filter('path', $params['search_dir']);
			}
			if (isset($params['search_filename'])){
				$path_parts = pathinfo($params['search_filename']);
				$name = substr( $path_parts['basename'], 0, strrpos($path_parts['basename'], '.'));
				$this->add_filter('name', $name);
				$this->add_filter('ext', $path_parts['extension']);
			}
			if (isset($params['search_name_starts_with'])){
				$this->add_filter('name_starts_with', $params['search_name_starts_with']);
			}
			if (isset($params['search_name_wildcard'])){
				$this->add_filter('name_wildcard', $params['search_name_wildcard']);
			}

			if (isset($params['search_fullname'])){
				$path_parts = pathinfo($params['search_fullname']);
				$name = substr( $path_parts['basename'], 0, strrpos($path_parts['basename'], '.'));
				$this->add_filter('name', $name);
				$this->add_filter('ext', $path_parts['extension']);
				$this->add_filter('path', $path_parts['dirname']);
			}
			if (isset($params['search_under_path'])){
				$this->add_filter('under_path',$params['search_under_path']);
			}
			if (isset($params['search_filetype_id'])){
				$this->add_filter('type_id', $params['search_filetype_id']);
			}
			if (isset($params['search_not_filetype_id'])){
				$this->add_filter('not_type_id', $params['search_not_filetype_id']);
			}
			if (isset($params['search_filetype'])){
				$this->add_filter('type', $params['search_filetype']);
			}
			if (isset($params['search_info'])){
				$this->add_filter('info', $params['search_info']);
			}
			if (!empty($params['search_date_after'])){
				$date = VIVVO_START_TIME - 3600 * 24 * (int)$params['search_date_after'];
				$this->add_filter('date_after', date('Y-m-d H:i:s', $date) );
			}
			if (isset($params['search_keywords'])){
				$this->add_filter('keywords', $params['search_keywords'] );
			}

			if (!isset($params['search_filetype_id']) && !isset($params['search_not_filetype_id']) && !isset($params['search_filetype'])) {
				$this->add_filter('not_type_id', VIVVO_ASSETS_DIRTYPE_ID);
			}

			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'name':
				case 'filename':
					$this->_query->add_order('name' . $search_direction);
					break;
				case 'path':
					$this->_query->add_order('path' . $search_direction);
					break;
				case 'filetype':
					$this->_query->add_order('filetype_id' . $search_direction);
					break;
				case 'size':
					$this->_query->add_order('size' . $search_direction);
					break;
				case 'width':
					$this->_query->add_order('width' . $search_direction);
					break;
				case 'height':
					$this->_query->add_order('height' . $search_direction);
					break;
				case 'ext':
				case 'extension':
					$this->_query->add_order('extension' . $search_direction);
					break;
				case 'date':
					$this->_query->add_order('mtime' . $search_direction);
					break;

				default:
					$order = 'name';
					$this->_query->add_order('name' . ' ASC');
					break;
			}

			$limit = (int) $limit;
			$this->_query->set_limit($limit);
			$offset = (int) $offset;
			$this->_query->set_offset($offset);

#			error_log( 'QUERY: '.$this->_query->get_query() );

			if ($set_list){
				$this->set_list();
				//error_log(print_r($this->list,1));
				return $this->list;
			}
		}

		function get_asset_by_id($id){
			$id = (int)$id;
			$this->search(array('search_id'=>$id));

			return empty($this->list)? false : current($this->list);
		}

		function get_asset_by_fullname($name){
			$this->search(array('search_fullname'=>$name));

			return empty($this->list)? false : current($this->list);
		}

		function get_assets_by_path($dir){
			$this->search(array('search_dir'=>$dir));

			return empty($this->list)? false : $this->list;
		}


		function get_assets_by_type($filetype){
			$this->search(array('search_filetype'=>$filetype));

			return empty($this->list)? false : $this->list;
		}

		function get_assets_by_type_in_dir($filetype, $path){

			$this->search(array('search_filetype'=>$filetype, 'search_path'=>$path));

			return empty($this->list)? false : $this->list;
		}

		function get_assets_by_keyword($key){
			$this->_default_query();
			$this->_query->add_join(','. VIVVO_DB_PREFIX .'asset_keywords AS k', 'k');
			$this->_query->add_where('k.asset_id=f.id');
			$this->_query->add_where('k.keyword='. secure_sql($key) );
			$this->_query->add_where('filetype_id != ' . VIVVO_ASSETS_DIRTYPE_ID);
			$this->_query->add_fields('k.id AS key_id');
			$this->_query->add_fields('k.keyword');

			$this->_query->add_order('name' . ' ASC');

			$this->set_list();
			return empty($this->list)? false : $this->list;
		}

		/**
		 * Try to figure out if files in $dir have been changed since last scan
		 *
		 * @param string $dir	Relative path to a directory
		 * @return boolean
		 */
		function is_changed($dir) {
			$flagfile = VIVVO_FS_ROOT . $dir . '.htstampdir';

			return ( file_exists($flagfile) and filemtime(VIVVO_FS_ROOT.$dir)==filemtime($flagfile) )? false : true;
		}

		function get_total_number() {
			return $this->get_total_count();
		}

		function get_total_file_size() {
			$query = $this->_query->get_sum_query('size');
			$res = vivvo_lite_site::get_instance()->get_db()->query($query);
			if (!is_a($res, 'mdb2_error')){
				$size = $res->fetchOne();

				if($size < 1024)
					return intval($size) . ' B';

				$size /= 1024;
				if($size < 1024)
					return round($size, 2) . ' KB';

				$size /= 1024;
				return round($size, 2) . ' MB';

			}else {
				return false;
			}

		}

		function get_assets_quick_search($keyword) {

			$keyword = secure_sql($keyword);

			$dir = VIVVO_ASSETS_DIRTYPE_ID;

			$sql = "(SELECT * FROM ". VIVVO_DB_PREFIX ."asset_files WHERE name LIKE '$keyword%' AND filetype_id != $dir)
					UNION ALL
					(SELECT f.* FROM ". VIVVO_DB_PREFIX ."asset_files as f, ". VIVVO_DB_PREFIX ."asset_keywords as k
						WHERE f.id=k.asset_id AND k.keyword LIKE '$keyword%')
					UNION ALL
					(SELECT * FROM ". VIVVO_DB_PREFIX ."asset_files
						WHERE MATCH (info) AGAINST ('\"$keyword\"' IN BOOLEAN MODE) AND filetype_id != $dir)
					ORDER BY path ASC
					";

			$sm = vivvo_lite_site::get_instance();

			$res = $sm->get_db()->query($sql);

			$this->list = array();

			if (!is_a($res, 'mdb2_error')){
				$class = $this->post_object_type;
				while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))){
					if ($sm->_object_cache_manager->is_cached($class, $row[$this->id_key])){
						$this->list[$row[$this->id_key]] = $sm->_object_cache_manager->retrive($class, $row[$this->id_key]);
						$this->list[$row[$this->id_key]]->populate($row);

					} else {
						$object = new $class(null, $row);
						$sm->_object_cache_manager->add($class, $row[$this->id_key], $object);
						$this->list[$row[$this->id_key]] = $object;
					}
				}

				$res->free();

			}else{
				$sm->debug_push("sql:", $query, 1);
				$sm->debug_push("sql:", $res->getMessage(), 1);
			}

			return empty($this->list)? false : $this->list;

		}



	}//end_class


#EOF