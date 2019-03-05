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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\NodeTypes\XsltNode;

class Action_deletetemplates extends ActionAbstract
{
    public function index()
    {
 		$idNode	= (int) $this->request->getParam('nodeid');
		$folder = new Node($idNode);
		$nodes_templates = $folder->GetChildren();
		$templates = array();
		if(! empty($nodes_templates)) {
			foreach ($nodes_templates as $id => $_template) {
				$node_template = new Node($_template);
				$templates[$id]['Id'] = $_template;
				$templates[$id]['Name'] = $node_template->get('Name');
			}
		}
 		$values = array(
			'id_node' => $idNode,
			'templates' => $templates,
 		    'nodeTypeID' => $folder->nodeType->getID(),
 		    'node_Type' => $folder->nodeType->GetName(),
			'go_method' => 'delete'
		);
		$this->addJs('/actions/deletetemplates/js/delete_templates.js');
		$this->addCSS('/actions/deletetemplates/css/style.css');
		$this->render($values, 'index', 'default-3.0.tpl');
    }

	public function delete()
	{
		$templates = $this->request->getParam('templates');
 		$idNode	= (int) $this->request->getParam('nodeid');
 		$node = new Node($idNode);
		$new_templates = array();
		if (! empty($templates)) {
			$i = 0;
			foreach($templates as $_template) {
				$templateNode = new Node($_template);
				$new_templates[$i]['Id'] = $_template;
				$new_templates[$i]['Name'] = $templateNode->get('Name');
				$new_templates[$i]['Result'] = $templateNode->delete();
				$i++;
			}
			
			// Update the templates_include.xsl files
			$xsltNode = new XsltNode($node);
			if ($xsltNode->reload_templates_include(new Node($node->getProject())) === false) {
			    $this->messages->mergeMessages($xsltNode->messages);
			}
			$this->messages->add(_('All nodes were successfully deleted'), MSG_TYPE_NOTICE);
		} else {
		    $this->messages->add(_('No templates selected'), MSG_TYPE_WARNING);
		}
		$values = array(
			'messages' => $this->messages->messages,
			'action_with_no_return' => true,
			'parentID' => $node->getID()
		);
		$this->sendJSON($values);
	}
}
