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



class Action_expiresection extends ActionAbstract {
   // Main method: shows initial form
    function index () {
		$idNode = $this->request->getParam('nodeid');
		$node = new Node($idNode);

		$sectionName = $node->get('Name');
		$values = array('section_name' => $sectionName,
						'go_method' => 'result');

		$this->render($values, '', 'default-3.0.tpl');
    }

    function result() {
    	$idNode = $this->request->getParam('nodeid');
    	$isRecursive = $this->request->getParam('is_recursive');

    	$node = new Node($idNode);
    	$nodeName = $node->get('Name');

    	$this->_DoPublicate($idNode, $isRecursive);
    	$this->messages->add(sprintf(_("Section <strong>%s</strong> has been successfully expired"), $nodeName), MSG_TYPE_NOTICE);
        $values = array(
            'messages' => $this->messages->messages,
        );
    	$this->sendJSON($values);


    }

	function _DoPublicate($idNode, $recursive) {
		$node = new Node($idNode);
		$childList = $node->GetChildren();

		if ($childList) {
			foreach($childList as $child) {
				$childNode = new Node($child);
				//It adds nodes of children except they are of section type and they have not been specified like recursives 
				if($recursive || ( $childNode->nodeType->GetName() != "Section") ) {
					$childList = array_merge($childList, $childNode->TraverseTree());
				}
			}

			foreach($childList as $nodeID) {
				$sync = new Synchronizer($nodeID);
				$sync->DeleteFramesFromNow($nodeID);
			}
		}
	}
}
?>
