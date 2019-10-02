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

use Ximdex\Logger;
use Ximdex\Deps\LinksManager;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\RelSemanticTagsNodes;
use Ximdex\Models\Section;
use Ximdex\Models\SectionType;
use Ximdex\Models\Server;
use Ximdex\Models\Version;
use Ximdex\NodeTypes\CommonNode;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\SimpleXMLExtended;

class ViewCommon extends AbstractView
{
    const DOCXIF = 'docxif';
    
    private $filePath;

    /**
     * {@inheritDoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
        if (! $content) {
            if (! $this->setFilePath($idVersion, $args)) {
                return false;
            }
            if (! is_file($this->filePath)) {
                Logger::error('VIEW COMMON: Se ha solicitado cargar un archivo inexistente. FilePath: ' . $this->filePath);
                return false;
            }
            $content = self::retrieveContent($this->filePath);
        }
        if (parent::transform($idVersion, $content, $args) === false) {
            return false;
        }
        
        // Replaces macros in content
        if (isset($args['REPLACEMACROS'])) {
            $linksManager = new LinksManager();
            $content = $linksManager->removeDotDot($content);
            $content = $linksManager->removePathTo($content);
        }
        
        // Process macros
        if (isset($args['PROCESSMACROS'])) {
            $content = preg_replace_callback(self::MACRO_PATHTO, [$this, 'getLinkPath'], $content);
        }
        
        // Channel
        if (isset($args['CHANNEL']) and $args['CHANNEL'] && isset($args['NODEID'])) {
            $server = new Server($args['SERVER']);
            if (! $this->channel->getID()) {
                Logger::error('Channel not found for ID: ' . $args['CHANNEL']);
                return false;
            }
            if ($this->channel->getRenderType() == Channel::RENDERTYPE_INDEX) {
                if (strcmp(FsUtils::get_extension($this->node->getNodeName()), 'pdf') == 0) {
                    $content = $this->createXIF($this->node, $content, $this->channel, $server);
                } else {
                    return false;
                }
            }
        }
        return $content;
    }

    private function setFilePath(int $idVersion = null, array $args = array())
    {
        if (! is_null($idVersion)) {
            $version = new Version($idVersion);
            $file = $version->get('File');
            $this->filePath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $file;
        } else {
            
            // Retrieves Params
            if (array_key_exists('FILEPATH', $args)) {
                $this->filePath = $args['FILEPATH'];
            }
            
            // Check Params
            if (! isset($this->filePath) || $this->filePath == "") {
                Logger::error('VIEW COMMON: No se ha especificado la version ni el path del fichero correspondiente al nodo ' 
                    . $args['NODENAME'] . ' que quiere renderizar');
                return false;
            }
        }
        return true;
    }

    /**
     * Create XIF format from HTML DOCUMENT NODE
     * 
     * @param Node $node
     * @param string $content
     * @param Channel $channel
     * @param Server $server
     * @return mixed
     */
    private static function createXIF(Node $node, string $content, Channel $channel, Server $server)
    {
        $sectionId = $node->getSection();
        $ximID = App::getValue('ximid');
        $version = $node->getLastVersion() ?? [];
        $section = new Section($sectionId);
        $sectionNode = new Node($section->getIdNode());
        $sectionType = new SectionType($section->getIdSectionType());
        $server->get('Url');
        $info = static::getInfo($node);

        // Create XML
        $xml = new SimpleXMLExtended('<' . static::DOCXIF . '></' . static::DOCXIF . '>');
        $xml->addChild('id', implode(":", [$ximID, $node->getID()]));
        $xml->addChild('name', $node->getNodeName());
        $xml->addChild('file_version', $version["Version"] ?? '');
        $xml->addChild('id_ximdex', $ximID);
        $xml->addChild('filename', $node->getNodeName());
        $xml->addChild('slug', static::getAbsolutePath($node, $server, $channel->GetId()));
        $xml->addChild('creation_date', date('Y-m-d H:i:s', $node->get('CreationDate')));
        $xml->addChild('update_date', date('Y-m-d H:i:s', $node->get('ModificationDate')));
        if ($sectionNode->GetID()) {
            $xml->addChild('section', $sectionNode->GetNodeName());
            $xml->addChild('id_section', $sectionNode->GetID());
        }
        $xml->addChild('state', "publish");
        foreach ($info['tags'] as $tag) {
            $xml->addChild('tag', $tag['Name']);
        }
        $content_payload = $xml->addChild('content-payload');
        foreach ($info['metadata'] as $key => $value) {
            $content_payload->addChild($key, $value);
        }
        $content_payload->addChild('type', $sectionType->get('sectionType'));
        return $xml->asXML();
    }

    private static function getInfo(Node $node)
    {
        $info = [];

        // Get tags
        $tags = static::getTags($node->getID());
        $info['tags'] = $tags;

        // Get metadata
        $metadata = CommonNode::getMetadata($node->getID());
        $info['metadata'] = CommonNode::prepareMetadata($metadata);
        return $info;
    }

    private static function getTags(int $nodeId)
    {
        $relSemanticTagsNodes = new RelSemanticTagsNodes();
        return $relSemanticTagsNodes->getTags($nodeId) ?? [];
    }
}
