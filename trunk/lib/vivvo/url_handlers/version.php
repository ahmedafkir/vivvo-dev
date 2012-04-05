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

function version_url_handler(&$sm, $url_array){
	header("Content-type: text/plain; charset=UTF-8");
	echo "Vivvo CMS Version ". VIVVO_VERSION . " revision " . VIVVO_CODE_REVISION ."

Copyright Spoonlabs d.o.o.
www.spoonlabs.com
--------------------------";
	exit;
}
?>