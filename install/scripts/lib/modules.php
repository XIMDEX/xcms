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


if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../"));

if (!defined('CLI_MODE'))
	define('CLI_MODE', 1);

require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallModulesManager.class.php');

/**
 * Usage function
 */
function module_usage(){
	echo "Usage: php modules.php install [-options | module_name] \n";
	echo "       php modules.php [uninstall|enable|disable|list] \n";
	echo "\nwhere options include:";
	echo "    -a                   install all modules\n";
	echo "    -c                   install only core modules (default option)\n";
	echo "    -r 		           install only recomended modules\n";	
	echo "    -h --help            show command help\n";
	echo "\nExamples:\n";
	echo "   php modules install ximIO\n";
	echo "   php modules install -c\n";
	echo "   php modules uninstall -c\n";
}

function module_list() {

	$imManager = new InstallModulesManager();
	$modules = $imManager->getAllModules();	

	//print(" -----------------------------\n");
	print(" + Listing Available Modules +\n");
	print(" -----------------------------\n");
	foreach ($modules as $idx => $module) {
		print("  - {$module['alias']}: {$module['description']} ");
		if ($modMngr->isEnabled($module['name'])) {
			print("(enabled)");
		}
		print("\n");
	}
	print("\n");
}

function module_install($argv){
//instance modules manager.
	$imManager = new InstallModulesManager();

	$mustInstall = true;
	$modules = array();
	//Load modules by command parameters
	if (isset($argv[2])){
		switch ($argv[2]) {
			case "-a":
				$modules = $imManager->getAllModules();
				break;
			case "-c":
				$modules = $imManager->getModulesByDefault();
				break;
			case "-r":
				$modules = $imManager->getModulesByDefault(false);
				break;
			default:
				$modules = $imManager->getModuleByName($argv[2],false);
				break;			
		}

	}else { //Default usage, install core modules
		$modules = $imManager->getModulesByDefault();	
	}

	//if must install, install the loaded modules
	if ($mustInstall){
		foreach ($modules as $module) {
			echo "Installing module {$module['alias']}. {$module['description']}\n";		
			$state = $imManager->installModule($module["name"]);
			$message = "";
			switch ($state) {
				case InstallModulesManager::ALREADY_INSTALL:
					$message = "\nIt was already installed!";
					break;
				case InstallModulesManager::SUCCESS_INSTALL:
					$message = "\nModule {$module["alias"]} sucesfully installed.\n";
					break;
				case InstallModulesManager::SUCCESS_INSTALL:
					$message = "\nModule {$module["alias"]} reported a problem while installation.\n";
					break;
			}

			echo "$message\n";
			
		}
	}
}

function module_uninstall($module_name) {

	$modMngr = new ModulesManager();

	$modMngr->uninstallModule($module_name);

	if(ModulesManager::$msg != null) {
		echo " * ERROR: ".ModulesManager::$msg."\n";
		ModulesManager::$msg = null;
	}

	 print("\nModule $module_name uninstalled.\n");
}

function module_enable($module_name) {

	$modMngr = new ModulesManager();

	$modMngr->enableModule($module_name);

	if(ModulesManager::$msg != null) {
		echo " * ERROR: ".ModulesManager::$msg."\n";
		ModulesManager::$msg = null;
	}
}

function module_disable($module_name) {

	$modMngr = new ModulesManager();

	$modMngr->disableModule($module_name);

	if(ModulesManager::$msg != null) {
		echo " * ERROR: ".ModulesManager::$msg."\n";
		ModulesManager::$msg = null;
	}
}

function main ($argc, $argv){
	if ($argc < 2) {
		print(" * ERROR: Bad syntax\n");
	}

	$mode = $argv[1];

	if ( $argc > 2 ) {
		$module_name = $argv[2];
		if("ximLOADER" == $module_name ) {
			//Preseletection of demo in ximloader
			if( $argc > 3 ) {
				define("XIMLOADER_DEFAULT", array_pop($argv) );
			}
		}
	}

	if ( $mode != "list" ) {
		if ($argc < 3) {
			die("* ERROR: I need the module name.\n");
		}
	}

	print("\n");
	switch ($mode) {
		case "list":
				module_list();
				break;

		case "install":
				//print(" + Installing ($module_name) ...\n");
				module_install($argv);
				break;

		case "uninstall":
				//print(" + Uninstalling ($module_name) ...\n");
				module_uninstall($module_name);
				break;

		case "enable":
				//print(" + Enabling ($module_name) ...\n");
				module_enable($module_name);
				break;

		case "disable":
				//print(" + Disabling ($module_name) ...\n");
				module_disable($module_name);
				break;
	    case "-h":
		case "--help":
				module_usage();
				break;
		default:
				print(" * ERROR: Mode not recognized\n");
				break;

	}
}
// Entry point.
main($argc, $argv);
?>