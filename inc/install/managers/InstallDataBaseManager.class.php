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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallManager.class.php');

class InstallDataBaseManager extends InstallManager
{

    const DB_ARRAY_KEY = "db_installer_connection";
    const DEFAULT_PORT = 3306;
    const SCHEMA_SCRIPT_PATH = "/inc/install/ximdex_data/ximdex_schema.sql";
    const DATA_SCRIPT_PATH = "/inc/install/ximdex_data/ximdex_data.sql";

    private $dbConnection = null;
    private $host;
    private $port;
    private $user;
    private $pass;
    private $name;
    private $errors = array();
	
    /**
     * Build FastTraverse and full path to every node in Ximdex
     */
    public function connect($host, $port, $user, $pass = NULL, $name = false, $newConn = false)
    {
        $myPid = "install";
        $result = false;
        if (!isset($GLOBALS[self::DB_ARRAY_KEY][$myPid]))
            $GLOBALS[self::DB_ARRAY_KEY][$myPid] = null;

            if ($GLOBALS[self::DB_ARRAY_KEY][$myPid] and !$newConn) {
            $this->dbConnection = $GLOBALS[self::DB_ARRAY_KEY][$myPid];
            $result = true;
        } else {
            if ($port)
            	$host .= ':' . $port;
            if ($name)
            	$url = 'mysql:dbname=' . $name . ';host=' . $host;
            else
            	$url = 'mysql:host=' . $host;
        	try
        	{
        		$this->dbConnection = new PDO($url, $user, $pass);
        	}
        	catch (PDOException $e)
        	{
        		error_log('ERROR: can\'t connect to database: ' . $e->getMessage());
        		return false;
        	}
            $GLOBALS[self::DB_ARRAY_KEY][$myPid] = $this->dbConnection;
			$this->host = $host;
			$this->port = $port;
			$this->user = $user;
			$this->pass = $pass;
			$result = true;
			if ($name)
				$this->name = $name;
        }
        return $result;
    }

    public function selectDataBase($name)
    {
        $res = $this->connect($this->host, null, $this->user, $this->pass, $name, true);
        if ($res === false)
        	return false;
        return $this->dbConnection;
    }

    public function getConnectionErrors()
    {
    	if ($this->dbConnection)
    	{
    		$res = $this->dbConnection->errorInfo();
    		return ($res[2]);
    	}
    	else
    		return 'Can\'t connect to database';
    }

    public function getErrors()
    {
    	$res = $this->dbConnection->errorInfo();
    	return ($res[2]);
    }

    /**
     * Forcing to reconnect to database next time
     */
    function reconectDataBase()
    {
        if ($this->dbConnection) {
            $this->dbConnection = null;
        }
        $GLOBALS[self::DB_ARRAY_KEY]["install"] = null;
    }


    public function createUser($user, $pass)
    {
        $sql = "GRANT ALL PRIVILEGES  ON $$this->name.* TO '$user'@'%' IDENTIFIED BY '$pass'";
        $result = $this->dbConnection->exec($this->dbConnection, $sql);
        $sql = "FLUSH privileges";
        $result = $result && $this->dbConnection->exec($this->dbConnection, $sql);
        if ($result === 0)
        	$result = false;
        return $result;
    }

    public function createDataBase($name)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = "create database $name";
            $result = $this->dbConnection->exec($query);
            if ($result === 0)
            	$result = false;
            if ($result === false) {
                error_log("ERROR:");
                error_log("a $result" . print_r($result, true) . " $query " . $this->dbConnection->error);
            }
        } else {
            error_log("ERROR: CREATING DATABASE.");
        }
        return $result;
    }

    public function deleteDataBase($name)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = sprintf("drop database %s", $name);
            $result = $this->dbConnection->exec($query);
            if ($result === 0)
            	$result = false;
        } else {
            error_log("ERROR: DELETING DATABASE.");
        }
        return $result;
    }


    public function loadData($host, $port, $user, $pass, $name)
    {
    	$data = file_get_contents(XIMDEX_ROOT_PATH . self::SCHEMA_SCRIPT_PATH);
    	$data .= file_get_contents(XIMDEX_ROOT_PATH . self::DATA_SCRIPT_PATH);
    	try
    	{
    		$statement = $this->dbConnection->prepare($data);
    		$res = $statement->execute();
    	}
    	catch (PDOException $e)
    	{
    		return false;
    	}
    	return true;
    }

    public function existDataBase($name)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = sprintf("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '%s'", $name);
            $result = $this->dbConnection->query($query);
        }
        return $result && $result->rowCount();
    }

    public function checkDataBase($host, $port, $user, $pass, $name)
    {
        $result = $this->selectDataBase($name);
        if ($result) {
            $query = "show tables like 'NodeProperties'";
            $result = $this->dbConnection->query($query);
        }
        return $result && $result->rowCount();

    }

    public function userExist($userName)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = " SELECT user FROM mysql.user where user='$userName' and host='%'";
            $result = $this->dbConnection->query($query);
        }
        return $result && $result->rowCount();
    }

    public function addUser($userName, $pass, $name)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = "GRANT ALL PRIVILEGES  ON $name.* TO '$userName'@'localhost' IDENTIFIED BY '$pass'";
            $result = $this->dbConnection->exec($query);
            $query = "GRANT ALL PRIVILEGES  ON $name.* TO '$userName'@'%' IDENTIFIED BY '$pass'";
            $result = $result && $this->dbConnection->exec($query);
            $result = $result && $this->dbConnection->exec("FLUSH privileges");
            if ($result === 0)
            	$result = false;
        }
        return $result;
    }

    public function addPrivileges($userName, $name)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = "GRANT ALL PRIVILEGES  ON $name.* TO '$userName'@'%'";
            $result = $this->dbConnection->exec($query);
            $result = $result && $this->dbConnection->exec("FLUSH privileges");
            if ($result === 0)
            	$result = false;
        }
        return $result;
    }

    public function changeUser($user, $pass, $name)
    {
        $result = false;
        if ($this->dbConnection)
        {
        	$result = $this->connect($this->host, null, $user, $pass, $name, true);
        }
        return $result;
    }
}