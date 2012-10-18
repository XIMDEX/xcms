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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

include_once(XIMDEX_ROOT_PATH . '/inc/modules/modules.const');
include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesConfig.class.php');
include_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
ModulesManager::file(MODULES_INSTALL_PARAMS);

/**
 *
 */
class ModulesManager {

	const ERROR = 'ERROR';
	const WARNING = 'WARNING';
	const SUCCESS = 'SUCCESS';

	var $modules;
	var $caller;

	function ModulesManager($caller = NULL) {

		// Init stuff.
		$this->caller = $caller;
		//$this->modules = $this->getModules();
	}

	function parseModules($constModule, &$modules){
		$paths = FsUtils::readFolder($constModule, false/*, $excluded = array()*/);
		if($paths){
			foreach ($paths as $moduleName) {
				$modulePath = $constModule . $moduleName;
				//if (is_dir($modulePath) && preg_match('/^xim+/', $moduleName, $matches)) {
				if (is_dir($modulePath) && "pro" != $moduleName) {
					$i = count($modules);
					$modules[$i]["name"] = $moduleName;
					$modules[$i]["path"] = $modulePath;
					$modules[$i]["enable"] = (int) self::isEnabled($moduleName);
				}
			}
		}
	}

	function parseMetaParent($constModule, &$metaParent) {
		$paths = FsUtils::readFolder($constModule, false);
		if($paths){
			foreach ($paths as $moduleName) {
				$modulePath = $constModule . $moduleName;
				//if (is_dir($modulePath) && preg_match('/^xim+/', $moduleName, $matches)) {
				if (is_dir($modulePath) && "pro" != $moduleName && file_exists($modulePath . "/conf.ini")) {
					$conf = parse_ini_file($modulePath . "/conf.ini");
					foreach($conf['module'] as $id => $childrenModName)
						$metaParent[$childrenModName] = $moduleName;
				}
			}
		}
	}

	function getModules() {
		$modules = array();
		self::parseModules(XIMDEX_MODULES_DIR, $modules);
		self::parseModules(XIMDEX_MODULES_PRO_DIR, $modules);
		return $modules;
	}

	function getMetaParent() {
		$modules = array();
		self::parseMetaParent(XIMDEX_MODULES_DIR, $metaParent);
		self::parseMetaParent(XIMDEX_MODULES_PRO_DIR, $metaParent);
		return $metaParent;
	}

	function hasMetaParent($name) {
		$metaParent = self::getMetaParent();
		if(!empty($metaParent) && in_array($name, array_keys($metaParent)) && $this->caller != $metaParent[$name])
			return $metaParent;
		return false;
	}

	function existModule($name) {


	}

   	static function  path($name) {
   		$str =  "MODULE_".strtoupper($name)."_PATH";
   		if(defined($str) ) {
   	   		return constant($str);
   		}else {
   			return "";
   		}
   	}

	function installModule($name) {
		if($metaParent = self::hasMetaParent($name)) {
			printf(" [*] ERROR: Can't install module %s directly. Try installing Meta-module %s instead\n", $name, $metaParent[$name]);
			return false;
		}

		if (ModulesManager::isEnabled($name)) {
			echo "checkModule: MODULE_STATE_ENABLED, try to reinstall \n";
			return MODULE_STATE_INSTALLED;
		}
		$module = $this->instanceModule($name);

		if ( is_null($module) ) {
			print(" * ERROR: Can't install module $name\n");
			return false;
		}

		$module->install();
	}

	function uninstallModule($name) {
		if($metaParent = self::hasMetaParent($name)) {
			printf(" [*] ERROR: Can't uninstall module %s directly. Try uninstalling Meta-module %s instead\n", $name, $metaParent[$name]);
			return false;
		}

		$module = $this->instanceModule($name);

		if ( is_null($module) ) {
			print(" * ERROR: Can't uninstall module $name\n");
			return false;
		}

		$module->uninstall();
	}

	function checkModule($name) {


		$module = $this->instanceModule($name);

		if ( is_null($module) ) {
			print(" * ERROR: Module instance down\n");
			return MODULE_STATE_ERROR;
		}

		return $module->state();
/*
		switch ($module->state()) {

			case MODULE_STATE_INSTALLED:
				print("checkModule: MODULE_STATE_INSTALLED\n");
				break;

			case MODULE_STATE_UNINSTALLED:
				print("checkModule: MODULE_STATE_UNINSTALLED\n");
				break;

			case MODULE_STATE_ERROR:
				print("checkModule: MODULE_STATE_ERROR\n");
				break;

			default:
				print("checkModule: DEFAULT\n");
				break;

		}
	*/
	}

//END


    /**
     *  Enable a Module.
     */
	function enableModule($name) {
		if($metaParent = self::hasMetaParent($name)) {
			printf(" [*] ERROR: Can't enable module %s directly. Try enabling Meta-module %s instead\n", $name, $metaParent[$name]);
			return false;
		}

		$module = $this->instanceModule($name);

		if ( is_null($module) ) {
			print(" * ERROR: instance module down\n");
			return false;
		}

		$modConfig = new ModulesConfig();
		$modConfig->enableModule($module->getModuleName());

		$module->enable();

	}

    /**
     *  Disable a Module.
     */
	function disableModule($name) {
		if($metaParent = self::hasMetaParent($name)) {
			printf(" [*] ERROR: Can't disable module %s directly. Try disabling Meta-module %s instead\n", $name, $metaParent[$name]);
			return false;
		}
		$module = $this->instanceModule($name);

		if ( is_null($module) ) {
			print(" * ERROR: instance module down\n");
			return false;
		}

		$modConfig = new ModulesConfig();
		$modConfig->disableModule($module->getModuleName());

		$module->disable();

	}

    /**
     *  Instantiate a module by name.
     *  @protected
     *  @param $name Name of the module.
     *  @return NULL | & Module (child).
     */
	function instanceModule($name) {

		// If no name provided exit.
		if (is_null($name)) {
			print(" * ERROR: Module name not provided.\n");
			return NULL;
		}

		// If module not exists exit.

		$moduleClassName = MODULE_PREFIX . $name;
		$moduleClassFile = MODULE_PREFIX . $name . ".class.php";
		//$moduleClassPath = XIMDEX_ROOT_PATH . "/modules/$name/" . $moduleClassFile;
		$moduleClassPath = XIMDEX_ROOT_PATH.self::path($name)."/".$moduleClassFile;
		if (file_exists($moduleClassPath)) {
			include_once($moduleClassPath);
		} else {
			print(" * ERROR: Module definition file not found [$moduleClassPath].\n");
			return NULL;
		}

		$module = new $moduleClassName;

		if ( is_null($module) ) {
			print(" * ERROR: Module not instantiated [$moduleClassName].\n");
			return NULL;
		}

		return $module;
	}

	public static function isEnabled($name) {
		$str = "MODULE_" . strtoupper($name) . "_ENABLED";

		if (defined($str)) {
			return true;
		} else {
			return false;
		}

	}


	public static function getEnabledModules() {

		$modules = self::getModules();
		foreach ($modules as $key => $module) {
			if (!self::isEnabled($module)) {
				unset($modules[$key]);
			}
		}
		return $modules;
	}

	function log($priority, $string) {

		if ($this instanceof Modules) {
			XMD_Log::warning("Using ModulesManager::log in a class that is not an instance of Module.");
			return false;
		}

		$module_name = $this->name;

		switch ($priority) {
			case self::SUCCESS:
				//echo(" - [$module_name] (SUCCESS): $string\n");
            XMD_Log::info(" - [$module_name] (SUCCESS): $string");
				break;
			case self::ERROR:
			default:
				echo(" * [$module_name] (ERROR): $string\n");
				XMD_Log::error($string);
		}
	}



	public static function component($_file, $_component = 'XIMDEX') {
		if("XIMDEX" == $_component) {
                        $dir = '';
                }else {
                        $dir = self::path($_component);
               }

		self::file($dir.$_file);
	}


	public static function file($_file, $_module = 'XIMDEX') {
		if("XIMDEX" == $_module) {
			$dir = '';
	    	}else {
			$dir = self::path($_module);
	    	}

		 //$trace = debug_backtrace();
		if(file_exists(XIMDEX_ROOT_PATH."{$dir}{$_file}")){
	    		if( ( self::isEnabled($_module) || 'XIMDEX' == $_module) ) {
				// $from =  $trace[0]["file"]." in line ".$trace[0]["line"];
			     //XMD_Log::info(" load file: <em>$_file</em> <strong>{$_module}</strong>  in $from <br>");
		//	 	echo " load file: <em>$_file</em> <strong>{$_module}</strong>  in $from <br>";
			 	return require_once(XIMDEX_ROOT_PATH."{$dir}{$_file}");
	    		}else {
        //	$from =  $trace[1]["file"]." in line ".$trace[1]["line"];
	      //XMD_Log::info("Not load file: <em>$_file</em> necesita <strong> {$_module}</strong>  in $from ");
		  // 	echo "Not load file: <em>$_file</em> necesita <strong>{$_module}</strong>  in $from <br>";
	    		}

		}
		else{

			//$from =  $trace[0]["file"]." in line ".$trace[0]["line"];
			//echo "File not found: <em>$_file</em> of <strong>{$_module}</strong> module in $from <br>";
		}
	}
}
?>
