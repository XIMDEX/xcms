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




ModulesManager::file('/inc/model/XimNewsList.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsAreas.php', 'ximNEWS');
ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
ModulesManager::file('/inc/io/BaseIO.class.php');

class Action_createcolector extends ActionAbstract {

	public function index() {

		$actionId = $this->request->getParam('actionid');
		$idNode = $this->request->getParam('nodeid');

		$node = new Node($idNode);
		$nodeTypeName = $node->nodeType->get('Name');

		$goMethod = ($nodeTypeName == 'XimNewsColector') ? 'editColector' : 'createColector';

		$params = $this->request->getParam("params");
		$query["confirm"] = _('Do you want to continue?');

		$this->addJs('/actions/createcolector/resources/js/index.js', 'ximNEWS');

		$colectorValues = array();
		if($goMethod == 'editColector')
			$colectorValues = $this->getColectorData($idNode);
		$colectorRelatedValues = $this->getColectorRelatedData($idNode);

		$actionValues = array(
			'query' => $query,
			'params' => $params,
			'id_node' => $idNode,
			'go_method' => $goMethod,
			'nodeUrl' => Config::getValue('UrlRoot') . "/xmd/loadaction.php?actionid=$actionId&nodeid=$idNode"
		);

		$values = array_merge($colectorRelatedValues, $actionValues, $colectorValues);
		$this->render($values, 'index', 'default-3.0.tpl');
	}

	public function editColector() {

	   	$data = array();

	   	$namelst = Request::post("namelst");
	   	$idNode = Request::post("nodeid");
	   	$lista = Request::post("listaid");
	   	$tipo = Request::post("tipo");
	   	$data['colector'] = Request::post("colector");
	   	$template = Request::post("template");
	   	$data['languages'] = Request::post("langidlst");
	   	$data['aliasLang'] = Request::post("namelst");
	   	$data['channels'] = Request::post("channellst");
	   	$timeToGenerateEnable = (int) Request::post("timetogenerateenable");
	   	$newsToGenerateEnable = (int) Request::post("newstogenerateenable");
	   	$timeToGenerate = Request::post("timetogenerate");
	   	$newsToGenerate = Request::post("newstogenerate");
	   	$newsPerBull = Request::post("newsperbull");
	   	$data['master'] = Request::post("master");
			$data['sortnews'] = Request::post("order");
	   	$canalCorreo = Request::post("canal_correo");
	   	$data['idarea'] = (int) Request::post("idarea");
	   	$data['global'] = Request::post("global");
	   	$data['paginacion'] = strtolower(Request::post("paginacion")) == 'si' ? true : false;

	   	$ximNewsColector = new XimNewsColector($idNode);
	   	$node = new Node($idNode);
		$colectorName = $ximNewsColector->get('Name');

		$data['filter'] = ($tipo == 'numeronoticias') ? $newsPerBull : $tipo;
		$currentType = $tipo;

	   	if (isset($template) && $template != $ximNewsColector->get('IdTemplate')) {
	  		$data['Template'] = $template;
	   	}

		// It is defined $data["listName"] even if  $list is empty.
		// (By this way, it is possible to delete completly the mail list associated to the bulletin)
		$data['listName'] = array();
		if(strlen(trim($lista)) > 0) {
			$lista =  explode(",",$lista);
			foreach($lista as $listName){
				$listName = trim($listName);
				if ($this->isValidEmail($listName)) {
					$data['listName'][] =  $listName;
				} else {
					$this->messages->add(sprintf(
						_('The email %s is not well-formed, it will not be added to the list'), $listName
					), MSG_TYPE_WARNING);
				}
			}
		}

		$data['inactive'] = $timeToGenerateEnable + $newsToGenerateEnable;

		$data['newsperbull'] = $data['paginacion'] === true ? $newsPerBull : 10000;

		if (isset($timeToGenerate)) {
			$data['timetogenerate'] = $timeToGenerate;
		}

		if (isset($newsToGenerate)) {
			$data['newstogenerate'] = $newsToGenerate;
		}

		$data['mailchannel'] = $canalCorreo;

	   	if(count($data) > 0) {

			$node=new node($idNode);
			$currentName = $node->GetNodeName();

			$currentFilter = $ximNewsColector->get('Filter');
			$currentNewsPerBulletin = $ximNewsColector->get('NewsPerBulletin');
			$currentSorting = $ximNewsColector->get('OrderNewsInBulletins');
			$forceTotal = null;
			$oldValues = null;

			if($currentFilter != $data['filter'] || $currentNewsPerBulletin != $data['newsperbull'] || $currentSorting != $data['sortnews'] || $currentName != $data['colector']){
			    $forceTotal = 1;
			    $data['forcetotalgeneration'] = 1;
			    $oldValues = "Nombre del colector: $currentName;Tipo de colector: $currentType;Número de noticias por boletín: $currentNewsPerBulletin;Ordenación de noticias: $currentSorting";

			    if($currentFilter != $data['filter']) {
					$data['forcetotalgeneration'] = 2;
			    }
			}

			$parentID = $node->getParent();

			if(!$result = $this->updateColector($idNode, $data)) {

				$this->messages->add(_("Error editing the colector. The operation was not performed successfully"), MSG_TYPE_NOTICE);
				$this->render(array('goback' => true, 'messages' => $this->messages->messages), NULL, 'messages.tpl');
				return false;
			}

			if($data['global'] == 1) {

				$this->messages->add(_("You have defined your colector as global. In order to get it working properly, you should assign it groups"), MSG_TYPE_NOTICE);
				// todo: redirect here
			}
	   	}

		$this->messages->add(_("The colector edition has been performed successfully"), MSG_TYPE_NOTICE);

	    $user = new User(XSession::get('userID'));
	    $email = $user->Get('Email');
	    $mail = new Mail();
	    $mail->addAddress($email);
	    $mail->Subject = _("Collector edition");
	    $mail->Body = _("The previous params were").":\n".str_replace(';',"\n",$oldValues);
/*
	    if ($mail->Send()) {

			$this->messages->add(_("Se le ha enviado un email con los valores antiguos del colector para que pueda restaurarlos en caso de problemas durante la generación total"), MSG_TYPE_NOTICE);
	    }
*/
		$this->render(array('goback' => true, 'messages' => $this->messages->messages), NULL, 'messages.tpl');
	}

	private function updateColector($idNode, $data) {

		$node = new Node($idNode);
		$currentName = $node->get('Name');

	  if ($data["colector"] != $currentName) 	$node->RenameNode($data["colector"]);

		$ximNewsColector = new XimNewsColector($idNode);
		//add last Master Language ( disabled input in html )
		$master = $node->class->getLangMaster();
		if(!in_array($master, $data['languages']) ) $data['languages'][] = $master;

		if (isset($data['Template'])) $ximNewsColector->set('IdTemplate', $data['Template']);

		if (isset($data["forcetotalgeneration"]))
			$ximNewsColector->set('ForceTotalGeneration', $data['forcetotalgeneration']);

		if (isset($data["filter"])) $ximNewsColector->set('Filter', $data['filter']);

		if (isset($data["colector"])) $ximNewsColector->set('Name', $data['colector']);

		if (isset($data["newsperbull"])) $ximNewsColector->set('NewsPerBulletin', $data['newsperbull']);

		if (isset($data["sortnews"])) $ximNewsColector->set('OrderNewsInBulletins', $data['sortnews']);

		if (isset($data["timetogenerate"])) $ximNewsColector->set('TimeToGenerate', $data["timetogenerate"] * 3600);

		if (isset($data["newstogenerate"])) $ximNewsColector->set('NewsToGenerate', $data['newstogenerate']);

		if (isset($data["mailchannel"])) $ximNewsColector->set('MailChannel', $data['mailchannel']);

		if (isset($data["inactive"])) $ximNewsColector->set('Inactive', $data['inactive']);

		if (isset($data["idarea"])) $ximNewsColector->set('IdArea', $data['idarea']);

		if (isset($data["global"])) $ximNewsColector->set('Global', $data['global']);

		if (!$ximNewsColector->update()) {
			$this->messages->add(_("The colector has NOT been edited successfully."), MSG_TYPE_ERROR);
			XMD_Log::error("Updating ximnewsColector table");
			return false;
		}

		if (!empty($data['languages'])) {
			$this->setLanguages($idNode, $data['languages'], $data['master']);
		}

		$idXimlet = $ximNewsColector->get('IdXimlet');
		$this->setAlias($idXimlet, $data['aliasLang']);

		if (!$this->setChannels($idNode, $data["channels"])) {
			$this->messages->add(_("Error updating channels."), MSG_TYPE_ERROR);
			XMD_Log::error(_("Updating channels list for colector")." $idNode");
		}

		if(isset($data["listName"])){
			$ximNewsList = new XimNewsList();
			if(!$ximNewsList->updateList($idNode,$data["listName"])){
				$this->messages->add(_("Error updating mail list."), MSG_TYPE_ERROR);
				XMD_Log::error(_("Updating channels list for colector")." $idNode");
			}
		}

		$this->messages->add(_("The colector has been edited successfully."), MSG_TYPE_NOTICE);

		return true;
	}

	public function createColector() {

		$idNode = $this->request->getParam('nodeid');
		$actionId	= $this->request->getParam("actionid");
		$colectorName = $this->request->getParam('colector');
		$idTemplate = $this->request->getParam('template');
		$languages = $this->request->getParam('langidlst');
		$aliasList = $this->request->getParam('namelst');
		$channels = $this->request->getParam('channellst');
		$master = $this->request->getParam('master');
		$global = $this->request->getParam('global');
		$mailList = $this->request->getParam('listaid');
		$order = $this->request->getParam('order');
		$canalCorreo = $this->request->getParam('canal_correo');
		$newstogenerate = $this->request->getParam('newstogenerate');
		$timetogenerate = $this->request->getParam('timetogenerate');
		$timeToGenerateEnable = (int) $this->request->getParam('timetogenerateenable');
		$newsToGenerateEnable = (int) $this->request->getParam('newstogenerateenable');
		$typeColector = $this->request->getParam('typeColector');
		$idArea = (int) $this->request->getParam('idarea');
		$newsPerBulletin = (!$this->request->getParam('newsperbull') ? 10000 : $this->request->getParam('newsperbull'));

		if(strlen(trim($mailList)) > 0) {
			$lista =  explode(',',$mailList);
	//		$mailList =  empty($mailList) ? NULL : explode(',', $mailList);
			$mailList = array();
			foreach($lista as $listName){
				$listName = trim($listName);
				if ($this->isValidEmail($listName)) {
					$mailList[] =  $listName;
				} else {
					$this->messages->add(sprintf(
						_('The email %s is not well-formed, it will not be added to the list'), $listName
					), MSG_TYPE_WARNING);
				}
			}
		}

		$filter = $this->request->getParam('tipo') == 'numeronoticias'
		? $newsPerBulletin : $this->request->getParam('tipo');

		$master = $this->request->getParam('master') == 'none' ? NULL : $this->request->getParam('master');

		$inactive = $timeToGenerateEnable + $newsToGenerateEnable;

		$adapter = new ximNEWS_Adapter();

		if (!$idColector = $adapter->createColector($idNode, $colectorName,
			$idTemplate, $languages, $aliasList, $channels, $global, $order,
			$canalCorreo, $newstogenerate, $timetogenerate, $inactive,
			$newsPerBulletin, $filter, $mailList, $idArea, $master)) {

			XMD_Log::info(_("Error creating the colector index").":".$idColector."");
			$this->messages->add(_("The colector has NOT been created successfully."), MSG_TYPE_ERROR);
			$this->messages->add(_("Error creating the colector index"), MSG_TYPE_ERROR);
			$this->messages->mergeMessages($adapter->messages);
		} else {

			$this->messages->add(_("The colector has been created successfully."), MSG_TYPE_NOTICE);

			// todo: make this in the createColector method

			$adapter->setOtfProperty($idColector, $typeColector);

			$this->reloadNode($idNode);
		}

		$this->render(array('goback' => true, 'messages' => $this->messages->messages), NULL, 'messages.tpl');

	}


	//AUXILIARY FUNCTIONS

	private function getColectorRelatedData ($idNode) {

		$node = new Node($idNode);
		$templates = $node->getTemplates('bulletin_template');

		foreach($templates as $templateId){
			$template = new Node($templateId);
			$templates_list[] = array('id' => $templateId, 'name' => $template->get('Name'));
		}

		$areas = XimNewsAreas::GetAllAreas();
		$allLists = XimNewsList::GetAllLists();
		$colectorLists = XimNewsList::getList($idNode);
		$lists = array_diff($allLists, $colectorLists);

		$language = new Language();
		$languages = $language->GetAllLanguages();

		foreach($languages as $languageId){
			$language = new Language($languageId);
			$languages_list[] = array('id' => $languageId, 'name' => $language->get('Name'));
		}

		$channels = Channel::GetAllChannels();

		foreach($channels as $channelId){
			$channel = new Channel($channelId);
			$channels_list[] = array('id' => $channelId, 'name' => $channel->get('Description'));
		}

		//Get if otf is up
		if (ModulesManager::isEnabled('ximOTF')){
			$otfAvailable=true;
			$isOTF = $node->getSimpleBooleanProperty('otf');
		}else{
			$otfAvailable=false;
			$isOTF='';
		}

		$values = array(
			'areas' => $areas,
			'isOTF' => $isOTF,
			'languages' => $languages_list,
			'lists' => $lists,
			'channels' => $channels_list,
			'templates' => $templates_list,
			'otfAvailable' => $otfAvailable
		);

		return $values;
	}

	private function getColectorData($idNode) {

		$ximNewsColector = new XimNewsColector($idNode);
		$idXimlet = $ximNewsColector->get('IdXimlet');

		$node = new Node($idNode);
		$ximletNode = new Node($idXimlet);

		$colectorLangs = $node->class->getLanguages();
		$checkedLangs = array();
		foreach($colectorLangs as $idColectorLang) {
			$checkedLangs[$idColectorLang] = 'checked';
			$langAlias[$idColectorLang] = $ximletNode->GetAliasForLang($idColectorLang);
		}
		$masterLang = $node->class->getLangMaster();

		$colectorChannels = $node->class->getChannels();
		$checkedChannels = array();
		foreach($colectorChannels as $idColectorChannel) {
			$checkedChannels[$idColectorChannel] = 'checked';
		}

		$colectorData = array(
		    'filter' => $ximNewsColector->get('Filter'),
			'news_per_bull' => $ximNewsColector->get('NewsPerBulletin'),
			'sort_news' => $ximNewsColector->get('OrderNewsInBulletins'),
			'time_to_generate' => $ximNewsColector->get('TimeToGenerate')/3600,
			'news_to_generate' => $ximNewsColector->get('NewsToGenerate'),
			'mail_channel' => $ximNewsColector->get('MailChannel'),
			'inactive' => $ximNewsColector->get('Inactive'),
			'global' => $ximNewsColector->get('Global'),
			'name' => $ximNewsColector->get('Name'),
			'id_template' => $ximNewsColector->get('IdTemplate'),
			'id_area' => $ximNewsColector->get('IdArea'),
			'news_per_bulletin' => $ximNewsColector->get('NewsPerBulletin'),
			'order_news_in_bulletins' => $ximNewsColector->get('OrderNewsInBulletins'),
			'mail_list' => implode(',', XimNewsList::getList($idNode)),
			'colector_langs' => $checkedLangs,
			'master_lang' => $masterLang,
			'colector_channels' => $checkedChannels,
			'lang_alias' => $langAlias
		);

		$colectorData['paginacion'] = $colectorData['news_per_bull'] == 10000 ? 'no' : 'si';

		return array('colector_data' => $colectorData);
	}

	private function setAlias($idXimlet, $alias) {

		$node = new Node($idXimlet);

		foreach ($alias as $idLang => $aliasName) {
			if ($node->GetAliasForLang($idLang) != $aliasName) {
				$node->SetAliasForLang($idLang, $aliasName);
			}
		}
	}

	private function setLanguages($idColector, $languages, $master) {

		$adapter = new ximNEWS_Adapter();

		$colectorNode = new Node($idColector);
		$channels = $colectorNode->class->getChannels();
		$colectorLanguages = $colectorNode->class->getLanguages();
		$colectorMaster = $colectorNode->class->getLangMaster();

		$languagesToAdd = array_diff($languages, $colectorLanguages);
		$languagesToDelete = array_diff($colectorLanguages, $languages);

		// anything changes

		if (sizeof(array_diff($languages, $colectorLanguages)) == 0 &&
			sizeof(array_diff($colectorLanguages, $languages)) == 0 && $colectorMaster == $master) return true;

		// updating colector languages

		$ximNewsColector = new XimNewsColector($idColector);
		$idXimletContainer = $ximNewsColector->get('IdXimlet');
		$colectorName = $ximNewsColector->get('Name');
		$idTemplate = $ximNewsColector->get('IdTemplate');

		$ximletNode = new Node($idXimletContainer);

		$targetLink = NULL;

		if (!empty($master)) {

			// sort languages starting by master

			uksort($languages, create_function('$v, $w', 'return ($v == ' . $master . ') ? -1 : +1;'));
		}

		//remove lenguagesTODelete
		foreach($languagesToDelete as $idLanguage) {
			$idChild = $ximletNode->class->GetChildByLang($idLanguage);
			$node = new Node($idChild);
			$node->delete();

//			$syncMngr = new SyncManager();
//				$syncMngr->setFlag('type', 'ximNEWS');
	//		$syncMngr->pushDocInPublishingPool($idChild, time(), time()+60*60);
		}

		foreach ($languagesToAdd as $idLanguage) {

			$idChild = $ximletNode->class->GetChildByLang($idLanguage);

			if (is_null($idChild)) {

				$idChild = $adapter->createXimletLanguage($colectorName, $idXimletContainer, $idLanguage, $aliasName,
					$idTemplate, $channels, $targetLink);
			}

			if (!empty($master) && $idLanguage == $master) $targetLink = $idChild;

			if (!empty($master) && $idLanguage != $master) {

				$node = new Node($idChild);
				$node->SetWorkflowMaster($targetLink);

				$doc = new StructuredDocument($idChild);
				$doc->SetSymLink($targetLink);
			}
		}

		return true;
	}

	function setChannels($idColector, $channels) {

		$nodeColector = new Node($idColector);
		if(!$colectorChannels = $nodeColector->class->GetChannels())
			$colectorChannels = array();

		$channelsToAdd = array_diff($channels, $colectorChannels);
		$channelsToDelete = array_diff($colectorChannels, $channels);

		// anything changes

		if (sizeof($channelsToAdd) == 0 && sizeof($channelsToDelete) == 0) return true;

		// get documents to modify

		$ximNewsColector = new XimNewsColector($idColector);
		$idXimlet = $ximNewsColector->get('IdXimlet');

		$nodeXimlet = new Node($idXimlet);
		$ximletChilds = $nodeXimlet->GetChildren();

		$bulletins = XimNewsBulletin::getAllByColector($idColector);

		$documents = array_merge($ximletChilds, $bulletins);

		foreach($documents as $idDoc) {

			$doc = new StructuredDocument($idDoc);

			if(!$docChannels = $doc->GetChannels())
				$docChannels = array();

			$diffChannels = array_diff($docChannels, $channels);

			// deleting channels

			if (sizeof($channelsToDelete) > 0) {

				foreach($channelsToDelete as $idChannel){

					$doc->DeleteChannel($idChannel);
				}
			}

			// adding channels

			if (sizeof($channelsToAdd) > 0) {

				foreach($channelsToAdd as $idChannel){

					$doc->AddChannel($idChannel);
				}
			}
		}

		return true;
	}

	protected function isValidEmail($email) {
		return (preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email) >= 1);
	}

}
?>
