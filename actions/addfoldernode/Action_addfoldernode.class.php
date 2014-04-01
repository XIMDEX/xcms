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


ModulesManager::file('/actions/addfoldernode/model/ProjectTemplate.class.php');
ModulesManager::file('/actions/addfoldernode/conf/addfoldernode.conf');

class Action_addfoldernode extends ActionAbstract {
	

	private $channels;
	private $languages;
	/**
	 * Main Method: shows the initial form	 
	 */
	function index() {

		//Getting node info from params.
		$nodeID = $this->request->getParam("nodeid");
    	$nodeType = $this->GetTypeOfNewNode ($nodeID);
    	$friendlyName = (!empty($nodeType["friendlyName"]))?  $nodeType["friendlyName"] : $nodeType["name"];

    	$go_method = ($nodeType["name"] == "Section") ? "addSectionNode" : "addNode";

		$this->request->setParam("go_method", $go_method);
		$this->request->setParam("friendlyName", $friendlyName);

		$values = array(
			'go_method' => 'addNode',
			'nodeID' => $nodeID
			);

		if($nodeType['name'] == 'Project'){
			$this->loadNewProjectForm($values);			
		}else{
			$this->render($values, "index", 'default-3.0.tpl');
		}
		
	}

    /**
     * Load the creation form for a new project     
     */
    private function loadNewProjectForm($values){
    	$chann = new Channel();
		$channs = $chann->find();
	
		if (!is_null($channs)) {
				foreach ($channs as $channData) {
					$channels[] = array('id' => $channData['IdChannel'], 'name' => $channData['Name']);
				}
		} else {
				$channels = NULL;
		}

		$lang = new Language();
		$langs = $lang->find();

		if (!is_null($langs)) {
			foreach ($langs as $langData) {
				$languages[] = array('id' => $langData['IdLanguage'], 'name' => $langData['Name']);
			}
		} else {
			$languages = NULL;
		}

		$values['langs'] = $languages;
		$values['channels'] = $channels;
		//Load projects
		
		$themes = ProjectTemplate::getAllProjectTemplates();
		$idNode = $this->request->getParam("nodeid");
		$nodeProjectRoot = new Node($idNode);        
       	$cssFolder = "/actions/addfoldernode/resources/css/";
       	$this->addCss($cssFolder . "style.css");

       	$jsFolder = "/actions/addfoldernode/resources/js/";
       	$this->addJs($jsFolder . "init.js");
        
        $arrayTheme = array();
        foreach ($themes as $theme ) {
            $themeDescription["name"] = $theme->__get("name");
            $themeDescription["title"] = $theme->__get("title");
            $themeDescription["description"] = $theme->__get("description");
            $themeDescription["configurable"] = $theme->configurable=="1"? true: false;

            $arrayTheme[] = $themeDescription;
        }

        $values["themes"] = $arrayTheme;
    	
        $template = "index";

		$this->render($values, "addProject", 'default-3.0.tpl');
    }	

	function addNode() {
		$nodeID = $this->request->getParam("nodeid");
		$name = $this->request->getParam("name");
        $this->name = $name;
		$channels = $this->request->getParam('channels_listed');	
		$languages = $this->request->getParam('langs_listed');

		$nodeType = $this->GetTypeOfNewNode($nodeID);
		$nodeTypeName = $nodeType["name"];

		$nodeType = new NodeType();
		$nodeType->SetByName($nodeTypeName);

		$folder = new Node();
		$idFolder = $folder->CreateNode($name, $nodeID, $nodeType->GetID(), null);

		// Adding channel and language properties (if project)
		if ($idFolder > 0 && $nodeTypeName == 'Project') {
			$node = new Node($idFolder);
			if(!empty($channels) && is_array($channels) ){
				$node->setProperty('channel', array_keys($channels));
				$this->channels = $channels;
			}

			if(!empty($languages) && is_array($languages) ){
				$node->setProperty('language', array_keys($languages));
				$this->languages = $languages;
			}
			$this->createProjectNodes($idFolder);
		}

		if ($idFolder > 0) {
			$this->messages->add(sprintf(_('%s has been successfully created'), $name), MSG_TYPE_NOTICE);
			//$this->reloadNode($nodeID);
		} else {
			$this->messages->add(sprintf(_('The operation has failed: %s'), $folder->msgErr), MSG_TYPE_ERROR);
		}

		$arrValores = array ('messages' => $this->messages->messages, 'parentID' => $nodeID);
		$this->sendJSON($arrValores);
	}

	function addSectionNode () {
		$nodeID = $this->request->getParam("nodeid");
		$name = $this->request->getParam("name");

        	$langidlst =  $this->request->getParam("langidlst");
	    	$namelst =   $this->request->getParam("namelst");
		$aliasLangArray = array_combine($langidlst, $namelst);

		$nodeType = $this->GetTypeOfNewNode($nodeID);
		$nodeTypeName = $nodeType["name"];
		$friendlyName = $nodeType["friendlyName"];

		$nodeType = new NodeType();
		$nodeType->SetByName($nodeTypeName);

		$folder = new Node();
		$idFolder = $folder->CreateNode($name, $nodeID, $nodeType->GetID(), null);

	    	if ($idFolder > 0) {
	    		foreach ($aliasLangArray as $langID => $longName) {
	        		$folder->SetAliasForLang($langID, $longName);
	            		if ($folder->numErr)
	            			break;
	        	}
	    	}

		$this->reloadNode($nodeID);

		$arrValores = array ("nodeId" => $nodeID,
		  		"friendlyName" => $friendlyName,
				"ret" => $idFolder > 0 ? 'true' : 'false',
				"name" => $name,
				"msgError" => $folder->msgErr);
		$this->render($arrValores);
	}

	private function createProjectNodes($projectId) {

        $theme = $this->request->getParam("theme");
        if ($theme) {
            $projectTemplate = new ProjectTemplate($theme);

            $servers = $projectTemplate->getServers();
            $schemas = $projectTemplate->getSchemes();
            $templates = $projectTemplate->getTemplates();

            foreach ($schemas as $schema) {
                $this->schemas = $this->insertFiles($projectId, Config::getValue("SchemasDirName"), array($schema));
            }

            foreach ($templates as $template) {
                $this->insertFiles($projectId, "templates", array($template));
            }

            foreach ($servers as $server) {
                $this->insertServer($projectId, $server);
            }
        }
    }

    /**
     * Create a Server Node and all the descendant: xmldocument, ximlet, images, css and common 
     *
     * @param int $projectId Ximdex id for node project
     * @param  Loader_Server $server Object to create the server
     * @return int Server id.
     */
    private function insertServer($projectId, $server) {

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
        $server->url = preg_replace('/\{URL_ROOT\}/', Config::GetValue('UrlRoot'), $server->url);
        $server->initialDirectory = preg_replace('/\{XIMDEX_ROOT_PATH\}/', XIMDEX_ROOT_PATH, $server->initialDirectory);

        $nodeServer = new Node($serverId);
        $physicalServerId = $nodeServer->class->AddPhysicalServer(
                $server->protocol, $server->login, $server->password, $server->host, $server->port, $server->url, $server->initialDirectory, $server->overrideLocalPaths, $server->enabled, $server->previsual, $server->description, $server->isServerOTF
        );

        $nodeServer->class->AddChannel($physicalServerId, $this->project->channel);
        Module::log(Module::SUCCESS, "Server creation O.K.");        
        
        // common
        $arrayCommon = $server->getCommon();

        $this->createResourceByFolder($server, "common", "CommonFolder", $arrayCommon);


        
        $arrayTemplates = $server->getTemplates();
        foreach($arrayTemplates as $template){
            $this->insertFiles($serverId, "templates", array($template));
        }       

        //images
        $arrayImages = $server->getImages();        
        $this->createResourceByFolder($server, "images", "ImagesFolder", $arrayImages);

        //Css
        $arrayCss = $server->getCSS();
        $this->createResourceByFolder($server, "css", "CssFolder", $arrayCss);
        

        // document
        $docs = $server->getXimdocs();
        $ret = $this->insertDocs($server->serverid, $docs);

        // ximlet
        $let = $server->getXimlet();
        $ret = $this->insertDocs($server->serverid, $let, true);
        
        return $serverId;
    }

    private function createResourceByFolder($server, $rootFolderName, $rootFolderNodeType, $arrayXimFiles){

        $this->server=$server->serverid;
        $nodeServer = new Node($server->serverid);
        $rootFolderId = $nodeServer->GetChildByName($rootFolderName);
        $this->$rootFolderName = $rootFolderId;
        $newFolderNodeType = new NodeType();
        $newFolderNodeType->SetByName($rootFolderNodeType);        
        $this->createResource($rootFolderId, $arrayXimFiles, $newFolderNodeType->GetID());

    }

    private function createResource($rootFolderId, $arrayXimFiles, $idFolderNodeType){

        $createdFolders = $this->createFolders($rootFolderId, array_keys($arrayXimFiles),$idFolderNodeType);
        foreach ($arrayXimFiles as $filePath => $ximFileObject) {
            $lastSlash = strrpos($filePath, "/");
            $folderPath = substr($filePath, 0, $lastSlash+1);
            if ($createdFolders[$folderPath]){

                $folderNode = new Node($createdFolders[$folderPath]);
                $folderName = $folderNode->GetNodeName();
                $idParent = $folderNode->get("IdParent");
                $this->insertFiles($idParent, $folderName, array($ximFileObject));
            }else{
                //Any error message here
            }            
            
        }
    }

    private function createFolders($rootFolderId, $arrayNames, $idNodeType){
        $createdFolders = array("/" => $rootFolderId);
        foreach ($arrayNames as $name) {
            $folderId = $rootFolderId;
            $arrayNews = explode("/", $name);
            $currentFolderName = "/";
            for($i = 1; $i < count($arrayNews)-1; $i++){
                $currentFolderName.= $arrayNews[$i]."/";
                if (!array_key_exists($currentFolderName, $createdFolders)){
                    $folder = new Node();
                    $idFolder = $folder->CreateNode($arrayNews[$i], $folderId, $idNodeType, null);
                    $createdFolders[$currentFolderName] = $idFolder;
                }
                $folderId = $createdFolders[$currentFolderName];
            }

        }

        return $createdFolders;
    }

    function insertDocs($parentId, $files,$isXimlet=false) {

        if ($isXimlet){
            $xFolderName = 'ximlet';
            $nodeTypeName = 'XIMLET';
            $nodeTypeContainer = "XIMLETCONTAINER";
        }else{
            $xFolderName = 'documents';
            $nodeTypeName = 'XMLDOCUMENT';
            $nodeTypeContainer = "XMLCONTAINER";
        }
        $ret = array();
        if (count($files) == 0)
            return $ret;

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
            $file->language = $file->language == '{?}' ? $languageObject->getList() : $file->language;


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
                Module::log(Module::ERROR, "document " . $file->name . " couldn't be created ($containerId)");
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
                error_log("Canal a añadir: $idChannel");
                $formChannels[] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $idChannel);
            }
            if(!empty($formChannels ) ) {
                foreach ($formChannels as $channel) {
                    $data['CHILDRENS'][] = $channel;
                }
            }

			$dataTmp = $data;
    			foreach ($file->language as $language) {
				$data = $dataTmp;
				$data["CHILDRENS"][]=array('NODETYPENAME' => 'LANGUAGE', 'ID' => $language);
				$docId = $io->build($data);
                error_log("DEBUG Creado $docId");
                $ret[] = $docId;
			}
            
		}
        if (count($ret) == 0)
            $ret = false;
        return $ret;
    }

    private function insertFiles($parentId, $xFolderName, $files) {

        $ret = array();
        if (count($files) == 0)
            return $ret;

        $project = new Node($parentId);
        $xFolderId = $project->GetChildByName($xFolderName);

        if (empty($xFolderId)) {
            Module::log(Module::ERROR, $xFolderName . ' folder not found');
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
                Module::log(Module::SUCCESS, "Importing " . $file->basename);
            } else {
                Module::log(Module::ERROR, "Error ($id) importing " . $file->basename);
                Module::log(Module::ERROR, print_r($io->messages->messages, true));
            }
        }

        if (count($ret) == 0)
            $ret = false;
        return $ret;
    }

    /**
    *Process file if its a special one.
    */
    private function specialCase($idNode, &$file){

        $node = new Node($idNode);
        if ($file->basename == "docxap.xsl"){
            $docxapContent = $node->GetContent();
            $urlPath = Config::GetValue("UrlRoot");
            $docxapContent = str_replace("{URL_PATH}", $urlPath, $docxapContent);
            $docxapContent = str_replace("{PROJECT_NAME}", $this->name, $docxapContent);
            $node->SetContent($docxapContent);
        }
    }


	function GetTypeOfNewNode($nodeID) {
//TODO: change this switch sentence for a query to the NodeAllowedContents table to check what subfolders can contain.
		$node = new Node($nodeID);
		if (!$node->get('IdNode') > 0) {
			return null;
		}
		$nodeTypeName = $node->nodeType->GetName();

		switch ($nodeTypeName) {
			case "Projects":
				$newNodeTypeName ="Project";
				$friendlyName = "Project";
			break;

			case "Project":
				$newNodeTypeName ="Server";
				$friendlyName = "Server";
			break;

			case "Server":
				$newNodeTypeName ="Section";
				$friendlyName = "Section";
			break;

			case "Section":
				$newNodeTypeName ="Section";
				$friendlyName = "Section";
			break;

			case "ImagesRootFolder":
				$newNodeTypeName ="ImagesFolder";
				$friendlyName = "Image folder";
			break;

			case "ImagesFolder":
				$newNodeTypeName ="ImagesFolder";
				$friendlyName = "Image folder";
			break;

			case "XmlRootFolder":
				$newNodeTypeName ="XmlFolder";
				$friendlyName = "XML Folder";
			break;

			case "XmlFolder":
				$newNodeTypeName ="XmlFolder";
				$friendlyName = "XML Folder";
			break;

			case "ImportRootFolder":
				$newNodeTypeName ="ImportFolder";
				$friendlyName = "XimCLUDE folder";
			break;

			case "ImportFolder":
				$newNodeTypeName ="ImportFolder";
				$friendlyName = "XimCLUDE folder";
			break;

			case "CommonRootFolder":
				$newNodeTypeName ="CommonFolder";
				$friendlyName = "Common folder";
			break;

			case "CommonFolder":
				$newNodeTypeName ="CommonFolder";
				$friendlyName = "Common folder";
			break;

			case "CssRootFolder":
				$newNodeTypeName ="CssFolder";
				$friendlyName = "CSS folder";
			break;

			case "CssFolder":
				$newNodeTypeName ="CssFolder";
				$friendlyName = "CSS folder";
			break;

			case "TemplatesRootFolder":
				$newNodeTypeName ="TemplatesRootFolder";
				$friendlyName = "Template folder";
			break;

			case "TemplatesFolder": case "TemplateViewFolder":
				$newNodeTypeName ="TemplateViewFolder";
				$friendlyName = "Template folder";
			break;

			case "LinkManager":
				$newNodeTypeName ="LinkFolder";
				$friendlyName = "Link folder";
			break;

			case "LinkFolder":
				$newNodeTypeName ="LinkFolder";
				$friendlyName = "Link folder";
			break;

			case "XimletRootFolder":
				$newNodeTypeName ="XimletFolder";
				$friendlyName = "Ximlets folder";
			break;

			case "XimletFolder":
				$newNodeTypeName ="XimletFolder";
				$friendlyName = "Ximlets folder";
			break;

			case "XimNewsSection":
				$newNodeTypeName ="XimNewsNews";
				$friendlyName =  "XimNEWS new folder";
			break;
			case "OpenDataSection":
				$newNodeTypeName ="OpenDataDataset";
				$friendlyName =  "Dataset";
			break;

			default:
				// Log to user.
				return null;
		}

		$a["name"] = $newNodeTypeName;
		$a["friendlyName"] = $friendlyName;

		return $a;
	}
}
?>
