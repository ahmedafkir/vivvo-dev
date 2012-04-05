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
	 * Service definition array
	 *
	 * @var	array
	 */
	$service_definition = array(

		'schedule.add' => array(
			'function' => 'schedule_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Add new schedule for an article.'
		),
		'schedule.edit' => array(
			'function' => 'schedule_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Edit article schedule.'
		),
		'schedule.delete' => array(
			'function' => 'schedule_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Delete article schedule.'
		),
		'schedule.save_changes' => array(
			'function' => 'schedule_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Add new schedule for an article.'
		)
	);

	/**
	 * Action handler function
	 *
	 * @param	&vivvo_lite_site	$sm
	 * @param	string				$action
	 * @param	string				$command
	 * @return	bool
	 */
	function schedule_service(&$sm, $action, $command) {

		if ($action != 'schedule') {
			return false;
		}

		require_once(VIVVO_FS_ROOT . 'lib/vivvo/service/Schedules.service.php');

		$service = new SchedulesService($sm);

		$dm = $sm->get_dump_manager();
		$um = $sm->get_url_manager();
		$lang = $sm->get_lang();

		switch ($command) {

			case 'add':
				$schedule_params = $um->get_param_regexp('/^schedule_/');
				if ( $service->add_schedule($schedule_params) ) {
					$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_SCHEDULE_ADD_SUCCESS'));
					header('X-Vivvo-Action-Status: 1');
					vivvo_hooks_manager::call('schedule_add_postaction');
					return true;
				}
			break;

			case 'edit':
				$schedule_params = $um->get_param_regexp('/^schedule_/');
				if ( $service->edit_schedule($schedule_params) ) {
					$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_SCHEDULE_EDIT_SUCCESS'));
					header('X-Vivvo-Action-Status: 1');
					vivvo_hooks_manager::call('schedule_edit_postaction');
					return true;
				}
			break;

			case 'delete':
				if ( $service->delete_schedule( $um->get_param('schedule_id') ) ) {
					$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_SCHEDULE_DELETE_SUCCESS'));
					header('X-Vivvo-Action-Status: 1');
					vivvo_hooks_manager::call('schedule_delete_postaction');
					return true;
				}
			break;

			case 'save_changes':

				$article_id = $um->get_param('article_id');

				foreach ($um->get_param('schedule') as $id => $mask) {
					if (!$service->edit_schedule(array(
						'id' => $id,
						'article_id' => $article_id,
						'time_mask' => $mask
					))) break 2;
				}

				foreach ($um->get_param('new_schedule') as $mask) {
					if (!$service->add_schedule(array(
						'article_id' => $article_id,
						'time_mask' => $mask
					))) break 2;
				}

				header('X-Vivvo-Action-Status: 1');
				return true;

			default:
		}

		header('X-Vivvo-Action-Status: 0');
		header('X-Vivvo-Action-Error: ' . json_encode($service->get_error_info()));
		return false;
	}
?>