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
 *  @version $Revision: 7839 $
 */




require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryHandler_SQL.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');

class QueryHandler_SQLTREE extends QueryHandler_SQL {

	protected function recordsetToArray($rset) {

		$array = array();

		while (!$rset->EOF) {

			$nodeId = $rset->getValue(0);

			$node = new Node($nodeId);

			if ($node->getID() !== null) {

			$record = array();
				$record['nodeid']               = $nodeId;
				$record['parentid']             = $node->getParent();
				$record['name']                 = _($node->getNodeName());
				$record['nodetypeid']           = $node->getNodeType();
				$record['nodetype']             = $node->getTypeName();
				$record['relpath']              = $node->getPathList();
				$record['isdir']                = $node->nodeType->isFolder();
				$record['icon']                 = $node->getIcon();
				$record['children']             = 1;
				$record['abspath']              = '';


				$array[] = $record;
			}

			$rset->next();
		}

		return $array;
	}


	public function search($query) {
		$queryStr = $this->createQuery($query);

		// This method must return an array
		$results = $this->doSearch($queryStr, $options);

		return $results;
	}

	protected function doSearch($query, &$options, $records = 1) {

		$options['items'] = isset($options['items']) ? $options['items'] : self::ITEMS_PER_PAGE;
		$options['items'] = $options['items'] >= 1 ? $options['items'] : 1;
		$options['page'] = isset($options['page']) ? $options['page'] : 1;
		$options['page'] = $options['page'] >= 1 ? $options['page'] : 1;

		$pages = ceil($records / $options['items']);
		$offset = ($options['page'] - 1) * $options['items'];

		$rset = new DB();
		$rset->query($query);
		error_log("DEBUG $query");
		$rset = $this->recordsetToArray($rset);

		$result = array(
			'records' => $records,
			'items' => $options['items'],
			'page' => $options['page'],
			'pages' => $pages,
			'data' => $rset
		);

		return $result;
	}

	protected function createQuery(&$options) {

		$this->select = array();
		$this->from = array();
		$this->where = array();
		$this->filters = array();
		$this->order = array();
		$this->limit = array();

		$options['parentid'] = isset($options['parentid']) ? $options['parentid'] : 1;
		$options['parentid'] = $options['parentid'] >= 1 ? $options['parentid'] : 1;
		$options['depth'] = isset($options['depth']) ? $options['depth'] : 0;
		$options['depth'] = $options['depth'] >= 1 ? $options['depth'] : 0;

		$this->select[] = "n.IdNode ";
		$this->from[]   = "Nodes n, NodeTypes nt ";
		$this->where[]  = "n.IdNodeType = nt.IdNodeType ";

		$this->where[]  = sprintf(" IdParent = %d",$options['parentid'] );
		$this->createFilters($options['filters'], $options);


		$this->select = array_unique($this->select);
		$this->from = array_unique($this->from);
		$this->where = array_unique($this->where);
		$this->filters = array_unique($this->filters);
		$this->order = array_unique($this->order);
	//	$this->limit = array_unique($this->limit);


		$query = sprintf(
			'select %s from %s where %s %s',
			implode(', ', $this->select),
			implode(', ', $this->from),
			implode(' and ', $this->where),
			(count($this->filters) == 0 ? '' : sprintf(' and (%s)', implode(" {$options['condition']}  ", $this->filters)))
		);

		return $query;
	}


	protected function createFilters($filters, $options = NULL) {


		foreach ($filters as $filter) {

			$field = $filter['field'];
			$comp = $filter['comparation'];
			$cont = isset($filter['content']) ? $filter['content'] : null;
			$cont = !empty($cont)
				? $cont
				: (isset($filter['from'])
					? $filter['from']
					: null);
			$to = isset($filter['to']) ? $filter['to'] : null;

			switch ($field) {
				case 'nodetype':
					$allowedNodeTypes = $this->getAllowedNodeTypes($cont);
					$this->filters[] = sprintf(' (n.IdNodeType  %s  OR %s OR n.IdNodetype = "5013")', $this->createComparation($comp, array($cont)), $allowedNodeTypes);
					break;

				case 'nodeid':
					//if parent is "projects", we get only the current project
					if("10000" == $options['parentid'] && $filter["content"] > 0) {
						$node = new Node($filter["content"]);
						$cont = $node->GetProject();

						$this->filters[] = sprintf(' (n.IdNode  %s)', $this->createComparation($comp, array($cont)));
					}
					break;

			}
		}
	}

	private function getAllowedNodetypes($idNodeType){

	    $result = "";
	    if ($idNodeType != null) {
		$nodetype = new NodeType($idNodeType);
		$arrayNodeTypesAllowed = $nodetype->getAllowedAncestors();
		if ($arrayNodeTypesAllowed != null and count($arrayNodeTypesAllowed)){
		    $result ="n.idnodetype in (";
		    foreach ($arrayNodeTypesAllowed as $idnodetypeallowed) {

			$result .= " ".$idnodetypeallowed.",";
		    }

		    $result = substr($result, 0, -1);
		    $result .= ") ";
		}
		return $result;
	    }
	    return $result;
	    
	}

}

?>
