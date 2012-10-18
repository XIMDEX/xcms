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




require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryHandler_TOL.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');

class QueryHandler_TOL_Forms_Datasource extends QueryHandler_TOL {

	protected function createQuery(&$options) {

		$this->select = array();
		$this->joins = array();
		$this->where = array();
		$this->filters = array();
		$this->order = array();
		$this->limit = array();
		$conditions = array();

		$page = isset($options['page']) ? $options['page'] : 1;

		$this->createSelect($options['select']);
		$this->createFilters($options['filters']);
		$this->createSorts($options['sorts']);
		$this->createJoins($options['joins']);
		$this->createLimit($options['depth'], $options['items'], $page);
		
		$this->select = array_unique($this->select);
		$this->joins = array_unique($this->joins);
		$this->where = array_unique($this->where);
		$this->filters = array_unique($this->filters);
		$this->order = array_unique($this->order);
		$this->limit = array_unique($this->limit);

		if (count($this->where) > 0) {
			$conditions[] = implode(' and ', $this->where);
		}

		if (count($this->filters) > 0) {
			$conditions[] = sprintf('%s', implode(' ', $this->filters));
		}
		if (count($this->limit) > 0) {
			$conditions[] = sprintf('%s', implode(' ', $this->limit));
		}

		$query = sprintf('select %s from %s %s %s', implode(', ', $this->select), 
			implode(', ', $this->joins), 
			sizeof($conditions) > 0 ? ' where ' . implode(' and ', $conditions) : '', 
			implode(' ', $this->order));

		return $query;
	}

	protected function createSelect($selects) {
		foreach ($selects as $select) {
			$this->select[] = $select;
		}
	}

	protected function createJoins($joins) {
		foreach ($joins as $join) {
			$this->joins[] = sprintf('TOL.%s', $join);
		}
	}

	protected function createSorts($sorts) {
		foreach ($sorts as $sort) {
			$this->order[] = sprintf('%s %s', $sort['field'], $sort['order']);
		}
	}

	protected function createFilters($filters) {
		foreach ($filters as $filter) {

			$field = $filter['field'];
			$comp = $filter['comparation'];
			$cont = isset($filter['content']) ? $filter['content'] : null;
			$cont = $cont === null ? (isset($filter['from']) ? $filter['from'] : null) : $cont;
			$cont = is_array($cont) ? $cont : array($cont);
			$to = isset($filter['to']) ? $filter['to'] : null;
			$operator = isset($filter['operator']) ? $filter['operator'] : '';

			$this->filters[] = sprintf('%s %s %s', $operator, $field, $this->createComparation($comp, $cont));
		}

	}

	protected function createLimit($depth, $items, $page) {

		if ($depth > 0) {
			$offset = ($page - 1) * $items;
			$this->limit[] = sprintf('rownum >= %s AND rownum <= %s', $offset, $items);
		}
	}

}

?>
