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
 *  @version $Revision: 7740 $
 */



ModulesManager::file('/inc/serializer/Serializer.class.php');

class Action_moduleslist extends ActionAbstract {

	public function index() {

		$modules = $this->readModules();
		$values = array('modules' => $modules);
		
		$this->render($values, null, 'only_template.tpl');
		
		//header('Content-type: application/json');
		//echo Serializer::encode(SZR_JSON, $data);
	}
	
	protected function readModules() {
		
		$modules = array();
		$userId = XSession::get('userID');
		if ($userId != '301') {
			// Must be administrator
			return $modules;
		}		
		
		$paths = ModulesManager::getModules();
				
		foreach ($paths as $module) {
			$moduleName=$module["name"];
			if(strpos($moduleName,'xim')!==false){
				$isEnabled = $this->isEnabled($moduleName);
				$modules[] = array(
					'name' => $moduleName,
					'enabled' => $isEnabled,
					'class' => ($isEnabled ? 'browser-modules-view-enabled' : 'browser-modules-view-disabled')
				);
			}
		}
		
		return $modules;
	}
	
	/*protected function isInstalled($moduleName) {
		return ModulesManager::isEnabled($moduleName);
	}*/
	
	protected function isEnabled($moduleName) {
		return ModulesManager::isEnabled($moduleName);
	}
	
}

?>
