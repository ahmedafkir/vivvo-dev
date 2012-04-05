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
	 * ArticlesSchedule object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 * @see			vivvo_post_object
	 */
	class ArticlesSchedule extends vivvo_post_object {

		/**
		 * id
		 * Database field type:	int(10)
		 * Null status:		NO
		 *
		 * @var	int	$id
		 */
		public $id;

		/**
		 * article_id
		 * Database field type:	int(11)
		 * Null status:		NO
		 *
		 * @var	int	$article_id
		 */
		public $article_id;

		/**
		 * minute
		 * Database field type:	bigint(20)
		 * Null status:		NO
		 *
		 * @var	array	$minute
		 */
		public $minute = array(0, 0);

		/**
		 * hour
		 * Database field type:	bigint(20)
		 * Null status:		NO
		 *
		 * @var	array	$hour
		 */
		public $hour = array(0, 0);

		/**
		 * dom
		 * Database field type:	bigint(20)
		 * Null status:		NO
		 *
		 * @var	array	$dom
		 */
		public $dom = array(0, 0);

		/**
		 * month
		 * Database field type:	bigint(20)
		 * Null status:		NO
		 *
		 * @var	array	$month
		 */
		public $month = array(0, 0);

		/**
		 * dow
		 * Database field type:	bigint(20)
		 * Null status:		NO
		 *
		 * @var	array	$dow
		 */
		public $dow = array(0, 0);

		/**
		 * year
		 * Database field type:	smallint(5)
		 * Null status:		YES
		 *
		 * @var	int	$year
		 */
		public $year;

		/**
		 * duration
		 * Database field type:	int(11)
		 * Null status:		YES
		 *
		 * @var	int	$duration
		 */
		public $duration;

		/**
		 * table name
		 * @var	string	$_sql_table
		 */
		var $_sql_table = 'articles_schedule';

		/**
		 * Sets {@link $id}
		 *
		 * @param	int	$id
		 */
		public function set_id($id) {
			$this->id = $id;
		}

		/**
		 * Sets {@link $article_id}
		 *
		 * @param	int	$article_id
		 */
		public function set_article_id($article_id) {
			$this->article_id = $article_id;
		}

		/**
		 * Sets {@link $minute}
		 *
		 * @param	mixed	$minute
		 */
		public function set_minute($minute) {
			$this->minute = is_array($minute) ? $minute : bigint_from_hex($minute);
		}

		/**
		 * Adds minute to set
		 *
		 * @param	int	$minute
		 */
		public function add_minute($minute) {
			bigint_set_bit($this->minute, $minute);
		}

		/**
		 * Removes minute from set
		 *
		 * @param	int	$minute
		 */
		public function remove_minute($minute) {
			bigint_clear_bit($this->minute, $minute);
		}



		/**
		 * Sets {@link $hour}
		 *
		 * @param	mixed	$hour
		 */
		public function set_hour($hour) {
			$this->hour = is_array($hour) ? $hour : bigint_from_hex($hour);
		}

		/**
		 * Adds hour to set
		 *
		 * @param	int	$hour
		 */
		public function add_hour($hour) {
			bigint_set_bit($this->hour, $hour);
		}

		/**
		 * Removes hour from set
		 *
		 * @param	int	$hour
		 */
		public function remove_hour($hour) {
			bigint_clear_bit($this->hour, $hour);
		}


		/**
		 * Sets {@link $dom}
		 *
		 * @param	mixed	$dom
		 */
		public function set_dom($dom) {
			$this->dom = is_array($dom) ? $dom : bigint_from_hex($dom);
		}

		/**
		 * Adds dom to set
		 *
		 * @param	int	$dom
		 */
		public function add_dom($dom) {
			bigint_set_bit($this->dom, $dom - 1);
		}

		/**
		 * Removes dom from set
		 *
		 * @param	int	$dom
		 */
		public function remove_dom($dom) {
			bigint_clear_bit($this->dom, $dom - 1);
		}



		/**
		 * Sets {@link $month}
		 *
		 * @param	mixed	$month
		 */
		public function set_month($month) {
			$this->month = is_array($month) ? $month : bigint_from_hex($month);
		}

		/**
		 * Adds month to set
		 *
		 * @param	int	$month
		 */
		public function add_month($month) {
			bigint_set_bit($this->month, $month - 1);
		}

		/**
		 * Removes month from set
		 *
		 * @param	int	$month
		 */
		public function remove_month($month) {
			bigint_clear_bit($this->month, $month - 1);
		}



		/**
		 * Sets {@link $dow}
		 *
		 * @param	mixed	$dow
		 */
		public function set_dow($dow) {
			$this->dow = is_array($dow) ? $dow : bigint_from_hex($dow);
		}

		/**
		 * Adds dow to set
		 *
		 * @param	int	$dow
		 */
		public function add_dow($dow) {
			bigint_set_bit($this->dow, $dow - 1);
		}

		/**
		 * Removes dow from set
		 *
		 * @param	int	$dow
		 */
		public function remove_dow($dow) {
			bigint_clear_bit($this->dow, $dow - 1);
		}



		/**
		 * Sets {@link $year}
		 *
		 * @param	mixed	$year
		 */
		public function set_year($year) {
			$this->year = $year;
		}


		/**
		 * Sets {@link $duration}
		 *
		 * @param	int	$duration
		 */
		public function set_duration($duration) {
			$this->duration = $duration;
		}

		/**
		 * Gets $id
		 *
		 * @return	int
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Gets $article_id
		 *
		 * @return	int
		 */
		public function get_article_id() {
			return $this->article_id;
		}


		/**
		 * Gets $minute
		 *
		 * @return	array
		 */
		public function get_minute() {
			return $this->minute;
		}

		/**
		 * Checks if $minute is in set
		 *
		 * @param	int		$minute	(0-59)
		 * @return	bool
		 */
		public function has_minute($minute) {
			return bigint_is_bit_set($this->minute, $minute);
		}


		/**
		 * Gets $hour
		 *
		 * @return	array
		 */
		public function get_hour() {
			return $this->hour;
		}

		/**
		 * Checks if $hour is in set
		 *
		 * @param	int		$hour	(0-23)
		 * @return	bool
		 */
		public function has_hour($hour) {
			return bigint_is_bit_set($this->hour, $hour);
		}


		/**
		 * Gets $dom
		 *
		 * @return	array
		 */
		public function get_dom() {
			return $this->dom;
		}

		/**
		 * Checks if $dom is in set
		 *
		 * @param	int		$dom	(1-31)
		 * @return	bool
		 */
		public function has_dom($dom) {
			return bigint_is_bit_set($this->dom, $dom - 1);
		}



		/**
		 * Gets $month
		 *
		 * @return	array
		 */
		public function get_month() {
			return $this->month;
		}

		/**
		 * Checks if $month is in set
		 *
		 * @param	int		$month	(1-12)
		 * @return	bool
		 */
		public function has_month($month) {
			return bigint_is_bit_set($this->month, $month - 1);
		}


		/**
		 * Gets $dow
		 *
		 * @return	array
		 */
		public function get_dow() {
			return $this->dow;
		}

		/**
		 * Checks if $dow is in set
		 *
		 * @param	int		$dow	(1-7)
		 * @return	bool
		 */
		public function has_dow($dow) {
			return bigint_is_bit_set($this->dow, $dow - 1);
		}


		/**
		 * Gets $year
		 *
		 * @return	array
		 */
		public function get_year() {
			return $this->year;
		}

		/**
		 * Gets $duration
		 *
		 * @return	int
		 */
		public function get_duration() {
			return $this->duration;
		}


		/**
		 * Generates cron-task look-alike mask
		 *
		 * @param	bool		$years
		 * @return	string
		 */
		public function getCronMask() {

			static $cron_part;

			if ( !isset($cron_part) ) $cron_part = array(
				array('minute', range(0, 59)),
				array('hour', range(0, 23)),
				array('dom', range(1, 31)),
				array('month', range(1, 12)),
				array('dow', range(1, 7))
			);

			$mask = array();

			for ($i = 0; $i < 5; $i++) {

				$part = $cron_part[$i][0];

				if ($this->$part == self::$ALL) {

					$mask[] = '*';

				} else {

					$has = 'has_' . $part;

					$values = array();

					foreach ($cron_part[$i][1] as $value) {
						if ($this->$has($value)) {
							$values[] = $value;
						}
					}

					if ( ($count = count($values)) == 1) {
						$mask[] = $values[0];
					} else {

						$step = 0;
						$prev = -1;
						$range_begin = -1;
						$mask_values = array();

						for ($k = 0; $k < $count; $k++) {

							if ($prev >= 0) {
								if ($step > 0) {
									if ($values[$k] - $prev != $step) {
										$step = 0;
										if ($step > 0) {
											$mask_values[] = implode(',', array_slice($values, $range_begin, $k - $range_begin));
										} else {
											$mask_values[] = $values[$range_begin] . '-' . $values[$k];
										}
									}
								} else {
									$step = $values[$k] - $prev;
									$range_begin = $k - 1;
								}
							}

							if ($step > 0 && $k == $count - 1) {
								$begin = $values[$range_begin];
								$end = $values[$k];
								$min = $cron_part[$i][1][0];
								$max = end($cron_part[$i][1]);

								if ($begin - $step == $min) {
									$begin = $min;
								}

								if ($end + $max % $step == $max) {
									$end = $max;
								}

								if ($begin == $min && $end == $max) {
									$mask_values[] = '*/' . $step;
								} else {
									$mask_values[] = $begin . '-' . $end . '/' . $step;
								}
							}

							$prev = $values[$k];
						}

						$mask[] = implode(',', $mask_values);
					}
				}
			}

			return implode(' ', $mask);
		}


		/**
		 * Returns bitmask in hexadecimal notation, for use in sql queries
		 *
		 * @param	mixed	$values	 	(array, int, or comma separated string)
		 * @param	string	$type		('minute', 'hour', 'dom', 'month' or 'dow')
		 * @return	mixed	string or array
		 */
		public static function getHexMask($values, $type = null) {

			if ( $type !== null && !is_array($values) ) {
				$values = explode(',', $values);
			}

			foreach ($values as $key => $value) {

				if ( !is_array($value) ) {
					$value = explode(',', $value);
				}

				$mask = array(0, 0);

				$part = $type === null ? $key : $type;

				$diff = in_array( $part, array('dom', 'month', 'dow' ) ) << 0;

				for ($i = 0, $count = count($value); $i < $count; $i++) {

					$bit = $value[$i] - $diff;

					if ($bit >= 32 && $bit <= 63) {
						$mask[1] |= (1 << ($bit - 32));
					} else if ($bit >= 0 && $bit <= 31) {
						$mask[0] |= 1 << $bit;
					}
				}

				$values[$key] = sprintf('0x%08X%08X', $mask[0], $mask[1]);
			}

			return $type === null ? $values : reset($values);
		}

		/**
		 * all bits set constant
		 */
		public static $ALL = array(0xFFFFFFFF, 0xFFFFFFFF);

		/**
		 * Converts crontab look-alike mask into ArticlesSchedule object
		 *
		 * @param	string				$mask
		 * @param	int					$article_id
		 * @param	int					$year
		 * @return	ArticlesSchedule
		 */
		public static function createFromCronMask($mask, $article_id = 0, $year = false) {

			/**
			 *	EBNF grammar for mask format
			 *
			 *	mask ::= part part part part part
			 *	part ::= ( value | '*' ) step?
			 *	value ::= ( digit+ | digit+ '-' digit+ ) ( ',' value )*
			 *	step ::= '/' digit+
			 *	digit ::= '0' | '1' | '2' | '3' | '4' | '5' | '6' | '7' | '8' | '9'
			 */

			$parts = explode(' ', $mask);

			if ( count($parts) != 5 ) {
				return null;	// invalid format
			}

			static $cron_part;

			if ( !isset($cron_part) ) $cron_part = array(
				range(0, 59),											// minutes
				range(0, 23),											// hours
				range(1, 31),											// day of month
				range(1, 12),											// month
				range(1, 7) 											// day of week
			);

			foreach ($parts as $index => $value) {

				$step = 0;
				$values = array();

				if ( preg_match('|/(\d+)$|', $value, $matches) ) {
					$value = substr( $value, 0, -strlen($matches[0]) );
					$step = (int)($matches[1]);
				}

				if ($value == '*') {
					$values = $cron_part[$index];
				} else {

					$list = explode(',', $value);

					foreach ($list as $item) {

						if ( preg_match('|^(\d+)-(\d+)$|', $item, $matches) ) {
							$values += range($matches[1], $matches[2]);
						} else {
							$values[] = (int)$item;
						}
					}

					$min = $cron_part[$index][0];
					$max = end($cron_part[$index]);

					foreach ($values as &$val) {
						if ($val < $min || $val > $max) {
							$val = null;
						}
					}
					unset($val);
				}

				if ($step > 1) {

					foreach ($values as &$val) {
						if ($val % $step) {
							$val = null;
						}
					}
					unset($val);
				}

				$values = array_filter($values, create_function('$v', 'return $v !== null;'));

				if ( ($count = count($values)) == count($cron_part[$index]) ) {
					$parts[$index] = self::$ALL;	// *
				} else {

					$diff = ($index >= 2 && $index <= 4) << 0;

					$bigint = array(0, 0);

					$values = array_merge($values);	// reindex items

					for ($i = 0; $i < $count; $i++) {

						$bit = $values[$i] - $diff;

						if ($bit >= 32 && $bit <= 63) {
							$bigint[1] |= (1 << ($bit - 32));
						} else if ($bit >= 0 && $bit <= 31) {
							$bigint[0] |= 1 << $bit;
						}
					}

					$parts[$index] = $bigint;
				}
			}

			$schedule = new ArticlesSchedule(vivvo_lite_site::get_instance());

			$schedule->set_article_id($article_id);
			$schedule->set_minute($parts[0]);
			$schedule->set_hour($parts[1]);
			$schedule->set_dom($parts[2]);
			$schedule->set_month($parts[3]);
			$schedule->set_dow($parts[4]);
			$schedule->set_year($year ? $year !== false : date('Y') << 0);

			return $schedule;
		}

		/**
		 * Inserts object in database
		 */
		public function sql_insert() {

			$values = array();

			foreach (array('minute', 'hour', 'dom', 'month', 'dow') as $property) {
				$bigint = $this->$property;
				$values[$property] = sprintf('0x%08X%08X', $bigint[0], $bigint[1]);
			}

			if ($this->id !== null) {
				$values['id'] = (int)($this->id);
			}

			$values['article_id'] = (int)($this->article_id);

			if ($this->duration !== null) {
				$values['duration'] = (int)($this->duration);
			}

			if ($this->year !== null) {
				$values['year'] = (int)($this->year);
			}

			$names = implode( ', ', array_keys($values) );
			$values = implode( ', ', $values );

			$query = 'INSERT INTO ' . VIVVO_DB_PREFIX . "articles_schedule ($names) VALUES ($values)";

			$sm = vivvo_lite_site::get_instance();

			$sm->debug_push('sql:', $query);

			$sm->get_db()->exec($query);
		}

		/**
		 * Updates object in database
		 */
		public function sql_update() {

			if ( !is_numeric($this->id) ) {
				return;
			}

			$values = array();

			foreach (array('minute', 'hour', 'dom', 'month', 'dow') as $property) {
				$bigint = $this->$property;
				$values[$property] = sprintf('0x%08X%08X', $bigint[0], $bigint[1]);
			}

			$values['article_id'] = (int)($this->article_id);
			$values['duration'] = $this->duration === null ? 'NULL' : (int)($this->duration);
			$values['year'] = (int)($this->year);

			$updates = array();

			foreach ($values as $name => $value) {
				$updates[] = "$name = $value";
			}

			$updates = implode( ', ', $updates );

			$id = (int)($this->id);

			$query = 'UPDATE ' . VIVVO_DB_PREFIX . "articles_schedule SET $updates WHERE id = $id";

			$sm = vivvo_lite_site::get_instance();

			$sm->debug_push('sql:', $query);

			$sm->get_db()->exec($query);
		}

		/**
		 * deletes object from database
		 */
		public function sql_delete() {

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_post.php');

			$pm = new vivvo_post_master( vivvo_lite_site::get_instance() );

			$pm->set_data_object($this);
			$pm->sql_delete();
		}
	}

	/**
	 * ArticlesSchedule list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 * @see			vivvo_db_paged_list
	 */
	class ArticlesSchedule_list extends vivvo_db_list {

		/**
		 * table name
		 * @var	string	$_sql_table
		 */
		var $_sql_table = 'articles_schedule';

		/**
		 * class name
		 * @var	string	$post_object_type
		 */
		public $post_object_type = 'ArticlesSchedule';


		/**
		 * Insert all objects from list to database
		 *
		 * @param	vivvo_post_master	$post_master
		 * @return	bool
		 */
		public function sql_insert_list($post_master = null) {
			if ( !empty($this->list) ){
				foreach ($this->list as &$object){
					$object->sql_insert();
				}
				unset($object);
			}
			return true;
		}

		/**
		 * Update all objects from list
		 *
		 * @param	vivvo_post_master	$post_master	(not used)
		 * @param 	array 				$params 		Params to change assoc array
		 * @param 	array 				$restiction		Restriction ids
		 *
		 * @return	bool
		 */
		public function sql_update_list($post_master, $params, $restriction = NULL, $all_matching = false){

			$ids = $this->get_list_ids();

			if ($ids && is_array($restriction) && !empty($restriction)) {
				$ids = array_intersect($ids, $restriction);
			}

			if ($ids){
				$where = $this->id_key . ' IN ('. secure_sql_in($ids) .')';
			} elseif ($all_matching === true) {
				$where = str_replace('WHERE', ' ', $this->_query->get_where());
			}

			$updates = array();

			foreach ($params as $name => $value) {
				$updates[] = "$name = $value";
			}

			$updates = implode( ', ', $updates );

			$query = 'UPDATE ' . VIVVO_DB_PREFIX . "articles_schedule SET $updates WHERE $where";

			$sm = vivvo_lite_site::get_instance();

			$sm->debug_push('sql:', $query);

			$sm->get_db()->exec($query);

			return true;
		}


		/**
		 * default SQL query
		 */
		function _default_query() {

			$this->_query->reset_query();

			$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table);

			$this->_query->add_fields('id');
			$this->_query->add_fields('article_id');
			$this->_query->add_fields('duration');
			$this->_query->add_fields('year');
			$this->_query->add_fields('HEX(minute) AS minute');
			$this->_query->add_fields('HEX(hour) AS hour');
			$this->_query->add_fields('HEX(dom) AS dom');
			$this->_query->add_fields('HEX(month) AS month');
			$this->_query->add_fields('HEX(dow) AS dow');
		}

		/**
		 * adds filter to sql query
		 *
		 * @param	string	$type
		 * @param	string	$condition
		 */
		public function add_filter($type, $condition = ''){

			switch ($type){

				case 'id':
				case 'article_id':
				case 'duration':
				case 'year':
					$condition = (int)$condition;
					$this->_query->add_where("$type = $condition");
				break;

				case 'minute':
				case 'hour':
				case 'dom':
				case 'month':
				case 'dow':
					$condition = ArticlesSchedule::getHexMask($condition, $type);
					$this->_query->add_where("$type & $condition");
				break;

				case 'id_in':
				case 'article_id_in':
				case 'duration_in':
				case 'year_in':
					$condition = secure_sql_in($condition);
					$type = substr($type, 0, -3);
					$this->_query->add_where("$type IN ($condition)");
				break;

				case 'id_not_in':
				case 'article_id_not_in':
				case 'duration_not_in':
				case 'year_not_in':
					$condition = secure_sql_in($condition);
					$type = substr($type, 0, -7);
					$this->_query->add_where("$type NOT IN ($condition)");
				break;

				case 'duration_lt':
				case 'year_lt':
					$condition = (int)$condition;
					$type = substr($type, 0, -3);
					$this->_query->add_where("$type < $condition");
				break;

				case 'duration_lte':
				case 'year_lte':
					$condition = (int)$condition;
					$type = substr($type, 0, -4);
					$this->_query->add_where("$type <= $condition");
				break;

				case 'duration_gt':
				case 'year_gt':
					$condition = (int)$condition;
					$type = substr($type, 0, -3);
					$this->_query->add_where("$type > $condition");
				break;

				case 'duration_gte':
				case 'year_gte':
					$condition = (int)$condition;
					$type = substr($type, 0, -4);
					$this->_query->add_where("$type >= $condition");
				break;
			}
		}

		/**
		 * Advaced search list generator
		 *
		 * @param	array	$params	Search parameters
		 * @param	string	$order	Order parameters
		 * @param	int	$limit	Limit
		 * @param	int	$offset	Offset
		 * @return	array	Array of article schedules
		 */
		public function &search($params, $order = '', $direction = 'ascending', $limit = 0, $offset = 0, $set_list = true) {

			$search_filters = array(
				'search_id', 'search_article_id', 'search_duration', 'search_duration_lt',
				'search_duration_gt', 'search_duration_lte', 'search_duration_gte', 'search_id_in',
				'search_article_id_in', 'search_duration_in', 'search_id_not_in',
				'search_article_id_not_in', 'search_duration_not_in', 'search_minute', 'search_hour',
				'search_dom', 'search_month', 'search_dow', 'search_year', 'search_year_in',
				'search_year_not_in', 'search_year_lt', 'search_year_lte', 'search_year_gt', 'search_year_gte'
			);

			$this->_default_query();

			foreach ($params as $name => $value) {
				if (in_array($name, $search_filters)) {
					$this->add_filter(substr($name, 7), $value);
				}
			}

			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			if (!in_array($order, array('id', 'article_id', 'duration', 'year'))) {
				$order = 'id';
			}

			$this->_query->add_order("$order $search_direction");

			$this->_query->set_limit((int)$limit);
			$this->_query->set_offset((int)$offset);

			if ($set_list) {
				$this->set_list();
				return $this->list;
			}

			return null;
		}

		/**
		 * gets all article schedules
		 *
		 * @return	mixed	array or false
		 */
		public function &get_all_schedules() {

			$this->_default_query();
			$this->set_list();

			$result = false;

			if ( !empty($this->list) ) {
				$result =& $this->list;
			}

			return $result;
		}

		/**
		 * gets article schedule by its id
		 *
		 * @param	int		$id
		 * @return	mixed	ArticlesSchedule or false
		 */
		public function &get_schedule_by_id($id) {

			$this->_default_query();
			$this->add_filter('id', $id);
			$this->set_list();

			$result = false;

			if ( !empty($this->list) ) {
				$result = reset($this->list);
			}

			return $result;
		}

		/**
		 * gets article schedules by article id
		 *
		 * @param	int		$article_id
		 * @return	mixed	array or false
		 */
		public function &get_schedules_by_article_id($article_id) {

			$this->_default_query();
			$this->add_filter('article_id', $article_id);
			$this->set_list();

			$result = false;

			if ( !empty($this->list) ) {
				$result =& $this->list;
			}

			return $result;
		}

		/**
		 * gets article schedules by date
		 *
		 * @param	mixed	$time	int (unix timestamp) or array
		 * @return	mixed	array or false
		 */
		public function &get_schedules_by_date($time) {

			if ( !is_array($time) ) {
				$parts = explode( ',', date('i,G,j,n,w,Y', $time) );
				$time = array(
					'minute' => (int)($parts[0]),
					'hour' => $parts[1],
					'dom' => $parts[2],
					'month' => $parts[3],
					'dow' => $parts[4] + 1,
					'year' => $parts[5]
				);
			}

			$this->_default_query();

			foreach ($time as $name => $value) {
				$this->add_filter($name, $value);
			}

			$this->set_list();

			$result = false;

			if ( !empty($this->list) ) {
				$result =& $this->list;
			}

			return $result;
		}
	}


	/**
	 * functions for manipulating bigint (64-bit) bitmaps (bitmasks)
	 */


	/**
	 * checks if specific bit is set in bigint bitmask
	 *
	 * @param	array	$bigint
	 * @param	int		$bit		(0-63)
	 * @return	bool
	 */
	function bigint_is_bit_set($bigint, $bit) {
		if ($bit >= 32 && $bit <= 63) {
			return ($bigint[1] & (1 << ($bit - 32))) != 0;
		} else if ($bit >= 0 && $bit <= 31) {
			return ($bigint[0] & (1 << $bit)) != 0;
		}
		return false;
	}

	/**
	 * sets specific bit in bigint bitmask
	 *
	 * @param	&array	$bigint
	 * @param	int		$bit		(0-63)
	 * @return	&array
	 */
	function &bigint_set_bit(&$bigint, $bit) {
		if ($bit >= 32 && $bit <= 63) {
			$bigint[1] |= (1 << ($bit - 32));
		} else if ($bit >= 0 && $bit <= 31) {
			$bigint[0] |= 1 << $bit;
		}
		return $bigint;
	}

	/**
	 * clears specific bit in bigint bitmask
	 *
	 * @param	&array	$bigint
	 * @param	int		$bit		(0-63)
	 * @return	&array
	 */
	function &bigint_clear_bit(&$bigint, $bit) {
		if ($bit >= 32 && $bit <= 63) {
			$bigint[1] &= ~(1 << ($bit - 32));
		} else if ($bit >= 0 && $bit <= 31) {
			$bigint[0] &= ~(1 << $bit);
		}
		return $bigint;
	}

	/**
	 * flips specific bit in bigint bitmask
	 *
	 * @param	&array	$bigint
	 * @param	int		$bit		(0-63)
	 * @return	&array
	 */
	function &bigint_flip_bit(&$bigint, $bit) {
		if ($bit >= 32 && $bit <= 63) {
			$bigint[1] ^= (1 << ($bit - 32));
		} else if ($bit >= 0 && $bit <= 31) {
			$bigint[0] ^= 1 << $bit;
		}
		return $bigint;
	}

	/**
	 * applies one bigint bitmask over another using bitwise OR
	 *
	 * @param	&array	$bigint
	 * @param	array	$mask
	 * @return	&array
	 */
	function &bigint_or_mask(&$bigint, $mask) {
		$bigint[0] |= $mask[0];
		$bigint[1] |= $mask[1];
		return $bigint;
	}

	/**
	 * applies one bigint bitmask over another using bitwise AND
	 *
	 * @param	&array	$bigint
	 * @param	array	$mask
	 * @return	&array
	 */
	function &bigint_and_mask(&$bigint, $mask) {
		$bigint[0] &= $mask[0];
		$bigint[1] &= $mask[1];
		return $bigint;
	}

	/**
	 * applies one bigint bitmask over another using bitwise XOR
	 *
	 * @param	&array	$bigint
	 * @param	array	$mask
	 * @return	&array
	 */
	function &bigint_xor_mask(&$bigint, $mask) {
		$bigint[0] ^= $mask[0];
		$bigint[1] ^= $mask[1];
		return $bigint;
	}

	/**
	 * inverts bigint bitmask (bitwise NOT)
	 *
	 * @param	&array	$bigint
	 * @return	&array
	 */
	function &bigint_invert(&$bigint) {
		$bigint[0] = ~$bigint[0];
		$bigint[1] = ~$bigint[1];
		return $bigint;
	}

	/**
	 * converts bigint bitmask to hexadecimal string
	 *
	 * @param	array	$bigint
	 * @return	string
	 */
	function bigint_to_hex($bigint) {
		return sprintf('%08X%08X', $bigint[0], $bigint[1]);
	}

	/**
	 * converts hexadecimal string to bigint bitmask
	 *
	 * @param	string	$hex
	 * @return	array
	 */
	function &bigint_from_hex($hex) {
		$hex = substr( str_repeat('0', 16) . $hex, -16 );
		return array(
			hexdec( substr($hex, 0, 8) ),
			hexdec( substr($hex, -8) )
		);
	}
?>