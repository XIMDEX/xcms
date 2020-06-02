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

use Ximdex\Logger;
use Ximdex\Models\Link;
use Ximdex\Models\Node;
use Ximdex\Modules\Manager;
use Ximdex\MVC\ActionAbstract;

Manager::file('/actions/browser3/inc/FormValidation.class.php');

class Action_modifylink extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
    	$idNode = (int) $this->request->getParam('nodeid');	
		$link = new Link($idNode);
		$node = new Node($idNode);
		if (! $link->get('IdLink') || ! $node->get('IdNode')) {
			$this->messages->add(_('Link could not be successfully loaded, contact with your administrator'), MSG_TYPE_ERROR);
			Logger::error('Error while loading link: ' . $idNode);
			$this->render(['messages' => $this->messages->messages], null, 'messages.tpl');
			return;
		}
		$this->addJs('/actions/createlink/resources/js/index.js');
		$values = [
    		'name' => $node->get('Name'),
    		'url' => $link->get('Url'),
    		'description' => $node->get('Description'),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName(),
    		'go_method' => 'modifylink'
		];
		$this->render($values, null, 'default-3.0.tpl');
    }

	public function modifylink()
    {
    	$idNode = (int) $this->request->getParam('nodeid');
    	$url = $this->request->getParam('Url');
    	$params = [
    	    'nodeid' => $idNode,
    	    'inputName' => 'url',
    	    'url' => $url
    	];
    	if (! FormValidation::isUniqueUrl($params,false)) {
    	    $this->messages->add(_('The URL link is already in use'), MSG_TYPE_ERROR);
    	    $values = [
    	        'messages' => $this->messages->messages
    	    ];
    	    $this->sendJSON($values);
    	}
    	$link = new Link($idNode);
    	$link->set('Url', $url);
    	if ($link->update() === false) {
    	    $this->messages->add(_('An error occurred while upadting link'), MSG_TYPE_ERROR);
    	    $this->messages->mergeMessages($link->messages);
    	    $values = [
    	        'messages' => $this->messages->messages
    	    ];
    	    $this->sendJSON($values);
    	}
    	$description = $this->request->getParam('Description');
    	$node = new Node($idNode);
    	$node->set('Description', $description);
    	$node->set('Name', $this->request->getParam('Name'));
    	if ($node->update() === false) {
    	    $this->messages->add(_('An error occurred while upadting link'), MSG_TYPE_ERROR);
    	    $this->messages->mergeMessages($node->messages);
    	    $this->sendJSON($values);
    	}
        $this->messages->add(_('Link has been successfully updated'), MSG_TYPE_NOTICE);
		$values = [
		    'messages' => $this->messages->messages, 
		    'parentID' => $node->get('IdParent')
        ];
		$this->sendJSON($values);
    }
    
    private function show(array $links)
    {	
    	$name = $this->request->getParam('Name');
    	$idNode = $this->request->getParam('nodeid');
    	$url = $this->request->getParam('Url');
    	$description = $this->request->getParam('Description');    	
    	$link = new Link();
    	$links = $link->query(sprintf('Select Node.name, Node.description, Link.Url'
    		. ' FROM Nodes Node'
    		. ' INNER JOIN Links Link on Node.IdNode = Link.IdLink AND Link.Url like \'%s\' AND Node.IdNode <> %s', $url, $idNode));
    	$this->render([
			'id_node' => $idNode,
			'name' => $name,
   			'description' => $description,
   			'url' => $url,
			'links' => $links,
			'go_method' => 'modifylink'
    	], 'show', 'default-3.0.tpl');
    }
}
