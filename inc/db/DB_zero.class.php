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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}


include_once ( XIMDEX_ROOT_PATH."/inc/modules/Module.class.php");
include_once (XIMDEX_ROOT_PATH."/inc/xml/XmlBase.class.php");

$debug = NULL;
// Class for mySql database management
class DB
{
	/**
	 * 
	 * @var unknown_type
	 */
	var $dbhost;
	
	/**
	 *
	 * @var unknown_type
	 */
	var $dbport;
	/**
	 * 
	 * @var unknown_type
	 */
	var $dbuser;
	/**
	 * 
	 * @var unknown_type
	 */
	var $dbpasswd;
	/**
	 * 
	 * @var unknown_type
	 */
	var $dbname;
	/**
	 * RecordSet
	 * @var unknown_type
	 */
	var $rs;				
	/**
	 * Rows which contain the current register data
	 * @var unknown_type
	 */
	var $row;					
	/**
	 * Number of rows affected by the query
	 * @var unknown_type
	 */
	var $numRows;				
	/**
	 *  Number of cols returned by the query
	 * @var unknown_type
	 */
	var $numFields;				
	/**
	 * Database pointer
	 * @var unknown_type
	 */
	var $dbConnection;			
	/**
	 * SQL sentence
	 * @var unknown_type
	 */
	var $sql;					
	/**
	 * New ID automatically generated after an INSERT
	 * @var unknown_type
	 */
	var $newID;					
	/**
	 * Indicates if there are registers left
	 * @var unknown_type
	 */
	var $EOF;					
	/**
	 * Produced error number
	 * @var unknown_type
	 */
	var $numErr;				
	/**
	 *  Error description
	 * @var unknown_type
	 */
	var $desErr;				
	/**
	 * Debug management
	 * @var unknown_type
	 */
	var $debug = false;			
	/**
	 * Log files
	 * @var unknown_type
	 */
	var $fileLog;				
	/**
	 * File pointer
	 * @var unknown_type
	 */
	var $fileHdl;				 
	/**
	 * Instance of logger configured for data access
	 * @var unknown_type
	 */
	var $dblog;	 
	/**
	 * Encodings
	 * @var unknown_type
	 */
	var $dbEncoding='';
	/**
	 * 
	 * @var unknown_type
	 */
	var $workingEncoding='';

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function DB() {
		$this->ConnectDatabase();
	}
	/**
	 * Function which creates a DB connection with the previously speficied params
	 * @return unknown_type
	 */
	function ConnectDatabase() {
		global $debug;
		if (!isset($GLOBALS['db_connection'][getmypid()])) $GLOBALS['db_connection'][getmypid()] = null;

		if($GLOBALS['db_connection'][getmypid()]) {
			$this->dbConnection = $GLOBALS['db_connection'][getmypid()];
			$this->debug = $debug;
		} else {
			include(MAIN_INSTALL_PARAMS);
				
			$this->dbhost = $DBHOST;
			$this->dbport = $DBPORT;
			$this->dbuser = $DBUSER;
			$this->dbpasswd = $DBPASSWD;
			$this->dbname = $DBNAME;
			$this->debug = $USE_SQL_LOG;
			
			$this->dbConnection = mysql_connect($this->dbhost.":".$this->dbport,$this->dbuser,$this->dbpasswd, true);
			//$this->dbConnection = mysql_connect("test.ximdex.es:8889",$this->dbuser,$this->dbpasswd, true);
			$GLOBALS['db_connection'][getmypid()] = $this->dbConnection;


			if(!$this->dbConnection) {
				if($this->debug) {
					$this->numErr = mysql_errno();
					$this->desErr = mysql_error();
					$msgDebug = "\error number:".$this->numErr;
					$msgDebug .= " trying to connect ";
					$msgDebug .= $this->dbname;
					$msgDebug .=  "\ndescription: ";
		 			$msgDebug .= $this->desErr;
					$msgDebug .= "\ndate: " . date("d/m/Y - H:i:s");
			   		DB_Log::error($msgDebug);
				}

				if (!defined('DB_CONNECTION'))
					define("DB_CONNECTION", "0");
				//Go to installer
				$_GET["action"] = "installer";

				// Dieing if connection could not be stablished
				//$conn_str = 'mysql://' . $this->dbuser . '@' . $this->dbhost . '/' . $this->dbname;
				//die("<p>"._("MySQL link could not been stablished.")."</p><br/><p>$conn_str</p>");
			} else {
				if(!defined("DB_CONNECTION") )
					define("DB_CONNECTION", "1");
				$res = mysql_select_db($this->dbname,$this->dbConnection);
			}
		}
	}

	/**
	 * Forcing to reconnect to database next time
	 * @return unknown_type
	 */	
	function reconectDataBase(){
		$GLOBALS['db_connection'][getmypid()] = null;
	}
	/**
	 * Finishing BD connection
	 * @return unknown_type
	 */
	function CloseDatabase() {
		if($this->dbConnection) {
			mysql_close($this->dbConnection);
		}
	}

	/**
	 * 
	 * @return unknown_type
	 */	
	function & _getInstance() {

	}
	/**
	 * Function which performs a BD query (preferred only SELETs)
	 * @param $sql
	 * @return unknown_type
	 */
	function Query($sql) {
		if($this->dbConnection) {
			//Encode to dbConfig value in table config
			$this->_getEncodings();
			$sql = XmlBase::recodeSrc($sql,$this->dbEncoding);

			$this->sql = $sql;
			$this->rs = mysql_query($this->sql, $this->dbConnection);

			if (!$this->rs) {
				$this->numErr = mysql_errno();
				$this->desErr = mysql_error();
				$msg = "Error executing query '".$sql."': ";
				$msg .= mysql_errno().": ".mysql_error()."<br>\n";
				$this->numRows = 0;
				$this->EOF = true;
				if($this->debug) {
					DB_Log::error("Query:".$msg);
				}
				return;
			}

			if($this->rs && !is_bool($this->rs) &&  mysql_num_rows($this->rs))
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
				$msgDebug .= "(". $this->numRows . ") row/s";
				DB_Log::error($msgDebug);
			}
		}
	}

	/**
	 * Function which performs a BD query
	 * @param $sql
	 * @return unknown_type
	 */
	function Execute($sql) {
		if($this->dbConnection) {
			//Encode to dbConfig value in table config
			$this->_getEncodings();
			$sql = XmlBase::recodeSrc($sql,$this->dbEncoding);

			$this->sql = $sql;
			$this->rs = mysql_query($this->sql,$this->dbConnection);
			$this->numRows = (mysql_affected_rows($this->dbConnection));
			if (!$this->numRows) {
				$matches = array();
				preg_match('/matched\:\s*(\d+)/', mysql_info($this->dbConnection), $matches);
				if (count($matches) == 2) {
					$this->numRows = $matches[1];
				}
			}
			

			if ($this->rs) {
				$this->EOF = false;
			} else {
				$this->EOF = true;
			}

			$this->numErr = mysql_errno();
			$this->desErr = mysql_error();
			if($this->debug) {
				$msgDebug  = "Execute: [" . $sql . "] => ";
				$msgDebug .= "(". $this->numRows . ") affected row/s";
				$msgDebug .= "  (error ". $this->numErr.")";
				DB_Log::error($msgDebug);
			}
			if (!$this->numErr) {
				$this->SetInsertID();
				if ($this->newID) {
					return $this->newID;
				}
				return true;
			}
		}

		return false;
	}

	/**
	 * Function which updates the newID when doing a new insertion using a mysql_insert_id
	 * @return unknown_type
	 */
	function SetInsertID() {
		$rs = mysql_query("SELECT LAST_INSERT_ID() as LAST_INSERT_ID", $this->dbConnection);
		if ($rs) {
			$result = mysql_fetch_array($rs);
			if (!$result) {
				return false;
			}
			$this->newID = $result['LAST_INSERT_ID'];
			return true;
		}
		return false;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function getInsertID() {

		return $this->newID;
	}
	/**
	 * Functions which obtains the current row value for a determined field
	 * @param $col
	 * @return unknown_type
	 */
	function GetValue($col) {
		if (is_array($this->row) && array_key_exists($col, $this->row)) {
			include_once (XIMDEX_ROOT_PATH."/inc/xml/XmlBase.class.php");
			include_once (XIMDEX_ROOT_PATH."/inc/xml/XML.class.php");

			$this->_getEncodings();
			$value = XmlBase::recodeSrc($this->row[$col],$this->workingEncoding);
			return $value;
		}
		$backtrace = debug_backtrace();
		error_log(sprintf('Trying to obtain a value from a query that has not been selected [inc/db/DB_zero.class.php] script: %s file: %s line: %s value: %s',
					$_SERVER['SCRIPT_FILENAME'],
					$backtrace[1]['file'],
					$backtrace[1]['line'],
					$col));

		return NULL;
	}

	/**
	 * Going to the next row of the recordset
	 * @return unknown_type
	 */
	function Next() {
		if($this->rs && $this->dbConnection) {
			$this->row = mysql_fetch_array($this->rs);
			$this->EOF = !((bool) $this->row);
			return  $this->EOF;
		}
		$this->EOF = true;
		return false;
	}

	/**
	 * Going to the first element of the recordset
	 * @return unknown_type
	 */
	function First()
		{
		$this->Go(0);
		}

	/**
	 * Going to the element $pos of the recordset
	 * @param $pos
	 * @return unknown_type
	 */
	function Go($pos)
		{
		if(($this->rs) && ($pos <= $this->numRows) && ($pos >= 0))
			{
			mysql_data_seek($this->rs,$pos);
			$this->Next();
			}
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
			XMD_Log::info("WARNING: A SQL statement is converting an empty string to NULL");
			return 'NULL';
		}


		return "'" . mysql_real_escape_string($value) . "'";
	}

	/**
	 * Function which returns the MySQL version which is being used as a string,
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
	private function _getEncodings(){

		$sql = "select ConfigKey,ConfigValue from Config where ConfigKey='workingEncoding' or ConfigKey='dbEncoding'";

		if (($this->dbEncoding=='') && ($this->workingEncoding==''))
		{
			if($this->dbConnection) {

				$this->sql = $sql;
				$this->rs = mysql_query($this->sql, $this->dbConnection);

				if (!$this->rs) {
					$this->numErr = mysql_errno();
					$this->desErr = mysql_error();
					$msg = "Error executing query '".$sql."': ";
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
					$msgDebug .= "(". $this->numRows . ") row/s";
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
