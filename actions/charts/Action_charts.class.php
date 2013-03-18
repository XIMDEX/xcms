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



ModulesManager::file('/inc/graphs/GraphManager.class.php');
ModulesManager::file('/inc/serializer/Serializer.class.php');


class Action_charts extends ActionAbstract {

	public function index() {

    	if ($this->request->getParam('cht') != null) {
    		$this->dynamicCharts();
    	}

		$url_root = Config::getValue('UrlRoot');
		$values = array(
			'url_root' => $url_root,
			'js_files' => array(
				Extensions::JQUERY_PATH . '/plugins/jquery.flot/jquery.js',
				Extensions::JQUERY_PATH . '/plugins/jquery.flot/jquery.flot.js',
				$url_root . '/actions/charts/js/charts.js'
			),
			'css_files' => array(
				$url_root . '/actions/charts/css/layout.css'
			)
		);
		$this->render($values, 'index', 'only_template.tpl');
	}

    private function printJSONContent($content) {
		// Returning the response through the MVC
		if (!is_array($content) && !is_object($content)) {
			$content = array('data'=>$content);
		}
		$content = Serializer::encode(SZR_JSON, $content);
		$this->response->set('Content-type', 'text/x-json');
		$this->response->sendHeaders();
		print $content;
		exit;
    }

	public function getGraphs() {
		$info = GraphManager::getGraphs();
		$this->printJSONContent($info);
	}

	public function getGraphInfo() {
		$graph = $this->request->getParam('graph');
		$info = GraphManager::getGraphInfo($graph);
		$this->printJSONContent($info);
	}

	public function getGraphSeries() {
		$graph = $this->request->getParam('graph');
		$info = GraphManager::getGraphSeries($graph);
		$this->printJSONContent($info);
	}

	protected function dynamicCharts() {
		/*
		chs=250x100				tamaño
		&chd=t:60,40			datos
		&chds=30,70				escala
		&cht=[l|b]				tipo
		&chl=Hello|World		leyenda
		*/
		// Ejemplo: ?cht=l&chl=Leyenda&chd=t:1,2,3,4,5,8,9,10,11,12|88,54,45,65,67,12,23,45,76,54

		$chs = $this->request->getParam('chs');
		$cht = $this->request->getParam('cht');
		$chd = $this->request->getParam('chd');
		$chl = $this->request->getParam('chl');
		$chds = $this->request->getParam('chds');

		// chart type
		if (!in_array($cht, array('l', 'b', 'p'))) {
			$cht = 'l';
		}

		// legend
		if ($chl != null) {
			$chl = sprintf("['%s']", implode("','", explode('|', $chl)));
		}

		// Size
		if ($chs != null) {
			$chs = explode('X', strtoupper($chs));
			if (!isset($chs[1])) $chs[1] = '200';
			$chs = sprintf("[%s, %s]", $chs[0], $chs[1]);
		} else {
			$chs = 'null';
		}

		// series
		if ($chd === null) {
			$chd = 't:';
		}
		$chd = str_replace('t:', '', $chd);

		$series = array();
		$_series = explode('|', $chd);
		$sc = count($_series);

		if ($sc > 0) {
			for ($i=0; $i<$sc; $i++) {
				$_series[$i] = explode(',', $_series[$i]);
			}
			for ($i=0; $i<count($_series[0]); $i++) {
				$aux = array();
				for ($j=0; $j<$sc; $j++) {
					$aux[] = isset($_series[$j]) && isset($_series[$j][$i])
						? (double)$_series[$j][$i]
						: 0;
				}
				$series[] = $aux;
			}
		}
		$series = Serializer::encode(SZR_JSON, $series);

		$url_root = Config::getValue('UrlRoot');
		$values = array(
			'url_root' => $url_root,
			'js_files' => array(
				Extensions::JQUERY_PATH . '/plugins/jquery.flot/jquery.js',
				Extensions::JQUERY_PATH . '/plugins/jquery.flot/jquery.flot.js',
				$url_root . '/actions/charts/js/dynamicCharts.js'
			),
			'css_files' => array(
				$url_root . '/actions/charts/css/layout.css'
			),
			'cht' => $cht,
			'chl' => $chl,
			'chs' => $chs,
			'series' => $series,
		);
		$this->render($values, 'dynamicCharts', 'only_template.tpl');
		exit;
	}

}

?>
