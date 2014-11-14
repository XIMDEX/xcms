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



if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/model/node.inc');
ModulesManager::file('/inc/helper/Messages.class.php');
ModulesManager::file('/inc/fsutils/TarArchiver.class.php');


define ('CONST_FIRST_ALLOWED_NODE', 'Projects');


class ExportXml {

//	var $xml = '';
	var $nodeID = 0;
	var $_arrNodeId = null;
	var $dbObj = null;
	var $messages = null;
	
	/**
	 * Construct
	 *
	 * @param int $nodeID when the object was obtained, we can check if the node id is the passed one
	 * 		to see if is an allowed node or not; if not, the xml generation process will return an empty string
	 * @return ExportXml
	 */
	function ExportXml($nodeID) {
		$this->dbObj = new DB();
		
		$this->messages = new Messages();
		
		// Checking if the node is inside the Projects folder or not.
		$this->_arrNodeId = array();
		// homogenizing input
		if (!is_array($nodeID)) {
			$nodeID = array($nodeID);
		}

		reset($nodeID);
		while (list(, $idNode) = each($nodeID)) {
			$node = new Node($idNode);
			if (!($node->GetID() > 0)) {
				$this->messages->add(sprintf(_('The node %s does not exist'), $idNode), MSG_TYPE_ERROR);
				continue;
			}
			
			if ($node->nodeType->GetName() == CONST_FIRST_ALLOWED_NODE) {
				$this->_arrNodeId[] = array($idNode);
				continue;
			}
			
			$nodeIsInProyect = false;
			
			do {
				$idParentNode = $node->GetParent();
				if((int) $idParentNode > 0) {
					$node = new Node($idParentNode);
					if ($node->nodeType->GetName() == CONST_FIRST_ALLOWED_NODE) {
						$nodeIsInProyect = true;
						$this->_arrNodeId[] = $idNode;
					}
				}
			} while($idParentNode && !$nodeIsInProyect);
			if (!$nodeIsInProyect) {
				$this->messages->add(sprintf(_('The node %s does not exists or is not included inside of %s'), $idNode, CONST_FIRST_ALLOWED_NODE), MSG_TYPE_ERROR);
			}
		}
	}
	
	/**
	 * It writes in the folder data/backup an exportation of the project xml from a given node with information to re-create this tree
	 *
	 * @param bool $recurrence
	 * @return bool Operation result
	 */
	
	function getXml($recurrence = true, &$files) {
		
		$ximId = Config::getValue('ximid');
		//header
		$xml = sprintf("<ximio-structure id=\"%s\">\n", $ximId);
		
		//Part corresponding with usefull information for the porject importation
		$xml .= $this->_getControlFolder('ChannelManager');
		$xml .= $this->_getControlFolder('LanguageManager');
		$xml .= $this->_getControlFolder('GroupManager');
		
		$xml .= $this->getContentXml($recurrence, $files);
		
		$xml .= "</ximio-structure>";

		return $xml;
	}
	
	function writeData($xml, $files, $fileName = NULL) {
/*		if (!is_null($fileName)) {
			if (!(preg_match('/^[^\s\\\./\*\?\"<>\|]{1}[^\s\\/\*\?\"<>\|]{0,254}$/', $fileName, $matches) > 0)) {
				var_dump($matches);
				unset($fileName);
			}
		}*/
		
		if (is_null($fileName)) {
			$fileName = date("YmdHi");
		}

		$backupLocation = sprintf("%s/data/backup/%s_ximio",XIMDEX_ROOT_PATH, $fileName);
		$tmpFolder = sprintf('%s/data/tmp/%s', XIMDEX_ROOT_PATH, $fileName);
		if (! mkdir($backupLocation, 0775)) {
			error_log(_('Error creating the backup folder, check data folder permits and user&#39;s ones configured in Apache server are compatible.'));
		}
		
		$tar = new TarArchiver($backupLocation . '/files.tar');
		$tar->addEntity($files);
		$tar->pack($tmpFolder);
		
		$xmlFile = fopen($backupLocation . '/ximio.xml', 'w');
		fwrite($xmlFile, $xml);
		fclose($xmlFile);
		
		return $fileName;
	}
	
	
	/**
	 * Obtaining the content to export XML
	 *
	 * @param int $recurrence recurrence level
	 * @param array $files array of files
	 * @return string
	 */
	function getContentXml($recurrence, &$files) {
		global $TOTAL_NODES;
		$db = new DB();
		$files = array();
		reset($this->_arrNodeId);
		while (list(, $idNode) = each($this->_arrNodeId)) {
			$node = new Node($idNode);
			if (defined('COMMAND_MODE_XIMIO')) {
				$query = sprintf('SELECT COUNT(DISTINCT(IdChild)) as total_nodes' . 
					' FROM FastTraverse WHERE IdNode = %s', 
					$idNode);
				$db->query($query);
				echo sprintf(_("The exportation that has as origin the node %s contains %d nodes")." \n", 
					$node->get('Name'), $db->getValue('total_nodes'));
				$TOTAL_NODES = $db->getValue('total_nodes');
			}
			if ($node->GetID() > 0) {
				$xml = $node->ToXml(0, $files, $recurrence);
			}
			unset($node);
		}
		return $xml;
	}

	/**
	 * Obtaining the content of a folder in XML format (thought just for folder inside the control center)
	 *
	 * @param string $nodeTypeName Name of the father of the folder we want to obtain
	 * @param string $tagName Name of the XML tag which is going to contain this information
	 * 
	 * @return string returns the XML of the folder
	 */
	function _getControlFolder($nodeTypeName, $tagName = '') {
		if (empty($nodeTypeName)) return '';
		if (empty($tagName)) $tagName = $nodeTypeName;
		
		// Assuming that nodetypename is unique, as it's a folder located in the control center
		
		$nodeType = new NodeType();
		$nodeType->SetByName($tagName);
		if (!$nodeType->get('IdNodeType') > 0) {
			return '';
		}
		
		$query = sprintf("SELECT IdNode FROM Nodes"
				. " WHERE IdNodeType = %d", $nodeType->ID);
		$this->dbObj->Query($query);
		// If there is more than one folder with this name, nothing is returned due to possible ambiguity
		if ($this->dbObj->numRows != 1) {
			return '';
		}
		$idNode = $this->dbObj->GetValue('IdNode');

		$node = new Node($idNode);
		$childrensToExport = $node->GetChildren();
		
		$xmlResult = '';
		$xmlResult .= sprintf("\t<%s>\n", $tagName);
		$files = array();
		if (is_array($childrensToExport)) {
			foreach ($childrensToExport as $idChildren) {
				$children = new Node($idChildren);
				// $files is ignored, because it is assumed that the nodes are located in the control center and there are no files there.
				$xmlResult .= sprintf("%s\n", $children->ToXml(2, $files));
				unset($children);
			}
			
		}

		$xmlResult .= sprintf("\t</%s>\n", $tagName);
		return $xmlResult;
	}
}
?>