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
    
    public function upPortalFrameVersion(int $portalId, int $idUser = null, string $type = self::TYPE_UP) : int
    {
        $portalFrameVersion = $this->getLastVersion($portalId);
        $portalFrameVersion++;
        $this->set('IdPortal', $portalId);
        $this->set('Version', $portalFrameVersion);
        $this->set('CreationTime', time());
        $this->set('PublishingType', $type);
        $this->set('CreatedBy', $idUser);
        $this->set('Status', self::STATUS_CREATED);
        $this->set('StatusTime', time());
        $idPortalFrameVersion = parent::add();
        return ($idPortalFrameVersion > 0) ? (int) $idPortalFrameVersion : 0;
    }

    public function getLastVersion(int $portalId) : int
    {
        $result = parent::find('MAX(Version)', 'IdPortal = %s', array('IdPortal' => $portalId), MONO);
        return (int) $result[0];
    }

    public function getId(int $portalId, int $version) : int
    {
        $result = parent::find('id', 'IdPortal = %s AND Version = %s',
            array('IdPortal' => $portalId, 'Version' => $version), MONO);
        return (int) $result[0];
    }

    public function getAllVersions(int $portalId) : array
    {
        $result = parent::find('id, Version', 'IdPortal = %s', array('IdPortal' => $portalId), MULTI);
        $portalFrameVersions = [];
        foreach ($result as $resultData) {
            $portalFrameVersions[] = array('id' => $resultData['id'], 'version' => $resultData['Version']);
        }
        return $portalFrameVersions;
    }
    
    /**
     * Called only when a batch change its state to a new one
     * 
     * @param Batch $batch
     * @param string $state
     * @throws \Exception
     */
    public static function updatePortalFrames(Batch $batch, string $state = null) : void
    {
        if (!$batch->get('IdBatch')) {
            return;
        }
        if (!$batch->get('IdPortalFrame')) {
            throw new \Exception('There is not a portal frame for batch with ID: ' . $batch->get('IdBatch'));
        }
        $portalFrame = new static($batch->get('IdPortalFrame'));
        if (!$portalFrame->get('id')) {
            throw new \Exception('Cannot load a portal frame with ID: ' . $batch->get('IdPortalFrame'));
        }
        if (!$state) {
            if (!$batch->get('State')) {
                throw new \Exception('Unknown state for portal frame with ID: ' . $portalFrame->get('id'));
            }
            $state = $batch->get('State');
        }
        if ($state == Batch::INTIME) {
            $portalFrame->set('StartTime', time());
            $portalFrame->set('Status', self::STATUS_ACTIVE);
            $portalFrame->set('SFpending', $portalFrame->get('SFpending') + $batch->get('ServerFramesTotal'));
        }
        elseif ($state == Batch::CLOSING) {
            return;
        }
        elseif ($state == Batch::ENDED) {
            $portalFrame->set('SFpending', $portalFrame->get('SFpending') - $batch->get('ServerFramesTotal'));
            $portalFrame->set('SFprocessed', $portalFrame->get('SFprocessed') + $batch->get('ServerFramesSucess'));
            $portalFrame->set('SFerrored', $portalFrame->get('SFerrored') + $batch->get('ServerFramesError'));
            
            // If all batchs minus one (current batch state is not saved) for this portal frame are ended, the status will change to Ended
            if (self::num_batchs_in_pool($portalFrame->get('id')) == 1) {
                $portalFrame->set('Status', self::STATUS_ENDED);
                $portalFrame->set('EndTime', time());
            }
        } else {
            return;
        }
        $portalFrame->set('StatusTime', time());
        if ($portalFrame->update() === false) {
            throw new \Exception('Cannot update the portal frame with ID: ' . $portalFrame->get('id'));
        }
    }
    
    public static function getByState(string $state) : array
    {
        $db = new Db();
        $res = $db->Query('SELECT id FROM PortalFrames WHERE Status = \'' . $state . '\'');
        if ($res === false) {
            throw new \Exception('Could not obtain a list of portal frames with ' . $state);
        }
        $portals = [];
        while (!$db->EOF) {
            $portals[] = new static($db->GetValue('id'));
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
        $sql = 'SELECT COUNT(IdBatch) AS total FROM Batchs where IdPortalFrame = ' . $idPortalFrame . ' AND State != \'' . Batch::ENDED . '\'';
        $db = new Db();
        if ($db->Query($sql) === false) {
            throw new \Exception('Could not obtain the number of batchs not ended for the portal frame ' . $idPortalFrame);
        }
        return (int) $db->getValue('total');
    }
}
