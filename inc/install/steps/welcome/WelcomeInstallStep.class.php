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

class WelcomeInstallStep extends GenericInstallStep {


	public function __construct(){
		
		$this->installManager = new installManager();
		$this->steps = $this->installManager->getSteps();		
	}

	/**
	 * Main function. Show the step	 
	 */
	public function index(){
		$error = false;
		$this->addJs("WelcomeController.js");
	
		$this->render();
		
	}

	public function continueInstallation(){
		$this->loadNextAction();
		
	}

	public function hasErrors(){
		$checks = $this->installManager->initialChecking();
		$checks = $this->installManager->initialChecking();
		$errors = array();
		foreach ($checks as $check) {
			if ($check["state"] == "error"){
				$error = "1";
			}
			if 	($check["state"] != "success"){
				$aux = array();
                if(count($check["messages"])>0){
				    foreach ($check["messages"] as $i => $message) {
					    $aux["message"] = $message;
					    $aux["help"] = $check["help"][$i];
					    $aux["state"] = $check["state"] ;
					    $errors[] = $aux;
				    }
                }
			}
		}

		if ($error)
			$values["failure"] = true;
		else 
			$values["success"] = true;
		$values["errors"] = $errors;
		$this->sendJSON($values);
	}




}

?>
