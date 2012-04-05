<?php

require_once (dirname(__FILE__) . '/../../conf.php');

define('MSB_NL', "\r\n");

class BackupMySQL
{

	var $server = VIVVO_DB_HOST;
	var $port = 3306;
	var $username = VIVVO_DB_USER;
	var $password = VIVVO_DB_PASSWORD;
	var $database = VIVVO_DB_DATABASE;
	var $link_id = -1;
	var $connected = false;
	var $tables = array();
	var $drop_tables = true;
	var $struct_only = false;
	var $comments = true;
	var $backup_dir = 'backup/';
	var $fname_format = 'd_m_y__H_i_s';
	var $error = '';

	function Execute($file_name,$database,$table_name,$start,$end,$max_rows)
	{
		$this->_Connect();
		if (!($sql = $this->_Retrieve($database,$table_name,$start,$end,$max_rows)))return false;
		return $this->_SaveToFile($file_name, $sql);
	}

	function _Connect()
	{
		$value = false;
		if (!$this->connected)
		{
			$host = $this->server . ':' . $this->port;
			$this->link_id = mysql_connect($host, $this->username, $this->password);
		}
		if ($this->link_id)
		{
			if (empty($this->database))
			{
				$value = true;
			}
			elseif ($this->link_id !== -1)
			{
				$value = mysql_select_db($this->database, $this->link_id);
			}
			else
			{
				$value = mysql_select_db($this->database);
			}
			@mysql_query('SET NAMES \'utf8\'');
		}
		if (!$value)
		{
			$this->error = mysql_error();
		}
		return $value;
	}

	function _Query($sql)
	{
		if ($this->link_id !== -1)
		{
			$result = mysql_query($sql, $this->link_id);
		}
		else
		{
			$result = mysql_query($sql);
		}
		if (!$result)
		{
			$this->error = mysql_error();
		}
		return $result;
	}

	function _GetTables($database)
	{
		$this->_Connect();
		$value = array();

		$like = strlen(VIVVO_DB_PREFIX) ? " LIKE '" . VIVVO_DB_PREFIX . "%'" : '';

		if (!($result = $this->_Query("SHOW TABLES$like")))
		{
			return false;
		}
		while ($row = mysql_fetch_row($result))
		{
			if (!($result1 = $this->_Query('SELECT count(*) FROM `'.$row[0].'`')))
			{
				return false;
			}
			$row1 = mysql_fetch_row($result1);

			$value[] = $row[0].':'.$row1[0];
		}

		if (!count($value))
		{
			$this->error = 'No tables found in database.';
			return false;
		}

		sort($value);
		return $value;
	}

	function GetTables($database)
	{
		return $this->_GetTables($database);
	}

	function _DumpTable($database,$table,$start,$end,$row_count)
	{
		$value = '';

		if($start==0)
		{
			if ($this->comments)
			{
				$value .= '#' . MSB_NL;
				$value .= '# Table structure for table `'.$table. '`'. MSB_NL;
				$value .= '#' . MSB_NL . MSB_NL;
			}
			if ($this->drop_tables)
			{
				$value .= 'DROP TABLE IF EXISTS `'.$table. '`;##VIVVO_END_OF_LINE'. MSB_NL;
			}
			if (!($result = $this->_Query('SHOW CREATE TABLE `'.$table.'`')))
			{
				return false;
			}
			$row = mysql_fetch_assoc($result);
			$value .= str_replace("\n", MSB_NL, $row['Create Table']) . ';##VIVVO_END_OF_LINE';
			$value .= MSB_NL . MSB_NL;
		}

		if (!$this->struct_only)
		{
			if ($start==0 && $this->comments)
			{
				$value .= '#' . MSB_NL;
				$value .= '# Dumping data for table `' . $table . '`' . MSB_NL;
				$value .= '#' . MSB_NL . MSB_NL;
			}
			$value .= $this->_GetInserts($database,$table,$start,$end);
		}

		if($end>=$row_count) {
			$value .= MSB_NL . MSB_NL;
		}

		return $value;
	}

	function _GetInserts($database,$table,$start,$end)
	{
		$value = '';
		
		if (!($result = $this->_Query('SELECT * FROM `'.$table.'` LIMIT '.$start.','.$end)))
		{
			return false;
		}
		while ($row = mysql_fetch_assoc($result))
		{
			$values = '';
			foreach ($row as $key => $data)
			{
				if ($table == VIVVO_DB_PREFIX . 'tags_groups' and $key == 'id' and $data == 0) {
					$data = 0x7fffffff;
				}
				$values .= is_null($data) ? 'NULL, ' : '\'' . addslashes($data) . '\', ';
			}
			$values = substr($values, 0, -2);
			$value .= 'INSERT INTO `'.$table . '` VALUES (' . $values . ');##VIVVO_END_OF_LINE' . MSB_NL;
		}
		return $value;
	}

	function _Retrieve($database,$table_name,$start,$end,$row_count)
	{
		$value = '';
		if (!$this->_Connect())
		{
			return false;
		}
		if ($start==0 && $this->comments)
		{
			$value .= '#' . MSB_NL;
			$value .= '# MySQL database dump' . MSB_NL;
			$value .= '#' . MSB_NL;
			$value .= '# Host: ' . $this->server . MSB_NL;
			$value .= '# Generated: ' . date('M j, Y') . ' at ' . date('H:i') . MSB_NL;
			$value .= '# MySQL version: ' . mysql_get_server_info() . MSB_NL;
			$value .= '# PHP version: ' . phpversion() . MSB_NL;
			if (!empty($this->database))
			{
				$value .= '#' . MSB_NL;
				$value .= '# Database: `' . $this->database . '`' . MSB_NL;
				$value .= '# Table prefix: "' . VIVVO_DB_PREFIX . '"' . MSB_NL;
			}
			$value .= '#' . MSB_NL . MSB_NL . MSB_NL;
		}

		if ($start == 0 and $table_name == VIVVO_DB_PREFIX . 'cache') {
            $value .= 'SET foreign_key_checks = 0;##VIVVO_END_OF_LINE' . MSB_NL;
			$value .= 'DROP TABLE IF EXISTS `'.VIVVO_DB_PREFIX. 'cache_tags`;##VIVVO_END_OF_LINE'. MSB_NL;
			$value .= 'DROP TABLE IF EXISTS `'.VIVVO_DB_PREFIX. 'cache`;##VIVVO_END_OF_LINE'. MSB_NL;
			$value .= 'SET foreign_key_checks = 1;##VIVVO_END_OF_LINE' . MSB_NL;
		}

		if (!($table_dump = $this->_DumpTable($database,$table_name,$start,$end,$row_count)))
		{
			$this->error = mysql_error();
			return false;
		}
		$value .= $table_dump;

		if ($table_name == VIVVO_DB_PREFIX . 'tags_groups') {
			$value .= 'UPDATE `'.$table_name. '` SET id = 0 WHERE id = ' . 0x7fffffff . ';##VIVVO_END_OF_LINE'. MSB_NL;
		}

		return $value;
	}

	function _SaveToFile($fname, $sql)
	{
		if (!($f = fopen($fname, 'a')))
		{
			$this->error = 'Can\'t create the output file.';
			return false;
		}
		fwrite($f, $sql);
		fclose($f);
		return true;
	}

}

#EOF
