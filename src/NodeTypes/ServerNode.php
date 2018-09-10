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
     * @param $hidePrevisual --> true --> dont get the "previous" servers
     * @param bool $enabled
     * @return array $list
     */
    function GetPhysicalServerList($hidePrevisual = NULL, bool $enabled = false)
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
        $sql = "SELECT IdServer FROM Servers WHERE Enabled = 1 AND ActiveForPumping = 1";
        $dbObj->Query($sql);
        $enabledServers = array();
        while (! $dbObj->EOF) {
            $enabledServers[] = $dbObj->GetValue("IdServer");
            $dbObj->Next();
        }
        return $enabledServers;
    }

    function CreateNode($name = NULL, $parentID = NULL, $nodeTypeID = NULL)
    {
        $this->updatePath();
    }

    function DeleteNode()
    {
        $servers = $this->GetPhysicalServerList();
        if ($servers) {
            foreach ($servers as $serverID) {
                $this->DeletePhysicalServer($serverID);
            }
        }
    }

    public function AddPhysicalServer($protocolID, $login, $password, $host, $port, $url, $initialDirectory, $overrideLocalPaths, $enabled
        , $previsual, $description, string $token = null)
    {
        if (! ($overrideLocalPaths)) {
            $overrideLocalPaths = 0;
        }
        if (! ($enabled)) {
            $enabled = 0;
        }
        if (! ($previsual)) {
            $previsual = 0;
        }
        $sql = "INSERT INTO Servers ";
        $sql .= "(IdServer, IdNode, IdProtocol, Login, Password, Host,";
        $sql .= " Port, Url, InitialDirectory, OverrideLocalPaths, Enabled, Previsual, Description, Token) ";
        $sql .= "VALUES ";
        $sql .= "(NULL, " . DB::sqlEscapeString($this->parent->get('IdNode')) . ", " . DB::sqlEscapeString($protocolID) . ", " 
            . DB::sqlEscapeString($login) . ", " . DB::sqlEscapeString($password) . ", " . DB::sqlEscapeString($host) . ",";
        $sql .= " " . DB::sqlEscapeString($port) . ", " . DB::sqlEscapeString($url) . ", " . DB::sqlEscapeString($initialDirectory) . ", ";
        $sql .= DB::sqlEscapeString($overrideLocalPaths) . ", " . DB::sqlEscapeString($enabled) . ", " . DB::sqlEscapeString($previsual) . ", " 
            . DB::sqlEscapeString($description) . ', ' . DB::sqlEscapeString($token) . ")";
        $this->dbObj->Execute($sql);
        return $this->dbObj->newID;
    }

    function DeletePhysicalServer($physicalID)
    {
        $this->DeleteAllChannels($physicalID);
        $this->DeleteAllStates($physicalID);
        $sql = "DELETE FROM Servers WHERE IdServer='" . $physicalID . "' AND IdNode='" . $this->nodeID . "'";
        $this->dbObj->Execute($sql);
    }

    function SetProtocol($physicalID, $protocolID)
    {
        $sql = "UPDATE Servers SET IdProtocol= '" . $protocolID . "' WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetEncode($physicalID, $encodeID)
    {
        $sql = "UPDATE Servers SET IdEncode= '" . $encodeID . "' WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetPassword($physicalID, $pass)
    {
        $sql = "UPDATE Servers SET Password= " . DB::sqlEscapeString($pass) . " WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetLogin($physicalID, $login)
    {
        $sql = "UPDATE Servers SET Login= " . DB::sqlEscapeString($login) . " WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetHost($physicalID, $host)
    {
        $sql = "UPDATE Servers SET Host= " . DB::sqlEscapeString($host) . " WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetPort($physicalID, $port)
    {
        $sql = "UPDATE Servers SET Port=" . DB::sqlEscapeString($port) . " WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetInitialDirectory($physicalID, $dir)
    {
        $sql = "UPDATE Servers SET InitialDirectory= '" . $dir . "' WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetURL($physicalID, $url)
    {
        $sql = "UPDATE Servers SET Url= '" . $url . "' WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetEnabled($physicalID, $enable)
    {
        $enable = ($enable) ? 'true' : 'false';
        $sql = "UPDATE Servers SET Enabled = " . $enable . " WHERE IdNode = " . $this->nodeID . " AND IdServer = " . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetPreview($physicalID, $preview)
    {
        $preview = ($preview) ? 'true' : 'false';
        $sql = "UPDATE Servers SET Previsual = " . $preview . " WHERE IdNode = " . $this->nodeID . " AND IdServer = " . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetDescription($physicalID, $description)
    {
        $sql = "UPDATE Servers SET Description= '" . $description . "' WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function SetOverrideLocalPaths($physicalID, $overrideLocalPaths)
    {
        $overrideLocalPaths = ($overrideLocalPaths) ? 'true' : 'false';
        $sql = "UPDATE Servers SET OverrideLocalPaths= " . $overrideLocalPaths . " WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Execute($sql);
    }

    function GetProtocol($physicalID)
    {
        $sql = "SELECT IdProtocol FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("IdProtocol") : NULL;
    }

    function GetEncode($physicalID)
    {
        $sql = "SELECT idEncode FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("idEncode") : NULL;
    }

    function GetPassword($physicalID)
    {
        $sql = "SELECT Password FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Password") : "";
    }

    function GetLogin($physicalID)
    {
        $sql = "SELECT Login FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Login") : "";
    }

    function GetHost($physicalID)
    {
        $sql = "SELECT Host FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Host") : "";
    }

    function GetPort($physicalID)
    {
        $sql = "SELECT Port FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Port") : "";
    }

    function GetInitialDirectory($physicalID)
    {
        $sql = "SELECT InitialDirectory FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("InitialDirectory") : "";
    }

    function GetURL($physicalID)
    {
        $sql = "SELECT Url FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        $uri = ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Url") : "";
        if (strlen($uri) >= 0 && isset($uri[strlen($uri) - 1]) && $uri[strlen($uri) - 1] == "/") {
            $uri = substr("$uri", 0, - 1);
        }
        return $uri;
    }

    function GetEnabled($physicalID)
    {
        $sql = "SELECT Enabled FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Enabled") : 0;
    }

    function GetPreview($physicalID)
    {
        $sql = "SELECT Previsual FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Previsual") : 0;
    }

    function GetDescription($physicalID)
    {
        $sql = "SELECT Description FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Description") : "";
    }

    function GetOverrideLocalPaths($physicalID)
    {
        $sql = "SELECT OverrideLocalPaths FROM Servers WHERE IdNode=" . $this->nodeID . " AND IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("OverrideLocalPaths") : 0;
    }

    function GetChannels($physicalID = NULL)
    {
        $sql = 'SELECT IdChannel FROM RelServersChannels';
        if ($physicalID != NULL) {
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

    function HasChannel($physicalID, $channelID)
    {
        $list = $this->GetChannels($physicalID);
        if (in_array($channelID, $list)) {
            return true;
        } else {
            return false;
        }
    }

    function DeleteAllChannels($physicalID)
    {
        $sql = "DELETE FROM RelServersChannels WHERE IdServer='" . $physicalID . "'";
        $this->dbObj->Execute($sql);
    }

    function DeleteChannel($physicalID, $channelID)
    {
        $sql = "DELETE FROM RelServersChannels WHERE IdServer='" . $physicalID . "' AND IdChannel='" . $channelID . "'";
        $this->dbObj->Execute($sql);
    }

    function AddChannel($physicalID, $channelID)
    {
        $sql = "INSERT INTO RelServersChannels (IdRel, IdServer, IdChannel) VALUES (NULL, " . DB::sqlEscapeString($physicalID) . ", " . DB::sqlEscapeString($channelID) . ")";
        $this->dbObj->Execute($sql);
    }

    function GetPreviewServersForChannel($idChannel)
    {
        $servers = $this->GetPhysicalServerList();
        if (sizeof($servers) > 0) {
            foreach ($servers as $idServer) {
                if ($this->GetPreview($idServer) && $this->HasChannel($idServer, $idChannel)) {
                    $retList[] = $idServer;
                }
            }
        }
        return isset($retList) ? $retList[rand(0, count($retList) - 1)] : NULL;
    }

    function GetStates($physicalID)
    {
        $sql = "SELECT IdState FROM RelServersStates WHERE IdServer=" . $physicalID;
        $this->dbObj->Query($sql);
        $list = array();
        while (! $this->dbObj->EOF) {
            $list[] = $this->dbObj->GetValue("IdState");
            $this->dbObj->Next();
        }
        return $list;
    }

    function HasState($physicalID, $stateID)
    {
        $list = $this->GetStates($physicalID);
        if (in_array($stateID, $list)) {
            return true;
        }
        return false;
    }

    function DeleteAllStates($physicalID)
    {
        $sql = "DELETE FROM RelServersStates WHERE IdServer='" . $physicalID . "'";
        $this->dbObj->Execute($sql);
    }

    function DeleteState($physicalID, $stateID)
    {
        $sql = "DELETE FROM RelServersStates WHERE IdServer='" . $physicalID . "' AND IdState='" . $stateID . "'";
        $this->dbObj->Execute($sql);
    }

    function AddState($physicalID, $stateID)
    {
        $sql = "INSERT INTO RelServersStates (IdRel, IdServer, IdState) VALUES (NULL, " . DB::sqlEscapeString($physicalID) . ", " 
            . DB::sqlEscapeString($stateID) . ")";
        $this->dbObj->Execute($sql);
    }

    function GetAllAvailableProtocols()
    {
        $sql = "SELECT IdProtocol FROM Protocols";
        $this->dbObj->Query($sql);
        $list = array();
        while (! $this->dbObj->EOF) {
            $list[] = $this->dbObj->GetValue("IdProtocol");
            $this->dbObj->Next();
        }
        return $list;
    }

    function ToXml($depth, & $files, $recurrence)
    {
        return parent::ToXml($depth, $files, $recurrence);
    }

    /**
     * Get the documents that must be publicated when the server is published
     * 
     * @param array $params
     * @return array
     */
    public function getPublishabledDeps($params)
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
                $condition = (empty($params['childtype'])) ? NULL : " AND n.IdNodeType = '{$params['childtype']}'";
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
    
    public function SetToken(int $physicalID, string $token = null) : void
    {
        $sql = 'UPDATE Servers SET Token = \'' . $token . '\' WHERE IdNode = ' . $this->nodeID . ' AND IdServer = ' . $physicalID;
        $this->dbObj->Execute($sql);
    }
}