<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

if (!defined ('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath (dirname (__FILE__)."/../"));
}

require_once(XIMDEX_ROOT_PATH . "/inc/utils.php");
require_once(XIMDEX_ROOT_PATH . "/inc/log/Log.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/log/logger/Logger_error.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/db/db.php");
require_once(XIMDEX_ROOT_PATH . "/inc/model/Versions.php");
//
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php' );
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipelineManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/sync/SynchroFacade.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/poolerd/PoolerClient.class.php');

ModulesManager::file('/inc/persistence/store/StoreFactory.class.php');


/**
*
* Clase que modela el Repositorio de Versiones a bajo nivel.
*
*    Gestiona el repositorio central de datos de ximDEX.
*	Para cada nodo, almacena su contenido y lleva un sistema de versionado,
*	configurable desde la tabla Config.
*
*	 El sistema de nombrado de versiones utiliza dos indices: version y
*	subversion. Cuando el coontenido es simplemente modificado, salta a la
*	siguiente subversion y cuendo es publicado se crea una nueva version.
*
*	 Al pasar a una nueva version se procede a limpiar las subversiones de la
*	version anterior, manteniendo siempre la primera subversion (*.0, que es la
*	que se publica.
*
*	 Las dos claves de configuracion son:
*
*	1) PurgeSubversionsOnNewVersion - decide si al saltar a una nueva version
*	se debe proceder al limpiado de subversiones de la version anterior.
*	 El proceso de limpiado forzosamente respetara la *.0.
*
*	2) MaxSubVersionsAllowed - Durante el desarrollo de una version, se procede
*	a guardar en multitud de ocasiones, generando un gran numero de subversiones
*	, por lo que este parametro indica cuantas queremos guardar. Asi, el sistema
*	unicamente guardara la primera subversion, y las N ultimas, donde N viene
*	definido por este parametro.
*
*
* @name		DataFactory
* @author	Jose I. Villar
* @version	1.0
*
**/
class DataFactory
{
	var $ID;					// Identificador del tipo de nodo actual.
   	var $numErr;				// Codigo de error.
    var $msgErr;				// Mensaje de Error.
	var $errorList = array(		// Lista de errores de la clase.
		1 => 'No existe el Nodo',
		2 => 'Error de conexion con la base de datos',
		3 => 'No se encontro el contenido para la version solicitada',
		4 => 'Error accediendo al sistema de archivos',
		5 => 'Error al establecer el contenido del documento',
		6 => 'Ha ocurrido un error al intentar guardar el documento'
		);
    var $conector;

	/**
	*
	* Constructor de la clase
	*
	* @name		DataFactory
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $nodeID=null (opcional) Identificador del Nodo cargado en el objeto
	* @return	$this
	*
	*/
    function DataFactory($nodeID = null) {
		$this->ClearError();
		$this->nodeID = (int) $nodeID;
		if (ModulesManager::isEnabled('ximRAM'))
		   $this->conector = new SolrConector();
	}

	/**
	*
	* Devuelve el identificador del Nodo cargado en el objeto
	*
	* @name		GetID
	* @author 	Jose I. Villar
	* @version	1.0
	* @return	$nodeID
	*
	*/
    function GetID() {
		$this->ClearError();
		if((int)$this->nodeID > 0) {
			return $this->nodeID;
		}
		$this->SetError(1);
		return false;
	}


	/**
	*
	* Devuelve la lista de versiones distintas para el nodo cargado en el objeto
	*
	* @name		GetVersionList
	* @author 	Jose I. Villar
	* @version	1.0
	* @return	array $versions
	*
	*/
	function GetVersionList($order = 'asc') {
		$this->ClearError();
		if (!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$versions = new Version();
		return $versions->find('DISTINCT Version', "IdNode = %s ORDER BY Version $order", array($this->nodeID), MONO);
	}

	/**
	*
	* Devuelve la lista de subversiones de una version concreta para el nodo cargado en el objeto
	*
	* @name		GetSubVersionList
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $version
	* @return	array $versions
	*
	*/
	function GetSubVersionList($version) {
		$this->ClearError();

		if (!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$versions = new Version();
		return $versions->find('DISTINCT SubVersion',
			'IdNode = %s AND Version = %s ORDER BY SubVersion', array($this->nodeID, $version), MONO);
	}


	/**
	*
	* Devuelve la ultima version del nodo que hay en el objeto
	*
	* @name		GetVersionList
	* @author 	Jose I. Villar
	* @version	1.0
	* @return	int $version
	*
	*/
	function GetLastVersion() {
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return NULL;
		}

		$versions = new Version();
		$result = $versions->find('MAX(Version) AS max_version',
			'IdNode = %s', array($this->nodeID), MONO);
		if (empty($result) || !is_array($result)) {
			return NULL;
		}

		return $result[0];
	}


	/**
	*
	* Devuelve la ultima SubVersion del la version dada
	*
	* @name		GetSubVersionList
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $version
	* @return	array $subVersions
	*
	*/
	function GetLastSubVersion($version) {
		$this->ClearError();
		if (!($this->nodeID) > 0) {
			$this->SetError(1);
			return NULL;
		}

		$versions = new Version();
		$result = $versions->find('MAX(SubVersion) as max_subversion',
			'IdNode = %s AND Version = %s', array($this->nodeID, $version), MONO);
		if (empty($result) || !is_array($result)) {
			return NULL;
		}

		return $result[0];
	}


	function getVersionId($version, $subversion) {

		if (!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$versions = new Version();
		$result = $versions->find('IdVersion',
			'Version = %s AND SubVersion = %s AND IdNode = %s', array($version, $subversion, $this->nodeID), MONO);
		if (empty($result) || !is_array($result)) {
			return NULL;
		}

		return $result[0];
	}

	/**
	*
	* Devuelve si ya hay almacenada alguna version del nodo que hay en el objeto
	*
	* @name		HasPreviousVersions
	* @author 	Jose I. Villar
	* @version	1.0
	* @return	int $version
	*
	*/
	function HasPreviousVersions() {
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$versions = new Version();
		$result = $versions->find('COUNT(*) AS has_versions',
			'IdNode = %s', array($this->nodeID), MONO);
		if (empty($result) || !is_array($result)) {
			return NULL;
		}

		return $result[0];
	}


	/**
	*
	* Devuelve el contenido de una version.
	*
	* @name		GetContent
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $versionID = null	: Si se omite este parametro y el siguiente, se tomara la ultima version del nodo en el objeto
	* @param	int $subVersion = null	: Si se omite este parametro, el anterior es el identificador unico de version, en otro caso, es el numero de Version
	* @return	string $content
	*
	*/
	function GetContent($versionID=null, $subVersion=null) {
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		/// Si no se nos especificaba la version, asumimos la ultima
		if (is_null($versionID) && is_null($subVersion)) {
			$versionID = $this->GetLastVersion();
			if (!is_null($versionID)) {
				$subVersion = $this->GetLastSubVersion($versionID);
			}
		}

		if (!(!(is_null($versionID)) && !(is_null($subVersion)))) {
			XMD_Log::warning('No se ha podido estimar la versi�n o la subversion');
			return false;
		}

		$uniqueName = $this->GetTmpFile($versionID, $subVersion);

		if(!$uniqueName) {
			XMD_Log::warning('No se ha podido obtener el file');
			$this->SetError(3);
			return false;
		}

		$targetPath = \App::getValue( "AppRoot") . \App::getValue( "FileRoot"). "/". $uniqueName;
		$content = FsUtils::file_get_contents($targetPath);

		XMD_Log::info("GetContent for Node:".$this->nodeID.", Version: ".$versionID.".".$subVersion.", File: .".$uniqueName. ", Chars: ".strlen($content));

		$nodo = new Node($this->nodeID);
		$isPlainFile = $nodo->nodeType->get('IsPlainFile');    	

		//only encoding the content if the node is not one of this 3.
		if (!$isPlainFile){
		    	//look for the working encoding from Config
				$workingEncoding = \App::getValue( 'workingEncoding');
				$content = \Ximdex\XML\Base::recodeSrc($content, $workingEncoding);
		    }

	    return $content;
	}


	function _generateCaches($idVersion) {
		if (ModulesManager::isEnabled('ximSYNC')) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				return NULL;
			}

			$idNode = $version->get('IdNode');
			$node = new Node($idNode);
			$isOTF = $node->getSimpleBooleanProperty('otf');


			if (!($node->get('IdNode') > 0)) {
				return NULL;
			}

			if (!$node->nodeType->GetIsStructuredDocument()) {
				return NULL;
			}

			$channels = $node->GetChannels();
			$pipelineManager = new PipelineManager();
			foreach ($channels as $idChannel) {
				XMD_Log::info("Generando cache para la versi�n $idVersion y el canal $idChannel");
				$data = array('CHANNEL' => $idChannel);

				if (!$isOTF) {
					$transformer = $node->getProperty('Transformer');
					$data['TRANSFORMER'] = $transformer[0];
					$pipelineManager->getCacheFromProcess($idVersion, 'StrDocToDexT', $data);
				} else {
					$pipelineManager->getCacheFromProcess($idVersion, 'ximOTFSql', $data);
				}
			}
		}
	}

	/**
	*
	* Cambia el contenido de una version.
	*
	* @name		SetContent
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	string $content
	* @param	int $versionID = null	: Si se omite este parametro y el siguiente, se tomara la ultima version del nodo en el objeto
	* @param	int $subVersion = null	: Si se omite este parametro, el anterior es el identificador unico de version, en otro caso, es el numero de Version
	*
	*/
	function SetContent($content, $versionID = NULL, $subVersion = NULL, $commitNode = NULL) {
		$nodo = new Node($this->nodeID);
    	$isPlainFile = $nodo->nodeType->get('IsPlainFile');
    	
    	//only encoding the content if the node is not one of this 3.
		if (!$isPlainFile){
				//look for the working encoding from Config
				$dataEncoding = \App::getValue( 'dataEncoding');
				$content = \Ximdex\XML\Base::recodeSrc($content, $dataEncoding);
		    }

		$this->ClearError();

		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}
		// (1) No se pasa version determinada, se incrementa la version con el contenido nuevo.
		if (empty($versionID) && empty($subVersion)) {
			$idVersion = $this->AddVersion(NULL, NULL, $content, $commitNode);
			$this->_generateCaches($idVersion);
			return $idVersion;
		}

		// (2) Se pasa version determinada y se machaca el contenido de esa version.
		if (!is_null($versionID) && !is_null($subVersion)) {
			$uniqueName = $this->GetTmpFile($versionID, $subVersion);

			if (!$uniqueName) {
				XMD_Log::error("Error al hacer un setContent for Node (No se ha podido obtener el file):".$this->nodeID.", Version: ".$versionID.".".$subVersion.", File: .".$uniqueName. ", Chars: ".strlen($content));
				return false;
			}

			$targetPath = \App::getValue( "AppRoot") . \App::getValue( "FileRoot"). "/". $uniqueName;
			XMD_Log::info("SetContent for Node:" . $this->nodeID . ", Version: " . $versionID . "." . $subVersion . ", File: ." . $uniqueName . ", Chars: " . strlen($content));
			$result = FsUtils::file_put_contents($targetPath, $content);

			$idVersion = $this->getVersionId($versionID, $subVersion);
			if ($result && ModulesManager::isEnabled('ximRAM')) {
				$this->indexNode($idVersion, $commitNode);
			}
			$this->_generateCaches($idVersion);

			return $result;
		}
		return false;
	}


	/**
	*
	* Crea una nueva Version
	*
	* @name		AddVersion
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	bool $newVersion = null
	* @param	string $comment = null
	*
	*/
	function AddVersion($jumpNewVersion = NULL, $comment = NULL, $content = NULL, $commitNode = NULL) {

		$this->ClearError();

		if (!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		/// Si tiene versiones anteriores, calculamos cual es la siguiente
		if ($this->HasPreviousVersions()) {
			$purgeAll = false;
			$curVersion = $this->GetLastVersion();

			if (is_null($curVersion)) {
				XMD_Log::warning('No se ha podido obtener la �ltima versi�n del documento');
				return false;
			}
			$curSubVersion = $this->GetLastSubVersion($curVersion);

			/// Si queremos saltar de version x.y -> x+1.0
			if ($jumpNewVersion) {
				$purgeAll = true;
				$newVersion = $curVersion + 1;
				$newSubVersion = '0';
			} else {
				/// Si queremos saltar solo de subversion x.y -> x.y+1
				$newVersion = $curVersion;
				$newSubVersion = $curSubVersion + 1;
			}

			if (is_null($content)) {
				$newContent = $this->GetContent($curVersion, $curSubVersion);
			} else {
				$newContent = $content;
			}

			if(\App::getValue( "PurgeVersionsOnNewVersion")) {
				$this->_purgeVersions();
			}

            if(\App::getValue( "PurgeSubversionsOnNewVersion")) {
                $this->_purgeSubVersions($newVersion);
            }

		} else {
			/// Si es la primera version a guardar -> 0.0
			$newVersion = 0;
			$newSubVersion = 0;

			if (is_null($content)) {
				$newContent = '';
			} else {
				$newContent = $content;
			}
		}

		$userID = XSession::get("userID");
		if ($userID == null) {
			$userID = "301"; // ximdex admin
		}

		$uniqueName = $this->_getUniqueFileName();

		FsUtils::file_put_contents(\App::getValue( "AppRoot") .
					\App::getValue( "FileRoot") .
					"/" . $uniqueName, $newContent);

		$version = new Version();
		$version->set('IdNode', $this->nodeID);
		$version->set('Version', $newVersion);
		$version->set('SubVersion', $newSubVersion);
		$version->set('File', $uniqueName);
		$version->set('IdUser', $userID);
		$version->set('Date', time());
		$version->set('Comment', $comment);
		$IdVersion = $version->add();

		XMD_Log::info("AddVersion for Node:".$this->nodeID.", Version: ".$newVersion.".".$newSubVersion.", File: .".$uniqueName);


		if (ModulesManager::isEnabled('ximRAM')) {
			$this->indexNode($this->getVersionId($newVersion,$newSubVersion), $commitNode);
		}

		return $IdVersion;
	}

	function _getUniqueFileName() {

		return FsUtils::getUniqueFile(\App::getValue( "AppRoot") . \App::getValue( "FileRoot"));
	}
	/*
	*
	* Recupera una antigua Version y la coloca como nueva
	*
	* @name		RecoverVersion
	* @author 	Jose Luis Fernandez
	* @version	1.0
	* @param	int $version not null
	* @param	int $subversion not null
	* @param	string $comment null
	*
	**/
	function RecoverVersion($version, $subversion, $comment=null) {
		$tmpVersion = $version;
		$this->ClearError();

		$node = new Node($this->nodeID);
		if (!((($node->get('IdNode') > 0)) &&
			(!is_null($version)) &&
			(!is_null($subversion)))) {
			$this->SetError(1);
			return false;
		}

		$purgeAll = null;
		// Siempre va a tener versiones anteriores (no tiene sentido recuperar la actual), calculamos cual es la siguiente

		$newVersion = $this->GetLastVersion();
		$curSubVersion = $this->GetLastSubVersion($newVersion);
		$purgePreviousSubVersions = \App::getValue( "PurgeSubversionsOnNewVersion");

		$newSubVersion = $curSubVersion + 1;

		/// Le ponemos el contenido de la version que queremos recuperar
		$newContent = $this->GetContent($version, $subversion);

		$userID = XSession::get("userID");

		/// Se guarda en un archivo de id unico
		$uniqueName = $this->_getUniqueFileName();
		$targetPath = \App::getValue( "AppRoot") . \App::getValue( "FileRoot") . "/" . $uniqueName;

		if (!FsUtils::file_put_contents($targetPath, $newContent)) {
			XMD_Log::error('Error al establecer el contenido del documento');
			$this->SetError(5);
		}

		$dbObj = new DB();

		/// Ejecutamos la insercion en la BD
		$version = new Version();
		$version->set('IdNode', $this->nodeID);
		$version->set('Version', $newVersion);
		$version->set('SubVersion', $newSubVersion);
		$version->set('File', $uniqueName);
		$version->set('IdUser', $userID);
		$version->set('Date', time());
		$version->set('Comment', $comment);
		$IdVersion = $version->add();


		$fileName = $node->class->GetNodePath();
		$fileContent = $node->class->GetRenderizedContent();

		/// Lo guardamos en el sistema de archivos
		if (!FsUtils::file_put_contents($fileName, $fileContent)) {
				XMD_Log::error('Ha ocurrido un error al intentar guardar el documento');
				$this->SetError(6);
				return false;
		}

		XMD_Log::debug("RecoverVersion for Node".$this->nodeID." with result:" .
			$IdVersion. ", Version: ".$newVersion.".".$newSubVersion .
			", OldVersion: ".$tmpVersion.".".$subversion.", File: .".$uniqueName, 4, "DataFactory");

		if (ModulesManager::isEnabled('ximRAM')) {
			$this->indexNode($this->getVersionId($newVersion,$newSubVersion), null);
		}


		$purgePreviousSubVersions = \App::getValue( "PurgeSubversionsOnNewVersion");
		if($purgePreviousSubVersions && $this->HasPreviousVersions()) {
			$this->_purgeSubVersions($newVersion, $purgeAll);
		}
		return true;
	}

	/*
	*
	* Elimina todas las Versiones del Nodo
	*
	* @name		DeleteAllVersions
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $version
	*
	**/
	function DeleteAllVersions() {
		$this->ClearError();
		if (!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$versions = $this->GetVersionList();
		if (!is_array($versions)) {
			return false;
		}
		foreach($versions as $version) {
			$this->DeleteVersion($version);
		}
		return true;
	}


	/*
	*
	* Elimina todas las subVersiones de la Version dada.
	*
	* @name		DeleteVersion
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $version
	*
	**/
	function DeleteVersion($version)
	{
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}
		$subVersions = $this->GetSubVersionList($version);
		if (!is_array($subVersions)) {
			return false;
		}
		foreach($subVersions as $subVersion) {
			$this->DeleteSubVersion($version, $subVersion);
		}
		return true;
	}


	/*
	*
	* Elimina una SubVersion
	*
	* @name		DeleteSubVersion
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version. El parametros versionID significa el campo IdVersion de la tabla versions si la funcion recibe $subVersions==null. El parametro versionID siginifica el campo Version de la tabla Versions si la funcion recibe $subVersions!=null;
	* @param	int $subVersion = null
	*
	**/
	function DeleteSubversion($versionID, $subVersion=null)	{
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$uniqueName = $this->GetTmpFile($versionID, $subVersion);

		if(!$uniqueName) {
			$this->SetError(3);
			return false;
		}

		$targetPath = \App::getValue( "AppRoot") . \App::getValue( "FileRoot"). "/". $uniqueName;

		/* Tal y como estaba el codigo dejaba sucia la base de datos
			si se borraba el archivo manualmente o simplemente no se podia borrar por permisos
		*/
		if (is_file($targetPath)) {
			FsUtils::delete($targetPath);
		}

		$dbObj = new DB();
		if(is_null($subVersion)) {
			$query = sprintf("DELETE FROM Versions WHERE IdVersion = %d AND IdNode = %d",
				$versionID, $this->nodeID);
			$versionToDelete = $versionID;
		} else {
			$query = sprintf("DELETE FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d",
				$versionID, $subVersion, $this->nodeID);
			$versionToDelete = $this->getVersionId($versionID, $subVersion);
		}

		// Deleting cache
		XMD_log::info("Deleting cache from versionId $versionToDelete");

		$pipeline = new PipelineManager();
		$pipeline->deleteCache($versionToDelete);

		// Se ejecutaba en cualquier caso
		if (ModulesManager::isEnabled('ximRAM')) {
			$this->conector->deleteNode($versionToDelete, true);
		}

		$dbObj->Execute($query);


		XMD_Log::info("DeleteVersion  for Node:".$this->nodeID.", Version: ".$versionID.".".$subVersion.", File: .".$uniqueName);
		return true;
	}


	/*
	*
	* Elimina las subVersiones de una determinada version que ya nos sirven. Tiene dos casos de uso, dependiendo del parametro all.
	* 1) $all = false => Elimina todas las subVersiones de la Version dada menos la primera, las X ultimas segun la tabla de configuracion (X >= 1).
	* 2) $all = true  =>  Elimina todas las subVersiones de la Version dada menos la primera.
	*
	* @name		PurgeSubVersions
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $version
	*
	**/
	function _purgeSubVersions($version, $all = null) {
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$maxSubVersionsAllowed = \App::getValue( "MaxSubversionsAllowed");

		if($maxSubVersionsAllowed <= 0) {
			$maxSubVersionsAllowed = 1;
		}

		$subVersions = $this->GetSubVersionList($version);
		if (!is_array($subVersions)) $subVersions = array();
		array_shift($subVersions);

		if(!$all) {
			for($i = 0; $i < $maxSubVersionsAllowed-1; $i++) {
				if(count($subVersions)) {
					array_pop($subVersions);
				}
			}
		}


		if($subVersions) {
			foreach($subVersions as $subVersion) {
				$this->DeleteSubVersion($version, $subVersion);
			}
		}
		return true;
	}

	function _purgeVersions() {
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$maxVersionsAllowed = \App::getValue( "MaxVersionsAllowed");
		if($maxVersionsAllowed <= 0) {
			$maxVersionsAllowed = 1;
		}

		$versions = $this->GetVersionList();

		for($i = 0; $i < $maxVersionsAllowed; $i++) {
			if(count($versions) > 0)
				array_pop($versions);
		}

		if(is_array($versions)) {
			foreach($versions as $version) {
				$this->DeleteVersion($version);
			}
		}
		return true;
	}

	/*
	*
	* Devuelve el nombre del archivo temporal en el que se guarda el contenido de una SubVersion
	*
	* @name		GetTmpFile
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
	* @param	int $subVersion = null
	*
	**/
	function GetTmpFile($versionID, $subVersion=null) {
		$this->ClearError();
		if (!($this->nodeID > 0)) {
			$this->SetError(1);
			return false;
		}

		$node = new Node($this->nodeID);
		$parentId = $node->GetParent();
		unset($node);

		$dbObj = new DB();

		if (is_null($subVersion)) {
			$query = sprintf("SELECT File FROM Versions v"
					. " INNER JOIN Nodes n on v.IdNode = n.Idnode AND n.IdParent = %d"
					. " WHERE IdVersion = %d AND v.IdNode = %d", $parentId, $versionID, $this->nodeID);
		} else {
			$query = sprintf("SELECT File FROM Versions v"
			 		. " INNER JOIN Nodes n on v.IdNode = n.Idnode AND n.IdParent = %d"
			 		. " WHERE Version = %d AND SubVersion = %d AND v.IdNode = %d",
			 		$parentId, $versionID, $subVersion, $this->nodeID);
		}

		$dbObj->Query($query);

		$uniqueName = !$dbObj->EOF ? $dbObj->GetValue('File') : false;
//		$uniqueName = $dbObj->GetValue('File');

		if(!$uniqueName) {
			$this->SetError(3);
			return false;
		}

		return $uniqueName;
	}


	/*
	*
	* Devuelve el comentario de una SubVersion
	*
	* @name		GetComment
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
	* @param	int $subVersion = null
	*
	**/
	function GetComment($versionID, $subVersion=null) {
		$this->ClearError();
		if ((!$this->nodeID > 0)) {
			$this->SetError(1);
			return NULL;
		}

		$dbObj = new DB();
		if(is_null($subVersion)) {
			$query = sprintf("SELECT Comment FROM Versions WHERE IdVersion = %d AND IdNode = %d",
				$versionID, $this->nodeID);
		} else {
			$query = sprintf ("SELECT Comment FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d",
				$versionID, $subVersion, $this->nodeID);
		}

		$dbObj->Query($query);
		return $dbObj->GetValue('Comment');
	}


	/**
	*
	* Devuelve el la fecha de almacenamiento de una SubVersion
	*
	* @name		GetDate
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
	* @param	int $subVersion = null
	*
	**/
	function GetDate($versionID, $subVersion=null) {
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return NULL;
		}
		$dbObj = new DB();

		if(is_null($subVersion)) {
			$query = sprintf("SELECT Date FROM Versions WHERE IdVersion = %d AND IdNode = %d",
				$versionID, $this->nodeID);
		} else {
			$query = sprintf("SELECT Date FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d",
				$versionID, $subVersion, $this->nodeID);
		}
		$dbObj->Query($query);

		return $dbObj->GetValue('Date');
	}


	/*
	*
	* Devuelve el id del usuario que gestiono la version.
	*
	* @name		GetUserID
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
	* @param	int $subVersion = null
	*
	**/
	function GetUserID($versionID, $subVersion=null)
	{
		$this->ClearError();
		if(!($this->nodeID > 0)) {
			$this->SetError(1);
			return NULL;
		}

		$dbObj = new DB();

		if(is_null($subVersion)) {
			$query = sprintf("SELECT IdUser FROM Versions WHERE IdVersion = %d AND IdNode = %d",
				$versionID, $this->nodeID);
		} else {
			$query = sprintf("SELECT IdUser FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d",
				$versionID, $subVersion, $this->nodeID);
		}
		$dbObj->Query($query);

		return $dbObj->GetValue('IdUser');
	}

	// Se queda de wrapper
	function GetVersionAndSubVersion($versionID){
		$version = new Version($versionID);
		if (!($version->get('IdVersion') > 0)) {
			return false;
		}

		return array($version->get('Version'), $version->get('SubVersion'));
	}

	/*
		Solo se usa en el script devel/scripts/ficheros_sobrantesIO.php
	*/
	function GetFiles() {
		$query = "SELECT File FROM Versions";
		$dbObj = new DB();
		$dbObj->Query($query);

		if (!((int) $dbObj->numRows > 0)) {
			return 0;
		}

		if (!$dbObj->numErr) {
			while(!$dbObj->EOF) {
					$array_files[] = $dbObj->GetValue("File");
					$dbObj->Next();
			}
			return $array_files;
		} else {
			$this->SetError(4);
		}
		return 0;
	}

	/*
	*
	* Resetea el flag de ocurrencia de error
	*
	* @name		ClearError
	* @author 	Jose I. Villar
	* @version	1.0
	*
	**/
    function ClearError()
	{
		$this->numErr = null;
		$this->msgErr = null;
	}


	/*
	*
	* Carga en el objeto el codigo del ultimo error ocurrido
	*
	* @name		SetError
	* @author 	Jose I. Villar
	* @version	1.0
	* @param	int $code
	*
	**/
	function SetError($code)
	{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}


	/*
	*
	* Devuelve un booleano que indica si hubo algun error
	*
	* @name		HasError
	* @author 	Jose I. Villar
	* @version	1.0
	* @return	bool $hasError
	*
	**/
    function HasError()
	{
		return $this->numErr;
	}

/*
    Devuelve el idversion correspondiente a la version publicada actualmente
*/
	function GetPublishedIdVersion() {
		$dbObj = new DB();
		$this->ClearError();
		if((int)$this->nodeID > 0) {
			$query = sprintf("SELECT MAX(IdVersion) AS max_version FROM Versions WHERE SubVersion = 0 AND IdNode = %d", $this->nodeID);
 			$dbObj->Query($query);
			$idVersion = $dbObj->GetValue('max_version');
			$version = new Version($idVersion);
			if (($version->get('IdVersion') > 0)
				&& ($version->get('Version') > 0)) {
				if(SynchroFacade::isNodePublished($this->nodeID)) {
					return $version->get('IdVersion');
				}
			}
		}
		return false;
	}

/*
    Si la version de la noticia en el colector la publicada devuelve false
*/
    function isEditedForPublishing($versionInColector){
		if(empty($versionInColector)){
			XMD_log::error("NOT VERSSION IN COLECTOR");
		    return false;
		}

        $publishedIdVersion = $this->GetPublishedIdVersion();
		$idVersionInColector = $this->getVersionId($versionInColector[0],$versionInColector[1]);

        if($publishedIdVersion == $idVersionInColector &&
        	SynchroFacade::isNodePublishedInAllActiveServer($this->nodeID)){
            return false;
        }

        return true;
    }

	function GetLastVersionId() {

		$dbObj = new DB();
		$this->ClearError();
		if (!is_null($this->nodeID)) {

 			$dbObj->Query("SELECT MAX(IdVersion) FROM Versions WHERE IdNode = ".$this->nodeID);
			$version = $dbObj->GetValue('MAX(IdVersion)');

			return $version;
		} else
			$this->SetError(1);
	}

	function GetVersionFromId($idVersion) {

 		$dbObj = new DB();
 		$dbObj->Query("SELECT Version, SubVersion FROM Versions WHERE IdVersion = $idVersion");

		$version = array();
		while(!$dbObj->EOF) {

			$version["version"] = $dbObj->GetValue("Version");
			$version["subversion"] = $dbObj->GetValue("SubVersion");
			$dbObj->Next();
		}

		return $version;
	}

	function GetPreviousVersion($idVersion) {
		if(!($this->nodeID > 0)) {
			return NULL;
		}

		$versions = new Version();
		$result = $versions->find('Max(IdVersion)', 'IdNode = %s AND IdVersion < %s',
			array($this->nodeID, $idVersion), MONO);
		if (empty($result) || !is_array($result)) {
			return NULL;
		}

		return $result[0];
	}

	function indexNode($idVersion, $commitNode) {

		if (!is_numeric($idVersion)) {
			XMD_Log::warning('Se ha intentado indexar un nodo por un IdVersion no valido.');
			return;
		}

		$usePool = (boolean)\App::getValue( 'AddVersionUsesPool');
		
		if (!$usePool) {

			$this->conector->indexNode($idVersion, $commitNode);
		} else {

			try {
				PoolerClient::request('Solr', array(
					array('versionid' => $idVersion, 'commit' => $commitNode)
				));
			} catch (Exception $e) {
				// do something
			}
		}
	}
}
?>
