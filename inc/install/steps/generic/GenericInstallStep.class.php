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
class GenericInstallStep {

	protected $steps;
	protected $renderer;
	protected $request;
	protected $response;
	protected $currentStep;
	protected $installManager;
	protected $js_files;
	protected $exception;
	const STATUSFILE = "/install/_STATUSFILE";

	public function __construct(){
		$this->js_files = array();
		$this->installManager = new installManager();
		$this->steps = $this->installManager->getSteps();
		$this->checkInstall();
	}

	public function index(){
		header(sprintf("Location: %s", "xmd/uninstalled/index.html"));
		die();
	}

	protected function render($values=array(), $view=null, $layout="installer.tpl"){
		
		header('Content-type', "text/html; charset=utf-8");
		foreach ($values as $name => $value) {
			$$name = $value;			
		}		
		$js_files = $this->js_files;
		$view = $view? $view : $this->request->getParam("method");
		$goMethod = isset($values["go_method"])? $values["go_method"]: $view;
		
		$folderName = trim(strtolower($this->steps[$this->currentStep]["class-name"]));
		if ($this->exception && is_array($this->exception) && count($this->exception)){
			$exception = $this->exception;
			$includeTemplateStep = XIMDEX_ROOT_PATH."/inc/install/steps/generic/view/exception.inc";
		}else
			$includeTemplateStep = XIMDEX_ROOT_PATH."/inc/install/steps/{$folderName}/view/{$view}.inc";
		include(XIMDEX_ROOT_PATH."/inc/install/view/install.inc");
		die();
	}

	/**
	 * Checking install parameters	 
	 */
	private  function checkInstall(){
		$filesToCheck = array(self::STATUSFILE,
								"/data",
								"/logs");
		
		foreach ($filesToCheck as $file) {
			if (!file_exists(XIMDEX_ROOT_PATH.$file)){
				$this->exception[] = "$file doesn't found.";
			}else if (!is_writable(XIMDEX_ROOT_PATH.$file)){
				$this->exception[] = "Ximdex need permissions to write on $file";
			}
		}
	}


	/**
	 * Sends a JSON string
	 * @param $_msgs
	 * @return unknown_type
	 */

	protected function sendJSON($data) {
		header(sprintf('Content-type: application/json; charset=utf-8'));
		$data = json_encode($data);
		echo $data;
		die();
    }

    
    public function setCurrentStep($currentStep){

    	$this->currentStep = $currentStep;
    }

    public function setRequest($request){
    	$this->request = $request;
    }

    public function setResponse($response){
    	$this->response = $response;
    }

   protected function addJs($jsPath){
   		$folderName = trim(strtolower($this->steps[$this->currentStep]["class-name"]));
    	$this->js_files[] = "inc/install/steps/{$folderName}/js/{$jsPath}";
    }

    public function loadNextAction(){

    	$this->installManager->nextStep();

    }


}
?>