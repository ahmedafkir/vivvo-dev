<?php
/* =============================================================================
 * $Revision: 5127 $
 * $Date: 2010-04-26 10:03:56 +0200 (Mon, 26 Apr 2010) $
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
	 * @see  'lib/vivvo/core/Pages.class.php'
 	 */
	require_once(dirname(__FILE__) . '/../Pages.class.php');
	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");


	/**
	 * Page service class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	class page_service extends vivvo_service {

		/**
		 * Add new page
		 *
		 * @param	array	$in_page
		 * @return	boolean	true on success or false on fail
		 */
		function add_page($in_page){
			if (!$this->check_token()) return false;

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user && $sm->user->can('MANAGE_PLUGIN','pages')){
				$page_list = new Pages_list();
				$new_order_num = $page_list->get_max('order_number');

                $in_page['title'] = htmlspecialchars($in_page['title'], ENT_QUOTES, 'UTF-8');

                if ($new_order_num){
					$in_page['order_number'] = $new_order_num + 1;
				}else{
					$in_page['order_number'] = 1;
				}

				if (empty($in_page['sefriendly']) or !strlen(trim($in_page['sefriendly']))) {
					$in_page['sefriendly'] = $in_page['title'];
				}


                $sefriendly_url = strtolower(preg_replace('/[^a-z\d_]/i', '-', $in_page['sefriendly']));
                $sefriendly_url = preg_replace('/-+/', '-', $sefriendly_url);
                $sefriendly_url = trim($sefriendly_url, '-');
                $in_page['sefriendly'] = $sefriendly_url;

                if ($sm->is_registered_url($in_page['sefriendly'] . '.html')){
                    $this->set_error_code(2601);
                    return false;
                }

				$page = new Pages();

				if ($page->populate($in_page, true) === false){
					return false;
				}

				$um = $sm->get_url_manager();
				$um->register_url($in_page['sefriendly'] . '.html', 'plugins/pages/page_url_handler.php', 'page_url_handler', 'page_content_handler');

				$this->_post_master->set_data_object($page);
				if ($this->_post_master->sql_insert()){

					$um->set_param('search_pid', $this->_post_master->get_work_id());

					return true;
				}else{
					$this->set_error_code(2602);
					return false;
				}
			}else{
				$this->set_error_code(2603);
				return false;
			}
		}

		/**
		 * Delete page
		 *
		 * @param	integer	$page_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_page($page_id){
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					if ($page_id){
						$page_list = new Pages_list();
						$page = $page_list->get_pages_by_id($page_id);
						if ($page){
							if ($page_list->sql_delete_list($this->_post_master)){
								$um = $sm->get_url_manager();
								$um->unregister_url($page->sefriendly . '.html');

								return true;
							}else{
								$this->set_error_code(2604);
								return false;
							}
						}else{
							$this->set_error_code(2605);
							return false;
						}
					}else{
						$this->set_error_code(2610);
						return false;
					}
				}else{
					$this->set_error_code(2606);
					return false;
				}
			}else{
				$this->set_error_code(2607);
				return false;
			}
		}

		/**
		 * Edit page
		 *
		 * @param	integer	$page_id
		 * @param	array	$data
		 * @return	boolean	true on success or false on fail
		 */
		function edit_page($page_id, $data) {
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if($sm->user->can('MANAGE_PLUGIN','pages')) {
					$page_list = new Pages_list();

                    $data['title'] = htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8');

					if (empty($data['sefriendly']) or !strlen(trim($data['sefriendly']))) {
						$data['sefriendly'] = $data['title'];
					}

					if (!empty($data['sefriendly'])) {
						$sefriendly_url = strtolower(preg_replace('/[^a-z\d_]/i', '-', $data['sefriendly']));
						$sefriendly_url = preg_replace('/-+/', '-', $sefriendly_url);
						$sefriendly_url = trim($sefriendly_url, '-');
						$data['sefriendly'] = $sefriendly_url;
                    }

					$page_list = new Pages_list();
					$page = $page_list->get_pages_by_id($page_id);

					if ($page !== false) {
                        
                        if ($page->sefriendly != $data['sefriendly']) {
                            if ($sm->is_registered_url($data['sefriendly'] . '.html')){
                                $this->set_error_code(2608);
                                return false;
                            }
                        }

						if (!empty($page->sefriendly) && $page->sefriendly != $data['sefriendly']) {
							$um = $sm->get_url_manager();
							$um->unregister_url($page->sefriendly . '.html');
							$um->register_url($data['sefriendly'] . '.html', 'plugins/pages/page_url_handler.php', 'page_url_handler', 'page_content_handler');
						}

						if ($page->populate($data, true) === false){
							return false;
						}

						$this->_post_master->set_data_object($page);
						if ($this->_post_master->sql_update()){
							return true;
						} else {
							$this->set_error_code(2609);
							return false;
						}
					} else {
						$this->set_error_code(2610);
						return false;
					}
				} else {
					$this->set_error_code(2611);
					return false;
				}
			} else{
				$this->set_error_code(2612);
				return false;
			}
		}

		/**
		 * Reorder pages
		 *
		 * @param	array	$pages_ids
		 * @return	boolean	true on success or false on fail
		 */
		function reorder_pages($pages_ids){
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->can('MANAGE_PLUGIN','pages')){
					$pages_list_ids = $pages_ids;
					$pages_list = new Pages_list();
					$pages_list->search(array('search_id'=>$pages_ids),'order_number','descending');
					$order_nums = $pages_list->get_property_list('order_number');

					foreach($pages_list_ids as $k){
						$new_order = array_shift($order_nums);
						$pages_list->list[$k]->set_order_number($new_order);
						$this->_post_master->set_data_object($pages_list->list[$k]);
						if (!$this->_post_master->sql_update()){
							$this->set_error_code(2613);
							return false;
						}
					}
					return true;
				}else{
					$this->set_error_code(2614);
					return false;
				}
			}else{
				$this->set_error_code(2615);
				return false;
			}
		}
	}
?>