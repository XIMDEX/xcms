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

namespace Ximdex\NodeTypes;

use Ximdex\Runtime\DataFactory;
use DOMDocument;
use Ximdex\Deps\DepsManager;
use Ximdex\Models\NodeDependencies;
use Ximdex\Models\Version;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Parsers\ParsingDependencies;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;

/**
 * @brief Handles files
 *
 *  Files are located in data/files directory.
 *  Its versions are stored in the Versions table who is handle by the class DataFactory.
 *  Also a file copy is stored in data / nodes directory.
 */
class FileNode extends Root
{
    /**
     * Creates a file in data / nodes directory
     * 
     * @return bool
     */
    public function renderizeNode() : bool
    {
        if (! $this->parent->isRenderizable()) {
            return true;
        }
        $parentID = $this->parent->getParent();
        $parent = new Node($parentID);
        if (! $parent->isRenderized()) {
            $parent->renderizeNode();
        }
        $node = new Node($this->nodeID);
        $nodetype = new NodeType($node->getNodeType());
        if (! $nodetype->getHasFSEntity()){
            return false;
        }
        $file = $this->getNodePath();
        $data = new DataFactory($this->nodeID);
        $content = $data->getContent();
        if (! FsUtils::file_put_contents($file, $content)) {
            return false;
        }
        return true;
    }

    /**
     * Wrapper for GetContent
     * 
     * @param int $channel
     * @param string $content
     * @return string
     */
    public function getRenderizedContent(int $channel = null, string $content = null)
    {
        return $this->getContent();
    }

    /**
     * Adds a row to Versions table and creates the file
     * 
     * @param string name
     * @param int parentID
     * @param int nodeTypeID
     * @param int stateID
     * @param string sourcePath
     * @return bool|int
     */
    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $sourcePath = null)
    {
        if ($sourcePath) {
        	$content = FsUtils::file_get_contents($sourcePath);
        } else {
    		$content = '';
        }
        $data = new DataFactory($this->parent->get('IdNode'));
        $ret = $data->setContent($content);
        if ($ret === false) {
            $this->messages->add($data->msgErr, MSG_TYPE_ERROR);
        }
        $this->updatePath();
        return $ret;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::setContent()
     */
    public function setContent(string $content, bool $commitNode = false, Node $node = null) : bool
    {
        $data = new DataFactory($this->nodeID);
        
        // Not neccesary up version here for template nodetypes (makes previously for insert correct idversion in xml wrapper)
        if ($this->parent->nodeType->getID() == NodeTypeConstants::XSL_TEMPLATE) {
            $lastVersionID = $data->getLastVersionId();
            list($version, $subversion) = $data->getVersionAndSubVersion($lastVersionID);
            $data->setContent($content, $version, $subversion, $commitNode);
        } else {
            if ($this->parent->nodeType->getID() == NodeTypeConstants::RNG_VISUAL_TEMPLATE) {
                $dom = new DOMDocument();
                $dom->formatOutput = true;
                $dom->preserveWhiteSpace = false;
                if (@$dom->loadXML($content) !== false) {
                    $content = $dom->saveXML();
                }
            }
            $data->setContent($content, null, null, $commitNode);
        }
        if ($this->parent->nodeType->get('Name') == 'CssFile') {
            if (! $node) {
                $node = new Node($this->nodeID);
            }
            ParsingDependencies::parseCssDependencies($node, $content);
        }
        return true;
    }

    /**
     * Gets the content of the file
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::getContent()
     */
    public function getContent(int $version = null, int $subversion = null)
    {
        $data = new DataFactory($this->nodeID);
        $content = $data->getContent($version, $subversion);
        return $content;
    }

    /**
     * Gets the nodes that must be published together with the file
     * 
     * @return array
     */
    public function getDependencies() : array
    {
        $nodeDependencies = new NodeDependencies();
        return $nodeDependencies->getByTarget($this->nodeID);
    }

    /**
     * Builds a XML wich contains the properties of the file
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::ToXml()
     */
    public function toXml(int $depth, array & $files, bool $recursive = false)
    {
        $query = sprintf("SELECT File FROM `Versions` WHERE idNode = %d ORDER BY Version DESC, SubVersion DESC LIMIT 1",
        $this->parent->get('IdNode'));
        $this->dbObj->query($query);
        if (! $this->dbObj->numRows) {
            Logger::error("File version not found for node: " . $this->parent->get('IdNode'));
        } else {
            $nodeFile = $this->dbObj->getValue('File');
            $routeToFile = sprintf("%s/data/files/%s", XIMDEX_ROOT_PATH, $nodeFile);
            if (! in_array($routeToFile, $files)) {
                $files[] = $routeToFile;
            }
            $indexTabs = str_repeat("\t", $depth + 1);
            return sprintf("%s<path src=\"%s\" />\n", $indexTabs, $routeToFile);
        }
    }

    /**
     * Deletes the dependencies of file
     * 
     * @return bool
     */
    public function deleteNode() : bool
    {
        // Deletes dependencies in rel tables
        $depsMngr = new DepsManager();
        if ($depsMngr->deleteByTarget(DepsManager::NODE2ASSET, $this->parent->get('IdNode')) === false) {
            return false;
        }
        if ($depsMngr->deleteBySource(DepsManager::NODE2ASSET, $this->parent->get('IdNode')) === false) {
            return false;
        }
        Logger::info('Filenode dependencies deleted');
        return true;
    }

    /**
     * Gets the documents that must be publicated together with the file
     * 
     * @param array $params
     * @return array|NULL
     */
    public function getPublishabledDeps(array $params = []) : ?array
    {
        if ($this->parent->nodeType->get('Name') == 'CssFile') {
            $depsMngr = new DepsManager();
            $dependencies = $depsMngr->getBySource(DepsManager::NODE2ASSET, $this->nodeID);
            return $dependencies;
        }
        return null;
    }

    public function getLastVersionFile()
    {
        $data = new DataFactory($this->nodeID);
        $idVersion = $data->getLastVersionId();
        $version = new Version($idVersion);
        return $version->get('File');
    }

    public function updatePath()
    {
        $node = new Node($this->nodeID);
        $path = pathinfo($node->getPath());
        if (isset($path['dirname'])) {
            $db = new \Ximdex\Runtime\Db();
            $db->execute(sprintf("update Nodes set Path = '%s' where IdNode = %s", $path['dirname'], $this->nodeID));
        }
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\Root::renameNode()
     */
    public function renameNode(string $name) : bool
    {
        $this->updatePath();
        return true;
    }
    
    /**
     * Gets the minimal content of a document created by a template
     * 
     * @return string
     */
    public function buildDefaultContent()
    {
        return '';
    }
}
