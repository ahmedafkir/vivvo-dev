<?php
Class archive_info {

		function get_info($file){

				require_once(VIVVO_FS_ROOT . "lib/vivvo/framework/PEAR/Archive/zip.php");

				$obj = new Archive_Zip($file);
				$files = $obj->listContent();

				$data = array();
				foreach ($files as $f) {

					if( empty($f['filename']) )
						continue;

					$size = empty($f['size'])? '' : ' ('. round($f['size']/1024, 2) .' KB)';

					$data[] = $f['filename'] . $size;
				}

				if($data)
					return implode("\n", $data);

				else
					return false;
		}

}

#EOF