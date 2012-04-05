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

	class pdf_info {

		function get_info($file){

			//too slow and not working most of the time
			return false;

			//Start snippet
		    $fp = fopen($file, "r");
		    $buffer = '';
		    while(!feof($fp)) {
			    $cool=ord(fgetc($fp));
				$ret = '';
				if (($cool < 127) & ($cool > 31)){
					$ret = chr($cool);
				}
			    $buffer .= $ret;
		    }
		    fclose($fp);
		    $buffer2 = $buffer;
		    $buffer2 = eregi_replace(">>startxref.*",'',eregi_replace(".*/Info ",'',$buffer2));
		    $buffer2 = eregi_replace(" R .*",'',$buffer2);
		    $buffer2 .= " obj<< ";
		    $zz = strpos($buffer, $buffer2);
		    $buffer = substr($buffer, $zz);
		    $buffer = eregi_replace("> endobj.*", '', $buffer);
		    $buffer2 = $buffer;
		    $val1 = '';
		    $val2 = '';
		    if (eregi(".*[/]Subject [(].*", $buffer2)){
		      $val1 = eregi_replace("[)][/ >].*", '', eregi_replace(".*[/]Subject [(]", '', $buffer2));
		    }
		    if (eregi(".*[/]Title [(].*", $buffer2)){
		      $val2 = eregi_replace("[)][/ >].*", '', eregi_replace(".*[/]Title [(]", '', $buffer2));
		    }
		    $buffer = stripslashes((strlen($val1) > strlen($val2)?$val1:$val2));
		    return $buffer;
			//End snippet
		}
	}

#EOF