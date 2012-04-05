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

    function weekday_name($dayoffset) {
        $dayoffset = (int)$dayoffset;
        $dow = strtoupper(date('l', strtotime("+$dayoffset days")));
        return vivvo_lite_site::get_instance()->get_lang()->get_value("LNG_$dow");
    }

    if (!function_exists('vivvo_inc')) {
        function vivvo_inc($value) {
            return $value + 1;
        }
    }

    if (!function_exists('num_tagged_articles')) {
        function num_tagged_articles($tag_id, $topic_id) {
            $tag_id = (int)$tag_id;
            $topic_id = (int)$topic_id;
            $sql = 'SELECT COUNT(*) FROM '.VIVVO_DB_PREFIX."ArticlesTags WHERE tag_id = $tag_id AND tags_group_id = $topic_id";
			$res = vivvo_lite_site::get_instance()->get_db()->query($sql);
            if (!PEAR::isError($sql) && ($total = $res->fetchOne())) {
                return $total;
            }
            return 0;
        }
    }
?>