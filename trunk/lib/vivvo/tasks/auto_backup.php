<?php
/* =============================================================================
 * $Revision: 6070 $
 * $Date: 2010-12-09 15:33:24 +0100 (Thu, 09 Dec 2010) $
 *
 * Vivvo CMS v4.5.2 (build 6085)
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

    //error_reporting(E_ALL);
	if (!function_exists('gzcompressfile')) {
		/**
		 * Compress file on disk
		 *
		 * @param	string	$source
		 * @param	int		$level
		 * @return	string|false
		 */
		function gzcompressfile($source, $level = false) {
			if (file_exists($source)) {

				$dest = $source . '.gz';
				$mode = 'wb' . $level;
				$error = false;

				if (($fp_out = gzopen($dest, $mode))) {
					if (($fp_in = fopen($source, 'rb'))) {
						while (!feof($fp_in)) {
							gzwrite($fp_out, fread($fp_in, 4096));
						}
						fclose($fp_in);
					} else {
						$error = true;
					}
					gzclose($fp_out);
				} else {
					$error = true;
				}

				if ($error) {
					return false;
				}
				return $dest;
			}
			return false;
		}
	}

	/**
 	 * Create database backup (cron task function).
	 *
	 * @param vivvo_lite_site	$sm
	 */
	function auto_backup($sm) {

		include_once VIVVO_FS_INSTALL_ROOT . 'lib/backup/mysql_backup.php';

		$folder_name = VIVVO_FS_ROOT . 'backup/';
		$file_name = $folder_name.'backup_details.csv';
		$max_rows = 5000;
		$total_rows = 0;
		$log = '';
		$backup_obj = new BackupMySQL();

		$basename = $backup_obj->database . date('-Ymd-Hi-') . md5(uniqid(VIVVO_URL, true)) . '.sql';
		$filename = $folder_name.$basename;
		$table_details = $backup_obj->GetTables($backup_obj->database);
        
		@unlink($filename.'gz');

		$table_count = count($table_details);

		for ($count = 0; $count < $table_count; $count++)
		{
			list($table_name, $row_count) = explode(':', $table_details[$count]);
			$start = 0;
			while ($start < $row_count && $row_count != 0)
			{
				$end = $start + $max_rows;
				$str = $table_name.':'.$row_count."\r\n";
				$file_contents[$count] = $str;

				if ($backup_obj->Execute($filename, $backup_obj->database, $table_name, $start, 5000, $row_count) == false)
				{
					$e = $backup_obj->error;
					$log .= 'Auto Backup Error - Table: '.$table_name.', details: '.$e."\n";
					break;
				}
				else
				{
					$total_rows += $end;
					$fp = fopen($file_name, 'w');
					$fc = count($file_contents);
					for ($counter = 0; $counter < $fc; $counter++)
						fwrite($fp, $file_contents[$counter]);
					fclose($fp);
				}
				//if ($total_rows >= $max_rows) break;
				$start += $max_rows;
			}
		}
		unset($count, $table_count, $table_name, $row_count, $start, $file_contents, $max_rows, $total_rows, $str, $backup_obj, $fp, $fc, $counter);

		if (gzcompressfile($filename, 9) === false) {
			$log .= 'Backup file created but cannot be gzipped.';
		} else {
			$log .= 'Backup created and successfully gzipped.';
			@unlink($filename);
		}

		if (defined('VIVVO_CRONJOB_MODE')) {
			echo 'auto_backup: ' . $log . PHP_EOL;
		} else {
			admin_log('(Cron task: Auto Backup)', $log);
		}
	}

	defined('VIVVO_CRONJOB_MODE') and $info = 'Creates database backup.';

#EOF