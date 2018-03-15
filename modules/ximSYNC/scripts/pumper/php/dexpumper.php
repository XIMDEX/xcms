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
use Ximdex\Models\Pumper;
use Ximdex\Models\Server;
use Ximdex\Runtime\App;
use Ximdex\Cli\CliParser;

// for legacy compatibility
if (!defined('XIMDEX_ROOT_PATH')) {
    require_once dirname(__FILE__) . '/../../../../../bootstrap.php';
}


function showErrors($errno, $errstr, $errfile = NULL, $errline= NULL) {
                $_msg = "FATAL ERROR:$errno, $errstr. $errfile : $errline. TRACE:  ".print_r(debug_backtrace(),true);
}
set_error_handler("showErrors");


\Ximdex\Modules\Manager::file('/inc/model/ServerFrame.class.php', 'ximSYNC');


    /*
     * Constants definition
     * Must be in conf
    */

// Errores:


define('PUMPER_ERROR_LEVEL_OK', 0);
define('PUMPER_ERROR_LEVEL_SOFT', 1);
define('PUMPER_ERROR_LEVEL_HARD', 2);
define('PUMPER_ERROR_LEVEL_FATAL', 3);


class DexPumperCli extends CliParser {
        var $_metadata = array(
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

        function __construct ($paramsCount, $params) {
                $this->_metadata[0]['message'] = _('Username (or identifier)');
                $this->_metadata[1]['message'] = _('Indicates the verbosity level of the log');
                $this->_metadata[2]['message'] = _('Number of empty cycles before the pumper end');
                $this->_metadata[3]['message'] = _('Identifier of pumper to be processed');
                $this->_metadata[4]['message'] = _('Base path of the documents');
                $this->_metadata[5]['message'] = _('Identifier of server to be tested');
                parent::__construct($paramsCount, $params);
        }
}

/**
 * @author jmgomez
 *
 */
class DexPumper {
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

	function __construct($params) {

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

	function start() {
		$cycle = 0;
		$this->registerPumper();

		while (true) {
			if(!$cycle) {
				$this->info("STARTING PUMPER CYCLE");
			}else {
				$this->info("PUMPER CYCLE");
			}

			$serverFrame = new ServerFrame();
			$pumperID = $this->pumper->get('PumperId');

			$serverFrameInfo = $serverFrame->getPublishableNodesForPumper($pumperID);
			$countNodes = count($serverFrameInfo);

			$this->info("$countNodes nodes for pumping with PumperID: " . $pumperID);
			
			// exit condition here when cycles reach max void cycles
			if (empty($serverFrameInfo)) {
				$cycle ++;
				if ($cycle <= $this->maxVoidCycles) {
					$this->updateTimeInPumper();
					$this->activeWaiting();
					
					// Manual stop for pumpers in sleeping mode
					$stopper_file_path = XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/pumper.stop";
					if (file_exists($stopper_file_path)) {
					    Logger::warning('[PUMPERS] ' . _("STOP: Detected file") . " $stopper_file_path");
					    break;
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

			if (empty($IdSync) ) { $this->fatal("ServerFrame not found :". $task); }

			$this->info("ServerFrame $IdSync to proccess.");

			$this->getHostConnection();
			if ($state_task == ServerFrame::DUE2IN) {
					$this->uploadAsHiddenFile();
					$this->updateStats("DUE2IN");
			} elseif ($state_task == ServerFrame::DUE2OUT) {
				$this->updateStats("DUE2OUT");
				$this->RemoveRemoteFile();
			} elseif ($state_task == ServerFrame::PUMPED) {
				$this->updateStats("PUMPED");
				$this->pumpFile();
			}
		} // end while(true)
		$this->unRegisterPumper();
	}


	private function uploadAsHiddenFile() {
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

	private function  RemoveRemoteFile() {
		$IdSync = (int)  $this->serverFrame->get('IdSync');
		$initialDirectory = $this->server->get('InitialDirectory');
		$fileName = $this->serverFrame->get('FileName');
		$remotePath = $this->serverFrame->get('RemotePath');

		$this->info("ServerFrame $IdSync DUE2OUT: download file from server ");


		$targetFile = "{$initialDirectory}{$remotePath}/{$fileName}";

		$removing = $this->taskDelete($targetFile);
		$this->updateTask($removing, ServerFrame::OUT);
	}

	private function RenameFile($file)
	{
		$initialDirectory = $this->server->get('InitialDirectory');
		$remotePath = $file['RemotePath'];
		$IdSync = (int) $file['IdSync'];
		$fileName =  $file['FileName'];
		$targetFolder = "{$initialDirectory}{$remotePath}/";
		$originFile = "{$targetFolder}.{$IdSync}_{$fileName}";
		$targetFile = $fileName;
		$this->info("Renaming file: $originFile -> $targetFile");
		return $this->taskRename($originFile, $targetFolder,  $targetFile, $IdSync);
	}

	private function getFilesToRename($IdBatchUp, $IdServer) {
	    
		$IdSync = (int)  $this->serverFrame->get('IdSync');
		$this->info("ServerFrame $IdSync Rename hidden file to final file ");

		$fields = 'IdSync, RemotePath, FileName';
		$state_pumped = " state = 'Pumped' ";
		$conditions = "{$state_pumped} AND IdBatchUp = %s AND IdServer = %s ";

		return $this->serverFrame->find($fields, $conditions,  array($IdBatchUp, $IdServer) , MULTI, false);
	}

	private function updateStateFiles($IdBatchUp, $IdServer) {
		$table = "ServerFrames";
		$stateToIn = " state = 'In' ";
		$state_pumped = " state = 'Pumped' ";
		$conditions = "{$state_pumped} AND IdBatchUp = '{$IdBatchUp}' AND IdServer = '{$IdServer}'";

		$this->info("UPDATE TO  {$stateToIn} : {$conditions} ");

		$this->updateStats("IN");
		return $this->serverFrame->execute("UPDATE {$table} SET {$stateToIn} WHERE {$conditions}");
	}

	private function pumpFile() {
	    
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
					     
					     //TODO check the conflict with process
                         // If this rename task does not work, generates a infinite loop
                         $this->updateTask(0, ServerFrame::DUE2OUTWITHERROR);
					 } else {
					     
					     // If this rename task does not work, generates a infinite loop
					     $this->updateTask(0, ServerFrame::IN);
					 }
				}
			}
		}
	}

	// DONE
	private function updateTimeInPumper() {
		$this->pumper->set('CheckTime', time());
		$this->pumper->update();
	}

	//DONE
	private function activeWaiting() {
		sleep($this->sleepTime);
	}

	//DONE
	private function getHostConnection($retry = 0) {
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

			$this->connection = \Ximdex\IO\Connection\ConnectionManager::getConnection($idProtocol);
		}

		if (!$this->connection->isConnected()) {
			if ($this->connection->connect($host, $port)) {
			    if (!$this->connection->login($login, $passwd))
			    {
			        $this->error('Can\'t log the user into host');
			    }
			}
			else
			{
			    $this->error('Can\'t connect to host');
			}
		}

		if (!$this->connection->isConnected()) {
			$msg_error = sprintf('Fail to connect o wrong login credentials for server: %s:%s with user: %s',  $host, $port, $login);
			$this->fatal($msg_error);
			$this->updateServerState('Failed to connect');
			exit(200);
		}

		$this->updateTimeInPumper();

		return true;
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
        $this->getHostConnection();
		if ( !$this->taskBasic($baseRemoteFolder, $relativeRemoteFolder) ) {
			return false;
		}
		$fullPath = $baseRemoteFolder . $relativeRemoteFolder . '/' . $remoteFile;
		if ($this->connection->isFile($fullPath)) {
		    $this->warning("Uploading file: $fullPath file already exist");
		    return null;
		}
		$this->info("Copying $localFile in $fullPath");
		if (!$this->connection->put($localFile, $fullPath)) {
		    $this->error(_('Could not upload the file').": $localFile -> $fullPath");
			return false;
		}
		return true;
	}
	
	private function taskDelete($remoteFile)
	{
		return $this->connection->rm($remoteFile);
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
		Logger::info(_('The file has been published succesfuslly') . ': ' . $newFile . ' (' . $idSync . ')', true);
		return true;
	}

	private function updateTask($result, $status = null) {
		$this->info('Processing ' . $this->serverFrame->get('IdSync'));
		if (!$result > 0) {
			$retries = $this->serverFrame->get('Retry');
			$this->serverFrame->set('Retry', $retries);
			if ($retries > self::RETRIES_TO_FATAL_ERROR) {
				$this->error("State  'Due2' . $status . 'WithError'");
			}
		}

		$this->serverFrame->set('ErrorLevel', $result ? 0 : 1);
		$retries ++;
		$this->serverFrame->set('Retry', $retries);
		if ($status !== null) {
		    $this->serverFrame->set('State', $status);
		}
		$this->serverFrame->set('Linked', 0);
		$this->serverFrame->update();

		$this->updateTimeInPumper();
	}
        
    private function finishTask($idSync){
            $serverFrame = new ServerFrame($idSync);
            $serverFrame->set('State', ServerFrame::IN);
            $serverFrame->update();
            $this->updateTimeInPumper();
        }

	//DONE
	private function updateServerState($status) {
		if (!empty($status)) {
		    $this->server->query("UPDATE ServerErrorByPumper SET WithError='$status' WHERE ServerId=" . $this->server->get('IdServer'));

			$this->server->set('ActiveForPumping', 1);
			$this->server->update();
		}
	}

	public function registerPumper() {
		$state_pumper = $this->pumper->get('State');

		if ('NEW' == $state_pumper) {
			$msg = "No ha sido posible registrar el bombeador al tener estado de NEW";
			$this->fatal($msg);
			exit(0);
		} else {
			$this->startPumper();
		}
	}

    private function unRegisterPumper() {
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


	public function startPumper() {
			$pid =  getmypid();
			$time = time();

			$this->pumper->set('State', 'Started');
			$this->pumper->set('ProcessId',$pid );
			$this->pumper->set('CheckTime',$time );
			$this->pumper->update();
			$this->info("Start pumper demond $time");
	}

	public function updateStats($state_pumper) {
		if(!\Ximdex\Modules\Manager::isEnabled("wix") ) return false;

		 $IdSync = (int)  $this->serverFrame->get('IdSync');
 	  	$idBatchUp = (int) $this->serverFrame->get('IdBatchUp');
			$idServer =  (int) $this->serverFrame->get('IdServer');

 		 $time = time();

		 if("IN" == $state_pumper) {
				$progress =  "Progress = '100' ";
				$idSync = ""; //all batch
		 }else {
				$progress = "Progress = '80' ";
				$idSync = " AND IdSync='$IdSync' ";
		 }

        $sqlReport = "UPDATE PublishingReport SET State = '{$state_pumper}', $progress  WHERE IdBatch = '" . $idBatchUp . "' AND IdSyncServer = '" . $idServer . "' $idSync  ";

			$this->serverFrame->execute($sqlReport);
			$this->debug($sqlReport);
	}

	public function info($_msg = NULL) { $this->msg_log("INFO PUMPER: $_msg"); Logger::info($_msg); }
	public function error($_msg = NULL) { $this->msg_log("ERROR PUMPER: $_msg"); Logger::error($_msg); }
	public function fatal($_msg = NULL) { $this->msg_log("FATAL PUMPER: $_msg"); Logger::fatal($_msg); }
	public function debug($_msg = NULL) { $this->msg_log("DEBUG PUMPER: $_msg"); Logger::debug($_msg); }
	public function warning($_msg = NULL) { $this->msg_log("WARNING PUMPER: $_msg"); Logger::warning($_msg); }

	public function msg_log($_msg) {
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
