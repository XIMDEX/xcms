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










if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

include_once XIMDEX_ROOT_PATH . "/inc/nodetypes/foldernode.inc";
include_once XIMDEX_ROOT_PATH . "/inc/sync/synchro.inc";
require_once XIMDEX_ROOT_PATH . "/inc/model/Server.class.php";

class ServerNode extends FolderNode
{

	const SERVERS_OTF = '1';
	const SERVERS_NO_OTF ='2';
	const ALL_SERVERS = '3';


	/**
	 * Get the Physical Server List
	 * @param $hidePrevisual --> true --> dont get the "previous" servers
	 * @param $otfServer
	 * @return array $list
	 */
	function GetPhysicalServerList($hidePrevisual = NULL, $otfServer = NULL) {

		if (!is_null($hidePrevisual)) {
			$sql = "SELECT IdServer FROM Servers WHERE Previsual != 1 AND IdNode=".$this->nodeID;
		}else{
			$sql = "SELECT IdServer FROM Servers WHERE IdNode=".$this->nodeID;
		}

		if (!is_null($otfServer)) {

			if ($otfServer == ServerNode::SERVERS_OTF){
				$sql .=  " AND otf=1";
			}else if ($otfServer == ServerNode::SERVERS_NO_OTF){
				$sql .=  " AND otf!=1";
			}
		}

		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			$list[] = $this->dbObj->GetValue("IdServer");
			$this->dbObj->Next();
		}

		return $list;
	}

	/**
	 * Get the enabled physical servers
	 * @param $hidePrevisual --> true --> dont get the "previous" servers
	 * @param $otfServer
	 * @return array $list
	 */
	function GetEnabledPhysicalServerList($hidePrevisual = NULL, $otfServer = NULL) {

		$server = new Server();

		if (!is_null($hidePrevisual)) {
			$where = "Previsual!=1 AND IdNode=%s AND Enabled=1";
		} else {
			$where = "IdNode=%s AND Enabled=1";
		}

		if (!is_null($otfServer)) {

			if ($otfServer == ServerNode::SERVERS_OTF){
				$where .=  " AND otf=1";
			}else if ($otfServer == ServerNode::SERVERS_NO_OTF){
				$where .=  " AND otf!=1";
			}
		}

		return $server->find('IdServer', $where, array($this->nodeID), MONO);
	}

	public static function getServersForPumping() {

		$dbObj = new DB();
		$sql = "SELECT IdServer FROM Servers WHERE Enabled = 1 AND ActiveForPumping = 1";
		$dbObj->Query($sql);

		$enabledServers = array();
		while(!$dbObj->EOF) {

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
		if($servers) foreach ($servers as $serverID)
		{
			$this->DeletePhysicalServer($serverID);
		}
	}


	function AddPhysicalServer($protocolID, $login, $password, $host, $port,
		$url, $initialDirectory, $overrideLocalPaths, $enabled, $previsual, $description,$isServerOTF = false) {

		if (!($overrideLocalPaths)) {
			$overrideLocalPaths=0;
		}
		if (!($enabled)) {
			$enabled=0;
		}
		if (!($previsual)) {
			$previsual=0;
		}
		if ($isServerOTF){
			$serverOTF = 1;
		}else{
			$serverOTF = 0;
		}

		$sql = "INSERT INTO Servers ";
		$sql.= "(IdServer, IdNode, IdProtocol, Login, Password, Host,";
		$sql.= " Port, Url, InitialDirectory, OverrideLocalPaths, Enabled, Previsual, Description,otf) ";
		$sql.= "VALUES ";
		$sql.= "(NULL, ".DB::sqlEscapeString($this->parent->get('IdNode')).", ".DB::sqlEscapeString($protocolID).", ".DB::sqlEscapeString($login).", ".DB::sqlEscapeString($password).", ".DB::sqlEscapeString($host).",";
		$sql.= " ".DB::sqlEscapeString($port).", ".DB::sqlEscapeString($url).", ".DB::sqlEscapeString($initialDirectory).", ";
		$sql.= DB::sqlEscapeString($overrideLocalPaths).", ".DB::sqlEscapeString($enabled).", ".DB::sqlEscapeString($previsual).", ".DB::sqlEscapeString($description).", ".$serverOTF.")";
		$this->dbObj->Execute($sql);



		return $this->dbObj->newID;
	}

	function DeletePhysicalServer($physicalID)
	{
		$this->DeleteAllChannels($physicalID);
		$this->DeleteAllStates($physicalID);
		$sql = "DELETE FROM Servers WHERE IdServer='".$physicalID."' AND IdNode='".$this->nodeID."'";
		$this->dbObj->Execute($sql);

		// Deleting frames

		$sync = new Synchronizer();
		$sync->deleteByColumn($physicalID, 'IdServer');
	}

	function SetProtocol($physicalID, $protocolID)
	{
		$sql = "UPDATE Servers SET IdProtocol= '".$protocolID."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetEncode($physicalID, $encodeID)
	{
		$sql = "UPDATE Servers SET IdEncode= '".$encodeID."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function setIsOTF($isServerOTF,$physicalID){
		$sql = "UPDATE Servers SET otf= '".$isServerOTF."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetPassword($physicalID, $pass)
	{
		$sql = "UPDATE Servers SET Password= '".$pass."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetLogin($physicalID, $login)
	{
		$sql = "UPDATE Servers SET Login= '".$login."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetHost($physicalID, $host)
	{
		$sql = "UPDATE Servers SET Host= '".$host."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetPort($physicalID, $port)
	{
		$sql = "UPDATE Servers SET Port= '".$port."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetInitialDirectory($physicalID, $dir)
	{
		$sql = "UPDATE Servers SET InitialDirectory= '".$dir."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetURL($physicalID, $url)
	{
		$sql = "UPDATE Servers SET Url= '".$url."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetEnabled($physicalID, $enable)
	{
		$sql = "UPDATE Servers SET Enabled= '".$enable."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetPreview($physicalID, $preview)
	{
		$sql = "UPDATE Servers SET Previsual= '".$preview."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetDescription($physicalID, $description)
	{
		$sql = "UPDATE Servers SET Description= '".$description."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function SetOverrideLocalPaths($physicalID, $overrideLocalPaths)
	{
		$sql = "UPDATE Servers SET OverrideLocalPaths= '".$overrideLocalPaths."' WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Execute($sql);
	}

	function GetProtocol($physicalID)
	{
		$sql = "SELECT IdProtocol FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("IdProtocol") : NULL;
	}

	function GetEncode($physicalID)
	{
		$sql = "SELECT idEncode FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("idEncode") : NULL;
	}

	function GetPassword($physicalID)
	{
		$sql = "SELECT Password FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Password") : "";
	}

	function GetLogin($physicalID)
	{
		$sql = "SELECT Login FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Login") : "";
	}

	function GetHost($physicalID)
	{
		$sql = "SELECT Host FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Host") : "";
	}

	function GetPort($physicalID)
	{
		$sql = "SELECT Port FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Port") : "";
	}

	function GetInitialDirectory($physicalID)
	{
		$sql = "SELECT InitialDirectory FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("InitialDirectory") : "";
	}

	function GetURL($physicalID)
	{
		$sql = "SELECT Url FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		$uri = ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Url") : "";

		if(strlen($uri) >= 0 &&
			isset($uri[strlen($uri)-1]) &&
			$uri[strlen($uri)-1] == "/") {
			$uri = substr("$uri", 0, -1);
		}

		return $uri;
	}

	function GetEnabled($physicalID)
	{
		$sql = "SELECT Enabled FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Enabled") : 0;
	}

	function GetPreview($physicalID)
	{
		$sql = "SELECT Previsual FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Previsual") : 0;
	}

	function GetDescription($physicalID)
	{
		$sql = "SELECT Description FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("Description") : "";
	}

	function GetOverrideLocalPaths($physicalID)
	{
		$sql = "SELECT OverrideLocalPaths FROM Servers WHERE IdNode=".$this->nodeID." AND IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("OverrideLocalPaths") : 0;
	}

	function GetChannels($physicalID = NULL) {

		$sql = "SELECT IdChannel FROM RelServersChannels";
		if ($physicalID != NULL)	{
			$sql .= " WHERE IdServer=".$physicalID;
		}

		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			$list[] = $this->dbObj->GetValue("IdChannel");
			$this->dbObj->Next();
		}

		return $list;
	}

	function HasChannel($physicalID, $channelID)
	{
		$list = $this->GetChannels($physicalID);

		if(in_array($channelID, $list)){
			return true;
		}else{
			return false;
		}
	}

	function DeleteAllChannels($physicalID)
	{
		$sql = "DELETE FROM RelServersChannels WHERE IdServer='".$physicalID."'";
		$this->dbObj->Execute($sql);
	}

	function DeleteChannel($physicalID, $channelID)
	{
		$sql = "DELETE FROM RelServersChannels WHERE IdServer='".$physicalID."' AND IdChannel='".$channelID."'";
		$this->dbObj->Execute($sql);
	}

	function AddChannel($physicalID, $channelID)
	{
		$sql = "INSERT INTO RelServersChannels (IdRel, IdServer, IdChannel) VALUES (NULL, ".DB::sqlEscapeString($physicalID).", ".DB::sqlEscapeString($channelID).")";
		$this->dbObj->Execute($sql);
	}

	function GetPreviewServersForChannel($idChannel) {

		$servers = $this->GetPhysicalServerList();

		if (sizeof($servers) > 0) {

			foreach ($servers as $idServer)

				if ($this->GetPreview($idServer) && $this->HasChannel($idServer, $idChannel)) {
					$retList[] = $idServer;
				}
		}

		return isset($retList) ? $retList[rand(0, sizeof($retList)-1)] : NULL;
	}

	function GetStates($physicalID)
	{
		$sql = "SELECT IdState FROM RelServersStates WHERE IdServer=".$physicalID;
		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			$list[] = $this->dbObj->GetValue("IdState");
			$this->dbObj->Next();
		}

		return $list;
	}

	function HasState($physicalID, $stateID)
	{
		$list = $this->GetStates($physicalID);

		if(in_array($stateID, $list))
		return true;
		else
		return false;
	}

	function DeleteAllStates($physicalID)
	{
		$sql = "DELETE FROM RelServersStates WHERE IdServer='".$physicalID."'";
		$this->dbObj->Execute($sql);
	}

	function DeleteState($physicalID, $stateID)
	{
		$sql = "DELETE FROM RelServersStates WHERE IdServer='".$physicalID."' AND IdState='".$stateID."'";
		$this->dbObj->Execute($sql);
	}

	function AddState($physicalID, $stateID)
	{
		$sql = "INSERT INTO RelServersStates (IdRel, IdServer, IdState) VALUES (NULL, ".DB::sqlEscapeString($physicalID).", ".DB::sqlEscapeString($stateID).")";
		$this->dbObj->Execute($sql);
	}

	function GetAllAvailableProtocols()
	{
		$sql = "SELECT IdProtocol FROM Protocols";
		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			$list[] = $this->dbObj->GetValue("IdProtocol");
			$this->dbObj->Next();
		}

		return $list;
	}

	function ToXml($depth, & $files, $recurrence) {
		return parent::ToXml($depth, $files, $recurrence);
	}

/**
*	Get the documents that must be publicated when the server is published
*	@param array $params
*	@return array
*/
	public function getPublishabledDeps($params) {

		$childList = $this->parent->GetChildren();
		$docsToPublish = array();
		foreach($childList as $childID) {

			$childNode = new Node($childID);
			$childNodeTypeID = $childNode->get('IdNodeType');
			$childNodeType = new NodeType($childNodeTypeID);

			// avoiding the publication of xsl templates in mode otf = null

			if (!isset($params['otf']) && $childNodeType->get('Name') == 'TemplatesRootFolder') continue;
/*
	recurrence IsSection Resultado
	0			0			1
	0			1			0
	1			0			1
	1			1			1			 => 1x = 1
*/

			if(!(!isset($params['recurrence']) && $childNodeType->get('IsSection'))) {

				$condition = (empty($params['childtype'])) ? NULL : " AND n.IdNodeType = '{$params['childtype']}'";
				$docsToPublish = array_merge($docsToPublish, $childNode->TraverseTree(6, true, $condition));
				continue;
			}
		}

		return $docsToPublish;
	}

}

?>
