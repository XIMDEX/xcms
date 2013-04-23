<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



if (!defined('DB_LOADED')) {
	define("DB_LOADED", 1);
}else {
	return null;
}


if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/..'));
}

include_once (XIMDEX_ROOT_PATH . "/extensions/adodb/adodb.inc.php");

/*require_once(XIMDEX_ROOT_PATH . "/inc/log/Log.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/log/logger/Logger_error.class.php");*/
$debug = NULL;
// Class for mySql database management
class DB {

	/**
	 * RecordSet
	 * @var unknown_type
	 */
	var $rs;					
	/**
	 *  Row which conatin the current registration data
	 * @var unknown_type
	 */
	var $row;					
	/**
	 * Number of files affected by the query
	 * @var unknown_type
	 */
	var $numRows;				
	/**
	 * New ID generated automatically after an INSERT
	 * @var unknown_type
	 */
	var $newID;					 
	/**
	 * Indicates if there are registers left
	 * @var unknown_type
	 */
	var $EOF;					
	/**
	 * Debug management
	 * @var unknown_type
	 */
	var $debug = false;		
	/**
	 *  Number of error accured
	 * @var unknown_type
	 */
	var $numErr = 0;	
	/**
	 * Error description
	 * @var unknown_type
	 */
	var $desErr = '';			
	/**
	 * Flag to define if sql log is used in file
	 * @var unknown_type
	 */
	var $useLog = false;

	
	/**
	 * Constructor
	 * @return unknown_type
	 */
	function DB() {
		global $debug;
		/* @var $dbConnection ADOConnection*/
		$dbConnection = & DB::_getInstance();
		if (!$dbConnection->IsConnected()) {
			include (MAIN_INSTALL_PARAMS);
			$this->useLog = $USE_SQL_LOG;
			$debug = $this->useLog;
			$dbConnection->Connect($DBHOST.":".$DBPORT, $DBUSER, $DBPASSWD, $DBNAME, true);
			if ($dbConnection->ErrorNo() > 0) {
				DB_Log::error( sprintf('Connect [host: %s] [port: %s] [user: %s] [pwd: %s] [dbname: %s]', $DBHOST, $DBPORT, $DBUSER, $DBPASSWD, $DBNAME) );
				$this->numErr = $dbConnection->ErrorNo();
				$this->desErr = $dbConnection->ErrorMsg();
			}
		} else {
			$this->useLog = $debug;
		}
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function & _getInstance() {

		static $dbConnection = NULL;

		if ($dbConnection === NULL) {
			$dbConnection = NewADOConnection('mysql');
		}

		return $dbConnection;
	}

	/**
	 * Function which performs a DB query (SELECTs preferred)
	 * @param $query
	 * @param $cacheTimeLimit
	 * @return unknown_type
	 */
	function query($query, $cacheTimeLimit = 0) {

		/* @var $dbConnection ADOConnection*/
		$dbConnection = & DB::_getInstance();
		/* @var $rs ADORecordset */
		if ($cacheTimeLimit > 0) {
			$this->rs = $dbConnection->CacheExecute($cacheTimeLimit, $query, false);
		} else {
			$this->rs = $dbConnection->Query($query, false);
		}

		if (!($dbConnection->ErrorNo() > 0)) {
			$this->EOF = $this->rs->EOF;
			$this->row = $this->rs->FetchRow();
			$this->numRows = $this->rs->NumRows();
			// The number of fields is not being used, so it's not used here neither to do not complicate the class
		} else {
			$this->EOF = true;
			$this->row = NULL;
			$this->numRows = 0;
			$this->numErr = $dbConnection->ErrorNo();
			$this->desErr = $dbConnection->ErrorMsg();
		}
		if ($this->useLog) DB_Log::debug( $query );
		return $this->numErr > 0 ? false : true;
	}

	/**
	 * Function which performs a BD query
	 * @param $query
	 * @return unknown_type
	 */
	function execute($query) {
		/* @var $dbConnection ADOConnection*/
		$dbConnection = & DB::_getInstance();
		/* @var $rs ADORecordset */
		$this->rs = $dbConnection->Execute($query, false);

		if (!($dbConnection->ErrorNo() > 0)) {
			$this->numRows = $dbConnection->Affected_Rows();
			// The number of fields is not being used, so it's not used here neither to do not complicate the class
		} else {
			$this->numRows = 0;
			$this->numErr = $dbConnection->ErrorNo();
			$this->desErr = $dbConnection->ErrorMsg();
		}
		$insertId = $dbConnection->Insert_ID();
		if ($insertId > 0) $this->newID = $insertId;
		if ($this->useLog) DB_Log::debug($query);
		return $this->numErr > 0 ? false : true;
	}

	/**
	 * Function which obtains the current row for a determined field
	 * @param $column
	 * @return unknown_type
	 */
	function getValue($column) {
		if (is_array($this->row) && array_key_exists($column, $this->row)) {
			return $this->row[$column];
		}
		$backtrace = debug_backtrace();
		error_log(sprintf(_('Trying to obtain a value from a query that has not been selected [inc/db/DB_zero.class.php] script: %s file: %s line: %s value: %s'),
					$_SERVER['SCRIPT_FILENAME'],
					$backtrace[0]['file'],
					$backtrace[0]['line'],
					$column));

		return NULL;
	}

	/**
	 * Going to the next row of the recordset
	 * @return unknown_type
	 */
	function next() {
		/* @var $rs ADORecordset */
		$this->rs->NextRecordSet();
		if ($this->rs->EOF) {
			$this->EOF = true;
			return false;
		}
		$this->row = $this->rs->FetchRow();
		return true;
	}

	/**
	 * Going to the first element of the recordset
	 * TODO Check if this function is being used
	 * @return unknown_type
	 */
	function first() {
		/* @var $rs ADORecordset */
		return $this->rs->MoveFirst();
	}

	/**
	 * Going to element $pos of the recordset
	 * TODO Check if this function is being used
	 * @param $rowNum
	 * @return unknown_type
	 */
	function Go($rowNum) {
		/* @var $rs ADORecordset */
		return $this->rs->Move($rowNum);
	}

	/**
	 * 
	 * @param $value
	 * @return unknown_type
	 */
	public static function sqlEscapeString($value) {
		if (is_null($value)) {
			return 'NULL';
		}
		if (!(strlen($value) > 0)) {
//			error_log("WARNING: THERE IS A QUERY WHICH IS CONVERTING '' IN NULL");
			return 'NULL';
		}
		$dbConnection = & DB::_getInstance();
		return $dbConnection->Quote($value);
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function setInsertID() {
		$dbConnection = & DB::_getInstance();
		if ($dbConnection->IsConnected()) {
			$this->newID = $dbConnection->Insert_ID();
		} else {
			$this->newID = false;
		}
	}

	/**
	 * Function which returns the MySQL version used as a string,
	 * NULL if it cannot be obtained
	 *
	 * @return string
	 */
	function getServerVersion() {

		$sql = "select version() as ver";
		$this->Query($sql);
		if ($this->EOF) return null;

		$version = $this->getValue('ver');

		$regex = '/^(\d+\.\d+\.\d+)(?:\D*)-(?:.*)/';
		$arr = null;
		$ret = preg_match($regex, $version, $arr);

		return $version = $arr[1];
	}

	/**
	 *
	 * Read dbEncoding and dbEncoding from database, Config
	 * Is not possible to do in other place, because if you put in getValue, for example, or in the constructor,
	 * is will create an infinite circle
	 *
	 */
	private function _getEncodings()
	{

		$sql = "select ConfigKey,ConfigValue from Config where ConfigKey='workingEncoding' or ConfigKey='dbEncoding'";

		if (($this->dbEncoding=='') && ($this->workingEncoding==''))
		{
			if($this->dbConnection) {

				$this->sql = $sql;
				$this->rs = mysql_query($this->sql, $this->dbConnection);

				if (!$this->rs) {
					$this->numErr = mysql_errno();
					$this->desErr = mysql_error();
					$msg = "Error when executing the query '".$sql."': ";
					$msg .= mysql_errno().": ".mysql_error()."<br>\n";
					$this->numRows = 0;
					$this->EOF = true;
					if($this->debug) {
						DB_Log::error("Query:".$msg);
					}
					return;
				}

				if($this->rs && mysql_num_rows($this->rs))
				{

					$this->EOF = false;
					$this->row = mysql_fetch_array($this->rs);
					$this->numRows = mysql_num_rows($this->rs);
					$this->numFields = mysql_num_fields($this->rs);
				}
				else
				{
					$this->numRows = 0;
					$this->EOF = true;
				}


				if($this->debug) {
					$msgDebug  = "Query: [" . $sql . "] => ";
					$msgDebug .= "(". $this->numRows . ") fila/s";
					DB_Log::error($msgDebug);
				}

				$i = 0;
				while (!$this->EOF) {
					$i++;
					$configKey = $this->row['ConfigKey'];
					$configValue = $this->row['ConfigValue'];

					if ($configKey == 'dbEncoding')
					{
						$this->dbEncoding = $configValue;
					}
					else if ($configKey == 'workingEncoding')
					{
						$this->workingEncoding = $configValue;
					}
					$this->next();
				}
			}
		}
	}
}
?>
