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

include(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
ModulesManager::file("/inc/modules/modules.const");

function module_list() {

	$modMngr = new ModulesManager();

	$modules = $modMngr->getModules();

	if(ModulesManager::$msg != null) {
		echo " * ERROR: ".ModulesManager::$msg."\n";
		ModulesManager::$msg = null;
	}

	//print(" -----------------------------\n");
	print(" + Listing Available Modules +\n");
	print(" -----------------------------\n");
	foreach ($modules as $idx => $module) {
		print("  - {$module['name']}\n");
	}
	print("\n");
}

function module_install($module_name) {

	$modMngr = new ModulesManager();

	$state = $modMngr->checkModule($module_name);

	if(ModulesManager::$msg != null) {
		echo " * ERROR: ".ModulesManager::$msg."\n";
		ModulesManager::$msg = null;
	}

	if (  $state == MODULE_STATE_UNINSTALLED ) {
		$result = $modMngr->installModule($module_name);

		if(ModulesManager::$msg != null) {
			echo " * ERROR: ".ModulesManager::$msg."\n";
			ModulesManager::$msg = null;
		}

		if($result)
			print("\nModule $module_name installed.\n");
	} else {
		print("\n* ERROR: Module $module_name was not installed.\n");
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


function main($argc, $argv) {

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
			die("* ERROR: Necesito el nombre del modulo.\n");
		}
	}

	switch ($mode) {
		case "list":
				module_list();
				break;

		case "install":
				//print(" + Installing ($module_name) ...\n");
				module_install($module_name);
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

		default:
				print(" * ERROR: Mode not recognized\n");
				break;

	}

}


// Entry point.
main($argc, $argv);

?>
