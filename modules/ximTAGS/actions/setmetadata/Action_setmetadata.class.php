<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/inc/Tags.inc', 'ximTAGS');
ModulesManager::file('/inc/RelTagsNodes.inc', 'ximTAGS');

class Action_setmetadata extends ActionAbstract {

    function index() {
   	$this->addCss('/xmd/style/jquery/ximdex_theme/widgets/tagsinput/tagsinput.css');
	$this->addJs('/actions/setmetadata/resources/js/setmetadata.js','ximTAGS');
	$this->addCss('/actions/setmetadata/resources/css/setmetadata.css','ximTAGS');

 	$idNode	= (int) $this->request->getParam("nodeid");
	$actionID = (int) $this->request->getParam("actionid");
	$params = $this->request->getParam("params");
	$tags = new Tag();

 	$values = array(
 		'nube_tags' => $tags->getTags(),
		'id_node' => $idNode,
		'go_method' => 'save_metadata',
		'nodeUrl' => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid=$idNode"
	);

	$this->render($values, 'index', 'default-3.0.tpl');
    }

    function save_metadata() {
     	$idNode	= (int) $this->request->getParam("nodeid");

		if(array_key_exists("tags", $_POST) ) {
			$tags = new RelTagsNodes();
     		$previous_tags = $tags->getTags($idNode);
		  	$tags->saveAll($_POST['tags'], $idNode, $previous_tags);
		 }
		$this->messages->add(_("The metadata has been properly associated."), MSG_TYPE_NOTICE);
		$values = array(
			'messages' => $this->messages->messages,
		);

		$this->render($values);
   }
}

