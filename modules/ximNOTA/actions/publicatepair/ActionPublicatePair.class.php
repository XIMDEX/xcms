<?php
/**
 *  Action_Publicate_Pair
 * 	Action controller
 *
 *  @version $Revision: $
 *
 *  $Id $
 */

if (!defined ('XIMDEX_ROOT_PATH')) {
    define ('XIMDEX_ROOT_PATH', realpath (dirname (__FILE__) . "/../../../.."));
}

//Status constant for Publicate Pair
define("PUBLICATE_PAIR_OK",1);
define("PUBLICATE_PAIR_INCORRECT_PARAMS",-1);
define("PUBLICATE_PAIR_ERROR_GET_FILES_2_PUBLISH",-2);
define("PUBLICATE_PAIR_ERROR_SEND_2_PUBLISH",-3);
define("PUBLICATE_PAIR_ERROR",-10);

require_once(XIMDEX_ROOT_PATH . '/inc/mvc/mvc.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/FrontController.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/io/BaseIO.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/XVFS.class.php');
require_once(XIMDEX_ROOT_PATH . '/modules/ximNOTA/model/RelNodeMetaData.class.php');
require_once(XIMDEX_ROOT_PATH . '/modules/ximSYNC/inc/manager/SyncManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/modules/ximNOTA/actions/migratepair/ActionMigratePair.class.php');

class ActionPublicatePair extends ActionAbstract {
	

	//PUBLIC FUNCTIONS
	function index(){}

	/**
	 * PushDocInpublishingpool for a pair of documents
	 *
	 * @param String $nameFileMaster name from the fileMaster
	 * @param String $pathInXimdex path from the fileMaster in Ximdex
	 * @return int statusOperation code status for this operation
	 */
	function publicatePair($nameFileMaster, $pathInXimdex){

		if ((is_null($nameFileMaster) || $nameFileMaster == '') ||
		(is_null($pathInXimdex) || $pathInXimdex == '')){
			//Arguments cannot be null
			return PUBLICATE_PAIR_INCORRECT_PARAMS;
		}

		/*
		if (!XVFS::mount('/', "xnodes://ximetrix:ximetrix@localhost/Proyectos")){
			return PUBLICATE_PAIR_ERROR;
		}
		*/

		$idParentMaster = ActionMigratePair::getIdNodeFromPath($pathInXimdex.$nameFileMaster);
		if (is_null($idParentMaster)){
			//Dont exits this path in ximdex
			return PUBLICATE_PAIR_ERROR_GET_FILES_2_PUBLISH;
		}
		
		//GET THE METADATAINFO NODE
		$rel = new RelNodeMetaData($idParentMaster);
		$idMetaData = $rel->get('IdMetaData');
		if (! $idMetaData > 0){
			return PUBLICATE_PAIR_ERROR_GET_FILES_2_PUBLISH;
		}
		
		//SEND TO PUBLISH
		$flags = array(); 
		
		//TODO DELETE DEBUG LINE
	//	return PUBLICATE_PAIR_OK;
		$sync = new SyncManager();
		$res1 = $sync->pushDocInPublishingPool($idParentMaster, time());
		if (!is_array($res1) || !in_array($idParentMaster,$res1)){
			//error in publishingpool
			return PUBLICATE_PAIR_ERROR_SEND_2_PUBLISH;
		}else{
			//$servers = array_keys($res1[$idParentMaster]);
			//$idFrames = array();
			//foreach($servers as $key=>$value){
			//	//array from the serverframes created
			//	$idFramesMaster = array_merge($idFrames,array_values($res1['ok'][$idParentMaster][$value]));
			//}
		}
		$res2 = $sync->pushDocInPublishingPool($idMetaData, time());
		if (!is_array($res2) || !in_array($idMetaData,$res2)){
			//error in publishingpool
			//Cancel Batch previous
			/*$batch = new Batch();
			foreach($idFramesMaster as $key=>$value){
				$serverFrames = new ServerFrame($value);
				$idBach = $serverFrames->get('IdBatchUp');
				$batch = new Batch($idBach);
				$batch->cancelBatch();
			}*/
			return PUBLICATE_PAIR_ERROR_SEND_2_PUBLISH;
		}
		
		return PUBLICATE_PAIR_OK;
	}

}

?>
