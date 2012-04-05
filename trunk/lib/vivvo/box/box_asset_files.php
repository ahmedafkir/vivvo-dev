<?php
/* =============================================================================
 * $Revision: 5370 $
 * $Date: 2010-05-24 11:42:36 +0200 (Mon, 24 May 2010) $
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

	class box_asset_files extends module {
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

			require_once(VIVVO_FS_FRAMEWORK . 'asset_keywords.class.php');

			if( $search_quick = $um->get_param('search_all_quick') ) { //quick search from filepicker

				$ids = array();
				$keywords = new AssetKeywords_list();
				if( $result = $keywords->get_keywords_by_fulltext($search_quick) ) {
					foreach($result as $row)
						$ids[] = $row->get_asset_id();
				}

				$search = array('search_name_wildcard' => $search_quick);
				if($ids) {

					$assets->_default_query(true);
					$assets->_query->add_where('(f.id IN (' . implode(',', $ids) . '))');
					$assets->_query->add_where("(f.name LIKE '%".secure_sql($search_quick)."%')", 'OR');
					$assets->_query->add_where('filetype_id != ' . VIVVO_ASSETS_DIRTYPE_ID);
					$assets->_query->add_order('path ASC');

					$limit = (int) $params['search_limit'];
					$assets->_query->set_limit($limit);
					$offset = (int) $params['offset'];
					$assets->_query->set_offset($offset);

					$assets->set_list();

				} else {

					$assets->search( array('search_name_wildcard' => $search_quick), 'path', 'ASC', $params['search_limit'], $params['offset']);
				}

			} elseif( empty($params['search_do_advanced']) ) { //regular search

				$assets->search( array( 'search_dir'=>$params['search_dir'], 'search_ext'=>$params['search_ext']), $params['search_sort_by'], $params['search_order'], $params['search_limit'], $params['offset']);

			} else { //advanced search
				$search = array();

				if( !empty($params['search_dir']) ) {
					if( empty($params['search_subfolders']) )
						$search['search_dir'] = $params['search_dir'];
					else
						$search['search_under_path'] = $params['search_dir'];
				}

				if( !empty($params['search_ext']) )
					$search['search_ext'] = $params['search_ext'];

				if( !empty($params['search_filetype_id']) )
					$search['search_filetype_id'] = $params['search_filetype_id'];

				if( !empty($params['search_filename']) ) {
					if( !empty($params['search_exact_name']) ) {
						// get rid of extension, if exists
						$pos = strrpos($params['search_filename'], '.');
						$search['search_name'] = ( $pos===false? $params['search_filename'] : substr($params['search_filename'], 0, $pos) );

					} else {
						$search['search_name_wildcard'] = $params['search_filename'];
					}
				}

				if( !empty($params['search_info']) )
					$search['search_info'] = $params['search_info'];

				if( !empty($params['search_keywords']) ) {

					$keywords = new AssetKeywords_list();
					$result = $keywords->get_keywords_by_fulltext($params['search_keywords']);

					if($result) {
						$ids = array();
						foreach($result as $row)
							$ids[] = $row->get_asset_id();

						if($ids)
							$search['search_id'] = $ids;
					}
				}

				if( !empty($params['search_date_after']) )
					$search['search_date_after'] = $params['search_date_after'];

				$assets->search( $search, $params['search_sort_by'], $params['search_order'], $params['search_limit'], $params['offset']);

			}

			$assets->set_pagination($cur_page);


			if( empty($search_quick) ) { // if not filepicker, prefetch keywords for each record

				$asset_ids = $assets->get_list_ids();
				$keylist = new AssetKeywords_list();
				$keywords = $keylist->search( array( 'search_asset_id_list'=>$asset_ids) );

				foreach($keywords as $key) {
					$key_id = $key->asset_id;
					$item =& $assets->list[ $key_id ];
					if( empty($item->keywords) ) {
						$item->keywords = array($key->keyword);
					} else {
						if (!is_array($item->keywords)) {
							$item->keywords = array_map('trim', explode(',', $item->keywords));
						}
						$item->keywords[] = $key->keyword;
					}
					$item->keywords = implode(', ', array_unique($item->keywords));
				}
				foreach($assets->list as $i=>$k){
					if(!$k->keywords)
						$assets->list[$i]->keywords='';
				}
			}

			$this->_template->assign('dir', $params['search_dir']);
			$this->_template->assign('file_list_object', $assets);
			$this->_template->assign('file_list', $assets->list);
			$this->_template->assign('box_id', $params['box_id']);
			$this->_template->assign('prefix', $params['prefix']);
		}
	}

#EOF