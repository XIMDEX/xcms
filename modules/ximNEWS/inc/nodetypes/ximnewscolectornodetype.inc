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


ModulesManager::file('/inc/nodetypes/foldernode.inc');
ModulesManager::file('/inc/model/XimNewsColector.php', 'ximNEWS');
ModulesManager::file('/inc/model/RelNewsColectorUsers.php', 'ximNEWS');
ModulesManager::file('/inc/ximNEWS_Adapter.php', 'ximNEWS');
ModulesManager::file('/inc/xml/XSLT.class.php');
ModulesManager::file('/inc/log/Automatic_log.class.php');

/**
*   @brief Handles Collectors.
*
*   A Collector is an entity that collects news according a specific criteria and generates Bulletins.
*/

class XimNewsColectorNodeType extends FolderNode  {

	/*
	*  Creates the xslt file of the XimNewsColector and adds the row to XimNewsColector table.
	*  @param string name
	*  @param int parentID
	*  @param int nodeTypeID
	*  @param int stateID
	*  @param string filter
	*  @param int idTemplate
	*  @param int idSection
	*  @param int idXimlet
	*  @param int orderNewsInBulletins
	*  @param int newsPerBulletin
	*  @param int timeToGenerate
	*  @param int newsToGenerate
	*  @param int lastGeneration
	*  @param int mailChannel
	*  @param int pvdVersion
	*  @param int inactive
	*  @param int idArea
	*  @param int global
	*  @param int locked
	*  @return bool
	*/

	function CreateNode($name, $parentID, $nodeTypeID, $stateID, $filter, $idTemplate, $idSection, $idXimlet,
		$orderNewsInBulletins, $newsPerBulletin, $timeToGenerate, $newsToGenerate, $lastGeneration, $mailChannel,
		$pvdVersion, $inactive, $idArea, $global, $locked = 0) {

		//Creo el archivo xsl necesario para crear boletines
		$ximNewsColector = new XimNewsColector();
		$fileXsl = $ximNewsColector->GetXslFile($idTemplate);

		if(!$fileXsl){
		    $fileXsl = $this->buildXslBulletin($idTemplate);
		    if(is_null($fileXsl)){
				XMD_Log::info("Creando el xsl de ".$this->nodeID."");
				$this->parent->messages->add(_('Se ha encontrado un error al parsear el xml del documento'), MSG_TYPE_ERROR);
				return false;
		    }
		}

		$ximNewsColector->set('IdColector', $this->nodeID);
		$ximNewsColector->set('Name', $name);
		$ximNewsColector->set('Filter', $filter);
		$ximNewsColector->set('IdTemplate', $idTemplate);
		$ximNewsColector->set('IdSection', $idSection);
		$ximNewsColector->set('IdXimlet', $idXimlet);
		$ximNewsColector->set('OrderNewsInBulletins', $orderNewsInBulletins);
		$ximNewsColector->set('NewsPerBulletin', $newsPerBulletin);
		$ximNewsColector->set('TimeToGenerate', $timeToGenerate);
		$ximNewsColector->set('NewsToGenerate', $newsToGenerate);
		$ximNewsColector->set('LastGeneration', time());
		$ximNewsColector->set('MailChannel', $mailChannel);
		$ximNewsColector->set('TemplateVersion', $pvdVersion);
		$ximNewsColector->set('Inactive', $inactive);
		$ximNewsColector->set('IdArea', $idArea);
		$ximNewsColector->set('Locked', $locked);
		$ximNewsColector->set('Global', $global);
		$ximNewsColector->set('XslFile', $fileXsl);

		$idXimNewsColector = $ximNewsColector->add();

		if (!($idXimNewsColector > 0)) {
		    XMD_Log::error("Al persistir en XimNewsColector ".$this->nodeID."");
		    $node = new Node($this->nodeID);
		    $node->delete();

			return false;

		    if(!$node->numErr){
				XMD_Log::info("Colectores error al insertar colector se ha eliminado el nodo ".$this->nodeID."");
		    }
		}

        $this->updatePath();
		return false;
	}

	/**
	*  Changes the XimNewscolector's name.
	*  @param string name
	*  @return unknown
	*/

	function RenameNode($name) {

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$ximNewsColector->set('Name', $name);
		$ximNewsColector->update();

		$idXimlet = $ximNewsColector->get('IdXimlet');

		$ximletNode = new Node($idXimlet);
		$ximletNode->RenameNode($name);

		$ximNewsBulletin = new XimNewsBulletin();
		$containers = $ximNewsBulletin->getBulletinContainers($this->parent->get('IdNode'));

		for($i = 0; $i < count($containers); $i++) {
			$node = new Node($containers[$i]);
			$oldName = $node->get('Name');

			$newName = str_replace($currentName, $name, $oldName);
			$node->RenameNode($newName);
		}

        $this->updatePath();
	}

	/**
	*  Wrapper for DeleteNode.
	*  @return bool
	*/

	function delete() {
		$this->DeleteNode();
	}

	/**
	*  Deletes the XimNewsColector and its dependencies.
	*  @return bool
	*/

	function DeleteNode() {
		$relNewsColector = new RelNewsColector();
		$relNewsColector->setColectorRemoved($this->parent->get('IdNode'));
		Automatic_Log::info("Colector[{$this->parent->get('IdNode')}] change to 'removed' in relNewsColectors.");

		$relColectorList = new RelColectorList();
		$relColectorList->deleteByColector($this->parent->get('IdNode'));
		Automatic_Log::info("Colector[{$this->parent->get('IdNode')}] deleted in RelColectorList");
		$this->purgeColector();

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$ximNewsColector->delete();
		Automatic_Log::info("Colector[{$this->parent->get('IdNode')}] deleted in XimNewsColector");
	}

	/**
	*  Returns an Xml with information about the XimNewsColector.
	*  @param int depth
	*  @param array files
	*  @param int recurrence
	*/

	function ToXml($depth, & $files, $recurrence) {

		$xml = '';

		$query = sprintf("SELECT * FROM `RelNewsColector`"
					. " WHERE IdColector = %s",
					$this->dbObj->sqlEscapeString($this->parent->nodeID));
		$this->dbObj->Query($query);
		$prefix = str_repeat("\t", $depth + 1);
		while (!$this->dbObj->EOF) {
			$elements = array();
			foreach ($this->dbObj->row as $key => $value) {
				if (!is_int($key)) {
					$elements[] = sprintf('%s="%s"', $key, $value);
				}
			}
			$xml .= sprintf("%s<RelNewsColector %s/>\n", $prefix, implode(' ', $elements));
			$this->dbObj->next();
		}

		return $xml;


	}

	/**
	*  Returns a xml fragment with XimNewsBulletin data.
	*  @return string
	*/

    function getXmlTail() {

    	$returnValue = parent::getXmlTail();
    	$ximNewsColector = new XimNewsColector($this->nodeID);

    	if ($ximNewsColector->get('IdColector') > 0) {
    		$returnValue .= sprintf(' Name="%s" Filter="%s" IdTemplate="%s" IdSection="%s" IdXimlet="%s"'
    			. ' OrderNewsInBulletins="%s" NewsPerBulletin="%s" TimeToGenerate="%s" NewsToGenerate="%s"'
    			. ' LastGeneration="%s" MailChannel="%s" PvdVersion="%s" Inactive="%s" IdArea="%s" Global="%s" ',
    			$ximNewsColector->get('Name'), $ximNewsColector->get('Filter'),
    			$ximNewsColector->get('IdTemplate'), $ximNewsColector->get('IdSection'),
    			$ximNewsColector->get('IdXimlet'), $ximNewsColector->get('OrderNewsInBulletins'),
    			$ximNewsColector->get('NewsPerBulletin'), $ximNewsColector->get('TimeToGenerate'),
    			$ximNewsColector->get('NewsToGenerate'), $ximNewsColector->get('LastGeneration'),
    			$ximNewsColector->get('MailChannel'),
    			$ximNewsColector->get('TemplateVersion'),
    			$ximNewsColector->get('Inactive'),
    			$ximNewsColector->get('IdArea'),
    			$ximNewsColector->get('Global'));
    	}
    	return $returnValue;
    }

	/**
	*  Gets the channels associated to XimNewsColector.
	*  @return array
	*/

	function getChannels() {

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$idXimlet = $ximNewsColector->get('IdXimlet');

		$nodeXimlet = new Node($idXimlet);
		$childrensXimlet = $nodeXimlet->GetChildren();

		$childrenNode = new Node($childrensXimlet[0]);
		if ($childrenNode->get('IdNode') > 0) {
			return $childrenNode->class->GetChannels();
		} else {
			return array();
		}
	}

	/**
	*  Gets the languages associated to XimNewsColector.
	*  @return array
	*/

	function getLanguages()	{

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$idXimlet = $ximNewsColector->get('IdXimlet');

		$node = new Node($idXimlet);
		if ($node->get('IdNode') > 0)  {
			return $node->class->GetLanguages();
		} else {
			return array();
		}

	}

	/**
	*  Gets the master language associated to XimNewsColector.
	*  @return int|null
	*/

	function getLangMaster() {

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$idXimlet = $ximNewsColector->get('IdXimlet');

		$node = new Node($idXimlet);
		$childrens = $node->GetChildren();

		foreach($childrens as $idChild){
			$doc = new StructuredDocument($idChild);
			$idSymlink = $doc->get('TargetLink');

			if (!is_null($idSymlink)) {
				$symlink = new StructuredDocument($idSymlink);
				return $symlink->get('IdLanguage');
			}
		}

		return NULL;
	}

	/**
	*  Calls to methods for publicate and expire the XimNewsNewLanguages associated to the XimNewsNewColector.
	*  @return unknown
	*/
	function updateRelNewsColector() {
		$generationTime = time();
		$relNewsColector = new RelNewsColector();
		$outdatedNews = $relNewsColector->getOutdatedNews($generationTime);
		$publishNews = $relNewsColector->setPublishNews($generationTime);

		if(!$outdatedNews && !$publishNews) {
			Automatic_Log::info("Without changes in relNewsColectors");
		}else {
			Automatic_Log::info("Update {$outdatedNews} states to removed from relNewsColectors");
			Automatic_Log::info("Update {$publishNews} pending to publishable from relNewsColectors");
		}
	}

	/**
	*	Checks if the XimNewsColector must be generated.
	*	@param total bool
	*	@return bool
	*	Campo inactive:
	*		- 0: se comprueban numero de noticias y tiempo de generacion para ver si el colector es generable
	*		- 1: sólo se comprueba el número de noticias pendientes para la generacion del colector (condicion de tiempo de generacion del colector inactiva)
	*		- 2: sólo se comprueba el tiempo de generacion (condicion de numero de noticias inactiva)
	*		- 3: no se compureba ninguna, el colector esta inactivo y no se genera nunca
	*
	*/

	function isGenerable($total=null){

		//Update states of relNewsColectors
		$this->updateRelNewsColector();

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$forceTotalGeneration = $ximNewsColector->get('ForceTotalGeneration');
		$inactive = $ximNewsColector->get('Inactive');

		if ($forceTotalGeneration > 0 || isset($total)){
			Automatic_Log::info("Generacion total del colector ".$this->parent->get('IdNode'));
			return true;
		}

		$relNewsColector = new RelNewsColector();
		$newsPending = count($relNewsColector->getPublishNews($this->parent->get('IdNode')));

		if ($newsPending == 0) {
				Automatic_Log::info("Colector ".$this->parent->get('IdNode')." sin nuevas noticias");
			return false;
		}else {
				Automatic_Log::info("Colector ".$this->parent->get('IdNode')." con {$newsPending} nuevas noticias");
		}

		if ($inactive == 1 || $inactive == 0) {
			$newsToGenerate = $ximNewsColector->get('NewsToGenerate');

			if ($newsPending >= $newsToGenerate){
				Automatic_Log::info("Colector ".$this->parent->get('IdNode')." generable por numero de noticias: $newsPending >= $newsToGenerate");
				return true;
			} else {
				Automatic_Log::info("Colector " . $this->parent->get('IdNode') . "no es generable por numero de noticias: $newsPending < $newsToGenerate");
				if($inactive != 0) return false;
			}
		}

		if($inactive == 2 || $inactive == 0){
			$timeToGenerate = $ximNewsColector->get('TimeToGenerate');
			$lastGeneration = $ximNewsColector->get('LastGeneration');
			$leftTime = $lastGeneration + $timeToGenerate - time();

			if($leftTime < 0){
				Automatic_Log::info("El colector ".$this->parent->get('IdNode')." es generable por tiempo: $leftTime < 0");
				return true;
			}else {
				Automatic_Log::info("El colector ".$this->parent->get('IdNode')." no es generable por tiempo: $leftTime > 0");
				return false;
			}
		}

		XMD_Log::warning('El colector ' . $this->parent->get('IdNode') . ' no es generable, no se ha especificado el motivo');
		return false;
	}

	/**
	*	Starts the generation of the XimNewsBulletins.
	*	@param int total (for a whole generation must be 1)
	*	@param array idNewsColectorUsers
	*	@return array
	*/

	function generateColector($total = NULL, $idNewsColectorUsers = null) {

		if (!$this->isGenerable($total)) {
			$this->messages->add('El colector no es generable', MSG_TYPE_WARNING);
			return array();
		}

		$isOTF = $this->parent->getSimpleBooleanProperty('otf');

		//if the colector is otf, dont generate it

		if ($isOTF){
			$this->messages->add('El colector esta configurado como OTF, es generado en cliente', MSG_TYPE_ERROR);
			return false;
		}

		$sinFuelle = false;

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$ximNewsColector->Lock();
		Automatic_Log::info("Colector[{$this->parent->get('IdNode')}] locked");

		$templateID = $ximNewsColector->get('IdTemplate');

		// todo: move this to method isgenerable
		$templateNode = new Node($templateID);
		if (!($templateNode->get('IdNode') > 0)) {
			XMD_Log::error('Colector pvd is deleted');
			return false;
		}

		$templateIdVersionInColector = $ximNewsColector->get('TemplateVersion');
		$forceTotalGeneration = $ximNewsColector->get('ForceTotalGeneration');
		$filter = $ximNewsColector->get('Filter');
		$order = $ximNewsColector->get('OrderNewsInBulletins');
		$newsPerBulletin = $ximNewsColector->get('NewsPerBulletin');

		// Updating xslFile if pvd version has changed

		$df = new DataFactory($templateID);
		$ver = $df->GetLastVersion();
		$subver = $df->GetLastSubVersion($ver);
		$templateIdVersion = $df->getVersionId($ver,$subver);

		if ($templateIdVersionInColector != $templateIdVersion) {

			$newXslFile = $this->buildXslBulletin($templateID);

			if(!$newXslFile) {
				XMD_Log::info("Actualizando xsl de la pvd $templateID");
				$this->messages->add('No se ha podido actualizar la plantilla xsl', MSG_TYPE_ERROR);
				return false;
			}

			$ximNewsColector = new XimNewsColector();

			if(!$ximNewsColector->updateByIdTemplate($templateID, $newXslFile, $templateIdVersion)){
				XMD_Log::info("Persistiendo xsl de pvd $templateID");
				return false;
			}

			$xslFile = $ximNewsColector->get('XslFile');
			$cache = new XimNewsCache();
			if(!$cache->GenerateAllCaches($templateID,$xslFile)){
				XMD_Log::info("Generado las caches de la pvd $templateID");
			}
		}

		// Setting partial or total generation
		$relNewsColector = new RelNewsColector();

		if ($forceTotalGeneration > 0 || isset($total)){
			XMD_Log::info("Starting total generation ".$this->parent->get('IdNode'));

			// Generacion total sin fuelle

			if ($total == 2) {
				XMD_log::info("Generacion total sin fuelle del colector ".$this->parent->get('IdNode'). " - ".mktime()."");
				$sinFuelle = true;
			}

			$total = true;
			$news = $relNewsColector->getNews($this->parent->get('IdNode'));

			// Updating set in relnewscolector if colector's filter has changed

			if ($forceTotalGeneration == 2) {

				foreach ($news as $newsID) {

					$nodeNews = new Node($newsID);
					if ($nodeNews->get('id') > 0) {
						$setAsoc = $nodeNews->class->calcSet($filter);

						$idRel = $relNewsColector->hasNews($this->parent->get('IdNode'), $newsID);

						if ($idRel > 0) {
							$relN = new RelNewsColector($idRel);
							$relN->set('SetAsoc', $setAsoc);
							$relN->update();
						}
					}
				}
			}

		} else {
			XMD_Log::info("Starting partial generation ".$this->parent->get('IdNode'));

			$news = $relNewsColector->getPublishNews($this->parent->get('IdNode'));
		}

		if(!$news){
			$this->messages->add("No existen noticias pendientes de publicar",MSG_TYPE_ERROR);
			XMD_Log::info("No existen noticias en colector ".$this->parent->get('IdNode'));

			$ximNewsColector->UnLock();
			Automatic_Log::info("Colector[{$this->parent->get('IdNode')}] unlocked");

			if ($forceTotalGeneration > 0) {
				$ximNewsColector->set('ForceTotalGeneration', 0);
				$ximNewsColector->update();
			}

			return array();
		}


		//Si es una generación forzada se borran todos los boletines que tuviera el colector
		if ($forceTotalGeneration == 2) {
			$ximNewsBulletin = new XimNewsBulletin();
			$bulletinContainers = array();
			$bulletinContainers = $ximNewsBulletin->getBulletinContainers($this->parent->get('IdNode'));

			if (count($bulletinContainers) > 0) {
				foreach ($bulletinContainers as $containerID) {
					$bulletinContainer = new Node($containerID);
					$bulletinContainer->delete();
				}
			}
		}


		$colectorSets = $relNewsColector->getSetsFromColector($this->parent->get('IdNode'));

		$master = $this->getLangMaster();

		if ($master) {
			$languages[] = $master;
		} else {
			$languages = $this->getLanguages();
		}

		$bulletinsGenerated = array();


		// Paginate for each set and language
		if(!is_null($idNewsColectorUsers))
			$cu = new XimNewsColectorusers($idNewsColectorUsers);

		foreach ($colectorSets as $set) {

			$numLanguages = count($languages);
			$iLanguage = 1;
			foreach ($languages as $langID) {
				$relNewsColector = new RelNewsColector();

				// Sorting news in set

				$relNewsColector->sortNewsInSetByDate($this->parent->get('IdNode'), $set, $newsPerBulletin, $order,
					$langID, $sinFuelle);

				// Only gets pages as changed

				$pagesChanged = $relNewsColector->getPages($this->parent->get('IdNode'), $set, $langID, $newsPerBulletin,
					$total);

				if ($pagesChanged) {
					if(!is_null($idNewsColectorUsers)) {
						$cu->set('Progress', floor(($iLanguage / $numLanguages) * 25));
						$cu->update();
					}
					$array_news = $relNewsColector->getNewsFromPages($this->parent->get('IdNode'), $set, $pagesChanged,
						$langID);
					$newsInBulletin = sizeof($array_news);

					$bulletins = $this->paginateSet($set, $array_news, $pagesChanged, $langID, $filter,
						$templateID, $master);

					if (!$bulletins) {
						continue;
					}

					$bulletinsGenerated = array_merge($bulletinsGenerated, (array) $bulletins);
				}

				if(!is_null($idNewsColectorUsers)) {
					$cu->set('Progress', floor(($iLanguage / $numLanguages) * 50));
					$cu->update();
				}
				$iLanguage ++;
			}
		}

		$this->purgeColector();

		$ximNewsColector->UnLock(); //unlocked colector
		if (!(sizeof($bulletinsGenerated) > 0)) {
			XMD_Log::info('No se ha generado ningún boletín');
			return NULL;
		}

		// Updating colector data

		if ($forceTotalGeneration > 0) {
			$ximNewsColector->set('ForceTotalGeneration', 0);
		}

		$ximNewsColector->set('LastGeneration', mktime());
		$ximNewsColector->set('State', 'generated');
		$ximNewsColector->update();

		$query = "SELECT source,target FROM RelBulletinXimlet WHERE source in (" . implode(', ', $bulletinsGenerated) . ")";
		$db = new DB();
		$db->query($query);
		$idBul = $db->getValue('source');
		$bulletinDoc = new StructuredDocument($idBul);
		$content = $bulletinDoc->GetContent();
		// Updates ximlet content
		$idXimlet = $db->getValue("target");
		$ximletNode = new Node($idXimlet);
		$ximletNode->SetContent($content);

		return $bulletinsGenerated;
	}

	/**
	 * Builds the XimNewsBulletins.
	 *
	 * @param int set
	 * @param array array_news
	 * @param int newsPerBulletin
	 * @param array array_pages
	 * @param int langID
	 * @param string $filter
	 * @param int templateID
	 * @param array channelLst
	 * @param int master
	 * @return array de identificadores de contenedores de boletines creados
	 */

	private function paginateSet($set, $arrayNews, $array_pages, $langID, $filter, $templateID,	$master = null) {

		$ximNewsBulletin = new XimNewsBulletin();
		$containers = $ximNewsBulletin->getContainersBySet($this->parent->get('IdNode'), $set);
		$numContainers = count($containers);
		$bulletins = $ximNewsBulletin->getBulletinsByLang($this->parent->get('IdNode'), $set, $langID);
		$maxBulletins = count($bulletins);

		$relNewsColector = new RelNewsColector();
		$maxPages = $relNewsColector->GetMaxPage($this->parent->get('IdNode'), $set, $langID);

		settype($array_pages,'array');
		$numPages = sizeof($array_pages);
		$nNews = count($arrayNews);

		XMD_Log::info("colector ".$this->parent->get('IdNode')." maxBulls $maxBulletins maxPage $maxPages numConts
			$numContainers lang $langID numPages $numPages numNews $nNews");

		if(!($nNews > 0)) {

			//Se borran boletines vacíos

			foreach($array_pages as $page){
				XMD_Log::info("Boletin ".$bulletins[$page-1]);

				$node = new Node($bulletins[$page-1]);
				$node->DeleteNode();
				if($node->numErr){
					$this->messages->add("Error borrando boletin ".$bulletins[$page-1],MSG_TYPE_ERROR);
					XMD_Log::info("Borrando boletin ".$bulletins[$page-1]);
					return false;
				}
			}
			return NULL;
		}

		$ximNewsColector = new XimNewsColector($this->parent->get('IdNode'));
		$idXimlet = $ximNewsColector->get('IdXimlet');

		$nodeXimlet = new Node($idXimlet);

		$alias = $nodeXimlet->GetAliasForLang($langID);

		// bulletin header data

		$headerData["tipo"] = $filter;
		$headerData["titulo"] = $alias;
		$idLote = NULL;

		// check if additional bulletins must be created

		$languages = $master > 0 ? $this->getLanguages() : array($langID);

		foreach($array_pages as $page) {

			if($page > $maxBulletins) {

				if($page > $numContainers) {

					XMD_Log::info("Creando container lang $langID set $set");

					ximNEWS_Adapter::createBulletins($this->parent->get('IdNode'), $languages, $headerData, $set, $page,
						$idLote, $master);

				} else {

					// if bulletin container exists only create the bulletindocument

					$idSection = $ximNewsColector->get('IdSection');
					$lang = new Language($langID);
					$containerNode = new Node($containers[$page - 1]);
					$bulletinName = $containerNode->get('Name') . '-id' . $lang->get('IsoName');
					$date = mktime();


					ximNEWS_Adapter::createBulletinLanguage($containers[$page -1], $idSection, $templateID, $bulletinName,
						$langID, $alias, $headerData, $this->parent->get('IdNode'), $set, $date, $idLote);
				}
			}
		}

		// comprobamos si hay que borrar boletines

		$numBulletinsForDelete = $maxBulletins - $maxPages;

		if($numBulletinsForDelete > 0){
			for($i=0;$i<$numBulletinsForDelete;$i++){
				$j = $maxBulletins - $i - 1;
					XMD_Log::info("Borrando boletin ".$bulletins[$j]);

				$node = new Node($bulletins[$j]);
				$bullName = $node->getNodeName();
				$node->DeleteNode();

				if($node->numErr){
					$this->messages->add("Borrando boletin $bullName",MSG_TYPE_ERROR);
					XMD_Log::info("Borrando boletin");
					return false;
				}
			}
		}

		// recalculamos los boletines, agregamos las noticias y paginamos

		$bulletinsReturn = array();
		$bulletins = $ximNewsBulletin->getBulletinsByLang($this->parent->get('IdNode'), $set, $langID);
		$totalBulletins = count($bulletins);
		$relNewsColector = new RelNewsColector();
		$totalNews = $relNewsColector->countNews($this->parent->get('IdNode'), $langID);

		foreach($array_pages as $page){

			$numNewsInBulletin = sizeof($relNewsColector->getNewsFromPages($this->parent->get('IdNode'), $set, $page,
				$langID));
			$newsInBulletin = array_splice($arrayNews, 0, $numNewsInBulletin);
			$i = $page - 1;

			if(empty($bulletins[$i])){
				XMD_Log::info('Nos saltamos el boletin ' . $bulletins[$i]);
				continue;
			}

			$bulletinId = $bulletins[$i];

			// Checks bulletin-ximlet dependency

			$bulletinNode = new Node($bulletinId);

			if ($bulletinNode->class->isBulletinForXimlet($this->parent->get('IdNode'))) {

				$ximletChilds = $nodeXimlet->getChildren();

				// Searchs the ximlet

				foreach ($ximletChilds as $ximletChildId) {

					$ximletDoc = new StructuredDocument($ximletChildId);

					if ($ximletDoc->get('IdLanguage') == $langID) {

						// Deletes old dependency (if exists)

						$depsMngr = new DepsManager();

						if (!is_null($depsMngr->getByTarget(DepsManager::BULLETIN_XIMLET, $ximletChildId))) {
							$depsMngr->deleteByTarget(DepsManager::BULLETIN_XIMLET, $ximletChildId);
							$depsMngr->deleteBySource(DepsManager::BULLETIN_XIMLET, $bulletinId);
						}

						// Set the new dependency

						$depsMngr->set(DepsManager::BULLETIN_XIMLET, $bulletinId, $ximletChildId);

						break;
					}
				}
			}

			XMD_Log::info("Llenando boletines $bulletinId");

			$prevBulletin = isset($bulletins[$i - 1]) ? $bulletins[$i - 1] : NULL;
			$nextBulletin = isset($bulletins[$i + 1]) ? $bulletins[$i + 1] : NULL;

			if (!$bulletinNode->class->createXml($this->parent->get('IdNode'), $newsInBulletin, $headerData, $totalBulletins,
				$totalNews,	$numNewsInBulletin, $prevBulletin, $nextBulletin)) {

				$this->messages->add("Error procesando XML del boletin $bulletinId",MSG_TYPE_ERROR);
				XMD_Log::error("Creando XML de boletin $bulletinId");
			}

			$ximNewsBulletin = new XimNewsBulletin($bulletinId);
			$ximNewsBulletin->set('State', 'generated');
			$ximNewsBulletin->update();

			$bulletinsReturn[] = $bulletinId;

			$relNewsColector->updatePosInSet($this->parent->get('IdNode'), $newsInBulletin);
		}

		return $bulletinsReturn;
	}


	/**
	 * Builds the collector xslt template and saves it in the filesystem
	 * Returns the file's name
	 * @param  int templateID
	 * @return string|null
	*/

	private function buildXslBulletin($templateID){

		if (is_null($templateID)) {
			XMD_Log::error('Pvd not found');
			return NULL;
		}

		$filePath = Config::GetValue("AppRoot") . Config::GetValue("FileRoot");
		$xslPath = Config::GetValue("AppRoot") .ModulesManager::path('ximNEWS'). "/inc/xsl/";

		$rngParser = new ParsingRng();
		$bulletinXml = $rngParser->buildDefaultContent($templateID);

		// Removes bulletin header

		$doc = new DomDocument;
		$doc->validateOnParse = true;
		$doc->LoadXML($bulletinXml);

		$xpath = new DOMXPath($doc);
		$nodeList = $xpath->query('//*[@id = "header"]');

		if ($nodeList->length > 0) {
			$root = $doc->documentElement;
			$root->removeChild($nodeList->item(0));
			$bulletinXml = $doc->saveXML();
		}

		$tmpPath = XIMDEX_ROOT_PATH . Config::GetValue('TempRoot') . '/tmpBulletinXml.xml';
		FSUtils::file_put_contents($tmpPath, $bulletinXml);

    	$fileName = FsUtils::getUniqueFile(Config::GetValue("AppRoot") . Config::GetValue("FileRoot"));
    	$targetPath = Config::GetValue("AppRoot") . Config::GetValue("FileRoot") . '/' . $fileName;

		// Construimos el xsl del boletin

		$xsltHandler = new XSLT();
		$xsltHandler->setXML($tmpPath);
		$xsltHandler->setXSL( XIMDEX_ROOT_PATH.ModulesManager::path('ximNEWS')."/xsl/generateBulletinXsl.xsl");

		$bulletinXsl = $xsltHandler->process();

		if (!$bulletinXsl) {
			XMD_Log::error("Generating bulletin xsl template for pvd $templateID");
			return NULL;
		}

		$xsltHandler->__destruct();

		if (FsUtils::file_put_contents($targetPath, $bulletinXsl)) {
			return $fileName;
		}

		return NULL;
	}

	/**
	*  Removes the dependencies between the XimNewsColector and the XimNewsNewLanguages that have expired.
	*  @return unknown
	*/

	private function purgeColector() {

	    $newsRemoved = RelNewsColector::getRemoved($this->parent->get('IdNode'));

		 Automatic_Log::info("Colector[{$this->parent->get('IdNode')}] purging despendences");

	    foreach($newsRemoved as $news){

		    if (!($news['cache'] > 0)) {
		    	Automatic_Log::info("Colector[{$this->parent->get('IdNode')}], News[{$news['new']}] whithout cache,we continue.");
		    	continue;
		    }

			$cache = new XimNewsCache($news['cache']);
			$counter = $cache->get('Counter') - 1;
			$cache->RestCounter($counter);
			Automatic_Log::info("Colector[{$this->parent->get('IdNode')}], News[{$news['new']}] cache -1 = {$counter}");

			// delete news-bulletin relation
			$idBulletinWithNew = XimNewsBulletin::getBulletinWithNew($this->parent->get('IdNode'), $news['new']);

			if ($idBulletinWithNew){
				$relNewsBulletin = new RelNewsBulletins();
				$relNewsBulletin->deleteRelation($news['new'], $idBulletinWithNew);
				Automatic_Log::info("Colector[{$this->parent->get('IdNode')}], News[{$news['new']}] removed from bulletin[{$idBulletinWithNew}] ");
			}
	    }

	    RelNewsColector::purgeColector($this->parent->get('IdNode'));
	  	 Automatic_Log::info("Colector[{$this->parent->get('IdNode')}] 'removed' news with Colector purged");
	}

	/**
	 *  Return a list for the idnews from this colector.
	 *  @param int recurrence
	 *  @return array
	 */

	public function getPublishabledChilds($recurrence = 0){
		$idColector = $this->parent->get('IdNode');
		$rel = new RelNewsColector($idColector);
		$news = $rel->getNewsFromColector($idColector);
		return $news;
	}
}

?>
