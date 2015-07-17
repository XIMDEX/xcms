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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
require_once(XIMDEX_ROOT_PATH . '/inc/model/orm/NodeTypes_ORM.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/NodeAllowedContent.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/RelNodeTypeMetadata.class.php');

define('NODETYPE_SECTION', 5015);

class NodeType extends NodeTypes_ORM{
	var $ID;			//Current node id
	var $dbObj;			// DB object used in methods

	var $flagErr;				// shows if there was an error
   	var $numErr;				// Error code
	var $msgErr;				// Error message
	var $errorList= array(		// Class error list
		1 => 'Database connection error',
		2 => 'Nodetype does not exist'
		);
	var $_cache = 0;
	var $_useMemCache = 0;
   	var $_fieldsToTraduce = array('Description');


   	/**
   	 *
   	 * @param $nodeTypeID
   	 * @return unknown_type
   	 */
	function NodeType($nodeTypeID = null)
	{
		$this->errorList[1] = _('Database connection error');
		$this->errorList[2] = _('Nodetype does not exist');
		$this->flagErr = FALSE;
		$this->autoCleanErr = TRUE;

		parent::__construct($nodeTypeID);
	}

	/**
	*
	* @return unknown_type
	*/
	function GetConstructor(){
		$query = sprintf("SELECT IdAction FROM NodeConstructors WHERE IdNodeType = %d", $this->get('IdNodeType'));
		$dbObj = new DB();
		$dbObj->Query($query);
		if ($dbObj->numRows == 1) return $dbObj->GetValue('IdAction');
		return 0;
	}

	/**
	 * Creates a new nodetype and load its nodeTypeID
	 * @param $name
	 * @param $icon
	 * @param $isRenderizable
	 * @param $hasFSEntity
	 * @param $canAttachGroups
	 * @param $isContentNode
	 * @param $description
	 * @param $class
	 * @param $nodeTypeID
	 * @return NodeTypeID - lo carga como atributo
	 */
	function CreateNewNodeType($name, $icon, $isRenderizable, $hasFSEntity, $canAttachGroups, $isContentNode, $description, $class, $nodeTypeID=null)
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
	 * @return array of idNodeType
	 */
	function GetAllNodeTypes(){
		$sql = "SELECT idNodeType FROM NodeTypes";
		$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0) {
			$this->SetError(1);
			return null;
		}

		while (!$dbObj->EOF) {
			$salida[] = $dbObj->getValue("idNodeType");
			$dbObj->Next();
		}
		return $salida ? $salida : NULL;
	}

	/**
	 *  Returns the current nodetype id
	 * @return nodetypeid
	 */
	function GetID(){
		return $this->get('IdNodeType');
	}

	/**
	 * Changes the curent node type
	 * @param $nodeTypeID
	 * @return int (status)
	 */
	function SetID($nodeTypeID){
		parent::GenericData($nodeTypeID);
		if (!($this->get('IdNodeType') > 0)) {
			$this->SetError(1);
			return null;
		}
		$this->ID = $this->get('IdNodeType');
		return $this->get('IdNodeType');
	}

	/**
	 *
	 * @param $name
	 * @return unknown_type
	 */
	function SetByName($name){
		$dbObj = new DB();
		$query = sprintf("SELECT IdNodeType FROM NodeTypes WHERE Name LIKE %s", $dbObj->sqlEscapeString($name));
		$dbObj->Query($query);
		if (!($dbObj->numRows > 0)) {
			$backtrace = debug_backtrace();
			error_log(sprintf("Se ha intentado cargar obtener un tipo de nodo por el nombre %s"
						. " y no se ha encontrado [inc/model/nodetype.php] script: %s file: %s line: %s",
						$name,
						$_SERVER['SCRIPT_FILENAME'],
						$backtrace[0]['file'],
						$backtrace[0]['line']));
			return false;
		}

		if (($dbObj->GetValue("IdNodeType") > 0)) {
			return $this->SetID($dbObj->GetValue("IdNodeType"));
		}
		return false;
	}

	/**
	 * Returns the current node type name
	 * @return string(name)
	 */
	function GetName() {
		return $this->get("Name");
	}

	/**
	 * Returns true or false depending on a nodetype existence
	 * @param $name
	 * @return unknown_type
	 */
	function IsNodeType($name) {
		$dbObj = new DB();
		$dbObj->Query("SELECT IdNodeType FROM NodeTypes WHERE Name = %s", $dbObj->sqlEscapeString($name));
		if ($dbObj->numRows==0) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Changes the current node type name
	 * @param $name
	 * @return int (status)
	 */
	function SetName($name) {
		if (!($this->get('IdNodeType') > 0)) {
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
	 * @return string (description)
	 */
	function GetClass(){
		return $this->get("Class");
	}

	/**
	 * Changes the current node type class
	 * @param $class
	 * @return int (status)
	 */
	function SetClass($class){
		if (!($this->get('IdNodeType') > 0)) {
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
	 * @return string (description)
	 */
	function GetDescription(){
    	return $this->get("Description");
	}

	/**
	 * Change the current node type description
	 * @param $description
	 * @return int (status)
	 */
	function SetDescription($description) {
		if (!($this->get('IdNodeType') > 0)) {
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
	 * @return boolean (isrenderizable)
	 */
	function GetIsRenderizable() {
    	return $this->get("IsRenderizable");
	}

	/**
	 * Change the property of being renderizable or not for a nodetype
	 * @param $isRenderizable
	 * @return int (status)
	 */
	function SetIsRenderizable($isRenderizable) {
		if (!($this->get('IdNodeType') > 0)) {
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
	 * @return boolean (hasfsntity)
	 */
	function GetHasFSEntity() {
    	return $this->get("HasFSEntity");
	}

	/**
	 * Change if the current nodetype will have an entity in the file system or not
	 * @param $hasFSEntity
	 * @return int (status)
	 */
	function SetHasFSEntity($hasFSEntity) {
		if (!($this->get('IdNodeType') > 0)) {
			$this->SetError(1);
			return false;
		}

		$result = $this->set('HasFSEntity', $hasFSEntity);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	public function isFolder() {
		return ($this->get('IsFolder') || $this->get('IsVirtualFolder'));
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetIsFolder() {
    	return $this->get("IsFolder");
	}

	/**
	 *
	 * @param $value
	 * @return unknown_type
	 */
	function SetIsFolder($value) {
		if (!($this->get('IdNodeType') > 0)) {
			$this->SetError(1);
			return false;
		}

		$result = $this->set('IsFolder', $value);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetIsPlainFile() {
    	return $this->get("IsPlainFile");
	}

	/**
	 *
	 * @param $value
	 * @return unknown_type
	 */
	function SetIsPlainFile($value) {
		if (!($this->get('IdNodeType') > 0)) {
			$this->SetError(1);
			return false;
		}

		$result = $this->set('IsPlainFile', $value);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetIsVirtualFolder() {
    	return $this->get("IsVirtualFolder");
	}

	/**
	 *
	 * @param $value
	 * @return unknown_type
	 */
	function SetIsVirtualFolder($value) {
		if (!($this->get('IdNodeType') > 0)) {
			$this->SetError(1);
			return false;
		}

		$result = $this->set('IsVirtualFolder', $value);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetIsStructuredDocument() {
    	return $this->get("IsStructuredDocument");
	}

	/**
	 *
	 * @param $value
	 * @return unknown_type
	 */
	function SetIsStructuredDocument($value) {
		if (!($this->get('IdNodeType') > 0)) {
			$this->SetError(1);
			return false;
		}

		$result = $this->set('IsStructuredDocument', $value);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetIsSection() {
    	return $this->get("IsSection");
	}

	/**
	 *
	 * @param $value
	 * @return unknown_type
	 */
	function SetIsSection($value) {
		if (!($this->get('IdNodeType') > 0)) {
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
	 * @return boolean (canattachgroups)
	 */
	function GetCanAttachGroups(){
		return $this->get["CanAttachGroups"];
	}

	/**
	 * Changes if the nodes can abort its deletion with an specific method
	 * @param $canDenyDeletion
	 * @return int (status)
	 */
	function SetCanDenyDeletion($canDenyDeletion) {
		if (!($this->get('IdNodeType') > 0)) {
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
	 * @return unknown_type
	 */
	function GetCanDenyDeletion(){
		return $this->get("CanDenyDeletion");
	}

	/**
	 * Change if the current node type can be associated to groups or not
	 * @param $canAttachGroups
	 * @return  int (status)
	 */
	function SetCanAttachGroups($canAttachGroups) {
		if (!($this->get('IdNodeType') > 0)) {
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
	 * @return return string (icon)
	 */
	function GetIcon() {
    	return $this->get("Icon");
	}

	/**
	 * Changes the current node type icon
	 * @param $icon
	 * @return int (status)
	 */
	function SetIcon($icon) {
		if (!($this->get('IdNodeType') > 0)) {
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
     * @param $nodeType
     * @param $amount
     * @return int (status)
     */
	function AddAllowedNodeType($nodeType, $amount) {
		$query = sprintf("SELECT COUNT(*) AS total FROM NodeAllowedContents"
				. " WHERE IdNodeType = %d AND NodeType = %d", $this->get('IdNodeType'), $nodeType);
		$dbObj = new DB();
		$dbObj->Query($query);
		if ($dbObj->GetValue('total') > 0) {
	        $sql = sprintf("UPDATE NodeAllowedContents SET Amount = %d"
    	            . " WHERE IdNodeType = %d"
                    . " AND NodeType = %d", $amount, $this->get('IdNodeType'), $nodeType);
		} else {
	        $sql = sprintf("INSERT INTO NodeAllowedContents (IdNodeType,NodeType,Amount)"
	                . " VALUES (%d, %d, %d)", $this->get('IdNodeType'), $nodeType, $amount);
		}
		$dbObj = new DB();
    	$dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        	return null;
        }
        return $dbObj->numErr;
	}

    /**
     * Delete an allowed nodetype
     * @param $nodeType
     * @return int (status)
     */
    function DeleteAllowedNodeType($nodeType) {
    	$sql = sprintf("DELETE FROM NodeAllowedContents "
                . " WHERE IdNodeType = "
                . " AND NodeType = ", $this->get('IdNodeType'), $nodeType);
		$dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        }
	}

	/**
	 * Replace a set of data of the AllowedContents by the pram.
	 * The param array of associative arrays of two fields: 'nodetype' and 'amount' means 'node type allowed' and 'allowed quantity'
	 * @param $arrayAllowed
	 * @return unknown_type
	 */
	function ReplaceAllowedNodeTypeList($arrayAllowed) {
    	$sql = sprintf("DELETE FROM NodeAllowedContents "
                . " WHERE IdNodeType = ".$this->get('IdNodeType'));
		$dbObj = new DB();
        $dbObj->Execute($sql);
        $sal = $dbObj->numErr;
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        	return null;
        }
		for($i=0; $i < count($arrayAllowed); $i++) {
    		$sal = $sal + $this->addAllowedNodeType($arrayAllowed[$i]["nodetype"],
        									$arrayAllowed[$i]["amount"]);
        }
        return $sal;
	}

	/**
	 * Returns an array of associative arrays of two fields:  'nodetype' and 'amount' means 'node type allowed' and 'allowed quantity'
	 * @return Array[i][nodetype||amount]
	 */
	function GetAllowedNodeTypes() {
		$allowedNodetypes = new NodeAllowedContent();
		$results = $allowedNodetypes->find('NodeType as nodetype, Amount as amount', 'idNodetype = %s', array($this->get('IdNodeType')));
		// print_r($salida);
    	return $results ? $results : NULL;
	}

    /**
     * Adds a content by default
     * @param $nodeType
     * @param $name
     * @return int (status)
     */
	function AddADefaultContent($nodeType, $name) {
		$dbObj = new DB();
    	$sql = sprintf("INSERT INTO NodeDefaultContents (IdNodeType,NodeType,Name)".
                " VALUES (%d, %d, %s)", $this->get('IdNodeType'), $nodeType, $dbObj->sqlEscapeString($name));
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        }
	}

    /**
     * Delete a content by default
     * @param $nodeType
     * @return int (status)
     */
	function DeleteADefaultContent($nodeType) {
    	$sql = sprintf("DELETE FROM NodeDefaultContents "
                . " WHERE IdNodeType = %d"
                . " AND NodeType = %d", $this->get('IdNodeType'), $nodeType);
		$dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        }
    }

	/**
	 *  Returns an array of associative arrays of two fields: 'nodetype' and 'name', as 'allowed node type' and 'node type name'
	 * @return Array[i][nodetype||name]
	 */
    function GetDefaultContents() {
      	$sql = sprintf("SELECT NodeType,Name FROM NodeDefaultContents" .
        		" WHERE idNodeType = %d", $this->get('IdNodeType'));
		$dbObj = new DB();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        	return null;
        }
	    while (!$dbObj->EOF) {
	    	$salida[] = array ( "nodetype" => $dbObj->row["NodeType"],
            						"name"     => $dbObj->row["Name"]);
            $dbObj->Next();
		}
        // print_r($salida);
    	return $salida;
	}

	/**
	 * REplace the whole set of data of DefaultContents by the param.
	 * Param is an array of associative arrays with two fields: 'nodetype' and 'name', as 'allowed node type' and 'node type name'
	 * @param $arrayDefault
	 * @return unknown_type
	 */
	function ReplaceDefaultContentsList($arrayDefault) {
    	$sql = sprintf("DELETE FROM NodeDefaultContents " .
                " WHERE IdNodeType = %d", $this->get('IdNodeType'));
		$dbObj = new DB();
        $dbObj->Execute($sql);
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        	return null;
        }
        $sal = $dbObj->numErr;
		for($i=0; $i < count($arrayDefault); $i++) {
    		$sal = $sal + $this->addADefaultContent($arrayDefault[$i]["nodetype"],
        									$arrayDefault[$i]["name"]);
            if ($dbObj->numErr != 0) {
	        	$this->SetError(1);
    	    	return null;
        	}
        }
        return $sal;
	}

	/**
	 * Deletes the current node type
	 * @return int (status)
	 */
	function DeleteNodeType() {
    	$sql = sprintf("DELETE FROM NodeAllowedContents WHERE idNodeType = %d", $this->get('IdNodeType'));
    	$dbObj = new DB();
      	$dbObj->Execute($sql);
		$salida = $dbObj->numErr;
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        	return null;
        }
        if ($salida != 0) return $salida;

    	$sql = sprintf("DELETE FROM NodeDefaultContents WHERE idNodeType = %d", $this->get('IdNodeType'));
      	$dbObj->Execute($sql);
		$salida = $dbObj->numErr;
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        	return null;
        }
        if ($salida != 0) return $salida;

		$sql = sprintf("DELETE FROM NodeTypes WHERE idNodeType = %d", $this->get('IdNodeType'));
      	$dbObj->Execute($sql);
  		$salida = $dbObj->numErr;
        if ($dbObj->numErr != 0) {
        	$this->SetError(1);
        	return null;
        }
        if ($salida != 0) return $salida;

       	$this->ID = null;
        return $salida;
	}

    /**
     * Deletes last error
     * @return unknown_type
     */
	function ClearError() {
	    $this->flagErr = FALSE;
    }

    /**
     *
     * @return unknown_type
     */
    function SetAutoCleanOn() {
    	$this->autoCleanErr = TRUE;
    }
    function SetAutoCleanOff() {
    	$this->autoCleanErr = FALSE;
    }

	/**
	 * Carga un error en la clase
	 * @param $code
	 * @return unknown_type
	 */
    function SetError($code) {
    	$this->flagErr = TRUE;
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

    /**
     * Returns true if tha class has had an error
     * @return unknown_type
     */
	function HasError() {
    	$aux = $this->flagErr;
        if ($this->autoCleanErr)
	        $this->ClearError();
        return $aux;
    }

    function getAllowedAncestors(){
	    if ($this->get('IdNodeType')){
		$firstNode = array($this->get('IdNodeType'));
		// El primer nivel va a ser la variable $selectableList, el resto va a ser $typeList
		$idTypeList = $idSelectableList = $tmpArray = $this->getContainers($firstNode);
		do {
			if (empty($tmpArray)) {
				continue;
			}
			$tmpArray = $this->getContainers($tmpArray);

			foreach ($tmpArray as $key => $idNodeTypeKey){
				if (in_array($idNodeTypeKey, $idTypeList)) {
					unset($tmpArray[$key]);
				}
			}
			$idTypeList = array_unique(array_merge($idTypeList, $tmpArray));
		} while (!empty($tmpArray));
		$typeList = ($idTypeList);
		$selectableList = ($idSelectableList);
	} else {
		$typeList = NULL;
		$selectableList = NULL;
	}

	return $typeList;
    }

    private function getContainers($nodeTypes){

	$nodeArray = array();

	if (is_numeric($nodeTypes)){
	    $nodeArray[] = $nodeTypes;
	}
	else if (is_array($nodeTypes)){
	    $nodeArray = $nodeTypes;
	}
	else{
		return array();
	}

	$returnObject = array();
	foreach ($nodeTypes as $idNodeType) {
	    $dbObj = new DB();
	    $query = sprintf("SELECT IdNodeType FROM NodeAllowedContents WHERE NodeType = %d", $idNodeType);
	    $dbObj->Query($query);
	    while(!$dbObj->EOF) {
		    $returnObject[] = $dbObj->GetValue('IdNodeType');
		    $dbObj->Next();
	    }
	    unset($dbObj);
	}
	return $returnObject;
    }

    public function getAllowedExtensions(){
        $dbObj = new DB();
        $sql = sprintf('SELECT rntmt.idRelNodeTypeMimeType as idRelNodeTypeMimeType,
            nt.Name as Name, nt.Description as Description, rntmt.extension as extension FROM
            NodeAllowedContents as nac INNER JOIN RelNodeTypeMimeType rntmt on
            nac.NodeType = rntmt.IdNodeType INNER JOIN NodeTypes nt on nac.NodeType = nt.IdNodeType
            where nac.IdNodeType=5022 and nt.IsFolder=0',$this->get('IdNodeType'));
        $dbObj->Query($sql);
        $returnArray = array();
        while(!$dbObj->EOF) {
            $returnElement = array();
            $returnElement["id"] = $dbObj->GetValue('idRelNodeTypeMimeType');
            $returnElement["description"] = _($dbObj->GetValue('Description'));
            $returnElement["extension"] = implode(",",
                preg_split("/;/",$dbObj->GetValue('extension'),0,PREG_SPLIT_NO_EMPTY));
            $returnArray[] = $returnElement;
            $dbObj->Next();
        }
        unset($dbObj);
        return $returnArray;
    }

 }

?>
