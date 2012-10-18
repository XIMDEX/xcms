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





ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsNews.inc', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColector.php', 'ximNEWS');


class Action_addtocolector extends ActionAbstract {

    function index() {

		$idNode	= (int) $this->request->getParam("nodeid");
		$actionID = (int) $this->request->getParam("actionid");
		$edit = $this->request->getParam('edit');

		$disabledInputs = empty($edit) ? '' : "disabled='disabled'";

		$params = $this->request->getParam("params");
		$query["confirm"] = _('Do you want to continue?');

		$colectors = $this->colectors_table($idNode);
		$newsVersion = $this->get_version($idNode);

		$ximNewsNews = new XimNewsNew($idNode);
		$news_name =  $ximNewsNews->get('Name');

		$this->addJs('/actions/addtocolector/resources/js/validations.js', 'ximNEWS');
		$this->addJs('/actions/addtocolector/resources/js/calendar.js', 'ximNEWS');
		$this->addCss('/actions/workflow_forward/resources/css/style.css');

		$values = array(
			'query' => $query,
			'params' => $params,
			'button' => _('Asociar'),
			'colectorscount' => sizeof($colectors),
			'id_node' => $idNode,
			'colectors' => $colectors,
			'versions' => $newsVersion,
			'attempts' => $ximNewsNews->get('AssociationAttempts'),
			'news_name' =>	$news_name,
			'go_method' => 'add_to_colector',
			'asoc_disabled' => $disabledInputs,
			'time_stamp' => mktime(),
			'nodeUrl' => Config::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=$actionID&nodeid=$idNode"
		);

		$this->render($values, 'index', 'default-3.0.tpl');
    }

	function date_system() {

		echo time();
	}

	function add_to_colector() {

		$actionID	= (int) $this->request->getParam("actionid");
		$nodeId = $this->request->getParam('nodeid');
		$colectors = $this->request->getParam('colectorsidlst');
		$versions = $this->request->getParam('versiones');
		$update = $this->request->getParam("fechainicio");
		$downdate = $this->request->getParam("fechafin");
		$params = $this->request->getParam("params");

		$newsNode = new Node($nodeId);
		$isOTF = $newsNode->getSimpleBooleanProperty('otf');

		foreach($colectors as $colectorId){
			$alreadyHybrid=false;

			//check if it's a hybrid colector
			$n = new Node($colectorId);
			$isHybrid = $n->getSimpleBooleanProperty('hybridColector');

			if($isHybrid && !$alreadyHybrid){
				//set the hybridColector to this news only once
				$n = new Node($nodeId);
				$n->setProperty('hybridColector',"true");
				$alreadyHybrid=true;
			}

			XMD_Log::info(_('Adding news $nodeId to colector $colectorId'));

			if (!$newsNode->class->addToColector($colectorId, $update, $downdate, $versions)) {
				$this->messages->add(_("The association has NOT been performed successfully"), MSG_TYPE_ERROR);
				$this->messages->mergeMessages($newsNode->class->messages);
			} else {

				$this->messages->add(_("The association has been performed successfully"), MSG_TYPE_NOTICE);
			}
		}

		if ($isOTF){
			$syncMngr = new SyncManager();
			$syncMngr->setFlag('type', 'ximNEWSColector');
			$syncMngr->setFlag('colector', $colectorId);
			$syncMngr->setFlag('otfPublication',true);
			$syncMngr->pushDocInPublishingPool($nodeId, time(), NULL);
		}

		if (empty($this->messages->messages)) {
			$this->messages->add(_('No association was performed.'), MSG_TYPE_WARNING);
		}

		$values = array('messages' => $this->messages->messages);

		$this->render($values, NULL, 'messages.tpl');
	}

/**
 * Gets versions from news
 *
 * @param int $nodeID
 * @return array / NULL
 */

	private function get_version($nodeID) {

		$dataFactory = new datafactory($nodeID);

		$lastVersion = $dataFactory->getLastVersion();
		$lastSubVersion = $dataFactory->getLastSubVersion($lastVersion);

		$idPublishedVersion = $dataFactory->GetPublishedIdVersion();

		if (!is_null($idPublishedVersion)) {
			list($publishedVersion, $publishedSubversion) = $dataFactory->GetVersionAndSubVersion($idPublishedVersion);
		}


		$versions = array('lastversion' => $lastVersion, 'lastsubversion' => $lastSubVersion,
			'publishedversion' => $publishedVersion, 'publishedsubversion' => $publishedSubversion);

		return $versions;
	}

	/**
	 * Gets info from colectors
	 *
	 * @param int $nodeID
	 * @return array / NULL
	 */

	private function colectors_table($nodeID) {

		$userID = XSession::get('userID');
		$user = new User();
		$user->SetID($userID);
		$groups = $user->GetGroupList();

		$node = new Node($nodeID);
    	$sectionID = $node->GetSection();

		$ximNewsColector = new XimNewsColector();
		$colectors = $ximNewsColector->getColectors($sectionID, $groups);

		if (is_null($colectors)) {
			return NULL;
		}

		$ximNewsNews = new XimNewsNew($nodeID);
		$attempts = $ximNewsNews->get('AssociationAttempts');

		foreach($colectors as $colectorID => $name) {

			$ximNewsColector = new XimNewsColector($colectorID);
			$colectorArea = $ximNewsColector->get('IdArea');
			$global = $ximNewsColector->get('Global');

			$areaName = '';
			$compatible = '';

			if ($colectorArea > 0) {
				$ximNewsArea = new XimNewsAreas($colectorArea);
				$areaName = $ximNewsArea->get('Name');
			}

			// If association exists checks the box

			$relNewsColector = new RelNewsColector();
			$idRel = $relNewsColector->hasNews($colectorID, $nodeID);

			if ($idRel > 0) {
				$relNewsColector = new RelNewsColector($idRel);
				$version = $relNewsColector->get('Version');
				$subversion = $relNewsColector->get('SubVersion');

				$state = $relNewsColector->get('State');
				$dateDown = $relNewsColector->get('FechaOut');

				if ($state == 'removed' || ($state == 'InBulletin' && !is_null($dateDown))) {
					$checked = '';
				} else {
					$checked = "checked='true'";
				}

			} else {

				$checked = "";

				// Suggests association if news and colector are compatible areas
				// and is the first association attempt

				if ($attempts == 0) {

					if ($colectorArea > 0) {
						$compatible = RelNewsArea::hasAreas($colectorArea, $nodeID);
					}

					if (!empty($compatible) && $attempts == 0) {
						$checked = 'checked';
					}

				}

			}

			$list[] = array('id' => $colectorID, 'name' => $name, 'global' => $global, 'area' => $areaName,
				'compatible' => $compatible, 'rel' => $idRel, 'checked' => $checked);

		}

		return $list;
	}
}
?>
