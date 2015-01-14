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



use Ximdex\Utils\Session;

ModulesManager::file('/inc/manager/ServerFrameManager.class.php');
ModulesManager::file('/inc/model/PublishingReport.class.php', 'ximSYNC'); 

class Action_managebatchs extends ActionAbstract {
	
	// Main method: shows initial form
	
	function index () {
		$acceso = true;
		// Initializing variables.
		$userID = Session::get('userID');
		
		$user = new User();
		$user->SetID($userID);

		if(!$user->HasPermission("view_publication_resume")) {
			$acceso = false;
			$errorMsg = "You have not access to this report. Consult an administrator.";
		}
		
		
		$jsFiles = array(
			App::getValue('UrlRoot') .ModulesManager::path('ximPUBLISHtools'). '/actions/managebatchs/resources/js/index.js',
			App::getValue('UrlRoot') . '/inc/js/ximtimer.js'
		);
		
		$cssFiles = array(
			App::getValue('UrlRoot') .ModulesManager::path('ximPUBLISHtools').'/actions/managebatchs/resources/css/index.css'
		);

		$arrValores = array(
			'acceso' => $acceso,
			'errorBox' => $errorMsg,
			'js_files' => $jsFiles,
			'css_files' => $cssFiles
		);
		
		$this->render ($arrValores, NULL, 'default-3.0.tpl');
	}
	
	public function getFrameList() {
	        $idNode = (int) $this->request->getParam("nodeid"); 
                $idBatch = (int) $this->request->getParam("batchid"); 
                 
 		if($idBatch > 0) { 
                   $batchObj = new Batch($idBatch); 
                   return $batchObj->setBatchPlayingOrUnplaying($idBatch, 2); 
                } 
            
		$pr = new PublishingReport(); 
                $frames = $pr->getReportByIdNode($idNode); 

		$json = Serializer::encode(SZR_JSON, $frames);
		
		$values = array(
			'result' => $json
		);

		$this->render($values, NULL, "only_template.tpl");
	}
	
	function batchlist () {
		
		// Initializing variables of actions.
		$errorMsg = "";
		
		if (isset($_POST['frm_prioritize_batch']) && $_POST['frm_prioritize_batch'] == "yes") {
			
			if (!$result = $this->doPrioritizeBatch($_POST['frm_id_batch'])) {
				
				$errorMsg = "An error occurred while prioritizing batch.";
			} else {
				
				$errorMsg = "Batch #" . $_POST['frm_id_batch'] . " has been prioritized.";
			}
		}
		
		if (isset($_POST['frm_deprioritize_batch']) && $_POST['frm_deprioritize_batch'] == "yes") {
			
			if (!$result = $this->doPrioritizeBatch($_POST['frm_id_batch'], 'down')) {
				
				$errorMsg = "An error occurred while down priority of batch.";
			} else {
				
				$errorMsg = "Priority of batch has been down #" . $_POST['frm_id_batch'];
			}
		}
		
		if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_deactivate_batch'] == "yes") {
			
			if (!$result = $this->doDeactivateBatch($_POST['frm_id_batch'])) {
				
				$errorMsg = "An error occurred while deactivate batch.";
			} else {
				
				if($_POST['frm_id_batch'] == "all") {
					
					$errorMsg = "All batches have been deactivated.";
				} else {
					
					$errorMsg = "Batch #" . $_POST['frm_id_batch'] . " has been deactivated.";
				}
			}
		}
		
		if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_activate_batch'] == "yes") {
			
			if (!$result = $this->doActivateBatch($_POST['frm_id_batch'])) {
				
				$errorMsg = "An error occurred while activating batch.";
			} else {
				
				if($_POST['frm_id_batch'] == "all") {
				
					$errorMsg = "All batches have been activated.";
				} else {
					
					$errorMsg = "Batch #" . $_POST['frm_id_batch'] . " has been activated.";
				}
			}
		}
		
		$frm_select_filter_node_gen = (isset($_POST['frm_select_filter_node_gen'])) ? $_POST['frm_select_filter_node_gen'] : "";
		$frm_select_filter_state_batch = (isset($_POST['frm_select_filter_state_batch'])) ? $_POST['frm_select_filter_state_batch'] : "InTime";
		$frm_select_filter_active_batch = (isset($_POST['frm_select_filter_active_batch'])) ? $_POST['frm_select_filter_active_batch'] : 'NULL';
		$frm_select_filter_up_date = (isset($_POST['frm_filter_up_date']) && $_POST['frm_filter_up_date'] != 0) ? strtotime($_POST['frm_filter_up_date']) : 0;
		$frm_select_filter_down_date = (isset($_POST['frm_filter_down_date']) && $_POST['frm_filter_down_date'] != 0) ? strtotime($_POST['frm_filter_down_date']) : 0;
		$frm_filter_batch = (isset($_POST['frm_select_filter_state_batch'])) ? ((isset($_POST['frm_filter_batch'])) ? $_POST['frm_filter_batch'] : 'no') : 'yes';

		$arrayDateValues = array();
		$arrayDateValues['update'] = (isset($_POST['update'])) ? $_POST['update'] : "Click Aqui...";
		$arrayDateValues['uphour'] = (isset($_POST['uphour'])) ? $_POST['uphour'] : "00";
		$arrayDateValues['upmin'] = (isset($_POST['upmin'])) ? $_POST['upmin'] : "00";
		$arrayDateValues['downdate'] = (isset($_POST['downdate'])) ? $_POST['downdate'] : "Click Aqui...";
		$arrayDateValues['downhour'] = (isset($_POST['downhour'])) ? $_POST['downhour'] : "00";
		$arrayDateValues['downmin'] = (isset($_POST['downmin'])) ? $_POST['downmin'] : "00";
		
		$acceso = true;
		// Initializing variables.
		$userID = Session::get('userID');
		
		$user = new User();
		$user->SetID($userID);

		if(!$user->HasPermission("view_publication_resume")) {
			$acceso = false;
			$errorMsg = "No tiene acceso a este informe. Consulte a un administrador.";
		}
		
		$jsFiles = array(
			//App::getValue('UrlRoot') . '/xmd/js/lib/prototype/prototype.js',
			App::getValue('UrlRoot') . ModulesManager::path('ximPUBLISHtools').'/actions/managebatchs/resources/js/batchlist.js'
		);
		
		$arrayStates = array (
			'Batch' => array (
				'Waiting' => 'En Cola',
				'InTime' => 'En Curso',
				'Ended' => 'Finalizado'
			),
			'ServerFrame' => array (
				'Pending' => 'Pendiente',
				'Due2In' => 'Preparado para publicar',
				'Due2PumpedWithError' => 'Con Error',
				'Due2In_' => 'Public&aacute;ndose',
				'Due2Out' => 'Preparado para despublicar',
				'Due2OutWithError' => 'Con Error',
				'Due2Out_' => 'Despublic&aacute;ndose',
				'In' => 'Publicado',
				'Pumped' => 'Bombeado',
				'Out' => 'Despublicado',
				'Replaced' => 'Reemplazado',
				'Removed' => 'Borrado',
				'Canceled' => 'Cancelado'
			)
		);
		
		$activeServers = array();
		
		
		if (isset($frm_select_filter_node_gen)) {
			
			$arraySelects['frm_select_filter_node_gen'][$frm_select_filter_node_gen] = "selected";
		}
		if (isset($frm_select_filter_state_batch)) {
			
			$arraySelects['frm_select_filter_state_batch'][$frm_select_filter_state_batch] = "selected";
		}
		if (isset($frm_select_filter_active_batch) && $frm_select_filter_active_batch != 'NULL') {
			
			$arraySelects['frm_select_filter_active_batch'][$frm_select_filter_active_batch] = "selected";
		}
	
		$doFilter = ($frm_filter_batch == "yes") ? true : false;
		$stateCryteria = $frm_select_filter_state_batch;
		$activeCryteria = (!isset($frm_select_filter_active_batch) || $frm_select_filter_active_batch == 'NULL') ? null : $frm_select_filter_active_batch;
		
		$batchObj = new Batch();
		$batchList = $batchObj->getAllBatchs($doFilter ? $stateCryteria : null, $doFilter ? $activeCryteria : null, 'Up', MANAGEBATCHS_BATCHS_PER_PAGE, $frm_select_filter_node_gen ? $frm_select_filter_node_gen : null, $frm_select_filter_up_date, $frm_select_filter_down_date);
		$hasBatchs = (is_array($batchList) && count($batchList) > 0) ? true : false;
		
		$distinctNodeGenerators = $batchObj->getNodeGeneratorsFromBatchs($frm_select_filter_state_batch);
		
		if ($hasBatchs) {
			
			$serverFrameObj = new ServerFrame();
			
			$activeServers = $serverFrameObj->getServers("complete");

			foreach ($batchList as $id => $batch) {
				
				$progress = array();
				$serverFrames = $serverFrameObj->getFramesOnBatch($batch['IdBatch'], 
								(($batch['Type'] == 'Up') ? 'IdBatch' : 'IdBatchDown'), 
								"extended", $progress, MANAGEBATCHS_FRAMES_PER_PAGE);
				$hasServerFrames = (is_array($serverFrames) && count($serverFrames) > 0) ? true : false;
				
				if ($hasServerFrames) {
					
					$batchs[$batch['IdBatch']]['serverFrames'] = $serverFrames;
					$batchs[$batch['IdBatch']]['progress'] = $progress;
					$batchs[$batch['IdBatch']]['totalPags'] = count($serverFrames);
				}
				
				$downBatch = $batchObj->getDownBatch($batch['IdBatch']);
				if (is_array($downBatch) && count($downBatch) > 0) {
					
					$batchs[$batch['IdBatch']]['downBatch'] = $downBatch;
				}
			}
		}
		
		$urlRoot = App::getValue('UrlRoot');
		
		$arrValores = array(
			'hasBatchs' => $hasBatchs,
			'distinctNodeGenerators' => $distinctNodeGenerators,
			'batchs' => $batchs,
			'acceso' => $acceso,
			'errorBox' => $errorMsg,
			'arrayStates' => $arrayStates,
			'activeServers' => $activeServers,
			'arraySelects' => $arraySelects,
			'frm_select_filter_node_gen' => $frm_select_filter_node_gen,
			'frm_select_filter_state_batch' => $frm_select_filter_state_batch,
			'frm_select_filter_active_batch' => $frm_select_filter_active_batch,
			'urlRoot' => $urlRoot,
			'arrayDateValues' => $arrayDateValues,
			'js_files' => $jsFiles
		);
		
		$this->_render ($arrValores, 'batchlist', 'default-3.0.tpl');
	}
	
	function doActivateBatch ($idBatch) {
		
		if($idBatch != "all") {
			
			$batchObj = new Batch();
			return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 1);
		} else {
			
			$batchManagerObj = new BatchManager();
			return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 1);
		}
	}
	
	function doDeactivateBatch ($idBatch) {
		
		if($idBatch != "all") {
			
			$batchObj = new Batch();
			return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 0);
		} else {
			
			$batchManagerObj = new BatchManager();
			return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 0);
		}
		
	}
	
	function doPrioritizeBatch ($idBatch, $mode = 'up') {
		
		$batchObj = new Batch();
		return $batchObj->prioritizeBatch($idBatch, $mode);
	}

}
?>
