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




if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

require_once(XIMDEX_ROOT_PATH . '/inc/graphs/GraphManager.class.php');

/**
 *
 */
class Appender_profilersql extends Appender {

	/**
	 * @param object params['layout']
	 * @param string params['table']
	 */
	function __construct(&$params) {
		parent::Appender($params);
	}

    function write(&$event) {
		// Check if profiling is active
		if (!ModulesManager::isEnabled('ximPROFILER')) {
			return;
		}

    	parent::write($event);

//		error_log(print_r($this->_msg['data'], true));

		$data = $this->_msg['data'];
		$graph = $data['Label'];
		$serie1 = 'Time';
		$serie2 = 'Memory';

		GraphManager::createGraph($graph, null, null/*, $data['Description']*/);

		GraphManager::createSerie($graph, $serie1, GraphManager::LINES);
		$x = GraphManager::getSerieNumValues($serie1);
		$x = $x[0]++;
//	$x = $data['Stop'];
		GraphManager::createSerieValue($graph, $serie1, $x, $data['Time']);
		GraphManager::createSerieProperty($serie1, 'class', $data['Class']);
		GraphManager::createSerieProperty($serie1, 'function', $data['Function']);
		GraphManager::createSerieProperty($serie1, 'x_unit', 'tstamp');
		GraphManager::createSerieProperty($serie1, 'y_unit', 'ms');

		GraphManager::createSerie($graph, $serie2, GraphManager::LINES);
		$x = GraphManager::getSerieNumValues($serie2);
		$x = $x[0]++;
//	$x = $data['Stop'];
		GraphManager::createSerieValue($graph, $serie2, $x, $data['Memory']);
		GraphManager::createSerieProperty($serie2, 'class', $data['Class']);
		GraphManager::createSerieProperty($serie2, 'function', $data['Function']);
		GraphManager::createSerieProperty($serie2, 'x_unit', 'tstamp');
		GraphManager::createSerieProperty($serie2, 'y_unit', 'bytes');
		GraphManager::createSerieProperty($serie2, 'yaxis', '2');
//		GraphManager::createSerieProperty($serie2, 'xaxis_mode', 'time');

	}

}

?>