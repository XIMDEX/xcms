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










if (!defined ("XIMDEX_ROOT_PATH")) {
	define ("XIMDEX_ROOT_PATH", realpath (dirname (__FILE__)."/../../"));
}

define('DOCXAP_VIEW', 1);
define('SOLR_VIEW', 2);
define('XIMIO_VIEW', 3);

require_once(XIMDEX_ROOT_PATH . "/inc/nodetypes/filenode.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/model/channel.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/model/language.inc");
require_once(XIMDEX_ROOT_PATH . "/inc/model/NodeDependencies.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/persistence/Config.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/cache/DexCache.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/dependencies/DepsManager.class.php');
ModulesManager::file('/inc/SolrViews.class.php', 'ximRAM');


class AbstractStructuredDocument extends FileNode  {


	/// Crea un nuevo nodo y carga su ID en la clase
	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $templateID = null, $IdLanguage = null, $aliasName = '', $channelList = null) {

		$loginID = XSession::get("userID");

		$templateNode = new Node($templateID);

		if ($templateNode->get('IdNode') > 0) {

			// relaxng template

			$templateNodeType = new NodeType($templateNode->get('IdNodeType'));

			if ($templateNodeType->get('Name') == 'RngVisualTemplate') {
				$content = $templateNode->class->buildDefaultContent();
			} else {
				$templateContent = $templateNode->class->GetContent();
				$templateContent = split("##########",$templateContent);
				$content = str_replace("'", "\'", $templateContent[1]);
			}

		} else {
			$content = '';
		}

		$doc = new StructuredDocument();
		$doc->CreateNewStrDoc($this->nodeID, $name, $loginID, $IdLanguage, $templateID, $channelList, $content);

		if ($doc->HasError())
			$this->parent->SetError(5);

		// Introducido por Jose Luis 10/03/2006
		// Antes estaba introduciendo el alias en el doc y no en el contenedor al crear el nodo.

		$nodeContainer=new Node($this->parent->GetParent());
		$nodeContainer->SetAliasForLang($IdLanguage, $aliasName);
		if ($nodeContainer->HasError())
			$this->parent->SetError(5);

		$this->updatePath();
	}


	/// Nombre para el archivo en produccion.
	function GetPublishedNodeName($channel = null) {

		$channel = new Channel($channel);
		$fileName = $this->parent->GetNodeName() . "-id".$channel->GetName() . "." . $channel->GetExtension();

		return $fileName;
	}

/**
*	Get the documents that must be publicated when the template is published
*	@param array $params
*	@return array
*/
	public function getPublishabledDeps($params) {

		$depsMngr = new DepsManager();

		// get links

		$level = empty($params['deeplevel']) ? 0 : $params['deeplevel'];
		$levelDocs[] = $this->parent->get('IdNode');
		$links = array();
		$i = 0;

		if ($level != 0) {
			do {
				$hasLinks = false;
				$levelDeps = array();

				foreach ($levelDocs as $idDoc) {
					$tmpDeps = $depsMngr->getBySource(DepsManager::STRDOC_NODE, $idDoc);

					if (!is_null($tmpDeps)) {
						$hasLinks = true;
						$levelDeps = array_unique(array_merge($levelDeps, $tmpDeps));
					}
				}

				$links = array_unique(array_merge($links, $levelDocs));
				$levelDocs = $levelDeps;
				$i++;

			} while (($i <= $level && $level > 0) || ($level < 0 && $hasLinks === true));
		}

		$links = (!isset($links) || is_null($links)) ? $levelDocs : $links;

		if (!in_array($this->parent->get('IdNode'), $links)) {
			array_push($links, $this->parent->get('IdNode'));
		}

		// foreach link get its not links dependencies

		$css = array();
		$script = array();
		$structure = array();
		$asset = array();
		$templateDeps = array();
		$workFlowSlaves = array();

		foreach ($links as $idDoc) {

			$tmpStructure = empty($params['withstructure']) ? array() :
				$depsMngr->getBySource(DepsManager::STRDOC_STRUCTURE, $idDoc);
			$structure = is_null($tmpStructure) ? $structure : array_unique(array_merge($tmpStructure, $structure));

			$tmpCss = empty($params['withcss']) ? array() :
				$depsMngr->getBySource(DepsManager::STRDOC_CSS, $idDoc);
			$css = is_null($tmpCss) ? $css : array_unique(array_merge($tmpCss, $css));

			$tmpScript = empty($params['withscript']) ? array() :
				$depsMngr->getBySource(DepsManager::STRDOC_SCRIPT, $idDoc);
			$script = is_null($tmpScript) ? $script : array_unique(array_merge($tmpScript, $script));

			$tmpAsset = empty($params['withasset']) ? array() :
				$depsMngr->getBySource(DepsManager::STRDOC_ASSET, $idDoc);
			$asset = is_null($tmpAsset) ? $asset : array_unique(array_merge($tmpAsset, $asset));

			$tmpTemplateDeps = empty($params['otf']) ? array() :
				$depsMngr->getBySource(DepsManager::STRDOC_TEMPLATE, $idDoc);
			$templateDeps = is_null($tmpTemplateDeps) ? $templateDeps :
				array_unique(array_merge($tmpTemplateDeps, $templateDeps));

			$node = new Node($idDoc);
			$tmpWorkFlowSlaves = $node->GetWorkFlowSlaves();
			$workFlowSlaves = is_null($tmpWorkFlowSlaves) ? $workFlowSlaves :
				array_unique(array_merge($tmpWorkFlowSlaves, $workFlowSlaves));
		}

		return array_merge($workFlowSlaves, $css, $script, $asset, $templateDeps, $links, $structure);
	}

	function GetContent() {

		$strDoc = new StructuredDocument($this->nodeID);

		return $strDoc->GetContent();
	}

	function SetContent($content, $commitNode = NULL) {

		$strDoc = new StructuredDocument($this->nodeID);
		$strDoc->SetContent($content, $commitNode);

		// Set content for any workflow slaves

		$wfSlaves = $this->parent->GetWorkflowSlaves();

		if (!is_null($wfSlaves)) {
			foreach ($wfSlaves as $docID) {
				$strDoc = new StructuredDocument($docID);
				$strDoc->SetContent($content, $commitNode);
			}
		}
	}

	function GetIcon() {

		$strDoc = new StructuredDocument($this->nodeID);
		if($strDoc->GetSymLink()) {
			$icon = pathinfo($this->parent->nodeType->GetIcon());
			/// Separa la extension del nombre del archivo
			$fileName = preg_replace('/(.+)\..*$/', '$1', $icon["basename"]);
			return $fileName."-link.".$icon["extension"];
		}

		return $this->parent->nodeType->GetIcon();
	}

	function view($viewType, $channel, $content = NULL, $idVersion = NULL) {

		switch ($viewType) {
			case DOCXAP_VIEW:
				return $this->RenderizeNode($channel, $content);
			case SOLR_VIEW:
				return SolrViews::solRview($this->nodeID, $channel, $content, $idVersion);
		}

		return NULL;
	}

	function _getPermissionGroups() {
		$node = new Node($this->nodeID);
		if (!($node->get('IdNode') > 0)) {
			return false;
		}
		$idNodeType = $node->get('IdNodeType');
		switch ($idNodeType) {
			case 5032: //xmldocumentnode
				$folderNodeType = 5018;
				break;
			case 5057: //ximlet
				$folderNodeType = 5055;
				break;
			case 5309: //noticia ximnews
				$folderNodeType = 5304;
				break;
			case 8002: //pdf
				$folderNodeType = 8000;
				break;
			case 5308:
				$folderNodeType = 5301;
				break;
		}

		do {
			$idNodeType = 0;
			$node = new Node($node->get('IdParent'));
			if (!($node->get('IdNode') > 0)) {
				return NULL;
			}
			$idNodeType = $node->get('IdNodeType');
		} while($idNodeType == $folderNodeType);

		$groups = $node->GetGroupList();
		if (!empty($groups)) {
			return implode(' ', $groups);
		}
		return NULL;
	}

	/// Renderiza el nodo en el sistema de archivos
	function RenderizeNode($channel=null,$content=null) {

		// Se obtiene el id del nodo padre (ya que parent contiene una instancia del node actual)
		// y creamos un objeto nodo con ese id
		$parentID = $this->parent->GetParent();
		$parent = new Node($parentID);

		/// Renderizamos hacia arriba toda la jerarqu\EDa
		if(!$parent->IsRenderized()) {
			$parent->RenderizeNode();
		}

		/// Conseguimos el path del archivo de destino
		$fileName = $this->GetNodePath();
		$fileContent = $this->GetRenderizedContent($channel, $content);

		/// Lo guardamos en el sistema de archivos
		if (!FsUtils::file_put_contents($fileName, $fileContent)) {
				$this->parent->SetError(7);
				$this->parent->messages->add(_('Ha ocurrido un error al intentar guardar el documento'), MSG_TYPE_ERROR);
				return false;
		}
		return true;
	}

	/**
	 * Cabecera docxap, hay que tener cuidado al a\F1adir par\E1metros, ya que estos deben ser enviados a solr y por lo tanto
	 * los que no deben ir a solr deben ser debidamente excluidos
	 *
	 * @param int $channel
	 * @param int $idLanguage
	 * @param int $documentType
	 * @param boolean $solrView
	 * @return string
	 */
	function _getDocXapHeader($channel, $idLanguage, $documentType) {
		/// El nombre de la plantilla de vista
		$template = new Node($documentType);
		$templateName = $template->get('Name');
		$visualTemplateTag = 'tipo_documento="'.$templateName .'"';
		$layoutName = str_replace('.xml', '', $templateName);
		$layoutTag = 'layout ="'. $layoutName .'"';

		$docxap = '';

		$docxap = sprintf("<docxap %s %s %s %s %s %s %s>", 
			$layoutTag,
			$this->_langXapAttrib($idLanguage),
                        $visualTemplateTag,
                        $this->ChannelsXapAttrib($channel),
                        $this->_buildDocXapAttribs($idLanguage),
                        $this->_getDocXapPropertiesAttrib(true),
                        NULL
                 );

		if (ModulesManager::isEnabled('ximPAS')) {
//			$rdfMacro = sprintf('@@@RMximdex.rdf(%s)@@@', $this->nodeID);
			$rdfMacro = '<semantic/>';
			$docxap .= $rdfMacro;
		}

		return $docxap;
	}

	function GetRenderizedContent($channel=null, $content=null, $onlyDocXap=null) {

		$strDoc = new StructuredDocument($this->nodeID);
		if (!($strDoc->get('IdDoc') > 0)) {
			return NULL;
		}

		$documentType = $strDoc->GetDocumentType();
		$idLanguage = $strDoc->GetLanguage();

		$docXapHeader = $this->_getDocXapHeader($channel, $idLanguage, $documentType);

		if ($onlyDocXap) {
			return $docXapHeader;
		}

//		$rdfMacro = '';
//		if (ModulesManager::isEnabled('ximPAS')) {
//			$rdfMacro = sprintf('@@@RMximdex.rdf(%s)@@@', $this->nodeID);
//		}

		$doctypeTag = Config::getValue("DoctypeTag");
		$encodingTag = Config::getValue("EncodingTag");
		/// El contenido que hay en la bd si no nos pasaron ninguno como parametro
		if (is_null($content)) {
			$content = $strDoc->GetContent();
		}
		return ($encodingTag . "\n" . $doctypeTag . "\n\n" .
						$docXapHeader .
//						$rdfMacro .
						$this->InsertLinkedximletS($idLanguage) . "\n" .
						$content . "\n" .
						"</docxap>\n") ;

	}

	function DeleteNode() {
		$parent = new Node($this->parent->get('IdParent'));
		$st = new StructuredDocument($this->parent->get('IdNode'));
		$dbObj = new DB();
		$query = sprintf("DELETE FROM NodeNameTranslations WHERE IdNode = %s AND IdLanguage = %s",
			$dbObj->sqlEscapeString($parent->get('IdNode')),
			$dbObj->sqlEscapeString($st->get('IdLanguage')));
		$dbObj->execute($query);
		$doc = new StructuredDocument();
		$doc->SetID($this->nodeID);
		if ($doc->HasError()) {
			$this->parent->SetError(5);
			return;
		}

		$doc->DeleteStrDoc();

		if ($doc->HasError()) {
			$this->parent->SetError(5);
		}

		// Deletes dependencies in rel tables

		$depsMngr = new DepsManager();
		$depsMngr->deleteBySource(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_NODE, $this->parent->get('IdNode'));
		$depsMngr->deleteByTarget(DepsManager::STRDOC_NODE, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_TEMPLATE, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_ASSET, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_CSS, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_SCRIPT, $this->parent->get('IdNode'));

		XMD_Log::info('StrDoc dependencies deleted');
	}

	function RenameNode($name = null) {

		$doc = new StructuredDocument($this->nodeID);
		$doc->SetName($name);
		$this->updatePath();
	}

	function GetAllGenerations() {

		$result = array();

		$chanList = $this->GetChannels();

		if ($chanList) {
			foreach($chanList as $chanID) {
				$result[] = array('channel' => $chanID, 'content' => $this->Generate($chanID));
			}
		}

		return $result;
	}

	function HasChannel($channelID) {

		$query = sprintf("SELECT IdDoc FROM RelStrDocChannels WHERE"
				. " IdDoc= %s"
				. " AND IdChannel= %s",
				$this->dbObj->sqlEscapeString($this->nodeID),
				$this->dbObj->sqlEscapeString($channelID));

	  	$this->dbObj->Query($query);

		if ($this->dbObj->numErr) {
			$this->parent->SetError(5);
		}

		return $this->dbObj->numRows;
	}

	// TODO: Rewrite in Views.
	function ToXml($depth, & $files, $recurrence) {


		$xmlBody = parent::ToXML($depth, $files, $recurrence);

		//Canales
		$channelList = $this->GetChannels();
		if (is_array($channelList)) {
			reset($channelList);
			while (list(, $idChannel) = each($channelList)) {
				$node = new Node($idChannel);
				$xmlBody .= $node->ToXml($depth, $files, $recurrence);
				unset($node);

			}
		}
		unset($channelList);

		$indexTabs = str_repeat("\t", $depth + 1);
		$query = sprintf("SELECT nt.IdLanguage, nt.Name"
					. " FROM NodeNameTranslations nt"
					. " INNER JOIN StructuredDocuments sd ON sd.IdLanguage = nt.IdLanguage AND sd.IdDoc = %d"
					. " WHERE IdNode = %d",
					$this->nodeID,
					$this->parent->GetParent());
		$this->dbObj->Query($query);
		while (!$this->dbObj->EOF) {
			$idLanguage = $this->dbObj->GetValue('IdLanguage');
			$name = $this->dbObj->GetValue('Name');
			$xmlBody .= sprintf("%s<NodeNameTranslation IdLang=\"%d\">\n",
					$indexTabs, $idLanguage);
			$xmlBody .= sprintf("%s\t<![CDATA[%s]]>\n",
					$indexTabs, utf8_encode($name));
			$xmlBody .= sprintf("%s</NodeNameTranslation>\n", $indexTabs);
			$this->dbObj->Next();

		}
		return $xmlBody;
	}

	function getXmlTail() {

		$returnValue = '';
		$query = sprintf("SELECT TargetLink FROM StructuredDocuments WHERE IdDoc = %d", $this->nodeID);
		$this->dbObj->Query($query);

		if ($this->dbObj->numRows == 1) {

			$targetLink = $this->dbObj->GetValue('TargetLink');
			if ((int)$targetLink > 0) {
				$returnValue = sprintf(' targetLink="%d"', $targetLink);
			}
		}

		return $returnValue;
	}

	function GetChannels() {

		$query = sprintf("SELECT idChannel FROM RelStrDocChannels WHERE IdDoc = %d", $this->nodeID);

		$this->dbObj->Query($query);

		if ($this->dbObj->numErr) {
			$this->parent->SetError(5);
		}

		$out = NULL;
		while (!$this->dbObj->EOF) {
	 		$out[] = $this->dbObj->GetValue("idChannel");
			$this->dbObj->Next();
		}

	 	return $out;
	}

	function getTemplate() {

		$structuredDocument = new StructuredDocument($this->nodeID);

		if ($structuredDocument->get('IdDoc') > 0) {
			return $structuredDocument->get('IdTemplate');
		}

		return false;
	}

	function getLanguage() {
		$structuredDodument = new StructuredDocument($this->nodeID);
		$idLanguage = $structuredDodument->get('IdLanguage');
		return $idLanguage > 0 ? $idLanguage : NULL;
	}

	function SetChannel($channelID)	{

		$sqls = "INSERT INTO RelStrDocChannels "  .
				"(IdRel, IdDoc, IdChannel) " .
				"VALUES (NULL, " . $this->nodeID . ", " . $channelID . ")";

		$this->dbObj->Execute($sqls);

		if ($this->dbObj->numErr)
		  	$this->parent->SetError(5);
	}

	// Borra la asociacion entre un canal y el nodo actual.
	function DeleteChannel($channelID) {

		$sqls = "DELETE FROM RelStrDocChannels " .
				" WHERE IdDoc = " . $this->nodeID .
				" AND IdChannel = " . $channelID ;

	  	$this->dbObj->Execute($sqls);

		if ($this->dbObj->numErr)
			$this->parent->SetError(5);
	}

	// Borra todos las asociaciones entre el nodo actual y los canales.
	function DeleteChannels() {

		$sqls = "DELETE FROM RelStrDocChannels " .
				" WHERE IdDoc = " . $this->nodeID ;

  		$this->dbObj->Execute($sqls);

		if ($this->dbObj->numErr)
			$this->parent->SetError(5);
	}

	function _langXapAttrib($idLang) {
			// Insertamos los idiomas de los hermanos del nodo
			$outPut2 = NULL;
	                $colectible = ' idiomas="' ;
	                $node = new Node($this->parent->get('IdNode'));
	                $idParent = $node->get('IdParent');
	                $nodeParent = new Node($idParent);
	                $docList[] = $nodeParent->GetChildren();
	                foreach ($docList as $docID)
			                        {
			                        foreach($docID as $docdocID){
			                                // obtengo el lenguaje
			                                $strDoc = new StructuredDocument($docdocID);
		        	                        $langID = $strDoc->GetLanguage();
		                	                $lang = new Language($langID);
		                        	        $colectible.= $lang->GetIsoName(). ',';
			                                }
		                        	}
			$colectible = substr($colectible,0,strlen($colectible)-1);
			$outPut2 .= $colectible.'"';
			// Fin insercion del los idiomas

		$lang = new Language($idLang);
		$outPut2 .= ' idioma="' . $lang->GetIsoName() . '"';
		return $outPut2;

	}

	function _buildDocXapAttribs($idLang) {

		return $this->DocXapAttribLevels($idLang);
	}

	function ChannelsXapAttrib($channelID=null) {

		$doc = new StructuredDocument($this->nodeID);
		$channelList = $doc->GetChannels();

		$outPut = NULL;
		if($channelList) {
			if(in_array($channelID, $channelList)) {
				$channel = new Channel($channelID);
				$outPut = 'canal="' . $channel->GetName() . '"';
				$outPut .= ' extension="' . $channel->GetExtension() . '"';
			} else {
				$outPut = 'canal="" ';
			}

			reset($channelList);
			while(list(, $channelID) = each($channelList)) {
				$channel = new Channel($channelID);
				$channelNames[] = $channel->get('Name');
				$channelDesc[] = $channel->get('Description');
			}

			$outPut .= ' canales="'.implode(",",$channelNames).'"';
			$outPut .= ' canales_desc="'.implode(",",$channelDesc).'"';
		}

		return $outPut;
	}

	// Genera un documento

	private function Generate($channel, $content=null, $outPut=null) {
		$nodeid = $this->nodeID;

		$node = new Node($nodeid);

		if (Config::getValue('dexCache')) {
			if ( ! DexCache::isModified($nodeid) ) {
				$content = DexCache::getPersistentSyncFile($nodeid, $channel);
				return $content;
				// Si no modificado. Devuelve el sync antiguo.
			}
		}

		$dataFactory = new DataFactory($nodeid);
		$version = $dataFactory->GetLastVersionId();

		$data['CHANNEL'] = $channel;
		$transformer = $node->getProperty('Transformer');
		$data['TRANSFORMER'] = $transformer[0];

		$pipeMng = new PipelineManager();
		$content = $pipeMng->getCacheFromProcessAsContent($version, 'StrDocToDexT', $data);

		if (Config::getValue('dexCache')) {
			$nodeid = $this->nodeID;
			if (!DexCache::createPersistentSyncFile($nodeid, $channel, $output)) {
				$this->messages->add(sprintf(_('Ha ocurrido un error al generar el documento %s para el canal %s'), $this->parent->get('Name'), $channel), MSG_TYPE_ERROR);
				return false;
			}
		}

		return $content;
	}




	function InsertLinkedximletS($langID, $sectionId = null) {
		$linkedXimlets = $this->getLinkedXimlets($langID, $sectionId);
		$output = '';
		if(sizeof($linkedXimlets) > 0) {
			foreach($linkedXimlets as $ximletId) {
				$output .= "<ximlet>@@@GMximdex.ximlet($ximletId)@@@</ximlet>";
			}
		}

		return $output;
	  }

	function getLinkedximletS($langID, $sectionId = null) {
		if (is_null($sectionId)) {
			$node = New Node($this->nodeID);
			$sectionId = $node->GetSection();
		}

		$depsMngr = new DepsManager();
		$ximletContainers = $depsMngr->getBySource(DepsManager::SECTION_XIMLET, $sectionId);
	  	$linkedXimlets = array();

		if (sizeof($ximletContainers) > 0) {
			foreach ($ximletContainers as $ximletContaineId) {
				$node = new Node($ximletContaineId);
				$ximlets = $node->GetChildren();
				foreach ($ximlets as $ximletId) {
					$strDoc = new StructuredDocument($ximletId);
					if ($strDoc->get('IdLanguage') == $langID) {
						$linkedXimlets[] = $ximletId;
					}
				}
			}
		}

		return $linkedXimlets;
	  }

	  function DocXapDynamicAttrib($nodeID) {

		  $prop=new Properties();
		  $array_prop=$prop->GetPropertiesNode($nodeID);
		  $nprop=count($array_prop);

		  $str_props="";

		  for($i=0;$i<$nprop;$i++) {
		  	$str_props=$str_props.' '.$array_prop[$i]["Name"].'="'.$array_prop[$i]["Value"].'"';
		  }

		 return $str_props;
	  }

	  function DocXapAttribLevels($langID) {

 		  $node = new Node($this->parent->get('IdNode'));

		  $parent = new Node($node->get('IdParent'));

		  $s = ' nodeid="'.$node->get('IdNode').'"  parentnodeid="'.$parent->get('IdNode').'"';
  		  $s .= ' nombre_tiponodo="'.$node->nodeType->get('Name').'"  id_tiponodo="'.$node->nodeType->get('IdNodeType').'"';
		  $s .= ' documento="'.$parent->get('Name').'" nombre_documento="'.$parent->GetAliasForLang($langID).'"';
		  $tree = $node->TraverseToRoot();

		  // It must to exclude from lenght the node itself, its container, and its folder

		  $length = sizeof($tree) - 3;

		  // the level

		  $j = 0;

		  for ($i = 1; $i < $length; $i++) {

			  $ancestor = new Node($tree[$i]);
			  $alias = $node->GetAliasForLang($langID);

			  switch ($i) {
				  case 1:
					  $s .= ' proyecto="'. $ancestor->get('Name').'"';
					  continue;

				  case 2:
					  $s .= ' servidor="'. $ancestor->get('Name').'"';
					  continue;

				  default:

					  if ($ancestor->nodeType->get('IsSection') == 1) {
						  $j++;
						  $s .= " nivel$j=\"".$ancestor->get('Name')."\" nombre_nivel$j=\"".
						  	$ancestor->GetAliasForLang($langID)."\"\n";
					  }

					  continue;
			  }

		  }

		  return $s;
	}

	function GetDependencies() {

		$nodeDependencies = new NodeDependencies();
		return $nodeDependencies->getByTarget($this->nodeID);
	}

	function getChildrenByLanguage($idLanguage) {
		$childrens = $this->parent->GetChildren();
		if (is_array($childrens) && !empty($childrens)) {
			foreach ($childrens as $idChildren) {
				$node = new Node($idChildren);
				if ($node->class->getLanguage == $idLanguage) {
					return $node->get('IdNode');
				}
			}
		}
		return NULL;
	}

	function _getDocXapPropertiesAttrib($withInheritance = false) {
		$node = new Node($this->nodeID);
		$properties = $node->getAllProperties($withInheritance);
		$docxapPropAttrs = "";

		if(is_array($properties) & count($properties) > 0) {
			foreach($properties as $idProperty => $propertyValue) {
				$docxapPropAttrs .= 'property_' . $idProperty . '="' . $propertyValue[0] . '" ';
			}
		}

		return $docxapPropAttrs;
	}
}
?>
