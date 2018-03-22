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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\MVC;

use Ximdex\Logger;
use Ximdex\Parsers\ParsingJsGetText;
use Ximdex\Utils\Serializer;
use Ximdex\Models\User;
use Ximdex\Models\Action;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Request;
use Ximdex\Utils\Factory;
use Ximdex\Utils\Mail;
use Ximdex\Utils\QueryManager;
use Ximdex\Runtime\Session;

/**
 *
 * @brief Base abstract class for Actions
 *
 *  Base abstract class for Actions who provides basic funcionality like rendering
 *  css/js inclusion and redirection
 *
 */
abstract class ActionAbstract extends IController
{
    /**
     * Keeps the js to use
     */
    public $displayEncoding;
    public $actionMethod;
    public $actionModule;
    
    /**
     * @var array
     */
    private $_js = array();

    /**
     * keeps the css to use
     * @var array
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
    
    private $actionId = null;

    /**
     * Action renderer
     */
    /**
     * @var mixed
     */
    public $renderer;

    /**
     * Action command
     * @var String
     */
    public $actionCommand;
    /**
     * @var bool
     */
    protected $endActionLogged = false;

    /**
     * ActionAbstract constructor.
     * 
     * @param null $_render
     */
    public function __construct($_render = null)
    {
        parent::__construct();
        $this->displayEncoding = App::getValue('displayEncoding');

        /** Obtaining the render to use */
        $rendererClass = $this->_get_render($_render);
        $factory = new Factory(RENDERER_ROOT_PATH, '');
        $this->renderer = $factory->instantiate($rendererClass . 'Renderer');
        $this->renderer->set("_BASE_TEMPLATE_PATH", sprintf('%s/xmd/template/%s/', XIMDEX_ROOT_PATH, $rendererClass));
    }

    /**
     * @param $actionName
     * @param $module
     * @param $actionId
     * @param $nodeId
     * @return array
     */
    private function getActionInfo($actionName, $module, $actionId, $nodeId)
    {
        unset($actionId);
        $nodeTypeId = "";
        if (!is_null($nodeId)) {
            $node = new Node();
            $nodeTypeId = $node->find('IdNodeType', 'IdNode = %s', array($nodeId), MONO);
            $nodeTypeId = serialize($nodeTypeId);
        }
        $action = new Action();
        $data = $action->find(
            'Command, Name, Description, Module, IdAction',
        	'IdNodeType = %s and Command = %s and Module ' . (($module === null) ? 'is null' : '= %s'),
            array($nodeTypeId, $actionName, $module)
        );
        if (!empty($data)) {
            $data = $data[0];
        }
        return $data;
    }

    /**
     * Execute the action
     * 
     * @param $request Request
     */
    function execute($request)
    {
        // Setting path or subset which current action belongs to
        $nodeid = $request->getParam("nodeid");
        $actionid = $request->getParam("actionid");
        $method = ($var = $request->getParam("method")) ? $var : 'index';
        $this->request = $request;
        $actionInfo = $this->getActionInfo(
            $request->getParam('action'),
            $request->getParam('module'),
            $request->getParam('actionid'),
            $request->getParam('nodeid')
        );
        if (!empty($actionInfo)) {
            $this->actionId = $actionInfo['IdAction'];
            $this->actionCommand = $actionInfo['Command'];
            $this->actionName = $actionInfo['Name'];
            $this->actionDescription = $actionInfo['Description'];
            $this->actionModule = isset($actionInfo['Module']) ? $actionInfo['Module'] : null;
            $this->actionCommand = $actionInfo['Command'];
        }
        if (method_exists($this, $method)) {
            $this->actionMethod = $method;
            $this->logInitAction();
            $res = $this->$method();
            //TODO $this->logEndAction(true, 'Ended with: ' . $res);
        } else {
            Logger::debug("MVC::ActionAbstract Method {$method} not found");
        }
    }

    private function getDefaultLogMessage()
    {
        $user = Session::get("userID") ? 'by ' . Session::get('user_name') . ' (' . Session::get('userID') . ')' : '';
        $moduleString = '';
        if (isset($this->actionModule)) {
            $moduleString = $this->actionModule ? "in module {$this->actionModule}." : "";
        }
        $actionId = '';
        if ($this->actionId) {
            $actionId = ' (' . $this->actionId . ')';
        }
        return $moduleString . get_class($this) . "->{$this->actionMethod}$actionId {$user}";
    }

    private function logInitAction()
    {
        $this->endActionLogged = false;
        $defaultLog = Logger::get_active_instance();
        Logger::setActiveLog('actions');
        Logger::info('INIT ' . $this->getDefaultLogMessage());
        Logger::debug("Request: " . print_r($this->request, true));
        Logger::setActiveLog($defaultLog);
    }

    protected function logEndAction($success = true, $message = null)
    {
        $message = $message ? ". $message" : "";
        $defaultLog = Logger::get_active_instance();
        Logger::setActiveLog('actions');
        if ($success) {
            Logger::info("FINISH OK " . $this->getDefaultLogMessage() . " $message");
        }
        else {
            Logger::error("FINISH FAIL " . $this->getDefaultLogMessage() . " $message");
        }
        Logger::setActiveLog($defaultLog);
        $this->endActionLogged = true;
    }

    protected function logSuccessAction($message = null)
    {
        $this->logEndAction(true, $message);
    }

    protected function logUnsuccessAction($message = null)
    {
        $this->logEndAction(false, $message);
    }

    /**
     * Renders the action
     * 
     * @param null $arrValores
     * @param null $view
     * @param null $layout
     * @param bool $return
     * @return null
     */
    function render($arrValores = NULL, $view = NULL, $layout = NULL, $return = FALSE)
    {
        if (!$this->endActionLogged)
            $this->logSuccessAction();
        if (is_null($this->renderer)) {
            $this->_setError("Renderizador no definido", "Actions");
            return null;
        }

        //Send the encoding to the browser
        $this->response->set('Content-type', "text/html; charset=$this->displayEncoding");

        // Render default values
        if ($view != NULL) $this->request->setParam("method", $view);

        // Visualize action headers ( Action name + description + node_path )
        $this->request->setParam("view_head", 1);

        // Saving in the request the css and js(passed by gettext before)
        $this->request->setParam("locale", Session::get('locale'));
        $getTextJs = new ParsingJsGetText();
        $this->request->setParam("js_files", $getTextJs->getTextArrayOfJs($this->_js));
        $this->request->setParam("css_files", $this->_css);

        // Usefull values
        $arrValores['_XIMDEX_ROOT_PATH'] = XIMDEX_ROOT_PATH;
        $arrValores['_ACTION_COMMAND'] = $this->actionCommand;
        $arrValores['_ACTION_NAME'] = $this->actionName;
        $arrValores['_ACTION_DESCRIPTION'] = $this->actionDescription;
        $query = App::get('\Ximdex\Utils\QueryManager');
        $arrValores['_MESSAGES_PATH'] = $query->getPage() . $query->buildWith();

        // Passing specified values
        $this->request->setParameters($arrValores);
        $this->renderer->setParameters($this->request->getRequests());

        // If layout was not specified
        if (empty($layout) || $layout == "messages.tpl") {
            if ($this->request->getParam("ajax") == "json") {

                // If there are some errors and op=json, errors are returned in json format
                if (isset($arrValores["messages"])) {
                    $this->sendJSON($arrValores["messages"]);
                } else {
                    $this->sendJSON($arrValores);
                }
            } else if (isset($arrValores["messages"])) {

                // If there are some arrores and op is not found, the errors are shown in a message.
                $layout = 'messages.tpl';
                if ($this->request->getParam("nodeid") > 0) {
                    $this->reloadNode($this->request->getParam("nodeid"));
                    $this->request->setParam("js_files", $getTextJs->getTextArrayOfJs($this->_js));
                }
            } else {
                
                // If there is no errors, $view is visualized
                $layout = 'default.tpl';
            }
        }
        $this->renderer->setTemplate('actions/commons/layouts/' . $layout);
        $output = $this->renderer->render($view);

        // Apply widgets renderer after smarty renderer
        $output = $this->_renderWidgets($output);
        if ($return === true) {
            return $output;
        }
        $this->request->setParam('outHTML', $output);
        $this->request->setParameters($this->renderer->getParameters());
        $this->response->sendHeaders();
        if ($this->request->getParam("out") == "WEB") {
            echo $this->request->getParam("outHTML");
        }
        return null;
    }

    /**
     * Renders the widgets of an action
     *
     * @param $output
     */
    function _renderWidgets($output)
    {
        // DEBUG: Apply widgets renderer after smarty renderer
        $factory = new  Factory(RENDERER_ROOT_PATH, '');
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
     * @param null $method
     * @param null $actionName
     * @param null $parameters
     */
    function redirectTo($method = NULL, $actionName = NULL, $parameters = NULL)
    {
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
            $_GET["actionid"] = $idAction;
            $_REQUEST["actionid"] = $idAction;
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
     * This method doesn't work when returning a JSON response
     * 
     * @param int $idnode
     */
    function reloadNode($idnode)
    {
        // TODO search and destroy the %20 generated in the last char of the query string
        $queryManager = new QueryManager(false);
        $file = App::getUrl( $queryManager->buildWith(array(
                'xparams[reload_node_id]' => $idnode,
                'js_file' => 'reloadNode',
                'method' => 'includeDinamicJs',
                'void' => 'SpacesInIE7HerePlease'
            ))
        );
        $this->addJs(urldecode($file));
    }

    /**
     * @param $_js
     * @param string $_module
     * @param null $params
     * @return array|string
     */
    public function addJs($_js, $_module = 'APP', $params = null)
    {
        if ('APP' == $_module) {
            $_js = App::getUrl($_js, false);
        } elseif ('XIMDEX' != $_module && 'internet'  != $_module ) {
            $path = \Ximdex\Modules\Manager::path($_module);
            $_js = $path . $_js;
        } elseif ('XIMDEX' == $_module) {
            $_js = App::getUrl($_js, false);
        }
        if ($params === null) {
            return $this->_js[] = $_js;
        } else {

            // If "params" attribute is set, javascript will be parsed
            return $this->_js[] = array(
                'file' => $_js,
                'params' => $params
            );
        }
    }

    /**
     * @param $_css
     * @param string $_module
     */
    public function addCss($_css, $_module = 'APP')
    {
        if ('APP' == $_module) {
            $_css = App::getUrl($_css);
        } elseif ('XIMDEX' != $_module) {
            $path = App::getValue('UrlRoot') . \Ximdex\Modules\Manager::path($_module);
            $_css = $path . $_css;
        }
        $this->_css[] = $_css;
    }

    /**
     * @param null $rendererClass
     * @return mixed|null|string
     */
    private function _get_render($rendererClass = null)
    {
        if ($rendererClass == null) {
            if (Session::get('debug_render') > 0) {
                switch (Session::get('debug_render')) {
                    case 1:
                        $rendererClass = "Smarty";
                        break;
                    case 2:
                        $rendererClass = "Json";
                        break;
                    case 3:
                        $rendererClass = "Debug";
                        break;
                    default:
                        $rendererClass = $this->request->getParam("renderer");
                }
            } else {
                $rendererClass = $this->request->getParam("renderer");
            }
        }

        // Si no hay definido ningún render
        if (!$rendererClass) {
            $rendererClass = "Smarty";
        }

        // Guardamos el render
        $this->request->setParam("renderer", $rendererClass);
        return $rendererClass;
    }

    /**
     * @param $data
     */
    public function sendJSON($data)
    {
        if (!$this->endActionLogged) {
            $this->logSuccessAction();
        }
        if (isset($data['status']) && is_int($data['status'])) {
            header('HTTP/1.1 {$data["status"]}');
        }
        header(sprintf('Content-type: application/json; charset=', $this->displayEncoding));
        $data = Serializer::encode(SZR_JSON, $data);
        echo $data;
        die();
    }

    /**
     * @param $data
     * @param null $etag
     */
    public function sendJSON_cached($data, $etag = null)
    {
        if ($etag) {
            $data = Serializer::encode(SZR_JSON, $data);
            $hash = md5($data);
            if ($hash == $etag) {
                header('HTTP/1.1 304 Not Modified');
                header('Content-Length: 0');
                die();
            } else {
                header(sprintf('Content-type: application/json; charset=', $this->displayEncoding));
                $data = rtrim($data, "}");
                $data = $data . ', "etag": "' . $hash . '"}';
                echo $data;
                die();
            }
        } else {
            header(sprintf('Content-type: application/json; charset=', $this->displayEncoding));
            $data = Serializer::encode(SZR_JSON, $data);
            $hash = md5($data);
            $data = rtrim($data, "}");
            $data = $data . ', "etag": "' . $hash . '"}';
            echo $data;
            die();
        }
    }

    /**
     * Remplace files con [LANG] to i18n file
     * Example:
     *  /assets/images/[LANG]/pingu.gif -> /assets/images/es/pingu.gif
     *  or
     *  /assets/images/ximTAGS/pingu_[LANG].gif -> /assets/images/ximTAGS/pingu_es.gif
     *  or ...
     *  This can be also done in html with the smarty var locale
     *  
     * @param $file
     * @param null $_lang
     * @param null $_default
     * @return mixed|null
     */
    function i18n_file($file, $_lang = null, $_default = null)
    {
        $_file = null;

        //Checking if the file is existing for the passed language
        if ($_lang != null) {
            $_file = str_replace("[LANG]", $_lang, $file);
            if (file_exists($_file))
                return $_file;
        }

        //if the associated file for this language is not existing, checking with system language
        $_lang = Session::get('locale');
        if ($_lang != null) {
            $_file = str_replace("[LANG]", $_lang, $file);
            if (file_exists($_file))
                return $_file;
        }
        $_lang = DEFAULT_LOCALE;
        if ($_lang != null) {
            $_file = str_replace("[LANG]", $_lang, $file);
            if (file_exists($_file))
                return $_file;
        }
        return $_default;
    }

    protected function renderMessages()
    {
        $this->render(array('messages' => $this->messages->messages));
        die();
    }

    /**
     * Decides if a tour is be able to be launched automatically given an user
     * 
     * @param $userId
     * @param null $action
     * @return bool
     */
    public function tourEnabled($userId, $action = null)
    {
        unset($userId);
        if (!\Ximdex\Modules\Manager::isEnabled('ximTOUR')) {
            return false;
        }
        $numReps = App::getValue('ximTourRep');
        $user = new User (Session::get("userID"));
        $result = $user->GetNumAccess();
        return ($result === null || $result < $numReps) ? true : false;
    }

    /**
     * @param $subject
     * @param $content
     * @param $to
     * @return array
     */
    protected function sendNotifications($subject, $content, $to)
    {
        $from = Session::get("userID");
        $result = $this->_sendNotification($subject, $content, $from, $to);
        $this->_sendNotificationXimdex($subject, $content, $from, $to);
        foreach ($result as $idUser => $resultByUser) {
            $user = new User($idUser);
            $userEmail = $user->get('Email');
            if ($resultByUser) {
                $this->messages->add(sprintf(_("Message successfully sent to %s"), $userEmail), MSG_TYPE_NOTICE);
            } else {
                $this->messages->add(sprintf(_("Error sending message to the mail address %s"), $userEmail), MSG_TYPE_WARNING);
            }
        }
        return $result;
    }

    protected function _sendNotification($subject, $content, $from, $to) {
        $result = array();
        foreach ($to as $toUser) {
            $user = new User($toUser);
            $userEmail = $user->get('Email');
            $userName = $user->get('Name');
            $mail = new Mail();
            $mail->addAddress($userEmail, $userName);
            $mail->Subject = $subject;
            $mail->Body = $content;
            if ($mail->Send()) {
                $result[$toUser] = true;
            } else {
                $result[$toUser] = false;
            }
        }
        return $result;
    }

    protected function _sendNotificationXimdex($subject, $content, $from, $to)
    {
        $result = array();
        foreach ($to as $toUser) {
            $messages = new \Ximdex\Models\ORM\MessagesOrm();
            $messages->set("IdFrom", $from);
            $messages->set("IdOwner", $toUser);
            $messages->set("Subject", $subject);
            $messages->set("Content", $content);
            if ($messages->add()) {
                $result[$toUser] = true;
            } else {
                $result[$toUser] = false;
            }
        }
        return $result;
    }
}