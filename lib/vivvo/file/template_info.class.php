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
 * @package    Vivvo
 * @subpackage file
 * @author     Slobodan Utvic <utvara@spoonlabs.com>
 * @copyright  Spoonlbas
 * @license    http://www.vivvo.net/license.php
 * @version    0.1
 * @category   CMS
 */

	/**
	 * template_info class
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage file
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	*/
	Class template_info extends vivvo_file{
		var $type = 'template';

		function set_icon (){
			if (file_exists(VIVVO_FS_TEMPLATE_ROOT . $this->dir . $this->filename . '.gif')){
				$this->icon = $this->dir . $this->filename . '.gif';
			}else{
				$this->icon = VIVVO_FS_ADMIN_DIR . 'img/asset_image.gif';
			}
		}
	}

#EOF