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

use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;

class Action_modifyserver extends ActionAbstract {

    public function index($operation = null, $serverID = null) {

		$idNode = (int) $this->request->getParam("nodeid");
		$actionID = (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");
		if (!$serverID)
		    $serverID = $this->request->getParam('serverid');

		$actionParam = $actionID == 0 ? 'action=' . $this->request->getParam('action') : "actionid=$actionID";

		$servers = new Node($idNode);
		$list = $servers->class->GetPhysicalServerList();
		$num_servers = count($list);

		$_servers = array();
		if ($num_servers > 0) {
		    
			foreach($list as $id) {
			    
				$_servers[] = array( "Id" => $id, "Description" => $servers->class->GetDescription($id) );
			}
		}

		if ($operation == 'mod' or $operation == 'new')
		{
		    //data provided from the form submit
		    $server = array();
		    $server['id'] = $serverID;
		    $server['name'] = $servers->GetNodeName();
		    $server['protocol'] = $this->request->getParam('protocol');
		    $server['host'] = $this->request->getParam('host');
		    $server['port'] = $this->request->getParam('port');
		    $server['initialdirectory'] = $this->request->getParam('initialdirectory');
		    $server['url'] = $this->request->getParam('url');
		    $server['user'] = $this->request->getParam('login');
		    $server['description'] = $this->request->getParam('description');
		    $server['enabled'] = $this->request->getParam('enabled');
		    $server['preview'] = $this->request->getParam('preview');
		    $server['overridelocalpaths'] = $this->request->getParam('overridelocalpaths');
		    $server['states'] = $this->request->getParam('states');
		    $server['encode'] = $this->request->getParam('encode');
		    $server['channels'] = $this->request->getParam('channels');
		}
		elseif ($operation != 'erase' and $servers and $serverID) {
		    
			$server = array(
				"id" => $serverID,
				"name" => $servers->GetNodeName(),
				'protocol' => strtoupper($servers->class->GetProtocol($serverID)),
				'encode' => strtoupper($servers->class->GetEncode($serverID)),
				'url' =>  $servers->class->GetURL($serverID),
				'host' => $servers->class->GetHost($serverID),
				'port' => $servers->class->GetPort($serverID),
				'initialdirectory' => $servers->class->GetInitialDirectory($serverID),
				'user' => $servers->class->GetLogin($serverID),
				'password' => $servers->class->GetPassword($serverID),
				'description' => $servers->class->GetDescription($serverID),
				'enabled' => $servers->class->GetEnabled($serverID),
				'preview' => $servers->class->GetPreview($serverID),
				'overridelocalpaths' => $servers->class->GetOverrideLocalPaths($serverID)
			);
		}
		else
		{
			$server = array();
		}

		//Getting encodes
		$encodes = $this->_getEncodes();
		$numEncodes = count($encodes);

		// Getting channels
		$channels = $this->_getChannels($idNode, $serverID, $server);
		$numChannels = count($channels);

		//add a js for validation and hidden or display elements about the protocol selected
		$this->addJs('/actions/modifyserver/resources/js/validate.js');
		$this->addCss('/actions/modifyserver/resources/css/style.css');

		$values = array(
			'id_node' => $idNode,
			'id_action' => $actionID,
			'params' => $params,
			"nodeURL" => App::getUrl("/?$actionParam&nodeid={$idNode}"),
			"go_method" => "modify_server",
			'servers' => $_servers,
			'num_servers' => $num_servers,
			'server' => $server,
			'protocols' => $this->_getProtocols(),
			'encodes' => $this->_getEncodes(),
			'channels' => $channels,
			'numchannels' => $numChannels,
			'id_server' => (int) $serverID,
			'messages' => $this->messages->messages
		);
		$this->render($values, "index", 'default-3.0.tpl');
	}

	public function modify_server() {
	    
		$idNode		= (int) $this->request->getParam("nodeid");
		$actionID	= (int) $this->request->getParam("actionid");
		$params 	= $this->request->getParam("params");
		$nodeID		= $this->request->getParam('nodeid');
		$serverID	= $this->request->getParam('serverid');
		$protocol	= $this->request->getParam('protocol');
		$host		= $this->request->getParam('host');
		$port		= $this->request->getParam('port');
		$initialDir	= $this->request->getParam('initialdirectory');
		$url		= $this->request->getParam('url');
		$login		= $this->request->getParam('login');
		$password	= $this->request->getParam('password');
		$description= $this->request->getParam('description');
		$enabled	= $this->request->getParam('enabled');
		$preview	= $this->request->getParam('preview');
		$override	= $this->request->getParam('overridelocalpaths');
		$channels	= $this->request->getParam('channels');
		$states		= $this->request->getParam('states');
		$encode		= $this->request->getParam('encode');

		$server = new Node($nodeID);
		$list = $server->class->GetPhysicalServerList();
		
		if (is_array($list) && in_array($serverID, $list))
		    $action = "mod";
		else
		    $action = "new";
		
		if ($this->_validate($serverID, $protocol,$host,$port,$initialDir,$url,$login,$password,$description, $encode, $idNode, $channels)){

			$node = new Node($nodeID);
            
			if ($this->request->getParam('borrar') == 1) {
			    
				$server = new Node($nodeID);
				$server->class->DeletePhysicalServer($serverID);
				$action = "erase";
				$this->messages->add(_("Server successfully removed"), MSG_TYPE_NOTICE);
				$serverID = null;
			} else {

				$dbObj = new \Ximdex\Runtime\Db();
				$sql = "SELECT IdProtocol FROM Protocols WHERE IdProtocol='".$protocol."'";
				$dbObj->Query($sql);
				if ($dbObj->numRows) {

					if ($action == "mod") {
					    
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

						if($password){
						    
							$node->class->SetPassword($serverID, $password);
						}
						elseif ($serverID and $server)
						{
							//if the password was specified before, we use the one stored
							$password = $server->class->GetPassword($serverID);
						}
						$this->messages->add(_("Server successfully modified"), MSG_TYPE_NOTICE);
					} else {
					    
						$serverID = $node->class->AddPhysicalServer($protocol, $login, $password, $host, $port, $url, $initialDir, !!$override
						      , !!$enabled, !!$preview, $description);
						if ($serverID) {
						    
                            $node->class->SetEncode($serverID,$encode);
							$this->messages->add(_("Server successfully created"), MSG_TYPE_NOTICE);
						} else {
						    
							$this->messages->add(_("Error while creating server"), MSG_TYPE_ERROR);
						}
					}

					$node->class->DeleteAllChannels($serverID);
					if ($channels) {
					    
						foreach($channels as $chan) {
						    
							$node->class->AddChannel($serverID, $chan);
						}
					}

					$node->class->DeleteAllStates($serverID);
					if ($states) {
					    
						foreach($states as $stat) {
						    
							$node->class->AddState($serverID, $stat);
						}
					}
				} else {
				    
					$this->messages->add(_("Not allowed protocol"), MSG_TYPE_ERROR);
				}
			}
		}

		$values = array(
			'messages' => $this->messages->messages,
			'goback' => true,
			'id_node' => $idNode,
			'params' => $params,
			'nodeURL' => App::getUrl("?actionid=$actionID&nodeid={$idNode}"),
		);
		
		$this->index($action, $serverID);
	}

	/**
	 * Function for validation the fields
	 */
	private function _validate($serverID, $protocol,$host,$port,$initialDir,$url,$login,$password,$description, $encode, $idNode, $channels){
	    
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
		} else if (($protocol == 'FTP') || ($protocol == 'SSH')){
		    
			if (!$serverID and (!$password)){
				$this->messages->add(_("A password is required"), MSG_TYPE_ERROR);
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
		else
		{
		    $servers = new Node($idNode);
		    $list = $servers->class->GetPhysicalServerList();
		    if (is_array($list) and count($list))
		    {
		        foreach($list as $id)
		        {
		            //we check that the server name is not in use for another one
		            if (strtoupper($servers->class->GetDescription($id)) == strtoupper($description) and $serverID != $id)
		            {
		                $this->messages->add(_("Server description " . strtoupper($description) . " is already in use"), MSG_TYPE_ERROR);
		                $validation = false;
		            }
		        }
		    }
		}
		if (!$encode)
		{
			$this->messages->add(_("An enconding type is required"), MSG_TYPE_ERROR);
			$validation = false;
		}
		if (!$channels)
		{
		    $this->messages->add(_('At least one channel is required'), MSG_TYPE_ERROR);
		    $validation = false;
		}

		return $validation;
	}

	private function _getEncodes() {
	    
		$dbObj = new \Ximdex\Runtime\Db();
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
	    
		$dbObj = new \Ximdex\Runtime\Db();
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

	private function _getChannels($nodeID, $serverID = null, $server = null) {
		
		$channel = new Channel();
		$channels = $channel->getChannelsForNode($nodeID);

		if (is_array($channels)) {
			
		    if (isset($server['channels']) and $server['channels'])
			{
				//data provided from the form submit
				foreach ($channels as & $channel)
				{
					$channel['InServer'] = in_array($channel['IdChannel'], $server['channels']);
				}
			}
			elseif ($serverID)
			{
				$server = new Node($nodeID);
				foreach ($channels as & $channel) {
					$channel['InServer'] = $server->class->HasChannel($serverID, $channel['IdChannel']);
				}
			}
		}

		return count($channels) > 0 ? $channels : null;
	}

}
