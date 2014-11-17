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



ModulesManager::file('/inc/db/db.php');

class Action_manageversions extends ActionAbstract {
	//Main method: shows initial form
    	function index () {
    		$idNode = $this->request->getParam('nodeid');
    		$node = new Node($idNode);
    		if (!$node->get('IdNode') > 0) {
    			$this->messages->add(_('Node could not be found'), MSG_TYPE_ERROR);
			$values = array('messages' => $node->messages->messages);
			$this->render($values, NULL, 'messages.tpl');
			return false;
 		}

 		$isStructuredDocument = (bool)$node->nodeType->get('IsStructuredDocument');
 		$channels = array();
 		if ($isStructuredDocument) {
			$structuredDocument = new StructuredDocument($idNode);
			$channelList = $structuredDocument->GetChannels();
			if (is_array($channelList) && !empty($channelList)) {
				foreach($channelList as $idChannel) {
					$channel = new Node($idChannel);
					$channels[$idChannel] = $channel->get('Name');
				}
			}
 		}

 		$dbObj = new DB();
		$query = sprintf("SELECT v.IdVersion, v.Version, v.SubVersion,"
			. " v.Date, v.Comment, u.Name"
			. " FROM Versions v INNER JOIN Users u USING (IdUser)"
			. " WHERE IdNode = %s"
			. " ORDER BY v.Version DESC, v.SubVersion DESC",
			$dbObj->sqlEscapeString($idNode));

		$dbObj->query($query);
		$versionList = array();
		while(!$dbObj->EOF) {
			$versionList[$dbObj->getValue('Version')][] = array(
									'IdVersion' => $dbObj->getValue('IdVersion'),
									'SubVersion' => $dbObj->getValue('SubVersion'),
									'Date' => date('d/m/Y H:i', $dbObj->getValue('Date')),
									'Name' => $dbObj->getValue('Name'),
									'Comment' => $dbObj->getValue('Comment'),
									'isLastVersion' => 'false');
			$dbObj->Next();
		}

		$keys = array_keys($versionList);
		$lastVersion = $keys[0];

		$keys = array_keys($versionList[$lastVersion]);
		$lastSubversion = $keys[0];

		$versionList[$lastVersion][$lastSubversion]['isLastVersion'] = 'true';

		$this->addJs('/actions/manageversions/resources/js/index.js');
		$this->addCss('/actions/manageversions/resources/css/index.css');

		$values = array('versionList' => $versionList,
				'isStructuredDocument' => $isStructuredDocument,
				'id_node' => $idNode,
				'node_type_name' => $node->nodeType->get('Name'),
				'channels' => $channels,
				'actionid' => $this->request->getParam('actionid')
				);
		$this->render($values, null, 'default-3.0.tpl');
    	}

    	function recover() {
    		$idNode = $this->request->getParam('nodeid');
    		$version = $this->request->getParam('version');
		$subVersion	= $this->request->getParam('subversion');

    		if (!is_null($version) && !is_null($subVersion)) {
			//If it is a recovery of a version, first we recover it and then we show the form
			$data = new DataFactory($idNode);
			$ret = $data->RecoverVersion($version, $subVersion);
			if ($ret === false) {
				$this->render(array(
					'messages' => array(array(
						'type' => $data->numErr,
						'message' => $data->msgErr
					)),
					'goback' => true
				));
				return;
			}
		}

    		$this->redirectTo('index');
    	}

    	function delete() {
    		$idNode = $this->request->getParam('nodeid');
    		$version = $this->request->getParam('version');
		$subVersion	= $this->request->getParam('subversion');
    		
		if (!is_null($version) && !is_null($subVersion)) {
			$data = new DataFactory($idNode);
			$ret = $data->DeleteSubversion($version,$subVersion);
			if ($ret === false) {
				$this->render(array(
					'messages' => array(array(
						'type' => $data->numErr,
						'message' => $data->msgErr
					)),
					'goback' => true
				));
				return;
			}
		}
    		$this->redirectTo('index');
    	}
}

?>