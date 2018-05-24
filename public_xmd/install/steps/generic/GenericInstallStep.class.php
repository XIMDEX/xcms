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
use Ximdex\Runtime\App;

require_once(APP_ROOT_PATH.'/install/steps/welcome/WelcomeInstallStep.class.php');

class GenericInstallStep
{
	protected $steps;
	protected $renderer;
	protected $request;
	protected $response;
	protected $currentStep;
	protected $installManager;
	protected $js_files;
	protected $exceptions;
	const STATUSFILE = "/conf/_STATUSFILE";

	public function __construct()
	{
		$this->js_files = array();
		$this->installManager = new installManager();
		$this->steps = $this->installManager->getSteps();		
		$this->checkPermissions();
	}

	public function index()
	{    
	    $this->initialize_values(false);
		$this->installManager->prevStep();
		$this->currentStep = 0;
        $this->addJs("WelcomeController.js");
		$this->render();		
	}

	protected function render($values=array(), $view=null, $layout="installer.tpl")
	{	
		// header('Content-type', "text/html; charset=utf-8");
		foreach ($values as $name => $value) {
			$$name = $value;			
		}		
		$js_files = $this->js_files;
		$view = $view? $view : $this->request->getParam("method");
		$goMethod = isset($values["go_method"])? $values["go_method"]: $view;
		
		$folderName = trim(strtolower($this->steps[$this->currentStep]["class-name"]));
		if ($this->exceptions && is_array($this->exceptions) && count($this->exceptions)){
			$exceptions = $this->exceptions;
			$includeTemplateStep = APP_ROOT_PATH."/install/steps/generic/view/exception.php";
		}else
			$includeTemplateStep = APP_ROOT_PATH."/install/steps/{$folderName}/view/{$view}.php";
		  	include(APP_ROOT_PATH."/install/view/install.php");
		ob_end_flush();
		exit;
	}

	/**
	 * Sends a JSON string
	 * @param $_msgs
	 * @return string
	 */
	protected function sendJSON($data)
	{
		header(sprintf('Content-type: application/json; charset=utf-8'));
		$data = json_encode($data);
		echo $data;
		die();
    }
    
    public function setCurrentStep($currentStep)
    {
    	$this->currentStep = $currentStep;
    }

    public function setRequest($request)
    {
    	$this->request = $request;
    }

    public function setResponse($response)
    {
    	$this->response = $response;
    }

    protected function addJs($jsPath)
    {
   		$folderName = trim(strtolower($this->steps[$this->currentStep]["class-name"]));
    	$this->js_files[] = "install/steps/{$folderName}/js/{$jsPath}";
    }

    public function loadNextAction()
    {
    	$this->installManager->nextStep();
    }

    public function check()
    {
    	return true;
    }

    protected function checkPermissions()
    {    
        $checkGroup = $this->installManager->checkInstanceGroup();
        if ($checkGroup["state"] != "success") {
            $this->exceptions[] = $checkGroup;
        }
    	$checkPermissions = $this->installManager->checkFilePermissions();
    	if ($checkPermissions["state"]!= "success") {
    		$this->exceptions[] = $checkPermissions;
    	}
   }

   /**
    * Initialize the default application values
    * @param bool $persist
    */
   protected function initialize_values(bool $persist = true)
   {
       // Relative URL ( do not save it if its value is only / )
       $pathInfo = pathinfo($_SERVER['SCRIPT_NAME'] ?? '/');
       $basepath = rtrim( $pathInfo['dirname'], '/');

       $subpath = '';
       if (defined('CORE_FRONTCONTROLLER')) {
           $subpath = '/'.basename(APP_ROOT_PATH);
       } else {
           
            // No access to /public_xmd directly without own domain
            if ($basepath == '/'.basename(APP_ROOT_PATH)) {
                header('Location: /');
                die();
           }
       }
       App::setValue('UrlRoot', ($basepath)?: '', $persist);
       App::setValue('UrlFrontController', $subpath, $persist);

       // Host and protocol
       App::setValue('UrlHost', $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['HTTP_HOST'], $persist);
   }
}