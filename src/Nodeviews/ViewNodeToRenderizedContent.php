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

namespace Ximdex\Nodeviews;

use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Version;
use Ximdex\NodeTypes\XmlDocumentNode;
use Ximdex\Runtime\App;
use Ximdex\Logger;

class ViewNodeToRenderizedContent extends AbstractView
{
    private $_node = null;
    private $_structuredDocument = null;
    private $_idChannel = null;
    private $_idLanguage = null;
    private $_idSection = null;
    private $_linkedXimlets = "";
    private $_content = "";

    /**
     * {@inheritDoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
        if (! $this->_setNode($idVersion)) {
            return false;
        }
        if (! $this->_setStructuredDocument($idVersion)) {
            return false;
        }
        if (! $this->_setIdChannel($args)) {
            return false;
        }
        if (! $this->_setIdLanguage($args)) {
            return false;
        }
        if (! $this->_setDocxapHeader($args)) {
            return false;
        }
        if (! $this->_setIdSection($args)) {
            return false;
        }
        if (! $this->_setLinkedXimlets($args)) {
            return false;
        }
        if (! $this->_setContent($content)) {
            return false;
        }
        $doctypeTag = App::getValue("DoctypeTag");
        $encodingTag = App::getValue("EncodingTag");

        // If the docxap tag is given in the XML document, is necessary to remove to don generate problems with the new docxap header tag
        $this->_content = str_ireplace('<docxap>', '', $this->_content);
        $this->_content = str_ireplace('</docxap>', '', $this->_content);
        $transformedContent = $encodingTag . "\n";
        $transformedContent .= $doctypeTag . "\n\n";
        $transformedContent .= $this->_docXapHeader;
        if ($this->_linkedXimlets != "") {
            $transformedContent .= $this->_linkedXimlets . "\n";
        }
        $transformedContent .= $this->_content . "\n";
        $transformedContent .= "</docxap>\n";
        return $transformedContent;
    }

    private function _setNode(int $idVersion = null) : bool
    {
        if (! is_null($idVersion)) {
            $version = new Version($idVersion);
            if (! $version->get('IdVersion')) {
                Logger::error('VIEW NODETORENDERIZEDCONTENT: Incorrect version has been loaded (' . $idVersion . ')');
                return false;
            }
            $this->_node = new Node($version->get('IdNode'));
            if (! $this->_node->get('IdNode')) {
                Logger::error('VIEW NODETORENDERIZEDCONTENT: The node you are trying to convert does not exists: ' . $version->get('IdNode'));
                return false;
            }
        }
        return true;
    }

    private function _setContent(string $content = null) : bool
    {
        if ($this->_structuredDocument && empty($content)) {
            
            // Return BD content if no content param
            $this->_content = $this->_structuredDocument->GetContent();
        } else {
            $this->_content = $content;
        }
        return true;
    }

    private function _setLinkedXimlets(array $args = array()) : bool
    {
        if ($this->_node) {       
            if (array_key_exists('CALLER', $args) && $args['CALLER'] == 'xEDIT') {
                $nodeTypeName = $this->_node->nodeType->GetName();
                if ($nodeTypeName == 'Ximlet') {
                    return true;
                }
            }
            $this->_linkedXimlets = $this->_node->class->InsertLinkedximletS($this->_idLanguage);
        } else {
            $xmlDocumentNode = new XmlDocumentNode();
            $this->_linkedXimlets = $xmlDocumentNode->InsertLinkedximletS($this->_idLanguage, $this->_idSection);
        }
        return true;
    }

    private function _setStructuredDocument(int $idVersion = null) : bool
    {
        if (! is_null($idVersion)) {
            $version = new Version($idVersion);
            if (! $version->get('IdVersion')) {
                Logger::error('VIEW NODETORENDERIZEDCONTENT: Incorrect version has been loaded (' . $idVersion . ')');
                return false;
            }
            $this->_structuredDocument = new StructuredDocument($version->get('IdNode'));
            if (! $this->_structuredDocument->get('IdDoc')) {
                Logger::error('VIEW NODETORENDERIZEDCONTENT: The specified structured document does not exists: ' 
                    . $this->_structuredDocument->get('IdDoc'));
                return false;
            }
        }
        return true;
    }

    private function _setIdChannel(array $args = array()) : bool
    {
        if (array_key_exists('CHANNEL', $args)) {
            $idChannel = $args['CHANNEL'];
        }

        // Check Params
        if (! isset($idChannel) || ! $idChannel) {
            Logger::error('VIEW NODETORENDERIZEDCONTENT: Channel not specified for node ' . $args['NODENAME']);
            return false;
        }
        $channel = new Channel($idChannel);
        $this->_idChannel = $channel->get('IdChannel');
        return true;
    }

    private function _setIdSection(array $args = array()) : bool
    {
        if (! $this->_node) {
            if (array_key_exists('SECTION', $args)) {
                $this->_idSection = $args['SECTION'];
            }

            // Check Params
            if (! $this->_idSection) {
                Logger::error('VIEW NODETORENDERIZEDCONTENT: Node section has not been specified ' . $args['NODENAME'] 
                    . ' that you want to renderize');
                return false;
            }
        }
        return true;
    }

    private function _setIdLanguage(array $args = array()) : bool
    {
        if ($this->_node && $this->_structuredDocument) {
            $this->_idLanguage = $this->_structuredDocument->getLanguage();
        }
        if (array_key_exists('LANGUAGE', $args)) {
            $this->_idLanguage = $args['LANGUAGE'];
        }

        // Check Params
        if (! $this->_idLanguage) {
            Logger::error("VIEW NODETORENDERIZEDCONTENT: Node's language not specified " . $args['NODENAME'] . " that you want to renderize");
            return false;
        }
        return true;
    }

    private function _setDocxapHeader(array $args = array()) : bool
    {
        if ($this->_node && $this->_structuredDocument) {
            $documentType = $this->_structuredDocument->GetDocumentType();
            $this->_docXapHeader = $this->_node->class->getDocHeader($this->_idChannel, $this->_idLanguage, $documentType);
        }
        if (array_key_exists('DOCXAPHEADER', $args)) {
            $this->_docXapHeader = $args['DOCXAPHEADER'];
        }

        // Check Params
        if (! isset($this->_docXapHeader) || $this->_docXapHeader == "") {
            Logger::error('VIEW NODETORENDERIZEDCONTENT: docxap header not specified of the node ' . $args['NODENAME'] 
                . ' that you want to renderize');
            return false;
        }
        return true;
    }
}
