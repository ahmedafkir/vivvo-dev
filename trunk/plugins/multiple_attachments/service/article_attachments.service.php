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
	 * @see  '/../article_attachments.class.php'
 	 */
	require_once(dirname(__FILE__) . '/../ArticleAttachments.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_post.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

	/**
	 * Plugin article attachments service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		vivvo_plugins
	 * @subpackage	attachment_gallery
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class article_attachments_service extends vivvo_service {

		/**
		 * Add new attachments
		 *
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function add ($data){

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','multiple_attachments')){

					$article_attachments_list = new ArticleAttachments_list();

					$new_order_num = $article_attachments_list->get_max('order_number');

					if ($new_order_num){
						$new_order_num = $new_order_num + 1;
					}else{
						$new_order_num = 1;
					}

					foreach ($data as $key){

						$article_attachments = new ArticleAttachments();
						$key['order_number'] = $new_order_num;
						$new_order_num++;

						if ($article_attachments->populate($key, true) === false){
							return false;
						}

						$this->_post_master->set_data_object($article_attachments);
						if (!$this->_post_master->sql_insert()){
							$this->set_error_code(11301);
							return false;
						}
					}
					return true;
				}else{
					$this->set_error_code(11302);
					return false;
				}
			}else{
				$this->set_error_code(11303);
				return false;
			}
		}

		/**
		 * Edit attachment
		 * @param	integer	$attachment_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function edit_attachment ($attachment_id, $data){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','multiple_attachments')){
					$article_attachments_list = new ArticleAttachments_list();
					$attachment = $article_attachments_list->get_attachment_by_id($attachment_id);

					if ($attachment !== false){

						if ($attachment->populate($data, true) === false){
							return false;
						}

						$this->_post_master->set_data_object($attachment);
						if ($this->_post_master->sql_update()){
							return true;
						}else{
							$this->set_error_code(11304);
							return false;
						}
					}else{
						$this->set_error_code(11305);
						return false;
					}

				}else{
					$this->set_error_code(11306);
					return false;
				}
			}else{
				$this->set_error_code(11307);
				return false;
			}
		}

		/**
		 * Remove attachment.
		 *
		 * @param	integer	$attachment_id
		 * @return	boolean	true on succes, or false on fail
		 */
		function remove ($attachment_id){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','multiple_attachments')){
					$attachment_id = (int) $attachment_id;
					$article_attachments_list = new ArticleAttachments_list();
					$attachment = $article_attachments_list->get_attachment_by_id($attachment_id);
					if ($attachment){
						$this->_post_master->set_data_object($attachment);
						if ($this->_post_master->sql_delete()){
							return true;
						}else{
							$this->set_error_code(11308);
							return false;
						}
					}else{
						$this->set_error_code(11309);
						return false;
					}
				}else{
					$this->set_error_code(11310);
					return false;
				}
			}else{
				$this->set_error_code(11311);
				return false;
			}
		}

		/**
		 * Reorder attachments
		 * @param	integer	$article_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function reorder ($order){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user->can('MANAGE_PLUGIN','multiple_attachments')){
					$order_number = 1;
					foreach($order as $id){
						$article_attachments_list = new ArticleAttachments_list();
						$attachment = $article_attachments_list->get_attachment_by_id($id);

						if ($attachment !== false){
							$attachment->set_order_number($order_number);
							$this->_post_master->set_data_object($attachment);

							if ($this->_post_master->sql_update()){
								$order_number++;
							}
						}
					}
					return true;
				}else{
					$this->set_error_code(11312);
					return false;
				}
			}else{
				$this->set_error_code(11313);
				return false;
			}
		}

	}
?>