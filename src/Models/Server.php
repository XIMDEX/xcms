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

class Server extends ServersOrm
{
    private $serverNode;
    const MAX_CYCLES_TO_RETRY_PUMPING = 10;
    const SECONDS_TO_WAIT_FOR_RETRY_PUMPING = 60;
    
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
    
    public function resetForPumping()
    {
        Logger::debug('Server ' . $this->Description . ' is now able for pumping (connection successful)', true);
        $this->ActiveForPumping = 1;
        $this->DelayTimeToEnableForPumping = null;
        $this->CyclesToRetryPumping = 0;
        return $this->update();
    }
    
    public function enableForPumping() : bool
    {
        Logger::info('Enabling the server ' . $this->Description . ' for pumping');
        $this->ActiveForPumping = 1;
        $this->DelayTimeToEnableForPumping = null;
        $res = $this->update();
        if ($res === false) {
            return false;
        }
        return true;
    }
    
    public function disableForPumping(bool $delay = false) : bool
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
            Logger::warning($message);
            
            // Send emails
            User::sendNotifications('Server ' . $this->Description . ' has been disabled permanently', $message);
        } else {
            
            // Disable the server temporally
            $delayTime = pow($this->CyclesToRetryPumping, 3) * self::SECONDS_TO_WAIT_FOR_RETRY_PUMPING;
            if ($delayTime > 86400) {
                
                // Max time to retry 24 hours
                $delayTime = 86400;
            }
            $this->DelayTimeToEnableForPumping = time() + $delayTime;
            if ($this->update() === false) {
                return false;
            }
            $message = 'Server ' . $this->Description . ' (' . $this->IdServer . ') have been temporally for pumping after cycle '
                . $this->CyclesToRetryPumping . ' (Will be restarted at ' . Date::formatTime($this->get('DelayTimeToEnableForPumping')) . ')';
            Logger::warning($message);
            
            // Send emails
            User::sendNotifications('Server ' . $this->Description . ' has been disabled temporally', $message);
        }
        return true;
    }
}
