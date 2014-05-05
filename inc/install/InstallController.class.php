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
require_once(XIMDEX_ROOT_PATH . '/inc/install/managers/InstallManager.class.php');

/**
 * Controller for install steps.
 * It's called only when the install process is not finished.
 */
class InstallController extends IController {


	/*Properties*/
	private $steps = array(); //Defined steps in installl.xml
	private $currentState = null; //Name of the current state.
	private $installManager = null; //Install manager object
	
	/*Methods*/

	/**
	 * Constructor. Init the class properties. 
	 */
	public function __construct(){
		$this->installManager = new InstallManager(InstallManager::WEB_MODE);
		$this->request = new Request();
		$this->response = new Response();
		$this->steps = $this->installManager->getSteps();
		$this->currentState = $this->installManager->getCurrentState();		
	}

	/**
	 * Run the selected method for the current step.	 
	 */
	public function dispatch(){

		//Set request with $_FILES, $_POST and $_GET arrays
		$this->setToRequest();
		
		//Instancing the step object, 
		//setting step properties 
		//and get the method to run
		$installStep = $this->compose();
		$method = (null !== $this->request->getParam('method'))? $this->request->getParam('method') : "index";
		$this->request->setParam("method",$method);
		$installStep->setRequest($this->request);
		$installStep->setResponse($this->response);


		if ($installStep){
			$check = $installStep->check();
			if (!$check){
				$this->installManager->prevStep();
				$this->dispatch();
				return;
			}

			if (method_exists($installStep, $method)){
				$installStep->$method();			
			}
		} 
	}

	/**
	 * Instance an object for the current step
	 * @return object Step Object
	 */
	public function compose(){

		return InstallStepFactory::getStep($this->steps, $this->currentState);		
	}

	/**
	 * Indicate if Ximdex is installed.
	 * @return boolean True if installed, false otherwise
	 */
	public static function isInstalled(){

		$installManager = new InstallManager(InstallManager::WEB_MODE);
		return $installManager->isInstalled();		
	}

	private function setToRequest() {
		$this->request->setParameters($_FILES);
		$this->request->setParameters($_GET);
		$this->request->setParameters($_POST);
	}
}

?>