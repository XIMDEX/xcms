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

use Ximdex\Models\ActionStatsOrm ;

/**
 * Class ActionsStats
 */
class ActionsStats extends ActionStatsOrm {

	/**
	 * ActionsStats constructor.
	 * @param null $statId
	 */
	function __construct($statId = null)  {
		parent::__construct($statId);
	}

	/**
	 * @param null $actionId
	 * @param $nodeId
	 * @param $userId
	 * @param $method
	 * @param $duration
	 * @return bool|null|string
	 */
	function Create($actionId = null, $nodeId, $userId, $method, $duration) {

		$this->set('IdAction', $actionId);
		$this->set('IdNode', $nodeId);
		$this->set('IdUser', $userId);
		$this->set('Method', $method);
		$this->set('TimeStamp', mktime());
		$this->set('Duration', $duration);

		if (parent::add()) {
			return $this->get('IdStat');
		}
				
		return NULL;
	}

	/**
	 * @param null $userId
	 * @return array|null
	 */
	function getTotals($userId = NULL) {
		$regs = array();
		if (!is_null($userId)) {
			$condition = "IdUser = %s";
			$params = array('IdUser' => $userId);
		} else {
			$condition = '1';
			$params = NULL;
		}

		$result = $this->find('IdAction, Method, COUNT(IdStat) AS Total', $condition 
			. ' GROUP BY IdAction, Method ORDER BY Total DESC', $params, MULTI);

		if (!is_null($result)) {
			foreach ($result as $resultData) {
				$regs[] = array('idaction' => $resultData['IdAction'], 'method' => $resultData['Method'], 
								'total' => $resultData['Total']);
			}

			return $regs;
		}

		return NULL;
	}
	
	/**
	 * 
	 * @param $userId
	 * @param $command
	 * @return integer
	 */
	function getCountByUserAndAction($userId = NULL, $command = NULL) {
		
		if($userId === null || $command === null)
			return 0;
		
		$condition = "IdUser = %s AND Method like %s";
		$params = array($userId, "%$command%");
		$result = $this->find('COUNT(IdStat) AS Total', $condition, $params, MULTI, true);
		
		if (!is_null($result)) {
			return $result[0]['Total'];
		}

		return 0;
	}

	/**
	 * @param null $userId
	 * @return array|null
	 */
	function getAverage($userId = NULL) {
		$regs = array();
		if (!is_null($userId)) {
			$condition = "IdUser = %s";
			$params = array('IdUser' => $userId);
		} else {
			$condition = '1';
			$params = NULL;
		}

		$result = $this->find('IdAction, Method, SUM(Duration)/COUNT(IdStat) AS Average', 
			$condition . ' GROUP BY IdAction, Method ORDER BY Average DESC', $params, MULTI);

		if (!is_null($result)) {
			foreach ($result as $resultData) {
				$regs[] = array('idaction' => $resultData['IdAction'], 'method' => $resultData['Method'], 
								'average' => $resultData['Average']);
			}

			return $regs;
		}

		return NULL;

	}
}