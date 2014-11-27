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

require_once(XIMDEX_ROOT_PATH . '/inc/dependencies/LinksManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_LinkParams extends Abstract_View implements Interface_View {

	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {

		$content = $this->retrieveContent($pointer);
		$version = new Version($idVersion);

		if (!($version->get('IdVersion') > 0)) {
			XMD_Log::error("Incorrect version $idVersion");
			return NULL;
		}

		$node = new Node($version->get('IdNode'));
		$nodeType = new NodeType($node->get('IdNodeType'));
		$nodeId = $node->get('IdNode');
		$nodeTypeName = $nodeType->get('Name');

		if (!($nodeId > 0)) {
			XMD_Log::error("Unexisting node: " . $version->get('IdNode'));
			return NULL;
		}

		$domDoc = new DOMDocument();
		$domDoc->loadXML(\Ximdex\XML\Base::recodeSrc($content, \Ximdex\XML\XML::UTF8));

		$xpath = new DOMXPath($domDoc);
		$nodeList = $xpath->query('/docxap//@*[starts-with(local-name(.), "a_enlaceid")]');

		if ($nodeList->length > 0) {

			foreach ($nodeList as $domNode) {

				$linksManager = new LinksManager();
				$domNode->nodeValue = $linksManager->aenlaceid($domNode->nodeValue);
					
			}

		}

		$nodeList = $xpath->query('/docxap//*[starts-with(local-name(.), "url")]');
		if ($nodeList->length > 0) {

			foreach ($nodeList as $domNode) {

				$linksManager = new LinksManager();
				$domNode->nodeValue = $linksManager->url($domNode->nodeValue);
					
			}

		}

		$content = $domDoc->saveXML();

		return $this->storeTmpContent($content);
	}

}
?>