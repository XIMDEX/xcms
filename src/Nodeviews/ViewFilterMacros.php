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
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\Sync\NodeFrameManager;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Models\Server;
use Ximdex\Models\Channel;
use Ximdex\Models\Version;
use Ximdex\Utils\Messages;
use Ximdex\Sync\SynchroFacade;
use Ximdex\Models\FastTraverse;
use Ximdex\Models\ServerFrame;
use Ximdex\Parsers\ParsingPathTo;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Utils\FsUtils;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Language;
use Ximdex\Models\IsoCode;

class ViewFilterMacros extends AbstractView
{
    private $_node;
    private $_server;
    private $_serverNode;
    private $_projectNode;
    private $_idChannel;
    private $_isPreviewServer;
    private $_depth;
    private $_idSection;
    private $_nodeName;
    private $_nodeTypeName;
    private $mode;
    private $preview;
    private $messages;
    private $originHasLangPath;
    private $originNodeID;

    const MACRO_SERVERNAME = "/@@@RMximdex\.servername\(\)@@@/";
    const MACRO_PROJECTNAME = "/@@@RMximdex\.projectname\(\)@@@/";
    const MACRO_NODENAME = "/@@@RMximdex\.nodename\(\)@@@/";
    const MACRO_SECTIONPATH = "/@@@RMximdex\.sectionpath\(([0-9]+)\)@@@/";
    const MACRO_SECTIONPATHABS = "/@@@RMximdex\.sectionpathabs\(([0-9]+)\)@@@/";
    const MACRO_DOTDOT = "/@@@RMximdex\.dotdot\(([^\)]*)\)@@@/";
    const MACRO_PATHTO = "/@@@RMximdex\.pathto\(([,-_#%=\.\w\s]+)\)@@@/";
    const MACRO_PATHTOABS = "/@@@RMximdex\.pathtoabs\(([,-_#%=\.\w\s]+)\)@@@/";
    const MACRO_BREADCRUMB = "/@@@RMximdex\.breadcrumb\(([,-_#%=\.\w\s]+)\)@@@/";
    const MACRO_INCLUDE = "/@@@RMximdex\.include\(([^\)]*)\)@@@/";
    const MACRO_METADATA = "/@@@RMximdex\.metadata\(([^,\)]*),([\w\.\-\s]*)\)@@@/";
    const MACRO_NODE_LANG_NAME = "/@@@RMximdex\.langname\(([A-Z]+|)\)@@@/";

    /**
     * Constructor with mode preview choise parameter
     *
     * @param bool $preview
     */
    public function __construct(bool $preview = null)
    {
        $this->preview = (bool) $preview;
        $this->messages = new Messages();
    }

    /**
     * Main method
     * Get a pointer content file and return a new transformed content file
     * This probably cames from Transformer (View_XSLT), so will be the renderized content
     *
     * @param int $idVersion Node version
     * @param string $pointer File name with the content to transform
     * @param array $args Params about the current node
     * @return string file name with the transformed content
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
        // Check the conditions
        if (! $this->initializeParams($args, $idVersion)) {
            return false;
        }

        // Get the content
        return $this->transformFromContent($content);
    }

    /**
     * Initialize params from transformation args
     *
     * @param array $args Arguments for transformation
     * @param int $idVersion
     * @return boolean True if everything is allright
     */
    private function initializeParams(array $args, int $idVersion = null) : bool
    {
        if ($this->preview) {
            $this->mode = (isset($args['MODE']) && $args['MODE'] == 'dinamic') ? 'dinamic' : 'static';
            if (! $this->_setIdSection($args)) {
                return false;
            }
        }
        if (! $this->_setNode($idVersion, $args)) {
            return false;
        }
        if (! $this->_setIdChannel($args)) {
            return false;
        }
        if (! $this->_setServer($args)) {
            return false;
        }
        if ( !$this->_setServerNode($args)) {
            return false;
        }
        if (! $this->_setProjectNode($args)) {
            return false;
        }
        if (! $this->_setDepth($args)) {
            return false;
        }
        if (! $this->_setNodeName($args)) {
            return false;
        }
        return true;
    }

    /**
     * Load the node param from an idVersion
     *
     * @param int $idVersion Version id
     * @param array $args
     * @return boolean True if exists node for selected version or the current node
     */
    private function _setNode(int $idVersion = null, array $args = null) : bool
    {
        if (isset($args['NODEID']) and $args['NODEID']) {
            $this->_node = new Node($args['NODEID']);
            if (! $this->_node->GetID()) {
                Logger::error('VIEW FILTERMACROS: The node you are trying to convert does not exist: ' . $args['NODEID']);
                return false;
            }
        } elseif (! is_null($idVersion)) {
            $version = new Version($idVersion);
            if (! $version->get('IdVersion')) {
                Logger::error('VIEW FILTERMACROS: An incorrect version has been loaded (' . $idVersion . ')');
                return false;
            }
            $this->_node = new Node($version->get('IdNode'));
            if (! $this->_node->GetID()) {
                Logger::error('VIEW FILTERMACROS: The node you are trying to convert does not exist: ' . $version->get('IdNode'));
                return false;
            }
        } elseif ($this->preview and array_key_exists('NODETYPENAME', $args)) {
            $this->_nodeTypeName = $args['NODETYPENAME'];
        }
        return true;
    }

    /**
     * Load channel param from args array
     *
     * @param array $args
     * @return boolean true if exists channel
     */
    private function _setIdChannel(array $args = array()) : bool
    {
        if (array_key_exists('CHANNEL', $args)) {
            $this->_idChannel = $args['CHANNEL'];
        }

        // Check Params
        if (!isset($this->_idChannel) || !$this->_idChannel) {
            $error = 'VIEW FILTERMACROS: Channel not specified for node';
            if (isset($args['NODENAME']) and $args['NODENAME']) {
                $error .= ' ' . $args['NODENAME'];
            }
            if (isset($args['NODEID']) and $args['NODEID']) {
                $error .= ' ' . $args['NODEID'];
            }
            Logger::error($error);
            return false;
        }
        return true;
    }

    /**
     * Load server param from args array
     *
     * @param array $args 
     * @return boolean true if exists the server in args
     */
    private function _setServer(array $args = array()) : bool
    {
        if (array_key_exists('SERVER', $args)) {
            $this->_server = new Server($args['SERVER']);
            if (! $this->_server->get('IdServer')) {
                Logger::error('VIEW FILTERMACROS: Server where you want to render the node not specified ');
                return false;
            }
            $this->_isPreviewServer = $this->_server->get('Previsual');
        }
        return true;
    }

    /**
     * Load the server node for the current node
     *
     * @param array $args Transformation args
     * @return boolean True if exists the server node
     */
    private function _setServerNode(array $args = array()) : bool
    {
        if ($this->_node) {
            $this->_serverNode = new Node($this->_node->getServer());
        } elseif (array_key_exists('SERVERNODE', $args)) {
            $this->_serverNode = new Node($args['SERVERNODE']);
        }

        // Check Params
        if (! $this->_serverNode || !is_object($this->_serverNode)) {
            Logger::error('VIEW FILTERMACROS: There is no server linked to the node ' . $args['NODENAME'] . ' you want to render');
            return false;
        }
        return true;
    }

    /**
     * Load the project node for the current transformed node
     *
     * @param array $args Transformation args
     * @return boolean true if exists the project node
     */
    private function _setProjectNode(array $args = array()) : bool
    {
        if ($this->_node) {
            $this->_projectNode = $this->_node->getProject();
        } elseif (array_key_exists('PROJECT', $args)) {
            $this->_projectNode = $args['PROJECT'];
        }

        // Check Params
        if (! isset($this->_projectNode) || !$this->_projectNode) {
            Logger::error('VIEW FILTERMACROS: There is not associated project for the node ' . $args['NODENAME']);
            return false;
        }
        return true;
    }

    /**
     * Load the depth for the current node
     *
     * @param array $args Transformation args
     * @return boolean true if exits depth form the current node
     */
    private function _setDepth(array $args = array()) : bool
    {
        if ($this->_node) {
            $this->_depth = $this->_node->GetPublishedDepth();
        } elseif (array_key_exists('DEPTH', $args)) {
            $this->_depth = $args['DEPTH'];
        }

        // Check Param
        if (! isset($this->_depth) || !$this->_depth) {
            Logger::error('VIEW FILTERMACROS: No depth has been specified for the node ' . $args['NODENAME'] . ' you want to render');
            return false;
        }
        return true;
    }

    /**
     * Load the nodename from the selected node
     *
     * @param array $args Transformation args
     * @return boolean true if exists name for the current node
     */
    private function _setNodeName(array $args = array()): bool
    {
        if ($this->_node) {
            $this->_nodeName = $this->_node->get('Name');
        } elseif (array_key_exists('NODENAME', $args)) {
            $this->_nodeName = $args['NODENAME'];
        }

        // Check Param
        if (! isset($this->_nodeName) || !$this->_nodeName) {
            Logger::error('VIEW FILTERMACROS: No se ha especificado el nombre del nodo que quiere renderizar');
            return false;
        }
        return true;
    }
    
    /**
     * Load the section id from the args array
     *
     * @param array $args Transformation args
     * @return boolean True if exits the section
     */
    private function _setIdSection(array $args = array()) : bool
    {
        if (array_key_exists('SECTION', $args)) {
            $this->_idSection = $args['SECTION'];
        }
        
        // Check Params
        if (! isset($this->_idSection) || !$this->_idSection) {
            Logger::error('VIEW FILTERMACROSPREVIEW: Node section not specified: ' . $args['NODENAME']);
            return false;
        }
        return true;
    }

    private function transformFromContent(string $content) : string
    {
        /**
         * Available macros:
         * * servername
         * * projectname
         * * nodename
         * * sectionpath
         * * dotdot
         * * pathto
         * * include
         */
        $serverName = $this->_serverNode->get('Name');
        $content = preg_replace(self::MACRO_SERVERNAME, $serverName, $content);
        if (preg_match(self::MACRO_PROJECTNAME, $content)) {
            $project = new Node($this->_projectNode);
            $projectName = $project->get('Name');
            $content = preg_replace(self::MACRO_PROJECTNAME, $projectName, $content);
        }
        $content = preg_replace(self::MACRO_NODENAME, $this->_nodeName, $content);
        $content = preg_replace_callback(self::MACRO_SECTIONPATH, array(
            $this,
            'getSectionPath'
        ), $content);
        $content = preg_replace_callback(self::MACRO_SECTIONPATHABS, array(
            $this,
            'getSectionPathAbs'
        ), $content);
        $content = preg_replace_callback(self::MACRO_DOTDOT, array(
            $this,
            'getdotdotpath'
        ), $content);
        $content = preg_replace_callback(self::MACRO_BREADCRUMB, array(
            $this,
            'getBreadCrumb'
        ), $content);

        // Pathto
        $content = preg_replace_callback(self::MACRO_PATHTO, array(
            $this,
            'getLinkPath'
        ), $content);

        // Pathtoabs
        $content = preg_replace_callback(self::MACRO_PATHTOABS, array(
            $this,
            'getLinkPathAbs'
        ), $content);
        
        // Files include
        $content = preg_replace_callback(self::MACRO_INCLUDE, array($this, 'getInclude'), $content);
        
        // Language name
        $content = preg_replace_callback(self::MACRO_NODE_LANG_NAME, array(
            $this,
            'getLangName'
        ), $content);
        
        // Once macros are resolver, remove uid attribute from tags
        $content = preg_replace_callback("/(<.*?)(uid=\".*?\")(.*?\/?>)/", array(
            $this,
            'removeUIDs'
        ), $content);
        return $content;
    }

    /**
     * Remove the uid attributes generated by the editor
     *
     * @param array $matches Array containing the matches of the regular expression
     * @return string String to be used to replace the matching of the regular expression
     */
    private function removeUIDs(array $matches) : string
    {
        return str_replace(" >", ">", $matches[1] . $matches[3]);
    }

    /**
     * Get the section node of the $idNode
     *
     * @param int $idNode descendant of the searched Section.
     * @return Node The section node or false on error
     */
    private function getSectionNode(int $idNode)
    {
        $node = new Node($idNode);
        if (! $node->get('IdNode')) {
            return false;
        }
        $idSection = $node->GetSection();
        $section = new Node($idSection);
        return $section;
    }

    private function getSectionPathAbs($matches)
    {
        return $this->getSectionPath($matches, true);
    }

    /**
     * Get section path for a selected idnode and channel in matches
     *
     * @param array $matches An idnode and an optional idchannel
     * @param boolean $abs
     * @return string Link url
     */
    private function getSectionPath(array $matches, bool $abs = false) : string
    {
        $target = $matches[1];
        $section = $this->getSectionNode($target);
        if (! $section) {
            Logger::warning('Linking to 404 EmptyHrefCode');
            return App::getValue('EmptyHrefCode');
        }
        if ($this->_isPreviewServer) {
            return App::getValue('UrlRoot') . App::getValue('NodeRoot') . '/' . $section->GetPublishedPath(null, true);
        }
        $sync = new SynchroFacade();
        if ($this->preview) {
            $idTargetChannel = isset($matches[2]) ? $matches[2] : null;
        } else {
            $idTargetChannel = null;
            $idTargetServer = $sync->getServer($target, $idTargetChannel, $this->_server->get('IdServer'));
        }
        if ($this->preview or (!$abs && !$this->_server->get('OverrideLocalPaths') && ($idTargetServer == $this->_serverNode->get('IdNode')))) {
            $dotdot = str_repeat('../', $this->_depth - 2);
            return $dotdot . $section->GetPublishedPath($idTargetChannel, true);
        }
        $targetServer = new Server($idTargetServer);
        return $targetServer->get('Url') . $section->GetPublishedPath($idTargetChannel, true);
    }

    private function getdotdotpath(array $matches) : string
    {
        $targetPath = $matches[1];
        if ($this->preview) {
            $targetPath .= '?token=' . uniqid();
        } elseif (! $this->_serverNode->get('IdNode')) {
            Logger::warning('Linking to 404 EmptyHrefCode');
            return App::getValue("EmptyHrefCode");
        }
        
        // If preview server, we return the path to data / nodes
        if ($this->_isPreviewServer) {
            return App::getValue('UrlRoot') . App::getValue('NodeRoot') . '/' . $targetPath;
        } else {
            $deep = 2;
            if ($this->preview) {

                // Get section path
                $section = new Node($this->_idSection);
                $sectionPath = $section->class->getNodeURL() . '/';
            } else {

                // Getting relative or absolute path
                if ($this->_server->get('OverrideLocalPaths')) {
                    return $this->_server->get('Url') . '/' . $targetPath;
                }
                if (App::getValue('PublishPathFormat') !== null && $this->_node->class && method_exists($this->_node->class, 'getPathToDeep')) {
                    $deep = $this->_node->class->getPathToDeep();
                }
                $sectionPath = '';
            }
            $dotdot = str_repeat('../', $this->_depth - $deep);
            return $sectionPath . $dotdot . $targetPath;
        }
    }

    private function getLinkPath(array $matches, bool $forceAbsolute = false)
    {
        // Get parentesis content
        $pathToParams = $matches[1];

        // Link target-node
        $parserPathTo = new ParsingPathTo();
        if (! $parserPathTo->parsePathTo($pathToParams, $this->_node->GetID(), null, $this->_idChannel)) {
            if ($parserPathTo->messages()->messages) {
                foreach ($parserPathTo->messages()->messages as $error) {
                    Logger::warning($error['message']);
                }
            } else {
                Logger::warning('Parse PathTo is not working for: ' . $pathToParams);
            }
            if ($this->preview) {
                return false;
            } else {
                Logger::warning('Linking to 404 EmptyHrefCode');
                return App::getValue('EmptyHrefCode');
            }
        }
        if ($parserPathTo->getNode() === null) {
            
            // There is not a node from Ximdex (ex. an external URL)
            return $pathToParams;
        }
        $targetNode = $parserPathTo->getNode();
        $res = [];
        $res["pathMethod"] = $parserPathTo->getPathMethod();
        $res["channel"] = $parserPathTo->getChannel();
        $idNode = $targetNode->GetID();
        if (! $this->preview and $targetNode->GetNodeType() != NodeTypeConstants::LINK) {
            $nodeFrameManager = new NodeFrameManager();
            $nodeFrame = $nodeFrameManager->getNodeFramesInTime($idNode, null, time());
            if (! isset($nodeFrame)) {
                return '';
            }
        }
        if ($this->_node && !$this->_node->get('IdNode')) {
            return '';
        }
        
        // Target channel
        if ($res["channel"] or $res["channel"] === null) {
            $idTargetChannel = $res["channel"];
        } elseif ($this->_idChannel) {
            $idTargetChannel = $this->_idChannel;
        } else {
            $idTargetChannel = null;
        }
        $isStructuredDocument = $targetNode->nodeType->GetIsStructuredDocument();
        if (! $this->preview and $isStructuredDocument) {
            $targetChannelNode = new Channel($idTargetChannel);
            $idTargetChannel = ($targetChannelNode->get('IdChannel') > 0) ? $targetChannelNode->get('IdChannel') : $this->_idChannel;
        }

        // When external link, return the url
        if ($targetNode->GetNodeType() == NodeTypeConstants::LINK) {
            return $targetNode->class->GetUrl();
        }

        // Generate the path
        if ($this->preview) {

            // Generate URL for preview mode
            if ($isStructuredDocument) {
                if ($this->mode == 'dinamic') {
                    return "javascript:parent.loadDivsPreview(" . $idNode . ")";
                } else {
                    $query = App::get('\Ximdex\Utils\QueryManager');
                    $src = $query->getPage(false) . $query->buildWith(array('nodeid' => $idNode, 'token' => uniqid()));
                    if ($parserPathTo->getAnchor()) {
                        $src .= '#' . $parserPathTo->getAnchor();
                    }
                    return $src;
                }
            }

            // Generate the URL to the rendernode action
            $url = App::getValue('UrlRoot') . '/?expresion=' . (($idNode) ? $idNode : $pathToParams) 
                . '&action=rendernode&token=' . uniqid();
            return $url;
        }
        if ($this->_isPreviewServer) {
            if ($isStructuredDocument) {
                $src = App::getValue('UrlRoot') . App::getValue('NodeRoot') . $targetNode->GetPublishedPath($idTargetChannel, true);
                if ($parserPathTo->getAnchor()) {
                    $src .= '#' . $parserPathTo->getAnchor();
                }
                return $src;
            } else {
                return $targetNode->class->getNodeURL();
            }
        }
        if (App::getValue('PullMode') == 1) {
            return App::getValue('UrlRoot') . '/src/Rest/Pull/index.php?idnode=' . $targetNode->get('IdNode') 
                . '&idchannel=' . $idTargetChannel . '&idportal=' . $this->_serverNode->get('IdNode');
        }
        if ($targetNode->nodeType->GetIsSection()) {
            $idTargetServer = $this->_server->get('IdServer');
        } else {
            
            // Get the server to publicate the node with the correspondant channel
            $sync = new SynchroFacade();
            $idTargetServer = $sync->getServer($targetNode->get('IdNode'), $idTargetChannel, $this->_server->get('IdServer'));
        }
        $targetServer = new Server($idTargetServer);
        if (! $targetServer->get('IdServer')) {
            Logger::warning('Linking to 404 EmptyHrefCode');
            return App::getValue('EmptyHrefCode');
        }
        
        // Get the relative or absolute path
        if ($forceAbsolute or ($targetServer->get('IdServer') != $this->_server->get('IdServer')) 
            or $this->_server->get('OverrideLocalPaths') or (isset($res['pathMethod']['absolute']) and $res['pathMethod']['absolute'])) {
            $src = $this->getAbsolutePath($targetNode, $targetServer, $idTargetChannel);
            if ($parserPathTo->getAnchor()) {
                $src .= '#' . $parserPathTo->getAnchor();
            }
            return $src;
        }
        $src = $this->getRelativePath($targetNode, $this->_server, $idTargetChannel);
        if ($parserPathTo->getAnchor()) {
            $src .= '#' . $parserPathTo->getAnchor();
        }
        return $src;
    }

    private function getBreadCrumb(array $matches) : string
    {
        $id = $matches[1];
        if ($id === 'THIS') {
            $id = $this->_node->GetID();
        }
        $parents = array_reverse(FastTraverse::getParents($id, 'node.Name', 'node.IdNode', ['isPublishable' => 1]), true);
        $breadcrumb = '<breadcrumb>';
        foreach ($parents as $nodeId => $nodeName) {
            if (App::getValue('PublishPathFormat') == App::PREFIX and $id == $nodeId) {
                $parts = explode('-', $nodeName);
                if (count($parts) > 1 ) {
                    unset($parts[count($parts) - 1]);
                    $nodeName = implode('-', $parts);
                }
            }
            $href = "@@@RMximdex.pathto({$nodeId})@@@";
            $breadcrumb .= PHP_EOL . "<link href=\"{$href}\">{$nodeName}</link>";
        }
        $breadcrumb .= '</breadcrumb>';
        return $breadcrumb;
    }
    
    private function getLinkPathAbs(array $matches)
    {
        return $this->getLinkPath($matches, true);
    }

    private function getRelativePathWithDotdot(Node $targetNode, int $idTargetChannel) : string
    {
        $deep = 2;
        if (! $this->preview and App::getValue("PublishPathFormat") == App::PREFIX) {
            if ($this->_node->nodeType->GetIsStructuredDocument()) {
                
                // Language for the original document
                if (! $this->originHasLangPath or $this->originNodeID != $this->_node->GetID()) {
                    $this->originHasLangPath = $this->_node->hasLangPath();
                    $this->originNodeID = $this->_node->GetID();
                }
            } else {
                $this->originHasLangPath = null;
            }
            if ($targetNode->nodeType->GetIsStructuredDocument()) {
                
                // Language for the target document
                $targetHasLangPath = $targetNode->hasLangPath();
            } else {
                $targetHasLangPath = null;
            }
            
            // If the origin and target document has language in their paths, or only the origin: deep is only a level
            if (($this->originHasLangPath and $targetHasLangPath) or ($this->originHasLangPath and ! $targetHasLangPath)) {
                $deep = 1;
            }
        }
        $dotdot = str_repeat('../', $this->_depth - $deep);
        
        // Removing last dash
        $dotdot = preg_replace('/\/$/', '', $dotdot);
        $dotdot = './' . $dotdot;
        $urlDotDot = $dotdot . $targetNode->GetPublishedPath($idTargetChannel, true);
        $urlDotDot = str_replace("//", "/", $urlDotDot);
        return $urlDotDot;
    }
    
    /**
     * Get relative path to target node
     * 
     * @param Node $targetNode
     * @param Server $targetSever
     * @param int $idTargetChannel
     * @return string
     */
    private function getRelativePath(Node $targetNode, Server $targetServer = null, int $idTargetChannel = null) : string
    {
        if ($targetServer) {
            $path = FsUtils::get_url_path($targetServer->get('Url'), false);
        } else {
            $path = '';
        }
        return $path . $targetNode->GetPublishedPath($idTargetChannel, true);
    }

    private function getAbsolutePath(Node $targetNode, Server $targetServer, int $idTargetChannel = null) : string
    {
        return $targetServer->get('Url') . $targetNode->GetPublishedPath($idTargetChannel, true);
    }

    private function getInclude(array $matches)
    {
        // Get parentesis content
        $nodeId = (int) $matches[1];
        if ($nodeId <= 1) {
            Logger::error('Node value for ' . $matches[1] . ' is not valid');
            return false;
        }
        $targetNode = new Node($nodeId);
        if (! $targetNode->GetID()) {
            Logger::error('Could not load a node with ID ' . $nodeId);
            return false;
        }
        $targetServer = new server($this->_server->get('IdServer'));
        if (! $targetServer->get('IdServer')) {
            Logger::error('Cannot include the file in unknown server with node ID: ' . $nodeId);
            return false;
        }
        
        // Get the channel for the include link if it is not published in the origin document
        $idChannel = $this->_idChannel;
        if (! $targetNode->nodeType->GetIsFolder()) {
            $targetFrame = new ServerFrame();
            $frameID = $targetFrame->getCurrent($targetNode->GetID(), $idChannel, $targetServer->get('IdServer'));
            if (! $frameID) {
                
                // Not published in the current channel
                $frames = $targetFrame->getFramesOnDate($targetNode->GetID(), time(), $targetServer->get('IdServer'));
                if ($frames) {
                    $sync = new SynchroFacade();
                    $idChannel = $sync->getFrameChannel($frames[0]['IdSync']);
                }
            }
        }
        
        // Get the path
        $src = $targetServer->get('InitialDirectory') . $targetNode->GetPublishedPath($idChannel, true);
        return $src;
    }
    
    private function getLangName(array $matches) : string
    {
        if ($matches[1]) {
            $iso = strtolower($matches[1]);
        } else {
            $strDoc = new StructuredDocument($this->_node->getID());
            $language = new Language($strDoc->getLanguage());
            $iso = $language->GetIsoName();
        }
        $isoCode = new IsoCode();
        $res = $isoCode->find('NativeName', "Iso2 = '$iso' OR Iso3 = '$iso'");
        if (! $res) {
            return '';
        }
        return $res[0]['NativeName'];
    }
}
