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



class Action_modifychannel extends ActionAbstract {
	//Main method: shows initial form
    	function index () {
    		$idNode = $this->request->getParam('nodeid');
    		$node = new Node($idNode);
    		
		if (!($node->get('IdNode') > 0)) {
    			$this->messages->add(_('Node could not be found'), MSG_TYPE_ERROR);
    			$this->render(array($this->messages), NULL, 'messages.tpl');
    			die();
    		}

    		$renderCheck = array(
    			'ximdex' => '',
    			'client' => ''
    		);
                
                $outputCheck = array(
                    'web' => '',
                    'xml' => '',
                    'other' => ''
    		);

    		$channel = new Channel($idNode);
    		$renderCheck[$channel->get('RenderMode')] = 'checked';
                $outputCheck[$channel->get("OutputType")] = 'checked';
                $ext=$channel->get('DefaultExtension')==NULL ? "(empty)": $channel->get('DefaultExtension');
                $desc=$channel->get('Description')==NULL ? "(empty)": $channel->get('Description');
                
    		$values = array(
    			'id_node' => $idNode,
    			'name' => $channel->get('Name'),
    			'extension' => $ext,
    			'description' => $desc,
    			'render_check' => $renderCheck,
    			'output_check' => $outputCheck,
    			'go_method' => 'modifychannel'
    	);

    	$this->render($values, null, 'default-3.0.tpl');
    }

    function modifychannel() {
    	$idNode = $this->request->getParam('nodeid');
    	$channel = new Channel($idNode);
    	$channel->loadFromArray($_POST);

		$result = $channel->update();
		switch ($result) {
			case 0:
				$channel->messages->add(_('Any change has been perfomed'), MSG_TYPE_WARNING);;
				break;
			case "NULL":
				$channel->messages->add(_('An error occurred while modifying channel'), MSG_TYPE_ERROR);
				break;
			default:
				$channel->messages->add(_('Channel has been successfully modified'), MSG_TYPE_NOTICE);
				break;
		}

		$this->reloadNode($idNode);

		$values = array('messages' => $channel->messages->messages );
		//$this->render($values, NULL, 'messages.tpl');
        $this->sendJSON($values);
    }
}
?>
