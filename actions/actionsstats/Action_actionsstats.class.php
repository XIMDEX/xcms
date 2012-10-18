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



ModulesManager::file('/inc/model/ActionsStats.class.php');
ModulesManager::file('/inc/model/action.inc');
ModulesManager::file('/inc/model/user.inc');


class Action_actionsstats extends ActionAbstract {

	public function index() {
		$userId = $this->request->getParam('userid');

		$selectedUserName = '';

		if (!is_null($userId)) {
			$user = new User($userId);
			$selectedUserName = $user->get('Name');
		}


		$stat = new ActionsStats();
		$result =  $stat->getTotals($userId);
			
		if (!is_null($result)) {

			$sumActions = array_reduce($result, create_function('$v, $w', 'return $v += $w["total"];'));

			foreach ($result as $resultData) {
				$action = new Action($resultData['idaction']);
				$name = $action->get('Name');
				$percent = number_format($resultData['total'] * 100 / $sumActions, 2, ',', '');

				$html[] = array('name' => $name, 'method' => $resultData['method'], 
					'total' => $resultData['total'], 'percent' => $percent);
			}
		}

		$values = array(
			'selectedUser' => $selectedUserName,
			'users' => $this->getUsers(),
			'title' => _('Number of times each action is exectuted'),
			'unit' => _('Times'),
			'html' => $html
		);

		$this->render($values);
	}


		
	// Reporting average time in execution action

	public function average() {
		$userId = $this->request->getParam('userid');

		$selectedUserName = '';

		if (!is_null($userId)) {
			$user = new User($userId);
			$selectedUserName = $user->get('Name');
		}

		$stat = new ActionsStats();
		$result =  $stat->getAverage($userId);

		if (!is_null($result)) {

			$sumTimes = array_reduce($result, create_function('$v, $w', 'return $v += $w["average"];'));

			foreach ($result as $resultData) {
				$action = new Action($resultData['idaction']);
				$name = $action->get('Name');
				$average = number_format($resultData['average'], 2, ',', '');
				$percent = number_format($resultData['average'] * 100 / $sumTimes, 2, ',', '');

				$html[] = array('name' => $name, 'method' => $resultData['method'], 
					'total' => $average, 'percent' => $percent);

			}
		}

		$values = array(
			'selectedUser' => $selectedUserName,
			'users' => $this->getUsers(),
			'method' => 'index',
			'title' => _('Average times of each action'),
			'unit' => _('Average (ms)'),
			'html' => $html
				);

		$this->render($values);

	}

	private function getUsers() {
		$user = new User();		
		$result = $user->find('IdUser, Name', '1 ORDER BY Name', NULL, MULTI);
		
		if (!is_null($result)) {
			foreach ($result as $resultData) {
				$users[] = array('id' => $resultData['IdUser'], 'name' => $resultData['Name']);
			}

			return $users;
		}

		return NULL;
	}
	
}

?>
