<?php
/* =============================================================================
 * $Revision: 5385 $
 * $Date: 2010-05-25 11:51:09 +0200 (Tue, 25 May 2010) $
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
 * @subpackage	service
 * @version		$Revision: 5385 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */

	/**
 	 * @see  'vivvo_post.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

	/**
	 * vivvo_service class
	 * Generic service class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	service
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	*/
	class vivvo_service extends vivvo_object{

		/**
		 * Site manager
		 *
		 * @access protected
		 * @var vivvo_list_site
		 */
		var $_site_manager;

		/**
		 * DB post master
		 *
		 * @access protected
		 * @var vivvo_post_master
		 */
		var $_post_master;

		/**
		 * Loged user
		 *
		 * @var user
		 */
		var $_user;

		/**
		 * Enter description here...
		 *
		 * @param vivvo_site $sm
		 * @return vivvo_service
		 */
		function vivvo_service($sm = null){
			$this->_post_master = new vivvo_post_master();
			$this->_user = vivvo_lite_site::get_instance()->get_user();
		}

		function check_token(){
			if (!vivvo_lite_site::get_instance()->check_token()){
				//TODO: error number
				$this->set_error_code(2121);
				return false;
			}
			return true;
		}
	}

#EOF