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
	 * @see  '/../article_images.class.php'
 	 */
	require_once(dirname(__FILE__) . '/../article_images.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_post.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

	/**
	 * Plugin article images service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		vivvo_plugins
	 * @subpackage	image_gallery
	 * @see			vivvo_service
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class article_images_service extends vivvo_service {

		/**
		 * Add new images
		 *
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function add ($data){

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','image_gallery')){

					$article_images_list = new ArticleImages_list();

					$new_order_num = $article_images_list->get_max('order_number');

					if ($new_order_num){
						$new_order_num = $new_order_num + 1;
					}else{
						$new_order_num = 1;
					}

					foreach ($data as $key){

						$article_images = new ArticleImages();
						$key['order_number'] = $new_order_num;
						$new_order_num++;

						if ($article_images->populate($key, true) === false){
							return false;
						}

						$this->_post_master->set_data_object($article_images);
						if (!$this->_post_master->sql_insert()){
							$this->set_error_code(11201);
							return false;
						}
					}
					return true;
				}else{
					$this->set_error_code(11202);
					return false;
				}
			}else{
				$this->set_error_code(11203);
				return false;
			}
		}

		/**
		 * Edit image

		 * @param	integer	$image_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function edit_image ($image_id, $data){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','image_gallery')){
					$article_images_list = new ArticleImages_list();
					$image = $article_images_list->get_image_by_id($image_id);

					if ($image !== false){

						if ($image->populate($data, true) === false){
							return false;
						}

						$this->_post_master->set_data_object($image);
						if ($this->_post_master->sql_update()){
							return true;
						}else{
							$this->set_error_code(11204);
							return false;
						}
					}else{
						$this->set_error_code(11205);
						return false;
					}
				}else{
					$this->set_error_code(11206);
					return false;
				}
			}else{
				$this->set_error_code(11207);
				return false;
			}
		}

		/**
		 * Remove image.
		 *
		 * @param	integer	$image_id
		 * @return	boolean	true on succes, or false on fail
		 */
		function remove ($image_id){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','image_gallery')){
					$image_id = (int) $image_id;
					$article_images_list = new ArticleImages_list();
					$image = $article_images_list->get_image_by_id($image_id);
					if ($image){
						$this->_post_master->set_data_object($image);
						if ($this->_post_master->sql_delete()){
							return true;
						}else{
							$this->set_error_code(11208);
							return false;
						}
					}else{
						$this->set_error_code(11209);
						return false;
					}
				}else{
					$this->set_error_code(11210);
					return false;
				}
			}else{
				$this->set_error_code(11211);
				return false;
			}
		}

		/**
		 * Reorder images
		 * @param	integer	$article_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function reorder ($order){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','image_gallery')){
					$order_number = 1;
					foreach($order as $id){
						$article_images_list = new ArticleImages_list();
						$image =& $article_images_list->get_image_by_id($id);

						if ($image !== false){
							$image->set_order_number($order_number);
							$this->_post_master->set_data_object($image);

							if ($this->_post_master->sql_update()){
								$order_number++;
							}
						}
					}
					return true;
				}else{
					$this->set_error_code(11212);
					return false;
				}
			}else{
				$this->set_error_code(11213);
				return false;
			}
		}

	}
?>