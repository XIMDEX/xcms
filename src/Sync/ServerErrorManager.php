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

namespace Ximdex\Sync;

use Ximdex\Models\Server;
use Ximdex\Models\ServerFrame;
use Ximdex\Models\ServerErrorByPumper;
use Ximdex\NodeTypes\ServerNode;
use Ximdex\Logger;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

/**
* @brief Manages the errors found during the sending of ServerFrames to Server.
*
* The activity of Pumpers is checked Periodically and if any error is detected the Pumper it's stopped and its ServerFrames 
* changed to Status WithError.   After an inactivity period, if the error doesn't exists the pumper restart its activity.
*/
class ServerErrorManager
{
	/**
	* Gets the publication servers that are availables for upload documents
	* 
	* @return array
	*/
   public static function getServersForPumping()
   {
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = 'SELECT ErrorId, UnactivityCycles FROM ServerErrorByPumper WHERE WithError = 1';
		$dbObj->Query($sql);
		if($dbObj->numRows > 0){
			while(!$dbObj->EOF) {
				$errorId = $dbObj->GetValue('ErrorId');
				$cycles = $dbObj->GetValue('UnactivityCycles');
				$error = new ServerErrorByPumper($errorId);

				// Enable for pumping servers with num. cycles > unactivityCycles
				if ($cycles > UNACTIVITY_CYCLES) {
					$idServer = $error->get('ServerId');
					$idPumper = $error->get('PumperId');
					$error->set('WithError', 0);
					$error->set('UnactivityCycles', 0);
					$error->update();
					self::enableServer($idServer, $idPumper);
				} else {
					$error->sumUnactivityCycles();
				}
				$dbObj->Next();
			}
		}

		// Getting servers
		$enabledServers = ServerNode::getServersForPumping();
		return $enabledServers;
    }

	/**
	* Disables a Server in which have detected an error
	* 
	* @param $pumperId
	*/
    public function disableServerByPumper($pumperId)
    {
		$serverError = new ServerErrorByPumper();
		$serverError->loadByPumper($pumperId);
		$idServer = $serverError->get('ServerId');
		$serverError->set('WithError', 1);
		$serverError->update();
		Logger::info('Disabling server ' . $idServer);
		$serverNode = new Server($idServer);
		$serverNode->set('ActiveForPumping', 0);
		$serverNode->update();
    }

	/**
	* Enables a Server in which a previous error is fixed
	* 
	* @param $idServer
	* @param $idPumper
	*/
	static public function enableServer($idServer, $idPumper)
	{
		Logger::info('Enabling server ' . $idServer);
		$serverNode = new Server($idServer);
		$serverNode->set('ActiveForPumping', 1);
		$serverNode->update();

		// Set serverframes to Due2In/Out for retry pumping
		Logger::info('Setting ServerFrames to Due2In/Out to retry pumping');
		$serverFrame = new ServerFrame();
		$serverFrame->rescueErroneous($idPumper);
	}
}
