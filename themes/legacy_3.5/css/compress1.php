<?php
	if (ini_get('zlib.output_compression')){
		ob_start();
	}elseif (function_exists('ob_gzhandler')){
		ob_start('ob_gzhandler');
	}else{
		ob_start();
	}
	
	$files = explode(',',$_SERVER['QUERY_STRING']);
	$output = '';
	
	if (!empty($files)){
		$type = $files[0];
		array_shift($files);
		if ($type === 'css'){
			if (!empty($files)){
				header("Content-type: text/css");
				$expires = 60 * 60 * 24 * 3;
				$exp_gmt = gmdate("D, d M Y H:i:s", time() + $expires )." GMT";
				$mod_gmt = gmdate("D, d M Y H:i:s", time() + (3600 * -5 * 24 * 365) )." GMT";
				
				@header("Expires: {$exp_gmt}");
				@header("Last-Modified: {$mod_gmt}");
				@header("Cache-Control: public, max-age={$expires}");
				@header("Pragma: !invalid");
				
				foreach ($files as $file){
					$file = preg_replace('/[^a-zA-Z0-9\_\-\/]/', '', $file);
					if (file_exists(dirname(__FILE__) . '/../../../' . $file .'.css')){
						$output .= file_get_contents(dirname(__FILE__) . '/../../../' . $file .'.css') . "\n";
					}
				}			
			}
		}
	}
	echo $output;
	ob_end_flush();
?>