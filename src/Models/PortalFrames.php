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
use Ximdex\Runtime\Db;
use Ximdex\Models\ORM\PortalFramesOrm;

class PortalFrames extends PortalFramesOrm
{
    const TYPE_UP = 'Up';
    const TYPE_DOWN = 'Down';
    const STATUS_CREATED = 'Created';
    const STATUS_ACTIVE = 'Active';
    const STATUS_ENDED = 'Ended';
    
    public function upPortalFrameVersion(int $nodeId, int $scheduledTime, int $idUser = null, string $type = self::TYPE_UP) : int
    {
        if (!$nodeId or !$scheduledTime) {
            Logger::error('Cannot generate a portal version without a node or scheduled time');
            return false;
        }
        $portalFrameVersion = $this->getLastVersion($nodeId);
        $portalFrameVersion++;
        $this->set('IdNodeGenerator', $nodeId);
        $this->set('ScheduledTime', $scheduledTime);
        $this->set('Version', $portalFrameVersion);
        $this->set('CreationTime', time());
        $this->set('PublishingType', $type);
        $this->set('CreatedBy', $idUser);
        $this->set('Status', self::STATUS_CREATED);
        $idPortalFrameVersion = parent::add();
        return ($idPortalFrameVersion > 0) ? (int) $idPortalFrameVersion : 0;
    }

    public function getLastVersion(int $nodeId) : int
    {
        $result = parent::find('MAX(Version)', 'IdNodeGenerator = %s', array('IdNodeGenerator' => $nodeId), MONO);
        return (int) $result[0];
    }

    public function getId(int $nodeId, int $version) : int
    {
        $result = parent::find('id', 'IdNodeGenerator = %s AND Version = %s',
            array('IdNodeGenerator' => $nodeId, 'Version' => $version), MONO);
        return (int) $result[0];
    }

    public function getAllVersions(int $nodeId) : array
    {
        $result = parent::find('id, Version', 'IdNodeGenerator = %s', array('IdNodeGenerator' => $nodeId), MULTI);
        $portalFrameVersions = [];
        foreach ($result as $resultData) {
            $portalFrameVersions[] = array('id' => $resultData['id'], 'version' => $resultData['Version']);
        }
        return $portalFrameVersions;
    }
    
    /**
     * Calculate the stats from batchs related to a given portal frame, or everything active
     * 
     * @param Batch $batch
     * @param Server $server
     * @param int $id
     * @throws \Exception
     */
    public static function updatePortalFrames(Batch $batch = null, Server $server = null, int $id = null) : void
    {
        if ($batch) {
            if (! $batch->get('IdBatch')) {
                throw new \Exception('Batch is not loaded to update portal frames');
            }
            if (! $batch->get('State')) {
                throw new \Exception('Unknown state for batch with ID: ' . $batch->get('IdBatch'));
            }
            if (! $batch->get('IdPortalFrame')) {
                throw new \Exception('There is not a portal frame for batch with ID: ' . $batch->get('IdBatch'));
            }
        } elseif ($server) {
            if (! $server->get('IdServer')) {
                throw new \Exception('Server is not loaded to update portal frames');
            }
        }
        
        // Get affected portal frames by function criteria
        $sql = 'SELECT DISTINCT pf.id FROM PortalFrames pf ';
        if ($server) {
            
            // Update only the portal frames running with a given server
            $sql .= 'INNER JOIN Batchs b ON (b.IdPortalFrame = pf.id AND b.ServerId = ' . $server->get('IdServer') . ') ';
        }
        elseif ($batch) {
            
            // Update an only portal frame with a given batch
            $sql .= 'WHERE pf.id = ' . $batch->get('IdPortalFrame') . ' ';
        }
        elseif ($id) {
            
            // Update an only portal frame with a given batch
            $sql .= 'WHERE pf.id = ' . $id . ' ';
        } else {
            
            // Update all portal frames active
            $sql .= 'WHERE pf.Status = \'' . PortalFrames::STATUS_ACTIVE . '\' ';
        }
        $sql .= 'GROUP BY pf.id';
        $db = new Db();
        if ($db->Query($sql) === false) {
            throw new \Exception('Could not obtain the portal frames in order to update the frames stats');
        }
        $db2 = new Db();
        while (! $db->EOF) {
        
            // Calculate stats from portal frame batchs
            $sql = 'SELECT b.IdPortalFrame, SUM(b.ServerFramesTotal) AS total, ';
            $sql .= 'SUM(IF (b.State != \'' . Batch::STOPPED . '\' AND s.ActiveForPumping = 1, b.ServerFramesPending, 0)) AS pending, ';
            $sql .= 'SUM(IF (b.State != \'' . Batch::STOPPED . '\' AND s.ActiveForPumping = 1, b.ServerFramesActive, 0)) AS active, ';
            $sql .= 'SUM(IF (b.State != \'' . Batch::STOPPED . '\' AND s.ActiveForPumping = 0, b.ServerFramesActive + b.ServerFramesPending, 0))';
            $sql .= ' AS sfdelayed, ';
            $sql .= 'SUM(IF (b.State = \'' . Batch::STOPPED . '\', b.ServerFramesPending + b.ServerFramesActive, 0)) AS stopped, ';
            $sql .= 'SUM(b.ServerFramesSuccess) as success, ';
            $sql .= 'SUM(b.ServerFramesFatalError) AS fatalError, ';
            $sql .= 'SUM(b.ServerFramesTemporalError) AS temporalError ';
            $sql .= 'FROM Batchs b INNER JOIN Servers s ON (s.IdServer = b.ServerId) ';
            $sql .= 'WHERE b.IdPortalFrame = ' . $db->GetValue('id') . ' ';
            $sql .= 'GROUP BY b.IdPortalFrame';
            if ($db2->Query($sql) === false) {
                throw new \Exception('Could not obtain the portal frames in order to update the frames stats');
            }
            
            // For each portal frame, update it stats and status
            $portalFrame = new static((int) $db->getValue('id'));
            if (! $portalFrame->get('id')) {
                throw new \Exception('Cannot load a portal frame with ID: ' . $db->getValue('id'));
            }
            $sucessFrames = (int) $db2->getValue('success');
            $fatalErrorFrames = (int) $db2->getValue('fatalError');
            $temporalErrorFrames = (int) $db2->getValue('temporalError');
            $stoppedFrames = (int) $db2->getValue('stopped');
            $delayedFrames = (int) $db2->getValue('sfdelayed');
            $totalFrames = (int) $db2->getValue('total');
            $portalFrame->set('SFtotal', $totalFrames);
            $portalFrame->set('SFpending', (int) $db2->getValue('pending'));
            $portalFrame->set('SFactive', (int) $db2->getValue('active'));
            $portalFrame->set('SFsuccess', $sucessFrames);
            $portalFrame->set('SFfatalError', $fatalErrorFrames);
            $portalFrame->set('SFsoftError', $temporalErrorFrames);
            $portalFrame->set('SFstopped', $stoppedFrames);
            $portalFrame->set('SFdelayed', $delayedFrames);
            
            // Update success rate
            $processedFrames = $sucessFrames + $fatalErrorFrames + $temporalErrorFrames + $stoppedFrames + $delayedFrames;
            if ($processedFrames) {
                $successRate = round($sucessFrames / $processedFrames, 2);
                Logger::debug('Set priority to ' . $successRate . ' for portal frame ' . $portalFrame->get('id'));
                $portalFrame->set('SuccessRate', $successRate);
            }
            
            // Check new batch status
            $checkPortalClosing = false;
            if ($batch) {
                if ($batch->get('State') == Batch::INTIME) {
                    if (! $portalFrame->get('StartTime')) {
                        $portalFrame->set('StartTime', time());
                        self::resetBoostCycles();
                    }
                    $portalFrame->set('Status', self::STATUS_ACTIVE);
                }
                if ($batch->get('State') == Batch::CLOSING) {
                    $portalFrame->set('Status', self::STATUS_ACTIVE);
                }
                elseif ($batch->get('State') == Batch::ENDED or $batch->get('State') == Batch::NOFRAMES) {
                    $checkPortalClosing = true;
                }
                $portalFrame->set('StatusTime', time());
            } elseif ($totalFrames and $totalFrames == $sucessFrames + $fatalErrorFrames) {
                $checkPortalClosing = true;
            }
            if ($checkPortalClosing) {
                
                // If all batchs minus one (current batch state is not saved) for this portal frame are ended, the status will change to Ended
                if (self::num_batchs_in_pool($portalFrame->get('id')) == 0) {
                    $portalFrame->set('Status', self::STATUS_ENDED);
                    $portalFrame->set('EndTime', time());
                }
            }
            
            // Update whole modified fields
            $currentPortal = new static($portalFrame->get('id'));
            $portalFrame->set('Playing', $currentPortal->get('Playing'));
            $portalFrame->set('Boost', $currentPortal->get('Boost'));
            if ($portalFrame->update() === false) {
                throw new \Exception('Cannot update the portal frame with ID: ' . $portalFrame->get('id'));
            }
            $db->Next();
        }
    }
    
    /**
     * Get a list of portal frames with a given state
     * 
     * @param string $state
     * @param int $endTime
     * @param int $idNodeGenerator
     * @param string $type
     * @throws \Exception
     * @return array
     */
    public static function getByState(string $state, int $endTime = null, int $idNodeGenerator = null, string $type = null) : array
    {
        $query = 'SELECT id FROM PortalFrames WHERE Status = \'' . $state . '\' AND SFtotal > 0';
        if ($endTime) {
            
            // Maximum time in seconds to get ended portal frames
            $query .= ' AND EndTime > ' . (time() - $endTime);
        }
        if ($idNodeGenerator) {
            $query .= ' AND IdNodeGenerator = ' . $idNodeGenerator;
        }
        if ($type) {
            $query .= ' AND PublishingType = \'' . $type . '\'';
        }
        $query .= ' ORDER BY ScheduledTime DESC, StartTime, id';
        $db = new Db();
        if ($db->Query($query) === false) {
            throw new \Exception('Could not obtain a list of portal frames with ' . $state);
        }
        $portals = [];
        while (!$db->EOF) {
            $portals[] = new static($db->GetValue('id'));
            $db->Next();
        }
        return $portals;
    }
    
    public static function resume() : array
    {
        $states = [self::STATUS_CREATED, self::STATUS_ACTIVE, self::STATUS_ENDED];
        $types = [self::TYPE_UP, self::TYPE_DOWN];
        $resume = [];
        $db = new Db();
        foreach ($states as $state) {
            $query = 'SELECT count(id) AS total FROM PortalFrames WHERE Status = \'' . $state . '\'';
            if ($db->Query($query) === false) {
                throw new \Exception('Could not obtain the states portal frames resume');
            }
            $resume['states'][$state] = $db->GetValue('total');
        }
        foreach ($types as $type) {
            $query = 'SELECT count(id) AS total FROM PortalFrames WHERE PublishingType = \'' . $type . '\'';
            if ($db->Query($query) === false) {
                throw new \Exception('Could not obtain the types portal frames resume');
            }
            $resume['types'][$type] = $db->GetValue('total');
        }
        return $resume;
    }
    
    /**
     * Retrieve a list of portal frames ID without any batch associated
     * 
     * @param int $time
     * @throws \Exception
     * @return array
     */
    public static function getVoidPortalFrames(int $time = null) : array
    {
        $query = 'SELECT id FROM PortalFrames pf LEFT JOIN Batchs b ON b.IdPortalFrame = pf.id WHERE b.IdBatch IS NULL';
        if ($time) {
            $query .= ' AND CreationTime < ' . (time() - $time);
        }
        $db = new Db();
        if ($db->Query($query) === false) {
            throw new \Exception('Could not obtain the void portal frame');
        }
        $portals = [];
        while (! $db->EOF) {
            $portals[] = $db->GetValue('id');
            $db->Next();
        }
        return $portals;
    }
    
    public function getServers() : array
    {
        $query = 'SELECT s.IdServer as id, s.Description as name FROM Servers s';
        $query .= ' INNER JOIN Batchs b ON (b.IdPortalFrame = ' . $this->id . ' AND b.ServerId = s.IdServer)';
        $db = new Db();
        if ($db->Query($query) === false) {
            throw new \Exception('Could not obtain the void portal frame');
        }
        $servers = [];
        while (! $db->EOF) {
            $servers[(string) $db->GetValue('id')] = $db->GetValue('name');
            $db->Next();
        }
        return $servers;
    }
    
    public function getBatchs() : array
    {
        $sql = 'SELECT IdBatch, State, ServerId';
        $sql .= ' FROM Batchs';
        $sql .= ' WHERE IdPortalFrame = ' . $this->id . ' AND ServerFramesTotal > 0 AND State != \'' . Batch::NOFRAMES . '\'';
        $sql .= ' ORDER BY IdBatch';
        $db = new Db();
        if ($db->Query($sql) === false) {
            throw new \Exception('Could not obtain the batchs for the portal frame ' . $this->id);
        }
        $batchs = [];
        $delayedServers = Server::getDelayed();
        while (! $db->EOF) {
            if (isset($delayedServers[$db->GetValue('ServerId')]) and ($db->GetValue('State') == Batch::INTIME 
                    or $db->GetValue('State') == Batch::CLOSING)) {
                $state = Batch::DELAYED;
            } else {
                $state = $db->GetValue('State');
            }
            $batchs[$db->GetValue('IdBatch')] = $state;
            $db->Next();
        }
        return $batchs;
    }
    
    /**
     * Retrieve a list of node frames identificators related to this portal
     * 
     * @param string $operation
     * @throws \Exception
     * @return array
     */
    public function getNodeFrames(string $operation = null, bool $withLostActivity = false) : array
    {
        $nodeFrame = new NodeFrame();
        $condition = 'IdPortalFrame = ' . $this->id;
        if ($operation) {
            switch ($operation) {
                case PortalFrames::TYPE_UP:
                    $condition .= ' AND IsProcessUp = 0';
                    break;
                case PortalFrames::TYPE_DOWN:
                    $condition .= ' AND IsProcessDown = 0';
                    break;
            }
        }
        if ($withLostActivity) {
            $condition .= ' AND Active = 0 AND IsProcessUp = 1';
        }
        $frames = $nodeFrame->find('IdNodeFrame', $condition, null, MONO);
        if ($frames === false) {
            throw new \Exception('Could not obtain the node frames for the portal frame ' . $this->id);
        }
        return $frames;
    }
    
    /**
     * Reset all the active portal frames to zero boost cycles
     *
     * @throws \Exception
     */
    public static function resetBoostCycles() : void
    {
        $sql = 'UPDATE PortalFrames SET BoostCycles = 0 WHERE Status = \'' . self::STATUS_ACTIVE . '\'';
        $db = new Db();
        if ($db->Execute($sql) === false) {
            throw new \Exception('Could not reset portal frames boost cycles');
        }
    }
    
    /**
     * Cancel all node frames belong to this portal in a 'pending to do something' state
     * 
     * @throws \Exception
     */
    public function cancel() : void
    {
        if (! $this->id) {
            throw new \Exception('Cannot cancel a portal without ID');
        }
        if ($this->PublishingType != self::TYPE_UP) {
            
            // For now only support cancellation for publishing type Up
            return;
        }
        $frames = $this->getNodeFrames($this->PublishingType);
        foreach ($frames as $id) {
            $frame = new NodeFrame($id);
            $frame->cancel(false);
        }
    }
    
    /**
     * Retrieve the total of batchs for a given portal frame without ended or no-frames state
     * 
     * @param int $idPortalFrame
     * @throws \Exception
     * @return int
     */
    private static function num_batchs_in_pool(int $idPortalFrame) : int
    {
        $sql = 'SELECT COUNT(IdBatch) AS total FROM Batchs where IdPortalFrame = ' . $idPortalFrame 
            . ' AND State NOT IN (\'' . Batch::ENDED . '\', \'' . Batch::NOFRAMES . '\')';
        $db = new Db();
        if ($db->Query($sql) === false) {
            throw new \Exception('Could not obtain the number of batchs not ended for the portal frame ' . $idPortalFrame);
        }
        return (int) $db->getValue('total');
    }
}
