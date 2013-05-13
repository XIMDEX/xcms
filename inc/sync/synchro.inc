<?php 

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    		      *
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
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/










/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../"));

include_once(XIMDEX_ROOT_PATH . "/inc/utils.inc");
include_once(XIMDEX_ROOT_PATH . "/inc/modules/ModulesManager.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/fsutils/FsUtils.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/model/orm/Synchronizer_ORM.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/model/orm/SynchronizerHistory_ORM.class.php");
include_once(XIMDEX_ROOT_PATH . "/inc/repository/nodeviews/View_ChannelFilter.class.php");

ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');

class Synchronizer {

	var $dbObj;
	var $config;
	var $nodeID;
	var $frameID;
	var $numErr;			// Error code.
	var $msgErr;			// Error message.
	var $errorList= array(	// Class error list.
	1 => 'The node does not exist',
	2 => 'Database connection error',
	3 => 'Error accessing to file system',
	4 => 'A publication window already exists in this interval',
	5 => 'Publication window does not exist',
	6 => 'Generation error',
	7 => 'Expiration date should be later than publication date',
	8 => 'Frames cannot overlap',
	9 => 'Document language is not enabled'
	);



	/// *************************************************************************
	/// CONSTRUCTOR METHODS, GET AND SET OF THE WINDOW LOADED IN THE OBJECT
	/// *************************************************************************

	/// Construct
	function Synchronizer($nodeID = null)
	{
		$this->dbObj = new DB();
		$this->config = new Config();
		if(!is_null($nodeID))
		$this->SetID($nodeID);
		$this->errorList[1]=_('The node does not exist');
		$this->errorList[2]=_('Database connection error');
		$this->errorList[3]=_('Error accessing to file system');
		$this->errorList[4]=_('A publication window already exists in this interval');
		$this->errorList[5]=_('Publication window does not exist');
		$this->errorList[6]=_('Generation error');
		$this->errorList[7]=_('Expiration date should be later than publication date');
		$this->errorList[8]=_('Frames cannot overlap');
		$this->errorList[9]=_('Document language is not enabled');
	}

	/// Function which returns the id of the node we're working with
	function GetID()
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			return $this->nodeID;
		} else {
			$this->SetError(1);
		}
	}

	/// Function which changes the node which we're working with
	function SetID($nodeID = null)
	{
		$this->ClearError();
		$this->nodeID = $nodeID;
	}


	/// *************************************************************************



	/// *************************************************************************
	/// Window creation and deletion methods
	/// *************************************************************************

	// Function which create a publication window and all its siblings, if they exist

	function CreateFrame($dateUp, $dateDown, $markEnd = NULL, $linked = NULL, $createWorkFlow = true) {
		$node_id = $this->nodeID;

		XMD_Log::info(sprintf(_("nodeID %s dateUp %s dateDown %s markEnd %s linked %s"), $node_id, $dateUp, $dateDown, $markEnd, $linked));

		$ret_val = array();

		if (!($this->nodeID > 0)) {
			XMD_Log::error(_('Void node'));
			return NULL;
		}

		$targetPath = Config::GetValue("AppRoot") . Config::GetValue("SyncRoot");

		if ($markEnd && $this->HasUnlimitedLifeTime()) {

			$lastFrame = $this->GetLastFrame();

			if ($lastFrame) {
				$this->SetDateDownOnFrame($lastFrame, $dateUp);
			}
		}

		
		if ($this->HasFrameOnInterval($dateUp, $dateDown)) {
			XMD_Log::error(_('There is a frame in this interval'));
			return NULL;
		}


		$node = new Node($this->nodeID);
		$name = $node->get('Name');
		$server = $node->GetServer();
		$relativePath = $node->GetPublishedPath();

		$nodeServer = new Node($server);

		if (Config::getValue('PublishOnDisabledServers') == 1) {
			$physicalServers = $nodeServer->class->GetPhysicalServerList(true);
		} else {
			$physicalServers = $nodeServer->class->GetEnabledPhysicalServerList(true);
		}

		if (is_null($physicalServers)) {
			XMD_Log::error(_('Any physical server available'));
			return NULL;
		}

		// Anyway the node may need to generate some extra nodes and publish them
		if ($node->nodeType->get('isGenerator')) {
			$generatedNodes = $node->class->generator();
			if (!empty($generatedNodes)) {
				foreach ($generatedNodes as $idGeneratedNode) {
					$sync = new Synchronizer($idGeneratedNode);
					$sync->CreateFrame($dateUp, $dateDown, $markEnd, $linked);
				}
			}
		}
		
		if ($node->nodeType->get('IsStructuredDocument')) {

			$doc = new StructuredDocument($this->nodeID);
			$lang_id = $doc->GetLanguage();

			$lang_doc = new Language();

			if (!$lang_doc->LanguageEnabled($lang_id)) {
				XMD_Log::error(_('Disabled language'));
				return NULL;
			}

			// Call to dexT

			$generations = $node->class->GetAllGenerations();
			foreach ($generations as $contents) {

				if (empty($contents['content'])) {
					XMD_Log::error(_("Generation error for channel")." {$contents['channel']}");
					foreach($node->class->messages as $message) {
						XMD_Log::error($message['message']);
					}
					continue;
				}

				$channel = new Channel($contents['channel']);
				$publishedName = $node->GetPublishedNodeName($contents['channel']);

				foreach ($physicalServers as $serverId) {

					if ($nodeServer->class->HasChannel($serverId, $contents['channel'])) {

						$query = "INSERT INTO Synchronizer (IdSync, IdServer, IdNode, IdChannel, DateUp, DateDown, 
							RemotePath, FileName, Linked) VALUES (NULL, $serverId, ".$node->get('IdNode').", 
							".$contents['channel'].", $dateUp, ".DB::sqlEscapeString($dateDown).", '$relativePath', 
							'$publishedName', '$linked')";
						$this->dbObj->Execute($query);

						$newID = $this->dbObj->newID;

						$filePath = $targetPath . "/" . $newID;

						$ret_val[] = $newID;

						FsUtils::file_put_contents($filePath, $contents['content']);
						$this->ParseDependencies($newID, $contents['content']);

						
						// Sends bulletin by mail
						
						if ($node->nodeType->get('Name') == 'XimNewsBulletinLanguage') {
							$ximNewsBulletin = new XimNewsBulletin($this->nodeID);
							$ximNewsBulletin->set('State', 'published');
							$ximNewsBulletin->update();
							$colectorId = $ximNewsBulletin->get('IdColector');

							$ximNewsColector = new XimNewsColector($colectorId);

							if ($contents['channel'] == $ximNewsColector->get('MailChannel')) {
								$this->AssociateFrameBulletin($newID, $this->nodeID);
							}
						}

					}
				}
			}
			
		} else {

			// Don't creates cache for not structured documents

			foreach ($physicalServers as $serverId) {

				$query = "INSERT INTO Synchronizer (IdSync, IdServer, IdNode, IdChannel, DateUp, DateDown, 
				RemotePath, FileName, Linked) VALUES (NULL, $serverId, ".$node->get('IdNode').", NULL, 
				".$dateUp.", ".DB::sqlEscapeString($dateDown).", '".$relativePath."', '".$name."','$linked')";
				$this->dbObj->Execute($query);
			}
		}

		// Repeats process for each workflow slave

		if ($createWorkFlow) {
			$workFlowSlaves = $node->GetWorkFlowSlaves();

			if (!is_null($workFlowSlaves)) {
				foreach($workFlowSlaves as $slave) {
					$sync = new Synchronizer($slave);
					$sync->CreateFrame($dateUp, $dateDown, $markEnd, $linked);
				}
			}
		}
		
		// return IdSync
		return $ret_val;
	}

	/// Function which updates de Dependencies table in a window from its id and content
	function ParseDependencies($frameID, $content)
	{
		/// Looking for content dependencies, directly the ids
		//XMD_Log::info("Extracting dependencies for FRAME $frameID with content $content");
		preg_match_all("/@@@RMximdex\.pathto\(([0-9,]+)\)@@@/i", $content, $contentTags);
		$deps = $contentTags[sizeof($contentTags)-1];

		/// And it is built again
		foreach($deps as $depID)
		{
			$pair = split(",", $depID);
			$depID = $pair[0];

			if (array_key_exists(1, $pair)) { 
				$channelID = $pair[1]; 
			} else { 
				$channelID = NULL; 
			} 

			$this->dbObj->Execute("INSERT INTO SynchronizerDependencies (IdSync, IdResource) VALUES (".$frameID.", ".$depID.")");

			XMD_Log::info("channel: $channelID frameID: $frameID depID: $depID");
		}
	}


	/// Delete a frame, and its sibling if exists
	function DeleteFrame($frameID, $unPublish = false)
	{

		$this->ClearError();
		if(!is_null($this->nodeID)) {
			if($frameID) {
				$conf = new Config();
				$targetPath = $conf->GetValue("AppRoot") . $conf->GetValue("SyncRoot");

				$sql = "SELECT IdNode,DateUp,DateDown FROM Synchronizer WHERE IdSync=".$frameID;
				$this->dbObj->Query($sql);
				$nodeID = $this->dbObj->GetValue("IdNode");
				$dateUp = $this->dbObj->GetValue("DateUp");
				$dateDown = $this->dbObj->GetValue("DateDown");

				$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$nodeID." AND DateUp=".$dateUp." AND DateDown=".$dateDown;
				$this->dbObj->Query($sql);

				$list = array();
				while(!$this->dbObj->EOF)
				{
					$list[] = $this->dbObj->GetValue("IdSync");
					$this->dbObj->Next();
				}

				foreach($list as $frameID)
				{
					if ($unPublish == true) {
						$state = $serverFrame->get('State');

						// Don't deletes frame if have to unpublish document

						if ($state == 'In') {
							XMD_log::info("Don't delete serverFrame $frameID - settig Due2Out for unpublish");
							
							$dbUp = new DB();
							$sql = "UPDATE Synchronizer SET State = 'Due2Out' WHERE IdSync = $frameID";
							$dbUp->Execute($sql);

							continue;
						}
					}

					$sql = "DELETE FROM Synchronizer WHERE IdSync=".$frameID;
					$this->dbObj->Execute($sql);
					$sql = "DELETE FROM SynchronizerDependencies WHERE IdSync=".$frameID;
					$this->dbObj->Execute($sql);
					$filePath = $targetPath . "/" . $frameID;
					FsUtils::delete($filePath);
				}

				/// Por cada nodo que este atado al wf de este nodo, repetimos la accion
				$node = new Node($nodeID);
				$workFlowSlaves = $node->GetWorkFlowSlaves();
				foreach($workFlowSlaves as $slave)
				{
					$sync = new Synchronizer($slave);
					$idSync = $sync->GetFrameOnDate($dateDown-1);
					$sync->DeleteFrame($idSync, $unPublish);
				}
			} else {
				$this->SetError(5);
			}
		} else {
			$this->SetError(1);
		}
	}



	function BorrarSolapas($nodeID,$up,$down,$markEnd=null) {

		$node = new Node($nodeID);

		$sync=New Synchronizer($nodeID);
		$solapa=$sync->CoincidentFramesOnInterval($up,$down);

		$n_outdated = 0;

		if (count($solapa)>0)   {

			$timeline=time();

			$sync = new Synchronizer($nodeID);

			for($i=0;$i<count($solapa);$i++) {

				$dateUp=$sync->GetDateUpOnFrame($solapa[$i]);
				$dateDown=$sync->GetDateDownOnFrame($solapa[$i]);


				if ($dateUp>$up) {
					$sync->SetState($solapa[$i],"OUTDATED");
					$n_outdated++;


				} elseif($dateUp==$up && $dateDown==$down) {
					$sync->SetState($solapa[$i],"OUTDATED");
					$n_outdated++;
				} elseif ($dateUp<$up) {
					if ($markEnd) {
						// Setting state to OUTDATED after cutting the frame
						// to avoid its expiration, because it is assumed that it is a re-publication
						$sync->SetState($solapa[$i],"OUTDATED");
						$n_outdated++;
						$sync->SetDateDownOnFrame($solapa[$i], $up);
					} else {
						$sync->SetState($solapa[$i],"OUTDATED");
						$n_outdated++;
					}
				}
			}

		}

		$workFlowSlaves = $node->GetWorkFlowSlaves();

		if($workFlowSlaves)  {

			foreach ($workFlowSlaves as $slave) {

				$sync=New Synchronizer($slave);
				$solapa=$sync->CoincidentFramesOnInterval($up,$down);

				if (count($solapa)>0)   {
					$timeline=time();

					for($i=0;$i<count($solapa);$i++) {

						$dateUp=$sync->GetDateUpOnFrame($solapa[$i]);
						$dateDown=$sync->GetDateDownOnFrame($solapa[$i]);


						if ($dateUp>$up) {

							$sync->SetState($solapa[$i],"OUTDATED");
							$n_outdated++;
						} elseif ($dateUp<$up) {
							if ($markEnd) {
								// Setting state to OUTDATED after cutting the frame
								// to avoid its expiration, because it is assumed that it is a re-publication
								$sync->SetState($solapa[$i],"OUTDATED");
								$n_outdated++;
								$sync->SetDateDownOnFrame($solapa[$i], $up);
							} else {
								$sync->SetState($solapa[$i],"OUTDATED");
								$n_outdated++;
							}
						}
					}
				}
			}

			return $n_outdated;
		}
	}






	//Function which associates a a bulletin element frame to its version

	function AssociateFrameVersion($frameID, $versionID) {

		$sql="INSERT INTO XimNewsFrameVersion (IdSync, IdVersion) VALUES ($frameID,$versionID)";


		$this->dbObj->Execute($sql);


	}

	//Function which returns a node version, from its publication frame

	function GetVersion($frameID) {
		$sql="SELECT v.Version, v.SubVersion FROM Synchronizer s"
			. " INNER JOIN Versions v ON s.IdNode = v.Idnode"
			. " WHERE s.IdSync=$frameID";

		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			$version["version"] = $this->dbObj->GetValue("Version");
			$version["subversion"] = $this->dbObj->GetValue("SubVersion");
			$this->dbObj->Next();
		}
		return $version;

	}

	//Function which associates a bulletin element frame to its bulletin

	function AssociateFrameBulletin($frameID,$bulletinID) {

		$sql="INSERT INTO XimNewsFrameBulletin (IdSync, BulletinID) VALUES ($frameID,$bulletinID)";

		$this->dbObj->Execute($sql);
	}

	// jmcarrasco ximnews, I thik the GetBulletin has an error and should be usesd GetBulletin2. Leaving it like this for the moment

	function GetBulletin2($frameID) {

		$sql="SELECT BulletinID FROM XimNewsFrameBulletin WHERE IdSync=$frameID";
		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			$list[] = $this->dbObj->GetValue("BulletinID");

			$this->dbObj->Next();
		}
		return $list;

	}

	//Function which returns all the frames depending on a bulletin

	function GetAssociatedFrames($bulletinID) {

		$sql="SELECT IdSync FROM XimNewsFrameBulletin WHERE BulletinID=$bulletinID";
		$this->dbObj->Query($sql);

		$list = array();
		$i=0;
		while(!$this->dbObj->EOF)
		{
			$list[$i++]["idsync"] = $this->dbObj->GetValue("IdSync");

			$this->dbObj->Next();
		}
		return $list;

	}


	/// *************************************************************************



	/// *************************************************************************
	/// FUNCTIONS FOR DEPENDENCY MANAGEMENT
	/// *************************************************************************

	/// Creating a definitive temporal file for synchornizer, it will pass to publication


	/// Returns the list of dependencies of the given frame
	/// Class method, it does not require a previous SetID.
	// omg, TODO review (what is it doing?)
	function GetFrameDependencies($frameID)
	{
		$this->ClearError();
		if(is_null($this->nodeID)) {
			$this->SetError(1);
		}
	}

	/// Function which returns if all the dependencies have frames in the given frame interval
	/// Object method, it requieres a previous SetID.
	function HasResolvedDependencies($frameID)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			$unsolvedDeps = array();
			$deps = $this->GetFrameDependencies($frameID);
			foreach($deps as $depID);
			if(!$this->HasSimultaneousFrames($frameID, $targetID))
			$unsolvedDeps[] = $depID;

			if(!sizeof($unsolvedDeps)) {
				return $unsolvedDeps;
			} else {
				return false;
			}
		} else {
			$this->SetError(1);
		}
	}

	/// Function which returns if the destiny node has frames simultaneous to the given one
	/// Object method, it does not requiere a previous SetID.
	function HasSimultaneousFrames($frameID, $targetID)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			$target = new Synchronizer($targetID);
			$startDate = $this->GetDateUpOnFrame($frameID);
			$endDate	 = $this->GetDateDownOnFrame($frameID);
			/// If our frame is indefined, loogin for gaps before the last frame of the destiny node
			/// And the destiny node last frame should be indefined too
			if(!$endDate)
			{
				$lastFrame = $target->GetLastFrame();
				$endDate = $target->GetDateUpOnFrame($lastFrame);
				$target->GetGapsBetweenDates($startDate, $endDate);
				if(!sizeof($target->GetGapsBetweenDates($startDate, $endDate)) && !$target->GetDateDownOnFrame($lastFrame)) {
					return true;
				} else {
					return false;
				}
			} else {	/// If our frame has limits, looking for gaps in the destiny node, between our frame validity frames
				$target->GetGapsBetweenDates($startDate, $endDate);
				if(!sizeof($target->GetGapsBetweenDates($startDate, $endDate))) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			$this->SetError(1);
		}
	}

	/// *************************************************************************


	/// *************************************************************************
	/// FUNCTIONS FOR FRAME COVERING AND QUERIES ABOUT THE TIMELINE FOR A NODE
	/// *************************************************************************


	/// Function which retrieves a gap between two frames, with format (gap init, gap end, duration)
	/// Class method, it does not requiere a previous SetID.
	function GetDistanceBetweenFrames($frameA, $frameB)
	{
		$sql= "Select DateDown FROM Synchronizer WHERE IdSync=".$frameA;
		$this->dbObj->Query($sql);
		$down = $this->dbObj->GetValue("DateDown");

		$sql= "Select DateUp FROM Synchronizer WHERE IdSync=".$frameB;
		$this->dbObj->Query($sql);
		$up = $this->dbObj->GetValue("DateUp");

		return array( $down, $up, $up-$down);
	}

	/**
	 * Function which returns the intervals between to dates in which there is any node frame.
	 * They are the gaps in which the new frame could be stablished.
	 * It is returned an array of arrays with (gap init, gap end, duration)
	 *
	 * @param timestamp $dateUp
	 * @param timestamp $dateDown
	 * @return array
	 */

	function GetGapsBetweenDates($dateUp,$dateDown)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			$gapTolerance = Config::GetValue("MaximunGapSizeTolerance");
			$gaps=array();



			/// Looking for gaps between publication frames
			$list = $this->GetFramesBetweenDates($dateUp, $dateDown);

			if(sizeof($list)>=1)
			for($i=0; $i<sizeof($list); $i++)
			{
				if($i == 0)

				$startDate = $this->GetDateUpOnFrame($list[$i]);

				if($i == sizeof($list)-2)
				$endDate = $this->GetDateDownOnFrame($list[$i+1]);


				$b = $this->GetDistanceBetweenFrames($list[$i], $list[$i+1]);
				if(is_array($b) && $b[2] > $gapTolerance)
				{
					$gaps[] = $b;
				}
			}

			/// If there is some gap before the first frame, we add it
			if(isset($startDate) && $startDate>$dateUp)
			{
				$firstGap = array($dateUp, $startDate, $startDate-$dateUp);
				if($firstGap[2] > $gapTolerance)
				array_unshift($gaps,$firstGap);
			}

			/// If there is some gap after the last frame, we add it
			if(isset($endDate) && $endDate<$dateDown)
			{
				$lastGap = array($endDate, $dateDown, $dateDown-$endDate);
				if($lastGap[2] > $gapTolerance)
				$gaps[] = $lastGap;
			}

			return($gaps);
		} else {
			$this->SetError(1);
		}
	}

	/// Function which look for all the fram which are parcially or totally between to dates
	/// Object method, it requieres a previous SetID.
	function GetFramesBetweenDates($dateUp,$dateDown)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{

			$node = new Node($this->nodeID);
			$sql = "SELECT IdSync, IdChannel FROM Synchronizer WHERE IdNode=".$this->nodeID." AND ((DateUp BETWEEN ".$dateUp." AND ".$dateDown.") OR (DateDown BETWEEN ".$dateUp." AND ".$dateDown.")) AND (DateDown>".time()." OR DateDown IS NULL) ORDER BY DateUp";

			$this->dbObj->Query($sql);

			$list = array();
			/// Listing just the one related to one channel, if there are more
			while(!$this->dbObj->EOF)
			{
				if(!isset($channel))
				$channel = $this->dbObj->GetValue("IdChannel");
				if(isset($channel) && $this->dbObj->GetValue("IdChannel") == $channel)
				$list[] = $this->dbObj->GetValue("IdSync");
				if (!isset($channel))
				//Added due to bulletins. They have no channel, but we need its IdSync...
				$list[] = $this->dbObj->GetValue("IdSync");


				$this->dbObj->Next();
			}
			return $list;
		} else {
			$this->SetError(1);
		}
	}

	/// Function which returns the list of all frames of a node
	/// Object method, it does not require a previous SetID.
	function GetFrameList($dateUp,$dateDown=null) {
		$this->ClearError();
		$salida = null;
		if(!is_null($this->nodeID)) { 

			if($dateDown) {
				$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND ((DateDown BETWEEN ".($dateUp+1)." AND ".($dateDown-1).") OR (DateUp BETWEEN ".($dateUp+1)." AND ".($dateDown-1)." OR (DateUp<".$dateUp." AND DateDown IS NULL)))";
			} else {
				$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND (DateDown>".($dateUp+1)." OR DateDown IS NULL)";
			}

			$this->dbObj->Query($sql);
			if ($this->dbObj->numErr != 0) {
				$this->SetError(2);
				return null;
			} else {   // Catchking the entries which match with the condition
				$i=0;
				while (!$this->dbObj->EOF) {
					$salida[$i++] = $this->dbObj->row["IdSync"];
					$this->dbObj->Next();
				}
			}
		} else {
			$this->SetError(1);
		}
		return $salida;    // returns null if there is no frame
	}


	/// Function which returns the list of frames in PUBLICATION of a node
	/// Object method, it des not require a previous SetID.
	function GetPublishedFrameList() {
		$this->ClearError();
		$salida = null;
		if(!is_null($this->nodeID)) { 

			$timeline=time();
			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND State <> 'OUTDATED' AND State <> 'OUT' AND (DateDown>$timeline OR DateDown IS NULL ) ORDER BY DateUp";


			$this->dbObj->Query($sql);
			if ($this->dbObj->numErr != 0) {
				$this->SetError(2);
				return null;
			} else {
				$i=0;
				while (!$this->dbObj->EOF) {
					$salida[$i++] = $this->dbObj->row["IdSync"];
					$this->dbObj->Next();
				}
			}
		} else {
			$this->SetError(1);
		}
		return $salida;    // returns null if there is no frame
	}

	// Function which deletes all the publication frames from a determined date
	// If this date is not provided, all frames from present will be deleted
	function DeleteFramesFromNow ($nodeid, $ximIO = null,$dateDown=null) {

		$node = new Node($nodeid);
		$type = $node->nodeType->GetName();
		$publicable = $node->nodeType->get("IsPublicable");

		if(!$dateDown) {
			$timeline = time();
		} else {
			$timeline=$dateDown;
		}
		if ($publicable) {
			if ($ximIO) print "Processing node id:$nodeid, type:$type; with present=$timeline\n";
			$sync = new Synchronizer($nodeid);
			$frames = $sync->GetFrameList($timeline, null);
			if ($frames)
			foreach ($frames as $frame) {
				$dateup = $sync->GetDateUpOnFrame($frame);
				if ($ximIO) print "  Frame id:$frame, up:$dateup ";
				if ($dateup <= $timeline) {
					if ($ximIO) print "<= present -> Downdate set to present. \n";
					$sync->SetDateDownOnFrame($frame, $timeline);
				} else {
					if ($ximIO) print ">  present -> Downdate set to present, ";
					$sync->SetDateDownOnFrame($frame, $timeline);
					if ($ximIO) print "removing frames in the future\n";
					$sync->DeleteFrame($frame);
				}
			} else {
				if ($ximIO) print "No frames for this node id\n";
			}
			return count($frames); // All was ok
		} else {
			return null; //no publishable
		}
	}


	/// Function which returns the first frames, or one of its sibling
	/// Object method, it requieres previous SetID.
	function GetFirstFrame()
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." ORDER BY DateUp";
			$this->dbObj->Query($sql);
			return $this->dbObj->GetValue("IdSync");
		} else {
			$this->SetError(1);
		}
	}

	/// Function which returns the last frame, or one of its sibling
	/// Object method, it requieres previous SetID.
	function GetLastFrame($IdServer=null)
	{

		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND IdServer=$IdServer ORDER BY DateUp DESC";

			$this->dbObj->Query($sql);
			if($this->dbObj->numRows == 0){
			    return NULL;
			}

			return $this->dbObj->GetValue("IdSync");
		} else {
			$this->SetError(1);
		}
	}

	function GetLastFrameBulletin()
	{
		$this->ClearError();
		$timeline=time();
		if(!is_null($this->nodeID))
		{
			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID."  AND State <> 'OUTDATED' AND State <> 'OUT' AND (DateDown>$timeline OR DateDown IS NULL ) ORDER BY DateUp DESC";

			$this->dbObj->Query($sql);
			
			if($this->dbObj->numRows == 0){
			    return NULL;
			}

			return $this->dbObj->GetValue("IdSync");
		} else {
			$this->SetError(1);
		}
	}





	/// Funtion which returns a frame prior to the current one, or one of its siblings
	/// Class method, it does not requiere previous SetID.
	function GetPreviousFrame($frameID)
	{
		$this->ClearError();
		if($frameID)
		{
			$sql = "SELECT IdNode, DateUp FROM Synchronizer WHERE IdSync=".$frameID;
			$this->dbObj->Query($sql);
			$prevTime = $this->dbObj->GetValue("DateUp");
			$nodeID = $this->dbObj->GetValue("IdNode");

			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$nodeID." AND DateDown<".$prevTime." ORDER BY DateDown DESC";
			$this->dbObj->Query($sql);
			//echo $this->dbObj->GetValue("IdSync");
			return $this->dbObj->GetValue("IdSync");
		} else {
			$this->SetError(5);
		}

	}

	/// Funtion which returns the next frame (from a given one), or one of its siblings
	/// Class method, it does not requiere previous SetID.
	function GetNextFrame($frameID)
	{
		$this->ClearError();
		if($frameID)
		{
			$sql = "SELECT IdNode, DateDown FROM Synchronizer WHERE IdSync=".$frameID;
			$this->dbObj->Query($sql);
			$nextTime = $this->dbObj->GetValue("DateDown");
			$nodeID = $this->dbObj->GetValue("IdNode");

			if(!$nextTime)
			return null;

			$sql = "SELECT IdSync FROM Synchronizer WHERE DateUp>".$nextTime." AND IdNode=".$nodeID." ORDER BY DateUp";
			$this->dbObj->Query($sql);
			return $this->dbObj->GetValue("IdSync");
		} else {
			$this->SetError(5);
		}

	}

	/// Function which returns the current frame id, or one of its siblings
	/// Object method, it requieres previous SetID.

	function GetCurrentFrame($channelID=null)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			if(!$channelID) {
				$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND (DateUp<".time()." AND (DateDown>".time()." OR DateDown IS NULL) AND State!='OUT' AND State!='OUTDATED')";
			} else {
				$sql = "SELECT IdSync FROM Synchronizer WHERE  IdNode=".$this->nodeID." AND (IdChannel=".DB::sqlEscapeString($channelID)." OR IdChannel IS NULL) AND (DateUp<".time()." AND (DateDown>".time()." OR DateDown IS NULL) AND State!='OUT' AND State!='OUTDATED')";
			}

			$this->dbObj->Query($sql);
			if (!($this->dbObj->numRows > 0)) {
				return NULL;
			}
			return $this->dbObj->GetValue("IdSync");
		} else {
			$this->SetError(1);
		}
	}

	/// Function which returns the current frame id, or one of its siblings
	/// Object method, it requieres previous SetID.
	function GetCurrentPublicatedFrame()
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND State='IN'";

			$this->dbObj->Query($sql);
			return ($this->dbObj->numRows > 0) ? $this->dbObj->GetValue("IdSync") : NULL;
		} else {
			$this->SetError(1);
		}
	}

	/// Function which returns the frame id in the given date (by default, current one) or one of its siblings
	/// Object method, it requieres previous SetID.
	function GetFrameOnDate($time=null)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			if(!$time)
			$time=time();
			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND DateUp>".$time." AND (DateDown<".time()." OR DateDown IS NULL)";
			$this->dbObj->Query($sql);
			return $this->dbObj->GetValue("IdSync");
		} else {
			$this->SetError(1);
		}
	}

	/// Function which returns if there is some frame defined for a given interval. It returns all its siblings
	/// Object method, it requieres previous SetID.
	function HasFrameOnInterval($dateUp,$dateDown=null)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			if($dateDown) {
				$this->dbObj->Query("SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND State <> 'OUTDATED' AND State <> 'OUT' AND ((DateDown BETWEEN ".($dateUp+1)." AND ".($dateDown-1).") OR (DateUp BETWEEN ".($dateUp+1)." AND ".($dateDown-1)." OR (DateUp<".$dateUp." AND DateDown IS NULL)) OR (DateUp<".$dateUp." AND DateDown>".$dateUp."))");
			} else {
				$this->dbObj->Query("SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND State <> 'OUTDATED' AND State <> 'OUT' AND (DateDown>".($dateUp+1)." OR DateDown IS NULL)");
			}
			return $this->dbObj->numRows;
		} else {
			$this->SetError(1);
		}
	}

	//As in HasFramOnInterval, but returning the data. 

	function CoincidentFramesOnInterval($dateUp,$dateDown=null)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			if($dateDown) {

				$this->dbObj->Query("SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID."  AND State <>'OUTDATED' AND State <> 'OUT' AND ((DateUp=".$dateUp." AND DateDown=".$dateDown.") OR (DateDown BETWEEN ".($dateUp+1)." AND ".($dateDown-1).") OR (DateUp BETWEEN ".($dateUp+1)." AND ".($dateDown-1)." OR (DateUp<".$dateUp." AND DateDown IS NULL)) OR (DateUp<".$dateUp." AND DateDown>".$dateUp.")) ORDER BY DateUp");
			} else {

				$this->dbObj->Query("SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND State <>'OUTDATED' AND State <> 'OUT' AND (DateDown>".($dateUp+1)." OR DateDown IS NULL) ORDER BY DateUp");
			}
			$i=0;
			while (!$this->dbObj->EOF) {
				$salida[$i++] = $this->dbObj->row["IdSync"];
				$this->dbObj->Next();
			}
			return isset($salida) ? $salida : NULL;
		} else {
			$this->SetError(1);
		}
	}

	function IsPublished() {
		$dbObj = new DB();

		$sql= "SELECT IdSync FROM Synchronizer WHERE IdNode=".$this->nodeID." AND STATE = 'DUE'";

		$dbObj->Query($sql);

		if ($dbObj->numRows > 0) {
			return true;
		} else {
			return false;
		}
	}


	/// Function which copies a node timeline to other one, overwritting the pre-existing one.
	/// Object method, it requieres previous SetID.
	function CopyTimeLineFromNode($node)
	{
		$this->ClearError();
		if(!is_null($this->nodeID))
		{
			$mainSync = new Synchronizer($node);

			/// It is set as end date "now" to the current frame in production to put it down
			$currentFrame = $this->GetCurrentPublicatedFrame();
			$lastFrame	= $this->GetLastFrame();
			$lastTime	= $this->GetDateDownOnFrame($lastFrame);
			if($currentFrame)
			{
				$nextFrame = $this->GetNextFrame($currentFrame);
				if($nextFrame)
				{
					$deletableFrames = $this->GetFramesBetweenDates($this->GetDateUpOnFrame($nextFrame), $lastTime+100);
				} else {
					$deletableFrames = array();
				}

				$this->SetDateDownOnFrame($currentFrame, time());
			} else {
				$deletableFrames = $this->GetFramesBetweenDates(time(), $lastTime+100);
			}

			foreach($deletableFrames as $deletableFrame)
			{
				$this->DeleteFrame($deletableFrame);
			}

			/// From here on, we look for frames to copy from master.
			$lastFrame	= $mainSync->GetLastFrame();
			if($lastFrame)
			{
				$lastTime	= $mainSync->GetDateDownOnFrame($lastFrame);

				$frameList	= $mainSync->GetFramesBetweenDates(time(), $lastTime+100);

				if(sizeof($frameList))
				{
					foreach($frameList as $fID)
					{
						$dateUp		= $mainSync->GetDateUpOnFrame($fID);
						$dateDown	= $mainSync->GetDateDownOnFrame($fID);
						if($dateUp < time())
						$dateUp = time();

						$this->CreateFrame($dateUp, $dateDown);
					}
				}

			}

		} else {
			$this->SetError(1);
		}
	}


	/// *************************************************************************



	/// *************************************************************************
	/// FUNCTIONS GET AND SET OF SOME FRAME ATTRIBUTES
	/// *************************************************************************


	function GetFrameId() {

		return $this->frameID;

	}


	/// Function which looks for a frame init date
	/// Class method, it does not requiere previous SetID.
	function GetDateUpOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT DateUp FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);
		return $this->dbObj->GetValue("DateUp");
	}

	// Function which returns a frame state
	function GetStateOfFrame($frameID) {
		$this->ClearError();

		$sql = "SELECT State FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);
		return $this->dbObj->GetValue("State");
	}

	/// Function which looks for a frame end date
	/// Class method, it does not requiere previous SetID.
	function GetDateDownOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT DateDown FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);

		if($this->dbObj->numRows == 0){
		    return NULL;
		}

		return $this->dbObj->GetValue("DateDown");
	}

	/// Function which changes a frame end date
	/// Class method, it does not requiere previous SetID.
	function SetDateDownOnFrame($frameID, $date)
	{
		$this->ClearError();
		XMD_Log::info("frame: $frameID date: $date");
		$dateUp	 = $this->GetDateUpOnFrame($frameID);
		$dateDown = $this->GetDateDownOnFrame($frameID);
		$nodeID	 = $this->GetNodeIdOnFrame($frameID);

		$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode=".$nodeID." AND DateUp=".$dateUp;
		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			$list[] = $this->dbObj->GetValue("IdSync");
			$this->dbObj->Next();
		}

		$nextFrame = $this->GetNextFrame($frameID);
		if($nextFrame)
		{
			$nextDateUp	 = $this->GetDateUpOnFrame($nextFrame);
			$nextDateDown = $this->GetDateDownOnFrame($nextFrame);
		}

		if($date)
		{
			if($nextFrame && $nextDateUp < $date) {
				$this->SetError(8);
			} else {
				foreach($list as $frame)
				{
					$sql = "UPDATE Synchronizer SET DateDown=".$date." WHERE DateUp=".$dateUp." AND IdSync=".$frame;
					$this->dbObj->Execute($sql);
				}
			}
		} else {
			if($nextFrame) {
				$this->SetError(8);
			} else {
				foreach($list as $frame)
				{
					$sql = "UPDATE Synchronizer SET DateDown= NULL WHERE DateUp=".$dateUp." AND IdSync=".$frame;
					$this->dbObj->Execute($sql);
				}
			}
		}

		/// For each node associated to this node workflow, the action is repeated
		$node = new Node($nodeID);
		$workFlowSlaves = $node->GetWorkFlowSlaves();
		if (!is_null($workFlowSlaves)) {
			foreach($workFlowSlaves as $slave)
			{
				$sync = new Synchronizer($slave);
				$idSync = $sync->GetFrameOnDate($dateDown-1);
				$sync->DeleteFrame($idSync, $date);
			}
		}
	}

	/// Function which looks for a frame content
	/// Class method, it does not requiere previous SetID.
	function GetContentOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT IdSync FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);

		if($this->dbObj->GetValue("IdSync"))
		{
			$conf = new Config();
			$targetPath = $conf->GetValue("AppRoot") . $conf->GetValue("SyncRoot");

			$filePath = $targetPath . "/". $frameID;

			$content = FsUtils::file_get_contents($filePath);

			return $content;
		} else {
			$this->SetError(5);
		}
	}

	function GetChannelOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT IdChannel FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);
		return $this->dbObj->GetValue("IdChannel");
	}


	/// Function which looks for the frame production path
	/// Class method, it does not requiere previous SetID.
	function GetRemotePathOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT RemotePath FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);
		return $this->dbObj->GetValue("RemotePath");
	}

	/// Function which looks for a frame remote path
	/// Class method, it does not requiere previous SetID.
	function GetFileNameOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT FileName FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);

		return $this->dbObj->GetValue("FileName");
	}

	/// Function which looks for a frame id server
	/// Class method, it does not requiere previous SetID.
	function GetServerOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT IdServer FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);
		return $this->dbObj->GetValue("IdServer");
	}

	/// Function which looks for a frame id server
	/// Class method, it does not requiere previous SetID.
	function GetServerListOnFrame($frameID, $channelID=null)
	{
		$this->ClearError();

		$dateUp	 = $this->GetDateUpOnFrame($frameID);
		$dateDown = $this->GetDateDownOnFrame($frameID);
		$nodeID	 = $this->GetNodeIdOnFrame($frameID);

		$sql="SELECT Synchronizer.IdServer,Synchronizer.IdChannel FROM Synchronizer, Servers WHERE Synchronizer.IdNode=$nodeID AND Synchronizer.DateUp=$dateUp AND Servers.Enabled=1 AND Synchronizer.IdServer=Servers.IdServer";
		$this->dbObj->Query($sql);

		$list = array();
		while(!$this->dbObj->EOF)
		{
			if(($channelID && $channelID == $this->dbObj->GetValue("IdChannel")) || (!$channelID && !$this->dbObj->GetValue("IdChannel")))
			$list[] = $this->dbObj->GetValue("IdServer");
			$this->dbObj->Next();
		}

		return $list;
	}


	/// Fucntion which looks for a frame init date
	/// Class method, it does not requiere previous SetID.
	function GetNodeIdOnFrame($frameID)
	{
		$this->ClearError();

		$sql = "SELECT IdNode FROM Synchronizer WHERE IdSync=".$frameID;

		$this->dbObj->Query($sql);
		return $this->dbObj->GetValue("IdNode");
	}

	/// Function which looks for a frame remote path
	/// Object method, it requieres previous SetID.
	function HasUnlimitedLifeTime()
	{
		$this->ClearError();

		$lastFrame = $this->GetLastFrame();
		$lastTime = $this->GetDateDownOnFrame($lastFrame);

		
		return (bool)($lastFrame && !$lastTime);

	}


	function SetState($frameID,$state) {


		$sql = "UPDATE Synchronizer SET state='".$state."' WHERE IdSync=".$frameID;
		$this->dbObj->Execute($sql);

	}


	function IsLinked($frameID) {

		$this->ClearError();

		$sql = "SELECT Linked FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);
		return $this->dbObj->GetValue("Linked");


	}


	/// *************************************************************************



	/// *************************************************************************
	/// FUNCTIONS FOR THE DEFINITIVE SYNCHRONIZATION WITH PRODUCTION (used by daemon)
	/// *************************************************************************

	/// Function which returns the task that should be executed by the daemon at the request moment
	/// Class method, it does not requiere previous SetID.
	function GetPendingUploadTasks()
	{
		XMD_Log::info("----> INIT!!! Looking for up tasks");
		/*
		MySQL 4.1
		SELECT IdSync, IdServer FROM Synchronizer WHERE
		State='DUE'
		AND
		((DateUp < NOW() AND DateDown > NOW()) OR (DateUp < NOW() AND DateDown IS NULL	))
		AND
		IdNode NOT IN (SELECT IdNode FROM Synchronizer WHERE State='IN' AND DateDown<NOW())
		ORDER BY IdServer";

		MySQL Antiguos, sin subquerys
		SELECT S1.IdSync, S1.IdServer FROM Synchronizer S1 LEFT JOIN Synchronizer S2 ON S1.IdSync=S2.IdSync WHERE
		S2.IdNode IS NULL AND
		S1.State='DUE' AND
		((S1.DateUp < NOW() AND S1.DateDown > NOW()) OR (S1.DateUp < NOW() AND S1.DateDown IS NULL)) AND
		S2.State='IN' AND S2.DateDown<NOW()
		ORDER BY IdServer
		*/
		///	Mysql >= 4.1 con subqueries
		//		$sql =" SELECT IdSync, IdServer FROM Synchronizer WHERE ";
		//		$sql.=" State='DUE'	";
		//		$sql.=" AND ";
		//		$sql.="	((DateUp<".time()." AND DateDown>".time().") OR (DateUp<".time()." AND DateDown IS NULL	)) ";
		//		$sql.=" AND ";
		//		$sql.="	IdNode NOT IN (SELECT IdNode FROM Synchronizer WHERE State='IN' AND DateDown<".time().") ";
		//		$sql.=" ORDER By IdServer ";

		/// Mysql without subqueries
		$sql =" SELECT Synchronizer.IdSync, Synchronizer.IdServer, Synchronizer.IdNode FROM Synchronizer, Servers WHERE ";
		$sql.=" Synchronizer.State='DUE'	";
		$sql.=" AND ";
		$sql.="	((Synchronizer.DateUp<".time()." AND Synchronizer.DateDown>".time().") OR (Synchronizer.DateUp<".time()." AND Synchronizer.DateDown IS NULL	)) ";
		$sql.=" AND ";
		$sql.=" Synchronizer.IdServer=Servers.IdServer ";
		$sql.=" AND ";
		$sql.=" Servers.Enabled=1";
		$sql.=" ORDER By IdServer ";



		$this->dbObj->Query($sql);
		XMD_Log::info("[SQL: ".$sql." -> ".$this->dbObj->numRows);
		$dbObj = new DB();
		$rawList = array();
		$list = array();
		$servers = array();
		$server = null;

		while(!$this->dbObj->EOF)
		{
			//// To comment from here if subqueries are used
			/// Filtering the ones with previous frames still published, due to error or delay
			$sql="SELECT IdNode FROM Synchronizer WHERE IdNode=".$this->dbObj->GetValue("IdNode")." AND IdServer=".$this->dbObj->GetValue("IdServer")." AND State='IN' AND DateDown<".time();

			$dbObj->Query($sql);
			if($dbObj->numRows)
			{
				$this->dbObj->Next();
				continue;
			}
			//// To comment until here if subqueries are used

			if($this->dbObj->GetValue("IdServer") != $server)
			{
				/// In servers we store the server of the frame which is in the same position
				$server	 = $this->dbObj->GetValue("IdServer");
				$servers[]= $server;
				$list[]	 = array();
			}
			$rawList[] = $this->dbObj->GetValue("IdSync");

			$list[sizeof($list)-1][] = $this->dbObj->GetValue("IdSync");
			$this->dbObj->Next();
		}

		/// If name or path has changed, updating references to it in other already published nodes
		foreach($rawList as $frameID)
		{
			$previousFrame = $this->GetPreviousFrame($frameID);
			if($previousFrame)
			{
				XMD_Log::info("\t[ ".$frameID." ] - Checking path/name changes with prior version:");
				/// Con este sql comprobamos que entre la ventana anterior y la actual no hayan cambiado el path o el nombre
				$sql= "SELECT DISTINCT FileName, RemotePath FROM Synchronizer WHERE IdSync=".$previousFrame." OR IdSync=".$frameID;
				$dbObj->Query($sql);
				if($dbObj->numRows>1)
				{
					/// Sí ha cambiado
					$sql = "SELECT DISTINCT IdSync FROM SynchronizerDependencies WHERE IdResource=".$frameID;
					$this->dbObj->Query($sql);

					/// Ponemos a DUE a todos los que nos apuntan
					while(!$this->dbObj->EOF)
					{
						$idSync = $this->dbObj->GetValue("IdSync");
						if(!in_array($idSync, $rawList))
						{
							$sql = "UPDATE Synchronizer SET State='DUE', Linked=1 WHERE State='IN' AND IdSync=".$idSync;
							$counterline = $dbObj->Execute($sql);
							XMD_Log::info("\t\t Ancestors [ Node ".$frameID." -> Ancestor ".$idSync." ] - $idSync. [SQL : ".$sql."] ... coupled nodes: $counterline");
						}
						$this->dbObj->Next();
					}

				} else {
					XMD_Log::info("\t[ ".$frameID." ] - Checking path/name changes with prior version: NO CHANGES");
				}
			} else {
				XMD_Log::info("\t[ $frameID ] - Checking path/name changes with prior version: NO PRIOR VERSION");
			}
		}
		/*
		No tested code, commented for production
		It updates dead reference to documents published while some of its linked was not even waiting for being published.
		*/

		XMD_Log::info("\trawList: $rawList");
		/// For each node to synchronize

		foreach($rawList as $frameID)
		{
			/// Looking for a down date for previous frame, 0 if it does not exist

			$islinked=$this->IsLinked($frameID);

			$previousFrame = $this->GetPreviousFrame($frameID);
			if($previousFrame) {
				$previousOutDate = $this->GetDateDownOnFrame($previousFrame);
			} else {
				$previousOutDate = 0;
			}
			/// Getting the  nodeID and up date for frame to publish
			$currentUpDate = $this->GetDateUpOnFrame($frameID);
			$currentNodeID = $this->GetNodeIdOnFrame($frameID);

			/// Selecting the ids for frames that:
			/// 	a) were published between the previous frame expiration and the publication of the current one
			///	b) are estill published
			/// 	c) and pointing to this  nodeID
			/// that's why there are some dead reference due to frame absences.
			$sql = "SELECT DISTINCT SynchronizerDependencies.IdSync FROM SynchronizerDependencies, Synchronizer WHERE ";
			$sql.= " SynchronizerDependencies.IdResource=".$currentNodeID." ";
			$sql.= " AND SynchronizerDependencies.IdSync=Synchronizer.IdSync ";
			$sql.= " AND Synchronizer.State='IN' ";
			$sql.= " AND Synchronizer.DateUp > ".$previousOutDate;
			$sql.= " AND Synchronizer.DateUp < ".$currentUpDate;
			$sql.= " AND Synchronizer.IdSync!=".$frameID;
			$sql.= " AND Synchronizer.IdNode!=".$currentNodeID;
			$this->dbObj->Query($sql);

			XMD_Log::info("\t[ ".$frameID." ] - Checking #'s in production [ ".$sql." -> ".$this->dbObj->numRows."]");
			while(!$this->dbObj->EOF)
			{

				if ($islinked==0) {
					$idSync = $this->dbObj->GetValue("IdSync");

					if(!in_array($idSync, $rawList))
					{
						$sql = "UPDATE Synchronizer SET State='DUE', Linked=1 WHERE State='IN' AND IdSync=".$idSync;
						$counterline = $dbObj->Execute($sql);
						XMD_Log::info("\t\tSetting dead references which are pointing to this node [ ".$frameID." -> ".$idSync." ] - $idSync. [SQL : ".$sql."]. Nodes coupled: $counterline ");
					}

				}
				$this->dbObj->Next();
			}
		}

		// Checking if the node has ximlet dependencies. If it does, links are re-published
		/*
		if ($islinked==0) {
			$st=New StructuredDocument($this->GetNodeIdOnFrame($frameID));
			$ximlets=$st->isximletlink();

			for ($i=0;$i<count($ximlets);$i++) {
				$ximlet=new StructuredDocument();
				$links=$ximlet->ximletLinks($ximlets[$i], $this->GetNodeIdOnFrame($frameID));

				for($j=0;$j<count($links);$j++) {
					$sql = "UPDATE Synchronizer SET State='DUE', Linked=1 WHERE State='IN' AND Linked<>1 AND IdNode=".$links[$j];
					$counterline = $dbObj->Execute($sql);
					XMD_Log::info("\t\tDUANDO apuntadores de ximletS: ".$links[$j]." [SQL : ".$sql."]. Nodos duados: $counterline ");
				}
			}
		}
		*/

		XMD_Log::info("----> END !!! Looking for up tasks");
		/// Two arrays are returned, one with serverids, and other with a task array per server
		return array($servers, $list);
	}

	/// Function which creates the file which will be synchronized in production for the daemon
	/// Class method, it does not requiere previous SetID.

	function CreateTmpFile($frameID) {

		$sql = "SELECT IdServer,IdChannel,IdNode FROM Synchronizer WHERE IdSync=".$frameID;
		$this->dbObj->Query($sql);
		
		$serverID = $this->dbObj->GetValue("IdServer");
		$tmpPath = Config::getValue("AppRoot").Config::getValue("TempRoot")."/".$frameID;
		$channel = $this->dbObj->GetValue("IdChannel");
		$nodeID = $this->dbObj->GetValue("IdNode");

		$dataFactory = new DataFactory($nodeID);
        	$idVersion = $dataFactory->GetPublishedIdVersion();

		$data['CHANNEL'] = $channel;
		$data['SERVER'] = $serverID;

		$node = new Node($nodeID);
		$transformer = $node->getProperty('Transformer');
		$data['TRANSFORMER'] = $transformer[0];
				
		$pipeMng = new PipelineManager();

		if ($channel) {
			$file = $pipeMng->getCacheFromProcess($idVersion, 'StrDocFromDexTToFinal', $data);

			// Post-filter

			$file = View_ChannelFilter::transform($idVersion, $file, $data);

		} else {

			$file = $pipeMng->getCacheFromProcess($idVersion, 'NotStrDocToFinal', $data);
		}

		// Bulletin sending

		if (ModulesManager::isEnabled('ximNEWS')) {

			$bulletins = $this -> GetBulletin2($frameID);

			if(count($bulletins) > 0) {
				foreach($bulletins as $bulletinID) {
					$sql="UPDATE XimNewsFrameBulletin SET State = 'mail_pending' WHERE 
						BulletinID = $bulletinID AND IdSync = $frameID";
					$this->dbObj->Execute($sql);
				}
			}
		}
		
		$nodo = new Node($nodeID);
		$nodoTypeContent = $nodo->nodeType->get('Name');

		// Only encoding the content if the node is not one of this 3.

		if (!(($nodoTypeContent == 'XimNewsImageFile') || 
			($nodoTypeContent == 'ImageFile') || ($nodoTypeContent == 'BinaryFile'))) {
		    	
		    	// Looking for idEncode for this server

				$server = new Server($serverID);
				$encodingServer = $server->get('idEncode');

				$content = XmlBase::recodeSrc(FsUtils::file_get_contents($file), $encodingServer);
		}
		
		FsUtils::file_put_contents($tmpPath, $content);
	}


	function DeleteSyncFile($frameID) {

		$config = new Config();

		$tmpPath = $config->GetValue("AppRoot").$config->GetValue("SyncRoot")."/".$frameID;

		echo "ELIMINANDO FICHERO SYNC".$tmpPath."\n";
		FsUtils::delete($tmpPath);

	}

	/// Function which returns the expiration tasks which should be executed by the daemon at the request moment
	/// Class method, it does not requiere previous SetID.
	function GetPendingDownloadTasks()
	{
		$sql =" SELECT .Synchronizer.IdSync, Synchronizer.IdServer FROM Synchronizer, Servers WHERE ";
		$sql.=" 	(State='IN'	OR State='OUTDATED') ";
		$sql.=" AND ";
		$sql.=" 	Synchronizer.DateDown<".time()." ";
		$sql.=" AND ";
		$sql.=" 	Synchronizer.DateDown IS NOT NULL ";
		$sql.=" AND ";
		$sql.="		Synchronizer.IdServer=Servers.IdServer";
		$sql.=" AND ";
		$sql.=" 	Servers.Enabled=1";
		$sql.=" ORDER By Synchronizer.IdServer ";

		$this->dbObj->Query($sql);

		// For massive publication on cascade update
		$rawList=array();

		$list = array();
		$servers = array();
		$server = null;
		while(!$this->dbObj->EOF)
		{
			if($this->dbObj->GetValue("IdServer") != $server)
			{
				$server	 = $this->dbObj->GetValue("IdServer");
				$servers[]= $server;
				$list[]	 = array();
			}

			$list[sizeof($list)-1][] = $this->dbObj->GetValue("IdSync");
		// For massive publication on cascade update
			$rawList[] = $this->dbObj->GetValue("IdSync");
			$this->dbObj->Next();
		}


		foreach ($rawList as $frameID) {

			$islinked=$this->IsLinked($frameID);

			$previousFrame = $this->GetPreviousFrame($frameID);
			if($previousFrame) {
				$previousOutDate		 = $this->GetDateDownOnFrame($previousFrame);
			} else {
				$previousOutDate		 = 0;
			}

			/// Getting the nodeID and up date for frame to publish
			$currentUpDate = $this->GetDateUpOnFrame($frameID);
			$currentNodeID = $this->GetNodeIdOnFrame($frameID);


			
			/// Selecting the ids for frames that:
			/// 	a) were published between the previous frame expiration and the publication of the current one
			///	b) are estill published
			/// 	c) and pointing to this  nodeID
			/// that's why there are some dead reference due to frame absences.
			$sql = "SELECT DISTINCT SynchronizerDependencies.IdSync FROM SynchronizerDependencies, Synchronizer WHERE ";
			$sql.= " SynchronizerDependencies.IdResource=".$currentNodeID." ";
			$sql.= " AND SynchronizerDependencies.IdSync=Synchronizer.IdSync ";
			$sql.= " AND Synchronizer.State='IN' ";
			$sql.= " AND Synchronizer.DateUp > ".$previousOutDate;
			$sql.= " AND Synchronizer.DateUp < ".$currentUpDate;
			$sql.= " AND Synchronizer.IdSync!=".$frameID;
			$sql.= " AND Synchronizer.IdNode!=".$currentNodeID;

			$this->dbObj->Query($sql);

			XMD_Log::info("\t[ ".$frameID." ] - COMPROBANDO #'s en produccion [ ".$sql." -> ".$this->dbObj->numRows."]");
			while(!$this->dbObj->EOF)
			{

				if ($islinked==0) {
					$idSync = $this->dbObj->GetValue("IdSync");
					$db2=new db();
					if(!in_array($idSync, $rawList))
					{
						$sql = "UPDATE Synchronizer SET State='DUE', Linked=1 WHERE State='IN' AND IdSync=".$idSync;
						$counterline = $db2->Execute($sql);
						XMD_Log::info("\t\t[GetPendingDownloadTasks] DUANDO REFERENCIAS MUERTAS QUE NOS APUNTAN [ ".$frameID." -> ".$idSync." ] - DUANDO $idSync. [SQL : ".$sql."]... Nodos duados: $counterline");
					}

				}
				$this->dbObj->Next();
			}






		}

		/// Two arrays are returned, one for server ids, and other one with a task array for each server

		return array($servers, $list);
	}

	/// Setting as outdated all the task from past which never went up to production and it should never do it now
	/// Class method, it does not require SetID.
	function SetOutDatedTasks()
	{
		$sql = "UPDATE Synchronizer SET State='OUTDATED' WHERE State='DUE' AND DateDown<=".time()." AND DateDown IS NOT NULL";
		$this -> dbObj -> Execute($sql);

		//Deleting the associated tasks of sending bulletins
		if(ModulesManager::isEnabled("ximNEWS")){
			$sql = "SELECT XimNewsFrameBulletin.IdSync AS Id FROM XimNewsFrameBulletin, Synchronizer WHERE XimNewsFrameBulletin.IdSync = Synchronizer.IdSync AND (Synchronizer.State = 'OUTDATED' OR Synchronizer.State = 'OUT') AND (XimNewsFrameBulletin.State = 'mail_pending' OR XimNewsFrameBulletin.State = '')";

			$this -> dbObj -> Query($sql);
			$arrayFrames = array();

			while(!$this -> dbObj -> EOF){
				$arrayFrames[] = $this -> dbObj -> GetValue('Id');
				$this -> dbObj -> Next();
			}

			foreach($arrayFrames as $frame){
				XMD_Log::info("Deleting task $frame of sending bulletins");
				$this -> dbObj -> Execute("DELETE FROM XimNewsFrameBulletin WHERE IdSync = $frame");
			}
		}
	}
	/// *************************************************************************


/*
    Deleting OUTDATED tasks (idem script purge_outdated)
*/
    function removeOutdated(){
	$this->dbObj->Query("SELECT IdSync FROM Synchronizer WHERE State = 'OUT' OR State = 'OUTDATED'");

	while (!$this->dbObj->EOF) {
	    $idFrame = $this->dbObj->GetValue('IdSync');
	    $this->DeleteSyncFile($idFrame);
	    XMD_Log::info("Deleting file sync/$idFrame");
	    $db = new DB();
	    $db->Execute("DELETE FROM Synchronizer WHERE IdSync = $idFrame");

	    $this->dbObj->Next();
	}

    }

	//Introduced by Jose Luis 28/11/2005 to update Linked when publication just finnished.

	function SetLinked() {

		$sql = "UPDATE Synchronizer SET Linked=1 WHERE IdNode=".$this->nodeID." AND State='DUE'";

		$this->dbObj->Execute($sql);

	}


/*
	Delete frames by Synchronizer column
	flag unPublish: if true unpublish frames at state 'IN' (don't deletes these frames)
	flag moveToHistory: if true move all frames an dependencies to history tables before deletion
*/
	function deleteByColumn($idColumn, $column, $unPublish = NULL, $moveToHistory = NULL) {

		if (is_null($idColumn)) {
			XMD_log::error('synchronizer->moveToHistory: Param needed');
			return false;			
		}

	    $dbObjExe = new DB();
	    $dbObjDeps = new DB();

		$columnsAllowed = array('IdNode', 'IdServer');

		if (!in_array($column, $columnsAllowed)) {
			XMD_log::error('synchronizer->moveToHistory: Unknow column in Synchronizer table');
			return false;			
		}

		$syncOrm = new Synchronizer_ORM();
		$result = $syncOrm->find(ALL, "$column = %s", array($column => $idColumn), MULTI);

		if (!is_null($result)) {
			foreach ($result as $data) {
				$idSync = $data['IdSync'];
				$state = $data['State'];

				$syncOrm = new Synchronizer_ORM($idSync);

				// Setting date down if node is published (don't move the frame)

				if ($state == 'IN' && !is_null($unPublish)) {
					$timeDown = mktime();

					$syncOrm->set('DateDown', $timeDown);
					$syncOrm->update();
					
					continue;
				}


				if (!is_null($moveToHistory)) {
					// Moving frame to history

					$syncHistoryOrm = new SynchronizerHistory_ORM();
					$syncHistoryOrm->loadFromArray($data);
					$syncHistoryOrm->add();

					// Searching and moving to history synchronizerDependencies

					$sql = "SELECT IdSync FROM SynchronizerDependencies WHERE IdSync = $idSync";
					$dbObjDeps->Query($sql);

					if ($dbObjDeps->numRows > 0) {
						$numColumnsSyncDeps = 2;

						while (!$dbObjDeps->EOF) {
							$sync = $dbObj->GetValue('IdSync');

							$sqlInsert = '';

							for ($i = 0; $i < $numColumnsSyncDeps; $i++) {
								$sqlInsert .= "','".$dbObjDeps->row[$i];
							}

							$sqlInsert = "INSERT INTO SynchronizerDependenciesHistory VALUES (".substr($sqlInsert,2)."')";
							$dbObjExe->Execute($sqlInsert);

							$dbObjDeps->Next();
						}
					}
				}
				
				// Deleting frame and dependencies

				$syncOrm->delete();

				$sqlDelete = "DELETE FROM SynchronizerDependencies WHERE IdSync = $idSync";
				$dbObjExe->Execute($sqlDelete);

			}
		}

		return true;
	}

	function getPendingFrames($nodeID) {
		$dbObj = new DB();
		$frames = array();

		$sql = "SELECT IdSync FROM Synchronizer WHERE State = 'DUE' AND IdNode = $nodeID";
		$dbObj->Query($sql);

		while (!$dbObj->EOF) {
			$frames[] = $dbObj->GetValue('IdSync');
			$dbObj->Next();
		}
	
		return $frames;
	}

	/// ************************************************************************
	/// FUNTIONS FOR SYNC FILES MANAGEMENT
	/// *************************************************************************

	function FicherosSync() {

		$sql="SELECT IdSync FROM Synchronizer WHERE State='IN' OR State='DUE'";


		array($arr_sync);

		$this->dbObj->Query($sql);

		if ($this->dbObj->numRows==0) {
			return 0;
		}

		if (!$this->dbObj->numErr)
		{

			$i = 0;
			while(!$this->dbObj->EOF)
			{
				$array_sync[$i]=$this->dbObj->GetValue("IdSync");
				$this->dbObj->Next();
				$i++;

			}

			return $array_sync;
		} else {
			$this->SetError(4);
			return 0;
		}

	}



    function getUrlLastPublicatedNews($nodeid, $channelID, $serverID) {

		$now = mktime();

		$sql = "SELECT IdSync, RemotePath, FileName FROM Synchronizer WHERE  ";
		$sql .= " IdChannel = $channelID AND DateUp < $now ";
		$sql .= " AND IdServer = $serverID ";
		$sql .= " AND IdNode = $nodeid ";
		$sql .= " AND (DateDown > $now OR DateDown IS NULL) AND State = 'In' ";

		$dbObj = new DB();
		$dbObj->Query($sql);
		$path = $dbObj->GetValue("RemotePath");
		$filename = $dbObj->GetValue("FileName");

		if($filename) return $path."/".$filename;

		return null;
    }


	/// *************************************************************************
	/// FUNCTION FOR CLASS ERROR MANAGEMENT
	/// *************************************************************************

	/// Cleaning the last error
	/// private method
	function ClearError()
	{
		$this->numErr = null;
		$this->msgErr = null;
	}

	/// Loading error in class
	/// private method
	function SetError($code)
	{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/// Returning last error code
	/// public method
	function HasError()
	{
		return $this->numErr;
	}

	/// *************************************************************************



}
?>
