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
 *  @version $Revision: 7740 $
 */




 
if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
}
require_once (XIMDEX_ROOT_PATH . '/inc/model/orm/RelValidityVersions_ORM.class.php');
//require_once (XIMDEX_ROOT_PATH . '/inc/model/iterators/I_RelValidityVersions_ORM.class.php');

class RelValidityVersions extends RelValidityVersions_ORM {
	
	public function __construct($id=null) {
		parent::GenericData($id);
	}

	/**
	 *	Returns the rel id
	 */
	public function getId() {
		return $this->IdRel;
	}
	
	/**
	 *	Returns the valid node id
	 */
	public function getIdNode() {
		return $this->IdNode;
	}
	
	/**
	 *	Returns the validity version id
	 */
	public function getIdValidity() {
		return $this->IdValidity;
	}
	
	/**
	 *	Returns the validity start version
	 */
	public function getValidFrom() {
		return $this->ValidFrom;
	}
	
	/**
	 *	Returns the validity end version
	 */
	public function getValidTo() {
		return $this->ValidTo;
	}
	
	/**
	 *	Static method that creates a new validity version and returns the related object
	 */
	static public function & create($idNode, $idValidity, $validFrom, $validTo) {
		
		$validFrom = RelValidityVersions::formatDate($validFrom);
		$validTo = RelValidityVersions::formatDate($validTo);
		
		$ns = new RelValidityVersions();
		$ns->set('IdNode', $idNode);
		$ns->set('IdValidity', $idValidity);
		$ns->set('ValidFrom', $validFrom);
		$ns->set('ValidTo', $validTo);
		$newId = $ns->add();
		
		return $ns;
	}
	
	static protected function formatDate($date) {
		$tokens = explode('/', $date);
		$ts = mktime(0, 0, 0, $tokens[1], $tokens[0], $tokens[2]);
		$date = date('Y-m-d', $ts);
		return $date;
	}
	
	/**
	 *	Deletes the current relation
	 */
	public function delete() {
	
		$db = new DB();		
		$ret = parent::delete();
		$sql = 'alter table RelValidityVersions auto_increment = 0';
		$db->execute($sql);
		return $ret;
	}
	
	/**
	 *	Returns the validity relation by node id
	 */
	static public function & getByIdNode($idNode) {
		$rel = new RelValidityVersions();
		$result = $rel->find(ALL, 'IdNode = %s', array($idNode));
		if (count($result) > 0 && $result[0]['IdRel'] > 0) {
			$rel = new RelValidityVersions($result[0]['IdRel']);
		}
		return $rel;
	}
	
	/**
	 *	Returns the validity relation by validity id
	 */
	static public function & getByIdValidity($idValidity) {
		$rel = new RelValidityVersions();
		$result = $rel->find(ALL, 'IdValidity = %s', array($idValidity));
		if (count($result) > 0 && $result[0]['IdRel'] > 0) {
			$rel = new RelValidityVersions($result[0]['IdRel']);
		}
		return $rel;
	}
	
}
?>
