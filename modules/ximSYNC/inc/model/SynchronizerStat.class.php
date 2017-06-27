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


use Ximdex\Runtime\App;
use Ximdex\Runtime\Db;

ModulesManager::file('/inc/model/orm/SynchronizerStats_ORM.class.php', 'ximSYNC');
ModulesManager::file('/inc/manager/Sync_Log.class.php', 'ximSYNC');
ModulesManager::file('/inc/utils.php');

/**
 * @brief Logging for the publication incidences.
 *
 *    This class includes the methods that interact with the Database.
 */
class SynchronizerStat extends SynchronizerStats_ORM
{


    /**
     * @param $batchId
     * @param $nodeFrameId
     * @param $channelFrameId
     * @param $serverFrameId
     * @param $pumperId
     * @param $class
     * @param $method
     * @param $file
     * @param $line
     * @param $type
     * @param $level
     * @param $comment
     * @param bool $doInsertSql
     * @return null
     */
    function create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
                    $class, $method, $file, $line, $type, $level, $comment, $doInsertSql = false)
    {


        if (strcmp(App::getValue("SyncStats"), "1") == 0) {

            // Seg�n el valor del parametro $doLog se insertara en la tabla o no.
            if ($doInsertSql) {
                $this->set('IdStat', null);
                $this->set('BatchId', $batchId);
                $this->set('NodeFrameId', $nodeFrameId);
                $this->set('ChannelFrameId', $channelFrameId);
                $this->set('ServerFrameId', $serverFrameId);
                $this->set('PumperId', $pumperId);
                $this->set('Class', ($class == '') ? null : $class);
                $this->set('Method', ($method == '') ? null : $method);
                $this->set('File', $file);
                $this->set('Line', $line);
                $this->set('Type', $type);
                $this->set('Level', $level);
                $this->set('Time', time());
                $this->set('Comment', $comment);

                parent::add();
            }

            //XMD_Log::info($comment);
            Sync_Log::write($comment, $level);
        }
        return null;
    }

    /**
     *  Gets a row from SynchronizerStats table which matching the value of idStat.
     * @param int $idStat
     * @return array|null
     */

    function getStatById($idStat)
    {

        parent::__construct($idStat);

        $stat = array();
        $dbObj = new Db();
        if ($stats['IdStat'] = $dbObj->GetValue("IdStat")) {

            $stats['BatchId'] = $dbObj->GetValue("BatchId");
            $stats['NodeFrameId'] = $dbObj->GetValue("NodeFrameId");
            $stats['ChannelFrameId'] = $dbObj->GetValue("ChannelFrameId");
            $stats['ServerFrameId'] = $dbObj->GetValue("ServerFrameId");
            $stats['PumperId'] = $dbObj->GetValue("PumperId");
            $stats['Class'] = $dbObj->GetValue("Class");
            $stats['Method'] = $dbObj->GetValue("Method");
            $stats['File'] = $dbObj->GetValue("File");
            $stats['Line'] = $dbObj->GetValue("Line");
            $stats['Type'] = $dbObj->GetValue("Type");
            $stats['Level'] = $dbObj->GetValue("Level");
            $stats['Time'] = $dbObj->GetValue("Time");
            $stats['Comment'] = $dbObj->GetValue("Comment");

            return $stat;
        }

        return null;

    }
}
