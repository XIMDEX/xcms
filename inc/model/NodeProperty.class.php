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



if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../');
require_once(XIMDEX_ROOT_PATH . '/inc/model/orm/NodeProperties_ORM.class.php');

class NodeProperty extends NodeProperties_ORM {

	function create($idNode, $property, $value = NULL) {

		if (is_null($idNode) || is_null($property)) {
			XMD_Log::error(_('Params node and property are mandatory'));
			return false;
		}

		$this->set('IdNode', $idNode);
		$this->set('Property', $property);
		$this->set('Value', $value);

		parent::add();
		$propertyId = $this->get('IdNodeProperty');

		if (!($propertyId > 0)) {
			XMD_Log::error(_("Adding nodeproperty"));
			return false;
		}

		return true;
	}

	/**
	 * Return the value of a property for a node
	 *
	 * @param int idNode
	 * @param string property
	 * @return string / null
	 */

	function getProperty($idNode, $property) {
		
		if (is_null($idNode) || is_null($property)) {
			XMD_Log::error(_('Params node and property are mandatory'));
			return NULL;
		}

		$result = $this->find('Value', "Property = %s AND IdNode = %s", array($property, $idNode), MONO);
		return empty($result) ? NULL : $result;
	}

	/**
	 * Deletes all node properties
	 *
	 * @param int idNode
	 * @return true / false
	 */
	
	function deleteByNode($idNode) {
		
		if (is_null($idNode)) {
			XMD_Log::error(_('Param nodeId is mandatory'));
			return false;
		}

 		$dbObj = new DB();
        $sql = sprintf("DELETE FROM NodeProperties WHERE IdNode = %d", $idNode);
		$dbObj->Execute($sql);

		return true;
	}


	/**
	 * Deletes all values for a property in a given node
	 *
	 * @param int idNode
	 * @param string property
	 * @return true / false
	 */
	
	function deleteByNodeProperty($idNode, $property) {
		
		if (is_null($idNode) || is_null($property)) {
			XMD_Log::error(_('Params nodeId and property are mandatories'));
			return false;
		}

 		$dbObj = new DB();
        $sql = "DELETE FROM NodeProperties WHERE IdNode = $idNode AND Property = '$property'";
		$dbObj->Execute($sql);

		return true;
	}

	/**
	 * Gets all node properties
	 *
	 * @param int idNode
	 * @return array / NULL
	 */
	
	function getPropertiesByNode($idNode) {

		$result = $this->find('Property, Value', 'IdNode = %s', array($idNode), MULTI);

		if (empty($result)) {
			return NULL;
		}

		return $result;
	}
	
	function cleanUpPropertyValue($property, $value) {
		$db = new DB();
		$query = sprintf("DELETE FROM NodeProperties WHERE Property = %s AND Value = %s", 
			$db->sqlEscapeString($property), $db->sqlEscapeString($value));
		$db->execute($query);
	}

	function getNodeByPropertyValue($property, $value) {
		return $this->find('IdNode', 'Property = %s AND Value = %s', array($property, $value), MONO);
	}
}
?>
