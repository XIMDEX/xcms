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




require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryHandler_TOL.class.php');

class QueryHandler_TOL_TAGS extends QueryHandler_TOL {

	protected function createQuery(&$options) {

		$this->createFilters($options['filters']);
		$this->joins[] = 'TOL.Docindice di';
		$this->order = 'di.Orderabs';

		if ($options['filters'][0]['field'] == 'materiaid') {
			$this->joins[] = 'TOL.docindice_materia dim';
			$this->order = 'di.descripcion';
		}

		if ($options['filters'][0]['field'] == 'catid') {
			$this->order = 'di.fecha, di.orden';
		}

		$this->select = 'di.catid as tagId, di.parentId, di.descripcion as tagName';

		$query = sprintf('select %s from %s where %s %s %s', $this->select, 
			count($this->joins) > 1 ? implode(', ', $this->joins) : $this->joins[0],
			count($this->filters) == 0 ? '' : implode(" {$options['condition']} ", $this->filters),
			isset($this->order) && !empty($this->order) ? ' order by ' . $this->order : '',
			isset($this->limit) && !empty($this->limit)  ? ' limit ' . $this->limit : '');

		return $query;
	}

	protected function createFilters($filters) {

		foreach ($filters as $filter) {

			$field = $filter['field'];
			$comp = $filter['comparation'];
			$cont = isset($filter['content']) ? $filter['content'] : null;
			$cont = $cont === null ? (isset($filter['from']) ? $filter['from'] : null) : $cont;
			$to = isset($filter['to']) ? $filter['to'] : null;

			switch ($field) {
				case 'Descripcion':
					$this->filters[] = sprintf("di.$field %s", $this->createComparation($comp, array($cont)));
					break;

				case 'materiaid':
					$this->filters[] = " dim.catid=di.catid and dim.$field = $cont and not exists
						(select d2.catid from TOL.docindice_materia d2 where d2.catid=di.parentid and d2.$field = $cont) ";
					break;

				case 'catid':
					$this->filters[] = " di.parentid = $cont";
					break;

/*				default:
					$this->filters[] = sprintf('di.%s %s', $field, $this->createComparation($comp, array($cont)));*/
			}
		}
	}

	public function count($query) {
		$options = $this->getQueryOptions($query);
		$query = $this->createQuery($options);
		
//		$query = $this->filterUsers($query);
		
		if (empty($this->filters)) {
			return 0;
		}
		$countQuery = preg_replace('/^select (.*) from/', 'select count(1) as records from', $query);
		
		
		$records = $this->getResult($countQuery);
		
		return $records[0]['RECORDS'];
	}
}

?>