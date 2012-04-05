<?php
/* =============================================================================
 * $Revision: 5348 $
 * $Date: 2010-05-18 18:02:19 +0200 (Tue, 18 May 2010) $
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

	defined('VIVVO_FS_INSTALL_ROOT') or define('VIVVO_FS_INSTALL_ROOT', realpath(dirname(__FILE__) . '/../../../') . '/');
	defined('VIVVO_FS_FILES_DIR') or define('VIVVO_FS_FILES_DIR', 'files/');

	/**
	 * Recursevly deletes all files from folder
	 *
	 * @param	string	$dir
	 */
	function clear_directory($dir) {

		$dir = rtrim($dir, '/') . '/';

		if (file_exists($dir) and is_dir($dir)) {
			if ($handle = opendir($dir)) {
				while (false !== ($filename = readdir($handle))) {
					if ($filename[0] != '.' and is_dir($dir . $filename)) {
						clear_thumbnails($dir . $filename);
					} elseif ($filename[0] != '.' and is_file($dir . $filename)) {
						@unlink($dir . $filename);
					}
				}
			}
		}
	}

	/**
	 * Clear thumbnails (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function auto_clear_thumbnails($sm = null) {

		clear_directory(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_FILES_DIR . 'thumbnail/');

		if (defined('VIVVO_CRONJOB_MODE')) {
			echo 'auto_clear_thumbnails: Finished.' . PHP_EOL;
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Clears cached thumbnail images.';

#EOF