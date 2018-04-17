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

use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Models\ProgrammingLanguage;

class Action_createchannel extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
		$progLanguage = new ProgrammingLanguage();
		$codeLanguages = $progLanguage->find();
		$this->addJs('/actions/createchannel/resources/js/index.js');
		$values = array(
			'id_node' => $idNode,
		    'code_languages' => $codeLanguages,
		    'node_Type' => $node->nodeType->GetName(),
			'go_method' => 'createchannel');
		$this->render($values, null, 'default-3.0.tpl');
    }

	public function createchannel()
	{
		$idNode = $this->request->getParam('id_node');
		$outputType = $this->request->getParam('output_type');
		if (!$outputType) {
		    $this->messages->add('No output type selected', MSG_TYPE_WARNING);
		    $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
		    $this->sendJSON($values);
		    return false;
		}
	    $renderType = $this->request->getParam('render_type');
        if (!$renderType) {
            $this->messages->add('No render type selected', MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
            $this->sendJSON($values);
            return false;
        }
        if (!isset(Channel::RENDER_TYPES[$renderType])) {
            $this->messages->add('There is not a render type for ' . $renderType, MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
            $this->sendJSON($values);
            return false;
        }
        $codeLanguage = $this->request->getParam('language');
        if (!$codeLanguage) {
            $this->messages->add('No code language selected', MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
            $this->sendJSON($values);
            return false;
        }
        $name = $this->request->getParam('name');
        $extension = $this->request->getParam('extension');
        $description = $this->request->getParam('description');
        $renderMode = $this->request->getParam('rendermode');
        $nodeType = new NodeType();
        $nodeType->SetByName('Channel');
        // $complexName = sprintf("%s.%s", $name, $extension);
        
        // Control uniqueness of tupla, channel, format
        $node = new Node();
        $result = $node->CreateNode(strtoupper($name), $idNode, $nodeType->get('IdNodeType'), NULL, $name, $extension, NULL, $description, ''
                , $renderMode, $outputType, $renderType, $codeLanguage);
        if ($result > 0) {
            $node->messages->add(_('Channel has been succesfully inserted'), MSG_TYPE_NOTICE);
        }
        $values = array('messages' => $node->messages->messages, 'idNode' => $idNode);
        $this->sendJSON($values);
	}
}