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

	require_once (dirname(__FILE__) . '/admin_include.php');

	$um = $sm->get_url_manager();
	$pm = $sm->get_plugin_manager();

	if ($um->isset_param('plugin') && $pm->is_installed($um->get_param('plugin')) && !$um->isset_param('uninstall')) {

		$view = $pm->get_admin_view($plugin = $um->get_param('plugin'));

		define('CURRENT_ADMIN_VIEW_FILE', basename(__FILE__) . '?plugin=' . $plugin);
		define('IN_PLUGIN_PAGE', $plugin);

		if ($view) {
			$pm->load_admin_lang($um->get_param('plugin'));
			$view->run();
		} else {
			$plugin = $pm->get_plugin_definiton($um->get_param('plugin'));
			if ($plugin !== false) {
				$plugin->load_admin_lang();
				$plugin->load_admin_js();
				echo $plugin->get_admin_content($sm);
			}
		}
	} else {

		define('CURRENT_ADMIN_VIEW_FILE', basename(__FILE__));

		$view = $sm->get_admin_view('plugins');
		if ($view) {
			$view->run();
		}
	}

	ob_end_flush();
?>