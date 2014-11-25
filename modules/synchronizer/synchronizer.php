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



 
if (!defined('XIMDEX_ROOT_PATH'))
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

//
ModulesManager::file('/inc/utils.php');
ModulesManager::file('/inc/sync/Mutex.class.php');

Main($argv, $argc);

function Main($argv, $argc) {
	
	$sync = new Synchronizer();
	$config = new Config();
	$tmpPath = $config->GetValue("AppRoot").$config->GetValue("TempRoot");
	$command = $config->GetValue("AppRoot").$config->GetValue("SynchronizerCommand");
	$stopper_file_path = Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/synchronizer.stop";

	GLOBAL $synchro_pid;
	$synchro_pid = posix_getpid();
	
	XMD_Log::display("---------------------------------------------------------------------");
	XMD_Log::display("Executing: Synchronizer (" . $synchro_pid . ")");
	XMD_Log::display("---------------------------------------------------------------------");
	XMD_Log::display("");
	XMD_Log::display("Checking lock...");

	$mutex = new Mutex($tmpPath . "/synchro.lck");
	if (!$mutex->acquire()) {
		XMD_Log::display("Cerrando...");
		XMD_Log::display("INFO: Block file exists, there is another process running.");
		exit(1);
	}
	
	if (file_exists($stopper_file_path)) {
		$mutex->release();
    	XMD_Log::info("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer");
		die("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer.\n");
	}
	
	XMD_Log::display("Bloqueo adquirido...");
	
	/// At first, marks as outdated task which are outdated. It is not necessary this process, but for clearly
	XMD_Log::display("Outdating outdated and not published tasks...\n");
	$sync->SetOutDatedTasks();

	XMD_Log::display("UNPUBLICATION PROCESS:");
	XMD_Log::display(" Computing tasks to be executed...");
	$downTasks	= $sync->GetPendingDownloadTasks();
	$downServers= $downTasks[0];
	$downTasks	= $downTasks[1];

	XMD_Log::display("");
	
	for($i=0; $i<sizeof($downServers); $i++) {
		$server = $downServers[$i];
		$tasks	= $downTasks[$i];

		$commandLine = $command." --hostid ".$server." --localbasepath ".$tmpPath . " --tasknumber " . sizeof($tasks); 
		foreach($tasks as $taskID) {
			if (file_exists($stopper_file_path)) {
				$mutex->release();
		    	XMD_Log::info("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer");
				die("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer.\n");
			}
			$commandLine .= " r:".$taskID;
			$sync->DeleteSyncFile($taskID);
		}
		XMD_Log::display("UNPUBLICATION PROCESS");
		XMD_Log::display(" Executing: ".$commandLine);
		system($commandLine);
	}
		

	XMD_Log::display("PUBLICATION PROCESS:");
	XMD_Log::display(" Computing tasks to be executed...");
	$upTasks	= $sync->GetPendingUploadTasks();
	$upServers	= $upTasks[0];
	$upTasks	= $upTasks[1];
	
	XMD_Log::display("");
	XMD_Log::display("");
	
	for($i=0; $i<sizeof($upServers); $i++) {
		$server = $upServers[$i];
		$tasks	= $upTasks[$i];

		$commandLine = $command." --hostid ".$server." --localbasepath ".$tmpPath . " --tasknumber " . sizeof($tasks); 
		foreach($tasks as $taskID) {
			if (file_exists($stopper_file_path)) {
				$mutex->release();
		    	XMD_Log::info("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer");
				die("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer.\n");
			}
			$commandLine .= " u:".$taskID;
			$sync->CreateTmpFile($taskID);
		}
			
		XMD_Log::display("PUBLICATION PROCESS:");
		XMD_Log::display(" Executing: ".$commandLine);
		system($commandLine);
	}
		
	// Deleting OUTDATED files
	$sync->removeOutdated();	

	$mutex->release();
	XMD_Log::display("PROCESS FINISHED");
}
?>