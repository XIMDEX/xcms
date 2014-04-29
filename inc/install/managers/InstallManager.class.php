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

require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/messages/ConsoleMessagesStrategy.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/messages/WebMessagesStrategy.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/helper/ServerConfig.class.php');

/**
 * Manager for install process
 */
class InstallManager {

	//CONSTANTS FOR INSTALL MODE
	const WEB_MODE = "web";
	const CONSOLE_MODE = "console";
	const STATUSFILE = "/install/_STATUSFILE";

	const INSTALL_CONF_FILE = "install.xml";	
	const INSTALL_PARAMS_TEMPLATE = "/install/templates/install-params.conf.php";
	const INSTALL_PARAMS_FILE = "conf/install-params.conf.php";
	const LAST_STATE = "INSTALLED";
	
	protected $mode = ""; //install mode.
	protected $installMessages = null;
	protected $installConfig=null;
	public $currentState;
	public $currentStep;

	/**
	 * Construct method
	 * @param string $mode Install mode: Web or console
	 */
	public function __construct($mode = self::CONSOLE_MODE){
		$this->mode = $mode;
		$messageClassName = $this->mode."MessagesStrategy";
		$this->installMessages = new $messageClassName();
		$installConfFile = XIMDEX_ROOT_PATH."/inc/install/conf/".self::INSTALL_CONF_FILE;
		
		$this->installConfig = new DomDocument();
		$this->installConfig->load($installConfFile);
		$this->currentState = $this->getCurrentState();		
	}

	/**
	 * Get Steps from config xml.
	 * @return array Associative array
	 */
	public function getSteps(){
		$xpath = new DomXPath($this->installConfig);
		$query = "/install/steps/step";
		$steps = $xpath->query($query);
		$result = array();
		foreach ($steps as $i => $step) {
			$auxStepArray=array(); 
			foreach($step->attributes as $attribute){
				$auxStepArray[$attribute->name] = $attribute->value;
			}
			$auxStepArray["description"] = $step->nodeValue;
			if ($auxStepArray["state"] == strtolower($this->currentState))
				$this->currentStep = $i;
			$result[] = $auxStepArray;
		}

		return $result;
	}

	/**
	 * Get status from _STATUSFILE
	 * @return string get Status
	 */
	public function getCurrentState(){
		$statusFile = XIMDEX_ROOT_PATH.self::STATUSFILE;
		if (!file_exists($statusFile))
			return false;
		return trim(strtolower(FsUtils::file_get_contents($statusFile)));
	}

	/**
	 * Check if Ximdex is already installed.
	 * @return boolean true if is already installed.
	 */
	public function isInstalled(){
		$currentState = $this->getCurrentState();		
		if (!$currentState)
			return false;

		return $currentState == strtolower(self::LAST_STATE);
	}

	/**
	 * Write the next step into _STATUSFILE file
	 */
	public function nextStep(){

		$newState = "";
		$steps = $this->getSteps();
		$nextStep = $this->currentStep + 1;
    	if (count($steps)>$nextStep){
    		$newState = $steps[$nextStep]["state"];

    	}else{
    		$newState =  self::LAST_STATE;
    	}
    	FsUtils::file_put_contents(XIMDEX_ROOT_PATH.self::STATUSFILE, strtoupper($newState));
	}

	/**
	 * Write the prev step into _STATUSFILE file
	 */
	public function prevStep(){

		$newState = "";
		$steps = $this->getSteps();
		$prevStep = $this->currentStep - 1;
		if ($prevStep<0)
			$prevStep = 0;    	
		$newState = $steps[$prevStep]["state"];
		FsUtils::file_put_contents(XIMDEX_ROOT_PATH.self::STATUSFILE, strtoupper($newState));
    	    	
	}

	public function getModulesByDefault($default=true){
		$query = "/install/modules/module";
		$query .= $default? "[@default='1']": "[not(@default) or @default='0']";
		return $this->getModulesByQuery($query);
	}

	public function getModulesByQuery($query){
		$result = array();
		$xpath = new DomXPath($this->installConfig);
		$modules = $xpath->query($query);

		foreach ($modules as $module) {
			$auxModuleArray=array(); 
			foreach($module->attributes as $attribute){
				$auxModuleArray[$attribute->name] = $attribute->value;
			}
			$auxModuleArray["description"] = $module->nodeValue;
			$result[] = $auxModuleArray;

		}		
		return $result;
	}

	public function getAllModules(){
		$query = "/install/modules/module";
		return $this->getModulesByQuery($query);
	}	

	public function getModuleByName($name, $exclude_alias = true){
		$extra_query = $exclude_alias? "": " or @alias='{$name}'";
		$query = "/install/modules/module[@name='{$name}' $extra_query]";
		return $this->getModulesByQuery($query);
	}

	
	public function initialChecking(){
		$result = array();
		$result[] = $this->checkDiskSpace();
		$result[] = $this->checkPHPVersion();
		$result[] = $this->checkRequiredPHPExtensions();
		$result[] = $this->checkRecommendedPHPExtensions();
		$result[] = $this->checkDisabledFunctions();
		$result[] = $this->checkFilePermissions();
		$result[] = $this->checkInstanceGroup();
 		$result[] = $this->checkMySQL();

		return $result;		
	}

	private function checkDiskSpace(){
		$result = array();
		$freeSpace = DiskUtils::disk_free_space("GB",XIMDEX_ROOT_PATH);
		$result["name"] = "DiskSpace";
		if ($freeSpace>1){
			$result["state"]="success";
		}else{
			$result["state"]="warning";
			$result["messages"][] = "Only {$freeSpace}GB available. Please free space from your disk";
			$result["help"][] = "";
		}

		return $result;
	}

	private function checkPHPVersion(){
		$version = phpversion();
		$minPHPVersion = "5.2.5";
		$version = explode(".", $version);
		$version = "{$version[0]}.{$version[0]}";
		$result["name"] = "PHP version";
		if ($version >= "5.2.5"){
			$result["state"]="success";
		}
		else {
			$result["state"]="warning";
			$result["messages"][] = "Recomended PHP $minPHPVersion or higher";
			$result["help"][] = "";
		}

		return $result;
	}

	private function checkRequiredPHPExtensions(){
		$modules = array_merge(apache_get_modules(),get_loaded_extensions());
		$requiredModules = array("gd","curl", "mysql", "xsl","xml");
		$result["state"] = "success";
		$result["name"] = "PHP required extensions";

		foreach ($requiredModules as $requiredModule) {
			if (!in_array($requiredModule, $modules)){
				$result["state"] = "error";
				$result["messages"][] = "PHP $requiredModule  extension is required";
				$result["help"][] = "";
			}
		}

		return $result;
	}

	private function checkRecommendedPHPExtensions(){
		$modules = array_merge(apache_get_modules(),get_loaded_extensions());
		$recommendedModules = array("enchant");
		$result["state"] = "success";
		$result["name"] = "PHP recommended extensions";

		foreach ($recommendedModules as $recommendedModule) {
			if (!in_array($recommendedModule, $modules)){
				$result["state"] = "warning";
				$result["message"][] = "PHP $recommendedModule extension is recommended.";
				$result["help"][] = "";
			}
		}
		return $result;
	}


	private function checkDisabledFunctions(){
		$ximdexServerConfig = new ServerConfig();
        //Checking pcntl_fork function is not disabled
        $result["state"]="success";
        $result["name"]="Disabled functions";
        if ($ximdexServerConfig->hasDisabledFunctions()){
        		$result["state"] = "warning";
	            $result["messages"][] = "Disabled pcntl_fork and pcntl_waitpid functions are recommended. Please, check php.ini file.";
	            $result["help"][] = "";
        }

        return $result;
	}

	private function checkMySQL(){
		
	}
		
	/**
	 * Checking install parameters	 
	 */
	public  function checkFilePermissions(){

		$result["state"] = "success";
		$result["name"] = "File permission";
		$filesToCheck = array(self::STATUSFILE,
								"/data",
								"/logs",
								"/conf");
		foreach ($filesToCheck as $file) {
			if (!file_exists(XIMDEX_ROOT_PATH.$file)){				
				$result["state"] = "error";
				$exception["messages"][] = "$file doesn't found.";				
			}else if (!$this->isWritable(XIMDEX_ROOT_PATH.$file)){
				$result["state"] = "error";
				$result["messages"][]="Write permissions on $file required.";
				$result["help"][] = "chmod -R 664 ".XIMDEX_ROOT_PATH.$file;
			}
		}

		return $result;
	}

	private function isWritable($file){
		return is_writable($file);
	}


	public function checkInstanceGroup(){
		
		$result["state"] = "success";
		$result["name"] = "File permission";
		
		$groupId = posix_getgroups();
		$groupName = posix_getgrgid($groupId[0]);
		$ximdexGroupId = filegroup(XIMDEX_ROOT_PATH);
		$ximdexGroupName = posix_getgrgid($ximdexGroupId);
		if (!in_array($ximdexGroupId, $groupId)){
			$result["state"] = "error";
			$result["messages"][] = "Advice you use {$groupName["name"]} group instead of {$ximdexGroupName["name"]}" ;
			$result["help"][] = "chgrp -R {$groupName["name"]} ".XIMDEX_ROOT_PATH;			
			
		}

		return $result;
	}


	public function setInstallParams($host, $port, $bdName, $user, $pass){

		$content = FsUtils::file_get_contents(XIMDEX_ROOT_PATH.self::INSTALL_PARAMS_TEMPLATE);
		$content = str_replace("##DB_HOST##", $host, $content);
		$content = str_replace("##DB_PORT##", $port, $content);
		$content = str_replace("##DB_USER##", $user, $content);
		$content = str_replace("##DB_PASSWD##", $pass, $content);
		$content = str_replace("##DB_NAME##", $bdName, $content);

		$content = str_replace("##XIMDEX_TIMEZONE##", date_timezone_get(), $content);
		$content = str_replace("##XIMDEX_PATH##", XIMDEX_ROOT_PATH, $content);

		if (file_exists(XIMDEX_ROOT_PATH.self::INSTALL_PARAMS_FILE)){
			rename(XIMDEX_ROOT_PATH.self::INSTALL_PARAMS_FILE, XIMDEX_ROOT_PATH.self::INSTALL_PARAMS_FILE."bck_".date(Ymd_his));
		}

		FsUtils::file_put_contents(XIMDEX_ROOT_PATH.self::INSTALL_PARAMS_FILE, $content);
	}
		
	public function setConfigValues(){

	}
	
}
?>