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



ModulesManager::file('/inc/serializer/Serializer.class.php');
ModulesManager::file('/inc/parsers/ParsingJsGetText.class.php');

class Action_searchpanel extends ActionAbstract {

	public function index() {
	}

	/**
	 * Simple search results interface
	 */
	public function showResults() {

		$this->addJs('/actions/searchpanel/resources/js/results.js');
		$this->addCSS('/actions/searchpanel/resources/css/searchpanel.css');
		$fields = array(
			array(
				'key' => 'nodetype',
				'type' => 'action-icon'
			),
			array(
				'key' => 'nodeid',
				'label' => _('Id'),
				'type' => 'text'
			),
			array(
				'key' => 'name',
				'label' => _('Name'),
				'type' => 'text'
			),
			array(
				'key' => 'nodetype',
				'label' => _('Nodetype'),
				'type' => 'text'
			),
			array(
				'key' => 'abspath',
				'label' => _('Path'),
				'type' => 'path'
			),
			array(
				'key' => 'creation',
				'label' => _('Created'),
				'type' => 'date'
			),
			array(
				'key' => 'modification',
				'label' => _('Modified'),
				'type' => 'date'
			),
			array(
				'key' => 'versionnumber',
				'label' => _('Version'),
					'type' => 'text'
			)
		);

		$this->render(array('fields' => json_encode($fields)), 'results', 'default-3.0.tpl');
	}

	/**
	 * Used from advanced search panel
	 */
	public function template() {
		$tpl = '<searchpanel />';
		$ret = Widget::process($tpl, array());
		$tpl = preg_replace('/<div style="display: none;" class="widget_includes">(.*?)<\/div>/s', '', $ret['tpl']);
		//error_log(print_r($ret['tpl'], true));
		printf($tpl);
	}

	/**
	 * Used from advanced search panel
	 */
	public function filters() {

		$filters = $this->request->getParam('filters');
		if (empty($filters)) {
			$filters = 'Ximdex';
		}

		$filters = ucfirst(strtolower($filters));

		$factory = new Factory(dirname(__FILE__) . '/inc', 'Filters_');
		$filter = $factory->instantiate('Ximdex');

		$data = array();

		if (is_object($filter)) {
			$data = $filter->getFilters();
		}

		$this->sendJSON($data);
	}

	/**
	 * Used from advanced search panel
	 */
	public function datastores() {

		$datastore = $this->request->getParam('datastore');

		if ($datastore === null) $datastore = 'ximdex';

		$dsPath = sprintf('%s/actions/searchpanel/resources/js/searchpanel.%s.conf.js', XIMDEX_ROOT_PATH, $datastore);
		$content = FsUtils::file_get_contents($dsPath);

		$content = ParsingJsGetText::parseContent($content);

		header('Content-type: text/javascript');
		printf($content);
		die();
	}

}

?>
