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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/TarArchiver.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
require_once(XIMDEX_ROOT_PATH . '/inc/parsers/ParsingRng.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/repository/nodeviews/View_SQL.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/xml/XmlBase.class.php");
include_once (XIMDEX_ROOT_PATH. "/inc/xml/XML.class.php");
include_once (XIMDEX_ROOT_PATH. "/inc/persistence/Config.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_TARGZ extends Abstract_View implements Interface_View {

	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {

		$content = $this->retrieveData($content);
		//VALIDATING DATA
		$version = new Version($idVersion);
		if (!($version->get('IdVersion') > 0)) {
			XMD_Log::error("Se ha cargado una versión incorrecta ($idVersion)");
			return NULL;
		}
		$node = new Node($version->get('IdNode'));
		$nodeType = new NodeType($node->get('IdNodeType'));
		$nodeId = $node->get('IdNode');
		$nodeTypeName = $nodeType->get('Name');
		$dataEncoding = Config::getValue('dataEncoding');

		if (!($nodeId > 0)) {
			XMD_Log::error("El nodo que se está intentando convertir no existe: " . $version->get('IdNode'));
			return NULL;
		}
		if (!array_key_exists('PATH', $args) && !array_key_exists('NODENAME', $args)) {
			XMD_Log::error('Path and nodename arguments are mandatory');
			return NULL;
		}

		$tarFile = $args['PATH'];
		$tmpFolder = XIMDEX_ROOT_PATH . Config::GetValue('TempRoot');

		//Sets content on SQL and XML files
		$arrayContent = explode('<sql_content>', $content);
		$tmpDocFile = $tmpFolder . '/' . $args['NODENAME'] . '.xml';
		$tmpSqlFile = $tmpFolder . '/' . $args['NODENAME'] . '.sql';
		$xmlContent = $arrayContent[0];
		$sqlContent = substr(trim($arrayContent[1]), 0, -14);

		//Encode the content to ISO, now OTF only work in ISO mode, because the jsp files are in ISO too
		$xmlContent = XmlBase::recodeSrc($xmlContent, XML::ISO88591);
		if (!FsUtils::file_put_contents($tmpDocFile, $xmlContent)) {
			return false;
		}

		//Work with each nodetype
		switch($nodeTypeName) {
			case 'XimNewsNewLanguage':
				//Add the insert query for ximNewsCache to sql content
				//this is here because when a doc is published, its version is up, and if we do it
				//in view sql, the version is old.	
	
				//Generate caches for this versions
				$this->generateNewsCache($version->get('IdNode'));
	
				$condition = "idNew=%s order by IdVersion desc limit 1";
				$params = array('IdNew' => $nodeId);
				$vistaSql = new View_SQL();

				$insertQuery = $vistaSql->makeInsertQuery('XimNewsCache', $condition, $params);
				$sqlContent.= $insertQuery;
				break;
			case 'XmlDocument':

				
				break;
			default:
				//do nothing
				break;
		}

		$sqlContaent = XmlBase::recodeSrc($sqlContent, XML::ISO88591);
		if (!FsUtils::file_put_contents($tmpSqlFile, $sqlContent)) {
			return false;
		}

		// Making tar file with the aditional files
		$tarArchiver = new TarArchiver($tarFile);
		$tarArchiver->addEntity($tmpDocFile);
		$tarArchiver->addEntity($tmpSqlFile);

		$additionalFiles = $this->getAdditionalFiles($nodeId,$idVersion,$args);
		if (!is_null($additionalFiles)) {
			//Encode the files to ISO, OTF only works in ISO mode
			if (($dataEncoding != XML::ISO88591) && (is_array($additionalFiles))){
				foreach ($additionalFiles as $key=>$file) {
					if (!XmlBase::recodeFile($file, XML::ISO88591)) {
						return false;
					}
				}
			}
			$tarArchiver->addEntity($additionalFiles);
		}
		$tarFileName = $tarArchiver->pack();

		//Recode the files to before encoding, about dataEncoding config valuea
		if (($dataEncoding != XML::ISO88591) && (is_array($additionalFiles))){
			foreach ($additionalFiles as $key=>$file) {
				if (!XmlBase::recodeFile($file, $dataEncoding)) {
					return false;
				}
				
			}
		}

		// Removing tar extension
		rename($tarFile . '.tar', $tarFile);

		return $this->storeTmpContent($arrayContent[0]);
	}

	/**
	 * Return an array file names for otf
	 *
	 * @param unknown_type $nodeId
	 * @return array String, filenames
	 */
	function getAdditionalFiles($nodeId,$idVersion,$args) {

		$node = new Node($nodeId);
		$nodeType = new NodeType($node->get('IdNodeType'));
		$nodeTypeName = $nodeType->get('Name');

		$additionalFiles = array();

		XMD_Log::info("Getting additional files for node $nodeId type $nodeTypeName");

		switch($nodeTypeName) {
			case 'XimNewsNewLanguage':
				// Adds XimNewsCache file
				$condition = 'IdNew = %s order by IdVersion desc limit 1';
				$params = array('IdNew' => $nodeId);
				$file = $this->getFile('XimNewsCache', 'File', $condition, $params);

				if (!is_null($file)) {
					$additionalFiles[] = XIMDEX_ROOT_PATH . '/data/files/' . $file;
				}

				//Add files for the colectors --> pvdheader & colector
				$sql = "select IdColector from RelNewsColector where IdNew = $nodeId";
				$colectors = array();
				//Work with each colector for this news
				$dbObj = new DB();
				$dbObj->Query($sql);
				$i = 0;
				while(!$dbObj->EOF) {
					$colectors[$i] = $dbObj->GetValue("IdColector");
					$i++;
					$dbObj->Next();
				}

				if (count($colectors)>0){
					foreach($colectors as $indice => $colectorId) {

						// Adds the default pvd content
						$ximNewsColector = new XimNewsColector($colectorId);
						$pvdId = $ximNewsColector->get('IdTemplate');

						$rngParser = new ParsingRng();
						$defaultContent = $rngParser->buildDefaultContent($pvdId);
						$tmpFolder = XIMDEX_ROOT_PATH . Config::GetValue('TempRoot');
						$headerFile = $tmpFolder . '/' . "pvdheader$pvdId";

						if (FsUtils::file_put_contents($headerFile, $defaultContent)) {
							$additionalFiles[] = $headerFile;
						}else{
							XMD_Log::error("View TARG:No se ha podido añadir el archivo $headerFile");
						}

						//Generate Docxap for the bulletin
						$idXimlet = $this->getFirstXimlet($colectorId);
						if ($idXimlet >0){
							$node = new Node($idXimlet);
							$lastVersion = $this->getLastVersion($idXimlet);
							$docxapContent = $this->generateDocXapForBulletin($node,$lastVersion);
							$docxapFile = $tmpFolder . '/' .$ximNewsColector->get('Name').".docxap";

							if (FsUtils::file_put_contents($docxapFile, $docxapContent)) {
								$additionalFiles[] = $docxapFile;
							}else{
								XMD_Log::error("View TARG:No se ha podido añadir el archivo $docxapFile");
							}
						}else{
							XMD_Log::error("View TARG:No se ha generado la docxap para el colector $colectorId");
						}
					}
				}
				break;
			case 'XmlDocument':
				break;
			default:
				//do nothing
				break;
		}
		return $additionalFiles;
	}

	/**
	 * Return the file name about the params
	 *
	 * @param unknown_type $tableName
	 * @param unknown_type $field
	 * @param unknown_type $condition
	 * @param unknown_type $params
	 * @return String filename
	 */
	function getFile($tableName, $field, $condition, $params) {

		$factory = new Factory(XIMDEX_ROOT_PATH . "/inc/model/orm/", $tableName);
		$object = $factory->instantiate("_ORM");

		if (!is_object($object)) {
			XMD_Log::info("Error, la clase $tableName de orm especificada no existe");
			return NULL;
		}

		$result = $object->find($field, $condition, $params, MULTI);

		if (!is_null($result)) {
			reset($result);
			$fileName = $result[0][0];
			return $fileName;
		}

		XMD_Log::info("Additional file for $tableName not found");

		return NULL;
	}

	/**
	 * Generate the bulletin docxap for OTF
	 *
	 * @param Node $node
	 * @param Int $idVersion
	 * @return String
	 */
	function generateDocXapForBulletin($node,$idVersion){

		//docxap for return it
		$docxapout="";
		$channels="";
		$channel;
		$language="";

		//check that the Version is ok
		if(!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				XMD_Log::error('VIEW TARGZ: Se ha cargado una versión incorrecta (' . $idVersion . ')');
				return "";
			}
			$structuredDocument = new StructuredDocument($version->get('IdNode'));
			$channels=$structuredDocument->GetChannels();
			$language=$structuredDocument->GetLanguage();

			if (!($structuredDocument->get('IdDoc') > 0)) {
				XMD_Log::error('VIEW TARGZ: El structured document especificado no existe: ' . $structuredDocument->get('IdDoc'));
				return "";
			}
			//If it is all ok
			if ((is_array($channels)) && (!is_null($node)) && (!is_null($structuredDocument)) && (array_key_exists(0, $channels)) && (!is_null($language))){
				//Select, for example, the first channel, it's the same because otf will renderize the
				//xml with a channel selected by the user
				$channel=$channels[0];
				$documentType = $structuredDocument->GetDocumentType();
				$docxapout = $node->class->_getDocXapHeader($channel, $language, $documentType);

				// Check out:
				if (!isset($docxapout) || $docxapout == "") {
					XMD_Log::error('VIEW TARGZ: No se ha especificado la cabecera docxap del nodo ' . $node->GetNodeName() . ' que quiere renderizar');
					return "";
				}
			}else{
				XMD_Log::error("VIEW TARGZ:No se ha podido generar la etiqueta doxcap para el boletin, renderizado para OTF");
			}

			return $docxapout;

		}else{
			XMD_Log::error("VIEW TARGZ:No se ha podido generar la etiqueta doxcap para el boletin, renderizado para OTF");
		}
	}

	private function getLastVersion($idNode){
		$sql = "select IdVersion from Versions where IdNode = $idNode order by Version desc limit 1;";

		$dbObj = new DB();
		$dbObj->Query($sql);
		while(!$dbObj->EOF) {
			$idVersion = $dbObj->GetValue("IdVersion");
			$dbObj->Next();
		}
		return $idVersion;
	}
	/**
	 * The news' cache is created when the colector is generated, in otf don't generate colector so the news
	 * cache is created here
	 *
	 * @param unknown_type $idNew
	 * @return unknown
	 */
	private function generateNewsCache($idNew){

		$sql = "select IdColector from RelNewsColector where IdNew = $idNew";
		$colectors = array();
		//Work with each colector for this news
		$dbObj = new DB();
		$dbObj->Query($sql);
		$i = 0;
		while(!$dbObj->EOF) {
			$colectors[$i] = $dbObj->GetValue("IdColector");
			$i++;
			$dbObj->Next();
		}

		if (count($colectors)>0){
			//Get the pvdid for the cache is generated, only one cache for colector and pvdid
			$idPvdsCacheGenerated = array();
			foreach($colectors as $indice => $idColector) {

				$ximNewsColector = new XimNewsColector($idColector);
				$xslFile = $ximNewsColector->get('XslFile');
				$idPvd = $ximNewsColector->get('IdTemplate');

				//If havent created cache for this pvd and colector
				if (!(in_array($idPvd,$idPvdsCacheGenerated))){

					//push the pvdid for dont do it any more
					array_push($idPvdsCacheGenerated,$idPvd);

					$relNewsColector = new RelNewsColector();
					$idRel = $relNewsColector->hasNews($idColector, $idNew);

					if ($idRel > 0) {
						$relNewsColector = new RelNewsColector($idRel);
						$version = $relNewsColector->get('Version');
						$subversion = $relNewsColector->get('SubVersion');
						$cacheId = $relNewsColector->get('IdCache');
					} else {
						XMD_Log::info('Sin relación en la base de datos');
					}

					if(!($cacheId > 0)){
						$df = new DataFactory($idNew);
						$versionId = $df->getVersionId($version,$subversion);

						//Creamos la cache (si procede) y modificamos contadores
						$cache = new XimNewsCache();
						$cacheId = $cache->CheckExistence($idNew,$versionId,$idPvd);

						if(!($cacheId > 0)){
							$cacheId = $cache->CreateCache($idNew,$versionId,$idPvd,$xslFile);

							if(!$cacheId){
								XMD_Log::info("ERROR Creando cache de noticia $idNew");

								//Elimino la asociacion para no dejar inconsistencia entre el boletin y la tabla
								$relNewColector = new RelNewsColector($idRel);
								$relNewColector->delete();
							}
						}

						$cache = new XimNewsCache($cacheId);
						$counter = $cache->get('Counter') + 1;
						$cache->set('Counter',$counter);
						$numRows = $cache->update();

						if($numRows == 0){
							XMD_Log::info("ERROR Actualizando contador en $cacheId");
							return false;
						}

						$relNewsColector->set('IdCache', $cacheId);
						$relNewsColector->update();
					}

					//cogemos el contenido de la noticia
					$ximNewsCache = new XimNewsCache($cacheId);
					$newContent = $ximNewsCache->getContentCache();
					$contenido = $newContent;
				}
			}
		}
	}

	private function getFirstXimlet($idColector){
		$sql = "select IdNode from Nodes as N inner join XimNewsColector as X on N.IdParent = X.IdXimlet where X.IdColector=$idColector limit 1;";
		$dbObj = new DB();
		$dbObj->Query($sql);
		$idXimlet = 0;
		if ($dbObj->numRows > 0) {
			$idXimlet = $dbObj->GetValue("IdNode");
			return $idXimlet;
		}else{
			return null;
		}
	}
}

?>
