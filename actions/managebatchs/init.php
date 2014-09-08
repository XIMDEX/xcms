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




	ModulesManager::file('/inc/utils.inc');
	ModulesManager::file('/inc/sync/SyncManager.class.php');
	ModulesManager::file('/inc/model/Batch.class.php');
	ModulesManager::file('/actions/managebatchs/persistence/Config.class.php');
	ModulesManager::file('/actions/managebatchs/forms.php');
	ModulesManager::file('/xmd/js/sajax/sajax.php');
	ModulesManager::file('/inc/utils.inc');    	

	// starting SAJAX stuff
	$sajax_request_type = "POST";
	sajax_init();
	sajax_export("batchListForm");
	sajax_handle_client_request();
	
	XSession::check();
	
	// Initializing variables of action.
	$errorMsg = "";
	
	if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_deactivate_batch'] == "yes") {
		
		if (!$result = doDeactivateBatch($_POST['frm_id_batch'])) {
			
			$errorMsg = _("An error occurred while deactivating batch.");
		} else {
			
			$errorMsg = _("Batch #") . $_POST['frm_id_batch'] . _(" has been deactivated.");
		}
	}
	
	if (isset($_POST['frm_deactivate_batch']) && $_POST['frm_activate_batch'] == "yes") {
		
		if (!$result = doActivateBatch($_POST['frm_id_batch'])) {
			
			$errorMsg = _("An error occurred while activating batch.");
		} else {
			
			$errorMsg = _("Batch #") . $_POST['frm_id_batch'] . _(" has been activated.");
		}
	}
	
	////
	//// Begins action flow.
	//// 
	
	gPrintHeader();
	gPrintBodyBegin(); 
	
    ?>
		<script type="text/javascript" language="JavaScript" src="managebatchs.js"></script>
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
	            x_batchListForm(show_me);
	
	            //do it every 1 second
	            setTimeout("get_batchs()", 60000);
		    }
			get_batchs();
	    </script>
	    <div id="form_div"><?php echo _('[ Loading... ]'); ?></div>
	<?
	
	gPrintBodyEnd();
	
	

	
	function doActivateBatch ($idBatch) {
		
		$batchObj = new Batch();
		return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 1);
	}
	
	function doDeactivateBatch ($idBatch) {
		
		$batchObj = new Batch();
		return $batchObj->setBatchPlayingOrUnplaying($idBatch, $playingValue = 0);
	}
	
?>