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

	function poll_url_handler(&$sm, $url_array){
		if (is_array ($url_array) && !empty($url_array)){
			if (is_numeric($url_array[1])){
				return (array('search_pid' => $url_array[1], 'url_module' => $url_array[0]));
				
			}
		}
		return (array('url_module' => $url_array[0]));
	}
	
	function poll_content_handler(&$sm){
		$sm->set_theme();
		$sm->_template->set_template_file(VIVVO_FS_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
		$content_template = new template($sm, $sm->_template);
		$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/poll.tpl');
		$sm->_template->assign_template('PAGE_CONTENT', $content_template);
	}
	
?>