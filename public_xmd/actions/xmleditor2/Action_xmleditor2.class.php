<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\User;
use Ximdex\Models\XimLocale;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Request;
use Ximdex\Utils\Serializer;
use Ximdex\Utils\Strings;
use Ximdex\Utils\Messages;
use Ximdex\Logger;

Ximdex\Modules\Manager::file('/actions/xmleditor2/XimlinkResolver.class.php');
Ximdex\Modules\Manager::file('/actions/createlink/Action_createlink.class.php');

class Action_xmleditor2 extends ActionAbstract
{
    private $editor = null;
    
    public function index()
    {
        $idnode = $this->request->getParam('nodeid');
        $strDoc = new StructuredDocument($idnode);
        if ($strDoc->getSymLink()) {
            $masterNode = new Node($strDoc->getSymLink());
            $values = array
            (
                'path_master' => $masterNode->getPath()
            );
            $this->render($values, 'linked_document', 'default-3.0.tpl');
            return false;
        }
        $queryManager = App::get('\Ximdex\Utils\QueryManager');
        $locale = new XimLocale();
        $user_locale = $locale->getLocaleByCode(\Ximdex\Runtime\Session::get('locale'));
        $action = $queryManager->getPage() . $queryManager->buildWith(array
        (
            'method' => 'load',
            'on_resize_functions' => '',
            'time_id' => microtime(true),  // Timestamp for javascripts
            'user_locale' => $user_locale,
            'action' => 'xmleditor2',
            'nodeid' => $idnode
        ));
        $this->render(array('action' => $action), null, 'iframe.tpl');
    }

    /**
     * Main method: shows initial form
     */
    public function load()
    {
        $idnode = $this->request->getParam('nodeid');
        $view = $this->request->getParam('view');
        $this->getEditor($idnode);
        $xslIncludesOnServer = App::getValue("XslIncludesOnServer");
        $values = $this->editor->openEditor($idnode, $view);
        $values['on_resize_functions'] = '';
        $values['xinversion'] = App::getValue("VersionName");
        $template = 'loadEditor_' . $this->editor->getEditorName();
        
        // Adding Config params for xsl:includes
        $values["xslIncludesOnServer"] = $xslIncludesOnServer;
        $values["user_connect"] = null;
        $values['time_id'] = 0;
        $this->render($values, $template, 'xmleditor2.tpl');
    }

    private function getEditor(int $idnode)
    {
        $editorName = strtoupper('KUPU');
        $msg = new Messages();
        $class = 'XmlEditor_' . $editorName;
        $file = '/actions/xmleditor2/model/XmlEditor_' . $editorName . '.class.php';
        $editor = null;
        if (! is_readable(APP_ROOT_PATH . $file)) {
            $msg->add(_('A non-existing editor has been refered.'), MSG_TYPE_ERROR);
            $this->render(array('nodeid' => $idnode, 'messages' => $msg->messages));
            exit();
        }
        Ximdex\Modules\Manager::file($file);
        if (! class_exists($class)) {
            $msg->add(_('A non-existing editor has been refered.'), MSG_TYPE_ERROR);
            $this->render(array('nodeid' => $idnode, 'messages' => $msg->messages));
            exit();
        }
        $query = App::get('\Ximdex\Utils\QueryManager');
        $base_url = $query->getPage() . $query->buildWith(array());
        $editor = new $class();
        $editor->setBaseURL($base_url);
        $editor->setEditorName($editorName);
        $this->editor = $editor;
        return $this->editor;
    }

    private function printContent($content, bool $serialize = true)
    {
        $ajax = $this->request->getParam('ajax');
        if ($ajax != 'json') {
            header('Content-type: text/xml');
        } else {
            if ($serialize) {
                if (! is_array($content) && ! is_object($content)) {
                    $content = array('data' => $content);
                }
                $content = Serializer::encode(SZR_JSON, $content);
            }
            header('Content-type: application/json');
        }
        print $content;
        exit();
    }

    public function getConfig()
    {
        $idnode = $this->request->getParam('nodeid');
        $this->getEditor($idnode);
        $content = $this->editor->getConfig($idnode);
        $this->printContent($content);
    }

    public function getInfo()
    {
        $idnode = $this->request->getParam('nodeid');
        $node = new Node($idnode);
        $info = $node->loadData();
        if (! empty($info)) {
            $info = json_encode($info);
            header('Content-type: application/json');
        }
        echo $info;
        die();
    }

    public function getXmlFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $view = $this->request->getParam('view');
        $content = $this->request->getParam('content');
        $this->getEditor($idnode);
        $content = $this->editor->getXmlFile($idnode, $view, $content);
        $this->printContent($content);
    }

    public function verifyTmpFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $this->getEditor($idnode);
        $content = $this->editor->verifyTmpFile($idnode);
        $this->printContent($content);
    }

    public function removeTmpFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $this->getEditor($idnode);
        $content = $this->editor->removeTmpFile($idnode);
        $this->printContent($content);
    }

    public function recoverTmpFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $this->getEditor($idnode);
        $content = $this->editor->recoverTmpFile($idnode);
        $this->printContent($content);
    }

    public function getXslFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $view = $this->request->getParam('view');
        $includesOnServer = $this->request->getParam("includesInServer");
        $this->getEditor($idnode);
        $content = $this->editor->getXslFile($idnode, $view, $includesOnServer);
        $this->printContent($content);
    }

    public function getSchemaFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $this->getEditor($idnode);
        $content = $this->editor->getSchemaFile($idnode);
        $this->printContent($content);
    }

    public function canEditNode()
    {
        $ximcludeId = $this->request->getParam('nodeid');
        $userId = \Ximdex\Runtime\Session::get('userID');
        $user = new User($userId);
        $ret = $user->canWrite(array('node_id' => $ximcludeId));
        $this->printContent(array('editable' => $ret));
    }

    public function validateSchema()
    {
        $idnode = $this->request->getParam('nodeid');
        $xmldoc = Request::post('content');
        $xmldoc = Strings::stripslashes($xmldoc);
        $this->getEditor($idnode);
        $ret = $this->editor->validateSchema($idnode, $xmldoc);
        $this->printContent($ret);
    }

    public function saveXmlFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $content = Request::post('content');
        $autoSave = ($this->request->getParam('autosave') == 'true') ? true : false;
        $this->getEditor($idnode);
        $response = $this->editor->saveXmlFile($idnode, $content, $autoSave);

        // TODO: Evaluate $response['saved']...
        foreach ($response['headers'] as $header) {
            header($header);
        }
        $this->printContent($response['content']);
    }

    public function publicateFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $content = Request::post('content');
        $this->getEditor($idnode);
        $response = $this->editor->publicateFile($idnode, $content);
        foreach ($response['headers'] as $header) {
            header($header);
        }
        $this->printContent($response['content']);
    }

    public function getSpellCheckingFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $content = Request::post('content');
        $this->getEditor($idnode);
        $content = $this->editor->getSpellCheckingFile($idnode, $content);
        $this->printContent($content);
    }

    public function getAnnotationFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $content = Request::post('content');
        $this->getEditor($idnode);
        $content = $this->editor->getAnnotationFile($idnode, $content);
        $this->printContent($content);
    }

    /**
     * Returns a JSON string with the allowed nodes under especified uid
     */
    public function getAllowedChildrens()
    {
        $idnode = $this->request->getParam('nodeid');
        $uid = $this->request->getParam('uid');
        $content = $this->request->getParam('content');
        $this->getEditor($idnode);
        $allowedChildrens = $this->editor->getAllowedChildrens($idnode, $uid, $content);
        $this->printContent($allowedChildrens);
    }

    public function getPreviewInServerFile()
    {
        $idnode = $this->request->getParam('nodeid');
        $content = Request::post('content');
        $idChannel = Request::post('channelid');
        $this->getEditor($idnode);
        $content = $this->editor->getPreviewInServerFile($idnode, $content, $idChannel);
        $this->printContent($content);
    }

    public function getNoRenderizableElements()
    {
        $idnode = $this->request->getParam('nodeid');
        $this->getEditor($idnode);
        $content = $this->editor->getNoRenderizableElements($idnode);
        $this->printContent($content);
    }

    public function getAvailableXimlinks()
    {
        $docid = $this->request->getParam('docid');
        $term = $this->request->getParam('term');
        $xr = new XimlinkResolver();
        $data = $xr->getAvailableXimlinks($docid, $term);
        $this->sendJSON($data);
    }

    public function resolveXimlinkUrl()
    {
        $idnode = $this->request->getParam('nodeid');
        $channel = $this->request->getParam('channel');
        $xr = new XimlinkResolver();
        $data = $xr->resolveXimlinkUrl($idnode, $channel);
        $this->sendJSON($data);
    }

    public function getAll()
    {
        $idnode = $this->request->getParam('nodeid');
        $view = $this->request->getParam('view');
        $content = $this->request->getParam('content');
        $this->getEditor($idnode);
        
        // Get XML File
        $contentXML = $this->editor->getXmlFile($idnode, $view, $content);
        $res = array();
        $res['xmlFile'] = $contentXML;
        
        // Get Schema File
        $contentRNG = $this->editor->getSchemaFile($idnode);
        if (is_array($contentRNG) and $contentRNG['error']) {
            
            // TODO in this place we need to show the validation errors in the editor
        }
        $res['schemaFile'] = $contentRNG;
        
        // Get XSL File
        $view = $this->request->getParam('view');
        $includesOnServer = $this->request->getParam("includesInServer");
        $this->getEditor($idnode);
        $contentXSL = $this->editor->getXslFile($idnode, $view, $includesOnServer);
        $res['xslFile'] = $contentXSL;
        
        // No Renderizable Elements
        $contentNoRender = $this->editor->getNoRenderizableElements($idnode);
        $res['noRenderizableElements'] = $contentNoRender;
        
        // Get Config
        $contentConfig = $this->editor->getConfig($idnode);
        $res['config'] = $contentConfig;
        
        // Print JSON
        /*
        $content = Serializer::encode(SZR_JSON, $res);
        header('Content-type: application/json');
        echo $content;
        exit();
        */
        $this->sendJSON($res);
    }

    /**
     * Check whether the node is being edited by some user
     *
     * @return string json string containing editing information
     */
    public function checkEditionStatus()
    {
        $idnode = $this->request->getParam('nodeid');
        $userID = (int)\Ximdex\Runtime\Session::get('userID');
        $nodeEdition = new \Ximdex\Models\NodeEdition();
        $results = $nodeEdition->getByNode($idnode);
        $edition = false;
        $extraEdition = array();
        if (count($results) > 0) {
            $edition = true;
            $userNames = array();
            foreach ($results as $result) {
                if (!isset($userNames[$result["IdUser"]])) {
                    $user = new User($result["IdUser"]);
                    $userNames[$result["IdUser"]] = $user->GetRealName();
                }
                $extra = array('user' => $userNames[$result["IdUser"]],
                    'startTime' => $result["StartTime"]);
                array_push($extraEdition, $extra);
            }
        }
        
        // Creating the new edition for this user
        $res = $nodeEdition->create($idnode, $userID);
        if (!$res) {
            Logger::error(_('Error creating a new Node Edition'));
        }
        $return = array('edition' => $edition, 'data' => $extraEdition);
        header('Content-type: application/json');
        echo json_encode($return);
    }

    /**
     * Removes a node edition according to a given node and user
     */
    public function removeNodeEdition()
    {
        $nodeid = $this->request->get('nodeid');
        $userid = \Ximdex\Runtime\Session::get('userID');
        $nodeEdition = new \Ximdex\Models\NodeEdition();
        $res = $nodeEdition->deleteByNodeAndUser($nodeid, $userid);
        if (!$res) {
            Logger::error("Error deleting Node Edition for node " . $nodeid . " and user " . $userid);
        }
    }

    public function saveXimLink()
    {
        $result = array();
        $url = urlencode($this->request->getParam("url"));
        $idParent = $this->request->getParam("idParent");
        $name = $this->request->getParam("name");
        $description = $this->request->getParam("description");
        
        // Check if name is available for the selected parent
        $nodeParent = new Node($idParent);
        if ($nodeParent->getChildByName($name)) {
            $result["success"] = false;
            $result["message"] = _("A link with that name already exists in the selected folder");
        }
        $actionCreateLink = new Action_createlink();
        $idLink = $actionCreateLink->createNodeLink($name, $url, $description, $idParent);
        if ($idLink) {
            $result["success"] = true;
            $result["idLink"] = $idLink;
        }
        $this->sendJSON($result);
    }

    public function getLinkFolder()
    {
        $result = array();
        $idNode = $this->request->getParam("nodeid");
        $node = new Node($idNode);
        $idProject = $node->getProject();
        $project = new Node($idProject);
        $children = $project->getChildren(\Ximdex\NodeTypes\NodeTypeConstants::LINK_MANAGER);
        $result["success"] = true;
        $result["idLinkFolder"] = $children[0];
        $this->sendJSON($result);
    }
}
