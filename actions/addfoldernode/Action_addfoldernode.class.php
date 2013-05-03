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




class Action_addfoldernode extends ActionAbstract {
	// Main Method: shows the initial form
    function index() {
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

	$template = 'index';

	// If project

	if ($nodeType['name'] == 'Project') {

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
		$template = 'addProject';
	}
	$this->render($values, $template, 'default-3.0.tpl');
    }

	// Fuction AddNode adds the folder node in fuction of the kind "add"
	function addNode() {
		$nodeID = $this->request->getParam("nodeid");
		$name = $this->request->getParam("name");
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
			if(!empty($channels) && is_array($channels) )
				$node->setProperty('channel', array_keys($channels));

			if(!empty($languages) && is_array($languages) )
				$node->setProperty('language', array_keys($languages));
		}

		if ($idFolder > 0) {
			$this->messages->add(sprintf(_('%s has been successfully created'), $name), MSG_TYPE_NOTICE);
			$this->reloadNode($nodeID);
		} else {
			$this->messages->add(sprintf(_('The operation has failed: %s'), $folder->msgErr), MSG_TYPE_ERROR);
		}

		$arrValores = array ('messages' => $this->messages->messages);
		$this->render($arrValores);
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
//		$ret = $folder->numErr;

	    if ($idFolder > 0) {
	    	foreach ($aliasLangArray as $langID => $longName) {
	        	$folder->SetAliasForLang($langID, $longName);
	            if ($folder->numErr)
	            	break;
	        }
	    }

//		$ret = ($ret) ? "false" : "true";
		$this->reloadNode($nodeID);

		$arrValores = array ("nodeId" => $nodeID,
							"friendlyName" => $friendlyName,
							"ret" => $idFolder > 0 ? 'true' : 'false',
							"name" => $name,
							"msgError" => $folder->msgErr);
		$this->render($arrValores);
	}

	// Identifying node type to allocate its propierties
	function GetTypeOfNewNode($nodeID) {

		// can it be made a query which ask what a foldernode can contain?
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
