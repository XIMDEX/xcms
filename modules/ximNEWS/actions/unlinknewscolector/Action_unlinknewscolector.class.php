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



class Action_unlinknewscolector extends ActionAbstract {

    function index() {

		$idNode	= $this->request->getParam("nodeid");
		$idAction = $this->request->getParam("actionid");

		$node = new Node($idNode);

		if ($node->nodeType->get('Name') == 'XimNewsColector') {

			$relNewsColector = new RelNewsColector();
			$list = $relNewsColector->getNews($idNode);
		} else {

			$relNewsColector = new RelNewsColector();
			$list = $relNewsColector->getColectorsFromNew($idNode);
		}

		$nodesList = array();

		if (sizeof($list) > 0) {

			foreach ($list as $id) {
				$elemNode = new Node($id);
				$nodesList[] = array('id' => $id, 'name' => $elemNode->get('Name'));
			}
		}

		$this->addJs('/actions/unlinknewscolector/resources/js/validations.js', 'ximNEWS');
		$this->addJs('/actions/unlinknewscolector/resources/js/calendar.js', 'ximNEWS');
		$this->addCss('/xmd/style/jquery/ximdex_theme/widgets/calendar/calendar.css');

		$timestamp_from = time();
		$timestamp_to = $timestamp_from + 60;

		$values = array(
			'nodetype' => $node->nodeType->get('Name'),
			'timestamp_from' => $timestamp_from,
			'timestamp_to' => $timestamp_to,
			'nodeslist' => $nodesList,
			'nodescount' => sizeof($nodesList),
			'idnode' => $idNode,
			'nodename' => $node->get('Name'),
			'go_method' => 'unlink',
			'nodeUrl' => Config::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=$idAction&nodeid=$idNode"
		);
		$this->render($values, 'index', 'default-3.0.tpl');
    }

	function unlink() {

		$idAction = $this->request->getParam("actionid");
		$idNode = $this->request->getParam('nodeid');
		$nodesList = $this->request->getParam('nodeslist');
		$downDate = $this->request->getParam('enddate');

		$node = new Node($idNode);

		if(!empty($nodesList) ) {
			foreach ($nodesList as $id) {

				if ($node->nodeType->get('Name') == 'XimNewsColector') {
					$idColector = $idNode;
					$idNews = $id;
				} else {
					$idColector = $id;
					$idNews = $idNode;
				}

				$idRel = RelNewsColector::hasNews($idColector, $idNews);

				if (!($idRel > 0)) {

					$this->messages->add(_('The dissociation '.$node->get('Name').' - '. $elemNode->get('Name').' was NOT successfully performed. Updating error.'), MSG_TYPE_NOTICE);
					continue;
				}

				$elemNode = new Node($id);
				$relNewsColector = new RelNewsColector($idRel);
				$relNewsColector->set('FechaOut', $downDate[$id]);

				if (!$relNewsColector->update()) {
					$this->messages->add(_('The dissociation '.$node->get('Name').' - '. $elemNode->get('Name').' was NOT successfully performed. Updating error.'), MSG_TYPE_NOTICE);

					XMD_Log::error("Updating relNewsColector $idRel");
				} else {
					$this->messages->add(_('The dissociation '.$node->get('Name').' - '. $elemNode->get('Name').' was successfully performed.'), MSG_TYPE_NOTICE);

					$idUser = XSession::get('userID');
					$rel = new RelNewsColectorUsers();
					$rel->add($idRel, $idUser);
				}
			}
		}else {
			$this->messages->add(_('Dissociations were not found'), MSG_TYPE_NOTICE);
		}

		$values = array(
			'messages' => $this->messages->messages,
		);

		$this->render($values, NULL, 'messages.tpl');
	}

	public function date_system() {
		echo time();
	}
}
