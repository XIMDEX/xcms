<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

require_once APP_ROOT_PATH . '/install/steps/generic/GenericInstallStep.class.php';
require_once APP_ROOT_PATH . '/install/managers/InstallModulesManager.class.php';
require_once APP_ROOT_PATH . '/install/managers/InstallDataBaseManager.class.php';

class BuildDataBaseInstallStep extends GenericInstallStep
{
    const RECOMMENDED_MYSQL_VERSION = '5.7';
    const RECOMMENDED_MARIADB_VERSION = '10.2';
    
    /**
     * Main function. Show the step
     */
    public function index()
    {
        $this->initialize_values(false);
        $this->addJs("InstallDatabaseController.js");
        $values = array();
        $values["ximdexName"] = basename(XIMDEX_ROOT_PATH);
        if (isset($_SERVER['DOCKER_CONF_HOME'])) {
            $values['ximdexDataBaseHostName'] = 'db';
        } else {
            $values['ximdexDataBaseHostName'] = 'localhost';
        }
        $this->render($values);
    }
    
    public function checkUser()
    {
        $idbManager = new InstallDataBaseManager();
        $idbManager->reconectDataBase();
        $host = $this->request->getParam("host");
        $port = $this->request->getParam("port");
        $user = $this->request->getParam("user");
        $pass = $this->request->getParam("pass") == "undefined" ? NULL : $this->request->getParam("pass");
        $values = array();
        if ($idbManager->connect($host, $port, $user, $pass)) {
            $values["success"] = true;
        } else {
            $values["failure"] = true;
            $values["errors"] = $idbManager->getConnectionErrors();
        }
        $this->sendJson($values);
    }

    public function checkExistDataBase()
    {
		$idbManager = new InstallDataBaseManager();
        $idbManager->reconectDataBase();
        $host = $this->request->getParam("host");
        $port = $this->request->getParam("port");
        $name = $this->request->getParam("name");
        $user = $this->request->getParam("user");
        $pass = $this->request->getParam("pass");
        if (is_null($pass)) {
            $pass = '' ;
        }
        $values = array();
        $idbManager->connect($host, $port, $user, $pass);
        if ($idbManager->existDataBase($name)) {
        	
        	// If the host specified is the db for docker, we don't need to show the database overwriting message 
            if (isset($_SERVER['DOCKER_CONF_HOME']))
        		$values["success"] = true;
        	else
            	$values["failure"] = true;
        } else {
            $values["success"] = true;
        }
        $this->sendJson($values);
    }
    
    public function createDataBase()
    {
        $idbManager = new InstallDataBaseManager();
        $host = $this->request->getParam("host");
        $port = $this->request->getParam("port");
        $name = $this->request->getParam("name");
        $user = $this->request->getParam("user");
        $pass = $this->request->getParam("pass");
        $values = array();
        if ($idbManager->connect($host, $port, $user, $pass) === false) {
            $values["failure"] = true;
            $values["errors"] = $idbManager->getErrors();
            $this->sendJSON($values);
        }
        if ($idbManager->existDataBase($name)) {
            $idbManager->deleteDataBase($name);
        }
        if (!$idbManager->connect($host, $port, $user, $pass)) {
            $values["failure"] = true;
            $values["errors"] = $idbManager->getErrors();
            $this->sendJSON($values);
        }
        if (!$idbManager->createDataBase($name))
        {
        	$values["failure"] = true;
        	if ($idbManager->getErrors()) {
        		$values["errors"] = $idbManager->getErrors();
        	} else {
        		$values["errors"] = 'Can\'t create database with the specified parameters';
        	}
        	$this->sendJSON($values);
        }
        if (!$idbManager->connect($host, $port, $user, $pass, $name, true)) {
            $values["failure"] = true;
            $values["errors"] = 'Cannot connect to database';
            $this->sendJSON($values);
        }
        if (!$idbManager->loadData($host, $port, $user, $pass, $name)) {
            $values["failure"] = true;
            $values["errors"] = 'Cannot generate the database schema and data';
            $this->sendJSON($values);
        }
        if (!$idbManager->checkDataBase($host, $port, $user, $pass, $name)) {
            $values["failure"] = true;
            if ($idbManager->getErrors()) {
            	$values["errors"] = $idbManager->getErrors();
            } else {
           		$values["errors"] = 'Can\'t create database schema and content';
           	}
           	$this->sendJSON($values);
        }
        // If the app is working under a Docker instance, the new user creation will be omited
        if (isset($_SERVER['DOCKER_CONF_HOME'])) {
            $this->initParams($host, $port, $name, $user, $pass);
            $values['skipNewDBUser'] = true;
        } else {
            $values['skipNewDBUser'] = false;
        }
        $values["success"] = true;
        $this->sendJSON($values);
    }

    public function addUser()
    {
        $host = $this->request->getParam("host");
        $port = $this->request->getParam("port");
        $user = $this->request->getParam("user");
        $pass = $this->request->getParam("pass");
        $name = $this->request->getParam("name");
        $root_user = $this->request->getParam("root_user");
        $root_pass = $this->request->getParam("root_pass");
        if (is_null($root_pass)) {
            $root_pass = '' ;
        }
        $values = array();
        if ($user == $root_user) {
            $values["success"] = true;
            $this->initParams($host, $port, $name, $user, $pass);
            $this->sendJson($values);
        }
        $idbManager = new InstallDataBaseManager();
        $idbManager->connect($host, $port, $root_user, $root_pass, $name);
        
        // Check if the new user exists already
        if ($userExists = $idbManager->userExist($user)) {
            
            // The password must be the actual one for the existant user, trying a connection with him
            $idbManagerAux = new InstallDataBaseManager();
            if ($idbManagerAux->connect($host, $port, $user, $pass, false, true) === false) {
                $values["failure"] = true;
                $values["errors"] = 'This user exists already in the server, but the password is not correct';
                $this->sendJson($values);
            }
            unset($idbManagerAux);
        }
        
        // Add the new user and associate it with the database, or create the link with a old user 
        if ($idbManager->addUser($user, $pass, $name, $userExists) === false) {
            $values["failure"] = true;
            if ($userExists) {
                $values["errors"] = 'This user exists alredy, but the password must be the specefied user one';
            } else {
                $values["errors"] = 'The user cannot be created';
            }
        } else {
            $values["success"] = true;
            $this->initParams($host, $port, $name, $user, $pass);
        }
        $this->sendJson($values);
    }

    public function initParams($host, $port, $bdName, $user, $pass)
    {
        $this->installManager->setInstallParams($host, $port, $bdName, $user, $pass);
        $this->loadNextAction();
    }

    /**
     * check the database server version to notice older versions
     */
    public function check_database_version()
    {
        $idbManager = new InstallDataBaseManager();
        $host = $this->request->getParam('host');
        $port = $this->request->getParam('port');
        $user = $this->request->getParam('user');
        $pass = $this->request->getParam('pass');
        $values = array();
        if ($idbManager->connect($host, $port, $user, $pass) === false) {
            $values['failure'] = true;
            $values['errors'] = $idbManager->getErrors(); 
        } else {
            $serverInfo = $idbManager->server_version();
            $version = doubleval($serverInfo[1] . '.' . $serverInfo[2]);
            if ($serverInfo[0] == 'mariadb') {
                $minVersion = self::RECOMMENDED_MARIADB_VERSION;
            } else {
                $minVersion = self::RECOMMENDED_MYSQL_VERSION;
            }
            if ($version < $minVersion) {
                $values['failure'] = true;
                $values['errors'] = 'The recommended database version is ' . $minVersion 
                    . ' or higher and the installed one is ' 
                    . $version . ' (' . $serverInfo[0] 
                    . '). You can continue with the installation process, but stability is not assured';
            } else {
                $values['success'] = true;
            }
        }
        $this->sendJson($values);
    }
}
