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

namespace Ximdex\NodeTypes;

use Ximdex\Runtime\DataFactory;
use DOMDocument;
use Ximdex\Deps\DepsManager;
use Ximdex\Models\NodeDependencies;
use Ximdex\Models\State;
use Ximdex\Models\Version;
use Ximdex\Models\Node;
use Ximdex\Parsers\ParsingDependencies;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;

\Ximdex\Modules\Manager::file('/actions/fileupload/baseIO.php');
\Ximdex\Modules\Manager::file('/inc/CacheWriter.class.php', 'ximRAM');

/**
 * @brief Handles files.
 *
 *  Files are located in data/files directory.
 *  Its versions are stored in the Versions table who is handle by the class DataFactory.
 *  Also a file copy is stored in data/nodes directory.
 */
class FileNode extends Root
{
    /**
     * Creates a file in data/nodes directory.
     * 
     * @return bool
     */
    function RenderizeNode()
    {
        $parentID = $this->parent->GetParent();
        $parent = new Node($parentID);
        if (!$parent->IsRenderized()) {
            $parent->RenderizeNode();
        }
        $node = new Node($this->nodeID);
        $nodetype = new \Ximdex\Models\NodeType($node->GetNodeType());
        if (!$nodetype->GetHasFSEntity()){
            return false;
        }
        $file = $this->GetNodePath();
        $data = new DataFactory($this->nodeID);
        $content = $data->GetContent();

        // If exists, it would be deleted
        if (file_exists($file)) {
            FsUtils::delete($file);
        }

        // And created again
        FsUtils::file_put_contents($file, $content);
        return true;
    }

    /**
     * Wrapper for GetContent.
     *  
     * @param int channel
     * @param string content
     * @return string
     */
    function getRenderizedContent($channel = NULL, $content = NULL)
    {
        return $this->GetContent();
    }

    /**
     * Adds a row to Versions table and creates the file.
     * 
     * @param string name
     * @param int parentID
     * @param int nodeTypeID
     * @param int stateID
     * @param string sourcePath
     * @return bool
     */
    function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $sourcePath = null)
    {
        if ($sourcePath) {
        	$content = FsUtils::file_get_contents($sourcePath);
        }
        else {
    		$content = '';
        }
        $data = new DataFactory($this->parent->get('IdNode'));
        $ret = $data->SetContent($content);
        if ($ret === false) {
            $this->messages->add($data->msgErr, MSG_TYPE_ERROR);
        }
        $this->updatePath();
        return $ret;
    }

    /**
     * Stores a content on the file located.
     * 
     * @param string content
     * @return string
     */
    function SetContent($content, $commitNode = NULL, Node $node = null)
    {
        $data = new DataFactory($this->nodeID);

        // @todo: move this piece to Template nodetype
        // Not neccesary up version here for template nodetypes (makes previously for insert correct idversion in xml wrapper)
        if ($this->parent->nodeType->GetID() == \Ximdex\NodeTypes\NodeTypeConstants::XSL_TEMPLATE) {
            $lastVersionID = $data->GetLastVersionId();
            list($version, $subversion) = $data->GetVersionAndSubVersion($lastVersionID);
            $data->SetContent($content, $version, $subversion, $commitNode);
        }
        else {
            if ($this->parent->nodeType->GetID() == \Ximdex\NodeTypes\NodeTypeConstants::RNG_VISUAL_TEMPLATE)
            {
                $dom = new DOMDocument();
                $dom->formatOutput = true;
                $dom->preserveWhiteSpace = false;
                if (@$dom->loadXML($content) !== false) {
                    $content = $dom->saveXML();
                }
            }
            $data->SetContent($content, NULL, NULL, $commitNode);
        }
        if ($this->parent->nodeType->get('Name') == 'CssFile') {
            ParsingDependencies::parseCssDependencies($this->nodeID, $content);
        }
        return true;
    }

    /**
     * Gets the content of the file.
     * 
     * @return string
     */
    function GetContent()
    {
        $data = new DataFactory($this->nodeID);
        $content = $data->GetContent();
        return $content;
    }

    /**
     * Gets the nodes that must be published together with the file.
     * 
     * @return array
     */
    function GetDependencies()
    {
        $nodeDependencies = new NodeDependencies();
        return $nodeDependencies->getByTarget($this->nodeID);
    }

    /**
     * Builds a XML wich contains the properties of the file.
     * 
     * @param int depth
     * @param array files
     * @param bool recurrence
     */
    function ToXml($depth, & $files, $recurrence)
    {
        $query = sprintf("SELECT File FROM `Versions` WHERE idNode = %d ORDER BY Version DESC, SubVersion DESC LIMIT 1",
        $this->parent->get('IdNode'));
        $this->dbObj->Query($query);
        if (!$this->dbObj->numRows > 0) {
            Logger::error("***************** File version not found -->" . $this->parent->get('IdNode'));
        }
        else {
            $nodeFile = $this->dbObj->GetValue('File');
            $routeToFile = sprintf("%s/data/files/%s", XIMDEX_ROOT_PATH, $nodeFile);
            if (!in_array($routeToFile, $files)) $files[] = $routeToFile;
            $indexTabs = str_repeat("\t", $depth + 1);
            return sprintf("%s<path src=\"%s\" />\n", $indexTabs, $routeToFile);
        }
    }

    /**
     * Deletes the dependencies of file.
     * 
     * @return bool
     */
    function DeleteNode()
    {
        // Deletes dependencies in rel tables
        $depsMngr = new DepsManager();
        $result = $depsMngr->deleteByTarget(DepsManager::NODE2ASSET, $this->parent->get('IdNode'));
        $result = $depsMngr->deleteBySource(DepsManager::NODE2ASSET, $this->parent->get('IdNode')) && $result;
        if ($result) {
            Logger::info('Filenode dependencies deleted');
        }
        return $result;
    }

    /**
     * Gets the documents that must be publicated together with the file
     * 
     * @param array $params
     * @return null|array
     */
    function getPublishabledDeps($params)
    {
        if ($this->parent->nodeType->get('Name') == 'CssFile') {
            $depsMngr = new DepsManager();
            $dependencies = $depsMngr->getBySource(DepsManager::NODE2ASSET, $this->nodeID);
            return $dependencies;
        }
        return NULL;
    }

    function getLastVersionFile()
    {
        $data = new DataFactory($this->nodeID);
        $idVersion = $data->GetLastVersionId();
        $version = new Version($idVersion);
        return $version->get('File');
    }

    function UpdatePath()
    {
        $node = new Node($this->nodeID);
        $path = pathinfo($node->GetPath());
        if (isset($path['dirname'])) {
            $db = new \Ximdex\Runtime\Db();
            $db->execute(sprintf("update Nodes set Path = '%s' where IdNode = %s", $path['dirname'], $this->nodeID));
        }
    }

    function RenameNode($name = null)
    {
        $this->updatePath();
    }

    /**
     * Promotes the File to the next workflow state.
     * 
     * @param string newState
     * @return bool
     */
    public function promoteToWorkFlowState($newState)
    {
        $state = new State();
        $idState = $state->loadByName($newState);
        $idActualState = $this->parent->GetState();
        if ($idState == $idActualState) {
            Logger::warning('It have requested to pass to an status, and that status is now the current one');
            return true;
        }
        $actualState = new State($idActualState);
        baseIO_CambiarEstado($this->nodeID, $idState);
        $lastState = new State();
        $idLastState = $lastState->loadLastState();
        if ($idState == $idLastState) {
            $up = time();
            $down = $up + 36000000; // unpublish date = dateup + 1year
            baseIO_PublishDocument($this->nodeID, $up, $down, null);
        }
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