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
	protected $currentState;
	protected $currentStep;
	const STATUSFILE = "/install/_STATUSFILE";

	public function __construct(){

		/** Obtaining the render to use */
		$rendererClass = $this->_get_render();

		$factory = new Factory(RENDERER_ROOT_PATH, '');
		$this->renderer = $factory->instantiate($rendererClass.'Renderer');

		$this->renderer->set("_BASE_TEMPLATE_PATH", sprintf('%s/xmd/template/%s/', XIMDEX_ROOT_PATH, $rendererClass)  );
	}

	public function index(){
		header(sprintf("Location: %s", "xmd/uninstalled/index.html"));
		die();
	}

	protected function render($values=array(), $view=null, $layout="installer.tpl"){
		
		header('Content-type', "text/html; charset=utf-8");		
		
		$view = $view? $view : $this->request->getParam("method");
		$goMethod = isset($values["go_method"])? $values["go_method"]: $view;
		$includeTemplateStep = XIMDEX_ROOT_PATH."/inc/install/steps/{$this->currentState}/view/{$view}.inc";
		include(XIMDEX_ROOT_PATH."/inc/install/view/install.inc");
		die();
	}

	/**
	 *
	 * @param $rendererClass
	 * @return unknown_type
	 */
	private function _get_render() {

		return "Smarty";
	}

	/**
	 * Sends a JSON string
	 * @param $_msgs
	 * @return unknown_type
	 */

	protected function sendJSON($data) {
		if (!$this->endActionLogged)
			$this->logSuccessAction();
    	header(sprintf('Content-type: application/json; charset=', $this->displayEncoding));
		$data = Serializer::encode(SZR_JSON, $data);
		echo $data;
		die();
    }

    public function setSteps($steps){

    	$this->steps = $steps;
    }

    public function setCurrentState($currentState){

    	$this->currentState = $currentState;
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


}

?>