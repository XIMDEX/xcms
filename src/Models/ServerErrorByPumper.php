<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\ServerErrorByPumperOrm;

/**
* @brief Manages the errors found during the sending of ServerFrames to Server.
*
* This class includes the methods that interact with the Database.
*/
class ServerErrorByPumper extends ServerErrorByPumperOrm
{
	/**
	* Gets the field ErrorId from ServerErrorByPumper table which matching the value of pumperId
	* 
	* @param int pumperId
	*/
    public function loadByPumper($pumperId = null)
    {
		if ($pumperId) {
			$dbObj = new \Ximdex\Runtime\Db();
			$sql = "SELECT ErrorId FROM ServerErrorByPumper WHERE PumperId = $pumperId";
			$dbObj->Query($sql);
			if ($dbObj->numRows == 0){
				Logger::info(sprintf("Pumper %s does not exist", $pumperId));
				die();
			}
			$errorId = $dbObj->GetValue(ErrorId);
		}
		parent::__construct($errorId);
    }

	/**
	* Adds a row to ServerErrorByPumper table
	* 
	* @param int pumperId
	* @param int serverId
	* @return int|null
	*/
    public function create($pumperId,$serverId)
    {
		$this->set('PumperId',$pumperId);
		$this->set('ServerId',$serverId);
		$this->set('WithError',0);
		$this->set('UnactivityCycles',0);
		parent::add();
		$errorId = $this->get('ErrorId');
		if ($errorId > 0) {
			return $errorId;
		}
		Logger::info("Creating serverError");
		return NULL;
    }

	/**
	* Increases the inactivity cycles and updates ServerErrorByPumper table
	*/
    public function sumUnactivityCycles()
    {
		$counter = $this->get('UnactivityCycles');
		$idServer = $this->get('ServerId');
		Logger::info(sprintf("Server %d has %d cycles inactive", $idServer, $counter));
		$this->set('UnactivityCycles', $counter + 1);
		$this->update();
    }
}
