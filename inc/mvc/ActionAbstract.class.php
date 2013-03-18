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




if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__). "/../../"));
}
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/parsers/ParsingJsGetText.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/action.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/IController.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/serializer/Serializer.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/ActionsStats.class.php');


/**
 *
 * @brief Base abstract class for Actions
 *
 *  Base abstract class for Actions who provides basic funcionality like rendering
 *  css/js inclusion and redirection
 *
 */

class ActionAbstract extends IController {

	/**
	 * Keeps the js to use
	 * @var unknown_type
	 */
	private $_js = array();

	/**
	 * keeps the css to use
	 * @var unknown_type
	 */
	private $_css = array();

	/**
	 * Action name
	 */
	private $actionName = '';

	/**
	 * Action description
	 */
	private $actionDescription = '';

	/**
	 * Action renderer
	 * @var unknown_type
	 */
	public $renderer;

	/**
	 * Action command
	 * @var String
	 */
	public $actionCommand;

	/**
	 * Construct
	 * @param $_render
	 * @return unknown_type
	 */
	function __construct($_render = null) {

		parent::__construct();

		$this->displayEncoding = Config::getValue('displayEncoding');

		/** Obtaining the render to use */
		$rendererClass = $this->_get_render($_render);

		$factory = new Factory(RENDERER_ROOT_PATH, '');
		$this->renderer = $factory->instantiate($rendererClass.'Renderer');

		$this->renderer->set("_BASE_TEMPLATE_PATH", sprintf('%s/xmd/template/%s/', XIMDEX_ROOT_PATH, $rendererClass)  );

	}

	private function getActionInfo($actionName, $module, $actionId, $nodeId) {

		$node = new Node();
		$nodeTypeId = $node->find('IdNodeType', 'IdNode = %s', array($nodeId), MONO);
		$nodeTypeId = $nodeTypeId[0];

		$action = new Action();
		$data = $action->find(
			'Command, Name, Description',
			'IdNodeType = %s and Command = %s and Module is %s',
			array($nodeTypeId, $actionName, $module)
		);

		$data = $data[0];
		//debug::log($data,$actionName, $module, $actionId, $nodeId);
		return $data;
	}

	/**
	 * Execute the action
	 * @param $request
	 * @return unknown_type
	 */
	function execute($request) {
		// Setting path or subset which current action belongs to
		$nodeid = $request->getParam("nodeid");
		$action = $request->getParam("action");
		$actionid = $request->getParam("actionid");

		if ($nodeid && $actionid) {
			$action = new Action($actionid);
			//XMD_Log::debug("MVC::ActionAbstract calling action $actionid (" . $action->get('Command') . ") in node $nodeid ");
		}

		$method = ($var = $request->getParam("method")) ? $var : 'index';
		$this->request = $request;

		$actionInfo = $this->getActionInfo(
			$request->getParam('action'),
			$request->getParam('module'),
			$request->getParam('actionid'),
			$request->getParam('nodeid')
		);

		$this->actionCommand = $actionInfo['Command'];
		$this->actionName = $actionInfo['Name'];
		$this->actionDescription = $actionInfo['Description'];

		if(method_exists($this, $method)) {
			$this->$method();
		} else {
			XMD_Log::debug("MVC::ActionAbstract Metodo {$method} not found" );
		}

	}

	/**
	 * Renders the action
	 *
	 * @param $arrValores
	 * @param $view
	 * @param $layout
	 */
	function render($arrValores = NULL, $view = NULL, $layout = NULL, $return = FALSE) {

		if (is_null($this->renderer)) {
			$this->_setError("Renderizador no definido", "Actions");
			return;
		}

		//Send the encoding to the browser
		$this->response->set('Content-type', "text/html; charset=$this->displayEncoding");

		// Render default values
		if($view != NULL ) $this->request->setParam("method", $view);

		// Visualize action headers ( Action name + description + node_path )
		$this->request->setParam("view_head", 1);

		// Saving in the request the css and js(passed by gettext before)
		$this->request->setParam("locale", XSession::get('locale'));

		$getTextJs = new ParsingJsGetText();

		$this->request->setParam("js_files",  $getTextJs->getTextArrayOfJs($this->_js));
		$this->request->setParam("css_files", $this->_css);

		// Usefull values
		$arrValores['_XIMDEX_ROOT_PATH'] = XIMDEX_ROOT_PATH;
		$arrValores['_ACTION_COMMAND'] = $this->actionCommand;
		$arrValores['_ACTION_NAME'] = $this->actionName;
		$arrValores['_ACTION_DESCRIPTION'] = $this->actionDescription;

		$query = App::get('QueryManager');
		$arrValores['_MESSAGES_PATH'] = $query->getPage() . $query->buildWith();


		// Passing specified values
		$this->request->setParameters($arrValores);
		$this->renderer->setParameters($this->request->getRequests());


		// If layout was not specified
		if (empty($layout) || $layout == "messages.tpl") {

			if ($this->request->getParam("ajax") == "json" ) {

				//If there are some errors and op=json, errors are returned in json format
				if (isset($arrValores["messages"]) /*&& isset($arrValores["messages"][0])*/) {
					$this->sendJSON($arrValores["messages"]);
				} else {
					$this->sendJSON($arrValores);
				}

			} else if (isset($arrValores["messages"]) /*&& isset($arrValores["messages"][0])*/) {

				//If there are some arrores and op is not found, the errors are shown in a message.
				$layout = 'messages.tpl';
				if ($this->request->getParam("nodeid") > 0) {

					$this->reloadNode($this->request->getParam("nodeid"));
					$this->request->setParam("js_files",  $getTextJs->getTextArrayOfJs($this->_js));
				}

			} else {

				// If there is no errors, $view is visualized
				$layout = 'default.tpl';
			}
		}

		$this->renderer->setTemplate(XIMDEX_ROOT_PATH . '/xmd/template/Smarty/layouts/' . $layout);
//		$this->request->setParam("outHTML", $this->renderer->render($view));
		$output = $this->renderer->render($view);

		// Apply widgets renderer after smarty renderer
		$output = $this->_renderWidgets($output);

		if ($return === true) {
			return $output;
		}

		$this->request->setParam('outHTML', $output);
		$this->request->setParameters($this->renderer->getParameters());
		$this->response->sendHeaders();

		if ($this->request->getParam("out") == "WEB" ) {
			echo $this->request->getParam("outHTML");
		}
	}

	/**
	 * Renders the widgets of an action
	 *
	 * @param $output
	 */
	function _renderWidgets($output) {

		// DEBUG: Apply widgets renderer after smarty renderer
		$factory = new Factory(RENDERER_ROOT_PATH, '');
		$wr = $factory->instantiate('WidgetsRenderer');
		$params = $this->renderer->getParameters();

		// Important!, clean up assets
		$params['css_files'] = array();
		$params['js_files'] = array();

		$wr->setParameters($params);
		$output = $wr->render($output);
		// DEBUG: Apply widgets renderer after smarty renderer

		return $output;
	}

	/**
	 * Redirects the action to another
	 *
	 * @param $method
	 * @param $actionName
	 * @param $parameters
	 * @return unknown_type
	 */
	function redirectTo($method = NULL, $actionName = NULL, $parameters = NULL) {
		if (empty($method)) {
			$method = 'index';
		}

		$_GET["redirect_other_action"] = 1;
		if (!empty($actionName)) {
			$action = new Action();
			$idNode = $this->request->getParam("nodeid");

			$node = new Node($idNode);
			$idAction = $action->setByCommand($actionName, $node->get('IdNodeType'));

			// IMPORTANT: If idAction is empty, that node has no permissions on the action.
			// Display the error and exit or an evil redirection loop will crash your server!
			if (intval($idAction) < 1) {
				$this->messages->add(sprintf(_('The action %s cannot be executed on the selected node'), $actionName), MSG_TYPE_ERROR);
				$values = array('messages' => $this->messages->messages);
				$this->render($values);
				die();
			}


			$_GET["actionid"]  = $idAction;
			$_REQUEST["actionid"]  = $idAction;
		}


		$_GET["method"] = $method;
		$frontController = new FrontController();
		if (!empty($parameters)) {
			$frontController->request->setParameters($parameters);
		}
		$frontController->dispatch();

		die();
	}

	/**
	 * Recargamos el arbol sobre el nodo especificado
	 * @param $idnode
	 * @return unknown_type
	 */
	function reloadNode($idnode) {

		// TODO search and destroy the %20 generated in the last char of the query string
		$queryManager = new QueryManager(false);
		$file = sprintf('%s%s',
			'/xmd/loadaction.php',
			$queryManager->buildWith(array(
					'xparams[reload_node_id]' => $idnode,
					'js_file' => 'reloadNode',
					'method' => 'includeDinamicJs',
					'void' => 'SpacesInIE7HerePlease'
			))
		);

		$this->addJs(urldecode($file));
	}


	/**
	 *
	 * @param $_js
	 * @param $params
	 * @return unknown_type
	 */
	public function addJs($_js, $_module = 'XIMDEX', $params=null) {

		if('XIMDEX' != $_module) {
			$path = ModulesManager::path($_module);
			$_js = $path.$_js;
		}

		if ($params === null) {

			return $this->_js[] = $_js;
		} else {

			// if "params" attribute is set, javascript will be parsed
			return $this->_js[] = array(
				'file' => $_js,
				'params' => $params
			);
		}
	}

	/**
	 *
	 * @param $_css
	 * @return unknown_type
	 */
	public function addCss($_css, $_module = 'XIMDEX') {

		if('XIMDEX' != $_module) {
			$path = ModulesManager::path($_module);
			$_css = $path.$_css;
		}

		$this->_css[] = Config::getValue('UrlRoot').$_css;
	}

	/**
	 *
	 * @param $rendererClass
	 * @return unknown_type
	 */
	private function _get_render($rendererClass = null) {

		// 		$this->request->setParam("renderer", "Debug");
		// 		$this->request->setParam("renderer", "Json");
		// 		$this->request->setParam("renderer", "Smarty");

		if($rendererClass == null) {
			if(XSession::get('debug_render')> 0 ) {
				switch(XSession::get('debug_render')) {
					case 1: $rendererClass = "Smarty"; break;
					case 2: $rendererClass = "Json"; break;
					case 3: $rendererClass = "Debug"; break;
					default:
						$rendererClass = $this->request->getParam("renderer");
				}
			}else {
				$rendererClass = $this->request->getParam("renderer");
			}
		}

		//Si no hay definido ning�n render
		if(!$rendererClass) {
			$rendererClass = "Smarty";
		}

		//Guardamos el render
		$this->request->setParam("renderer", $rendererClass);
		return $rendererClass;
	}

	/**
	 * Sends a JSON string
	 * @param $_msgs
	 * @return unknown_type
	 */

	public function sendJSON($data) {
    	header(sprintf('Content-type: application/json; charset=', $this->displayEncoding));
		$data = Serializer::encode(SZR_JSON, $data);
		echo $data;
		die();
    }

	/**
	 * Remplace files con [LANG] to i18n file
	 * Example:
	 *  /var/www/ximdex/xmd/images/[LANG]/pingu.gif -> /var/www/ximdex/xmd/images/es/pingu.gif
	 *  or
	 *  /var/www/ximdex/xmd/images/ximNEWS/pingu_[LANG].gif -> /var/www/ximdex/xmd/images/ximNEWS/pingu_es.gif
	 *  or ...
	 *  This can be also done in html with the smarty var locale
	 * @param $file
	 * @param $_lang
	 * @param $_default
	 * @return unknown_type
	 */
	function i18n_file($file, $_lang = null, $_default = null) {
		$_file = null;

		//Checking if the file is existing for the passed language
		if($_lang != null ) {
			$_file = str_replace("[LANG]", $_lang, $file);
			if(file_exists($_file) )
			return $_file;
		}

		//if the associated file for this language is not existing, checking with system language
		$_lang = XSession::get('locale');
		if($_lang != null ) {
			$_file = str_replace("[LANG]", $_lang, $file);
			if(file_exists($_file) )
			return $_file;
		}

		$_lang = DEFAULT_LOCALE;
		if($_lang != null ) {
			$_file = str_replace("[LANG]", $_lang, $file);
			if(file_exists($_file) )
			return $_file;
		}

		return $_default;
	}

	protected function renderMessages() {
		$this->render(array('messages' => $this->messages->messages));
		die();
	}

	/**
	 * Decides if a tour is be able to be launched automatically given an user
	 * @param $userId
	 * @return boolean
	 */

	public function tourEnabled($userId, $action=null) {
    	if(!ModulesManager::isEnabled('ximTOUR'))
    		return false;
	$actionsStats = new ActionsStats();
	if (!$action)
	    $action = $this->actionCommand;
	$result = $actionsStats->getCountByUserAndAction($userId, $action);

		return ($result === null || $result < 10) ? true : false;
    }
}
?>
