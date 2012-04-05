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

	Class image_info {

		function get_info($file){

			if( !function_exists('exif_read_data') )
				return false;

			$exif = @exif_read_data($file);

			if($exif){
				$data = array();

				if( !empty( $exif['Artist']) )
					$data[] = 'Author: '.$exif['Artist'];

				if( !empty( $exif['COMPUTED']) and !empty($exif['COMPUTED']['Copyright']) )
					$data[] = 'Copyright: '. $exif['COMPUTED']['Copyright'];

				// some photos have empty Description with just a couple of spaces, so trim that away
				$exif['ImageDescription'] = trim( $exif['ImageDescription'] );

				if( !empty( $exif['ImageDescription'] ) )
					$data[] = 'Description: '. $exif['ImageDescription'];

				if( !empty( $exif['Software']) )
					$data[] = 'Software:'. $exif['Software'];

				if( !empty( $exif['DateTime']) )
					$data[] = 'Created: '. $exif['DateTime'];

				if( !empty( $exif['COMMENT']) )
					$data[] = "Embedded Comments:\n". implode("\n", $exif['COMMENT']);

				if($data)
					return implode("\n", $data);
			}

			return false;

		}
	}

#EOF