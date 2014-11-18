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


ModulesManager::file('/inc/nodetypes/structureddocument.php');
ModulesManager::file('/inc/model/XimNewsNews.inc', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsArea.php', 'ximNEWS');
ModulesManager::file('/inc/ximNEWS_Adapter.php', 'ximNEWS');
ModulesManager::file('/inc/dependencies/DepsManager.class.php');

/**
*   @brief Handles news.
*/

class XimNewsNewLanguage extends AbstractStructuredDocument  {

	/**
	*  Creates the structuredDocument and adds the row to the table XimNewsNews.
	*  @param string name
	*  @param int parentID
	*  @param int nodeTypeID
	*  @param int stateID
	*  @param int templateID
	*  @param int IdLanguage
	*  @param int aliasName
	*  @param array channelList
	*  @param array dataNews
	*  @return int|null
	*/

	function CreateNode($name, $parentID, $nodeTypeID, $stateID, $templateID, $IdLanguage,
		$aliasName, $channelList, $dataNews) {



		// Creating structureddocument node

		parent::CreateNode($name, $parentID, $nodeTypeID, $stateID, $templateID, $IdLanguage, $aliasName, $channelList);

		$node = new Node($parentID);

		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error("Error creating strdoc");
			return NULL;
		}

		$idSection = $node->GetSection();
		$lang = new Language($IdLanguage);

		$nodeId = $this->parent->get('IdNode');
		$dataNews['nodeid'] = $nodeId;
		$dataNews['name'] = $name;
		// wk
		$newsDate = isset($dataNews['noticia_fecha']) ? $dataNews['noticia_fecha'] : $dataNews['fecha'];

		// builds xml
		
		if (is_null($dataNews)) {
			XMD_Log::error("Data news don't exist. It will save a document with empty values. ");
		}else{
			$result = ximNEWS_Adapter::SetNewsXmlContent($dataNews, $templateID);			
		}
	
		if (is_null($result)) {
			XMD_log::error("Setting news content, id: $nodeId");
		} else {
			$strDoc = new StructuredDocument($nodeId);
			$strDoc->SetContent($result, true);
		}

		if ((int) preg_match("/([0-9]{1,2})[-\/]([0-9]{1,2})[-\/]([0-9]{2,4})(\s*([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))*/",
					$newsDate, $regs) == 0) {
			
			XMD_Log::error('Incorrect date format');
			$date = mktime();
		} else {

			$hourNews = isset($regs[5]) ? $regs[5] : 0;
			$minNews = isset($regs[6]) ? $regs[7] : 0;
			$secNews = isset($regs[6]) ? $regs[7] : 0;
			$date = mktime($hourNews, $minNews, $secNews, $regs[2], $regs[1], $regs[3]);
		}

		// Inserts ximNewsNews row
		$title = isset($dataNews['title']) ? $dataNews['title'] : 'title';
		$ximNewsNews = new XimNewsNew();
		$ximNewsNews->set('IdNew', $nodeId);
		$ximNewsNews->set('Fecha', $date);
		$ximNewsNews->set('Name', $name);
		$ximNewsNews->set('Titular', $title);
		$ximNewsNews->set('TimeStamp', mktime());
		$ximNewsNews->set('IdSection', $idSection);

		$idNew = $ximNewsNews->add();

		if (!($idNew > 0)) {
			XMD_log::error("Error al persisitir en XimNewsNews");
			return NULL;
		}

		return $idNew;
	}

	/**
	*  Updates some XimNewsNewLanguage data.
	*  @param int idNode
	*  @param string name
	*  @param int idSection
	*  @param array dataNews
	*  @return unknown
	*/

	function update($idNode, $name, $idSection, $dataNews) {

		// update ximNewsNews table
	
		if ((int) preg_match("/([0-9]{1,2})[-\/]([0-9]{1,2})[-\/]([0-9]{2,4})(\s*([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))*/",
					$dataNews['noticia_fecha'], $regs) == 0) {
			
			XMD_Log::error('Incorrect date format');
			$date = mktime();
		} else {

			$hourNews = isset($regs[5]) ? $regs[5] : 0;
			$minNews = isset($regs[6]) ? $regs[7] : 0;
			$secNews = isset($regs[6]) ? $regs[7] : 0;
			$date = mktime($hourNews, $minNews, $secNews, $regs[2], $regs[1], $regs[3]);
		}


		$ximNewsNews = new XimNewsNew($idNode);
		$ximNewsNews->set('Fecha', $date); 
		$ximNewsNews->set('Name', $name);
		$ximNewsNews->set('Titular', $dataNews['title']);
		$ximNewsNews->set('TimeStamp', mktime());
		$ximNewsNews->set('IdSection', $idSection);

		$ximNewsNews->update();

		// update xml
		
		$strDoc = new StructuredDocument($idNode);
		$idTemplate = $strDoc->get('IdTemplate');

		$result = ximNEWS_Adapter::SetNewsXmlContent($dataNews, $idTemplate);

		if (is_null($result)) {
			XMD_log::error("Setting news content, id: $idNode");
		} else {
			$strDoc->SetContent($result, true);
		}

	}

	/**
	*	Updates the fields that persists in database
	*	@return bool
	*/

	function updateNew(){

		$nodeNew = new Node($this->nodeID);
		$content = Config::getValue('EncodingTag') . $nodeNew->getContent();

		$domDoc = new DOMDocument();
		
		if (!$domDoc->loadXML($content)) {
			XMD_Log::error('Invalid XML from newsdocument '. $this->nodeID);
			return false;
		}

		$xpath = new DOMXPath($domDoc);

		$nodeList = $xpath->query('//*[@name="title"]');


		if ($nodeList->length > 0) {
			$titular = $nodeList->item(0)->nodeValue;
		} else {
			XMD_Log::error('Title not found in newsDocument ' . $this->nodeID);
		}

		$nodeList = $xpath->query('//@noticia_fecha');

		if ($nodeList->length > 0) {
			$newsDate = $nodeList->item(0)->nodeValue;

			if ((int) preg_match("/([0-9]{1,2})[-/]([0-9]{1,2})[-/]([0-9]{2,4})( ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))*/",$newsDate, $regs) == 0) {
				XMD_Log::error('Incorrect date format ' . $this->nodeID);
				$time = mktime();
			} else {
				$time = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[1], $regs[3]);
			}

		} else {
			XMD_Log::error('Date not found in newsDocument ' . $this->nodeID);
			return false;
		}


		$ximNewsNew = new XimNewsNew($this->nodeID);

		if (!($ximNewsNew->get('IdNew') > 0)) {
			XMD_Log::error('News document not found');
			return false;
		}

		$ximNewsNew->set('Titular', utf8_decode($titular));
		$ximNewsNew->set('Fecha', $time);
		
		if (!$ximNewsNew->update()) {
			XMD_Log::error('Updating in XimNewsNews table');
			return false;
		}
		
		return true;
	}

	/**
	*	Uptes the name of the news in XML and ximNEWS table
	*	@param  string name
	*	@return bool
	*/

	function RenameNode($name){

		parent::RenameNode($name);

		// Setting name in newsXML
		
		$content = $this->GetContent();

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		if (!$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8))) return false;

		$xpath = new DOMXPath($domDoc);
		$entries = $xpath->query('@name');

		foreach ($entries as $entry) {
			$entry->nodeValue = XmlBase::recodeSrc($name, XML::UTF8);	
		}

		$result = $domDoc->saveXML();
		$result = XmlBase::recodeSrc(str_replace('<?xml version="1.0"?>', '', $result), XML::UTF8);
		
		if (empty($result)) {
			XMD_Log::error('Updating news ' . $this->parent->get('IdNode'). ' name in XML');
			return false;
		}

		$this->SetContent($result, true);					

		// Updating name in database

		$ximNewsNew = new XimNewsNew($this->parent->get('IdNode'));
		$ximNewsNew->set('Name', $name);
		$ximNewsNew->update();

		return true;
	}

	/**
	*  Wrapper for DeleteNode.
	*  @return bool
	*/

	function delete() {
		return $this->DeleteNode();
	}

	/**
	*  Deletes the XimNewsNewLanguage and its dependencies.
	*  @return bool
	*/

	function DeleteNode() {

		parent::DeleteNode();

		$relNewsBulletins = new RelNewsBulletins();
		$relNewsBulletins->deleteByNew($this->parent->get('IdNode'));
	
		$relNewsColector = new RelNewsColector();
		$relNewsColector->deleteByNew($this->parent->get('IdNode'));
		
		$relNewsArea = new RelNewsArea();
		$relNewsArea->deleteByNew($this->parent->get('IdNode'));
	
		$ximNewsCache = new XimNewsCache();
		$ximNewsCache->deleteByNew($this->parent->get('IdNode'));

    	$ximNewsNew = new XimNewsNew($this->parent->get('IdNode'));
		$ximNewsNew->delete();
	}
	
	/**
	*  Returns a xml fragment with XimNewsBulletin data.
	*  @return string
	*/

    function getXmlTail() {

    	$returnValue = parent::getXmlTail();
    	$ximNewsNew = new XimNewsNew($this->nodeID);

    	if ($ximNewsNew->get('IdNew') > 0) {
    		$returnValue .= sprintf(' Fecha="%s" Name="%s" Titular="%s" TimeStamp="%s" IdSection="%s"',
    			$ximNewsNew->get('Fecha'), $ximNewsNew->get('Name'),
    			$ximNewsNew->get('Titular'), $ximNewsNew->get('TimeStamp'),
    			$ximNewsNew->get('IdSection'));
    	}
    	return $returnValue;
    }


    /**
     * Funci�n que actualiza el esquema de una noticia al del esquema 
     *
     * @return bool
     */

	function updateToSchema() {
		$structuredDocument = new StructuredDocument($this->nodeID);
		if (!$structuredDocument->get('IdDoc') > 0) {
			XMD_Log::error('No se ha podido cargar el structured Document asociado a la noticia ' . $this->nodeID . ' abortando adecuaci�n a schema');
			return false;
		}

		$idTemplate = $structuredDocument->get('IdTemplate');
		if (!($idTemplate > 0)) {
			XMD_Log::error("An error has occurred while loading the document {$this->nodeID} schema.");
			return false;
		}

		$visualTemplate = new Node($idTemplate);
		if (!($visualTemplate->get('IdNode') > 0)) {
			XMD_Log::error("La plantilla $idTemplate a la que est� asociada la noticia {$this->nodeID} ha sido borrada, por lo que la noticia no se puede adecuar al esquema");
			return false;
		}

		$defaultContents = $visualTemplate->class->getDefaultContent();
		if (!($defaultContents)) {
			return false;
		}


		$xmlHeader = '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
		$xmlDefaultContent = $xmlHeader . $defaultContents;

		$contents = $this->GetContent();
		if (empty($contents)) {
			$msg = "La noticia {$this->nodeID} no tiene contenido, se va a asociar el contenido por defecto";
			XMD_Log::notice($msg);
			$this->messages->add($msg, MSG_TYPE_NOTICE);
			$this->SetContent($defaultContents);
			return true;
		}

		$xmlContents = $xmlHeader . $contents;

		$error = false;

		$domDefault = domxml_open_mem($xmlDefaultContent);
		if (!$domDefault) {
			XMD_Log::error("El contenido por defecto del esquema $idTemplate no se ha podido cargar como xml");
			$error = true;
		}

		$domContent = domxml_open_mem($xmlContents);

		if (!$domContent) {
			XMD_Log::error("The content of the document {$this->nodeID} could not be loaded as XML.");
			$error = true;
		}

		if ($error) {
			return false;
		}

		$rootDefault = $domDefault->document_element();
		$rootContent = $domContent->document_element();

		if ($rootDefault->tagname() != $rootContent->tagname()) {
			$msg = "The schema $idTemplate doesn't match with this document.";
			XMD_Log::error($msg);
			$this->messages->add($msg, MSG_TYPE_ERROR);
			return false;
		}

		$domContent = $this->_updateLevel($domContent, $rootDefault, $rootContent);
		$newContent = $domContent->dump_mem();

		$newContent = str_replace($xmlHeader, '', $newContent);
		$this->SetContent($newContent);
	}

	function _updateLevel($domContent, $levelDefault, $levelContent) {
		$childrenDefault = $levelDefault->child_nodes();
		foreach ($childrenDefault as $children) {
			if (strtolower(get_class($children)) != 'domelement') {
				continue;
			}
			$childrenContent = $this->_inNode($children, $levelContent);
			if (!$childrenContent) {
				$nodeInfo = $this->_getNodeInfo($children, true);
				$this->_createNode($domContent, $levelContent, $nodeInfo);
			} else {
				$nodeInfo = $this->_getNodeInfo($children, false);
				$this->_updateNode($domContent, $children, $nodeInfo);
				$this->_updateLevel($domContent, $children, $childrenContent);
			}
		}
		return $domContent;
	}

	function _getNodeInfo($node, $recursive = true) {
		$nodeInfo = array();
		if (get_class($node) == 'domtext') {
			$content = trim($node->get_content());
			if (empty($content)) {
				return NULL;
			}
			return $content;
		}

		if (get_class($node) == 'domelement') {
			$nodeInfo['TAGNAME'] = $node->tagname();
			$nodeInfo['ATTRIBUTES'] = array();
			$attributes = $node->attributes();
			if (is_array($attributes)) {
				foreach ($attributes as $attribute) {
					$nodeInfo['ATTRIBUTES'][$attribute->name()] = $attribute->value();
				}
			}

			$childrens = $node->child_nodes();
			if (is_array($childrens) && count($childrens) > 0) {
				foreach ($childrens as $children) {
					if ($recursive) {
						$childrenInfo = $this->_getNodeInfo($children);
					}
					if (!empty($childrenInfo)) {
						if (is_array($childrenInfo)) {
							$nodeInfo['CHILDRENS'][]  = $childrenInfo;
							continue;
						}
						$nodeInfo['CONTENT'] = $childrenInfo;
					}
				}
			}

			return $nodeInfo;
		}
		return NULL;
	}

	function _inNode($node, $level) {
		$childrens = $level->child_nodes();
		foreach ($childrens as $children) {
			if (strtolower(get_class($children)) != 'domelement') {
				continue;
			}
			if ($children->tagname() == $node->tagname()) {
				return $children;
			}
		}
		return false;
	}

	function _updateNode($domDocument, $node, $nodeInfo) {
		if (!get_class($node) == 'domelement') {
			return ;
		}
		if ($node->tagname() != $nodeInfo['TAGNAME']) {
			return ;
		}
		$attributes = $node->attributes();
		if (is_array($attributes) && is_array($nodeInfo['ATTRIBUTES'])) {
			foreach ($attributes as $attribute) {
				$attributeName = $attribute->name();
				if (in_array($attributeName, $nodeInfo['ATTRIBUTES'])) {
		 			// no se pueden actualizar los atributos de los nodos por que pisar�amos valores antiguos
//					$attribute->set_value($nodeInfo['ATTRIBUTES'][$attributeName]);
					unset($nodeInfo);
				}
			}
		}
		if (is_array($nodeInfo['ATTRIBUTES'])) {
			foreach ($nodeInfo['ATTRIBUTES'] as $key => $value) {
				$node->set_attribute($key, $value);
			}
		}
	}

	function _createNode($domDocument, $parentNode, $nodeInfo) {
		$element = $domDocument->create_element($nodeInfo['TAGNAME']);
		if (is_array($nodeInfo['ATTRIBUTES'])) {
			foreach ($nodeInfo['ATTRIBUTES'] as $key => $value) {
				$attribute = $element->set_attribute($key, $value);
			}
		}

		if (!empty($nodeInfo['CONTENT'])) {
			$textNode = $domDocument->create_text_node($nodeInfo['CONTENT']);
			$element->append_child($textNode);
		}

		if (isset($nodeInfo['CHILDRENS']) && is_array($nodeInfo['CHILDRENS'])) {
			foreach ($nodeInfo['CHILDRENS'] as $children) {
				$this->_createNode($domDocument, $element, $children);
			}
		}

		$parentNode->append_child($element);

	}

	/**
	*	Set association news-colector.
	*	@param int idColector
	*	@param  string startDate
	*	@param  string endDate
	*	@param  string strVersion
	*	@return bool
	*/

	function addToColector($idColector, $startDate, $endDate = NULL, $strVersion) {

		$nodeColector = new Node($idColector);

		if (!($nodeColector->get('IdNode') > 0)) {
			XMD_Log::error("Unexisting colector $idColector");
			return false;
		}

		$newsID = $this->parent->get('IdNode');

		if (!$strVersion) {
			XMD_Log::info("Version not found");
			$this->messages->add("Falta version para la noticia $newsID", MSG_TYPE_ERROR);
			return false;
		}

		$news = new StructuredDocument($newsID);
		$idNewsLanguage = $news->get('IdLanguage');


		$colectorName = $nodeColector->get('Name');
		$colectorMasterLanguage = $nodeColector->class->getLangMaster();
		$colectorLanguages = $nodeColector->class->getLanguages();

		$newsNode = new Node($newsID);
		$newsName = $newsNode->get('Name');

		if ((!$colectorMasterLanguage && !in_array($idNewsLanguage, $colectorLanguages)) 
			|| ($colectorMasterLanguage && $idNewsLanguage != $colectorMasterLanguage)) {
		
			$this->messages->add("News $newsName language is not compatible with the colector $colectorName",
				MSG_TYPE_WARNING);
			XMD_Log::warning("News $newsID has an incompatible language with the colector $idColector");

			return false;
		}
		
		$ximNewsColector = new XimNewsColector($idColector);
		$filter = $ximNewsColector->get('Filter');

		$set = $this->calcSet($filter);
		list($version, $subVersion) = explode("-", $strVersion);

		$relNewsColector = new RelNewsColector();
		$relNewsColector->set('FechaIn', $startDate);
		$relNewsColector->set('FechaOut', $endDate);
		$relNewsColector->set('Version', $version);
		$relNewsColector->set('SubVersion', $subVersion);
		$relNewsColector->set('IdColector', $idColector);
		$relNewsColector->set('IdNew', $newsID);
		$relNewsColector->set('LangId', $idNewsLanguage);
		$relNewsColector->set('SetAsoc', $set);
	
		// set the state for otf, with this the automatic wont work with the otf news

		$otf = $nodeColector->getSimpleBooleanProperty('otf');
		$hybrid = $nodeColector->getSimpleBooleanProperty('hybridColector');

		$state =  ($otf && !$hybrid) ? 'otf' : 'pending';
		
		$relNewsColector->set('State', $state);

		// news previously associated to colector

		$idRel = RelNewsColector::hasNews($idColector, $newsID);

		if ($idRel > 0) {

			$relNewsColector2 = new RelNewsColector($idRel);
			$idCache = $relNewsColector2->get('IdCache');

			// subtract from ximnewscache

			if ($idCache > 0) {
				$ximNewsCache = new XimNewsCache($idCache);
				$counter = $ximNewsCache->get('Counter') - 1;
				$ximNewsCache->RestCounter($counter);
			}

			$relNewsColector->set('IdRel', $idRel);
			$result = $relNewsColector->update();
		} else {
			if($result = $relNewsColector->add()) { 
				$idRel = $result; 
			}
		}

		if (!$result) {
			XMD_Log::error("La noticia $newsName no se ha asociado al colector $idColector. informaci�n adicional " . 
				print_r($relNewsColector->messages->messages, true));
			$this->messages->mergeMessages($relNewsColector->messages);
			return false;
		} 

		$idUser = XSession::get('userID'); 
		$rel = new RelNewsColectorUsers(); 
		$rel->add($idRel, $idUser);
		
		$this->messages->add("La noticia $newsName se ha asociado correctamente al colector $colectorName", MSG_TYPE_WARNING);
		return true;
	}

	/**
	*	Calculate the set wich correspond to the news in its association to a colector.
	*	@param  string colectorFilter
	*	@return string
	*/

	function calcSet($colectorFilter) {

		$ximNewsNew = new XimNewsNew($this->parent->get('IdNode'));
		$newsTime = $ximNewsNew->get('Fecha');
		$newsDate = getdate($newsTime);

		switch ($colectorFilter) {

			case "fechaDia":
				$set = date("Y", $newsTime) . date("m", $newsTime) . date("d", $newsTime);
				break;

			case "fechaSemana":
				
				$monday = ($newsDate['wday'] == 0) ? $newsDate["mday"] - 6 : $newsDate["mday"] - $newsDate["wday"] + 1;

				$startWeek = mktime(0, 0, 0, $newsDate["mon"], $monday, $newsDate["year"]);
				$set = date("Y", $startWeek) . date("m", $startWeek) . date("d", $startWeek);
				break;

			case "fechaMes":
				$set = date("Y", $newsTime) . date("m", $newsTime) . "01";
				break;

			default:
				$set = $colectorFilter;
				break;
		}

		return $set;
	}

	/**
	*  Checks if the news have a relationship with the area
	*  @param int idArea
	*  @return bool
	*/

	public function hasArea($idArea) {

		return RelNewsArea::hasAreas($idArea, $this->parent->get('IdNode'));
	}

	/**
	*  Adds a relationship news-area
	*  @param int idArea
	*  @return bool
	*/

	public function addToArea($idArea) {

		if ($this->hasArea($idArea)) {
			return false;
		}

		$relNewsAreas = new RelNewsArea();
		$relNewsAreas->set('IdNew', $this->parent->get('IdNode'));
		$relNewsAreas->set('IdArea', $idArea);
		$result = $relNewsAreas->add();

		if (!($result > 0)) {
			XMD_Log::error("Adding relation with area $idArea");
			return false;
		}

		// modifying the xml of the news

		$content = $this->GetContent();
		
		$domDoc = new DOMDocument();
		$domDoc->xmlStandalone = true;
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;
		$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8));

		$areaNode = $domDoc->getElementsByTagName('area_tematica')->item(0);

		if (empty($areaNode)) {
			XMD_Log::info("The xml of news haven't any tag area");
			return true;
		}
		
		$ximNewsArea = new XimNewsAreas($idArea);

		$optionNode = $domDoc->createElement('opcion');
		$optionNode->nodeValue = $ximNewsArea->get('Name');
		$optionNode->setAttribute('value', $idArea);
		$areaNode->appendChild($optionNode);
		
		$xpath = new DOMXPath($domDoc);
		$items = $xpath->query('/*');
		$rootNode = $items->item(0);
		
		if (is_null($rootNode)) {
			XMD_Log::info("The area $idArea isn't included in XML");
			return false;
		}
		
		$resultContent = $domDoc->saveXML($rootNode);

		$this->SetContent($resultContent);

		return true;
	}

	/**
	*  Deletes a relationship news-area
	*  @param int idArea
	*  @return bool
	*/

	public function deleteFromArea($idArea) {

		if (!$this->hasArea($idArea)) {
			return false;
		}

		$ximNewsAreas = new XimNewsAreas($idArea);
		
		if (!$ximNewsAreas->DeleteRelNewsArea($idArea, $this->parent->get('IdNode'))) {
			XMD_Log::error("Deleting the relation with area $idArea");
		}

		// modifying the xml

		$content = $this->GetContent();

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;
		$domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8));

		$xpath = new DOMXPath($domDoc);
		$option = $xpath->query("//area_tematica/opcion[@value='$idArea']")->item(0);

		if (is_null($option)) {
			XMD_Log::info("The area $idArea isn't included in XML");
			return true;
		}

		$domDoc->getElementsByTagName('area_tematica')->item(0)->removeChild($option);
		$items = $xpath->query('/*');
		$rootNode = $items->item(0);
				
		if (is_null($rootNode)) {
			XMD_Log::info("The area $idArea isn't remove in XML");
			return false;
		}
		
		$resultContent = $domDoc->saveXML($rootNode);
		$this->SetContent($resultContent);

		return true;
	}
}
?>
