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
 *
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


ModulesManager::file('/inc/model/Versions.php');
ModulesManager::file('/inc/model/node.php');
ModulesManager::file('/inc/model/channel.php');
ModulesManager::file('/inc/model/Server.class.php');
ModulesManager::file('/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/PAS_Conector.class.php', 'ximPAS');
ModulesManager::file('/inc/repository/nodeviews/Abstract_View.class.php');
ModulesManager::file('/inc/repository/nodeviews/Interface_View.class.php');
ModulesManager::file('/inc/parsers/ParsingPathTo.class.php');

class View_FilterMacros extends Abstract_View implements Interface_View
{

    protected $_node = NULL;
    protected $_server = NULL;
    protected $_serverNode = NULL;
    protected $_projectNode = NULL;
    protected $_idChannel;
    protected $_isPreviewServer = false;
    protected $_depth = NULL;
    protected $_idSection = NULL;
    protected $_nodeName = "";
    const MACRO_SERVERNAME = "/@@@RMximdex\.servername\(\)@@@/";
    const MACRO_PROJECTNAME = "/@@@RMximdex\.projectname\(\)@@@/";
    const MACRO_NODENAME = "/@@@RMximdex\.nodename\(\)@@@/";
    const MACRO_SECTIONPATH = "/@@@RMximdex\.sectionpath\(([0-9]+)\)@@@/";
    const MACRO_SECTIONPATHABS = "/@@@RMximdex\.sectionpathabs\(([0-9]+)\)@@@/";
    const MACRO_DOTDOT = "/@@@RMximdex\.dotdot\(([^\)]*)\)@@@/";
    const MACRO_PATHTO = "/@@@RMximdex\.pathto\(([,-_#%=\.\w\s]+)\)@@@/";
    const MACRO_PATHTOABS = "/@@@RMximdex\.pathtoabs\(([,-_#%=\.\w\s]+)\)@@@/";
    const MACRO_RDF = "/@@@RMximdex\.rdf\(([^\)]+)\)@@@/";
    const MACRO_RDFA = "/@@@RMximdex\.rdfa\(([^\)]+)\)@@@/";


    /**
     * Main method. Get a pointer content file and return a new transformed content file. This probably cames from Transformer (View_XSLT), so will be the renderized content.
     * @param  int $idVersion Node version
     * @param  string $pointer file name with the content to transform
     * @param  array $args Params about the current node
     * @return string file name with the transformed content.
     */
    public function transform($idVersion = NULL, $pointer = NULL, $args = NULL)
    {

        //Check the conditions
        if (!$this->initializeParams($args, $idVersion))
            return NULL;

        $content = $this->transformFromPointer($pointer);
        //Return the pointer to the transformed content.
        return $this->storeTmpContent($content);
    }

    /**
     * Initialize params from transformation args
     * @param array $args Arguments for transformation
     * @param int $idVersion
     * @return boolean True if everything is allright.
     */
    protected function initializeParams($args, $idVersion)
    {
        if (!$this->_setNode($idVersion))
            return NULL;

        if (!$this->_setIdChannel($args))
            return NULL;

        if (!$this->_setServer($args))
            return NULL;

        if (!$this->_setServerNode($args))
            return NULL;

        if (!$this->_setProjectNode($args))
            return NULL;

        if (!$this->_setDepth($args))
            return NULL;

        if (!$this->_setNodeName($args))
            return NULL;

        return true;
    }

    /**
     * Load the node param from an idVersion.
     * @param int $idVersion Version id
     * @return boolean True if exists node for selected version or the current node.
     */
    protected function _setNode($idVersion = NULL)
    {

        if (!is_null($idVersion)) {
            $version = new Version($idVersion);
            if (!($version->get('IdVersion') > 0)) {
                XMD_Log::error(
                    'VIEW FILTERMACROS: Se ha cargado una versión incorrecta (' . $idVersion .
                    ')');
                return NULL;
            }

            $this->_node = new Node($version->get('IdNode'));
            if (!($this->_node->get('IdNode') > 0)) {
                XMD_Log::error(
                    'VIEW FILTERMACROS: El nodo que se está intentando convertir no existe: ' .
                    $version->get('IdNode'));
                return NULL;
            }
        }

        return true;
    }

    /**
     * Load channel param from args array.
     * @param array $args [description]
     * @return boolean true if exists channel.
     */
    protected function _setIdChannel($args = array())
    {

        if (array_key_exists('CHANNEL', $args)) {
            $this->_idChannel = $args['CHANNEL'];
        }

        // Check Params:
        if (!isset($this->_idChannel) || !($this->_idChannel > 0)) {
            XMD_Log::error(
                'VIEW FILTERMACROS: Channel not specified for node ' . $args['NODENAME']);
            return NULL;
        }

        return true;
    }

    /**
     * Load server param from args array.
     * @param array $args [description]
     * @return boolean true if exists the server in args.
     */
    protected function _setServer($args = array())
    {

        if (array_key_exists('SERVER', $args)) {
            $this->_server = new Server($args['SERVER']);
            if (!($this->_server->get('IdServer') > 0)) {
                XMD_Log::error(
                    'VIEW FILTERMACROS: Server where you want to render the node not specified ');
                return NULL;
            }
            $this->_isPreviewServer = $this->_server->get('Previsual');
        }

        return true;
    }


    /**
     * Load the server node for the current node
     * @param array $args Transformation args.
     * @return  boolean True if exists the server node.
     */
    protected function _setServerNode($args = array())
    {

        if ($this->_node) {
            $this->_serverNode = new Node($this->_node->getServer());
        } elseif (array_key_exists('SERVERNODE', $args)) {
            $this->_serverNode = new Node($args['SERVERNODE']);
        }

        // Check Params:
        if (!($this->_serverNode) || !is_object($this->_serverNode)) {
            XMD_Log::error(
                'VIEW FILTERMACROS: There is no server linked to the node ' . $args['NODENAME'] .
                ' que quiere renderizar');
            return NULL;
        }

        return true;
    }

    /**
     * Load the project node for the current transformed node.
     * @param array $args Transformation args.
     * @return  boolean true if exists the project node.
     */
    protected function _setProjectNode($args = array())
    {

        if ($this->_node) {
            $this->_projectNode = $this->_node->getProject();
        } elseif (array_key_exists('PROJECT', $args)) {
            $this->_projectNode = $args['PROJECT'];
        }

        // Check Params:
        if (!isset($this->_projectNode) || !($this->_projectNode > 0)) {
            XMD_Log::error(
                'VIEW FILTERMACROS: There is not associated project for the node ' . $args['NODENAME']);
            return NULL;
        }

        return true;
    }

    /**
     * Load the depth for the current node.
     * @param array $args Transformation args.
     * @return boolean true if exits depth form the current node.
     */
    protected function _setDepth($args = array())
    {

        if ($this->_node) {
            $this->_depth = $this->_node->GetPublishedDepth();
        } elseif (array_key_exists('DEPTH', $args)) {
            $this->_depth = $args['DEPTH'];
        }

        // Check Param:
        if (!isset($this->_depth) || !($this->_depth > 0)) {
            XMD_Log::error(
                'VIEW FILTERMACROS: No se ha especificado la profundidad del nodo ' . $args['NODENAME'] .
                ' que quiere renderizar');
            return NULL;
        }

        return true;
    }

    /**
     * Load the nodename from the selected node.
     * @param array $args Transformation args.
     * @return  boolean true if exists name for the current node.
     */
    protected function _setNodeName($args = array())
    {

        if ($this->_node) {
            $this->_nodeName = $this->_node->get('Name');
        } elseif (array_key_exists('NODENAME', $args)) {
            $this->_nodeName = $args['NODENAME'];
        }

        // Check Param:
        if (!isset($this->_nodeName) || $this->_nodeName == "") {
            XMD_Log::error(
                'VIEW FILTERMACROS: No se ha especificado el nombre del nodo que quiere renderizar');
            return NULL;
        }

        return true;
    }


    protected function transformFromPointer($pointer)
    {
        //Get the content.
        $content = $this->retrieveContent($pointer);

        /**
         * Available macros:
         * * servername
         * * projectname
         * * nodename
         * * sectionpath
         * * dotdot
         * * pathto
         * * rdf
         * * rdfa
         */

        $serverName = $this->_serverNode->get('Name');
        $content = preg_replace(self::MACRO_SERVERNAME, $serverName, $content);

        if (preg_match(self::MACRO_PROJECTNAME, $content)) {
            $project = new Node($this->_projectNode);
            $projectName = $project->get('Name');
            $content = preg_replace(self::MACRO_PROJECTNAME, $projectName, $content);
        }

        $content = preg_replace(self::MACRO_NODENAME, $this->_nodeName, $content);

        $content = preg_replace_callback(self::MACRO_SECTIONPATH,
            array($this, 'getSectionPath'),
            $content);

        $content = preg_replace_callback(self::MACRO_SECTIONPATHABS,
            array($this, 'getSectionPathAbs'),
            $content);

        $content = preg_replace_callback(self::MACRO_DOTDOT,
            array($this, 'getdotdotpath'), $content);

        //Pathto
        $content = preg_replace_callback(self::MACRO_PATHTO,
            array($this, 'getLinkPath'), $content);

        //Pathtoabs
        $content = preg_replace_callback(self::MACRO_PATHTOABS,
            array($this, 'getLinkPathAbs'), $content);


        $content = preg_replace_callback(self::MACRO_RDF,
            array($this, 'getRDFByNodeId'), $content);

        $content = preg_replace_callback(self::MACRO_RDFA,
            array($this, 'getRDFaByNodeId'), $content);


        //Once macros are resolver, remove uid attribute from tags.
        $content = preg_replace_callback("/(<.*?)(uid=\".*?\")(.*?\/?>)/", array($this, 'removeUIDs'), $content);

        return $content;
    }

    /**
     * <p>Remove the uid attributes generated by the editor</p>
     * @param array $matches Array containing the matches of the regular expression
     *
     * @return string String to be used to replace the matching of the regular expression
     */
    private function removeUIDs($matches)
    {
        return str_replace(" >", ">", $matches[1] . $matches[3]);
    }


    /**
     * Get the section node of the $idNode
     * @param  int $idNode descendant of the searched Section.
     * @return Node The section node.
     */
    protected function getSectionNode($idNode)
    {
        $node = new Node($idNode);
        if (!($node->get('IdNode') > 0)) {
            return false;
        }
        // Target Channel

        $idSection = $node->GetSection();
        $section = new Node($idSection);
        return $section;
    }

    private function getSectionPathAbs($matches)
    {
        return $this->getSectionPath($matches, true);
    }

    /**
     * Get section path for a selected idnode and channel in matches.
     * @param  array $matches An idnode and an optional idchannel
     * @return string Link url.
     */
    private function getSectionPath($matches, $abs = false)
    {
        $target = $matches[1];
        $node = new Node($target);
        $section = $this->getSectionNode($target);
        if (!$section) {
            return \App::getValue( 'EmptyHrefCode');
        }
        if ($this->_isPreviewServer) {
            return \App::getValue( 'UrlRoot') . \App::getValue( 'NodeRoot') . '/' . $section->GetPublishedPath(
                NULL, true);
        }

        $sync = new SynchroFacade();
        $idTargetChannel = null;
        $idTargetServer = $sync->getServer($target, $idTargetChannel,
            $this->_server->get('IdServer'));
        $targetServer = new Server($idTargetServer);
        if (!$abs && !$this->_server->get('OverrideLocalPaths') && ($idTargetServer == $this->_serverNode->get(
                    'IdNode'))
        ) {
            $dotdot = str_repeat('../', $this->_depth - 2);
            return $dotdot . $section->GetPublishedPath($idTargetChannel, true);
        }
        return $targetServer->get('Url') . $section->GetPublishedPath($idTargetChannel, true);

    }

    private function getdotdotpath($matches)
    {

        $targetPath = $matches[1];

        if (!($this->_serverNode->get('IdNode') > 0)) {
            return \App::getValue( "EmptyHrefCode");
        }

        //If preview, we return the path to data/nodes
        if ($this->_isPreviewServer) {
            return \App::getValue( "UrlRoot") . \App::getValue( "NodeRoot") . "/" . $targetPath;
        } else {
            //Getting relative or absolute path.
            if ($this->_server->get('OverrideLocalPaths')) {
                return $this->_server->get('Url') . "/" . $targetPath;
            }

            $deep = 2;
            if (\App::getValue("PublishPathFormat", null) !== null &&
                $this->_node->class &&
                method_exists($this->_node->class, "getPathToDeep")
            ) {
                $deep = $this->_node->class->getPathToDeep();
            }

            $dotdot = str_repeat('../', $this->_depth - $deep);

            return $dotdot . $targetPath;
        }
    }

    private function getLinkPath($matches, $forceAbsolute = false)
    {

        $absolute = $relative = false;
        //Get parentesis content
        $pathToParams = $matches[1];
        $parserPathTo = new ParsingPathTo();
        $parserPathTo->parsePathTo($pathToParams, $this->_node->GetID());

        $res["idNode"] = $parserPathTo->getIdNode();
        $res["pathMethod"] = $parserPathTo->getPathMethod();
        $res["channel"] = $parserPathTo->getChannel();

        if (!$res || !is_array($res) || !count($res)) {
            return '';
        } else {
            $idNode = $res["idNode"];
            $idTargetChannel = (count($res) == 3 && isset($res["channel"])) ? $res["channel"] : NULL;
        }
        $targetNode = new Node($idNode);

        if (isset($res["pathMethod"])) {
            $absolute = isset($res["pathMethod"]["absolute"]) && $res["pathMethod"]["absolute"];
            $relative = isset($res["pathMethod"]["relative"]) && $res["pathMethod"]["relative"];
        }

        if (!$targetNode->get('IdNode')) {
            return '';
        }

        if ($this->_node && !$this->_node->get('IdNode')) {
            return '';
        }

        $isStructuredDocument = $targetNode->nodeType->GetIsStructuredDocument();

        $targetChannelNode = new Channel($idTargetChannel);

        if ($isStructuredDocument) {
            $idTargetChannel = ($targetChannelNode->get('IdChannel') > 0) ? $targetChannelNode->get(
                'IdChannel') : $this->_idChannel;
        }

        // When external link, return the url.
        if ($targetNode->nodeType->get('Name') == 'Link') {
            return $targetNode->class->GetUrl();
        }

        if ($this->_isPreviewServer) {
            if ($isStructuredDocument) {
                return \App::getValue( 'UrlRoot') . \App::getValue( 'NodeRoot') . $targetNode->GetPublishedPath(
                    $idTargetChannel, true);
            } else {
                return $targetNode->class->GetNodeURL();
            }
        }

        if (\App::getValue( 'PullMode') == 1) {

            return \App::getValue( 'UrlRoot') . '/services/pull/index.php?idnode=' . $targetNode->get(
                'IdNode') . '&idchannel=' . $idTargetChannel . '&idportal=' . $this->_serverNode->get(
                'IdNode');
        }

        $sync = new SynchroFacade();
        $idTargetServer = $sync->getServer($targetNode->get('IdNode'), $idTargetChannel,
            $this->_server->get('IdServer'));
        $targetServer = new server($idTargetServer);
        $idTargetServer = $targetServer->get('IdServer');
        if (!($idTargetServer > 0)) {
            return \App::getValue( 'EmptyHrefCode');
        }

        if (!$forceAbsolute && !$absolute && !$relative) {
            if (!$this->_server->get('OverrideLocalPaths') && ($idTargetServer == $this->_server->get(
                        'IdServer'))
            ) {
                return $this->getRelativePath($targetNode, $idTargetChannel);
            } else {
                return $this->getAbsolutePath($targetNode, $targetServer, $idTargetChannel);
            }
        } else if ($forceAbsolute || $absolute) {
            return $this->getAbsolutePath($targetNode, $targetServer, $idTargetChannel);
        } else { //Must be relative.
            return $this->getRelativePath($targetNode, $idTargetChannel);
        }
    }

    private function getLinkPathAbs($matches)
    {
        return $this->getLinkPath($matches, true);
    }

    private function getRelativePath($targetNode, $idTargetChannel)
    {

        $deep = 2;
        if ( \App::getValue("PublishPathFormat", null ) !== null  &&
            $this->_node->class &&
            method_exists($this->_node->class, "getPathToDeep")
        ) {
            $deep = $this->_node->class->getPathToDeep();
        }
        $dotdot = str_repeat('../', $this->_depth - $deep);
        //Removing last dash.
        $dotdot = preg_replace('/\/$/', '', $dotdot);
        $dotdot = './' . $dotdot;
        $urlDotDot = $dotdot . $targetNode->GetPublishedPath($idTargetChannel, true);
        $urlDotDot = str_replace("//", "/", $urlDotDot);
        return $urlDotDot;
    }

    private function getAbsolutePath($targetNode, $targetServer, $idTargetChannel)
    {

        return $targetServer->get('Url') . $targetNode->GetPublishedPath($idTargetChannel, true);
    }

    protected function getRDFByNodeId($params, $rdfa = false)
    {

        if (!ModulesManager::isEnabled('ximPAS')) {
            return '';
        }

        $nodeId = $params[1];
        $node = new Node($nodeId);
        if (!$node->get('IdNode')) {
            return '';
        }

        $pas = new PAS_Conector();
        $rdf = $rdfa === false ? $pas->getRDFByNodeId($nodeId) : $pas->getRDFaByNodeId($nodeId);
        return "\n$rdf\n";
    }

    protected function getRDFaByNodeId($params)
    {
        return $this->getRDFByNodeId($params, true);
    }

    /*********************************************************/
    /****************LinkPath auxiliar methods****************/
    /*********************************************************/













}
