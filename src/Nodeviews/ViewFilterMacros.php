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
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Models\Server;
use Ximdex\Utils\Messages;
use Ximdex\Sync\SynchroFacade;
use Ximdex\Models\FastTraverse;
use Ximdex\Models\ServerFrame;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Language;
use Ximdex\Models\IsoCode;
use Ximdex\Models\RelSemanticTagsNodes;

class ViewFilterMacros extends AbstractView
{
    const MACRO_SERVERNAME = "/@@@RMximdex\.servername\(\)@@@/";
    
    const MACRO_PROJECTNAME = "/@@@RMximdex\.projectname\(\)@@@/";
    
    const MACRO_NODENAME = "/@@@RMximdex\.nodename\(\)@@@/";
    
    const MACRO_SECTIONPATH = "/@@@RMximdex\.sectionpath\(([0-9]+)\)@@@/";
    
    const MACRO_SECTIONPATHABS = "/@@@RMximdex\.sectionpathabs\(([0-9]+)\)@@@/";
    
    const MACRO_DOTDOT = "/@@@RMximdex\.dotdot\(([^\)]*)\)@@@/";
    
    const MACRO_BREADCRUMB = "/@@@RMximdex\.breadcrumb\(([,-_#%=\.\w\s]+)\)@@@/";
    
    const MACRO_INCLUDE = "/@@@RMximdex\.include\(([^\)]*)\)@@@/";
    
    const MACRO_METADATA = "/@@@RMximdex\.metadata\(([^,\)]*),([\w\.\-\s]*)\)@@@/";
    
    const MACRO_NODE_LANG_NAME = "/@@@RMximdex\.langname\(([A-Z]+|)\)@@@/";
    
    const MACRO_STRUCTURED_METADATA = "/@@@RMximdex\.structuredmetadata\(([,-_#%=\.\w\s]+)\)@@@/";
    
    private $_projectNode;
    
    private $_depth;
    
    private $_idSection;
    
    private $_nodeName;
    
    private $_nodeTypeName;
    
    private $messages;
    
    private $originHasLangPath;
    
    private $originNodeID;

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
     * {@inheritDoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
        if (parent::transform($idVersion, $content, $args) === false) {
            return false;
        }
        
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
    private function initializeParams(array $args, int $idVersion = null): bool
    {
        if ($this->preview) {
            if (array_key_exists('NODETYPENAME', $args)) {
                $this->_nodeTypeName = $args['NODETYPENAME'];
            }
            $this->mode = (isset($args['MODE']) && $args['MODE'] == 'dinamic') ? 'dinamic' : 'static';
            if (! $this->_setIdSection($args)) {
                return false;
            }
        }
        if (! $this->setProjectNode($args)) {
            return false;
        }
        if (! $this->setDepth($args)) {
            return false;
        }
        if (! $this->setNodeName($args)) {
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
    private function setProjectNode(array $args = array()): bool
    {
        if ($this->node) {
            $this->_projectNode = $this->node->getProject();
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
    private function setDepth(array $args = array()): bool
    {
        if ($this->node) {
            $this->_depth = $this->node->getPublishedDepth();
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
    private function setNodeName(array $args = array()): bool
    {
        if ($this->node) {
            $this->_nodeName = $this->node->get('Name');
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
    private function _setIdSection(array $args = array()): bool
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

    private function transformFromContent(string $content): string
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
        $serverName = $this->serverNode->get('Name');
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
        
        // Semantic tags
        $content = preg_replace_callback(self::MACRO_STRUCTURED_METADATA, array(
            $this,
            'getStructuredMetadata'
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
    private function removeUIDs(array $matches): string
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
        $idSection = $node->getSection();
        $section = new Node($idSection);
        return $section;
    }

    private function getSectionPathAbs(array $matches)
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
    private function getSectionPath(array $matches, bool $abs = false): string
    {
        $target = $matches[1];
        $section = $this->getSectionNode($target);
        if (! $section) {
            return $this->getLinkTo404($abs);
        }
        if ($this->isPreviewServer) {
            return App::getValue('UrlRoot') . App::getValue('NodeRoot') . '/' . $section->GetPublishedPath(null, true);
        }
        $sync = new SynchroFacade();
        if ($this->preview) {
            $idTargetChannel = isset($matches[2]) ? $matches[2] : null;
        } else {
            $idTargetChannel = null;
            $idTargetServer = $sync->getServer($target, $idTargetChannel, $this->server->get('IdServer'));
        }
        if ($this->preview or (! $abs && ! $this->server->get('OverrideLocalPaths') && ($idTargetServer == $this->serverNode->get('IdNode')))) {
            $dotdot = str_repeat('../', $this->_depth - 2);
            return $dotdot . $section->getPublishedPath($idTargetChannel, true);
        }
        $targetServer = new Server($idTargetServer);
        return $targetServer->get('Url') . $section->getPublishedPath($idTargetChannel, true);
    }

    /**
     * @deprecated
     * @param array $matches
     * @return string
     */
    private function getdotdotpath(array $matches): string
    {
        $targetPath = $matches[1];
        if ($this->preview) {
            $targetPath .= '?token=' . uniqid();
        } elseif (! $this->serverNode->get('IdNode')) {
            return $this->getLinkTo404();
        }
        
        // If preview server, we return the path to data / nodes
        if ($this->isPreviewServer) {
            return App::getValue('UrlRoot') . App::getValue('NodeRoot') . '/' . $targetPath;
        } else {
            $deep = 2;
            if ($this->preview) {

                // Get section path
                $section = new Node($this->_idSection);
                $sectionPath = $section->class->getNodeURL() . '/';
            } else {

                // Getting relative or absolute path
                if ($this->server->get('OverrideLocalPaths')) {
                    return $this->server->get('Url') . '/' . $targetPath;
                }
                if (App::getValue('PublishPathFormat') !== null && $this->node->class && method_exists($this->node->class, 'getPathToDeep')) {
                    $deep = $this->node->class->getPathToDeep();
                }
                $sectionPath = '';
            }
            $dotdot = str_repeat('../', $this->_depth - $deep);
            return $sectionPath . $dotdot . $targetPath;
        }
    }

    private function getBreadCrumb(array $matches): string
    {
        $id = $matches[1];
        if ($id === 'THIS') {
            $id = $this->node->getID();
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

    /**
     * @deprecated
     * @param Node $targetNode
     * @param int $idTargetChannel
     * @return string
     */
    private function getRelativePathWithDotdot(Node $targetNode, int $idTargetChannel): string
    {
        $deep = 2;
        if (! $this->preview and App::getValue("PublishPathFormat") == App::PREFIX) {
            if ($this->node->nodeType->getIsStructuredDocument()) {
                
                // Language for the original document
                if (! $this->originHasLangPath or $this->originNodeID != $this->node->getID()) {
                    $this->originHasLangPath = $this->node->hasLangPath();
                    $this->originNodeID = $this->node->getID();
                }
            } else {
                $this->originHasLangPath = null;
            }
            if ($targetNode->nodeType->getIsStructuredDocument()) {
                
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

    private function getInclude(array $matches)
    {
        // Get parentesis content
        $nodeId = (int) $matches[1];
        if ($nodeId <= 1) {
            Logger::error('Node value for ' . $matches[1] . ' is not valid');
            return false;
        }
        $targetNode = new Node($nodeId);
        if (! $targetNode->getID()) {
            Logger::error('Could not load a node with ID ' . $nodeId);
            return false;
        }
        $targetServer = new server($this->server->get('IdServer'));
        if (! $targetServer->get('IdServer')) {
            Logger::error('Cannot include the file in unknown server with node ID: ' . $nodeId);
            return false;
        }
        
        // Get the channel for the include link if it is not published in the origin document
        $idChannel = $this->channel->getID();
        if (! $targetNode->nodeType->getIsFolder()) {
            $targetFrame = new ServerFrame();
            $frameID = $targetFrame->getCurrent($targetNode->getID(), $idChannel, $targetServer->get('IdServer'));
            if (! $frameID) {
                
                // Not published in the current channel
                $frames = $targetFrame->getFramesOnDate($targetNode->getID(), time(), $targetServer->get('IdServer'));
                if ($frames) {
                    $sync = new SynchroFacade();
                    $idChannel = $sync->getFrameChannel($frames[0]['IdSync']);
                }
            }
        }
        
        // Get the path
        $src = $targetServer->get('InitialDirectory') . $targetNode->getPublishedPath($idChannel, true);
        return $src;
    }
    
    private function getLangName(array $matches): string
    {
        if ($matches[1]) {
            $iso = strtolower($matches[1]);
        } else {
            $strDoc = new StructuredDocument($this->node->getID());
            $language = new Language($strDoc->getLanguage());
            $iso = $language->getIsoName();
        }
        $isoCode = new IsoCode();
        $res = $isoCode->find('NativeName', "Iso2 = '$iso' OR Iso3 = '$iso'");
        if (! $res) {
            return '';
        }
        return $res[0]['NativeName'];
    }
    
    private function getStructuredMetadata(array $matches): ?string
    {
        $type = strtoupper($matches[1]);
        switch ($type) {
            case 'HTML':
                $code = 'html';
                break;
            case 'XML':
                $code = 'xml';
                break;
            case 'JSON':
                $relTags = (new RelSemanticTagsNodes)->getTags($this->node->getID());
                $tags = [];
                foreach ($relTags as $tag) {
                    $tags[] = [
                        'name' => $tag['Name'],
                        'url' => $tag['Link'],
                        'description' => (string) $tag['Description']
                    ];
                }
                $code = json_encode($tags);
                break;
            default:
                Logger::error("Type not {$type} supported");
                return null;
        }
        return $code;
    }
}
