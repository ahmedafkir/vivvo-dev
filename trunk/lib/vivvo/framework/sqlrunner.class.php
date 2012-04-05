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

defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time());

	/**
	 * SQL Runner class for executing SQL file scripts.
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 * @author      Mihail Binev <mbinev@spoonlabs.com>
	 */
	class SQLRunner {

		/**
		 * SQL script filename
		 * @var	string
		 */
		protected $filename;

		/**
		 * MySQL link
		 * @var	resource
		 */
		protected $db;

		/**
		 * When set to true SQLRunnerException is thrown on first failed query
		 * @var	bool
		 */
		protected $stop_on_error = false;

		/**
		 * Array of parsed queries
		 * @var	array
		 */
		public $queries = array();

		/**
		 * Array of variables which can be used in sql scripts
		 * @var	array
		 */
		public $variables = array();

		/**
		 * Creates a new instance of SQLRunner class.
		 *
		 * @param	array     $params
		 * @return	SQLRunner
		 */
		public function __construct(array $params = array()) {

			empty($params['filename']) or $this->filename = $params['filename'];
			empty($params['db']) or $this->set_db($params['db']);

			isset($params['stop_on_error']) and $this->stop_on_error = $params['stop_on_error'];

			$this->variables['MYSQL_VIVVO_START_TIME'] = date('Y-m-d H:i:s', VIVVO_START_TIME);
		}

		/**
		 * Sets database object.
		 *
		 * @param	mixed	$db Connection to MySQL server
		 * @return	SQLRunner
		 */
		public function set_db($db) {

			if (is_resource($db) or $db instanceof MDB2_Driver_Common and is_resource($db = $db->getConnection())) {
				$this->db = $db;
			}

			return $this;
		}

		/**
		 * Executes a single query by index
		 *
		 * @param	int       $index Index in query list to be executed
		 * @param	resource  $db Connection to MySQL server
		 * @return	bool
		 */
		public function runQuery($index, $db = null) {

			$db !== null and $this->set_db($db);

			if (empty($this->queries[$index])) {
				return false;	// no such query
			}

			$result = @mysql_query($this->queries[$index]['sql'], $this->db);

			if (!$result and $this->stop_on_error) {
				throw new SQLRunnerException(mysql_error($this->db));
			}

			return $result;
		}

		/**
		 * Executes all queries using transaction. Before calling this method,
		 * an sql script should be loaded with load() method.
		 *
		 * @param	resource  $db Connection to MySQL server
		 * @return	array
		 */
		public function run($db = null, $transaction = true) {

			$db !== null and $this->set_db($db);
			$result = array();

			if ($transaction and !mysql_query('BEGIN', $this->db)) {
				throw new SQLRunnerException('Failed to START transaction.');
			}

			$count  = count($this->queries);
			for ($i = 0; $i < $count; $i++) {
				$result[$i] = array(
					'query' => $this->queries[$i],
					'result' => $this->runQuery($i),
				);
			}

			if ($transaction and !mysql_query('COMMIT', $this->db)) {
				mysql_query('ROLLBACK', $this->db);
				throw new SQLRunnerException('Failed to COMMIT transaction.');
			}

			return $result;
		}

		/**
		 * Loads SQL file and returns number of queries parsed.
		 *
		 * @param	string  $filename  An SQL file script
		 * @return	int
		 */
		public function load($filename = null) {

			!empty($filename) or $filename = $this->filename;

			if (!is_readable($this->filename = $filename)) {
				throw new SQLRunnerException("File not readable: '$filename'.");
			}

			$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			if (!is_array($lines)) {
				throw new SQLRunnerException("Could not read file: '$filename'.");
			}

			$this->queries = array();
			$in_query = false;
			foreach ($lines as $line) {
				$line = trim($line);
				if (preg_match('|^/\*@QUERY\s*(.*)\*/$|i', $line, $matches)) {
					$in_query and $this->queries[] = $query;

					if (($start = strpos($matches[1], '{')) !== false and ($end = strrpos($matches[1], '}')) !== false and $start < $end) {
						$meta = json_decode(substr($matches[1], $start, $end - $start + 1), true);
						is_array($meta) or $meta = array();
					} else {
						$meta = array();
					}

					$query = array(
						'meta' => $meta,
						'sql' => ''
					);
					$in_query = true;
				} elseif ($in_query) {
					$query['sql'] .= ' ' . preg_replace_callback('|/\*{(.+?)}\*/|', array($this, 'constant_replace'), $line);
				}
			}
			$in_query and $this->queries[] = $query;
			return count($this->queries);
		}

		/**
		 * Callback function for preg_replace_callback (for applying constants in queries)
		 *
		 * @param	array   $matches
		 * @return	string
		 */
		private function constant_replace($matches) {

			$value = '';
			$escape = false;

			if ($matches[1] and $matches[1][0] == '@') {
				$matches[1] = substr($matches[1], 1);
				$escape = true;
			}

			if (array_key_exists($matches[1], $this->variables)) {
				$value = $this->variables[$matches[1]];
			} elseif (defined($matches[1])) {
				$value = constant($matches[1]);
			}

			return $escape ? mysql_real_escape_string($value, $this->db) : $value;
		}
	}

	/**
	 * SQL Runner exception class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class SQLRunnerException extends Exception {
	}

#EOF