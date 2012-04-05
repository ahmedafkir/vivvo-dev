<?php
/* =============================================================================
 * $Revision: 4907 $
 * $Date: 2010-04-07 15:37:11 +0200 (Wed, 07 Apr 2010) $
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

	defined('VIVVO_FS_FILES_DIR') or define('VIVVO_FS_FILES_DIR', 'files/');

	if (!function_exists('is_url')) {
		function is_url($string) {
			return preg_match('|^[^:/\.\?]+://|', $string);
		}
	}

	function is_youtube_video($string) {
		return preg_match('|^[^:]+://(.*?.)?youtube\.com|', $string);
	}

	function flashvars_encode($value) {
		return str_replace(array('?', '=', '&'), array('%3F', '%3D', '%26'), $value);
	}

    function attachment_filesize($attachment) {
        return filesize(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $attachment->real_path);
    }

	function get_mp3duration($attachment) {
		require_once VIVVO_FS_INSTALL_ROOT . 'lib/zedwood/mp3file.php';
		$mp3 = new mp3file($filename = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $attachment->real_path);
		$metadata = $mp3->get_metadata();
		if (isset($metadata['Length'])) {
			return $metadata['Length'];
		} else {
			return ceil(filesize($filename) / 60000000);	// 128kbps, stereo
		}
	}

	function extract_youtube_image($url, $size = null) {

		if ($size != null) {
			list($url, $size) = array($size, $url);	// swap
		} else {
			$size = '0';
		}

		if (preg_match('/[\/\?&]v=([^&#]*)/i', $url, $match)) {
			return 'http://img.youtube.com/vi/' . $match[1] . '/' . $size . '.jpg';
		}

		return '';
	}

	function get_video_object($article) {

		$video = new StdClass;

		$video->file = '';
		$video->image = '';
		$video->provider = '';

		$params = array();

		if (is_url($article->video_attachment)) {
			if (is_youtube_video($video->file = $article->video_attachment)) {
				$video->image = extract_youtube_image($video->file);
				$video->provider = 'youtube';
				$params[] = 'provider=youtube';
			}
		} else {
			$video->file = VIVVO_STATIC_URL . $article->video_attachment;
		}

		if (!$video->image and $article->image) {
			$video->image = VIVVO_STATIC_URL . "thumbnail.php?file={$article->image}&size=article_large";
		}

		if ($video->image) {
			$params[] = 'image=' . flashvars_encode($video->image);
		}

		$params[] = 'file=' . flashvars_encode($video->file);
		$params[] = 'logo.hide=true';

		$video->flashvars = implode('&', $params);

		return $video;
	}
?>