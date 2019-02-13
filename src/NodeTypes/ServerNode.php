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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use Ximdex\Runtime\Db;
use Ximdex\Models\NodeType;
use Ximdex\Models\Server;
use Ximdex\Models\Node;

class ServerNode extends FolderNode
{
    const ALL_SERVERS = '3';
    
    /**
     * Get the physical servers list with criteria
     * 
     * @param bool $hidePrevisual --> true --> dont get the "previous" servers
     * @param bool $enabled
     * @return array $list
     */
    public function getPhysicalServerList(bool $hidePrevisual = null, bool $enabled = false)
    {
        $where = '';
        if (! is_null($hidePrevisual)) {
            $where .= 'Previsual != 1 AND ';
        }
        if ($enabled) {
            $where .= 'Enabled = 1 AND ';
        }
        $where .= 'IdNode = %s';
        $server = new Server();
        return $server->find('IdServer', $where, array($this->nodeID), MONO);
    }

    public static function getServersForPumping()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        
        // Find and enable servers disabled for pumping with delay time
        $sql = 'SELECT IdServer FROM Servers WHERE ActiveForPumping = 0' 
            . ' AND NOT(DelayTimeToEnableForPumping IS NULL) AND DelayTimeToEnableForPumping <= ' . time();
        if ($dbObj->Query($sql) === false) {
            return false;
        }
        while (! $dbObj->EOF) {
            $server = new Server($dbObj->GetValue('IdServer'));
            $server->enableForPumping();
            $dbObj->Next();
        }
        
        // Get servers enabled and active for pumping operations
        $sql = 'SELECT IdServer FROM Servers WHERE Enabled = 1 AND ActiveForPumping = 1';
        $dbObj->Query($sql);
        $enabledServers = array();
        while (! $dbObj->EOF) {
            $enabledServers[] = $dbObj->GetValue('IdServer');
            $dbObj->Next();
        }
        return $enabledServers;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FolderNode::createNode()
     */
    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null)
    {
        $this->updatePath();
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::deleteNode()
     */
    public function deleteNode() : bool
    {
        $servers = $this->getPhysicalServerList();
        if ($servers) {
            foreach ($servers as $serverID) {
                $this->deletePhysicalServer($serverID);
            }
        }
        return true;
    }
    
    public function addPhysicalServer(string $protocolID = null, string $login = null, string $password, string $host = null, int $port = null
        , string $url = null, string $initialDirectory = null, bool $overrideLocalPaths = null, bool $enabled = null, bool $previsual = null
        , string $description = null, string $token = null)
    {
        $sql = 'INSERT INTO Servers ';
        $sql .= '(IdServer, IdNode, IdProtocol, Login, Password, Host,';
        $sql .= ' Port, Url, InitialDirectory, OverrideLocalPaths, Enabled, Previsual, Description, Token) ';
        $sql .= 'VALUES ';
        $sql .= '(NULL, ' . DB::sqlEscapeString($this->parent->get('IdNode')) . ', ' . DB::sqlEscapeString($protocolID) . ', ' 
            . DB::sqlEscapeString($login) . ', ' . DB::sqlEscapeString($password) . ', ' . DB::sqlEscapeString($host) . ',';
        $sql .= ' ' . DB::sqlEscapeString($port) . ', ' . DB::sqlEscapeString($url) . ', ' . DB::sqlEscapeString($initialDirectory) . ', ';
        $sql .= (int) $overrideLocalPaths . ', ' . (int) $enabled . ', ' . (int) $previsual . ', ' 
            . DB::sqlEscapeString($description) . ', ' . DB::sqlEscapeString($token) . ')';
        $this->dbObj->Execute($sql);
        return $this->dbObj->newID;
    }

    public function deletePhysicalServer(int $physicalID)
    {
        $sql = 'DELETE FROM Servers WHERE IdServer = \'' . $physicalID . '\' AND IdNode = \'' . $this->nodeID . '\'';
        $this->dbObj->Execute($sql);
    }

    public function setProtocol(int $physicalID, string $protocolID = null)
    {
        $sql = 'UPDATE Servers SET IdProtocol = \'' . $protocolID . '\' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setEncode(int $physicalID, string $encodeID = null)
    {
        $sql = 'UPDATE Servers SET IdEncode = \'' . $encodeID . '\' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setPassword(int $physicalID, string $pass = null)
    {
        $sql = 'UPDATE Servers SET Password = ' . DB::sqlEscapeString($pass) . ' WHERE IdNode = ' . $this->nodeID 
            . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setLogin(int $physicalID, string $login = null)
    {
        $sql = 'UPDATE Servers SET Login = ' . DB::sqlEscapeString($login) . ' WHERE IdNode = ' . $this->nodeID 
            . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setHost(int $physicalID, string $host = null)
    {
        $sql = 'UPDATE Servers SET Host = ' . DB::sqlEscapeString($host) . ' WHERE IdNode = ' . $this->nodeID 
            . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setPort(int $physicalID, int $port = null)
    {
        $sql = 'UPDATE Servers SET Port = ' . DB::sqlEscapeString($port) . ' WHERE IdNode = ' . $this->nodeID 
            . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setInitialDirectory(int $physicalID, string $dir = null)
    {
        $sql = 'UPDATE Servers SET InitialDirectory = \'' . $dir . '\' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setURL(int $physicalID, string $url = null)
    {
        $sql = 'UPDATE Servers SET Url = \'' . $url . '\' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setEnabled(int $physicalID, bool $enable = null)
    {
        $enable = ($enable) ? 'true' : 'false';
        $sql = 'UPDATE Servers SET Enabled = ' . $enable . ' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setPreview(int $physicalID, bool $preview = null)
    {
        $preview = ($preview) ? 'true' : 'false';
        $sql = 'UPDATE Servers SET Previsual = ' . $preview . ' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setDescription(int $physicalID, string $description = null)
    {
        $sql = 'UPDATE Servers SET Description = \'' . $description . '\' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function setOverrideLocalPaths(int $physicalID, bool $overrideLocalPaths = null)
    {
        $overrideLocalPaths = ($overrideLocalPaths) ? 'true' : 'false';
        $sql = 'UPDATE Servers SET OverrideLocalPaths = ' . $overrideLocalPaths . ' WHERE IdNode = ' . $this->nodeID 
            . ' AND IdServer =' . $physicalID;
        $this->dbObj->Execute($sql);
    }

    public function getProtocol(int $physicalID)
    {
        $sql = 'SELECT IdProtocol FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('IdProtocol') : null;
    }

    public function getEncode(int $physicalID)
    {
        $sql = 'SELECT idEncode FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('idEncode') : null;
    }

    public function getPassword(int $physicalID)
    {
        $sql = 'SELECT Password FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Password') : '';
    }

    public function getLogin(int $physicalID)
    {
        $sql = 'SELECT Login FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Login') : '';
    }

    public function getHost(int $physicalID)
    {
        $sql = 'SELECT Host FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Host') : '';
    }

    public function getPort(int $physicalID)
    {
        $sql = 'SELECT Port FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Port') : '';
    }

    public function getInitialDirectory(int $physicalID)
    {
        $sql = 'SELECT InitialDirectory FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('InitialDirectory') : '';
    }

    public function getURL(int $physicalID)
    {
        $sql = 'SELECT Url FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        $uri = ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Url') : '';
        if (strlen($uri) >= 0 && isset($uri[strlen($uri) - 1]) && $uri[strlen($uri) - 1] == '/') {
            $uri = substr("$uri", 0, - 1);
        }
        return $uri;
    }

    public function getEnabled(int $physicalID)
    {
        $sql = 'SELECT Enabled FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Enabled') : 0;
    }

    public function getPreview(int $physicalID)
    {
        $sql = 'SELECT Previsual FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Previsual') : 0;
    }

    public function getDescription(int $physicalID)
    {
        $sql = 'SELECT Description FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Description') : '';
    }

    public function getOverrideLocalPaths(int $physicalID)
    {
        $sql = 'SELECT OverrideLocalPaths FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('OverrideLocalPaths') : 0;
    }

    public function getChannels(int $physicalID = null)
    {
        $sql = 'SELECT IdChannel FROM RelServersChannels';
        if ($physicalID != null) {
            $sql .= ' WHERE IdServer=' . $physicalID;
        }
        $this->dbObj->Query($sql);
        $list = array();
        while (! $this->dbObj->EOF) {
            $list[$this->dbObj->GetValue('IdChannel')] = $this->dbObj->GetValue('IdChannel');
            $this->dbObj->Next();
        }
        return $list;
    }

    public function hasChannel(int $physicalID, int $channelID)
    {
        $list = $this->GetChannels($physicalID);
        if (in_array($channelID, $list)) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAllChannels(int $physicalID)
    {
        $sql = 'DELETE FROM RelServersChannels WHERE IdServer = \'' . $physicalID . '\'';
        $this->dbObj->Execute($sql);
    }

    function DeleteChannel($physicalID, $channelID)
    {
        $sql = 'DELETE FROM RelServersChannels WHERE IdServer = \'' . $physicalID . '\' AND IdChannel = \'' . $channelID . '\'';
        $this->dbObj->Execute($sql);
    }

    function AddChannel($physicalID, $channelID)
    {
        $sql = 'INSERT INTO RelServersChannels (IdRel, IdServer, IdChannel) VALUES (NULL, ' . DB::sqlEscapeString($physicalID) 
            . ', ' . DB::sqlEscapeString($channelID) . ')';
        $this->dbObj->Execute($sql);
    }

    public function getPreviewServersForChannel(int $idChannel = null)
    {
        $servers = $this->getPhysicalServerList();
        if (sizeof($servers) > 0) {
            $retList = [];
            foreach ($servers as $idServer) {
                if ($this->GetPreview($idServer) && $this->HasChannel($idServer, $idChannel)) {
                    $retList[] = $idServer;
                }
            }
        }
        return (isset($retList) and $retList) ? $retList[rand(0, count($retList) - 1)] : null;
    }

    public function getStates(int $physicalID)
    {
        $sql = 'SELECT IdState FROM RelServersStates WHERE IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        $list = array();
        while (! $this->dbObj->EOF) {
            $list[] = $this->dbObj->GetValue('IdState');
            $this->dbObj->Next();
        }
        return $list;
    }

    public function hasState(int $physicalID, int $stateID)
    {
        $list = $this->GetStates($physicalID);
        if (in_array($stateID, $list)) {
            return true;
        }
        return false;
    }

    public function deleteAllStates(int $physicalID)
    {
        $sql = 'DELETE FROM RelServersStates WHERE IdServer = \'' . $physicalID . '\'';
        $this->dbObj->Execute($sql);
    }

    public function deleteState(int $physicalID, int $stateID)
    {
        $sql = 'DELETE FROM RelServersStates WHERE IdServer = \'' . $physicalID . '\' AND IdState = \'' . $stateID . '\'';
        $this->dbObj->Execute($sql);
    }

    public function addState(int $physicalID, int $stateID)
    {
        $sql = 'INSERT INTO RelServersStates (IdRel, IdServer, IdState) VALUES (NULL, ' . DB::sqlEscapeString($physicalID) . ', ' 
            . DB::sqlEscapeString($stateID) . ')';
        $this->dbObj->Execute($sql);
    }

    public function getAllAvailableProtocols()
    {
        $sql = 'SELECT IdProtocol FROM Protocols';
        $this->dbObj->Query($sql);
        $list = array();
        while (! $this->dbObj->EOF) {
            $list[] = $this->dbObj->GetValue('IdProtocol');
            $this->dbObj->Next();
        }
        return $list;
    }

    /**
     * Get the documents that must be publicated when the server is published
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FolderNode::getPublishabledDeps()
     */
    public function getPublishabledDeps(array $params = []) : ?array
    {
        $childList = $this->parent->GetChildren();
        $docsToPublish = array();
        foreach ($childList as $childID) {
            $childNode = new Node($childID);
            $childNodeTypeID = $childNode->get('IdNodeType');
            $childNodeType = new NodeType($childNodeTypeID);
            if ($childNodeType->get('Name') == 'TemplatesRootFolder') {
                continue;
            }
            
            /*
             * recurrence IsSection Resultado
             * 0 0 1
             * 0 1 0
             * 1 0 1
             * 1 1 1 => 1x = 1
             */
            if (! (! isset($params['recurrence']) && $childNodeType->get('IsSection'))) {
                $condition = (empty($params['childtype'])) ? null : " AND n.IdNodeType = '{$params['childtype']}'";
                $docsToPublish = array_merge($docsToPublish, $childNode->TraverseTree(6, true, $condition));
                continue;
            }
        }
        return $docsToPublish;
    }
    
    public function getToken(int $physicalID) : ?string
    {
        $sql = 'SELECT Token FROM Servers WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue('Token') : null;
    }
    
    public function setToken(int $physicalID, string $token = null) : void
    {
        $sql = 'UPDATE Servers SET Token = \'' . $token . '\' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }
}
