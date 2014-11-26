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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '../../');
}

require_once(XIMDEX_ROOT_PATH . '/inc/graphs/Graphs.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/graphs/GraphSeries.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/graphs/GraphSerieValues.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/graphs/GraphSerieProperties.class.php');

//

class GraphManager {
	/**
	 *
	 * @var unknown_type
	 */
	const DEFAULT_SERIE_SELECTOR = 'x, y, TimeStamp';
	/**
	 *
	 * @var unknown_type
	 */
	const LINES = 1;
	/**
	 *
	 * @var unknown_type
	 */
	const BARS = 2;
	/**
	 *
	 * @var unknown_type
	 */
	const POINTS = 3;

	/**
	 *
	 * @var unknown_type
	 */
	private static $GraphsInfo;
	/**
	 *
	 * @return unknown_type
	 */
	public function getGraphs() {
		$graph = new Graphs();
		return $graph->find('label', '', array(), MONO);
	}

	/**
	 *
	 * @param $graphLabel
	 * @return unknown_type
	 */
	public function getGraphSeries($graphLabel) {
		$idGraph = self::getGraphIdFromLabel($graphLabel);
		if (!$idGraph > 0) {
			return NULL;
		}

		$graphSerie = new GraphSeries();
		return $graphSerie->find('label', 'IdGraph = %s', array($idGraph), MONO);
	}

	/**
	 *
	 * @return unknown_type
	 */
	private static function _init() {
		if (!is_array(self::$GraphsInfo)) {
			self::$GraphsInfo = array();
		}
	}
	/**
	 *
	 * @param $label
	 * @param $width
	 * @param $height
	 * @param $description
	 * @param $callback
	 * @param $series
	 * @return unknown_type
	 */
	public static function createGraph($label, $width = NULL,
		$height = NULL, $description = NULL, $callback = NULL,
		$series = NULL) {

		$id = self::getGraphIdFromLabel($label);
		if ($id > 0) {
			return $id;
		}
		$graph = new Graphs();
		$graph->set('label', $label);
		$graph->set('width', $width);
		$graph->set('height', $height);
		$graph->set('description', $description);
		$graph->set('callback', $callback);
		$graph->set('series', $series);
		$insertedElement = $graph->add();

		if ($insertedElement > 0) {
			self::$GraphsInfo[$label]['Id'] = $insertedElement;
		}
		return $insertedElement;
	}

	/**
	 *
	 * @param $label
	 * @return unknown_type
	 */
	private static function getGraphIdFromLabel($label) {
		self::_init();
		if (isset(self::$GraphsInfo[$label])) {
			return self::$GraphsInfo[$label]['Id'];
		}

		$graph = new Graphs();
		$id = $graph->getIdFromLabel($label);
		if ($id > 0) {
			self::$GraphsInfo[$label]['Id'] = $id;
		}
		return $id;
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $label
	 * @return unknown_type
	 */
	private static function getSerieIdFromLabel($graphLabel, $label) {
		self::_init();
		if (isset(self::$GraphsInfo[$graphLabel]['Series'][$label])) {
			return self::$GraphsInfo[$graphLabel]['Series'][$label];
		}

		$serie = new GraphSeries();
		$id = $serie->getIdFromLabel(self::getGraphIdFromLabel($graphLabel), $label);
		if ($id > 0) {
			self::$GraphsInfo[$graphLabel]['Series'][$label] = $id;
		}
		return $id;
	}

	/**
	 * add properties with key => value
	 * @param $graphLabel
	 * @param $label
	 * @param $serieRepresentation
	 * @param $serieType
	 * @return unknown_type
	 */
	public static function createSerie($graphLabel, $label,
		$serieRepresentation = NULL, $serieType = NULL) {

		$idGraph = self::getGraphIdFromLabel($graphLabel);
		if (!($idGraph > 0)) {
			return NULL;
		}

		$id = self::getSerieIdFromLabel($graphLabel, $label);
		if ($id > 0) {
			return $id;
		}

		$graphSeries = new GraphSeries();
		$graphSeries->set('IdGraph', $idGraph);
		$graphSeries->set('Label', $label);
		$graphSeries->set('SerieRepresentation', $serieRepresentation);
		$graphSeries->set('SerieType', $serieType);
		$insertedSerie = $graphSeries->add();

		if ($insertedSerie > 0) {
			self::$GraphsInfo[$graphLabel]['Series'][$label] = $insertedSerie;
		}
		return $insertedSerie;
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $serieLabel
	 * @param $x
	 * @param $y
	 * @param $timeStamp
	 * @return unknown_type
	 */
	public static function createSerieValue($graphLabel, $serieLabel, $x = NULL, $y = NULL, $timeStamp = NULL) {
		$idSerie = self::getSerieIdFromLabel($graphLabel, $serieLabel);

		if (!($idSerie > 0)) {
			return NULL;
		}

		$graphSerieValue = new GraphSerieValues();
		$graphSerieValue->set('IdGraphSerie', $idSerie);
		$graphSerieValue->set('x', $x);
		$graphSerieValue->set('y', $y);
		if (is_null($timeStamp)) {
			$timeStamp = date('Y-m-d H:i:s', time());
		}
		$graphSerieValue->set('TimeStamp', $timeStamp);
		return $graphSerieValue->add();
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $serie
	 * @param $property
	 * @return unknown_type
	 */
	private static function getSeriePropertyFromSerie($graphLabel, $serie, $property) {
		self::_init();
		if (isset(self::$GraphsInfo[$graphLabel]['Series'][$serie]['SerieProperty'][$property])) {
			return self::$GraphsInfo[$graphLabel]['Series'][$serie]['SerieProperty'][$property];
		}

		$graphSerieProperty = new GraphSerieProperties();
		$id = $graphSerieProperty->getIdFromLabel(self::getSerieIdFromLabel($graphLabel, $serie), $property);
		if ($id > 0) {
			self::$GraphsInfo[$graphLabel]['Series'][$serie]['SerieProperty'] = $id;
		}
		return $id;

	}

	/**
	 *
	 * @param $graphLabel
	 * @param $serieLabel
	 * @param $key
	 * @param $value
	 * @return unknown_type
	 */
	public static function createSerieProperty($graphLabel, $serieLabel, $key, $value) {
		$idSerie = self::getSerieIdFromLabel($graphLabel, $serieLabel);
		if (!($idSerie > 0)) {
			return NULL;
		}

		$id = self::getSeriePropertyFromSerie($graphLabel, $serieLabel, $key);
		if ($id > 0) {
			return $id;
		}

		$graphSerieProperty = new GraphSerieProperties();
		$graphSerieProperty->set('IdGraphSerie', $idSerie);
		$graphSerieProperty->set('property', $key);
		$graphSerieProperty->set('value', $value);
		return $graphSerieProperty->add();
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $label
	 * @return unknown_type
	 */
	public static function getSerieNumValues($graphLabel, $label) {
		$idSerie = self::getSerieIdFromLabel($graphLabel, $label);
		if (!($idSerie > 0)) {
			return NULL;
		}
		$graphSerieValue = new GraphSerieValues();
		return $graphSerieValue->count('IdGraphSerie = %s', array($idSerie));
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $label
	 * @param $aggregator
	 * @return unknown_type
	 */
	public static function getSerieValues($graphLabel, $label, $aggregator = NULL) {
		$idSerie = self::getSerieIdFromLabel($graphLabel, $label);
		if (!($idSerie > 0)) {
			return NULL;
		}

		$graphSerieValue = new GraphSerieValues();
		if ($aggregator) {
			return $graphSerieValue->find($aggregator, 'IdGraphSerie = %s', array($idSerie));
		}

		$graphSerie = new GraphSeries($idSerie);
		$serieType = $graphSerie->get('SerieType');
		if ($serieType) {
			return $graphSerieValue->find($serieType, 'IdGraphSerie = %s', array($idSerie));
		}
		return $graphSerieValue->find(self::DEFAULT_SERIE_SELECTOR, 'IdGraphSerie = %s', array($idSerie));
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $label
	 * @return unknown_type
	 */
	public static function getSerieProperties($graphLabel, $label) {
		$idSerie = self::getSerieIdFromLabel($graphLabel, $label);
		if (!($idSerie > 0)) {
			return NULL;
		}

		$graphSerieProperty = new GraphSerieProperties();
		$properties = $graphSerieProperty->find('property, value', 'IdGraphSerie = %s', array($idSerie));
		$p = array();
		if (is_array($properties)) {
			foreach ($properties as $property) {
				$p[$property['property']] = $property['value'];
			}
		}
		return $p;
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $label
	 * @return unknown_type
	 */
	public static function getSerieInfo($graphLabel, $label) {
		return array('VALUES' => self::getSerieValues($graphLabel, $label),
			'PROPERTIES' => self::getSerieProperties($graphLabel, $label),
			'ATTRIBUTES' => self::getSerieAttributes($graphLabel, $label));
	}

	/**
	 *
	 * @param $label
	 * @param $overrideCallback
	 * @return unknown_type
	 */
	public static function getGraphInfo($label, $overrideCallback = false) {
		$idGraph = self::getGraphIdFromLabel($label);
		if (!($idGraph > 0)) {
			return NULL;
		}

		$graph = new Graphs($idGraph);
		$graphInfo = $graph->_serialize();

		$callback = $graph->get('callback');
		$series = $graph->get('series');
		if (!empty($callback) && !$overrideCallback) {
			$graphInfo['SERIES'][$callback] = array(
				'VALUES' => self::processSeriesInCallBack($callback, $series, $label)
			);
			/*
			$allSeries = self::getGraphSeries($label);
			$graphInfo['SERIES'][$callback]['PROPERTIES'] = array();
			$graphInfo['SERIES'][$callback]['ATTRIBUTES'] = array();
			foreach($allSeries as $serieLabel) {
				$graphInfo['SERIES'][$callback]['PROPERTIES'][$serieLabel] = self::getSerieProperties($label, $serieLabel);
				$graphInfo['SERIES'][$callback]['ATTRIBUTES'][$serieLabel] = self::getSerieAttributes($label, $serieLabel);
			}*/
			$graphInfo['SERIES'][$callback]['ATTRIBUTES'] = self::processSerieAttributesInCallBack($callback, $series, $label);
			return $graphInfo;
		}

		$allSeries = self::getGraphSeries($label);

		foreach ($allSeries as $serieLabel) {
			$graphInfo['SERIES'][$serieLabel] = self::getSerieInfo($label, $serieLabel);
		}
		return $graphInfo;
	}

	/**
	 *
	 * @param $graphLabel
	 * @param $label
	 * @return unknown_type
	 */
	public static function getSerieAttributes($graphLabel, $label) {
		$idSerie = self::getSerieIdFromLabel($graphLabel, $label);
		if (!($idSerie > 0)) {
			return NULL;
		}

		$graphSeries = new GraphSeries($idSerie);
		return $graphSeries->_serialize();
	}

	/**
	 *
	 * @param $callback
	 * @param $params
	 * @param $graphName
	 * @return unknown_type
	 */
	public static function processSeriesInCallBack($callback, $params, $graphName) {
		$factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . '/inc/graphs/callbacks', 'Graph_');
		$object = $factory->instantiate($callback);

		return $object->getSerie($params, $graphName);
	}
	public static function processSerieAttributesInCallBack($callback, $params, $graphName) {
		$factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . '/inc/graphs/callbacks', 'Graph_');
		$object = $factory->instantiate($callback);

		return $object->getSerieAttributes($params, $graphName);
	}
}
?>