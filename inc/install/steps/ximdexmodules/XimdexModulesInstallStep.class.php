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

class XimdexModulesInstallStep extends GenericInstallStep {

	const ALREADY_INSTALL = "Already installed";
	const ERROR_INSTALL = "Error";
	const UNINSTALLED = "Uninstalled";
	const SUCCESS_INSTALL = "Installed!";
	const DISABLED = "Disabled";

	public function index(){
		
		/*$modules = $this->getModules();
		$values = array(
			"modules"=>$modules	
		); */
		$this->addJs("InstallModulesController.js");
		$this->render();
	}

	private function getModules(){
		$result = array();
		$xpath = new DomXPath($this->installConfig);
		$query = "/install/modules/module";
		$modules = $xpath->query($query);

		foreach ($modules as $module) {
			$auxModuleArray=array(); 
			foreach($module->attributes as $attribute){
				$auxModuleArray[$attribute->name] = $attribute->value;
			}
			$auxModuleArray["name"] = $module->nodeValue;
			$result[] = $auxModuleArray;

		}		
		return $result;
	}

	public function getModulesLikeJson(){
		$modules = $this->getModules();		
		$this->sendJSON($modules);
	}

	public function installModule(){
		$module_name = $this->request->getParam("module");
		$installState = self::UNINSTALLED;
		$modMngr = new ModulesManager();
		$state = $modMngr->checkModule($module_name);

		$myenabled = $modMngr->isEnabled($module_name);
		
		switch ($state) {
			case MODULE_STATE_INSTALLED:
				$installState = self::ALREADY_INSTALL;
				# code...
				break;
			case MODULE_STATE_UNINSTALLED:
				if (!$myenabled){
					$result = $modMngr->installModule($module_name);
					$installState =  $result ? self::SUCCESS_INSTALL: self::ERROR_INSTALL;

				}
				# code...
				break;				
			case MODULE_STATE_ERROR:
					$installState =  self::ERROR_INSTALL;
				break;
			default:
				# code...
				break;
		}

		$values=array("result"=>$installState);
		$this->sendJSON($values);
				
	}
}

?>