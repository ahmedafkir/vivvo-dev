<?php
	/**
	 * Import feed
	 */
	function import_feed(&$sm){
		
		
		require_once(dirname(__FILE__) . '/../feed.class.php');
		$category_list = $sm->get_categories();
		$category_keys = array_keys($category_list->list);
		
		require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

		require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

		$pm =& new vivvo_post_master($sm);

		$al =& new Articles_list($sm); 
		
		if (VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE > 0){
			$al->_query->add_where('created < ( DATE_SUB( \'' . date('Y-m-d H:i:s') . '\', INTERVAL ' .  VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE .' DAY ))');
			$al->_query->add_where('feed_data != \'\'');
			$al->_query->set_from('`'.VIVVO_DB_PREFIX . 'Articles`');
			$al->_query->add_fields('*');
			$al->sql_delete_list($pm, NULL, true);
			$al->reset_list_query();
		}
		
		if (VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE > 0){
			$al->search(array('search_search_date' => VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE, 'search_before_after' => '-1'), '', '', 0, 0, false);
			$al->_query->add_where('feed_data != \'\'');
			$al->sql_update_list($pm, array('status' => -1), NULL, true);
		}
		
		$fl =& new Feeds_list($sm);
		$fl->search(array());
		
		foreach($fl->list as $feed){
			if (in_array($feed->category_id, $category_keys)){
				$feed->get_articles();
				require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
				$post_master = new vivvo_post_master($sm);
				$post_master->set_data_object($feed);
				$post_master->sql_update();
			}else{
				$pm->set_data_object($feed);
				$pm->sql_delete();
			}
		}
	}
?>