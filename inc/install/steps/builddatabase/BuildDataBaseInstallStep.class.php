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

require_once(XIMDEX_ROOT_PATH . '/inc/install/steps/generic/GenericInstallStep.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallDataBaseManager.class.php');

class BuildDataBaseInstallStep extends GenericInstallStep {

	/**
	 * Main function. Show the step	 
	 */
	public function index(){
		
		$this->addJs("InstallDatabaseController.js");
		$this->render();
		
	}

	public function checkHost(){

		$host = $this->request->getParam("host");

		if (!$host){
			if (mysqli_connect()){
				$values["host"]="localhost";
				$values["port"]="3306";
				$values["success"]="1";
			}else{
				$values = array("host"=>"",
							"port"=>"", 
							"failure"=>"1");
			}		
		}

		$this->sendJson($values);
	}

	public function checkUser(){
		$idbManager = new InstallDataBaseManager();
		$idbManager->reconectDataBase();
		$host = $this->request->getParam("host");
		$port = $this->request->getParam("port");
		$root_user = $this->request->getParam("root_user");
		$root_pass = $this->request->getParam("root_pass");
		$values = array();
		if ($idbManager->connect($host, $port, $user, $pass)){
			$values["success"]=true;
		}else{
			$values["failure"] = true;
		}

		$this->sendJson($values);


	}

	/**
	 * [createDataBase description]
	 * @return [type] [description]
	 */
	public function createDataBase(){

		$idbManager = new InstallDataBaseManager();
		$host = $this->request->getParam("host");
		$port = $this->request->getParam("port");
		$root_user = $this->request->getParam("root_user");
		$root_pass = $this->request->getParam("root_pass");
		$name = $this->request->getParam("name");
		$user = $this->request->getParam("user");
		$pass = $this->request->getParam("pass");
		$userExist = $databaseExist = false;
		if ($idbManager->connect($host, $port, $user, $pass)){
			$userExist = true;
			if ($idbManager->selectDataBase()){
				$idbManager->loadData();
			}
		}else{
			if ($idbManager->connect($host, $port, $root_user, $root_pass)){
				if (!true){
					//crea el usuario
				}
				
			}
		}
	}




}

?>