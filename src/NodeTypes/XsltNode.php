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

namespace Ximdex\NodeTypes;

use Ximdex\Deps\DepsManager;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;
use Ximdex\Models\FastTraverse;

class XsltNode extends FileNode
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
        $this->dbObj = new \Ximdex\Runtime\Db();
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

            $xslSourcePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/' . $parentID . $xsltName;

            if (!FsUtils::file_put_contents($xslSourcePath, $xslContent)) {
                Logger::error("Error saving xslt file");
                $this->messages->add('Error saving xslt file: ' . $parentID . $xsltName, MSG_TYPE_ERROR);
                return false;
            }
        }
        if (parent::CreateNode($xsltName, $parentID, $nodeTypeID, $stateID, $xslSourcePath) === false)
            return false;
        
        // Creating include file with the template inside
        $parent = new Node($parentID);
        if ($parent->GetNodeType() != \Ximdex\NodeTypes\NodeTypeConstants::TEMPLATES_ROOT_FOLDER)
        {
            $this->messages->add('The node ' . $parentID . ' is not a templates folder', MSG_TYPE_ERROR);
            return false;
        }
        $includeId = $parent->GetChildByName('templates_include.xsl');
        if (!$includeId)
        {
            // there is not a templates_include.xsl file in this templates folder
            $includeId = $this->create_templates_include($parentID, $stateID);
            if (!$includeId)
                return false;
        }
        
        return true;
    }

    /**
     * Generation of a new templates_include.xsl node in a templates folder
     * @param integer $idTemplatesFolder
     * @param integer $nodeTypeID
     * @param integer $stateID
     * @param string $templateName
     * @return boolean
     */
    public function create_templates_include($idTemplatesFolder, $stateID = null, $templateName = null)
    {
        Logger::info("Creating unexisting templates include xslt file at folder $idTemplatesFolder");
        $dummyXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<dext:root xmlns:dext=\"http://www.ximdex.com\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">
				<xsl:dummy />
				</dext:root>";
        $xslSourcePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/templates_include.xsl';
        if (!FsUtils::file_put_contents($xslSourcePath, $dummyXml))
        {
            Logger::error("Error saving templates_include.xsl file");
            $this->messages->add('Error saving templates_include.xsl file', MSG_TYPE_ERROR);
            return false;
        }
        $incNode = new Node();
        $id = $incNode->CreateNode('templates_include.xsl', $idTemplatesFolder, \Ximdex\NodeTypes\NodeTypeConstants::XSL_TEMPLATE, $stateID, $xslSourcePath);
        if ($id > 0) {
            $incNode = new Node($id);
            $includeContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $includeContent .= '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">' . "\n";
            if ($templateName)
            {
                // generate the URL to the XSL template file
                $projectId = $incNode->GetProject();
                $templatesNode = new Node($idTemplatesFolder);
                $templateURL = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') 
                        . $templatesNode->GetRelativePath($projectId) . '/' . $templateName;
                $includeContent .= "\t<xsl:include href=\"$templateURL\"/>\n";
            }
            $includeContent .= '</xsl:stylesheet>';
            $incNode->SetContent($includeContent);
            
            // save the dependencies to the documents folders if exist (with the templates folder node)
            $project = new Node($incNode->getProject());
            if (!$this->rel_include_templates_to_documents_folders($project))
                return false;
        }
        else
        {
            $this->messages->mergeMessages($incNode->messages);
            return false;
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
        
        return $id;
    }
    
    /**
     * Make the relations between a templates parent section node given and the dependant documents/ximlets folders nodes
     * @param Node $section
     * @param Node $node
     * @param DepsManager $depsMngr
     * @return boolean
     */
    public function rel_include_templates_to_documents_folders(Node $section, Node $node = null, DepsManager $depsMngr = null)
    {
        if (!$depsMngr)
            Logger::info('Making a relation between documents section and templates with section ' . $section->GetID());
        // check if there is a local templates_includes
        $idTemplatesNode = $section->GetChildren(\Ximdex\NodeTypes\NodeTypeConstants::TEMPLATES_ROOT_FOLDER);
        if ($idTemplatesNode)
        {
            // there is a templates folder, search for a template with the name templates_include.xsl
            $templatesNode = new Node($idTemplatesNode[0]);
            $idTemplatesIncludes = $templatesNode->GetChildByName('templates_include.xsl');
            // get templates folder for the section
            if ($idTemplatesIncludes)
            {
                $node = new Node($templatesNode->GetID());
                if (!$node->GetID())
                {
                    $this->messages->add('There is not a templates folder for the section: ' . $section->GetID(), MSG_TYPE_ERROR);
                    return false;
                }
            }
        }
        // get the documents folder node
        $documentsNode = $section->GetChildren(\Ximdex\NodeTypes\NodeTypeConstants::XML_ROOT_FOLDER);
        if ($documentsNode)
        {
            // there is a documents folder in this place
            if ($node)
            {
                $idDocFolder = $documentsNode[0];
                if (!$depsMngr)
                    $depsMngr = new DepsManager();
                // delete the previous relation
                $depsMngr->deleteBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $idDocFolder);
                // set the relation 
                if ($depsMngr->set(DepsManager::DOCFOLDER_TEMPLATESINC, $idDocFolder, $node->GetID()) === false)
                {
                    $this->messages->add('Cannot link templates node ' . $node->GetID() . ' with documents folder ' . $idDocFolder, MSG_TYPE_ERROR);
                    return false;
                }
            }
        }
        // get the ximlets folder node
        $ximletsNode = $section->GetChildren(\Ximdex\NodeTypes\NodeTypeConstants::XIMLET_ROOT_FOLDER);
        if ($ximletsNode)
        {
            // there is a ximlets folder in this place
            if ($node)
            {
                $idXimletFolder = $ximletsNode[0];
                if (!$depsMngr)
                    $depsMngr = new DepsManager();
                // delete the previous relation
                $depsMngr->deleteBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $idXimletFolder);
                // set the relation
                if ($depsMngr->set(DepsManager::DOCFOLDER_TEMPLATESINC, $idXimletFolder, $node->GetID()) === false)
                {
                    $this->messages->add('Cannot link templates node ' . $node->GetID() . ' with ximlets folder ' . $idXimletFolder, MSG_TYPE_ERROR);
                    return false;
                }
            }
        }
        // get the children nodes of the current section
        $nodes = FastTraverse::get_children($section->GetID(), ['node' => ['IdNodeType']], 1);
        if ($nodes === false)
        {
            $this->messages->add('Cannot get children nodes from node: ' . $section->GetID() . ' in reload templates include files process'
                    , MSG_TYPE_ERROR);
            return false;
        }
        if (!$nodes) {
            return true;
        }
        foreach ($nodes[1] as $idChildNode => $nodeData)
        {
            // only project, servers and section/subsections can storage template folders
            $idNodeType = $nodeData['node']['IdNodeType'];
            if ($idNodeType == \Ximdex\NodeTypes\NodeTypeConstants::SERVER or $idNodeType == \Ximdex\NodeTypes\NodeTypeConstants::SECTION)
            {
                // call in recursive mode with the templates folder, for whole project nodes tree
                $childNode = new Node($idChildNode);
                $res = $this->rel_include_templates_to_documents_folders($childNode, $node, $depsMngr);
                if ($res === false)
                    return false;
            }
        }
        return true;
    }
    
    /**
     * Make the relations between a server templates section and the paralel metadata section, by the metadata document node given
     * @param Node $node
     * @return boolean
     */
    public function rel_include_templates_to_metadata_section(Node $node)
    {
        Logger::info('Making a relation between metadata section and templates with node ' . $node->GetID());
        $server = new Node($node->getServer());
        $idTemplatesNode = $server->GetChildren(\Ximdex\NodeTypes\NodeTypeConstants::TEMPLATES_ROOT_FOLDER);
        $depsMngr = new DepsManager();
        if ($depsMngr->set(DepsManager::DOCFOLDER_TEMPLATESINC, $node->GetID(), $idTemplatesNode[0]) === false)
        {
            $this->messages->add('Cannot link templates node ' . $idTemplatesNode . ' with metadata section ' . $node->GetID(), MSG_TYPE_ERROR);
            return false;
        }
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
        
        $oldName = explode(".", $this->xsltOldName);
        $newName = explode(".", $newName);
        if (count($newName) != 2)
        {
            $this->messages->add('The file extension is necessary', MSG_TYPE_ERROR);
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
        
        // reload the templates include files for this new project
        $node = new Node($this->nodeID);
        if ($this->reload_templates_include(new Node($node->getProject())) === false)
            return false;
        
        return true;
    }

    /**
     * Delete a node
     */
    public function DeleteNode()
    {
        // Deletes dependencies in rel tables
        $nodeTypeId = $this->parent->get('IdNodeType');
        $templateName = $this->parent->get('Name');
        Logger::info('Xslt dependencies deleted');
        return true;
    }

    public function SetContent($content, $commitNode = NULL, Node $node = null)
    {
        //checking the valid XML of the given content
        $domDoc = new \DOMDocument();
        $domDoc->formatOutput = true;
        $domDoc->preserveWhiteSpace = false;
        $res = @$domDoc->loadXML($content);
        
        //validating of the correct XSL document in the correct system path (only if node is given)
        if ($node and $res)
        {
            $xsltprocessor = new \XSLTProcessor();
            $dom = new \DOMDocument();
            @$dom->loadXML($content);
            $project = new Node($node->GetProject());
            $dom->documentURI = XIMDEX_ROOT_PATH . App::getValue('NodeRoot') . $node->GetRelativePath($project->GetID());
            if (@$xsltprocessor->importStyleSheet($dom) === false)
            {
                $error = \Ximdex\Utils\Messages::error_message('XSLTProcessor::importStylesheet(): ');
                
                // avoid the PATH_TO_LOCAL_TEMPLATE_INCLUDE token error
                if ($error and strpos($error, '##PATH_TO_LOCAL_TEMPLATE_INCLUDE##') === false)
                {
                    if ($node and $node->GetDescription())
                        $error = 'Invalid XSL for node ' . $node->GetDescription() . ': ' . $error;
                    else
                        $error = 'Invalid XSL to set content operation: ' . $error;
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
        if ($res)
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
                if ($section->GetNodeType() == \Ximdex\NodeTypes\NodeTypeConstants::PROJECT)
                {
                    $docxapId = $templates->GetChildByName('docxap.xsl');
                    if (!$docxapId)
                        $this->messages->add('A docxap.xsl template file must be in the project templates folder', MSG_TYPE_WARNING);
                }
            }
            if ($node->GetNodeName() != 'templates_include.xsl' and !self::isIncludedInTemplates($node->GetNodeName(), $node))
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
        
        $xsldom = new \DOMDocument();
        $xsldom->formatOutput = true;
        $xsldom->preserveWhiteSpace = false;
        if (@$xsldom->loadXML($content) === false)
            return $content;
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
        $xslSourcePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/docxap.xsl';

        //generation of the file docxap.xsl with project name inside
        $content=<<<DOCXAP
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


		if (!FsUtils::file_put_contents($xslSourcePath, $content))
		    return false;
		
		//obtain the ID for XSL templates node type
		$nodeTypeID =\Ximdex\NodeTypes\NodeTypeConstants::XSL_TEMPLATE;
		
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
    
    /**
     * Move a template node to another include templates
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
        
        // reload the templates include files for this new project
        if ($this->reload_templates_include(new Node($templates->getProject())) === false)
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
        if (stripos($includeContent, '/' . $templateName) !== false)
        {
            //template exists
            return true;
        }
        return false;
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
        $dom = new \DOMDocument();
        if (!@$dom->loadXML($includeContent))
        {
            Logger::error('Can\'t load XML content from docxap node with ID: ' . $includeNode->GetID());
            return false;
        }
        
        //check if there is a template with that name
        $xPath = new \DOMXPath($dom);
        $projectId = $node->GetProject();
        $templateURL = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') . $node->GetRelativePath($projectId);
        $includeTag = $xPath->query("/xsl:stylesheet/xsl:include[@href='$templateURL']");
        if ($includeTag->length)
        {
            //template exists
            return true;
        }
        return false;
    }
    
    /**
     * Include the correspondant includes_template.xsl for the current document; based in DOCFOLDER_TEMPLATESINC dependencie
     * If the $idDocLocalNode parameter is null, the templates to use will be the associated to the project with node given by $idProject parameter
     * @param string $content
     * @param int $idDocLocalNode
     * @param int|null $idProject
     * @param string|null $urlTemplatesInclude
     * @return bool|null
     */
    public static function replace_path_to_local_templatesInclude(& $content, $idDocLocalNode, $idProject = null, & $urlTemplatesInclude = null)
    {
        if ($idDocLocalNode)
        {
            Logger::info('Replacing includes template with document node ' . $idDocLocalNode);
            $node = new Node($idDocLocalNode);
            if (!$node->GetID())
            {
                Logger::error('Cannot replace the local templates include: The node ' . $idDocLocalNode . ' does not exists');
                return false;
            }
            // get the documents folder ID of the document node ID given
            if ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT)
                $documentsFolderId = $node->_getParentByType(NodeTypeConstants::XML_ROOT_FOLDER);
            elseif ($node->GetNodeType() == NodeTypeConstants::METADATA_DOCUMENT)
                $documentsFolderId = $node->_getParentByType(NodeTypeConstants::METADATA_SECTION);
            elseif ($node->GetNodeType() == NodeTypeConstants::XIMLET)
                $documentsFolderId = $node->_getParentByType(NodeTypeConstants::XIMLET_ROOT_FOLDER);
            else
            {
                Logger::error('Cannot replace the local templates include: Node is not of XML document, Ximlet or METADATA type');
                return false;
            }
            if (!$documentsFolderId)
            {
                Logger::error('Cannot replace the local templates include: Container for node ' . $idDocLocalNode . ' not found');
                return false;
            }
            
            // get the templates folder node that references the previous document
            $depsManager = new DepsManager();
            $idTemplatesFolder = $depsManager->getBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $documentsFolderId);
        }
        elseif ($idProject)
        {
            // get the templates folder of the project
            Logger::info('Replacing includes template with project node ' . $idProject);
            $node = new Node($idProject);
            if (!$node->GetID())
            {
                Logger::error('Cannot replace the local templates include: Project node ' . $idProject . ' does not exists');
                return false;
            }
            $idTemplatesFolder = $node->GetChildren(\Ximdex\NodeTypes\NodeTypeConstants::TEMPLATES_ROOT_FOLDER);
        }
        else
        {
            Logger::error('Cannot replace the local templates include: empty node parameters given');
            return false;
        }
        if ($idTemplatesFolder)
            $idTemplatesFolder = $idTemplatesFolder[0];
        else
            $idTemplatesFolder = 0;
        $templatesFolderNode = new Node($idTemplatesFolder);
        if (!$templatesFolderNode->GetID())
        {
            Logger::error('Cannot replace the local templates include: Templates folder not found for document node ' . $idDocLocalNode);
            return false;
        }
        
        // assing the templates_include in the docxap content
        $PATH_TEMPLATE_INCLUDE = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') 
                . $templatesFolderNode->GetRelativePath($node->getProject());
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
        if ($init)
        {
            // Only project, servers and section/subsections can storage template folders
            if ($node->GetNodeType() !=\Ximdex\NodeTypes\NodeTypeConstants::PROJECTS 
                    and $node->GetNodeType() !=\Ximdex\NodeTypes\NodeTypeConstants::PROJECT
                    and $node->GetNodeType() !=\Ximdex\NodeTypes\NodeTypeConstants::SERVER 
                    and $node->GetNodeType() !=\Ximdex\NodeTypes\NodeTypeConstants::SECTION)
            {
                $this->messages->add('Cannot reload nodes with a node type diferent than project, server or section', MSG_TYPE_ERROR);
                return false;
            }
        }
        
        // Look for templates folder
        $templateFolderId = $node->GetChildren(\Ximdex\NodeTypes\NodeTypeConstants::TEMPLATES_ROOT_FOLDER);
        if ($templateFolderId)
        {
            $templateFolder = new Node($templateFolderId[0]);
            
            // Look for templates_include
            $templatesIncludeId = $templateFolder->GetChildByName('templates_include.xsl');
            if ($templatesIncludeId)
            {
                if (!$projectId)
                {
                    // Get the project node ID
                    $projectId = $node->getProject();
                }
                
                // Generate the basic XML header
                $content = '<?xml version="1.0" encoding="UTF-8"?>';
                $content .= "\n" . '<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">';
                
                // Add the local templates
                $templates = $templateFolder->GetChildren();
                foreach ($templates as $idTemplate)
                {
                    $template = new Node($idTemplate);
                    if ($template->GetNodeName() == 'templates_include.xsl' or $template->GetNodeName() == 'docxap.xsl')
                        continue;
                        
                    // Generate the template URL
                    $templateURL = App::getValue('UrlHost') . App::getValue('UrlRoot') . App::getValue('NodeRoot') 
                            . $template->GetRelativePath($projectId);
                    
                    // Save the template and remove a possible ocurrence with the same name (local one is always priority)
                    $priorTemplates[$template->GetNodeName()] = $templateURL;
                }
                
                // Include the prior templates
                foreach ($priorTemplates as $templateURL)
                    $content .= "\n\t" . '<xsl:include href="' . $templateURL . '"/>';
                    
                // Close the XSL content
                $content .= "\n" . '</xsl:stylesheet>';
                
                // Save the XSL content into the templates_include.xsl node
                $templatesInclude = new Node($templatesIncludeId);
                if ($templatesInclude->SetContent($content) === false)
                {
                    $this->messages->mergeMessages($templatesInclude->messages);
                    return false;
                }
            }
        }
        
        // Get children of the node with its node types
        $nodes = FastTraverse::get_children($node->GetID(), ['node' => ['IdNodeType']], 1);
        if ($nodes === false)
        {
            $this->messages->add('Cannot get children nodes from node: ' . $node->GetID() . ' in reload templates include files process', MSG_TYPE_ERROR);
            return false;
        }
        if (!$nodes) {
            return true;
        }
        foreach ($nodes[1] as $idChildNode => $nodeData)
        {
            // Only project, servers and section/subsections can storage template folders
            $idNodeType = $nodeData['node']['IdNodeType'];
            if ($idNodeType ==\Ximdex\NodeTypes\NodeTypeConstants::PROJECT or $idNodeType ==\Ximdex\NodeTypes\NodeTypeConstants::SERVER
                    or $idNodeType ==\Ximdex\NodeTypes\NodeTypeConstants::SECTION)
            {
                // Call in recursive mode with the child node
                $childNode = new Node($idChildNode);
                if (!$childNode->GetID())
                {
                    Logger::error('Cannot load a node with ID: ' . $childNode->GetID() . ' in reload templates includes process');
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