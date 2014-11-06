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




require_once(XIMDEX_ROOT_PATH . '/inc/search/QueryHandler_Abstract.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');

class QueryHandler_SQL extends QueryHandler_Abstract {

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
				$record['nodetype']             = _($node->getTypeName());
                $record['nodetype_nemo']        = ($node->getTypeName());
				$record['relpath']              = $node->getPathList();
				$record['isdir']                = $node->nodeType->isFolder();
				$record['icon']                 = $node->getIcon();
				$record['children']             = count($node->getChildren());
				$record['abspath']              = str_replace("Project","Proyecto" , sprintf('%s/%s', $rset->getValue('Path'), $rset->getValue('Name')) );
				$record['creation']             = $node->get('CreationDate');
				$record['creationformated']     = date('d/m/Y', $record['creation']);
				$record['modification']         = $node->get('ModificationDate');
				$record['modificationformated'] = date('d/m/Y', $record['modification']);
				$record['versionid']            = $rset->getValue('IdVersion');
				$record['version']              = $rset->getValue('Version');
				$record['subversion']           = $rset->getValue('SubVersion');
				$record['versionnumber']        = $rset->getValue('VersionNumber');
				$record['versiondate']          = $rset->getValue('VersionDate');
				$record['versiondateformated']  = $record['versionid'] > 0 ? date('d/m/Y', $record['versiondate']) : '';
				$array[] = $record;
			}

			$rset->next();
		}

		return $array;
	}

	protected function doSearch($query, &$options, $records) {

		$options['items'] = isset($options['items']) ? $options['items'] : self::ITEMS_PER_PAGE;
		$options['items'] = $options['items'] >= 1 ? $options['items'] : 1;
		$options['page'] = isset($options['page']) ? $options['page'] : 1;
		$options['page'] = $options['page'] >= 1 ? $options['page'] : 1;

		$pages = ceil($records / $options['items']);
		$offset = ($options['page'] - 1) * $options['items'];

		if (isset($options['low_limit'])
			&& isset($options['high_limit'])
			&& $options['low_limit'] >= 0
			&& $options['high_limit'] > 0) {

			$query .= sprintf(' limit %s, %s', $options['low_limit'], $options['high_limit']);
		} else {
			$query .= sprintf(' limit %s, %s', $offset, $options['items']);

		}

		// if there is no filters return empty set
		if (empty($this->filters)) {
			return array('records' => 0, 'items' => 0, 'page' => 1, 'data' => array());
		}
//debug::log($query);
		$rset = new DB();
		$rset->query($query);
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


	public function count($query) {
		$options = $this->getQueryOptions($query);
		$query = $this->createQuery($options);
		$rset = new DB();
		$countQuery = preg_replace('/^select\s(.+?)\sfrom/ims', 'select count(1) as records from', $query);
		$countQuery = preg_replace('/\sorder\sby\s(.+?)$/ims', '', $countQuery);
//debug::log($query, $countQuery);
		$rset->query($countQuery);
		$records = $rset->getValue('records');

		return $records;
	}

	protected function createQuery(&$options) {

		$this->select = array();
		$this->joins = array();
		$this->where = array();
		$this->filters = array();
		$this->order = array();
		$this->limit = array();

		$options['parentid'] = isset($options['parentid']) ? $options['parentid'] : 1;
		$options['parentid'] = $options['parentid'] >= 1 ? $options['parentid'] : 1;
		$options['depth'] = isset($options['depth']) ? $options['depth'] : 0;
		$options['depth'] = $options['depth'] >= 1 ? $options['depth'] : 0;

		$this->select[] = "distinct n.IdNode, n.*, t.name as NodeTypeName, v.IdVersion, v.Version, v.SubVersion, concat(v.Version, '.', v.SubVersion) as VersionNumber,
							v.Date as VersionDate";
		$this->joins[] = 'FastTraverse ft left join Nodes n on ft.idChild = n.idNode left join NodeTypes t on n.IdNodeType=t.IdNodeType';
		$this->joins[] = 'left join
						(select v.IdNode, max(IdVersion) as IdVersion, max(v.Version) as Version, max(v.SubVersion) as SubVersion,
						max(v.Date) as Date
						from Versions v
						group by idnode) v on v.IdNode = n.IdNode';
		$this->where[] = sprintf('ft.idNode = %s', $options['parentid']);
		$this->where[] = 'ft.depth > 0';
		if ($options['depth'] > 0) {
			$this->where[] = sprintf('ft.depth <= %s', $options['depth']);
		}

		$this->createFilters($options['filters']);
		$this->createSorts($options['sorts']);


		$this->select = array_unique($this->select);
		$this->joins = array_unique($this->joins);
		$this->where = array_unique($this->where);
		$this->filters = array_unique($this->filters);
		$this->order = array_unique($this->order);
		$this->limit = array_unique($this->limit);


		$query = sprintf(
			'select %s from %s where %s %s %s',
			implode(', ', $this->select),
			implode(' ', $this->joins),
			implode(' and ', $this->where),
//			implode(" {$options['condition']} ", $this->filters),
			(count($this->filters) == 0 ? '' : sprintf(' and (%s)', implode(" {$options['condition']} ", $this->filters))),
			(count($this->order) == 0 ? '' : ' order by ' . implode(', ', $this->order))
		);
		return $query;
	}

	protected function createSorts($sorts) {
		foreach ($sorts as $sort) {
			switch ($sort['field']) {
				case 'nodeid':
					$sort['field'] = 'n.IdNode';
					break;
				case 'nodetype':
					$sort['field'] = 'n.IdNodeType';
					break;
				case 'abspath':
					$sort['field'] = sprintf('n.Path %s, n.Name %s', $sort['order'], $sort['order']);
					$sort['order'] = '';
					break;
				case 'creationformated':
					$sort['field'] = 'n.CreationDate';
					break;
				case 'versionnumber':
					$sort['field'] = sprintf('v.Version %s, v.SubVersion %s', $sort['order'], $sort['order']);
					$sort['order'] = '';
					break;
				case 'versiondateformated':
					$sort['field'] = 'v.Date';
					break;
			}
			$this->order[] = sprintf('%s %s', $sort['field'], $sort['order']);
		}
	}

	protected function createFilters($filters) {

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
				case 'nodeid':
					$this->filters[] = sprintf('(n.IdNode %s)', $this->createComparation($comp, array($cont)));
					break;

				case 'name':
					$this->filters[] = sprintf('(n.Name %s)', $this->createComparation($comp, array($cont)));
					break;

				case 'path':
					$this->filters[] = sprintf("(replace(concat(n.Path, '/', n.Name), '/ximDEX', '') %s)", $this->createComparation($comp, array($cont)));
					break;

				case 'parentid':
					$this->filters[] = sprintf('(n.IdParent %s)', $this->createComparation($comp, array($cont)));
					break;

				case 'content':
					// IMPORTANT: Suported only by ximRAM
					break;

				case 'nodetype':
					$this->filters[] = sprintf('(n.IdNodeType %s)', $this->createComparation($comp, array($cont)));
					break;

				case 'nodetypeset':

					break;

				case 'creation':
				case 'fechaalta':

					$conditions = array();

					$conditions[] = sprintf(
						"unix_timestamp(date_format(from_unixtime(n.CreationDate), '%%Y%%m%%d')) %s",
						$this->createComparation($comp, array($this->mktime($cont), $this->mktime($to)))
					);

					$this->filters[] = sprintf(
						'(%s)',
						implode(' and ', $conditions)
					);

					break;

				case 'publication':

					$conditions = array();

					$this->joins[] = 'left join NodeFrames nf on nf.NodeId = n.IdNode left join ServerFrames sf using(IdNodeFrame)';

					$conditions[] = sprintf(
						"unix_timestamp(date_format(from_unixtime(sf.DateUp), '%%Y%%m%%d')) %s",
						$this->createComparation($comp, array($this->mktime($cont), $this->mktime($to)))
					);
					// Si publicado
					$conditions[] = sprintf("sf.State in ('In', 'Replaced', 'Removed')");
					// else
//					$this->filters[] = sprintf("sf.State = 'Out'");

					$conditions[] = sprintf(
						'(%s)',
						implode(' and ', $conditions)
					);

					break;

				case 'categoria':
					$conditions = array();

					$this->joins[] = 'left join StructuredDocuments sd on sd.IdDoc = n.IdNode left join Nodes nsd on sd.IdTemplate = nsd.IdNode';

					$conditions[] = sprintf(
						"nsd.Name %s",
						$this->createComparation($comp, array($cont))
					);

					$this->filters[] = sprintf(
						'(%s)',
						implode(' and ', $conditions)
					);
					break;

				case 'tag':
					if(ModulesManager::isEnabled('ximTAGS')){
						$conditions = array();

					$this->joins[] = 'left join RelTagsNodes rtn on rtn.Node = n.IdNode left join RelTagsDescriptions rtd on rtd.IdTagDescription = rtn.TagDesc left join XimTAGSTags xt on xt.IdTag = rtd.Tag';

						$conditions[] = sprintf(
							"xt.Name %s",
							$this->createComparation($comp, array($cont))
						);

						$this->filters[] = sprintf(
							'(%s)',
							implode(' and ', $conditions)
						);
					}
					break;

				case 'versioned':
					$conditions = array();

					$conditions[] = sprintf(
						"unix_timestamp(date_format(from_unixtime(v.Date), '%%Y%%m%%d')) %s",
						$this->createComparation($comp, array($this->mktime($cont), $this->mktime($to)))
					);
					$this->filters[] = sprintf(
						'(%s)',
						implode(' and ', $conditions)
					);
					break;
				case 'url':
					$conditions = array();
					$this->filters[] = sprintf('(l.Url %s)', $this->createComparation($comp, array($cont)));
					$this->joins[] = 'left join Links l on l.IdLink = n.IdNode';
					break;
				case 'desc':
					$conditions = array();
					$this->filters[] = sprintf('(rld.Description %s)', $this->createComparation($comp, array($cont)));
                                        $this->joins[] = 'left join RelLinkDescriptions rld on rld.IdLink = n.IdNode';
					break;
			}
		}
	}

	protected function createComparation($comp, $values=array()) {

		$str = '';
		for ($i=0; $i<count($values); $i++) {
			// Escape special chars and wildcards
			if (!is_string($values[$i])) continue;
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
				$str = sprintf("= '%s'", $values[0]);
				break;
			case 'nonequal':
				$str = sprintf("<> '%s'", $values[0]);
				break;
			case 'startswith':
				$str = sprintf("like '%s%%'", $values[0]);
				break;
			case 'endswith':
				$str = sprintf("like '%%%s'", $values[0]);
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
				$str = sprintf("in (%s)", implode(', ', $values[0]));
				break;
		}

		return $str;
	}

}

?>