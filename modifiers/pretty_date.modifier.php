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

    /**
	 * pretty_date
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	Spoonlabs
	 * @package		Vivvo
	 * @subpackage	modifier
	 * @version		1.0
	 * @category	CMS
	 * @author 		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
    function pretty_date($datetime, $format = false) {

		$format and list($format, $datetime) = array($datetime, $format);	// swap

		if (!is_numeric($datetime)) {
			$datetime = strtotime($datetime);
		}

        $seconds = VIVVO_START_TIME - $datetime;
        if ($seconds < 0 || floor($seconds / 86400) > 0) {
            return format_date(date('Y-m-d H:i:s', $datetime), $format); // date in future or >= 1 days ago...
        }

		$lang = vivvo_lang::get_instance();

        return $lang->get_value('LNG_PRETTY_DATE_PREFIX') . _pretty_date_format($seconds) . $lang->get_value('LNG_PRETTY_DATE_SUFFIX');
    }

    /**
     * Formats date
     *
     * @param   int             $seconds
     * @return  string
     */
    function _pretty_date_format($seconds) {

		$lang = vivvo_lang::get_instance();

        $formats = array(
            array( 60,                  $lang->get_value('LNG_PRETTY_DATE_FEW_MOMENTS')),
            array( 120,                 $lang->get_value('LNG_PRETTY_DATE_1_MINUTE')),
            array( 3600,  array( 60,    $lang->get_value('LNG_PRETTY_DATE_MINUTES'))),
            array( 7200,                $lang->get_value('LNG_PRETTY_DATE_1_HOUR')),
            array( 86400, array( 3600,  $lang->get_value('LNG_PRETTY_DATE_HOURS')))
        );

        for ($i = 0, $len = count($formats); $i < $len; $i++) {
            $format = $formats[$i];
            if ($seconds < $format[0]) {
                if (is_array($format[1])) {
                    $formatted = floor($seconds / $format[1][0]) . ' ' . $format[1][1];
                    $seconds = $seconds % $format[1][0];
                    if ($seconds > 60) {
                        $formatted .= ' ' . _pretty_date_format($seconds);
                    }
                } else {
                    $formatted = $format[1];
                }
                return $formatted;
            }
        }

        return $lang->get_value('LNG_PRETTY_DATE_VERY_LONG_TIME');
    }

#EOF