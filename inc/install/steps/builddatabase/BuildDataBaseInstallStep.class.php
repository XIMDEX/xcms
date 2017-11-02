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

require_once(XIMDEX_ROOT_PATH . '/inc/install/steps/generic/GenericInstallStep.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallDataBaseManager.class.php');

class BuildDataBaseInstallStep extends GenericInstallStep
{
    const RECOMMENDED_MYSQL_VERSION = '5.7';
    const RECOMMENDED_MARIADB_VERSION = '10.2';
    
    /**
     * Main function. Show the step
     */
    public function index()
    {
        $this->addJs("InstallDatabaseController.js");
        $values = array();
        $values["ximdexName"] = basename(XIMDEX_ROOT_PATH);
        if (isset($_SERVER['DOCKER_CONF_HOME']))
            $values['ximdexDataBaseHostName'] = 'db';
        else
            $values['ximdexDataBaseHostName'] = 'localhost';
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
        if ( is_null( $pass )) {
            $pass = '' ;
        }
        $values = array();
        $idbManager->connect($host, $port, $user, $pass);
        if ($idbManager->existDataBase($name)) {
        	
        	//if the host specified is the db for docker, we don't need to show the database overwriting message 
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
        if ($idbManager->connect($host, $port, $user, $pass) === false)
        {
            $values["failure"] = true;
            $values["errors"] = $idbManager->getErrors();
        }
        else
        {
            if ($idbManager->existDataBase($name)) {
                $idbManager->deleteDataBase($name);
            }
            if ($idbManager->connect($host, $port, $user, $pass)) {
                $result = $idbManager->createDataBase($name);
                if (!$result)
                {
                	$values["failure"] = true;
                	if ($idbManager->getErrors())
                		$values["errors"] = $idbManager->getErrors();
                	else
                		$values["errors"] = 'Can\'t create database';
                }
                $idbManager->connect($host, $port, $user, $pass, $name, true);
                $idbManager->loadData($host, $port, $user, $pass, $name);
                $result = $idbManager->checkDataBase($host, $port, $user, $pass, $name);
                if ($result) {
                    $values["success"] = true;
                } else {
                    $values["failure"] = true;
                    if ($idbManager->getErrors())
                    	$values["errors"] = $idbManager->getErrors();
                   	else
                   		$values["errors"] = 'Can\'t create database schema and content';
                }
            } else {
                $values["failure"] = true;
                $values["errors"] = $idbManager->getErrors();
            }
        }
        $this->sendJSON($values);
    }

    public function addUser()
    {
        $idbManager = new InstallDataBaseManager();
        $host = $this->request->getParam("host");
        $port = $this->request->getParam("port");
        $user = $this->request->getParam("user");
        $pass = $this->request->getParam("pass");
        $name = $this->request->getParam("name");
        $root_user = $this->request->getParam("root_user");
        $root_pass = $this->request->getParam("root_pass");
        
        if ( is_null( $root_pass )) {
            $root_pass = '' ;
        }
        $values = array();
        if ($user == $root_user) {
            $values["success"] = true;
            $this->initParams($host, $port, $name, $user, $root_pass);
            $this->sendJson($values);
        }
        $idbManager->connect($host, $port, $root_user, $root_pass, $name );
        $values = array();
        $failure = false;
        if (!$idbManager->changeUser($user, $pass, $name)) {

            $idbManager->reconectDataBase(  ); //
            $idbManager->connect($host, $port, $root_user, $root_pass, $name );

            $idbManager->addUser($user, $pass, $name);
        }
        if ($failure)
            $values["failure"] = true;
        else {
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
        $name = $this->request->getParam('name');
        $user = $this->request->getParam('user');
        $pass = $this->request->getParam('pass');
        $values = array();
        if ($idbManager->connect($host, $port, $user, $pass) === false)
        {
            $values['failure'] = true;
            $values['errors'] = $idbManager->getErrors();
        }
        else
        {
            $serverInfo = $idbManager->server_version();
            $version = doubleval($serverInfo[1] . '.' . $serverInfo[2]);
            if ($serverInfo[0] == 'mariadb')
                $minVersion = self::RECOMMENDED_MARIADB_VERSION;
            else
                $minVersion = self::RECOMMENDED_MYSQL_VERSION;
            if ($version < $minVersion)
            {
                $values['failure'] = true;
                $values['errors'] = 'The recommended database version is ' . $minVersion . ' or higher and the installed one is ' . $version 
                        . ' (' . $serverInfo[0] . '). You can continue with the installation process, but stability is not secured';
            }
            else
                $values['success'] = true;
        }
        $this->sendJson($values);
    }
}