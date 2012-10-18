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




require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryHandler_Abstract.class.php');

class QueryHandler_XVFS extends QueryHandler_Abstract {

	protected function recordsetToArray($data) {
	}

	protected function doSearch($query, &$options) {
		return array();
	}

	protected function createQuery(&$options) {

		$this->select = array();
		$this->joins = array();
		$this->where = array();
		$this->filters = array();
		$this->order = array();
		$this->limit = array();

		$this->createFilters($options['filters']);
		$this->createSorts($options['sorts']);

		$this->select = array_unique($this->select);
		$this->joins = array_unique($this->joins);
		$this->where = array_unique($this->where);
		$this->filters = array_unique($this->filters);
		$this->order = array_unique($this->order);
		$this->limit = array_unique($this->limit);
	}

	protected function createSorts($sorts) {
	}

	protected function createFilters($filters) {
	}

	protected function createComparation($comp, $values=array()) {
	}

	protected function fieldMapper($field) {
	}

}

?>
