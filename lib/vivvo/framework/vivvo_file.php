<?php
/* =============================================================================
 * $Revision: 5433 $
 * $Date: 2010-06-03 13:47:02 +0200 (Thu, 03 Jun 2010) $
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
 * Vivvo CMS
 *
 * LICENSE:
 *
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * @copyright	SpoonLabs
 * @package		vivvo_framework
 * @subpackage	file
 * @version		$Revision: 5433 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */

defined('VIVVO_MAX_UPLOAD_FILE_SIZE') or define('VIVVO_MAX_UPLOAD_FILE_SIZE', 100 * 1024 * 1024);

	/**
	 * Vivvo file manager
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	file
	 * @version		$Revision: 5433 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	*/
	class vivvo_file_manager extends vivvo_object{

		var $_handlers = array();

		var $_file_type = array();

		/**
		 * Get file extension
		 *
		 * @param string $file
		 * @return sting
		 */
		function get_extension($file){
			$exto = explode(".",basename ($file));
			$exto = end($exto);
			$ext = strtolower($exto);
			return $ext;
		}

		function get_basename($file,$path = false){
			if ($path){
				$file_array = explode("/",$file);
				$file = end($file_array);
			}
			$exto = explode(".",basename ($file));
			$exto = end($exto);
			return $last3 = substr($file, 0 ,(strlen($file)-(strlen($exto)))-1);
		}

		function delete_fs($file){
			if (file_exists($file)){
				if (is_writable($file)){
					if (unlink ($file)){
						return true;
					}else{
						$this->set_error_code(5001);
						return false;
					}
				}else{
					$this->set_error_code(5002);
					return false;
				}
			}else{
				$this->set_error_code(5003);
				return false;
			}
		}

		function _copy_fs($file, $new_file){
			return copy($file, $new_file);
		}

		function tmp_upload($file){
			$dir = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . 'tmp/';
			$file_basename = $this->get_basename($file['name']).'_'.rand(100000000,999999999);
			$file_ext = $this->get_extension($file['name']);
			$file_name = $file_basename.'.'.$file_ext;
			if ($this->upload($file, $dir . $file['name'])){
				rename($dir.$file['name'],$dir.$file_name);
				return new vivvo_file(null, $file_name, $dir);
			}else{
				return false;
			}
		}

		function get_upload_keys(){
			if (is_array($_FILES) && !empty($_FILES)){
				return array_keys($_FILES);
			}
			return $false;
		}

		function is_uploaded ($input_filename){
			if (isset($_FILES[$input_filename]) && is_uploaded_file($_FILES[$input_filename]['tmp_name']) && ($_FILES[$input_filename]["error"] == UPLOAD_ERR_OK)){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Creates file with random string in name of file
		 *
		 * @param	string	$file
		 * @return	string
		 */
		function random_file_name($file){
			$file_basename = $this->get_basename($file).'_'.rand(100000000,999999999);
			$file_ext = $this->get_extension($file);
			$file_basename = str_replace('%', '_', $file_basename);
			$file_name = $file_basename.'.'.$file_ext;

			return $file_name;
		}


		function upload_to($FILE, $destination = '', $ext_local_array = ''){
			if ($destination == ''){
				return $this->upload($FILE, '', $ext_local_array);
			}else{
				if (is_string($FILE)){
					if (isset($_FILES[$FILE])){
						$FILE = $_FILES[$FILE];
					}else{
						return false;
					}
				}
				$destination = preg_replace('/\.\./', '_', $destination);
				$destination = rtrim($destination, '/');
				$new_base_name = preg_replace('/[^a-z^A-Z^\d\.+]/i', '_', $FILE['name']);
				$new_base_name = $this->random_file_name($new_base_name);
				
				if ($destination === 'backup') {
                    $new_file = VIVVO_FS_ROOT . 'backup/' . $new_base_name;
                    $ext_local_array = 'gz';
				}else{    
                    $new_file = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $destination .'/' . $new_base_name;
                }

				$res = $this->upload($FILE, $new_file, $ext_local_array);
				if ($res !== false){
					return VIVVO_FS_FILES_DIR . $destination .'/' . $new_base_name;
				}else{
					return false;
				}
			}
		}

		/**
		 * Upload file
		 *
		 * @param array|string $FILE
		 * @param string $new_file
		 */
		function upload($FILE, $new_file = '', $ext_local_array = ''){
			//mime_type $FILE['type']

			if (is_string($FILE)){
				if (isset($_FILES[$FILE])){
					$FILE = $_FILES[$FILE];
				}else{
					return false;
				}
			}
			if (empty($new_file)){
				$new_base_name = preg_replace('/[^a-z^A-Z^\d\.+]/i', '_', $FILE['name']);
				$new_base_name = $this->random_file_name($new_base_name);
				$new_file = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $new_base_name;
			}

			if (is_uploaded_file($FILE['tmp_name'])){

				if (!empty($ext_local_array)){
					$allowed = explode(',', $ext_local_array);
				}else{
					$allowed = explode(',', VIVVO_ALLOWED_EXTENSIONS);
				}
				$ext = $this->get_extension($FILE["name"]);

				if (!defined('VIVVO_ALLOWED_EXTENSIONS') || in_array($ext, $allowed)){
					if($FILE["error"] == UPLOAD_ERR_OK){
						if ($FILE['size'] <= VIVVO_MAX_UPLOAD_FILE_SIZE){
							if ($ext == 'gif' and !defined('VIVVO_FILE_UPLOAD_DISABLE_SCRIPT_CHECK')){
								$file_str = file_get_contents($FILE['tmp_name']);
								if (strpos($file_str, '<?') !== false){
									$this->set_error_code(5004);
									return false;
								}
							}
							if ($this->_copy_fs($FILE["tmp_name"], $new_file)) {
								if (isset($_GET['debug'])) echo '<hr />success<hr />';
								chmod($new_file, 0666);
								return $new_base_name;
							}else{
								if (isset($_GET['debug'])) echo '<hr />Copy failed.<hr />';
								$this->set_error_code(5005);
								return false;
							}
						}else{
							if (isset($_GET['debug'])) echo '<hr />Location image size over limit. Upload failed.<hr />';
							$this->set_error_code(5006);
							return false;
						}
					}else{
						if (isset($_GET['debug'])) echo '<hr />Upload failed.<hr />';
						$this->set_error_code(5007);
						return false;
					}
				}else{
					if (isset($_GET['debug'])) echo '<hr />Not allowed file type. Wrong file type or no location image.<hr />';
						$this->set_error_code(5008);
						return false;
				}
			}else {
				if (isset($_GET['debug'])) echo '<hr />Posible file attack.<hr />';
					$this->set_error_code(5009);
					return false;
			}
			return false;
		}

		function download ($file, $filename){
			if (file_exists($file)){
				set_time_limit(86400);
				error_reporting('E_NONE');
				$str = file_get_contents($file);
				Header ("Content-Type: application/octet-stream");
		        Header ("Content-Length: ".strlen($str));
		        Header ("Content-Disposition: attachment; filename=".$filename);
		        echo $str;
		        exit();
			}
		}

		/**
		 * Create vivvo_file object coresponding to file typr
		 *
		 * @param string $filename
		 * @param string $dir
		 * @param string $root_dir
		 * @return vivvo_file
		 */
		function create_file ($filename, $dir, $root_dir = ''){
			$ext = $this->get_extension($filename);
			$class = $this->get_handler_class($ext);
			return new $class(null, $filename, $dir, $root_dir);
		}

		function set_handlers(){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$this->_handlers = $configuration->get_configuration_property_list('file_type');
			$this->set_file_type();
		}

		function has_handler($ext){
			if (is_array($this->_file_type) && !empty($this->_file_type)){
				if (key_exists($ext, $this->_file_type)){
					return true;
				}
			}
			return false;
		}

		function set_file_type(){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$this->_file_type = $configuration->get_configuration_property_list('files');
		}

		function get_file_type ($type){
			$ext = array ();
			if (is_array($type) && !empty($type)){
				foreach ($type as $t){
					if (is_array($this->_file_type) && !empty($this->_file_type)){
						foreach($this->_file_type as $k => $v){
							if ($v['type'] == $t){
								$ext[] = $k;
							}
						}
					}
				}
			}elseif (is_string($type)){
				if (is_array($this->_file_type) && !empty($this->_file_type)){
					foreach($this->_file_type as $k => $v){
						if ($v['type'] == $type){
							$ext[] = $k;
						}
					}
				}
			}
			return $ext;
		}

		function get_handlers($file_ext){
			if (is_array($this->_file_type)){
				if (key_exists($file_ext, $this->_file_type)){
					$type = $this->_file_type[$file_ext]['type'];
				}else{
					return false;
				}
			}else{
				return false;
			}

			if (is_array($this->_handlers)){
				if (key_exists($type, $this->_handlers)){
					return $this->_handlers[$type];
				}
			}
			return false;
		}

		function load_handler($type){
			if (is_array($this->_handlers)){
				if (key_exists($type, $this->_handlers)){
					if (file_exists(VIVVO_FS_INSTALL_ROOT . $this->_handlers[$type]['file'])){
						require_once(VIVVO_FS_INSTALL_ROOT . $this->_handlers[$type]['file']);
					}
				}
			}
			return false;
		}

		function get_handler_class($file_ext){
			if (is_array($this->_file_type)){
				if (key_exists($file_ext, $this->_file_type)){
					$type = $this->_file_type[$file_ext]['type'];
				}else{
					return 'vivvo_file';
				}
			}else{
				return 'vivvo_file';
			}
			if (is_array($this->_handlers)){
				if (key_exists($type, $this->_handlers)){
					$this->load_handler($type);
					return $this->_handlers[$type]['class_name'];
				}
			}
			return 'vivvo_file';
		}

		function assign_handler($ext, $type){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->add_conf($ext, 'type', $type, 'files');
		}

		function remove_handler($ext){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->remove_from_module('files', $ext);
		}

		function register_handler($type, $class_name, $file){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->add_conf($type, 'class_name', $class_name, 'file_type');
			$configuration->add_conf($type, 'file', $file, 'file_type');
		}

		function unregister_handler($type){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->remove_from_module('file_type', $type);
			$configuration->remove_from_module('files', '', $type);
		}

		function vivvo_file_manager ($site_manager){
			parent::vivvo_object($site_manager);
			$this->set_handlers();
		}
	}

	/**
	 * Vivvo file class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	file
	 * @version		$Revision: 5433 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	*/
	class vivvo_file extends vivvo_object {

		/**
		 * Directory name
		 *
		 * @var string
		 */
		var $dir;

		/**
		 * File name
		 *
		 * @var string
		 */
		var $filename;

		/**
		 * File extension
		 *
		 * @var string
		 */
		var $extension;

		/**
		 * File mime type
		 *
		 * @var string
		 */
		var $mime_type;

		/**
		 * File size bytes
		 *
		 * @var integer
		 */
		var $size;

		/**
		 * File comment
		 *
		 * @var string
		 */
		var $comment;

		/**
		 * File icon file
		 *
		 * @var string
		 */
		var $icon;

		/**
		 * File thumbnail link
		 *
		 * @var string
		 */
		var $thumbnail;

		var $type = 'file';

		var $time = 0;
		var $mtime = 0;

		var $changed;

		var $writeable;

		var $_root_dir = VIVVO_FS_ROOT;

		function set_size(){
			if (file_exists ($this->_root_dir . $this->dir . $this->filename)){
				$this->size = filesize($this->_root_dir . $this->dir . $this->filename);
			}
		}

		function set_extension(){
			if ($this->filename != ''){
				$this->extension = vivvo_file_manager::get_extension($this->filename);
			}
		}

		function set_icon (){
			$this->icon = VIVVO_FS_ADMIN_DIR . 'img/asset_file.gif';
		}

		function set_info(){
			$file = $this->_root_dir . $this->dir . $this->filename;
			if ($this->comment == '') $this->comment = basename($file, '.' . $this->extension);
		}

		function set_dir($dir){
			if (file_exists($this->_root_dir . $dir) && is_dir($this->_root_dir . $dir)){
				$this->dir = dir_list::cleanPath($dir);
				$this->dir = $dir;
				if (substr($dir, -1, 1) != '/' && substr($dir, -1, 1) != '\\'){
					$this->dir .= '/';
				}
			}
		}

		function set__root_dir($root_dir){
			if (file_exists($root_dir) && is_dir($root_dir)){
				$this->_root_dir = $root_dir;
				if (substr($this->_root_dir, -1, 1) != '/' && substr($this->_root_dir, -1, 1) != '\\'){
					$this->_root_dir .= '/';
				}
			}else{
				$this->_root_dir = VIVVO_FS_ROOT;
			}
		}

		function set_time(){
			if (file_exists ($this->_root_dir . $this->dir . $this->filename)){
				$this->time = filectime($this->_root_dir . $this->dir . $this->filename);
			}
		}

		function set_mtime(){
			if (file_exists ($this->_root_dir . $this->dir . $this->filename)){
				$this->mtime = filemtime($this->_root_dir . $this->dir . $this->filename);
			}
		}

		function set_changed(){
			if (file_exists($this->_root_dir . $this->dir . $this->filename . '.original')){
				if (md5_file($this->_root_dir . $this->dir . $this->filename . '.original') == md5_file($this->_root_dir . $this->dir . $this->filename)){
					$this->changed = 0;
				}else{
					$this->changed = 1;
				}
			}else{
				$this->changed = -1;
			}
		}

		function set_writeable(){
			$this->writeable = (int) is_writeable($this->_root_dir . $this->dir . $this->filename);
		}

		function has_subfolders(){
			if (is_dir($this->_root_dir . $this->dir . $this->filename)){
				if (glob(preg_quote($this->_root_dir . $this->dir . $this->filename . '/') . '*' , GLOB_ONLYDIR)){
					return true;
				}
			}
			return false;
		}

		/**
		 * This method return href
		 *
		 * @return string
		 */
		function get_href(){
			return VIVVO_URL . $this->dir . $this->filename;
		}

		function get_thumb_href() {
			$parts = pathinfo($this->filename);

			//if image, return thumb
			if (in_array( strtolower($parts['extension']), array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
				return VIVVO_STATIC_URL . 'thumbnail.php?file=' . preg_replace('|^' . VIVVO_FS_FILES_DIR . '/|', '', $this->dir) . $this->filename . '&size=thumbview';
			}

			//if icon exists return path to icon
			if (file_exists(VIVVO_FS_ROOT . ($icon_file = VIVVO_FS_ADMIN_DIR . 'img/mime_' . strtolower($parts['extension']) . '.gif'))) {
				return VIVVO_URL . $icon_file;
			}

			//otherwise return default icon
			return VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'img/icon_file.gif';
		}

		function get_dimensions() {

			$fullpath = $this->_root_dir . $this->dir . $this->filename;

			if ( !file_exists($fullpath) )
				return false;

			if( empty($this->extension) )
				$this->set_extension();

			// makes sense just for images
			if( in_array( $this->extension, array('jpg', 'jpeg', 'png', 'gif', 'bmp')) ) {
				$dim = getimagesize($fullpath);
				if( !empty($dim[0]) and !empty($dim[1]) ) {
					return  array(
						'width' => $dim[0],
						'height' => $dim[1],
					);
				}
			}

			return false;
		}


		function get_basename(){
			return vivvo_file_manager::get_basename($this->filename);
		}

		function get_extension(){
			return vivvo_file_manager::get_extension($this->filename);
		}

		function get_content(){
			return file_get_contents($this->_root_dir . $this->dir . $this->filename);
		}

		function get_html_content(){
			$content = file_get_contents($this->_root_dir . $this->dir . $this->filename);
			return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
		}

		function get_size($param = ''){
			if ($param == 'M'){
				return number_format($this->size / (1024 * 1024), 2, '.', ' ');
			}elseif($param == 'K'){
				return number_format($this->size / 1024, 2, '.', ' ');
			}else{
				return $this->size;
			}
		}

		function get_datetime() {
			$this->set_mtime();
			return date('Y-m-d H:i:s', $this->mtime);
		}

		function get_info(){
			return '';
		}

		function get_comment(){
			return $this->comment;
		}
		function vivvo_file ($site_manager, $filename, $dir, $root_dir){
			parent::vivvo_object($site_manager);
			$this->set__root_dir($root_dir);
			$this->set_dir($dir);
			$this->filename = $filename;
			$this->set_extension();
			$this->set_size();
			$this->set_icon();
			$this->set_info();
			$this->set_time();
			$this->set_mtime();
			$this->set_changed();
			$this->set_writeable();
		}
	}

	/**
	 * dir_info class
	 * Dir info class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	file
	 * @version		$Revision: 5433 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	*/
	class dir_list extends vivvo_object{
		/**
		 * Directory name
		 * @var string $dir
		 */
		var $dir;

		var $dirs = array();
		var $list = array();
		var $allowed_ext = array();

		/**
		 * Vivvo file manager
		 *
		 * @var vivvo_file_manager
		 */
		var $_file_manager;

		var $recursive = true;

		var $_root_dir = VIVVO_FS_ROOT;

		function cleanPath($path) {
			$path = str_replace('\\', '/', $path);
			$path = ereg_replace('\.+/', '/', $path);
			$path = ereg_replace('/+', '/', $path);
			return $path;
		}

		function set__file_manager(){
			$this->_file_manager = vivvo_lite_site::get_instance()->get_file_manager();
		}

		function set_dir($dir){
			$dir = $this->cleanPath($dir);
			if (file_exists($this->_root_dir . $dir) && is_dir($this->_root_dir . $dir)){
				$this->dir = $dir;
				if (substr($dir, -1, 1) != '/' && substr($dir, -1, 1) != '\\'){
					$this->dir .= '/';
				}
			}else{
				$this->dir = false;
			}
		}

		function set__root_dir($root_dir){
			if (file_exists($root_dir) && is_dir($root_dir)){
				$this->_root_dir = $root_dir;
				if (substr($this->_root_dir, -1, 1) != '/' && substr($this->_root_dir, -1, 1) != '\\'){
					$this->_root_dir .= '/';
				}
			}else{
				$this->_root_dir = VIVVO_FS_ROOT;
			}
		}

		function set_allowed_ext($allowed_ext){
			if (is_array($allowed_ext)){
				$this->allowed_ext = $allowed_ext;
			}else if (is_string($allowed_ext) && !empty($allowed_ext)){
				$this->allowed_ext = explode(',', $allowed_ext);
			}
		}

		/**
		 * Compare vivvo_files for sorting, frist by type, than by filenam
		 *
		 * @param vivvo_file $a
		 * @param vivvo_file $b
		 * @return integer
		 */
		function cmp_files($a, $b){
			if ($a->type == $b->type) {
				return strnatcasecmp ($a->filename, $b->filename);
			}
			if ($a->type == 'file') return +1;
			if ($b->type == 'file') return -1;
			return ($a->type > $b->type) ? +1 : -1;
		}

		function get_directory_name (){
			$dir = trim ($this->dir, '/');
			$dir = explode('/', $dir);
			return array_pop($dir);
		}

 		function &get_files(){
			if ($this->dir === false) return '';
			if (empty($this->allowed_ext)){
				$allowed_ext = true;
			}else{
				$allowed_ext = $this->allowed_ext;
			}

			$dir = $this->_root_dir . $this->dir;

			$i = 0;
			if ($handle = opendir($dir)) {
				while (false !== ($filename = readdir($handle))) {
	        		if ($filename != "." && $filename != ".." && !is_dir($dir . $filename)) {
	        			$ext = vivvo_file_manager::get_extension($filename);
	        			if ($allowed_ext === true || in_array($ext, $allowed_ext)){
	        				$this->list[] = $this->_file_manager->create_file($filename, $this->dir, $this->_root_dir);
	        			}
	        		}elseif ($filename[0] != "." && is_dir($dir . $filename)){
	        			if($this->recursive){
		        			$this->dirs[$i] = new dir_list(null, $this->dir . $filename . '/', $this->allowed_ext);
		        			$i++;
	        			}
	        		}
	        	}
	        }
	        usort($this->list, array("dir_list", "cmp_files"));
	        return $this->list;
		}

		function &get_dirs(){
			if ($this->dir === false) return '';
			$dir = $this->_root_dir . $this->dir;
			$i = 0;
			if (substr($dir, -1) != '/') $dir .= '/';
			$dirlen = strlen($dir);
			$dir = str_replace(array('*', '?', '['), array('[*]', '[?]', '[[]'), $dir);
			foreach (glob($dir . '*', GLOB_ONLYDIR | GLOB_NOSORT) as $filename) {
				if (strlen($filename) > 0 && $filename[0] != '.' && $filename[0] != '_') {
					if ($this->recursive) {
						$this->dirs[$i] = new dir_list($this->_site_manager, rtrim($filename, '/') . '/', $this->allowed_ext);
						$this->dirs[$i]->get_dirs();
						$i++;
					} else {
						$this->list[] = $this->_file_manager->create_file(rtrim(substr($filename, $dirlen), '/'), $this->dir);
					}
				}
			}

	        usort($this->list, array('dir_list', 'cmp_files'));
	        return $this->list;
		}

		function get_total_number($force = false, $recursive = true) {

			return $this->number_of_files_in_dir($this->_root_dir . $this->dir, $recursive);

		}

		function number_of_files_in_dir ($dir, $recursive = true){
			if (substr($dir, -1, 1) != '/' && substr($dir, -1, 1) != '\\'){
				$dir .= '/';
			}

			if (empty($this->allowed_ext)){
				$allowed_ext = true;
			}else{
				$allowed_ext = $this->allowed_ext;
			}
			$count = 0;
			if ($handle = @opendir($dir)) {
				while (false !== ($filename = readdir($handle))) {
	        		if ($filename != "." && $filename != ".." && !is_dir($dir . $filename)) {
	        			$ext = vivvo_file_manager::get_extension($filename);
	        			if (($allowed_ext === true || in_array($ext, $allowed_ext))){
        					$count++;
        				}
	        		}elseif ($filename[0] != "." && is_dir($dir . $filename)){
	        			if ($recursive) $count += $this->number_of_files_in_dir($dir . $filename, $recursive);
	        		}
	        	}
	        }
			return $count;
		}

		function get_total_file_size($param = '', $force = force, $recursive = true) {

			$size = $this->file_size_in_dir($this->_root_dir . $this->dir, $recursive);

			if ($param == 'M'){
				return number_format($size / (1024 * 1024), 2, '.', ' ');
			}elseif($param == 'K'){
				return number_format($size / 1024, 2, '.', ' ');
			}else{
				return $size;
			}
		}

		function file_size_in_dir($dir, $recursive = true){
			if (substr($dir, -1, 1) != '/' && substr($dir, -1, 1) != '\\'){
				$dir .= '/';
			}

			if (empty($this->allowed_ext)){
				$allowed_ext = true;
			}else{
				$allowed_ext = $this->allowed_ext;
			}
			$size = 0;
			if ($handle = @opendir($dir)) {
				while (false !== ($filename = readdir($handle))) {
	        		if ($filename != "." && $filename != ".." && !is_dir($dir . $filename)) {
	        			$ext = vivvo_file_manager::get_extension($filename);
	        			if (($allowed_ext === true || in_array($ext, $allowed_ext))){
        					$size += filesize($dir . $filename);
        				}
	        		}elseif ($filename[0] != "." && is_dir($dir . $filename)){
	        			if ($recursive)	$size += $this->file_size_in_dir($dir . $filename, $recursive);
	        		}
	        	}
	        }
			return $size;
		}


		function dir_list ($site_manager = null, $dir = '', $ext = '', $root_dir = '', $recursive = true){
			$this->set__root_dir($root_dir);
			$this->set_dir($dir);
			$this->set__file_manager();
			$this->set_allowed_ext($ext);
			$this->recursive = $recursive;
		}

	}//end_class

#EOF