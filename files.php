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


	require_once dirname(__FILE__) . '/conf.php';

	defined('VIVVO_FS_FILES_DIR') or define('VIVVO_FS_FILES_DIR', 'files/');
	defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time());

	if (isset($_GET['file'])) {

		$filename = str_replace('logs/', '', $_GET['file']);
		$filename = str_replace('..', '', $filename);
	    $file = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $filename;

	    if (file_exists($file) and !is_link($file) and !is_dir($file)) {

			if (!isset($_GET['force'])) {

		    	header('Content-Transfer-Encoding: Binary');
				header('Content-length: ' . filesize($file));

				$expires = 60 * 60 * 24 * 3;
				$exp_gmt = gmdate('D, d M Y H:i:s', VIVVO_START_TIME + $expires) . ' GMT';
				$mod_gmt = gmdate('D, d M Y H:i:s', VIVVO_START_TIME + (3600 * -5 * 24 * 365) ) . ' GMT';	// should we use filemtime() here?

				header("Expires: {$exp_gmt}");
				header("Last-Modified: {$mod_gmt}");
				header("Cache-Control: public, max-age={$expires}");
				header("Pragma: !invalid");

				if (function_exists('finfo_open') and is_resource($finfo = finfo_open(FILEINFO_MIME))) {
					$type = finfo_file($finfo, $file);
					finfo_close($finfo);
				} elseif (function_exists('mime_content_type')) {
					$type = mime_content_type($file);
				} else {
					switch (strtolower(substr($file, strrpos($file, '.') + 1))) {
						case 'txt':   $type = 'text/plain'; break;
						case 'htm':
						case 'html':
						case 'php':   $type = 'text/html'; break;
						case 'xml':	  $type = 'application/xml'; break;
						case 'css':   $type = 'text/css'; break;
						case 'js':    $type = 'application/javascript'; break;
						case 'json':  $type = 'application/json'; break;
						case 'xml':   $type = 'application/xml'; break;
						case 'swf':   $type = 'application/x-shockwave-flash'; break;
						case 'flv':   $type = 'video/x-flv'; break;
						case 'png':   $type = 'image/png'; break;
						case 'jpe':
						case 'jpeg':
						case 'jpg':   $type = 'image/jpeg'; break;
						case 'gif':   $type = 'image/gif'; break;
						case 'bmp':   $type = 'image/bmp'; break;
						case 'ico':   $type = 'image/vnd.microsoft.icon'; break;
						case 'tiff':
						case 'tif':   $type = 'image/tiff'; break;
						case 'svg':
						case 'svgz':  $type = 'image/svg+xml'; break;
						case 'zip':   $type = 'application/zip'; break;
						case 'rar':   $type = 'application/x-rar-compressed'; break;
						case 'exe':
						case 'msi':   $type = 'application/x-msdownload'; break;
						case 'cab':   $type = 'application/vnd.ms-cab-compressed'; break;
						case 'mpe':
						case 'mpeg':
						case 'mpg':   $type = 'video/mpeg'; break;
						case 'mpga':
						case 'mp2':
						case 'mp3':   $type = 'audio/mpeg'; break;
						case 'ogx':   $type = 'application/ogg'; break;
						case 'ogv':   $type = 'video/ogg'; break;
						case 'oga':
						case 'ogg':
						case 'spx':   $type = 'audio/ogg'; break;
						case 'flac':  $type = 'audio/flac'; break;
						case 'anx':   $type = 'application/annodex'; break;
						case 'axa':   $type = 'audio/annodex'; break;
						case 'axv':   $type = 'video/annodex'; break;
						case 'xspf':  $type = 'application/xspf+xml'; break;
						case 'aif':
						case 'aifc':
						case 'aiff':  $type = 'audio/x-aiff'; break;
						case 'movie': $type = 'video/x-sgi-movie'; break;
						case 'wav':   $type = 'audio/x-wav'; break;
						case 'snd':
						case 'au':    $type = 'audio/basic'; break;
						case 'avi':   $type = 'video/x-msvideo'; break;
						case 'dif':   $type = 'video/x-dv'; break;
						case 'wmf':	  $type = 'application/x-msmetafile'; break;
						case 'kar':
						case 'mid':
						case 'midi':  $type = 'audio/midi'; break;
						case 'm3u':	  $type = 'audio/x-mpegurl'; break;
						case 'aac':
						case 'm4a':
						case 'm4b':
						case 'm4p':   $type = 'audio/mp4a-latm'; break;
						case 'm4u':   $type = 'video/vnd.mpegurl'; break;
						case 'm4v':   $type = 'video/x-m4v'; break;
						case 'ram':
						case 'ra':    $type = 'audio/x-pn-realaudio'; break;
						case 'qt':
						case 'mov':   $type = 'video/quicktime'; break;
						case 'pdf':   $type = 'application/pdf'; break;
						case 'psd':   $type = 'image/vnd.adobe.photoshop'; break;
						case 'ai':
						case 'eps':
						case 'ps':    $type = 'application/postscript'; break;
						case 'doc':   $type = 'application/msword'; break;
						case 'rtf':   $type = 'application/rtf'; break;
						case 'xls':   $type = 'application/vnd.ms-excel'; break;
						case 'ppt':   $type = 'application/vnd.ms-powerpoint'; break;
						case 'odt':   $type = 'application/vnd.oasis.opendocument.text'; break;
						case 'ods':   $type = 'application/vnd.oasis.opendocument.spreadsheet'; break;
					}
				}

				if (empty($type)) {
					header('Content-type: application/force-download');
					header('Content-disposition: attachment; filename="' . basename($file) . '"');
				} else {
					header("Content-type: $type");
				}
			} else {
				header('Content-type: application/force-download');
				header('Content-disposition: attachment; filename="' . basename($file) . '"');
			}

			readfile($file);
			exit;
	    }
	}

	require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php';
	go_404();

#EOF