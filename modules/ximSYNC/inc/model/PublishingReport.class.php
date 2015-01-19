<?php

/* * ****************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision:$                                                       *
 *                                                                            *
 *                                                                            *
 * **************************************************************************** */



ModulesManager::file('/inc/model/orm/PublishingReport_ORM.class.php', 'ximSYNC');

/**
 * 	@brief CRUD for Publishing Reports data.
 *
 * 	This class includes the methods that interact with the Database.
 */
class PublishingReport extends PublishingReport_ORM {

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
        'In' => '100',
        'Out' => '100',
        'Canceled' => '100',
        'Replaced' => '100',
        'Removed' => '100'
    );

    function create($idSection, $idNode, $idChannel, $idSyncServer, $idPortalVersion
    , $pubTime, $state, $progress, $fileName, $filePath, $idSync, $idBatch, $idParentServer) {

        if ($idSection != null && $idNode != null) {
            $dbObj = new DB();
            $sql = "SELECT * " .
                    "FROM PublishingReport " .
                    "WHERE IdSection = " . $idSection . " AND IdNode = " . $idNode . " " .
                    "LIMIT 1";
            $dbObj->Query($sql);
            if (!$dbObj->EOF) {
                $updateFields = array(
                    'IdChannel' => $idChannel,
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
                    'IdSection' => $idSection,
                    'IdNode' => $idNode
                );
                $this->updateReportByField($updateFields, $searchFields, true);
                return null;
            }
        }

        $this->set('IdReport', null);
        $this->set('IdSection', $idSection);
        $this->set('IdNode', $idNode);
        $this->set('IdChannel', $idChannel);
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
    function updateReportByField($updateFields, $searchFields, $fromCreate = false) {

        $whereClause = " WHERE TRUE";
        if (is_array($searchFields) && count($searchFields) >= 0) {
            foreach ($searchFields as $fieldName => $fieldValue) {
                if ($this->isField($fieldName)) {
                    $whereClause .= " AND " . $fieldName . " = '" . $fieldValue . "'";
                }
            }
        }

        $setClause = " ";
        if (is_array($updateFields) && count($updateFields) >= 0) {
            $setClause .= ($fromCreate === false) ? "SET PubTime = PubTime" : "SET PubTime = " . time();
            foreach ($updateFields as $fieldName => $fieldValue) {
                if ($this->isField($fieldName)) {
                    $setClause .= "," . $fieldName . " = '" . $fieldValue . "' ";
                }
            }
        }

        $query = "UPDATE PublishingReport" . $setClause . $whereClause;
        $dbObj = new DB();
        $dbObj->execute($query);

        return null;
    }

    function getReportByIdNode($idNodeGenerator = null) {
        $extraWhereClause = ($idNodeGenerator !== null) ? "AND IdParentServer = '" . $idNodeGenerator . "' " : "";

        $dbObj = new DB();
        $sql = "SELECT * " .
                "FROM PublishingReport " .
                "WHERE State NOT IN ('Replaced', 'Removed') " .
                $extraWhereClause; // . "ORDER BY IdPortalVersion DESC, FilePath ASC, FileName ASC LIMIT 100";
        $dbObj->Query($sql);

        $frames = array();
        while (!$dbObj->EOF) {
            $sectionNode = new Node($dbObj->GetValue("IdSection"));
            $batch = new Batch($dbObj->GetValue("IdBatch"));
            $idportal = $dbObj->GetValue("IdPortalVersion");

            if (!isset($frames[$idportal])) {
                $frames[$idportal] = array(
                    "IdNodeGenerator" => $dbObj->GetValue("IdSection"),
                    "NodeName" => $sectionNode->get('Name'),
                    "IdBatch" => $dbObj->GetValue("IdBatch"),
                    "BatchPriority" => $batch->get('Priority'),
                    "BatchState" => $batch->get('Playing') == 1,
                    "BatchStateText" => ($batch->get('Playing') == 1) ? 'activa' : 'detenida',
                    'elements' => array()
                );
            }

            $frames[$idportal]['elements'][] = array(
                "IdSection" => $dbObj->GetValue("IdSection"),
                "IdNode" => $dbObj->GetValue("IdNode"),
                "IdChannel" => $dbObj->GetValue("IdChannel"),
                "PubTime" => ($dbObj->GetValue("Progress") != '100') ? time() - $dbObj->GetValue("PubTime") : $dbObj->GetValue("PubTime"),
                "State" => $dbObj->GetValue("State"),
                "Progress" => $dbObj->GetValue("Progress"),
                "FileName" => $dbObj->GetValue("FileName"),
                "FilePath" => $dbObj->GetValue("FilePath"),
                "IdSync" => $dbObj->GetValue("IdSync"),
                "Error" => ($dbObj->GetValue("Progress") != '-1') ? 0 : 1,
            );

            $dbObj->Next();
        }

        return $frames;
    }

}

?>