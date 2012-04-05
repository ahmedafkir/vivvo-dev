<?php
/* =============================================================================
 * $Revision: 4834 $
 * $Date: 2010-03-30 11:39:23 +0200 (Tue, 30 Mar 2010) $
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
	 * @see	'lib/vivvo/framework/vivvo_plugin.php'
	 */
	class_exists('module') or require VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php';

	/**
	 * @see	'lib/vivvo/core/ArticlesRevisions.class.php'
	 */
	class_exists('ArticlesRevisions') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesRevisions.class.php';

	/**
	 * Article revisions module
	 *
	 * @version		$Revision: 4834 $
	 * @copyright	Spoonlabs
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class box_article_revisions extends module {

		/**
		 * Generates box output
		 *
		 * @param	array	$params		Parameters passed from VTE
		 */
		public function generate_output($params = array()) {

			$this->set_template($params);

			$list = ArticlesRevisions_list::factory();
			$list->search_from_params($params);

			if (!empty($list->list)) {
				$list->set_pagination($search_params['pg']);
				$this->_template->assign('article_revision_list_object', $list);
				$this->_template->assign('article_revision_list', $list->list);
			} else {
				$false = false;
				$this->_template->assign('article_revision_list_object', $false);
				$this->_template->assign('article_revision_list', $false);
			}

			if (isset($params['TITLE'])) {
				$this->_template->assign('TITLE', $params['TITLE']);
			}

			if (isset($params['BOX_ID'])) {
				$this->_template->assign('BOX_ID', $params['BOX_ID']);
			}
		}
	}
?>