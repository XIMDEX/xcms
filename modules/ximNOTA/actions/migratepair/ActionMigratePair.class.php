<?php
/**
 *  Action_Migrate_Pair
 * 	Action controller
 *
 *  $Id $
 */

if (!defined ('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath (dirname (__FILE__) . "/../../../.."));
}

//STATUS CONSTANTS
define('MIGRATE_PAIR_OK',1);
define('MIGRATE_PAIR_INCORRECT_PARAMS',-1);
define("MIGRATE_PAIR_FILE_NOT_FOUND",-2);
define("MIGRATE_PAIR_ERROR_CREATING_NODE",-3);
define("MIGRATE_PAIR_ERROR_ADD_VERSION",-4);
define("MIGRATE_PAIR_ERROR_IN_SET_CONTENT",-5);
define("MIGRATE_PAIR_PATH_IN_XIMDEX_NOT_EXITS",-6);
define("MIGRATE_PAIR_CORRUPTED", -9);
define("MIGRATE_PAIR_ERROR",-10);

require_once(XIMDEX_ROOT_PATH . '/inc/mvc/mvc.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/FrontController.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/io/BaseIO.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/XVFS.class.php');
require_once(XIMDEX_ROOT_PATH . '/modules/ximNOTA/model/RelNodeMetaData.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/sync/SynchroFacade.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/helper/String.class.php');



class ActionMigratePair  extends ActionAbstract {

	var $parentNode = null;
        var $binaryFile = null;
	var $annotatedNode = null;
	var $metadataSection = null;

 	function ActionMigratePair() {

                $this->binaryFile = new NodeType();
                $this->binaryFile->SetByName("BinaryFile");

		$this->annotatedNode = new NodeType();
		$this->annotatedNode->SetByName("AnnotatedNode");

		$this->metadataSection = new NodeType();
		$this->metadataSection->SetByName("MetaDataSection");
        }


	//PUBLIC FUNCTIONS
	function index(){}

	/**
	 * Create a new version for a pair of documents from the content files in the filesystem
	 *
	 * @param String $pathToFile path to file master in the pair of documents 
	 * @param String $pathToXml path to file slave in the pair of documents
	 * @param String $pathToXimdex path in Ximdex where the files will be copying
	 * @return int statusOperation code status for this operation
	 */
	function migratePair($pathToMaster, $pathToXml, $pathToXimdex, $checksum) {
error_log("pathToMaster: $pathToMaster");
error_log("pathToXml: $pathToXml");
error_log("pathToXimdex: $pathToXimdex");
		if (!file_exists($pathToMaster) || !file_exists($pathToXml)){
			return MIGRATE_PAIR_FILE_NOT_FOUND;
		}

		$fileNameXml = substr(strrchr($pathToXml,"/"), 1);
		$fileNameMaster = substr(strrchr($pathToMaster,"/"), 1);
error_log("files found: $fileNameXml , $fileNameMaster");

		if (is_null($idParent = $this->getIdNodeFromPath($pathToXimdex))) {
			error_log(MIGRATE_PAIR_PATH_IN_XIMDEX_NOT_EXISTS);
			return MIGRATE_PAIR_PATH_IN_XIMDEX_NOT_EXITS;			
		} 
		
		if ($this->validateMigrateParams($pathToMaster, $pathToXml, $pathToXimdex) == false) {
			error_log(MIGRATE_PAIR_INCORRECT_PARAMS);
			return MIGRATE_PAIR_INCORRECT_PARAMS;
		}

		//Added: 14:47 06/06/2014
		if ($this->validateMasterFileIntegrity($pathToMaster, $checksum) ==false){
			error_log(MIGRATE_PAIR_CORRUPTED);
			return MIGRATE_PAIR_CORRUPTED;

		}

		// Master file and metadata folder parent node.
		$this->parentNode = new Node ($idParent);

		if ($this->validateMasterFile() == false) {
			error_log(MIGRATE_PAIR_ERROR_CREATING_NODE);
			return MIGRATE_PAIR_ERROR_CREATING_NODE;
		}

		if ($this->validateMetadata() == false) {
			error_log(MIGRATE_PAIR_ERROR);
			return MIGRATE_PAIR_ERROR;
		}
	
		// At this point master file, metadata folder and xml node insertion should never fail.
		$idNodeFileMaster = $this->importFile($fileNameMaster, $this->parentNode->getID(), $pathToMaster, $this->binaryFile->get('Name'));
		$metadataID = $this->importMetaData($idNodeFileMaster);

		// Replaces any extension with "xml" or add it to unextensioned files
		if (!preg_match('/\.[^.]+$/', $fileNameXml)) {
		        $fileNameXml = $fileNameXml . '.xml';
		} else {
		        $fileNameXml = preg_replace('/\.[^.]+$/', '.xml', $fileNameXml);
		}

		$idNodeFileXml = $this->importFile($fileNameXml, $metadataID, $pathToXml, $this->annotatedNode->get('Name'));
		
		//INSERT THE RELATIONS IN RelNodeMetada table
		$rel = new RelNodeMetaData();
		$find = $rel->find('IdNode', 'IdNode = %s', array($idNodeFileMaster));

		if (is_null($find)){
			$rel->set('IdNode', $idNodeFileMaster);
			$rel->set('IdMetaData',$idNodeFileXml);
			if (!$rel->add()){
				return MIGRATE_PAIR_ERROR;
			}
		}
		
		return MIGRATE_PAIR_OK;
		
	}

	//PRIVATE FUNCTIONS

	/**
	* Validate master file integrity
	* @param pathToMaster Path in filesystem to the filemaster
	*/
	function validateMasterFileIntegrity($pathToMaster, $checksum){
	
		$content = FsUtils::file_get_contents($pathToMaster);
		$checksumLocal = md5_file($pathToMaster);
		//if (strpos($content,"%%EOF") ===FALSE)
		if ($checksumLocal != $checksum){
			error_log("CHECKSUM FAILED!: $pathToMaster - $checksumLocal - $checksum");	
			return false;
		}
		else
			return true;
	}
	
	/**
	* Validate the params for Migrate Actions
	*
	* @param String $pathToFile Path in filesystem to the fileMaster
	* @param String $pathToXml Path in filesystem to the xml file metadata
	* @param String $pathToXimdex Path in ximdex where will import the fileMaster
	* @return int validate Return the code error if exits, and if all is ok return 1
	*/
	function validateMigrateParams($pathToFile, $pathToXml, $pathToXimdex){
		//Arguments cannot be null and file names should be equal
		return
		(!
			(is_null($pathToFile) || $pathToFile == '') ||
			(is_null($pathToXml) || $pathToXml == '') ||
			(is_null($pathToXimdex) || $pathToXimdex == '') || 
			(!preg_match("/(.*\/)?(\w+)\.\w+$/",$pathToFile, $matchFileMaster)) ||
			(!preg_match("/(.*\/)?(\w+)\.\w+$/",$pathToXml, $matchFileXml)) ||
			($matchFileMaster[2] != $matchFileXml[2])
			
		);
	}

	/**
	* Verify if master file is allowed under ParentMaster
	**/
	function validateMasterFile() {
		$idParent = $this->parentNode->GetID();
		$nodeType = $this->binaryFile->get('IdNodeType');
		return $this->isAllowedContent($idParent, $nodeType);
	}

	/**
	* Verify if 'metadataSection' is allowed under ParentMaster and if there is no other normal section also called 'metadata'
	**/
	function validateMetadata() {
		if (!$this->isAllowedContent($this->parentNode->GetID(), $this->metadataSection->get('IdNodeType'))) {
			return false;			
		}

		$idFolderMetaData = $this->parentNode->GetChildByName("metadata");
		if ($idFolderMetaData > 0){
			$metadata = new node($idFolderMetaData);
			if ($metadata->nodeType->GetName() != "MetaDataSection") {
				error_log("ximNOTA: No se ha validado correctamente la carpeta metadata");
				return false;
			}
		}
		return true;
	}

	/**
	* Verify if $nodetype is allowed under $idParent.
	**/
	function isAllowedContent($idParent, $nodetype) {
		$parentNode = new node($idParent);
		return $parentNode->checkAllowedContent($nodetype, $idParent, false);
	}

	/**
	 * Get the id in ximdex from a path
	 * @see XVFS
	 * @param $path
	 * @return int $id id in ximdex for the path, if not exits the path return null
	 */
	function getIdNodeFromPath($path){
		$paths = explode('/', $path);
		$localNode = new Node(10000);
		$idNode = NULL;
		foreach ($paths as $pathPart) {
			if (empty($pathPart)) {
				continue;
			}
			$idNode = $localNode->getChildByName($pathPart);
			if (empty($idNode)) {
				return NULL;
			} else {
				$localNode = new Node($idNode);
			}
		}
		return $idNode;
	}

	/**
	 * Return the id for node container for the metaDataSection where i'm going to insert the xml file
	 * @param $idNodeFileMaster idNode from the pdf file
	 * @return unknown_type
	 */
	function importMetaData($idNodeFileMaster){
		$idFolderMetaData = $this->parentNode->GetChildByName("metadata");
		if (!is_null($idFolderMetaData)){
			//if not exits MetaDataSection --> to create it
			$bio = new baseIO();
			$data = array(
			'NODETYPENAME' => 'MetaDataSection',
			'NAME' => 'metadata',
			'PARENTID' => $this->parentNode->GetID());
			$idFolderMetaData = $bio->build($data);
			if (is_null($idFolderMetaData) || ($idFolderMetaData < 0)){
				return MIGRATE_PAIR_ERROR;
			}
		}

		return $idFolderMetaData;
	}

	/**
	 * Import a file in a path of ximdex
	 * @param string $fileNameMaster name of the file to import
	 * @param string $pathToFile path to file to import
	 * @param string $nodeTypeName 
	 * @return int $idNodeFileMaster id from the new node for imported file
	 */
	function importFile($fileNameMaster, $parentId, $pathToFile, $nodeTypeName){
		//If file is already in XimDEX: update version.
		//If not, create it.
		$parentNode = new Node($parentId);
		$idNodeFileMaster = $parentNode->GetChildByName($fileNameMaster);
		if ($idNodeFileMaster > 0){
			$n = new Node($idNodeFileMaster);
			$content = FsUtils::file_get_contents($pathToFile);
			$n->SetContent($content);
			//TODO: checkear el error de subida de version??
				
		}else{
			$bio = new baseIO();
			//TODO: check permissions
			$data = array(
						'NODETYPENAME' => $nodeTypeName,
						'NAME' => $fileNameMaster,
						'PARENTID' => $parentId,
						'CHILDRENS' => array (array ('NODETYPENAME' => 'PATH', 'SRC' => $pathToFile)));
			$idNodeFileMaster = $bio->build($data);
			$masterNode = new Node($idNodeFileMaster);
			if ($masterNode->getId()){
				$content = FsUtils::file_get_contents($pathToFile);
				$masterNode->setContent($content);

			}

		}

		return $idNodeFileMaster;
	}

	/**
	* In case any error happend, calling this function should roll back all the previous changes.
	* @param int $idNodeFileMaster Master file idNode
	* @param int $idParentXml Metadata folder idNode (this folder contains the xml file)
	* @param int $idNodeFileXml XML file idNode
	*/
	function undo($idNodeFileMaster, $idParentXml, $idNodeFileXml) {
		$bio = new baseIO();

		// If master file exist it has to be deleted.
		if ($idNodeFileMaster > 0) {
			$node = new Node($idNodeFileMaster);
			$data = array(
						'ID' => $node->get('IdNode'),
						'NODETYPENAME' => $node->nodeType->get('Name'),
						'NAME' => $node->get('Name'));
			
			$bio->delete($data);
		}

		// If xml file exist it has to be deleted.
		if ($idNodeFileXml > 0) {
			$node = new Node($idNodeFileXml);
			$data = array(
						'ID' => $node->get('IdNode'),
						'NODETYPENAME' => $node->nodeType->get('Name'),
						'NAME' => $node->get('Name'));
			
			$bio->delete($data);
		}

		// If metadata folder exist and is now empty it has to be deleted.
/*
		if ($idParentXml > 0) {
			// TODO: Not sure what to do with metadatafolder			
		}
*/

	}

}

?>
