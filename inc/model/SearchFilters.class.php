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
require_once (XIMDEX_ROOT_PATH . '/inc/model/orm/SearchFilters_ORM.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/model/iterators/I_SearchFilters.class.php');

class SearchFilters extends SearchFilters_ORM {

	public function __construct($id=null) {
		parent::GenericData($id);
	}

	/**
	 *	Returns the filter id
	 */
	public function getId() {
		return $this->Id;
	}

	/**
	 *	Returns the filter name
	 */
	public function getName() {
		return $this->Name;
	}

	/**
	 *	Returns the handler
	 */
	public function getHandler() {
		return $this->Handler;
	}

	/**
	 *	Returns the filter
	 */
	public function getFilter($format='JSON') {
		return $this->Filter;
	}

	/**
	 *	Static method that creates a new SearchFilter and returns the related object
	 *	Filter must by an XML string
	 */
	static public function & create($name, $handler, $filter) {

		// TODO: Create a unique key in SearchFilters table.
		// Key length for Filter field must be specified....
		$checksum = md5(sprintf('%s:%s', $handler, $filter));
		$db = new DB();
		$sql = sprintf("select Name from SearchFilters where md5(concat(Handler, ':', Filter)) = '%s'", $checksum);
		$db->query($sql);

		$ns = new SearchFilters();
		if (!$db->EOF) {
			$ns->messages->add(sprintf('The filter exists with name %s', $db->getValue('Name')), MSG_TYPE_ERROR);
			return $ns;
		}

		$ns->set('Name', $name);
		$ns->set('Handler', $handler);
		$ns->set('Filter', $filter);
		$newId = $ns->add();
		return $ns;
	}

	/**
	 *	Deletes the current filter
	 */
	public function delete() {
		$db = new DB();
		$ret = parent::delete();
		$sql = sprintf('alter table %s auto_increment = 0', $this->_table);
		$db->execute($sql);
		return $ret;
	}

	/**
	 *	Returns an iterator of all node filters
	 */
	static public function & getFilters() {
		$it = new I_SearchFilters('', array());
		return $it;
	}

	/**
	 *	Executes the filter and returns an array of nodes
	 */
	public function & getNodes() {
		// TODO: Use QueryProcessor here or parametrize an instance...
	}

}
?>