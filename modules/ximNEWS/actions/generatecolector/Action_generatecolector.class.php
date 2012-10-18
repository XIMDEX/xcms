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



ModulesManager::file('/inc/model/XimNewsColectorUsers.php', 'ximNEWS');

class Action_generatecolector extends ActionAbstract {

    function index() {

		$idNode	= $this->request->getParam("nodeid");
		$idAction = $this->request->getParam("actionid");

		$timeMsg = '';
		$newsMsg = '';
		$inactiveMsg = '';

		$ximNewsColector = new XimNewsColector($idNode);
		$inactive = $ximNewsColector->get('Inactive');

		if ($inactive == 0 || $inactive == 2) {

			$lastGeneration = $ximNewsColector->get('LastGeneration');
			$timeToGenerate = $ximNewsColector->get('TimeToGenerate');
			$leftTime = (int) ($timeToGenerate + $lastGeneration - time() )/60/60;

			$timeMsg =  $leftTime <= 0 ?  _('Immediately') :
				_('At') . strftime(' '._('%H hours %M minutes %S seconds'), $lastGeneration + $timeToGenerate);
		}

		if (($inactive == 0 && $lastGeneration > 0) || $inactive == 1) {
			$newsMsg = sprintf(_('When %d news would be associated'), $ximNewsColector->get('NewsToGenerate'));
		}

		if ($inactive == 3) {
			$inactiveMsg = _('This collector has no automatic generation');
		}

		$values = array(
			'id_node' => $idNode,
			'time_msg' => $timeMsg,
			'news_msg' => $newsMsg,
			'inactive_msg' => $inactiveMsg,
			'go_method' => 'generate_colector'
		);

		$this->render($values, 'index', 'default-3.0.tpl');
    }

	function generate_colector() {

		$idNode = $this->request->getParam('nodeid');
		$total = $this->request->getParam('total');
		$total = empty($total) ? NULL : true;
		$bulletins = array();

		$idUser = XSession::get('userID');
		$ximNewsColectorUsers = new ximNewsColectorUsers();
		$idNewsColectorUsers = $ximNewsColectorUsers->add($idNode, $idUser, 'generating');

		$nodeColector = new Node($idNode);
		$generatedBulletins = $nodeColector->class->generateColector($total);
		$message = $nodeColector->class->messages->getRaw();

		if (empty($generatedBulletins)) {

			$message .= _('Bulletins have not been generated');
		} else {

			$message .=  _('The following bulletins have been generated successfully');

			foreach ($generatedBulletins as $key => $idBulletin) {
				$node = new Node($idBulletin);

				$bulletins[] = array('id' => $idBulletin, 'name' => $node->get('Name'));
			}

			$ximNewsColector = new XimNewsColector($idNode);
			$ximNewsColector->set('LastGeneration', mktime());
			$ximNewsColector->update();

			if(!is_null($idNewsColectorUsers)) {
				$cu = new XimNewsColectorusers($idNewsColectorUsers);
				$cu->set('EndGenerationTime', mktime());
				$cu->set('Progress', 100);
				$cu->set('State', 'published');
				$cu->update();
			}
		}

		$this->addJs('/actions/generatecolector/resources/js/auxiliary.js', 'ximNEWS');

		$values = array('message' => $message,
			'num_bulletins' => sizeof($generatedBulletins),
			'bulletins' => $bulletins
			);

		$this->render($values, 'generate_colector', 'default-3.0.tpl');
	}
}
?>
