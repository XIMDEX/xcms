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

require_once(XIMDEX_ROOT_PATH . "/inc/nodetypes/filenode.php");
require_once(XIMDEX_ROOT_PATH . '/inc/parsers/ParsingRng.class.php');

/**
*  @brief Handles RNG templates.
*/

class rngvisualtemplatenode extends FileNode {

	var $minimalXml = '';
	var $xpathObj;
	var $relaxSource;
	var $elementsForRender;
	var $renderCount = 0;

	/**
	*  Creates the file in data/files directory.
	*  @param string name
	*  @param int parentID
	*  @param int nodeTypeID
	*  @param int stateID
	*  @param string sourcePath
	*  @return unknown
	*/

	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $sourcePath = null) {

		$content = FsUtils::file_get_contents($sourcePath);

		$data = new DataFactory($this->parent->get('IdNode'));
		$this->updatePath();
		return $data->SetContent($content);
	}

	/**
	*  Gets the minimal content of a document created by a template.
	*  @return string
	*/

	function buildDefaultContent() {

		$rngParser = new ParsingRng();
		$content = $rngParser->buildDefaultContent($this->nodeID);

		return $content;
	}

}