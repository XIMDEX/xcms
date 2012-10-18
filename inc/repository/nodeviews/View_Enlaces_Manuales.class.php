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




require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/xml/XSLT.class.php');

class View_Enlaces_Manuales extends Abstract_View implements Interface_View {
	
	
	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		$content = $this->retrieveContent($pointer);
		$domDocument = new DOMDocument();
		$domDocument->preserveWhiteSpace = false;
		$domDocument->validateOnParse = true;
		$domDocument->loadXML($content);
		if (!$domDocument) {
			XMD_Log::error('Failed to load xml in View_Enlaces_Manuales');
			return $pointer;
		}
		$xpathExp = new DOMXPath($domDocument);
		
		if ($xpathExp) {
	 		$query1Result = $xpathExp->query('//doclink');
	 		$query2Result = $xpathExp->query('//enlacesmanuales');
	 		
	 		$doclinks = array();
	 		for ($i = 0; $i < $query1Result->length; $i ++) {
	 			$docLinkElement = $query1Result->item($i);
	 			$doclinks[] = $query1Result->item($i)->getAttributeNode('ref')->value;
	 		}

	 		$enlacesManualesContent = $query2Result->item(0)->nodeValue;
	 		$enlacesManualesArray = array();
	 		if (!empty($enlacesManualesContent)) {
	 			$enlacesManualesArray = explode(',', $enlacesManualesContent);
	 			foreach($enlacesManualesArray as $key => $value) {
	 				$enlacesManualesArray[$key] = trim($value);
	 			}
	 		}
	 		$diff = array_diff($enlacesManualesArray, $doclinks);
	 		 
	 		$enlacesManualesAEliminar = implode(',', $diff);
	 		
	 		$mergeItem = $xpathExp->query('//enlacesmanualesaeliminar');
			
	 		$mergeLength = $mergeItem->length;
	 		if ($mergeLength != 1) {
	 			XMD_Log::error('Wrong count of items detected');
	 			return $pointer;
	 		}
	 		
 			$element = $mergeItem->item(0);
			$childNodes = $element->childNodes;
			$childNodesLength = $childNodes->length;
			for ($j = 0; $j < $childNodesLength; $j++) {
				$item = $childNodes->item($j);
				if (strtolower(get_class($item)) == 'domtext') {
					$element->removeChild($item);
				}
			}
			
			$textNode = $domDocument->createTextNode($enlacesManualesAEliminar);
			$element->appendChild($textNode);
			

	 		$updateItem = $xpathExp->query('//enlacesmanuales');
			
	 		$mergeLength = $updateItem->length;
	 		if ($mergeLength != 1) {
	 			XMD_Log::error('Wrong count of items detected');
	 			return $pointer;
	 		}
	 		
 			$element = $updateItem->item(0);
			$childNodes = $element->childNodes;
			$childNodesLength = $childNodes->length;
			for ($j = 0; $j < $childNodesLength; $j++) {
				$item = $childNodes->item($j);
				if (strtolower(get_class($item)) == 'domtext') {
					$element->removeChild($item);
				}
			}
			
			$textNode = $domDocument->createTextNode(implode(',', $doclinks));
			$element->appendChild($textNode);
			
			
		}
		
		return $this->storeTmpContent($domDocument->saveXML());
		
	}
	

}
?>
