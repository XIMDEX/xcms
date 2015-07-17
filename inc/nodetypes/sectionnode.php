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

include_once XIMDEX_ROOT_PATH . "/inc/nodetypes/foldernode.php";
require_once(XIMDEX_ROOT_PATH . '/inc/dependencies/DepsManager.class.php');

/**
*  @brief Handles ximDEX sections.
*/

class SectionNode extends FolderNode {

	/**
	*	Gets the documents that must be published together with the section. 
	*	@param array params
	*	@return array
	*/

	function getPublishabledDeps($params) {

		$childList = $this->parent->GetChildren();
		$node = new Node($this->parent->get('IdNode'));
		$idNodeType = $node->get('IdNodeType');
		$nodeType = new NodeType($idNodeType);
		$nodeTypeName = $nodeType->get('Name');

		$isOTFSection = $node->getSimpleBooleanProperty('otf');
		if ($nodeTypeName=='XimNewsSection'){
			$sectionId = $this->parent->get('IdNode');
		}else{
			$sectionId = null;
		}

		$docsToPublish = array();
		foreach($childList as $childID) {
			$childNode = new Node($childID);
			$childNodeTypeID = $childNode->get('IdNodeType');
			$childNodeType = new NodeType($childNodeTypeID);
			$childNodeTypeName = $childNodeType->get('Name');

			if(isset($params['recurrence']) || ($childNodeTypeName != "Section" && !isset($params['recurrence']))) {
			// filter bulletins nodetype
				if ($isOTFSection) {		

					$condition = (empty($params['childtype'])) ? NULL : " AND n.IdNodeType = '{$params['childtype']}'";
					$docsToPublish = $node->TraverseTree(6, true, $condition);
				} else {
					 $docsToPublish = array_merge($docsToPublish,$childNode->TraverseTree(6));
				}
			}
		}

		return $docsToPublish;
	}

	/**
	*  Deletes the Section and its dependencies.
	*  @return unknown
	*/

	function DeleteNode() {
		
		// Deletes dependencies in rel tables

		$depsMngr = new DepsManager();
		$depsMngr->deleteBySource(DepsManager::SECTION_XIMLET, $this->parent->get('IdNode'));

		XMD_Log::info('Section dependencies deleted');
	}

	/**
	*  Gets all Nodes of NodeType XimNewsNewLanguage that belong to the Section.
	*  @return array
	*/

	function getAllXimNewsForIdSection($idSection){

		//Not include news with shared workflow, this will be add in publicate method, batchmanager

		$sql = "select n.IdNew as IdNew, no.SharedWorkflow from XimNewsNews as n inner join Nodes as no on n.IdNew =no.IdNode and isnull(no.SharedWorkflow)";

		if ($idSection != null){
			$sql .= " and IdSection = $idSection";
		}

		$news = array();
		$i=0;
		$dbObj = new DB();
		$dbObj->Query($sql);
		
		while(!$dbObj->EOF) {
			$news[$i] = $dbObj->GetValue("IdNew");
			$i++;
			$dbObj->Next();
		}
		
		return $news;
	}
}