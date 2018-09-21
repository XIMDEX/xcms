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

use \Ximdex\Runtime\Db;
use Ximdex\Models\ORM\PortalFramesOrm;

class PortalFrames extends PortalFramesOrm
{
    const TYPE_UP = 'Up';
    const TYPE_DOWN = 'Down';
    const STATUS_CREATED = 'Created';
    const STATUS_ACTIVE = 'Active';
    const STATUS_ENDED = 'Ended';
    
    public function upPortalFrameVersion(int $nodeId, int $idUser = null, string $type = self::TYPE_UP) : int
    {
        $portalFrameVersion = $this->getLastVersion($nodeId);
        $portalFrameVersion++;
        $this->set('IdNodeGenerator', $nodeId);
        $this->set('Version', $portalFrameVersion);
        $this->set('CreationTime', time());
        $this->set('PublishingType', $type);
        $this->set('CreatedBy', $idUser);
        $this->set('Status', self::STATUS_CREATED);
        $this->set('StatusTime', time());
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
     * Calculate the stats from batchs related to a given portal frame
     * 
     * @param Batch $batch
     * @throws \Exception
     */
    public static function updatePortalFrames(Batch $batch) : void
    {
        if (!$batch->get('IdBatch')) {
            return;
        }
        if (!$batch->get('State')) {
            throw new \Exception('Unknown state for portal frame with ID: ' . $portalFrame->get('id'));
        }
        if (!$batch->get('IdPortalFrame')) {
            throw new \Exception('There is not a portal frame for batch with ID: ' . $batch->get('IdBatch'));
        }
        $portalFrame = new static($batch->get('IdPortalFrame'));
        if (!$portalFrame->get('id')) {
            throw new \Exception('Cannot load a portal frame with ID: ' . $batch->get('IdPortalFrame'));
        }
        
        // Calculate stats from portal frame batchs
        $sql = 'SELECT SUM(ServerFramesTotal) as total, SUM(ServerFramesPending) as pending, SUM(ServerFramesActive) as active, ';
        $sql .= 'SUM(ServerFramesSuccess) as success, SUM(ServerFramesError) as errored ';
        $sql .= 'FROM Batchs ';
        $sql .= 'WHERE IdPortalFrame = ' . $portalFrame->get('id') . ' ';
        // $sql .= 'GROUP BY IdPortalFrame';
        $db = new Db();
        if ($db->Query($sql) === false) {
            throw new \Exception('Could not obtain the batchs stats for the portal frame ' . $portalFrame->get('id'));
        }
        $portalFrame->set('SFtotal', (int) $db->getValue('total'));
        $portalFrame->set('SFpending', (int) $db->getValue('pending'));
        $portalFrame->set('SFactive', (int) $db->getValue('active'));
        $portalFrame->set('SFsuccess', (int) $db->getValue('success'));
        $portalFrame->set('SFerrored', (int) $db->getValue('errored'));
        
        // Check new batch status
        if ($batch->get('State') == Batch::INTIME) {
            if (!$portalFrame->get('StartTime')) {
                $portalFrame->set('StartTime', time());
            }
            $portalFrame->set('Status', self::STATUS_ACTIVE);
        }
        if ($batch->get('State') == Batch::CLOSING) {
            $portalFrame->set('Status', self::STATUS_ACTIVE);
        }
        elseif ($batch->get('State') == Batch::ENDED or $batch->get('State') == Batch::NOFRAMES) {
            
            // If all batchs minus one (current batch state is not saved) for this portal frame are ended, the status will change to Ended
            if (self::num_batchs_in_pool($portalFrame->get('id')) == 0) {
                $portalFrame->set('Status', self::STATUS_ENDED);
                $portalFrame->set('EndTime', time());
            }
        }
        $portalFrame->set('StatusTime', time());
        if ($portalFrame->update() === false) {
            throw new \Exception('Cannot update the portal frame with ID: ' . $portalFrame->get('id'));
        }
    }
    
    /**
     * Get a list of portal frames with a given state
     * 
     * @param string $state
     * @param int $endTime
     * @throws \Exception
     * @return array
     */
    public static function getByState(string $state, int $endTime = null) : array
    {
        $query = 'SELECT id FROM PortalFrames WHERE Status = \'' . $state . '\'';
        if ($endTime) {
            
            // Maximum time in seconds to get ended portal frames
            $query .= ' AND EndTime > ' . (time() - $endTime);
        }
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
     * @throws \Exception
     * @return array
     */
    public static function getVoidPortalFrames() : array
    {
        $query = 'SELECT id FROM PortalFrames pf LEFT JOIN Batchs b ON b.IdPortalFrame = pf.id WHERE b.IdBatch IS NULL';
        $db = new Db();
        if ($db->Query($query) === false) {
            throw new \Exception('Could not obtain the void portal frame');
        }
        $portals = [];
        while (!$db->EOF) {
            $portals[] = $db->GetValue('id');
            $db->Next();
        }
        return $portals;
    }
    
    /**
     * Retrieve the total of batchs for a given portal frame without ended state
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
