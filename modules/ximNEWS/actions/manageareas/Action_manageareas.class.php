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




ModulesManager::file('/inc/mvc/drawers/gprint.inc');
ModulesManager::file('/inc/persistence/Config.class.php');
ModulesManager::file('/inc/model/XimNewsAreas.php', 'ximNEWS');

class Action_manageareas extends ActionAbstract
{

    public function index () {
		$idAction = (int) $this->request->getParam("actionid");
		$idNode	= (int) $this->request->getParam("nodeid");

		$area = new XimNewsAreas();
		$areas = $area->GetAllAreas();
		$this->addJs('/actions/manageareas/resources/js/manageareas.js', 'ximNEWS');
		$values = array(
			'id_node' => $idNode,
			'id_action' => $idAction,
			'areas' => $areas,
			'nodeUrl' => Config::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=$idAction&nodeid=$idNode"
		);

		$this->render($values, 'index', 'default-3.0.tpl');
    }

    public function edit () {
		$idAction = (int) $this->request->getParam("actionid");
		$idNode	= (int) $this->request->getParam("nodeid");
		$idArea = (int) $this->request->getParam("area_id");

		$objArea = new XimNewsAreas($idArea);
		$areaData = array(
			'id' => $objArea->get('IdArea'),
			'name' => $objArea->get('Name'),
			'description' => $objArea->get('Description')
		);

		$values = array(
			'id_node' => $idNode,
			'id_action' => $idAction,
			'area_data' => $areaData,
			'go_method' => 'modifyArea',
			'nodeUrl' => Config::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=$idAction&nodeid=$idNode"
		);

		$this->render($values, NULL, 'only_template.tpl');
    }

    public function create() {

		$idAction = (int) $this->request->getParam("actionid");
		$idNode	= (int) $this->request->getParam("nodeid");

		$values = array(
			'id_node' => $idNode,
			'id_action' => $idAction,
			'go_method' => 'createArea',
			'nodeUrl' => Config::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=$idAction&nodeid=$idNode"
		);

		$this->render($values, NULL, 'only_template.tpl');
    }

    public function delete() {

		$idArea = (int) $this->request->getParam("area_id");

		if (ximNEWS_Adapter::deleteArea($idArea)) {
			$this->messages->add(_("The category has been deleted successfully"), MSG_TYPE_NOTICE);
		} else {
			$this->messages->add(_("Category deletion failed."), MSG_TYPE_NOTICE);
		}

		$this->render(array('messages' => $this->messages->messages, 'view_head' => 0), '', 'messages.tpl');
    }

    public function createArea() {

		$name = Request::post("area_name");
		$description = Request::post("area_description");

		if(!ximNEWS_Adapter::createArea($name, $description)) {
			$this->messages->add(_("Category creation failed. The operation was NOT performed successfully"), MSG_TYPE_NOTICE);
		} else {
			$this->messages->add(_("Category successfully created"), MSG_TYPE_NOTICE);
		}

		$this->render(array('messages' => $this->messages->messages), '', 'messages.tpl');
    }

    public function modifyArea() {

		$idArea = Request::post("area_id");
		$name = Request::post("area_name");
		$description = Request::post("area_description");

		$objAreas = new XimNewsAreas($idArea);
		$objAreas->set('Name', XmlBase::recodeSrc($name, XML::UTF8));
		$objAreas->set('Description', XmlBase::recodeSrc($description, XML::UTF8));

		if(!$objAreas->update()) {
			$this->messages->add(_("Category update failed. The operation was NOT performed successfully"), MSG_TYPE_NOTICE);
		} else {
			$this->messages->add(_("Category successfully updated"), MSG_TYPE_NOTICE);
		}

		$this->render(array('messages' => $this->messages->messages), '', 'messages.tpl');
    }
}
?>