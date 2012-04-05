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
 * @see  'vivvo_post.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

define("PC_MINUTE",	1);
define("PC_HOUR",	2);
define("PC_DOM",	3);
define("PC_MONTH",	4);
define("PC_DOW",	5);
define("PC_CMD",	7);
define("PC_COMMENT",	8);
define("PC_CRONLINE", 20);

	/**
	 * vivvo_cron_manager class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	cron
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 */
	class vivvo_cron_manager extends vivvo_object {

		function parse_element($element, &$targetArray, $numberOfElements) {
			$subelements = explode(",",$element);
			for ($i=0;$i<$numberOfElements;$i++) {
				$targetArray[$i] = $subelements[0]=="*";
			}

			for ($i=0;$i<count($subelements);$i++) {
				if (preg_match("~^(\\*|([0-9]{1,2})(-([0-9]{1,2}))?)(/([0-9]{1,2}))?$~",$subelements[$i],$matches)) {
					if ($matches[1]=="*") {
						$matches[2] = 0;		// from
						$matches[4] = $numberOfElements;		//to
					} elseif (empty($matches[4])) {
						$matches[4] = $matches[2];
					}
					if (empty($matches[5]) || $matches[5][0]!="/") {
						$matches[6] = 1;		// step
					}
					for ($j = $matches[2] << 0, $inc = $matches[6] << 0, $end = $matches[4] << 0; $j <= $end; $j += $inc) {
						$targetArray[$j] = TRUE;
					}
				}
			}
		}

		function inc_date(&$dateArr, $amount, $unit) {

			if($unit=="mon") {
				$dateArr["mon"] += $amount;
				$dateArr["mday"] = 1;
				$dateArr["hours"] = 0;
				$dateArr["minutes"] = 0;
				$dateArr["seconds"] = 0;

			} elseif ($unit=="mday") {
				$dateArr["mday"] += $amount;
				$dateArr["hours"] = 0;
				$dateArr["minutes"] = 0;
				$dateArr["seconds"] = 0;

			} elseif ($unit=="hour") {
				$dateArr["hours"] += $amount;
				$dateArr["minutes"] = 0;
				$dateArr["seconds"] = 0;

			} elseif ($unit=="minute") {
				$dateArr["minutes"] += $amount;
				$dateArr["seconds"] = 0;
			}

			$date = mktime($dateArr["hours"], $dateArr["minutes"], 0, $dateArr["mon"], $dateArr["mday"], $dateArr["year"]);

			$dateArr = getdate($date);

		}

		function get_last_scheduled_run_time($job, $last_execute) {
			$extjob = Array();

			$last_execute = $last_execute + 60 - ($last_execute % 60);
			vivvo_cron_manager::parse_element($job[PC_MINUTE], $extjob[PC_MINUTE], 60);
			vivvo_cron_manager::parse_element($job[PC_HOUR], $extjob[PC_HOUR], 24);
			vivvo_cron_manager::parse_element($job[PC_DOM], $extjob[PC_DOM], 31);
			vivvo_cron_manager::parse_element($job[PC_MONTH], $extjob[PC_MONTH], 12);
			vivvo_cron_manager::parse_element($job[PC_DOW], $extjob[PC_DOW], 7);

			$dateArr = getdate($last_execute);
			$minutesAhead = 0;

			$i = 0;
			while (
				$minutesAhead < 525600 AND
				(
					!$extjob[PC_MINUTE][$dateArr["minutes"]] OR
					!$extjob[PC_HOUR][$dateArr["hours"]] OR
					(!$extjob[PC_DOM][$dateArr["mday"]] OR !$extjob[PC_DOW][$dateArr["wday"]]) OR
					!$extjob[PC_MONTH][$dateArr["mon"]]
				)
			)
			{
				if (!$extjob[PC_MONTH][$dateArr["mon"]]) {
					vivvo_cron_manager::inc_date($dateArr,1,"mon");
					$minutesAhead+=1440;
				}elseif (!$extjob[PC_DOM][$dateArr["mday"]] OR !$extjob[PC_DOW][$dateArr["wday"]]) {
					vivvo_cron_manager::inc_date($dateArr,1,"mday");
					$minutesAhead+=1440;
				}elseif (!$extjob[PC_HOUR][$dateArr["hours"]]) {
					vivvo_cron_manager::inc_date($dateArr,1,"hour");
					$minutesAhead+=60;
				}elseif (!$extjob[PC_MINUTE][$dateArr["minutes"]]) {
					vivvo_cron_manager::inc_date($dateArr,1,"minute");
					$minutesAhead++;
				}
				$i++;
				if ($i > 1000)
					return false;
			}

			return mktime($dateArr["hours"],$dateArr["minutes"],0,$dateArr["mon"],$dateArr["mday"],$dateArr["year"]);
		}

		function parse_cron_line($cron_str, $last_execute) {
			if (preg_match("~^([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-7,/*]+|(-|/|Sun|Mon|Tue|Wed|Thu|Fri|Sat)+)$~i", $cron_str, $job)) {
				if ($job[PC_DOW][0]!='*' AND !is_numeric($job[PC_DOW])) {
					$job[PC_DOW] = str_replace(
						Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat"),
						Array(0,1,2,3,4,5,6),
						$job[PC_DOW]);
				}
			}else{
				return false;
			}

			return vivvo_cron_manager::get_last_scheduled_run_time($job, $last_execute);
		}

		function execute (){

			$cron_list = new vivvo_cron_list();
			$cron_list->search( array('search_scheduled' => time()) );

			if (!empty($cron_list->list)){
				$pm = new vivvo_post_master();

				foreach($cron_list->list as $cron_entry){
					$now = VIVVO_START_TIME;
					$scheduled = $cron_entry->get_nextrun();

					// calculate next run time (added 30 secs safety margine to avoid edge problems)
					$next = $this->parse_cron_line($cron_entry->time_mask, $now + 30);

					// if illegal cron format skip it
					if ($next === false)
						continue;

					//update cron record
					$cron_entry->set_lastrun( $now );
					$cron_entry->set_nextrun( $next );
					$pm->set_data_object($cron_entry);
					$pm->sql_update();

					// if no schedule yet (or file property is not set), skip this one time
					if (!$scheduled or empty($cron_entry->file)) {
						continue;
					}

					// include script file
					if (file_exists(VIVVO_FS_INSTALL_ROOT . $cron_entry->file)) {
						require_once VIVVO_FS_INSTALL_ROOT . $cron_entry->file;
					} elseif (file_exists(VIVVO_FS_PLUGIN_ROOT . $cron_entry->file)) {
						require_once VIVVO_FS_PLUGIN_ROOT . $cron_entry->file;
					}

					$callback = false;

					// call cron handler
					if (!empty($cron_entry->class)) {
						if (class_exists($cron_entry->class) and in_array($cron_entry->method, get_class_methods($cron_entry->class))) {
							$callback = array($cron_entry->class, $cron_entry->method);
						}
					} elseif (!empty($cron_entry->method) and function_exists($cron_entry->method)) {
						$callback = $cron_entry->method;
					}

					if ($callback) {
						$args = unserialize($cron_entry->arguments);
						if (!is_array($args)) {
							$args = array();
						}

						array_unshift($args, 0);
						$args[0] =& vivvo_lite_site::get_instance();

						call_user_func_array($callback, $args);
					}

				} // end_foreach
			}
		}

		function create_time_mask($params){
			$time_mask = '';
			if (isset($params['i'])){
				$time_mask .= $params['i'] . ' ';
			}elseif (isset($params['every_i'])){
				$time_mask .= '*/' . $params['every_i'] . ' ';
			}else{
				$time_mask .= '0 ';
			}

			if (isset($params['H'])){
				$time_mask .= $params['H'] . ' ';
			}elseif (isset($params['every_H'])){
				$time_mask .= '*/' . $params['every_H'] . ' ';
			}else{
				$time_mask .= '* ';
			}

			if (isset($params['d'])){
				$time_mask .= $params['d'] . ' ';
			}elseif (isset($params['every_d'])){
				$time_mask .= '*/' . $params['every_d'] . ' ';
			}else{
				$time_mask .= '* ';
			}

			if (isset($params['m'])){
				$time_mask .= $params['m'] . ' ';
			}elseif (isset($params['every_m'])){
				$time_mask .= '*/' . $params['every_m'] . ' ';
			}else{
				$time_mask .= '* ';
			}

			if (isset($params['w'])){
				$time_mask .= $params['w'];
			}elseif (isset($params['every_w'])){
				$time_mask .= '*/' . $params['every_w'];
			}else{
				$time_mask .= '*';
			}
			return $time_mask;
		}

		/**
		 * Add/edit cron job
		 *
		 * @param array|string $time_mask 	Unix crontab time mask/array @see vivvo_cron_manager::create_time_mask
		 * @param string $file				File containg the script
		 * @param string $class				Class name
		 * @param string $method			Method/function name
		 * @param array $arguments			Arguments for cron function
		 */
		function cron_job($time_mask, $file, $class, $method, $arguments = array()){
			if (is_array($time_mask)){
				$time_mask = $this->create_time_mask($time_mask);
			}
			$cron_list = new vivvo_cron_list();
			$cron_job = $cron_list->get_cron_job_by_hash(md5($file . $class . $method . serialize($arguments)));
			if ($cron_job){
				$pm = new vivvo_post_master();

				$cron_job->set_time_mask($time_mask);
				$cron_job->set_nextrun(0);

				$pm->set_data_object($cron_job);

				$pm->sql_update();
			}else{
				$pm = new vivvo_post_master();
				$cron_job = new vivvo_cron();
				$cron_job->set_time_mask($time_mask);
				$cron_job->set_file($file);
				$cron_job->set_class($class);
				$cron_job->set_method($method);
				$cron_job->set_arguments(serialize($arguments));
				$cron_job->set_hash(md5 ($file . $class . $method . serialize($arguments)));
				$pm->set_data_object($cron_job);
				$pm->sql_insert();
			}
		}

		function delete_cron_job($file, $class, $method, $arguments = array()){
			$cron_list = new vivvo_cron_list();
			$cron_job = $cron_list->get_cron_job_by_hash(md5 ($file . $class . $method . serialize($arguments)));

			if ($cron_job){
				$pm = new vivvo_post_master();
				$cron_list->sql_delete_list($pm);
			}
		}

		/**
		 * Register cron task in configuration table
		 *
		 * @param string $name				Cron task name
		 * @param string $template			Cron task configuration template
		 * @param string $file				File containg the script
		 * @param string $class				Class name
		 * @param string $method			Method/function name
		 * @param array $arguments			Arguments for cron function
		 */
		function register_cron_task ($name, $template, $file, $class, $method, $arguments = array()){
			if ($name){
				$configuration = vivvo_lite_site::get_instance()->get_configuration();
				$configuration->add_conf($name, 'template', $template, 'cron_task');
				$configuration->add_conf($name, 'file', $file, 'cron_task');
				$configuration->add_conf($name, 'class_name', $class_name, 'cron_task');
				$configuration->add_conf($name, 'method', $method, 'cron_task');
				$configuration->add_conf($name, 'arguments', serialize($arguments), 'cron_task');
			}
		}

		/**
		 * Remove cron task from configuration table
		 *
		 * @param string $name				Cron task name
		 */
		function unregister_cron_task ($name){
			if ($name){
				$configuration = vivvo_lite_site::get_instance()->get_configuration();
				$configuration->remove_from_module('cron_task', $name);
			}
		}
	}

	/**
	 * vivvo_cron object
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	cron
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 * @see			vivvo_post_object
	 */
	class vivvo_cron extends vivvo_post_object {

		/**
		 * id
		 * Database field type:	int(10) unsigned
		 * Null status:		NO
		 *
		 * @var	integer	$id
		 */
		var $id;

		/**
		 * lastrun
		 * Database field type:	int(10)
		 * Null status:		NO
		 *
		 * @var	integer	$lastrun
		 */
		var $lastrun;

		/**
		 * time of the next scheduled run
		 * Database field type:	int(10)
		 * Null status:		NO
		 *
		 * @var	integer	$nextrun
		 */
		var $nextrun;

		/**
		 * time_mask
		 * Database field type:	varchar(32)
		 * Null status:		NO
		 *
		 * @var	string	$time_mask
		 */
		var $time_mask;

		/**
		 * file
		 * Database field type:	varchar(255)
		 * Null status:		NO
		 *
		 * @var	string	$file
		 */
		var $file;

		/**
		 * class
		 * Database field type:	varchar(255)
		 * Null status:		YES
		 *
		 * @var	string	$class
		 */
		var $class;


		/**
		 * method
		 * Database field type:	varchar(255)
		 * Null status:		NO
		 *
		 * @var	string	$method
		 */
		var $method;

		/**
		 * arguments
		 * Database field type:	varchar(255)
		 * Null status:		NO
		 *
		 * @var	string	$arguments
		 */
		var $arguments;

		/**
		 * hash
		 * Database field type:	smallint(5)
		 * Null status:		NO
		 *
		 * @var	integer	$hash
		 */
		var $hash;

		var $_sql_table = 'cron';

		/**
		 * Sets {@link $id}
		 *
		 * @param	integer	$id
		 */
		function set_id($id){
			$this->id = $id;
		}

		/**
		 * Sets {@link $lastrun}
		 *
		 * @param	integer	$lastrun
		 */
		function set_lastrun($lastrun){
			$this->lastrun = $lastrun;
		}

		/**
		 * Sets {@link $nextrun}
		 *
		 * @param	integer	$nextrun
		 */
		function set_nextrun($nextrun){
			$this->nextrun = $nextrun;
		}

		/**
		 * Sets {@link $time_mask}
		 *
		 * @param	string	$time_mask
		 */
		function set_time_mask($time_mask){
			$this->time_mask = $time_mask;
		}

		/**
		 * Sets {@link $file}
		 *
		 * @param	string	$file
		 */
		function set_file($file){
			$this->file = $file;
		}

		/**
		 * Sets {@link $class}
		 *
		 * @param	string	$class
		 */
		function set_class($class){
			$this->class = $class;
		}

		/**
		 * Sets {@link $method}
		 *
		 * @param	string	$method
		 */
		function set_method($method){
			$this->method = $method;
		}

		/**
		 * Sets {@link $arguments}
		 *
		 * @param	string	$arguments
		 */
		function set_arguments($arguments){
			$this->arguments = $arguments;
		}

		/**
		 * Sets {@link $hash}
		 *
		 * @param	integer	$hash
		 */
		function set_hash($hash){
			$this->hash = $hash;
		}



		/**
		 * Gets $id
		 *
		 * @return	integer
		 */
		function get_id(){
			return $this->id;
		}
		/**
		 * Gets $lastrun
		 *
		 * @return	integer
		 */
		function get_lastrun(){
			return $this->lastrun;
		}

		/**
		 * Gets $nextrun
		 *
		 * @return	integer
		 */
		function get_nextrun(){
			return intval( $this->nextrun );
		}

		/**
		 * Gets $time_mask
		 *
		 * @return	string
		 */
		function get_time_mask(){
			return $this->time_mask;
		}
		/**
		 * Gets $file
		 *
		 * @return	string
		 */
		function get_file(){
			return $this->file;
		}

		/**
		 * Gets $class
		 *
		 * @return	string
		 */
		function get_class(){
			return $this->class;
		}

		/**
		 * Gets $method
		 *
		 * @return	string
		 */
		function get_method(){
			return $this->method;
		}
		/**
		 * Gets $arguments
		 *
		 * @return	string
		 */
		function get_arguments(){
			return $this->arguments;
		}
		/**
		 * Gets $hash
		 *
		 * @return	integer
		 */
		function get_hash(){
			return $this->hash;
		}
	}

	/**
	 * vivvo cron list
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	cron
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 * @see			vivvo_db_list
	 */
	class vivvo_cron_list extends vivvo_db_list {
		var $_sql_table = 'cron';
		var $post_object_type = 'vivvo_cron';

		function _default_query(){
			$this->_query->set_from( VIVVO_DB_PREFIX . 'cron ');
			$this->_query->add_fields('*');
		}

		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where('(id = \'' . $condition . '\')');
				break;
				case 'lastrun':
					$this->_query->add_where('(lastrun = \'' . $condition . '\')');
				break;
				case 'nextrun':
					$this->_query->add_where('(nextrun = \'' . $condition . '\')');
				break;
				case 'scheduled':
					$this->_query->add_where('(nextrun < '. intval($condition) .' OR nextrun IS NULL)');
				break;
				case 'time_mask':
					$this->_query->add_where('(time_mask = \'' . $condition . '\')');
				break;
				case 'file':
					$this->_query->add_where('(file = \'' . $condition . '\')');
				break;
				case 'method':
					$this->_query->add_where('(method = \'' . $condition . '\')');
				break;
				case 'arguments':
					$this->_query->add_where('(arguments = \'' . $condition . '\')');
				break;
				case 'hash':
					$this->_query->add_where('(hash = \'' . $condition . '\')');
				break;
			}
		}

		/**
		 * Advaced search list generator
		 *
		 * @param	array	$params	Search parameters
		 * @param	string	$order	Order parameters
		 * @param	integer	$limit	Limit
		 * @param	integer	$offset	Offset
		 * @return	array	Array of content_items
		 */
		function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
			//search_query
			if (isset($params['search_id'])){
				$this->add_filter('id',$params['search_id']);
			}
			if (isset($params['search_lastrun'])){
				$this->add_filter('lastrun',$params['search_lastrun']);
			}
			if (isset($params['search_nextrun'])){
				$this->add_filter('nextrun',$params['search_nextrun']);
			}
			if (isset($params['search_scheduled'])){
				$this->add_filter('scheduled',$params['search_scheduled']);
			}
			if (isset($params['search_time_mask'])){
				$this->add_filter('time_mask',$params['search_time_mask']);
			}
			if (isset($params['search_file'])){
				$this->add_filter('file',$params['search_file']);
			}
			if (isset($params['search_method'])){
				$this->add_filter('method',$params['search_method']);
			}
			if (isset($params['search_arguments'])){
				$this->add_filter('arguments',$params['search_arguments']);
			}
			if (isset($params['search_hash'])){
				$this->add_filter('hash',$params['search_hash']);
			}

			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'lastrun':
					$this->_query->add_order('lastrun' . $search_direction);
					break;
				case 'nextrun':
					$this->_query->add_order('nextrun' . $search_direction);
					break;
				case 'time_mask':
					$this->_query->add_order('time_mask' . $search_direction);
					break;
				case 'file':
					$this->_query->add_order('file' . $search_direction);
					break;
				case 'method':
					$this->_query->add_order('method' . $search_direction);
					break;
				case 'arguments':
					$this->_query->add_order('arguments' . $search_direction);
					break;
				case 'hash':
					$this->_query->add_order('hash' . $search_direction);
					break;

				default:
					$order = 'id';
					$this->_query->add_order('id' . ' DESC');
					break;
			}
			$limit = (int) $limit;
			if ($limit)	$this->_query->set_limit($limit);
			$offset = (int) $offset;
			if ($offset) $this->_query->set_offset($offset);
			$this->_default_query(true);
			if ($set_list){
				$this->set_list();
				return $this->list;
			}
		}

		function get_cron_job_by_hash ($hash){
			$this->_query->reset_query();
			$this->search(array('search_hash'=>$hash));
			if (empty($this->list)){
				return false;
			}else{
				return current($this->list);
			}
		}

	}

#EOF