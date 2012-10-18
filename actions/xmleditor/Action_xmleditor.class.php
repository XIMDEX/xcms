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




ModulesManager::file('/inc/utils.inc');

class Action_xmleditor extends ActionAbstract {

    function index() {

		$nodeID = $this->request->getParam('nodeid');

		$queryManager = new QueryManager();
		$action = $queryManager->getPage() . $queryManager->buildWith(array('method' => 'load', 'nodeid' => $nodeID));
    	    	

		$node = new Node($nodeID);
		$template = new Node ($node->class->getTemplate());
		
		if ($template->get('IdNode') > 0) {

			if ($template->nodeType->get('Name') == 'RngVisualTemplate') {			
			
				$this->request->setParam('params', array('editor' => 'kupu'));
				$this->redirectTo('index', 'xmleditor2');
				return;
				
			} else {
				
				//Checks for Firefox Version. Only supports Firefox 3
				$u_agent = $_SERVER['HTTP_USER_AGENT'];
				if(!preg_match('/Firefox\/3/', $u_agent)){
					$this->messages->add(_('This action is not supported on your browser.'), MSG_TYPE_ERROR);
					$this->messages->add(_('We recommend that you updated your system of RNG templates.'), MSG_TYPE_ERROR);
					$this->messages->add(_('You can also use browser Firefox 3.6 to see this editor.'), MSG_TYPE_ERROR);
					$this->render(array('messages' => $this->messages->messages));
					$this->renderMessages();
				}
				
				$this->render(array('action' => $action), NULL, 'iframe.tpl');				
			}
		
		} else {
			$this->messages->add(_("Selected node has not a valid template, it could be deleted"), MSG_TYPE_ERROR);  
			$this->messages->add(_("Check if template exists and if it does contact your administrator"), MSG_TYPE_ERROR);
			$this->render(array('messages' => $this->messages->messages));  
		}
    }
    
    function load() {
		ModulesManager::file("/actions/xmleditor/init.php");
    }
}
?>
