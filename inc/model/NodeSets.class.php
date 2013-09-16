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




 
if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
}
require_once (XIMDEX_ROOT_PATH . '/inc/model/orm/NodeSets_ORM.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/model/RelNodeSetsNode.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/model/RelNodeSetsUsers.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/model/iterators/I_NodeSets.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/model/iterators/I_NodeSetsNodes.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/model/iterators/I_NodeSetsUsers.class.php');

class NodeSets extends NodeSets_ORM {
	
	public function __construct($id=null) {
		parent::GenericData($id);
	}
	
	/**
	 *	Returns the set id
	 */
	public function getId() {
		return $this->Id;
	}
	
	/**
	 *	Returns the set name
	 */
	public function getName() {
		return $this->Name;
	}
	
	/**
	 *	Returns the sets count
	 */
	public function getItems() {
		$db = new DB();
		$db->query(sprintf('select count(1) as total from RelNodeSetsNode where IdSet = %s', $this->getId()));
		return $db->getValue('total');
	}
	
	/**
	 *	Static method that creates a new NodeSet and returns the related object
	 */
	static public function & create($name) {
		
		$ns = new NodeSets();
		$ns->set('Name', $name);
		$newId = $ns->add();
		return $ns;
	}
	
	/**
	 *	Deletes the current set
	 */
	public function delete() {
	
		$db = new DB();
		
		$sql = sprintf('delete from RelNodeSetsNode where IdSet = %s', $this->getId());
		$db->execute($sql);
		$sql = 'alter table RelNodeSetsNode auto_increment = 0';
		$db->execute($sql);
		
		$sql = sprintf('delete from RelNodeSetsUsers where IdSet = %s', $this->getId());
		$db->execute($sql);
		$sql = 'alter table RelNodeSetsUsers auto_increment = 0';
		$db->execute($sql);
		
		$ret = parent::delete();
		$sql = 'alter table NodeSets auto_increment = 0';
		$db->execute($sql);
		return $ret;
	}
	
	/**
	 *	Returns an iterator of all node sets by user
	 */
	static public function & getSets($idUser) {
		$it = new I_NodeSetsUsers('IdUser = %s', array($idUser));
		$sets = array();
		while ($set = $it->next()) {
			$sets[] = $set->getIdSet();
		}
		$it = new I_NodeSets('Id in (%s)', array(implode(',', $sets)), NO_ESCAPE);
		return $it;
	}
	
	/**
	 *	Returns an iterator of all node sets
	 */
	static public function & getAllSets() {
		$it = new I_NodeSets('', array());
		return $it;
	}
	
	/**
	 *	Returns an iterator of all related nodes in this set
	 */
	public function & getNodes() {
		$it = new I_NodeSetsNodes('IdSet = %s', array($this->getId()));
		return $it;
	}
	
	/**
	 *	Adds a new node to the current set and returns the related object
	 */
	public function & addNode($idNode) {
		$rel = RelNodeSetsNode::create($this->getId(), $idNode);
		return $rel;
	}
	
	/**
	 *	Deletes a node from the current set
	 */
	public function deleteNode($idNode) {
		$rel = new RelNodeSetsNode();
		$rel = $rel->find(ALL, 'IdSet = %s and IdNode = %s', array($this->getId(), $idNode));
		if (count($rel) > 0 && $rel[0]['Id'] > 0) {
			$rel = new RelNodeSetsNode($rel[0]['Id']);
			$rel->delete();
		}
		return $rel;
	}
	
	/**
	 *	Returns an iterator of all related users can see this set
	 */
	public function & getUsers() {
		$it = new I_NodeSetsUSers('IdSet = %s', array($this->getId()));
		return $it;
	}
	
	/**
	 *	Adds a new user to the current set and returns the related object
	 */
	public function & addUser($idUser, $owner=RelNodeSetsUsers::OWNER_NO) {
		$rel = RelNodeSetsUsers::create($this->getId(), $idUser, $owner);
		return $rel;
	}
	
	/**
	 *	Deletes an user from the current set
	 */
	public function deleteUser($idUser) {
		$rel = new RelNodeSetsUsers();
		$rel = $rel->find(ALL, 'IdSet = %s and IdUser = %s', array($this->getId(), $idUser));
		if (count($rel) > 0 && $rel[0]['Id'] > 0) {
			$rel = new RelNodeSetsUsers($rel[0]['Id']);
			$rel->delete();
		}
		return $rel;
	}
	
}
?>
