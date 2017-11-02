#!/usr/bin/env php
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


use Ximdex\Logger;
use Ximdex\Runtime\App;
use Ximdex\Utils\Sync\Mutex;
use Ximdex\Utils\Sync\Synchronizer;

ModulesManager::file('/inc/utils.php');

Main($argv, $argc);

function Main($argv, $argc)
{

    $sync = new Synchronizer();
    $tmpPath = App::getValue("AppRoot") . App::getValue("TempRoot");
    $command = App::getValue("AppRoot") . App::getValue("SynchronizerCommand");
    $stopper_file_path = App::getValue("AppRoot") . App::getValue("TempRoot") . "/synchronizer.stop";

    GLOBAL $synchro_pid;
    $synchro_pid = posix_getpid();

    Logger::display("---------------------------------------------------------------------");
    Logger::display("Executing: Synchronizer (" . $synchro_pid . ")");
    Logger::display("---------------------------------------------------------------------");
    Logger::display("");
    Logger::display("Checking lock...");

    $mutex = new Mutex($tmpPath . "/synchro.lck");
    if (!$mutex->acquire()) {
        Logger::display("Cerrando...");
        Logger::display("INFO: Block file exists, there is another process running.");
        exit(1);
    }

    if (file_exists($stopper_file_path)) {
        $mutex->release();
        Logger::info("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer");
        die("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer.\n");
    }

    Logger::display("Bloqueo adquirido...");

    /// At first, marks as outdated task which are outdated. It is not necessary this process, but for clearly
    Logger::display("Outdating outdated and not published tasks...\n");
    $sync->SetOutDatedTasks();

    Logger::display("UNPUBLICATION PROCESS:");
    Logger::display(" Computing tasks to be executed...");
    $downTasks = $sync->GetPendingDownloadTasks();
    $downServers = $downTasks[0];
    $downTasks = $downTasks[1];

    Logger::display("");

    for ($i = 0; $i < count($downServers); $i++) {
        $server = $downServers[$i];
        $tasks = $downTasks[$i];

        $commandLine = $command . " --hostid " . $server . " --localbasepath " . $tmpPath . " --tasknumber " . count($tasks);
        foreach ($tasks as $taskID) {
            if (file_exists($stopper_file_path)) {
                $mutex->release();
                Logger::info("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer");
                die("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer.\n");
            }
            $commandLine .= " r:" . $taskID;
            $sync->DeleteSyncFile($taskID);
        }
        Logger::display("UNPUBLICATION PROCESS");
        Logger::display(" Executing: " . $commandLine);
        system($commandLine);
    }


    Logger::display("PUBLICATION PROCESS:");
    Logger::display(" Computing tasks to be executed...");
    $upTasks = $sync->GetPendingUploadTasks();
    $upServers = $upTasks[0];
    $upTasks = $upTasks[1];

    Logger::display("");
    Logger::display("");

    for ($i = 0; $i < count($upServers); $i++) {
        $server = $upServers[$i];
        $tasks = $upTasks[$i];

        $commandLine = $command . " --hostid " . $server . " --localbasepath " . $tmpPath . " --tasknumber " . count($tasks);
        foreach ($tasks as $taskID) {
            if (file_exists($stopper_file_path)) {
                $mutex->release();
                Logger::info("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer");
                die("STOP: Detected file $stopper_file_path. You need to delete this file for successful restart of synchronizer.\n");
            }
            $commandLine .= " u:" . $taskID;
            $sync->CreateTmpFile($taskID);
        }

        Logger::display("PUBLICATION PROCESS:");
        Logger::display(" Executing: " . $commandLine);
        system($commandLine);
    }

    // Deleting OUTDATED files
    $sync->removeOutdated();

    $mutex->release();
    Logger::display("PROCESS FINISHED");
}