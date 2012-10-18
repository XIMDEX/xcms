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
 *  @version $Revision: 8111 $
 */




ModulesManager::file('/actions/composer/Action_composer.class.php');


 // Standard data source.

class Datasource_Composer extends AbstractDatasource implements IDatasource {

	private $fc = null;

	public function __construct($conf=array()) {
		parent::__construct($conf);
		$this->fc = new FrontController();
	}

	protected function redirect($request, $method) {

		/*$request->setParam('actionid', null);
		$request->setParam('module', null);
		$request->setParam('action', 'composer');
		$request->setParam('actionName', 'composer');
		$request->setParam('method', 'treedata');*/

		$request->setParam('action', null);
		$request->setParam('actionid', null);

		unset($_GET['action']);
		unset($_GET['actionid']);
		$_GET['method'] = $method;

		unset($_REQUEST['action']);
		unset($_REQUEST['actionid']);
		$_REQUEST['method'] = $method;

		$_GET["redirect_other_action"] = 1;


//		$this->fc->setRequest($request);
		$this->fc->dispatch();

		die();
	}

	public function read($request, $recursive = true) {

		$idNode = $request->getParam('nodeid');
		$children = $request->getParam('children');
		$from = $request->getParam('from');
		$to = $request->getParam('to');
		$items = $request->getParam('items');
		$find = $request->getParam('find');

		// Should we consider the IdNode 10000 directly?
		$idNode = $idNode == '/' ? 10000 : $idNode;

		//$ret = $this->redirect($request, 'readTreedata');

		$c = new Action_composer();
		$ret = $c->readTreedata($idNode, $children, $from, $to, $items, $find);

		$data = $this->normalizeNode($ret['node']);
		if ($recursive) {
			$data['collection'] = array();
			foreach ($ret['children'] as $child) {
				$data['collection'][] = $this->normalizeNode($child);
			}
		}
		return $data;
	}

	private function normalizeNode($node) {
/*
		$node['name'] = $node['text'];
		unset($node['text']);
		$node['idnode'] = $node['nodeid'];
		unset($node['nodeid']);
		$node['idnodetype'] = $node['nodetypeid'];
		unset($node['nodetypeid']);
		$node['idparent'] = $node['parentid'];
		unset($node['parentid']);
*/
		return $node;
	}

	public function parents($request) {
		return $this->redirect($request, 'parents');
	}

	public function nodetypes($request) {
		return $this->redirect($request, 'nodetypes');
	}

	public function search($request) {

		$handler = strtoupper($request->getParam('handler'));
		$output = strtoupper($request->getParam('output'));
		$query = $request->getParam('query');

		$handler = $handler !== null ? $handler : 'SQL';	// SQL / Solr / XVFS ?
		$output = $output !== null ? $output : 'JSON';		// JSON / XML

		if (is_string($query)) {
			$query = XmlBase::recodeSrc($query, Config::getValue('workingEncoding'));
			$query = str_replace('\\"', '"', $query);
		}

		$qh = QueryProcessor::getInstance($handler);
		if (is_object($qh)) {
			$format = $output == 'XML' ? 'XML' : 'ARRAY';
			$results = $qh->search($query, $format);
		} else {
			$results = array('error'=>1, 'msg'=>_('The query handler was not found.'));
		}

		return $results;
	}

	public function write($request) {
		return null;
	}

}

?>
