<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Models\ORM\NodeTypesOrm;
use Ximdex\NodeTypes\NodeTypeConstants;

define('NODETYPE_SECTION', NodeTypeConstants::SECTION);

class NodeType extends NodeTypesOrm
{
    /**
     * Current node id
     * 
     * @var int
     */
    public $ID;
    
    /**
     * DB object used in methods
     * 
     * @var \Ximdex\Runtime\Db
     */
    public $dbObj;
    
    /**
     * Shows if there was an error
     * 
     * @var bool
     */
    public $flagErr;
    
    /**
     * Error code
     * 
     * @var int
     */
    public $numErr;
    
    /**
     * Error message
     * 
     * @var string
     */
    public $msgErr;
    
    /**
     * Class error list
     * @var array
     */
    public $errorList = [];
    
    public $_cache = 0;
    public $_useMemCache = 0;
    public $_fieldsToTraduce = array('Description');
    public $autoCleanErr = true;
    
    public function __construct(int $nodeTypeID = null)
    {
        $this->errorList[1] = _('Database connection error');
        $this->errorList[2] = _('Nodetype does not exist');
        $this->flagErr = false;
        $this->autoCleanErr = true;
        $this->ID = $nodeTypeID;
        parent::__construct($nodeTypeID);
    }

    /**
     * @return string
     */
    public function getConstructor()
    {
        $query = sprintf('SELECT IdAction FROM NodeConstructors WHERE IdNodeType = %d', $this->get('IdNodeType'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        if ($dbObj->numRows == 1) {
            return $dbObj->getValue('IdAction');
        }
        return 0;
    }

    /**
     * Creates a new nodetype and load its nodeTypeID
     * 
     * @param string $name
     * @param string $icon
     * @param bool $isRenderizable
     * @param bool $hasFSEntity
     * @param bool $canAttachGroups
     * @param bool $isContentNode
     * @param string $description
     * @param string $class
     * @param int $nodeTypeID
     * @return boolean|NULL|string
     */
    public function createNewNodeType(string $name, string $icon, bool $isRenderizable, bool $hasFSEntity, bool $canAttachGroups
        , bool $isContentNode, string $description, string $class, int $nodeTypeID = null)
    {
        $this->set('Name', $name);
        $this->set('Icon', $icon);
        $this->set('Description', $description);
        $this->set('Class', $class);
        $this->set('IsRenderizable', $isRenderizable);
        $this->set('HasFSEntity', $hasFSEntity);
        $this->set('CanAttachGroups', $canAttachGroups);
        $this->set('IsContentNode', $isContentNode);
        $this->set('IdNodeType', $nodeTypeID);
        return $this->add();
    }

    /**
     * Returns an array with the ids of all existing nodetypes
     * 
     * @return array of idNodeType
     */
    public function getAllNodeTypes()
    {
        $salida = null;
        $sql = 'SELECT idNodeType FROM NodeTypes';
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        while (! $dbObj->EOF) {
            $salida[] = $dbObj->getValue('idNodeType');
            $dbObj->Next();
        }
        return $salida ? $salida : null;
    }

    /**
     * Returns the current nodetype id
     * 
     * @return bool|string
     */
    public function getID()
    {
        return $this->get('IdNodeType');
    }

    /**
     * Changes the curent node type
     * 
     * @param int $nodeTypeID
     * @return int (status)
     */
    public function setID(int $nodeTypeID)
    {
        parent::__construct($nodeTypeID);
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return null;
        }
        $this->ID = $this->get('IdNodeType');
        return $this->get('IdNodeType');
    }

    public function setByName(string $name)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT IdNodeType FROM NodeTypes WHERE Name LIKE %s', $dbObj->sqlEscapeString($name));
        $dbObj->query($query);
        if (! $dbObj->numRows) {
            $backtrace = debug_backtrace();
            error_log(sprintf('Se ha intentado cargar obtener un tipo de nodo por el nombre %s'
                . ' y no se ha encontrado [src/Models/nodetype.php] script: %s file: %s line: %s',
                $name,
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line']));
            return false;
        }
        if ($dbObj->getValue('IdNodeType')) {
            return $this->setID($dbObj->getValue('IdNodeType'));
        }
        return false;
    }

    /**
     * Returns the current node type name
     * 
     * @return string(name)
     */
    public function getName()
    {
        return $this->get('Name');
    }

    /**
     * Returns true or false depending on a nodetype existence
     * 
     * @param string $name
     * @return bool
     */
    public function isNodeType(string $name)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query('SELECT IdNodeType FROM NodeTypes WHERE Name = %s', $dbObj->sqlEscapeString($name));
        if ($dbObj->numRows == 0) {
            return false;
        }
        return true;
    }

    /**
     * Changes the current node type name
     * 
     * @param string $name
     * @return int (status)
     */
    public function setName(string $name)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Return the class of the current nodetype
     * 
     * @return string (description)
     */
    public function getClass()
    {
        return $this->get('Class');
    }

    /**
     * Changes the current node type class
     * 
     * @param string $class
     * @return int (status) | bool
     */
    public function setClass(string $class)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Class', $class);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current node type description
     * 
     * @return string (description)
     */
    public function getDescription()
    {
        return $this->get('Description');
    }

    /**
     * Change the current node type description
     * 
     * @param string $description
     * @return int (status) | bool
     */
    public function setDescription(string $description)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Description', $description);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns if the node type is renderizable or not
     * 
     * @return boolean (isrenderizable)
     */
    public function getIsRenderizable()
    {
        return $this->get('IsRenderizable');
    }

    /**
     * Change the property of being renderizable or not for a nodetype
     * 
     * @param bool $isRenderizable
     * @return int (status) | bool
     */
    public function setIsRenderizable(bool $isRenderizable)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('IsRenderizable', $isRenderizable);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns if the current nodetype will have an entity in the file system
     * 
     * @return boolean (hasfsntity)
     */
    public function getHasFSEntity()
    {
        return $this->get('HasFSEntity');
    }

    /**
     * Change if the current nodetype will have an entity in the file system or not
     * 
     * @param bool $hasFSEntity
     * @return int (status)
     */
    function setHasFSEntity(bool $hasFSEntity)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('HasFSEntity', $hasFSEntity);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function isFolder()
    {
        return ($this->get('IsFolder') || $this->get('IsVirtualFolder'));
    }

    public function getIsFolder()
    {
        return $this->get('IsFolder');
    }

    public function setIsFolder(bool $value)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('IsFolder', $value);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function getIsPlainFile()
    {
        return $this->get('IsPlainFile');
    }

    function setIsPlainFile(bool $value)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('IsPlainFile', $value);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function getIsVirtualFolder()
    {
        return $this->get('IsVirtualFolder');
    }

    public function setIsVirtualFolder(bool $value)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('IsVirtualFolder', $value);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function getIsStructuredDocument()
    {
        return $this->get('IsStructuredDocument');
    }

    public function setIsStructuredDocument(bool $value)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('IsStructuredDocument', $value);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function getIsSection()
    {
        return $this->get('IsSection');
    }

    public function setIsSection(bool $value)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('IsSection', $value);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns if the current nodetype can be associated to groups
     * 
     * @return boolean (canattachgroups)
     */
    public function getCanAttachGroups()
    {
        return $this->get('CanAttachGroups');
    }

    /**
     * Changes if the nodes can abort its deletion with an specific method
     * 
     * @param $canDenyDeletion
     * @return int (status)
     */
    public function setCanDenyDeletion(bool $canDenyDeletion)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('CanDenyDeletion', $canDenyDeletion);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns if the node can abort its deletion
     * 
     * @return boolean|string
     */
    public function getCanDenyDeletion()
    {
        return $this->get('CanDenyDeletion');
    }

    /**
     * Change if the current node type can be associated to groups or not
     * 
     * @param $canAttachGroups
     * @return  int (status) | bool
     */
    public function setCanAttachGroups(bool $canAttachGroups)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('CanAttachGroups', $canAttachGroups);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current node type icon
     * 
     * @return string
     */
    public function getIcon()
    {
        return $this->get('Icon');
    }

    /**
     * Changes the current node type icon
     * 
     * @param string $icon
     * @return int (status)
     */
    public function setIcon(string $icon)
    {
        if (! $this->get('IdNodeType')) {
            $this->SetError(1);
            return false;
        }
        $result = $this->set('Icon', $icon);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Add an allowed nodetype as child of the current one
     * 
     * @param int $nodeType
     * @param int $amount
     * @return int (status)
     */
    public function addAllowedNodeType(int $nodeType, int $amount)
    {
        $query = sprintf('SELECT COUNT(*) AS total FROM NodeAllowedContents'
            . ' WHERE IdNodeType = %d AND NodeType = %d', $this->get('IdNodeType'), $nodeType);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($query);
        if ($dbObj->GetValue('total') > 0) {
            $sql = sprintf('UPDATE NodeAllowedContents SET Amount = %d'
                . ' WHERE IdNodeType = %d'
                . ' AND NodeType = %d', $amount, $this->get('IdNodeType'), $nodeType);
        } else {
            $sql = sprintf('INSERT INTO NodeAllowedContents (IdNodeType, NodeType, Amount)'
                . ' VALUES (%d, %d, %d)', $this->get('IdNodeType'), $nodeType, $amount);
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        return $dbObj->numErr;
    }

    /**
     * Replace a set of data of the AllowedContents by the pram
     * The param array of associative arrays of two fields: 'nodetype' and 'amount' means 'node type allowed' and 'allowed quantity'
     * 
     * @param array $arrayAllowed
     */
    public function replaceAllowedNodeTypeList(array $arrayAllowed)
    {
        $sql = sprintf('DELETE FROM NodeAllowedContents WHERE IdNodeType = ' . $this->get('IdNodeType'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
        $sal = $dbObj->numErr;
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        for ($i = 0; $i < count($arrayAllowed); $i++) {
            $sal = $sal + $this->addAllowedNodeType($arrayAllowed[$i]['nodetype'], $arrayAllowed[$i]['amount']);
        }
        return $sal;
    }

    /**
     * Returns an array of associative arrays of two fields:  'nodetype' and 'amount' means 'node type allowed' and 'allowed quantity'
     * 
     * @return array|null
     */
    public function getAllowedNodeTypes()
    {
        $allowedNodetypes = new NodeAllowedContent();
        $results = $allowedNodetypes->find('NodeType as nodetype, Amount as amount', 'idNodetype = %s', array($this->get('IdNodeType')));
        return $results ? $results : null;
    }

    /**
     * Adds a content by default
     * 
     * @param $nodeType
     * @param $name
     */
    public function addADefaultContent(int $nodeType, string $name)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf('INSERT INTO NodeDefaultContents (IdNodeType, NodeType, Name)' .
            ' VALUES (%d, %d, %s)', $this->get('IdNodeType'), $nodeType, $dbObj->sqlEscapeString($name));
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    /**
     * Delete a content by default
     * 
     * @param int $nodeType
     */
    public function deleteADefaultContent(int $nodeType)
    {
        $sql = sprintf('DELETE FROM NodeDefaultContents '
            . ' WHERE IdNodeType = %d'
            . ' AND NodeType = %d', $this->get('IdNodeType'), $nodeType);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
    }

    /**
     * Returns an array of associative arrays of two fields: 'nodetype' and 'name', as 'allowed node type' and 'node type name'
     * 
     * @return array|null
     */
    public function getDefaultContents()
    {
        $sql = sprintf('SELECT NodeType,Name FROM NodeDefaultContents WHERE idNodeType = %d', $this->get('IdNodeType'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $salida = [];
        while (! $dbObj->EOF) {
            $salida[] = array('nodetype' => $dbObj->row['NodeType'], 'name' => $dbObj->row['Name']);
            $dbObj->Next();
        }
        return $salida;
    }

    /**
     * Replace the whole set of data of DefaultContents by the param.
     * Param is an array of associative arrays with two fields: 'nodetype' and 'name', as 'allowed node type' and 'node type name'
     * 
     * @param array $arrayDefault
     */
    public function replaceDefaultContentsList(array $arrayDefault)
    {
        $sql = sprintf('DELETE FROM NodeDefaultContents WHERE IdNodeType = %d', $this->get('IdNodeType'));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $sal = $dbObj->numErr;
        for ($i = 0; $i < count($arrayDefault); $i++) {
            $sal = $sal + $this->addADefaultContent($arrayDefault[$i]['nodetype'], $arrayDefault[$i]['name']);
            if ($dbObj->numErr != 0) {
                $this->SetError(1);
                return null;
            }
        }
        return $sal;
    }

    /**
     * Deletes the current node type
     * 
     * @return int (status) | null
     */
    public function deleteNodeType() : bool
    {
        $sql = sprintf('DELETE FROM NodeAllowedContents WHERE idNodeType = %d', $this->get('IdNodeType'));
        $dbObj = new \Ximdex\Runtime\Db();
        if ($dbObj->execute($sql) === false) {
            $this->setError(1);
            return false;
        }
        $sql = sprintf('DELETE FROM NodeDefaultContents WHERE idNodeType = %d', $this->get('IdNodeType'));
        if ($dbObj->execute($sql) === false) {
            $this->setError(1);
            return false;
        }
        $sql = sprintf('DELETE FROM NodeTypes WHERE idNodeType = %d', $this->get('IdNodeType'));
        if ($dbObj->execute($sql) === false) {
            $this->setError(1);
            return false;
        }
        $this->ID = null;
        return true;
    }

    /**
     * Deletes last error
     */
    public function clearError()
    {
        $this->flagErr = false;
    }

    public function setAutoCleanOn()
    {
        $this->autoCleanErr = true;
    }

    public function setAutoCleanOff()
    {
        $this->autoCleanErr = false;
    }

    /**
     * Carga un error en la clase
     * 
     * @param $code
     */
    public function setError(int $code)
    {
        $this->flagErr = true;
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    /**
     * Returns true if tha class has had an error
     */
    public function hasError()
    {
        $aux = $this->flagErr;
        if ($this->autoCleanErr) {
            $this->clearError();
        }
        return $aux;
    }

    public function getAllowedAncestors()
    {
        if ($this->get('IdNodeType')) {
            $firstNode = array($this->get('IdNodeType'));
            
            // El primer nivel va a ser la variable $selectableList, el resto va a ser $typeList
            $idTypeList = $tmpArray = $this->getContainers($firstNode);
            do {
                if (empty($tmpArray)) {
                    continue;
                }
                $tmpArray = $this->getContainers($tmpArray);
                foreach ($tmpArray as $key => $idNodeTypeKey) {
                    if (in_array($idNodeTypeKey, $idTypeList)) {
                        unset($tmpArray[$key]);
                    }
                }
                $idTypeList = array_unique(array_merge($idTypeList, $tmpArray));
            } while (! empty($tmpArray));
            $typeList = ($idTypeList);
        } else {
            $typeList = null;
        }
        return $typeList;
    }

    private function getContainers(array $nodeTypes)
    {
        if (! is_array($nodeTypes)) {
            return array();
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $returnObject = array();
        foreach ($nodeTypes as $idNodeType) {
            $query = sprintf('SELECT IdNodeType FROM NodeAllowedContents WHERE NodeType = %d', $idNodeType);
            $dbObj->Query($query);
            while (! $dbObj->EOF) {
                $returnObject[] = $dbObj->GetValue('IdNodeType');
                $dbObj->Next();
            }
        }
        return $returnObject;
    }

    public function getAllowedExtensions(bool $onlyExtensions = false)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf('SELECT rntmt.idRelNodeTypeMimeType as idRelNodeTypeMimeType,
            nt.Name as Name, nt.Description as Description, rntmt.extension as extension FROM
            NodeAllowedContents as nac INNER JOIN RelNodeTypeMimeType rntmt on
            nac.NodeType = rntmt.IdNodeType INNER JOIN NodeTypes nt on nac.NodeType = nt.IdNodeType
            where nac.IdNodeType = %s and nt.IsFolder = 0', $this->GetID());
        $dbObj->Query($sql);
        $returnArray = array();
        while (! $dbObj->EOF) {
            if ($onlyExtensions) {
                $returnArray = array_merge($returnArray, explode(';', trim($dbObj->GetValue('extension'), ';'))); 
            }
            else {
                $returnElement = array();
                $returnElement['id'] = $dbObj->GetValue('idRelNodeTypeMimeType');
                $returnElement['description'] = _($dbObj->GetValue('Description'));
                $returnElement['extension'] = implode(',',
                    preg_split('/;/', $dbObj->GetValue('extension'), 0, PREG_SPLIT_NO_EMPTY));
                $returnArray[] = $returnElement;
            }
            $dbObj->Next();
        }
        return $returnArray;
    }
    
    public function getWorkflow() : ?int
    {
        return $this->get('workflowId');
    }
}
