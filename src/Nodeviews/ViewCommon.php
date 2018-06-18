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

namespace Ximdex\Nodeviews;

use Codeception\Step\Meta;
use Ximdex\Logger;
use Ximdex\Deps\LinksManager;
use Ximdex\Models\Channel;
use Ximdex\Models\Metadata;
use Ximdex\Models\Node;
use Ximdex\Models\RelSemanticTagsNodes;
use Ximdex\Models\Section;
use Ximdex\Models\SectionType;
use Ximdex\Models\Server;
use Ximdex\Models\Version;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\SimpleXMLExtended;

class ViewCommon extends AbstractView implements IView
{
    const DOCXIF = 'docxif';

    private $_filePath;

    function transform($idVersion = NULL, $pointer = NULL, $args = NULL)
    {
        if (!$this->_setFilePath($idVersion, $args))
            return NULL;

        if (!is_file($this->_filePath)) {
            Logger::error('VIEW COMMON: Se ha solicitado cargar un archivo inexistente. FilePath: ' . $this->_filePath);
            return NULL;
        }


        if (!(isset($args['CHANNEL']) and $args['CHANNEL'] && isset($args['NODEID'])) && !array_key_exists('REPLACEMACROS', $args)) {
            return $pointer;
        }

        // Replaces macros in content
        $content = $this->retrieveContent($this->_filePath);

        $linksManager = new LinksManager();
        $content = $linksManager->removeDotDot($content);
        $content = $linksManager->removePathTo($content);

        // Channel
        if (isset($args['CHANNEL']) and $args['CHANNEL'] && isset($args['NODEID'])) {
            $channel = new Channel($args['CHANNEL']);
            $server = new Server($args['SERVER']);
            if (!$channel->GetID()) {
                Logger::error('Channel not found for ID: ' . $args['CHANNEL']);
                return false;
            }
            if ($channel->getRenderType() && $channel->getRenderType() == Channel::RENDERTYPE_INDEX) {
                $node = new Node($args['NODEID']);
                if (strcmp(FsUtils::get_extension($node->GetNodeName()), 'pdf') == 0) {
                    $content = $this->createXIF($node, $content, $channel, $server);
                } else {
                    return false;
                }
            }
        }

        return $this->storeTmpContent($content);
    }

    private function _setFilePath($idVersion = NULL, $args = array())
    {
        if (!is_null($idVersion)) {
            $version = new Version($idVersion);
            $file = $version->get('File');
            $this->_filePath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $file;
        } else {
            // Retrieves Params:
            if (array_key_exists('FILEPATH', $args)) {
                $this->_filePath = $args['FILEPATH'];
            }
            // Check Params:
            if (!isset($this->_filePath) || $this->_filePath == "") {
                Logger::error('VIEW COMMON: No se ha especificado la version ni el path del fichero correspondiente al nodo ' . $args['NODENAME'] . ' que quiere renderizar');
                return NULL;
            }
        }

        return true;
    }


    /**
     * Create XIF format from HTML DOCUMENT NODE
     *
     * @param $nodeID
     * @param $content
     * @param $channel
     * @return mixed
     */
    private static function createXIF(Node $node, $content, Channel $channel, Server $server)
    {
        $sectionId = $node->GetSection();
        $ximID = App::getValue('ximid');
        $version = $node->GetLastVersion() ?? [];
        $section = new Section($sectionId);
        $sectionNode = new Node($section->getIdNode());
        $sectionType = new SectionType($section->getIdSectionType());
        $server->get('Url');
        $info = static::getInfo($node);

        // Create XML
        $xml = new SimpleXMLExtended('<' . static::DOCXIF . '></' . static::DOCXIF . '>');
        $xml->addChild('id', implode(":", [$ximID, $node->GetID()]));
        $xml->addChild('name', $node->GetNodeName());
        $xml->addChild('file_version', $version["Version"] ?? '');
        $xml->addChild('id_ximdex', $ximID);
        $xml->addChild('filename', $node->GetNodeName());
        $xml->addChild('slug', static::getAbsolutePath($node, $server, $channel->GetId()));
        $xml->addChild('creation_date', date('Y-m-d H:i:s', $node->get('CreationDate')));
        $xml->addChild('update_date', date('Y-m-d H:i:s', $node->get('ModificationDate')));
        $xml->addChild('section', $sectionNode->GetNodeName());
        $xml->addChild('id_section', $sectionNode->GetID());
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
        $tags = static::getTags($node->GetID());
        $info['tags'] = $tags;

        // Get metadata
        $metadata = Metadata::getByNodeAndGroup($node->GetID()) ?? []; //TODO Select group
        $info['metadata'] = $metadata;

        return $info;
    }

    private static function getTags($nodeId)
    {
        $relSemanticTagsNodes = new RelSemanticTagsNodes();
        return $relSemanticTagsNodes->getTags($nodeId) ?? [];
    }

    /**
     * @param $targetNode
     * @param $targetServer
     * @param $idTargetChannel
     * @param bool $include
     * @return string
     */
    private static function getAbsolutePath($targetNode, $targetServer, $idTargetChannel)
    {
        return $targetServer->get('Url') . $targetNode->GetPublishedPath($idTargetChannel, true);
    }

}