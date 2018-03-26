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

namespace Ximdex\Models;

use DOMDocument;
use Ximdex\Deps\DepsManager;
use Ximdex\Logger;
use Ximdex\Models\ORM\NodesOrm;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\XmlDocumentNode;
use Ximdex\Nodeviews\ViewFilterMacros;
use Ximdex\Parsers\ParsingDependencies;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\PipelineManager;
use Ximdex\Runtime\Session;
use Ximdex\Sync\Synchronizer;
use Ximdex\Workflow\WorkFlow;
use Ximdex\XML\Base;
use Ximdex\XML\XML;

define('DETAIL_LEVEL_LOW', 0);
define('DETAIL_LEVEL_MEDIUM', 1);
define('DETAIL_LEVEL_HIGH', 2);

if (! defined('COUNT')) {
    define('COUNT', 0);
    define('NO_COUNT', 1);
    define('NO_COUNT_NO_RETURN', 2);
}

/**
 * Class Node
 *
 * @package Ximdex\Models
 */
class Node extends NodesOrm
{
    /**
     *
     * @var bool|string
     */
    var $nodeID;

    // current node ID.
    
    /**
     *
     * @var mixed
     */
    var $class;

    // Class which implements the specific methos for this nodetype.
    
    /* @var $nodeType \Ximdex\Models\NodeType */
    var $nodeType;

    // nodetype object.
    
    /* @var $dbObj \Ximdex\Runtime\Db */
    var $dbObj;

    // DB object which will be used in the methods.
    
    /**
     *
     * @var
     */
    var $numErr;

    // Error code.
    
    /**
     *
     * @var
     */
    var $msgErr;

    // Error message.
    
    /**
     *
     * @var array
     */
    var $errorList = array();

    // Class error list.
    
    /**
     * Node constructor.
     *
     * @param null $nodeID
     * @param bool $fullLoad
     */
    public function __construct($nodeID = null, $fullLoad = true)
    {
        $this->errorList[1] = _('The node does not exist');
        $this->errorList[2] = _('The nodetype does not exist');
        $this->errorList[3] = _('Arguments missing or invalid');
        $this->errorList[4] = _('Some of the children could not be deleted');
        $this->errorList[5] = _('Database connection error');
        $this->errorList[6] = _('No root in tree');
        $this->errorList[7] = _('Error accessing to file system');
        $this->errorList[8] = _('A node with the given name already exists');
        $this->errorList[9] = _('Invalid name format');
        $this->errorList[10] = _('Parent node does not exist');
        $this->errorList[11] = _('The nodetype does not exist');
        $this->errorList[12] = _('The node cannot be moved to an own internal node');
        $this->errorList[13] = _('The node cannot be deleted');
        $this->errorList[14] = _('It is not located under the given node');
        $this->errorList[15] = _('A master node cannot link other');
        $this->errorList[16] = _('A node cannot link itself');
        $this->errorList[17] = _('This node is not allowed in this position');
        $this->flagErr = FALSE;
        $this->autoCleanErr = TRUE;
        parent::__construct($nodeID);
        // In order to do not breack compatibility with previous version
        if ($this->get('IdNode') > 0) {
            $this->nodeID = $this->get('IdNode');
        }
        if ($this->get('IdNodeType') > 0) {
            $this->nodeType = new NodeType($this->get('IdNodeType'));
            if ($this->nodeType->get('IdNodeType') > 0) {
                $nodeTypeClass = $this->nodeType->get('Class');
                $nodeTypeModule = $this->nodeType->get('Module');
                $this->class = \Ximdex\NodeTypes\Factory::getNodeTypeByName($nodeTypeClass, $this, $nodeTypeModule);
                if (! $fullLoad) {
                    return;
                }
            }
        }
    }

    /**
     *
     * @return null
     */
    function GetRoot()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query("SELECT IdNode FROM Nodes WHERE IdParent IS null");
        if ($dbObj->numRows) {
            return $dbObj->GetValue('IdNode');
        }
        
        $this->SetError(6);
        return NULL;
    }

    /**
     *
     * @return bool|string
     */
    function GetID()
    {
        return $this->get('IdNode');
    }

    /**
     *
     * @param null $nodeID
     */
    function SetID($nodeID = null)
    {
        $this->ClearError();
        self::__construct($nodeID);
    }

    /**
     *
     * @return bool|string
     */
    function GetNodeName()
    {
        $this->ClearError();
        return $this->get('Name');
    }

    /**
     * Returns the list of paths relative to project, of all the files and directories with belong to the node in the file system
     *
     * @param null $channel
     * @return null
     */
    function GetPublishedNodeName($channel = null)
    {
        $this->ClearError();
        if (! ($this->get('IdNode') > 0)) {
            $this->SetError(1);
            return NULL;
        }
        return $this->class->GetPublishedNodeName($channel);
    }

    /**
     * Changes node name
     * 
     * @param $name
     * @return boolean
     */
    function SetNodeName($name)
    {
        // it is a renamenode alias
        return $this->RenameNode($name);
    }

    /**
     * Returns the nodetype ID
     *
     * @return bool|string
     */
    function GetNodeType()
    {
        return $this->get('IdNodeType');
    }

    /**
     *
     * @return bool|string
     */
    function GetTypeName()
    {
        return $this->nodeType->get('Name');
    }

    /**
     * Changes the nodetype
     * 
     * @param $nodeTypeID
     * @return boolean|number|NULL|string
     */
    function SetNodeType($nodeTypeID)
    {
        if (! ($this->get('IdNode') > 0)) {
            $this->SetError(2);
            return false;
        }
        
        $result = $this->set('IdNodeType', $nodeTypeID);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the node description
     *
     * @return bool|string
     */
    function GetDescription()
    {
        return $this->get('Description');
    }

    /**
     * Changes the node description
     * 
     * @param $description
     * @return boolean|boolean|number|NULL|string
     */
    function SetDescription($description)
    {
        if (! ($this->get('IdNode') > 0)) {
            $this->SetError(2);
            return false;
        }
        
        $result = $this->set('Description', $description);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the node state
     *
     * @return bool|string
     */
    function GetState()
    {
        $this->ClearError();
        return $this->get('IdState');
    }

    /**
     * Changes the node workflow state
     * 
     * @param $stateID
     * @return boolean
     */
    function SetState($stateID)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        if (($this->get('IdNode') > 0)) {
            $sql = sprintf("UPDATE Nodes SET IdState= %d WHERE IdNode=%d OR SharedWorkflow = %d", $stateID, $this->get('IdNode'), $this->get('IdNode'));
            
            $result = $dbObj->Execute($sql);
            if ($result) {
                return true;
            }
        }
        
        $this->messages->add(sprintf(_('The node could not be moved to state %s'), $stateID), MSG_TYPE_ERROR);
        return false;
    }

    /**
     * Returns the node icon
     *
     * @return bool|null|string
     */
    function GetIcon()
    {
        $this->ClearError();
        if (($this->get('IdNode') > 0)) {
            if (method_exists($this->class, 'GetIcon')) {
                return $this->class->GetIcon();
            }
            
            return $this->nodeType->GetIcon();
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Returns the list of channels for the node
     *
     * @return null
     */
    function GetChannels()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->GetChannels($this->GetID());
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Returns the node parent ID
     *
     * @return bool|string
     */
    function GetParent()
    {
        $this->ClearError();
        return $this->get('IdParent');
    }

    /**
     * Changes the node parent
     * 
     * @param $parentID
     * @return boolean
     */
    function SetParent($parentID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $children = $this->GetChildByName($this->get('Name'));
            if (! empty($children)) {
                $this->SetError(8);
                return false;
            }
            $this->set('IdParent', $parentID);
            $result = $this->update();
            if (! $result)
                $this->messages->add(_('Node could not be moved'), MSG_TYPE_ERROR);
            $this->msgErr = _('Node could not be moved');
            $this->numErr = 1;
        }
        return true;
    }

    /**
     * Returns the list of node children
     *
     * @param $idtype
     * @param $order
     * @return array
     */
    function GetChildren($idtype = null, $order = null)
    {
        if (! $this->get('IdNode')) {
            return array();
        }
        
        $where = 'IdParent = %s';
        $params = array(
            $this->get('IdNode')
        );
        
        if (! empty($idtype)) {
            $where .= ' AND IdNodeType = %s';
            $params[] = $idtype;
        }
        
        $validDirs = array(
            'ASC',
            'DESC'
        );
        if (! empty($order) && is_array($order) && isset($order['FIELD'])) {
            $where .= sprintf(" ORDER BY %s %s", $order['FIELD'], isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
        }
        
        return $this->find('IdNode', $where, $params, MONO);
    }

    /**
     * Returns a node list with the info for treedata
     *
     * @param null $idtype
     * @param null $order
     * @return array
     */
    function GetChildrenInfoForTree($idtype = null, $order = null)
    {
        $validDirs = array(
            'ASC',
            'DESC'
        );
        
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $childrenList = array();
            $sql = "select N.IdNode, N.Name as name,NT.System FROM Nodes as N inner join NodeTypes as NT on N.IdNodeType = NT.IdNodeType";
            $sql .= " WHERE NOT(NT.IsHidden) AND IdParent =" . $this->get('IdNode');
            
            if ($idtype) {
                $sql .= " AND IdNodeType = $idtype";
            }
            
            if (! empty($order) && is_array($order) && isset($order['FIELD'])) {
                $sql .= sprintf(" ORDER BY %s %s", $order['FIELD'], isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
            }
            
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Query($sql);
            $i = 0;
            while (! $dbObj->EOF) {
                $childrenList[$i]['id'] = $dbObj->GetValue('IdNode');
                $childrenList[$i]['name'] = $dbObj->GetValue('name');
                $childrenList[$i]['system'] = $dbObj->GetValue('System');
                $i ++;
                $dbObj->Next();
            }
            return $childrenList;
        } else {
            $this->SetError(1);
            return array();
        }
    }
    
    /**
     * Looks for a child node with same name
     * 
     * @param $name : optional. If none is passed, considered name will be current node name
     * @return NULL|string|boolean
     */
    function GetChildByName($name = NULL)
    {
        if (empty($name)) {
            $name = $this->get('Name');
        }
        $this->ClearError();
        if (($this->get('IdParent') > 0) && ! empty($name)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf("SELECT IdNode FROM Nodes WHERE IdParent = %d AND Name = %s", $this->get('IdNode'), $dbObj->sqlEscapeString($name));
            
            $dbObj->Query($sql);
            if ($dbObj->numRows > 0) {
                return $dbObj->GetValue('IdNode');
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Looks for one or more children nodes with the given type
     *
     * @param int $type
     * @return NULL|string|boolean
     */
    function GetChildByType(int $type = null)
    {
        if (empty($type)) {
            $type = $this->GetNodeType();
        }
        $this->ClearError();
        if ($this->GetParent() and ! empty($type)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf("SELECT IdNode FROM Nodes WHERE IdParent = %d AND IdNodeType = %s", $this->GetID(), $type);
            $dbObj->Query($sql);
            if ($dbObj->numRows) {
                return $dbObj->GetValue('IdNode');
            }
        }
        return false;
    }

    /**
     * Looks for nodes by name
     *
     * @param string $name
     *            name, optional. If none is passed, considered name will be current node name
     * @return int/false
     */
    function GetByName($name = NULL)
    {
        if (empty($name)) {
            $name = $this->get('Name');
        }
        $this->ClearError();
        if (! empty($name)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf("SELECT Nodes.IdNode, Nodes.Name, NodeTypes.Icon, Nodes.IdParent FROM Nodes, NodeTypes WHERE Nodes.IdNodeType = NodeTypes.IdNodeType AND Nodes.Name like %s", $dbObj->sqlEscapeString("%" . $name . "%"));
            
            $dbObj->Query($sql);
            
            if ($dbObj->numRows > 0) {
                $result = array();
                
                while (! $dbObj->EOF) {
                    $node_t = new Node($dbObj->GetValue('IdNode'));
                    if ($node_t)
                        $children = count($node_t->GetChildren());
                    else
                        $children = 0;
                    
                    $result[] = array(
                        'IdNode' => $dbObj->GetValue('IdNode'),
                        'Name' => $dbObj->GetValue('Name'),
                        'Icon' => $dbObj->GetValue('Icon'),
                        'Children' => $children
                    );
                    $dbObj->Next();
                }
                
                return $result;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     *
     * @param null $name
     * @param null $path
     * @return array|bool
     */
    function GetByNameAndPath($name = NULL, $path = NULL)
    {
        if (empty($name)) {
            $name = $this->get("Name");
        }
        
        if (empty($path)) {
            $path = $this->get("Path");
        }
        
        $result = array();
        
        $this->ClearError();
        if (! empty($name) && ! empty($path)) {
            
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf("SELECT Nodes.IdNode, Nodes.Name, NodeTypes.Icon, Nodes.IdParent FROM Nodes, NodeTypes
				WHERE Nodes.IdNodeType = NodeTypes.IdNodeType
				AND Nodes.Name like %s
				AND Nodes.Path like %s", $dbObj->sqlEscapeString($name), $dbObj->sqlEscapeString($path));
            $dbObj->Query($sql);
            
            while (! $dbObj->EOF) {
                
                $result[] = array(
                    'IdNode' => $dbObj->GetValue('IdNode')
                );
                $dbObj->Next();
            }
            
            return $result;
        } else
            return false;
    }

    /**
     * Returns a list of paths relatives to the project of all the files and directories belonging to the node in filesystem
     *
     * @return null
     */
    function GetPathList()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->GetPathList();
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Returns de node path (ximdex hierarchy!!! no file system one!!!)
     *
     * @return null|string
     */
    function GetPath()
    {
        $path = $this->_GetPath();
        // $idNode = $this->get('IdNode');
        if (! $path) {
            Logger::warning("Model::Node::getPath(): Path can not be deduced from NULL idNode.");
        }
        return $path;
    }

    /**
     *
     * @return null|string
     */
    function _GetPath()
    {
        $this->ClearError();
        $idNode = $this->get('IdNode');
        if ($idNode > 0) {
            
            $sql = "select Name from FastTraverse ft inner join Nodes n on ft.idNode = n.idNode
					where ft.IdChild = $idNode
					order by depth desc";
            
            $db = new \Ximdex\Runtime\Db();
            $db->Query($sql);
            
            $path = '';
            
            while (! $db->EOF) {
                $path .= '/' . $db->getValue('Name');
                $db->Next();
            }
            
            return $path;
        }
        
        $this->SetError(1);
        return NULL;
    }

    // In a process with 20 calls to each function, this consumes a 16% and getpublishedpath2 a 75% in an intermediate case
    
    /**
     *
     * @param null $channelID
     * @param null $addNodeName
     * @return mixed
     */
    function GetPublishedPath($channelID = null, $addNodeName = null)
    {
        return $this->class->GetPublishedPath($channelID, $addNodeName);
    }

    /**
     * If it is contained, returns the relative path from node $nodeID
     *
     * @param int $nodeID
     * @param Node $nodeReplace
     * @return string|NULL
     */
    function GetRelativePath($nodeID, Node $nodeReplace = null)
    {
        $this->ClearError();
        if ($this->get('IdNode')) {
            if ($this->IsOnNode($nodeID)) {
                $nodes = FastTraverse::get_parents($this->get('IdNode'), 'Name');
                if ($nodes) {
                    $path = '';
                    foreach ($nodes as $parentId => $name) {
                        if ($nodeReplace and $parentId == $nodeReplace->GetID())
                            $nodeName = $nodeReplace->GetNodeName();
                        else
                            $nodeName = $name;
                        $path = '/' . $nodeName . $path;
                        if ($parentId == $nodeID)
                            break;
                    }
                    return $path;
                }
            } else
                $this->SetError(1);
        }
        return null;
    }

    /**
     * Returns if a node is contained in the node with id $nodeID
     *
     * @param
     *            $nodeID
     * @return bool
     */
    function IsOnNode($nodeID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            
            $nodes = FastTraverse::get_parents($this->get('IdNode'));
            if ($nodes === false)
                return false;
            foreach ($nodes as $parentId => $level) {
                if ($parentId == $nodeID)
                    return true;
            }
            return false;
        }
        $this->SetError(1);
        return false;
    }

    /**
     * Returns if a node is contained in the node with nodetype $nodeTypeID
     *
     * @param
     *            $nodeTypeID
     * @return bool
     */
    function IsOnNodeWithNodeType($nodeTypeID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $nodes = FastTraverse::get_parents($this->get('IdNode'), 'IdNodeType');
            if ($nodes === false)
                return false;
            foreach ($nodes as $idNodeType) {
                if ($idNodeType == $nodeTypeID)
                    return true;
            }
            return false;
        }
        $this->SetError(1);
        return false;
    }

    /**
     * Returned the Id of the nearest parent which can attach groups (nodeType)
     */
    public function GetNearest(Node $node)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $nodes = FastTraverse::get_parents($node->get('IdNode'), 'idNodeType');
            if ($nodes === false)
                return false;
            foreach ($nodes as $idNodeType) {
                $parentNodeType = new NodeType($idNodeType);
                if (! $parentNodeType->GetID())
                    return false;
                if ($parentNodeType->get('CanAttachGroups'))
                    return $node->get('IdNode');
            }
        }
        $this->SetError(1);
        return false;
    }

    /**
     * Returns a path in the file system from where children are pending
     * Function used for renderization
     *
     * @return null
     */
    function GetChildrenPath()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->GetChildrenPath();
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Returns a list of allowed nodetypes
     *
     * TODO - Take into account amount to returns the ones really allowed
     *
     * @return array
     */
    function GetCurrentAllowedChildren()
    {
        $query = sprintf("SELECT NodeType" . " FROM NodeAllowedContents" . " WHERE IdNodeType = %d", $this->nodeType->GetID());
        $allowedChildrens = array();
        $dbObj = new \Ximdex\Runtime\Db();
        
        $dbObj->Query($query);
        while (! $dbObj->EOF) {
            $allowedChildrens[] = $dbObj->GetValue('NodeType');
            $dbObj->Next();
        }
        return $allowedChildrens;
    }

    /**
     * Renders a node in the file system
     *
     * @param null $recursive
     */
    function RenderizeNode($recursive = null)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if ($this->nodeType->get('IsRenderizable')) {
                if ($this->nodeType->get('HasFSEntity')) {
                    if ($this->class->RenderizeNode() === false)
                        return false;
                }
                if ($recursive) {
                    $children = $this->GetChildren();
                    if (! empty($children)) {
                        foreach ($children as $childID) {
                            $child = new Node($childID);
                            if ($child->RenderizeNode(true) === false)
                                return false;
                        }
                    }
                }
            }
        } else {
            $this->SetError(1);
            return false;
        }
        return true;
    }

    /**
     * Returns a node content
     *
     * @return null
     */
    function GetContent()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->GetContent();
        }
        return NULL;
    }

    /**
     * Set a node content
     *
     * @param string $content
     * @param string $commitNode
     * @return boolean
     */
    function SetContent($content, $commitNode = NULL)
    {
        $this->ClearError();
        if ($this->getID()) {
            
            // Validate HTML or XML valid contents (including XSL schemas)
            if ($this->GetNodeType()) {
                $res = true;
                if ($this->GetNodeType() == NodeTypeConstants::XSL_TEMPLATE or $this->GetNodeType() == NodeTypeConstants::XML_DOCUMENT 
                    or $this->getNodeType() == NodeTypeConstants::RNG_VISUAL_TEMPLATE) {
                            
                    //TODO change global variable to another entity
                    $GLOBALS['errorsInXslTransformation'] = array();
                    
                    // Check the valid XML
                    $domDoc = new DOMDocument();
                    $res = @$domDoc->loadXML($content);
                    if ($res === false) {
                        Logger::error('Invalid XML for IdNode: ' . $this->GetID() . ' (' . $this->GetDescription() . ')');
                        $error = \Ximdex\Utils\Messages::error_message('DOMDocument::loadXML(): ');
                        if ($error) {
                            $error = 'Invalid XML content for node: ' . $this->GetID() . ' (' . $error . ')';
                            $this->messages->add($error, MSG_TYPE_WARNING);
                            Logger::warning($error);
                            $GLOBALS['errorsInXslTransformation'] = [$error];
                        }
                    }
                } elseif ($this->getNodeType() == NodeTypeConstants::HTML_VIEW or $this->getNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                    
                    // Validation of HTML documents and views
                    $domDoc = new DOMDocument();
                    $res = @$domDoc->loadHTML($content);
                    if ($res === false) {
                        $error = 'Invalid HTML';
                        $this->messages->add($error, MSG_TYPE_WARNING);
                        Logger::warning('Saving content: Invalid HTML for node: ' . $this->GetID() . ' ' . $this->GetDescription());
                    }
                }
                
                // Check dependencias for HTML and XML documents
                if ($res and $this->getNodeType() == NodeTypeConstants::XML_DOCUMENT or $this->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                    if ($this->GetNodeName() != 'templates_include.xsl') {
                            
                        // dotdot dependencies only can be checked in templates under a server node
                        $templatesNode = new Node($this->GetParent());
                        if ($templatesNode->GetNodeType() == NodeTypeConstants::TEMPLATES_ROOT_FOLDER) {
                            $projectNode = new Node($templatesNode->getParent());
                            if ($projectNode->GetNodeType() == NodeTypeConstants::PROJECT)
                                $idServer = false;
                        }
                        if (! isset($idServer)) {
                            $idServer = $this->getServer();
                        }
                        if ($idServer) {
                            
                            // Check the dotdot dependencies
                            ParsingDependencies::getDotDot($content, $idServer);
                            if (isset($GLOBALS['parsingDependenciesError']) and $GLOBALS['parsingDependenciesError']) {
                                $this->messages->add($GLOBALS['parsingDependenciesError'], MSG_TYPE_WARNING);
                                Logger::warning('Parsing dotDot dependencies: ' . $GLOBALS['parsingDependenciesError'] 
                                        . ' for IdNode: ' . $this->GetID() . ' (' . $this->GetDescription() . ')');
                                $GLOBALS['parsingDependenciesError'] = null;
                            }
                        }
                        
                        // Check the pathto dependencies
                        ParsingDependencies::getPathTo($content, $this->GetID());
                        if (isset($GLOBALS['parsingDependenciesError']) and $GLOBALS['parsingDependenciesError']) {
                            $this->messages->add($GLOBALS['parsingDependenciesError'], MSG_TYPE_WARNING);
                            Logger::warning('Parsing pathTo dependencies: ' . $GLOBALS['parsingDependenciesError'] 
                                . ' for IdNode: ' . $this->GetID() . ' (' . $this->GetDescription() . ')');
                            $GLOBALS['parsingDependenciesError'] = null;
                        }
                    }
                }
                
                // Validation of the RNG schema for the RNG template
                if ($res and $this->getNodeType() == NodeTypeConstants::RNG_VISUAL_TEMPLATE) {
                    $schema = FsUtils::file_get_contents(APP_ROOT_PATH . '/actions/xmleditor2/views/common/schema/relaxng-1.0.rng.xml');
                    $rngValidator = new \Ximdex\XML\Validators\RNG();
                    $res = $rngValidator->validate($schema, $content);
                    if ($res === false) {
                        $errors = $rngValidator->getErrors();
                        if (! $errors and \Ximdex\Utils\Messages::error_message()) {
                            $error = \Ximdex\Utils\Messages::error_message('DOMDocument::relaxNGValidateSource(): ');
                        } else {
                            
                            // Only will be shown the first error (more easy to read)
                            $error = $errors[0];
                        }
                        $this->messages->add($error, MSG_TYPE_WARNING);
                        Logger::warning('Saving content: Invalid RNG template for node: ' . $this->GetID() . ' ' . $this->GetDescription() 
                                . ' (' . $error . ')');
                        $GLOBALS['errorsInXslTransformation'] = [$error];
                    }
                }
                
                // Validation of the JSON schemas
                if ($this->getNodeType() == NodeTypeConstants::HTML_LAYOUT or $this->GetNodeType() == NodeTypeConstants::HTML_COMPONENT) {
                    $res = json_decode($content);
                    if ($res === null or $res === false) {
                        $error = 'Invalid JSON schema';
                        $this->messages->add($error, MSG_TYPE_WARNING);
                        Logger::warning('Saving content: Invalid JSON HTML schema for node: ' . $this->GetID() . ' ' . $this->GetDescription());
                    }
                }
            }
            if ($this->class->SetContent($content, $commitNode) === false) {
                $this->messages->mergeMessages($this->class->messages);
                return false;
            }
            $this->messages->mergeMessages($this->class->messages);
            if ($this->RenderizeNode() === false)
                return false;
        }
        return true;
    }

    /**
     * Checks if the node is blocked and returns the blocker user id
     *
     * @return bool|null|string
     */
    function IsBlocked()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if (time() < ($this->get('BlockTime') + App::getValue('BlockExpireTime'))) {
                return $this->get('BlockUser');
            } else {
                $this->unBlock();
                return NULL;
            }
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Checks if the node is blocked, and returns the blocking time
     *
     * @return bool|null|string
     */
    function GetBlockTime()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if (time() < ($this->get('BlockTime') + App::getValue('BlockExpireTime'))) {
                return $this->get('BlockTime');
            } else {
                $this->unBlock();
                return NULL;
            }
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Blocks a node and returns the blocking timestamp
     *
     * @param $userID
     * @return bool|null|string
     */
    function Block($userID)
    {
        $this->ClearError();
        
        if ($this->get('IdNode') > 0) {
            
            $currentBlockUser = $this->IsBlocked();
            if (! $currentBlockUser || $currentBlockUser == $userID) {
                $this->set('BlockTime', time());
                $this->set('BlockUser', $userID);
                $this->update();
                return $this->get('BlockTime');
            } else {
                return NULL;
            }
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Delete a block.
     */
    function unBlock()
    {
        $this->ClearError();
        
        if ($this->get('IdNode') > 0) {
            $this->set('BlockTime', 0);
            $this->set('BlockUser', '');
            $this->update();
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Checks if node is renderized in the file system
     *
     * @return bool
     */
    function IsRenderized()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if (! $this->nodeType->get('IsRenderizable')) {
                return false;
            }
            // $pathList = array();
            
            // / Consigue el path hasta el directorio de nodos
            $absPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot");
            
            // / consigue la lista de paths del nodo
            $pathList = $this->class->GetPathList();
            if (empty($pathList)) {
                return false;
            }
            
            $path = $absPath . $pathList;
            if (! file_exists($path)) { // / si falta alguna devuelve false
                return false;
            }
            
            // / en otro caso devuelve true
            return true;
        }
        $this->SetError(1);
        return false;
    }

    function add()
    {
        die(_('This call should be done through CreateNode'));
    }

    /**
     *
     * @return bool|int|null|string
     */
    function update()
    {
        $this->set('ModificationDate', time());
        return parent::update();
    }

    /**
     *
     * @param null $idParent
     * @param null $idNodeType
     * @return bool|null|string
     */
    function getFirstStatus($idParent = NULL, $idNodeType = NULL)
    {
        if (empty($idParent)) {
            $idParent = $this->get('IdNode');
        }
        if (empty($idNodeType)) {
            $idNodeType = $this->get('IdNodeType');
        }
        
        $nodeType = new NodeType($idNodeType);
        if (! ($nodeType->get('IsPublishable') > 0)) {
            return NULL;
        }
        
        $node = new Node($idParent);
        
        // first, I try to get it from the inherits properties
        $pipelines = $node->getProperty('Pipeline');
        if (count($pipelines) > 0) {
            $idPipeline = $pipelines[0];
            $workflow = new WorkFlow(NULL, NULL, $idPipeline);
            $idStatus = $workflow->GetInitialState();
            if ($idStatus > 0) {
                return $idStatus;
            }
        }
        
        // if i cant find it, i try to get it from the nodetypes
        $pipeNodeTypes = new PipeNodeTypes();
        $result = $pipeNodeTypes->find('IdPipeline', 'IdNodeType = %s', array(
            $idNodeType
        ), MONO);
        if (count($result) > 0) {
            $idPipeline = $result[0];
            $workflow = new WorkFlow(NULL, NULL, $idPipeline);
            $idStatus = $workflow->GetInitialState();
            if ($idStatus > 0) {
                return $idStatus;
            }
        }
        
        // finally, i get it from the default value
        $idPipeline = App::getValue('IdDefaultWorkflow');
        $workflow = new WorkFlow(NULL, NULL, $idPipeline);
        return $workflow->GetInitialState();
    }

    /**
     * Creates a new node and loads its ID in the class
     *
     * @param $name
     * @param $parentID
     * @param $nodeTypeID
     * @param null $stateID
     * @param array $subfolders
     * @return bool|string
     */
    function CreateNode($name, $parentID, $nodeTypeID, $stateID = null, $subfolders = array())
    {
        $this->set('IdParent', (int) $parentID);
        $this->set('IdNodeType', (int) $nodeTypeID);
        $this->set('Name', $name);
        $this->set('CreationDate', time());
        $this->set('ModificationDate', time());
        $nodeType = new NodeType($nodeTypeID);
        $parentNode = new Node($this->get('IdParent'));
        
        // Set IdState
        if ($nodeType->get('IsPublishable')) {
            $this->set('IdState', $this->getFirstStatus($parentID, $nodeTypeID));
        } else {
            $this->set('IdState', NULL);
        }
        
        // check name, parentID and nodeTypeID
        if (! ($name || $parentID || $nodeTypeID)) {
            $this->SetError(3);
            $this->messages->add(_('The name, parent or nodetype is missing'), MSG_TYPE_ERROR);
            return false;
        }
        
        // If nodetype is not existing, we are done
        if (! ($nodeType->get('IdNodeType')) > 0) {
            $this->messages->add(_('The specified nodetype does not exist'), MSG_TYPE_ERROR);
            $this->SetError(11);
            return false;
        }
        
        // Checking for correct name format
        if (! $this->IsValidName($this->get('Name'), $this->get('IdNodeType'))) {
            $this->messages->add(_('Node name is not valid'), MSG_TYPE_ERROR);
            $this->SetError(9);
            return false;
        }
        
        // If parent does not exist, we are done
        if (! ($parentNode->get('IdNode') > 0)) {
            $this->messages->add(_('Parent node does not exist'), MSG_TYPE_ERROR);
            $this->SetError(10);
            return false;
        }
        
        // check if already exist a node with the same name under the current parent
        if (! ($parentNode->GetChildByName($this->get('Name')) === false)) {
            $this->messages->add(_('There is already a node with this name under this parent'), MSG_TYPE_ERROR);
            $this->SetError(8);
            return false;
        }
        
        // node is not allowed to live there
        if (! $this->checkAllowedContent($nodeTypeID, $parentID)) {
            $this->messages->add(_('This node is not allowed under this parent'), MSG_TYPE_ERROR);
            $this->SetError(17);
            return false;
        }
        
        // Inserts the node in the Nodes table
        if (parent::add() === false)
            return false;
        
        if (! ($this->get('IdNode') > 0)) {
            $this->messages->add(_('Error creating the node'), MSG_TYPE_ERROR);
            $this->SetError(5);
            return false;
        }
        $this->SetID($this->get('IdNode'));
        
        // Updating fastTraverse before the setcontent, because in the node cache this information is needed
        $this->UpdateFastTraverse();
        
        // All the args from this function call are passed to this nodetype create method.
        if (is_object($this->class)) {
            $argv = func_get_args();
            call_user_func_array(array(
                & $this->class,
                'CreateNode'
            ), $argv);
            if (is_object($this->class)) {
                $this->messages->mergeMessages($this->class->messages);
            }
        }
        
        if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
            if ($this->get('IdNode') > 0) {
                $this->delete();
                return false;
            }
        }
        
        // Add general group by default
        $id_usuario = Session::get('userID');
        $user = new User($id_usuario);
        $group = new Group();
        $this->AddGroupWithRole($group->GetGeneralGroup());
        
        // get associated groups from the parent/s
        if ($nodeType->get('CanAttachGroups')) {
            $nearestId = $this->GetNearest($parentNode);
            $nearest = new Node($nearestId);
            $associated = $nearest->GetGroupList();
            if (count($associated) > 0) {
                foreach ($associated as &$group) {
                    $this->AddGroupWithRole($group, $user->GetRoleOnGroup($group));
                }
            }
        }
        
        // if the create node type is Section (section inside a server)
        // it is checked if the user who created it belongs to some group
        // to include the relation between nodes and groups
        if ($this->nodeType->get('Name') == 'Section') {
            $grupos = $user->GetGroupList();
            
            // The first element of the list $grupos is always the general group
            // this insertion is not considered as it the relation by default
            if (is_array($grupos)) {
                reset($grupos);
                while (list (, $grupo) = each($grupos)) {
                    $this->AddGroupWithRole($grupo, $user->GetRoleOnGroup($grupo));
                }
            }
        }
        
        // / Updating the hierarchy index for this node.
        $this->RenderizeNode();
        
        Logger::debug("Model::Node::CreateNode: Creating node id(" . $this->nodeID . "), name(" . $name . "), parent(" . $parentID . ").");
        
        // / Once created, its content by default is added.
        $dbObj = new \Ximdex\Runtime\Db();
        
        if (! empty($subfolders) && is_array($subfolders)) {
            $subfolders_str = implode(",", $subfolders);
            $query = sprintf("SELECT NodeType, Name, State, Params FROM NodeDefaultContents WHERE IdNodeType = %d AND NodeType in (%s)", $this->get('IdNodeType'), $subfolders_str);
        } else {
            $query = sprintf("SELECT NodeType, Name, State, Params FROM NodeDefaultContents WHERE IdNodeType = %d", $this->get('IdNodeType'));
        }
        $dbObj->Query($query);
        
        while (! $dbObj->EOF) {
            $childNode = new Node();
            Logger::debug("Model::Node::CreateNode: Creating child name(" . $this->get('Name') . "), type(" . $this->get('IdNodeType') . ").");
            $res = $childNode->CreateNode($dbObj->GetValue('Name'), $this->get('IdNode'), $dbObj->GetValue('NodeType'), $dbObj->GetVAlue('State'));
            if ($res === false) {
                $this->messages->mergeMessages($childNode->messages);
                return false;
            }
            $dbObj->Next();
        }
        
        $node = new Node($this->get('IdNode'));
        if ($nodeTypeID == NodeTypeConstants::TEMPLATES_ROOT_FOLDER) {
            // if the node is a type of templates folder or ximlets section, generates the templates_include.xsl inside
            $xsltNode = new \Ximdex\NodeTypes\XsltNode($node);
            if ($xsltNode->create_templates_include($node->GetID()) === false) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        } elseif ($nodeTypeID == NodeTypeConstants::METADATA_SECTION) {
            // if the node is a type of metadata section, generates the relation with the templates folder
            $xsltNode = new \Ximdex\NodeTypes\XsltNode($node);
            if ($xsltNode->rel_include_templates_to_metadata_section($node) === false) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        } elseif ($nodeTypeID == NodeTypeConstants::XIMLET_ROOT_FOLDER) {
            // if the node is a type of ximlets section, generates the relation with the templates folder
            $xsltNode = new \Ximdex\NodeTypes\XsltNode($node);
            if ($xsltNode->rel_include_templates_to_documents_folders($parentNode) === false) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        }
        return $node->get('IdNode');
    }

    /**
     *
     * @return bool|int|string
     */
    function delete()
    {
        return $this->DeleteNode(true);
    }

    /**
     * Deletes a node and all its children
     *
     * @param bool $firstNode
     * @return bool|int|string
     */
    function DeleteNode($firstNode = true)
    {
        if ($this->CanDenyDeletion() && $firstNode) {
            $this->messages->add(_('Node deletion was denied'), MSG_TYPE_WARNING);
            return false;
        }
        
        $this->ClearError();
        if (! ($this->get('IdNode') > 0)) {
            $this->SetError(1);
            return false;
        }
        
        $IdChildrens = $this->GetChildren();
        
        if (! is_null($IdChildrens)) {
            reset($IdChildrens);
            while (list (, $IdChildren) = each($IdChildrens)) {
                $childrenNode = new Node($IdChildren);
                if ($childrenNode->get('IdNode') > 0) {
                    $childrenNode->DeleteNode(false);
                } else {
                    $this->SetError(4);
                }
            }
        }
        
        // Deleting from file system
        if ($this->nodeType->get('HasFSEntity')) {
            $absPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot");
            $deletablePath = $this->class->GetPathList();
            
            $nodePath = $absPath . $deletablePath;
            
            if (is_dir($nodePath)) {
                FsUtils::deltree($nodePath);
            } else {
                FsUtils::delete($nodePath);
            }
        }
        
        // Deleting properties it may has
        $nodeProperty = new \Ximdex\Models\NodeProperty();
        $nodeProperty->deleteByNode($this->get('IdNode'));
        
        // first invoking the particular Delete...
        if ($this->GetNodeType() != NodeTypeConstants::XSL_TEMPLATE)
            $this->class->DeleteNode();
        
        // and the the general one
        $data = new DataFactory($this->nodeID);
        $data->DeleteAllVersions();
        unset($data);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute(sprintf("DELETE FROM NodeNameTranslations WHERE IdNode = %d", $this->get('IdNode')));
        $dbObj->Execute(sprintf("DELETE FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));
        // deleting potential entries on table NoActionsInNode
        $dbObj->Execute(sprintf("DELETE FROM NoActionsInNode WHERE IdNode = %d", $this->get('IdNode')));
        
        // if the folder is of structured documents type, the relation with templates folder will be deleted
        if ($this->nodeType->GetIsStructuredDocument()) {
            $depsMngr = new DepsManager();
            $depsMngr->deleteBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $this->nodeID);
        }
        
        // delete the references to the XML documents folders, if the node type is templates folder
        if ($this->nodeType->get('IdNodeType') == NodeTypeConstants::TEMPLATES_ROOT_FOLDER) {
            $depsMngr = new DepsManager();
            $depsMngr->deleteByTarget(DepsManager::STRDOC_TEMPLATE, $this->nodeID);
            
            // reload the dependencies to the documents folders if exist (with the templates folder node)
            $project = new Node($this->getProject());
            $xsltNode = new \Ximdex\NodeTypes\XsltNode($this);
            if (! $xsltNode->rel_include_templates_to_documents_folders($project)) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        }
        
        $nodeDependencies = new NodeDependencies();
        $nodeDependencies->deleteBySource($this->get('IdNode'));
        $nodeDependencies->deleteByTarget($this->get('IdNode'));
        
        $dbObj->Execute(sprintf("DELETE FROM FastTraverse WHERE IdNode = %d OR  IdChild = %d", $this->get('IdNode'), $this->get('IdNode')));
        
        $dependencies = new Dependencies();
        $dependencies->deleteDependentNode($this->get('IdNode'));
        
        $rtn = new RelTagsNodes();
        $rtn->deleteTags($this->nodeID);
        
        $res = parent::delete();
        
        if ($this->GetNodeType() == NodeTypeConstants::XSL_TEMPLATE)
            $this->class->DeleteNode(false);
        
        Logger::info("Node " . $this->nodeID . " deleted");
        $this->nodeID = null;
        $this->class = null;
        
        return $res;
    }

    /**
     *
     * @return bool
     */
    function CanDenyDeletion()
    {
        if (is_object($this->class) && method_exists($this->class, 'CanDenyDeletion')) {
            return $this->class->CanDenyDeletion();
        }
        return true;
    }

    /**
     * Returns the list of nodes which depend on the one in the object
     *
     * @return null
     */
    function GetDependencies()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->GetDependencies();
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Returns the set of nodes which depend in a direct or indirect way on the node which are in the object
     * (the set of verts of the dependency graph)
     *
     * @param array $excludeNodes
     * @return array|null
     */
    function GetGlobalDependencies($excludeNodes = array())
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $deps = array_unique($this->TraverseTree(4));
            $list = array_unique($this->TraverseTree());
            $brokenDeps = array_diff($deps, $list);
            $brokenDeps = array_diff($brokenDeps, $excludeNodes);
            
            if (sizeof($brokenDeps)) {
                foreach ($brokenDeps as $depID) {
                    if (is_array($excludeNodes) && in_array($depID, $excludeNodes)) {
                        $exclude = array_merge($excludeNodes, $brokenDeps, $list);
                        $dep = new Node($depID);
                        $brokenDeps = array_merge($brokenDeps, $dep->GetGlobalDependencies($exclude));
                        // unset($dep);
                        unset($dep);
                    }
                }
            }
            
            return (array_unique($brokenDeps));
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Changes the node name
     *
     * @param
     *            $name
     * @return bool
     */
    function RenameNode($name)
    {
        $folderPath = null;
        
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if ($this->get('Name') == $name) {
                return true;
            }
            // / Checking if node name is in correct format
            if (! $this->IsValidName($name)) {
                $this->SetError(9);
                return false;
            }
            // / Checking if the parent has no other child with same name
            $parent = new Node($this->get("IdParent"));
            $idChildren = $parent->GetChildByName($name);
            if ($idChildren && $idChildren != $this->get("IdNode")) {
                $this->SetError(5);
                return false;
            }
            
            $fsEntity = $this->nodeType->get('HasFSEntity');
            $isFile = ($fsEntity && ($this->nodeType->get('IsPlainFile') || $this->nodeType->get('IsStructuredDocument')));
            $isDir = ($fsEntity && ($this->nodeType->get('IsFolder') || $this->nodeType->get('IsVirtualFolder')));
            // $isNone = (!$isFile && !$isDir);
            
            // / If it is a directory or file, we cannot not allow the process to stop before finishing and leave it inconsistent
            if ($isDir || $isFile) {
                ignore_user_abort(true);
            }
            
            if ($isDir) {
                // / Temporal backup of children nodes. In this case, it is passed the path and a flag to specify that it is a path
                $folderPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot") . $this->class->GetChildrenPath();
            }
            
            if ($isFile) {
                $absPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot");
                $deletablePath = $this->class->GetPathList();
                FsUtils::delete($absPath . $deletablePath);
            }
            
            // / Changing the name in the Nodes table
            $this->set('Name', $name);
            /* $result = */
            $this->update();
            // / If this node type has nothing else to change, the method rename node of its specific class is called
            if ($this->class->RenameNode($name) === false) {
                $this->messages->mergeMessages($this->class->messages);
                return false;
            }
            if ($isFile) {
                // / The node is renderized, its children are lost in the filesystem
                $node = new Node($this->get('IdNode'));
                $node->RenderizeNode();
            }
            
            if ($isDir) {
                // / Retrieving all children from the backup we kept, identified by $backupID
                $parentNode = new Node($this->get('IdParent'));
                $newPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot") . $parentNode->GetChildrenPath() . '/' . $name;
                rename($folderPath, $newPath);
            }
            
            return true;
        }
        $this->SetError(1);
        return false;
    }

    /**
     * Moves the node
     *
     * @param
     *            $targetNode
     *
     * @param
     *            $targetNode
     */
    function MoveNode($targetNode)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if ($targetNode > 0) {
                $target = new Node($targetNode);
                if (! $target->IsOnNode($this->get('IdNode'))) {
                    // $fsEntity = $this->nodeType->get('HasFSEntity');
                    $absPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot");
                    ignore_user_abort(true);
                    
                    // Temporal children backup. In this case it is passed the path and a flag to indicate that it is a path
                    $folderPath = $absPath . $this->class->GetChildrenPath();
                    
                    // FastTraverse is updated for current node
                    $this->SetParent($targetNode);
                    $this->UpdateFastTraverse();
                    
                    // Retrieving all children from stored backup, identified by $backupID
                    $parentNode = new Node($this->get('IdParent'));
                    $newPath = $absPath . $parentNode->GetChildrenPath() . '/' . $this->GetNodeName();
                    @rename($folderPath, $newPath);
                    // $this->TraverseTree(2);
                    // A language document cannot be moved
                    // Node is renderized, so we lost its children in filesystem
                    $this->RenderizeNode(1);
                    
                    // Updating paths and FastTraverse of children (if existing)
                    $this->UpdateChildren();
                    
                    // If there is a new name, we change the node name.
                    $name = FsUtils::get_name($this->GetNodeName());
                    $ext = FsUtils::get_extension($this->GetNodeName());
                    if ($ext != null && $ext != "")
                        $ext = "." . $ext;
                    $newName = $name . $ext;
                    $index = 1;
                    while (($child = $target->GetChildByName($newName)) > 0 && $child != $this->get("IdNode")) {
                        $newName = sprintf("%s_copia_%d%s", $name, $index, $ext);
                        $index ++;
                    }
                    // If there is no name change, we leave all as is.
                    if ($this->GetNodeName() != $newName) {
                        $this->SetNodeName($newName);
                    }
                } else {
                    $this->SetError(12);
                }
            } else {
                $this->SetError(3);
            }
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Returns a list of groups associated to this node
     *
     * @return array|null
     */
    function GetGroupList()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            
            if (! $this->nodeType->get('CanAttachGroups')) {
                $parent = $this->get('IdParent');
                if ($parent) {
                    $parent = new Node($parent);
                    if ($parent->get('IdNode') > 0) {
                        $groupList = $parent->GetGroupList();
                    } else {
                        $groupList = array();
                    }
                } else {
                    $groupList = array();
                }
            } else {
                $dbObj = new \Ximdex\Runtime\Db();
                $dbObj->Query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));
                $groupList = array();
                while (! $dbObj->EOF) {
                    $groupList[] = $dbObj->GetValue("IdGroup");
                    $dbObj->Next();
                }
            }
            return $groupList;
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Returns the list of groups associated to this node
     *
     * @param
     *            $groupID
     * @return null|String
     */
    function GetRoleOfGroup($groupID)
    {
        $this->ClearError();
        if ($this->get('IdNode')) {
            if (! $this->nodeType->get('CanAttachGroups')) {
                $parent = $this->GetParent();
                if ($parent) {
                    if (! $this->numErr) {
                        $node = new Node($parent);
                        $role = $node->GetRoleOfGroup($groupID);
                        if (! $node->numErr)
                            return $role;
                    }
                }
            } else {
                $sql = sprintf("SELECT IdRole FROM RelGroupsNodes WHERE IdNode = %d AND IdGroup = %d", $this->get('IdNode'), $groupID);
                $dbObj = new \Ximdex\Runtime\Db();
                $dbObj->Query($sql);
                if ($dbObj->numRows > 0) {
                    return $dbObj->GetValue("IdRole");
                } else {
                    $this->SetError(5);
                }
            }
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Returns the list of users associated to this node* @param $ignoreGeneralGroup
     *
     * @param null $ignoreGeneralGroup
     * @return array|null
     */
    function GetUserList($ignoreGeneralGroup = null)
    {
        $this->ClearError();
        $group = new Group();
        if ($this->get('IdNode') > 0) {
            $groupList = $this->GetGroupList();
            
            // / Taking off the General Group if needed
            if ($ignoreGeneralGroup) {
                $groupList = array_diff($groupList, array(
                    $group->GetGeneralGroup()
                ));
            }
            
            $userList = array();
            if (! $this->numErr) {
                foreach ($groupList as $groupID) {
                    $group = new Group($groupID);
                    $tempUserList = $group->GetUserList();
                    $userList = array_merge($userList, $tempUserList);
                    unset($group);
                }
                return array_unique($userList);
            }
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * This function does not delete an user from Users table, this disassociated from the group
     *
     * @param
     *            $groupID
     */
    function DeleteGroup($groupID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if ($this->nodeType->get('CanAttachGroups')) {
                $dbObj = new \Ximdex\Runtime\Db();
                $query = sprintf("DELETE FROM RelGroupsNodes WHERE IdNode = %d AND IdGroup = %d", $this->get('IdNode'), $groupID);
                $dbObj->Execute($query);
                if ($dbObj->numErr) {
                    $this->SetError(5);
                }
            }
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Associated an user to a group with a concrete role
     *
     * @param
     *            $groupID
     * @param null $roleID
     * @return bool
     */
    function AddGroupWithRole($groupID, $roleID = null)
    {
        $this->ClearError();
        if (! is_null($groupID)) {
            if ($this->nodeType->get('CanAttachGroups')) {
                
                $dbObj = new \Ximdex\Runtime\Db();
                /*
                 * Raise an error when creating a new project from a template, node-group can't be repeated
                 * To prevent this problem, we are going to make a query of this ocurrence
                 */
                $sql = 'select * from RelGroupsNodes where IdGroup = ' . $groupID . ' and IdNode = ' . $this->get('IdNode');
                $res = $dbObj->query($sql);
                if ($res === false or $dbObj->numErr) {
                    $this->SetError(5);
                    return false;
                }
                if ($dbObj->numRows) {
                    // the relation between this node and the given group is defined already
                    return true;
                }
                $query = sprintf("INSERT INTO RelGroupsNodes (IdGroup, IdNode, IdRole) VALUES (%d, %d, %d)", $groupID, $this->get('IdNode'), $roleID);
                $dbObj->Execute($query);
                if ($dbObj->numErr) {
                    
                    $this->SetError(5);
                    return false;
                }
                return true;
            }
        } else {
            $this->SetError(1);
        }
        return false;
    }

    /**
     * It allows to change the role a user participates in a group with
	 *
     * @param
     *            $groupID
     * @param
     *            $roleID
     */
    function ChangeGroupRole($groupID, $roleID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if ($this->nodeType->get('CanAttachGroups')) {
                /** @var string $roleID */
                $sql = sprintf("UPDATE RelGroupsNodes SET IdRole = %d WHERE IdNode = %d AND IdGroup = %d", $roleID, $this->get('IdNode'), $groupID);
                $dbObj = new \Ximdex\Runtime\Db();
                $dbObj->Execute($sql);
                if ($dbObj->numErr) {
                    $this->SetError(5);
                }
            }
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Returns true if an user belongs to a group
     *
     * @param
     *            $groupID
     * @return int|null
     */
    function HasGroup($groupID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdGroup = %d AND IdNode = %d", $groupID, $this->get('IdNode')));
            if ($dbObj->numErr) {
                $this->SetError(5);
            }
            return $dbObj->numRows;
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     *
     * @return array|null
     */
    function GetAllGroups()
    {
        $salida = array();
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));
            if (! $dbObj->numErr) {
                while (! $dbObj->EOF) {
                    $salida[] = $dbObj->GetValue("IdGroup");
                    $dbObj->Next();
                }
                return $salida;
            } else
                $this->SetError(5);
        }
        return NULL;
    }

    /**
     * Function which makes a node to have a workflow as other node and depends on it
     *
     * @param
     *            $nodeID
     */
    function SetWorkFlowMaster($nodeID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if ($nodeID != $this->get('IdNode')) {
                $this->set('SharedWorkflow', $nodeID);
                $this->update();
                
                $synchro = new Synchronizer($this->get('IdNode'));
                $synchro->CopyTimeLineFromNode($nodeID);
            }
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Function which makes the node to have a new independent workflow
     */
    function ClearWorkFlowMaster()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $this->set('SharedWorkflow', '');
            $this->update();
        } else {
            $this->SetError(1);
        }
    }

    /**
     * Function which makes the node to have a new independent workflow
	 *
     * @param null $id
     * @return array
     */
    function GetWorkFlowSlaves($id = null)
    {
        unset($id);
        return $this->find('IdNode', 'SharedWorkflow = %s', array(
            $this->get('IdNode')
        ), MONO);
    }

    /**
     *
     * @return bool|string
     */
    function IsWorkflowSlave()
    {
        return $this->get('SharedWorkflow');
    }

    /**
     *
     * @return array|bool
     */
    function GetAllAlias()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf("SELECT IdLanguage, Name FROM NodeNameTranslations WHERE" . " IdNode= %d");
            $dbObj->Query($query);
            if ($dbObj->numRows) {
                $result = array();
                while (! $dbObj->EOF) {
                    $result[$dbObj->GetValue('IdLanguage')] = $dbObj->GetValue('Name');
                    $dbObj->Next();
                }
                return $result;
            }
        }
        return false;
    }

    /**
     * Obtains the current node alias
     *
     * @param
     *            $langID
     * @return null|String
     */
    function GetAliasForLang($langID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $sql = sprintf("SELECT Name FROM NodeNameTranslations WHERE" . " IdNode= %d" . " AND IdLanguage = %d", $this->get('IdNode'), $langID);
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Query($sql);
            if ($dbObj->numErr) {
                $this->SetError(5);
            } else {
                if ($dbObj->numRows) {
                    return $dbObj->GetValue("Name");
                }
            }
        } else {
            $this->SetError(1);
        }
        return NULL;
    }

    /**
     * Controls if the current node has alias
     */
    /**
     *
     * @param
     *            $langID
     * @return null|String
     */
    function HasAliasForLang($langID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $sql = sprintf("SELECT IdNode FROM NodeNameTranslations WHERE" . " IdNode =  %d" . " AND IdLanguage = %d", $this->get('IdNode'), $langID);
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Query($sql);
            if ($dbObj->numErr)
                $this->SetError(1);
            
            return $dbObj->GetValue("IdNode");
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     *
     * @param
     *            $langID
     * @return bool|null|String
     */
    function GetAliasForLangWithDefault($langID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $this->ClearError();
            $sql = sprintf("SELECT Name FROM NodeNameTranslations WHERE" . " IdNode = %d" . " AND IdLanguage = %d", $this->get('IdNode'), $langID);
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Query($sql);
            if ($dbObj->numRows > 0) {
                // si encuentra el traducido lo devuelve
                return $dbObj->GetValue("Name");
            }
            
            $langDefault = App::getValue("DefaultLanguage");
            if (strlen($langDefault) != 0) {
                $lang = new Language();
                $lang->SetByIsoName($langDefault);
                $sql = sprintf("SELECT Name FROM NodeNameTranslations WHERE" . " IdNode = %d" . " AND IdLanguage = %d", $this->get('IdNode'), $lang->get('IdLanguage'));
                $dbObj = new \Ximdex\Runtime\Db();
                $dbObj->Query($sql);
                if ($dbObj->numRows > 0) {
                    // Returns the default language
                    return $dbObj->GetValue("Name");
                }
            }
            
            return $this->GetNodeName();
        }
        $this->SetError(1);
        return NULL;
    }

    /**
     * Setting a alias to current node.
     *
     * @param
     *            $langID
     * @param
     *            $name
     * @return bool
     */
    function SetAliasForLang($langID, $name)
    {
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf("SELECT IdNode FROM NodeNameTranslations" . " WHERE IdNode = %d AND IdLanguage = %d", $this->get('IdNode'), $langID);
            $dbObj->Query($query);
            if ($dbObj->numRows > 0) {
                $sql = sprintf("UPDATE NodeNameTranslations " . " SET Name = %s" . " WHERE IdNode = %d" . " AND IdLanguage = %d", $dbObj->sqlEscapeString($name), $this->get('IdNode'), $langID);
            } else {
                $sql = sprintf("INSERT INTO NodeNameTranslations " . "(IdNode, IdLanguage, Name) " . "VALUES (%d, %d, %s)", $this->get('IdNode'), $langID, $dbObj->sqlEscapeString($name));
            }
            
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Execute($sql);
            
            if ($dbObj->numErr) {
                $this->messages->add(_('Alias could not be updated, incorrect operation'), MSG_TYPE_ERROR);
                Logger::error(sprintf(_("Error in query %s or %s"), $query, $sql));
                return false;
            }
            return true;
        }
        
        $this->messages->add(_('The node you want to operate with does not exist'), MSG_TYPE_WARNING);
        Logger::warning(_("Error: unexisting node") . "{$this->IdNode}");
        return false;
    }

    /**
     * Deletes a current node alias.
     *
     * @param
     *            $langID
     * @return bool
     */
    function DeleteAliasForLang($langID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $sql = sprintf("DELETE FROM NodeNameTranslations " . " WHERE IdNode = %d" . " AND IdLanguage = %d", $this->get('IdNode'), $langID);
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Execute($sql);
            if ($dbObj->numErr) {
                $this->messages->add(_('Alias could not be deleted, incorrect operation'), MSG_TYPE_ERROR);
                Logger::error(sprintf(_("Error in query %s"), $sql));
                return false;
            }
            return true;
        }
        $this->messages->add(_('The node you want to operate with does not exist'), MSG_TYPE_WARNING);
        Logger::warning(_("Error: unexisting node") . "{$this->IdNode}");
        return false;
    }

    /**
     * Deletes all current node aliases.
     */
    function DeleteAlias()
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $sql = sprintf("DELETE FROM NodeNameTranslations " . " WHERE IdNode = %d", $this->get('IdNode'));
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Execute($sql);
            if ($dbObj->numErr)
                $this->SetError(5);
        } else
            $this->SetError(1);
    }

    /**
     * If it is contained, it give translated names from node $nodeID in a list form
     */
    /**
     *
     * @param
     *            $nodeID
     * @param
     *            $langID
     * @return array
     */
    function GetAliasForLangPath($nodeID, $langID)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            if ($this->IsOnNode($nodeID)) {
                if ((! $this->get('IdParent')) || ($this->get('IdNode') == $nodeID)) {
                    return array(
                        $this->GetAliasForLangWithDefault($langID)
                    );
                } else {
                    $parent = new Node($this->get('IdParent'));
                    return array_merge($parent->GetAliasForLangPath($nodeID, $langID), array(
                        $this->GetAliasForLangWithDefault($langID)
                    ));
                }
            } else {
                $this->SetError(14);
                return array();
            }
        } else {
            $this->SetError(1);
            return array();
        }
    }

    /**
     *
     * @return bool|null|string
     */
    function GetSection()
    {
        if (! ($this->get('IdNode') > 0)) {
            return NULL;
        }
        
        if ($this->nodeType->get('IsSection')) {
            return $this->get('IdNode');
        }
        
        $idParent = $this->get('IdParent');
        if (! $idParent) {
            return NULL;
        }
        
        $parent = new Node($idParent);
        return $parent->GetSection();
    }

    /**
     *
     * @return bool|null|String
     */
    function getServer()
    {
        $result = $this->_getParentByType(NodeTypeConstants::SERVER);
        
        if (! ($result > 0)) {
            
            $result = $this->_getParentByType(NodeTypeConstants::METADATA_SECTION);
        }
        
        return $result;
    }

    /**
     *
     * @return bool|null|String
     */
    function getProject()
    {
        $result = $this->_getParentByType(NodeTypeConstants::PROJECT);
        
        if (! ($result > 0)) {
            
            $result = $this->_getParentByType(NodeTypeConstants::METADATA_SECTION);
        }
        if (! ($result > 0)) {
            
            $result = $this->_getParentByType(NodeTypeConstants::XSIR_REPOSITORY);
        }
        
        return $result;
    }

    /**
     *
     * @param null $type
     * @return bool|null|String
     */
    function _getParentByType($type = NULL)
    {
        if (is_null($type)) {
            Logger::fatal(_('It is being tried to call a function without param'));
            return false;
        }
        
        if ($this->get('IdNodeType') == $type) {
            return $this->get('IdNode');
        }
        
        $query = sprintf("SELECT ft.IdNode FROM `FastTraverse` ft" . " INNER JOIN Nodes n ON ft.IdNode = n.IdNode AND n.IdNodeType = %d" . " WHERE ft.IdChild = %d and ft.IdNode <> %d", $type, $this->get('IdNode'), $this->get('IdNode'));
        $db = new \Ximdex\Runtime\Db();
        $db->query($query);
        if ($db->numRows > 0) {
            return $db->getValue('IdNode');
        }
        
        Logger::warning(sprintf(_("The nodetype %s could not be obtained for node "), $type) . $this->get('IdNode'));
        return NULL;
    }

    /**
     * If it is depending on a project, its depth is returned
     */
    /**
     *
     * @return int|null
     */
    function GetDepth()
    {
        if (! ($this->get('IdNode') > 0)) {
            return NULL;
        }
        
        if ($this->nodeType->get('Name') == "Server") {
            return 1;
        }
        
        $idParent = $this->get('IdParent');
        
        if (! $idParent) {
            return null;
        }
        
        $parent = new Node($idParent);
        $depth = $parent->GetDepth();
        if ($depth) {
            return NULL;
        }
        
        return ($depth + 1);
    }

    /**
     * If its pending on some project, its depth is returned
     */
    /**
     *
     * @return int|null
     */
    function GetPublishedDepth()
    {
        if (! ($this->get('IdNode') > 0)) {
            return NULL;
        }
        
        if ($this->nodeType->get('Name') == "Server") {
            return 1;
        }
        
        $idParent = $this->get('IdParent');
        if (! $idParent) {
            return NULL;
        }
        
        $parent = new Node($idParent);
        $depth = $parent->GetPublishedDepth();
        if (! $depth) {
            return NULL;
        }
        if ($this->nodeType->get('IsVirtualFolder')) {
            return $depth;
        }
        return ($depth + 1);
    }

    /**
     * Returns the list of nodes which depend on given one.
     * If flag=3, returns just the ones associated with groups 'CanAttachGroups'
     * If flag=4, returns all the nodes which depend on the one in the object and its lists of dependencies.
     * If flag=5, returns all the nodes which depend on the one in the object which cannot be deleted.
     * If flag=6, returns all the nodes which depend on the one in the object which are publishable.
     * If flag=null, returns all the nodes which depend on the one in the object
     *
     * @param int $flag
     * @param string $firstNode
     * @param string $filters
     * @param string $nodeName
     * @return array|boolean[]|string[]
     */
    function TraverseTree($flag = null, $firstNode = true, $filters = '')
    {
        unset($filters);
        
        // / Making an object with current node and its ID is added
        $nodeList = array();
        
        if (($flag == 3) && $this->nodeType->get('CanAttachGroups')) {
            $nodeList[0] = $this->get('IdNode');
        } else if ($flag == 4) {
            $nodeList[0] = $this->get('IdNode');
            $nodeList = array_merge($nodeList, $this->GetDependencies());
        } else if ($flag == 5) {
            if ($this->CanDenyDeletion() && $firstNode) {
                if ($this->get('IdNode') > 0) {
                    $nodeList[0] = $this->get('IdNode');
                }
            }
        } else if ($flag == 6) {
            if ($this->nodeType->get('IsPublishable') == 1) {
                $nodeList[0] = $this->get('IdNode');
            }
        } else {
            $nodeList[0] = $this->get('IdNode');
        }
        
        $nodeChildren = $this->GetChildren();
        
        // / Doing the same for each child
        if (is_array($nodeChildren)) {
            foreach ($nodeChildren as $child) {
                $childNode = new Node($child);
                $nodeList = array_merge($nodeList, $childNode->traverseTree($flag, false));
                unset($childNode);
            }
        }
        return $nodeList;
    }

    /*
     * Gets all ancestors of the node
     *
     */
    /**
     *
     * @param null $fromNode
     * @return array
     */
    function getAncestors($fromNode = null)
    {
        unset($fromNode);
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("SELECT IdNode FROM FastTraverse WHERE IdChild= %d ORDER BY Depth DESC", $this->get('IdNode'), $this->get('IdNode'));
        $dbObj->Query($sql);
        
        $list = array();
        
        while (! $dbObj->EOF) {
            $list[] = $dbObj->GetValue('IdNode');
            $dbObj->Next();
        }
        
        return $list;
    }

    /**
     * Returns a list with the path from root (or the one in the param fromID if it is) until the nod ein the object
     * Keeps the list of node ids ordered by depth, including the object.
     */
    /**
     *
     * @param int $minIdNode
     * @return array
     */
    function TraverseToRoot($minIdNode = 10000)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("SELECT IdNode FROM FastTraverse WHERE IdChild= %d AND IdNode >=%d ORDER BY Depth DESC", $this->get('IdNode'), $minIdNode);
        $dbObj->Query($sql);
        
        $list = array();
        while (! $dbObj->EOF) {
            $list[] = $dbObj->GetValue('IdNode');
            $dbObj->Next();
        }
        return $list;
    }

    /**
     *
     * @param
     *            $depth
     * @param int $node_id
     * @return array
     */
    function TraverseByDepth($depth, $node_id = 1)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("SELECT IdChild FROM FastTraverse WHERE IdNode = %d AND Depth = %d ORDER BY IdNode", $node_id, $depth);
        $dbObj->Query($sql);
        
        $list = array();
        while (! $dbObj->EOF) {
            $list[] = $dbObj->getValue('IdChild');
            $dbObj->Next();
        }
        
        return $list;
    }

    /**
     * JAP 20040617, GetSections_ximTREE
     */
    /**
     *
     * @param
     *            $langID
     * @param
     *            $top
     * @param
     *            $bottom
     * @return string
     */
    function GetSections_ximTREE($langID, $top, $bottom)
    {
        // getting the nodetypes to select
        $auxType = new NodeType();
        $auxType->SetByName("Section");
        $sectionTypeId = $auxType->get('IdNodeType');
        
        $auxType = new NodeType();
        $auxType->SetByName("Server");
        $serverTypeId = $auxType->get('IdNodeType');
        
        // list of nodes of type 'section' until the server (inclusive)
        $sectionList = array();
        
        // surfing the node tree, looking for the section which contain the current node
        $parentid = $this->GetSection();
        
        $profundidad = 0;
        
        while ($parentid) {
            $node = new Node($parentid);
            $nodetype = $node->get('IdNodeType');
            
            array_push($sectionList, $node->get('IdNode'));
            
            // seguimos subiendo en el árbol
            if ($nodetype == $serverTypeId) {
                $parentid = null; // we are in the server, exiting
            } else {
                $parentid = $node->GetParent(); // take the parent, it will include the server
            }
            $profundidad ++;
        }
        
        // re-ordering the list to start from the tree top
        $sectionList = array_reverse($sectionList);
        
        /*
         * DEBUG ...
         * foreach ($sectionList as $mysection)
         * echo "SECCION: $mysection ";
         * ...
         */
        
        // surfing through sections building the exchange XML
        $cad = "<ximTREE ximver='2.0' top='$top' bottom='$bottom'>";
        
        $startlevel = $profundidad - $top - 1; // start section
        if ($startlevel < 0)
            $startlevel = 0;
        
        $endlevel = $profundidad + $bottom; // end section
        
        if ($startlevel <= count($sectionList))
            $section = $sectionList[$startlevel];
        else
            $section = null;
        
        $level = $startlevel + 1;
        
        $branch = null;
        if ($level == count($sectionList))
            $branch = 1;
        
        // DEBUG
        // echo "SELECCIONADA SECCION $section PARA PROCESADO con TOP:$top y BOTTOM:$bottom START:$level END:$endlevel ...";
        
        if ($section && $level <= $endlevel)
            $cad .= $this->expandChildren_ximTREE($section, $sectionTypeId, $level, $langID, $sectionList, $endlevel, $branch);
        
        $cad = "$cad</ximTREE>";
        return $cad;
    }

    /**
     *
     * @param
     *            $nodeID
     * @param
     *            $sectionTypeId
     * @param
     *            $level
     * @param
     *            $langID
     * @param
     *            $sectionList
     * @param
     *            $endlevel
     * @param null $branch
     * @return string
     */
    function expandChildren_ximTREE($nodeID, $sectionTypeId, $level, $langID, $sectionList, $endlevel, $branch = null)
    {
        $node = new Node($nodeID);
        $nodoseleccionado = $sectionList[$level];
        $children = $node->GetChildren($sectionTypeId);
        
        $cad2 = ""; // opening tag for children family "<ximCHILDREN>"
        
        foreach ($children as $child) {
            if ($child and $level < $endlevel) {
                $childnodeid = new Node($child);
                $childname = $childnodeid->get('Name');
                $childnamelang = $childnodeid->GetAliasForLangWithDefault($langID);
                
                if ($child == $nodoseleccionado)
                    $childseleccionado = 1;
                else
                    $childseleccionado = 0;
                
                $original_level = count($sectionList) - 1;
                $distance = $level - $original_level;
                
                $relationship = "relative";
                if ($childseleccionado and $distance < 0)
                    $relationship = "ascendant";
                if ($childseleccionado and $distance == 0) {
                    $relationship = "me";
                    $branch = 1;
                }
                if ($branch and $distance > 0)
                    $relationship = "descendant";
                
                $cad2 .= "<ximNODE sectionid='$child' level='$level' distance='$distance' relationship='$relationship' onpath='$childseleccionado' type='section' name='$childname' langname='$childnamelang' langid='$langID'>";
                $cad2 .= $node->expandChildren_ximTREE($child, $sectionTypeId, $level + 1, $langID, $sectionList, $endlevel, $branch);
                $cad2 .= "</ximNODE>";
            }
        }
        // $cad2 .= "</ximCHILDREN>"; // if we want to close tag for children family
        return $cad2;
    }

    /**
     * Updating the table FastTraverse
     * The parameter delete indicates if we want or not delete the node before
     *
     * @param boolean $delete
     * @return boolean
     */
    function UpdateFastTraverse($delete = true)
    {
        $this->ClearError();
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            if ($delete) {
                $sql = sprintf('DELETE FROM FastTraverse WHERE IdChild = %d', $this->get('IdNode'));
                $dbObj->Execute($sql);
                if ($dbObj->numErr) {
                    $this->SetError(5);
                    return false;
                }
            }
            $parent = $this->get('IdNode');
            $level = '0';
            do {
                $sql = sprintf('INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (%d, %d, %d)', $parent, $this->get('IdNode'), $level);
                $dbObj->Execute($sql);
                // unset($dbObj);
                $level ++;
                $node = new Node($parent);
                $parent = $node->get('IdParent');
                // unset($node);
            } while ($parent);
        } else {
            $this->SetError(1);
            return false;
        }
        return true;
    }

    /**
     *
     * @return array
     */
    function loadData()
    {
        $ret = array();
        $ret['nodeid'] = $this->get('IdNode');
        $ret['parent'] = $this->get('IdParent');
        $ret['type'] = $this->get('IdNodeType');
        $ret['name'] = $this->get('Name');
        $ret['state'] = $this->get('IdState');
        $ret['ctime'] = $this->get('CreationDate');
        $ret['atime'] = $this->get('ModificationDate');
        $ret['desc'] = $this->get('Description');
        $ret['path'] = $this->getPath();
        
        $ret['typename'] = $this->nodeType->get('Name');
        $ret['class'] = $this->nodeType->get('Class');
        $ret['icon'] = $this->nodeType->get('Icon');
        $ret['isdir'] = $this->nodeType->get('IsFolder');
        $ret['isfile'] = $this->nodeType->get('IsPlainFile');
        $ret['isvirtual'] = $this->nodeType->get('IsVirtualFolder');
        $ret['isfs'] = $this->nodeType->get('HasFSEntity');
        $ret['issection'] = $this->nodeType->get('IsSection');
        $ret['isxml'] = $this->nodeType->get('IsStructuredDocument');
        
        $version = $this->GetLastVersion();
        if (! empty($version)) {
            $ret['version'] = $version["Version"];
            $ret['subversion'] = $version["SubVersion"];
            $ret['published'] = $version["Published"];
            $ret['lastuser'] = $version["IdUser"];
            $ret['date'] = date('d/m/Y H:i', $version["Date"]);
            $ret['lastusername'] = $version["UserName"];
        }
        
        return $ret;
    }

    /**
     *
     * @return array
     */
    function DatosNodo()
    {
        $list = array();
        $list['IdNode'] = $this->get('IdNode');
        $list['NodeName'] = $this->get('Name');
        $list['NodeType'] = $this->get('IdNodeType');
        $list['State'] = $this->get('IdState');
        
        return $list;
    }

    /**
     */
    function ClearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }

    /**
     *
     * @param
     *            $code
     */
    function SetError($code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    /**
     *
     * @return mixed
     *
     */
    function HasError()
    {
        return $this->numErr;
    }

    /**
     * Returns this node xml interpretation and its descendents.
     */
    /**
     *
     * @param int $depth
     * @param
     *            $files
     * @param null $recurrence
     * @return string
     */
    function ToXml($depth = 0, & $files, $recurrence = NULL)
    {
        global $STOP_COUNT;
        // TODO check if current user has permits to read this node, and if he does not, returns an empty string.
        if (! ($this->get('IdNode') > 0)) {
            Logger::warning(sprintf(_("It is being tried to load the unexistent node %s"), $this->get('IdNode')));
            return false;
        }
        
        if (! is_array($files)) {
            $files = array();
        }
        
        $depth ++;
        $indexTabs = str_repeat("\t", $depth);
        if ($this->get('IdState') > 0) {
            $query = sprintf("SELECT s.Name as statusName" . " FROM States s" . " WHERE IdState = %d" . " LIMIT 1", $this->get('IdState'));
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Query($query);
            $statusName = $dbObj->GetValue('statusName');
            unset($dbObj);
        }
        
        $idNode = $this->get('IdNode');
        $nodeTypeName = $this->nodeType->get('Name');
        $nodeName = utf8_encode($this->get('Name'));
        $nodeParent = $this->get('IdParent');
        $nodeTypeClass = $this->nodeType->get('Class');
        $sharedWorkflow = $this->get('SharedWorkflow');
        
        $tail = '';
        if (! empty($sharedWorkflow)) {
            $tail .= sprintf(' SharedWorkflow="%s"', $sharedWorkflow);
        }
        
        $tail .= $this->class->getXmlTail();
        
        if (! empty($statusName)) {
            $tail .= sprintf(' state="%s"', utf8_encode($statusName));
        }
        
        // Getting node Properties
        
        $nodeProperty = new \Ximdex\Models\NodeProperty();
        $result = $nodeProperty->getPropertiesByNode($this->get('IdNode'));
        
        if (! is_null($result)) {
            foreach ($result as $resultData) {
                $tail .= sprintf(' %s="%s"', $resultData['Property'], $resultData['Value']);
            }
        }
        
        $xmlHeader = sprintf('<%s id="%d" name="%s" class="%s" nodetype="%d" parentid="%d"%s>', $nodeTypeName, $idNode, $nodeName, $nodeTypeClass, $this->nodeType->get('IdNodeType'), $nodeParent, $tail) . "\n";
        if ($STOP_COUNT == COUNT) {
            $STOP_COUNT = NO_COUNT;
        } else {
            $STOP_COUNT = NO_COUNT_NO_RETURN;
        }
        $xmlBody = $this->class->ToXml($depth, $files, $recurrence);
        if ($STOP_COUNT == NO_COUNT) {
            $STOP_COUNT = COUNT;
        } else {
            $STOP_COUNT = NO_COUNT;
        }
        
        /*
         * This block of code makes if a xmlcontainer has not associated a visualtemplate,
         * it looks automatically if some child has associated a visualtemplate and associate it to the container
         */
        
        if (($this->nodeType->get('Class') == 'Xmlcontainernode') && (empty($idTemplate))) {
            $childrens = $this->GetChildren();
            if (! empty($childrens)) {
                reset($childrens);
                while (list (, $idChildrenNode) = each($childrens)) {
                    $children = new Node($idChildrenNode);
                    if (! ($children->get('IdNode') > 0)) {
                        Logger::warning(sprintf(_("It is being tried to load the node %s from the unexistent node %s"), $children->get('IdNode'), $this->get('IdNode')));
                        continue;
                    }
                    
                    if ($children->nodeType->GetIsStructuredDocument()) {
                        $structuredDocument = new StructuredDocument($children->GetID());
                        $idTemplate = $structuredDocument->GetDocumentType();
                        $node = new Node($idTemplate);
                        if (! ($node->get('IdNode') > 0)) {
                            Logger::warning(sprintf(_("It is being tried to load the node %s from the unexistent node %s"), $node->get('IdNode'), $this->get('IdNode')));
                            continue;
                        }
                        if ($STOP_COUNT == COUNT) {
                            $STOP_COUNT = NO_COUNT;
                        } else {
                            $STOP_COUNT = NO_COUNT_NO_RETURN;
                        }
                        $xmlBody .= $node->ToXml($depth, $files, $recurrence);
                        if ($STOP_COUNT == NO_COUNT) {
                            $STOP_COUNT = COUNT;
                        } else {
                            $STOP_COUNT = NO_COUNT;
                        }
                        unset($node);
                        break;
                    }
                }
            }
        }
        
        if ($this->nodeType->get('IsStructuredDocument')) {
            $structuredDocument = new StructuredDocument($this->get('IdNode'));
            $idLanguage = $structuredDocument->GetLanguage();
            $node = new Node($idLanguage);
            if ($node->get('IdNode') > 0) {
                if ($STOP_COUNT == COUNT) {
                    $STOP_COUNT = NO_COUNT;
                } else {
                    $STOP_COUNT = NO_COUNT_NO_RETURN;
                }
                $xmlBody .= $node->toXml($depth, $files, $recurrence);
                unset($node);
                
                $idTemplate = $structuredDocument->GetDocumentType();
                $node = new Node($idTemplate);
                $xmlBody .= $node->ToXml($depth, $files, $recurrence);
                if ($STOP_COUNT == NO_COUNT) {
                    $STOP_COUNT = COUNT;
                } else {
                    $STOP_COUNT = NO_COUNT;
                }
            }
            unset($node);
        }
        
        $xmlFooter = sprintf('</%s>', $nodeTypeName) . "\n";
        if (! $STOP_COUNT && defined('COMMAND_MODE_XIMIO')) {
            global $PROCESSED_NODES, $LAST_REPORT, $TOTAL_NODES;
            $PROCESSED_NODES ++;
            
            $processedNodes = $TOTAL_NODES > 0 ? (int) (($PROCESSED_NODES * 100) / $TOTAL_NODES) : 0;
            if ($processedNodes > $LAST_REPORT) {
                echo sprintf(_("It has been processed a %s%% of the nodes"), $processedNodes);
                echo sprintf("\n");
                echo sprintf(_("The last processed node was %s"), $this->get('Name'));
                echo sprintf("\n");
                $LAST_REPORT = $processedNodes;
            }
        }
        
        unset($nodeTypeName, $idNode, $nodeName, $nodeTypeClass, $statusName, $sharedWorkflow, $tail);
        
        // If a recursive importation was applied, here is where recurrence is performed
        if (is_null($recurrence) || (! is_null($recurrence) && $depth <= $recurrence)) {
            $childrens = $this->GetChildren();
            if ($childrens) {
                reset($childrens);
                while (list (, $idChildren) = each($childrens)) {
                    $childrenNode = new Node($idChildren);
                    if (! ($childrenNode->get('IdNode') > 0)) {
                        Logger::warning(sprintf(_("It is being tried to load the node %s from the unexistent node %s"), $childrenNode->get('IdNode'), $this->get('IdNode')));
                        continue;
                    }
                    $xmlBody .= $childrenNode->toXml($depth, $files, $recurrence);
                    unset($childrenNode);
                }
            }
        }
        return $indexTabs . $xmlHeader . $xmlBody . $indexTabs . $xmlFooter;
    }

    /**
     * Function which determines if the name $name is valid for the nodetype $nodeTypeID,
     * nodetype is optional, if it is not passed, it is loaded from current node
     *
     * @param
     *            $name
     * @param int $idNodeType
     * @return bool
     */
    function IsValidName($name, $idNodeType = 0)
    {
        if ($idNodeType === 0) {
            if (! $this->nodeType) {
                $this->messages->add('Cannot obtain the node type in order to validate the node name', MSG_TYPE_ERROR);
                return false;
            }
            $idNodeType = $this->nodeType->get('IdNodeType');
        }
        $nodeType = new NodeType($idNodeType);
        $nodeTypeName = $nodeType->get('Name');
        // the pattern and the string must be in the same encode
        $pattern1 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.\s]+$/", XML::UTF8);
        $pattern2 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.\s\@\:\/\?\+\=\#\%\*\,]+$/", XML::UTF8);
        $pattern3 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.]+$/", XML::UTF8);
        $pattern4 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.\@]+$/", XML::UTF8);
        $name = Base::recodeSrc($name, XML::UTF8);
        unset($nodeType);
        if (! strcasecmp($nodeTypeName, 'Action') || ! strcasecmp($nodeTypeName, 'Group') || ! strcasecmp($nodeTypeName, 'Language') || ! strcasecmp($nodeTypeName, 'LinkFolder') || ! strcasecmp($nodeTypeName, 'LinkManager') || ! strcasecmp($nodeTypeName, 'Role') || ! strcasecmp($nodeTypeName, 'WorkflowState')) {
            return (preg_match($pattern1, $name) > 0);
        } elseif (! strcasecmp($nodeTypeName, 'Link')) {
            return (preg_match($pattern2, $name) > 0);
        } elseif (! strcasecmp($nodeTypeName, 'User')) {
            return (preg_match($pattern4, $name) > 0);
        } else {
            return (preg_match($pattern3, $name) > 0);
        }
    }

    /**
     *
     * @param
     *            $idNodeType
     * @param null $parent
     * @param bool $checkAmount
     * @return bool
     */
    function checkAllowedContent($idNodeType, $parent = NULL, $checkAmount = true)
    {
        if (is_null($parent)) {
            if (is_null($this->get('IdParent'))) {
                Logger::error(_('Error checking if the node is allowed - parent does not exist [1]'));
                return false;
            }
            $parent = $this->get('IdParent');
        }
        $parentNode = new Node($parent);
        if (! $parentNode->GetID()) {
            Logger::error(_('Error checking if the node is allowed - parent does not exist [2]'));
            $this->messages->add(_('The specified parent node does not exist'), MSG_TYPE_ERROR);
            return false;
        }
        
        $nodeAllowedContents = $parentNode->GetCurrentAllowedChildren();
        if (! $nodeAllowedContents) {
            Logger::error(sprintf(_("The parent %s does not allow any nested node from him"), $parent));
            $this->messages->add(_('This node type is not allowed in this position'), MSG_TYPE_ERROR);
            return false;
        }
        
        $nodeType = new NodeType($idNodeType);
        if (! $nodeType->GetID()) {
            Logger::error(sprintf(_("The introduced nodetype %s does not exist"), $idNodeType));
            $this->messages->add(_('The specified nodetype does not exist'), MSG_TYPE_ERROR);
            return false;
        }
        
        if (! in_array($idNodeType, $nodeAllowedContents)) {
            Logger::error("The nodetype $idNodeType is not allowed in the parent (idnode = " . $parent . ") - (idnodetype = " . $parentNode->get('IdNodeType') . ") which allowed nodetypes are: " . print_r($nodeAllowedContents, true));
            $this->messages->add(_('This node type is not allowed in this position'), MSG_TYPE_ERROR);
            return false;
        }
        
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT Amount from NodeAllowedContents WHERE IdNodeType = %s AND NodeType = %s', $dbObj->sqlEscapeString($parentNode->nodeType->get('IdNodeType')), $dbObj->sqlEscapeString($idNodeType));
        $dbObj->query($query);
        if (! $dbObj->numRows) {
            $this->messages->add(_('The node is not allowed inside this parent'), MSG_TYPE_ERROR);
            return false;
        }
        if (! $checkAmount) {
            return true;
        }
        
        $amount = $dbObj->getValue('Amount');
        if ($amount == 0) {
            return true;
        }
        
        $nodeTypesInParent = count($parentNode->GetChildren($idNodeType));
        if ($amount > $nodeTypesInParent) {
            return true;
        }
        
        $this->messages->add(_('No more nodes can be created in this folder type'), MSG_TYPE_ERROR);
        return false;
    }

    /**
     *
     * @param int $detailLevel
     * @return string
     */
    function toStr($detailLevel = DETAIL_LEVEL_LOW)
    {
        $details = sprintf("Nombre: %s\n", $this->get('Name'));
        $details .= sprintf("IdNodeType: %s\n", $this->get('IdNodeType'));
        
        if ($detailLevel <= DETAIL_LEVEL_LOW) {
            return $details;
        }
        
        $details .= sprintf("Description: %s\n", $this->get('Description'));
        if (is_object($this->class)) {
            $details .= sprintf("Type: %s\n", $this->nodeType->get('Name'));
        }
        $details .= sprintf("Path: %s\n", $this->GetPath());
        $details .= sprintf("Parent node: %s\n", $this->get('IdParent'));
        
        if ($detailLevel <= DETAIL_LEVEL_MEDIUM) {
            return $details;
        }
        
        return $details;
    }

    /**
     *
     * @param
     *            $property
     * @param bool $withInheritance
     * @return bool|null|array
     */
    function getProperty($property, $withInheritance = true)
    {
        if (! $this->get('IdNode')) {
            Logger::error('Cannot load the \'' . $property . '\' property without a node ID');
            return false;
        }
        if ($withInheritance) {
            $sql = "SELECT IdNode FROM FastTraverse WHERE IdChild = " . $this->get('IdNode') . " ORDER BY Depth ASC";
            
            $db = new \Ximdex\Runtime\Db();
            $db->Query($sql);
            
            while (! $db->EOF) {
                
                // Getting property
                if ($db->getValue('IdNode') < 1) {
                    break;
                }
                $nodeProperty = new \Ximdex\Models\NodeProperty();
                $propertyValue = $nodeProperty->getProperty($db->getValue('IdNode'), $property);
                
                if (! is_null($propertyValue)) {
                    return $propertyValue;
                }
                
                $db->Next();
            }
        } else {
            $nodeProperty = new \Ximdex\Models\NodeProperty();
            return $nodeProperty->getProperty($this->get('IdNode'), $property);
        }
        
        Logger::warning(sprintf(_("Property %s not found for node %d"), $property, $this->get('IdNode')));
        
        return null;
    }

    /**
     *
     * @param bool $withInheritance
     * @return array|null
     */
    function getAllProperties($withInheritance = false)
    {
        $returnValue = array();
        $nodeProperty = new \Ximdex\Models\NodeProperty();
        
        if ($withInheritance) {
            $sql = "SELECT IdNode FROM FastTraverse WHERE IdChild = " . $this->get('IdNode') . " ORDER BY Depth ASC";
            
            $db = new \Ximdex\Runtime\Db();
            $db->Query($sql);
            
            while (! $db->EOF) {
                
                // Getting property
                if ($db->getValue('IdNode') < 1) {
                    break;
                }
                $properties = $nodeProperty->find('Property, Value', 'IdNode = %s', array(
                    $db->getValue('IdNode')
                ));
                
                if (empty($properties)) {
                    $db->Next();
                    continue;
                }
                
                if (is_array($properties) && count($properties) > 0) {
                    foreach ($properties as $propertyInfo) {
                        if (array_key_exists($propertyInfo['Property'], $returnValue))
                            continue;
                        $returnValue[$propertyInfo['Property']][] = $propertyInfo['Value'];
                    }
                }
                
                $db->Next();
            }
        } else {
            
            $properties = $nodeProperty->find('Property, Value', 'IdNode = %s', array(
                $this->get('IdNode')
            ));
            if (empty($properties)) {
                return NULL;
            }
            
            foreach ($properties as $propertyInfo) {
                $returnValue[$propertyInfo['Property']][] = $propertyInfo['Value'];
            }
        }
        
        // Compactamos un poco el array
        foreach ($returnValue as $key => $propertyInfo) {
            if (count($propertyInfo) == 1) {
                $propertyInfo[$key] = $propertyInfo[0];
            }
        }
        
        return $returnValue;
    }

    /**
     * Returna boolean value for a property with 'true' or 'false'
     */
    /**
     *
     * @param
     *            $property
     * @param bool $withInheritance
     * @return bool
     */
    function getSimpleBooleanProperty($property, $withInheritance = true)
    {
        $property = $this->getProperty($property, $withInheritance);
        if (! ((is_array($property)) && ($property[0] == "true"))) {
            $value = false;
        } else {
            $value = true;
        }
        return $value;
    }

    /**
     *
     * @param
     *            $property
     * @param
     *            $value
     */
    function setSingleProperty($property, $value)
    {
        $nodeProperty = new \Ximdex\Models\NodeProperty();
        
        $properties = $nodeProperty->find('IdNodeProperty', 'IdNode = %s AND Property = %s AND Value = %s', array(
            $this->get('IdNode'),
            $property,
            $value
        ));
        if (empty($properties)) {
            /* $propertyValue = */
            $nodeProperty->create($this->get('IdNode'), $property, $value);
        }
    }

    /**
     *
     * @param
     *            $property
     * @param
     *            $values
     */
    function setProperty($property, $values)
    {
        // Removing previous values
        if (! is_array($values))
            $values = array(
                "0" => $values
            );
        $nodeProperty = new \Ximdex\Models\NodeProperty();
        $nodeProperty->deleteByNodeProperty($this->get('IdNode'), $property);
        
        // Adding new values
        
        $n = count($values);
        
        for ($i = 0; $i < $n; $i ++) {
            $this->setSingleProperty($property, $values[$i]);
        }
    }

    /**
     *
     * @param
     *            $property
     * @return bool|true
     */
    function deleteProperty($property)
    {
        if (! ($this->get('IdNode') > 0)) {
            $this->messages->add(_('The node over which property want to be deleted does not exist ') . $property, MSG_TYPE_WARNING);
            return false;
        }
        $nodeProperty = new \Ximdex\Models\NodeProperty();
        return $nodeProperty->deleteByNodeProperty($this->get('IdNode'), $property);
    }

    /**
     *
     * @param
     *            $property
     * @param
     *            $value
     * @return bool
     */
    function deletePropertyValue($property, $value)
    {
        if (! ($this->get('IdNode') > 0)) {
            $this->messages->add(_('The node over which property want to be deleted does not exist ') . $property, MSG_TYPE_WARNING);
            return false;
        }
        
        $nodeProperty = new \Ximdex\Models\NodeProperty();
        $properties = $nodeProperty->find('IdNodeProperty', 'IdNode = %s AND Property = %s AND Value = %s', array(
            $this->get('IdNode'),
            $property,
            $value
        ), MONO);
        
        // debug::log($properties);
        foreach ($properties as $idNodeProperty) {
            $nodeProperty = new \Ximdex\Models\NodeProperty($idNodeProperty);
            $nodeProperty->delete();
        }
        return true;
    }

    /**
     * This function overwrite the workflow_forward function.
     * The action function is then deprecated.
     *
     * @param int $idUser
     * @param int $idGroup
     * @return int status
     */
    function GetNextAllowedState($idUser, $idGroup)
    {
        if (! ($this->get('IdNode') > 0)) {
            return NULL;
        }
        
        if (! ($this->get('IdState') > 0)) {
            return NULL;
        }
        
        $user = new User($idUser);
        $idRole = $user->GetRoleOnNode($this->get('IdNode'), $idGroup);
        
        $role = new Role($idRole);
        $allowedStates = $role->GetAllowedStates();
        
        $idNextState = $this->get('IdState');
        if (is_array($allowedStates) && ! empty($allowedStates)) {
            
            $workflow = new WorkFlow($this->get('IdNode'), $idNextState);
            $idNextState = null;
            do {
                
                $idNextState = $workflow->GetNextState();
                
                if (empty($idNextState)) {
                    return NULL;
                } else if (in_array($idNextState, $allowedStates)) {
                    return $idNextState;
                }
                $workflow = new WorkFlow($this->get('IdNode'), $idNextState);
            } while (! $workflow->IsFinalState());
        }
        
        return NULL;
    }

    /**
     * Update node childs FastTraverse info
     */
    /**
     */
    function UpdateChildren()
    {
        $arr_children = $this->GetChildren();
        if (! empty($arr_children)) {
            foreach ($arr_children as $child) {
                $node_child = new Node($child);
                
                $node_child->UpdateFastTraverse();
                $node_child->RenderizeNode();
                
                $node_child->UpdateChildren();
            }
        }
    }

    /**
     * *
     *
     * @param
     *            $idPipeline
     */
    function updateToNewPipeline($idPipeline)
    {
        unset($idPipeline);
        
        $db = new \Ximdex\Runtime\Db();
        $query = sprintf("SELECT IdChild FROM FastTraverse WHERE IdNode = %d", $this->get('IdNode'));
        $db->Query($query);
        
        while (! $db->EOF) {
            $idNode = $db->GetValue('IdChild');
            $node = new Node($idNode);
            $idStatus = $node->getFirstStatus();
            if ($idStatus > 0) {
                $node->set('IdState', $idStatus);
                $node->update();
            }
            $db->Next();
        }
    }

    /**
     *
     * @return array
     */
    function GetLastVersion()
    {
        $sql = "SELECT V.IdVersion, V.Version, V.SubVersion, V.IdUser, V.Date, U.Name as UserName, V.File ";
        $sql .= " FROM Versions V INNER JOIN Users U on V.IdUser = U.IdUser ";
        $sql .= " WHERE V.IdNode = '" . $this->get('IdNode') . "' ";
        $sql .= " ORDER BY V.IdVersion DESC LIMIT 1 ";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numRows > 0) {
            if ($dbObj->GetValue("Version") == 0 && $dbObj->GetValue("SubVersion") == 0)
                $state = 0;
            elseif ($dbObj->GetValue("Version") != 0 && $dbObj->GetValue("SubVersion") == 0)
                $state = 1;
            else
                $state = 2;
            
            return array(
                "IdVersion" => $dbObj->GetValue("IdVersion"),
                "Version" => $dbObj->GetValue("Version"),
                "SubVersion" => $dbObj->GetValue("SubVersion"),
                "Published" => $state,
                "IdUser" => $dbObj->GetValue("IdUser"),
                "Date" => $dbObj->GetValue("Date"),
                "UserName" => $dbObj->GetValue("UserName"),
                "File" => $dbObj->GetValue("File")
            );
        } else {
            $this->SetError(5);
        }
        return null;
    }

    /**
     * Return an array with all the layout schemas for the current node
     *
     * @return boolean|array
     */
    public function getLayoutSchemas()
    {
        if ($this->GetNodeType() != NodeTypeConstants::XML_ROOT_FOLDER) {
            $this->messages->add('The node is not the type of HTML container', MSG_TYPE_ERROR);
            return false;
        }
        
        // Load parent nodes
        $parents = FastTraverse::get_parents($this->GetID(), 'IdNodeType');
        if ($parents === false) {
            Logger::error('An error ocurred getting the parent nodes for the document with node ID: ' . $this->GetID());
            return false;
        }
        $schemas = array();
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::PROJECT:
                case NodeTypeConstants::SERVER:
                case NodeTypeConstants::SECTION:
                    
                    // Load the node for the section, server or project ID given
                    $node = new Node($nodeID);
                    if (! $node->GetID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }
                    
                    // Load the layouts root folder inside the previous node, if it exists
                    $layoutFolder = new Node($node->GetChildByType(NodeTypeConstants::HTML_LAYOUT_FOLDER));
                    if (! $layoutFolder->getID()) {
                        continue;
                    }
                    
                    // Load the JSON layout schemas
                    $schemas = $schemas + $layoutFolder->find('Name, IdNode', 'IdParent = %s AND IdNodeType = %s ORDER BY Name', array(
                        $layoutFolder->GetID(),
                        NodeTypeConstants::HTML_LAYOUT
                    ), MONO, false, 'Name');
                    
                    // If the is the project one, the process end
                    if ($nodeTypeID == NodeTypeConstants::PROJECT) {
                        break;
                    }
            }
        }
        return $schemas;
    }

    /**
     *
     * @param
     *            $type
     * @param int $schemaTypeID
     * @return NULL|array|bool
     */
    function getSchemas($type = NULL)
    {
        $idProject = $this->GetProject();
        if (! $idProject) {
            Logger::error(_('It was not possible to obtain the node project folder'));
            return NULL;
        }
        $project = new Node($idProject);
        if (! $project->getID()) {
            Logger::error('A project with ID: ' . $idProject . ' could not be obtained');
            return NULL;
        }
        $dirName = App::getValue("SchemasDirName");
        $folder = new Node($project->GetChildByName($dirName));
        if (! $folder->getID()) {
            Logger::error('Schemas folder could not be obtained');
            return NULL;
        }
        $schemas = $this->getProperty('DefaultSchema');
        if (empty($schemas)) {
            $schemas = NodeTypeConstants::VISUAL_TEMPLATE . ',' . NodeTypeConstants::RNG_VISUAL_TEMPLATE;
        } else {
            $schemas = implode(',', $schemas);
        }
        $schemas = $folder->find('IdNode', 'IdParent = %s AND IdNodeType in (%s) ORDER BY Name', array(
            $folder->get('IdNode'),
            $schemas
        ), MONO, false);
        if (! empty($type)) {
            foreach ($schemas as $key => $idSchema) {
                $schema = new Node($idSchema);
                $schemaType = $schema->getProperty('SchemaType');
                if (is_array($schemaType) && count($schemaType) == 1) {
                    $schemaType = $schemaType[0];
                }
                if ($schemaType != $type) {
                    unset($schemas[$key]);
                }
            }
        }
        if (! is_array($schemas)) {
            Logger::info(sprintf('The specified folder (%s) is not containing schemas', $folder->get('IdNode')));
            return;
        }
        return $schemas;
    }

    /**
     *
     * @param
     *            $destNodeId
     * @return array
     */
    function checkTarget($destNodeId)
    {
        if (! $destNodeId)
            return null;
        
        $changeName = 0; // assuming by default they're not the same
        $existing = 0;
        $amount = 0;
        $insert = 0; // by default, dont insert.
                     
        // $actionNodeId = $this->GetID();
        $actionNodeType = $this->Get('IdNodeType');
        
        $destNode = new Node($destNodeId);
        $destNodeType = $destNode->Get('IdNodeType');
        
        // parents data.
        $parent = new Node($this->GetParent()); // parent node
                                                // $parentname = $parent->Get('Name');
                                                
        // query to NodeAllowedContents
        $sql1 = "SELECT Amount FROM NodeAllowedContents WHERE IdNodeType=$destNodeType and NodeType=$actionNodeType";
        $db = new \Ximdex\Runtime\Db();
        $db->Query($sql1);
        while (! $db->EOF) {
            $amount = $db->getValue('Amount');
            $db->Next();
        }
        if ($amount == NULL) {
            $amount = - 1;
        } // If there is not a relation allowed, abort the copy.
          // query to FastTraverse
        $sql2 = "SELECT count(Depth) FROM FastTraverse WHERE FastTraverse.IdNode=$destNodeId and IdChild in (SELECT IdNode FROM Nodes WHERE IdNodeType=$actionNodeType) and Depth=1";
        $db->Query($sql2);
        while (! $db->EOF) {
            $existing = $db->getValue('count(Depth)');
            $db->Next();
        }
        if ($existing == NULL) {
            $existing = 0;
        } // dont exist a relation yet
          // first check, insert allowed?
        if ($amount == 0) {
            $insert = 1;
        } // destination node allows an infinite number of copies.
        else if ($amount == - 1) {
            $insert = 0;
        } // destination node does not allow this kind of content.
        else { // limited capacity.
            if ($amount > $existing) {
                $insert = 1;
            } // there is place for another copy.
        }
        
        // only if we can insert, we must check if the copy is going to be at the same level
        if ($insert == 1) {
            if ($destNodeId == $parent->Get('IdNode')) {
                $changeName = 1;
            } // coinciden. Copiamos el nodo al mismo nivel y debemos renombrarlo.
        }
        // $data=array('NodoCopia Id'=>$actionNodeId,'NodoCopia Tipo'=>$actionNodeType,'NodoDest Id'=>$destNodeId,'NodoDest Tipo'=>$destNodeType,'changeName'=>$changeName,'existing'=>$existing,'amount'=>$amount,'insert'=>$insert,'sentencia'=>$sql2);
        return array(
            'NodoDest Id' => $destNodeId,
            'NodoDest Tipo' => $destNodeType,
            'changeName' => $changeName,
            'insert' => $insert
        );
    }

    /**
     * @return bool
     */
    function IsModified()
    {
        $version = $this->GetLastVersion();
        if ($version["SubVersion"] == "0") {
            return false;
        }
        if ($version["Version"] == "0" && $version["SubVersion"] == "1") {
            return false;
        }
        return true;
    }
    
    /**
     * Get the mime type of the node content
     * 
     * @param Node $node
     * @return boolean|string
     */
    public function getMimeType(string $content = null)
    {
        if (!$this->GetID()) {
            Logger::error('No node ID has been specified');
            return false;
        }
        if ($content !== null) {
            
            // Get the mime type from given content
            $basePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot');
            $pointer = FsUtils::getUniqueFile($basePath);
            $file = $basePath . "/preview_" . $this->GetID() . '_' . $pointer;
            FsUtils::file_put_contents($file, $content);
        }
        else {
            
            // Get the mime type from node content
            $info = pathinfo($this->GetNodeName());
            if (strtolower($info['extension']) == 'css') {
                
                // CSS files return text/plain by default
                return 'text/css';
            }
            else {
                
                // Obtain the mime type from the last version of the file
                $version = $this->GetLastVersion();
                if (!isset($version['IdVersion']) or !$version['IdVersion']) {
                    Logger::error('There is no a version for node: ' . $this->GetID());
                    return false;
                }
                $versionID = $version['IdVersion'];
                $version = new Version($versionID);
                $file = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $version->get('File');
                if (!file_exists($file)) {
                    Logger::error('Cannot load the file: ' . $file . ' for version: ' . $versionID);
                    return false;
                }
            }
        }
        $mimeType = mime_content_type($file);
        if ($content) {
            @unlink($file);
        }
        if (!$mimeType) {
            Logger::error('Cannot load the mime type for the file: ' . $file);
            return false;
        }
        return $mimeType;
    }
    
    /**
     * Get the channel that will be available to render the node
     * If Channel parameter is provided and is in available ones, use it
     * 
     * @param int $channel
     * @return boolean|int
     */
    public function getTargetChannel(int $channel = null)
    {
        if (!$this->GetID()) {
            $this->messages->add('The node has not the ID value', MSG_TYPE_ERROR);
            return false;
        }
        if ($channel) {
            $strDoc = new StructuredDocument($this->GetID());
            if (!$strDoc->GetID()) {
                $this->messages->add('Cannot load the structured document with ID: ' . $this->GetID(), MSG_TYPE_WARNING);
                return false;
            }
            if ($strDoc->HasChannel($channel)) {
                
                // The document channel is in the inherited channels of the target document
                return $channel;
            }
        }
        
        // The channel will be the first one available in the inherited properties
        $properties = InheritedPropertiesManager::getValues($this->GetID());
        if (!$properties['Channel']) {
            Logger::error('The document with ID: ' . $this->GetID() . ' has no channels');
            return false;
        }
        foreach ($properties['Channel'] as $channelProperty) {
            if ($channelProperty['Inherited']) {
               return $channelProperty['Id'];
            }
        }
        // There is no channel available for the target document
        $this->messages->add('The target document ' . $this->GetID() . ' has not any channel available', MSG_TYPE_WARNING);
        return false;
    }
    
    /**
     * Return data to render the node to desired output with response headers
     * 
     * @param string $idChannel
     * @param string $showprev
     * @param string $content
     * @param string $version
     * @param string $subversion
     * @param string $mode
     * @return boolean|array
     */
    public function filemapper(string $idChannel = null, string $showprev = null, string $content = null
        , string $version = null, string $subversion = null, string $mode = null)
    {
        // Checks node existence
        if (! $this->GetID()) {
            $this->messages->add(_('It is not possible to show preview.') . _(' The node you are trying to preview does not exist.'), MSG_TYPE_NOTICE);
            return false;
        }
        
        // If the node is a structured document, render the preview, else return the file content
        if ($this->nodeType->GetIsStructuredDocument()) {
            
            // Checks if node is a structured document
            $structuredDocument = new StructuredDocument($this->GetID());
            if (! $structuredDocument->get('IdDoc')) {
                $this->messages->add(_('It is not possible to show preview.') . _(' Provided node is not a structured document.'), MSG_TYPE_NOTICE);
                return false;
            }
            
            // Checks content existence
            if ($content !== null) {
                $content = $structuredDocument->GetContent($version, $subversion);
            } elseif ($this->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                $content = XmlDocumentNode::normalizeXmlDocument($content);
            }
            
            // Get the available target channel
            $idChannel = $this->getTargetChannel($idChannel);
            if (!$idChannel) {
                $this->messages->add(_('It is not possible to show preview. There is not any defined channel.'), MSG_TYPE_NOTICE);
                return false;
            }
            
            // Populates variables and view/pipelines args
            $idSection = $this->GetSection();
            $idProject = $this->GetProject();
            $idServerNode = $this->getServer();
            $documentType = $structuredDocument->getDocumentType();
            $idLanguage = $structuredDocument->getLanguage();
            if ($this->GetNodeType() == NodeTypeConstants::XML_DOCUMENT and method_exists($this->class, "_getDocXapHeader")) {
                
                $docXapHeader = $this->class->_getDocXapHeader($idChannel, $idLanguage, $documentType);
            } else {
                $docXapHeader = null;
            }
            $nodeName = $this->get('Name');
            $depth = $this->GetPublishedDepth();
            
            // Initializes variables:
            $args = array();
            $args['NODEID'] = $this->GetID();
            $args['MODE'] = $mode == 'dinamic' ? 'dinamic' : 'static';
            $args['CHANNEL'] = $idChannel;
            $args['SECTION'] = $idSection;
            $args['PROJECT'] = $idProject;
            $args['SERVERNODE'] = $idServerNode;
            $args['LANGUAGE'] = $idLanguage;
            if ($this->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                $args['DOCXAPHEADER'] = $docXapHeader;
            }
            $args['NODENAME'] = $nodeName;
            $args['DEPTH'] = $depth;
            $args['DISABLE_CACHE'] = true;
            $args['CONTENT'] = $content;
            $args['NODETYPENAME'] = $this->nodeType->get('Name');
            if ($this->GetID() < 10000) {
                $idNode = 10000;
                $node = new Node($idNode);
                $transformer = $node->getProperty('Transformer');
            }
            else {
                $idNode = $this->GetID();
                $transformer = $this->getProperty('Transformer');
            }
            $args['TRANSFORMER'] = $transformer[0];
            if ($this->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                $process = 'HTMLToPrepared';
            } else {
                $process = 'StrDocToDexT';
            }
            $pipelineManager = new PipelineManager();
            $file = $pipelineManager->getCacheFromProcess(NULL, $process, $args);
            if ($file === false) {
                
                // The transformation process did not work !
                if ($this->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                    
                    // If content is false, show the xslt errors instead the document preview
                    $stDoc = new StructuredDocument($idNode);
                    $errors = $stDoc->GetXsltErrors();
                    if ($errors) {
                        $errors = str_replace("\n", "\n<br />\n", $errors);
                    }
                }
                if (!isset($errors)) {
                    $errors = 'The preview cannot be processed due to an unknown error';
                }
                $this->messages->add($errors, MSG_TYPE_WARNING);
                return false;
            }
            
            // Specific FilterMacros View for previsuals
            $viewFilterMacrosPreview = new ViewFilterMacros(true);
            $filePrev = $viewFilterMacrosPreview->transform(NULL, $file, $args, $idNode, $idChannel);
            if (strpos($file, App::getValue('TempRoot')) and file_exists($file)) {
                @unlink($file);
            }
            if ($filePrev === false) {
                $this->messages->add('Cannot transform the document ' . $this->GetNodeName() . ' for a preview operation', MSG_TYPE_WARNING);
                return false;
            }
            $content = FsUtils::file_get_contents($filePrev);
            if (strpos($filePrev, App::getValue('TempRoot')) and file_exists($filePrev)) {
                @unlink($filePrev);
            }
            if ($content === false) {
                return false;
            }
        }
        else {
            
            // Node is not a structured document
            $content = $this->GetContent();
            if ($content === false) {
                $this->messages->add('Cannot get the content from file ' . $this->GetNodeName() . ' for a preview operation', MSG_TYPE_WARNING);
                return false;
            }
        }
        $headers = array();
        if ($this->nodeType->GetIsStructuredDocument()) {
            
            // Get mime type for structured documents
            $mimeType = $this->getMimeType($content);
        }
        else {
            
            // Response headers for non structured documents
            $mimeType = $this->getMimeType();
            $headers['Content-Disposition'] = 'attachment; filename=' . $this->GetNodeName();
            $headers['Content-Length'] = strlen(strval($content));
        }
        
        // Common response headers
        $headers['Content-type'] = $mimeType;
        $headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
        $headers['Last-Modified'] = gmdate('D, d M Y H:i:s') . ' GMT';
        $headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
        $headers['Pragma'] = 'no-cache';
        
        // Return the obtained information
        $data = array();
        $data['content'] = $content;
        $data['headers'] = $headers;
        return $data;
    }
    
    /**
     * Return true if the node has included a language in its path, in prefix mode; or null instead
     * 
     * @return boolean|NULL
     */
    public function hasLangPath()   
    {
        $structuredDocument = new StructuredDocument($this->GetID());
        if (!$structuredDocument->get('IdLanguage')) {
            $error = 'Language has not been specified for document: ' . $this->GetNodeName();
            $this->messages->add($error, MSG_TYPE_ERROR);
            Logger::error($error);
            return false;
        }
        $nodeProperty = new NodeProperty();
        $property = $nodeProperty->getProperty($this->getServer(), NodeProperty::DEFAULTSERVERLANGUAGE);
        if ($property) {
            if ($structuredDocument->get('IdLanguage') != $property[0]) {
                
                // Language of the document is different than the default server
                return true;
            }
        }
        return null;
    }
}