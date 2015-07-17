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

require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallManager.class.php');

class InstallModulesManager extends InstallManager
{

    const ALREADY_INSTALL = "Already installed";
    const ERROR_INSTALL = "Error";
    const UNINSTALLED = "Uninstalled";
    const SUCCESS_INSTALL = "Installed";
    const DISABLED = "Disabled";

    public function installModule($name)
    {

        $installState = self::UNINSTALLED;
        $modMngr = new ModulesManager();
        $state = $modMngr->checkModule($name);
        $myenabled = $modMngr->isEnabled($name);

        switch ($state) {
            case ModulesManager::get_module_state_installed():
                $installState = self::ALREADY_INSTALL;
                # code...
                break;
            case ModulesManager::get_module_state_uninstalled():
                if (!$myenabled) {
                    $result = $modMngr->installModule($name);
                    $installState = $result ? self::SUCCESS_INSTALL : self::ERROR_INSTALL;
                }
                break;
            case ModulesManager::get_module_state_error():
                $installState = self::ERROR_INSTALL;
                break;
            default:
                break;
        }

        return $installState;
    }

    public function enableModule($name)
    {

        if (strtolower($name) != "ximloader") {
            $modMngr = new ModulesManager();
            $modMngr->enableModule($name);
        }
    }

    public function uninstallModule($name)
    {
        $modMngr = new ModulesManager();
        $modMngr->uninstallModule($name);
    }

    public function buildModulesFile()
    {


        $fileName = XIMDEX_ROOT_PATH . ModulesManager::get_modules_install_params();
        @unlink($fileName);
        /*if(!file_exists($fileName) || !is_writable($fileName))
            return false;*/
        $config = FsUtils::file_get_contents($fileName);

        $modMan = new ModulesManager();
        $modules = $modMan->getModules();
        foreach ($modules as $mod) {
            if (isset($mod["enabled"])) {
                $modMan->uninstallModule($mod["name"]);
            }
        }
        $str = "<?php\n\n";
        $str .= "/**
			}
 * Paths and states constants for the Ximdex Modules, e.g.
 * The path is relative to ximdex folder.
 * define('MODULE_XIMSYNC_PATH','/modules/ximSYNC');
 */\n\n";

        foreach ($modules as $mod) {
            @unlink(XIMDEX_ROOT_PATH . "/data/." . $mod["name"]);
            $str .= ModulesManager::get_pre_define_module() . strtoupper($mod["name"]) . ModulesManager::get_post_path_define_module() . str_replace(XIMDEX_ROOT_PATH, '', $mod["path"]) . "');" . "\n";
        }
        $str .= "\n?>";
        $result = FsUtils::file_put_contents($fileName, $str);
        chmod($fileName, 0775);
        return $result;
    }

    public function installDefaultModules()
    {
        $defaultModules = $this->getModulesByDefault();
        foreach ($defaultModules as $module) {
            $this->installModule($module["name"]);
        }
    }
}