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

class View_Merge_Tags extends Abstract_View implements Interface_View {
	protected $query1 = '';
	protected $query2 = '';
	protected $merge = '';	
	
	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {

		$domDocument = new DOMDocument();
		$domDocument->preserveWhiteSpace = false;
		$domDocument->validateOnParse = true;
		$domDocument->loadXML(FsUtils::file_get_contents($pointer));
		
		if (!$domDocument) {
			return false;
		}
		
		$xpathExp = new DOMXPath($domDocument);
		
		if ($xpathExp) {
	 		$query1Result = $xpathExp->query($this->query1);
	 		$query2Result = $xpathExp->query($this->query2);
	 		
	 		$mergeResult = $query1Result->item(0)->nodeValue . ',' . $query2Result->item(0)->nodeValue; 
	 		$mergeItem = $xpathExp->query($this->merge);
			
	 		$mergeLength = $mergeItem->length;
	 		if ($mergeLength != 1) {
	 			XMD_Log::error('Wrong count of items detected, returning unmodified document');
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
			
			$textNode = $domDocument->createTextNode($mergeResult);
			$element->appendChild($textNode);
		}
		
		$content = $domDocument->saveXML();
		return $this->storeTmpContent($content);
	}
}
?>
