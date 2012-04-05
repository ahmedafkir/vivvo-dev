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

	Class video_info {

		function get_info($file){
			if(	!class_exists('ffmpeg_movie') or !$movie = new ffmpeg_movie($file) ) //ffmpeg extension required
				return false;

			$duration = $movie->getDuration();
			$width = $movie->getFrameWidth();
			$height = $movie->getFrameHeight();
			$framerate = $movie->getFrameRate();

			$data = '';

			if($duration)
				$data .= "Duration: $duration\n";

			if($width and $height)
				$data .= "Dimensions: $width x $height px\n";

			if($framerate)
				$data .= "Framerate: $framerate fps\n";

			return $data;

		}
	}

#EOF