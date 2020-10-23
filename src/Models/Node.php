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

namespace Ximdex\Models;

use DOMDocument;
use Ximdex\Deps\DepsManager;
use Ximdex\Logger;
use Ximdex\Models\ORM\NodesOrm;
use Ximdex\NodeTypes\Factory;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\NodeTypeGroupConstants;
use Ximdex\NodeTypes\XmlDocumentNode;
use Ximdex\Nodeviews\ViewFilterMacros;
use Ximdex\Parsers\ParsingDependencies;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\Messages;
use Ximdex\Runtime\Session;
use Ximdex\XML\Base;
use Ximdex\XML\XML;
use Ximdex\NodeTypes\XsltNode;

define('DETAIL_LEVEL_LOW', 0);
define('DETAIL_LEVEL_MEDIUM', 1);
define('DETAIL_LEVEL_HIGH', 2);

if (! defined('COUNT')) {
    define('COUNT', 0);
    define('NO_COUNT', 1);
    define('NO_COUNT_NO_RETURN', 2);
}

/**
 * Class which implements the specific methos for this nodetype
 *
 * @package Ximdex\Models
 */
class Node extends NodesOrm
{
    const ID_XIMDEX = 1;
    
    const ID_CONTROL_CENTER = 2;
    
    const ID_PROJECTS = 10000;
    
    /**
     * current node ID
     * 
     * @var bool|string
     */
    public $nodeID;
    
    /**
     * @var mixed
     */
    public $class;
    
    /**
     * NodeType object
     * 
     * @var $nodeType \Ximdex\Models\NodeType
     */
    public $nodeType;

    /**
     * DB object which will be used in the methods
     * 
     * @var $dbObj \Ximdex\Runtime\Db
     */
    public $dbObj;
    
    /**
     * Error code
     */
    public $numErr;

    /**
     * Error message
     */
    public $msgErr;

    /**
     * Class error list
     * 
     * @var array
     */
    public $errorList = [];

    /**
     * Node constructor
     *
     * @param int $nodeID
     * @param bool $fullLoad
     */
    public function __construct(int $nodeID = null, bool $fullLoad = true)
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
        $this->flagErr = false;
        $this->autoCleanErr = true;
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
                $this->class = Factory::getNodeTypeByName($nodeTypeClass, $this, $nodeTypeModule);
                if (! $fullLoad) {
                    return;
                }
            }
        }
    }

    public function getRoot()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query("SELECT IdNode FROM Nodes WHERE IdParent IS null");
        if ($dbObj->numRows) {
            return $dbObj->getValue('IdNode');
        }
        $this->SetError(6);
        return NULL;
    }

    public function getID()
    {
        return $this->IdNode;
    }

    /**
     * @param null $nodeID
     */
    function SetID($nodeID = null)
    {
        $this->clearError();
        self::__construct($nodeID);
    }

    public function getNodeName()
    {
        $this->clearError();
        return $this->get('Name');
    }

    /**
     * Returns the list of paths relative to project, of all the files and directories with belong to the node in the file system
     *
     * @param mixed $channel
     * @return mixed
     */
    public function getPublishedNodeName(int $channel = null)
    {
        $this->clearError();
        if (! $this->get('IdNode')) {
            $this->setError(1);
            return null;
        }
        return $this->class->getPublishedNodeName($channel);
    }

    /**
     * Changes node name
     *
     * @param $name
     * @return boolean
     */
    public function setNodeName(string $name)
    {
        // It is a renamenode alias
        return $this->renameNode($name);
    }

    /**
     * Returns the nodetype ID
     *
     * @return bool|string
     */
    public function getNodeType()
    {
        return $this->get('IdNodeType');
    }

    public function getTypeName()
    {
        return $this->nodeType->get('Name');
    }

    /**
     * Changes the nodetype
     *
     * @param $nodeTypeID
     * @return boolean|number|NULL|string
     */
    public function setNodeType(int $nodeTypeID)
    {
        if (! $this->get('IdNode')) {
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
    public function getDescription()
    {
        return $this->get('Description');
    }

    /**
     * Changes the node description
     *
     * @param string $description
     * @return boolean|boolean|number|NULL|string
     */
    function setDescription(string $description = null)
    {
        if (! $this->get('IdNode')) {
            $this->setError(2);
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
    public function getState()
    {
        $this->clearError();
        return $this->get('IdState');
    }

    /**
     * Changes the node workflow state
     *
     * @param int $stateID
     * @return boolean
     */
    public function setState(int $stateID): bool
    {
        if (! $this->IdNode) {
            $this->messages->add(_('The node ID is mandatory in order to change the state'), MSG_TYPE_ERROR);
            return false;
        }
        $workflowStatus = new WorkflowStatus($stateID);
        if (! $workflowStatus->get('id')) {
            $this->messages->add(sprintf(_('The state %s does not exist'), $stateID), MSG_TYPE_ERROR);
            return false;
        }
        if ($workflowStatus->get('action')) {

            // Call a specified action in this transition to the new state
            $actions = Workflow::getActions();
            if (!isset($actions[$workflowStatus->get('action')])) {
                $this->messages->add(sprintf(_('The action %s does not exist'), $workflowStatus->get('action')), MSG_TYPE_ERROR);
                return false;
            }
            $action = explode('@', $workflowStatus->get('action'));
            $className = Workflow::WORKFLOW_ACTIONS_NAMESPACE . $action[0];
            $class = new $className($this);
            $method = $action[1];
            Logger::info('Calling method ' . $method . ' in ' . $action[0] . ' class before changing the status to ' 
                . $workflowStatus->get('name'));
            if ($class->$method() === false) {
                if ($class->_getError()) {
                    $error = $class->_getError();
                } else {
                    $error = 'The action ' . $method . ' (' . $action[0] . ') is not working propertly';
                }
                $this->messages->add($error, MSG_TYPE_ERROR);
                Logger::error($error);
                return false;
            }
            Logger::info('Method ' . $method . ' (' . $className . ') for node ' . $this->IdNode . ' executed', true);
        }
        $this->set('IdState', $stateID);
        $result = $this->update();
        if (! $result) {
            $this->messages->add(sprintf(_('The node could not be moved to state %s'), $stateID), MSG_TYPE_ERROR);
            return false;
        }
        
        // Update new state in shared worflow nodes
        $slaves = $this->getWorkFlowSlaves();
        if ($slaves === false) {
            $this->messages->add(sprintf(_('Could not retrieve the workflow slaves for node %s'), $this->IdNode, MSG_TYPE_ERROR));
            return false;
        }
        foreach ($slaves as $id) {
            $slave = new Node($id);
            if (! $slave->SetState($stateID)) {
                $this->messages->mergeMessages($slave->messages);
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the node icon
     *
     * @return bool|null|string
     */
    public function getIcon()
    {
        $this->clearError();
        if (($this->get('IdNode') > 0)) {
            if (method_exists($this->class, 'GetIcon')) {
                return $this->class->getIcon();
            }
            return $this->nodeType->getIcon();
        }
        $this->setError(1);
        return NULL;
    }

    /**
     * Returns the list of channels for the node
     *
     * @return null
     */
    public function getChannels()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->getChannels($this->IdNode);
        }
        $this->setError(1);
        return NULL;
    }

    /**
     * Returns the node parent ID
     *
     * @return bool|string
     */
    public function getParent()
    {
        $this->clearError();
        return $this->IdParent;
    }

    /**
     * Changes the node parent
     *
     * @param int $parentID
     * @return boolean
     */
    public function setParent(int $parentID) : bool
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $parent = New Node($parentID);
            $children = $parent->getChildByName($this->get('Name'));
            if (! empty($children)) {
                $this->setError(8);
                return false;
            }
            $this->set('IdParent', $parentID);
            $result = $this->update();
            if (! $result) {
                $this->msgErr = _('Node could not be moved');
                $this->messages->add($this->msgErr, MSG_TYPE_ERROR);
                $this->numErr = 1;
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Returns the list of node children
     *
     * @param $idtype
     * @param $order
     * @return array
     */
    public function getChildren(int $idtype = null, array $order = null)
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
            $where .= sprintf(" ORDER BY %s %s", $order['FIELD'], isset($order['DIR']) 
                && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
        }
        return $this->find('IdNode', $where, $params, MONO);
    }

    /**
     * Returns a node list with the info for treedata
     *
     * @param int $idtype
     * @param array $order
     * @return array
     */
    public function getChildrenInfoForTree(int $idtype = null, array $order = null)
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
                $sql .= sprintf(" ORDER BY %s %s", $order['FIELD'], isset($order['DIR']) 
                    && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
            }
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->query($sql);
            $i = 0;
            while (! $dbObj->EOF) {
                $childrenList[$i]['id'] = $dbObj->getValue('IdNode');
                $childrenList[$i]['name'] = $dbObj->getValue('name');
                $childrenList[$i]['system'] = $dbObj->getValue('System');
                $i++;
                $dbObj->next();
            }
            return $childrenList;
        }
        $this->setError(1);
        return array();
    }

    /**
     * Looks for a child node with same name
     *
     * @param $name : optional. If none is passed, considered name will be current node name
     * @return NULL|string|boolean
     */
    public function getChildByName(string $name = null)
    {
        if (empty($name)) {
            $name = $this->get('Name');
        }
        $this->clearError();
        if ($this->get('IdParent') > 0 && ! empty($name)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf("SELECT IdNode FROM Nodes WHERE IdParent = %d AND Name = %s", $this->get('IdNode')
                , $dbObj->sqlEscapeString($name));
            $dbObj->Query($sql);
            if ($dbObj->numRows > 0) {
                return $dbObj->getValue('IdNode');
            }
            return false;
        }
        return false;
    }

    /**
     * Looks for one or more children nodes with the given type
     *
     * @param int $type
     * @return NULL|string|boolean
     */
    public function getChildByType(int $type = null)
    {
        if (empty($type)) {
            $type = $this->getNodeType();
        }
        $this->clearError();
        if ($this->getParent() and ! empty($type)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf("SELECT IdNode FROM Nodes WHERE IdParent = %d AND IdNodeType = %s", $this->IdNode, $type);
            $dbObj->query($sql);
            if ($dbObj->numRows) {
                return $dbObj->getValue('IdNode');
            }
        }
        return false;
    }

    /**
     * Looks for nodes by name
     *
     * @param string $name name, optional. If none is passed, considered name will be current node name
     * @return array|bool
     */
    public function getByName(string $name = null)
    {
        if (empty($name)) {
            $name = $this->get('Name');
        }
        $this->clearError();
        if (! empty($name)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf("SELECT Nodes.IdNode, Nodes.Name, NodeTypes.Icon, Nodes.IdParent FROM Nodes, NodeTypes " . 
                "WHERE Nodes.IdNodeType = NodeTypes.IdNodeType AND Nodes.Name like %s", $dbObj->sqlEscapeString("%" . $name . "%"));
            $dbObj->Query($sql);
            if ($dbObj->numRows > 0) {
                $result = array();
                while (! $dbObj->EOF) {
                    $node_t = new Node($dbObj->getValue('IdNode'));
                    if ($node_t) {
                        $children = count($node_t->getChildren());
                    } else {
                        $children = 0;
                    }
                    $result[] = array(
                        'IdNode' => $dbObj->getValue('IdNode'),
                        'Name' => $dbObj->getValue('Name'),
                        'Icon' => $dbObj->getValue('Icon'),
                        'Children' => $children
                    );
                    $dbObj->Next();
                }
                return $result;
            }
            return false;
        }
        return false;
    }

    public function getByNameAndPath(string $name = null, string $path = null)
    {
        if (empty($name)) {
            $name = $this->get('Name');
        }
        if (empty($path)) {
            $path = $this->get('Path');
        }
        $result = array();
        $this->clearError();
        if (! empty($name) && ! empty($path)) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = sprintf('SELECT Nodes.IdNode, Nodes.Name, NodeTypes.Icon, Nodes.IdParent FROM Nodes, NodeTypes
				WHERE Nodes.IdNodeType = NodeTypes.IdNodeType
				AND Nodes.Name like %s
				AND Nodes.Path like %s', $dbObj->sqlEscapeString($name), $dbObj->sqlEscapeString($path . "%" ));
            $dbObj->query($sql);
            while (! $dbObj->EOF) {
                $result[] = array(
                    'IdNode' => $dbObj->getValue('IdNode')
                );
                $dbObj->next();
            }
            return $result;
        }
        return false;
    }

    /**
     * Returns a list of paths relatives to the project of all the files and directories belonging to the node in filesystem
     *
     * @return null|array
     */
    public function getPathList()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->getPathList();
        }
        $this->setError(1);
        return null;
    }

    /**
     * Returns de node path (ximdex hierarchy!!! no file system one!!!)
     *
     * @param bool $advanced If is true return key-value, else return string
     * @return null|string
     */
    public function getPath(bool $advanced = false)
    {
        $path = $this->_getPath($advanced);
        if (! $path) {
            Logger::warning('Model::Node::getPath(): Path can not be deduced from idNode: ' . $this->IdNode);
        }
        return $path;
    }

    /**
     * Returns de node path
     *
     * @param bool $advanced If is true return key-value, else return string
     * @return null|string|array
     */
    private function _getPath(bool $advanced = false)
    {
        $this->clearError();
        $idNode = $this->get('IdNode');
        if ($idNode > 0) {
            $sql = "select Name, ft.idNode from FastTraverse ft inner join Nodes n on ft.idNode = n.idNode
					where ft.IdChild = $idNode
					order by depth desc";
            $db = new \Ximdex\Runtime\Db();
            $db->query($sql);
            $path = $advanced ? [] : '';
            while (! $db->EOF) {
                if ($advanced) {
                    $path[$db->getValue('idNode')] = $db->getValue('Name');
                } else {
                    $path .= '/' . $db->getValue('Name');
                }
                $db->next();
            }
            return $path;
        }
        $this->setError(1);
        return NULL;
    }

    public function getPublishedPath(int $channelID = null, bool $addNodeName = false, bool $structure = false, bool $addLanguagePrefix = true)
    {
        return $this->class->getPublishedPath($channelID, $addNodeName, $structure, $addLanguagePrefix);
    }

    /**
     * If it is contained, returns the relative path from node $nodeID
     *
     * @param int $nodeID
     * @param Node $nodeReplace
     * @return string|NULL
     */
    public function getRelativePath(int $nodeID, Node $nodeReplace = null)
    {
        $this->ClearError();
        if ($this->get('IdNode')) {
            if ($this->IsOnNode($nodeID)) {
                $nodes = FastTraverse::getParents($this->get('IdNode'), 'Name', 'ft.IdNode');
                if ($nodes) {
                    $path = '';
                    foreach ($nodes as $parentId => $name) {
                        if ($nodeReplace and $parentId == $nodeReplace->getID()) {
                            $nodeName = $nodeReplace->getNodeName();
                        } else {
                            $nodeName = $name;
                        }
                        $path = '/' . $nodeName . $path;
                        if ($parentId == $nodeID) {
                            break;
                        }
                    }
                    return $path;
                }
            } else {
                $this->SetError(1);
            }
        }
        return null;
    }

    /**
     * Returns if a node is contained in the node with id $nodeID
     *
     * @param int $nodeID
     * @return bool
     */
    public function isOnNode(int $nodeID)
    {
        $this->clearError();
        if ($this->IdNode > 0) {
            $nodes = FastTraverse::getParents($this->IdNode);
            if ($nodes === false) {
                return false;
            }
            foreach ($nodes as $parentId) {
                if ($parentId == $nodeID) {
                    return true;
                }
            }
            return false;
        }
        $this->setError(1);
        return false;
    }

    /**
     * Returns if a node is contained in the node with nodetype $nodeTypeID
     * 
     * @param int $nodeTypeID
     * @return boolean
     */
    public function isOnNodeWithNodeType(int $nodeTypeID)
    {
        $this->clearError();
        if ($this->IdNode > 0) {
            $nodes = FastTraverse::getParents($this->IdNode, 'IdNodeType');
            if ($nodes === false) {
                return false;
            }
            foreach ($nodes as $idNodeType) {
                if ($idNodeType == $nodeTypeID) {
                    return true;
                }
            }
            return false;
        }
        $this->setError(1);
        return false;
    }

    /**
     * Returned the Id of the nearest parent which can attach groups (nodeType)
     * 
     * @param Node $node
     * @return boolean|NULL|mixed
     */
    public function getNearest(Node $node)
    {
        $this->clearError();
        if ($this->IdNode) {
            $parent = FastTraverse::getParents($node->getID(), null, null, ['CanAttachGroups' => 1], 1);
            if ($parent === false) {
                return false;
            }
            if (! $parent) {
                return null;
            }
            return current($parent);
        }
        $this->setError(1);
        return false;
    }

    /**
     * Returns a path in the file system from where children are pending
     * Function used for renderization
     * 
     * @return string|NULL
     */
    public function getChildrenPath()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->getChildrenPath();
        }
        $this->setError(1);
        return null;
    }

    /**
     * Returns a list of allowed nodetypes
     *
     * @return array
     */
    public function getCurrentAllowedChildren()
    {
        $query = sprintf("SELECT NodeType FROM NodeAllowedContents WHERE IdNodeType = %d", $this->nodeType->getID());
        $allowedChildrens = array();
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        while (! $dbObj->EOF) {
            $allowedChildrens[] = $dbObj->getValue('NodeType');
            $dbObj->next();
        }
        return $allowedChildrens;
    }

    /**
     * Renders a node in the file system
     *
     * @param null $recursive
     * @return boolean
     */
    public function renderizeNode(bool $recursive = null) : bool
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($this->nodeType->get('IsRenderizable')) {
                if ($this->nodeType->get('HasFSEntity')) {
                    if ($this->class->renderizeNode() === false) {
                        return false;
                    }
                }
                if ($recursive) {
                    $children = $this->getChildren();
                    if (! empty($children)) {
                        foreach ($children as $childID) {
                            $child = new Node($childID);
                            if ($child->renderizeNode(true) === false) {
                                return false;
                            }
                        }
                    }
                }
            }
        } else {
            $this->setError(1);
            return false;
        }
        return true;
    }

    /**
     * Returns a node content
     * 
     * @param int $version
     * @param int $subversion
     * @return string|boolean|NULL
     */
    public function getContent(int $version = null, int $subversion = null)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->getContent($version, $subversion);
        }
        return null;
    }

    /**
     * Set a node content
     *
     * @param string $content
     * @param bool $commitNode
     * @return bool
     */
    public function setContent(string $content, bool $commitNode = false) : bool
    {
        $this->clearError();
        if ($this->IdNode) {

            // Validate HTML or XML valid contents (including XSL schemas)
            if ($this->getNodeType() and $content) {
                $res = true;
                if ($this->getNodeType() == NodeTypeConstants::XSL_TEMPLATE or $this->getNodeType() == NodeTypeConstants::XML_DOCUMENT
                    or $this->getNodeType() == NodeTypeConstants::RNG_VISUAL_TEMPLATE) {
                    $GLOBALS['errorsInXslTransformation'] = array();

                    // Check the valid XML
                    $domDoc = new DOMDocument();
                    $res = @$domDoc->loadXML($content);
                    if ($res === false) {
                        Logger::warning('Invalid XML for IdNode: ' . $this->IdNode . ' (' . $this->getDescription() . ')');
                        $error = Messages::error_message('DOMDocument::loadXML(): ');
                        if ($error) {
                            $error = 'Invalid XML content for node: ' . $this->IdNode . ' (' . $error . ')';
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
                        Logger::warning('Saving content: Invalid HTML for node: ' . $this->IdNode . ' ' . $this->getDescription());
                    }
                }

                // Check dependencias for HTML and XML documents
                if ($res and $this->getNodeType() == NodeTypeConstants::XML_DOCUMENT or $this->getNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                    if ($this->getNodeName() != 'templates_include.xsl') {

                        // dotdot dependencies only can be checked in templates under a server node
                        $templatesNode = new Node($this->getParent());
                        if ($templatesNode->getNodeType() == NodeTypeConstants::TEMPLATES_ROOT_FOLDER) {
                            $projectNode = new Node($templatesNode->getParent());
                            if ( in_array( $projectNode->GetNodeType(),NodeTypeGroupConstants::NODE_PROJECTS ) )
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
                                    . ' for IdNode: ' . $this->IdNode . ' (' . $this->getDescription() . ')');
                                $GLOBALS['parsingDependenciesError'] = null;
                            }
                        }

                        // Check the pathto dependencies
                        ParsingDependencies::getPathTo($content, $this->IdNode);
                        if (isset($GLOBALS['parsingDependenciesError']) and $GLOBALS['parsingDependenciesError']) {
                            $this->messages->add($GLOBALS['parsingDependenciesError'], MSG_TYPE_WARNING);
                            Logger::warning('Parsing pathTo dependencies: ' . $GLOBALS['parsingDependenciesError']
                                . ' for IdNode: ' . $this->IdNode . ' (' . $this->getDescription() . ')');
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
                        Logger::warning('Saving content: Invalid RNG template for node: ' . $this->IdNode . ' ' . $this->getDescription()
                            . ' (' . $error . ')');
                        $GLOBALS['errorsInXslTransformation'] = [$error];
                    }
                }

                // Validation of the JSON schemas
                if ( $this->getNodeType() == NodeTypeConstants::HTML_LAYOUT
                    || $this->getNodeType() == NodeTypeConstants::HTML_COMPONENT
                    || $this->getNodeType() == NodeTypeConstants::JSON_SCHEMA_FILE
                    || $this->getNodeType() == NodeTypeConstants::JSON_DOCUMENT
                ) {
                    $res = json_decode($content);
                    if ($res === null or $res === false) {
                        $error = 'Invalid JSON schema';
                        $this->messages->add($error, MSG_TYPE_WARNING);
                        Logger::warning('Saving content: Invalid JSON HTML schema for node: ' . $this->IdNode . ' ' . $this->getDescription());
                    }
                }
            }
            if ($this->class->setContent($content, $commitNode) === false) {
                $this->messages->mergeMessages($this->class->messages);
                return false;
            }
            $this->messages->mergeMessages($this->class->messages);
            if ($this->renderizeNode() === false)
                return false;
        }
        return true;
    }

    /**
     * Checks if the node is blocked and returns the blocker user id
     * 
     * @return boolean|string|NULL
     */
    public function isBlocked()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if (time() < ($this->get('BlockTime') + App::getValue('BlockExpireTime'))) {
                return $this->get('BlockUser');
            } else {
                $this->unBlock();
                return null;
            }
        }
        $this->setError(1);
        return null;
    }

    /**
     * Checks if the node is blocked, and returns the blocking time
     *
     * @return bool|null|string
     */
    public function getBlockTime()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if (time() < ($this->get('BlockTime') + App::getValue('BlockExpireTime'))) {
                return $this->get('BlockTime');
            } else {
                $this->unBlock();
                return null;
            }
        }
        $this->setError(1);
        return null;
    }

    /**
     * Blocks a node and returns the blocking timestamp
     *
     * @param $userID
     * @return bool|null|string
     */
    public function Block(int $userID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $currentBlockUser = $this->IsBlocked();
            if (! $currentBlockUser || $currentBlockUser == $userID) {
                $this->set('BlockTime', time());
                $this->set('BlockUser', $userID);
                $this->update();
                return $this->get('BlockTime');
            }
            return null;
        }
        $this->setError(1);
        return null;
    }

    /**
     * Delete a block
     */
    public function unBlock()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $this->set('BlockTime', null);
            $this->set('BlockUser', '');
            $this->update();
        } else {
            $this->setError(1);
        }
    }

    /**
     * Checks if node is renderized in the file system
     *
     * @return bool
     */
    public function isRenderized() : bool
    {
        $this->clearError();
        if ($this->get('IdNode')) {
            if (! $this->nodeType->get('IsRenderizable')) {
                return false;
            }
            if (! $this->isRenderizable()) {
                return true;
            }
            
            // Consigue el path hasta el directorio de nodos
            $absPath = XIMDEX_ROOT_PATH . App::getValue('NodeRoot');

            // Consigue la lista de paths del nodo
            $pathList = $this->class->getPathList();
            if (empty($pathList)) {
                return false;
            }
            $path = $absPath . $pathList;
            if (! file_exists($path)) {
                
                // Si falta alguna devuelve false
                return false;
            }

            // en otro caso devuelve true
            return true;
        }
        $this->setError(1);
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::add()
     */
    public function add(bool $useAutoIncrement = true)
    {
        die(_('This call should be done through CreateNode'));
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::update()
     */
    public function update()
    {
        $this->set('ModificationDate', time());
        return parent::update();
    }

    public function getFirstStatus(int $idParent = null, int $idNodeType = null)
    {
        if (empty($idParent)) {
            $idParent = $this->get('IdNode');
        }
        if (empty($idNodeType)) {
            $nodeType = $this->nodeType;
        } else {
            $nodeType = new NodeType($idNodeType);
        }
        $workflow = new Workflow($nodeType->getWorkflow());
        if ($workflow->get('id')) {
            $idStatus = $workflow->getInitialState();
            if ($idStatus) {
                return $idStatus;
            }
        }
        
        // Finally, I get it from the default value
        $workflow->loadMaster();
        return $workflow->getInitialState();
    }

    /**
     * Creates a new node and loads its ID in the class
     * 
     * @param string $name
     * @param int $parentID
     * @param int $nodeTypeID
     * @param int $stateID
     * @param array $subfolders
     * @return boolean|boolean|string
     */
    public function createNode(string $name, int $parentID, int $nodeTypeID, int $stateID = null, $subfolders = [])
    {
        $this->set('IdParent', (int) $parentID);
        $this->set('IdNodeType', (int) $nodeTypeID);
        $this->set('Name', $name);
        $this->set('CreationDate', time());
        $this->set('ModificationDate', time());
        $nodeType = new NodeType($nodeTypeID);
        $parentNode = new Node($this->get('IdParent'));

        // Set workflow state
        if ($nodeType->get('workflowId')) {
            if (! $stateID) {
                $stateID = $this->getFirstStatus($parentID, $nodeTypeID);
                if (! $stateID) {
                    $this->messages->add(_('Cannot load the fisrt state in create node'), MSG_TYPE_ERROR);
                    return false;
                }
            }
            $this->set('IdState', $stateID);
        } else {
            $this->set('IdState', null);
        }

        // check name, parentID and nodeTypeID
        if (! $name or ! $parentID or ! $nodeTypeID) {
            $this->setError(3);
            $this->messages->add(_('The name, parent or nodetype is missing'), MSG_TYPE_ERROR);
            return false;
        }

        // If nodetype is not existing, we are done
        if (! $nodeType->get('IdNodeType')) {
            $this->messages->add(_('The specified nodetype does not exist'), MSG_TYPE_ERROR);
            $this->setError(11);
            return false;
        }

        // Checking for correct name format
        if (! $this->isValidName($this->get('Name'), $this->get('IdNodeType'))) {
            $this->messages->add(_('Node name is not valid'), MSG_TYPE_ERROR);
            $this->setError(9);
            return false;
        }

        // If parent does not exist, we are done
        if (! $parentNode->get('IdNode')) {
            $this->messages->add(_('Parent node does not exist'), MSG_TYPE_ERROR);
            $this->setError(10);
            return false;
        }

        // Check if already exist a node with the same name under the current parent
        if (! $parentNode->getChildByName($this->get('Name')) === false) {
            $this->messages->add(_('There is already a node with this name under this parent'), MSG_TYPE_ERROR);
            $this->setError(8);
            return false;
        }

        // Node is not allowed to live there
        if (! $this->checkAllowedContent($nodeTypeID, $parentID)) {
            $this->messages->add(_('This node is not allowed under this parent'), MSG_TYPE_ERROR);
            $this->setError(17);
            return false;
        }

        // Generate the node ID in a range with specified node types
        $useAutoincrement = true;
        try {
            if ($this->generateIdForRange()) {
                $useAutoincrement = false;
            }
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            $this->messages->add(_('Error generating the node ID'), MSG_TYPE_ERROR);
            $this->setError(5);
            return false;
        }
        
        // Inserts the node in the Nodes table
        if (parent::add($useAutoincrement) === false) {
            return false;
        }
        if (! $this->get('IdNode')) {
            $this->messages->add(_('Error creating the node'), MSG_TYPE_ERROR);
            $this->setError(5);
            return false;
        }
        $this->setID($this->get('IdNode'));

        // Updating fastTraverse before the setcontent, because in the node cache this information is needed
        if (! $this->updateFastTraverse()) {
            return false;
        }

        // All the args from this function call are passed to this nodetype create method
        if (is_object($this->class)) {
            $argv = func_get_args();
            call_user_func_array(array(
                & $this->class,
                'createNode'
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
        $this->addGroupWithRole(Group::getGeneralGroup());

        // Get associated groups from the parent/s
        if ($nodeType->get('CanAttachGroups')) {
            $nearestId = $this->getNearest($parentNode);
            $nearest = new Node($nearestId);
            $associated = $nearest->getGroupList();
            if (count($associated) > 0) {
                foreach ($associated as & $group) {
                    $this->AddGroupWithRole($group, $user->getRoleOnGroup($group));
                }
            }
        }

        // If the create node type is Section (section inside a server)
        // it is checked if the user who created it belongs to some group
        // to include the relation between nodes and groups
        if ($this->nodeType->get('Name') == 'Section') {
            $grupos = $user->getGroupList();

            // The first element of the list $grupos is always the general group
            // this insertion is not considered as it the relation by default
            if (is_array($grupos)) {
                foreach ($grupos as $grupo) {
                    $this->addGroupWithRole($grupo, $user->getRoleOnGroup($grupo));
                }
            }
        }

        // Updating the hierarchy index for this node
        $this->renderizeNode();
        Logger::debug("Model::Node::CreateNode: Creating node id(" . $this->nodeID . "), name(" . $name . "), parent(" . $parentID . ").");

        // Once created, its content by default is added
        $dbObj = new \Ximdex\Runtime\Db();
        if (is_array($subfolders) and $subfolders) {
            $subfolders_str = implode(",", $subfolders);
            $query = sprintf("SELECT NodeType, Name, State, Params FROM NodeDefaultContents WHERE IdNodeType = %d AND NodeType in (%s)"
                , $this->get('IdNodeType'), $subfolders_str);
        } else {
            $query = sprintf("SELECT NodeType, Name, State, Params FROM NodeDefaultContents WHERE IdNodeType = %d", $this->get('IdNodeType'));
        }
        $dbObj->query($query);
        while (! $dbObj->EOF) {
            $childNode = new Node();
            Logger::debug("Model::Node::CreateNode: Creating child name(" . $this->get('Name') . "), type(" . $this->get('IdNodeType') . ").");
            $res = $childNode->createNode($dbObj->getValue('Name'), $this->get('IdNode'), $dbObj->getValue('NodeType')
                , $dbObj->getValue('State'));
            if ($res === false) {
                $this->messages->mergeMessages($childNode->messages);
                return false;
            }
            $dbObj->next();
        }
        $node = new Node($this->get('IdNode'));
        if ($nodeTypeID == NodeTypeConstants::TEMPLATES_ROOT_FOLDER) {
            
            // If the node is a type of templates folder or ximlets section, generates the templates_include.xsl inside
            $xsltNode = new \Ximdex\NodeTypes\XsltNode($node);
            if ($xsltNode->create_templates_include($node->getID()) === false) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        } elseif ($nodeTypeID == NodeTypeConstants::XIMLET_ROOT_FOLDER) {
            
            // If the node is a type of ximlets section, generates the relation with the templates folder
            $xsltNode = new \Ximdex\NodeTypes\XsltNode($node);
            if ($xsltNode->rel_include_templates_to_documents_folders($parentNode) === false) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        }
        return $node->get('IdNode');
    }

    public function delete()
    {
        return $this->deleteNode(true);
    }

    /**
     * Deletes a node and all its children
     *
     * @param bool $firstNode
     * @return bool|int|string
     */
    public function deleteNode(bool $firstNode = true)
    {
        if ($this->canDenyDeletion() && $firstNode) {
            $this->messages->add(_('Node deletion was denied'), MSG_TYPE_WARNING);
            return false;
        }
        $this->clearError();
        if (! $this->get('IdNode')) {
            $this->setError(1);
            return false;
        }
        $IdChildrens = $this->getChildren();
        if (! is_null($IdChildrens)) {
            foreach ($IdChildrens as $IdChildren) {
                $childrenNode = new Node($IdChildren);
                if ($childrenNode->get('IdNode') > 0) {
                    $childrenNode->deleteNode(false);
                } else {
                    $this->setError(4);
                }
            }
        }

        // Deleting from file system
        if ($this->nodeType->get('HasFSEntity')) {
            if ($this->isRenderizable()) {
                $absPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot");
                $deletablePath = $this->class->getPathList();
                $nodePath = $absPath . $deletablePath;
                if (is_dir($nodePath)) {
                    FsUtils::deltree($nodePath);
                } else {
                    FsUtils::delete($nodePath);
                }
            }
        }

        // first invoking the particular Delete...
        if ($this->getNodeType() != NodeTypeConstants::XSL_TEMPLATE) {
            if ($this->class->deleteNode() === false) {
                if ($this->class->messages) {
                    $this->messages->mergeMessages($this->class->messages);
                } else {
                    $this->messages->add(_('Error in concrete deletion operation'), MSG_TYPE_ERROR);
                }
                return false;
            }
        }

        // And the general one
        $data = new DataFactory($this->nodeID);
        $data->deleteAllVersions();
        unset($data);

        // if the folder is of structured documents type, the relation with templates folder will be deleted
        if ($this->nodeType->getIsStructuredDocument()) {
            $depsMngr = new DepsManager();
            $depsMngr->deleteBySource(DepsManager::DOCFOLDER_TEMPLATESINC, $this->nodeID);
        }

        // delete the references to the XML documents folders, if the node type is templates folder
        if ($this->nodeType->get('IdNodeType') == NodeTypeConstants::TEMPLATES_ROOT_FOLDER) {
            $depsMngr = new DepsManager();
            $depsMngr->deleteByTarget(DepsManager::STRDOC_TEMPLATE, $this->nodeID);

            // reload the dependencies to the documents folders if exist (with the templates folder node)
            $project = new Node($this->getProject());
            $xsltNode = new XsltNode($this);
            if (! $xsltNode->rel_include_templates_to_documents_folders($project)) {
                $this->messages->mergeMessages($xsltNode->messages);
                return false;
            }
        }
        $rtn = new RelSemanticTagsNodes();
        $rtn->deleteTags($this->nodeID);
        $res = parent::delete();
        if ($this->getNodeType() == NodeTypeConstants::XSL_TEMPLATE) {
            if ($this->class->deleteNode(false) === false) {
                $this->messages->mergeMessages($this->class->messages);
                return false;
            }
        }
        Logger::info("Node {$this->nodeID} deleted");
        $this->nodeID = null;
        $this->class = null;
        return $res;
    }

    public function canDenyDeletion()
    {
        if (is_object($this->class) && method_exists($this->class, 'CanDenyDeletion')) {
            return $this->class->CanDenyDeletion();
        }
        return true;
    }

    /**
     * Returns the list of nodes which depend on the one in the object
     *
     * @return array|null
     */
    public function getDependencies()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            return $this->class->getDependencies();
        }
        $this->setError(1);
        return NULL;
    }

    /**
     * Returns the set of nodes which depend in a direct or indirect way on the node which are in the object
     * (the set of verts of the dependency graph)
     *
     * @param array $excludeNodes
     * @return array|null
     */
    public function getGlobalDependencies(array $excludeNodes = array())
    {
        $this->clearError();
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
                        $brokenDeps = array_merge($brokenDeps, $dep->getGlobalDependencies($exclude));
                    }
                }
            }
            return (array_unique($brokenDeps));
        }
        $this->setError(1);
        return null;
    }

    /**
     * Changes the node name
     *
     * @param string $name
     * @return bool
     */
    public function renameNode(string $name) : bool
    {
        $folderPath = null;
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($this->get('Name') == $name) {
                return true;
            }
            
            // Checking if node name is in correct format
            if (! $this->isValidName($name)) {
                $this->setError(9);
                return false;
            }
            
            // Checking if the parent has no other child with same name
            $parent = new Node($this->get("IdParent"));
            $idChildren = $parent->getChildByName($name);
            if ($idChildren && $idChildren != $this->get("IdNode")) {
                $this->setError(8);
                return false;
            }
            $fsEntity = $this->nodeType->get('HasFSEntity');
            $isFile = ($fsEntity && ($this->nodeType->get('IsPlainFile') || $this->nodeType->get('IsStructuredDocument')));
            $isDir = ($fsEntity && ($this->nodeType->get('IsFolder') || $this->nodeType->get('IsVirtualFolder')));

            // If it is a directory or file, we cannot not allow the process to stop before finishing and leave it inconsistent
            if ($isDir || $isFile) {
                ignore_user_abort(true);
            }
            if ($isFile) {
                if ($this->isRenderizable()) {
                    $absPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot");
                    $deletablePath = $this->class->getPathList();
                    FsUtils::delete($absPath . $deletablePath);
                }
            }

            // Changing the name in the Nodes table
            $this->set('Name', $name);
            $this->update();
            
            // If this node type has nothing else to change, the method rename node of its specific class is called
            if ($this->class->renameNode($name) === false) {
                $this->messages->mergeMessages($this->class->messages);
                return false;
            }
            if ($isFile) {
                
                // The node is renderized, its children are lost in the filesystem
                $node = new Node($this->get('IdNode'));
                $node->renderizeNode();
            }
            if ($isDir) {
                if ($this->isRenderizable()) {
                    
                    // Temporal backup of children nodes. In this case, it is passed the path and a flag to specify that it is a path
                    $folderPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot") . $this->class->getChildrenPath();
                    
                    // Retrieving all children from the backup we kept, identified by $backupID
                    $parentNode = new Node($this->get('IdParent'));
                    $newPath = XIMDEX_ROOT_PATH . App::getValue("NodeRoot") . $parentNode->getChildrenPath() . '/' . $name;
                    rename($folderPath, $newPath);
                }
            }
            return true;
        }
        $this->setError(1);
        return false;
    }

    /**
     * Moves the node
     * 
     * @param int $targetNode
     * @return bool
     */
    public function moveNode(int $targetNode) : bool
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($targetNode > 0) {
                $target = new Node($targetNode);
                if (! $target->isOnNode($this->get('IdNode'))) {
                    ignore_user_abort(true);
                    $folderPath = XIMDEX_ROOT_PATH . App::getValue('NodeRoot') . $this->class->getChildrenPath();
                    if (! $this->setParent($targetNode)) {
                        return false;
                    }
                    
                    // FastTraverse is updated for current node
                    if (! $this->updateFastTraverse()) {
                        return false;
                    }
                    
                    // Node is renderized, so we lost its children in filesystem
                    if (! $this->renderizeNode(false)) {
                        return false;
                    }
                    
                    // Updating paths and FastTraverse of children (if existing)
                    if (! $this->updateChildren()) {
                        return false;
                    }

                    // If there is a new name, we change the node name
                    $name = FsUtils::get_name($this->getNodeName());
                    $ext = FsUtils::get_extension($this->getNodeName());
                    if ($ext != null && $ext != "") {
                        $ext = "." . $ext;
                    }
                    $newName = $name . $ext;
                    $index = 1;
                    while (($child = $target->getChildByName($newName)) > 0 && $child != $this->get("IdNode")) {
                        $newName = sprintf("%s_copia_%d%s", $name, $index, $ext);
                        $index++;
                    }
                    
                    // If there is no name change, we leave all as is.
                    if ($this->getNodeName() != $newName) {
                        $this->setNodeName($newName);
                    }
                    
                    // Remove old folder or file in data / nodes file system
                    if ($this->isRenderizable()) {
                        if ($this->getNodeType() == NodeTypeConstants::XSL_TEMPLATE) {
                            FsUtils::delete($folderPath);
                        } else {
                            $xsltNode = new XsltNode($this);
                            $xsltNode->reload_templates_include($this);
                            FsUtils::deltree($folderPath);
                        }
                    }
                } else {
                    $this->setError(12);
                    return false;
                }
            } else {
                $this->setError(3);
                return false;
            }
        } else {
            $this->setError(1);
            return false;
        }
        return true;
    }

    /**
     * Returns a list of groups associated to this node
     *
     * @return array
     */
    public function getGroupList()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if (! $this->nodeType->get('CanAttachGroups')) {
                $parent = $this->get('IdParent');
                if ($parent) {
                    $parent = new Node($parent);
                    if ($parent->get('IdNode') > 0) {
                        $groupList = $parent->getGroupList();
                    } else {
                        $groupList = array();
                    }
                } else {
                    $groupList = array();
                }
            } else {
                $dbObj = new \Ximdex\Runtime\Db();
                $dbObj->query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));
                $groupList = array();
                while (! $dbObj->EOF) {
                    $groupList[] = $dbObj->getValue("IdGroup");
                    $dbObj->next();
                }
            }
            return $groupList;
        }
        $this->setError(1);
        return [];
    }

    /**
     * Returns the list of groups associated to this node
     *
     * @param int $groupID
     * @return null|String
     */
    public function getRoleOfGroup(int $groupID)
    {
        $this->clearError();
        if ($this->get('IdNode')) {
            if (! $this->nodeType->get('CanAttachGroups')) {
                $parent = $this->getParent();
                if ($parent) {
                    if (! $this->numErr) {
                        $node = new Node($parent);
                        $role = $node->getRoleOfGroup($groupID);
                        if (! $node->numErr) {
                            return $role;
                        }
                    }
                }
            } else {
                $sql = sprintf("SELECT IdRole FROM RelGroupsNodes WHERE IdNode = %d AND IdGroup = %d", $this->get('IdNode'), $groupID);
                $dbObj = new \Ximdex\Runtime\Db();
                $dbObj->query($sql);
                if ($dbObj->numRows > 0) {
                    return $dbObj->getValue("IdRole");
                } else {
                    $this->setError(5);
                }
            }
        }
        $this->setError(1);
        return null;
    }

    /**
     * Returns the list of users associated to this node
     *
     * @param bool $ignoreGeneralGroup
     * @return array|null
     */
    public function getUserList(bool $ignoreGeneralGroup = null)
    {
        $this->clearError();
        $group = new Group();
        if ($this->get('IdNode') > 0) {
            $groupList = $this->getGroupList();

            // Taking off the General Group if needed
            if ($ignoreGeneralGroup) {
                $groupList = array_diff($groupList, array(
                    Group::getGeneralGroup()
                ));
            }
            $userList = array();
            if (! $this->numErr) {
                foreach ($groupList as $groupID) {
                    $group = new Group($groupID);
                    $tempUserList = $group->getUserList();
                    $userList = array_merge($userList, $tempUserList);
                    unset($group);
                }
                return array_unique($userList);
            }
        }
        $this->setError(1);
        return null;
    }

    /**
     * This function does not delete an user from Users table, this disassociated from the group
     *
     * @param int $groupID
     */
    public function deleteGroup(int $groupID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($this->nodeType->get('CanAttachGroups')) {
                $dbObj = new \Ximdex\Runtime\Db();
                $query = sprintf("DELETE FROM RelGroupsNodes WHERE IdNode = %d AND IdGroup = %d", $this->get('IdNode'), $groupID);
                $dbObj->execute($query);
                if ($dbObj->numErr) {
                    $this->setError(5);
                }
            }
        } else {
            $this->setError(1);
        }
    }

    /**
     * Associated an user to a group with a concrete role
     * 
     * @param int $groupID
     * @param int $roleID
     * @return boolean
     */
    public function AddGroupWithRole(int $groupID, int $roleID = null)
    {
        $this->clearError();
        if (! is_null($groupID)) {
            if ($this->nodeType->get('CanAttachGroups')) {
                
                /*
                 * Raise an error when creating a new project from a template, node-group can't be repeated
                 * To prevent this problem, we are going to make a query of this ocurrence
                 */
                $dbObj = new \Ximdex\Runtime\Db();
                $sql = 'select * from RelGroupsNodes where IdGroup = ' . $groupID . ' and IdNode = ' . $this->get('IdNode');
                $res = $dbObj->query($sql);
                if ($res === false or $dbObj->numErr) {
                    $this->setError(5);
                    return false;
                }
                if ($dbObj->numRows) {
                    
                    // the relation between this node and the given group is defined already
                    return true;
                }
                if (! $roleID) {
                    $roleID = 'NULL';
                }
                $query = 'INSERT INTO RelGroupsNodes (IdGroup, IdNode, IdRole) VALUES (' . $groupID . ', ' . $this->get('IdNode') 
                    . ', ' . $roleID . ')';
                $dbObj->execute($query);
                if ($dbObj->numErr) {
                    $this->setError(5);
                    return false;
                }
                return true;
            }
        } else {
            $this->setError(1);
        }
        return false;
    }

    /**
     * It allows to change the role a user participates in a group with
     *
     * @param int $groupID
     * @param int $roleID
     */
    public function changeGroupRole(int $groupID, int $roleID = null)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($this->nodeType->get('CanAttachGroups')) {
                $sql = sprintf("UPDATE RelGroupsNodes SET IdRole = %s WHERE IdNode = %d AND IdGroup = %d"
                    , $roleID ? $roleID : 'NULL', $this->get('IdNode'), $groupID);
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
     * @param int $groupID
     * @return int|null
     */
    public function hasGroup(int $groupID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdGroup = %d AND IdNode = %d", $groupID, $this->get('IdNode')));
            if ($dbObj->numErr) {
                $this->setError(5);
            }
            return $dbObj->numRows;
        }
        $this->setError(1);
        return null;
    }

    public function getAllGroups()
    {
        $salida = array();
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->query(sprintf("SELECT IdGroup FROM RelGroupsNodes WHERE IdNode = %d", $this->get('IdNode')));
            if (! $dbObj->numErr) {
                while (!$dbObj->EOF) {
                    $salida[] = $dbObj->getValue("IdGroup");
                    $dbObj->next();
                }
                return $salida;
            } else {
                $this->setError(5);
            }
        }
        return null;
    }

    /**
     * Function which makes a node to have a workflow as other node and depends on it
     *
     * @param int $nodeID
     */
    public function setWorkFlowMaster(int $nodeID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($nodeID != $this->get('IdNode')) {
                $this->set('SharedWorkflow', $nodeID);
                $this->update();
            }
        } else {
            $this->setError(1);
        }
    }

    /**
     * Function which makes the node to have a new independent workflow
     */
    public function clearWorkFlowMaster()
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $this->set('SharedWorkflow', null);
            $this->update();
        } else {
            $this->setError(1);
        }
    }

    /**
     * Function which makes the node to have a new independent workflow
     * 
     * @return array|boolean
     */
    public function getWorkFlowSlaves()
    {
        return $this->find('IdNode', 'SharedWorkflow = %s', array($this->get('IdNode')), MONO);
    }

    /**
     * @return bool|string
     */
    function IsWorkflowSlave()
    {
        return $this->get('SharedWorkflow');
    }

    public function getAllAlias()
    {
        $this->clearError();
        if ($this->IdNode > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            $query = sprintf("SELECT IdLanguage, Name FROM NodeNameTranslations WHERE IdNode = %d", $this->IdNode);
            $dbObj->query($query);
            if ($dbObj->numRows) {
                $result = array();
                while (! $dbObj->EOF) {
                    $result[(string) $dbObj->getValue('IdLanguage')] = $dbObj->getValue('Name');
                    $dbObj->next();
                }
                return $result;
            }
        }
        return false;
    }

    /**
     * Obtains the current node alias
     *
     * @param int $langID
     * @return null|String
     */
    public function getAliasForLang(int $langID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $sql = sprintf('SELECT Name FROM NodeNameTranslations WHERE IdNode = %d AND IdLanguage = %d', $this->get('IdNode'), $langID);
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->query($sql);
            if ($dbObj->numErr) {
                $this->SetError(5);
            } else {
                if ($dbObj->numRows) {
                    return $dbObj->getValue('Name');
                }
            }
        } else {
            $this->setError(1);
        }
        return null;
    }

    /**
     * Controls if the current node has alias
     *
     * @param int $langID
     * @return null|String
     */
    public function hasAliasForLang(int $langID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $sql = sprintf('SELECT Name FROM NodeNameTranslations WHERE IdNode =  %d AND IdLanguage = %d', $this->get('IdNode'), $langID);
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->query($sql);
            if ($dbObj->numErr) {
                $this->setError(1);
            }
            return $dbObj->getValue('Name');
        }
        $this->setError(1);
        return null;
    }

    public function getAliasForLangWithDefault(int $langID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($alias = $this->hasAliasForLang($langID)) {
                return $alias;
            }
            $langDefault = App::getValue('DefaultLanguage');
            if (strlen($langDefault) != 0) {
                $lang = new Language();
                $lang->setByIsoName($langDefault);
                $alias = $this->hasAliasForLang($lang->get('IdLanguage'));
                if ($alias) {
                    
                    // Returns the default language
                    return $alias;
                }
            }
            return $this->getNodeName();
        }
        $this->setError(1);
        return null;
    }

    /**
     * Setting a alias to current node
     *
     * @param int $langID
     * @param string $name
     * @return bool
     */
    public function setAliasForLang(int $langID, string $name = null)
    {
        if ($this->get('IdNode') > 0) {
            if ($this->hasAliasForLang($langID)) {
                if ($name) {
                    
                    // Update alias
                    $sql = sprintf('UPDATE NodeNameTranslations SET Name = %s WHERE IdNode = %d AND IdLanguage = %d'
                        , $dbObj->sqlEscapeString($name), $this->get('IdNode'), $langID);
                } else {
                    
                    // Delete old name
                    $sql = sprintf('DELETE FROM NodeNameTranslations WHERE IdNode = %d AND IdLanguage = %d', $dbObj->sqlEscapeString($name)
                        , $this->get('IdNode'), $langID);
                }
            } elseif ($name) {
                
                // New name
                $sql = sprintf('INSERT INTO NodeNameTranslations (IdNode, IdLanguage, Name) VALUES (%d, %d, %s)'
                    , $this->get('IdNode'), $langID, $dbObj->sqlEscapeString($name));
            }
            if (isset($sql)) {
                $dbObj = new \Ximdex\Runtime\Db();
                if ($dbObj->execute($sql) === false or $dbObj->numErr) {
                    $this->messages->add(_('Alias could not be updated, incorrect operation'), MSG_TYPE_ERROR);
                    return false;
                }
            }
            return true;
        }
        $this->messages->add(_('The node you want to operate with does not exist'), MSG_TYPE_WARNING);
        Logger::warning("Error: node {$this->IdNode} does not exist");
        return false;
    }

    /**
     * If it is contained, it give translated names from node $nodeID in a list form
     * 
     * @param $nodeID
     * @param $langID
     * @return array
     */
    public function getAliasForLangPath(int $nodeID, int $langID)
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            if ($this->isOnNode($nodeID)) {
                if (! $this->get('IdParent') || $this->get('IdNode') == $nodeID) {
                    return array(
                        $this->getAliasForLangWithDefault($langID)
                    );
                }
                $parent = new Node($this->get('IdParent'));
                return array_merge($parent->getAliasForLangPath($nodeID, $langID), array(
                    $this->getAliasForLangWithDefault($langID)
                ));
            }
            $this->setError(14);
            return array();
        }
        $this->setError(1);
        return array();
    }

    /**
     * Obtain the closest parent section of a node, or null if no section found
     * 
     * @return int|NULL
     */
    public function getSection() : ?int
    {
        if (! $this->IdNode) {
            Logger::error('Call to obtain the section of a node without ID');
            return null;
        }
        $section = FastTraverse::getParents($this->IdNode, null, null, ['IsSection' => 1], 1);
        if (! $section) {
            return null;
        }
        return current($section);
    }

    public function getServer()
    {
        $result = $this->_getParentByType(NodeTypeConstants::SERVER);
        return $result;
    }

    public function getProject()
    {
        $result = $this->_getParentByType(NodeTypeConstants::PROJECT);
        if (! $result) {
            $result = $this->_getParentByType(NodeTypeConstants::XLMS_PROJECT);
        }
        if (! $result) {
            $result = $this->_getParentByType(NodeTypeConstants::XSIR_REPOSITORY);
        }
        return $result;
    }

    public function _getParentByType(int $type = null)
    {
        if (is_null($type)) {
            Logger::fatal('Trying to call a function without params');
            return false;
        }
        if ($this->get('IdNodeType') == $type) {
            return $this->get('IdNode');
        }
        $query = sprintf("SELECT ft.IdNode FROM `FastTraverse` ft" . " INNER JOIN Nodes n ON ft.IdNode = n.IdNode AND n.IdNodeType = %d" . 
            " WHERE ft.IdChild = %d and ft.IdNode <> %d", $type, $this->get('IdNode'), $this->get('IdNode'));
        $db = new \Ximdex\Runtime\Db();
        $db->query($query);
        if ($db->numRows > 0) {
            return $db->getValue('IdNode');
        }
        Logger::warning(sprintf(_("The nodetype %s could not be obtained for node "), $type) . $this->get('IdNode'));
        return null;
    }
    
    /**
     * If its pending on some project, its depth is returned
     * 
     * @return int|null
     */
    public function getPublishedDepth() : ?int
    {
        if (! $this->IdNode) {
            return null;
        }
        if ($this->nodeType->getID() == NodeTypeConstants::SERVER) {
            return 1;
        }
        if (! $this->get('IdParent')) {
            return null;
        }
        $parents = FastTraverse::getParents($this->IdNode, null, null, ['IsVirtualFolder' => false]);
        if (! $parents) {
            return null;
        }
        return count($parents) + 1;
    }

    /**
     * Returns the list of nodes which depend on given one
     * If flag=3, returns just the ones associated with groups 'CanAttachGroups'
     * If flag=4, returns all the nodes which depend on the one in the object and its lists of dependencies.
     * If flag=5, returns all the nodes which depend on the one in the object which cannot be deleted.
     * If flag=6, returns all the nodes which depend on the one in the object which are publishable.
     * If flag=null, returns all the nodes which depend on the one in the object
     *
     * @param int $flag
     * @param bool $firstNode
     * @param string $filters
     * @return array|boolean[]|string[]
     */
    public function traverseTree(int $flag = null, bool $firstNode = true, string $filters = '')
    {
        unset($filters);

        // Making an object with current node and its ID is added
        $nodeList = array();
        if (($flag == 3) && $this->nodeType->get('CanAttachGroups')) {
            $nodeList[0] = $this->get('IdNode');
        } else if ($flag == 4) {
            $nodeList[0] = $this->get('IdNode');
            $nodeList = array_merge($nodeList, $this->getDependencies());
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
        $nodeChildren = $this->getChildren();

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

    /**
     * Gets all ancestors of the node
     *
     * @param int $fromNode
     * @return array
     */
    function getAncestors(int $fromNode = null)
    {
        unset($fromNode);
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("SELECT IdNode FROM FastTraverse WHERE IdChild= %d ORDER BY Depth DESC", $this->get('IdNode'), $this->get('IdNode'));
        $dbObj->query($sql);
        $list = array();
        while (! $dbObj->EOF) {
            $list[] = $dbObj->getValue('IdNode');
            $dbObj->next();
        }
        return $list;
    }

    /**
     * Returns a list with the path from root (or the one in the param fromID if it is) until the nod ein the object
     * Keeps the list of node ids ordered by depth, including the object
     *
     * @param int $minIdNode
     * @return array
     */
    public function traverseToRoot(int $minIdNode = 10000)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("SELECT IdNode FROM FastTraverse WHERE IdChild = %d AND IdNode >= %d ORDER BY Depth DESC"
            , $this->get('IdNode'), $minIdNode);
        $dbObj->query($sql);
        $list = array();
        while (! $dbObj->EOF) {
            $list[] = $dbObj->getValue('IdNode');
            $dbObj->next();
        }
        return $list;
    }

    /**
     * JAP 20040617, GetSections_ximTREE
     *
     * @param int $langID
     * @param int $top
     * @param int $bottom
     * @return string
     */
    public function getSections_ximTREE(int $langID, int $top, int $bottom)
    {
        // Getting the nodetypes to select
        $auxType = new NodeType();
        $auxType->SetByName("Section");
        $sectionTypeId = $auxType->get('IdNodeType');
        $auxType->SetByName("Server");
        $serverTypeId = $auxType->get('IdNodeType');

        // List of nodes of type 'section' until the server (inclusive)
        $sectionList = array();

        // Surfing the node tree, looking for the section which contain the current node
        $parentid = $this->getSection();
        $profundidad = 0;
        while ($parentid) {
            $node = new Node($parentid);
            $nodetype = $node->get('IdNodeType');
            array_push($sectionList, $node->get('IdNode'));

            // Seguimos subiendo en el árbol
            if ($nodetype == $serverTypeId) {
                $parentid = null;   // We are in the server, exiting
            } else {
                $parentid = $node->getParent(); // Take the parent, it will include the server
            }
            $profundidad++;
        }

        // Re-ordering the list to start from the tree top
        $sectionList = array_reverse($sectionList);

        /*
         * DEBUG ...
         * foreach ($sectionList as $mysection)
         * echo "SECCION: $mysection ";
         * ...
         */

        // Surfing through sections building the exchange XML
        $cad = "<ximTREE ximver='2.0' top='$top' bottom='$bottom'>";
        $startlevel = $profundidad - $top - 1; // start section
        if ($startlevel < 0) {
            $startlevel = 0;
        }
        $endlevel = $profundidad + $bottom; // End section
        if ($startlevel <= count($sectionList)) {
            $section = $sectionList[$startlevel];
        } else {
            $section = null;
        }
        $level = $startlevel + 1;
        $branch = null;
        if ($level == count($sectionList)) {
            $branch = 1;
        }
        
        // DEBUG
        // echo "SELECCIONADA SECCION $section PARA PROCESADO con TOP:$top y BOTTOM:$bottom START:$level END:$endlevel ...";
        if ($section && $level <= $endlevel) {
            $cad .= $this->expandChildren_ximTREE($section, $sectionTypeId, $level, $langID, $sectionList, $endlevel, $branch);
        }
        $cad = "$cad</ximTREE>";
        return $cad;
    }

    /**
     * @param $nodeID
     * @param $sectionTypeId
     * @param $level
     * @param $langID
     * @param $sectionList
     * @param $endlevel
     * @param $branch
     * @return string
     */
    public function expandChildren_ximTREE(int $nodeID, int $sectionTypeId, int $level, int $langID, array $sectionList, int $endlevel
        , int $branch = null)
    {
        $node = new Node($nodeID);
        $nodoseleccionado = $sectionList[$level];
        $children = $node->getChildren($sectionTypeId);
        $cad2 = ""; // Opening tag for children family "<ximCHILDREN>"
        foreach ($children as $child) {
            if ($child and $level < $endlevel) {
                $childnodeid = new Node($child);
                $childname = $childnodeid->get('Name');
                $childnamelang = $childnodeid->getAliasForLangWithDefault($langID);
                if ($child == $nodoseleccionado) {
                    $childseleccionado = 1;
                } else {
                    $childseleccionado = 0;
                }
                $original_level = count($sectionList) - 1;
                $distance = $level - $original_level;
                $relationship = "relative";
                if ($childseleccionado and $distance < 0) {
                    $relationship = "ascendant";
                }
                if ($childseleccionado and $distance == 0) {
                    $relationship = "me";
                    $branch = 1;
                }
                if ($branch and $distance > 0) {
                    $relationship = "descendant";
                }
                $cad2 .= "<ximNODE sectionid='$child' level='$level' distance='$distance' relationship='$relationship' " . 
                    "onpath='$childseleccionado' type='section' name='$childname' langname='$childnamelang' langid='$langID'>";
                $cad2 .= $node->expandChildren_ximTREE($child, $sectionTypeId, $level + 1, $langID, $sectionList, $endlevel, $branch);
                $cad2 .= "</ximNODE>";
            }
        }
        // $cad2 .= "</ximCHILDREN>"; // if we want to close tag for children family
        return $cad2;
    }

    /**
     * Updating the table FastTraverse.
     * The parameter delete indicates if we want or not delete the node before
     * 
     * @param bool $delete
     * @return bool
     */
    public function updateFastTraverse(bool $delete = true) : bool
    {
        $this->clearError();
        if ($this->get('IdNode') > 0) {
            $dbObj = new \Ximdex\Runtime\Db();
            if ($delete) {
                $sql = sprintf('DELETE FROM FastTraverse WHERE IdChild = %d', $this->get('IdNode'));
                $dbObj->execute($sql);
                if ($dbObj->numErr) {
                    $this->setError(5);
                    return false;
                }
            }
            $parent = $this->get('IdNode');
            $level = '0';
            do {
                $sql = sprintf('INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (%d, %d, %d)', $parent, $this->get('IdNode'), $level);
                $dbObj->execute($sql);
                $level++;
                $node = new Node($parent);
                $parent = $node->get('IdParent');
            } while ($parent);
        } else {
            $this->setError(1);
            return false;
        }
        return true;
    }

    public function loadData(bool $advanced = false)
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
        $ret['path'] = $this->getPath($advanced);
        $ret['typename'] = $this->nodeType->get('Name');
        $ret['typedescription'] = $this->nodeType->get('Description');
        $ret['class'] = $this->nodeType->get('Class');
        $ret['icon'] = $this->nodeType->get('Icon');
        $ret['isdir'] = $this->nodeType->get('IsFolder');
        $ret['isfile'] = $this->nodeType->get('IsPlainFile');
        $ret['isvirtual'] = $this->nodeType->get('IsVirtualFolder');
        $ret['isfs'] = $this->nodeType->get('HasFSEntity');
        $ret['issection'] = $this->nodeType->get('IsSection');
        $ret['isxml'] = $this->nodeType->get('IsStructuredDocument');
        $version = $this->getLastVersion();
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

    public function DatosNodo()
    {
        $list = array();
        $list['IdNode'] = $this->get('IdNode');
        $list['NodeName'] = $this->get('Name');
        $list['NodeType'] = $this->get('IdNodeType');
        $list['State'] = $this->get('IdState');
        return $list;
    }

    public function clearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }
    
    public function setError(int $code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }
    
    public function getError() : ?string
    {
        return $this->msgErr;
    }

    /**
     * @return int|null
     */
    public function hasError() : ?int
    {
        return $this->numErr;
    }

    /**
     * Returns this node xml interpretation and its descendents
     *
     * @param int $depth
     * @param array $files
     * @param bool $recursive
     * @return string|bool
     */
    public function toXml(int $depth, array & $files, bool $recursive = false)
    {
        global $STOP_COUNT;
        if (! $this->get('IdNode')) {
            Logger::warning(sprintf(_("It is being tried to load the unexistent node %s"), $this->get('IdNode')));
            return false;
        }
        if (! is_array($files)) {
            $files = array();
        }
        $depth++;
        $indexTabs = str_repeat("\t", $depth);
        if ($this->get('IdState') > 0) {
            $query = sprintf("SELECT name FROM WorkflowStatus WHERE id = %d LIMIT 1", $this->get('IdState'));
            $dbObj = new \Ximdex\Runtime\Db();
            $dbObj->Query($query);
            $statusName = $dbObj->getValue('name');
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
        $nodeProperty = new NodeProperty();
        $result = $nodeProperty->getPropertiesByNode($this->get('IdNode'));
        if (! is_null($result)) {
            foreach ($result as $resultData) {
                $tail .= sprintf(' %s="%s"', $resultData['Property'], $resultData['Value']);
            }
        }
        $xmlHeader = sprintf('<%s id="%d" name="%s" class="%s" nodetype="%d" parentid="%d"%s>'
            , $nodeTypeName, $idNode, $nodeName, $nodeTypeClass, $this->nodeType->get('IdNodeType'), $nodeParent, $tail) . "\n";
        if ($STOP_COUNT == COUNT) {
            $STOP_COUNT = NO_COUNT;
        } else {
            $STOP_COUNT = NO_COUNT_NO_RETURN;
        }
        $xmlBody = $this->class->ToXml($depth, $files, $recursive);
        if ($STOP_COUNT == NO_COUNT) {
            $STOP_COUNT = COUNT;
        } else {
            $STOP_COUNT = NO_COUNT;
        }

        /*
         * This block of code makes if a xmlcontainer has not associated a visualtemplate,
         * it looks automatically if some child has associated a visualtemplate and associate it to the container
         */
        if (($this->nodeType->get('Class') == 'Xmlcontainernode')) {
            $childrens = $this->getChildren();
            if (!empty($childrens)) {
                foreach ($childrens as $idChildrenNode) {
                    $children = new Node($idChildrenNode);
                    if (! $children->get('IdNode')) {
                        Logger::warning(sprintf('It is being tried to load the node %s from the unexistent node %s'
                            , $children->get('IdNode'), $this->get('IdNode')));
                        continue;
                    }
                    if ($children->nodeType->getIsStructuredDocument()) {
                        $structuredDocument = new StructuredDocument($children->getID());
                        $idTemplate = $structuredDocument->getDocumentType();
                        $node = new Node($idTemplate);
                        if (! $node->get('IdNode')) {
                            Logger::warning(sprintf('It is being tried to load the node %s from the unexistent node %s'
                                , $node->get('IdNode'), $this->get('IdNode')));
                            continue;
                        }
                        if ($STOP_COUNT == COUNT) {
                            $STOP_COUNT = NO_COUNT;
                        } else {
                            $STOP_COUNT = NO_COUNT_NO_RETURN;
                        }
                        $xmlBody .= $node->ToXml($depth, $files, $recursive);
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
            $idLanguage = $structuredDocument->getLanguage();
            $node = new Node($idLanguage);
            if ($node->get('IdNode') > 0) {
                if ($STOP_COUNT == COUNT) {
                    $STOP_COUNT = NO_COUNT;
                } else {
                    $STOP_COUNT = NO_COUNT_NO_RETURN;
                }
                $xmlBody .= $node->toXml($depth, $files, $recursive);
                $idTemplate = $structuredDocument->getDocumentType();
                $node = new Node($idTemplate);
                $xmlBody .= $node->ToXml($depth, $files, $recursive);
                if ($STOP_COUNT == NO_COUNT) {
                    $STOP_COUNT = COUNT;
                } else {
                    $STOP_COUNT = NO_COUNT;
                }
            }
        }
        $xmlFooter = sprintf('</%s>', $nodeTypeName) . "\n";
        if (!$STOP_COUNT && defined('COMMAND_MODE_XIMIO')) {
            global $PROCESSED_NODES, $LAST_REPORT, $TOTAL_NODES;
            $PROCESSED_NODES++;
            $processedNodes = $TOTAL_NODES > 0 ? (int)(($PROCESSED_NODES * 100) / $TOTAL_NODES) : 0;
            if ($processedNodes > $LAST_REPORT) {
                echo sprintf(_("It has been processed a %s%% of the nodes"), $processedNodes);
                echo sprintf("\n");
                echo sprintf(_("The last processed node was %s"), $this->get('Name'));
                echo sprintf("\n");
                $LAST_REPORT = $processedNodes;
            }
        }
        unset($nodeTypeName, $idNode, $nodeName, $nodeTypeClass, $statusName, $sharedWorkflow, $tail);

        // If a recursive importation was applied, here is where recursive is performed
        if (is_null($recursive) || (! is_null($recursive) && $depth <= $recursive)) {
            $childrens = $this->getChildren();
            if ($childrens) {
                foreach ($childrens as $idChildren) {
                    $childrenNode = new Node($idChildren);
                    if (! $childrenNode->get('IdNode')) {
                        Logger::warning(sprintf(_("It is being tried to load the node %s from the unexistent node %s")
                            , $childrenNode->get('IdNode'), $this->get('IdNode')));
                        continue;
                    }
                    $xmlBody .= $childrenNode->toXml($depth, $files, $recursive);
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
     * @param string $name
     * @param int $idNodeType
     * @return bool
     */
    public function isValidName(string $name, int $idNodeType = 0)
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
        
        // The pattern and the string must be in the same encode
        $pattern1 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.\s]+$/", XML::UTF8);
        $pattern2 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.\s\@\:\/\?\+\=\#\%\*\,]+$/", XML::UTF8);
        $pattern3 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.]+$/", XML::UTF8);
        $pattern4 = Base::recodeSrc("/^[A-Za-z0-9\_\-\.\@]+$/", XML::UTF8);
        $name = Base::recodeSrc($name, XML::UTF8);
        unset($nodeType);
        if (! strcasecmp($nodeTypeName, 'Action') || ! strcasecmp($nodeTypeName, 'Group') || ! strcasecmp($nodeTypeName, 'Language') 
            || ! strcasecmp($nodeTypeName, 'LinkFolder') || ! strcasecmp($nodeTypeName, 'LinkManager') || ! strcasecmp($nodeTypeName, 'Role') 
            || ! strcasecmp($nodeTypeName, 'WorkflowState')) {
            return (preg_match($pattern1, $name) > 0);
        } elseif (!strcasecmp($nodeTypeName, 'Link')) {
            return (preg_match($pattern2, $name) > 0);
        } elseif (!strcasecmp($nodeTypeName, 'User')) {
            return (preg_match($pattern4, $name) > 0);
        } else {
            return (preg_match($pattern3, $name) > 0);
        }
    }

    public function checkAllowedContent(int $idNodeType, int $parent = null, bool $checkAmount = true)
    {
        if (is_null($parent)) {
            if (is_null($this->get('IdParent'))) {
                Logger::error('Error checking if the node is allowed - parent does not exist [1]');
                return false;
            }
            $parent = $this->get('IdParent');
        }
        $parentNode = new Node($parent);
        if (! $parentNode->getID()) {
            Logger::error(_('Error checking if the node is allowed - parent does not exist [2]'));
            $this->messages->add(_('The specified parent node does not exist'), MSG_TYPE_ERROR);
            return false;
        }
        $nodeAllowedContents = $parentNode->getCurrentAllowedChildren();
        if (! $nodeAllowedContents) {
            Logger::error(sprintf(_("The parent %s does not allow any nested node from him"), $parent));
            $this->messages->add(_('This node type is not allowed in this position'), MSG_TYPE_ERROR);
            return false;
        }
        $nodeType = new NodeType($idNodeType);
        if (! $nodeType->getID()) {
            Logger::error(sprintf(_("The introduced nodetype %s does not exist"), $idNodeType));
            $this->messages->add(_('The specified nodetype does not exist'), MSG_TYPE_ERROR);
            return false;
        }
        if (! in_array($idNodeType, $nodeAllowedContents)) {
            Logger::error("The nodetype $idNodeType is not allowed in the parent (idnode = " . $parent . ") - (idnodetype = " 
                . $parentNode->get('IdNodeType') . ") which allowed nodetypes are: " . print_r($nodeAllowedContents, true));
            $this->messages->add(_('This node type is not allowed in this position'), MSG_TYPE_ERROR);
            return false;
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT Amount from NodeAllowedContents WHERE IdNodeType = %s AND NodeType = %s', 
            $dbObj->sqlEscapeString($parentNode->nodeType->get('IdNodeType')), $dbObj->sqlEscapeString($idNodeType));
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
        $nodeTypesInParent = count($parentNode->getChildren($idNodeType));
        if ($amount > $nodeTypesInParent) {
            return true;
        }
        $this->messages->add(_('No more nodes can be created in this folder type'), MSG_TYPE_ERROR);
        return false;
    }

    public function toStr(int $detailLevel = DETAIL_LEVEL_LOW)
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
        $details .= sprintf("Path: %s\n", $this->getPath());
        $details .= sprintf("Parent node: %s\n", $this->get('IdParent'));
        if ($detailLevel <= DETAIL_LEVEL_MEDIUM) {
            return $details;
        }
        return $details;
    }

    public function getProperty(string $property, bool $withInheritance = true)
    {
        if (! $this->get('IdNode')) {
            Logger::error('Cannot load the \'' . $property . '\' property without a node ID');
            return false;
        }
        if ($withInheritance) {
            $sql = "SELECT IdNode FROM FastTraverse WHERE IdChild = " . $this->get('IdNode') . " ORDER BY Depth ASC";
            $db = new \Ximdex\Runtime\Db();
            $db->query($sql);
            while (! $db->EOF) {

                // Getting property
                if ($db->getValue('IdNode') < 1) {
                    break;
                }
                $nodeProperty = new NodeProperty();
                $propertyValue = $nodeProperty->getProperty($db->getValue('IdNode'), $property);
                if (! is_null($propertyValue)) {
                    return $propertyValue;
                }
                $db->next();
            }
        } else {
            $nodeProperty = new NodeProperty();
            return $nodeProperty->getProperty($this->get('IdNode'), $property);
        }
        Logger::debug(sprintf("Property %s not found for node %d", $property, $this->get('IdNode')));
        return null;
    }

    public function getAllProperties(bool $withInheritance = false)
    {
        $returnValue = array();
        $nodeProperty = new NodeProperty();
        if ($withInheritance) {
            $sql = "SELECT IdNode FROM FastTraverse WHERE IdChild = " . $this->get('IdNode') . " ORDER BY Depth ASC";
            $db = new \Ximdex\Runtime\Db();
            $db->query($sql);
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
                        if (array_key_exists($propertyInfo['Property'], $returnValue)) {
                            continue;
                        }
                        $returnValue[$propertyInfo['Property']][] = $propertyInfo['Value'];
                    }
                }
                $db->next();
            }
        } else {
            $properties = $nodeProperty->find('Property, Value', 'IdNode = %s', array(
                $this->get('IdNode')
            ));
            if (empty($properties)) {
                return null;
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
     * Return a boolean value for a property with 'true' or 'false'
     * 
     * @param string $property
     * @param bool $withInheritance
     * @return boolean
     */
    public function getSimpleBooleanProperty(string $property, bool $withInheritance = true)
    {
        $property = $this->getProperty($property, $withInheritance);
        if (! ((is_array($property)) && ($property[0] == "true"))) {
            $value = false;
        } else {
            $value = true;
        }
        return $value;
    }

    public function setSingleProperty(string $property, string $value)
    {
        $nodeProperty = new NodeProperty();
        $properties = $nodeProperty->find('IdNodeProperty', 'IdNode = %s AND Property = %s AND Value = %s', array(
            $this->get('IdNode'),
            $property,
            $value
        ));
        if (empty($properties)) {
            $nodeProperty->create($this->get('IdNode'), $property, $value);
        }
    }

    function setProperty(string $property, $values)
    {
        // Removing previous values
        if (! is_array($values)) {
            $values[] = $values;
        }
        $nodeProperty = new NodeProperty();
        $nodeProperty->deleteByNodeProperty($this->get('IdNode'), $property);

        // Adding new values
        $n = count($values);
        for ($i = 0; $i < $n; $i++) {
            $this->setSingleProperty($property, $values[$i]);
        }
    }

    public function deleteProperty(string $property)
    {
        if (!($this->get('IdNode') > 0)) {
            $this->messages->add(_('The node over which property want to be deleted does not exist ') . $property, MSG_TYPE_WARNING);
            return false;
        }
        $nodeProperty = new NodeProperty();
        return $nodeProperty->deleteByNodeProperty($this->get('IdNode'), $property);
    }

    public function deletePropertyValue(string $property, string $value)
    {
        if (! $this->get('IdNode')) {
            $this->messages->add(_('The node over which property want to be deleted does not exist ') . $property, MSG_TYPE_WARNING);
            return false;
        }
        $nodeProperty = new NodeProperty();
        $properties = $nodeProperty->find('IdNodeProperty', 'IdNode = %s AND Property = %s AND Value = %s', array(
            $this->get('IdNode'),
            $property,
            $value
        ), MONO);
        foreach ($properties as $idNodeProperty) {
            $nodeProperty = new NodeProperty($idNodeProperty);
            $nodeProperty->delete();
        }
        return true;
    }

    /**
     * This function overwrite the workflow_forward function
     * The action function is then deprecated
     *
     * @param int $idUser
     * @param int $idGroup
     * @return int status
     */
    public function getNextAllowedState(int $idUser, int $idGroup)
    {
        if (! $this->get('IdNode')) {
            return null;
        }
        if (! $this->get('IdState')) {
            return null;
        }
        $user = new User($idUser);
        $idRole = $user->getRoleOnNode($this->get('IdNode'), $idGroup);
        $role = new Role($idRole);
        $allowedStates = $role->getAllowedStates();
        $idNextState = $this->get('IdState');
        if (is_array($allowedStates) && ! empty($allowedStates)) {
            $workflow = new Workflow($this->nodeType->getWorkflow(), $idNextState);
            $idNextState = null;
            do {
                $idNextState = $workflow->getNextState();
                if (empty($idNextState)) {
                    return null;
                } else if (in_array($idNextState, $allowedStates)) {
                    return $idNextState;
                }
                $workflow = new Workflow($this->nodeType->getWorkflow(), $idNextState);
            } while (! $workflow->isFinalState());
        }
        return null;
    }

    /**
     * Update node childs FastTraverse info
     * 
     * @return boolean
     */
    private function updateChildren() : bool
    {
        $arr_children = $this->getChildren();
        if (! empty($arr_children)) {
            foreach ($arr_children as $child) {
                $node_child = new Node($child);
                if (! $node_child->getID()) {
                    return true;
                }
                if (! $node_child->updateFastTraverse()) {
                    return false;
                }
                if (! $node_child->renderizeNode()) {
                    return false;
                }
                if (! $node_child->updateChildren()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getLastVersion()
    {
        $sql = "SELECT V.IdVersion, V.Version, V.SubVersion, V.IdUser, V.Date, U.Name as UserName, V.File ";
        $sql .= " FROM Versions V INNER JOIN Users U on V.IdUser = U.IdUser ";
        $sql .= " WHERE V.IdNode = '" . $this->get('IdNode') . "' ";
        $sql .= " ORDER BY V.IdVersion DESC LIMIT 1 ";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($sql);
        if ($dbObj->numRows > 0) {
            if ($dbObj->getValue("Version") == 0 && $dbObj->getValue("SubVersion") == 0) {
                $state = 0;
            } elseif ($dbObj->getValue("Version") != 0 && $dbObj->getValue("SubVersion") == 0) {
                $state = 1;
            } else {
                $state = 2;
            }
            return array(
                "IdVersion" => $dbObj->getValue("IdVersion"),
                "Version" => $dbObj->getValue("Version"),
                "SubVersion" => $dbObj->getValue("SubVersion"),
                "Published" => $state,
                "IdUser" => $dbObj->getValue("IdUser"),
                "Date" => $dbObj->getValue("Date"),
                "UserName" => $dbObj->getValue("UserName"),
                "File" => $dbObj->getValue("File")
            );
        } else {
            $this->setError(5);
        }
        return null;
    }

    /**
     * Return an array with all the layout schemas for the current node
     *
     * @return bool|array
     */
    public function getLayoutSchemas()
    {
        if ($this->getNodeType() != NodeTypeConstants::XML_ROOT_FOLDER) {
            $this->messages->add('The node is not the type of HTML container', MSG_TYPE_ERROR);
            return false;
        }

        // Load parent nodes
        $parents = FastTraverse::getParents($this->IdNode, 'IdNodeType', 'ft.IdNode');
        if ($parents === false) {
            Logger::error('An error ocurred while getting the parents node for document with node ID: ' . $this->IdNode);
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
                    if (! $node->getID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the layouts root folder inside the previous node, if it exists
                    $layoutFolder = new Node($node->getChildByType(NodeTypeConstants::HTML_LAYOUT_FOLDER));
                    if (! $layoutFolder->getID()) {
                        continue 2;
                    }

                    // Load the JSON layout schemas
                    $schemas = $schemas + $layoutFolder->find('Name, IdNode', 'IdParent = %s AND IdNodeType = %s ORDER BY Name', array(
                            $layoutFolder->getID(),
                            NodeTypeConstants::HTML_LAYOUT
                        ), MONO, false, 'Name');

                    // If the is the project one, the process end
                    if ( in_array($nodeTypeID,NodeTypeGroupConstants::NODE_PROJECTS) ) {
                        break;
                    }
            }
        }
        return $schemas;
    }


    /**
     * Return an array with all the layout schemas for the current node
     *
     * @return bool|array
     */
    public function getJsonSchemas()
    {
        $currentNodeType = $this->getNodeType();

        // Load parent nodes
        $parents = FastTraverse::getParents($this->IdNode, 'IdNodeType', 'ft.IdNode');
        if ($parents === false) {
            Logger::error('An error ocurred while getting the parents node for document with node ID: ' . $this->IdNode);
            return false;
        }

        $schemas = array();
        foreach ($parents as $nodeID => $nodeTypeID) {
            switch ($nodeTypeID) {
                case NodeTypeConstants::XLMS_PROJECT:
                case NodeTypeConstants::PROJECT:
                    // Load the node for the section, server or project ID given
                    $node = new Node($nodeID);
                    if (! $node->getID()) {
                        $this->messages->add('Cannot load a node with the ID: ' . $nodeID, MSG_TYPE_ERROR);
                        return false;
                    }

                    // Load the layouts root folder inside the previous node, if it exists
                    $layoutFolder = new Node($node->getChildByType(NodeTypeConstants::JSON_SCHEMA_CONTAINER));
                    if (! $layoutFolder->getID()) {
                        continue 2;
                    }

                    // Load the JSON layout schemas
                    $schemas = $schemas + $layoutFolder->find('Name, IdNode', 'IdParent = %s AND IdNodeType = %s ORDER BY Name', array(
                            $layoutFolder->getID(),
                            NodeTypeConstants::JSON_SCHEMA_FILE
                        ), MONO, false, 'Name');

                    // If the is the project one, the process end
                    if ( in_array($nodeTypeID,NodeTypeGroupConstants::NODE_PROJECTS) ) {
                        break;
                    }
            }
        }
        return $schemas;
    }

    public function getSchemas($type = NULL)
    {
        $idProject = $this->getProject();
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
        $folder = new Node($project->getChildByName($dirName));
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

    public function checkTarget(int $destNodeId = null)
    {
        if (! $destNodeId) {
            return null;
        }
        $changeName = 0;    // Assuming by default they're not the same
        $existing = 0;
        $amount = 0;
        $insert = 0;    // By default, dont insert.
        $actionNodeType = $this->get('IdNodeType');
        $destNode = new Node($destNodeId);
        $destNodeType = $destNode->get('IdNodeType');

        // Parents data
        $parent = new Node($this->getParent()); // Parent node

        // Query to NodeAllowedContents
        $sql1 = "SELECT Amount FROM NodeAllowedContents WHERE IdNodeType = $destNodeType and NodeType = $actionNodeType";
        $db = new \Ximdex\Runtime\Db();
        $db->query($sql1);
        while (! $db->EOF) {
            $amount = $db->getValue('Amount');
            $db->next();
        }
        if ($amount == null) {
            $amount = -1;
        } // If there is not a relation allowed, abort the copy
        
        // Query to FastTraverse
        $sql2 = "SELECT count(Depth) FROM FastTraverse WHERE FastTraverse.IdNode = $destNodeId " . 
            "and IdChild in (SELECT IdNode FROM Nodes WHERE IdNodeType = $actionNodeType) and Depth = 1";
        $db->query($sql2);
        while (! $db->EOF) {
            $existing = $db->getValue('count(Depth)');
            $db->next();
        }
        if ($existing == null) {
            $existing = 0;
        } // Dont exist a relation yet
        
        // First check, insert allowed?
        if ($amount == 0) {
            $insert = 1;
        } // Destination node allows an infinite number of copies
        else if ($amount == -1) {
            $insert = 0;
        } // Destination node does not allow this kind of content
        else { // Limited capacity
            if ($amount > $existing) {
                $insert = 1;
            } // There is place for another copy
        }

        // Only if we can insert, we must check if the copy is going to be at the same level
        if ($insert == 1) {
            if ($destNodeId == $parent->get('IdNode')) {
                $changeName = 1;
            } // Coinciden. Copiamos el nodo al mismo nivel y debemos renombrarlo
        }
        return array(
            'NodoDest Id' => $destNodeId,
            'NodoDest Tipo' => $destNodeType,
            'changeName' => $changeName,
            'insert' => $insert
        );
    }

    public function isModified()
    {
        $version = $this->getLastVersion();
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
     * @return bool|string
     */
    public function getMimeType(string $content = null)
    {
        if (! $this->IdNode) {
            Logger::error('No node ID has been specified');
            return false;
        }
        if ($content !== null) {

            // Get the mime type from given content
            $basePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot');
            $pointer = FsUtils::getUniqueFile($basePath);
            $file = $basePath . "/preview_" . $this->IdNode . '_' . $pointer;
            FsUtils::file_put_contents($file, $content);
        } else {

            // Get the mime type from node content
            $info = pathinfo($this->getNodeName());
            if (strtolower($info['extension']) == 'svg') {

                // SVG files return text/plain by default
                return 'image/svg+xml';
            } elseif (strtolower($info['extension']) == 'css') {

                // CSS files return text/plain by default
                return 'text/css';
            } else {

                // Obtain the mime type from the last version of the file
                $version = $this->getLastVersion();
                if (!isset($version['IdVersion']) or !$version['IdVersion']) {
                    Logger::error('There is no a version for node: ' . $this->IdNode);
                    return false;
                }
                $versionID = $version['IdVersion'];
                $version = new Version($versionID);
                $file = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $version->get('File');
                if (! file_exists($file)) {
                    Logger::error('Cannot load the file: ' . $file . ' for version: ' . $versionID);
                    return false;
                }
            }
        }
        $mimeType = mime_content_type($file);
        if ($content) {
            @unlink($file);
        }
        if (! $mimeType) {
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
        if (! $this->IdNode) {
            $this->messages->add('The node has not the ID value', MSG_TYPE_ERROR);
            return false;
        }
        if ($channel) {
            $strDoc = new StructuredDocument($this->IdNode);
            if (! $strDoc->getID()) {
                $this->messages->add('Cannot load the structured document with ID: ' . $this->IdNode, MSG_TYPE_WARNING);
                return false;
            }
            if ($strDoc->hasChannel($channel)) {

                // The document channel is in the inherited channels of the target document
                return $channel;
            }
        }

        // The channel will be the first one available in the inherited properties
        $properties = InheritedPropertiesManager::getValues($this->IdNode, true);
        if (! $properties['Channel']) {
            Logger::error('The document with ID: ' . $this->IdNode . ' has no channel');
            return false;
        }
        foreach ($properties['Channel'] as $channelProperty) {
            if ($channelProperty['Inherited']) {
                return $channelProperty['Id'];
            }
        }
        // There is no channel available for the target document
        $this->messages->add('The target document ' . $this->IdNode . ' has not any channel available', MSG_TYPE_WARNING);
        return null;
    }

    /**
     * Return data to render the node to desired output with response headers
     * 
     * @param int $idChannel
     * @param bool $showprev
     * @param string $content
     * @param int $version
     * @param int $subversion
     * @param string $mode
     * @param int $previewServer
     * @return boolean|array
     */
    public function filemapper(int $idChannel = null, bool $showprev = false, string $content = null, int $version = null
        , int $subversion = null, string $mode = null, int $previewServer = null)
    {
        // Checks node existence
        if (! $this->IdNode) {
            $this->messages->add(_('It is not possible to show preview.') . _(' The node you are trying to preview does not exist.')
                , MSG_TYPE_NOTICE);
            return false;
        }

        // If the node is a structured document, render the preview, else return the file content
        if ($this->nodeType->getIsStructuredDocument()) {

            // Checks if node is a structured document
            $structuredDocument = new StructuredDocument($this->IdNode);
            if (! $structuredDocument->get('IdDoc')) {
                $this->messages->add(_('It is not possible to show preview.') . _(' Provided node is not a structured document.')
                    , MSG_TYPE_NOTICE);
                return false;
            }

            // Checks content existence
            if ($content === null) {
                $content = $structuredDocument->getContent($version, $subversion);
            } elseif ($this->getNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                $content = XmlDocumentNode::normalizeXmlDocument($content);
            }

            // Get the available target channel
            $idChannel = $this->getTargetChannel($idChannel);
            if (! $idChannel) {
                $this->messages->add(_('It is not possible to show preview. There is not any defined channel.'), MSG_TYPE_NOTICE);
                return false;
            }

            // Populates variables and view args
            $idSection = $this->getSection();
            $idProject = $this->getProject();
            $idServerNode = $this->getServer();
            $documentType = $structuredDocument->getDocumentType();
            $idLanguage = $structuredDocument->getLanguage();
            if ($this->getNodeType() == NodeTypeConstants::XML_DOCUMENT and method_exists($this->class, 'getDocHeader')) {
                $docXapHeader = $this->class->getDocHeader($idChannel, $idLanguage, $documentType);
            } else {
                $docXapHeader = null;
            }
            $nodeName = $this->get('Name');
            $depth = $this->getPublishedDepth();

            // Initializes variables
            $args = array();
            $args['NODEID'] = $this->IdNode;
            $args['MODE'] = $mode == 'dinamic' ? 'dinamic' : 'static';
            $args['CHANNEL'] = $idChannel;
            $args['SECTION'] = $idSection;
            $args['PROJECT'] = $idProject;
            $args['SERVERNODE'] = $idServerNode;
            $args['LANGUAGE'] = $idLanguage;
            if ($this->getNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                $args['DOCXAPHEADER'] = $docXapHeader;
            }
            $args['NODENAME'] = $nodeName;
            $args['DEPTH'] = $depth;
            $args['DISABLE_CACHE'] = true;
            $args['CONTENT'] = $content;
            $args['NODETYPENAME'] = $this->nodeType->get('Name');
            if ($this->IdNode < 10000) {
                $idNode = 10000;
                $node = new Node($idNode);
                $transformer = $node->getProperty('Transformer');
            } else {
                $idNode = $this->IdNode;
                $transformer = $this->getProperty('Transformer');
            }
            $args['TRANSFORMER'] = $transformer[0];
            $args['PREVIEW'] = true;
            if ($previewServer) {
                $args['SERVER'] = $previewServer;
            }
            if ($this->getNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                $process = 'PrepareHTML';
            } else {
                $process = 'FromPreFilterToDexT';
            }
            $transition = new Transition();
            try {
                $res = $transition->process($process, $args);
            } catch (\Exception $e) {

                // The transformation process did not work !
                Logger::error($e->getMessage());
                if ($this->getNodeType() == NodeTypeConstants::XML_DOCUMENT) {

                    // If content is false, show the xslt errors instead the document preview
                    $stDoc = new StructuredDocument($idNode);
                    $errors = $stDoc->getXsltErrors();
                    if ($errors) {
                        $errors = str_replace("\n", "\n<br />\n", $errors);
                    }
                }
                if (! isset($errors)) {
                    $errors = 'The preview cannot be processed due to an unknown error';
                }
                $this->messages->add($errors, MSG_TYPE_WARNING);
                return false;
            }

            // Specific FilterMacros View for previews
            $viewFilterMacrosPreview = new ViewFilterMacros(true);
            $content = $viewFilterMacrosPreview->transform(null, $res, $args);
            if ($content === false) {
                $this->messages->add('Cannot transform the document ' . $this->getNodeName() . ' for a preview operation', MSG_TYPE_WARNING);
                return false;
            }
        } else {

            // Node is not a structured document (common view)
            $content = $this->getContent($version, $subversion);
            if ($content === false) {
                $this->messages->add('Cannot get the content from file ' . $this->getNodeName() . ' for a preview operation', MSG_TYPE_WARNING);
                return false;
            }
            
            // Common transformation
            $args = [];
            $args['NODEID'] = $this->IdNode;
            $args['DISABLE_CACHE'] = true;
            $args['CONTENT'] = $content;
            $args['NODENAME'] = $this->getNodeName();
            $args['PREVIEW'] = true;
            if ($this->nodeType->getID() == NodeTypeConstants::CSS_FILE) {
                $args['PROCESSMACROS'] = 'yes';
            }
            $process = 'ToFinal';
            $transition = new Transition();
            try {
                $content = $transition->process($process, $args);
            } catch (\Exception $e) {
                
                // The transformation process did not work !
                $this->messages->add($e->getMessage(), MSG_TYPE_WARNING);
                return false;
            }
        }
        $headers = array();
        if ($this->nodeType->getIsStructuredDocument()) {

            // Get mime type for structured documents
            $mimeType = $this->getMimeType($content);
        } else {

            // Response headers for non structured documents
            $mimeType = $this->getMimeType();
            $headers['Content-Disposition'] = 'attachment; filename=' . $this->getNodeName();
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
        $structuredDocument = new StructuredDocument($this->IdNode);
        if (! $structuredDocument->get('IdLanguage')) {
            $error = 'Language has not been specified for document: ' . $this->getNodeName();
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
    
    /**
     * Used for new node ID in an interval for special node types
     * 
     * @throws \Exception
     */
    private function generateIdForRange()
    {
        switch ($this->IdNodeType) {
            case NodeTypeConstants::GROUP:
                $range = [110, 199];
                break;
            case NodeTypeConstants::ROLE:
                $range = [210, 299];
                break;
            case NodeTypeConstants::USER:
                $range = [310, 399];
                break;
            case NodeTypeConstants::WORKFLOW:
                $range = [400, 499];
                break;
            case NodeTypeConstants::PERMISSION:
                $range = [1000, 1999];
                break;
            case NodeTypeConstants::NODE_TYPE:
                $range = [5000, 5999];
                break;
            case NodeTypeConstants::ACTION:
                $range = [6000, 9999];
                break;
        }
        if (isset($range) and is_array($range) and count($range) == 2) {
            $res = $this->find('max(IdNode)', 'IdNode >= ' . $range[0] . ' AND IdNode <= ' . $range[1], null, MONO);
            if ($res === false) {
                throw new \Exception('SQL problem');
            }
            if (! $res or ! $res[0]) {
                $this->IdNode = $range[0];
                return true;
            }
            if ($res[0] >= $range[1]) {
                throw new \Exception('Maximun range reached for this node type: ' . $this->IdNodeType);
            }
            $this->IdNode = $res[0] + 1;
            return true;
        }
        return false;
    }
    
    public function getSiblings()
    {
        $result = $this->find("IdNode", "idparent = %s", array($this->IdParent), MONO);
        for ($i = 0; count($result); $i++) {
            if ($result[$i] == $this->nodeID) {
                unset($result[$i]);
                break;
            }
        }
        return array_values($result);
    }
    
    /**
     * Return true if the node can be stored in data/nodes directory
     *
     * @return boolean
     */
    public function isRenderizable() : bool
    {
        if (App::getValue('RenderizeAll')) {
            return true;
        }
        if (in_array($this->getNodeType(), [NodeTypeConstants::XSL_TEMPLATE, NodeTypeConstants::TEMPLATES_ROOT_FOLDER,
                NodeTypeConstants::SECTION, NodeTypeConstants::SERVER, NodeTypeConstants::PROJECT, NodeTypeConstants::XLMS_PROJECT])) {
            return true;
        }
        return false;
    }
}
