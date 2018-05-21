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

\Ximdex\Modules\Manager::file('/inc/model/orm/PublishingReport_ORM.class.php', 'ximSYNC');
\Ximdex\Modules\Manager::file('/inc/model/Batch.class.php', 'ximSYNC');

/**
 * 	@brief CRUD for Publishing Reports data.
 *
 * 	This class includes the methods that interact with the Database.
 */
class PublishingReport extends PublishingReport_ORM
{
    /**
     *  Adds a row to PublishingReport table.
     *  @param int idSection
     *  @param int idNode
     *  @param int idChannel
     *  @param int idSyncServer
     *  @param int idPortalVersion
     *  @param int pubTime
     *  @param string state
     *  @param string progress
     *  @param string fileName
     *  @param string filePath
     */
    public $progressTable = array(
        'Pending' => '20',
        'Due2In_' => '40',
        'Due2In' => '60',
        'Due2Out_' => '40',
        'Due2Out' => '60',
        'Pumped' => '80',
        ServerFrame::IN => '100',
        'Out' => '100',
        'Canceled' => '100',
        'Replaced' => '100',
        'Removed' => '100',
        'Error' => '100',
        'Warning' => '100',
        'Due2InWithError' => '100',
        'Due2OutWithError' => '100'
    );

    function create($idSection, $idNode, $idChannel, $idSyncServer, $idPortalVersion
        , $pubTime, $state, $progress, $fileName, $filePath, $idSync, $idBatch, $idParentServer)
    {
        if ($idSection != null && $idNode != null) {
            $dbObj = new \Ximdex\Runtime\Db();
            $sql = "SELECT * " .
                    "FROM PublishingReport " .
                    "WHERE IdNode = " . $idNode . 
                    " AND IdSyncServer = " . $idSyncServer .
                    " AND IdChannel " . (empty($idChannel) ? "IS NULL" : ("=" .$idChannel)) .
                    " LIMIT 1";
            $dbObj->Query($sql);
            if (!$dbObj->EOF) {
                $updateFields = array(
                    'IdSection' => $idSection,
                    'IdChannel' => empty($idChannel) ? NULL : $idChannel,
                    'IdSyncServer' => $idSyncServer,
                    'IdPortalVersion' => $idPortalVersion,
                    'State' => $state,
                    'Progress' => $progress,
                    'FileName' => $fileName,
                    'FilePath' => $filePath,
                    'IdSync' => $idSync,
                    'IdBatch' => $idBatch,
                    'IdParentServer' => $idParentServer,
                );
                $searchFields = array(
                    'IdNode' => $idNode,
                    'IdSyncServer' => $idSyncServer,
                    'IdChannel' => empty($idChannel) ? NULL : $idChannel
                );
                $this->updateReportByField($updateFields, $searchFields, true);
                return null;
            }
        }
        $this->set('IdReport', null);
        $this->set('IdSection', $idSection);
        $this->set('IdNode', $idNode);
        $this->set('IdChannel', empty($idChannel) ? NULL : $idChannel);
        $this->set('IdSyncServer', $idSyncServer);
        $this->set('IdPortalVersion', $idPortalVersion);
        $this->set('PubTime', time());
        $this->set('State', $state);
        $this->set('Progress', $progress);
        $this->set('FileName', $fileName);
        $this->set('FilePath', $filePath);
        $this->set('IdSync', $idSync);
        $this->set('IdParentServer', $idParentServer);
        $this->set('IdBatch', $idBatch);
        parent::add();
        return null;
    }

    /**
     *  Gets the rows from PublishingReport table which match the values of a list of fields.
     *  @param array arrayFields
     *  @return array|null
     */
    function updateReportByField($updateFields, $searchFields, $fromCreate = false)
    {
        $whereClause = " WHERE TRUE";
        if (is_array($searchFields) && count($searchFields) >= 0) {
            foreach ($searchFields as $fieldName => $fieldValue) {
                if ($this->isField($fieldName)) {
                    $whereClause .= " AND " . $fieldName . (empty($fieldValue) ? " IS NULL " : (" = '" . $fieldValue . "'"));
                }
            }
        }
        $setClause = " ";
        if (is_array($updateFields) && count($updateFields) >= 0) {
            $setClause .= ($fromCreate === false) ? "SET PubTime = PubTime" : ("SET PubTime = " . time());
            foreach ($updateFields as $fieldName => $fieldValue) {
                if ($this->isField($fieldName)) {
                    $setClause .= "," . $fieldName . (empty($fieldValue) ? "=NULL " : (" = '" . $fieldValue . "' "));
                }
            }
        }
        $query = "UPDATE PublishingReport" . $setClause . $whereClause;
        Logger::debug($query);
        Logger::debug(print_r($updateFields,1));
        Logger::debug(print_r($searchFields, 1));
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->execute($query);
        return null;
    }

    function getReports($params)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $sql = "SELECT * " .
                "FROM PublishingReport pr INNER JOIN Nodes n on pr.IdNode = n.IdNode inner join
                 NodeTypes nt on n.IdNodeType = nt.IdNodeType ";
        if ($params['finished']) {
            $sql .= "WHERE State IN ('" . ServerFrame::IN . "','" . ServerFrame::OUT . "')";
        }
        if ($params['idNode'] !== null && $params['idNode'] !== 0) {
            $sql .= " AND IdParentServer = " . $params['idNode'];
        }
        if ($params['idBatch'] !== null && $params['idBatch'] !== 0) {
            $sql .= " AND IdBatch = " . $params['idBatch'];
        }
        
        // Date as unixtime integer
        if ($params['dateFrom'] !== null) {
            $sql .= " AND PubTime >= " . $params['dateFrom'];
        }
        if ($params['dateTo'] !== null) {
            $sql .= " AND PubTime <= " . $params['dateTo'];
        }
        if ($params['searchText'] !== null) {
            $sql .= " AND filename REGEXP '" . $params['searchText'] . "'";
        }

        // Close query
        $sql .= ' LIMIT 100';
        $dbObj->Query($sql);
        $frames = array();
        while (!$dbObj->EOF) {
            $sectionNode = new Node($dbObj->GetValue("IdSection"));
            $batch = new Batch($dbObj->GetValue("IdBatch"));
            $idportal = $dbObj->GetValue("IdPortalVersion");
            if (!isset($frames[$idportal])) {
                $finished = !isset($frames[$idportal]["Finished"]) ? true : $frames[$idportal]["Finished"];
                $frames[$idportal] = array(
                    "IdNodeGenerator" => $dbObj->GetValue("IdSection"),
                    "NodeName" => $sectionNode->get('Name'),
                    "IdBatch" => $dbObj->GetValue("IdBatch"),
                    "BatchPriority" => $batch->get('Priority'),
                    "BatchState" => $batch->get('Playing') == 1,
                    "BatchStateText" => ($batch->get('Playing') == 1) ? _('is active') : _('is stopped'),
                    "Finished" => $finished && $dbObj->GetValue("Progress") == 100,
                    'elements' => array()
                );
            }

            // Calculate estimated time
            $estimatedTime = '';
            if (!$params['finished']) {
                $pubTime = (int) $dbObj->GetValue("PubTime");
                $curTime = time();
                if ($pubTime > $curTime) {
                    $estimatedTime = $pubTime;
                } else {
                    $SECONDS_TO_PUBLISH = 1;
                    $serverFramesTotal = (int) $batch->get('ServerFramesTotal');
                    $serverFramesSucess = (int) $batch->get('ServerFramesSucess');
                    $total = ($serverFramesTotal - $serverFramesSucess) * $SECONDS_TO_PUBLISH;
                    $estimatedTime = $curTime + $total;
                }
            }
            $channelName = "";
            if (!empty($dbObj->GetValue("IdChannel"))) {
                $channel = new \Ximdex\Models\Channel($dbObj->GetValue("IdChannel"));
                $channelName = $channel->GetName();
            }
            $frames[$idportal]['elements'][] = array(
                "IdReport" => $dbObj->GetValue("IdReport"),
                "IdSection" => $dbObj->GetValue("IdSection"),
                "IdNode" => $dbObj->GetValue("IdNode"),
                "IdChannel" => $dbObj->GetValue("IdChannel"),
                "ChannelName" => $channelName,
                "PubTime" => ($params['finished']) ? $dbObj->GetValue("PubTime") : '',
                "EstimatedTime" => $estimatedTime,
                "State" => $dbObj->GetValue("State"),
                "Progress" => $dbObj->GetValue("State") == "Warning" || $dbObj->GetValue("State") == "Error" ? 100 : $dbObj->GetValue("Progress"),
                "FileName" => $dbObj->GetValue("FileName"),
                "FilePath" => $dbObj->GetValue("FilePath"),
                "IdSync" => $dbObj->GetValue("IdSync"),
                "Error" => ($dbObj->GetValue("Progress") != '-1') ? 0 : 1,
            );
            $frames[$idportal]["PubTime"] = $dbObj->GetValue("PubTime");
            if (!isset($frames[$idportal]["EstimatedTime"]) || $frames[$idportal]["EstimatedTime"] < $estimatedTime){
                $frames[$idportal]["EstimatedTime"] = $estimatedTime;
            }
            $dbObj->Next();
        }
        $res = [];
        foreach ($frames as $k => $frame) {
            $frame["IdPortal"] = $k;
            $numSuccess = $numErrors = $numWarnings = 0;
            $numOfElements = count($frame['elements']);
            if ($numOfElements > 0) {
                $acumProgress = 0;
                $acumSuccess = 0;
                $acumWarning = 0;
                $acumErrors = 0;
                foreach ($frame['elements'] as $element) {
                    $acumProgress += (int) $element["Progress"];
                    if ($element['State'] == 'Error') {
                        $acumErrors += (int) $element["Progress"];
                        $numErrors++;
                    } else if ($element['State'] == 'Warning') {
                        $acumWarning += (int) $element["Progress"];
                        $numWarnings++;
                    } else {
                        $acumSuccess += (int) $element["Progress"];
                        $numSuccess++;
                    }
                }
                $frame["Progress"] = $acumProgress/$numOfElements;
                $frame["ProgressSuccess"] = $acumSuccess/$numOfElements;
                $frame["ProgressWarning"] = $acumWarning/$numOfElements;
                $frame["ProgressError"] = $acumErrors/$numOfElements;
                $frame['NumSuccess'] = $numSuccess;
                $frame['NumErrors'] = $numErrors;
                $frame['NumWarnings'] = $numWarnings;
            } else {
                $frame["Progress"] = 0;
                $frame["ProgressSuccess"] = 0;
                $frame["ProgressWarning"] = 0;
                $frame["ProgressError"] = 0;
                $frame['NumSuccess'] = 0;
                $frame['NumErrors'] = 0;
                $frame['NumWarnings'] = 0;
            }
            $res[] = $frame;
        }
        return $res;
    }
}