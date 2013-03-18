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




ModulesManager::file('/inc/model/List.class.php');
ModulesManager::file('/inc/patterns/Factory.class.php');

class Action_manageList extends ActionAbstract {

	
	public function index() {
		$params = $this->request->getParam('params');
		$mode = $this->request->getParam('mode');
		$type = isset($params['type']) ? $params['type'] : NULL;
		
		$list = $this->getObjectInstance($type);
		$all = $list->find('id, Name, Description');
		
		$this->addJs(Extensions::JQUERY_PATH.'/plugins/jquery.blockUI.js');
		$this->addJs('/actions/manageList/resources/js/common.js');
		$this->addCss('/actions/manageList/resources/css/common.css');
		$values = array('list' => $all, 'type' => $type);
		if ($mode == 'single') {
			$this->render($values, 'index.tpl', 'single.tpl');
		} else {
			$this->render($values);
		}
	}
	
	/**
	 * function ready to be called and return json result
	 */
	public function add() {
		
		$name = $this->request->getParam('name');
		$description = $this->request->getParam('description');
		$type = $this->request->getParam('type');
		
		$element = $this->getObjectInstance($type);
		$element->set('IdList', 0);
		$element->set('Name', $name);
		$element->set('Description', $description);
		$result = $element->add();
		
		$this->render(array('result' => $result));
	}
	
	/**
	 * function ready to be called and return json result
	 */
	public function update() {
		$id = $this->request->getParam('id');
		$name = $this->request->getParam('name');
		$description = $this->request->getParam('description');
		$type = $this->request->getParam('type');
		
		$element = $this->getObjectInstance($type, $id);
		if (!($element->get('id') > 0)) {
			$this->render(array('result' => -1));
		}
		
		$element->set('Name', $name);
		$element->set('Description', $description);
		$result = $element->update();
		$this->render(array('result' => $result));
	}
	
	/**
	 * function ready to be called and return json result
	 */
	public function remove() {
		$id = $this->request->getParam('id');
		$type = $this->request->getParam('type');
		
		$element = $this->getObjectInstance($type, $id);
		if (!($element->get('id') > 0)) {
			$this->render(array('result' => -1));
		}
		
		$result = $element->delete();
		$this->render(array('result' => $result));
	}
	
	public function loadElement() {
		$id = $this->request->getParam('id');
		$name = $this->request->getParam('name');
		$description = $this->request->getParam('description');
		
		$listInfo = array('id' => $id, 'Name' => $name, 'Description' => $description);
		
		$this->render(array(
			'listInfo' => $listInfo
		), '_element', 'only_template.tpl');
	}
	private function getObjectInstance($type, $arg = NULL) {
		$rootName = 'List_';
		$factory = new Factory(XIMDEX_ROOT_PATH . '/inc/model/', $rootName);
		return $factory->instantiate($type, $arg);
	}
}
?>
