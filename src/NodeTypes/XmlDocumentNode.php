<?php

namespace Ximdex\NodeTypes;

use Ximdex\Models\Channel;
use Ximdex\Models\StructuredDocument;
use Ximdex\Nodeviews\ViewFilterMacros;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\PipelineManager;
use Ximdex\Models\Node;
use DOMDocument;

/**
 * \details &copy; 2011 Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 * Ximdex a Semantic Content Management System (CMS)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License
 * version 3 along with Ximdex (see LICENSE file).
 *
 * If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

class XmlDocumentNode extends AbstractStructuredDocument
{
    /**
     * Deletes docxap tag
     * @param string $xmldoc
     * @return string
     */
    public static function normalizeXmlDocument(string $xmldoc) : string
    {
        $xmldoc = \Ximdex\Utils\Strings::stripslashes($xmldoc);
        $doc = new DOMDocument();
        $doc->formatOutput = true;
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xmldoc);
        $doc->encoding = 'UTF-8';
        $docxap = $doc->getElementsByTagName('docxap');
        if (! $docxap) {
            return $xmldoc;
        }
        $docxap = $docxap->item(0);
        $childrens = $docxap->childNodes;
        $l = $childrens->length;
        $xmldoc = '';
        for ($i = 0; $i < $l; $i ++) {
            $child = $childrens->item($i);
            if ($child->nodeType == 1) {
                $xmldoc .= $doc->saveXML($child) . "";
            }
        }
        return $xmldoc;
    }
    
    /**
     * Render a node to the web output with response headers
     * 
     * @param int $idNode
     * @return boolean
     * //TODO ajlucena!
     */
    public function filemapper(int $idNode, string $idChannel = null, string $showprev = null, string & $content = null
        , string $version = null, string $subversion = null, string $mode = null) : bool
    {
        // Checks node existence
        $node = new Node($idNode);
        if (! ($node->get('IdNode') > 0)) {
            $this->messages->add(_('It is not possible to show preview.') . _(' The node you are trying to preview does not exist.'), MSG_TYPE_NOTICE);
            return false;
        }
        
        // If the node is a structured document, render the preview, else return the file content
        if ($node->nodeType->GetIsStructuredDocument()) {
            
            // Checks if node is a structured document
            $structuredDocument = new StructuredDocument($idNode);
            if (! ($structuredDocument->get('IdDoc') > 0)) {
                $this->messages->add(_('It is not possible to show preview.') . _(' Provided node is not a structured document.'), MSG_TYPE_NOTICE);
                return false;
            }
            
            // Checks content existence
            if (! $content) {
                $content = $structuredDocument->GetContent($version, $subversion);
            } elseif ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                $content = XmlDocumentNode::normalizeXmlDocument($content);
            }
            
            // Validates channel
            if (! is_numeric($idChannel)) {
                $channels = $node->getChannels();
                $firstChannel = null;
                $idChannel = NULL;
                if (! empty($channels)) {
                    foreach ($channels as $c) {
                        $c = new Channel($c);
                        $cName = $c->getName();
                        $ic = $c->get('IdChannel');
                        if ($firstChannel === null) {
                            $firstChannel = $ic;
                        }
                        if (strToUpper($cName) == 'HTML') {
                            $idChannel = $ic;
                        }
                        unset($c);
                    }
                }
                if ($idChannel === null) {
                    $idChannel = $firstChannel;
                }
                if ($idChannel === null) {
                    $this->messages->add(_('It is not possible to show preview. There is not any defined channel.'), MSG_TYPE_NOTICE);
                    return false;
                }
            }
            
            // Populates variables and view/pipelines args
            $idSection = $node->GetSection();
            $idProject = $node->GetProject();
            $idServerNode = $node->getServer();
            $documentType = $structuredDocument->getDocumentType();
            $idLanguage = $structuredDocument->getLanguage();
            if ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT and method_exists($node->class, "_getDocXapHeader")) {
                
                $docXapHeader = $node->class->_getDocXapHeader($idChannel, $idLanguage, $documentType);
            } else {
                $docXapHeader = null;
            }
            $nodeName = $node->get('Name');
            $depth = $node->GetPublishedDepth();
            
            // Initializes variables:
            $args = array();
            $args['MODE'] = $mode == 'dinamic' ? 'dinamic' : 'static';
            $args['CHANNEL'] = $idChannel;
            $args['SECTION'] = $idSection;
            $args['PROJECT'] = $idProject;
            $args['SERVERNODE'] = $idServerNode;
            $args['LANGUAGE'] = $idLanguage;
            if ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                $args['DOCXAPHEADER'] = $docXapHeader;
            }
            $args['NODENAME'] = $nodeName;
            $args['DEPTH'] = $depth;
            $args['DISABLE_CACHE'] = true;
            $args['CONTENT'] = $content;
            $args['NODETYPENAME'] = $node->nodeType->get('Name');
            if ($idNode < 10000) {
                $idNode = 10000;
                $node = new Node($idNode);
            }
            $transformer = $node->getProperty('Transformer');
            $args['TRANSFORMER'] = $transformer[0];
            if ($node->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                $process = 'HTMLToPrepared';
            } else {
                $process = 'StrDocToDexT';
            }
            $pipelineManager = new PipelineManager();
            $content = $pipelineManager->getCacheFromProcess(NULL, $process, $args);
            if ($content === false) {
                
                // The transformation process did not work !
                if ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                    
                    // If content is false, show the xslt errors instead the document preview
                    $stDoc = new StructuredDocument($idNode);
                    $errors = $stDoc->GetXsltErrors();
                    if ($errors) {
                        $errors = str_replace("\n", "\n<br />\n", $errors);
                    }
                }
                if (!isset($errors)) {
                    $errors = 'The preview cannot be processed due to an unknown error';
                }
                //TODO ajlucena!
                /*
                $this->addCss('/assets/style/jquery/ximdex_theme/widgets/browserwindow/actionPanel.css');
                $this->render(array('errors' => $errors), 'index', 'basic_html.tpl');
                */
                $this->messages->add($errors, MSG_TYPE_WARNING);
                return false;
            }
            
            // Specific FilterMacros View for previsuals
            $viewFilterMacrosPreview = new ViewFilterMacros(true);
            $file = $viewFilterMacrosPreview->transform(NULL, $content, $args, $idNode, $idChannel);
            if ($file === false) {
                $this->messages->add('Cannot transform the document ' . $node->GetNodeName() . ' for a preview operation', MSG_TYPE_WARNING);
                return false;
            }
            $content = FsUtils::file_get_contents($file);
            if ($content === false) {
                return false;
            }
        }
        else {
            
            // Node is not a structured document
            $content = $node->GetContent();
            if ($content === false) {
                $this->messages->add('Cannot get the content from file ' . $node->GetNodeName() . ' for a preview operation', MSG_TYPE_WARNING);
                return false;
            }
        }
        return true;
    }
}