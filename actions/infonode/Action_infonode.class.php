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

ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/channel.inc');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');

class Action_infonode extends ActionAbstract {

	function index() {

    		$this->addCss('/actions/infonode/resources/css/style.css');
 		$idNode	= (int) $this->request->getParam("nodeid");
		$node = new Node($idNode);
		$info = $node->loadData();

		//channels
		$channel = new Channel();
		$channels = $channel->getChannelsForNode($idNode);

		//languages
		$nodeLanguages = $node->getProperty('language', true);
		$languages = array();
		if(!empty($nodeLanguages) ) {
			$i = 0;
			foreach($nodeLanguages as $_lang) {
				$_node = new Node($_lang);
				$languages[$i]["Id"] = $_lang;
				$languages[$i]["Name"] = $_node->get("Name");
				$i++;
			}
		}
 		$values = array(
			'id_node' => $idNode,
			'info' => $info,
			'channels' => $channels,
			'languages' => $languages
		);
		$this->render($values, 'index', 'default-3.0.tpl');
    	}
}

?>