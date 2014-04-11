#!/usr/bin/php -q
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



/**
 * Script to list or install default modules. 
 * This modules are defined in inc/install/conf/install.xml
 */
if (!defined('XIMDEX_ROOT_PATH'))
define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallModulesManager.class.php');

//Get all default modules
$imManager = new InstallModulesManager();
$modules = $imManager->getModulesByDefault();

//List them
if (isset($argv[1]) &&  "-l" == $argv[1]) {
	foreach($modules as $mod){
		echo $mod["alias"]."\n";
	}
}else { //Installing every module
	foreach ($modules as $module) {
		echo "Installing module {$module['alias']}. {$module['description']}\n";		
		$imManager->installModule($module["name"]);
	}
}
?>
