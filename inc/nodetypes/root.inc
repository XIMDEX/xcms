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

include_once XIMDEX_ROOT_PATH . '/inc/db/db.inc';
include_once XIMDEX_ROOT_PATH . '/inc/patterns/xObject.class.php';
require_once(XIMDEX_ROOT_PATH . '/inc/helper/Messages.class.php');

// Define estructure at inc/xvfs/backends/XVFS_Backend_xnodes.class.php here.
if (!defined('ROOT_NODE')) {
	define('ROOT_NODE', 1);
}

/**
*  @brief Includes the fundamental methods for handling Nodes in ximDEX.
*/

class Root extends xObject {

	var $parent;
	var $nodeID;
	var $dbObj;
	var $nodeType;
	var $numErr;
    var $msgErr;
    var $messages;
	var $errorList= array();

	/**
	*  Constructor.
	*  @param int idNode
	*/

	function Root(&$node = null)	{

		if (is_object($node))
		    $this->parent = $node;
		else if (is_numeric ($node) || $node == null)
		    $this->parent = new Node($node, false);
		$this->nodeID = $this->parent->get('IdNode');
		$this->dbObj = new DB();
		$this->nodeType = &$this->parent->nodeType;
		$this->messages = new Messages();
	}

	/**
	*  Gets the MetaType of a NodeType.
	*  @return string|null
	*/

	function getMetaType() {

		require(XIMDEX_ROOT_PATH . "/inc/io/BaseIOConstants.php");
		global $metaTypesArray;
		$class = get_class($this);

		if (isset($metaTypesArray[strtoupper($class)])) {
			return $metaTypesArray[strtoupper($class)];
		}

		return NULL;
	}

	/**
	*  Gets the path relating to its Project of the Node in the filesystem.
	*  @return string
	*/

	function GetPathList() {

		$idParentNode = $this->parent->get('IdParent');
		$parentNode = new Node($idParentNode);

		if (!($parentNode->get('IdNode') > 0)) {
//			XMD_Log::error('No se ha podido obtener el nodo padre de ' . $parentNode->get('IdNode'));
			return "";
		}

		$parentPath = $parentNode->class->getPathList();

		if (!$this->nodeType->GetIsRenderizable()) {
			XMD_Log::warning('Se ha solicitado el path de un nodo no renderizable con id ' . $this->parent->get('IdNode'));
			return $parentPath;
		}

		/// Obtenemos el path donde el nodo padre guarda a sus hijos
		/// Unimos el path del padre y el nombre del nodo para obtener el path de este nodo si este nodo no es virtual.
/*		if ($this->nodeType->GetIsVirtualFolder()) {
			return $parentPath;
		}
*/
		if ($this->nodeType->GetHasFSEntity()) {
			return $parentPath . "/" .  $this->parent->get('Name');			/// CON ENTIDAD EN EL FS O NO VIRTUAL (ROOT, XML, IMAGES)
		}

		return $parentPath;
	}

	/**
	*  Gets the path for storing the Node children.
	*/

	function GetChildrenPath() {

		return $this->GetPathList();
	}

	/**
	*  Creates the Node in the data/nodes directory.
	*  @return null
	*/

	function RenderizeNode() {

		return null;
	}

	/**
	*  Clears the error messages.
	*  @return unknown
	*/

	function ClearError() {

		$this->numErr = null;
		$this->msgErr = null;
	}

	/**
	*  Sets an error (code and message).
	*  @return unknown
	*/

	function SetError($code) {

		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/**
	*  Checks if has happened any error.
	*  @return bool
	*/

    function HasError() {

        return ($this->numErr != null);
	}

	/**
	*  Builds a XML wich contains the properties of the Node.
	*  @param int depth
	*  @param array files
	*  @param bool recurrence
	*  @return string
	*/

    function ToXml($depth, & $files, $recurrence) {

    	return "";
    }

	/**
	 *  Returns a xml fragment with XimNewsBulletin data.
	 *  @return string
	 */

    function getXmlTail() {

    	return '';
    }

	/**
	*  Gets all channels which transform the Node.
	*  @return array
	*/

    function GetChannels() {

    	return array();
    }

	/**
	*  Gets the content of the Node.
	*  @return string
	*/

	function GetContent() {

		return '';
	}

	/**
	*  Sets the content of the Node.
	*  @return unknown
	*/

	function SetContent($content, $commitNode) {

		return;
	}

	/**
	*  Creates the Node.
	*  @param array args
	*  @return unknown
	*/

	function CreateNode($name = null, $parentID = null, $nodeTypeID = null) {
		$this->UpdatePath();
		return;
	}

	/**
	*  Deletes the Node.
	*  @return unknown
	*/

	function DeleteNode() {

	}

	/**
	 *  Checks whether the NodeType has the property CanDenyDeletion.
	 *  @return bool
	 */

	function CanDenyDeletion () {

		return $this->parent->nodeType->get('CanDenyDeletion');
	}

	/**
	*  Gets the dependencies of the Node.
	*  @return array
	*/

	function GetDependencies() {

		return array();
	}

	function UpdatePath() {
		// Think in root node as a file for performance purposes.
		// This method is overwritten in FileNode and FolderNode.
		$node = new Node($this->nodeID);
		$path = pathinfo($node->GetPath());
		$db = new DB();
		$db->execute(sprintf("update Nodes set Path = '%s' where IdNode = %s", $path['dirname'], $this->nodeID));
	}

	/**
	*  Changes the name of the Node.
	*  @param string name
	*  @return unknown
	*/

	function RenameNode($name = null) {
		$this->UpdatePath();
		return;
	}

	/**
	*  Gets the Url of the Node.
	*  @return string
	*/

	function GetNodeURL() {

		$pathList = $this->GetPathList();
		$relativePath = $pathList;

		return Config::getValue("UrlRoot") . Config::getValue("NodeRoot"). $relativePath ;
	}

	/**
	*  Gets all Nodes of a given NodeType.
	*  @return array|null
	*/

	function getAll() {

		$query = sprintf("SELECT IdNode,Name FROM Nodes WHERE IdNodeType = %d", $this->parent->get('IdNodeType'));
		$this->dbObj->query($query);
		$return = NULL;
		while(!$this->dbObj->EOF) {
			$return[$this->dbObj->getValue('IdNode')] = $this->dbObj->getValue('Name');
			$this->dbObj->next();
		}
		return $return;
	}

	/**
	*  Gets the name in which the Node will be published.
	*  @param int channel
	*  @return string
	*/

	function GetPublishedNodeName($channel = NULL) {

		return $this->parent->get('Name');
	}

	/**
	*  Gets the path of the Node in the data/nodes directory.
	*  @return string
	*/

	function GetNodePath() {

		$pathList = $this->GetPathList();
		$relativePath = $pathList;

		return Config::getValue("AppRoot") . Config::getValue("NodeRoot"). $relativePath ;
	}

	/**
	 *  Checks whether the NodeType has the is_section_index property.
	 *  @return null
	 */

	function getIndex() {

		return NULL;
	}

	/**
	 * Gets the path in which the Node will be published.
	 * @param channelID
	 * @param addNodeName
	 * @return unknown_type
	 */

	function GetPublishedPath($channelID = NULL, $addNodeName) {

		$db = new DB();
		$nodes = array();
		$query = sprintf("SELECT n.IdNode"
				. " FROM `FastTraverse` ft"
				. " INNER JOIN Nodes n USING(IdNode)"
				. " INNER JOIN NodeTypes nt ON n.IdNodeType = nt.IdNodeType AND nt.IsVirtualFolder = 0"
				. " WHERE ft.`IdChild` = %s AND ft.`IdChild` != ft.`IdNode` order by ft.Depth DESC",
					$db->sqlEscapeString($this->parent->get('IdNode')));

		$db->query($query);
		while(!$db->EOF) {
			$node = new Node($db->getValue('IdNode'));
			$nodes[] = $node->GetPublishedNodeName($channelID);
			$db->next();
		}

		if ($addNodeName && !$this->nodeType->get('IsVirtualFolder')) {
			$parent = new Node($this->parent->get('IdNode'));
			$nodes[] = $parent->GetPublishedNodeName($channelID);
		}

		return '/' . implode('/', $nodes);
	}

}

?>
