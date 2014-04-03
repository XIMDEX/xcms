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
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Request.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Response.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/mvc.php');
require_once(XIMDEX_ROOT_PATH . '/inc/install/InstallStepFactory.class.php');

class InstallController extends IController {


	const INSTALL_CONF = "install.xml";	
	const LAST_STATE = "INSTALL";
	private $steps = array();
	private $currentStep = null;
	
	public function __construct(){
		parent::__construct();
		$this->buildArrayFromInstallConf();
		$this->currentStep = self::getCurrentStep();		
	}

	public function dispatch(){

		$this->setToRequest();		
		$installStep = $this->compose();
		$method = (null !== $this->request->getParam('method'))? $this->request->getParam('method') : "index";
		$this->request->setParam("method",$method);
		$installStep->setRequest($this->request);
		$installStep->setResponse($this->response);

			if (!$installStep){
			
		}else{
			if (method_exists($installStep, $method)){
				$installStep->$method();
			}
		}
	}

	public function compose(){

		$installArrayFromXml = $this->buildArrayFromInstallConf();		
		return InstallStepFactory::getStep($this->steps, $this->currentStep);		
	}

	/**
	*
	*/
	private function buildArrayFromInstallConf(){
		$installConfFile = XIMDEX_ROOT_PATH."/inc/install/conf/".self::INSTALL_CONF;
		if (!file_exists($installConfFile))
			return false;

		$domDocument = new DomDocument();
		$domDocument->load($installConfFile);
		$xpath = new DomXPath($domDocument);
		$query = "/install/steps/step";
		$steps = $xpath->query($query);
		$this->steps = array();
		foreach ($steps as $step) {
			$auxStepArray=array(); 
			foreach($step->attributes as $attribute){
				$auxStepArray[$attribute->name] = $attribute->value;
			}
			$auxStepArray["description"] = $step->nodeValue;
			$this->steps[] = $auxStepArray;
		}
	}

	private static function getCurrentStep(){
		$statusFile = XIMDEX_ROOT_PATH."/install/_STATUSFILE";
		if (!file_exists($statusFile))
			return false;
		return trim(strtolower(FsUtils::file_get_contents($statusFile)));
	}

	/**
	* 
	*/
	public static function isInstalled(){

		$currentStep = self::getCurrentStep();		
		if (!$currentStep)
			return false;
		return $currentStep == "INSTALLED";
	}

	private function setToRequest() {
		$this->request->setParameters($_FILES);
		$this->request->setParameters($_GET);
		$this->request->setParameters($_POST);
	}
}

?>