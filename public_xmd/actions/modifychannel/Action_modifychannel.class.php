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

use Ximdex\Models\Channel;
use Ximdex\Models\ProgrammingLanguage;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;

class Action_modifychannel extends ActionAbstract
{
	/**
	 * Main method: shows initial form
	 */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);	
		if (! $node->GetID()) {
    			$this->messages->add(_('Node could not be found'), MSG_TYPE_ERROR);
    			$this->render(array($this->messages), null, 'messages.tpl');
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
        $renderTypeCheck = Channel::RENDER_TYPES;
		$channel = new Channel($idNode);
		$renderCheck[$channel->get('RenderMode')] = 'checked';
        $outputCheck[$channel->get('OutputType')] = 'checked';
        if ($channel->get('RenderType')) {
            $renderTypeCheck[$channel->get('RenderType')] = 'checked';
        }
        $ext = $channel->get('DefaultExtension') == null ? '(empty)' : $channel->get('DefaultExtension');
        $desc = $channel->get('Description') == null ? '(empty)' : $channel->get('Description');
        $progLanguage = new ProgrammingLanguage();
        $codeLanguages = $progLanguage->find();
        $this->addJs('/actions/modifychannel/resources/js/index.js');
		$values = array(
			'id_node' => $idNode,
			'name' => $channel->get('Name'),
            'default_channel' => $channel->get('Default_Channel'),
			'extension' => $ext,
			'description' => $desc,
			'render_check' => $renderCheck,
			'output_check' => $outputCheck,
			'go_method' => 'modifychannel',
		    'render_type' => $renderTypeCheck,
		    'code_languages' => $codeLanguages,
		    'language' => $channel->getIdLanguage(),
		    'nodeTypeID' => $node->nodeType->getID(),
		    'node_Type' => $node->nodeType->GetName()
    	);
    	$this->render($values, null, 'default-3.0.tpl');
    }

    public function modifychannel()
    {
    	$idNode = $this->request->getParam('nodeid');
    	if ($idNode == Channel::JSON_CHANNEL) {
        	$this->messages->add('This channel cannot be modified', MSG_TYPE_WARNING);
        	$values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
        	$this->sendJSON($values);
        	return false;
    	}
    	$outputType = $this->request->getParam('output_type_' . $idNode);
        if (! $outputType) {
            $this->messages->add('No output type selected', MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
            $this->sendJSON($values);
            return false;
        }
        $renderType = $this->request->getParam('render_type_' . $idNode);
        if (! $renderType) {
            $this->messages->add('No render type selected', MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
            $this->sendJSON($values);
            return false;
        }
        if (! isset(Channel::RENDER_TYPES[$renderType])) {
            $this->messages->add('There is not a render type for ' . $renderType, MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
            $this->sendJSON($values);
            return false;
        }
        $codeLanguage = $this->request->getParam('language');
        if (! $codeLanguage) {
            $this->messages->add('No code language selected', MSG_TYPE_WARNING);
            $values = array('messages' => $this->messages->messages, 'idNode' => $idNode);
            $this->sendJSON($values);
            return false;
        }
        $channel = new Channel($idNode);
        $channel->set('Description', $this->request->getParam('Description'));
        $channel->set('RenderMode', $this->request->getParam('renderMode'));
        $channel->set('OutputType', $outputType);
        $channel->setRenderType($renderType);
        $channel->setIdLanguage($codeLanguage);
        $default = (int) $this->request->getParam('Default_Channel');
        $channel->set('Default_Channel', $default);
        $result = $channel->update();
		if ($result === null) {
		    $channel->messages->add(_('Not any change has been performed'), MSG_TYPE_WARNING);
		} elseif ($result === false) {
            $channel->messages->add(_('An error occurred while modifying channel'), MSG_TYPE_ERROR);
		} else {
		    if ($default == 1) {
                if ($channel->setDefaultChannelToZero($idNode)) {
                    $channel->messages->add(_('Channel has been successfully modified'), MSG_TYPE_NOTICE);
                } else {
                    $channel->messages->add(_('Error setting Default Channel property'), MSG_TYPE_ERROR);
                }
            } else {
                $channel->messages->add(_('Channel has been successfully modified'), MSG_TYPE_NOTICE);
            }
		}
		$values = array('messages' => $channel->messages->messages);
        $this->sendJSON($values);
    }
}
