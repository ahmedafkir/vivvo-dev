<?php
/* =============================================================================
 * $Revision: 5442 $
 * $Date: 2010-06-04 10:21:14 +0200 (Fri, 04 Jun 2010) $
 *
 * Vivvo CMS v4.5 (build 5661)
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
	if (version_compare(PHP_VERSION, '5.0.0') < 0) {
    	die('<p>Bummer. You need at least PHP 5.0 (you have '.PHP_VERSION.' at he moment) to run Vivvo CMS. Please contact your Server Administrator.</p>');
	}elseif(!date_default_timezone_get()){
		die('<p>Bummer. You must set Default Timezone on your server to run Vivvo CMS. Please contact your Server Administrator.</p>');
	}else{
		require_once (dirname(__FILE__) . '/index_5.php');
	}
?>