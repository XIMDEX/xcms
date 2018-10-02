<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\Models\Node;
use Ximdex\Models\Version;
use Ximdex\Parsers\ParsingRng;
use Ximdex\Runtime\App;

if (!defined('TREE_VIEW_DOCXAP_PATH')) {
    define('TREE_VIEW_DOCXAP_PATH', App::getValue('UrlHost') 
        . App::getUrl('/actions/xmleditor2/views/editor/tree/templates/docxap.xsl'));
}
if (!defined('RNG_EDITION_DOCXAP_PATH')) {
    define('RNG_EDITION_DOCXAP_PATH', App::getValue('UrlHost') 
        . App::getUrl('/actions/xmleditor2/views/rngeditor/templates/docxap.xsl'));
}

class ViewXedit extends AbstractView implements IView
{	
	private $content = null;
	private $domDocument = null;
	private $node = null;
	
	public function transform($idVersion = null, $pointer = null, $args = null)
	{	
		$content = $this->retrieveContent($pointer);
		if ($content == '') {
			Logger::warning('VIEW XEDIT: empty content');
			return $this->storeTmpContent($content);
		}	
		if (!$this->setNode($idVersion)) {
			return null;
		}
		if (!$this->setView($args)) {
			return null;
		}
		if (!$this->setContent($content)) {
			return null;
		}
		if (!$this->setUids()) {
			return null;
		}
		if (!$this->setXimletIds()) {
			return null;
		}
		if (!$this->parametrizeDocxapByNodeType()) {
			return null;
		}
		if (!$this->addXslReference()) {
			return $this->storeTmpContent($this->content);
		}
		return $this->storeTmpContent($this->content);
	}
	
	private function setUids()
	{
		$xpath = new \DOMXPath($this->domDocument);
		$nodeList = $xpath->query('//*');
		$counter = 1;
		foreach ($nodeList as $element) {
			$element->setAttributeNode(new \DOMAttr('uid', $this->node->get('IdNode') . '.' . ($counter ++) ));
		}	
		return $this->setContent($this->domDocument->saveXml());
	}
	
	private function populateVoidNode()
	{
		$xpath = new \DOMXPath($this->domDocument);

		// Check if there is no content
		$query = '//docxap/*';
		$nodelist = $xpath->query($query);
		if ($nodelist->length == 0) {
			$schemaData = $this->getSchemaData($this->node->get('IdNode'));
			$parser = new ParsingRng();
			$content = $parser->buildDefaultContent($schemaData['id']);
		}
		
		return $this->setContent($content);
	}
	
	private function setContent($content) {
		$this->content = $content;
		return $this->setDomDocument();
	}
	
	private function setDomDocument() {
		if(!$this->domDocument)
		{
			$this->domDocument = new \DOMDocument();
			$this->domDocument->formatOutput = true;
			$this->domDocument->preserveWhiteSpace = false;
		}

		if (!$this->domDocument->loadXML($this->content)) {
			Logger::error('VIEW XEDIT: Invalid XML (' . $this->content . ')');
			return false;
		}
		return true;
	}
	
	private function setXimletIds()
	{
		$idcontainer = $this->node->getParent();
		$reltemplate = new \Ximdex\Models\RelTemplateContainer();
		$idTemplate = $reltemplate->getTemplate($idcontainer);	
		$parser = new ParsingRng($idTemplate);
		$ximletTags = $parser->getElementsByType('ximlet');
		$idLanguage = $this->node->class->GetLanguage();
		$linkedXimlets = $this->node->class->getLinkedximletS($idLanguage);
		$xpath = new \DOMXPath($this->domDocument);
		$nodeList = $xpath->query('//*');
		$matches = [];
		foreach ($nodeList as $element) {
			if(in_array($element->nodeName, $ximletTags) && preg_match("/@@@GMximdex\.ximlet\(([0-9]+)\)@@@/"
			    , $element->textContent, $matches)) {
			        $element->setAttributeNode(new \DOMAttr('ximlet_id', $matches[1]));
			        if (in_array($matches[1], $linkedXimlets)) {
			            $element->setAttributeNode(new \DOMAttr('section_ximlet', 'yes'));
			        }
			}
		}
		$this->setContent($this->domDocument->saveXml());
		return true;
	}
	
	private function setNode ($idVersion = null)
	{
		if (!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				Logger::error('VIEW XEDIT: An incorrect version has been loaded (' . $idVersion . ')');
				return false;
			}
			$this->node = new Node($version->get('IdNode'));
			if (!($this->node->get('IdNode') > 0)) {
				Logger::error('VIEW XEDIT: The node it\'s trying to convert doesn\'t exists: ' . $version->get('IdNode'));
				return false;
			}
		}
		return true;
	}
	
	private function setView($args)
	{
		if (!array_key_exists('XEDIT_VIEW', $args)) {
			Logger::error('VIEW XEDIT: No se ha especificado la vista de XEDIT');
			return false;
		}
		$this->view = $args['XEDIT_VIEW'];
		return true;
	}
	
	private function parametrizeDocxapByNodeType()
	{
		$nodeTypeName = $this->node->nodeType->GetName();
		if ($nodeTypeName == 'RngVisualTemplate') {
		    $content = App::getValue( 'EncodingTag') . App::getValue( 'DoctypeTag') 
                . '<docxap xmlns:xim="http://www.ximdex.com/">' . $this->content . '</docxap>';
		}
		return isset($content) ? $this->setContent($content) : true;
	}
	
	private function addXslReference()
	{
	    if (!$xslFile = $this->getXslPath()) {
			return false;
	    }
		$xslHeader = '<?xml-stylesheet type="text/xsl" href="' . $xslFile . '"?>';
		$xmlHeader = App::getValue( 'EncodingTag');
		$content = str_replace($xmlHeader, $xmlHeader . $xslHeader, $this->content);
		return $this->setContent($content);
	}
	
	private function getXslPath()
	{
		if ($this->view == 'tree') {
			return TREE_VIEW_DOCXAP_PATH;
		}
		$nodeTypeName = $this->node->nodeType->GetName();
		if ($nodeTypeName == 'RngVisualTemplate') {
			return RNG_EDITION_DOCXAP_PATH;
		}
		$tplFolder = App::getValue( "TemplatesDirName");
		$section = new Node($this->node->GetSection());
		$sectionPath = $section->class->GetNodePath();
		$docxap = $sectionPath . '/' . $tplFolder . '/docxap.xsl';
		if (is_readable($docxap)) {
		    return str_replace(XIMDEX_ROOT_PATH, App::getValue('UrlHost') . App::getValue('UrlRoot'),  $docxap);
		}
		$project = new Node($this->node->GetProject());
		$nodeProjectPath = $project->class->GetNodePath();
		$docxap = $nodeProjectPath . '/' . $tplFolder . '/docxap.xsl';
		if (is_readable($docxap)) {
		    return str_replace(XIMDEX_ROOT_PATH, App::getValue('UrlHost') . App::getValue('UrlRoot'),  $docxap);
		}
		return null;
	}
}
