<?php
/* =============================================================================
 * $Revision: 6873 $
 * $Date: 2011-05-16 11:39:25 +0200 (Mon, 16 May 2011) $
 *
 * Vivvo CMS ${X_VERSION} (build ${X_REVISION})
 *
 * ${X_CODE_COPYRIGHT:m}
 *
 * ${X_CODE_LICENSE:m}
 * =============================================================================
 */

define('VIVVO_AVATAR_IMAGE_WIDTH', 40);
define('VIVVO_AVATAR_IMAGE_HEIGHT', 40);



require_once(dirname(__FILE__) . '/conf.php');

defined('VIVVO_FS_FILES_DIR') or define('VIVVO_FS_FILES_DIR', 'files/');
defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time()); 

$filename = str_replace('..', '', $_REQUEST["file"]);
$file = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $filename;
$file_thumbnail = VIVVO_FS_ROOT . 'cache/thumbnail/' . $filename;
$file_thumb_name = str_replace('/', '_', $filename);

$thumb_width = 100;
$thumb_height = 70;


if (isset($_GET['size'])){
	$size = preg_replace('/[^a-zA-Z0-9\-\_]/','', $_GET['size']);

    $file_thumbnail = VIVVO_FS_ROOT . 'cache/thumbnail/'.$size.'/' . $file_thumb_name;
}

$ext = strtolower( substr($file, strrpos($file, ".")) );

if (!file_exists($file) || is_link($file) || is_dir($file) || ($ext != '.jpg' && $ext != '.gif' && $ext != '.png' && $ext != '.jpeg')){
	error_not_found();
}

if (!file_exists($file_thumbnail)){
    //echo $thumb_width .' '. $thumb_height;
    if (isset($_GET['size'])){
        $connection = @mysql_connect(VIVVO_DB_HOST, VIVVO_DB_USER, VIVVO_DB_PASSWORD) or die("Can't connect! ".mysql_error());
        if (!mysql_select_db(VIVVO_DB_DATABASE, $connection)) die ("Error while connection to database. ".mysql_error());

        $size = preg_replace('/[^a-zA-Z0-9\-\_]/','', $_GET['size']);

        $res = mysql_query('SELECT * FROM ' . VIVVO_DB_PREFIX . 'configuration WHERE variable_name LIKE \'VIVVO_'.strtoupper($size).'_IMAGE_%\'');
        while ($row = mysql_fetch_assoc($res)){
            @define ($row['variable_name'], $row['variable_value']);
        }
        if (defined ('VIVVO_'.strtoupper($size).'_IMAGE_HEIGHT') && defined ('VIVVO_'.strtoupper($size).'_IMAGE_WIDTH')){
            $file_thumbnail = VIVVO_FS_ROOT . 'cache/thumbnail/'.$size.'/' . $file_thumb_name;
            $thumb_width = constant('VIVVO_'.strtoupper($size).'_IMAGE_WIDTH');
            $thumb_height = constant('VIVVO_'.strtoupper($size).'_IMAGE_HEIGHT');
        }
    }

    mysql_close($connection);

	createThumbnail($file, $file_thumbnail, $thumb_width, $thumb_height);
}

if (file_exists($file_thumbnail) && !is_link($file_thumbnail) && !is_dir($file_thumbnail)){
	$file_split =  explode('.', $file_thumbnail);
	$ext = end ($file_split);

	$sendbody = true;

	$expires = 60 * 60 * 24 * 10;
	$exp_gmt = gmdate("D, d M Y H:i:s", VIVVO_START_TIME + $expires )." GMT";
	$lastmod = @filemtime($file_thumbnail);
	$mod_gmt = gmdate("D, d M Y H:i:s", $lastmod) . " GMT";

	# get file content

	$etag = '"'.md5($file_thumbnail . $lastmod).'"';

	# check 'If-Modified-Since' header
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && gmdate('D, d M Y H:i:s', $lastmod)." GMT" == trim($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		header("HTTP/1.0 304 Not Modified");
		header("ETag: {$etag}");
		header("Content-Length: 0");
		$sendbody = false;
	}

	# check 'If-None-Match' header (ETag)
	if ($sendbody && isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
		$inm = explode(",",$_SERVER['HTTP_IF_NONE_MATCH']);
		foreach ($inm as $i) {
			if (trim($i) != $etag) continue;
			header("HTTP/1.0 304 Not Modified");
			header("ETag: {$etag}");
			header("Content-Length: 0");
			$sendbody = false; break;
		}
	}

	# caching headers (enable cache for one day)
	$expires = 60 * 60 * 24 * 10;
	$exp_gmt = gmdate("D, d M Y H:i:s",VIVVO_START_TIME+$expires)." GMT";
	$mod_gmt = gmdate("D, d M Y H:i:s",$lastmod)." GMT";
	header("Expires: {$exp_gmt}");
	header("Last-Modified: {$mod_gmt}");
	header("Cache-Control: public, max-age={$expires}");
	header("Pragma: !invalid");

	# send image
	if ($sendbody) {
		header("Content-Transfer-Encoding: Binary");
		header("Content-Disposition: inline; filename=" . $filename);
		if (strtolower($ext) == 'gif'){
			header('Content-type: image/gif');
		}elseif (strtolower($ext) == 'jpg' || strtolower($ext) == 'jpeg'){
			header("Content-type: image/jpeg");
		}elseif (strtolower($ext) == 'png'){
			header("Content-type: image/png");
		}

		$size = @filesize($file_thumbnail);
		header("ETag: {$etag}");
		header("Content-Length: {$size}");
		$content = file_get_contents($file_thumbnail);
		echo $content;
	}else {
		# don't send
		header("Content-Type: !invalid");
	}
} else {
	error_not_found();
}

@ob_end_flush();

function createThumbnail( $fileSrc, $thumbDest, $thumb_width = 100, $thumb_height = 70 )
{
	$ext = strtolower( substr($fileSrc, strrpos($fileSrc, ".")) );

	if( $ext == ".png" )
	{
		$base_img = ImageCreateFromPNG($fileSrc);
	}
	else if( ($ext == ".jpeg") || ($ext == ".jpg") )
	{
		$base_img = ImageCreateFromJPEG($fileSrc);
	}
	else if( ($ext == ".gif") )
	{
		$base_img = imagecreatefromgif($fileSrc);
	}

	// If the image is broken
	if ( !$base_img) {
		echo 'Image is broken';
		return false;
	}


	// Get image sizes from the image object we just created
	$img_width = imagesx($base_img);
	$img_height = imagesy($base_img);


	// Work out which way it needs to be resized
	$img_width_per  = $thumb_width / $img_width;
	if ($thumb_height == 0){
		$img_height_per = $thumb_width / $img_width;
		$thumb_height = (int) $img_height_per * $thumb_width;
	}else{
		$img_height_per = $thumb_height / $img_height;
	}

	if ($img_width_per <= $img_height_per)
	{
		$thumb_width = $thumb_width;
		$thumb_height = intval($img_height * $img_width_per);
	}
	else
	{
		$thumb_width = intval($img_width * $img_height_per);
		$thumb_height = $thumb_height;
	}

	$thumb_img = ImageCreateTrueColor($thumb_width, $thumb_height);

	ImageCopyResampled($thumb_img, $base_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $img_width, $img_height);


	$fc = @fopen($thumbDest, "w");
	if ($fc){
		fclose($fc);
	}
	if( $ext == ".png" )
	{
		ImagePNG($thumb_img, $thumbDest);
	}
	else if( ($ext == ".jpeg") || ($ext == ".jpg") )
	{
		ImageJPEG($thumb_img, $thumbDest);

	}else if($ext == ".gif"){

		ImageGIF($thumb_img, $thumbDest);

	}

	// Clean up our images
	ImageDestroy($base_img);
	ImageDestroy($thumb_img);

	return true;
}

function error_not_found() {
	require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php';
	go_404();
}

#EOF