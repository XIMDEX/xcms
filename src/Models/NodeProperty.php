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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Data\GenericData;

class NodeProperty extends GenericData
{
    public $_idField = 'IdNodeProperty';
    
    public $_table = 'NodeProperties';
    
    public $_metaData = array
    (
        'IdNodeProperty' => array('type' => "int(11)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdNode' => array('type' => "int(11)", 'not_null' => 'true'),
        'Property' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Value' => array('type' => "longblob", 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array();
    
    public $_indexes = array('IdNodeProperty');
    
    public $IdNodeProperty;
    
    public $IdNode;
    
    public $Property;
    
    public $Value;
    
    const DEFAULTSERVERLANGUAGE = 'DefaultServerLanguage';

	public function create(int $idNode, string $property, string $value = null)
	{
		$this->set('IdNode', $idNode);
		$this->set('Property', $property);
		$this->set('Value', $value);
		parent::add();
		$propertyId = $this->get('IdNodeProperty');
		if (! $propertyId) {
			Logger::error("When adding NodeProperty (idNode: $idNode, property: $property, value: $value)");
			return false;
		}
		return true;
	}

	/**
	 * Return the value of a property for a node
	 *
	 * @param int idNode
	 * @param string property
	 * @return string|null
	 */
	public function getProperty(int $idNode, string $property)
	{
		$result = $this->find('Value', "Property = %s AND IdNode = %s", array($property, $idNode), MONO);
		return empty($result) ? null : $result;
	}

	/**
	 * Deletes all node properties
	 *
	 * @param int idNode
	 * @return boolean
	 */
	public function deleteByNode(int $idNode)
	{
 		$dbObj = new \Ximdex\Runtime\Db();
        $sql = sprintf("DELETE FROM NodeProperties WHERE IdNode = %d", $idNode);
		return $dbObj->execute($sql);
	}

	/**
	 * Deletes all values for a property in a given node
	 *
	 * @param int idNode
	 * @param string property
	 * @return boolean
	 */
	public function deleteByNodeProperty(int $idNode, string $property)
	{
 		$dbObj = new \Ximdex\Runtime\Db();
        $sql = "DELETE FROM NodeProperties WHERE IdNode = $idNode AND Property = '$property'";
		$dbObj->execute($sql);
		return true;
	}

	/**
	 * Gets all node properties
	 *
	 * @param int idNode
	 * @return array|null
	 */
	public function getPropertiesByNode(int $idNode)
	{
		$result = $this->find('Property, Value', 'IdNode = %s', array($idNode), MULTI);
		if (empty($result)) {
			return null;
		}
		return $result;
	}
	
	public function cleanUpPropertyValue(string $property, string $value = null) : bool
	{    
		$db = new \Ximdex\Runtime\Db();
		$query = sprintf("DELETE FROM NodeProperties WHERE Property = %s AND Value = %s"
		    , $db->sqlEscapeString($property), $db->sqlEscapeString($value));
		return $db->execute($query);
	}

	public function getNodeByPropertyValue(string $property, string $value = null)
	{
		return $this->find('IdNode', 'Property = %s AND Value = %s', array($property, $value), MONO);
	}
}
