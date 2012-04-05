<?php
/* =============================================================================
 * $Revision: 4991 $
 * $Date: 2010-04-14 12:04:27 +0200 (Wed, 14 Apr 2010) $
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

function imagecode_url_handler(&$sm, $url_array) {

	$sm->set_theme();

	if (isset($_GET['article_id'])) {

		header("Content-type: image/png");
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 	// Date in the past

		if (defined('VIVVO_FS_THEME_ROOT')) {
			$root = str_replace(VIVVO_URL, VIVVO_FS_THEME_ROOT, VIVVO_THEME);
		} else {
	    	$root = str_replace(VIVVO_URL, VIVVO_FS_ROOT, VIVVO_THEME);
	    }
	    $img_handle = imagecreatefrompng($root . 'img/imagecode_bg.png');
	    $color = imagecolorallocate($img_handle, 450, 450, 450);

	    if (!$_SESSION['vivvo']['comment_captcha'][$_GET['article_id']]) {
	    	exit();
	    } else {
		    imagestring($img_handle, 5, 20, 13, $_SESSION['vivvo']['comment_captcha'][$_GET['article_id']], $color);
		}

	    imagepng($img_handle);
	    imagedestroy($img_handle);
	}else if (isset($_GET['login'])) {

        header("Content-type: image/png");
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");   // Date in the past

        if (defined('VIVVO_FS_THEME_ROOT')) {
            $root = str_replace(VIVVO_URL, VIVVO_FS_THEME_ROOT, VIVVO_THEME);
        } else {
            $root = str_replace(VIVVO_URL, VIVVO_FS_ROOT, VIVVO_THEME);
        }
        $img_handle = imagecreatefrompng($root . 'img/imagecode_bg.png');
        $color = imagecolorallocate($img_handle, 450, 450, 450);

        $enc = rand(1000,100000);
        $enc = substr(md5('icemelondawg' . $enc), 2,8);
        if (!isset($_SESSION['vivvo'])) $_SESSION['vivvo'] = array();
        $_SESSION['vivvo']['login_captcha'] = $enc;
        
        imagestring($img_handle, 5, 20, 13, $_SESSION['vivvo']['login_captcha'], $color);

        imagepng($img_handle);
        imagedestroy($img_handle);
    }
    
	exit;
}
?>