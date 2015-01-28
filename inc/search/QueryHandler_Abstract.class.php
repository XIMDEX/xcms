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



/**
 *
 * Input (As PHP Array or XML String/DOM):
 *
 *	<search>
 *		<parentid>1</parentid>			<!-- Will search for nodes under "Parent" -->
 *		<depth>0</depth>				<!-- 0 = unlimited -->
 *		<items>50</items>				<!-- Items per page -->
 *		<page>1</page>					<!-- Page to return back -->
 *		<low_limit>1</low_limit>		<!-- Low limit query -->
 *		<high_limit>1</high_limit>		<!-- High limit query -->
 *		<condition>or</condition>		<!-- Comparation to use [and | or] -->
 *		<filters>
 *			<filter content="navidad" comparation="contains" field="name" />
 *			<filter content="titular" comparation="contains" field="content" />
 *		</filters>
 *		<sorts>
 *			<sort order="desc" field="Name" />
 *		</sorts>
 *	</search>
 *
 * Output (As PHP Array or XML String/DOM):
 *
 *	<results>
 *		<records>7</records>			<!-- Total records -->
 *		<items>50</items>				<!-- Items per page -->
 *		<page>1</page>					<!-- Actual page -->
 *		<pages>1</pages>				<!-- Total pages -->
 *		<low_limit>1</low_limit>		<!-- Total pages -->
 *		<high_limit>1</high_limit>		<!-- Total pages -->
 *		<data>
 *			<record>
 *				<nodeid>10000</nodeid>
 *				<parentid>1</parentid>
 *				<name>Proyectos</name>
 *				<nodetypeid>5012</nodetypeid>
 *				<nodetype>Projects</nodetype>
 *				<relpath></relpath>						<!-- Relative Path -->
 *				<isdir>1</isdir>
 *				<icon>projects.png</icon>
 *				<children>3</children>
 *				<abspath>/ximDEX/Proyectos</abspath>	<!-- Absolute Path -->
 *			</record>
 *		</data>
 *	</results>
 *
 */
abstract class QueryHandler_Abstract {

	const ITEMS_PER_PAGE = 50;

	protected $select = null;
	protected $joins = null;
	protected $where = null;
	protected $filters = null;
	protected $order = null;
	protected $limit = null;

	public function search($query, $output='ARRAY') {

		$output = strtoupper($output);

		$options = $this->getQueryOptions($query);
		$queryStr = $this->createQuery($options);
		
		$records = $this->count($query);
		// This method must return an array
		$results = $this->doSearch($queryStr, $options, $records);

		if ($output == 'XML') {
			$results = $this->recordsetToXML($results);
		} else if ($output == 'XMLDOM') {
			$results = $this->recordsetToXMLDOM($results);
		}

		return $results;
	}
	
	abstract public function count($query);
	
	abstract protected function doSearch($query, &$options, $records);

	abstract protected function createQuery(&$options);

	abstract protected function createSorts($sorts);

	abstract protected function createFilters($filters);

	abstract protected function createComparation($comp, $values=array());

	abstract protected function recordsetToArray($rset);

	protected function mktime($date) {
		$tokens = explode('/', $date);
		if (count($tokens) != 3) return '';
		return mktime(0, 0, 0, $tokens[1], $tokens[0], $tokens[2]);
	}

	/**
	 * You may need to overwrite this method like in QueryHandler_SOLR class
	 */
	protected function fieldMapper($field) {
		return $field;
	}

	/**
	 * Transforms a query into an options array.
	 * Acepts a XML string, XML DOM object or an array
	 * of options itself.
	 */
	public function getQueryOptions($query) {
		
		if (is_array($query)) {
			$query['filters'] = isset($query['filters']) ? $query['filters'] : array();
			$query['sorts'] = isset($query['sorts']) ? $query['sorts'] : array();
			return $query;
		}

		if (is_string($query)) {
			$_query = $query;
			$query = new DOMDocument('1.0', \App::getValue( 'workingEncoding'));
			$query->loadXML($_query);
		}
		return $this->parseDOMXML($query);
	}

	protected function parseDOMXML($dom) {

		$query = array();

		if (!($dom instanceof DOMDocument)) {

			$dom = new DOMDocument('1.0', \App::getValue( 'workingEncoding'));
			$dom->loadXML($dom);
		}

		$xpath = new DOMXPath($dom);
		$path = '//search/*';
		$list = $xpath->query($path);

		foreach ($list as $node) {
			$query[$node->nodeName] = $node->nodeValue;
		}

		$path = '//search/filters/filter';
		$list = $xpath->query($path);
		$query['filters'] = array();

		foreach ($list as $node) {
			$aux = array();
			foreach ($node->attributes as $attr) {
				$aux[$attr->name] = $attr->value;
			}
			$query['filters'][] = $aux;
		}

		$path = '//search/sorts/sort';
		$list = $xpath->query($path);
		$query['sorts'] = array();

		foreach ($list as $node) {
			$aux = array();
			foreach ($node->attributes as $attr) {
				$aux[$attr->name] = $attr->value;
			}
			$query['sorts'][] = $aux;
		}

		$path = '//search/joins/join';
		$list = $xpath->query($path);
		$query['joins'] = array();

		foreach ($list as $node) {
			$query['joins'][] = $node->nodeValue;
		}

		$path = '//search/select';
		$list = $xpath->query($path);
		$query['select'] = array();

		foreach ($list as $node) {
			$query['select'][] = $node->nodeValue;
		}

		return $query;
	}

	protected function recordsetToXML($rset) {

		$xmlBase = '<results><records>%s</records><items>%s</items><page>%s</page><pages>%s</pages><data>%s</data></results>';

		$xmlData = '<record><nodeid>%s</nodeid><parentid>%s</parentid><name>%s</name><nodetypeid>%s</nodetypeid>';
		$xmlData .= '<nodetype>%s</nodetype><relpath>%s</relpath><isdir>%s</isdir><icon>%s</icon><children>%s</children>';
		$xmlData .= '<abspath>%s</abspath></record>';

		$data = array();
		foreach ($rset['data'] as $record) {

			$data[] = sprintf(
				$xmlData,
				$record['nodeid'], $record['parentid'], $record['name'], $record['nodetypeid'], $record['nodetype'],
				$record['relpath'], $record['isdir'], $record['icon'], $record['children'], $record['abspath']
			);
		}

		$xml = sprintf(
			$xmlBase,
			$rset['records'], $rset['items'], $rset['page'], $rset['pages'], implode('', $data)
		);

		$xml = \Ximdex\XML\Base::recodeSrc($xml, \App::getValue( 'workingEncoding'));
//		$xml = str_replace('\\"', '"', $xml);

		return $xml;
	}

	protected function recordsetToXMLDOM($rset) {

		$xml = $this->recordsetToXML($rset);
		$dom = new DOMDocument('1.0', \App::getValue( 'workingEncoding'));
		$dom->loadXML($xml);

		return $dom;
	}
	
	public function getAttr($attr) {
		return property_exists($this, $attr) ? $this->$attr : null;
	}

}

?>