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

ModulesManager::file('/inc/model/Links.inc');

class Action_modifylink extends ActionAbstract {
   // Main method: shows initial form
    function index () {
    	$idNode = $this->request->getParam('nodeid');
    	
		$link = new Link($idNode);
		$node = new Node($idNode);
		if (!(($link->get('IdLink') > 0) && ($node->get('IdNode') > 0))) {
			$this->messages->add(_('Link could not be successfully loaded, contact with your administrator'), MSG_TYPE_ERROR);
			XMD_Log::error("Error while loading link" . $idNode);
			$this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
			return false;
		}
		
		$this->addJs('/actions/createlink/resources/js/index.js');
		
		$values = array(
					'name' => $node->get('Name'),
					'url' => $link->get('Url'),
					'description' => $node->get('Description'),
					'go_method' => 'modifylink');
		
		$this->render($values, null, 'default-3.0.tpl');
    }

	function modifylink() {
		
    	$idNode = $this->request->getParam('nodeid');
    	$validated = $this->request->getParam('validated');
    	
    	if (!$validated) {
	    	$link = new Link();
	    	$links = $link->search(
	    		array('conditions' => 
	    			array('Url' => $this->request->getParam('Url'))
	    		)
	    	);
	    	if (is_array($links)) {
		    	$links = array_diff($links, array($idNode));
	    		if (count($links) > 0) {
		    		$this->_show($links);
		    		return;
	    		}
	    	}
    	}
    	
    	$link = new Link($idNode);
    	$node = new Node($idNode);
    	
    	$link->set('Url', $this->request->getParam('Url'));
    	$linkResult = $link->update();
    	
    	$node->set('Description', $this->request->getParam('Description'));
    	$node->set('Name', $this->request->getParam('Name'));
    	$nodeResult = $node->update();
    	
    	if (($nodeResult > 0) || ($linkResult > 0)) {
    		$this->messages->add(_('Link has been successfully updated'), MSG_TYPE_NOTICE);
    	} else {
    		$this->messages->add(_('An error occurred while upadting link'), MSG_TYPE_ERROR);
    	}
    	
    	foreach ($link->messages->messages as $messageInfo) {
    		$this->messages->messages[] = $messageInfo;
    	}
    	
    	foreach ($node->messages->messages as $messageInfo) {
    		$this->messages->messages[] = $messageInfo;
    	}
    	
		
		$this->reloadNode($node->get('IdParent') );

		$values = array('messages' => $this->messages->messages, "parentID" =>$node->get('IdParent') );
    	
		$this->sendJSON($values);
    }
    
    function _show($links) {
    	
    	$name = $this->request->getParam('Name');
    	$idNode = $this->request->getParam('nodeid');
    	$url = $this->request->getParam('Url');
    	$description = $this->request->getParam('Description');
    	
    	$link = new Link();
    	$links = $link->query(sprintf("Select Node.name, Node.description, Link.Url"
    		. " FROM Nodes Node"
    		. " INNER JOIN Links Link on Node.IdNode = Link.IdLink AND Link.Url = '%s' AND Node.IdNode <> %s", $url, $idNode));
    		
    	$this->render(array(
    			'id_node' => $idNode,
    			'name' => $name,
	   			'description' => $description,
	   			'url' => $url,
    			'links' => $links,
    			'go_method' => 'modifylink'
    	), 'show', 'default-3.0.tpl');
    }
    
}

?>