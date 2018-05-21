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

use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;

class Action_managebatchs extends ActionAbstract
{
	/**
	 * Metodo principal: presenta el formulario inicial
	 */
	function batchlist()
	{
		// Inicializando variables propias de la accion.
		$errorMsg = "";
		if (isset($_POST['frm_prioritize_batch']) && $_POST['frm_prioritize_batch'] == "yes") {
			if (!$result = $this->doPrioritizeBatch($_POST['frm_id_batch'])) {
				$errorMsg = _("An error occurred when prioritizing the batch.");
			} else {
				$errorMsg = _("Batch #") . $_POST['frm_id_batch'] . _(" has been prioritized.");
			}
		}
		if (isset($_POST['frm_deprioritize_batch']) && $_POST['frm_deprioritize_batch'] == "yes") {
			if (!$result = $this->doPrioritizeBatch($_POST['frm_id_batch'], 'down')) {
				$errorMsg = _("An error occurred when lowering the batch priority.");
			} else {
				$errorMsg = _("Batch priority has been lowered")." #" . $_POST['frm_id_batch'];
			}
		}
		if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_deactivate_batch'] == "yes") {
			if (!$result = $this->doDeactivateBatch($_POST['frm_id_batch'])) {
				$errorMsg = _("An error occurred while deactivating batch.");
			} else {
				if ($_POST['frm_id_batch'] == "all") {
					$errorMsg = _("All batches have been disabled.");
				} else {
					$errorMsg = _("Batch #") . $_POST['frm_id_batch'] . _(" have been disabled.");
				}
			}
		}
		if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_activate_batch'] == "yes") {
			if (!$result = $this->doActivateBatch($_POST['frm_id_batch'])) {
				$errorMsg = _("An error occurred while activating batch.");
			} else {
				if($_POST['frm_id_batch'] == "all") {
					$errorMsg = _("All batches have been actived.");
				} else {
					$errorMsg = _("Batch #") . $_POST['frm_id_batch'] . _(" has been actived.");
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
		$arrayDateValues['update'] = (isset($_POST['update'])) ? $_POST['update'] : _("Click here")."...";
		$arrayDateValues['uphour'] = (isset($_POST['uphour'])) ? $_POST['uphour'] : "00";
		$arrayDateValues['upmin'] = (isset($_POST['upmin'])) ? $_POST['upmin'] : "00";
		$arrayDateValues['downdate'] = (isset($_POST['downdate'])) ? $_POST['downdate'] : _("Click here")."...";
		$arrayDateValues['downhour'] = (isset($_POST['downhour'])) ? $_POST['downhour'] : "00";
		$arrayDateValues['downmin'] = (isset($_POST['downmin'])) ? $_POST['downmin'] : "00";
		$acceso = true;
		
		// Inicializando variables.
		$userID = \Ximdex\Runtime\Session::get('userID');
		$user = new User();
		$user->SetID($userID);
		if (!$user->HasPermission("view_publication_resume")) {
			$acceso = false;
			$errorMsg = _("You do not have access to this report. Consult an administrator");
		}
		$arrayStates = array (
			'Batch' => array (
				Batch::WAITING =>  _('Waiting'),
			    Batch::INTIME => _('In Time'),
			    Batch::ENDED => _('Ended')
			),
			'ServerFrame' => array (
				ServerFrame::PENDING => _('Pending'),
			    ServerFrame::DUE2IN => _('Ready to publish'),
				'Due2PumpedWithError' => _('With error'),
				'Due2In_' => _('Being published'),
			    ServerFrame::DUE2OUT => _('Ready to unpublish'),
			    ServerFrame::DUE2OUTWITHERROR => _('With error'),
				'Due2Out_' => _('Being unpublished'),
			    ServerFrame::IN =>  _('Published'),
			    ServerFrame::PUMPED => _('Pumped'),
			    ServerFrame::OUT => _('Unpublished'),
			    ServerFrame::REPLACED => _('Replaced'),
			    ServerFrame::REMOVED => _('Deleted'),
			    ServerFrame::CANCELED => _('Cancelled')
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
			'arrayDateValues' => $arrayDateValues
		);
		$this->_render ($arrValores, NULL, 'default-3.0.tpl');
	}

	function doActivateBatch($idBatch)
	{
		if ($idBatch != "all") {
			$batchObj = new Batch();
			return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 1);
		} else {
			$batchManagerObj = new BatchManager();
			return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 1);
		}
	}

	function doDeactivateBatch($idBatch)
	{
		if ($idBatch != "all") {
			$batchObj = new Batch();
			return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 0);
		} else {
			$batchManagerObj = new BatchManager();
			return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 0);
		}
	}

	function doPrioritizeBatch($idBatch, $mode = 'up')
	{
		$batchObj = new Batch();
		return $batchObj->prioritizeBatch($idBatch, $mode);
	}
}