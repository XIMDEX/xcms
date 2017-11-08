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


use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;

ModulesManager::file('/inc/nodetypes/ximnewsimagesfolder.inc', 'ximNEWS');

class Action_createLote extends ActionAbstract {

	const TMP_FOLDER = '/data/tmp/uploaded_files/';

    function index() {

		$actionID = (int) $this->request->getParam("actionid");
		$idNode	= (int) $this->request->getParam("nodeid");
		$params = $this->request->getParam("params");

		$this->addJs('/actions/createLote/resources/js/index.js', 'ximNEWS');

		$values = array(
			'params' => $params,
			'id_node' => $idNode,
			'cadenafecha' => date('d/m/Y'),
			'go_method' => 'add_node',
		    'nodeUrl' => App::getValue('UrlHost') . App::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=$actionID&nodeid=$idNode"
		);

		$this->render($values, 'index', 'default-3.0.tpl');
    }

	function add_node() {

		$nodeId = $this->request->getParam('nodeid');
		$actionID	= $this->request->getParam("name");
		$type = $this->request->getParam('tipo');
		$name = $this->request->getParam('lotename');
		$cadenaFecha = $this->request->getParam('boxfecha');

		$idLote = ximNEWS_Adapter::createLote($name, $nodeId, $type, $cadenaFecha);

		if (!($idLote > 0)) {
			$this->messages->add(_("Error creating the batch."), MSG_TYPE_NOTICE);
			$this->render(array('messages' => $this->messages->messages), NULL, 'messages.tpl');
			return false;
		}

		$this->sendJSON(array('idParent' => $nodeId, 'idLote' => $idLote, 'type' => 'ximnewsimage'));
	}

}