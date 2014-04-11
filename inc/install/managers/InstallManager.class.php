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

class InstallManager {

	//CONSTANTS FOR INSTALL MODE
	const WEB_MODE = "web";
	const CONSOLE_MODE = "console";
	const STATUSFILE = "/install/_STATUSFILE";

	const INSTALL_CONF_FILE = "install.xml";	
	const LAST_STATE = "INSTALLED";
	
	protected $mode = ""; //install mode.
	protected $installMessages = null;
	protected $installConfig=null;
	public $currentState;
	public $currentStep;

	public function __construct($mode = self::CONSOLE_MODE){
		$this->mode = $mode;
		$messageClassName = $this->mode."MessagesStrategy";
		$this->installMessages = new $messageClassName();
		$installConfFile = XIMDEX_ROOT_PATH."/inc/install/conf/".self::INSTALL_CONF_FILE;
		
		$this->installConfig = new DomDocument();
		$this->installConfig->load($installConfFile);
		$this->currentState = $this->getCurrentState();		
	}

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

	public function getCurrentState(){
		$statusFile = XIMDEX_ROOT_PATH.self::STATUSFILE;
		if (!file_exists($statusFile))
			return false;
		return trim(strtolower(FsUtils::file_get_contents($statusFile)));
	}

	public function isInstalled(){
		$currentState = $this->getCurrentState();		
		if (!$currentState)
			return false;

		return $currentState == strtolower(self::LAST_STATE);
	}

	public function nextStep(){

		$newState = "";
		$steps = $this->getSteps();
    	if (count($steps)>$this->currentStep){
    		$newState = $this->steps[$this->currentStep]["state"];

    	}else{
    		$newState =  InstallController::LAST_STATE;
    	}
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
	
}
?>