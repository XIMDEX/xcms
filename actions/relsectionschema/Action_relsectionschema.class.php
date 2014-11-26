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

class Action_relsectionschema extends ActionAbstract {

	function index() {
      		$idNode	= (int) $this->request->getParam("nodeid");
		$actionID= (int) $this->request->getParam("actionid");

		$templates = array('VisualTemplate', 'RngVisualTemplate');
		$nodeType = new NodeType();

		foreach ($templates as $templateName) {
			$nodeType->SetByName($templateName);
			$idNodeType = $nodeType->get('IdNodeType');

			if ($idNodeType > 0) {
				$list[] = array('id' => $idNodeType, 'name' => $templateName);
			}
		}
		$list[] = array('id' => 'all', 'name' => 'VisualTemplate y RngVisualTemplate');

		$node = new Node($idNode);
		($idNode==$node->getProject())?$type="p":$type="s";
		$defaultSchema = $node->getProperty('DefaultSchema');
		if (count($defaultSchema) > 1) {
			$defaultSchema = 'all';
		} else {
			$defaultSchema = $defaultSchema[0]; 
		}
		
		$values = array(
			'title' => _('Asociar schema a section'),
			'label' => _('Tipo de plantilla'),
			'type' => $type,
			'id_node' => $idNode,
			'nodeURL' => \App::getValue( 'UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}",
			'go_method' => 'set_property',
			'list' => $list,
			'default_schema' => $defaultSchema
		);

		$this->render($values, '', 'default-3.0.tpl');
	}

	function set_property() {
      	$idNode		= (int) $this->request->getParam('nodeid');
      	$idAction = $this->request->getParam('actionid');
		$selected = $this->request->getParam('schema');

		if ($selected == 'all') {
			$selected = array(5045, 5078);
		}
		$node = new Node($idNode);
		$node->deleteProperty('DefaultSchema');
		
		$result = $node->setProperty('DefaultSchema', $selected);

		if (!$result) {
			$this->messages->add(_("Association has been successfully performed."), MSG_TYPE_NOTICE);
		} else {
			$this->messages->add(_("Error while associating."), MSG_TYPE_NOTICE);
			$this->messages->mergeMessages($node->messages);
		}

		$values = array(
				'messages' => $this->messages->messages,
				'id_node' => $idNode,
				"nodeURL" => \App::getValue( 'UrlRoot')."/xmd/loadaction.php?actionid=$idAction&nodeid={$idNode}",
				);

		$this->render($values);

	}

}
?>