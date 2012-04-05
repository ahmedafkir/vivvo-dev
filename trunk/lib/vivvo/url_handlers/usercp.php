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

function usercp_url_handler(&$sm, $url_array){
	if (VIVVO_USER_SOURCE != 'vivvo@localhost'){
		go_404();
	}
	return array('url_module' => 'usercp.html');
}

function usercp_content_handler(&$sm){
	$template = $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');

	$um = $sm->get_url_manager();
	$dm = $sm->get_dump_manager();

	$dm->restore_dump(md5(VIVVO_PROXY_URL . 'usercp.html'));

	$template->assign('DUMP_MESSAGES', $dm->_dump_pool);

	$content_template = new template($sm, $template);
	$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/usercp.tpl');
	$template->assign('PAGE_CONTENT', $content_template->get_output());
}

?>