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

class Action_createlink extends ActionAbstract {
    // Main method: shows initial form
    function index() {
    	$idNode = $this->request->getParam('nodeid');
		$this->addJs('/actions/createlink/resources/js/index.js');
		$values = array( 'go_method' => 'createlink');
		$this->render($values, null, 'default-3.0.tpl');
    }
    
    function createlink() {
        $name = $this->request->getParam('name');
	    $idParent = $this->request->getParam('id_node');
	    $url = $this->request->getParam('url');
	    $description = $this->request->getParam('description');
	    $validated = $this->request->getParam('validated');
    	
		$link = new Link();
    	if (!$validated) {
	    	$links = $link->search(array('conditions' => array('Url' => $url)));
	    	if (is_array($links)) {
	    		$this->_show($links);
	    		return;
	    	}
    	}
    	
		$data = array('NODETYPENAME' => 'LINK',
				'NAME' => $name,
				'PARENTID' => $idParent,
				'IDSTATE' => 0,
				'CHILDRENS' => array (
					array ('URL' => $url),
					array ('DESCRIPTION' => $description)
				)
			);
			
		$bio = new baseIO();
		$result = $bio->build($data);
		
		if ($result > 0) {
			$link = new Link($result);
			$link->set('ErrorString','NOT CHECKED');
			$link->set('CheckTime',time());
			$linkResult = $link->update();
			$bio->messages->add(_('Link has been successfully added'), MSG_TYPE_NOTICE);
		}
		
		$this->reloadNode($idParent);
		
		$values = array('messages' => $bio->messages->messages);
		$this->render($values, NULL, 'messages.tpl');	
    }
    
   	function _show($links){
   		$name = $this->request->getParam('name');
   		$idParent = $this->request->getParam('id_node');
   		$url = $this->request->getParam('url');
    	$description = $this->request->getParam('description');
    	
   		$link = new Link();
   		$links = $link->query(sprintf("Select Node.name, Node.description, Link.Url FROM Nodes Node INNER JOIN Links Link on Node.IdNode = Link.IdLink AND Link.Url = '%s'", $url));
    	
   		$this->render(array(
                        'id_node' => $idParent,
    					'name' => $name,
	   				    'description' => htmlspecialchars($description, ENT_QUOTES),
	   				    'url' => $url,
    					'links' => $links,
    					'go_method' => 'createlink'
    				),  
                    'show', 'default-3.0.tpl');
    }
}
?>
