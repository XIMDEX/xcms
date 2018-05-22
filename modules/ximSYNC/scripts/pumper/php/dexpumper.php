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
use Ximdex\IO\Connection\Connector;
use Ximdex\Models\Pumper;
use Ximdex\Models\Server;
use Ximdex\Runtime\App;
use Ximdex\Cli\CliParser;

// For legacy compatibility
if (!defined('XIMDEX_ROOT_PATH')) {
    require_once dirname(__FILE__) . '/../../../../../bootstrap.php';
}

function showErrors($errno, $errstr, $errfile = NULL, $errline= NULL) {
    $_msg = "FATAL ERROR:$errno, $errstr. $errfile : $errline. TRACE:  ".print_r(debug_backtrace(),true);
}
set_error_handler("showErrors");

\Ximdex\Modules\Manager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');

/**
 * Constants definition
 */
define('PUMPER_ERROR_LEVEL_OK', 0);
define('PUMPER_ERROR_LEVEL_SOFT', 1);
define('PUMPER_ERROR_LEVEL_HARD', 2);
define('PUMPER_ERROR_LEVEL_FATAL', 3);

class DexPumperCli extends CliParser
{
    public $_metadata = array(
        array('name' => '--iduser', 'mandatory' => false, 'default' => 'ximdex',
                        'message' => 'Username (or identifier)', 'type' => TYPE_STRING),
        array('name' => '--verbose', 'mandatory' => false, 'default' => 5,
                        'message' => 'Indicates the verbosity level of the log', 'type' => TYPE_INT),
        array('name' => '--maxvoidcycles', 'mandatory' => false, 'default' => 100,
                        'message' => 'Number of empty cycles before the pumper end',
                        'type' => TYPE_INT),
        array('name' => '--sleeptime', 'mandatory' => false, 'default' => 10,
                        'message' => 'Number of seconds to wait between empty cycles',
                        'type' => TYPE_INT),
        array('name' => '--pumperid', 'mandatory' => true,
                        'message' => 'Identifier of pumper to be processed',
                        'type' => TYPE_INT, 'group' => array('name' => 'operation', 'value' => 1)),
        array('name' => '--localbasepath', 'mandatory' => true,
                        'message' => 'Base path of the documents', 'type' => TYPE_STRING,
                        'group' => array('name' => 'operation', 'value' => 1)),
        array('name' => '--tryserver', 'mandatory' => true,
                        'message' => 'Identifier of server to be tested',
                        'type' => TYPE_INT, 'group' => array('name' => 'operation', 'value' => 2)));

    public function __construct ($paramsCount, $params)
    {
        $this->_metadata[0]['message'] = _('Username (or identifier)');
        $this->_metadata[1]['message'] = _('Indicates the verbosity level of the log');
        $this->_metadata[2]['message'] = _('Number of empty cycles before the pumper end');
        $this->_metadata[3]['message'] = _('Identifier of pumper to be processed');
        $this->_metadata[4]['message'] = _('Base path of the documents');
        $this->_metadata[5]['message'] = _('Identifier of server to be tested');
        parent::__construct($paramsCount, $params);
    }
}

class DexPumper
{
	private $idUser;
	private $verbose;
	private $maxVoidCycles;
	private $sleepTime;
	private $localBasePath;
	private $pumper;
	private $connection;
	private $serverFrame;
	private $server;
	const RETRIES_TO_HARD_ERROR = 2;
	const RETRIES_TO_FATAL_ERROR = 5;

	public function __construct($params)
	{
		// Collect parameters
		$this->tryserver = trim($params['--tryserver']);
		$this->idUser = trim($params['--iduser']);
		$this->verbose = trim($params['--verbose']);
		$this->maxVoidCycles = trim($params['--maxvoidcycles']);
		$this->sleepTime = trim($params['--sleeptime']);
		$this->pumper = new Pumper(trim($params['--pumperid']));
		if (!($this->pumper->get('PumperId') > 0)) {
			$this->fatal('Pumper Id NOT found for id: ' . $params['--pumperid']);
		}
		$this->debug("NEW PUMPER:: ".print_r($params,true));
		$this->localBasePath = trim($params['--localbasepath']);
	}

	public function start()
	{
		$cycle = 0;
		$this->registerPumper();
		while (true) {
			if (!$cycle) {
				$this->info("STARTING PUMPER CYCLE");
			}else {
				$this->info("PUMPER CYCLE");
			}
			$serverFrame = new ServerFrame();
			$pumperID = $this->pumper->get('PumperId');
			$serverFrameInfo = $serverFrame->getPublishableNodesForPumper($pumperID);
			$countNodes = count($serverFrameInfo);
			$this->info("$countNodes nodes for pumping with PumperID: " . $pumperID);
			
			// Exit condition here when cycles reach max void cycles
			if (empty($serverFrameInfo)) {
				$cycle ++;
				if ($cycle <= $this->maxVoidCycles) {
					$this->updateTimeInPumper();
					$this->activeWaiting();
					
					// Manual stop for pumpers in sleeping mode
					$stopper_file_path = XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/pumpers.stop";
					if (file_exists($stopper_file_path)) {
					    Logger::warning('[PUMPERS] ' . "STOP: Detected file" . " $stopper_file_path");
					    $this->unRegisterPumper();
					    exit();
					}
					$this->info("cycle $cycle without working. Sleeping.....");
					continue;
				} else {
					$this->info("Max Cycle $cycle. Bye!");
					break;
				}
			}
			$serverFrameInfo = $serverFrameInfo[0];
			$task = $serverFrameInfo['IdSync'];
			$state_task = $serverFrameInfo['State'];
			$this->serverFrame = new ServerFrame($task);
			$IdSync = $this->serverFrame->get('IdSync');
			if (empty($IdSync))
			{
			    $this->fatal("ServerFrame not found :". $task);
			}
			$this->info("ServerFrame $IdSync to proccess.");
			$this->getHostConnection();
			if ($state_task == ServerFrame::DUE2IN) {
			    $this->connection->setIsFile(false);
			    $this->uploadAsHiddenFile();
			    $this->updateStats("DUE2IN");
			} elseif ($state_task == ServerFrame::DUE2OUT) {
				$this->updateStats("DUE2OUT");
				$this->RemoveRemoteFile();
			} elseif ($state_task == ServerFrame::PUMPED) {
			    $this->connection->setIsFile(true);
				$this->updateStats("PUMPED");
				$this->pumpFile();
			}
		} // End while(true)
		$this->unRegisterPumper();
	}

	private function uploadAsHiddenFile()
	{
		$localPath = $this->localBasePath."/";
		$initialDirectory = $this->server->get('InitialDirectory');
		$IdSync = (int)  $this->serverFrame->get('IdSync');
		$remotePath = $this->serverFrame->get('RemotePath');
		$fileName = $this->serverFrame->get('FileName');
		$this->info("ServerFrame $IdSync DUE2IN: upload as hidden file ");
		$originFile = "{$localPath}{$IdSync}";
		$targetFile = ".{$IdSync}_{$fileName}";
		$uploading = $this->taskUpload($originFile, $initialDirectory, $remotePath, $targetFile);
		$this->updateTask($uploading, ServerFrame::PUMPED);
	}

	private function RemoveRemoteFile()
	{
		$IdSync = (int) $this->serverFrame->get('IdSync');
		$initialDirectory = $this->server->get('InitialDirectory');
		$fileName = $this->serverFrame->get('FileName');
		$remotePath = $this->serverFrame->get('RemotePath');
		$this->info("ServerFrame $IdSync DUE2OUT: Download file from server");
		$targetFile = "{$initialDirectory}{$remotePath}/{$fileName}";
		$removing = $this->taskDelete($targetFile);
		if ($removing) {
		    Logger::info('Successfusly removed file ' . $fileName . ' from server', true);
		}
		$this->updateTask($removing, ServerFrame::OUT);
	}

	private function RenameFile($file)
	{
		$initialDirectory = $this->server->get('InitialDirectory');
		$remotePath = $file['RemotePath'];
		$IdSync = (int) $file['IdSync'];
		$fileName =  $file['FileName'];
		$targetFolder = "{$initialDirectory}{$remotePath}";
		if (substr($targetFolder, -1) != '/') {
		    $targetFolder .= '/';
		}
		$originFile = "{$targetFolder}.{$IdSync}_{$fileName}";
		$targetFile = $fileName;
		$this->info("Renaming file: $originFile -> $targetFile");
		return $this->taskRename($originFile, $targetFolder,  $targetFile, $IdSync);
	}

	private function getFilesToRename($IdBatchUp, $IdServer)
	{
		$IdSync = (int)  $this->serverFrame->get('IdSync');
		$this->info("ServerFrame $IdSync Rename hidden file to final file ");
		$fields = 'IdSync, RemotePath, FileName';
		$state_pumped = " state = 'Pumped' ";
		$conditions = "{$state_pumped} AND IdBatchUp = %s AND IdServer = %s ";
		return $this->serverFrame->find($fields, $conditions,  array($IdBatchUp, $IdServer) , MULTI, false);
	}

	private function updateStateFiles($IdBatchUp, $IdServer)
	{
		$table = "ServerFrames";
		$stateToIn = " state = '" . ServerFrame::IN . "' ";
		$state_pumped = " state = 'Pumped' ";
		$conditions = "{$state_pumped} AND IdBatchUp = '{$IdBatchUp}' AND IdServer = '{$IdServer}'";
		$this->info("UPDATE TO  {$stateToIn} : {$conditions} ");
		$this->updateStats("IN");
		return $this->serverFrame->execute("UPDATE {$table} SET {$stateToIn} WHERE {$conditions}");
	}

	private function pumpFile()
	{
		$idBatchUp = (int) $this->serverFrame->get('IdBatchUp');
		$idServer =  (int) $this->serverFrame->get('IdServer');
		$idSync =  (int) $this->serverFrame->get('IdSync');
		$this->info("ServerFrame $idSync PUMPING ");
		$batch = new Batch($idBatchUp);
		$batchId = (int) $batch->get('IdBatch');
		$batchState = $batch->get('State');
		if (!empty($batchId) && ServerFrame::CLOSING == $batchState) {
			$filesToRename = $this->getFilesToRename($idBatchUp,$idServer);
			$totalToRename = count($filesToRename);
			if (is_array($filesToRename) && $totalToRename > 0) {
				$this->info("$totalToRename files to rename ");
				foreach ($filesToRename as $file) {
					 $renameResult = $this->RenameFile($file);
					 if ($renameResult) {
                         $this->finishTask($file["IdSync"]);
					 } elseif ($renameResult === false) {
					     
                         // If this rename task does not work, generates a infinite loop
                         $this->updateTask(false, ServerFrame::DUE2OUTWITHERROR);
					 } else {
					     
					     // If this rename task does not work, generates a infinite loop
					     $this->updateTask(false, ServerFrame::IN);
					 }
				}
			}
		}
	}

	private function updateTimeInPumper()
	{
		$this->pumper->set('CheckTime', time());
		$this->pumper->update();
	}

	private function activeWaiting()
	{
		sleep($this->sleepTime);
	}

	private function getHostConnection($retry = 0)
	{
		$this->updateTimeInPumper();
		if (is_null($this->connection)) {
			if (is_null($this->server)) {
				$this->server = new Server($this->pumper->get('IdServer'));
				$host = $this->server->get('Host');
				$port = $this->server->get('Port');
				$login = $this->server->get('Login');
				$passwd =  $this->server->get('Password');
				$idProtocol = $this->server->get('IdProtocol');
			}
			$this->connection = \Ximdex\IO\Connection\ConnectionManager::getConnection($idProtocol, $this->server);
		}
		$res = true;
		if (!$this->connection->isConnected()) {
			if ($this->connection->connect($host, $port)) {
			    if (!$this->connection->login($login, $passwd))
			    {
			        $this->error('Can\'t log the user into host: ' . $host);
			        $res = false;
			    }
			}
			else
			{
			    $this->error('Can\'t connect to host: ' . $host);
			    $res = false;
			}
		}
		if ($this->connection->getError()) {
		    $this->error($this->connection->getError());
		}
		if (!$this->connection->isConnected()) {
			$msg_error = sprintf('Fail to connect or wrong login credentials for server: %s:%s with user: %s',  $host, $port, $login);
			$this->fatal($msg_error);
			$this->updateTask(false);
			$this->updateServerState('Failed to connect');
			/*
			$this->unRegisterPumper();
			exit(200);
			*/
			$res = false;
		}
		$this->updateTimeInPumper();
		return $res;
	}

	private function taskBasic($baseRemoteFolder, $relativeRemoteFolder)
	{
		$msg_not_found_folder =  _('Could not find the base folder').": {$baseRemoteFolder}";
		$msg_cant_create_folder = _('Could not find or create the destination folder')." {$baseRemoteFolder}{$relativeRemoteFolder}";
		if (!$this->connection->cd($baseRemoteFolder))
		{
			$this->warning($msg_not_found_folder);
		}
		if (!$this->connection->mkdir($baseRemoteFolder . $relativeRemoteFolder, 0755, true))
		{
			$this->error($msg_cant_create_folder);
			return false;
		}
		return true;
	}

	private function taskUpload($localFile, $baseRemoteFolder, $relativeRemoteFolder, $remoteFile)
	{
	    if (!file_exists($localFile)) {
	        $this->error('The sync file: ' . $localFile . ' does not exist');
	        return false;
	    }
        $this->getHostConnection();
		if (!$this->taskBasic($baseRemoteFolder, $relativeRemoteFolder)) {
			return false;
		}
		$fullPath = $baseRemoteFolder . $relativeRemoteFolder;
		if (substr($relativeRemoteFolder, -1) != '/') {
		    $fullPath .= '/';
		}
		$fullPath .= $remoteFile;
		if ($this->connection->isFile($fullPath)) {
		    $this->warning("Uploading file: $fullPath file already exist");
		    return null;
		}
		$this->info("Copying $localFile in $fullPath");
		if (!$this->connection->put($localFile, $fullPath)) {
		    $this->error(_('Could not upload the file').": $localFile -> $fullPath");
		    if ($this->connection->getError()) {
		        $this->error($this->connection->getError());
		    }
			return false;
		}
		return true;
	}
	
	private function taskDelete($remoteFile)
	{
	    if ($this->connection->getType() == Connector::TYPE_API) {
	        if (!$this->serverFrame->get('IdNodeFrame')) {
	            $this->error('Cannot load the node frame from the current server frame');
	            return false;
	        }
	        $nodeFrame = new NodeFrame($this->serverFrame->get('IdNodeFrame'));
	        $id = $nodeFrame->get('NodeId');
	        if (!$id) {
	            $this->error('Cannot load the node ID from the current node frame');
	            return false;
	        }
	    }
	    else {
	        $id = null;
	    }
		return $this->connection->rm($remoteFile, $id);
	}

	private function taskRename($targetFile, $targetFolder, $newFile, $idSync = null)
	{
        $this->getHostConnection();
		if (!$this->taskBasic($targetFolder, ''))
		{
			return false;
		}
		if (!$this->connection->isFile($targetFile)) {
		    $this->warning("Renaming file: $targetFile file not found");
		    return null;
		}
		if (!$this->connection->rename($targetFile, $targetFolder . $newFile))
		{
		    $this->error("Could not rename the target document: {$targetFile} -> {$targetFolder}{$newFile} ");
            return false;
		}
		Logger::info('The file has been published succesfuslly' . ': ' . $newFile . ' (' . $idSync . ')', true);
		return true;
	}

	private function updateTask($result, $status = null)
	{
		$this->info('Processing ' . $this->serverFrame->get('IdSync'));
		if (!$result) {
			$retries = $this->serverFrame->get('Retry');
			$this->serverFrame->set('Retry', $retries);
			if ($retries >= self::RETRIES_TO_FATAL_ERROR) {
			    $this->error('Maximum of retries reached (' . self::RETRIES_TO_FATAL_ERROR . ') for server frame: ' 
			        . $this->serverFrame->IdSync . '. Marked as errored');
				$this->serverFrame->set('State', ServerFrame::DUE2INWITHERROR);
			}
			else {
			    $retries++;
			    $this->serverFrame->set('Retry', $retries);
			    $this->serverFrame->set('ErrorLevel', 1);
			}
			$this->serverFrame->update();
			return false;
		}
		if ($status !== null) {
		    $this->serverFrame->set('State', $status);
		    $this->serverFrame->set('Linked', 0);
		    $this->serverFrame->update();
		}
		$this->updateTimeInPumper();
	}
    
    private function finishTask($idSync)
    {
        $serverFrame = new ServerFrame($idSync);
        $serverFrame->set('State', ServerFrame::IN);
        $serverFrame->update();
        $this->updateTimeInPumper();
    }

	private function updateServerState($status)
	{
		if (!empty($status)) {
		    $sql = 'UPDATE ServerErrorByPumper SET WithError = 1, Error = \'' . $status . '\' WHERE ServerId = ' 
		        . $this->server->get('IdServer');
		    $this->server->query($sql);
			$this->server->set('ActiveForPumping', 1);
			$this->server->update();
		}
	}

	public function registerPumper()
	{
		$state_pumper = $this->pumper->get('State');
		if ('NEW' == $state_pumper) {
			$msg = "No ha sido posible registrar el bombeador al tener estado de NEW";
			$this->fatal($msg);
			$this->unRegisterPumper();
			exit(0);
		} else {
			$this->startPumper();
		}
	}

    private function unRegisterPumper()
    {
		$state_pumper = $this->pumper->get('State');
		if ('NEW' == $state_pumper) {
			$msg = "No ha sido posible registrar el bombeador al tener estado de NEW";
			$this->fatal($msg);
			exit(0);
		} else {
			$processId = $this->pumper->get('ProcessId');
            $this->pumper->set('State', 'Ended');
			$this->pumper->set('ProcessId','xxxx');
            $this->pumper->set('CheckTime', time());
            $this->pumper->update();
		}
	}

	public function startPumper()
	{
		$pid =  getmypid();
		$time = time();
		$this->pumper->set('State', 'Started');
		$this->pumper->set('ProcessId',$pid );
		$this->pumper->set('CheckTime',$time );
		$this->pumper->update();
		$this->info("Start pumper demond $time");
	}

	public function updateStats($state_pumper)
	{
	    if (!\Ximdex\Modules\Manager::isEnabled("wix")) {
	        return false;
	    }
		$IdSync = (int)  $this->serverFrame->get('IdSync');
 	  	$idBatchUp = (int) $this->serverFrame->get('IdBatchUp');
 	  	$idServer = (int) $this->serverFrame->get('IdServer');
 		$time = time();
 		if ("IN" == $state_pumper) {
 		    $progress =  "Progress = '100' ";
 		    $idSync = ""; // All batchs
		}
		else {
		    $progress = "Progress = '80' ";
		    $idSync = " AND IdSync='$IdSync' ";
		}
        $sqlReport = "UPDATE PublishingReport SET State = '{$state_pumper}', $progress  WHERE IdBatch = '" . $idBatchUp 
            . "' AND IdSyncServer = '" . $idServer . "' $idSync";
        $this->serverFrame->execute($sqlReport);
        $this->debug($sqlReport);
	}

	public function info($_msg = NULL)
	{
	    $this->msg_log("INFO PUMPER: $_msg");
	    Logger::info($_msg);
	}
	public function error($_msg = NULL)
	{
	    $this->msg_log("ERROR PUMPER: $_msg");
	    Logger::error($_msg);
	}
	
	public function fatal($_msg = NULL)
	{
	    $this->msg_log("FATAL PUMPER: $_msg");
	    Logger::fatal($_msg);
	}
	
	public function debug($_msg = NULL)
	{
	    $this->msg_log("DEBUG PUMPER: $_msg");
	    Logger::debug($_msg);
	}
	
	public function warning($_msg = NULL)
	{
	    $this->msg_log("WARNING PUMPER: $_msg");
	    Logger::warning($_msg);
	}

	public function msg_log($_msg)
	{
		$pumperID = (int) $this->pumper->get('PumperId');
		$_msg = "[PumperId: $pumperID] ".$_msg;
		error_log($_msg);
	}
}

$parameterCollector = new DexPumperCli($argc, $argv);
$dexPumper = new DexPumper($parameterCollector->getParametersArray());
Logger::generate('PUBLICATION', 'publication');
Logger::setActiveLog('publication');
$dexPumper->start();
