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

require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallManager.class.php');
class InstallModulesManager extends InstallManager {

	const ALREADY_INSTALL = "Already installed";
	const ERROR_INSTALL = "Error";
	const UNINSTALLED = "Uninstalled";
	const SUCCESS_INSTALL = "Installed";
	const DISABLED = "Disabled";

	public function installModule($name){

		$installState = self::UNINSTALLED;
		$modMngr = new ModulesManager();
		$state = $modMngr->checkModule($name);

		$myenabled = $modMngr->isEnabled($name);
		
		switch ($state) {
			case MODULE_STATE_INSTALLED:
				$installState = self::ALREADY_INSTALL;
				# code...
				break;
			case MODULE_STATE_UNINSTALLED:
				if (!$myenabled){
					$result = $modMngr->installModule($name);
					$installState =  $result ? self::SUCCESS_INSTALL: self::ERROR_INSTALL;
				}
				break;				
			case MODULE_STATE_ERROR:
				$installState =  self::ERROR_INSTALL;
				break;
			default:
				break;
		}

		return $installState;
	}

	public function buildModulesFile(){

		$fileName = XIMDEX_ROOT_PATH.MODULES_INSTALL_PARAMS;
		if(!file_exists($fileName) || !is_writable($fileName))
			return false;
		$config = FsUtils::file_get_contents($fileName);
		
		$modMan=new ModulesManager();
		$modules=$modMan->getModules();
		$str="<?php\n\n";
		$str .= "/**
 * Paths and states constants for the Ximdex Modules, e.g.
 * The path is relative to ximdex folder.
 * define('MODULE_XIMSYNC_PATH','/modules/ximSYNC');
 */\n\n";
		foreach($modules as $mod){
			$str.=PRE_DEFINE_MODULE.strtoupper($mod["name"]).POST_PATH_DEFINE_MODULE.str_replace(XIMDEX_ROOT_PATH,'',$mod["path"])."');"."\n";
		}
		$str.="\n?>";
		$result = FsUtils::file_put_contents($fileName,$str);
		return $result;
	}

	public function installDefaultModules(){
		$defaultModules = $this->getModulesByDefault();
		foreach ($defaultModules as $module) {
			$this->installModule($module["name"]);
		}
	}	
}
?>