#!/usr/bin/php -q
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




 


// NOTE:
// Launched with CRON.
//

if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/model/XimNewsList.php', 'ximNEWS');
ModulesManager::file('/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/sync/Mutex.class.php');
ModulesManager::file('/inc/mail/Mail.class.php');
ModulesManager::file('/inc/workflow/Workflow.class.php');
ModulesManager::file('/inc/pipeline/PipelineManager.class.php');


function GetMessageFromSync($idSync, $nodeID) {

	// Gets frame data
	$path = SynchroFacade::getFramePath($idSync);
	$fileName = SynchroFacade::getFrameName($idSync);
	$physicalID = SynchroFacade::getFrameServer($idSync);
	$channel = SynchroFacade::getFrameChannel($idSync);

	// Gets server data
	$server = new Server($physicalID);
	$serverID = $server->get('IdNode');
	$uri = $server->get('Url');

	if ($uri{strlen($uri)-1} == "/") {
		$uri = substr($uri, 0, -1);
	}

	// Gets the content
	$urlRoot = Config::getValue("UrlRoot");

	$df = new DataFactory($nodeID);
	$lv = $df->GetLastVersionId();
	$pipeman = new PipelineManager();
	$content = $pipeman->getCacheFromProcessAsContent($lv, 'StrDocToDexT', array('CHANNEL' => $channel));
	
	if ($content === null) {
		XMD_Log::error("Could not get the content of the document: NodeId: $nodeID, ChannelId: $channel, Process: StrDocToDexT");
		return null;
	}

	$nodeRoot = Config::getValue("NodeRoot");
	$nodeRoot = str_replace("/","\/",$nodeRoot);

	$srcServer = new Node($serverID);
	$relPath = $srcServer->GetRelativePath($srcServer->GetProject());
	$relPath = str_replace("/","\/",$relPath);

	$urlRoot = str_replace("/","\/",$urlRoot);
	$urlRoot = str_replace(".","\.",$urlRoot);
	$nodesPath = $urlRoot.$nodeRoot.$relPath;

	$content = preg_replace("/$nodesPath\/(.+?)\.(\w{3})/e", "'$uri/\\1.\\2'", $content);
	$content = preg_replace("/$urlRoot\/actions\/prevdoc\/init\.php\?nodeid=([0-9]*)&channel=([0-9]*)/e", "'$uri$path/'.GetLinkPath($nodeID, '\\1', $channel, '\\2', null, $idSync)", $content);

	return $content;
}

function GetToFromSync($idSync) {

	$dbObj = new DB();

	$sql = "SELECT BulletinID FROM XimNewsFrameBulletin WHERE IdSync=".$idSync;
	$dbObj->Query($sql);
	$bulletinID = $dbObj->GetValue("BulletinID");

	$node = new Node($bulletinID);
	$containerID = $node->GetParent();
	XMD_Log::display("Contenedor " . $containerID);

	$dbObj->Query("SELECT IdColector FROM XimNewsBulletins WHERE IdContainer=" .$containerID);
	$colectorID = $dbObj->GetValue('IdColector');
	if ($colectorID) {

		$ximNewsList = new XimNewsList($colectorID); 
		$list = array();
		$list = $ximNewsList->getList($colectorID);
		return $list;

	} else {
		return NULL;
	}		
}

function main($argc, $argv) {

	/*
	// Check if module is installed.
	if ( ! ModulesManager::isEnabled('ximNEWS') ) {
		exit();
	}
	*/

	// Begin

	$mailmngr_pid = posix_getpid();

        XMD_Log::display("---------------------------------------------------------------------");
        XMD_Log::display("Executing: MailMngr (" . $mailmngr_pid . ")");
        XMD_Log::display("---------------------------------------------------------------------");
        XMD_Log::display("");
        XMD_Log::display("Checking lock...");

	$mutex = new Mutex(Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/mailmngr.lck");
        if (!$mutex->acquire()) {
                XMD_Log::display("Closing...");
                XMD_Log::display("INFO: lock file exists, there is another process running.");
                exit(1);
        }

        XMD_Log::display("Lock acquired...");
	
	$db = new DB();

	// Obtain an array all bulletin frames in state == mail_pending
	$sql = "SELECT * FROM XimNewsFrameBulletin WHERE State='mail_pending'";
	$db->Query($sql);

	$sent = array();

	while (!$db->EOF) {

		$bulletinId = $db->GetValue("BulletinID");
		$bulletinFrame = $db->GetValue("IdSync");

		$state_frame = SynchroFacade::getFrameState($bulletinFrame);

		if (is_null($state_frame)) {
			XMD_Log::error("Incorrect frame: $bulletinFrame");
			continue;
		}


		$bul = new Node($bulletinId);
		$bulName = $bul->class->getAlias();

		if (strtoupper($state_frame) == 'IN') {

			$mail_lists = array();
			$mail_lists = GetToFromSync($bulletinFrame);
			$message = GetMessageFromSync($bulletinFrame, $bulletinId);
			$subject = $bulName;

			$mail_lists = is_array($mail_lists) ? $mail_lists : array();

			foreach ($mail_lists as $list) {

				if (empty($list)) {
					continue;
				}

				XMD_Log::display("Sending mail " . $list);

				$mail = new Mail();

				$mail->addAddress($list, "list name");
				$mail->Subject = $subject;
				$mail->Body = $message;
				$mail->ContentType = "text/html";

				if ($mail->Send()) {
					$sent[] = $bulletinId;
				}
				else{
					echo "Error while sending mail\n";
				}
			}
		}

		$db->Next();
	}

	if (count($sent) > 0) {
		$sql = sprintf("UPDATE XimNewsFrameBulletin SET State = 'mail_sent' WHERE BulletinID in (%s)", implode(',', $sent));
		$db->Execute($sql);
	}

	// End.

	$mutex->release();

	XMD_Log::display("PROCESS FINISHED");
}
	

/**
 * Entry point.
 */

main($argc, $argv);

?>