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

	require_once (dirname(__FILE__) . '/../conf.php');
	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php');

	define('VIVVO_ADMIN_MODE', true);

	$sm = new vivvo_lite_site();

	if ($sm->user && ($sm->user->is_admin() || $sm->user->can('MANAGE_ASSETS'))){

		if (isset($_REQUEST["file"])) {

			$filename = str_replace('..', '', $_REQUEST["file"]);
		    $file = VIVVO_FS_ROOT . $filename;

		    if (file_exists($file) && !is_link($file) && !is_dir($file) && preg_match('/(files\/|backup\/)/', $filename)){
		    	$file_split =  explode('.', $file);
		    	$ext = end ($file_split);
		    	if (strtolower($ext) != 'sql' && !isset($_REQUEST['force'])){
		    		header("Content-Transfer-Encoding: Binary");
					header("Content-length: ".filesize($file));

					$expires = 60 * 60 * 24 * 3;
					$exp_gmt = gmdate("D, d M Y H:i:s", VIVVO_START_TIME + $expires )." GMT";
					$mod_gmt = gmdate("D, d M Y H:i:s", VIVVO_START_TIME + (3600 * -5 * 24 * 365) )." GMT";

					@header("Expires: {$exp_gmt}");
					@header("Last-Modified: {$mod_gmt}");
					@header("Cache-Control: public, max-age={$expires}");
					@header("Pragma: !invalid");

			    	if (strtolower($ext) == 'gif'){
						header('Content-type: image/gif');
			    	}elseif (strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg'){
						header("Content-type: image/jpeg");
			    	}elseif (strtolower($ext) == 'png'){
			    		header("Content-type: image/png");
			    	}elseif (strtolower($ext) == 'flv'){
			    		header("Content-type: video/x-FLV");
			    	}elseif (strtolower($ext) == 'avi'){
			    		header("Content-type: video/x-msvideo");
			    	}elseif (strtolower($ext) == 'wmf'){
			    		header("Content-type: application/x-msmetafile");
			    	}elseif (strtolower($ext) == 'mov' || strtolower($ext) == 'qt'){
			    		header("Content-type: video/quicktime");
			    	}elseif (strtolower($ext) == 'swf'){
			    		header("Content-type: application/x-shockwave-flash");
			    	}else{
						header("Content-type: application/force-download");
						header("Content-disposition: attachment; filename=\"".basename($file)."\"");
			    	}
					readfile($file);
			   	}else{
			   		header("Content-type: application/force-download");
					header("Content-disposition: attachment; filename=\"".basename($file)."\"");
		    		readfile($file);
		    	}
		    }else{
		    	go_404();
		    }
		}
	}
?>