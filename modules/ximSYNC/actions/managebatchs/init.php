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




	

	if (!defined ("XIMDEX_ROOT_PATH")) {		
		define ("XIMDEX_ROOT_PATH", realpath (dirname (__FILE__)."/../../../../"));
	}
	
	 include_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
	 ModulesManager::file('/inc/utils.php');
 	 ModulesManager::file('/inc/model/action.php');
 	 ModulesManager::file('/inc/model/Batch.class.php', 'ximSYNC');
 	 ModulesManager::file('/conf/synchro.conf', 'ximSYNC');
 	 ModulesManager::file('/actions/managebatchs/forms.php', 'ximSYNC');
	 ModulesManager::file('/xmd/js/sajax/sajax.php');
	
	if($_GET['mode'] == "action") {
		
		$actionObj = new Action();
		$arrActions = $actionObj->GetActionListOnNodeType();
		$numActions = count($arrActions);
		if (is_array($arrActions) && $numActions > 0) {
			foreach ($arrActions as $actionId) {
				
				$actionObj->Action($actionId);
				if ($actionObj->getCommand() == "managebatchs") {
					
					header("Location:" . Config::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=" . $actionId . "&nodeid=NULL&method=batchlist");
					exit();
				}
			}
		}
	}
	
	
	// starting SAJAX stuff
	$sajax_request_type = "POST";
	$sajax_debug_mode = 0;
	sajax_init();
	sajax_export("batchListForm");
	sajax_handle_client_request();
	
	XSession::check();
	
	// Inicializando variables propias de la accion.
	$errorMsg = "";
	
	if (isset($_POST['frm_prioritize_batch']) && $_POST['frm_prioritize_batch'] == "yes") {
		
		if (!$result = doPrioritizeBatch($_POST['frm_id_batch'])) {
			
			$errorMsg = "Ha ocurrido un error al priorizar el Batch.";
		} else {
			
			$errorMsg = "El Batch #" . $_POST['frm_id_batch'] . " ha sido priorizado.";
		}
	}
	
	if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_deactivate_batch'] == "yes") {
		
		if (!$result = doDeactivateBatch($_POST['frm_id_batch'])) {
			
			$errorMsg = "Ha ocurrido un error al desactivar el Batch.";
		} else {
			
			$errorMsg = "El Batch #" . $_POST['frm_id_batch'] . " ha sido desactivado.";
		}
	}
	
	if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_activate_batch'] == "yes") {
		
		if (!$result = doActivateBatch($_POST['frm_id_batch'])) {
			
			$errorMsg = "Ha ocurrido un error al activar el Batch.";
		} else {
			
			$errorMsg = "El Batch #" . $_POST['frm_id_batch'] . " ha sido activado.";
		}
	}
	
	$frm_select_filter_node_gen = (isset($_POST['frm_select_filter_node_gen'])) ? $_POST['frm_select_filter_node_gen'] : "";
	$frm_select_filter_state_batch = (isset($_POST['frm_select_filter_state_batch'])) ? $_POST['frm_select_filter_state_batch'] : "Any";
	$frm_select_filter_active_batch = (isset($_POST['frm_select_filter_active_batch'])) ? $_POST['frm_select_filter_active_batch'] : 'NULL';
	$frm_filter_batch = (isset($_POST['frm_filter_batch'])) ? $_POST['frm_filter_batch'] : 'no';
	
	////
	//// Inicio del flujo de la acci�n.
	//// 
	
	gPrintHeader();
	gPrintBodyBegin(); 
	
    ?>
		<script type="text/javascript" language="JavaScript" src="../../js/managebatchs.js"></script>
	    <script>
		    <?
		    	sajax_show_javascript();
		    ?>
		    
		    function show_me(form) {
				
				document.getElementById("form_div").innerHTML = form;
		    }
		
		    function get_batchs() {
		
	            //put the return of php's batchListForm func
	            //to the javascript show_me func as a parameter
	            x_batchListForm('', 'false', '<?php echo $frm_select_filter_state_batch; ?>', '<?php echo $frm_select_filter_active_batch; ?>', '<?php echo $frm_select_filter_node_gen; ?>', '<?php echo $frm_filter_batch; ?>', show_me);
	
	            //do it every 1 second
	            setTimeout("get_batchs()", 60000);
		    }
			get_batchs();
	    </script>
	    <div id="form_div">[ Cargando... ]</div>
	<?
	
	gPrintBodyEnd();


	
	function doActivateBatch ($idBatch) {
		
		if($idBatch != 'all') {
			
			$batchObj = new Batch();
			return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 1);
		} else {
			
			$batchManagerObj = new BatchManager();
			return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 1);
		}
	}
	
	function doDeactivateBatch ($idBatch) {
		
		if($idBatch != 'all') {
			
			$batchObj = new Batch();
			return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 0);
		} else {
			
			$batchManagerObj = new BatchManager();
			return $batchManagerObj->setAllBatchsPlayingOrUnplaying($playingValue = 0);
		}
		
	}
	
	function doPrioritizeBatch ($idBatch) {
		
		$batchObj = new Batch();
		return $batchObj->prioritizeBatch($idBatch);
	}
	
?>