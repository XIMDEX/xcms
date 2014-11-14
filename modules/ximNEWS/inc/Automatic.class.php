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



ModulesManager::file('/inc/sync/Mutex.class.php');
ModulesManager::file('/inc/persistence/Config.class.php');
ModulesManager::file('/inc/log/Automatic_log.class.php');
ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsColectorUsers.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/Profiler.class.php', 'ximPROFILER');


if (!defined('XIMDEX_ROOT_PATH')) {
        define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../');
}

/**
*	@brief Manages the proccess that generates bulletins automatically.
*/

class Automatic {
	private $mutex;
	private $stopperFilePath;
	private $docsToPublish;
	
	private $minHourFuelle;
	private $maxHourFuelle;
	private $now;
	
	function __construct() {
	    Automatic_Log::info("Starting Automatic");
	    GLOBAL $generate_pid;
	    $generate_pid = posix_getpid();
		$this->stopperFilePath = Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/automatic.stop";
	
		$this->mutex = new Mutex(Config::getValue("AppRoot") . Config::getValue("TempRoot") . "/generate.lck");
		if (!$this->mutex->acquire()) {
	        Automatic_Log::fatal("Automatic previo en ejecucion");
	    }
	    $this->now = mktime();
	    
		$this->minHourFuelle = mkTime(Config::getValue('StartCheckNoFuelle'), 
			0, 0, date('m', $this->now), date('d', $this->now), date('Y', $this->now));
		$this->maxHourFuelle = mktime(Config::getValue('EndCheckNoFuelle'), 
			0, 0, date('m', $this->now), date('d', $this->now), date('Y', $this->now));
	}
	
    function checkStopper() {
		if (file_exists($this->stopperFilePath)) {
			$this->mutex->release();
			Automatic_Log::info("STOP: Detected file {$this->stopperFilePath}. You need to delete this file for successful restart of automatic");
			die("STOP: Detected file {$this->stopperFilePath}. You need to delete this file for successful restart of automatic.\n");
		}
    }
	
	function process($colectors = NULL) {
		// Si son horas se obtienen los colectores con fuelle
	
		$colectoresConFuelle = array();
		$relNewsColector=new RelNewsColector();
	
		if ($this->now > $this->minHourFuelle && $this->now < $this->maxHourFuelle) {
			$colectoresConFuelle = $relNewsColector->colectoresConFuelle();
		}
		
		if (!empty($colectors) && !is_array($colectors)) {
			$colectors = array($colectors);
		}
	    if (empty($colectors)) {
			$ximNewsColector = new XimNewsColector();
		    $colectors = $ximNewsColector->getAllColectors();
	    }
	    $numColectors = count($colectors);
	    $actualColector = 0;
	
	    Automatic_Log::info("$numColectors colectors to be processed");

	    $bulletins = array();
	    foreach ($colectors as $colectorID => $colectorName) {
		$this->checkStopper();
	    	$actualColector ++;
			$colectorLogHead = "Colector ($actualColector of $numColectors) '[$colectorID] $colectorName': ";
			Automatic_Log::info($colectorLogHead . "Start processing");
	    	$bulletins = array_merge($this->_processColector($colectorID, $colectorName, $colectoresConFuelle, $colectorLogHead),
	    		$bulletins);
	    }
	
		Automatic_Log::info("Exiting Automatic");
		$this->mutex->release();
		return $bulletins;
	}
	
	function _processColector($colectorID, $colectorName, $colectoresConFuelle, $colectorLogHead) {
    	$generados = array();

		$totalGeneration = NULL;
		$generate = false;

		$ximNewsColector = new XimNewsColector($colectorID);
		$ximNewsColectorUsers = new ximNewsColectorUsers();
		$nodeColector = new Node($colectorID);
		// Se hace generacion total y sin fuelle de los colectores que se hayan generado con fuelle
		if (in_array($colectorID, $colectoresConFuelle)) {
			$now2 = time();
			// Nuevo chequeo de hora para no sobrepasar el intervalo fijado
			if ($now2 > $this->minHourFuelle && $now2 < $this->maxHourFuelle) {
				$generate = true;
				$totalGeneration = 2;

				Automatic_Log::info($colectorLogHead . "Fuelle-less generation");
			}
		}

		// Checking if colector is locked
		$lockColector = $ximNewsColector->get('Locked');
		if ($lockColector == 1) {
			Automatic_Log::info($colectorLogHead . "Locked (Maybe colector's being generated at this moment)");
			$generate = false;
		//isGenerable also update states from relNewsColectors and checks inactive property
		}else if ($nodeColector->class->isGenerable()) {
			$generate = true;			
		} 

		$idNewsColectorUsers = $ximNewsColectorUsers->add($colectorID, 0, 'generating');
		
		if ($generate == true) {
			Automatic_Log::info($colectorLogHead . "Starting generation");
			$this->checkStopper();
			$generados = $nodeColector->class->generateColector($totalGeneration);
			Automatic_Log::info($colectorLogHead . "Ending generation");
			$this->checkStopper();
		} elseif ($ximNewsColector->get('State') == 'generated') {
			Automatic_Log::info($colectorLogHead . "Bulletin already generated");
			$this->checkStopper();
			$ximNewsBulletin = new XimNewsBulletin();
			$generados = $ximNewsBulletin->getPublishableBulletins($colectorID);
			$this->checkStopper();
		}
		
		if(!is_null($idNewsColectorUsers)) {
			$cu = new XimNewsColectorusers($idNewsColectorUsers);  
			$cu->set('EndGenerationTime', mktime()); 
			$cu->set('Progress', 50); 
			$cu->set('State', 'publishing'); 
			$cu->update();
		}
		

		if(!empty($generados)){
			$numBulletins = count($generados);
			$actualBulletin = 0;
			Automatic_Log::info($colectorLogHead . "Successfully generation ($numBulletins bulletins)");

			//Los boletines generados se publican desde ya hasta el infinito
			foreach($generados as $bulletinID){
				$actualBulletin ++;
				Automatic_Log::info($colectorLogHead . "Publishing bulletin $bulletinID ($actualBulletin of $numBulletins)");
				$this->_processBulletin($colectorID, $bulletinID, $colectorLogHead);
				if(!is_null($idNewsColectorUsers)) {
					$cu->set('Progress', 50 + floor(($actualBulletin / $numBulletins) * 50)); 
					$cu->update();
				}
			}
			$ximNewsColector = new XimNewsColector($colectorID);
			$ximNewsColector->set('State', 'published');
			$ximNewsColector->update();
		} else {
			$generados = array();
			Automatic_Log::info($colectorLogHead . "No generation needed");
		}

		
		if(!is_null($idNewsColectorUsers)) {
			$cu->set('EndPublicationTime', mktime());
			$cu->set('Progress', 100);
			$cu->set('State', 'published');
			$cu->update();
		}
		
		Automatic_Log::info($colectorLogHead . "Ending processing");
		return $generados;
	}
	
	function _processBulletin($colectorID, $bulletinID, $colectorLogHead) {
		
		$this->checkStopper();
		
		if (ModulesManager::isEnabled('ximSYNC')) {							
			include_once(XIMDEX_ROOT_PATH . "/modules/ximSYNC/inc/manager/SyncManager.class.php");
			// Get news to publish
			$this->docsToPublish = array();

			$bulletinNode = new Node($bulletinID);
			$this->docsToPublish = $bulletinNode->class->getNewsToPublish($colectorID);
			$this->docsToPublish[] = $bulletinID;

			$numDocs = count($this->docsToPublish);
			$actualDoc = 0;
			Automatic_Log::info($colectorLogHead . "$numDocs docs to be published");
			Profiler::start($colectorID."_".$bulletinID."_Serie");

			foreach ($this->docsToPublish as $docID) {
				
				$this->checkStopper();
				$actualDoc ++;
				Automatic_Log::info($colectorLogHead . "Starting doc publication [$docID] ($actualDoc of $numDocs)");
				$syncMngr = new SyncManager();
//				$syncMngr->setFlag('type', 'ximNEWS');
				$syncMngr->setFlag('colector', $colectorID);
				$syncMngr->pushDocInPublishingPool($docID, time(), NULL);
			}
			Profiler::stop($colectorID."_".$bulletinID."_Serie");
		} else {
			SynchroFacade::pushDocInPublishingPool($bulletinID, time(), NULL);
		}
	}
}
?>