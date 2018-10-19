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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\ServersOrm;
use Ximdex\NodeTypes\ServerNode;
use Ximdex\Utils\Date;
use Ximdex\Runtime\Db;

class Server extends ServersOrm
{
    const MAX_CYCLES_TO_RETRY_PUMPING = 0;
    const SECONDS_TO_WAIT_FOR_RETRY_PUMPING = 60;
    
    private $serverNode;
    
    public function __construct(int $id = null)
    {
        parent::__construct($id);
        if ($id) {
            $this->setServerNode($id);
        }
    }
    
    public function setServerNode(int $id)
    {
        $this->serverNode = new ServerNode($id);
    }
    
    public function getChannels() : array
    {
        if (!$this->serverNode) {
            $this->setServerNode($this->get('IdServer'));
        }
        return $this->serverNode->GetChannels($this->get('IdServer'));
    }
    
    public function resetForPumping() : bool
    {
        if (!$this->CyclesToRetryPumping) {
            return true;
        }
        $this->ActiveForPumping = 1;
        $this->DelayTimeToEnableForPumping = null;
        $this->CyclesToRetryPumping = 0;
        if ($this->update() === false) {
            return false;
        }
        $message = 'Server ' . $this->Description . ' (' . $this->IdServer . ') is now able for pumping (connection successful)';
        Logger::info($message, true);
        User::sendNotifications('Server ' . $this->Description . ' has been activated', $message);
        return true;
    }
    
    public function enableForPumping() : bool
    {
        Logger::info('Enabling the server ' . $this->Description . ' for pumping');
        $this->ActiveForPumping = 1;
        $this->DelayTimeToEnableForPumping = null;
        if ($this->update() === false) {
            return false;
        }
        return true;
    }
    
    public function disableForPumping(bool $delay = false, int $delayTime = null) : bool
    {
        if (! $this->ActiveForPumping) {
            return true;
        }
        $this->ActiveForPumping = 0;
        $this->CyclesToRetryPumping = $this->CyclesToRetryPumping + 1;
        if (!$delay or (self::MAX_CYCLES_TO_RETRY_PUMPING and $this->CyclesToRetryPumping > self::MAX_CYCLES_TO_RETRY_PUMPING)) {
                
            // Disable the server permanently
            $this->DelayTimeToEnableForPumping = null;
            if ($this->update() === false) {
                return false;
            }
            $message = 'Disabling the server ' . $this->Description . ' (' . $this->IdServer . ') for pumping permanently';
            Logger::error($message);
            User::sendNotifications('Server ' . $this->Description . ' has been disabled permanently', $message);
        } else {
            
            // Disable the server temporally
            if (!$delayTime) {
                $delayTime = pow($this->CyclesToRetryPumping, 3) * self::SECONDS_TO_WAIT_FOR_RETRY_PUMPING;
            }
            if ($delayTime > 86400) {
                
                // Max time to retry 24 hours
                $delayTime = 86400;
            }
            $this->DelayTimeToEnableForPumping = time() + $delayTime;
            if ($this->update() === false) {
                return false;
            }
            $message = 'Server ' . $this->Description . ' (' . $this->IdServer . ') have been delayed temporally for pumping after cycle '
                . $this->CyclesToRetryPumping . ' (Will be restarted at ' . Date::formatTime($this->get('DelayTimeToEnableForPumping')) . ')';
            Logger::warning($message);
            User::sendNotifications('Server ' . $this->Description . ' has been disabled temporally', $message);
        }
        return true;
    }
    
    public function stats(int $portalId = null) : array
    {
        if (!$this->IdServer) {
            throw new \Exception('No server selected');
        }
        $sql = 'SELECT SUM(ServerFramesTotal) AS total, SUM(ServerFramesPending) AS pending, SUM(ServerFramesActive) AS active';
        $sql .= ', SUM(ServerFramesSuccess) AS success, SUM(ServerFramesFatalError) AS fatal, SUM(ServerFramesTemporalError) AS soft';
        $sql .= ' FROM Batchs WHERE ServerId = ' . $this->IdServer;
        if ($portalId) {
            $sql .= ' AND IdPortalFrame = ' . $portalId;
        }
        $dbObj = new Db();
        if ($dbObj->Query($sql) === false) {
            throw new \Exception('SQL error');
        }
        $stats = [];
        if (!$dbObj->numRows) {
            throw new \Exception('There is not stats information for the server');
        }
        $stats['total'] = (int) $dbObj->GetValue('total');
        $stats['pending'] = (int) $dbObj->GetValue('pending');
        $stats['active'] = (int) $dbObj->GetValue('active');
        $stats['success'] = (int) $dbObj->GetValue('success');
        $stats['fatal'] = (int) $dbObj->GetValue('fatal');
        $stats['soft'] = (int) $dbObj->GetValue('soft');
        return $stats;
    }
}
