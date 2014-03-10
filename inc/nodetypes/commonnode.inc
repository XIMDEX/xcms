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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . "/inc/nodetypes/filenode.inc");
ModulesManager::file('/inc/metadata/MetadataManager.class.php');

/***
	Class for NodeType common 
*/
class CommonNode extends FileNode{

	/**
	 * Build a new common node file.
	 * Use parent CreateNode method and generate a new metadata document for the new common node created.	
     * @return boolean true.
     */
	function CreateNode($name, $parentID, $nodeTypeID, $stateID=7, $sourcePath=""){
		parent::CreateNode($name, $parentID, $nodeTypeID, $stateID, $sourcePath);
        $mm = new MetadataManager($this->nodeID);
        $mm->generateMetadata(); 
	}	

	/**
	 * Delete the common file node and its metadata asociated.
 	 */
	function DeleteNode (){
		parent::DeleteNode();
        $mm = new MetadataManager($this->nodeID);
        $mm->deleteMetadata();
	}	

}
?>
