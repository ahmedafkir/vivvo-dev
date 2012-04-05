<?php
/* =============================================================================
 * $Revision: 5035 $
 * $Date: 2010-04-19 17:13:50 +0200 (Mon, 19 Apr 2010) $
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
 * @package    Vivvo
 * @subpackage service
 * @author     Slobodan Utvic <utvara@spoonlabs.com>
 * @copyright  Spoonlbas
 * @license    http://www.vivvo.net/license.php
 * @version    0.1
 * @category   CMS
 */

	/**
	 * File service class
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage service
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	*/
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");

	class file_service extends vivvo_service {

		/**
		 * Save file
		 *
		 * @param string $filename
		 * @param string $content
		 * @return boolean
		 */
		function save($filename, $content){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('file_save', array(&$filename, &$content) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					if (file_exists(VIVVO_FS_ROOT . $filename)){
						if (is_writeable(VIVVO_FS_ROOT . $filename)){
							if (file_put_contents(VIVVO_FS_ROOT . $filename, $content)){
								chmod(VIVVO_FS_ROOT . $filename, 0666);
								admin_log($sm->user->get_username(), 'Saved file ' . $filename);
								return true;
							}else{
								$this->set_error_code(2801);
								return false;
							}
						}else{
							$this->set_error_code(2802);
							return false;
						}
					}else{
						$this->set_error_code(2803);
						return false;
					}
				}else {
					$this->set_error_code(2804);
					return false;
				}
			}else{
				$this->set_error_code(2805);
				return false;
			}
		}

		/**
		 * Revert file
		 *
		 * @param string $filename
		 * @return boolean
		 */
		function revert($filename){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('file_revert', array(&$filename) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					if (file_exists(VIVVO_FS_ROOT . $filename)){
						if (file_exists(VIVVO_FS_ROOT . $filename . '.original')){
							if (copy(VIVVO_FS_ROOT . $filename . '.original', VIVVO_FS_ROOT . $filename)){
								chmod(VIVVO_FS_ROOT . $filename, 0666);
								admin_log($sm->user->get_username(), 'Reverted file ' . $filename);
								return true;
							}else{
								$this->set_error_code(2806);
								return false;
							}
						}else{
							$this->set_error_code(2807);
							return false;
						}
					}else{
						$this->set_error_code(2808);
						return false;
					}
				}else {
					$this->set_error_code(2809);
					return false;
				}
			}else{
				$this->set_error_code(2810);
				return false;
			}
		}

		/**
		 * Duplicate file
		 *
		 * @param string $filename
		 * @return boolean
		 */
		function duplicate($filename, $new_name){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('file_duplicate', array(&$filename, &$new_name) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					if (file_exists(VIVVO_FS_ROOT . $filename)){
						$file_array = explode("/",$filename);
						$ext = explode(".",basename ($filename));
						$ext = end($ext);
						array_pop($file_array);
						foreach($file_array as $v)
							$str .= $v.'/';
						$new_name = $str . $new_name .'.'. $ext;
						if (copy(VIVVO_FS_ROOT . $filename, VIVVO_FS_ROOT . $new_name)){
							chmod(VIVVO_FS_ROOT . $new_name, 0666);
							admin_log($sm->user->get_username(), 'Duplicated file ' . $filename);
							return true;
						}else{
							$this->set_error_code(2811);
							return false;
						}
					}else{
						$this->set_error_code(2812);
						return false;
					}
				}else {
					$this->set_error_code(2813);
					return false;
				}
			}else{
				$this->set_error_code(2814);
				return false;
			}
		}

		function delete($filename){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('file_delete', array(&$filename) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is('ASSET_MANAGER') || $sm->user->can('MANAGE_FILES')) {
					if (file_exists(VIVVO_FS_ROOT . $filename)) {
						if (@unlink(VIVVO_FS_ROOT . $filename)) {
							admin_log($sm->user->get_username(), 'Deleted file ' . $filename);
							return true;
						} else {
							$this->set_error_code(2815);
							return false;
						}
					} else {
						$this->set_error_code(2816);
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

		function upload($control_name, $destination = '') {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('file_upload', array(&$control_name, &$destination))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($control_name != ''){
				if ($sm->user){
					if($sm->user->can('UPLOAD')){
						$fm = $sm->get_file_manager();
						if ($fm->is_uploaded($control_name)){
							$uploaded = $fm->upload_to($control_name, $destination, VIVVO_ALLOWED_EXTENSIONS);
							if ($uploaded != false) {
								admin_log($sm->user->get_username(), 'Uploaded file ' . $uploaded);
								return $uploaded;
							} else {
								$this->set_error_code(2819);
								return false;
							}
						} else {
							$this->set_error_code(2820);
							return false;
						}
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

		function move($filename, $destination){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('file_move', array(&$filename, &$destination) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is('ASSET_MANAGER') || $sm->user->can('MANAGE_FILES')){
					if (file_exists(VIVVO_FS_ROOT . $filename)){
						if (file_exists(VIVVO_FS_ROOT . $destination) && is_dir(VIVVO_FS_ROOT . $destination)){
							if (substr(VIVVO_FS_ROOT . $destination, -1, 1) != '/' && substr(VIVVO_FS_ROOT . $destination, -1, 1) != '\\'){
								$destination .= '/';
							}
							$file = basename(VIVVO_FS_ROOT . $filename);
							if (@rename(VIVVO_FS_ROOT . $filename, VIVVO_FS_ROOT . $destination . $file)){
								admin_log($sm->user->get_username(), 'Moved file ' . $filename . ' to ' . $destination);
								return true;
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
				}else {
					$this->set_error_code(2826);
					return false;
				}
			}else{
				$this->set_error_code(2827);
				return false;
			}
		}
	}
?>