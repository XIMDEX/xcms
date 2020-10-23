<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Modules\Module;
use Ximdex\MVC\ActionAbstract;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\NodeTypeGroupConstants;
use Ximdex\Runtime\App;
use Ximdex\NodeTypes\XsltNode;
use Ximdex\IO\BaseIO;

Ximdex\Modules\Manager::file('/actions/addfoldernode/model/ProjectTemplate.class.php');
Ximdex\Modules\Manager::file('/actions/addfoldernode/conf/addfoldernode.conf.php');

class Action_addfoldernode extends ActionAbstract
{
    public $channels;
    public $languages;

    /**
     * Main Method: shows the initial form
     */
    public function index()
    {
        // Getting node info from params.
        $nodeID = $this->request->getParam('nodeid');
        $node = new Node($nodeID);

        // First, checks if has nodetypeid param
        if ($this->request->get('nodetypeid')) {
            $nt = new NodeType($this->request->get('nodetypeid'));
            $nodeType = [];
            $nodeType['name'] = $nt->get('Name');
            $nodeType['friendlyName'] = $nt->get('Description');
        } else {
            $nodeType = $this->GetTypeOfNewNode($nodeID);
        }
        $friendlyName = (!empty($nodeType['friendlyName'])) ? $nodeType['friendlyName'] : $nodeType['name'];
        $go_method = ($nodeType['name'] == 'Section') ? 'addSectionNode' : 'addNode';
        
        // Show disclaimer if node canAttachGroups
        $CanAttachGroups = 0;
        if ($this->request->get('nodetypeid')) {
            $nt = new NodeType($this->request->get('nodetypeid'));
            $CanAttachGroups = $nt->get('CanAttachGroups');
        } else {
            $nodeType = $this->GetTypeOfNewNode($nodeID);
            $CanAttachGroups = (isset( $nodeType['CanAttachGroups'] )) ? $nodeType['CanAttachGroups'] : false;
        }
        $this->request->setParam('go_method', $go_method);
        $this->request->setParam('friendlyName', $friendlyName);
        $this->request->setParam('CanAttachGroups', $CanAttachGroups);

        /* If we can attachgroups, we check that we are a xlms folder */
        if ( $CanAttachGroups ) {
            $isXlms = in_array($node->nodeType->getID(), NodeTypeGroupConstants::XLMS_TYPE_FOLDERS);
            $this->request->setParam('xlms', $isXlms);
        }

        $values = array(
            'go_method' => 'addNode',
            'nodeID' => $nodeID,
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'name' => $node->GetNodeName(),
            'type' => $nodeType['name']
        );
        if (isset($nodeType['name']) and ( $nodeType['name'] == 'Project' || $nodeType['name'] == 'XLMSProject') ){
            $this->loadNewProjectForm($values);
        } else {
            $this->render($values, 'index', 'default-3.0.tpl');
        }
    }

    public function getTypeOfNewNode($nodeID)
    {
        // TODO change this switch sentence for a query to the NodeAllowedContents table to check what subfolders can contain
        $node = new Node($nodeID);
        if (!$node->get('IdNode') > 0) {
            return null;
        }
        $nodeTypeName = $node->nodeType->GetName();
        switch ($nodeTypeName) {
            case 'Projects':
                $newNodeTypeName = 'Project';
                $friendlyName = 'Project';
                break;
            case 'Project':
                $newNodeTypeName = 'Server';
                $friendlyName = 'Server';
                break;
            case 'Server':
                $newNodeTypeName = 'Section';
                $friendlyName = 'Section';
                break;
            case 'Section':
                $newNodeTypeName = 'Section';
                $friendlyName = 'Section';
                break;
            case 'ImagesRootFolder':
                $newNodeTypeName = 'ImagesFolder';
                $friendlyName = 'Image folder';
                break;
            case 'ImagesFolder':
                $newNodeTypeName = 'ImagesFolder';
                $friendlyName = 'Image folder';
                break;
            case 'XmlRootFolder':
                $newNodeTypeName = 'XmlFolder';
                $friendlyName = 'XML Folder';
                break;
            case 'XmlFolder':
                $newNodeTypeName = 'XmlFolder';
                $friendlyName = 'XML Folder';
                break;
            case 'XLMSRootFolderMultimedia':
                $newNodeTypeName = 'XLMSRootFolderMultimedia';
                $friendlyName = 'XML Root Folder Multimedia';
                break;
            case 'ImportRootFolder':
                $newNodeTypeName = 'ImportFolder';
                $friendlyName = 'Ximclude folder';
                break;
            case 'ImportFolder':
                $newNodeTypeName = 'ImportFolder';
                $friendlyName = 'Ximclude folder';
                break;
            case 'CommonRootFolder':
                $newNodeTypeName = 'CommonFolder';
                $friendlyName = 'Common folder';
                break;
            case 'CommonFolder':
            case 'XOTFFolder':
                $newNodeTypeName = 'CommonFolder';
                $friendlyName = 'Common folder';
                break;
            case 'CssRootFolder':
                $newNodeTypeName = 'CssFolder';
                $friendlyName = 'CSS folder';
                break;
            case 'CssFolder':
                $newNodeTypeName = 'CssFolder';
                $friendlyName = 'CSS folder';
                break;
            case 'TemplatesRootFolder':
                $newNodeTypeName = 'TemplatesRootFolder';
                $friendlyName = 'Template folder';
                break;
            case 'TemplatesFolder':
            case 'TemplateViewFolder':
                $newNodeTypeName = 'TemplateViewFolder';
                $friendlyName = 'Template folder';
                break;
            case 'LinkManager':
                $newNodeTypeName = 'LinkFolder';
                $friendlyName = 'Link folder';
                break;
            case 'LinkFolder':
                $newNodeTypeName = 'LinkFolder';
                $friendlyName = 'Link folder';
                break;
            case 'XimletRootFolder':
                $newNodeTypeName = 'XimletFolder';
                $friendlyName = 'Ximlet folder';
                break;
            case 'XimletFolder':
                $newNodeTypeName = 'XimletFolder';
                $friendlyName = 'Ximlet folder';
                break;
            case 'OpenDataSection':
                $newNodeTypeName = 'OpenDataDataset';
                $friendlyName = 'Dataset';
                break;
            case 'JsRootFolder':
                $newNodeTypeName = 'JsFolder';
                $friendlyName = 'JS folder';
                break;
            case 'JsFolder':
                $newNodeTypeName = 'JsFolder';
                $friendlyName = 'JS folder';
                break;
            default:
                // Log to user
                return null;
        }
        $type = [];
        $type['name'] = $newNodeTypeName;
        $type['friendlyName'] = $friendlyName;
        return $type;
    }

    /**
     * Load the creation form for a new project
     */
    private function loadNewProjectForm($values)
    {
        $chann = new Channel();
        $channs = $chann->find();
        if ($channs) {
            $channels = [];
            foreach ($channs as $channData) {
                $channels[] = array('id' => $channData['IdChannel'], 'name' => $channData['Name']);
            }
        } else {
            $channels = NULL;
        }
        $lang = new Language();
        $langs = $lang->find();
        if ($langs) {
            $languages = [];
            foreach ($langs as $langData) {
                $languages[] = array('id' => $langData['IdLanguage'], 'name' => $langData['Name']);
            }
        } else {
            $languages = NULL;
        }
        $values['langs'] = $languages;
        $values['channels'] = $channels;
        
        // Load projects

        $cssFolder = '/actions/addfoldernode/resources/css/';
        $jsFolder = '/actions/addfoldernode/resources/js/';

        $this->addCss($cssFolder . 'style.css');
        $this->addJs($jsFolder . 'init.js');

        // Only Project has theme, XLMSProject dont have a associated theme
        if ( $values['type'] == 'Project' ) {
            $themes = ProjectTemplate::getAllProjectTemplates();
            $arrayTheme = array();
            foreach ($themes as $theme) {
                $themeDescription = [];
                $themeDescription['name'] = $theme->__get('name');
                $themeDescription['title'] = $theme->__get('title');
                $themeDescription['description'] = $theme->__get('description');
                $themeDescription['configurable'] = $theme->configurable == '1' ? true : false;
                $arrayTheme[] = $themeDescription;
            }
            $values['themes'] = $arrayTheme;
        }
        $this->render($values, 'addProject', 'default-3.0.tpl');
    }

    public function addNode()
    {
        $nodeID = $this->request->getParam('nodeid');
        $name = $this->request->getParam('name');
        $this->name = $name;
        $channels = $this->request->getParam('channels_listed');
        $languages = $this->request->getParam('languages_listed');
        $nodeType = [];

        /**
         * First, checks if has nodetypeid param
         */
        if ($this->request->get('nodetypeid')) {
            $nt = new NodeType($this->request->get('nodetypeid'));
            $nodeType['name'] = $nt->get('Name');
            $nodeType['friendlyName'] = $nt->get('Description');
        } else {
            $nodeType = $this->GetTypeOfNewNode($nodeID);
        }
        $nodeTypeName = $nodeType['name'];
        $nodeType = new NodeType();
        $nodeType->SetByName($nodeTypeName);
        if ($this->request->getParam('theme'))
        {
            // We use this global variable to know that we are creating a project from a theme
            $GLOBALS['fromTheme'] = true;
        }
        $folder = new Node();
        $idFolder = $folder->createNode($name, $nodeID, $nodeType->GetID(), null);

        // Adding channel and language properties (if project)
        if ($idFolder > 0 && ($nodeTypeName == 'Project' || $nodeTypeName == 'XLMSProject' )) {
            $node = new Node($idFolder);
            if (!empty($channels) && is_array($channels)) {
                $node->setProperty('channel', array_keys($channels));
                $this->channels = $channels;
            }
            if (!empty($languages) && is_array($languages)) {
                $node->setProperty('language', array_keys($languages));
                $this->languages = $languages;
            }
            $this->createProjectNodes($idFolder);
        }
        elseif ($idFolder > 0 && $nodeTypeName == 'XSIRRepository') {
            $node = new Node();
            $node->CreateNode('schemes', $idFolder, NodeTypeConstants::TEMPLATE_VIEW_FOLDER);
            $node->CreateNode('images', $idFolder, NodeTypeConstants::XSIR_IMAGE_FOLDER);
            $node->CreateNode('videos', $idFolder, NodeTypeConstants::XSIR_VIDEO_FOLDER);
            $node->CreateNode('widgets', $idFolder, NodeTypeConstants::XSIR_WIDGET_FOLDER);
            $node->CreateNode('other', $idFolder, NodeTypeConstants::XSIR_OTHER_FOLDER);
        }
        if ($idFolder)
        {
            // Reload the templates include files for this new project
            if (!isset($node)) {
                $node = new Node($idFolder);
            }
            if ( in_array( $node->GetNodeType(),NodeTypeGroupConstants::NODE_PROJECTS )
                or $node->GetNodeType() == NodeTypeConstants::TEMPLATES_ROOT_FOLDER
                or $node->GetNodeType() == NodeTypeConstants::SERVER 
                or $node->GetNodeType() == NodeTypeConstants::SECTION ) {
                $xsltNode = new XsltNode($node);
                if ($xsltNode->reload_templates_include(new Node($node->getProject())) === false)
                    $this->messages->mergeMessages($xsltNode->messages);
            }
            $this->messages->add(sprintf(_('%s has been successfully created'), $name), MSG_TYPE_NOTICE);
        } else {
            if ($folder->msgErr) {
                $error = $folder->msgErr;
            }
            elseif ($folder->messages->messages) {
                $error = $folder->messages->messages[0];
            } else {
                $error = 'Unknown error';
            }
            $this->messages->add(sprintf(_('The operation has failed: %s'), $error), MSG_TYPE_ERROR);
        }
        $arrValores = array('messages' => $this->messages->messages, 'parentID' => $nodeID);
        $this->sendJSON($arrValores);
    }

    public function createProjectNodes($projectId)
    {
        $theme = $this->request->getParam('theme');
        if ($theme)
        {
            // we use this global variable to know that we are creating a project from a theme
            $GLOBALS['fromTheme'] = true;
            $projectTemplate = new ProjectTemplate($theme);
            $servers = $projectTemplate->getServers();
            $schemas = $projectTemplate->getSchemes();
            $templates = $projectTemplate->getTemplates();
            foreach ($schemas as $schema) {
                $this->schemas = $this->insertFiles($projectId, App::getValue('SchemasDirName'), array($schema));
            }
            foreach ($templates as $template) {
                $this->insertFiles($projectId, 'templates', array($template));
            }
            foreach ($servers as $server) {
                $this->insertServer($projectId, $server);
            }
            
            // Set to off the flag that indicates the project has been generated from a theme
            $GLOBALS['fromTheme'] = null;
        }
        return true;
    }

    private function insertFiles($parentId, $xFolderName, $files)
    {
        $ret = array();
        if (count($files) == 0) {
            return $ret;
        }
        $project = new Node($parentId);
        $xFolderId = $project->GetChildByName($xFolderName);
        if (empty($xFolderId)) {
            Logger::error($xFolderName . ' folder not found');
            return false;
        }
        $io = new BaseIO();
        foreach ($files as $file) {
            $nodeType = new NodeType();
            $nodeType->SetByName($file->nodetypename);
            $idNodeType = $nodeType->get('IdNodeType') > 0 ? $nodeType->get('IdNodeType') : NULL;
            $data = array(
                'NODETYPENAME' => $file->nodetypename,
                'NAME' => $file->basename,
                'NODETYPE' => $idNodeType,
                'PARENTID' => $xFolderId,
                'CHILDRENS' => array(
                    array(
                        'NODETYPENAME' => 'PATH',
                        'SRC' => $file->path
                    )
                )
            );
            $id = $io->build($data);
            $this->specialCase($id, $file);
            if ($id > 0) {
                $ret[$file->filename] = $id;
                Logger::info('Importing ' . $file->basename);
            } else {
                Logger::error('Error (' . $id . ') importing ' . $file->basename);
                Logger::error(print_r($io->messages->messages, true));
            }
        }
        if (count($ret) == 0) {
            $ret = false;
        }
        return $ret;
    }

    /**
     * Process file if its a special one
     */
    private function specialCase($idNode, & $file)
    {
        $node = new Node($idNode);
        if ($file->basename == 'docxap.xsl') {
            $docxapContent = $node->GetContent();
            $urlPath = App::getValue('UrlHost') . App::getValue('UrlRoot');
            $docxapContent = str_replace('{URL_PATH}', $urlPath, $docxapContent);
            $docxapContent = str_replace('{PROJECT_NAME}', $this->name, $docxapContent);
            $node->SetContent($docxapContent);
        }
    }

    /**
     * Create a Server Node and all the descendant: xmldocument, ximlet, images, css and common
     *
     * @param int $projectId Ximdex id for node project
     * @param  Loader_Server $server Object to create the server
     * @return int Server id.
     */
    private function insertServer($projectId, $server)
    {
        $channels = $this->request->getParam('channels_listed');
        $nodeType = new NodeType();
        $nodeType->SetByName($server->nodetypename);
        $idNodeType = ($nodeType->get('IdNodeType') > 0) ? $nodeType->get('IdNodeType') : NULL;
        $data = array(
            'NODETYPENAME' => $server->nodetypename,
            'NAME' => $server->name,
            'NODETYPE' => $idNodeType,
            'PARENTID' => $projectId
        );
        $io = new BaseIO();
        $serverId = $io->build($data);
        if ($serverId < 1) {
            return false;
        }
        $server->serverid = $serverId;
        $server->url = preg_replace('/\{URL_ROOT\}/', App::getValue('UrlHost') . App::getValue('UrlRoot'), $server->url);
        $server->initialDirectory = preg_replace('/\{XIMDEX_ROOT_PATH\}/', XIMDEX_ROOT_PATH, $server->initialDirectory);
        $nodeServer = new Node($serverId);
        $physicalServerId = $nodeServer->class->addPhysicalServer($server->protocol, $server->login, $server->password, $server->host
            , $server->port, $server->url, $server->initialDirectory, $server->overrideLocalPaths, $server->enabled, $server->previsual
            , $server->description
        );
        foreach ($channels as $ch) {
            $nodeServer->class->AddChannel($physicalServerId, $ch);
        }
        Logger::info('Server creation O.K.');

        // Common
        $arrayCommon = $server->getCommon();
        $this->createResourceByFolder($server, 'common', 'CommonFolder', $arrayCommon);
        $arrayTemplates = $server->getTemplates();
        foreach ($arrayTemplates as $template) {
            $this->insertFiles($serverId, 'templates', array($template));
        }
        
        // Images
        $arrayImages = $server->getImages();
        $this->createResourceByFolder($server, 'images', 'ImagesFolder', $arrayImages);

        // CSS
        $arrayCss = $server->getCSS();
        $this->createResourceByFolder($server, 'css', 'CssFolder', $arrayCss);

        // Document
        $docs = $server->getXimdocs();
        $this->insertDocs($server->serverid, $docs);

        // Ximlet
        $let = $server->getXimlet();
        $this->insertDocs($server->serverid, $let, true);
        return $serverId;
    }

    private function createResourceByFolder($server, $rootFolderName, $rootFolderNodeType, $arrayXimFiles)
    {
        $this->server = $server->serverid;
        $nodeServer = new Node($server->serverid);
        $rootFolderId = $nodeServer->GetChildByName($rootFolderName);
        $this->$rootFolderName = $rootFolderId;
        $newFolderNodeType = new NodeType();
        $newFolderNodeType->SetByName($rootFolderNodeType);
        $this->createResource($rootFolderId, $arrayXimFiles, $newFolderNodeType->GetID());
    }

    private function createResource($rootFolderId, $arrayXimFiles, $idFolderNodeType)
    {
        $createdFolders = $this->createFolders($rootFolderId, array_keys($arrayXimFiles), $idFolderNodeType);
        foreach ($arrayXimFiles as $filePath => $ximFileObject) {
            $lastSlash = strrpos($filePath, '/');
            $folderPath = substr($filePath, 0, $lastSlash + 1);
            if ($createdFolders[$folderPath]) {
                $folderNode = new Node($createdFolders[$folderPath]);
                $folderName = $folderNode->GetNodeName();
                $idParent = $folderNode->get('IdParent');
                $this->insertFiles($idParent, $folderName, array($ximFileObject));
            } else {
                
                //Any error message here
            }
        }
    }

    private function createFolders($rootFolderId, $arrayNames, $idNodeType)
    {
        $createdFolders = array('/' => $rootFolderId);
        foreach ($arrayNames as $name) {
            $folderId = $rootFolderId;
            $arrayNews = explode('/', $name);
            $currentFolderName = '/';
            for ($i = 1; $i < count($arrayNews) - 1; $i++) {
                $currentFolderName .= $arrayNews[$i] . '/';
                if (!array_key_exists($currentFolderName, $createdFolders)) {
                    $folder = new Node();
                    $idFolder = $folder->CreateNode($arrayNews[$i], $folderId, $idNodeType, null);
                    $createdFolders[$currentFolderName] = $idFolder;
                }
                $folderId = $createdFolders[$currentFolderName];
            }
        }
        return $createdFolders;
    }

    private function insertDocs($parentId, $files, $isXimlet = false)
    {
        if ($isXimlet) {
            $xFolderName = 'ximlet';
            $nodeTypeName = 'XIMLET';
            $nodeTypeContainer = 'XIMLETCONTAINER';
        } else {
            $xFolderName = 'documents';
            $nodeTypeName = 'XMLDOCUMENT';
            $nodeTypeContainer = 'XMLCONTAINER';
        }
        $ret = array();
        if (count($files) == 0) {
            return $ret;
        }
        $project = new Node($parentId);
        $xFolderId = $project->GetChildByName($xFolderName);
        if (empty($xFolderId)) {
            Module::log(Module::ERROR, $xFolderName . ' folder not found');
            return false;
        }
        $nodeType = new NodeType();
        $nodeType->SetByName($nodeTypeName);
        $idNodeType = $nodeType->get('IdNodeType') > 0 ? $nodeType->get('IdNodeType') : NULL;
        $io = new BaseIO();
        $languageObject = new Language();
        foreach ($files as $file) {
            $idSchema = $this->schemas[$file->templatename];
            $file->channel = $file->channel == '{?}' ? $this->channels : $file->channel;
            if (!$languageObject->LanguageEnabled($file->language)) {
                $file->language = $languageObject->getList();
            } else {
                $file->language = array($file->language);
            }
            $data = array(
                'NODETYPENAME' => $nodeTypeContainer,
                'NAME' => $file->name,
                'PARENTID' => $xFolderId,
                'CHILDRENS' => array(
                    array(
                        'NODETYPENAME' => 'VISUALTEMPLATE',
                        'ID' => $idSchema
                    )
                )
            );
            $containerId = $io->build($data);
            if (!($containerId > 0)) {
                Module::log(Module::ERROR, 'document ' . $file->name . ' couldn\'t be created (' . $containerId . ')');
                continue;
            }
            $data = array(
                'NODETYPENAME' => $nodeTypeName,
                'NAME' => $file->name,
                'NODETYPE' => $idNodeType,
                'PARENTID' => $containerId,
                'CHILDRENS' => array(
                    array('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $idSchema),
                    array('NODETYPENAME' => 'PATH', 'SRC' => $file->getPath())
                )
            );
            $formChannels = array();
            foreach ($file->channel as $idChannel) {
                $formChannels[] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $idChannel);
            }
            if (!empty($formChannels)) {
                foreach ($formChannels as $channel) {
                    $data['CHILDRENS'][] = $channel;
                }
            }
            $dataTmp = $data;
            foreach ($file->language as $language) {
                $data = $dataTmp;
                $data['CHILDRENS'][] = array('NODETYPENAME' => 'LANGUAGE', 'ID' => $language);
                $docId = $io->build($data);
                $ret[] = $docId;
            }
        }
        if (count($ret) == 0) {
            $ret = false;
        }
        return $ret;
    }

    public function addSectionNode()
    {
        $nodeID = $this->request->getParam('nodeid');
        $name = $this->request->getParam('name');
        $langidlst = $this->request->getParam('langidlst');
        $namelst = $this->request->getParam('namelst');
        $aliasLangArray = array_combine($langidlst, $namelst);
        $nodeType = $this->GetTypeOfNewNode($nodeID);
        $nodeTypeName = $nodeType['name'];
        $friendlyName = $nodeType['friendlyName'];
        $nodeType = new NodeType();
        $nodeType->SetByName($nodeTypeName);
        $folder = new Node();
        $idFolder = $folder->CreateNode($name, $nodeID, $nodeType->GetID(), null);
        if ($idFolder > 0) {
            foreach ($aliasLangArray as $langID => $longName) {
                $folder->SetAliasForLang($langID, $longName);
                if ($folder->numErr) {
                    break;
                }
            }
        }
        $this->reloadNode($nodeID);
        $arrValores = array('nodeId' => $nodeID,
            'friendlyName' => $friendlyName,
            'ret' => $idFolder > 0 ? 'true' : 'false',
            'name' => $name,
            'msgError' => $folder->msgErr);
        $this->render($arrValores);
    }
}
