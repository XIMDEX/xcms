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


include_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');
include_once(XIMDEX_ROOT_PATH . '/inc/model/node.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallManager.class.php');


class InstallDataBaseManager extends InstallManager{

	const DB_ARRAY_KEY="db_installer_connection";
	const DEFAULT_PORT = 3306;
	const SCRIPT_PATH = "/install/ximdex_data/ximdex.sql";

	private $dbConnection = null;
	private $host;
	private $port;
	private $user;
	private $pass;
	private $name; 
	private $errors = array();

	public function __construct(){

	}

	/**
	 * Build FastTraverse and full path to every node in Ximdex	 
	 */
	public function connect($host, $port, $user, $pass=NULL, $name=false){
		$myPid = getmypid();
		$result = false;
		if (!isset($GLOBALS[self::DB_ARRAY_KEY][$myPid])) 
			$GLOBALS[self::DB_ARRAY_KEY][$myPid] = null;

		if($GLOBALS[self::DB_ARRAY_KEY][$myPid]) {
			$this->dbConnection = $GLOBALS[self::DB_ARRAY_KEY][$myPid];
			$result = true;
		} else {
			$this->dbConnection = new mysqli($host,$user,$pass,$name,$port);
			$GLOBALS[self::DB_ARRAY_KEY][$myPid] = $this->dbConnection;
			if (!$this->dbConnection->connect_error){
				$this->host = $host;
				$this->port = $port;
				$this->user = $user;
				$this->pass = $pass;				
				$result = true;
				if ($name)
					$result = $this->selectDataBase($name);

			}
		}

		return $result;
	}

	public function selectDataBase($name){

		$res = $this->dbConnection->select_db($name);
		if ($res)
			$this->name = $name;
		return $res;
	}

	public function getConnectionErrors(){
		return $this->dbConnection->connect_error;
	}

	public function getErrors(){
		return $this->dbConnection->error;
	}

	/**
	 * Forcing to reconnect to database next time
	 */	
	function reconectDataBase(){
		if ($this->dbConnection){
		    $this->dbConnection->close();
        }
	    $GLOBALS['db_connection'][getmypid()] = null;
	}


	public function createUser($user, $pass){
		$sql = "GRANT ALL PRIVILEGES  ON $$this->name.* TO '$user'@'%' IDENTIFIED BY '$pass'";
		$result = $this->dbConnection->query($this->dbConnection, $sql);
		$sql = "FLUSH privileges";
		$result = $result && $this->dbConnection->query($this->dbConnection, $sql);		
		return $result;
	}

	public function createDataBase($name){
		$result = false;
		if($this->dbConnection){
			$query = "create database $name";			
			$result = $this->dbConnection->query($query);
			if ($result === TRUE)
				error_log("Suc");
			else{
				error_log("ERROR:");
				error_log("a $result".print_r($result, true)." $query ".$this->dbConnection->error);
			}
		}else{
			error_log("CREATE DATABASE");
		}
		return $result;
	}

	public function deleteDataBase($name){
		$result = false;
		if($this->dbConnection){
			$query = sprintf("drop database %s", $name);
			$result = $this->dbConnection->query($query);
		}else{
			error_log("DELETE DATABASE");
		}
		return $result;
	}


	public function loadData($host, $port, $user, $pass,  $name){
		$mysqlCommand = shell_exec("which mysql");
		$mysqlCommand = $mysqlCommand? trim($mysqlCommand) : "mysql";
		$command = $mysqlCommand
        . ' --host=' . $host
        . ' --user=' . $user
        . ' --port=' . $port
        . ' --password=' . $pass
        . ' --database=' . $name
        . ' --execute="SOURCE ' . XIMDEX_ROOT_PATH.self::SCRIPT_PATH.'"';
        
        $result = shell_exec($command);        
        return $result;
	}

	public function existDataBase($name){
		$result = false;
		if($this->dbConnection){
			$query = sprintf("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '%s'", $name);
			$result = $this->dbConnection->query($query);
		}
		return $result && $result->num_rows;
	}

	public function checkDataBase($host, $port, $user, $pass, $name){
		$result = $this->selectDataBase($name);
		if ($result){
			$query = "show tables like 'NodeProperties'";
			$result = $this->dbConnection->query($query);	
		}		
		return $result && $result->num_rows;

	}

	public function userExist($userName){
		$result = false;
		if ($this->dbConnection){
			$query = " SELECT user FROM mysql.user where user='$userName' and host='%'";
			$result = $this->dbConnection->query($query);				}
		return $result && $result->num_rows;
	}

	public function addUser($userName, $pass, $name){
		$result = false;
		if ($this->dbConnection){
			$query = "GRANT ALL PRIVILEGES  ON $name.* TO '$userName'@'localhost' IDENTIFIED BY '$pass'" ;
			$result = $this->dbConnection->query($query);
			$query = "GRANT ALL PRIVILEGES  ON $name.* TO '$userName'@'%' IDENTIFIED BY '$pass'" ;			
			$result = $result && $this->dbConnection->query($query);
			$result = $result && $this->dbConnection->query("FLUSH privileges");
		}
		return $result;
	}

	public function addPrivileges($userName,$name){
		$result = false;
		if ($this->dbConnection){
			$query = "GRANT ALL PRIVILEGES  ON $name.* TO '$userName'@'%'" ;
			error_log($query);
			$result = $this->dbConnection->query($query);
			$result = $result && $this->dbConnection->query("FLUSH privileges");
		}
		return $result;
	}

	public function changeUser($user, $pass, $name){
		$result = false;
		if ($this->dbConnection){
			$result = $this->dbConnection->change_user($user,$pass, $name);
		}

		return $result;
	}
	
}

?>
