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

use Ximdex\Deps\DepsManager;
use Ximdex\Models\Node;
use Ximdex\NodeTypes\FileNode;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;


if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}


class xsltnode extends FileNode
{

    private $xsltOldName = ""; //String;
    public $messages;

    public function __construct(&$node)
    {

        if (is_object($node))
            $this->parent = $node;
        else if (is_numeric($node) || $node == null)
            $this->parent = new Node($node, false);
        $this->nodeID = $this->parent->get('IdNode');
        $this->dbObj = new DB();
        $this->nodeType = &$this->parent->nodeType;
        $this->messages = new \Ximdex\Utils\Messages();
        $this->xsltOldName = $this->parent->get("Name");
    }


    public function CreateNode($xsltName = null, $parentID = null, $nodeTypeID = null, $stateID = null, $ptdSourcePath = NULL)
    {
        $xslSourcePath = NULL;
        if ($ptdSourcePath != null) {

            // Saving xslt content

            $xslContent = FsUtils::file_get_contents($ptdSourcePath);
            $xslContent = $this->sanitizeContent($xslContent);

            $xslSourcePath = App::getValue('AppRoot') . App::getValue('TempRoot') . '/' . $parentID . $xsltName;

            if (!FsUtils::file_put_contents($xslSourcePath, $xslContent)) {
                Logger::error("Error saving xslt file");
                $this->messages->add('Error saving xslt file: ' . $parentID . $xsltName, MSG_TYPE_ERROR);
                return false;
            }
        }
        if (parent::CreateNode($xsltName, $parentID, $nodeTypeID, $stateID, $xslSourcePath) === false)
            return false;
        
        // Checks if exists template_include.xsl node
        if ($xsltName != 'docxap.xsl') {
            if ($this->setIncludeContent($xsltName, $parentID, $nodeTypeID, $stateID) === false)
                return false;
        }
        
        return true;
    }


    /**
     * Make a xsl:include line and call to inserts on inclusion files
     * @param string $fileName
     * @param integer $parentId
     * @param integer $nodeTypeId
     * @param integer $stateID
     * @return boolean|true
     */
    private function setIncludeContent($fileName, $parentId, $nodeTypeId, $stateID)
    {
        if ($fileName == 'templates_include.xsl' or $fileName == 'docxap.xsl')
            return true;
        
        $node = new Node($this->nodeID);
        $projectId = $node->GetProject();

        $ximptd = new Node($parentId);
        $idProject = $node->GetProject();

        $ptdFolder = App::getValue("TemplatesDirName");

        if ($ximptd->get('IdParent') == $projectId) {

            // Making include in project (modify includes from project and its sections)
            $this->writeIncludeFile($fileName, $projectId, $nodeTypeId, $stateID);
            
        } else {

            // Making include only in section ximptd
            $sectionId = $node->GetSection();
            $section = new Node($sectionId);
            return $this->writeIncludeFile($fileName, $sectionId, $nodeTypeId, $stateID);
        }
        return true;
    }

    /**
     * Insert xsl:include line in inclusion
     * @param string $templateName
     * @param integer $sectionId
     * @param integer $nodeTypeID
     * @param integer $stateID
     * @return boolean
     */
    private function writeIncludeFile($templateName, $sectionId, $nodeTypeID, $stateID)
    {
        $section = new Node($sectionId);
        $ximPtdId = $section->GetChildByName('templates');
        if ($ximPtdId === false)
        {
            $this->messages->add('Can\'t get the Templates folder to include the template ' . $templateName, MSG_TYPE_ERROR);
            return false;
        }

        $parent = new Node($ximPtdId);
        $includeId = $parent->GetChildByName('templates_include.xsl');

        if (!($includeId > 0)) {

            $xslSourcePath = App::getValue('AppRoot') . App::getValue('TempRoot') . '/templates_include.xsl';

            // Creating include file

            Logger::info("Creating unexisting include xslt file at folder $ximPtdId");

            $includeContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $includeContent .= '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">' . "\n";
            $includeContent .= "\t<xsl:include href=\"$templateName\"/>\n";
			$includeContent .= '</xsl:stylesheet>';
            /*
            $arrayContent = explode("\n", $includeContent);
            $includeContent = implode("\n", array_unique($arrayContent));
            */
            $dummyXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<dext:root xmlns:dext=\"http://www.ximdex.com\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">
				<xsl:dummy />
				</dext:root>";

            if (!FsUtils::file_put_contents($xslSourcePath, $dummyXml)) {
                Logger::error("Error saving templates_include.xsl file");
                $this->messages->add('Error saving templates_include.xsl file', MSG_TYPE_ERROR);
                return false;
            }

            $incNode = new Node();
            $id = $incNode->CreateNode('templates_include.xsl', $ximPtdId, $nodeTypeID, $stateID, $xslSourcePath);

            if ($id > 0) {
                $incNode = new Node($id);
                
                // include the templates for the existing parents
                if ($this->include_templates($includeContent, $incNode) === false)
                    return false;
                
                $incNode->SetContent($includeContent);
            }

        } else {

            $includeNode = new Node($includeId);
            $includeContent = $includeNode->getContent();
            $dom = new DOMDocument();
            $dom->formatOutput = true;
            $dom->preserveWhiteSpace = false;
            $dom->validateOnParse = true;
            if (!@$dom->loadXML($includeContent))
            {
                $this->messages->add('File templates_include.xsl for section ' . $sectionId . ' has errors', MSG_TYPE_ERROR);
                return false;
            }
            
            //check if there is a template with that name
            $xPath = new DOMXPath($dom);
            $includeTag = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateName']");
            if ($includeTag->length)
            {
                //template already exists
                return true;
            }
            
            //get the root of the document where to include the template
            $rootTag = $xPath->query('/xsl:stylesheet');
            
            //search and remove another include of a parent section
            $includes = $dom->getElementsByTagName('include');
            foreach ($includes as $include)
            {
                $template = FsUtils::get_url_file($include->getAttribute('href'));
                if ($template == $templateName)
                    $rootTag->item(0)->removeChild($include);
            }
            
            //generate the include tag with its href value
            $domElement = $dom->createElement('xsl:include');
            $domAttribute = $dom->createAttribute('href');
            $domAttribute->value = $templateName;
            
            //add the element
            $domElement->appendChild($domAttribute);
            $rootTag->item(0)->appendChild($domElement);
            
            //save XSL content in the node
            $includeContent = $dom->saveXML();
            if ($includeContent === false)
            {
                $error = \Ximdex\Error::error_message();
                $this->messages->add($error, MSG_TYPE_ERROR);
                return false;
            }
            
            // include the templates for the existing parents
            if ($this->include_templates($includeContent, $includeNode) === false)
                return false;
            
            $includeNode->setContent($includeContent);
        }
        
        //it is not necesary to create a docxap file in a project based in a theme
        if (!isset($GLOBALS['fromTheme']) or !$GLOBALS['fromTheme'])
        {
            // if there is not a docxap.xsl file in the project/templates folder, create a new one
            $res = $this->create_project_docxap_file();
            if ($res === false)
            {
                Logger::fatal('The project docxap XSL template could not been created');
                return false;
            }
        }
        
        // include the template for the existing children
        $template = New Node($parent->GetChildByName($templateName));
        if (!$template->GetID())
        {
            $this->messages->add('Cannot load the template node with name: ' . $templateName, MSG_TYPE_ERROR);
            return false;
        }
        if ($this->include_template($template) === false)
            return false;
        
        return true;
    }

    /**
     * Rename a node
     * @param string $newName
     * @return boolean
     */
    public function RenameNode($newName = NULL)
    {
        if (null == $newName) return false;

        $nodeTypeId = $this->parent->get('IdNodeType');
        $projectId = $this->parent->GetProject();
        $parentId = $this->parent->get('IdParent');
        $sectionId = $this->parent->getSection();
        $oldName = explode(".", $this->xsltOldName);
        $newName = explode(".", $newName);
        if (count($newName) != 2)
        {
            $this->messages->add('The file extension is necessary', MSG_TYPE_ERROR);
            return false;
        }
        if ($this->xsltOldName) {
            $templateName = $this->xsltOldName;
            if ($this->removeIncludeFile($templateName, $sectionId, $nodeTypeId) === false)
                return false;
            if ($this->removeIncludeFile($templateName, $projectId, $nodeTypeId) === false)
                return false;
        }
        //open the file and make the replacement inside
        $tpl = new Node($this->nodeID);
        $rpl1 = 'name="' . $oldName[0];
        $rpl2 = 'name="' . $newName[0];
        $new_content = str_replace($rpl1, $rpl2, $tpl->GetContent());
        $rpl1 = 'match="' . $oldName[0];
        $rpl2 = 'match="' . $newName[0];
        $new_content = str_replace($rpl1, $rpl2, $new_content);
        $tpl->SetContent($new_content);
        
        if ($this->setIncludeContent($newName[0] . "." . $newName[1], $parentId, $nodeTypeId, null) === false)
            return false;
        
        return true;
    }

    /**
     * Delete a node
     */
    public function deleteNode()
    {
        // Deletes dependencies in rel tables
        $nodeId = $this->nodeID;
        $node = new Node($nodeId);
        $nodeTypeId = $this->parent->get('IdNodeType');
        $templateName = $this->parent->get('Name');
        
        //remove the template from includes_template node
        $templatesId = $this->parent->getParent();
        if ($templatesId)
        {
            $templates = new Node($templatesId);
            if ($templates and $templates->GetNodeType() == \Ximdex\Services\NodeType::TEMPLATES_ROOT_FOLDER)
            {
                $sectionId = $templates->GetParent();
                if ($this->removeIncludeFile($templateName, $sectionId, $nodeTypeId) === false)
                    return false;
            }
        }
        
        //TODO ajlucena: RelStrdocTemplate
        $depsMngr = new DepsManager();
        $depsMngr->deleteByTarget(DepsManager::STRDOC_TEMPLATE, $this->parent->get('IdNode'));

        Logger::info('Xslt dependencies deleted');
        return true;
    }

    /**
     * Remove from templates_include $templateName occurrences
     * Returns true if the reference have been removed, false in error and null if the reference doesn't exists
     * @param string $templateName
     * @param integer $sectionId
     * @param integer $nodeTypeId
     * @return boolean|null
     */
    private function removeIncludeFile($templateName, $sectionId, $nodeTypeId = null)
    {
        if ($templateName == 'templates_include.xsl' or $templateName == 'docxap.xsl')
            return true;
        
        $section = new Node($sectionId);
        $ximPtdId = $section->GetChildByName('templates');

        $parent = new Node($ximPtdId);
        $includeId = $parent->GetChildByName('templates_include.xsl');

        if ($includeId > 0) {

            $includeNode = new Node($includeId);
            $includeContent = $includeNode->getContent();
            $dom = new DOMDocument();
            $dom->formatOutput = true;
            $dom->preserveWhiteSpace = false;
            $dom->validateOnParse = true;
            if (!@$dom->loadXML($includeContent))
            {
                $this->messages->add('File templates_include.xsl for section ' . $sectionId . ' has errors', MSG_TYPE_ERROR);
                return false;
            }
            
            //check if there is a template with that name
            $xPath = new DOMXPath($dom);
            $includeTag = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateName']");
            if (!$includeTag->length)
            {
                //template does not exists
                //$this->messages->add('The template named ' . $templateName . ' does not exists in templates_include.xsl', MSG_TYPE_ERROR);
                return null;
            }
            
            //remove the specified include element
            $includes = $dom->getElementsByTagName('stylesheet');
            $includes->item(0)->removeChild($includeTag->item(0));
            
            //save XSL content into a string content
            $includeContent = $dom->saveXML();
            if ($includeContent === false)
            {
                $error = \Ximdex\Error::error_message();
                $this->messages->add($error, MSG_TYPE_ERROR);
                return false;
            }
            
            //save XSL content in the node
            $includeNode->setContent($includeContent);
            
            //remove template in cascade in others inclusions
            $template = New Node($this->nodeID);
            $template->SetNodeName($templateName);
            if (!$template->GetID())
            {
                $this->messages->add('Cannot load the template node with name: ' . $templateName, MSG_TYPE_ERROR);
                return false;
            }
            if (!$this->remove_template($template))
                return false;
            
            return true;
        }
        return null;
    }

    public function SetContent($content, $commitNode = NULL, Node $node = null)
    {
        //checking the valid XML of the given content
        $domDoc = new DOMDocument();
        $domDoc->formatOutput = true;
        $domDoc->preserveWhiteSpace = false;
        if (@$domDoc->loadXML($content) === false)
        {
            //we don't allow to save an invalid XML
            //$this->messages->add('The XML document is not valid. Changes have not been saved', MSG_TYPE_ERROR);
            if (isset($GLOBALS['InBatchProcess']))
            {
                if ($node and $node->getDescription())
                    Logger::error('Invalid XML for node: ' . $node->getDescription());
                else
                    Logger::error('Invalid XML to set content operation');
            }
            $error = \Ximdex\Error::error_message('DOMDocument::loadXML(): ');
            if ($error)
                $this->messages->add($error, MSG_TYPE_WARNING);
            //return false;
        }
        
        //validating of the correct XSL document in the correct system path (only if node is given)
        if ($node)
        {
            $xsltprocessor = new XSLTProcessor();
            $dom = new DOMDocument();
            if (@$dom->loadXML($content) === false)
            {
                $error = \Ximdex\Error::error_message('DOMDocument::loadXML(): ');
                if (isset($GLOBALS['InBatchProcess']))
                {
                    if ($node and $node->getDescription())
                        Logger::error('Invalid XML for node: ' . $node->getDescription() . ' (' . $error . ')');
                    else
                        Logger::error('Invalid XML (' . $error . ')');
                }
                $this->messages->add('Invalid XML (' . $error . ')', MSG_TYPE_WARNING);
                //return false;
            }
            $project = new Node($node->GetProject());
            $dom->documentURI = App::getValue('AppRoot') . App::getValue('NodeRoot') . $node->GetRelativePath($project->GetID());
            if (@$xsltprocessor->importStyleSheet($dom) === false)
            {
                //$this->messages->add('The XSL document (or its inclusions) has errors. Changes have not been saved', MSG_TYPE_ERROR);
                if (isset($GLOBALS['InBatchProcess']))
                {
                    if ($node and $node->getDescription())
                        Logger::error('Invalid XSL for node: ' . $node->getDescription());
                    else
                        Logger::error('Invalid XSL to set content operation');
                }
                $error = \Ximdex\Error::error_message('XSLTProcessor::importStylesheet(): ');
                if ($error)
                    $this->messages->add($error, MSG_TYPE_WARNING);
                //return false;
            }
        }
        $content = $domDoc->saveXML();
        $content = $this->sanitizeContent($content);
        if ($content === false)
            return false;
        
        if (parent::SetContent($content, $commitNode, $node) === false)
            return false;
        
        if (isset($node))
        {
            if ($node->GetNodeName() != 'docxap.xsl')
            {
                //if the templates folder is the project one, and there is not a docxap file, send a alert to the user
                $templates = new Node($node->getParent());
                $section = new Node($templates->getParent());
                if ($section->GetNodeType() == \Ximdex\Services\NodeType::PROJECT)
                {
                    $docxapId = $templates->GetChildByName('docxap.xsl');
                    if (!$docxapId)
                        $this->messages->add('A docxap.xsl template file must be in the project templates folder', MSG_TYPE_WARNING);
                }
            }
            if ($node->GetNodeName() == 'templates_include.xsl')
            {
                //check if the node is templates_include and is included in docxap file
                if (!self::isIncludedInDocxapFile($node))
                    $this->messages->add('Note that this file isn\'t included in the docxap.xsl file', MSG_TYPE_WARNING);
                
                // check for a templates witch doesnot include in this docxap
                $includes = array();
                foreach ($domDoc->getElementsByTagName('include') as $include)
                {
                    $includes[$include->getAttribute('href')] = true;
                }
                $templates = new Node($node->getParent());
                foreach ($templates->GetChildren() as $child)
                {
                    $child = new Node($child);
                    if ($child->GetNodeName() == 'docxap.xsl' or $child->GetNodeName() == 'templates_include.xsl')
                        continue;
                    if (!isset($includes[$child->GetNodeName()]))
                    {
                        $this->messages->add('Note that there are one or more XSL templates that are not included here', MSG_TYPE_WARNING);
                        break;
                    }
                }
            }
            if ($node->GetNodeName() == 'docxap.xsl')
            {
                //check if there is a templates_include in the same folder with docxap file, and ther reference to this has been not set
                $templates = new Node($node->getParent());
                $templatesIncludeId = $templates->GetChildByName('templates_include.xsl');
                if ($templatesIncludeId)
                {
                    $templatesIncludenode = new Node($templatesIncludeId);
                    if (!self::isIncludedInDocxapFile($templatesIncludenode))
                        $this->messages->add('Note that there is a template_include.xls file, which it is not referenced here', MSG_TYPE_WARNING);
                }
            }
            elseif (!self::isIncludedInTemplates($node->GetNodeName(), $node))
            {
                //check if the saved template is already included in templates_include sending an advise to the user
                $this->messages->add('Note that this template isn\'t included in the templates_includes.xsl file', MSG_TYPE_WARNING);
            }
        }
    }

    private function sanitizeContent($content)
    {
        if (empty($content)) {
            Logger::info('It have been created or edited a document with empty content');
            return $content;
        }
        
        $xsldom = new DOMDocument();
        $xsldom->formatOutput = true;
        $xsldom->preserveWhiteSpace = false;
        $xsldom->loadXML($content);
        $xpath = new DOMXPath($xsldom);

        $nodelist = $xpath->query('//xsl:text');
        $count = $nodelist->length;
        for ($i = 0; $i < $count; $i++) {
            $textnode = $nodelist->item($i);
            // Split CDATA sections if contains attributes references
            $nodes = $this->splitCData($textnode, $xsldom);
            // If splitCData returns only one node there is nothing to change, it's the same node
            if (count($nodes) > 1) {
                $parent = $textnode->parentNode;
                foreach ($nodes as $node) {
                    $parent->insertBefore($node, $textnode);
                }
                $parent->removeChild($textnode);
            }
        }

        $content = $xsldom->saveXML();
        return $content;
    }

    private function splitCData($node, &$xsldom)
    {

        $nodevalue = $node->nodeValue;

        // Split CDATA sections if contains attributes references
        $ret = preg_match_all('/"{@([^}]+)}"/', $nodevalue, $matches);

        if (!$ret) {
            return array($node);
        } else {

            $matches = array_unique($matches[1]);
            $attribute = $matches[0];

            $attrvalue = "@$attribute";
            $sep = '{' . $attrvalue . '}';
            $tokens = explode($sep, $nodevalue);
            $arrCD = array();

            $count = count($tokens);
            for ($i = 0; $i < $count; $i++) {

                $token = $tokens[$i];
                $textnode = $xsldom->createElement('xsl:text');
                $textnode->setAttribute('disable-output-escaping', 'yes');
                $textnode->appendChild($xsldom->createCDATASection($token));

                $arrCD = array_merge($arrCD, (array)$this->splitCData($textnode, $xsldom));

                if ($i < ($count - 1)) {
                    $valueof = $xsldom->createElement('xsl:value-of');
                    $valueof->setAttribute('select', $attrvalue);
                    $arrCD[] = $valueof;
                }
            }

            return $arrCD;
        }
    }

    /**
     *    Get the documents that must be publish when the template is published
     * @param array $params
     * @return array
     */
    public function getPublishabledDeps($params)
    {
        //TODO ajlucena: RelStrdocTemplate
        $depsMngr = new DepsManager();
        return $depsMngr->getByTarget(DepsManager::STRDOC_TEMPLATE, $this->parent->get('IdNode'));
    }
    
    /**
     * Create a new basic docxap XSLT file for the project if it's not exists
     * @param null|int $sectionId
     * @return boolean|NULL|int
     */
    private function create_project_docxap_file()
    {
        //obtain the project node
        $node = new Node($this->nodeID);
        $project = new Node($node->GetProject());
        
        //obtain the project templates node
        $idXimptdProject = $project->GetChildByName('templates');
        $ptdProject = new Node($idXimptdProject);
        
        //obtain the ID for an existant docaxp file yet
        $idDocxapProject = $ptdProject->GetChildByName('docxap.xsl');
        if ($idDocxapProject)
            return null;
        
        //generation of the file docxap.xsl with project name inside
        $xslSourcePath = App::getValue('AppRoot') . App::getValue('TempRoot') . '/docxap.xsl';
        Logger::info('Creating unexisting docxap XSLT file in ' . $xslSourcePath);
        $docxapTemplate = App::getValue('AppRoot') . '/xmd/xslt/docxap.xsl.template';
        $content = FsUtils::file_get_contents($docxapTemplate);
        if (!$content)
            return false;
        
        //make sure that the path to templates is in the correct place of the project docxap templates folder
        /*
        $content = str_replace('##URL_ROOT##', App::getValue('UrlRoot'), $content);
        $content = str_replace('##PROJECT_NAME##', $project->GetNodeName(), $content);
        */
		if (!FsUtils::file_put_contents($xslSourcePath, $content))
		    return false;
		
		//obtain the ID for XSL templates node type
		$nodeTypeID = Ximdex\Services\NodeType::XSL_TEMPLATE;
		
		//create the node for the generated file
		$node = new Node();
		$idDocxapProject = $node->CreateNode('docxap.xsl', $idXimptdProject, $nodeTypeID, null, $xslSourcePath);	
		if (!$idDocxapProject)
		{
		    Logger::error('Error creating the node for project docxap template');
		    return false;
		}
		
		Logger::info('Project docxap.xsl node generated');
		
		//return the ID for the new project docxap template node
		return $idDocxapProject;
    }
    
    //TODO ajlucena: remove previous includes? remove InBatchProcess questions?
    /**
     * Includes all the templates of the parent nodes
     * For each one, it change the relative location of the templates references to the URL of the project corresponding node
     * The purpose is remove duplicated templates and maintain the one referenced by the node given
     * If the originNode parameter is given, the templates related to that node will be prioritary
     * @param string $content
     * @param Node $node
     * @param Node $originNode
     * @return boolean
     */
    public function include_templates(& $content, Node $node, Node $originNode = null)
    {
        // only the templates_include.xsl node will be processed
        if ($node->GetNodeName() != 'templates_include.xsl')
            return true;
        
        // load de XML document from the XSL content given
        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($content);
        
        // load the root node
        $xPath = new DomXPath($dom);
        $root = $xPath->query('//xsl:stylesheet');
        if (!$root->length)
        {
            $error = 'Can\'t find the xsl:stylesheet element in the templates_include XML content';
            if (isset($GLOBALS['InBatchProcess']))
                Logger::error($error . ' for node: ' . $node->getDescription());
            else
                $this->messages->add($error, MSG_TYPE_WARNING);
            return false;
        }
        
        // if the origin node has not been passed and the parent attribute has one loaded, take it instead
        if (!$originNode)
        {
            if ($this->parent and $this->parent->GetID())
                $originNode = $this->parent;
            else
                $originNode = $node;
        }
        
        // load the project node
        $project = new Node($node->GetProject());
        
        // load the parent nodes if exist
        $parent = new Node($originNode->GetParent());
        if (!$parent->GetID() or $parent->GetNodeType() != Ximdex\Services\NodeType::TEMPLATES_ROOT_FOLDER)
        {
            $this->messages->add('The node is not inside a templates folder', MSG_TYPE_ERROR);
            return false;
        }
        
        // the templates to include will be storage in an array (these are prioritary)
        $templates = array();
        
        // for each templates folder, include the templates references in the templates_include.xsl file
        while ($parent = new Node($parent->GetParent()))
        {
            // if the parent node is the projects section, stop the search
            if ($parent->GetNodeType() == Ximdex\Services\NodeType::PROJECTS)
                break;
            
            // if the parent node is not a section, a server or a project, continues to the next parent node
            if ($parent->GetNodeType() != Ximdex\Services\NodeType::SECTION 
                    and $parent->GetNodeType() != Ximdex\Services\NodeType::SERVER and $parent->GetNodeType() != Ximdex\Services\NodeType::PROJECT)
                continue;
            
            // check if the templates folder of this node contains a folder templates with a templates_include.xsl node
            $idTemplatesNode = $parent->GetChildByName('templates');
            if (!$idTemplatesNode)
                continue;
            $templatesNode = new Node($idTemplatesNode);
            $idTemplatesInclude = $templatesNode->GetChildByName('templates_include.xsl');
            if (!$idTemplatesInclude)
                continue;
            
            // if the templates if the same folder that the given, these will not include, but will be stored to avoid duplicates
            if ($idTemplatesNode == $originNode->GetParent())
                $storeTemplates = false;
            else
                $storeTemplates = true;
            
            // load the node and the templates include in the document
            $templatesInclude = new Node($idTemplatesInclude);
            $domIncludes = new DOMDocument();
            if (!$storeTemplates and $content)
                $domIncludes->loadXML($content);
            else
                $domIncludes->loadXML($templatesInclude->GetContent());
            $includes = $domIncludes->getElementsByTagName('include');
            if (!$includes->length)
            {
                // there isn't templates to include
                continue;
            }
            
            // get the path to the current node
            $tempPath = $parent->GetRelativePath($project->GetID());
            
            foreach ($includes as $include)
            {
                //obtain the template file
                $templateURL = $include->getAttribute('href');
                if (FsUtils::is_url($templateURL))
                    $template = FsUtils::get_url_file($templateURL);
                else
                    $template = $templateURL;
                
                // inclde the template with the absolute URL
                if ($storeTemplates)
                {
                    // if the template have been stored yet, it will not been saved
                    if (isset($templates[$template]))
                        continue;
                    
                    // create a new xsl:include tag for each template inclusion
                    $domElement = $dom->createElement('xsl:include');
                    $domAttribute = $dom->createAttribute('href');
                    if ($template == $templateURL)
                        $domAttribute->value = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $tempPath . '/templates/' . $template;
                    else
                        $domAttribute->value = $templateURL;
                    $domElement->appendChild($domAttribute);
                    
                    // insert the new include tag before the xsl:template tag that will be replaced
                    $root->item(0)->appendChild($domElement);
                }
                // save the element in the templates array
                $templates[$template] = true;
            }
        }
        
        //save the regenerated XML content to a string
        $content = $dom->saveXML();
        return true;
    }
    
    /**
     * Change all the templates inclusions in each docxap.xsl template that dependant of the given node with the new name
     * Used after a name project, server o section has been changed
     * @param Node $node
     * @param Node $oldNode
     * @return boolean
     */
    public static function rename_include_templates(Node $node, Node $oldNode)
    {
        if ($node->GetNodeName() == $oldNode->GetNodeName())
            return true;
        
        //if the node type is not a project, server or section, it do nothing 
        if ($node->GetNodeType() != Ximdex\Services\NodeType::PROJECT and $node->GetNodeType() != Ximdex\Services\NodeType::SERVER
               and $node->GetNodeType() != Ximdex\Services\NodeType::SECTION)
            return true;
            
        //load the project ID of the node given
        $projectId = $node->GetProject();
        
        return self::rename_templates_in_node($node, $oldNode, $projectId);
    }
    
    /**
     * Change the references to a templates_include.xsl in a docxap file with a templates node given
     * @param Node $node
     * @param Node $oldNode
     * @param int $projectId
     * @param array $urls
     * @return boolean
     */
    private static function rename_templates_in_node(Node $node, Node $oldNode, $projectId, $urls = array())
    {
        //look for template folder
        $templateFolderId = $node->GetChildren(Ximdex\Services\NodeType::TEMPLATES_ROOT_FOLDER);
        if ($templateFolderId)
        {
            $templateFolder = new Node($templateFolderId[0]);
            
            //look for docxap template
            $docxapTemplateId = $templateFolder->GetChildByName('docxap.xsl');
            if ($docxapTemplateId)
            {
                $docxapTemplate = new Node($docxapTemplateId);
                $content = $docxapTemplate->GetContent();
                
                //change the include references for templates_include.xsl to the new path
                $dom = new DOMDocument();
                $dom->formatOutput = true;
                $dom->preserveWhiteSpace = false;
                if (!@$dom->loadXML($content))
                {
                    $error = 'Can\'t load a dependant docxap template to update the new name in include references';
                    $this->messages->add($error, MSG_TYPE_WARNING);
                    return false;
                }
                //generate the new and old template URL for this node
                $oldTemplateURL = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $node->GetRelativePath($projectId, $oldNode)
                        . '/templates/templates_include.xsl';
                $newTemplateURL = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $node->GetRelativePath($projectId) . '/templates/templates_include.xsl';
                //read the include tags from the docxap
                $root = $dom->getElementsByTagName('stylesheet');
                $xPath = new DOMXPath($dom);
                $templatesIncludes = $xPath->query('//xsl:stylesheet/xsl:include');
                foreach ($templatesIncludes as $templateInclude)
                {
                    //if the template file is not a templates_include.xsl file continue to the next one
                    if (FsUtils::get_url_file($templateInclude->getAttribute('href')) == 'templates_include.xsl')
                    {
                        if ($templateInclude->getAttribute('href') == $oldTemplateURL)
                        {
                            //replace the xsl:include tag for each template inclusion with the new URL
                            $domElement = $dom->createElement('xsl:include');
                            $domAttribute = $dom->createAttribute('href');
                            $domAttribute->value = $newTemplateURL;
                            $domElement->appendChild($domAttribute);
                            $root->item(0)->replaceChild($domElement, $templateInclude);
                            
                            //store the new include URL linked to the old one (for the next templates)
                            $urls[$oldTemplateURL] = $newTemplateURL;
                        }
                        elseif (isset($urls[$templateInclude->getAttribute('href')]))
                        {
                            //check if the template inclusion is in the parent nodes list, and replace it
                            $domElement = $dom->createElement('xsl:include');
                            $domAttribute = $dom->createAttribute('href');
                            $domAttribute->value = $urls[$templateInclude->getAttribute('href')];
                            $domElement->appendChild($domAttribute);
                            $root->item(0)->replaceChild($domElement, $templateInclude);
                        }
                    }
                }
                $content = $dom->saveXML();
                if ($content === false)
                    return false;
                if ($docxapTemplate->SetContent($content) === false)
                    return false;
            }
        }
        
        //get children of the node
        $childNodes = $node->GetChildren();
        foreach ($childNodes as $childNode)
        {
            $childNode = new Node($childNode);
            //only project, servers and section/subsections can storage template folders
            if ($childNode->GetNodeType() == Ximdex\Services\NodeType::PROJECT or $childNode->GetNodeType() == Ximdex\Services\NodeType::SERVER
                    or $childNode->GetNodeType() == Ximdex\Services\NodeType::SECTION)
            {
                //call in recursive mode with the child node
                $res = self::rename_templates_in_node($childNode, $oldNode, $projectId, $urls);
                if ($res === false)
                    return false;
            }
        }
        return true;
    }
    
    /**
     * Move a template node to another include templates and remove the previous reference
     * Only do it if there is already a inclusion of this template in the origin templates include
     * @param int $targetParentID
     * @return boolean
     */
    public function move_node($targetParentID)
    {
        //locate the NodeID for the parent templates node
        $templatesId = $this->parent->GetParent();
        $templates = new Node ($templatesId);
        if (!$templates->GetID())
        {
            $this->messages->add('The node has not a parent node');
            return false;
        }
        $parentId = $templates->GetParent();
        
        //remove the template
        $res = $this->removeIncludeFile($this->parent->GetNodeName(), $parentId, $this->parent->GetNodeType());
        if ($res === false)
            return false;
        
        //if the template doesn't exists in the templates inclusions, it will not included in the destination templates include
        if ($res === null)
            return true;
            
        //include the template
        if ($this->setIncludeContent($this->parent->GetNodeName(), $targetParentID, $this->parent->GetNodeType(), null) === false)
            return false;
        return true;
    }
    
    /**
     * Search for a template name in a parent templates folder by the correspondant node given
     * @param string $templateName
     * @param Node $node
     * @return boolean
     */
    private static function isIncludedInTemplates($templateName, Node $node)
    {
        if ($templateName == 'templates_include.xsl' or $templateName == 'docxap.xsl')
            return true;
        //get parent templates folder
        $templates = new Node($node->getParent());
        $includeId = $templates->GetChildByName('templates_include.xsl');
        if (!$includeId)
        {
            //There's not a templates_include.xsl file
            return false;
        }
        //get includes template node and its content
        $includeNode = new Node($includeId);
        $includeContent = $includeNode->getContent();
        $dom = new DOMDocument();
        if (!@$dom->loadXML($includeContent))
        {
            Logger::error('Can\'t load XML content from templates_includes.xsl node with ID: ' . $includeNode->GetID());
            return false;
        }
        //check if there is a template with that name
        $xPath = new DOMXPath($dom);
        $includeTag = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateName']");
        if ($includeTag->length)
        {
            //template exists
            return true;
        }
        return false;
    }
    
    /**
     * Remove all references to the template given in the templates_include files under the nodes tree
     * Use it after a template.xsl file has been removed
     * @param Node $node
     * @param string $templateURL
     * @return boolean
     */
    private function remove_template(Node $node, $templateURL = null)
    {
        if ($node->GetNodeName() == 'templates_include.xsl' or $node->GetNodeName() == 'docxap.xsl')
            return true;
        
        // generate the template URL for the template node to remove one time
        if (!$templateURL)
        {
            $projectId = $node->GetProject();
            $templateURL = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $node->GetRelativePath($projectId);
            
            // we need the node in the parent section or server of the template
            $node = new Node($node->getParent());
            if (!$node->GetID() or $node->GetNodeType() != \Ximdex\Services\NodeType::TEMPLATES_ROOT_FOLDER)
            {
                $this->messages->add('Cannot load the templates folder for ID: ' . $node->getParent(), MSG_TYPE_WARNING);
                return false;
            }
            $node = new Node($node->getParent());
            if (!$node->GetID())
            {
                $this->messages->add('Cannot load node for ID: ' . $node->getParent(), MSG_TYPE_WARNING);
                return false;
            }
        }
        
        //look for templates folder
        $templateFolderId = $node->GetChildren(Ximdex\Services\NodeType::TEMPLATES_ROOT_FOLDER);
        if ($templateFolderId)
        {
            $templateFolder = new Node($templateFolderId[0]);
            
            // look for templates_include node
            $templatesIncludeId = $templateFolder->GetChildByName('templates_include.xsl');
            if ($templatesIncludeId)
            {
                // get templates_include XML content
                $templatesInclude = new Node($templatesIncludeId);
                $content = $templatesInclude->GetContent();
                
                //change the include references for templates_include.xsl to the new path
                $dom = new DOMDocument();
                $dom->formatOutput = true;
                $dom->preserveWhiteSpace = false;
                if (!@$dom->loadXML($content))
                {
                    $this->messages->add('Can\'t load a dependant includes template to remove the include reference', MSG_TYPE_WARNING);
                    return false;
                }
                
                // search an ocurrence for the related include tag from the XSL
                $xPath = new DOMXPath($dom);
                $template = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateURL']");
                if ($template->length)
                {
                    //remove the template to delete from the content
                    $root = $dom->getElementsByTagName('stylesheet');
                    if ($root->item(0)->removeChild($template->item(0)) === false)
                    {
                        $this->messages->add('Can\'t remove the template include reference in includes_template node: ' . $docxapTemplateId
                                , MSG_TYPE_WARNING);
                        return false;
                    }
                    
                    // save the modified content to the templates_include node
                    $content = $dom->saveXML();
                    if ($content === false)
                    {
                        $error = \Ximdex\Error::error_message();
                        $this->messages->add($error, MSG_TYPE_ERROR);
                        return false;
                    }
                    if ($templatesInclude->SetContent($content) === false)
                    {
                        $this->messages->mergeMessages($includeTemplate->messages);
                        return false;
                    }
                }
            }
        }
        
        //get children of the node
        $childNodes = $node->GetChildren();
        foreach ($childNodes as $childNode)
        {
            $childNode = new Node($childNode);
            
            //only project, servers and section/subsections can storage template folders
            if ($childNode->GetNodeType() == Ximdex\Services\NodeType::PROJECT or $childNode->GetNodeType() == Ximdex\Services\NodeType::SERVER
                or $childNode->GetNodeType() == Ximdex\Services\NodeType::SECTION)
            {
                //call in recursive mode with the child node
                $res = $this->remove_template($childNode, $templateURL);
                if ($res === false)
                    return false;
            }
        }
        return true;
    }
    
    /**
     * Add reference to the template node given in the templates_include files under the nodes tree (recursive method)
     * @param Node $node
     * @param string $templateURL
     * @param string $templateName
     * @return boolean
     */
    private function include_template(Node $node, $templateURL = null, $templateName = null)
    {
        if ($node->GetNodeName() == 'templates_include.xsl' or $node->GetNodeName() == 'docxap.xsl')
            return true;
        
        // generate the template URL for the template node to remove one time
        if (!$templateURL)
        {
            $templateName = $node->GetNodeName();
            $projectId = $node->GetProject();
            $templateURL = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $node->GetRelativePath($projectId);
            
            // we need the node in the parent section or server of the template
            $node = new Node($node->getParent());
            if (!$node->GetID() or $node->GetNodeType() != \Ximdex\Services\NodeType::TEMPLATES_ROOT_FOLDER)
            {
                $this->messages->add('Cannot load the templates folder for ID: ' . $node->getParent(), MSG_TYPE_WARNING);
                return false;
            }
            $node = new Node($node->getParent());
            if (!$node->GetID())
            {
                $this->messages->add('Cannot load node for ID: ' . $node->getParent(), MSG_TYPE_WARNING);
                return false;
            }
        }
        
        //look for templates folder
        $templateFolderId = $node->GetChildren(Ximdex\Services\NodeType::TEMPLATES_ROOT_FOLDER);
        if ($templateFolderId)
        {
            $templateFolder = new Node($templateFolderId[0]);
            
            // look for templates_include
            $templatesIncludeId = $templateFolder->GetChildByName('templates_include.xsl');
            if ($templatesIncludeId)
            {
                // get templates_include XML content
                $templatesInclude = new Node($templatesIncludeId);
                $content = $templatesInclude->GetContent();
                
                //change the include references for templates_include.xsl to the new path
                $dom = new DOMDocument();
                $dom->formatOutput = true;
                $dom->preserveWhiteSpace = false;
                if (!@$dom->loadXML($content))
                {
                    $this->messages->add('Can\'t load a dependant docxap template to include the new templates include', MSG_TYPE_WARNING);
                    return false;
                }
                
                // search a possible ocurrence for the related include tag from the XSL to avoid the inclusion
                $xPath = new DOMXPath($dom);
                $template = $xPath->query("/xsl:stylesheet/xsl:include[contains(@href,'/$templateName')]/@href");
                $res = $template->length;
                if (!$res)
                {
                    $template = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateName']");
                    $res = $template->length;
                }
                if (!$res)
                {
                    //add the template
                    $root = $xPath->query('//xsl:stylesheet');
                    
                    //generate the include tag with its href value
                    $domElement = $dom->createElement('xsl:include');
                    $domAttribute = $dom->createAttribute('href');
                    $domAttribute->value = $templateURL;
                    $domElement->appendChild($domAttribute);
                    
                    //add the new element
                    $root->item(0)->appendChild($domElement);
                    
                    // save the modified content to the includes_template node
                    $content = $dom->saveXML();
                    if ($content === false)
                    {
                        $error = \Ximdex\Error::error_message();
                        $this->messages->add($error, MSG_TYPE_ERROR);
                        return false;
                    }
                    if ($templatesInclude->SetContent($content) === false)
                    {
                        $this->messages->mergeMessages($templatesInclude->messages);
                        return false;
                    }
                }
            }
        }
        
        //get children of the node
        $childNodes = $node->GetChildren();
        foreach ($childNodes as $childNode)
        {
            $childNode = new Node($childNode);
            
            //only project, servers and section/subsections can storage template folders
            if ($childNode->GetNodeType() == Ximdex\Services\NodeType::PROJECT or $childNode->GetNodeType() == Ximdex\Services\NodeType::SERVER
                    or $childNode->GetNodeType() == Ximdex\Services\NodeType::SECTION)
            {
                //call in recursive mode with the child node
                $res = $this->include_template($childNode, $templateURL, $templateName);
                if ($res === false)
                    return false;
            }
        }
        return true;
    }
    
    /**
     * Search for a templates_include from the correspondant node given in the docxap node, if there's one
     * @param Node $node
     * @return boolean
     */
    private static function isIncludedInDocxapFile(Node $node)
    {
        if ($node->GetNodeName() != 'templates_include.xsl')
            return false;
        
        //get parent templates folder
        $templates = new Node($node->getParent());
        $docxapId = $templates->GetChildByName('docxap.xsl');
        if (!$docxapId)
        {
            //There's not a docxap.xsl file in the current templates folder
            return true;
        }
        
        //get includes template node and its content
        $includeNode = new Node($docxapId);
        $includeContent = $includeNode->getContent();
        $dom = new DOMDocument();
        if (!@$dom->loadXML($includeContent))
        {
            Logger::error('Can\'t load XML content from docxap node with ID: ' . $includeNode->GetID());
            return false;
        }
        
        //check if there is a template with that name
        $xPath = new DOMXPath($dom);
        $projectId = $node->GetProject();
        $templateURL = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $node->GetRelativePath($projectId);
        $includeTag = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateURL']");
        if ($includeTag->length)
        {
            //template exists
            return true;
        }
        return false;
    }
}