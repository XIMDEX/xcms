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


ModulesManager::file('/inc/io/BaseIOInferer.class.php');


class Action_fileupload_common_multiple extends ActionAbstract {

	// Main method: shows initial form
	function index () {

     		$idNode = (int) $this->request->getParam("nodeid");
	     	$actionID = (int) $this->request->getParam("actionid");
		$type = $this->request->getParam('type');

		$userid = XSession::get('userID');
		$dir_tmp = XIMDEX_ROOT_PATH.Config::getValue('TempRoot');

		/** ********* Find out folder nodetype **** */

		$baseIoInferer = new BaseIOInferer();
		$type_folder = $baseIoInferer->infereType('FOLDER', $idNode );
		$type_node = $type_folder["NODETYPENAME"];

		/** ********* Checking permits **************************** */
		$userid = XSession::get('userID');

		if(empty($userid)) {
			$this->messages->add(_('It is necessary to be an active user to upload files'), MSG_TYPE_ERROR);
		}

		if(empty($type_node)) {
			$this->messages->add(_('NodeType is empty'), MSG_TYPE_ERROR);
		}

		if(!Auth::canWrite($userid, array('node_id' => $idNode)) ) {
			$this->messages->add(_('Files cannot be added because of lack of permits'), MSG_TYPE_ERROR);
		}

		/** ********* Preparing view ************ */
		//Filter and button tag according to type of upload file
		switch($type_node)  {
			case 'CssFolder':
				$lbl_anadir = _(' Add style sheets');
				$filter = ".*css"; break;
			case 'ImagesFolder':
				$lbl_anadir = _(' Add images ');
				$filter = ".*jpeg,.*jpg,.*gif,.*png,.*ico";
			break;
			case 'TemplateViewFolder':
				$lbl_anadir = _(' Add schemas ');
				$filter = ".*xml";
			break;
			case 'TemplatesFolder':
				$lbl_anadir = _(' Add templates ');
				$filter = ".*xml,.*xsl";
			break;
			case 'ImportFolder':
				$lbl_anadir = _(' Add HTML files ');
				$filter = ".*ht,.*htm,.*html,.*xhtml,.*plain,.*txt"; 
			break;
			/*case 'CommonFolder':
				$lbl_anadir = _(' Add common files ');
				$filter = ".*ht,.*htm,.*html,.*xhtml,.*plain,.*txt,.*zip"; 
			break;*/
			default:
				$lbl_anadir = _(' Add files');
				$filter = "all";
				break;
		};

		$this->addJs('/actions/fileupload_common_multiple/resources/javascript/loader.js');
		$this->addCss('/actions/fileupload_common_multiple/resources/css/loader.css');

		$arrValores = array (
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}",
			"lbl_anadir" => $lbl_anadir,
			'messages' => $this->messages->messages,
			'go_method' => 'showUploadResult',
			'nodeid' => $idNode,
			'actionid' => $this->request->getParam('actionid'),
			'type' => $type,
			'filter' => $filter,
		);

		$this->render($arrValores, 'index', 'default-3.0.tpl');
    	}


	function uploadFileBak() {
		$idNode = (int) $this->request->getParam("nodeid");

		$file = $_FILES["ximfile"];

  		$idNode = $this->request->getParam('nodeid');
  		$type = $this->request->getParam('type');
     		$option = $this->request->getParam('option');

    		if(null == $file || 0 == $file["size"]) {
    			$retval  = $this->_setRest(_("Error inesperado en la subida de archivo ").$file["name"]);
    		} else {
    			$retval = $this->_createNode($file, $idNode, $type, $option);
    		}
		die(json_encode($retval));
    	}

	function uploadFile() {
      		//$idNode = (int) $this->request->getParam("nodeid");
		$idNode = $this->request->getParam('nodeid');
  	  	$type = $this->request->getParam('type');
      		$option = $this->request->getParam('option');
      		$up = $this->request->getParam("up");
      		$base64 = $this->request->getParam("base64");
	  	$retval = "";
	  	if(count($_FILES)>0) { //Browser supporting sendAsBinary()
			$file = $_FILES["ximfile"];

		    	if(null == $file || 0 == $file["size"]) {
    				$retval  = $this->_setRest(_("Error inesperado en la subida de archivo ").$file["name"]);
    			} else {
    				$retval = $this->_createNode($file, $idNode, $type, $option);
    			}

	   	}
	   	else {
	   		$headers = getallheaders();
	   	  	$headers = array_change_key_case($headers, CASE_UPPER);
	   	 	if($up == "true") {
	   	 		if($base64 == "true") {
	   	 	  		$content = base64_decode(file_get_contents('php://input'));
	   	 		}
	   	 		else {
	   	 			$content = file_get_contents('php://input');
	   	 		}
	   	 	
	   	 		$tmp_folder = XIMDEX_ROOT_PATH."/data/tmp/uploaded_files/";
	   	 		$tmp_name = $tmp_folder.$headers['XIM-FILENAME'].".".mt_rand();
	   	 		if(file_put_contents($tmp_name,$content)==false) {
	   	 			$retval  = $this->_setRest(_("Error inesperado en la subida de archivo ").$headers["XIM-FILENAME"]);
	   	 		}
	   	 	
	   	 		$file = array('name' => $headers['XIM-FILENAME'],
	   	 				  'type' => $headers['XIM-TYPE'],
	   	 				  'size' => $headers['XIM-SIZE'],
	   	 				  'tmp_name' => $tmp_name,
	   	 				  'error' => 0
	   	 				  );
	   	    		$retval = $this->_createNode($file, $idNode, $type, $option);
				//Delete the tmp_file
	   	    		unlink($tmp_name);
	   	 	}
	   	 	else {
	   			$retval  = $this->_setRest(_("Error inesperado en la subida de archivo ").$headers["XIM-FILENAME"]);
	   	 	}
	   	}
	    	die(json_encode($retval));
    	}

	public function getpreview() {
		$up = $this->request->getParam("up");
      		$base64 = $this->request->getParam("base64");
		$headers = getallheaders();
	   	$headers = array_change_key_case($headers, CASE_UPPER);
	   	if($up == "true") {
	   	 	if($base64 == "true") {
	   	 		$content = file_get_contents('php://input');
	   	 	}
	   	 	else {
	   	 		$content = base64_encode(file_get_contents('php://input'));
	   	 	}
	   	 	$retval = array("data" => "data:".$headers["XIM-TYPE"].";base64,".$content,"status" => "ok");
	    		die(json_encode($retval));
	   	}
	   	$retval = array("data" => "","status" => "nok");
		die(json_encode($retval));
	}

    	private function _checkExistence($file, $idNode) {

    		$node = new Node($idNode);
  		$idNode = $node->GetChildByName($file);
  		if ($idNode > 0) {
  			 return true; //Exists
  		} else {
			return false;
    		}
		$this->addJs('/actions/fileupload_common_multiple/javascript/loader.js');

		$arrValores = array (
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid={$idNode}",
			"lbl_anadir" => $lbl_anadir,
			'messages' => $this->messages->messages,
			'go_method' => 'showUploadResult',
			'nodeid' => $idNode,
			'actionid' => $this->request->getParam('actionid'),
			'type' => $type,
			'filter' => $filter,
		);

		$this->render($arrValores, 'index', 'default-3.0.tpl');
    	}

	private function _setRest($msg, $status="nok") {
		$retval = array();
	   	$retval["msg"] = utf8_encode($msg);
    		$retval['status'] =  $status;

    		return $retval;
	}

	//Creating a node according to name and file path
	private function _createNode($file, $idNode,  $type, $option) {

		$baseIoInferer = new BaseIOInferer();
		//Finding out element nodetype
		if (empty($type)) {
			$nodeTypeName = $baseIoInferer->infereFileType($file);
		} else {
			$nodeTypeName = $baseIoInferer->infereFileType($file, $type);
		}

		$result = 0;

		if(!$nodeTypeName) {
     			return  $this->_setRest(_(" File is not of expected type " ) );
		} else {
			$node = new Node($idNode);

			if(!$node) {
		     		return  $this->_setRest(_(" Destination node has not been found "));
			}

			$estimatedNode = $node->GetChildByName($file["name"]);
			if ($estimatedNode > 0) {
				if (1 == $option) {
					$data = array(
						'NODETYPENAME' => $nodeTypeName,
						'ID' => $estimatedNode,
						'CHILDRENS' => array (
							array ('NODETYPENAME' => 'PATH', 'SRC' => $file["tmp_name"])
						)
					);

					$baseIO = new baseIO();
					$result = $baseIO->update($data);
				} else {
					return  $this->_setRest(_('A file already exists with same name.') );
				}
			} else if ($node->nodeType->get('IsFolder')) {
				$data = array(
					'NODETYPENAME' => $nodeTypeName,
					'NAME' => $file["name"],
					'PARENTID' => $idNode,
					'CHILDRENS' => array (
						array ('NODETYPENAME' => 'PATH', 'SRC' => $file["tmp_name"] )
					)
				);

				$baseIO = new baseIO();
				$result = $baseIO->build($data);
			}


			// If there is any problem with file name: spaces, uncommon characters, etc.
			if ($result > 0) {
				if ($option > 0) {
					return  $this->_setRest(_('File has been successfully overwritten.'), "ok" );
				} else {
					return  $this->_setRest(_('File has been successfully uploaded.'), "ok" );
				}
			}else {
				XMD_Log::error(_("BaseIO has returned the error code"). $result);
				return  $this->_setRest($baseIO->messages->messages[0]["message"] );
			}
		}

		return $result;
	}


	function showUploadResult() {

		if(array_key_exists("ximfile", $_POST) ){
			 $idNode = (int) $this->request->getParam("nodeid");
			 $arrValores = array();
			 if(array_key_exists("ok", $_POST["ximfile"] ) ) {
				  $arrValores["files_ok"] = $_POST["ximfile"]["ok"];
			 }else {
				  $arrValores["files_ok"] = array();
			 }

			 if(array_key_exists("nok", $_POST["ximfile"] ) ) {
				  $arrValores["files_nok"] = $_POST["ximfile"]["nok"];
			 }else {
				  $arrValores["files_nok"] = array();
			 }

			 $this->reloadNode($idNode);
			 $this->render($arrValores, 'result', 'default-3.0.tpl');
		}else {
			$this->messages->add(_("No files have been found"), MSG_TYPE_ERROR);
		  	$this->render(array('messages' => $this->messages->messages));
		}
    	}


  	function checkname() {
		$idNode = (int) $this->request->getParam("nodeid");
	 	$name = $this->request->getParam("name");

		$node = new Node($idNode);
		$estimatedNode = $node->GetChildByName($name);

   		if($estimatedNode > 0 ) {
			$retval = $this->_setRest( _('File already exists.') );
   		}else {
			$retval = $this->_setRest( _('File does not exist.'), 'ok');
    		}
		die(json_encode($retval));
  	}
}
?>
