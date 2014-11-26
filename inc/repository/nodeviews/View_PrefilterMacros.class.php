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

class View_PrefilterMacros extends Abstract_View implements Interface_View {
	
	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		$content = $this->retrieveContent($pointer);
		if (preg_match("/@@@GMximdex\.ximlet\(([0-9]+)\)@@@/", $content)) {
			$content = preg_replace_callback("/@@@GMximdex\.ximlet\(([0-9]+)\)@@@/",  
				array($this,'GetXimletContent'), $content);
		}
		
		if (preg_match("/@@@GMximdex\.sections\(([0-9]+)\)@@@/", $content)) {
			$content = preg_replace_callback("/@@@GMximdex\.sections\(([0-9]+)\)@@@/",  
				array($this, 'GetSections_ximTree'), $content);
		}
		
		if (preg_match('/ a_import_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/i', $content)) {  
			$content = preg_replace_callback('/ a_import_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/i' ,  
				array($this, 'GetLocalPath'), $content);
		}
		
		return $this->storeTmpContent($content);
	}

	private function GetXimletContent($matches) {
		$node = new Node($matches[1]);

		if (!($node->get('IdNode') > 0)) {
			return '';
		}
		
		return $node->class->GetContent();
	}
	  

	private function GetSections_ximTree($matches) {

		$node = new Node($matches[1]);
		$retorno = "";
		$codigo = 10;

		if ($node->nodeType->GetIsStructuredDocument()) {
			$strdoc = new StructuredDocument($node->nodeID);
			$langID	 = $strdoc->GetLanguage();
			$retorno = $node->GetSections_ximTree($langID, 2, 1);
			$codigo = 0;
		} else {
			$codigo = 15; // not structured document
		}

		return $retorno;
 	}

	private function GetLocalPath($matches) {
		$node = new Node($matches[2]);

		if ($node->numErr) {
			$absPath = $matches[2];
		} else {
			$pathList = $node->class->GetPathList();			
			$absPath = \App::getValue( "AppRoot") . \App::getValue( "NodeRoot") . $pathList;
 		}
			
		return " a_import_enlaceid{$matches[1]}='$absPath'";
	}
}
?>