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

include_once XIMDEX_ROOT_PATH . "/inc/nodetypes/structureddocument.inc";

class XimletNode extends AbstractStructuredDocument 
{
	function getRefererDocs() {
		$query = sprintf("SELECT Distinct(idNodeDependent) FROM Dependencies WHERE DepType ='XIMLET' AND idNodeMaster = %d", 
			$this->nodeID);

		$this->dbObj->Query($query);
		$docsToPublish = array();	
	
		while (!$this->dbObj->EOF) {
			$docsToPublish[] = $this->dbObj->GetValue("idNodeDependent");
			$this->dbObj->Next();
		}
		
		return $docsToPublish;
	}

	function getLanguage() {
		$strDoc = new StructuredDocument($this->nodeID);
		$langId = $strDoc->get('IdLanguage');

		return !empty($langId) ? $langId : NULL;
	}

	/**
	 * Gets the ximlet dependencies
	 * @param int documentId
	 * @return true / false
	 */

	function GetDependencies() {
		
		$depsMngr = new DepsManager();
		
		$deps = array();

		if ($sections = $depsMngr->getByTarget(DepsManager::SECTION_XIMLET, $this->parent->get('IdNode'))) 
			$deps = array_merge($deps, $sections);
		
		if ($strDocs = $depsMngr->getByTarget(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode')))
			$deps = array_merge($deps, $strDocs);

		return $deps;
	}

	function DeleteNode() {

		// Deletes dependencies in rel tables

		$depsMngr = new DepsManager();
		$depsMngr->deleteByTarget(DepsManager::SECTION_XIMLET, $this->parent->get('IdNode'));
		$depsMngr->deleteByTarget(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_NODE, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_TEMPLATE, $this->parent->get('IdNode'));
		$depsMngr->deleteByTarget(DepsManager::BULLETIN_XIMLET, $this->parent->get('IdNode'));

		XMD_Log::info('Ximlet dependencies deleted');


	}

	/**
	 * Removes the ximlet macro from the document's content
	 * @param int documentId
	 * @return true / false
	 */

	private function removeFromXml($documentId) {

		if (is_null($documentId)) {
			XMD_Log::error('Param documentId required');
			return false;
		}

		$node = new Node($documentId);
		$content = $node->getContent();

		//if (preg_match("/@@@GMximdex\.ximlet\(".$this->nodeID."\)@@@/", $content)) {
			$content = preg_replace("/@@@GMximdex\.ximlet\(([0-9]+)\)@@@/e", '', $content);
			$node->setContent($content);

			return true;
		//}

	}

/**
*	Get the documents that must be publicated when the ximlet is published
*	@param array $params
*	@return array
*/
	public function getPublishabledDeps($params) {
			$depsMngr = new DepsManager();
			return $depsMngr->getByTarget(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode'));
	}

	// The intended use for this method is just generate a colector, is not related with xmldocument
    function generator() {
    	//we need to estimate the colector
    	$node = new Node($this->parent->get('IdParent'));
    	// the colector is the second level parent
    	$idColector = $node->get('IdParent');
		
    	// check the obtained node
    	$colector = new Node($idColector);
    	if ($colector->nodeType->get('Name') != 'XimNewsColector') {
    		XMD_Log::fatal('Se ha estimado un tipo de nodo incorrecto');
    		return false; // xmd::fatal must kill the process anyway, so dont wait any further trace 
    	} 
    	
		return $colector->class->generateColector();
    }
	
	
}		
		
?>
