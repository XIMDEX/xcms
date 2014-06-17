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
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/FastTraverseManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallModulesManager.class.php');

/**
 * Step class to install the modules. It will install the no core modules
 */
class XimdexModulesInstallStep extends GenericInstallStep {

    /**
     * Default step method. List all the modules	 
     */
    public function index(){		
        $this->addJs("InstallModulesController.js");
        $imManager = new InstallModulesManager(InstallModulesManager::WEB_MODE);
        $result = $imManager->buildModulesFile();		
        if (!$result){
            $error["state"]="error";
            $error["messages"][] = "Impossible to install modules. Do you have the proper permissions on install/install-modules.conf file?";
            $this->exceptions[]=$error;
        }
        $this->render();
    }

    /**
     * List all none default modules and send a json object
     */
    public function getModulesLikeJson(){
            $imManager = new InstallModulesManager(InstallModulesManager::WEB_MODE);
            $ftManager = new FastTraverseManager (FastTraverseManager::WEB_MODE);		
            $ftManager->buildFastTraverse();		
            $modules = $this->installManager->getModulesByDefault(false);		
            $this->sendJSON($modules);
    }

    /**
     * Install a module specified in params.	 
     */
    public function installModule(){
        $moduleName = $this->request->getParam("module");
        $imManager = new InstallModulesManager(InstallModulesManager::WEB_MODE);
        $imManager->uninstallModule($moduleName);
        $installState = $imManager->installModule($moduleName);
        $imManager->enableModule($moduleName);
        $values=array("result"=>strtolower($installState));
        $this->sendJSON($values);
    }
}

?>