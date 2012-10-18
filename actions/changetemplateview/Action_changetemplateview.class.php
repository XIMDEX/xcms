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



class Action_changetemplateview extends ActionAbstract {
   // Main method: it shows initial form
    function index() {
      	$idNode		= (int) $this->request->getParam("nodeid");
      	$actionID	= (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");

		$node = new Node($nodeID);
		$templates = $node->getTemplates();
		
		$_templates = array();
		if(count($templates)>0) {
			foreach($templates as $templateID)
			{
				$node->SetID($templateID);
				$_templates[] = array("id" => $node->GetID(), "name" =>$node->GetNodeName() );
			}
		}

		$values = array(
			'title' => _('Change template view'),
			'question' => _('Are you sure you want to create a new node?'),
			'no_template' => _('There are no templates to select'),
			'button' => _('Create'),
			'id_node' => $idNode,
			'params' => $params,
			'action' => $action,
			'templates' => $_templates,
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}",
			"go_method" => "save",
		);

		$this->render($values);
    }

	function save() {

      	$idNode		= (int) $this->request->getParam("nodeid");
      	$actionID	= (int) $this->request->getParam("actionid");
		$templateID = (int) $this->request->getParam("templateid");
		$params = $this->request->getParam("params");

		$node = new Node($idNode);
		$docList = array();
		$containerList = $node->GetChildren();

		if(count($containerList)>0 && $templateID >= 0) {
			foreach($containerList as $containerID) {
				$node->SetID($containerID);
				$docList = array_merge($docList, $node->GetChildren());
			}

			$doc = new StructuredDocument();
			if(count($docList)>0) {
				foreach($docList as $docID) {
					$doc->SetID($docID);
					$doc->SetDocumentType($templateID);
				}
			}
			$this->messages->add(_("View templates have been successfully updated"), MSG_TYPE_NOTICE);
		}else {
			$this->messages->add(_("Template to change was not found"), MSG_TYPE_ERROR);
		}

		
		$jsFiles = array(Config::getValue('UrlRoot') . '/xmd/template/Smarty/helper/reloadNode.tpl');
		$onLoadFunctions = "reloadNode($idNode);";

		$values = array(
			'messages' => $this->messages->messages,
			'id_node' => $idNode,
			'params' => $params,
			'action' => $action,
			'templates' => $_templates,
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}",
			'js_files' => $jsFiles,
			'on_load_functions' => $onLoadFunctions
		);

		$this->render($values);

    }
}
?>
