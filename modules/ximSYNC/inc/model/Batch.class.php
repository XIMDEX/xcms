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

use Ximdex\Logger;
use Ximdex\Models\Node;

Ximdex\Modules\Manager::file('/inc/model/orm/SynchronizerStats_ORM.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/inc/model/NodeFrame.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/inc/model/ChannelFrame.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/inc/model/orm/Batchs_ORM.class.php', 'ximSYNC');
Ximdex\Modules\Manager::file('/conf/synchro_conf.php', 'ximSYNC');

/**
 * 	@brief Handles operations with Batchs.
 *
 * 	A Batch is a set of documents which have to be published together for obtain the correct graph of the portal.
 * 	This class includes the methods that interact with the Database.
 */
class Batch extends Batchs_ORM
{
    var $syncStatObj;
    const TYPE_UP = 'Up';
    const TYPE_DOWN = 'Down';
    const WAITING = 'Waiting';
    const INTIME = 'InTime';
    const ENDED = 'Ended';
    const CLOSING = 'Closing';

    /**
     *  Adds a row to Batchs table
     *  
     *  @param int timeOn
     *  @param string type
     *  @param int idNodeGenerator
     *  @param int priority
     *  @param int idBatchDown
     *  @param int idPortalVersion
     *  @return int|null
     */
    function create($timeOn, $type, $idNodeGenerator, $priority, $idBatchDown = NULL, $idPortalVersion = 0, $userId = NULL)
    {
        $priority = (float) (MIN_TOTAL_PRIORITY * $priority);
        $this->set('TimeOn', $timeOn);
        $this->set('State', Batch::WAITING);
        $this->set('Playing', 1);
        $this->set('Type', $type);
        $this->set('IdBatchDown', $idBatchDown);
        $this->set('IdNodeGenerator', $idNodeGenerator);
        $this->set('Priority', $priority);
        $this->set('IdPortalVersion', $idPortalVersion);
        $this->set('UserId', $userId);
        $idBatch = parent::add();
        if ($idBatch > 0) {
            return $idBatch;
        }
        Logger::error("Batch type $type for node $idNodeGenerator");
        return null;
    }

    /**
     *  Gets the field IdBatch from Batchs table which matching the value of nodeId
     *  
     *  @param int nodeId
     *  @return array
     */
    function getAllBatchsFromNode($nodeId)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $time = time();
        $dbObj->Query("SELECT IdBatch FROM Batchs WHERE Type='Up' AND TimeOn > $time AND IdNodeGenerator = $nodeId
						ORDER BY TimeOn ASC");
        $arrayBatchs = array();
        while (!$dbObj->EOF) {
            $arrayBatchs[] = $dbObj->GetValue("IdBatch");
            $dbObj->Next();
        }
        return $arrayBatchs;
    }

    /**
     *  Increases the number of cycles of a Batch
     *  
     *  @param int majorCycle
     *  @param int minorCycle
     *  @return array
     */
    function calcCycles($majorCycle, $minorCycle)
    {
        if (is_null($majorCycle) || is_null($minorCycle)) {
            $this->BatchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "ERROR", 8
                , _("ERROR Params $majorCycle - $minorCycle"));
            return null;
        }
        if ($minorCycle > $majorCycle) {
            $majorCycle++;
            $minorCycle = 0;
            $this->BatchToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8
                , _("Up to major cycle $majorCycle batch ") . $this->get('IdBatch') . "");
        } else {
            $minorCycle++;
        }
        return array($majorCycle, $minorCycle);
    }

    /**
     *  Logs the activity of the Batch
     *  
     *  @param int batchId
     *  @param int nodeFrameId
     *  @param int channelFrameId
     *  @param int serverFrameId
     *  @param int pumperId
     *  @param string class
     *  @param string method
     *  @param string file
     *  @param int line
     *  @param string type
     *  @param int level
     *  @param string comment
     *  @param int doInsertSql
     */
    function BatchToLog($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId, $class, $method, $file
        , $line, $type, $level, $comment, $doInsertSql = false)
    {
        if (!isset($this->syncStatObj)) {
            $this->syncStatObj = new SynchronizerStat();
        }
        $this->syncStatObj->create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId, $class, $method, $file
            , $line, $type, $level, $comment, $doInsertSql);
    }

    /**
     *  Gets the field IdBatch from Batchs join Nodes which matching the value of State
     *  
     *  @param string stateCryteria
     *  @return array|false
     */
    function getNodeGeneratorsFromBatchs($stateCryteria = null)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $where = "";
        if ($stateCryteria) {
            if ($stateCryteria != "Any") {
                $where .= " AND Batchs.State = '" . $stateCryteria . "'";
            }
        }
        $query = "SELECT Batchs.IdNodeGenerator, Nodes.Name FROM Batchs, Nodes where Batchs.IdNodeGenerator = Nodes.IdNode " 
            . $where . " group by Batchs.IdNodeGenerator";
        $dbObj->Query($query);
        if (!$dbObj->numErr) {
            if ($dbObj->numRows > 0) {
                $arrayNodes = array();
                while (!$dbObj->EOF) {
                    $arrayNodes[$dbObj->row['IdNodeGenerator']]['Name'] = $dbObj->row['Name'];
                    $nodeGeneratorObj = new Node($dbObj->row['IdNodeGenerator']);
                    $arrayNodes[$dbObj->row['IdNodeGenerator']]['Path'] = $nodeGeneratorObj->getPath();
                    $dbObj->Next();
                }
                return $arrayNodes;
            }
        } else {
            Logger::info("Error in DB: " . $dbObj->desErr);
        }
        return false;
    }

    /**
     *  Gets the Batchs which matching some criteria
     *  
     *  @param string stateCriteria
     *  @param int activeCriteria
     *  @param int downCriteria
     *  @param int limitCriteria
     *  @param int idNodeGenerator
     *  @param int dateUpCriteria
     *  @param int dateDownCriteria
     *  @return array|false
     */
    function getAllBatchs($stateCriteria = null, $activeCriteria = null, $downCriteria = null, $limitCriteria = null
        , $idNodeGenerator = null, $dateUpCriteria = 0, $dateDownCriteria = 0)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $where = " WHERE 1 ";
        if ($stateCriteria) {
            if ($stateCriteria != "Any") {
                $where .= " AND State = '" . $stateCriteria . "'";
            }
        }
        if ($activeCriteria !== null) {
            if ($activeCriteria != "Any") {
                $where .= " AND Playing = '" . $activeCriteria . "'";
            }
        }
        if ($downCriteria !== null) {
            if ($downCriteria != "Any") {
                $where .= " AND Type = 'Up'";
            }
        }
        if ($idNodeGenerator !== null) {
            $where .= " AND IdNodeGenerator = " . $idNodeGenerator;
        }
        if ($dateUpCriteria != 0) {
            $where .= " AND TimeOn >= " . $dateUpCriteria;
        }
        if ($dateDownCriteria != 0) {
            $where .= " AND TimeOn <= " . $dateDownCriteria;
        }
        if ($limitCriteria !== null) {
            $limit = " LIMIT 0," . $limitCriteria;
        }
        $query = "SELECT IdBatch, TimeOn, State, Playing, Type, IdBatchDown, " .
                "IdNodeGenerator FROM Batchs" . $where . " ORDER BY Priority DESC, TimeOn DESC" .
                $limit;
        $dbObj->Query($query);
        if (!$dbObj->numErr) {
            if ($dbObj->numRows > 0) {
                $arrayBatchs = array();
                while (!$dbObj->EOF) {
                    $arrayBatchs[] = $dbObj->row;
                    $dbObj->Next();
                }
                return $arrayBatchs;
            }
        } else {
            Logger::info("Error en BD: " . $dbObj->desErr);
        }
        return false;
    }

    /**
     *  Gets the Batch of type Down associated to a Batch of type Up
     *  
     *  @param int batchId
     *  @return array
     */
    function getDownBatch($batchId)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $time = time();
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
    function getUpBatch($batchId)
    {
        $result = parent::find('IdBatch', 'IdBatchDown = %s', array('IdBatchDown' => $batchId), MONO);
        if (is_null($result)) {
            return null;
        }
        return $result;
    }

    /**
     *  Sets the field Playing for a Batch
     *  
     *  @param int idBatch
     *  @param int playingValue
     *  return bool
     */
    function setBatchPlayingOrUnplaying($idBatch, $playingValue = 1)
    {
        if ($playingValue == 2) {
            $playingValue = ($this->get('Playing') == 0) ? 1 : 0;
        }
        parent::__construct($idBatch);
        $this->set('Playing', $playingValue);
        $updatedRows = parent::update();
        if ($updatedRows == 1) {
            $this->BatchToLog($idBatch, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO", 8
                , _("Setting playing Value = $playingValue for batch $idBatch"));
            return true;
        } else {
            Logger::info("Error en BD: " . $dbObj->desErr);
        }
        return false;
    }

    /**
     *  Sets the field Priority for a Batch
     *  
     *  @param int idBatch
     *  @param string mode
     *  return bool
     */
    function prioritizeBatch($idBatch, $mode = 'up')
    {
        setlocale (LC_NUMERIC, 'C'); //Hack: fix bad compose of float field in sql update
        parent::__construct($idBatch);
        $priority = (float) $this->get('Priority');   
        if ($mode === 'up') {
            $priority += 0.3;
            if ($priority > MAX_TOTAL_PRIORITY) {
                $priority = MAX_TOTAL_PRIORITY;
            }
        } else {
            $priority -= 0.3;
            if ($priority < MIN_TOTAL_PRIORITY) {
                $priority = MIN_TOTAL_PRIORITY;
            }
        }
        $this->set('Priority', $priority);
        $hasUpdated = parent::update();
        if ($hasUpdated) {
            $this->BatchToLog($idBatch, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__, __LINE__, "INFO"
                , 8, _("Setting priority Value = $playingValue for batch $idBatch"));
            return true;
        } else {
            return false;
        }
    }
}