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

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Constants;
use Ximdex\Runtime\Db;

if (! defined('ROOT_NODE')) {
    define('ROOT_NODE', 1);
}

/**
 * @brief Includes the fundamental methods for handling Nodes in ximDEX
 */
class Root
{
    /**
     * @var Node
     */
    var $parent;
    
    /**
     * @var bool|string
     */
    var $nodeID;
    
    /**
     * @var Db
     */
    var $dbObj;
    
    /**
     * @var \Ximdex\Models\NodeType
     */
    var $nodeType;
    
    /**
     * @var
     */
    var $numErr;
    
    /**
     * @var
     */
    var $msgErr;
    
    /**
     * @var \Ximdex\Utils\Messages
     */
    var $messages;
    
    /**
     * @var array
     */
    var $errorList = array();

    /**
     * Root constructor
     * 
     * @param int|Node $node
     */
    public function __construct($node = null)
    {
        if (is_object($node)) {
            $this->parent = $node;
        }
        elseif (is_numeric($node) || $node == null) {
            $this->parent = new Node($node, false);
        }
        $this->nodeID = $this->parent->get('IdNode');
        $this->dbObj = new \Ximdex\Runtime\Db();
        $this->nodeType = $this->parent->nodeType;
        $this->messages = new \Ximdex\Utils\Messages();
    }

    /**
     * Gets the MetaType of a NodeType.
     * @return string|null
     */
    public function getMetaType()
    {
        $metaTypesArray = Constants::$METATYPES_ARRAY;
        $class = get_class($this);
        if (isset($metaTypesArray[strtoupper($class)])) {
            return $metaTypesArray[strtoupper($class)];
        }
        return NULL;
    }

    /**
     * Gets the path relating to its Project of the Node in the filesystem.
     * 
     * @return string
     */
    public function GetPathList()
    {
        $idParentNode = $this->parent->get('IdParent');
        $parentNode = new Node($idParentNode);
        if (! $parentNode->get('IdNode')) {
            return "";
        }
        $parentPath = $parentNode->class->getPathList();
        if (! $this->nodeType->GetIsRenderizable()) {
            Logger::warning('Se ha solicitado el path de un nodo no renderizable con id ' . $this->parent->get('IdNode'));
            return $parentPath;
        }
        
        /*
        Obtenemos el path donde el nodo padre guarda a sus hijos
        Unimos el path del padre y el nombre del nodo para obtener el path de este nodo si este nodo no es virtual
        */
        if ($this->nodeType->GetHasFSEntity()) {
            
            // CON ENTIDAD EN EL FS O NO VIRTUAL (ROOT, XML, IMAGES)
            return $parentPath . "/" . $this->parent->get('Name');
        }
        return $parentPath;
    }

    /**
     * Gets the path for storing the Node children
     */
    public function getChildrenPath()
    {
        return $this->GetPathList();
    }

    /**
     * Clears the error messages
     */
    public function clearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }

    /**
     * Sets an error (code and message)
     */
    public function setError(int $code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    /**
     * Checks if has happened any error
     * 
     * @return bool
     */
    public function hasError()
    {
        return ($this->numErr != null);
    }

    /**
     * Builds a XML wich contains the properties of the Node
     * 
     * @param int - depth
     * @param array - files
     * @param bool - recurrence
     * @return string
     */
    public function toXml(int $depth, array & $files, bool $recurrence = false)
    {
        return '';
    }

    /**
     * Returns a xml fragment
     * 
     * @return string
     */
    public function getXmlTail()
    {
        return '';
    }

    /**
     * Gets all channels which transform the Node
     * 
     * @return array
     */
    public function getChannels()
    {
        return array();
    }

    /**
     * Gets the content of the Node
     * 
     * @return string
     */
    public function getContent()
    {
        return '';
    }

    public function setContent(string $content, bool $commitNode = false) : bool
    {
        return true;
    }

    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null)
    {
        $this->updatePath();
        return true;
    }
    
    public function deleteNode() : bool
    {
        return true;
    }

    /**
     * @return bool|string
     */
    public function canDenyDeletion()
    {
        return $this->parent->nodeType->get('CanDenyDeletion');
    }

    public function getDependencies() : array
    {
        return array();
    }

    /**
     * Think in root node as a file for performance purposes
     * This method is overwritten in FileNode and FolderNode
     */
    public function updatePath()
    {
        $node = new Node($this->nodeID);
        $path = pathinfo($node->GetPath());
        $db = new \Ximdex\Runtime\Db();
        return $db->execute(sprintf('update Nodes set Path = \'%s\' where IdNode = %s', $path['dirname'], $this->nodeID));
    }

    /**
     * Changes the name of the Node
     * 
     * @param string name
     * @return boolean
     */
    public function renameNode(string $name) : bool
    {
        $this->updatePath();
        return true;
    }

    /**
     * Gets the Url of the Node
     * 
     * @return string
     */
    public function getNodeURL()
    {
        return App::getValue('UrlRoot') . App::getValue("NodeRoot") . $this->GetPathList();
    }

    /**
     * Gets all Nodes of a given NodeType
     * 
     * @return array|null
     */
    public function getAll()
    {
        $query = sprintf("SELECT IdNode,Name FROM Nodes WHERE IdNodeType = %d", $this->parent->get('IdNodeType'));
        $this->dbObj->query($query);
        $return = null;
        while (! $this->dbObj->EOF) {
            $return[$this->dbObj->getValue('IdNode')] = $this->dbObj->getValue('Name');
            $this->dbObj->next();
        }
        return $return;
    }

    /**
     * Gets the name in which the Node will be published.
     *  
     * @param int channel
     * @return string
     */
    public function getPublishedNodeName(int $channel = null)
    {
        return $this->parent->get('Name');
    }

    /**
     * Gets the path of the Node in the data / nodes directory
     * 
     * @return string
     */
    public function getNodePath()
    {
        $pathList = $this->GetPathList();
        $relativePath = $pathList;
        return XIMDEX_ROOT_PATH . App::getValue("NodeRoot") . $relativePath;
    }

    /**
     * Checks whether the NodeType has the is_section_index property
     * 
     * @return null
     */
    public function getIndex()
    {
        return null;
    }

    public function getPublishedPath(int $channelID = null, bool $addNodeName = false, bool $structure = false)
    {
        $db = new \Ximdex\Runtime\Db();
        $nodes = array();
        $query = sprintf("SELECT n.IdNode"
            . " FROM `FastTraverse` ft"
            . " INNER JOIN Nodes n USING(IdNode)"
            . " INNER JOIN NodeTypes nt ON n.IdNodeType = nt.IdNodeType AND nt.IsVirtualFolder = 0"
            . " WHERE ft.`IdChild` = %s AND ft.`IdChild` != ft.`IdNode` order by ft.Depth DESC",
        $db->sqlEscapeString($this->parent->get('IdNode')));
        $db->query($query);
        while (! $db->EOF) {
            $node = new Node($db->getValue('IdNode'));
            $nodes[] = $node->GetPublishedNodeName($channelID);
            $db->next();
        }
        if ($addNodeName && !$this->nodeType->get('IsVirtualFolder')) {
            $parent = new Node($this->parent->get('IdNode'));
            $nodes[] = $parent->GetPublishedNodeName($channelID);
        }
        if ($structure) {
            return $nodes;
        }
        return '/' . implode('/', $nodes);
    }
}
