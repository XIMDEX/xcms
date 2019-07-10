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

use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\RelTemplateContainer;
use Ximdex\NodeTypes\XsltNode;
use Ximdex\Parsers\ParsingRng;
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\Strings;
use Ximdex\Logger;

abstract class XmlEditor_Abstract
{
    protected $_editorName = '';
    
    protected $_base_url;
    
    protected $rel_path_docxap;

    abstract public function getEditorName();

    abstract public function getBaseURL();

    abstract public function setBaseURL(string $base_url);

    abstract public function setEditorName(string $editorName);

    abstract public function openEditor(int $idnode, string $view = null);

    abstract public function getConfig(int $idnode);

    abstract public function getSpellCheckingFile(int $idnode, string $content);

    abstract public function getAnnotationFile(int $idnode, string $content);

    abstract public function getPreviewInServerFile(int $idNode, string $content, int $idChannel = null);

    abstract public function getNoRenderizableElements(int $idNode);

    public function getXmlFile(int $idnode)
    {
        $node = new Node($idnode);
        if (! $node->get('IdNode')) {
            Logger::error(_('A non-existing node cannot be obtained'));
            return null;
        }

        // ---------------------------------------------------------------------------------------------
        // TODO: Study the logic about how to obtain an edition channel.
        // For the moment, it is selected the channel HTML; if it is not existing, the first found.
        // ---------------------------------------------------------------------------------------------

        // Channel IDs are returned, but XSLT needs the name
        $channels = $node->getChannels();
        $defaultChannel = null;
        foreach ($channels as $channelID) {
            $channel = new Channel($channelID);
            $channelName = $channel->getName();
            if ($defaultChannel == null) $defaultChannel = $channelID;
            if (strToUpper($channelName) == 'HTML' || strToUpper($channelName) == 'WEB') {
                $defaultChannel = $channelID;
                break;
            }
        }
        
        // Document content is returned with the corresponding docxap labels
        $content = $node->class->getRenderizedContent($defaultChannel);
        return $content;
    }

    public function getXslFile(int $idnode, string $view = null, bool $includesInServer = null)
    {
        $content = '';
        $docxap = $this->getXslPath($idnode, false, $view);
        if ($docxap !== null) {
            $pathDocxap = str_replace(App::getValue('UrlHost') . App::getValue('UrlRoot'), XIMDEX_ROOT_PATH, $docxap);
            $pos = strrpos($pathDocxap, '/');
            $pathToFileRel = substr($pathDocxap,0, $pos);
            $this->rel_path_docxap = $pathToFileRel . '/';
            $content = FsUtils::file_get_contents($pathDocxap);
            
            // Include the correspondant includes_template.xsl for the current document
            XsltNode::replace_path_to_local_templatesInclude($content, $idnode);
        } else {
            $msg = "docxap.xsl was not found for node $idnode";
            Logger::error($msg);
        }
        return $content;
    }

    public function getXslPath(int $idnode, bool $asURL = false, string $view = null)
    {
        $node = new Node($idnode);
        if (! $node->get('IdNode')) {
            Logger::error(_('A non-existing node cannot be obtained: ') . $idnode);
            return null;
        }
        if ($view == 'form') {
            return $this->getFormViewXsl($idnode);
        } elseif ($view == 'tree') {
            return App::getValue( 'UrlHost') . App::getUrl('/actions/xmleditor2/views/editor/tree/templates/docxap.xsl' );
        }
        $nodeTypeName = $node->nodeType->GetName();
        if ($nodeTypeName == 'RngVisualTemplate') {
            return App::getValue( 'UrlHost') .  App::getUrl('/actions/xmleditor2/views/rngeditor/templates/docxap.xsl');
        }

        // Return full project docxap path
        $project = new Node($node->getProject());
        if (! $project->GetID()) {
            Logger::error('A non-existing project cannot be obtained from node: ' . $idnode);
            return false;
        }
        $docxap = XIMDEX_ROOT_PATH . App::getValue('NodeRoot') . '/' . $project->GetNodeName() . '/' . App::getValue('TemplatesDirName')
                . '/docxap.xsl';
        if ($docxap && $asURL) {
            $docxap = str_replace(XIMDEX_ROOT_PATH, App::getValue('UrlHost') . App::getValue('UrlRoot'),  $docxap);
        }
        return $docxap;
    }

    public function getSchemaNode(int $idnode)
    {
        $node = new Node($idnode);
        $nodeTypeName = $node->nodeType->GetName();
        if ($nodeTypeName == 'RngVisualTemplate') {
            $rngPath = APP_ROOT_PATH . '/actions/xmleditor2/views/rngeditor/schema/rng-schema.xml';
            return trim(FsUtils::file_get_contents($rngPath));
        }
        $idcontainer = $node->getParent();
        $reltemplate = new RelTemplateContainer();
        $idTemplate = $reltemplate->getTemplate($idcontainer);
        $templateNode = new Node($idTemplate);
        return $templateNode;
    }

    protected function getSchemaData(int $idnode)
    {
        $schemaData = array('id' => null, 'content' => '');
        if (! is_object($templateNode = $this->getSchemaNode($idnode))) {
            return array('id' => 0, 'content' => $templateNode);
        }
        $schemaId = $templateNode->getID();
        $rngTemplate = new Node($schemaId);
        $content = $rngTemplate->GetContent();
        $schemaData['id'] = $schemaId;
        $schemaData['content'] = $content;
        return $schemaData;
    }

    protected function enrichSchema(string $schema)
    {
        return $schema;
    }

    public function getSchemaFile(int $idnode)
    {
        $schemaData = $this->getSchemaData($idnode);
        $content = $schemaData['content'];
        $schema = FsUtils::file_get_contents(APP_ROOT_PATH . '/actions/xmleditor2/views/common/schema/relaxng-1.0.rng.xml');
        $rngvalidator = new \Ximdex\XML\Validators\RNG();
        $rngvalidator->validate($schema, $content);
        $errors = $rngvalidator->getErrors();
        if (count($errors) > 0) {
            $content = array('error' => $errors);
        } else {
            $content = preg_replace('/xmlns:xim="([^"]*)"/', sprintf('xmlns:xim="%s"', ParsingRng::XMLNS_XIM),  $content);
        }
        return $content;
    }

    abstract public function saveXmlFile(int $idnode, string $content, bool $autoSave = false);

    /**
	 * @param int idnode idNode is needed to get the asociated schema
	 * @param string xmldoc Is the XML string to validate
	 */
    public function validateSchema(int $idnode, string $xmldoc)
    {
        $xmldoc = '<?xml version="1.0" encoding="UTF-8"?>' . Strings::stripslashes( $xmldoc);
        $schema = $this->getSchemaFile($idnode);
        $rngvalidator = new \Ximdex\XML\Validators\RNG();
        $valid = $rngvalidator->validate($schema, $xmldoc);
        $response = array('valid' => $valid, 'errors' => $rngvalidator->getErrors());
        return $response;
    }

    public function getAllowedChildrens(int $idnode, string $uid, string $htmldoc)
    {
        $node = new Node($idnode);
        $xmlOrigenContent = $node->class->getRenderizedContent();

        // Loading XML & HTML content into respective DOM Documents
        $docXmlOrigen = new DOMDocument();
        $docXmlOrigen->loadXML($xmlOrigenContent);
        $docHtml = new DOMDocument();
        $docHtml = $docHtml->loadHTML(Strings::stripslashes($htmldoc));

        // Transforming HTML into XML
        $htmlTransformer = new HTML2XML();
        $htmlTransformer->loadHTML($docHtml);
        $htmlTransformer->loadXML($docXmlOrigen);
        $htmlTransformer->setXimNode($idnode);
        // $xmldoc = null;
        if ($htmlTransformer->transform()) {
            // $xmldoc = $htmlTransformer->getXmlContent();
        }
        $node = $htmlTransformer->getNodeWithUID($uid);
        return $node->tagName;
    }
    
    /**
     * Delete docxap tags
	 * Delete UID attributes
	 * 
     * @param int $idNode
     * @param string $xmldoc
     * @param bool $deleteDocxap
     * @return string
     */
    protected function _normalizeXmlDocument(int $idNode, string $xmldoc, bool $deleteDocxap = true)
    {
        $xmldoc = '<?xml version="1.0" encoding="UTF-8"?>' . Strings::stripslashes($xmldoc);
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xmldoc);
        $docxap = $doc->firstChild;
        $this->_deleteUIDAttributes($docxap);
        if ($deleteDocxap) {
            $childrens = $docxap->childNodes;
            $l = $childrens->length;
            $xmldoc = '';
            for ($i = 0; $i < $l; $i++) {
                $child = $childrens->item($i);
                if ($child->nodeType == 1) {
                    $xmldoc .= $doc->saveXML($child);
                }
            }
        } else {
            $xmldoc = $doc->saveXML($docxap);
        }
        return $xmldoc;
    }

    /**
     * Recursive!
	 * Called by _normalizeXmlDocument()
	 * 
     * @param mixed $node
     */
    protected function _deleteUIDAttributes($node)
    {
        if ($node->nodeType != 1) {
            return;
        }
        if ($node->hasAttribute('uid')) {
            $node->removeAttribute('uid');
        }
        $childrens = $node->childNodes;
        $count = $childrens->length;
        for ($i = 0; $i < $count; $i++) {
            $this->_deleteUIDAttributes($childrens->item($i));
        }
    }
    
    /**
     * Replace xsl:include tags by the content of file included
     * 
     * @param string $content
     */
    private function replaceIncludes(string & $content)
    {
        $xsl = new DOMDocument();
        $xsl->loadXML($content);
        
        // Get template-include tag and is source
        
        // Get template include path
        $arrayTags = $xsl->getElementsByTagName('include');
        
        // We supposed just 1 include in docxap file (template_includes)
        if ($arrayTags->item(0) != null) {
            $templateIncludeTag = $arrayTags->item(0);
            
            // This href is a complete url path
            $templateIncludePath = $templateIncludeTag->getAttribute('href');
            $folderPath = $templateIncludePath;
            $xslTemplateInclude = new DOMDocument();
            $xslTemplateInclude->load($this->rel_path_docxap .$folderPath);
            $arrayFinalTags = $xslTemplateInclude->getElementsByTagName('include');
            $auxContent = '';
            
            // For each include in template_includes
            foreach ($arrayFinalTags as $domElement) {
                $auxPath = $domElement->getAttribute('href');
                $auxXsl = new DOMDocument();
                $auxXsl->formatOutput = true;
                $auxXsl->preserveWhiteSpace = false;
                $auxXsl->load($this->rel_path_docxap .$auxPath);
                $temporalContent = $auxXsl->saveXML();
                $temporalContent = preg_replace('/\<\/*xsl:stylesheet.*\>/', '', $temporalContent);
                $temporalContent = preg_replace('/\<\?xml version.*\>/', '', $temporalContent);
                $auxContent .= $temporalContent;
            }
            $content = preg_replace("/\<xsl:include.*href=\'.*templates_include.xsl\".*\>/", $auxContent, $content);
        }
    }

    /**
     * Get the form view xsl for the current node
     * If exist an updated xsl, it will return that
     * Otherwise will generate a new one
     * 
     * @param  int $idnode
     * @return string pointer to the xsl file
     */
    private function getFormViewXsl(int $idnode)
    {
        $node = new Node($idnode);
        $idSchema = $node->class->getTemplate();
        $schemaNode = new Node($idSchema);
        $schemaName = $schemaNode->GetNodeName();
        $dataFactory = new DataFactory($idSchema);
        $maxIdVersion = $dataFactory->getLastVersionId();
        $formXslFile = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . "/xslformview_{$schemaName}_{$maxIdVersion}.xsl";
        if (file_exists($formXslFile)) {
            return $formXslFile;
        }
        array_map('unlink', glob(XIMDEX_ROOT_PATH . App::getValue('FileRoot') . "/xslformview_{$schemaName}*"));
        return $this->buildFormXsl($idSchema, $maxIdVersion);
    }

    /**
     * Generate a xsl file from schema and the template
     * 
     * @param int $idSchema associated to the current node
     * @param int $maxIdVersion Schema idversion
     * @return string pointer to the new generated xsl file
     */
    private function buildFormXsl(int $idSchema, int $maxIdVersion)
    {
        $warnings = '';
        $xslTemplateContent = FsUtils::file_get_contents(APP_ROOT_PATH . '/actions/xmleditor2/views/editor/form/templates/docxap.xsl');
        $rngXpathObj = $this->getXPathFromSchema($idSchema);
        $textElements = $this->getTextElements($rngXpathObj);
        $dateElements = $this->getElementsByType($rngXpathObj, 'date');
        $elements = $rngXpathObj->query('//element');
        $applyElements = $this->getApplyElements($rngXpathObj);
        $boldElements = $this->getElementsByType($rngXpathObj,'bold');
        $italicElements = $this->getElementsByType($rngXpathObj,'italic');
        $linkElements = $this->getElementsByType($rngXpathObj,'link');
        $imageElements = $this->getElementsByType($rngXpathObj,'image');
        $listElements = $this->getElementsByType($rngXpathObj,'list');
        $itemElements = $this->getElementsByType($rngXpathObj,'item');
        $textAreaElements = $this->getElementsByType($rngXpathObj,'textarea');

        /*
         * For every element, if isn't a docxap element, an apply element or an
         * explicit textarea element, infer if is textarea, input or container
         */
        $inputTextElements = [];
        $containerElements = [];
        foreach ($elements as $element) {
            $tagName = $element->getAttribute('name');
            if ($tagName == 'docxap') {
                continue;
            }
            $toLowerTagName = strtolower($tagName);
            
            // Apply elements are italic, bold, underlink or link
            if (in_array($toLowerTagName, $applyElements)) {
                continue;
            }
            if (in_array($toLowerTagName, $textAreaElements)) {
                continue;
            }
            if (in_array($tagName, $textElements)) {
                
                /* 
                 * A textarea element if it can have child elements defined by
                 * reference or inside of current element
                 */
                $resultLength = $rngXpathObj->query('.//element', $element)->length + $rngXpathObj->query('.//ref', $element)->length;
                if ($resultLength) {
                    $textAreaElements[] = $tagName;
                    $textAreaElements[] = $toLowerTagName;
                } else {
                    $inputTextElements[] = $tagName;
                    $inputTextElements[] = $toLowerTagName;
                }
            } else {
                $containerElements[] = $tagName;
                $containerElements[] = $toLowerTagName;
            }
        }
        $allApplyElements = array_values(array_unique(array_diff($applyElements, $boldElements, $italicElements, $linkElements)));
        $containerElements = array_values(array_unique(array_diff($containerElements, $imageElements, $listElements)));
        $textAreaElements = array_values(array_unique(array_diff($textAreaElements, $itemElements)));
        $inputTextElements = array_values(array_unique(array_diff($inputTextElements, $itemElements)));
        $blockEditionElements = array_values(array_unique(array_merge($imageElements,$textAreaElements, $listElements)));

        /**
        * Nesting every array in other one. The keys for this array are
        * element macros.
        */
        $groupedElements = [];
        $groupedElements['##APPLY_ELEMENTS##'] = $allApplyElements;
        $groupedElements['##CONTAINER_ELEMENTS##'] = $containerElements;
        $groupedElements['##INPUT_TEXT_ELEMENTS##'] = $inputTextElements;
        $groupedElements['##TEXTAREA_ELEMENTS##'] = $textAreaElements;
        $groupedElements['##BOLD_ELEMENTS##'] = $boldElements;
        $groupedElements['##ITALIC_ELEMENTS##'] = $italicElements;
        $groupedElements['##LINK_ELEMENTS##'] = $linkElements;
        $groupedElements['##BLOCK_EDITION_ELEMENTS##'] = $blockEditionElements;
        $groupedElements['##IMAGE_ELEMENTS##'] = $imageElements;
        $groupedElements['##LIST_ELEMENTS##'] = $listElements;
        $groupedElements['##ITEM_ELEMENTS##'] = $itemElements;
        $groupedElements['##DATE_ELEMENTS##'] = $dateElements;
        foreach ($groupedElements as $macro => $elements) {
            if (count($elements)){
                $implodedElements = implode(' | ', $elements);
                $xslTemplateContent = str_replace($macro, $implodedElements, $xslTemplateContent);
            } else { 
                $matches = array();
                preg_match('/##(\w+)_ELEMENTS##/', $macro, $matches);
                if (count($matches)>1){
                    $elementName = $matches[1];
                    $xslTemplateContent = str_replace($macro, "{$elementName}_undefined", $xslTemplateContent);
                    if ($elementName != 'BLOCK_EDITION'){                        
                        $warnings .= "<p>Doesn't found any $elementName element</p>";
                        $warningElements .= "$elementName ";
                    }
                }            
            }
        }
        $xslTemplateContent = str_replace('##WARNING_ELEMENTS##', $warningElements, $xslTemplateContent);
        $xslTemplateContent = str_replace('##WARNINGS##', $warnings, $xslTemplateContent);
        $xslTemplateContent = str_replace('@@URL_PATH@@', App::getValue('UrlHost') . App::getValue('UrlRoot'), $xslTemplateContent);
        $schemaNode = new Node ($idSchema);
        $schemaName = $schemaNode->GetNodeName();
        $formViewFile = XIMDEX_ROOT_PATH.App::getValue('FileRoot')."/xslformview_{$schemaName}_{$maxIdVersion}.xsl";
        FsUtils::file_put_contents($formViewFile, $xslTemplateContent);
        return $formViewFile;
    }

    /**
     * Clean namespaces and get XPath object for a Relax-NG schema
     * 
     * @param  int $idSchema
     * @return \DOMXPath Path to root element in Relax-NG
     */
    private function getXPathFromSchema(int $idSchema)
    {
        $schemaNode = new Node($idSchema);
        $schemaContent = $schemaNode->GetContent();
        $docRNG = new DOMDocument();
        $docRNG->validateOnParse = true;
        
        // Removing namespaces declaration.
        $schemaContent = preg_replace('/<grammar[^>]*>/', '<grammar>', $schemaContent,1);
        $docRNG->loadXML($schemaContent,LIBXML_NOERROR);
        $xpathObj = new DOMXPath($docRNG);
        $xpathObj->registerNameSpace('xim', 'http://www.ximdex.com');
        return $xpathObj;
    }

    /**
     * Get elements typed like apply and $elementType
     * 
     * @param DomXPath $xpathObj pointer to the current element in Relax-NG
     * @param  string $elementType searched type
     * @return array Names for found elements
     */
    private function getSpecialApplyElements(DOMXPath $xpathObj, string $elementType)
    {
        $result = array();
        $applytags = $xpathObj->query("//type[contains(text(),'$elementType')]");
        foreach ($applytags as $applyTag) {
            if (strpos($applyTag->nodeValue, 'apply')!==FALSE) {
                $elementTag = $applyTag->parentNode;
                $elementName = $elementTag->getAttribute('name');
                $result[] = strtolower($elementName);
            }
        }
        return $result;
    }

    /**
     * Get applies elements
     * 
     * @param DomXPath $xpathObj pointer to the current element in Relax-NG
     * @return array Names for found elements
     */
    private function getApplyElements(DOMXPath & $xpathObj)
    {
        $result = array();
        $applytags = $xpathObj->query("//*[name()='xim:type']");
        foreach ($applytags as $applyTag) {
            if (strpos($applyTag->nodeValue, 'apply')!==FALSE) {
                $elementTag = $applyTag->parentNode;
                $elementName = $elementTag->getAttribute('name');
                $result[] = strtolower($elementName);
            }
        }
        return $result;
    }

    /**
     * Get an array for elements with type $elementType
     * 
     * @param DomXPath $xpathObj
     * @param string $elementType Searching type
     * @return array with element names
     */
    private function getElementsByType(DOMXPath & $xpathObj, string $elementType)
    {
        $result = array();
        $applytags = $xpathObj->query("//*[name()='xim:type']");
        foreach ($applytags as $applyTag) {
            error_log($applyTag->nodeValue.", $elementType");
            if (strpos($applyTag->nodeValue, $elementType)!==FALSE) {
                $elementTag = $applyTag->parentNode;
                $elementName = $elementTag->getAttribute('name');
                $result[] = strtolower($elementName);
            }
        }
        return $result;
    }

    /**
     * Get elements with a text tag inside
     * 
     * @param DomXPath $xpathObj Relax-NG XPath
     * @return array Name of all elements with a text tag.
     */
    private function getTextElements(DOMXPath & $xpathObj)
    {
        $result = array();
        $elementsTag = $xpathObj->query('//text/ancestor::element');
        foreach ($elementsTag as $elementTag) {
            $elementName = $elementTag->getAttribute('name');
            $result[] = $elementName;
        }
        return $result;
    }
}
