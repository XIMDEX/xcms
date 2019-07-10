<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\Deps\LinksManager;
use Ximdex\Models\Node;
use Ximdex\Models\Version;

class ViewLinkParams extends AbstractView
{
    /**
     * {@inheritDoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
	{
		$version = new Version($idVersion);
		if (! $version->get('IdVersion')) {   
			Logger::error("Incorrect version $idVersion");
			return false;
		}
		$node = new Node($version->get('IdNode'));
		$nodeId = $node->get('IdNode');
		if (! $nodeId) {
			Logger::error('Unexisting node: ' . $version->get('IdNode'));
			return false;
		}
		$domDoc = new \DOMDocument();
		$domDoc->formatOutput = true;
		$domDoc->preserveWhiteSpace = false;
		$domDoc->loadXML(\Ximdex\XML\Base::recodeSrc($content, \Ximdex\XML\XML::UTF8));
		$xpath = new \DOMXPath($domDoc);
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
		return $domDoc->saveXML();
	}
}
