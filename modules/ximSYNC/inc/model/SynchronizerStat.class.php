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



 ModulesManager::file('/inc/model/orm/SynchronizerStats_ORM.class.php', 'ximSYNC');
 ModulesManager::file('/inc/manager/Sync_Log.class.php', 'ximSYNC');
 ModulesManager::file('/inc/utils.php');

/**
*	@brief Logging for the publication incidences.
*
*	This class includes the methods that interact with the Database.
*/

class SynchronizerStat extends SynchronizerStats_ORM {

	/**
	*  Adds a row to SynchronizerStats table and writes in the log file.
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

	function create($batchId, $nodeFrameId, $channelFrameId, $serverFrameId, $pumperId,
					 $class, $method, $file, $line, $type, $level, $comment, $doInsertSql = false) {


		if(strcmp(\App::getValue( "SyncStats"),"1")==0){

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
				$this->set('Time', mktime());
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
	*  @param int idStat
	*  @return array|null
	*/

    function getStatById($idStat) {

		parent::GenericData($idStat);

		$stat = array();

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

	/**
	*  Gets the rows from SynchronizerStats table which match the values of a list of fields.
	*  @param array arrayFields
	*  @return array|null
	*/

    function getStatByField($arrayFields) {

		$dbObj = new DB();

		$whereClause = " WHERE TRUE";
		if (is_array($arrayFields) && count($arrayFields) >= 0) {

			foreach  ($arrayFields as $fieldName => $fieldValue) {

				if ($this->isField($fieldName)) {

					$whereClause .= " AND " . $fieldName . " = '" . $fieldValue . "'";
				}
			}
		}

		$query = "SELECT IdStat, BatchId, NodeFrameId, ChannelFrameId, ServerFrameId, " .
				 "PumperId, Class, Method, File, Line, Type, Level, Time, Comment " .
				 "FROM SynchronizerStats" . $whereClause;

		$i = 0;
		$dbObj->Query($query);
		if ($dbObj->numRows == 0) {

		    return null;
		}

		$stats = array();

		while (!$dbObj->EOF) {

		    $stats[$i]['IdStat'] = $dbObj->GetValue("IdStat");
		    $stats[$i]['BatchId'] = $dbObj->GetValue("BatchId");
		    $stats[$i]['NodeFrameId'] = $dbObj->GetValue("NodeFrameId");
		    $stats[$i]['ChannelFrameId'] = $dbObj->GetValue("ChannelFrameId");
		    $stats[$i]['ServerFrameId'] = $dbObj->GetValue("ServerFrameId");
		    $stats[$i]['PumperId'] = $dbObj->GetValue("PumperId");
		    $stats[$i]['Class'] = $dbObj->GetValue("Class");
		    $stats[$i]['Method'] = $dbObj->GetValue("Method");
		    $stats[$i]['File'] = $dbObj->GetValue("File");
		    $stats[$i]['Line'] = $dbObj->GetValue("Line");
		    $stats[$i]['Type'] = $dbObj->GetValue("Type");
		    $stats[$i]['Level'] = $dbObj->GetValue("Level");
		    $stats[$i]['Time'] = $dbObj->GetValue("Time");
		    $stats[$i]['Comment'] = $dbObj->GetValue("Comment");
		    $i++;
		    $dbObj->Next();
		}

		return $stats;
    }

}
?>