<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

class Action_modifyserver extends ActionAbstract
{
    public function index($operation = null, $serverID = null)
    {
		$idNode = (int) $this->request->getParam('nodeid');
		$actionID = (int) $this->request->getParam('actionid');
		$params = $this->request->getParam('params');
		if (! $serverID) {
		    $serverID = $this->request->getParam('serverid');
		}
		$actionParam = $actionID == 0 ? 'action=' . $this->request->getParam('action') : 'actionid=' . $actionID;
		$servers = new Node($idNode);
		$list = $servers->class->getPhysicalServerList();
		$num_servers = count($list);
		$_servers = array();
		if ($num_servers > 0) {
			foreach($list as $id) {
				$_servers[] = array('Id' => $id, 'Description' => $servers->class->GetDescription($id));
			}
		}
		if ($operation == 'mod' or $operation == 'new') {
		    
		    // Data provided from the form submit
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
		    $server['token'] = $this->request->getParam('token');
		}
		elseif ($operation != 'erase' and $servers and $serverID) {
			$server = array(
				'id' => $serverID,
				'name' => $servers->GetNodeName(),
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
			    'token' => $servers->class->GetToken($serverID),
			    'nodeTypeID' => $servers->nodeType->getID(),
			    'node_Type' => $servers->nodeType->GetName(),
				'overridelocalpaths' => $servers->class->GetOverrideLocalPaths($serverID)
			);
		}
		else {
			$server = array();
		}

		// Getting channels
		$channels = $this->getChannels($idNode, $serverID, $server);
		$numChannels = count($channels);

		// Add a js for validation and hidden or display elements about the protocol selected
		$this->addJs('/actions/modifyserver/resources/js/validate.js');
		$this->addCss('/actions/modifyserver/resources/css/style.css');
		$values = array(
			'id_node' => $idNode,
			'id_action' => $actionID,
			'params' => $params,
			'nodeURL' => App::getUrl('/?' . $actionParam . '&nodeid=' . $idNode),
			'go_method' => 'modify_server',
			'servers' => $_servers,
			'num_servers' => $num_servers,
			'server' => $server,
			'protocols' => $this->getProtocols(),
			'encodes' => $this->getEncodes(),
			'channels' => $channels,
			'numchannels' => $numChannels,
			'id_server' => (int) $serverID,
			'messages' => $this->messages->messages,
			'nodeTypeID' => $servers->nodeType->getID(),
			'node_Type' => $servers->nodeType->GetName()
		);
		$this->render($values, 'index', 'default-3.0.tpl');
	}

	public function modify_server()
	{
		$idNode = (int) $this->request->getParam('nodeid');
		$nodeID	= $this->request->getParam('nodeid');
		$serverID = $this->request->getParam('serverid');
		$protocol = $this->request->getParam('protocol');
		$host = $this->request->getParam('host');
		$port = (int) $this->request->getParam('port');
		$initialDir	= $this->request->getParam('initialdirectory');
		$url = $this->request->getParam('url');
		$login = $this->request->getParam('login');
		$password = $this->request->getParam('password');
		$description = $this->request->getParam('description');
		$enabled = $this->request->getParam('enabled');
		$preview = $this->request->getParam('preview');
		$override = $this->request->getParam('overridelocalpaths');
		$channels = $this->request->getParam('channels');
		$states	= $this->request->getParam('states');
		$encode	= $this->request->getParam('encode');
		$token = $this->request->getParam('token');
		$server = new Node($nodeID);
		$list = $server->class->getPhysicalServerList();
		if (is_array($list) && in_array($serverID, $list)) {
		    $action = 'mod';
		}
		else {
		    $action = 'new';
		}
		if ($this->validate($serverID, $protocol, $host, $port, $initialDir, $url, $login, $password, $description, $encode, $idNode, $channels)) {
			$node = new Node($nodeID);
			if ($this->request->getParam('borrar') == 1) {
				$server = new Node($nodeID);
				$server->class->deletePhysicalServer($serverID);
				$action = 'erase';
				$this->messages->add(_('Server successfully removed'), MSG_TYPE_NOTICE);
				$serverID = null;
			} else {
				$dbObj = new \Ximdex\Runtime\Db();
				$sql = 'SELECT IdProtocol FROM Protocols WHERE IdProtocol=\'' . $protocol . '\'';
				$dbObj->Query($sql);
				if ($dbObj->numRows) {
					if ($action == 'mod') {
						$node->class->setProtocol($serverID, $protocol);
						$node->class->setHost($serverID, $host);
						$node->class->setPort($serverID, $port);
						$node->class->setInitialDirectory($serverID, $initialDir);
						$node->class->setLogin($serverID, $login);
						$node->class->setURL($serverID, $url);
						$node->class->setDescription($serverID, $description);
						$node->class->setEnabled($serverID, !!$enabled);
						$node->class->setPreview($serverID, !!$preview);
						$node->class->setOverrideLocalPaths($serverID, !!$override);
						$node->class->setEncode($serverID, $encode);
						$node->class->setToken($serverID, $token);
						if ($password){
							$node->class->setPassword($serverID, $password);
						}
						elseif ($serverID and $server) {
						    
							//If the password was specified before, we use the one stored
							$password = $server->class->getPassword($serverID);
						}
						$this->messages->add(_('Server successfully modified'), MSG_TYPE_NOTICE);
					} else {
						$serverID = $node->class->addPhysicalServer($protocol, $login, $password, $host, $port, $url, $initialDir, !!$override
						      , !!$enabled, !!$preview, $description, $token);
						if ($serverID) {
                            $node->class->setEncode($serverID,$encode);
							$this->messages->add(_('Server successfully created'), MSG_TYPE_NOTICE);
						} else {
							$this->messages->add(_('Error while creating server'), MSG_TYPE_ERROR);
						}
					}
					$node->class->deleteAllChannels($serverID);
					if ($channels) {
						foreach($channels as $chan) {
							$node->class->AddChannel($serverID, $chan);
						}
					}
					$node->class->deleteAllStates($serverID);
					if ($states) {
						foreach($states as $stat) {
							$node->class->addState($serverID, $stat);
						}
					}
				} else {
					$this->messages->add(_('Not allowed protocol'), MSG_TYPE_ERROR);
				}
			}
		}
		$this->index($action, $serverID);
	}

	/**
	 * Function for validation the fields
	 */
	private function validate($serverID, $protocol, $host, $port, $initialDir, $url, $login, $password, $description, $encode, $idNode, $channels)
	{
		$validation = true;
		if ($protocol == 'LOCAL') {
			if (! $initialDir) {
				$this->messages->add(_('A local directory is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
			if (! $url) {
				$this->messages->add(_('A local url is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
		} else if (($protocol == 'FTP') || ($protocol == 'SSH')) {
			if (! $serverID and ! $password) {
				$this->messages->add(_('A password is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
			if (! $initialDir) {
				$this->messages->add(_('A remote directory is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
			if (! $url) {
				$this->messages->add(_('A remote url is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
			if (! $port or ! is_numeric($port)) {
				$this->messages->add(_('A connection port is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
			if (! $host) {
				$this->messages->add(_('A remote address is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
			if (! $login) {
				$this->messages->add(_('A login is required'), MSG_TYPE_ERROR);
				$validation = false;
			}
		}
		
		// Validate the common fields
		if (! $description) {
			$this->messages->add(_('Server description is required'), MSG_TYPE_ERROR);
			$validation = false;
		}
		else
		{
		    $servers = new Node($idNode);
		    $list = $servers->class->getPhysicalServerList();
		    if (is_array($list) and count($list)) {
		        foreach($list as $id) {
		            
		            // We check that the server name is not in use for another one
		            if (strtoupper($servers->class->GetDescription($id)) == strtoupper($description) and $serverID != $id) {
		                $this->messages->add(sprintf(_('Server description %s is already in use'), strtoupper($description)), MSG_TYPE_ERROR);
		                $validation = false;
		            }
		        }
		    }
		}
		if (! $encode) {
			$this->messages->add(_('An enconding type is required'), MSG_TYPE_ERROR);
			$validation = false;
		}
		if (! $channels) {
		    $this->messages->add(_('At least one channel is required'), MSG_TYPE_ERROR);
		    $validation = false;
		}
		return $validation;
	}

	private function getEncodes()
	{
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = 'SELECT IdEncode,Description FROM Encodes';
		$dbObj->Query($sql);
		$_protocols = array();
		while (! $dbObj->EOF) {
			$_protocols[] = array(
				'Id' => $dbObj->GetValue('IdEncode'),
				'Description' => $dbObj->GetValue('Description')
			);
			$dbObj->Next();
		}
		return $_protocols;
	}

	private function getProtocols()
	{
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = 'SELECT IdProtocol,Description FROM Protocols';
		$dbObj->query($sql);
		$_protocols = array();
		while(! $dbObj->EOF) {	    
			$_protocols[] = array(
				'Id' => $dbObj->GetValue('IdProtocol'),
				'Description' => $dbObj->GetValue('Description')
			);
			$dbObj->Next();
		}
		return $_protocols;
	}

	private function getChannels($nodeID, $serverID = null, $server = null)
	{
	    $serverNode = new Node($nodeID);
		$channel = new Channel();
		$channels = $channel->getChannelsForNode($serverNode->getProject());
		if (is_array($channels)) {
            if (isset($server['channels']) and $server['channels']) {
		        
	   			// Data provided from the form submit
		  		foreach ($channels as & $channel) {
					$channel['InServer'] = in_array($channel['IdChannel'], $server['channels']);
				}
			} elseif ($serverID) {
				foreach ($channels as & $channel) {
					$channel['InServer'] = $serverNode->class->HasChannel($serverID, $channel['IdChannel']);
				}
			}
		}
		return count($channels) > 0 ? $channels : null;
	}
}
