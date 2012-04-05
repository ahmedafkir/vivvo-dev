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
	 * @see  'lib/vivvo/core/ArticlesSchedule.class.php'
 	 */
	require_once(VIVVO_FS_ROOT . 'lib/vivvo/core/ArticlesSchedule.class.php');

	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");


	/**
	 * Schedule service class
	 *
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class SchedulesService extends vivvo_service {

		/**
		 * Add new schedule for an article
		 *
		 * @param	array	$schedule_params
		 * @return	bool
		 */
		public function add_schedule($schedule_params) {

			if( !vivvo_hooks_manager::call('schedule_add', array(&$schedule_params) ) )
				return vivvo_hooks_manager::get_status();

			$sm = $this->get_site_manager();

			if ( $sm->user && $sm->user->can('MANAGE_SCHEDULES') ) {

				$article_id = (int)($schedule_params['article_id']);

				if ($article_id > 0) {

					$duration = (int)($schedule_params['duration']);
					$status = (int)($schedule_params['status']);

					if ( strlen( $time_mask = trim($schedule_params['time_mask']) ) ) {

						$schedule = ArticlesSchedule::createFromCronMask($time_mask, $article_id);

						if ($schedule) {

							if ($duration > 0) {
								$schedule->set_duration($duration);
							}

							$schedule->sql_insert();

							return true;

						} else {
							$this->set_error_code(2802);
						}
					} else {
						//TODO: hendlovati alternativni set parametara (min/hour/day/... umesto time_mask)
						$this->set_error_code(2801);
					}
				} else {
					$this->set_error_code(2801);
				}
			} else {
				$this->set_error_code(2803);
			}

			return false;
		}


		/**
		 * Edit article schedule
		 *
		 * @param	array	$schedule_params
		 * @return	bool
		 */
		public function edit_schedule($schedule_params) {

			if( !vivvo_hooks_manager::call('schedule_edit', array(&$schedule_params) ) )
				return vivvo_hooks_manager::get_status();

			$sm = $this->get_site_manager();

			if ( $sm->user && $sm->user->can('MANAGE_SCHEDULES') ) {

				$schedule_list = new ArticlesSchedule_list($sm);
				$schedule = $schedule_list->get_schedule_by_id( (int)($schedule_params['id']) );

				if ($schedule) {

					$new_schedule = ArticlesSchedule::createFromCronMask( $schedule_params['time_mask'], (int)($schedule_params['article_id']) );
					if ($new_schedule) {

						$new_schedule->set_id( $schedule->get_id() );
						$new_schedule->set_duration( $schedule_params['duration'] );

						$new_schedule->sql_update();

						return true;

					} else {
						$this->set_error_code(2802);
					}
				} else {
					$this->set_error_code(2804);
				}
			} else {
				$this->set_error_code(2803);
			}

			return false;
		}


		/**
		 * Delete article schedule
		 *
		 * @param	array	$schedule_params
		 * @return	bool
		 */
		public function delete_schedule($schedule_params) {

			if( !vivvo_hooks_manager::call('schedule_delete', array(&$schedule_params) ) )
				return vivvo_hooks_manager::get_status();

			$sm = $this->get_site_manager();

			if ( $sm->user && $sm->user->can('MANAGE_SCHEDULES') ) {

				$schedule_list = new ArticlesSchedule_list($sm);
				$schedule = $schedule_list->get_schedule_by_id( (int)($schedule_params['id']) );

				if ($schedule) {

					$schedule->sql_delete();

					return true;

				} else {
					$this->set_error_code(2805);
				}
			} else {
				$this->set_error_code(2803);
			}

			return false;
		}
	}
?>