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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use Ximdex\Deps\DepsManager;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\Messages;
use Ximdex\Logger;
use Ximdex\Models\FastTraverse;

class XsltNode extends FileNode
{
    public $messages;
    
    private $xsltOldName = '';

    public function __construct($node = null)
    {
        if (is_object($node)) {
            $this->parent = $node;
        } else if (is_numeric($node) || $node == null) {
            $this->parent = new Node($node, false);
        }
        $this->nodeID = $this->parent->get('IdNode');
        $this->dbObj = new \Ximdex\Runtime\Db();
        $this->nodeType = $this->parent->nodeType;
        $this->messages = new Messages();
        $this->xsltOldName = $this->parent->get('Name');
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::createNode()
     */
    public function createNode(string $xsltName = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null
        , string $ptdSourcePath = null)
    {
        $xslSourcePath = null;
        if ($ptdSourcePath != null) {

            // Saving xslt content
            $xslContent = FsUtils::file_get_contents($ptdSourcePath);
            if ($xslContent === false) {
                return false;
            }
            $xslContent = $this->sanitizeContent($xslContent);
            $xslSourcePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/' . uniqid() . '_' . $parentID . $xsltName;
            if (! FsUtils::file_put_contents($xslSourcePath, $xslContent)) {
                Logger::error('Error saving xslt file');
                $this->messages->add('Error saving xslt file: ' . $parentID . $xsltName, MSG_TYPE_ERROR);
                return false;
            }
        }
        $res = parent::createNode($xsltName, $parentID, $nodeTypeID, $stateID, $xslSourcePath);
        if ($res === false) {
            return false;
        }
        if ($xslSourcePath) {
            FsUtils::delete($xslSourcePath);
        }
        
        // Creating include file with the template inside
        $parent = new Node($parentID);
        if ($parent->getNodeType() != NodeTypeConstants::TEMPLATES_ROOT_FOLDER) {
            $this->messages->add('The node ' . $parentID . ' is not a templates folder', MSG_TYPE_ERROR);
            return false;
        }
        $includeId = $parent->getChildByName('templates_include.xsl');
        if (! $includeId) {
            
            // There is not a templates_include.xsl file in this templates folder
            $includeId = $this->create_templates_include($parentID, $stateID);
            if (! $includeId)
                return false;
        }
        return true;
    }

    /**
     * Generation of a new templates_include.xsl node in a templates folder
     * 
     * @param int $idTemplatesFolder
     * @param int $stateID
     * @param string $templateName
     * @return boolean|int
     */
    public function create_templates_include(int $idTemplatesFolder, int $stateID = null, string $templateName = null)
    {
        Logger::info('Creating unexisting templates include xslt file at folder ' . $idTemplatesFolder);
        $dummyXml = '<?xml version="1.0" encoding="UTF-8"?>
				<dext:root xmlns:dext="http://www.ximdex.com" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
				<xsl:dummy />
				</dext:root>';
        $xslSourcePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/' . uniqid() . '_templates_include.xsl';
        if (! FsUtils::file_put_contents($xslSourcePath, $dummyXml)) {
            Logger::error('Error saving templates_include.xsl file');
            $this->messages->add('Error saving templates_include.xsl file', MSG_TYPE_ERROR);
            return false;
        }
        $incNode = new Node();
        $id = $incNode->createNode('templates_include.xsl', $idTemplatesFolder, NodeTypeConstants::XSL_TEMPLATE, $stateID, $xslSourcePath);
        FsUtils::delete($xslSourcePath);
        if ($id > 0) {
            $incNode = new Node($id);
            $includeContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $includeContent .= '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">' . "\n";
            if ($templateName) {
                
                // Generate the URL to the XSL template file
                $projectId = $incNode->getProject();
                $templatesNode = new Node($idTemplatesFolder);
                $templateURL = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') 
                    . $templatesNode->getRelativePath($projectId) . '/' . $templateName;
                $includeContent .= "\t<xsl:include href=\"$templateURL\"/>\n";
            }
            $includeContent .= '</xsl:stylesheet>';
            $incNode->setContent($includeContent);
            
            // Save the dependencies to the documents folders if exist (with the templates folder node)
            $project = new Node($incNode->getProject());
            if (! $this->rel_include_templates_to_documents_folders($project)) {
                return false;
            }
        } else {
            $this->messages->mergeMessages($incNode->messages);
            return false;
        }
        
        // It is not necesary to create a docxap file in a project based in a theme
        if (! isset($GLOBALS['fromTheme']) or ! $GLOBALS['fromTheme']) {
            
            // If there is not a docxap.xsl file in the project/templates folder, create a new one
            $res = $this->create_project_docxap_file();
            if ($res === false) {
                Logger::fatal('The project docxap XSL template could not been created');
                return false;
            }
        }
        return $id;
    }
    
    /**
     * Make the relations between a templates parent section node given and the dependant documents/ximlets folders nodes
     * 
     * @param Node $section
     * @param Node $node
     * @param DepsManager $depsMngr
     * @return boolean
     */
    public function rel_include_templates_to_documents_folders(Node $section, Node $node = null, DepsManager $depsMngr = null) : bool
    {
        if (! $depsMngr) {
            Logger::info('Making a relation between documents section and templates with section ' . $section->getID());
        }
        
        // Check if there is a local templates_includes
        $idTemplatesNode = $section->getChildren(NodeTypeConstants::TEMPLATES_ROOT_FOLDER);
        if ($idTemplatesNode) {
            
            // There is a templates folder, search for a template with the name templates_include.xsl
            $templatesNode = new Node($idTemplatesNode[0]);
            $idTemplatesIncludes = $templatesNode->getChildByName('templates_include.xsl');
            
            // Get templates folder for the section
            if ($idTemplatesIncludes) {
                $node = new Node($templatesNode->getID());
                if (! $node->getID()) {
                    $this->messages->add('There is not a templates folder for the section: ' . $section->getID(), MSG_TYPE_ERROR);
                    return false;
                }
            }
        }
        
        // Get the documents folder node
        $documentsNode = $section->getChildren(NodeTypeConstants::XML_ROOT_FOLDER);
        if ($documentsNode) {
            
            // There is a documents folder in this place
            if ($node) {
                $idDocFolder = $documentsNode[0];
                if (! $depsMngr) {
                    $depsMngr = new DepsManager();
                }
                
                // Delete the previous relation
                $depsMngr->deleteBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $idDocFolder);
                
                // Set the relation 
                if ($depsMngr->set(DepsManager::DOCFOLDER_TEMPLATESINC, $idDocFolder, $node->getID()) === false) {
                    $this->messages->add('Cannot link templates node ' . $node->getID() . ' with documents folder ' 
                        . $idDocFolder, MSG_TYPE_ERROR);
                    return false;
                }
            }
        }
        
        // Get the ximlets folder node
        $ximletsNode = $section->getChildren(NodeTypeConstants::XIMLET_ROOT_FOLDER);
        if ($ximletsNode) {
            
            // There is a ximlets folder in this place
            if ($node) {
                $idXimletFolder = $ximletsNode[0];
                if (! $depsMngr) {
                    $depsMngr = new DepsManager();
                }
                
                // Delete the previous relation
                $depsMngr->deleteBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $idXimletFolder);
                
                // Set the relation
                if ($depsMngr->set(DepsManager::DOCFOLDER_TEMPLATESINC, $idXimletFolder, $node->getID()) === false) {
                    $this->messages->add('Cannot link templates node ' . $node->getID() . ' with ximlets folder ' 
                        . $idXimletFolder, MSG_TYPE_ERROR);
                    return false;
                }
            }
        }
        
        // Get the children nodes of the current section
        $nodes = FastTraverse::getChildren($section->getID(), ['node' => ['IdNodeType']], 1);
        if ($nodes === false) {
            $this->messages->add('Cannot get children nodes from node: ' . $section->getID() . ' in reload templates include files process'
                , MSG_TYPE_ERROR);
            return false;
        }
        if (! $nodes) {
            return true;
        }
        foreach ($nodes[1] as $idChildNode => $nodeData)
        {
            // Only project, servers and section/subsections can storage template folders
            $idNodeType = $nodeData['node']['IdNodeType'];
            if ($idNodeType == NodeTypeConstants::SERVER or $idNodeType == NodeTypeConstants::SECTION) {
                
                // Call in recursive mode with the templates folder, for whole project nodes tree
                $childNode = new Node($idChildNode);
                $res = $this->rel_include_templates_to_documents_folders($childNode, $node, $depsMngr);
                if ($res === false) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::renameNode()
     */
    public function renameNode(string $name) : bool
    {
        if (null == $name) {
            return false;
        }
        $oldName = explode('.', $this->xsltOldName);
        $name = explode('.', $name);
        if (count($name) != 2) {
            $this->messages->add('The file extension is necessary', MSG_TYPE_ERROR);
            return false;
        }
        
        // Open the file and make the replacement inside
        $tpl = new Node($this->nodeID);
        $rpl1 = 'name="' . $oldName[0];
        $rpl2 = 'name="' . $name[0];
        $new_content = str_replace($rpl1, $rpl2, $tpl->getContent());
        $rpl1 = 'match="' . $oldName[0];
        $rpl2 = 'match="' . $name[0];
        $new_content = str_replace($rpl1, $rpl2, $new_content);
        $tpl->SetContent($new_content);
        
        // Reload the templates include files for this new project
        $node = new Node($this->nodeID);
        if ($this->reload_templates_include(new Node($node->getProject())) === false) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::deleteNode()
     */
    public function deleteNode() : bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::setContent()
     */
    public function setContent(string $content, bool $commitNode = false, Node $node = null) : bool
    {
        // Checking the valid XML of the given content
        $domDoc = new \DOMDocument();
        $domDoc->formatOutput = true;
        $domDoc->preserveWhiteSpace = false;
        $res = @$domDoc->loadXML($content);
        
        // Validating of the correct XSL document in the correct system path (only if node is given)
        if ($node and $res) {
            $xsltprocessor = new \XSLTProcessor();
            $dom = new \DOMDocument();
            @$dom->loadXML($content);
            $project = new Node($node->getProject());
            $dom->documentURI = XIMDEX_ROOT_PATH . App::getValue('NodeRoot') . $node->getRelativePath($project->getID());
            if (@$xsltprocessor->importStyleSheet($dom) === false) {
                $error = Messages::error_message('XSLTProcessor::importStylesheet(): ');
                
                // Avoid the PATH_TO_LOCAL_TEMPLATE_INCLUDE token error
                if ($error and strpos($error, '##PATH_TO_LOCAL_TEMPLATE_INCLUDE##') === false) {
                    if ($node and $node->getDescription()) {
                        $error = 'Invalid XSL for node ' . $node->getDescription() . ': ' . $error;
                    } else {
                        $error = 'Invalid XSL to set content operation: ' . $error;
                    }
                    $defaultLog = Logger::get_active_instance();
                    Logger::generate('XSLT', 'xslt');
                    Logger::setActiveLog('xslt');
                    Logger::error($error);
                    Logger::setActiveLog($defaultLog);
                    $this->messages->add($error, MSG_TYPE_WARNING);
                    $GLOBALS['errorsInXslTransformation'] = [$error];
                    $res = true;
                }
            }
        }
        if ($res) {
            $content = $domDoc->saveXML();
        }
        $content = $this->sanitizeContent($content);
        if ($content === false) {
            return false;
        }
        if (parent::SetContent($content, $commitNode, $node) === false) {
            return false;
        }
        if (isset($node)) {
            if ($node->getNodeName() != 'docxap.xsl') {
                
                // If the templates folder is the project one, and there is not a docxap file, send a alert to the user
                $templates = new Node($node->getParent());
                $section = new Node($templates->getParent());
                if ($section->getNodeType() == NodeTypeConstants::PROJECT) {
                    $docxapId = $templates->getChildByName('docxap.xsl');
                    if (! $docxapId) {
                        $this->messages->add('A docxap.xsl template file must be in the project templates folder', MSG_TYPE_WARNING);
                    }
                }
            }
            if ($node->getNodeName() != 'templates_include.xsl' and !self::isIncludedInTemplates($node->getNodeName(), $node)) {
                
                // Check if the saved template is already included in templates_include sending an advise to the user
                $this->messages->add('Note that this template isn\'t included in the templates_includes.xsl file', MSG_TYPE_WARNING);
            }
        }
        return true;
    }

    private function sanitizeContent(string $content)
    {
        if (empty($content)) {
            Logger::info('It have been created or edited a document with empty content');
            return $content;
        }
        $xsldom = new \DOMDocument();
        $xsldom->formatOutput = true;
        $xsldom->preserveWhiteSpace = false;
        if (@$xsldom->loadXML($content) === false) {
            return $content;
        }
        $xpath = new \DOMXPath($xsldom);
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

    private function splitCData(\DOMElement $node, \DOMDocument & $xsldom)
    {
        $nodevalue = $node->nodeValue;

        // Split CDATA sections if contains attributes references
        $matches = [];
        $ret = preg_match_all('/"{@([^}]+)}"/', $nodevalue, $matches);
        if (! $ret) {
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
                $arrCD = array_merge($arrCD, (array) $this->splitCData($textnode, $xsldom));
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
     * Get the documents that must be publish when the template is published
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FileNode::getPublishabledDeps()
     */
    public function getPublishabledDeps(array $params = []) : ?array
    {
        $depsMngr = new DepsManager();
        return $depsMngr->getByTarget(DepsManager::STRDOC_TEMPLATE, $this->parent->get('IdNode'));
    }
    
    /**
     * Create a new basic docxap XSLT file for the project if it's not exists
     * 
     * @return NULL|boolean|string
     */
    private function create_project_docxap_file()
    {
        // Obtain the project node
        $node = new Node($this->nodeID);
        $project = new Node($node->getProject());
        
        // Obtain the project templates node
        $idXimptdProject = $project->getChildByName('templates');
        $ptdProject = new Node($idXimptdProject);
        
        // Obtain the ID for an existant docaxp file yet
        $idDocxapProject = $ptdProject->getChildByName('docxap.xsl');
        if ($idDocxapProject) {
            return null;
        }
        
        // Generation of the file docxap.xsl with project name inside
        $xslSourcePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/docxap.xsl';

        // Generation of the file docxap.xsl with project name inside
        $content = <<<DOCXAP
<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html" />
   	<xsl:param name="xmlcontent" />
   	<!-- DO NOT REPLACE THE FOLLOWING LINE IF LOCAL XSLT INCLUDES ARE IN USE -->
   	<xsl:include href="##PATH_TO_LOCAL_TEMPLATE_INCLUDE##/templates_include.xsl" />
   	<!-- END XSLT INCLUDE INSERTION -->
	<xsl:template name="docxap" match="docxap">
		<!-- XHTML Document -->
	</xsl:template>
</xsl:stylesheet>
DOCXAP;
		if (! FsUtils::file_put_contents($xslSourcePath, $content)) {
		    return false;
		}
		
		// Obtain the ID for XSL templates node type
		$nodeTypeID = NodeTypeConstants::XSL_TEMPLATE;
		
		// Create the node for the generated file
		$node = new Node();
		$idDocxapProject = $node->CreateNode('docxap.xsl', $idXimptdProject, $nodeTypeID, null, $xslSourcePath);	
		if (! $idDocxapProject) {
		    Logger::error('Error creating the node for project docxap template');
		    return false;
		}
		Logger::info('Project docxap.xsl node generated');
		
		// Return the ID for the new project docxap template node
		return $idDocxapProject;
    }
    
    /**
     * Move a template node to another include templates
     * 
     * @param int $targetParentID
     * @return boolean
     */
    public function move_node(int $targetParentID)
    {
        // Locate the NodeID for the parent templates node
        $templatesId = $this->parent->getParent();
        $templates = new Node($templatesId);
        if (! $templates->getID()) {
            $this->messages->add('The node has not a parent node');
            return false;
        }
        
        // Reload the templates include files for this new project
        if ($this->reload_templates_include(new Node($templates->getProject())) === false) {
            return false;
        }
        return true;
    }
    
    /**
     * Search for a template name in a parent templates folder by the correspondant node given
     * 
     * @param string $templateName
     * @param Node $node
     * @return boolean
     */
    private static function isIncludedInTemplates(string $templateName, Node $node)
    {
        if ($templateName == 'templates_include.xsl' or $templateName == 'docxap.xsl') {
            return true;
        }
        
        // Get parent templates folder
        $templates = new Node($node->getParent());
        $includeId = $templates->getChildByName('templates_include.xsl');
        if (! $includeId) {
            
            // There's not a templates_include.xsl file
            return false;
        }
        // Get includes template node and its content
        $includeNode = new Node($includeId);
        $includeContent = $includeNode->getContent();
        if (stripos($includeContent, '/' . $templateName) !== false) {
            
            // Template exists
            return true;
        }
        return false;
    }
    
    /**
     * Search for a templates_include from the correspondant node given in the docxap node, if there's one
     * 
     * @param Node $node
     * @return boolean
     */
    private static function isIncludedInDocxapFile(Node $node)
    {
        if ($node->getNodeName() != 'templates_include.xsl') {
            return false;
        }
        
        // Get parent templates folder
        $templates = new Node($node->getParent());
        $docxapId = $templates->getChildByName('docxap.xsl');
        if (! $docxapId) {
            
            // There's not a docxap.xsl file in the current templates folder
            return true;
        }
        
        // Get includes template node and its content
        $includeNode = new Node($docxapId);
        $includeContent = $includeNode->getContent();
        $dom = new \DOMDocument();
        if (! @$dom->loadXML($includeContent)) {
            Logger::error('Can\'t load XML content from docxap node with ID: ' . $includeNode->getID());
            return false;
        }
        
        // Check if there is a template with that name
        $xPath = new \DOMXPath($dom);
        $projectId = $node->getProject();
        $templateURL = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') . $node->getRelativePath($projectId);
        $includeTag = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateURL']");
        if ($includeTag->length) {
            
            // Template exists
            return true;
        }
        return false;
    }
    
    /**
     * Include the correspondant includes_template.xsl for the current document; based in DOCFOLDER_TEMPLATESINC dependencie
     * If the $idDocLocalNode parameter is null, the templates to use will be the associated to the project with node 
     * given by $idProject parameter
     * 
     * @param string $content
     * @param int $idDocLocalNode
     * @param int $idProject
     * @param string $urlTemplatesInclude
     * @return bool
     */
    public static function replace_path_to_local_templatesInclude(string & $content, int $idDocLocalNode, int $idProject = null
        , string & $urlTemplatesInclude = null)
    {
        if ($idDocLocalNode) {
            Logger::info('Replacing includes template with document node ' . $idDocLocalNode);
            $node = new Node($idDocLocalNode);
            if (! $node->getID()) {
                Logger::error('Cannot replace the local templates include: The node ' . $idDocLocalNode . ' does not exists');
                return false;
            }
            
            // Get the documents folder ID of the document node ID given
            if ($node->getNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                $documentsFolderId = $node->_getParentByType(NodeTypeConstants::XML_ROOT_FOLDER);
            } elseif ($node->getNodeType() == NodeTypeConstants::XIMLET) {
                $documentsFolderId = $node->_getParentByType(NodeTypeConstants::XIMLET_ROOT_FOLDER);
            } else {
                Logger::error('Cannot replace the local templates include: Node is not of XML document, Ximlet or METADATA type');
                return false;
            }
            if (! $documentsFolderId) {
                Logger::error('Cannot replace the local templates include: Container for node ' . $idDocLocalNode . ' not found');
                return false;
            }
            
            // Get the templates folder node that references the previous document
            $depsManager = new DepsManager();
            $idTemplatesFolder = $depsManager->getBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $documentsFolderId);
        } elseif ($idProject) {
            
            // Get the templates folder of the project
            Logger::info('Replacing includes template with project node ' . $idProject);
            $node = new Node($idProject);
            if (! $node->getID()) {
                Logger::error('Cannot replace the local templates include: Project node ' . $idProject . ' does not exists');
                return false;
            }
            $idTemplatesFolder = $node->getChildren(NodeTypeConstants::TEMPLATES_ROOT_FOLDER);
        } else {
            Logger::error('Cannot replace the local templates include: empty node parameters given');
            return false;
        }
        if ($idTemplatesFolder) {
            $idTemplatesFolder = $idTemplatesFolder[0];
        } else {
            $idTemplatesFolder = 0;
        }
        $templatesFolderNode = new Node($idTemplatesFolder);
        if (! $templatesFolderNode->getID()) {
            Logger::error('Cannot replace the local templates include: Templates folder not found for document node ' . $idDocLocalNode);
            return false;
        }
        
        // Assing the templates_include in the docxap content
        $PATH_TEMPLATE_INCLUDE = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') 
                . $templatesFolderNode->getRelativePath($node->getProject());
        $content = str_replace('##PATH_TO_LOCAL_TEMPLATE_INCLUDE##', $PATH_TEMPLATE_INCLUDE, $content);
        $urlTemplatesInclude = $PATH_TEMPLATE_INCLUDE . '/templates_include.xsl';
        Logger::info('Using document: ' . $urlTemplatesInclude);
        return true;
    }
    
    /**
     * Regenerate the content of all the templates_include.xsl file under the node given (usually the project node)
     * The $priorTemplates parameter is loaded with the nearest templates URLs to the current section
     * 
     * @param Node $node
     * @param array $priorTemplates
     * @param int $projectId
     * @param bool $init
     * @return boolean
     */
    public function reload_templates_include(Node $node, array $priorTemplates = array(), int $projectId = null, bool $init = true)
    {
        if ($init) {
            
            // Only project, servers and section/subsections can storage template folders
            if ($node->getNodeType() != NodeTypeConstants::PROJECTS and $node->getNodeType() != NodeTypeConstants::PROJECT 
                    and $node->getNodeType() != NodeTypeConstants::SERVER and $node->getNodeType() != NodeTypeConstants::SECTION) {
                $this->messages->add('Cannot reload nodes with a node type diferent than project, server or section', MSG_TYPE_ERROR);
                return false;
            }
        }
        
        // Look for templates folder
        $templateFolderId = $node->getChildren(NodeTypeConstants::TEMPLATES_ROOT_FOLDER);
        if ($templateFolderId) {
            $templateFolder = new Node($templateFolderId[0]);
            
            // Look for templates_include
            $templatesIncludeId = $templateFolder->getChildByName('templates_include.xsl');
            if ($templatesIncludeId) {
                if (! $projectId) {
                    
                    // Get the project node ID
                    $projectId = $node->getProject();
                }
                
                // Generate the basic XML header
                $content = '<?xml version="1.0" encoding="UTF-8"?>';
                $content .= "\n" . '<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">';
                
                // Add the local templates
                $templates = $templateFolder->getChildren();
                foreach ($templates as $idTemplate) {
                    $template = new Node($idTemplate);
                    if ($template->getNodeName() == 'templates_include.xsl' or $template->getNodeName() == 'docxap.xsl') {
                        continue;
                    }
                    
                    // Generate the template URL
                    $templateURL = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') 
                        . $template->getRelativePath($projectId);
                    
                    // Save the template and remove a possible ocurrence with the same name (local one is always priority)
                    $priorTemplates[$template->getNodeName()] = $templateURL;
                }
                
                // Include the prior templates
                foreach ($priorTemplates as $templateURL) {
                    $content .= "\n\t" . '<xsl:include href="' . $templateURL . '"/>';
                }
                    
                // Close the XSL content
                $content .= "\n" . '</xsl:stylesheet>';
                
                // Save the XSL content into the templates_include.xsl node
                $templatesInclude = new Node($templatesIncludeId);
                if ($templatesInclude->SetContent($content) === false) {
                    $this->messages->mergeMessages($templatesInclude->messages);
                    return false;
                }
            }
        }
        
        // Get children of the node with its node types
        $nodes = FastTraverse::getChildren($node->getID(), ['node' => ['IdNodeType']], 1);
        if ($nodes === false) {
            $this->messages->add('Cannot get children nodes from node: ' . $node->getID() . ' in reload templates include files process'
                , MSG_TYPE_ERROR);
            return false;
        }
        if (! $nodes) {
            return true;
        }
        foreach ($nodes[1] as $idChildNode => $nodeData) {
            
            // Only project, servers and section/subsections can storage template folders
            $idNodeType = $nodeData['node']['IdNodeType'];
            if ($idNodeType == NodeTypeConstants::PROJECT or $idNodeType == NodeTypeConstants::SERVER 
                    or $idNodeType == NodeTypeConstants::SECTION) {

                // Call in recursive mode with the child node
                $childNode = new Node($idChildNode);
                if (! $childNode->getID()) {
                    Logger::error('Cannot load a node with ID: ' . $childNode->getID() . ' in reload templates includes process');
                    return false;
                }
                $res = $this->reload_templates_include($childNode, $priorTemplates, $projectId, false);
                if ($res === false)
                    return false;
            }
        }
        return true;
    }
}
