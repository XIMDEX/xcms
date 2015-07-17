#!/usr/bin/php
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



if (!defined('XIMDEX_ROOT_PATH')) {
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__) . "/../../../../../"));
}

//

ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColector.php', 'ximNEWS');
ModulesManager::file('inc/sync/Mutex.class.php');
ModulesManager::file('/inc/log/Automatic_log.class.php');
ModulesManager::file('/inc/MPM/MPMManager.class.php');
ModulesManager::file('/inc/MPM/MPMProcess.class.php');


GLOBAL $generate_pid;
$generate_pid = posix_getpid();
$stopperFilePath = \App::getValue( "AppRoot") . \App::getValue( "TempRoot") . "/automatic.stop";
$msg_lck = _("STOP: Detected file")." $stopperFilePath "._("You need to delete this file in order to get an Automatic succesfull restart");

$mutex = new Mutex(\App::getValue( "AppRoot") . \App::getValue( "tmpRoot") . "/generate.lck");
if (!$mutex->acquire()) {
	Automatic_Log::info("Automatic previo en ejecucion");
	exit(1);
}

Automatic_Log::info("Starting Automatic");

// Si son horas se obtienen los colectores con fuelle

$minHourFuelle = mkTime(\App::getValue( 'StartCheckNoFuelle'), 0, 0, date('m', $now), date('d', $now), date('Y', $now));
$maxHourFuelle = mktime(\App::getValue('EndCheckNoFuelle'), 0, 0, date('m', $now), date('d', $now), date('Y', $now));

$colectoresConFuelle = array();

if ($now > $minHourFuelle && $now < $maxHourFuelle) {
	$colectoresConFuelle = $relNewsColector->colectoresConFuelle();
}

$ximNewsBulletin = new XimNewsBulletin();
$ximNewsColector = new XimNewsColector();
$colectors = $ximNewsColector->getAllColectors();
$numColectors = count($colectors);
$actualColector = 0;

Automatic_Log::info("$numColectors colectors to be processed");

foreach ($colectors as $colectorID => $colectorName) {
	$generados="";

	// STOPPER
	if (file_exists($stopperFilePath)) {
		$mutex->release();
		Automatic_Log::info($msg_lck);
		die($msg_lck."\n");
	}

	$actualColector ++;
	$colectorLogHead = _("Collector")." ($actualColector "._("of")." $numColectors) '[$colectorID] $colectorName': ";
	Automatic_Log::info($colectorLogHead . _("Start processing"));

	$totalGeneration = NULL;
	$generate = false;

	$ximNewsColector = new XimNewsColector($colectorID);
	$nodeColector = new Node($colectorID);

	// Total generation and without bellow for these collectors generated without bellow
	if (in_array($colectorID, $colectoresConFuelle)) {

		$now2 = mktime();

		// Checking again time in order to avoid to get over the fixed interval
		if ($now2 > $minHourFuelle && $now2 < $maxHourFuelle) {
			$generate = true;
			$totalGeneration = 2;

			Automatic_Log::info($colectorLogHead . _("Bellows-less generation"));
		}
	}


	if ($nodeColector->class->isGenerable()) {
		$generate = true;
	}

	// Checking if colector is locked

	$lockColector = $ximNewsColector->get('Locked');

	if ($lockColector == 1) {
		Automatic_Log::info($colectorLogHead . _("Locked (Maybe colector's being generated at this moment)") );
		$generate = false;
	}

	if ($generate == true) {

		Automatic_Log::info($colectorLogHead . _("Starting generation") );
		$generados = $nodeColector->class->generateColector($totalGeneration);

		// STOPPER
		if (file_exists($stopperFilePath)) {
			$mutex->release();
			Automatic_Log::info($msg_lck);
			die($msg_lck."\n");
		}

		Automatic_Log::info($colectorLogHead . _("Ending generation"));
	} elseif ($ximNewsColector->get('State') == 'generated') {

		$generados = $ximNewsBulletin->getPublishableBulletins($colectorID);
	}

	if(!empty($generados)){
		$numBulletins = count($generados);
		$actualBulletin = 0;
		Automatic_Log::info($colectorLogHead . _("Generation finished")." ($numBulletins "._("bulletins").")");

		// Generated bulletins are published since now until infinite

		foreach($generados as $bulletinID){

			$actualBulletin ++;
			Automatic_Log::info($colectorLogHead . _("Publishing bulletin")." $bulletinID ($actualBulletin "._("of")." $numBulletins)");

			if (ModulesManager::isEnabled('ximSYNC')) {
				include_once(XIMDEX_ROOT_PATH . "/modules/ximSYNC/inc/manager/SyncManager.class.php");

				// STOPPER
				if (file_exists($stopperFilePath)) {
					$mutex->release();
					Automatic_Log::info($msg_lck);
					die($msg_lck."\n");
				}

				// Get news to publish
				$docsToPublish = array();

				$bulletinNode = new Node($bulletinID);
				$docsToPublish = $bulletinNode->class->getNewsToPublish($colectorID);

				if (sizeof($docsToPublish) > 0) {
					array_push($docsToPublish, $bulletinID);
				} else {
					$docsToPublish = array($bulletinID);
				}

				$numDocs = count($docsToPublish);
				$actualDoc = 0;
				Automatic_Log::info($colectorLogHead . "$numDocs "._("docs to be published"));
				$dataIn = array();
				$i=0;

				foreach ($docsToPublish as $docID) {

					// STOPPER
					if (file_exists($stopperFilePath)) {
						$mutex->release();
						Automatic_Log::info($msg_lck);
						die($msg_lck."\n");
					}

					$dataIn[$i]=array($docID,$colectorID,time());
					$i++;
				}

				pushAllDocumentsInPublishingPool($dataIn);

			} else {
				SynchroFacade::pushDocInPublishingPool($bulletinID, time(), NULL);
			}
		}
		$ximNewsColector = new XimNewsColector($colectorID);
		$ximNewsColector->set('State', 'published');
		$ximNewsColector->update();
	} else {
		$generados = array();
		Automatic_Log::info($colectorLogHead . _("No generation needed"));
	}

	Automatic_Log::info($colectorLogHead . _("Ending processing"));
}

Automatic_Log::info(_("Exiting Automatic"));
$mutex->release();

function pushAllDocumentsInPublishingPool($dataIn){
	Automatic_Log::info(sprintf(_("parallelization begins with %d documents"), count($dataIn) ));
	$callback = array("/modules/ximSYNC/inc/manager/SyncManager", "pushDocInPublishingPoolForMPM");
	$mpm = new MPMManager($callback, $dataIn, MPMProcess::MPM_PROCESS_OUT_BOOL,4,3);
	$mpm->run();
	Automatic_Log::info(_("Ended parallelization"));
}


?>