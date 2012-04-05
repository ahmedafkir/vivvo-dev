<?php
/* =============================================================================
 * $Revision: 5156 $
 * $Date: 2010-04-26 14:26:38 +0200 (Mon, 26 Apr 2010) $
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

	require_once (dirname(__FILE__) . '/../admin_include.php');

	header("Content-Type: application/x-javascript");

	echo 'vivvo.admin.lang = ' . json_encode($admin_lang->_lang_stack) . ";\n";
	echo "vivvo.admin.lang.get = function(lang){ return (lang in vivvo.admin.lang) ? vivvo.admin.lang[lang] : lang};";
?>