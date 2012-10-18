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




require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryHandler_SQL.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/user.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/NodeProperty.class.php');

class QueryHandler_TOL extends QueryHandler_SQL {

	protected $conn;
	protected $queryResult;
	function QueryHandler_TOL () {
		$this->select = array();
		$this->joins = array();
		$this->where = array();
		$this->filters = array();
		$this->order = array();
		$this->limit = array();
	}
	
	protected function connect() {
		$str = FsUtils::file_get_contents(XIMDEX_ROOT_PATH .ModulesManager::path('tolDOX'). '/conf/tnsnames.ora');
		$config = parse_ini_file(XIMDEX_ROOT_PATH .ModulesManager::path('tolDOX').  '/conf/db_conf.ini');

		putenv("NLS_LANG={$config['lang']}");

		$this->conn = oci_connect($config['username'], $config['password'], $str);
		if (!$this->conn) {
		    $e = oci_error();
		    XMD_Log::fatal($e['message']);
		}
		
	}
	
	protected function query($query) {
		$this->connect();

		$this->queryResult = oci_parse($this->conn, $query);
		if (!$this->queryResult) {
		    $e = oci_error($this->conn);
		    XMD_Log::fatal($e['message']);
		}
		
		// Perform the logic of the query
		$r = oci_execute($this->queryResult);
		if (!$r) {
		    $e = oci_error($this->queryResult);
		    XMD_Log::fatal($e['message']);
		}
	}
	
	public function getResult($query) {
		$this->query($query);
		$result = array();
		// Fetch the results of the query
		$i = 0;
		while ($row = oci_fetch_array($this->queryResult, OCI_ASSOC+OCI_RETURN_NULLS)) {
		    foreach ($row as $key => $item) {
		    	$result[$i][$key] = $item;
		    }
		    $i++;
		}

		oci_free_statement($this->queryResult);
		oci_close($this->conn);

		return $result;
	}
	
	public function count($query) {
		$options = $this->getQueryOptions($query);
		$query = $this->createQuery($options);
		$query = $this->filterUsers($query);
		
		
		if (empty($this->filters)) {
			return 0;
		}
		$countQuery = preg_replace('/^select (.*) from/', 'select count(1) as records from', $query);
		
		
		$records = $this->getResult($countQuery);
		return $records[0]['RECORDS'];
	}
	
	protected function doSearch($query, &$options, $records) {
		if (empty($this->filters)) {
			return array('records' => 0, 'items' => 0, 'page' => 1, 'data' => array());
		}
		
		
		$query = $this->filterUsers($query);
		//http://www.oracle.com/technology/oramag/oracle/06-sep/o56asktom.html
		// how row nums works 
		$options['items'] = isset($options['items']) && $options['items'] >= 1 ? $options['items'] : self::ITEMS_PER_PAGE;
		$options['page'] = isset($options['page']) && $options['page'] >= 1 ? $options['page'] : 1;
		if (isset($options['low_limit']) 
			&& isset($options['high_limit'])
			&& $options['low_limit'] >= 0
			&& $options['high_limit'] > 0) {
			$lowRow = $options['low_limit'];
			$highRow = $options['high_limit'];
				
		} else {
			$pages = ceil($records / $options['items']);
			$offset = ($options['page'] - 1) * $options['items'];
	
			$lowRow = $offset;
			$highRow = $options['items'];
		}
		/*
		$where = implode (' AND ', $this->filters);
		
		$query = preg_replace('/where /', 'where ' . $where, $query);*/
		$query = $this->createQuery($options);

		if (preg_match('/ROWNUM/', $query) > 0) {
			$query = sprintf("select * from (%s) where rn >= %d and rn <= %d", $query, $lowRow, $highRow);
		}
		$result = $this->getResult($query);
		$result = array(
			'records' => count($result),
			'items' => $options['items'],
			'page' => $options['page'],
			'pages' => $pages,
			'data' => $result
		);
		return $result;
	}

	protected function createQuery(&$options) {

		$this->select[] = 'ROWNUM rn, d.docid, d.path, d.titulo, d.tipoid, dt.nombre as nombretipo, d.publicado, d.fechaalta, d.fecharevision, d.fechadocumento';

				
		$this->joins[] = 'TOL.documento d inner join TOL.doctipo dt on d.tipoid = dt.tipoid';
		$this->select = array_unique($this->select);
		$this->joins = array_unique($this->joins);
//		$this->where[] = 'dt.tipoid = d.tipoid';
		$this->where = array_unique($this->where);
		$this->filters = array_unique($this->filters);
		$this->order = array_unique($this->order);
		$this->limit = array_unique($this->limit);

		
		$this->createFilters($options['filters']);
		
		
		$this->createSorts($options['sorts']);
		
		$query = sprintf(
			'select %s from %s where %s %s %s %s',
			implode(', ', $this->select),
			implode(' ', $this->joins),
			implode(' and ', $this->where),
//			implode(" {$options['condition']} ", $this->filters),
			(count($this->where) != 0 && count($this->filters) != 0) ? ' and ' : '',
			(count($this->filters) == 0 ? '' : sprintf('(%s)', implode(" {$options['condition']} ", $this->filters))),
			(count($this->order) == 0 ? '' : ' order by ' . implode(', ', $this->order))
		);
		
		
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
				case 'path':
					if (in_array($comp, array('equal', 'nonequal', 'startswith'))) {
						$this->filters[] = sprintf('%s', $this->createComparation($comp, array($cont), $field));
					} else {
						$this->filters[] = sprintf('d.Path %s', $this->createComparation($comp, array($cont), $field));
					}
					break;
				case 'usuario':
					if (in_array($comp, array('equal', 'nonequal', 'startswith', 'endswith'))) {
						$this->filters[] = sprintf('%s', $this->createComparation($comp, array($cont), $field));
					} else {
						$this->filters[] = sprintf('d.Path %s', $this->createComparation($comp, array($cont), $field));
					}
					break;
					
				case 'fechaalta':
					$this->filters[] = sprintf('trunc(d.FechaAlta) %s', $this->createComparation($comp, array($cont, $to)));
					break;
					
				case 'fecharevision':
					$this->filters[] = sprintf('trunc(d.FechaRevision) %s', $this->createComparation($comp, array($cont, $to)));
					break;
					
				case 'publicado':
					$this->filters[] = sprintf('d.Publicado = %s', $cont == 'true' ? 1 : 0);
					break;
					
				case 'tolid':
					$this->filters[] = sprintf('d.docid %s', $this->createComparation($comp, array($cont)));
					break;
					
				case 'categoria':

					$this->filters[] = sprintf('dt.nombre %s', $this->createComparation($comp, array($cont)));
					break;
					
				default:
					$this->filters[] = sprintf('d.%s %s', $field, $this->createComparation($comp, array($cont)));
			}
		}
	}

	
	protected function filterUsers($query) {
		
		$userID = XSession::get('userID');
		$user = new User($userID);
		$login = strtoupper($user->get('Login'));
		$this->filters[] = sprintf("regexp_like(substr(d.path, 32), '^[A-Z]%s')", $login);
		
		// Dont find nodes who are already in ximdex
		$nodeProperty = new NodeProperty();
		$tolIds = $nodeProperty->find('Value', 'Property = %s', array('tolID'), MONO);
		
		$this->filters[] = sprintf("d.docid not in (%s)", implode(',', $tolIds));
		
		$userFilters = '';
		$query = preg_replace('/%userFilter%/', $userFilters, $query);
		
		return $query;
		
	}
	
	protected function createSorts($sorts) {
		foreach ($sorts as $sort) {
			if ($sort['field'] == 'Name') {
				$sort['field'] = 'd.path';
			}
			$this->order[] = sprintf('%s %s', $sort['field'], $sort['order']);
		}
	}
	
	protected function createComparation($comp, $values=array(), $field = NULL) {

		$str = '';
		for ($i=0; $i<count($values); $i++) {
			// Escape special chars and wildcards
			$values[$i] = addslashes(stripslashes($values[$i]));
			$values[$i] = str_replace('%', '\%', $values[$i]);
//			$values[$i] = str_replace('_', '\_', $values[$i]);
		}

		switch ($comp) {
			case 'contains':
				$str = sprintf("like '%%%s%%'", $values[0]);
				break;
			case 'nocontains':
				$str = sprintf("not like '%%%s%%'", $values[0]);
				break;
			case 'equal':
				if ($field == 'path') {
					$str = sprintf("regexp_like(substr(d.path, 32), '%s')", $values[0]);
				} elseif ($field == 'usuario') {
					$str = sprintf("substr(d.path, 33, 3) = '%s'", $values[0]);
				} else {
					$str = sprintf("= '%s'", $values[0]);
				}
				break;
			case 'nonequal':
				if ($field == 'path') {
					$str = sprintf("not regexp_like(substr(d.path, 32), '%s')", $values[0]);
				} elseif ($field == 'usuario') {
					$str = sprintf("substr(d.path, 33, 3) <> '%s'", $values[0]);
				} else {
					$str = sprintf("<> '%s'", $values[0]);
				}
				break;
			case 'startswith':
				if ($field == 'path') {
					$str = sprintf("regexp_like(substr(d.path, 32), concat('%s', '.*'))", $values[0]);
				} elseif ($field == 'usuario') { 
					$str = sprintf("regexp_like(substr(d.path, 33, 3), concat('%s', '.*'))", $values[0]);
				} else {
					$str = sprintf("like '%s%%'", $values[0]);
				}
				break;
			case 'endswith':
				if ($field == 'usuario') { 
					$str = sprintf("regexp_like(substr(d.path, 33, 3), concat('.*', '%s'))", $values[0]);
				} else {
					$str = sprintf("like '%%%s'", $values[0]);
				}
				break;
			case 'previousto':
				$str = sprintf("< '%s'", $values[0]);
				break;
			case 'laterto':
				$str = sprintf("> '%s'", $values[0]);
				break;
			case 'inrange':
				$str = sprintf("between '%s' and '%s'", $values[0], $values[1]);
				break;
			case 'in':
				$str = sprintf("in (%s)", implode(', ', $values));
				break;
		}

		return $str;
	}
	
	
}

?>
