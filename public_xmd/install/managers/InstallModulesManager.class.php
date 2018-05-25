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

use Ximdex\Logger;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;

require_once(APP_ROOT_PATH.'/install/managers/InstallManager.class.php');

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
        $modMngr = new \Ximdex\Modules\Manager();
        $state = $modMngr->checkModule($name);
        $myenabled = $modMngr->isEnabled($name);
        switch ($state) {
            case \Ximdex\Modules\Manager::get_module_state_installed():
                $installState = self::ALREADY_INSTALL;
                
                # Code...
                break;
            case \Ximdex\Modules\Manager::get_module_state_uninstalled():
                if (!$myenabled) {
                    $result = $modMngr->installModule($name);
                    $installState = $result ? self::SUCCESS_INSTALL : self::ERROR_INSTALL;
                }
                break;
            case \Ximdex\Modules\Manager::get_module_state_error():
                $installState = self::ERROR_INSTALL;
                break;
            default:
                break;
        }
        return $installState;
    }

    public function enableModule($name)
    {
        $modMngr = new \Ximdex\Modules\Manager();
        $modMngr->enableModule($name);
    }

    public function uninstallModule($name)
    {
        $modMngr = new \Ximdex\Modules\Manager();
        $modMngr->uninstallModule($name);
    }
	
    public function buildModulesFile()
    {
        $fileName = XIMDEX_ROOT_PATH . \Ximdex\Modules\Manager::get_modules_install_params();
        @unlink($fileName);
        $modMan = new \Ximdex\Modules\Manager();
        $modules = $modMan->getModules();
        foreach ($modules as $mod) {
            if (isset($mod["enabled"])) {
                $modMan->uninstallModule($mod["name"]);
            }
        }
        $str = "<?php\n\n";
        $str .= "/**
 * Paths and states constants for the Ximdex Modules, e.g.
 * The path is relative to ximdex folder.
 * define('MODULE_XIMSYNC_PATH','/modules/ximSYNC');
 */\n\n";
        foreach ($modules as $mod) {
            @unlink(XIMDEX_ROOT_PATH . "/data/." . $mod["name"]);
            $str .= \Ximdex\Modules\Manager::get_pre_define_module() . strtoupper($mod["name"]) 
                . \Ximdex\Modules\Manager::get_post_path_define_module() . str_replace(XIMDEX_ROOT_PATH, '', $mod["path"]) . "');" . "\n";
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
    
    /**
     * Xedit configuration
     *
     * @return boolean
     */
    public function installXedit()
    {
        $xeditPath = App::getValue('XmodulesRoot') . '/xedit';
        if (!is_dir(XIMDEX_ROOT_PATH . $xeditPath)) {
            Logger::error('Cannot configure Xedit. Directory ' . XIMDEX_ROOT_PATH . $xeditPath . ' does not exists');
            return true;
        }
        App::setValue('HTMLEditorURL', App::getValue('UrlHost') . App::getValue('UrlRoot') . $xeditPath, true);
        App::setValue('HTMLEditorEnabled', '1', true);
        return true;
    }
}
