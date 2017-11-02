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


use Monolog\Handler\StreamHandler;
use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Version;
use Ximdex\Runtime\App;

ModulesManager::file('/xslt/functions.php', 'dexT');
ModulesManager::file('/inc/repository/nodeviews/Abstract_View.class.php');
ModulesManager::file('/inc/repository/nodeviews/Interface_View.class.php');
ModulesManager::file('/inc/nodetypes/xsltnode.php');

class View_Xslt extends Abstract_View
{
    private $_node;
    private $_idSection;
    private $_idChannel;
    private $_idProject;
    
    public function transform($idVersion = NULL, $pointer = NULL, $args = NULL)
    {
        $content = $this->retrieveContent($pointer);
        if (!$this->_setNode($idVersion))
            return NULL;

        if (!$this->_setIdChannel($args))
            return NULL;

        if (!$this->_setIdSection($args))
            return NULL;

        if (!$this->_setIdProject($args))
            return NULL;

        $ptdFolder = App::getValue("TemplatesDirName");
        
        // get always the project docxap file
        $projectId = $this->_idProject;
        $project = new Node($projectId);
        $docxap = $project->class->GetNodePath() . '/' . $ptdFolder . '/docxap.xsl';

        // Only make transformation if channel's render mode is ximdex (or null)

        if ($this->_idChannel) {
            $channel = new Channel($this->_idChannel);
            $renderMode = $channel->get('RenderMode');

            if ($renderMode == 'client') {
                $inclusionHeader = '<?xml-stylesheet type="text/xsl" href="' . $ptdFolder . '/docxap.xsl"?>';
                $xmlHeader = App::getValue('EncodingTag');
                $content = str_replace($xmlHeader, $xmlHeader . $inclusionHeader, $content);

                Logger::info('Render in client, return XML content + path to template');
                return $content;
            }
        }

        // XSLT Transformation
        
        //TODO change the global variable to a function parameter
        if (!isset($GLOBALS['errorsInXslTransformation']))
            $GLOBALS['errorsInXslTransformation'] = array();
        
        Logger::info('Starting xslt transformation');
        if ($this->_node and $this->_node->GetID())
            Logger::info('Processing XML document with ID: ' . $this->_node->GetID() . ' and name: ' . $this->_node->GetNodeName());
        
        $xsltHandler = new \Ximdex\XML\XSLT();
        if (!$xsltHandler->setXML($pointer))
        {
            $error = 'The XML document has syntax errors (' . \Ximdex\Error::error_message('DOMDocument::load(): ') . ')';
            $GLOBALS['errorsInXslTransformation'][] = $error;
        }
        
        Logger::info('Loading XSL content from ' . $docxap);
        
        // load the docxap content
        $domDoc = new DOMDocument();
        if (@$domDoc->load($docxap) === false)
            $GLOBALS['errorsInXslTransformation'][] = 'Invalid docxap.xsl file (' . \Ximdex\Error::error_message('DOMDocument::load(): ') . ')';
        $docxapContent = $domDoc->saveXML();
        
        // include the correspondant includes_template.xsl for the current document
        if ($this->_node and $this->_node->GetID())
            $idNode = $this->_node->GetID();
        else
            $idNode = null;
        $urlTemplatesInclude = null;
        if (!xsltnode::replace_path_to_local_templatesInclude($docxapContent, $idNode, $projectId, $urlTemplatesInclude))
        {
            $error = 'Cannot load the XSL file ' . $urlTemplatesInclude . ' for XML document ';
            if ($idNode)
                $error .= 'with ID: ' . $idNode;
            else
                $error .= 'with project ID: ' . $projectId;
            $GLOBALS['errorsInXslTransformation'][] = $error;
        }
        
        $xsltHandler->setXSL(null, $docxapContent);
        
        $params = array('xmlcontent' => $content);
        
        if (App::debug())
        {
            # DEBUG
            @file_put_contents("/tmp/docxap-pre.xsl", $docxap);
            @file_put_contents("/tmp/docxap-post.xsl", $docxapContent);
            @file_put_contents("/tmp/pointer.xml", $pointer);
            @file_put_contents("/tmp/content-pre.xml", $content);
            # END DEBUG
        }
        
        foreach ($params as $param => $value) {
            $xsltHandler->setParameter(array($param => $value));
        }

        $content = $xsltHandler->process();
        
        if (App::debug())
        {
            # DEBUG
            @file_put_contents("/tmp/content-post.xml", $content);
            # END DEBUG
        }
        
        if ($content === false)
        {
            // try to reload templates includes in order to try to solve the problem, in case of empty templates_include.xsl
            Logger::info('Checking if the local templates_include.xsl is empty to reload its content');
            $dom = new DOMDocument();
            if (!$dom->load($urlTemplatesInclude))
                $GLOBALS['errorsInXslTransformation'][] = 'Cannot load the includes template URL: ' . $urlTemplatesInclude;
            else
            {
                $xPath = new DOMXPath($dom);
                $templates = $xPath->query('/xsl:stylesheet/xsl:include');
                if (!$templates->length)
                {
                    Logger::info('XSL templates file: ' . $urlTemplatesInclude 
                            . ' is empty; trying to reload the templates files in the project ID: ' . $projectId);
                    $xsltNode = new xsltnode($project);
                    if ($xsltNode->reload_templates_include($project) === false)
                    {
                        foreach ($xsltNode->messages as $message)
                            $GLOBALS['errorsInXslTransformation'][] = $message;
                    }
                    $xsltHandler->setXSL(null, $docxapContent);
                    $content = $xsltHandler->process();
                    
                    if (App::debug())
                    {
                        # DEBUG
                        @file_put_contents("/tmp/content-post.xml", $content);
                        # END DEBUG
                    }
                }
            }
        }
        
        // creates the XSLT log if there is not one yet
        $log = new Monolog\Logger('XSLT');
        $log->pushHandler(new StreamHandler(App::getValue('XIMDEX_ROOT_PATH') . '/logs/xslt.log', Monolog\Logger::DEBUG));
        Logger::addLog($log, 'xslt');
        
        if (empty($content)) {
            
            $error = 'Error in XSL transformation process (' . \Ximdex\Error::error_message('XSLTProcessor::transformToXml(): ') . ')';
            $GLOBALS['errorsInXslTransformation'][] = $error;
            // activation of the XSLT log and init the XSLT errors array
            $defaultLog = Logger::get_active_instance();
            Logger::setActiveLog('xslt');
            foreach ($GLOBALS['errorsInXslTransformation'] as $error)
                Logger::error($error);
            // we save the error trace into the previous file
            $this->set_xslt_errors($GLOBALS['errorsInXslTransformation']);
            Logger::setActiveLog($defaultLog);
            if (isset($GLOBALS['InBatchProcess']))
                return NULL;
            return false;
        }

        // Tags counter

        $counter = 1;
        
        $domDoc->validateOnParse = true;

        if ($channel->get("OutputType") == "xml") {
            if (!@$domDoc->loadXML($content)) {
                
                $defaultLog = Logger::get_active_instance();
                Logger::setActiveLog('xslt');
                Logger::error('XML invalid: ' . $content);
                Logger::setActiveLog($defaultLog);
                $GLOBALS['errorsInXslTransformation'][] = 'Invalid XML source: ' . $content;
                // we save the error trace into the previous file
                $this->set_xslt_errors($GLOBALS['errorsInXslTransformation']);
                return false;
            }
        } else if ($channel->get("OutputType") == "web") {
            if (!@$domDoc->loadHTML($content)) {
                
                $defaultLog = Logger::get_active_instance();
                Logger::setActiveLog('xslt');
                Logger::error('HTML invalid: ' . $content);
                Logger::setActiveLog($defaultLog);
                $GLOBALS['errorsInXslTransformation'][] = 'Invalid HTML or XHTML source: ' . $content;
                // we save the error trace into the previous file
                $this->set_xslt_errors($GLOBALS['errorsInXslTransformation']);
                return false;
            }
        } else {
            return $this->storeTmpContent($content);
        }
        $xpath = new DOMXPath($domDoc);

        $nodeList = $xpath->query('/html/body//*[string(text())]');

        // In non-node transform we've not got a nodeid, and it's not necessary for tag counting.
        foreach ($nodeList as $element) {
            $element->setAttributeNode(new DOMAttr('uid', (($this->_node) ? $this->_node->get('IdNode') : '00000') . ".$counter"));
            $counter++;
        }

        if ($channel->get("OutputType") == "xml")
            $content = $domDoc->saveXML();
        else if ($channel->get("OutputType") == "web")
            $content = $domDoc->saveHTML();
        
        // the document has been processed propertly, so if there is any previous errors they will be deleted
        $this->reset_xslt_errors();

        return $this->storeTmpContent($content);
    }
    
    /**
     * Generate a string with the XSLT errors, to will save in the post transformation information of the current structured document node
     * @param array $errors
     * @return NULL|boolean
     */
    private function set_xslt_errors(array $errors)
    {
        if (!$this->_node or !$this->_node->GetID())
            return null;
        $content = '';
        foreach ($errors as $error)
            $content .= $error . "\n\n";
        $stDoc = new StructuredDocument($this->_node->GetID());
        if (!$stDoc->GetID())
            return false;
        if ($stDoc->SetXsltErrors($content) === false)
            return false;
        return true;
    }
    
    /**
     * Set to null the value XSLT Errors in the current sctructured document
     * @return NULL|boolean
     */
    private function reset_xslt_errors()
    {
        if (!$this->_node or !$this->_node->GetID())
            return null;
        $stDoc = new StructuredDocument($this->_node->GetID());
        if (!$stDoc->GetID())
            return false;
        if ($stDoc->GetXsltErrors())
            if ($stDoc->SetXsltErrors(null) === false)
                return false;
        return true;
    }

    private function _setNode($idVersion = NULL)
    {

        if (!is_null($idVersion)) {
            $version = new Version($idVersion);
            if (!($version->get('IdVersion') > 0)) {
                Logger::error('VIEW XSLT: Incorrect version has been loaded (' . $idVersion . ')');
                return NULL;
            }

            $this->_node = new Node($version->get('IdNode'));
            if (!($this->_node->get('IdNode') > 0)) {
                Logger::error('VIEW XSLT: The node that it\'s trying to convert doesn\'t exists: ' . $version->get('IdNode'));
                return NULL;
            }
        } else {
            Logger::info("VIEW XSLT: xslt view is instantiate without 'idVersion'");
        }

        return true;
    }

    private function _setIdChannel($args = array())
    {

        if (array_key_exists('CHANNEL', $args)) {
            $this->_idChannel = $args['CHANNEL'];
        }

        // Check Params:
        if (!isset($this->_idChannel) || !($this->_idChannel > 0)) {
            Logger::error('VIEW XSLT: Node ' . $args['NODENAME'] . ' has not an associated channel');
            return NULL;
        }

        return true;
    }

    private function _setIdSection($args = array())
    {

        if ($this->_node) {
            $this->_idSection = $this->_node->GetSection();
        } else {
            if (array_key_exists('SECTION', $args)) {
                $this->_idSection = $args['SECTION'];
            }

            // Check Params:
            if (!isset($this->_idSection) || !($this->_idSection > 0)) {
                Logger::error('VIEW XSLT: There is not associated section for the node ' . $args['NODENAME']);
                return NULL;
            }
        }

        return true;
    }

    private function _setIdProject($args = array())
    {

        if ($this->_node) {
            $this->_idProject = $this->_node->GetProject();
        } else {
            if (array_key_exists('PROJECT', $args)) {
                $this->_idProject = $args['PROJECT'];
            }

            // Check Params:
            if (!isset($this->_idProject) || !($this->_idProject > 0)) {
                Logger::error('VIEW XSLT: There is not associated project for the node ' . $args['NODENAME']);
                return NULL;
            }
        }

        return true;
    }

}