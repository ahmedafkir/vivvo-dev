<?php

/* =============================================================================
 * $Revision: 5418 $
 * $Date: 2010-05-28 12:09:06 +0200 (Fri, 28 May 2010) $
 *
 * Vivvo CMS v4.1.6 (build 4214)
 * Copyright (c) 2010, Spoonlabs d.o.o.
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
	 * Vivvo CMS
	 *
	 * LICENSE:
	 *
	 * @package    Vivvo
	 * @subpackage service
	 * @author     Slobodan Utvic <utvara@spoonlabs.com>
	 * @copyright  Spoonlbas
	 * @license    http://www.vivvo.net/license.php
	 * @version    0.1
	 * @category   CMS
	 */

	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");
	require_once(dirname(__FILE__) . '/file.service.php');
	require_once(VIVVO_FS_FRAMEWORK . 'asset_files.class.php');

	/**
	 * Asset files service class
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage service
	 * @author Ivan Dilber <idilber@spoonlabs.com>
	*/
	class assets_service extends vivvo_service {

		function delete($filename) {
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('asset_delete', array(&$filename) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is('ASSET_MANAGER') || $sm->user->can('MANAGE_FILES')){

					if (file_exists(VIVVO_FS_ROOT . $filename)) {

						if (@unlink(VIVVO_FS_ROOT . $filename)) {
							admin_log($sm->user->get_username(), 'Deleted file ' . $filename);

						}else{
							$this->set_error_code(2815);
							return false;
						}
					}else{
						$this->set_error_code(2816);
						return false;
					}

					//TODO: provera referenci u bazi, i brisanje iz artikala
					//TODO: confirmation dijalog oko ovog gore

					$asset_list = new AssetFiles_list();
					$asset_list->get_asset_by_fullname($filename);

					if ($asset_list->sql_delete_list($this->_post_master)) {
						return true;
					} else {
						$this->set_error_code(2815);
						return false;
					}
				} else {
					$this->set_error_code(2817);
					return false;
				}
			} else {
				$this->set_error_code(2818);
				return false;
			}

		}

		function move($filename, $destination) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('asset_move', array(&$filename, &$destination))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if ($sm->user->is('ASSET_MANAGER') || $sm->user->can('MANAGE_FILES')){

					//just in case
					if (strpos($destination, '..') !== false) {
						$this->set_error_code(2828);
						return false;
					}

					if (file_exists(VIVVO_FS_ROOT . $filename)) {
						if (file_exists(VIVVO_FS_ROOT . $destination) && is_dir(VIVVO_FS_ROOT . $destination)) {
							if (substr(VIVVO_FS_ROOT . $destination, -1, 1) != '/' && substr(VIVVO_FS_ROOT . $destination, -1, 1) != '\\') {
								$destination .= '/';
							}
							$file = basename(VIVVO_FS_ROOT . $filename);
							if (@rename(VIVVO_FS_ROOT . $filename, VIVVO_FS_ROOT . $destination . $file)) {
								admin_log($sm->user->get_username(), 'Moved file ' . $filename . ' to ' . $destination);
							}else{
								$this->set_error_code(2823);
								return false;
							}
						}else{
							$this->set_error_code(2824);
							return false;
						}
					}else{
						$this->set_error_code(2825);
						return false;
					}

					$asset_list = new AssetFiles_list();
					$file = $asset_list->get_asset_by_fullname($filename);
					$file->set_path($destination);
					$file->set_path_md5(md5($destination));

					$this->_post_master->set_data_object($file);

					if ($this->_post_master->sql_update()) {
						return true;
					} else {
						$this->set_error_code(2823);
						return false;
					}
				} else {
					$this->set_error_code(2826);
					return false;
				}
			} else {
				$this->set_error_code(2827);
				return false;
			}
		}

		function rename($filename, $new_name) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('asset_rename', array(&$filename, &$new_name))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if($sm->user->is('ASSET_MANAGER') || $sm->user->can('MANAGE_FILES')) {

					//just in case
					if (strpos($new_name, '/') !== false) {
						$this->set_error_code(2828);
						return false;
					}

					$full_old = VIVVO_FS_ROOT . $filename;
					$dir = dirname($full_old) . '/';
					$ext = substr($filename, strrpos($filename, '.'));
					$full_new = $dir . $new_name . $ext;

					if (file_exists($full_old)){

						if (file_exists($full_new)) {
							$this->set_error_code(2829);
							return;
						}

						if (@rename($full_old, $full_new)) {
							admin_log($sm->user->get_username(), 'Renamed file ' . $filename . ' to ' . $new_name);
						} else {
							$this->set_error_code(2823);
							return false;
						}
					} else {
						$this->set_error_code(2824);
						return false;
					}

					$asset_list = new AssetFiles_list();
					$file = $asset_list->get_asset_by_fullname($filename);
					$file->set_name($new_name);

					$this->_post_master->set_data_object($file);

					if ($this->_post_master->sql_update()) {
						return true;
					} else {
						$this->set_error_code(2823);
						return false;
					}
				} else {
					$this->set_error_code(2826);
					return false;
				}
			} else {
				$this->set_error_code(2827);
				return false;
			}
		}

		function upload($control_name, $destination = '') {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('asset_upload', array(&$control_name, &$destination))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($control_name != '') {
				if ($sm->user) {
					if($sm->user->can('UPLOAD')) {
						$fm = $sm->get_file_manager();
						if ($fm->is_uploaded($control_name)){
							$uploaded = $fm->upload_to($control_name, $destination, VIVVO_ALLOWED_EXTENSIONS);
							if ($uploaded != false){
								admin_log($sm->user->get_username(), 'Uploaded file ' . $uploaded);
							}else{
								$this->set_error_code(2819);
								return false;
							}
						}else{
							$this->set_error_code(2820);
							return false;
						}

						if (!preg_match('|^' . preg_quote(VIVVO_FS_FILES_DIR, '|') . '|', $uploaded)) {
							$uploaded = VIVVO_FS_FILES_DIR . $uploaded;
						}

						$newfile = new AssetFiles();
						if ($newfile->populate_from_file($uploaded) === false) {
							$this->set_error_code(2835);
							return false;
						}

						//read meta data
						$newfile->read_info_from_file();

						$this->_post_master->set_data_object($newfile);

						if (!$this->_post_master->sql_insert()) {
							$this->set_error_code(2835);
							return false;
						}

						$this->mark_scanned(dirname($uploaded));

						return $uploaded;
					}else{
						$this->set_error_code(2821);
						return false;
					}
				}else{
					$this->set_error_code(2822);
					return false;
				}
			}
		}

		function edit($asset_id, $data) {

			if (!vivvo_hooks_manager::call('asset_edit', array(&$asset_id, &$data))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if($sm->user->is('ASSET_MANAGER') || $sm->user->can('MANAGE_FILES')) {

					$asset_list = new AssetFiles_list();
					$file = $asset_list->get_asset_by_id($asset_id);

					if ($file !== false) {

						if (empty($data['filename'])) {
							$this->set_error_code(2839);
							return false;
						}

						//rename file
						if ($data['filename'] != $file->get_name()) {
							//just in case
							if (strpos($data['filename'], '/') !== false) {
								$this->set_error_code(2828);
								return false;
							}

							$full_old = VIVVO_FS_ROOT . $file->get_fullname();
							$dir = dirname($full_old) . '/';
							$ext = $file->get_extension();
							$full_new = $dir . $data['filename'] . '.' . $ext;

							if (file_exists($full_old)) {

								if (file_exists($full_new)) {
									$this->set_error_code(2829);
									return false;
								}

								if (@rename($full_old, $full_new)) {
									$data['name'] = $data['filename'];
									admin_log($sm->user->get_username(), 'Renamed file ' . $file->get_fullname() . ' to ' . $file->get_path() . $data['filename'] . '.' . $ext);
								} else {
									$this->set_error_code(2823);
									return false;
								}
							} else {
								$this->set_error_code(2824);
								return false;
							}
						}

						//save keywords
						if (isset($data['keywords'])) {
							$keywords = explode(',', $data['keywords']);
							$db = $sm->get_db();
							$db->exec("DELETE FROM ". VIVVO_DB_PREFIX ."asset_keywords WHERE asset_id=$asset_id");
							foreach($keywords as $key) {
								$db->exec("INSERT INTO ". VIVVO_DB_PREFIX ."asset_keywords (asset_id, keyword) VALUES ($asset_id, '" . secure_sql( trim($key) ) . "')");
							}
						}
						unset($data['keywords']);

						if ($file->populate($data, true) === false){
							$this->set_error_info($file->get_error_info());
							return false;
						}

						$this->_post_master->set_data_object($file);
						if ($this->_post_master->sql_update()){
							return true;
						}else {
							$this->set_error_code(2835);
							return false;
						}
					}else {
						$this->set_error_code(2808);
						return false;
					}
				}else {
					$this->set_error_code(2821);
					return false;
				}
			}else{
				$this->set_error_code(2822);
				return false;
			}
		}


		function scan_dir($path, $recursive = false, $dump = false) {

			if (!vivvo_hooks_manager::call('asset_scan_dir', array(&$path, &$recursive, &$dump))) {
				return vivvo_hooks_manager::get_status();
			}

			if (substr($path, -1) != '/') {
				$path .= '/';
			}

			$fullpath = VIVVO_FS_ROOT .$path;

			if (!file_exists($fullpath) or !is_dir($fullpath)) {
				$this->set_error_code(2833);
				return false;
			}

			$db = vivvo_lite_site::get_instance()->get_db();

			//get filetypes first
			$res = $db->query("SELECT id, extensions FROM ". VIVVO_DB_PREFIX ."asset_file_types");
			if (PEAR::isError($res)) {
				$this->set_error_code(2838);
				return false;
			}

			$filetypes = array();
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				$extensions = explode(',', $row['extensions']);
				foreach($extensions as $e)
					$filetypes[$e] = $row['id'];
			}
			$res->free();

			if (!isset($filetypes['.dirext'])) {
				$filetypes['.dirext'] = VIVVO_ASSETS_DIRTYPE_ID;
			}

			$res = $db->query("SELECT id, name, extension, mtime, size
							   FROM ".VIVVO_DB_PREFIX."asset_files
							   WHERE path_md5='".md5($path)."'");
			if (PEAR::isError($res)) {
				$this->set_error_code(2838);
				return false;
			}

			if (!$dump) {
				set_time_limit(600); //just in case
			}

			$assets = array();
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				$filename = $row['name'] .'.'. $row['extension'];
				$assets[ $filename ] = $row;
			}
			$res->free();
			if (!$handle = opendir($fullpath)) {
				$this->set_error_code(2837);
				return false;
			}

			//prepare insert
			$sql = 'INSERT INTO '. VIVVO_DB_PREFIX .'asset_files
					(name, extension, path, path_md5, size, filetype_id, mtime)
					VALUES (?, ?, ?, ?, ?, ?, ?)';

			$sth_ins = $db->prepare( $sql, array('text','text','text','text','integer','integer','timestamp'), MDB2_PREPARE_MANIP);

			if (PEAR::isError($sth_ins)) {
				$this->set_error_code(2838);
				return false;
			}

			//prepare update
			$sql = 'UPDATE '. VIVVO_DB_PREFIX .'asset_files
					SET size=?, filetype_id=?, mtime=?
					WHERE id=?';

			$sth_upd = $db->prepare($sql, array('integer', 'integer', 'timestamp', 'integer'), MDB2_PREPARE_MANIP);
			if (PEAR::isError($sth_upd)) {
				$this->set_error_code(2838);
				return false;
			}

			$path_md5 = md5($path);

			while (false !== ($file = readdir($handle))) {

				$fullname = $fullpath . $file;

				// skip hidden files
				if ($file[0] == '.') continue;

				$filestats = stat($fullname); // get size and mtime

				$key = ($dir = is_dir($fullname)) ? $file . '..dirext' : $file;

				if( isset($assets[$key]) ) { //if file in db

					unset($assets[$key]);

					if( $filestats['mtime'] == strtotime($assets[$key]['mtime']) ) {
						// file hasn't been changed, do nothing
						continue;
					}

					$ext = $assets[$key]['extension'];
					$ext_lower = strtolower($ext);
					$ftype = isset($filetypes[$ext_lower])? $filetypes[$ext_lower] : 0;

					// otherwise file has been changed, update the record
					$rez = $sth_upd->execute(array(
								$filestats['size'],
								$ftype,
								date('Y-m-d H:i:s', $filestats['mtime']),
								$assets[$key]['id']
					));

					if ($dump) echo '<br />Skipped: '. $fullname;
				} else {

					if ($dir) {
						$name = $file;
						$ext_lower = $ext = '.dirext';
						$filestats['size'] = 0;
						if ($dump) echo '<br /><b>Scanned directory</b>: '. $fullname;
					} else {
						preg_match('/^(.+)\.([\w\-]+)$/', $file, $parts);
						$name = $parts[1];
						$ext = $parts[2];
						$ext_lower = strtolower($ext);
						if ($dump) echo '<br /><b>Scanned file</b> [' . $filestats['size'] . ']: '. $fullname;
					}

					if ($name && $ext) {

						$ftype = isset($filetypes[$ext_lower]) ? $filetypes[$ext_lower] : 0;
						// new file, insert it
						$res=$sth_ins->execute( array( $name, $ext, $path, $path_md5, $filestats['size'],
							$ftype, date('Y-m-d H:i:s', $filestats['mtime'])));
					}
				}
				if ($recursive && $dir) {
					$this->scan_dir($path . $file, true, $dump);
				}
			} // end_while

			$sth_upd->free();
			$sth_ins->free();
			closedir($handle);

			if($assets) {// delete records for files that don't exist anymore

				$ids = array();
				foreach($assets as $record) {
					$ids[] = $record['id'];
				}

				if ($ids) {
					$id_list = implode(',', $ids);
					$db->exec('DELETE FROM '. VIVVO_DB_PREFIX ."asset_files
								WHERE id IN ($id_list)");
					$db->exec('DELETE FROM '. VIVVO_DB_PREFIX ."asset_keywords
							  WHERE asset_id IN ($id_list)");
				}
			}

			//mark dir as scanned
			$this->mark_scanned($path);

			return true;
		}

		function mark_scanned($path) {

			if (!vivvo_hooks_manager::call('asset_mark_scanned', array(&$path))) {
				return vivvo_hooks_manager::get_status();
			}

			$path = rtrim($path, '/');
			$flagfile = VIVVO_FS_ROOT . $path . '/.htstampdir';
			if (file_exists($flagfile)) {
				@unlink($flagfile);
			}

			touch($flagfile);
		}

		function new_dir($current, $newdir) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('asset_new_dir', array(&$current, &$newdir))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if ($sm->user->is('ASSET_MANAGER') || $sm->user->can('MANAGE_FILES')) {

					//just in case
					if( strpos($newdir, '../') !== false ) {
						$this->set_error_code(2828);
						return false;
					}

					$newdir = preg_replace('/[^\w\.\-\+]+/', '_', $newdir);
					if( !$newdir = trim($newdir, '_')) {
						$this->set_error_code(2828);
						return false;
					}

					$full_old = VIVVO_FS_ROOT . ($current = rtrim($current, '/') . '/');

					$full_new = $full_old . $newdir;

					if (is_dir($full_old)) {

						if (file_exists($full_new)) {
							$this->set_error_code(2829);
							return false;
						}

						if (mkdir($full_new)) {
							chmod($full_new, 0777);
							admin_log($sm->user->get_username(), 'Created directory ' . $newdir . ' in ' . $current);

							//update flag file, to avoid extra scanning
							$this->mark_scanned($current);

							$db = $sm->get_db();

							$sql = 'INSERT INTO '. VIVVO_DB_PREFIX .'asset_files
									(name, extension, path, path_md5, size, filetype_id, mtime)
									VALUES (?, ?, ?, ?, ?, ?, ?)';

							$sth_ins = $db->prepare($sql, array('text','text','text','text','integer','integer','timestamp'), MDB2_PREPARE_MANIP);

							if (PEAR::isError($sth_ins)) {
								$this->set_error_code(2838);
								return false;
							}

							$sth_ins->execute(array(rtrim($newdir, '/'), '.dirext', $current, md5($current), 0,
							VIVVO_ASSETS_DIRTYPE_ID, date('Y-m-d G:i:s', filemtime($full_new)) ));

							return true;
						}else{
							$this->set_error_code(2830);
							return false;
						}
					}else{
						$this->set_error_code(2824);
						return false;
					}
				}else {
					$this->set_error_code(2826);
					return false;
				}
			}else{
				$this->set_error_code(2827);
				return false;
			}
		}

		function rm_dir($dir) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('asset_delete_dir', array(&$dir))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is('ASSET_MANAGER') or $sm->user->can('MANAGE_FILES')) {
					$fulldir = VIVVO_FS_ROOT . $dir;
					if( substr($fulldir, -1)!='/')
						$fulldir .= '/';

					if (file_exists($fulldir) and is_dir($fulldir) and $files = @scandir($fulldir) ){

						//quick check if not empty
						if (count($files) > 3) {
							$this->set_error_code(2832);
							return false;
						}

						foreach($files as $f) { //check if any files inthere
							if( $f!='.' and $f!='..' and $f!='.htstampdir') {
								$this->set_error_code(2832);
								return false;
							}
						}

						//remove flagfile, if any
						@unlink($fulldir . '.htstampdir');

						// try to delete
						if (@rmdir($fulldir)) {
							admin_log($sm->user->get_username(), 'Removed directory ' . $fulldir);

							$parts = explode('/', $dir);
							$name = secure_sql(end($parts));
							array_pop($parts);
							$path = md5(implode('/', $parts) . '/');
							$sm->get_db()->exec('DELETE FROM '. VIVVO_DB_PREFIX ."asset_files WHERE path_md5 = '$path' AND name = '$name'");

							return true;
						} else {
							$this->set_error_code(2831);
							return false;
						}
					} else {
						$this->set_error_code(2836);
						return false;
					}

				} else {
					$this->set_error_code(2826);
					return false;
				}
			}else{
				$this->set_error_code(2827);
				return false;
			}

		}

	}//end_class


#EOF