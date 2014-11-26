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
ModulesManager::file('/inc/SolrOp.class.php', 'ximRAM');

class QueryHandler_SOLR extends QueryHandler_Abstract {

	protected function recordsetToArray($data) {

		$dom = new DOMDocument('1.0', \App::getValue( 'workingEncoding'));
		$dom->resolveExternals = true;
		$dom->loadXML($data);

		$xpath = new DOMXPath($dom);
		$list = $xpath->query('/response/result/@numFound');
		$records = $list->item(0)->value;

		$rset = array();
		$docs = $xpath->query('/response/result/doc');

		foreach ($docs as $doc) {

			// The easy way: get the NodeId and instantiate a Node...
			$nodeId = $xpath->evaluate('string(int[@name="nodeid"])', $doc);

			$node = new Node($nodeId);

			if ($node->getID() !== null) {

				$path  = pathinfo($node->GetPath());
				$path =  (!empty($path['dirname']) ? $path['dirname'] : '') . (!empty($path['basename']) ? '/'.$path['basename'] : '');

				$record = array();
				$record['nodeid'] = $nodeId;
				$record['parentid'] = $node->getParent();
				$record['name'] = $node->getNodeName();
				$record['nodetypeid'] = $node->getNodeType();
				$record['nodetype'] = $node->getTypeName();
				$record['relpath'] = $node->getPathList();
				$record['isdir'] = $node->nodeType->isFolder();
				$record['icon'] = $node->getIcon();
				$record['children'] = count($node->getChildren());
				$record['abspath'] = $path;
				$record['creation'] = $node->get('CreationDate');
				$record['creationformated'] = date('d/m/Y', $record['creation']);
				$record['modification'] = $node->get('ModificationDate');
				$record['modificationformated'] = date('d/m/Y', $record['modification']);
				$record['versionid'] = null;
				$record['version'] = null;
				$record['subversion'] = null;
				$record['versionnumber'] = null;
				$record['versiondate'] = null;
				$record['versiondateformated'] = null;

				$datafactory = new DataFactory($nodeId);
				$versionId = $datafactory->GetLastVersionId();
				if ($versionId > 0) {
					$version = new Version($versionId);
					$record['versionid'] = $versionId;
					$record['version'] = $version->get('Version');
					$record['subversion'] = $version->get('SubVersion');
					$record['versionnumber'] = $record['version'] . '.' . $record['subversion'];
					$record['versiondate'] = $version->get('Date');
					$record['versiondateformated'] = date('d/m/Y', $record['versiondate']);
				}

				$rset[] = $record;
			}
		}

		return array(
			'records' => $records,
			'rset' => $rset
		);
	}

	protected function doSearch($query, &$options) {


		$solrOp = new SolrOp();
		$rset = $solrOp->read($query);
//debug::log($query, $rset);
		$rset = $this->recordsetToArray($rset);

		$pages = ceil($rset['records'] / $options['items']);

		$result = array(
			'records' => $rset['records'],
			'items' => $options['items'],
			'page' => $options['page'],
			'pages' => $pages,
			'data' => $rset['rset']
		);

//		debug::log($result);
		return $result;
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

		$options['items'] = isset($options['items']) ? $options['items'] : 50;
		$options['items'] = $options['items'] >= 1 ? $options['items'] : 1;
		$options['page'] = isset($options['page']) ? $options['page'] : 1;
		$options['page'] = $options['page'] >= 1 ? $options['page'] : 1;
		$offset = ($options['page'] - 1) * $options['items'];

		$query = sprintf(
			'%s %s',
			implode(" {$options['condition']} ", $this->filters),
			'&sort=' . implode(',', $this->order)
		);
		$query = str_replace(':', '%3A', $query);
		$query = str_replace(';', '%3B', $query);
		$query = str_replace(' ', '+', $query);
		$query = str_replace('[', '%5B', $query);
		$query = str_replace(']', '%5D', $query);
		$query = sprintf('q=%s&start=%s&rows=%s&fl=nodeid', $query, $offset, $options['items']);

		return $query;
	}

	protected function createSorts($sorts) {

		foreach ($sorts as $sort) {

			$sortField = null;

			switch ($this->fieldMapper($sort['field'])) {
				case 'nombre_documento':
					$sortField = 'nombre_documento';
					break;
			}

			if ($sortField !== null) $this->order[] = sprintf('%s %s', $sortField, strtolower($sort['order']));
		}
	}

	protected function createFilters($filters) {

		foreach ($filters as $filter) {

			$field = $this->fieldMapper($filter['field']);
			$comp = strtolower($filter['comparation']);
			$cont = isset($filter['content']) ? $filter['content'] : null;
			$cont = $cont === null ? (isset($filter['from']) ? $filter['from'] : null) : $cont;
			$to = isset($filter['to']) ? $filter['to'] : null;

			switch ($field) {
				case 'nombre_documento':
					$this->filters[] = sprintf('(nombre_documento:%s)', $this->createComparation($comp, array($cont)));
					break;

				case 'contenido':
					$this->filters[] = sprintf('(contenido:%s)', $this->createComparation($comp, array($cont)));
					break;

				case 'nodetypeId':
					$this->filters[] = sprintf('(nodetypeId%s)', $this->createComparation($comp, array($cont)));
					break;

				case '__CREATION_FIELD__':
//					$this->filters[] = sprintf('(__CREATION_FIELD__%s)', $this->createComparation($comp, array($this->mktime($cont), $this->mktime($to))));
					break;

				case '__PUBLICATION_FIELD__':
//					$this->filters[] = sprintf('(__PUBLICATION_FIELD__%s)', $this->createComparation($comp, array($this->mktime($cont), $this->mktime($to))));
					break;
			}
		}
	}

	protected function createComparation($comp, $values=array()) {

		$str = '';

		// Escape special chars... but use regexps please!
		// + - && || ! ( ) { } [ ] ^ " ~ * ? : \
		for ($i=0; $i<count($values); $i++) {
			$values[$i] = str_replace('+', '\+', $values[$i]);
			$values[$i] = str_replace('-', '\-', $values[$i]);
			$values[$i] = str_replace('&&', '\&&', $values[$i]);
			$values[$i] = str_replace('||', '\||', $values[$i]);
			$values[$i] = str_replace('!', '\!', $values[$i]);
			$values[$i] = str_replace('(', '\(', $values[$i]);
			$values[$i] = str_replace(')', '\)', $values[$i]);
			$values[$i] = str_replace('{', '\{', $values[$i]);
			$values[$i] = str_replace('}', '\}', $values[$i]);
			$values[$i] = str_replace('[', '\[', $values[$i]);
			$values[$i] = str_replace(']', '\]', $values[$i]);
			$values[$i] = str_replace('^', '\^', $values[$i]);
			$values[$i] = str_replace('"', '\"', $values[$i]);
			$values[$i] = str_replace('~', '\~', $values[$i]);
			$values[$i] = str_replace('*', '\*', $values[$i]);
			$values[$i] = str_replace('?', '\?', $values[$i]);
			$values[$i] = str_replace(':', '\:', $values[$i]);
			$values[$i] = str_replace('\\', '\\\\', $values[$i]);
		}

		switch ($comp) {
			case 'contains':
			case 'equal':
			case 'startswith':
			case 'endswith':
				$str = sprintf('"%s"', $values[0]);
				break;
			case 'nocontains':
			case 'nonequal':
				$str = sprintf('NOT "%s"', $values[0]);
				break;
			case 'previousto':
				$str = sprintf('[* TO %s]', $values[0]);
				break;
			case 'laterto':
				$str = sprintf('[%s TO *]', $values[0]);
				break;
			case 'inrange':
				$str = sprintf('[%s TO %s]', $values[0], $values[1]);
				break;
		}

		return $str;
	}

	protected function fieldMapper($field) {
		switch (strtolower($field)) {
			case 'name':
				$field = 'nombre_documento';
				break;

			case 'content':
				$field = 'contenido';
				break;

			case 'nodetype':
				$field = 'nodetypeId';
				break;

			case 'creation':
				$field = '__CREATION_FIELD__';
				break;

			case 'publication':
				$field = '__PUBLICATION_FIELD__';
				break;
		}
		return $field;
	}

}

?>