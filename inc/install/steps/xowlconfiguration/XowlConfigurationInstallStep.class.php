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
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/FastTraverseManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallModulesManager.class.php');

/**
 * Step class to install the modules. It will install the no core modules
 */
class XowlConfigurationInstallStep extends GenericInstallStep
{

    /**
     * Default step method. List all the modules
     */
    public function index()
    {
        $this->addJs("XowlConfigurationController.js");
        $this->render();
    }

    public function configure()
    {
        $module = ModulesManager::instanceModule("Xowl");

        $apikey = trim($this->request->getParam("apikey"));
        $serviceurl = trim($this->request->getParam("serviceurl"));

        $data = array();
        if (is_null($module)) {
            $data["error"] = 1;
            $this->sendJSON($data);
            return;
        }
        if ( !preg_match("/^(https?:\\/\\/.+)$/i", $serviceurl)
            || !preg_match("/\\d+-\\d+-\\d+/", $apikey) ) {
            $data["error"] = 1;
            $data["message"] = "These fields are not correct.";
        } else {
            if ($module->configure($apikey, $serviceurl)) {
                $data["error"] = 0;
                $data["message"] = "Xowl service has been properly configured.";
            } else {
                $data["error"] = 1;
                $data["message"] = "Xowl service configuration is not correct.";
            }
        }
        $this->sendJSON($data);


    }

}

?>