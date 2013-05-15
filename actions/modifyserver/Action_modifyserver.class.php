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




ModulesManager::file('/inc/workflow/Workflow.class.php');

class Action_modifyserver extends ActionAbstract {

	function index() {

		$idNode = (int) $this->request->getParam("nodeid");
		$actionID = (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");
		$serverID = $this->request->getParam('serverid');

		$actionParam = $actionID == 0 ? 'action=' . $this->request->getParam('action') : "actionid=$actionID";

		$servers = new Node($idNode);
		$list = $servers->class->GetPhysicalServerList();
		$num_servers = sizeof($list);

		$_server = array();
		if ($num_servers > 0) {
			foreach($list as $id) {
				$_server[] = array( "Id" => $id, "Description" => $servers->class->GetDescription($id) );
			}
		}

		//Get if otf is up
		if (ModulesManager::isEnabled('ximOTF')){
			$otfAvailable=true;
			$s = new Server($serverID);
			$isServerOTF = $s->get('otf');
		}else{
			$otfAvailable=false;
			$isServerOTF=false;
		}

		if($servers) {
			$server = array(
				"id" => $serverID,
				"name" => $servers->GetNodeName(),
				'protocol' => strtoupper($servers->class->GetProtocol($serverID)),
				'encode' => strtoupper($servers->class->GetEncode($serverID)),
				'url' =>  $servers->class->GetURL($serverID),
				'host' => $servers->class->GetHost($serverID),
				'port' => $servers->class->GetPort($serverID),
				'directory' => $servers->class->GetInitialDirectory($serverID),
				'user' => $servers->class->GetLogin($serverID),
				'description' => $servers->class->GetDescription($serverID),
				'enable' => $servers->class->GetEnabled($serverID),
				'preview' => $servers->class->GetPreview($serverID),
				'path' => $servers->class->GetOverrideLocalPaths($serverID),
				'isServerOTF' => $isServerOTF

			);
		}

		//Getting encodes
		$encodes = $this->_getEncodes();
		$numEncodes = sizeof($encodes);

		// Getting channels

		$channels = $this->_getChannels($idNode, $serverID);
		$numChannels = sizeof($channels);

		// Getting languages

		$languages = $this->_getLanguages($idNode, $serverID);
		$numLanguages = sizeof($languages);

		//add a js for validation and hidden or display elements about the protocol selected
		$this->addJs('/actions/modifyserver/resources/js/validate.js');
/*		if (ModulesManager::isEnabled('ximDEMOS'))
			$this->addJs('/actions/modifyserver/resources/js/tour.js');
		if ($this->tourEnabled(XSession::get("userID")))
			$this->addJs('/resources/js/start_tour.js','ximDEMOS');
*/
		$this->addCss('/actions/modifyserver/resources/css/style.css');

		$values = array(
			'id_node' => $idNode,
			'id_action' => $actionID,
			'params' => $params,
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?$actionParam&nodeid={$idNode}",
			"go_method" => "modify_server",
			'servers' => $_server,
			'num_servers' => $num_servers,
			'server' => $server,
			'protocols' => $this->_getProtocols(),
			'encodes' => $this->_getEncodes(),
			'channels' => $channels,
			'numchannels' => $numChannels,
			'languages' => $languages,
			'numlanguages' => $numLanguages,
			'otfAvailable' => $otfAvailable,
		//'states' => $this->_getStates($idNode, $serverID),
			'id_server' => (int) $serverID,
		);

		$this->render($values, NULL, 'default-3.0.tpl');
	}

	function modify_server() {




		$idNode		= (int) $this->request->getParam("nodeid");
		$actionID	= (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");

		$nodeID			= $this->request->getParam('nodeid');
		$serverID		= $this->request->getParam('serverid');
		$protocol		= $this->request->getParam('protocol');
		$host			= $this->request->getParam('host');
		$port			= $this->request->getParam('port');
		$initialDir		= $this->request->getParam('initialdirectory');
		$url			= $this->request->getParam('url');
		$login			= $this->request->getParam('login');
		$password		= $this->request->getParam('password');
		$password2		= $this->request->getParam('password2');
		$description	= $this->request->getParam('description');
		$enabled		= $this->request->getParam('enabled');
		$preview		= $this->request->getParam('preview');
		$override		= $this->request->getParam('overridelocalpaths');
		$actionID		= $this->request->getParam('actionid');
		$channels		= $this->request->getParam('channels');
		$states			= $this->request->getParam('states');
		$languages		= $this->request->getParam('languages');
		$encode			= $this->request->getParam('encode');
		$isServerOTF 	= $this->request->getParam('serverOTF');

		//validate the fields about protocol
		//If ximDEMOS is actived and nodeis is rol "Demo" then  remove is not allowed
		if(ModulesManager::isEnabled("ximDEMOS") ) {
			if("FTP" != $protocol && XSession::get('user_demo')) {
					$this->messages->add(_("Not allowed protocol for demo user. Get Ximdex open source to get full functionality."), MSG_TYPE_ERROR);
					$values = array(
						'messages' => $this->messages->messages,
						'goback' => true,
						'id_node' => $idNode,
						'params' => $params,
						'nodeURL' => Config::getValue('UrlRoot').'/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}',
					);

					$this->render($values);
					return ;
			}
		}




		if ($this->_validate($protocol,$host,$port,$initialDir,$url,$login,$password,$password2,$description)){

			$node = new Node($nodeID);

			$server = new Node($nodeID);
			$list = $server->class->GetPhysicalServerList();

			if (is_array($list) && in_array($serverID, $list)) {
				$action = "mod";
			} else {
				$action = "new";
			}

			if( $this->request->getParam('borrar') == 1) {
				$server = new Node($nodeID);
				$server->class->DeletePhysicalServer($serverID);
				$action = "erase";
				$this->messages->add(_("Server successfully removed"), MSG_TYPE_NOTICE);
			}else {

				$dbObj = new DB();
				$sql = "SELECT IdProtocol FROM Protocols WHERE IdProtocol='".$protocol."'";
				$dbObj->Query($sql);
				if($dbObj->numRows) {

					if($action == "mod") {
						$node->class->SetProtocol($serverID,$protocol);
						$node->class->SetHost($serverID,$host);
						$node->class->SetPort($serverID,$port);
						$node->class->SetInitialDirectory($serverID,$initialDir);
						$node->class->SetLogin($serverID,$login);
						$node->class->SetURL($serverID,$url);
						$node->class->SetDescription($serverID,$description);
						$node->class->SetEnabled($serverID,!!$enabled);
						$node->class->SetPreview($serverID,!!$preview);
						$node->class->SetOverrideLocalPaths($serverID,!!$override);
						$node->class->SetEncode($serverID,$encode);
						if (ModulesManager::isEnabled('ximOTF')){
							$node->class->setIsOTF($isServerOTF,$serverID);
						}
						if($password){
							$node->class->SetPassword($serverID, $password);
						}
						$this->messages->add(_("Server successfully modified"), MSG_TYPE_NOTICE);
					}else{
						$serverID = $node->class->AddPhysicalServer($protocol, $login, $password, $host, $port, $url, $initialDir, !!$override, !!$enabled, !!$preview, $description, $isServerOTF);
						if($serverID) {
							$this->messages->add(_("Server successfully created"), MSG_TYPE_NOTICE);
						}else {
							$this->messages->add(_("Error while creating server"), MSG_TYPE_ERROR);
						}
					}


					$node->class->DeleteAllChannels($serverID);
					if($channels) {
						foreach($channels as $chan) {
							$node->class->AddChannel($serverID, $chan);
						}
					}

					$node->class->DeleteAllStates($serverID);
					if($states) {
						foreach($states as $stat) {
							$node->class->AddState($serverID, $stat);
						}
					}

					// Setting languages

					$node->setProperty('language', $languages);

				}else {
					$this->messages->add(_("Not allowed protocol"), MSG_TYPE_ERROR);
				}
			}
		}

		$values = array(
			'messages' => $this->messages->messages,
			'goback' => true,
			'id_node' => $idNode,
			'params' => $params,
			'nodeURL' => Config::getValue('UrlRoot').'/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}',
		);

		$this->render($values);
	}

	/**
	 * Function for validation the fields
	 *
	 */
	private function _validate($protocol,$host,$port,$initialDir,$url,$login,$password,$password2,$description){
		$validation = true;

		if ($protocol == 'LOCAL'){
			if ((!$initialDir) || ($initialDir=='')){
				$this->messages->add(_("A local directory is required"), MSG_TYPE_ERROR);
				$validation=false;
			}
			if ((!$url) || ($url == '')){
				$this->messages->add(_("A local url is required"), MSG_TYPE_ERROR);
				$validation=false;
			}
		}else if (($protocol == 'FTP') || ($protocol == 'SSH')){
			if ((!$password) || (!$password2) || ($password!=$password2)){
				$this->messages->add(_("Passwords do not match"), MSG_TYPE_ERROR);
				$validation=false;
			}
			if ((!$initialDir) || ($initialDir=='')){
				$this->messages->add(_("A remote directory is required"), MSG_TYPE_ERROR);
				$validation=false;
			}
			if ((!$url) || ($url == '')){
				$this->messages->add(_("A remote url is required"), MSG_TYPE_ERROR);
				$validation=false;
			}
			if ((!$port) || ($port == '')){
				$this->messages->add(_("A connection port is required"), MSG_TYPE_ERROR);
				$validation=false;
			}
			if ((!$host) || ($host == '')){
				$this->messages->add(_("A remote address is required"), MSG_TYPE_ERROR);
				$validation=false;
			}
			if ((!$login) || ($login == '')){
				$this->messages->add(_("A login is required"), MSG_TYPE_ERROR);
				$validation=false;
			}
		}
		//validate the common fields
		if ((!$description) || ($description =='')){
			$this->messages->add(_("Server description is required"), MSG_TYPE_ERROR);
			$validation=false;
		}

		return $validation;
	}

	private function _getEncodes() {
		$dbObj = new DB();
		$sql = "SELECT IdEncode,Description FROM Encodes";
		$dbObj->Query($sql);
		$_protocols = array();
		while(!$dbObj->EOF) {
			$_protocols[] = array(
				"Id" => $dbObj->GetValue("IdEncode"),
				"Description" => $dbObj->GetValue("Description")
			);
			$dbObj->Next();
		}

		return $_protocols;
	}

	private function _getProtocols() {
		$dbObj = new DB();
		$sql = "SELECT IdProtocol,Description FROM Protocols";
		$dbObj->Query($sql);
		$_protocols = array();
		while(!$dbObj->EOF) {
			$_protocols[] = array(
				"Id" => $dbObj->GetValue("IdProtocol"),
				"Description" => $dbObj->GetValue("Description")
			);
			$dbObj->Next();
		}

		return $_protocols;
	}

	private function _getChannels($nodeID, $serverID) {

		$server = new Node($nodeID);
		$channel = new Channel();
		$channels = $channel->getChannelsForNode($nodeID);

		if (is_array($channels)) {
			foreach ($channels as &$channel) {
				$ch = new Channel($channel['IdChannel']);
				$channel['InServer'] = $server->class->HasChannel($serverID, $channel['IdChannel']);
			}
		}

		return count($channels) > 0 ? $channels : null;
	}

	private function _getLanguages($nodeID, $serverID) {
		$node = new Node($nodeID);

		$nodeProperty = new NodeProperty();
		$nodeLanguages = $nodeProperty->getProperty($nodeID, 'language');

		$project = new Node($node->getProject());
		(array) $langs = $project->getProperty('language');

		if (sizeof($langs) > 0) {
			foreach ($langs as $langId) {

				$language = new Language($langId);

				if (in_array($langId, (array) $nodeLanguages)) {
					$checked = 'checked';
				} else {
					$checked = '';
				}

				$languages[] = array(
					'IdLanguage' => $langId,
					'Name' => $language->get('Name'),
					'Checked' => $checked
				);
			}
			return $languages;

		}

		return null;
	}

	private function _getStates($nodeID, $serverID) {
		$workflow = new WorkFlow($nodeID);
		$pipeProcess = $workflow->pipeProcess;
		$allIdStates = $pipeProcess->getAllIdNodeStatus();

		$server = new Node($nodeID);

		if(count($allIdStates) ) {
			$_states = array();
			foreach($allIdStates as $idStatus) {
				$status = new Node($idStatus);
				$_states[] = array(
					"Id" => $idStatus,
					"Name" => $status->get('Name'),
					'HasServer' =>   $server->class->HasState($serverID, $idStatus)
				);

			}
			return $_states;

		}

		return null;
	}


}
?>
