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

use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;
use Ximdex\NodeTypes\XsltNode;
use Ximdex\Runtime\App;

require_once(APP_ROOT_PATH . XIMDEX_VENDORS . '/flow/ConfigInterface.php');
require_once(APP_ROOT_PATH . XIMDEX_VENDORS . '/flow/Config.php');
require_once(APP_ROOT_PATH . XIMDEX_VENDORS . '/flow/Exception.php');
require_once(APP_ROOT_PATH . XIMDEX_VENDORS . '/flow/File.php');
require_once(APP_ROOT_PATH . XIMDEX_VENDORS . '/flow/RequestInterface.php');
require_once(APP_ROOT_PATH . XIMDEX_VENDORS . '/flow/Request.php');
require_once(APP_ROOT_PATH . XIMDEX_VENDORS . '/flow/Uploader.php');

class Action_fileupload_common_multiple extends ActionAbstract
{
    public function __construct() {
        parent::__construct();
        $this->uploadsFolder = XIMDEX_ROOT_PATH . App::getValue( 'TempRoot') .'/'. App::getValue( 'UploadsFolder');
        $this->chunksFolder = XIMDEX_ROOT_PATH . App::getValue( 'TempRoot') .'/'. App::getValue( 'ChunksFolder');
    }

    // Main method: shows initial form
    public function index () {
        $is_structured = false;
        $idNode = (int) $this->request->getParam("nodeid");
        $actionID = (int) $this->request->getParam("actionid");
        $type = $this->request->getParam('type');
        $node = new Node($idNode);

        /** ********* Find out folder nodetype **** */
        $baseIoInferer = new \Ximdex\IO\BaseIOInferer();
        $type_folder = $baseIoInferer->infereType('FOLDER', $idNode );
        $type_node = $type_folder["NODETYPENAME"];
        
        /** ********* Checking permits **************************** */
        $userid = \Ximdex\Runtime\Session::get('userID');
        if(empty($userid)) {
            $this->messages->add(_('It is necessary to be an active user to upload files'), MSG_TYPE_ERROR);
        }
        if(empty($type_node)) {
            $this->messages->add(_('NodeType is empty'), MSG_TYPE_ERROR);
        }
        $user = new \Ximdex\Models\User($userid);
        if(!$user->canWrite(array('node_id' => $idNode)) ) {
            $this->messages->add(_('Files cannot be added because of lack of permits'), MSG_TYPE_ERROR);
        }

        /** ********* Preparing view ************ */
        //Filter and button tag according to type of upload file
        switch($type_node)  {
            case 'CssFolder':
                $lbl_anadir = _(' Add style sheets');
                $allowedMimes = 'text/css';
                $allowedExtensions = '.css';
                break;
            case 'TemplateViewFolder':
                $lbl_anadir = _(' Add schemas ');
                $allowedExtensions = '.xml';
                $allowedMimes = 'text/xml, application/xml';
                break;
            case 'TemplatesFolder':
                $lbl_anadir = _(' Add templates ');
                $allowedExtensions = '.xml, .xsl';
                $allowedMimes = 'text/xml';
                break;
            case 'XmlContainer':
                $lbl_anadir = _(' Add XML files ');
                $is_structured=true;
                $allowedExtensions = '.xml, .xsl';
                $allowedMimes = 'text/xml';
                break;
            /*
            case 'CommonFolder':
                $lbl_anadir = _(' Add common files ');
                $allowedExtensions = array( 'ht','htm','html','xhtml','plain','txt','zip');
                $allowedMimes = array( 'ht','htm','html','xhtml','plain','txt','zip');
                break;
            */
            default:
                $lbl_anadir = _(' Add files');
                $allowedMimes = '';
                $allowedExtensions = '';
                break;
        };
        $this->addJs('/actions/fileupload_common_multiple/resources/js/loader.js');
        $this->addCss('/actions/fileupload_common_multiple/resources/css/loader.css');
        $this->addCss('/actions/fileupload_common_multiple/resources/css/uploader_html5.css');
        $uploaderOptions = array (
            "nodeURL" => App::getUrl("/?actionid=$actionID&nodeid={$idNode}"),
            "lbl_anadir" => $lbl_anadir,
            'messages' => $this->messages->messages,
            'nodeid' => $idNode,
            'actionid' => $this->request->getParam('actionid'),
            'type' => $type,
            'allowedMimes' => $allowedMimes,
            'allowedExtensions' => $allowedExtensions,
            'type_node'=>$type_node,
            'is_structured' => $is_structured,
            'name' => $node->get('Name')
        );
        if ($type_node == "XmlContainer"){
            
            // Gets default schema for XML through propInheritance
            $schemas = null;
            $section = $node->getSection();
            if ($section > 0) {
                $section = new Node($section);
                $hasTheme = (bool) count($section->getProperty('theme'));
                if ($hasTheme) {
                    $schemas = $section->getProperty('theme_visualtemplates');
                }
            }
            $schemas = $schemas === null ? $node->getSchemas() : $schemas;
            $schemaArray = array();
            if (!is_null($schemas)) {
                foreach ($schemas as $idSchema) {
                    $schemaNode = new Node($idSchema);
                    $schemaArray[] = array('id' => $idSchema, 'name' => $schemaNode->get('Name'));
                }
            }
            $language = new Language();
            $languages = $language->getLanguagesForNode($idNode);
            $languageArray = array();
            foreach ($languages as $lang) {
                $languageArray[] = array('id' => $lang['IdLanguage'], 'name' => $lang['Name']);
            }
            $uploaderOptions['globalMetaOnly'] = true;
            $uploaderOptions['metaFields'] = array(
                "schema" => array(
                    "type" => "select",
                    "options" => $schemaArray,
                    "label" => _("Select a schema"),
                    "required" => true
                ),
                "language" => array(
                    "type" => "select",
                    "options" => $languageArray,
                    "label" => _("Select a language"),
                    "required" => true
                )
            );
        }
        $values = array (
            'nodeid' => $idNode,
            'uploaderOptions' => json_encode($uploaderOptions)
        );
        $this->render($values, 'index', 'default-3.0.tpl');
        $this->clearChunks();
    }

    public function clearChunks() {
        if (file_exists($this->chunksFolder)) {
            $uploader = new \Flow\Uploader();
            $uploader->pruneChunks($this->chunksFolder);
        }
    }

    private function _saveFile($path) {
        if (!file_exists($this->chunksFolder)) {
            mkdir($this->chunksFolder, 0777, true);
        }
        $config = new \Flow\Config(array(
            'tempDir' => $this->chunksFolder
        ));
        $file = new \Flow\File($config);
        if ($file->validateChunk()) {
            $file->saveChunk();
        }
        else {
            
            // Error, invalid chunk upload request, retry
            header("HTTP/1.1 400 Bad Request");
            return false;
        }
        if ($file->validateFile() && $file->save($path)) {
            return true;
        }
        return false;
    }

    public function uploadFlowFile() {
        if (!file_exists($this->uploadsFolder)) {
            mkdir($this->uploadsFolder, 0777, true);
        }
        $path = $this->uploadsFolder . '/' . $_POST['flowIdentifier'];
        if ($this->_saveFile($path)) {
            $idNode = $this->request->getParam('nodeid');
            $type = $this->request->getParam('type');
            $metadata = json_decode($this->request->getParam('meta'));
            $overwrite = ($_POST['overwrite'] == 'true') ? true : false;
            $file = $_FILES['file'];
            $file['tmp_name'] = $path;
            if (!empty($_POST['ximFilename']))
            {
                $file['name'] = $_POST['ximFilename'];
            }
            else if (isset($_POST['flowFilename']))
            {
                $file['name'] = $_POST['flowFilename'];
            }
            ini_set('memory_limit', -1);
            $result = $this->_createNode($file, $idNode, $type, $metadata, $overwrite);
            
            // Update the templates_include.xsl files if the file is a template
            if ($result and $type == 'ptd')
            {
                $node = new Node($idNode);
                $xsltNode = new XsltNode($node);
                if ($xsltNode->reload_templates_include(new Node($node->getProject())) === false) {
                    $result['msg'] = 'File uploaded but the template cannot be added to the templates include file (' 
                        . $xsltNode->messages . ')';
                }
            }
            if (file_exists($path)) {
                unlink($path);
            }
            $this->sendJSON($result);
        }
        else {
            return false;
        }
    }

    private function _setRest($msg, $status=500) {
        $retval = array();
        $retval["msg"] = $msg;
        $retval['status'] =  $status;
        return $retval;
    }

    /**
     * Creating a node according to name and file path
     */
    private function _createNode($file, $idNode,  $type, $metadata, $overwrite) {
        $normalizedName = \Ximdex\Utils\Strings::normalize($file["name"]);
        $baseIoInferer = new \Ximdex\IO\BaseIOInferer();
        
        // Finding out element nodetype
        if (empty($type) || $type == 'null') {
            $nodeTypeName = $baseIoInferer->infereFileType($file, $idNode);
        }
        else {
            $nodeTypeName = $baseIoInferer->infereFileType($file, $idNode, $type);
        }
        $result = 0;
        $node = new Node($idNode);
        if (!$node) {
            return  $this->_setRest(_(" Destination node has not been found "));
        }
        $estimatedNode = $node->GetChildByName($normalizedName);
        if ($estimatedNode > 0) {
            if ($overwrite) {
                $data = array(
                    'NODETYPENAME' => $nodeTypeName,
                    'ID' => $estimatedNode,
                    'CHILDRENS' => array (
                        array ('NODETYPENAME' => 'PATH', 'SRC' => $file["tmp_name"])
                    )
                );
                $baseIO = new \Ximdex\IO\BaseIO();
                $result = $baseIO->update($data);
            }
            else {
                return $this->_setRest(_('A file already exists with same name.') );
            }
        }
        elseif ($node->nodeType->get('IsFolder')) {
            
            //To upload xml content
            if ($node->nodeType->get("Name")=="XmlRootFolder"){
                $newNodeName = str_replace(".xml","",$normalizedName);
                $data = array(
                    'NODETYPENAME' => "XmlContainer",
                    'NAME' => $newNodeName,
                    'PARENTID' => $idNode,
                    'CHILDRENS' => array(
                        array(
                            'NODETYPENAME' => 'VISUALTEMPLATE',
                            'ID' => $metadata->schema
                        )
                    )
                );
                $baseIO = new \Ximdex\IO\BaseIO();
                $result = $baseIO->build($data);
                $documentNodeType = new NodeType();
                $documentNodeType->SetByName("XMLDOCUMENT");
                $data = array(
                    'NODETYPENAME' => $documentNodeType->get("Name"),
                    'NAME' => $newNodeName,
                    'NODETYPE' => $documentNodeType->get('IdNodeType'),
                    'PARENTID' => $result,
                    'CHILDRENS' => array(
                        array('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $metadata->schema),
                        array('NODETYPENAME' => 'LANGUAGE', 'ID' => $metadata->language),
                    )
                );
                $channels = new Channel();
                $allChannels = $channels->GetAllChannels();
                if (!empty($allChannels)) {
                    foreach ($allChannels as $channel) {
                        $data['CHILDRENS'][] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $channel);;
                    }
                }
                $docId = $baseIO->build($data);
                if ($docId > 0){
                    $newDocumentNode = new Node($docId);
                    $content = file_get_contents($file["tmp_name"]);
                    $content = preg_replace('/<\?xml.*\?>.*\n/','',$content);
                    $newDocumentNode->SetContent($content);
                }
            }
            elseif ($nodeTypeName){
                $data = array(
                    'NODETYPENAME' => $nodeTypeName,
                    'NAME' => $normalizedName,
                    'PARENTID' => $idNode,
                    'CHILDRENS' => array (
                        array ('NODETYPENAME' => 'PATH', 'SRC' => $file["tmp_name"] )
                    )
                );
                $baseIO = new \Ximdex\IO\BaseIO();
                $result = $baseIO->build($data);
            }
            else {
                return $this->_setRest(_(" File is not of expected type "));
            }

            // If there is any problem with file name: spaces, uncommon characters, etc.
            if ($result > 0) {
                if ($overwrite) {
                    return $this->_setRest(_('File has been successfully overwritten.'), "ok" );
                }
                return $this->_setRest(_('File has been successfully uploaded.'), "ok" );
            }
            Logger::error(_("BaseIO has returned the error code"). $result);
            return  $this->_setRest($baseIO->messages->messages[0]["message"]);
        }
        return $result;
    }
}