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




require_once(XIMDEX_ROOT_PATH . '/inc/mvc/App.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_Dependencies extends Abstract_View implements Interface_View {
	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		$content = $this->retrieveContent($pointer);
		preg_match_all("/@@@RMximdex\.pathto\(([0-9,]+)\)@@@/i", $content, $contentTags);
		$deps = $contentTags[count($contentTags)-1];

		/// Y se vuelve a construir
		foreach($deps as $depID) {
			$pair = split(",", $depID);
			$depID = $pair[0];

			if (array_key_exists(1, $pair)) { 
				$channelID = $pair[1]; 
			} else { 
				$channelID = NULL; 
			} 
			$dbObj = App::get('DB');
			// TODO: Check if this SQL runs OK.
			$dbObj->Execute("INSERT INTO SynchronizerDependencies (IdSync, IdResource) VALUES (".$frameID.", ".$depID.")");

		}		
		return $this->storeTmpContent($content);
	}
}
?>
