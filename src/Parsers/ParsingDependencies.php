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

namespace Ximdex\Parsers;

use Ximdex\Logger;
use Ximdex\Runtime\DataFactory;
use Ximdex\Models\Dependencies;
use Ximdex\Deps\DepsManager;
use DOMDocument;
use DOMXPath;
use Ximdex\Models\NodeDependencies;
use Ximdex\Models\NodeType;
use Ximdex\Utils\PipelineManager;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Node;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Utils\Messages;

class ParsingDependencies
{
    public $messages;

    public function __construct()
    {
        $this->messages = new Messages();
    }

    /**
     * Function which obtain the structuredDocument indentifier
     * (normal if it is resolved, or exportation one if its not resolved yet)
     *
     * @param string $content xml's content
     * @return array
     */
    function GetStructuredDocumentXimletsExtended($content)
    {
        $regExp = '';
        if (preg_match_all('/<ximlet(\s*idExportationXimlet\="(\d*?)"\s*)?>@@@GMximdex.ximlet\((\d+)\)/i', $content, $matches) > 0) {

            // Looking for in all results in a iterative way
            $totalMatches = count($matches[0]);

            /*
             * In position 2 of the array: IdExportationXimlet
             * In position 3 of the array: Gmximdex.ximlet
             *
             * Position 2 is mandatory over the position 3 always this method is executed
             */
            $results = array();
            $results[0] = array();
            $results[1] = array();
            for ($i = 0; $i < $totalMatches; $i++) {
                $results[0][] = $matches[0][$i];

                // Previous node which is included in the xml
                $results[2][] = $matches[3][$i];
                if (!empty($matches[2][$i])) {
                    $results[1][] = $matches[2][$i];
                    continue;
                }
                $results[1][] = $matches[3][$i];
            }
            return $results;
        }
        return array();
    }

    /**
     * Gets all document dependencies and updates database
     * The expected dependencies are from:
     * Server, Section, Documents, Channels, Xslts, ximlets, links
     *
     * @param int $idNode
     * @param string $content
     * @return boolean
     */
    public function parseAllDependencies($idNode, $content)
    {
        $node = new Node($idNode);
        if (!($node->get('IdNode') > 0)) {
            Logger::error('Error while node loading.');
            $this->messages->add('There is not a Node with the IdNode: ' . $idNode, MSG_TYPE_ERROR);
            return false;
        }
        if (!($node->nodeType->get('IsStructuredDocument') == 1)) {
            Logger::info('This node is not a structured document');
            $this->messages->add('This node is not a structured document (IdNode: ' . $idNode . ')', MSG_TYPE_ERROR);
            return false;
        }
        $dataFactory = new DataFactory($idNode);
        $idVersion = $dataFactory->GetLastVersionId();
        switch ($node->get("IdNodeType")) {
            case NodeTypeConstants::XHTML5_DOC:
            case NodeTypeConstants::XML_DOCUMENT:
            case NodeTypeConstants::HTML_DOCUMENT:
                $result = self::parseXMLDependencies($node, $content, $idVersion);
                break;
            case NodeTypeConstants::CSS_FILE:
                $result = self::parseCssDependencies($node, $content, $idVersion);
                break;
            //TODO ajlucena Parse js dependencies?
            default:
                return true;
        }

        // If there is any error in the parsing process, the global error provided from the static method will be added to the warning messages
        if (isset($GLOBALS['parsingDependenciesError']) and $GLOBALS['parsingDependenciesError']) {
            $this->messages->add('Parsing dependencies mistake detected: ' . $GLOBALS['parsingDependenciesError'], MSG_TYPE_WARNING);
            $GLOBALS['parsingDependenciesError'] = null;
        }
        if ($result) {
            Logger::info('All dependencies have been parsed for the node with ID: ' . $node->GetID() . ' and name: ' . $node->GetNodeName(), true);
        }
        return $result;
    }

    /**
     * Search Dependencies in $content for $node and save it.
     *
     * @param Node $node
     * @param String $content
     * @param int $idVersion
     * @return bool
     */
    public static function parseXMLDependencies($node, $content, $idVersion)
    {
        $idNode = $node->get("IdNode");
        $structuredDocument = new StructuredDocument($idNode);
        if (!self::clearDependencies($node)) {
            $GLOBALS['parsingDependenciesError'] = 'The dependencies of the given XML cant\'t be cleared';
            return false;
        }
        if (self::buildDependenciesFromStructuredDocument($node, $structuredDocument) === false) {
            return false;
        }
        if (self::buildDependenciesWithXimlets($node, $structuredDocument, $content) === false) {
            if (!isset($GLOBALS['parsingDependenciesError']) or !$GLOBALS['parsingDependenciesError']) {
                $GLOBALS['parsingDependenciesError'] = 'Can\'t build dependencies with related Ximlets documents';
            }
            return false;
        }
        if ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT and self::buildDependenciesWithXsl($node, $content) === false) {
            if (!isset($GLOBALS['parsingDependenciesError']) or !$GLOBALS['parsingDependenciesError']) {
                $GLOBALS['parsingDependenciesError'] = 'Can\'t build the dependencies with related XSL templates';
            }
            return false;
        }
        if (self::buildDependenciesWithAssetsAndLinks($node, $content, $idVersion) === false) {
            if (!isset($GLOBALS['parsingDependenciesError']) or !$GLOBALS['parsingDependenciesError']) {
                $GLOBALS['parsingDependenciesError'] = 'Can\'t build the dependencies with related Assets and links nodes';
            }
            return false;
        }
        return true;
    }

    /**
     * Search Dependencies in $content for the css $node and save it.
     *
     * @param Node $node
     * @param null $content
     * @return bool
     */
    public static function parseCssDependencies($node, $content = NULL)
    {
        if (is_numeric($node)) {
            $node = new Node($node);
        }
        $patron = "/url\(([^\)]*)?\)/";
        $type = 'ASSET';
        $matches = array();
        $idNode = $node->getID();
        if (!($node->get('IdNode') > 0)) {
            Logger::error('Error while node loading');
            return false;
        }
        $version = $node->getVersion();
        $idServer = $node->getServer();
        $server = new Node($idServer);
        $server_path = $server->getPath();

        // Delete previous dependencies
        $nodeDependencies = new NodeDependencies();
        $nodeDependencies->deleteBySource($idNode);
        $dependencies = new Dependencies();
        $dependencies->deleteMasterNodeandType($idNode, $type);
        $depsMngr = new DepsManager();
        $depsMngr->deleteBySource(DepsManager::NODE2ASSET, $idNode);

        // Search for images inside the css file content
        preg_match_all($patron, $content, $matches);
        if (!empty($matches) && !empty($matches[1])) {
            $images = array_unique(array_values($matches[1]));

            // Inserting new dependencies between css file and images
            foreach ($images as $_image) {
                $dependencies = new Dependencies();
                $pathImage = preg_replace("/\.\.(\/\.\.)*/", $server_path, $_image);
                $idNodeDep = self::_getIdNode($pathImage);
                $res = $dependencies->find('IdDep', 'IdNodeMaster = %s and IdNodeDependent = %s', array(
                    $idNode,
                    $idNodeDep
                ), MONO);
                if (empty($res)) {
                    $dependencies->insertDependence($idNode, $idNodeDep, $type, $version);
                    $nodeDependencies->set($idNode, $idNodeDep, NULL);
                    $depsMngr->set(DepsManager::NODE2ASSET, $idNode, $idNodeDep);
                }
            }
        }
        return true;
    }

    /**
     * Save dependencies for channels, languages and schema
     *
     * @param $node
     * @param $structuredDocument
     * @return array
     */
    private static function buildDependenciesFromStructuredDocument($node, $structuredDocument)
    {
        $channels = $structuredDocument->GetChannels();
        $schemas = (array)$structuredDocument->get('IdTemplate');
        $languages = (array)$structuredDocument->get('IdLanguage');
        if (!self::addDependencies($node, $channels, "channel")) {
            $GLOBALS['parsingDependenciesError'] = 'Can\'t add the dependencies for channels';
            return false;
        }
        if (!self::addDependencies($node, $languages, "language")) {
            $GLOBALS['parsingDependenciesError'] = 'Can\'t add the dependencies for language';
            return false;
        }
        if (!self::addDependencies($node, $schemas, "schema")) {
            $GLOBALS['parsingDependenciesError'] = 'Can\'t add the dependencies for schemas';
            return false;
        }
        $result = array(
            "channels" => $channels,
            "languages" => $languages,
            "schemas" => $schemas
        );
        return $result;
    }

    /**
     * Search dependencies with ximlets in $content for $node and save these
     *
     * @param $node
     * @param $structuredDocument
     * @param $content
     * @return array
     */
    private static function buildDependenciesWithXimlets($node, $structuredDocument, $content)
    {
        $sectionXimlets = self::getSectionXimlets($node, $structuredDocument->get('IdLanguage'));
        $ximlets = self::getXimletsInContent($content);
        $ximlets = array_unique(array_merge($ximlets, $sectionXimlets));
        return self::addDependencies($node, $ximlets, "ximlet") ? $ximlets : false;
    }

    /**
     * Search dependencies with xsl templates in $content and save these
     *
     * @param $node
     * @param $content
     * @return array
     */
    private static function buildDependenciesWithXsl($node, $content)
    {
        $xslTemplates = self::getXslDependencies($node, $content);
        return self::addDependencies($node, $xslTemplates, "template") ? $xslTemplates : false;
    }

    /**
     * Get dependencies with any linkable element
     * This elements are getting from xml and transformed xml in every available channel
     *
     * @param Node $node master Node
     * @param String $content
     * @param $idVersion
     * @return array|bool
     */
    private static function buildDependenciesWithAssetsAndLinks($node, $content, $idVersion)
    {
        $dotDots = $pathTos = array();
        $idNode = $node->get("IdNode");
        $idServer = $node->getServer();
        $strDoc = new StructuredDocument($node->get("IdNode"));
        $channels = $strDoc->GetChannels();
        $transformer = $node->getProperty('Transformer');
        $assets = self::getAssets($content, $node->nodeType->get('Name'));
        $links = self::getLinks($content, $node->nodeType->get('Name'));
        $pipelineManager = new PipelineManager();
        $pathToByChannel = array();
        if ($node->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
            $process = 'HTMLToPrepared';
        } else {
            $process = 'StrDocToDexT';
        }

        // Transforming the content for each defined channel
        if ($channels)
            foreach ($channels as $idChannel) {

                // Transforming with the given content and no cache
                $postContent = $pipelineManager->getCacheFromProcessAsContent($idVersion, $process, array(
                    'CHANNEL' => $idChannel,
                    'TRANSFORMER' => $transformer[0],
                    'DISABLE_CACHE' => true,
                    'CONTENT' => $content,
                    'NODEID' => $idNode
                ));

                // Post-transformation dependencies
                $pathToByChannel[$idChannel] = self::getPathTo($postContent, $idNode);
                if ($pathToByChannel[$idChannel]) {
                    $pathTos = array_merge($pathTos, $pathToByChannel[$idChannel]);
                }
                $res = self::getDotDot($postContent, $idServer);
                $dotDots = array_merge($dotDots, $res);
            }
        $links = array_unique(array_merge($assets, $links, $pathTos, $dotDots));

        // Add dependencies between nodes for every channel in NodeDependencies
        if (self::addIntoNodeDependencies($idNode, $pathToByChannel) === false) {
            return false;
        }
        return self::addDependencies($node, $links) ? $links : false;
    }

    /**
     * Save into NodeDependencies relations between nodes by channel
     * The relations depends on the transformation for a specific channel
     *
     * @param $idNode
     * @param $nodesByChannel
     */
    private static function addIntoNodeDependencies($idNode, $nodesByChannel)
    {
        $nodeDependencies = new NodeDependencies();
        foreach ($nodesByChannel as $idChannel => $nodes) {
            foreach ($nodes as $idDep) {
                if ($nodeDependencies->set($idNode, $idDep, $idChannel) === false) {
                    $GLOBALS['parsingDependenciesError'] = 'There is a problem to set the dependencies';
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get xsl Dependencies from Content
     *
     * @param $node
     * @param $content
     * @return array
     */
    private static function getXslDependencies($node, $content)
    {
        $section = new Node($node->GetSection());
        $sectionTemplates = new Node($section->GetChildByName('templates'));
        $project = new Node($node->GetProject());
        $projectTemplates = new Node($project->GetChildByName('templates'));
        $nodeType = new NodeType();
        $nodeType->SetByName('XslTemplate');

        // Gets document tags
        $domDoc = new DOMDocument();
        $domDoc->validateOnParse = true;
        if (@$domDoc->loadXML(\Ximdex\XML\Base::recodeSrc("<docxap>$content</docxap>", \Ximdex\XML\XML::UTF8)) === false) {
            return false;
        }
        $xpath = new DOMXPath($domDoc);
        $nodeList = $xpath->query('//*');

        // Searchs xslt nodes
        $xslDependencies = array();
        $parsedTags = array();
        foreach ($nodeList as $element) {
            $tagName = $element->nodeName;
            if (!in_array($tagName, $parsedTags)) {
                $xsltId = $sectionTemplates->GetChildByName($tagName . '.xsl');

                // If not found in section it searchs in project
                if (!($xsltId > 0)) {
                    $xsltId = $projectTemplates->GetChildByName($tagName . '.xsl');
                }
                if ($xsltId > 0) {
                    $xslDependencies[] = $xsltId;
                }
                $parsedTags[] = $tagName;
            }
        }
        return $xslDependencies;
    }

    /**
     * Remove from Data Base every dependence for current node
     *
     * @param Node $node Node master
     * @return bool
     */
    private static function clearDependencies($node)
    {
        // deletes old dependency (if exists)
        $idNode = $node->get("IdNode");
        $nodeDependencies = new NodeDependencies();
        $nodeDependencies->deleteBySource($idNode);
        $strDoc = new StructuredDocument($idNode);
        $version = is_null($strDoc->GetLastVersion()) ? 0 : $strDoc->GetLastVersion();

        // Removing all dependencies from Dependencies Table
        $dependencies = new Dependencies();
        $dependencies->deleteByMasterAndVersion($idNode, $version);
        $depsMngr = new DepsManager();
        if (!$depsMngr->deleteBySource(DepsManager::STRDOC_TEMPLATE, $idNode)) {
            return false;
        }
        if (!$depsMngr->deleteBySource(DepsManager::NODE2ASSET, $idNode)) {
            return false;
        }
        if (!$depsMngr->deleteBySource(DepsManager::XML2XML, $idNode)) {
            return false;
        }
        return true;
    }

    /**
     * Add into Data Base every dependence between master and dependent nodes.
     *
     * @param int $master
     * @param array /int $idDeps
     * @param string $type
     * @return bool
     */
    private static function addDependencies($master, $idDeps, $type = null)
    {
        $result = true;
        $idMaster = $master->get("IdNode");
        if (!is_array($idDeps)) {
            $idDeps = (array)$idDeps;
        }
        if (count($idDeps)) {
            foreach ($idDeps as $idDep) {
                if ($idMaster == $idDeps) {
                    continue;
                }
                if (!$idDep) {
                    Logger::error('Cannot add dependencie without parameter idDep for master node ID: ' . $idMaster);
                    continue;
                }
                $dependencies = new Dependencies();
                $depsMngr = new DepsManager();
                $table = false;

                // If we don't know the type, we'll get it from IdNodeType
                $currentType = $type ? strtolower($type) : self::inferType($idDep);
                if ($currentType) {
                    switch ($currentType) {
                        case Dependencies::ASSET:
                            $table = DepsManager::NODE2ASSET;
                            break;
                        case Dependencies::TEMPLATE:
                            $table = DepsManager::STRDOC_TEMPLATE;
                            break;
                        case Dependencies::XIMLET:
                        case Dependencies::XML:
                            $table = DepsManager::XML2XML;
                            break;
                    }
                    $dependencies->insertDependence($idMaster, $idDep, $currentType);
                    if ($table) {
                        $result = $depsMngr->set($table, $idMaster, $idDep) && $result;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Infer the type for dependence of $idNode
     *
     * @param int $idNode
     * @return bool|string
     */
    private static function inferType($idNode)
    {
        $type = false;
        $depNode = new Node($idNode);
        if ($depNode->get('IdNode') > 0) {
            switch ((int)$depNode->get("IdNodeType")) {
                case NodeTypeConstants::LINK:
                    $type = Dependencies::XIMLINK;
                    break;
                case NodeTypeConstants::CSS_FILE:
                case NodeTypeConstants::BINARY_FILE:
                case NodeTypeConstants::TEXT_FILE:
                case NodeTypeConstants::NODE_HT:
                case NodeTypeConstants::IMAGE_FILE:
                case NodeTypeConstants::JS_FILE:
                    $type = Dependencies::ASSET;
                    break;
                case NodeTypeConstants::XIMLET_CONTAINER:
                case NodeTypeConstants::XIMLET:
                    $type = Dependencies::XIMLET;
                    break;
                case NodeTypeConstants::XML_DOCUMENT:
                case NodeTypeConstants::METADATA_DOCUMENT:
                case NodeTypeConstants::HTML_DOCUMENT:
                    $type = Dependencies::XML;
                    break;
                case NodeTypeConstants::XSL_TEMPLATE:
                    $type = Dependencies::TEMPLATE;
                    break;
                default:
                    break;
            }
        }
        return $type;
    }

    private static function getLinks($content, $nodeTypeName = NULL)
    {
        preg_match_all('/ a_enlaceid[_|\w|\d]*\s*=\s*[\'|"](\d)(,\d)?[\'|"]/i', $content, $matches);
        $links = count($matches[1]) > 0 ? $matches[1] : array();
        preg_match_all('/ a_import_enlaceid[_|\w|\d]*\s*=\s*[\'|"](\d+)[\'|"]/i', $content, $matches);
        $importLinks = count($matches[1]) > 0 ? $matches[1] : array();
        return array_merge($links, $importLinks);
    }

    private static function getAssets($content, $nodeTypeName = NULL)
    {
        preg_match_all('/<url.*>\s*(\d+)\s*<\/url>/i', $content, $matches);
        $assets = count($matches[1]) > 0 ? $matches[1] : array();
        return $assets;
    }

    public static function getDotDot($content, $idServer)
    {
        preg_match_all("/@@@RMximdex\.dotdot\((css|common)([^\)]*)\)@@@/", $content, $matches);
        $result = array();
        if (count($matches[1]) > 0) {
            $serverNode = new Node($idServer);
            $idCssFolder = $serverNode->GetChildByName('css');
            $idCommonFolder = $serverNode->GetChildByName('common');
            $cssNode = new Node($idCssFolder);
            $commonNode = new Node($idCommonFolder);
            $css = $common = array();
            foreach ($matches[1] as $n => $match) {
                switch ($match) {
                    case 'css':
                        $id = $cssNode->GetChildByName(substr($matches[2][$n], 1));
                        if (!($id > 0)) {
                            $error = "CSS file {$matches[2][$n]} not found";
                            Logger::error($error);
                            $GLOBALS['parsingDependenciesError'] = $error;
                        } else {
                            $css[] = $id;
                        }
                        break;
                    case 'common':
                        $id = $commonNode->GetChildByName(substr($matches[2][$n], 1));
                        if (!($id > 0)) {
                            $error = "Common file {$matches[2][$n]} not found";
                            Logger::error($error);
                            $GLOBALS['parsingDependenciesError'] = $error;
                        } else {
                            $common[] = $id;
                        }
                        break;
                    default:
                        break;
                }
            }
            $result = array_merge($css, $common);
        }
        return $result;
    }

    /**
     * Get an array with the NodeId values of PathTo links in the content given
     * If the $test param is passed with true value, return true if the nodes linked exists in nodes database (now is inter-projects)
     *
     * @param string $content
     * @param integer $nodeId
     * @param string $test
     * @return boolean|array
     */
    public static function getPathTo($content, $nodeId)
    {
        preg_match_all("/@@@RMximdex\.pathto\(([^\)]*)\)@@@/", $content, $matches);
        $links = array();
        if (count($matches[1])) {
            $node = new Node($nodeId);
            $server = $node->getServer();
            $parserPathTo = new ParsingPathTo();
            foreach ($matches[1] as $pathTo) {

                // If the document is a template in the project templates node, the resources in the macros (not nodeId given) cannot be obtained
                if (($server !== null or is_numeric($pathTo)) and ($parserPathTo->parsePathTo($pathTo, $nodeId) === false)) {
                    $error = 'The document or its dependencies references a non existant node or resource (' . $pathTo . ') in a RMximdex.pathto directive';
                    Logger::warning($error);
                    $GLOBALS['parsingDependenciesError'] = $error;
                } else {
                    $links[$parserPathTo->getIdNode()] = $parserPathTo->getIdNode();
                }
            }
        }
        return $links;
    }

    /**
     * @param $_path
     * @return null|int
     */
    private static function _getIdNode($_path)
    {
        // Building file and path
        $file = pathinfo($_path);
        $filename = $file["filename"] . "." . $file['extension'];
        $path = $file["dirname"];

        // Searching in Nodes by name and path
        $node = new Node();
        $foundNodes = $node->find("IdNode", "Path =%s and Name=%s", array(
            $path,
            $filename
        ), MONO);
        if ($foundNodes and is_array($foundNodes) && count($foundNodes)) {
            return $foundNodes[0];
        }
        return null;
    }

    /**
     * Checks if section has ximlet dependencies and returns these dependencies
     *
     * @param int $idSection
     * @param int $idLanguage
     * @return array
     */
    private static function getSectionXimlets($node, $idLanguage)
    {
        $sectionId = $node->getSection();
        $depsManager = new DepsManager();
        $ximletContainers = $depsManager->getBySource(DepsManager::SECTION_XIMLET, $sectionId);
        if (empty($ximletContainers)) {
            return array();
        }
        foreach ($ximletContainers as $idXimletContainer) {
            $ximletContainer = new Node($idXimletContainer);
            $ximlets = $ximletContainer->GetChildren();
            foreach ($ximlets as $ximletId) {
                $ximlet = new StructuredDocument($ximletId);
                if ($ximlet->get('IdLanguage') == $idLanguage) {
                    $ximlets[] = $ximletId;
                }
            }
        }
        return count($ximlets) > 0 ? $ximlets : array();
    }

    /**
     * Find ximlets id in content by Regexp ximlet([0-9]+)
     *
     * @param String $content
     * @return array Dependencies found.
     */
    private static function getXimletsInContent($content)
    {
        preg_match_all('/ximlet\((\d+)\)/i', $content, $matches);
        return count($matches[1]) > 0 ? $matches[1] : array();
    }
}