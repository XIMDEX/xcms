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

use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;
use Ximdex\Models\Server;
use Ximdex\Models\Version;
use Ximdex\Sync\NodeFrameManager;
use Ximdex\Sync\SynchroFacade;
use Ximdex\Parsers\ParsingPathTo;
use Ximdex\NodeTypes\NodeTypeConstants;

abstract class AbstractView implements IView
{
    const MACRO_PATHTO = "/@@@RMximdex\.pathto\(([,-_#%=\.\w\s]+)\)@@@/";
    
    const MACRO_PATHTOABS = "/@@@RMximdex\.pathtoabs\(([,-_#%=\.\w\s]+)\)@@@/";
    
    protected $node;
    
    protected $channel;
    
    protected $preview;
    
    protected $server;
    
    protected $isPreviewServer;
    
    protected $serverNode;
    
    protected $mode;
    
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
        Logger::info('Transforming with ' . get_class($this));
     
        // Load base node
        if (isset($args['NODEID']) and $args['NODEID']) {
            $id = $args['NODEID'];
        } elseif (! is_null($idVersion)) {
            $version = new Version($idVersion);
            if (! $version->get('IdVersion')) {
                Logger::error('An incorrect version has been loaded (' . $idVersion . ')');
                return false;
            }
            $id = $version->get('IdNode');
        }
        if ($id) {
            $node = new Node($id);
            if (! $node->getID()) {
                Logger::error('Node not found for ID: ' . $args['NODEID']);
                return false;
            }
            $this->node = $node;
        } else {
            $this->node = null;
        }
        
        // Load channel
        if (isset($args['CHANNEL']) and $args['CHANNEL']) {
            $channel = new Channel($args['CHANNEL']);
            if (! $channel->getID()) {
                Logger::error('Channel not found for ID: ' . $args['CHANNEL']);
                return false;
            }
            $this->channel = $channel;
        } else {
            $this->channel = null;
        }
        
        // Load server
        if (array_key_exists('SERVER', $args)) {
            $this->server = new Server($args['SERVER']);
            if (! $this->server->get('IdServer')) {
                Logger::error('Server ' . $args['SERVER'] . ' where you want to render the node not specified');
                return false;
            }
            $this->isPreviewServer = (bool) $this->server->get('Previsual');
        }
        if ($this->node) {
            $this->serverNode = new Node($this->node->getServer());
        } elseif (array_key_exists('SERVERNODE', $args)) {
            $this->serverNode = new Node($args['SERVERNODE']);
        }
        
        // Check Params
        if (! $this->serverNode || ! is_object($this->serverNode)) {
            Logger::error('There is no server linked to the node ' . $args['NODENAME'] . ' you want to render');
            return false;
        }
        
        if (isset($args['PREVIEW']) and $args['PREVIEW'] === true) {
            $this->preview = true;
        } else {
            $this->preview = false;
        }
        
        // Return given content
        return $content;
    }
    
    public static function storeTmpContent(string $content) : ?string
    {
        // Si el contenido es una variable que contiene false ha ocurrido un error
        if ($content !== false)
        {
            $basePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/';
            $pointer = FsUtils::getUniqueFile($basePath);
            if (isset($_GET['nodeid'])) {
                $file = $basePath . 'preview_' . $_GET['nodeid'] . '_' . $pointer;
            } else {
                $file = $basePath . $pointer;
            }
            Logger::debug('Storing temporal file in ' . $file);
            if (FsUtils::file_put_contents($file, $content)) {
                Logger::debug($file . ' has been saved');
                return $file;
            }
        }
        if (isset($file)) {
            Logger::error('An error has happened trying to store the temporal file with content ' . $file);
        } else {
            Logger::error('An error has happened with content to save (previous error)');
        }
        return null;
    }
    
    public static function retrieveContent(string $pointer)
    {
        return FsUtils::file_get_contents($pointer);
    }
    
    public static function get_class_constants()
    {
        $reflect = new \ReflectionClass(static::class);
        return $reflect->getConstants();
    }
    
    protected function getLinkPath(array $matches, bool $forceAbsolute = false)
    {
        // Get parentesis content
        $pathToParams = $matches[1];
        
        // Channel
        $channelId = ($this->channel and $this->channel->getId()) ? $this->channel->getId() : null;
        
        // Link target-node
        $parserPathTo = new ParsingPathTo();
        if (! $parserPathTo->parsePathTo($pathToParams, $this->node->getID(), null, $channelId)) {
            if ($parserPathTo->messages()->messages) {
                foreach ($parserPathTo->messages()->messages as $error) {
                    Logger::warning($error['message']);
                }
            } else {
                Logger::warning('Parse PathTo is not working for: ' . $pathToParams);
            }
            if ($this->preview) {
                return false;
            }
            return $this->getLinkTo404($forceAbsolute);
        }
        if ($parserPathTo->getNode() === null) {
            
            // There is not a node from Ximdex (ex. an external URL)
            return $pathToParams;
        }
        $targetNode = $parserPathTo->getNode();
        $res = [];
        $res['channel'] = $parserPathTo->getChannel();
        $idNode = $targetNode->getID();
        if (! $this->preview and $targetNode->getNodeType() != NodeTypeConstants::LINK) {
            $nodeFrameManager = new NodeFrameManager();
            $nodeFrame = $nodeFrameManager->getNodeFramesInTime($idNode, null, time());
            if (! isset($nodeFrame)) {
                return '';
            }
        }
        if ($this->node && ! $this->node->get('IdNode')) {
            return '';
        }
        
        // Target channel
        if ($res['channel'] or $res['channel'] === null) {
            $idTargetChannel = $res['channel'];
        } elseif ($channelId) {
            $idTargetChannel = $channelId;
        } else {
            $idTargetChannel = null;
        }
        $isStructuredDocument = $targetNode->nodeType->GetIsStructuredDocument();
        if (! $this->preview and $isStructuredDocument) {
            $targetChannelNode = new Channel($idTargetChannel);
            $idTargetChannel = ($targetChannelNode->get('IdChannel') > 0) ? $targetChannelNode->get('IdChannel') : $channelId;
        }
        
        // When external link, return the url
        if ($targetNode->getNodeType() == NodeTypeConstants::LINK) {
            return $targetNode->class->getUrl();
        }
        
        // Generate the path
        if ($this->preview) {
            
            // Generate URL for preview mode
            if ($isStructuredDocument) {
                if ($this->mode == 'dinamic') {
                    return "javascript:parent.loadDivsPreview(" . $idNode . ")";
                } else {
                    $query = App::get('\Ximdex\Utils\QueryManager');
                    // $src = $query->getPage() . $query->buildWith(array('nodeid' => $idNode, 'token' => uniqid()));
                    $src = $query->getPage() . '?expresion=' . (($idNode) ? $idNode : $pathToParams) . '&channelId=' . $idTargetChannel
                        . '&action=rendernode&token=' . uniqid();
                    if ($parserPathTo->getAnchor()) {
                        $src .= '#' . $parserPathTo->getAnchor();
                    }
                    return $src;
                }
            }
            
            // Generate the URL to the rendernode action
            $query = App::get('\Ximdex\Utils\QueryManager');
            $url = $query->getPage() . '?expresion=' . (($idNode) ? $idNode : $pathToParams)
                . '&action=rendernode&token=' . uniqid();
            return $url;
        }
        /*
        if ($this->isPreviewServer) {
            if ($isStructuredDocument) {
                $src = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $targetNode->getPublishedPath($idTargetChannel, true);
                if ($parserPathTo->getAnchor()) {
                    $src .= '#' . $parserPathTo->getAnchor();
                }
                return $src;
            } else {
                return $targetNode->class->getNodeURL();
            }
        }
        */
        if (App::getValue('PullMode') == 1) {
            return App::getValue('UrlRoot') . '/src/Rest/Pull/index.php?idnode=' . $targetNode->get('IdNode')
            . '&idchannel=' . $idTargetChannel . '&idportal=' . $this->serverNode->get('IdNode');
        }
        if ($targetNode->nodeType->getIsSection()) {
            $idTargetServer = $this->server->get('IdServer');
        } else {
            
            // Get the server to publicate the node with the correspondant channel
            $sync = new SynchroFacade();
            $idTargetServer = $sync->getServer($targetNode->get('IdNode'), $idTargetChannel, $this->server->get('IdServer'));
        }
        $targetServer = new Server($idTargetServer);
        if (! $targetServer->get('IdServer')) {
            return $this->getLinkTo404($forceAbsolute);
        }
        
        // Get the relative or absolute path
        if ($forceAbsolute or $targetServer->get('IdServer') != $this->server->get('IdServer') or $this->server->get('OverrideLocalPaths')) {
            $src = $this->getAbsolutePath($targetNode, $targetServer, $idTargetChannel);
            if ($parserPathTo->getAnchor()) {
                $src .= '#' . $parserPathTo->getAnchor();
            }
            return $src;
        }
        $src = $this->getRelativePath($targetNode, $this->server, $idTargetChannel);
        if ($parserPathTo->getAnchor()) {
            $src .= '#' . $parserPathTo->getAnchor();
        }
        return $src;
    }
    
    protected function getLinkPathAbs(array $matches)
    {
        return $this->getLinkPath($matches, true);
    }
    
    /**
     * Get relative path to target node
     *
     * @param Node $targetNode
     * @param Server $targetSever
     * @param int $idTargetChannel
     * @return string
     */
    protected function getRelativePath(Node $targetNode, Server $targetServer = null, int $idTargetChannel = null) : string
    {
        if ($targetServer) {
            $path = FsUtils::get_url_path($targetServer->get('Url'), false);
        } else {
            $path = '';
        }
        return $path . $targetNode->getPublishedPath($idTargetChannel, true);
    }
    
    protected static function getAbsolutePath(Node $targetNode, Server $targetServer, int $idTargetChannel = null) : string
    {
        return $targetServer->get('Url') . $targetNode->getPublishedPath($idTargetChannel, true);
    }
    
    /**
     * Always the 404 document must be in server root folder
     * 
     * @param bool $absolute
     * @return string
     */
    protected function getLinkTo404(bool $absolute = false) : string
    {
        $channelId = ($this->channel and $this->channel->getId()) ? $this->channel->getId() : null;
        $parserPathTo = new ParsingPathTo();
        if ($parserPathTo->parsePathTo('/documents/404', $this->node->getID(), null, $channelId)) {
            $src = $parserPathTo->getNode()->getPublishedPath($channelId, true);
        } else { 
            $src = App::getValue('EmptyHrefCode');
        }
        if ($this->server and ($absolute or $this->server->get('OverrideLocalPaths'))) {
            $src = $this->server->get('Url') . $src;
        }
        Logger::warning("Linking to 404 EmptyHrefCode ({$src})");
        return $src;
    }
}
