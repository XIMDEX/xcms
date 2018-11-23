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
use Ximdex\Models\ORM\BatchsOrm;
use Ximdex\Runtime\Db;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

/**
 * 	@brief Handles operations with Batchs.
 *
 * 	A Batch is a set of documents which have to be published together for obtain the correct graph of the portal.
 * 	This class includes the methods that interact with the Database.
 */
class Batch extends BatchsOrm
{
    
    // Publishing types
    const TYPE_UP = 'Up';
    const TYPE_DOWN = 'Down';
    
    // Status
    const WAITING = 'Waiting';
    const INTIME = 'InTime';
    const ENDED = 'Ended';
    const CLOSING = 'Closing';
    const NOFRAMES = 'NoFrames';
    const STOPPED = 'Stopped';
    const DELAYED = 'Delayed';
    const CREATING = 'Creating';
    
    // Priorities
    const PRIORITY_TYPE_DOWN = 1.1;

    public function set($attribute, $value)
    {
        if ($attribute == 'State') {
            if ($this->get('State')) {
                $from = $this->get('State');
            } else {
                $from = '- Nothing -';
            }
            Logger::info('Changing state for batch: ' . $this->IdBatch . ' from ' . $from . ' to ' . $value);
        }
        parent::set($attribute, $value);
    }
    
    /**
     *  Adds a row to Batchs table
     *  
     *  @param int timeOn
     *  @param string type
     *  @param int idNodeGenerator
     *  @param int priority
     *  @param int idBatchDown
     *  @param int idPortalFrame
     *  @param int $userId
     *  @return int|null
     */
    public function create($timeOn, $type, $idNodeGenerator, $priority, $serverId, $idBatchDown = null, $idPortalFrame = null, $userId = null)
    {
        setlocale(LC_NUMERIC, 'C');
        $this->set('TimeOn', $timeOn);
        $this->set('State', Batch::CREATING);
        $this->set('Type', $type);
        $this->set('IdBatchDown', $idBatchDown);
        $this->set('IdNodeGenerator', $idNodeGenerator);
        $this->set('Priority', $priority);
        $this->set('IdPortalFrame', $idPortalFrame);
        $this->set('UserId', $userId);
        $this->set('ServerId', $serverId);
        $idBatch = parent::add();
        if ($idBatch > 0) {
            return $idBatch;
        }
        Logger::error("Batch type $type for node $idNodeGenerator");
        return null;
    }

    /**
     *  Increases the number of cycles of a Batch
     *  
     *  @return bool
     */
    public function calcCycles() : bool
    {
        $cycles = (int) $this->get('Cycles') + 1;
        $this->set('Cycles', $cycles);
        return true;
    }
    
    public function calcPriority() : bool
    {
        $sucessFrames = (int) $this->get('ServerFramesSuccess');
        $fatalErrorFrames = (int) $this->get('ServerFramesFatalError');
        $temporalErrorFrames = (int) $this->get('ServerFramesTemporalError');
        $processedFrames = $sucessFrames + $fatalErrorFrames + $temporalErrorFrames;
        $portal = new PortalFrames($this->get('IdPortalFrame'));
        if ($processedFrames) {
            $priority = round($sucessFrames / $processedFrames * $portal->get('Boost'), 2);
        } else {
            $priority = 1.0 * $portal->get('Boost');
        }
        if ($priority) {
            Logger::info('Set priority to ' . $priority . ' for batch ' . $this->IdBatch);
            $this->set('Priority', $priority);
        }
        return true;
    }

    /**
     *  Gets the Batch of type Down associated to a Batch of type Up
     *  
     *  @param int batchId
     *  @return array
     */
    public function getDownBatch($batchId)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = "SELECT downBatchs.IdBatch, downBatchs.TimeOn FROM Batchs upBatchs, " .
                "Batchs AS downBatchs WHERE downBatchs.IdBatch = upBatchs.IdBatchDown " .
                "AND upBatchs.IdBatch = $batchId";
        $dbObj->Query($query);
        $arrayBatch = array();
        while (!$dbObj->EOF) {
            $arrayBatch = array(
                'IdBatch' => $dbObj->GetValue("IdBatch"),
                'TimeOn' => $dbObj->GetValue("TimeOn")
            );
            $dbObj->Next();
        }
        return $arrayBatch;
    }

    /**
     *  Gets the Batch of type Up associated to a Batch of type Down
     *  
     *  @param int batchId
     *  @return array
     */
    public function getUpBatch($batchId)
    {
        $result = parent::find('IdBatch', 'IdBatchDown = %s', array('IdBatchDown' => $batchId), MONO);
        if (is_null($result)) {
            return null;
        }
        return $result;
    }
    
    /**
     * Retrieve a total of batchs in processing status
     * 
     * @param string $state
     * @return int
     */
    public static function countBatchsInProcess(string $state = Batch::INTIME) : int
    {
        $sql = 'SELECT COUNT(b.IdBatch) AS total FROM Batchs b INNER JOIN PortalFrames pf ON pf.id = b.IdPortalFrame AND pf.Playing IS TRUE'
            . ' WHERE b.TimeOn < UNIX_TIMESTAMP() AND b.State = \'' . $state . '\' AND b.ServerFramesTotal > 0';
        $dbObj = new Db();
        $dbObj->Query($sql);
        if ($dbObj->numRows) {
            return (int) $dbObj->GetValue('total');
        }
        return 0;
    }
    
    /**
     * Update all batchs for a given portal frame to intime status and zero cycles
     * 
     * @param int $portalId
     * @return boolean
     */
    public static function restart(int $portalId) : bool 
    {
        if (!$portalId) {
            return false;
        }
        $sql = 'UPDATE `Batchs` SET State = \'' . Batch::INTIME . '\', Cycles = 0 WHERE State = \'Stopped\' AND IdPortalFrame = ' . $portalId;
        $db = new Db();
        return $db->Execute($sql);
    }
}
