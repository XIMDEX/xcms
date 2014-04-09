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
	const STATUSFILE = "/install/_STATUSFILE";

	public function __construct(){
		$this->js_files = array();
		$this->installManager = new installManager();
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
		$includeTemplateStep = XIMDEX_ROOT_PATH."/inc/install/steps/{$this->currentState}/view/{$view}.inc";
		include(XIMDEX_ROOT_PATH."/inc/install/view/install.inc");
		die();
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

    
    public function setCurrentState($currentState){

    	$this->currentState = $currentState;
    }

    public function setRequest($request){
    	$this->request = $request;
    }

    public function setResponse($response){
    	$this->response = $response;
    }

   protected function addJs($jsPath){
    	$this->js_files[] = "inc/install/steps/{$this->currentState}/js/{$jsPath}";
    }

    public function loadNextAction(){

    	$this->currentStep++;    	
    	$newState = "";
    	if (count($this->steps)>$this->currentStep){
    		$newState = $this->steps[$this->currentStep]["state"];

    	}else{
    	    		$newState =  InstallController::LAST_STATE;
    	}
    	FsUtils::file_put_contents(XIMDEX_ROOT_PATH.self::STATUSFILE, strtoupper($newState));

    }


}
?>