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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


namespace Ximdex\Parsers;

use DOMDocument;
use DOMXPath;
use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;


class ParsingXsl
{

	private $xpathObj;
	private $idTemplate = NULL;
	private $node = NULL;
	private $includedElements = array();
	private $path = NULL;
	private $editor = true;

	function __construct($idTemplate = NULL, $path = NULL, $editor = true)
	{
		if (!$this->setNode($idTemplate, $path))
			return NULL;
		$this->setXpathObj();
		$this->setIncludedElements();
		$this->editor = $editor;
	}

	public function getIncludedElements($name = NULL, $removeExtension = false, $baseName = false)
	{
		if ($removeExtension || $baseName)
			$this->setIncludedElements($removeExtension, $baseName);

		if (is_null($name))
			return $this->includedElements;

		$out = array();
		foreach ($this->includedElements as $includedElement) {
			if (strpos($includedElement, $name) !== false)
				$out[] = $includedElement;
		}

		return $out;
	}

	private function setIncludedElements($removeExtension = false, $baseName = false)
	{

		$this->includedElements = array();

		if (!$this->setXpathObj())
			return false;

		$nodeList = $this->xpathObj->query('//*[local-name(.)="include"]');
		if ($nodeList->length > 0) {
			foreach ($nodeList as $domNode) {
				if ($domNode->nodeName == 'xsl:include') {
					$templateRef = $domNode->getAttribute('href');
					if ($baseName)
						$templateRef = basename($templateRef);
					if ($removeExtension)
						$templateRef = str_replace('.xsl', '', $templateRef);
					$this->includedElements[] = $templateRef;
				}
			}
		}

		return true;
	}

	private function setNode($idNode, $path)
	{
		if (is_null($idNode) && is_null($path)) {
			Logger::error('Cannot parse template: idNode and path are NULL');
			return false;
		}

		if (is_null($idNode)) {
			$this->path = $path;
			return true;
		}

		$this->node = new Node($idNode);
		if (!($this->node->get('IdNode')) > 0) {
			Logger::error('Cannot parse template: Non existant node ' . $idNode);
			return false;
		}

		if ($this->node->nodeType->get('Name') != 'XslTemplate') {
			Logger::error('Cannot parse template: Node ' . $idNode . ' is not a Xsl Template');
			return false;
		}

		$this->idTemplate = $idNode;
		return true;
	}

	private function setXpathObj()
	{
		if ($this->node)
			$content = $this->node->GetContent();
		else
		{
		    $path = $this->path;
		    if (isset($GLOBALS['docker']) and $this->editor)
		    {
		        $path = str_ireplace(URL_ROOT_XSL_TEMPLATES . '/', App::getValue('UrlRoot') . '/', $path);
		        Logger::debug('Replaced XSL path ' . URL_ROOT_XSL_TEMPLATES . '/ to ' . App::getValue( 'UrlRoot') . '/');
		    }
			$content = FsUtils::file_get_contents($path);
		}
		if (isset($GLOBALS['docker']) and $this->editor)
		{
		    $content = str_ireplace(URL_ROOT_XSL_TEMPLATES . '/', App::getValue('UrlRoot') . '/', $content);
		    Logger::debug('Replaced ' . URL_ROOT_XSL_TEMPLATES . '/ to ' . App::getValue( 'UrlRoot') . '/');
		}
		if (!$content)
		{
		    $error = 'setXpathObj error: empty XML content or another problem to get it';
		    if (\Ximdex\Error::error_message())
		        $error .= ' (' . \Ximdex\Error::error_message() . ')';
		    Logger::error($error);
		    return false;
		}
		$domDoc = new DOMDocument();
		$domDoc->preserveWhiteSpace = false;
		$domDoc->validateOnParse = true;
		$domDoc->formatOutput = true;
		$res = @$domDoc->loadXML($content);
		if ($res === false)
		{
		    Logger::error('setXpathObj error: can\'t load XML content (' . \Ximdex\Error::error_message() . ')');
		    return false;
		}

		$this->xpathObj = new DOMXPath($domDoc);

		return true;
	}
}