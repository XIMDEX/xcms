<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;

require_once APP_ROOT_PATH . '/install/managers/InstallManager.class.php';

class InstallDataBaseManager extends InstallManager
{
    const DB_ARRAY_KEY = 'db_installer_connection';
    
    const DEFAULT_PORT = 3306;
    
    const DATA_PATH = '/install/ximdex_data/';
    
    const SCHEMA_SCRIPT_FILE = 'ximdex_schema.sql';
    
    const DATA_SCRIPT_FILE = 'ximdex_data.sql';
    
    const CHANGES_PATH = 'changes/';
    
    private $dbConnection;
    
    private $host;
    
    private $port;
    
    private $user;
    
    private $pass;
    
    private $name;
    
    private $error = '';
    
    public function getError() : string
    {
        return $this->error;
    }
	
    /**
     * Return true if the connection to the database was correct, or false if not
     * 
     * @param string $host
     * @param integer $port
     * @param string $user
     * @param string $pass
     * @param string $name
     * @param boolean $newConn
     * @return boolean
     */
    public function connect(string $host, int $port, string $user, string $pass = null, string $name = null, bool $newConn = false)
    {
        $myPid = 'install';
        $result = false;
        if (! isset($GLOBALS[self::DB_ARRAY_KEY][$myPid])) {
            $GLOBALS[self::DB_ARRAY_KEY][$myPid] = null;
        }
        if ($GLOBALS[self::DB_ARRAY_KEY][$myPid] and ! $newConn) {
            $this->dbConnection = $GLOBALS[self::DB_ARRAY_KEY][$myPid];
            $result = true;
        } else {
            if ($port) {
            	$host .= ';port=' . $port;
            }
            if ($name) {
            	$url = 'mysql:dbname=' . $name . ';host=' . $host;
            }
            else {
            	$url = 'mysql:host=' . $host;
            }
            $url .= ';charset=utf8';
        	try {
        	    
        	    // We need to avoid warning messages due to a problem with JSON reported
        	    $oldErrorReporting = error_reporting();
        	    error_reporting($oldErrorReporting ^ E_WARNING);
        		$this->dbConnection = new PDO($url, $user, $pass);
        		error_reporting($oldErrorReporting);
        	}
        	catch (PDOException $e) {
        		Logger::error('Can\'t connect to database: ' . $e->getMessage());
        		return false;
        	}
            $GLOBALS[self::DB_ARRAY_KEY][$myPid] = $this->dbConnection;
			$this->host = $host;
			$this->port = $port;
			$this->user = $user;
			$this->pass = $pass;
			$result = true;
			if ($name) {
				$this->name = $name;
			}
        }
        return $result;
    }

    public function selectDataBase(string $name)
    {
        return $this->connect($this->host, $this->port, $this->user, $this->pass, $name, true);
    }

    public function getConnectionErrors()
    {
    	if ($this->dbConnection) {
    		$res = $this->dbConnection->errorInfo();
    		return ($res[2]);
    	} else {
    		return 'Can\'t connect to database';
    	}
    }

    public function getErrors()
    {
        if ($this->dbConnection) {
            $res = $this->dbConnection->errorInfo();
            return ($res[2]);
        } else {
            return 'Not connected to database server. Check the connection parameters, please';
        }
    }

    /**
     * Forcing to reconnect to database next time
     */
    function reconectDataBase()
    {
        if ($this->dbConnection) {
            $this->dbConnection = null;
        }
        $GLOBALS[self::DB_ARRAY_KEY]['install'] = null;
    }
    
    public function createUser(string $user, string $pass)
    {
        $sql = "GRANT ALL PRIVILEGES ON {$$this->name}.* TO '{$user}'@'%' IDENTIFIED BY '{$pass}'";
        $result = $this->dbConnection->exec($this->dbConnection, $sql);
        $sql = 'FLUSH privileges';
        $result = $result && $this->dbConnection->exec($this->dbConnection, $sql);
        if ($result === 0) {
            $result = false;
        }
        return $result;
    }

    public function createDataBase(string $name)
    {
        if (isset($_SERVER['DOCKER_CONF_HOME'])) {
            return true;
        }
        $result = false;
        if ($this->dbConnection) {
            $query = "CREATE DATABASE {$name} DEFAULT CHARACTER SET utf8";
            $result = $this->dbConnection->exec($query);
            if ($result === false) {
                Logger::error('Cannot create database: ' . $name);
            }
        } else {
            Logger::error('There is not an active connection');
        }
        return $result;
    }

    public function deleteDataBase(string $name)
    {
        if (isset($_SERVER['DOCKER_CONF_HOME'])) {
            return true;
        }
        $result = false;
        if ($this->dbConnection) {
            $query = sprintf('drop database %s', $name);
            $result = $this->dbConnection->exec($query);
            if ($result === false) {
            	Logger::error('Fail deleting database ' . $name);
            }
        } else {
            Logger::error('There is not an active connection');
        }
        return $result;
    }

    public function loadData()
    {
        $sqlFiles = array(self::SCHEMA_SCRIPT_FILE, self::DATA_SCRIPT_FILE);
        $files = scandir(APP_ROOT_PATH . self::DATA_PATH . self::CHANGES_PATH, SCANDIR_SORT_ASCENDING);
        if ($files === false) {
            return false;
        }
        foreach ($files as $file) {
            $info = pathinfo($file);
            if ($info['extension'] != 'sql') {
                continue;
            }
            $sqlFiles[(string) $info['basename']] = self::CHANGES_PATH . $file;
        }
        foreach ($sqlFiles as $sqlFile) {
            /*
            if ($sqlFile == 'changes/20190108110000.sql') {
                Logger::warning('Manual stop in SQL importation');
                exit();
            }
            */
            $content = file_get_contents(APP_ROOT_PATH . self::DATA_PATH . $sqlFile);
            if ($content === false) {
                return false;
            }
            try {
                Logger::info('Executing SQL file: ' . $sqlFile);
                $statement = $this->dbConnection->prepare($content);
                $res = $statement->execute();
            } catch (PDOException $e) {
                $this->error = $e->getMessage() . ' (' . $sqlFile . ')';
                Logger::error($this->error);
                return false;
            }
            if ($res === false) {
                $error = $statement->errorInfo();
                $this->error = $error[2] . ' (' . $sqlFile . ')';
                Logger::error($this->error);
                return false;
            }
        }
    	return true;
    }

    public function existDataBase(string $name)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = sprintf("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '%s'", $name);
            $result = $this->dbConnection->query($query);
        }
        return $result && $result->rowCount();
    }

    public function checkDataBase(string $name)
    {
        $result = $this->selectDataBase($name);
        if ($result) {
            $query = "SHOW TABLES LIKE 'NodeProperties'";
            $result = $this->dbConnection->query($query);
        }
        return $result && $result->rowCount();

    }

    public function userExist(string $userName)
    {
        $result = false;
        if ($this->dbConnection) {
            $host = explode(';', $this->host);
            if (! $host) {
                return false;
            }
            $host = $host[0];
            if ($host == 'localhost' and ! isset($_SERVER['DOCKER_CONF_HOME'])) {
                $query = "SELECT user FROM mysql.user where user = '{$userName}' and host = 'localhost'";
            } else {
                $query = "SELECT user FROM mysql.user where user = '{$userName}' and host = '%'";
            }
            $result = $this->dbConnection->query($query);
        }
        return $result && $result->rowCount();
    }

    public function addUser(string $userName, string $pass, string $name, bool $userExists = false)
    {
        if ($this->dbConnection) {
            
            // If the database server is installed in localhost, only the local user can access it, otherwise any remote connection be able
            $host = explode(';', $this->host);
            if (! $host) {
                return false;
            }
            $host = $host[0];
            try {
                if ($host == 'localhost' and ! isset($_SERVER['DOCKER_CONF_HOME'])) {
                    if (! $userExists) {
                        Logger::info("Creating user '{$userName}'@'localhost'");
                        $sql = "CREATE USER '{$userName}'@'localhost' IDENTIFIED BY '{$pass}'";
                        $result = $this->dbConnection->exec($sql);
                    } else {
                        $result = true;
                    }
                    if ($result !== false) {
                        $query = "GRANT ALL PRIVILEGES ON `{$name}`.* TO '{$userName}'@'localhost' WITH GRANT OPTION";
                        $result = $this->dbConnection->exec($query);
                    }
                    if ($result !== false) {
                        Logger::info("User '{$userName}'@'localhost' created / associated to database");
                    }
                } else {
                    if (! $userExists) {
                        Logger::info("Creating user '{$userName}'@'%'");
                        $sql = "CREATE USER '{$userName}'@'%' IDENTIFIED BY '{$pass}'";
                        $result = $this->dbConnection->exec($sql);
                    }
                    else {
                        $result = true;
                    }
                    if ($result !== false) {
                        $query = "GRANT ALL PRIVILEGES ON `{$name}`.* TO '{$userName}'@'%' WITH GRANT OPTION";
                        $result = $this->dbConnection->exec($query);
                    }
                    if ($result !== false) {
                        Logger::info("User '{$userName}'@'%' created / associated to database");
                    }
                }
                if ($result !== false) {
                    $this->dbConnection->exec('FLUSH privileges');
                }
            } catch (PDOException $e) {
                Logger::error('Cannot create database user: ' . $e->getMessage());
                return false;
            }
            if ($result !== false) {
                $this->user = $userName;
                $this->pass = $pass;
            }
        }
        return $result;
    }

    public function addPrivileges(string $userName, string $name)
    {
        $result = false;
        if ($this->dbConnection) {
            $query = "GRANT ALL PRIVILEGES ON {$name}.* TO '{$userName}'@'%'";
            $result = $this->dbConnection->exec($query);
            $result = $result && $this->dbConnection->exec('FLUSH privileges');
            if ($result === 0) {
            	$result = false;
            }
        }
        return $result;
    }

    public function changeUser(string $user, string $pass, string $name)
    {
        $result = false;
        if ($this->dbConnection) {
        	$result = $this->connect($this->host, null, $user, $pass, $name, true);
        }
        return $result;
    }
    
    /**
     * Return the database version in an array
     * 
     * @return string|boolean
     */
    public function server_version()
    {
        if ($this->dbConnection) {
            $res = $this->dbConnection->query('select version() as dbversion');
            if (! $res) {
                return false;
            }
            $version = $res->fetch(PDO::FETCH_ASSOC);
            if (! $version) {
                return false;
            }
            $version = $version['dbversion'];
            $info = explode('.', $version);
            if (count($info) < 2) {
                return false;
            }
            $res = array();
            if (stripos($version, 'mariadb') !== false) {
                $res[0] = 'mariadb';
            } else {
                $res[0] = 'mysql';
            }
            $res[] = $info[0];
            $res[] = $info[1];
            return $res;
        }
        return false;
    }
}
