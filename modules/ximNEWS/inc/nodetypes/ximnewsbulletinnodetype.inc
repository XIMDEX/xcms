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
ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
ModulesManager::file('/inc/Automatic.class.php', 'ximNEWS');
ModulesManager::file('/inc/mail/MailRenderer.class.php');

/**
*   @brief Handles Bulletins of News.
*
*   A Bulletin is a set of News grouped according a specific criteria.
*/

class XimNewsBulletinNodeType extends AbstractStructuredDocument  {

	/**
	*  Creates the structuredDocument and adds the row to the XimNewsBulletin table.
	*
	*  @param string name
	*  @param int idParent
	*  @param int nodeTypeID
	*  @param int stateID
	*  @param int templateID
	*  @param int idLanguage
	*  @param int aliasName
	*  @param array channelList
	*  @param int idColector
	*  @param int date
	*  @param string set
	*  @param int idLote
	*  @return bool
	*/

	function CreateNode($name, $idParent, $nodeTypeID, $stateID, $templateID, $idLanguage, $aliasName, $channelList,
		$idColector, $date, $set, $idLote = NULL) {

		parent::CreateNode($name, $idParent, $nodeTypeID, $stateID, $templateID, $idLanguage, $aliasName, $channelList);

		$idNode = $this->parent->get('IdNode');

		$ximNewsBulletins = new XimNewsBulletin();
		$ximNewsBulletins->set('IdBulletin', $idNode);
		$ximNewsBulletins->set('IdContainer', $idParent);
		$ximNewsBulletins->set('IdColector', $idColector);
		$ximNewsBulletins->set('IdLote', $idLote);
		$ximNewsBulletins->set('Fecha', $date);
		$ximNewsBulletins->set('SetAsoc', $set);
		$result = $ximNewsBulletins->add();

		return $result;
	}

	/**
	*  Returns a xml fragment with XimNewsBulletin data.
	*  @return string
	*/

    function getXmlTail() {
    	$returnValue = parent::getXmlTail();
    	$ximNewsBulletin = new XimNewsBulletin($this->nodeID);

    	if ($ximNewsBulletin->get('IdBulletin') > 0) {
    		$returnValue .= sprintf(' IdContainer="%s" IdColector="%s" IdLote="%s" Fecha="%s" SetAsoc="%s"',
    			$ximNewsBulletin->get('IdContainer'), $ximNewsBulletin->get('IdColector'),
    			$ximNewsBulletin->get('IdLote'), $ximNewsBulletin->get('Fecha'),
    			$ximNewsBulletin->get('SetAsoc') );
    	}
    	return $returnValue;
    }

	/**
	*  Gets the nodes that must be published together with the XimNewsBulletin.
	*  @param bool recurrence
	*  @return array
	*/

    function getPublishabledChilds($recurrence = true) {
    	$node = new Node($this->parent->get('IdNode'));
    	//Section retrieved from SynchroFacade
		$ximNewsBulletin = new XimNewsBulletin($this->parent->get('IdNode'));
		$colectorID = $ximNewsBulletin->get('IdColector');
		$docsToPublish = array();
		$docsToPublish = $node->class->getNewsToPublish($colectorID);
		$docsToPublish[] = $idNode;

//		foreach ($docsToPublish as $docID) {
//			XMD_log::info("Start doc publication $docID");

//			$syncMngr = new SyncManager();
//			$syncMngr->setFlag('type', 'ximNEWS');
//			$syncMngr->setFlag('colector', $colectorID);
//			$resultNew = $syncMngr->pushDocInPublishingPool($docID, time(), NULL);
//		}
    	// End SynchroFacade section
  		$docs = $docsToPublish;
   		$docs[] = $this->parent->get('IdNode');

		// Gets ximlet dependencies

		$depsMngr = new DepsManager();
		$result = $depsMngr->getBySource(DepsManager::BULLETIN_XIMLET, $this->parent->get('IdNode'));

		$ximletId = $result[0];

		if (!is_null($ximletId)) {
			$docs[] = $ximletId;

			// Getting documents associated to ximlet
			$sourceDocs = $depsMngr->getByTarget(DepsManager::STRDOC_XIMLET, $ximletId);

			if (!is_null($sourceDocs)) {

				// Up documents version for force publication
				foreach ($sourceDocs as $docId) {
					$docs[] = $docId;
				}
			}
		}

		return $docs;
    }

	/**
	*  Calls to generation method.
	*  @return bool
	*/

    function generator() {
//		$automatic = new Automatic();
		$ximNewsBulletin = new XimNewsBulletin($this->parent->get('IdNode'));
		$node = new Node($ximNewsBulletin->get('IdColector'));
		return $node->class->generateColector();
    }

	/**
	*  Wrapper for DeleteNode.
	*  @return bool
	*/

	function delete() {
		return $this->DeleteNode();
	}

	/**
	*  Deletes the XimNewsBulletin and its dependencies.
	*  @return bool
	*/

	function DeleteNode() {

	    $relNewsBulletins = new RelNewsBulletins();
	    $relNewsBulletins->deleteByBulletin($this->parent->get('IdNode'));

		$ximNewsBulletin = new XimNewsBulletin($this->parent->get('IdNode'));
	    $ximNewsBulletin->deleteFramesBulletin($this->parent->get('IdNode'));
	    $this->unPublishBulletin();

		if (!$ximNewsBulletin->delete()) return false;

		// Deletes dependencies in rel tables

		$depsMngr = new DepsManager();
		$depsMngr->deleteBySource(DepsManager::BULLETIN_XIMLET, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_NODE, $this->parent->get('IdNode'));
		$depsMngr->deleteByTarget(DepsManager::STRDOC_NODE, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_TEMPLATE, $this->parent->get('IdNode'));
		$depsMngr->deleteBySource(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode'));

		XMD_Log::info('Bulletin dependencies deleted');

	    return true;
	}

	/**
	*  Gets the XimNewsNewLanguage nodes that must to be published together with the XimNewsBulletin.
	*  @param int idColector
	*  @return array
	*/

	function getNewsToPublish($idColector) {
		$docs = array();

		$relNewsBulletins = new RelNewsBulletins();
		$news = $relNewsBulletins->GetNewsByBulletin($this->parent->get('IdNode'));

		$relNewsColector = new RelNewsColector();

		foreach ($news as $newsID) {
			$versionInColector = $relNewsColector->getNewsVersionInColector($idColector, $newsID);

			if (!is_null($versionInColector)) {

				$dataFactory = new DataFactory($newsID);

				if ($dataFactory->isEditedForPublishing($versionInColector)) {
					$docs[] = $newsID;

				}

			} else {
				 XMD_log::error("Inconsistency: news $newsID in bulletin ".$this->parent->get('IdNode')." but not in colector $idCcolector");
			}
		}

		return $docs;
	}

	/**
	 * Builds the XimNewsBulletin XML.
	 *
	 * @param int colectorID
	 * @param array array_newsID
	 * @param array data
	 * @param int totalBulletins
	 * @param int totalNews
	 * @param int newsPerPage
	 * @param int prevBulletin
	 * @param int nextBulletin
	 * @return bool
	 */

	function createXml($colectorID, $array_newsID, $data, $totalBulletins, $totalNews, $newsPerPage,
		$prevBulletin = NULL, $nextBulletin = NULL) {

		$ximNewsColector = new XimNewsColector($colectorID);
		$bulletinPvd = $ximNewsColector->get('IdTemplate');
		$xslFile = $ximNewsColector->get('XslFile');
		$colectorName = $ximNewsColector->get('Name');

		if (!$array_newsID) {
			XMD_Log::info('Array noticias nulo');
			return false;
		}

		settype($array_newsID,"array");
		$contenido = '';
		$strDoc = new StructuredDocument($this->parent->get('IdNode'));
		$bulletinPvd = $strDoc->GetDocumentType();

		$cache = new XimNewsCache();

		foreach($array_newsID as $newID){
			$relNewsColector = new RelNewsColector();
			$idRel = $relNewsColector->hasNews($colectorID, $newID);

			if ($idRel > 0) {
				$relNewsColector = new RelNewsColector($idRel);
				$version = $relNewsColector->get('Version');
				$subversion = $relNewsColector->get('SubVersion');
				$cacheId = $relNewsColector->get('IdCache');
			} else {
				XMD_Log::info('Sin relaci�n en la base de datos');
				continue;
			}

			if(!($cacheId > 0)){
				$df = new DataFactory($newID);
				$versionId = $df->getVersionId($version,$subversion);

				//Creamos la cache (si procede) y modificamos contadores

				$cache = new XimNewsCache();
				$cacheId = $cache->CheckExistence($newID,$versionId,$bulletinPvd);

				if(!($cacheId > 0)){
					$cacheId = $cache->CreateCache($newID,$versionId,$bulletinPvd,$xslFile);

					if(!$cacheId){
						XMD_Log::info("ERROR Creando cache de noticia $newID");

						//Elimino la asociacion para no dejar inconsistencia entre el boletin y la tabla

						$relNewColector = new RelNewsColector($idRel);
						$relNewColector->delete();

						//Envio mail informando al administrador

						$ximNewsNew = new XimNewsNew($newID);
						$newsName = $ximNewsNew->get('Name');

						$mailRender = new MailRenderer();

						$mailRender->setTemplate(XIMDEX_ROOT_PATH .ModulesManager::path('ximNEWS'). '/tpl/mail/insertionError.tpl');
						$mailRender->set('newsName', "'$newsName'");
						$mailRender->set('colectorName', "'$colectorName'");

						$body = $mailRender->render();

						$user = new User(301);
						$email = $user->Get('Email');
						$mail = new Mail();
						$mail->addAddress($email);
						$mail->Subject = "Error insertando noticia en boletin";
						$mail->Body = $body;
						$mail->Send();

						continue;
					}
				}

				$cache = new XimNewsCache($cacheId);
				$counter = $cache->get('Counter') + 1;
				$cache->set('Counter',$counter);
				$numRows = $cache->update();

				if (!$cache->update()) {
					XMD_Log::info("ERROR Actualizando contador en $cacheId");
				}

				$relNewsColector->set('IdCache', $cacheId);
				$relNewsColector->update();
			}

			$ximNewsCache = new XimNewsCache($cacheId);
			$newContent = $ximNewsCache->getContentCache();
			$contenido .= $newContent;


			$relNewsBulletin = new RelNewsBulletins();
			$relNewsBulletin->add($this->parent->get('IdNode'), $newID, $colectorID);
		}

		//rellenamos el bolet�n

		$header = ximNEWS_Adapter::getBulletinHeader($data, $bulletinPvd);

		$pos = strrpos($header,"</");
		$endBulletin = substr($header,$pos);
		$startBulletin = substr($header,0,$pos);

		$header2 = "<total_boletines value=\"$totalBulletins\" /><total_noticias value=\"$totalNews\" />
		<por_pagina value=\"$newsPerPage\" />";

		// Setting links and dependencies to next and prev bulletins

		$prev = is_null($prevBulletin) ? '' : "<prev nodeid=\"$prevBulletin\" />";
		$next = is_null($nextBulletin) ? '' : "<next nodeid=\"$nextBulletin\" />";

		$bulletinContent = $startBulletin . $prev . $next . $header2 . $contenido . $endBulletin;

		$strDoc->SetContent($bulletinContent);

		return true;
	}

	/**
	*	Removes the bulletin from the publication server
	*	@return int
	*/

	function unPublishBulletin(){

		$idBulletin = $this->parent->get('IdNode');

		if (ModulesManager::isEnabled('ximSYNC')) {
			include_once(XIMDEX_ROOT_PATH . ModulesManager::path('ximSYNC')."/inc/manager/SyncManager.class.php");

			// Creates a batchDown for bulletin

			$timeUp = mktime();
			$timeDown = mktime();
			$bulletinDocs = array($idBulletin);

			$node = new Node($idBulletin);
			$serverID = $node->GetServer();

			$nodeServer = new Node($serverID);

			if (Config::getValue('PublishOnDisabledServers') == 1) {
				$physicalServers = $nodeServer->class->GetPhysicalServerList(true);
			} else {
				$physicalServers = $nodeServer->class->GetEnabledPhysicalServerList(true);
			}


			foreach ($physicalServers as $serverId) {
				$batchMng = new BatchManager();
				$idBatchUp = $batchMng->getPublicatedBatchForNode($idBulletin, $serverId);

				$batchUp = new Batch($idBatchUp);
				$numFrames = $batchUp->get('ServerFramesTotal');
				$idGenerator = $batchUp->get('IdNodeGenerator');

				// Workaround??
				// Creating an un publishing batch if the batchUp node generator in the bulletin
				if ($idGenerator == $idBulletin) {
					$batch = new Batch();
					$idBatchDown = $batch->create($timeDown, 'Down', $idBulletin, 1, null);

					if ($idBatchDown > 0) {
						$batch->set('ServerFramesTotal', $numFrames);
						$batch->update();

						$batchUp->set('IdBatchDown', $idBatchDown);
						$batchUp->update();
					} else {
						XMD_log::error("Error in batch down creation for bulletin $idBulletin");
					}

					$bul = new XimNewsBulletin($this->nodeID);
                                        $colectorID = $bul->get('IdColector');

					if ($this->isBulletinForXimlet($colectorID)) {
						XMD_log::info("The bulletin is from a ximlet which re-publish documents");
						$ximNewsBulletin = new XimNewsBulletin($this->nodeID);
						$ximletID = $ximNewsBulletin->getBulletinXimlet();

						$ximletNode = new Node($ximletID);
						$ximletNode->SetContent('');

						$docsToPublish = array();
						$docsToPublish = $ximletNode->class->getRefererDocs();

						if (sizeof($docsToPublish) > 0) {
							foreach ($docsToPublish as $docID) {
								$docNode = new DataFactory($docID);
								$docNode->AddVersion();

								$syncMngr = new SyncManager();
								$syncMngr->pushDocInPublishingPool($docID, time(), NULL);
							}
						}

					}
				}
			}

		} else {
			$sync = new Synchronizer($idBulletin);
			$now = mktime();

			$dbObj = new DB();
			$updateObj = new DB();
			$sql = "SELECT IdSync FROM Synchronizer WHERE IdNode = $idBulletin AND State = 'IN'";

			XMD_Log::info("Unpublishing the bulleting $idBulletin");
			$i = 0;
			$dbObj->Query($sql);

			while (!$dbObj->EOF) {
				$frameId = $dbObj->GetValue('IdSync');
				$sql = "UPDATE Synchronizer SET DateDown = $now WHERE IdSync = $frameId";
				$updateObj->Execute($sql);

				if ($updateObj->numRows > 0) {
					$i++;
				}

				$dbObj->Next();
			}

			return $i;
		}
	}

	/**
	*  Checks if the XimNewsBulletin is the associated to XimNewsColector Ximlet.
	*  @param int idColector
	*  @return bool
	*/

	function isBulletinForXimlet($idColector) {

		$ximNewsColector = new XimNewsColector($idColector);
		$sorting = $ximNewsColector->get('OrderNewsInBulletins');

		if (preg_match("/^fecha/", $ximNewsColector->get('Filter')) > 0) {

			$orderBy = 'unix_timestamp(SetAsoc)';
		} else {

			$sorting = ($ximNewsColector->get('OrderNewsInBulletins') == 'desc') ? 'asc' : 'desc';
			$orderBy = 'IdContainer';
		}

		$ximNewsBulletin = new XimNewsBulletin();
		$result = $ximNewsBulletin->find('IdContainer', "IdColector = %s ORDER BY $orderBy $sorting LIMIT 1",
			array($idColector), MONO);

		if (!(sizeof($result) > 0)) {
			XMD_Log::error("Any container");
			return false;
		}

		$idContainer = $result[0];

		$bulletinContainer = new node($result[0]);
		$bulletins = $bulletinContainer->GetChildren();

		if (!in_array($this->parent->get('IdNode'), $bulletins))  return false;

		$nodeColector = new Node($idColector);
		$master = $nodeColector->class->getLangMaster();

		if (is_null($master)) 	return true;

		foreach ($bulletins as $idBulletin) {
			$strDoc = new StructuredDocument($idBulletin);

			if ($strDoc->get('IdLanguage') == $master) return true;
		}

		return false;
	}

	function getAlias(){

		$langBulletin = $this->getLanguage();

		$node = new Node($this->nodeID);
		$idContainer = $node->getParent();
		$node = new Node($idContainer);

		return $node->GetAliasForLang($langBulletin);
	}


}

?>
