<?php
	class box_paged_files extends module {
		var $_default_template_file = 'system/box_default/box_files.tpl';

		/**
		 * Generate box output
		 *
		 * @param array $params Parameters
		 */
		function generate_output ($params){
			$this->set_template($params);

			if (isset($params['pg'])){
				$cur_page = (int) $params['pg'];
			}
			if (empty($cur_page))
				$cur_page=1;

			$params['pg'] = $cur_page;

			$params['offset'] = ($cur_page-1) * $params['search_limit'];
			if (empty($params['offset']))
				$params['offset'] = 0;

			if( substr($params['search_dir'],-1)!='/' )
				$params['search_dir'] .= '/';

			require_once(VIVVO_FS_FRAMEWORK . 'asset_files.class.php');

			$assets = new AssetFiles_list();

			if( $assets->is_changed($params['search_dir']) ) { //if dir changed re-read the file list

				require_once(VIVVO_FS_ROOT . 'lib/vivvo/service/assets.service.php');
				$asset_service = new assets_service();
				$asset_service->scan_dir($params['search_dir']);
			}

			$um = vivvo_lite_site::get_instance()->get_url_manager();

			if( $search_quick = $um->get_param('search_all_quick') ) { //quick search from filepicker

				require_once(VIVVO_FS_FRAMEWORK . 'asset_keywords.class.php');

				$ids = array();
				$keywords = new AssetKeywords_list();
				if( $result = $keywords->get_keywords_by_fulltext($search_quick) ) {
					foreach($result as $row)
						$ids[] = $row->get_asset_id();
				}

				$search = array('search_name_starts_with' => $search_quick);
				if($ids) {

					$assets->_default_query(true);
					$assets->_query->add_where('(f.id IN (' . implode(',', $ids) . '))');
					$assets->_query->add_where("(f.name LIKE '".secure_sql($search_quick)."%')", 'OR');
					$assets->_query->add_order('path ASC');

					$limit = (int) $params['search_limit'];
					$assets->_query->set_limit($limit);
					$offset = (int) $params['offset'];
					$assets->_query->set_offset($offset);

					$assets->set_list();

				} else {

					$assets->search( array('search_name_starts_with' => $search_quick), 'path', 'ASC', $params['search_limit'], $params['offset']);
				}

			} elseif( empty($params['search_do_advanced']) ) { //regular search

				$assets->search( array( 'search_dir'=>$params['search_dir'], 'search_ext'=>$params['search_ext']), $params['search_sort_by'], $params['search_order'], $params['search_limit'], $params['offset']);

			} else { //advanced search
				$search = array();

				if( empty($params['search_subfolders']) )
					$search['search_dir'] = $params['search_dir'];
				else
					$search['search_under_path'] = $params['search_dir'];

				if( !empty($params['search_ext']) )
					$search['search_ext'] = $params['search_ext'];

				if( !empty($params['search_filename']) )
					$search['search_name_starts_with'] = $params['search_filename'];

				if( !empty($params['search_info']) )
					$search['search_info'] = $params['search_info'];

				if( !empty($params['search_keywords']) ) {

					require_once(VIVVO_FS_FRAMEWORK . 'asset_keywords.class.php');

					$keywords = new AssetKeywords_list();
					$result = $keywords->get_keywords_by_fulltext($params['search_keywords']);

					if($result) {
						$ids = array();
						foreach($result as $row)
							$ids[] = $row['asset_id'];

						if($ids)
							$search['search_id'] = $ids;
					}
				}

				if( !empty($params['search_date_after']) )
					$search['search_date_after'] = $params['search_date_after'];

				$assets->search( $search, $params['search_sort_by'], $params['search_order'], $params['search_limit'], $params['offset']);

			}

			$assets->set_pagination($cur_page);

			$this->_template->assign('dir', $params['search_dir']);
			$this->_template->assign('file_list_object', $assets);
			$this->_template->assign('file_list', $assets->list);
			$this->_template->assign('box_id', $params['box_id']);
			$this->_template->assign('prefix', $params['prefix']);
		}
	}
?>