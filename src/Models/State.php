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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\StatesOrm;


class State extends StatesOrm
{

	function loadByName($name)
	{

		$result = $this->find('IdState', 'Name = %s', array($name), MONO);
		if (count($result) < 1) {
			Logger::error('No states found with the given name');
			return NULL;
		}
		if (count($result) > 1) {
			Logger::error('Inconsistency: Several states found with the given name');
			return NULL;
		}

		$state = new State($result[0]);

		return $state->get('IdState');
	}

	function getPreviousState()
	{
		$result = $this->find('IdState', 'NextState = %s', array($this->get('IdState')), MONO);
		if (count($result) < 1) {
			Logger::error('Inconsistency: No previous state found');
			return NULL;
		}
		if (count($result) > 1) {
			Logger::error('Inconsistency: Several previous states found');
			return NULL;
		}

		if (!($result[0] > 0)) {
			Logger::error('Inconsistency: The estimated previous state is not valid');
			return NULL;
		}
		return $result[0];
	}

	function loadFirstState()
	{
		$result = $this->find('IdState', 'IsRoot = 1', NULL, MONO);
		if (count($result) < 1) {
			Logger::error('Init state was not found');
			return NULL;
		}
		if (count($result) > 1) {
			Logger::error('Inconsistency: Several init states found');
			return NULL;
		}

		$this->State($result[0]);
		return $this->get('IdState');
	}

	function loadLastState()
	{
		$result = $this->find('IdState', 'IsEnd = 1', NULL, MONO);
		if (count($result) < 1) {
			Logger::error('Final state not found');
			return NULL;
		}
		if (count($result) > 1) {
			Logger::error('Inconsistency: Several final states found');
			return NULL;
		}

		$this->State($result[0]);
		return $this->get('IdState');
	}

	function getSortedStatus()
	{
		$idState = $this->loadFirstState();
		$allStatus[] = $idState;
		$status = new State($idState);
		while (!$status->get('IsEnd')) {
			$idState = $status->get('NextState');
			$status = new State($idState);
			if (!$status->get('IdState') > 0) {
				Logger::error('Inconsistency: Referencing an unexistent state');
				return NULL;
			}
			$allStatus[] = $idState;
		}
		return $allStatus;
	}

	function add()
	{
		$nextState = new State($this->get('NextState'));
		$idPreviousState = $nextState->getPreviousState();

		$result = parent::add();
		if (!($result > 0)) {
			Logger::warning('Workflow state could not be inserted');
			return false;
		}

		$previousState = new State($idPreviousState);
		$previousState->set('NextState', $result);
		$previousState->update();

		return $this->get('IdState');
	}

}