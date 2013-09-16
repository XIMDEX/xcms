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




 
if(!defined('TREE_VIEW_DOCXAP_PATH'))
	define('TREE_VIEW_DOCXAP_PATH', Config::getValue('UrlRoot') . '/actions/xmleditor2/views/editor/tree/templates/docxap.xsl');

if(!defined('RNG_EDITION_DOCXAP_PATH'))
	define('RNG_EDITION_DOCXAP_PATH', Config::getValue('UrlRoot') . '/actions/xmleditor2/views/rngeditor/templates/docxap.xsl');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');
	
class View_Xedit extends Abstract_View implements Interface_View {
	
	private $content = NULL;
	private $domDocument = NULL;
	private $node = NULL;
	
	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		
		$content = $this->retrieveContent($pointer);
		if($content == '') {
			XMD_log::warning('VIEW XEDIT: empty content');
			return $this->storeTmpContent($content);
		}
		
		if(!$this->setNode($idVersion))
			return NULL;
			
		if(!$this->setView($args))
			return NULL;
		
		if(!$this->setContent($content))
			return NULL;
		
		if(!$this->setUids())
			return NULL;
			
		if(!$this->setXimletIds())
			return NULL;
			
		if(!$this->parametrizeDocxapByNodeType())
			return NULL;

		if(!$this->addXslReference())
			return $this->storeTmpContent($this->content);

		return $this->storeTmpContent($this->content);
	}
	
	private function setUids() {
		$xpath = new DOMXPath($this->domDocument);
		$nodeList = $xpath->query('//*');
		$counter = 1;
		foreach ($nodeList as $element) {
			$element->setAttributeNode(new DOMAttr('uid', $this->node->get('IdNode') . '.' . ($counter ++) ));
		}
		
		return $this->setContent($this->domDocument->saveXml());
	}
	
	private function populateVoidNode () {
		$xpath = new DOMXPath($this->domDocument);

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
			$this->domDocument = new DOMDocument();

		if (!$this->domDocument->loadXML($this->content)) {
			XMD_log::error('VIEW XEDIT: Invalid XML (' . $this->content . ')');
			return false;
		}
		return true;
	}
	
	private function setXimletIds() {

		$idcontainer = $this->node->getParent();
		$reltemplate = new RelTemplateContainer();
		$idTemplate = $reltemplate->getTemplate($idcontainer);
		
		$parser = new ParsingRng($idTemplate);
		$ximletTags = $parser->getElementsByType('ximlet');
		
		$idLanguage = $this->node->class->GetLanguage();
		$linkedXimlets = $this->node->class->getLinkedximletS($idLanguage);

		$xpath = new DOMXPath($this->domDocument);
		$nodeList = $xpath->query('//*');
		foreach ($nodeList as $element) {
			if(in_array($element->nodeName, $ximletTags) && preg_match("/@@@GMximdex\.ximlet\(([0-9]+)\)@@@/", $element->textContent, $matches)) {
					$element->setAttributeNode(new DOMAttr('ximlet_id', $matches[1]));
					if(in_array($matches[1], $linkedXimlets))
						$element->setAttributeNode(new DOMAttr('section_ximlet', 'yes'));
			}
		}
		
		$this->setContent($this->domDocument->saveXml());
		return true;
	}
	
	private function setNode ($idVersion = NULL) {
		
		if(!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				XMD_Log::error('VIEW XEDIT: Se ha cargado una versión incorrecta (' . $idVersion . ')');
				return false;
			}
			
			$this->node = new Node($version->get('IdNode'));
			if (!($this->node->get('IdNode') > 0)) {
				XMD_Log::error('VIEW XEDIT: El nodo que se está intentando convertir no existe: ' . $version->get('IdNode'));
				return false;
			}
		}
		
		return true;
	}
	
	private function setView($args) {
		if (!array_key_exists('XEDIT_VIEW', $args)) {
			XMD_Log::error('VIEW XEDIT: No se ha especificado la vista de XEDIT');
			return false;
		}
		$this->view = $args['XEDIT_VIEW'];
		return true;
	}
	
	private function parametrizeDocxapByNodeType() {
		$nodeTypeName = $this->node->nodeType->GetName();
		if ($nodeTypeName == 'RngVisualTemplate') {
			$content = Config::getValue('EncodingTag') . Config::getValue('DoctypeTag') . '<docxap xmlns:xim="http://www.ximdex.com/">' . $this->content . '</docxap>';
		}
		return isset($content) ? $this->setContent($content) : true;
	}
	
	private function addXslReference () {
		if(!$xslFile = $this->getXslPath())
			return false;

		$xslHeader = '<?xml-stylesheet type="text/xsl" href="' . $xslFile . '"?>';
		$xmlHeader = Config::getValue('EncodingTag');
		$content = str_replace($xmlHeader, $xmlHeader . $xslHeader, $this->content);
		
		return $this->setContent($content);
	}
	
	private function getXslPath() {
		if($this->view == 'tree') {
			return TREE_VIEW_DOCXAP_PATH;
		}

		$nodeTypeName = $this->node->nodeType->GetName();
		if ($nodeTypeName == 'RngVisualTemplate') {
			return RNG_EDITION_DOCXAP_PATH;
		}
		
		$tplFolder = Config::getValue("TemplatesDirName");
		$section = new Node($this->node->GetSection());
		$sectionPath = $section->class->GetNodePath();
		$docxap = $sectionPath . '/' . $tplFolder . '/docxap.xsl';
		if(is_readable($docxap))
			return str_replace(Config::getValue('AppRoot'), Config::getValue('UrlRoot'),  $docxap);

		$project = new Node($this->node->GetProject());
		$nodeProjectPath = $project->class->GetNodePath();
		$docxap = $nodeProjectPath . '/' . $tplFolder . '/docxap.xsl';
		
		if(is_readable($docxap))
			return str_replace(Config::getValue('AppRoot'), Config::getValue('UrlRoot'),  $docxap);
			
		return NULL;
	}
	
}
?>
