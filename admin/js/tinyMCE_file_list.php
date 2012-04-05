<?php
/* =============================================================================
 * $Revision: 3378 $
 * $Date: 2008-12-16 18:28:26 +0100 (Tue, 16 Dec 2008) $
 * 
 * Vivvo CMS v4.1.6 (build 4214) 
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 * 
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * ============================================================================ 
 */


	require_once (dirname(__FILE__) . '/../admin_include.php');
	
	
	header("content-type: application/x-javascript");
	
	
	$fm =& $sm->get_file_manager();

	if ($um->get_param('type') == 'image'){
		echo "var tinyMCEImageList = new Array(\n";
		$file_list =& new dir_list($sm, 'files/', array('png', 'gif', 'jpg', 'jpeg'), '', false);
	}elseif ($um->get_param('type') == 'media'){
		echo "var tinyMCEMediaList = new Array(\n";
		$file_list =& new dir_list($sm, 'files/', array('swf', 'dcr', 'mov', 'qt', 'mpg', 'mp3', 'mp4', 'mpeg', 'avi', 'wmv', 'wm', 'asf', 'asx', 'wmx', 'wvx', 'rm', 'ra', 'ram'), '', false);
	}elseif ($um->get_param('type') == 'flash'){
		echo "var tinyMCEFlashList = new Array(\n";
		$file_list =& new dir_list($sm, 'files/', array('swf'), '', false);
	}
	if (isset($file_list)){
		$list =& $file_list->get_files();
		
		if (is_array($list)){
			$cmd_array = array();
			$number_of_files = count ($list);
			for ($i = 0; $i < $number_of_files; $i++){
				$cmd_array[] = '["' . $list[$i]->filename . '","' . VIVVO_URL . 'files.php?file=' . $list[$i]->filename . "\"]";
			}
			echo implode(",\n", $cmd_array);
		}
	}
	echo "\n);";
?>