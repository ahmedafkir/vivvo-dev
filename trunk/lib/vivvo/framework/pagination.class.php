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
 * Vivvo CMS
 *
 * LICENSE:
 *
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * @copyright	SpoonLabs
 * @package		vivvo_framework
 * @subpackage	framework
 * @version		$Revision: 5385 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */

	/**
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */

	class pagination{
		var $max_page;
		var $record_per_page;
		var $href;
		var $_template;

		function generate_href($pageno){
			//if (VIVVO_FRIENDY_URL != 0 && (strpos($this->href,VIVVO_URL . 'admin') === false)) {
			if ((strpos($this->href,VIVVO_URL . 'admin') === false)) {
				if (strpos($this->href,'?') !== false){
					if (strpos($this->href,'pg=') !== false){
						$href = preg_replace('/&(amp;)*pg=\d+/',"&pg=$pageno", $this->href);
						return "$href";
					}else{
						return $this->href . "&pg=$pageno";
					}
				}elseif (preg_match('/\.\d+(?=(\.\w{2})?\.html)/', $this->href)){
					$href = preg_replace('/\.\d+(?=(\.\w{2})?\.html)/',".$pageno", $this->href);
				}else{
					$href = $this->href;
					if (substr($this->href, -1) !== '/') $href .= '/';
					$href .= 'index.' . $pageno . '.html';
				}
				return $href;
			}else{
				if (strpos($this->href,'pg=') !== false){
					$href = preg_replace('/&(amp;)*pg=\d+/',"&pg=$pageno", $this->href);
					return "$href";
				}else{
					if (strpos($this->href,'?') === false){
						return $this->href . "?pg=$pageno";
					}else {
						return $this->href . "&pg=$pageno";
					}
				}
			}
		}

		function numer_of_pages($total_rows){
			if ($this->record_per_page != 0){
				return ceil($total_rows/$this->record_per_page);
			}else{
				return 0;
			}
		}

		function generate_pagelist($total_rows = 0, $pageno = false){
			if ($total_rows <= 0) return '';

			if(!$pageno) $pageno=1;

			$this->_template->assign('current_page', $pageno);

			$numofpages = $this->numer_of_pages($total_rows);
			$this->_template->assign('total_records', $total_rows);

			$start_page = floor(($pageno -1) / $this->max_page) * $this->max_page + 1;

			//previous page group
			if ($pageno > $this->max_page){
				$this->_template->assign('previous_page_group', $this->generate_href($start_page - $this->max_page));
				$this->_template->assign('previous_page_group_number', strval($start_page - $this->max_page));
			}

			//previous page
			if ($pageno > 1 ){
				$this->_template->assign('previous_page', $this->generate_href($pageno - 1));
				$this->_template->assign('previous_page_number', strval($pageno - 1));
			}

			//pagelist
			$pageslist = array();
			$this->max_page = min ($this->max_page, $numofpages);
			if ($this->max_page > 1){
				for ($i = 0; ($i <= $this->max_page - 1) && ($start_page + $i <= $numofpages); $i++){
					$pageslist[$i]['number'] = $start_page + $i;
					if ($start_page + $i == $pageno){
						$pageslist[$i]['href'] = '';
					}else{
						$pageslist[$i]['href'] = $this->generate_href($start_page + $i);
					}
				}
				$this->_template->assign('page_list', $pageslist);
			}

			$displaying = ($pageno - 1) * $this->record_per_page + 1 . ' - ';
			$displaying .= (($pageno * $this->record_per_page) > $total_rows) ? $total_rows: $pageno * $this->record_per_page;
			$this->_template->assign('displaying', $displaying);

			//next page
			if ($pageno < $numofpages){
				$this->_template->assign('next_page', $this->generate_href($pageno + 1));
				$this->_template->assign('next_page_number', strval($pageno + 1));
			}

			//next page group
			if ($start_page + $this->max_page <= $numofpages){
				$this->_template->assign('next_page_group', $this->generate_href($start_page + $this->max_page));
				$this->_template->assign('next_page_group_number', strval($start_page + $this->max_page));
			}
		}

		function set_template($template){
			$this->_template = $template;
		}


		function pagination($record_per_page, $max_page, $href = ''){
			if ($href == '') {
				$this->href = CURRENT_URL;
			}
			$this->href = str_replace('&', '&amp;' , $this->href);
			$this->href = str_replace('&amp;&amp;', '&amp;' , $this->href);

			$this->record_per_page = (int) $record_per_page;
			$this->max_page = (int) $max_page;
		}
	}

	/**
	 * Box pagination
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	box
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see module
	 */

	class box_pagination extends module {
		/**
		 * Pagination
		 *
		 * @var pagination
		 */
		var $pagination;

		var $_default_template_file = 'system/box_default/box_pagination.tpl';

		/**
		 * Generate box output
		 *
		 * @param array $params Parameters
		 */
		function generate_output($params){

			$this->set_template($params);

			if (isset($params['max_page'])){
				$max_page = (int) $params['max_page'];
			}else{
				$max_page = 10;
			}

			if (isset($params['list']) && is_a($params['list'],'vivvo_db_list')){
				$paging_params = $params['list']->get_pagination();
				$this->pagination = new pagination($paging_params['pg_records_per_page'], $max_page, $paging_params['pg_href']);
				$this->pagination->set_template($this->_template);
				$this->pagination->generate_pagelist($paging_params['pg_total'], $paging_params['pg_current']);

			}elseif (isset($params['list']) && is_a($params['list'],'vivvo_file_paged_list')){
				$paging_params = $params['list']->get_pagination();
				$this->pagination = new pagination($paging_params['pg_records_per_page'], $max_page, $paging_params['pg_href']);
				$this->pagination->set_template($this->_template);
				$this->pagination->generate_pagelist($paging_params['pg_total'], $paging_params['pg_current']);
			}else{
				$um = vivvo_lite_site::get_instance()->get_url_manager();

				$this->pagination = new pagination($um->get_param('pg_records_per_page'), $max_page, $um->get_param('pg_href'));
				$this->pagination->set_template($this->_template);
				$this->pagination->generate_pagelist($um->get_param('pg_total'), $um->get_param('pg_current'));
			}

			if (isset ($params['title'])){
				$this->_template->assign('title', $params['title']);
			}

			$this->_template->assign('box_id', $params['box_id']);
		}
	}

#EOF