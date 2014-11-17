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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../../');
}
require_once(XIMDEX_ROOT_PATH . '/inc/db/db.php');
/**
 *
 */
class Getter_profilersql extends Getter {

	function __construct($layout, $params) {
		parent::Getter($layout, $params);
	}

	function read($conditions='', $order='', $limit='') {

		if (!ModulesManager::isEnabled('ximPROFILER')) array();

		$data = GraphManager::getGraphInfo($conditions);
		return $data;

		if (strlen($conditions) > 0) {
			$conditions = 'where ' . $conditions;
		}
		if (strlen($order) > 0) {
			$order = 'order by ' . $order;
		}
		if (strlen($limit) > 0) {
			$limit = 'limit ' . $limit;
		}


		$dbObj = new DB();
//		$query = sprintf("select * from ProfilerStats s left join ProfilerTests t using(idtest) %s %s %s", $conditions, $order, $limit);
		$query = sprintf("select * from %s %s %s %s", $this->_params['table'], $conditions, $order, $limit);
//		debug::log($query);
		$dbObj->Query($query);

		$data = array();
		while(!$dbObj->EOF) {
			$data[] = $dbObj->row;
			$dbObj->Next();
		}

		unset($dbObj);
		return $data;
	}

	function getAvarage($tests=null, $label=null) {

		if (!ModulesManager::isEnabled('ximPROFILER')) array();

		// $tests es un conjunto de ids separados por comas o intervalos separados por guiones
		// Si el valor es * se consideran todos los tests.
		// Ej: '1-3,5,9,20-24'

		if ($label === null) return array();

		$where = 'where ';
		if ($tests !== null && $tests != '*') {
			$testsIds = '';
			$ids = explode(',', $tests);
			foreach ($ids as $id) {
				$id = trim($id);
				$interval = explode('-', $id);
				if (count($interval) > 1) {
					for ($i=$interval[0]; $i<=$interval[1]; $i++) {
						$testsIds .= ','.$i;
					}
				} else {
					$testsIds .= ','.$id;
				}
			}
			$where .= sprintf('IdTest in (%s) and ', substr($testsIds, 1));
		}
		$where .= "label = '$label'";

		$query = "select IdTest, Label, count(1) as Calls, " .
			"avg(time) as TimeAvarage, avg(memory) as MemoryAvarage  " .
			"from %s %s ".
			"group by idtest, label order by label, calls";
		$dbObj = new DB();
		$query = sprintf($query, $this->_params['table'], $where);
//		debug::log($query);
		$dbObj->Query($query);

		$data = array();
		while(!$dbObj->EOF) {
			$data[] = $dbObj->row;
			$dbObj->Next();
		}

		unset($dbObj);
		return $data;
	}

}

?>