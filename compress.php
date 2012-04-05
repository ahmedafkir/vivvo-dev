<?php
/* =============================================================================
 * $Revision: 5491 $
 * $Date: 2010-06-10 15:13:09 +0200 (Thu, 10 Jun 2010) $
 *
 * Vivvo CMS v4.7 (build 6082)
 *
 * Copyright (c) 2012, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */

	require_once 'conf.php';

	defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time());

	/**
	 * @var	int		Number of seconds to keep cache file (if set to 0 cache is skipped)
	 */
	defined('VIVVO_COMPRESS_CACHE_LIFETIME') or define('VIVVO_COMPRESS_CACHE_LIFETIME', 3600);

	/**
	 * @var	bool	Wether to enable or disable usage of compress filters
	 */
	defined('VIVVO_COMPRESS_ENABLE_FILTERS') or define('VIVVO_COMPRESS_ENABLE_FILTERS', true);

	// default compress filter for javascript files
	defined('VIVVO_COMPRESS_FILTER_JS') or define('VIVVO_COMPRESS_FILTER_JS', '{"file":"lib/vivvo/functions.php","function":"jsmin_compress_filter"}');

	// default compress filter for css files
	defined('VIVVO_COMPRESS_FILTER_CSS') or define('VIVVO_COMPRESS_FILTER_CSS', '{"file":"lib/vivvo/functions.php","function":"cssmin_compress_filter"}');


	if (ini_get('zlib.output_compression')) {
		ob_start();
	} elseif (function_exists('ob_gzhandler')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}

	$sendbody = true;
	$type = substr($_SERVER['QUERY_STRING'], 0, strpos($_SERVER['QUERY_STRING'], ','));
	
	if ($type !== 'js' && $type !== 'css') {
        exit();
	}
	
	$cache_file = VIVVO_FS_INSTALL_ROOT . 'cache/' . md5($_SERVER['QUERY_STRING']) . '.' . $type;

	if (!function_exists('json_decode') or !function_exists('json_encode')) {
		require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/PEAR/JSON.php');
		if (!function_exists('json_decode')) {
			function json_decode($str, $flag = false) {
				if ($flag) {
					$value = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
				} else {
					$value = new Services_JSON();
				}
				return $value->decode($str);
			}
		}
		if (!function_exists('json_encode')) {
			function json_encode($str) {
				$value = new Services_JSON();
				return $value->encode($str);
			}
		}
	}

	if (VIVVO_COMPRESS_CACHE_LIFETIME > 0 and @filesize($cache_file) and VIVVO_START_TIME - ($lastmod = filemtime($cache_file)) < VIVVO_COMPRESS_CACHE_LIFETIME) {
		$output = file_get_contents($cache_file);
	} else {

		$files = explode(',', substr($_SERVER['QUERY_STRING'], strlen($type) + 1));
		$output = '';

		$lastmod = 0;

		foreach ($files as $file) {
			$file = str_replace('..', '', preg_replace('/[^a-zA-Z0-9\_\-\/\.]/', '', $file));
			if (file_exists($filename = VIVVO_FS_INSTALL_ROOT . $file . '.' . $type)) {
				$output .= file_get_contents($filename) . "\n";
				$lastmod = max($lastmod, filemtime($filename));
			}
		}

		if (VIVVO_COMPRESS_CACHE_LIFETIME > 0) {

			if (VIVVO_COMPRESS_ENABLE_FILTERS and defined($name = 'VIVVO_COMPRESS_FILTER_' . strtoupper($type)) and ($filter = json_decode(constant($name), true)) !== false) {
				require_once $filter['file'];
				$output = call_user_func($filter['function'], $output);
			}

			file_put_contents($cache_file, $output);
		}
	}

	$expires = 864000; // = 10 days
	$exp_gmt = gmdate('D, d M Y H:i:s', VIVVO_START_TIME + $expires) . ' GMT';
	$mod_gmt = gmdate('D, d M Y H:i:s', $lastmod) . ' GMT';

	$etag = '"' . dechex(crc32($output)) . '"';

	# check 'If-Modified-Since' header
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) and $mod_gmt == trim($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		header('Content-Length: 0', true, 304);	// 304 - Not Modified
		$sendbody = false;
	}

	# check 'If-None-Match' header (ETag)
	if ($sendbody and isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
		$inm = explode(',', $_SERVER['HTTP_IF_NONE_MATCH']);
		foreach ($inm as $i) {
			if (trim($i) != $etag) continue;
			header('Content-Length: 0', true, 304);	// 304 - Not Modified
			$sendbody = false;
			break;
		}
	}

	# caching headers
	header("Expires: $exp_gmt");
	header("Last-Modified: $mod_gmt");
	header("Cache-Control: public, max-age=$expires");
	header("Pragma: !invalid");
	header("ETag: $etag");

	switch (strtolower($type)) {
		case 'js':
			header('Content-Type: application/x-javascript');
		break;
		case 'css':
			header('Content-Type: text/css');
		break;
		default:
	}

	if ($sendbody) {
		echo $output;
	}

	ob_end_flush();

#EOF