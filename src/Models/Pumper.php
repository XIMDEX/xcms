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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use ServerErrorByPumper;
use ServerErrorManager;
use SynchronizerStat;
use Ximdex\Logger;
use Ximdex\Models\ORM\PumpersOrm;
use Ximdex\Runtime\App;

\Ximdex\Modules\Manager::file('/inc/model/ServerErrorByPumper.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/manager/ServerErrorManager.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/conf/synchro_conf.php', 'ximSYNC');

/**
 * @brief Handles operations with Pumpers.
 *
 * A Pumper is an instance of the dexPumper script, wich is responsible for sending the ServerFrames to Server (via ftp, ssh, etc).
 * This class includes the methods that interact with the Database.
 */
class Pumper extends PumpersOrm
{
    private $maxvoidcycles = 10;
    private $sleeptime = 2;
    var $syncStatObj;

    /**
     * Sets the value of any variable
     * 
     * @param string key
     * @param unknown value
     */
    function setFlag($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Gets the value of any variable
     * 
     * @param string key
     */
    function getFlag($key)
    {
        return $this->$key;
    }

    /**
     * Adds a row to Pumpers table
     * 
     * @param int idServer
     * @return int|null
     */

    function create($idServer)
    {
        $this->set('IdServer', $idServer);
        $this->set('State', 'New');
        $this->set('StartTime', time());
        $this->set('CheckTime', time());
        $this->set('ProcessId', 'xxxx');
        parent::add();
        $pumperID = $this->get('PumperId');
        if ($pumperID > 0) {
            $serverError = new ServerErrorByPumper();
            $serverError->create($pumperID, $idServer);
            return $pumperID;
        }
        $this->PumperToLog(null, null, null, null, null, __CLASS__, __FUNCTION__, __FILE__,
            __LINE__, "ERROR", 8, "ERROR Inserting pumper");
        return null;
    }

    /**
     * Gets the Pumpers whose state is different to Ended
     * 
     * @return array|null
     */
    function getPumpersInRegistry()
    {
        $sql = "SELECT PumperId FROM Pumpers WHERE State != 'Ended'";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        $pumpers = array();
        while (!$dbObj->EOF) {
            $pumpers[] = $dbObj->GetValue("PumperId");
            $dbObj->Next();
        }
        return $pumpers;
    }

    /**
     * Calls to command for start a Pumper
     * 
     * @param int pumperId
     * @param string modo
     * @return bool
     */
    function startPumper($pumperId, $modo = "php")
    {
        $dbObj = new \Ximdex\Runtime\Db();
        
        // Initialize the pumper to Starting state
        $this->set('State', 'Starting');
        $this->update();
        $startCommand =  "php ".XIMDEX_ROOT_PATH.'/bootstrap.php '.PUMPERPHP_PATH . "/dexpumper." . $modo 
            . " --pumperid=$pumperId --sleeptime=" . $this->sleeptime . " --maxvoidcycles=" . $this->maxvoidcycles 
            . " --localbasepath=" . SERVERFRAMES_SYNC_PATH . " > /dev/null 2>&1 &";
        $this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
            __LINE__, "INFO", 8, "Pumper call: $startCommand");
        $out = array();
        system($startCommand, $var);
        $this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
            __LINE__, "INFO", 8, $startCommand, true);

        // 0: OK, 200: connection problem, 255: unexistent server, 127:command not found
        if ($var == 0) {
            $this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "INFO", 8, "Pumper $pumperId started succefully", true);
            return true;
        } else if ($var == 200) {
            $this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "ERROR", 8, "ERROR In server connection starting pumper $pumperId");
            $serverMng = new ServerErrorManager();
            $serverMng->disableServerByPumper($pumperId);
            return false;
        } else if ($var == 400) {
            $this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "ERROR", 8, "ERROR registering pumper $pumperId.");
            return false;
        } else {
            $this->PumperToLog(null, null, null, null, $pumperId, __CLASS__, __FUNCTION__, __FILE__,
                __LINE__, "ERROR", 8, "ERROR Code $var starting pumper $pumperId");
            return false;
        }
    }

    /**
     * Logs the activity of the Pumper
     * 
     * @param int batchId
     * @param int nodeFrameId
     * @param int channelFrameId
     * @param int serverFrameId
     * @param int pumperId
     * @param string class
     * @param string method
     * @param string file
     * @param int line
     * @param string type
     * @param int level
     * @param string comment
     * @param int doInsertSql
     */
    function PumperToLog($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
                         $class, $method, $file, $line, $type, $level, $comment, $doInsertSql = false)
    {
        if (strcmp(App::getValue("SyncStats"), "1") == 0) {
            if (!isset($this->syncStatObj)) {
                $this->syncStatObj = new SynchronizerStat();
            }
            $this->syncStatObj->create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
                $class, $method, $file, $line, $type, $level, $comment, $doInsertSql);
        }
        Logger::debug('PumperToLog -> batchId:' . $batchId . ' nodeFrameId:' . $nodeFrameId . ' channelFrameId:' . $channelFrameId 
            . ' serverFrameId:' . $serverFrameId . ' pumperId:' . $pumperId . ' class:' . $class . ' method:' . $method . ' $file:' . $file
            . ' line:' . $line . ' type:' . $type . ' level:' . $level . ' comment:' . $comment . ' doInsertSql:' . $doInsertSql);
    }
}