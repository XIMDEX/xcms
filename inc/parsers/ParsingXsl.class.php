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

class ParsingXsl {

	private $xpathObj;
	private $idTemplate = NULL;
	private $node = NULL;
	private $includedElements = array();
	private $path = NULL;

	function __construct($idTemplate = NULL, $path = NULL) {
		if(!$this->setNode($idTemplate, $path))
			return NULL;
		$this->setXpathObj();
		$this->setIncludedElements();
	}

	public function getIncludedElements($name = NULL, $removeExtension = false, $baseName = false) {
		if($removeExtension || $baseName)
			$this->setIncludedElements($removeExtension, $baseName);

		if(is_null($name))
			return $this->includedElements;

		$out = array();
		foreach($this->includedElements as $includedElement) {
			if(strpos($includedElement, $name) !== false)
				$out[] = $includedElement;
		}

		return $out;
	}

	private function setIncludedElements($removeExtension = false, $baseName = false) {

		$this->includedElements = array();

		if(!$this->setXpathObj())
			return false;

		$nodeList = $this->xpathObj->query('//*[local-name(.)="include"]');
		if ($nodeList->length > 0) {
			foreach ($nodeList as $domNode) {
				if($domNode->nodeName == 'xsl:include') {
					$templateRef = $domNode->getAttribute('href');
					if($baseName)
						$templateRef = basename($templateRef);
					if($removeExtension)
						$templateRef = str_replace('.xsl', '', $templateRef);
					$this->includedElements[] = $templateRef;
				}
			}
		}

		return true;
	}

	private function setNode($idNode, $path) {
		if(is_null($idNode) && is_null($path)) {
			XMD_Log::error('Cannot parse template: idNode and path are NULL');
			return false;
		}

		if(is_null($idNode)) {
			$this->path = $path;
			return true;
		}

		$this->node = new Node($idNode);
		if(!($this->node->get('IdNode')) > 0) {
			XMD_Log::error('Cannot parse template: Non existant node ' . $idNode);
			return false;
		}

		if ($this->node->nodeType->get('Name') != 'XslTemplate') {
			XMD_Log::error('Cannot parse template: Node ' . $idNode . ' is not a Xsl Template');
			return false;
		}

		$this->idTemplate = $idNode;
		return true;
	}

	private function setXpathObj() {
		if($this->node)
			$content = $this->node->GetContent();
		else
			$content = FsUtils::file_get_contents($this->path);
		$domDoc = new DOMDocument();
		$domDoc->preserveWhiteSpace = false;
		$domDoc->validateOnParse = true;
	   $domDoc->formatOutput = true;
		$domDoc->loadXML($content);

		$this->xpathObj = new DOMXPath($domDoc);

		return true;
	}
}
?>
