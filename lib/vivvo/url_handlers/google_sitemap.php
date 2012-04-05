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


function format_date_atom($date){
	return date('Y-m-d\TH:i:s', strtotime($date)) . substr(date('O',strtotime($date)),0,3) . ':' . substr(date('O',strtotime($date)),3,2);
}


function google_sitemap_url_handler(&$sm, $url_array){

	if (!defined('VIVVO_PROXY_URL')){
		if (defined('VIVVO_SYSTEM_PROXY_URL')) {
			define('VIVVO_PROXY_URL', VIVVO_SYSTEM_PROXY_URL);
		} else {
			define('VIVVO_PROXY_URL', '');
		}
	}

	defined('VIVVO_ABSOLUTE_URL') or define('VIVVO_ABSOLUTE_URL', make_absolute_url(''));
	
	$template =& $sm->get_template();
	$template->assign('CURRENT_TIME',strval(date('Y-m-d\TH:i:s', VIVVO_START_TIME) . substr(date('O',VIVVO_START_TIME),0,3) . ':' . substr(date('O',VIVVO_START_TIME),3,2)));

	header("Content-type: text/xml; charset=UTF-8");
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/google_sitemap.tpl');

	echo $sm->get_output();
	exit;
}

?>