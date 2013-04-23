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



ModulesManager::file('/inc/ximNEWS_Adapter.php', 'ximNEWS');
ModulesManager::file('/actions/composer/Action_composer.class.php');
ModulesManager::file('/inc/serializer/Serializer.class.php');
ModulesManager::file('/inc/model/Links.inc');


class Action_createnews extends ActionAbstract {

    function index() {

		$idNode	= $this->request->getParam("nodeid");
		$actionID = $this->request->getParam("actionid");

		$node = new Node($idNode);
		$sectionId = $node->GetSection();
		$langList = $node->getProperty('language');
		$templates_list = array();

		if (empty($langList)) {
			$language = new Language();
			$langList = $language->GetAllLanguages();
		}

		foreach ($langList as $languageId) {
			 $language = new Language($languageId);
			 $languages_list[] = array('id' => $languageId, 'name' => $language->get('Name'));
		}

		$channel = new Channel();
		$channels_list = $channel->getChannelsForNode($idNode);

		$templateslst = $node->getTemplates('news_template');

		if (!is_null($templateslst)) {
			foreach ($templateslst as $templateId) {
				 $template = new Node($templateId);
				 $templates_list[] = array('id' => $templateId, 'name' => $template->get('Name'));
			}
		}else{
			XMD_Log::info(_("There is not defined any template to create news"));
		}

		$lotes_list = ximNEWS_Adapter::getLotes($idNode);

		$this->addCss('/actions/createnews/resources/css/index.css', 'ximNEWS');

		$values = array(
			'id_node' => $idNode,
			'languages' => $languages_list,
			'channels' => $channels_list,
			'templates' => $templates_list,
			'lotes' => sizeof($lotes_list) > 0 ? $lotes_list : NULL,
			'go_method' => 'creation_form'
		);
		$this->render($values, 'index', 'default-3.0.tpl');
    }

	function creation_form() {

		$userID = XSession::get('userID');
		$user = new User();
		$user->SetID($userID);
		$groups = $user->GetGroupList();

		$nodeId = $this->request->getParam('nodeid');
		$actionID = $this->request->getParam("actionid");
		$templateId = $this->request->getParam("template");
		$idLote = $this->request->getParam("loteid");
		$master = $this->request->getParam("master");
		$channels_list = $this->request->getParam("channellst");
		$languages = $this->request->getParam("langidlst");

		foreach ($languages as $languageId) {
			$alias = $this->request->getParam($languageId);
			$languages_list[$languageId] = $alias;
		}

		$langlst = !empty($master) ? array($master) : $languages;

		foreach ($langlst as $languageId) {
			 $lang = new Language($languageId);
			 $languages_table[] = array('id' => $languageId, 'name' => $lang->get('Name'), 'iso' => $lang->get('IsoName'));
		}

		$node = new Node($nodeId);
		$idSection = $node->GetSection();

		if (empty($idLote)) {

			$nodeTypeFolder = new NodeType();
			$nodeTypeFolder->setByName('ximnewsimages');
			$idNodeTypeFolder = $nodeTypeFolder->get('IdNodeType');

			$sectionNode = new Node($idSection);
			$result = $sectionNode->getChildren($idNodeTypeFolder);
			$idLote = $result[0];
		}

		$colectorslst = XimNewsColector::GetColectors($idSection, $groups);
		$areas = XimNewsAreas::GetAllAreas();

		$this->addJs('/actions/createnews/resources/js/tabs.js', 'ximNEWS');
		$this->addJs('/actions/createnews/resources/js/calendar.js', 'ximNEWS');
		$this->addCss('/actions/createnews/resources/css/index.css', 'ximNEWS');

		$adapter = new ximNEWS_Adapter();

		$values = array(
			'id_node' => $nodeId,
			'id_section' => $idSection,
			'id_action' => $actionID,
			'languages' => $languages_list,
			'languages_table' => $languages_table,
			'form_elements' => $adapter->getContentElements($templateId),
			'num_languages' => sizeof($langlst),
			'master' => $master,
			'now' => date('d-m-Y H:i:s'),
			'channels' => $channels_list,
			'template' => $templateId,
			'colectors' => $colectorslst,
			'num_colectors' => sizeof($colectorslst),
			'areas' => $areas,
			'num_areas' => sizeof($areas),
			'datesystem' => date('d/m/Y/G/i/s'),
			'loteid' => $idLote,
			'go_method' => 'create_news',
			'time_stamp' => mktime()
		);
		$this->render($values, 'creation_form', 'default-3.0.tpl');
	}

	function load_language_tab() {

		$idTemplate = $this->request->getParam('idtemplate');
		$idLanguage = $this->request->getParam('idlang');

		$adapter = new ximNEWS_Adapter();
		$values = array('id_lang' => $idLanguage, 'form_elements' => $adapter->getContentElements($idTemplate));
		$this->render($values, NULL, 'only_template.tpl');
	}

	function create_news() {

		$idNode = $this->request->getParam('nodeid');
		$actionID = $this->request->getParam("actionid");

		$timeStampFrom = $this->request->getParam("timestamp_from");
		$timeStampTo = $this->request->getParam("timestamp_to");

		$images = array();
		$links = NULL;

		$files = !empty($_FILES['a_enlaceid_noticia_archivo_asociado']['name']) ?
			$_FILES['a_enlaceid_noticia_archivo_asociado'] : NULL;
		$videos = !empty($_FILES['a_enlaceid_noticia_video_asociado']['name']) ?
			$_FILES['a_enlaceid_noticia_video_asociado'] : NULL;
		$images[] = !empty($_FILES['a_enlaceid_noticia_imagen_asociada']['name']) ? 
			$_FILES['a_enlaceid_noticia_imagen_asociada'] : NULL;

		$master = ($this->request->getParam('master') != 'none') ? $this->request->getParam('master') : NULL;
		$adapter = new ximNEWS_Adapter();

		if (!$adapter->createNews($idNode, $this->request->getParam('template'), $this->request->getParam('nombrenoticia'),
			array_keys($this->request->getParam('alias_lst')), $this->request->getParam('channellst'), $_POST, $master,
			$this->request->getParam('alias_lst'), $this->request->getParam('colectorsidlst'), $this->request->getParam('areas'), $images, $links,
			$files, $videos, false)) {

			$adapter->messages->add(_('Error creating news'), MSG_TYPE_ERROR);
		} else {
			$adapter->messages->add(_('The news was created correctly'), MSG_TYPE_NOTICE);
		}

		$this->reloadNode($this->request->getParam('nodeid'));

		$values = array(
			'messages' => $adapter->messages->messages,
			'id_node' => $idNode,
			"nodeURL" => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid=$idNode"
		);

		$this->render($values, NULL, 'messages.tpl');
	}

	function validate_name() {

		$newsName = $this->request->getParam('name');
		$idSection = $this->request->getParam('section');

		$newsSection = new Node($idSection);
		$idFolder = $newsSection->GetChildByName('news');

		$newsFolder = new Node($idFolder);
		$id = $newsFolder->GetChildByName($newsName);

		echo $id > 0 ? '0' : '1';
	}

	function get_links() {

		$link = new Link();
		$links = array();
		$result = $link->find('IdLink, Url');

		if (sizeof($result) > 0) {
			foreach($result as $data) {
				$links[] = array('id' => $data['IdLink'], 'value' => $data['Url'], 'label' => $data['Url']);
			}
		}

		$this->sendJSON($links);
	}

	function get_images() {

		$images = array();
		$idLote = $this->request->getParam('lote');

		$folderNode = new Node($idLote);
		$children = $folderNode->TraverseTree();

		// excludes the node itself

		array_shift($children);

		if (sizeof($children) > 0) {

			foreach ($children as $idChild) {

				$childNode = new Node($idChild);

				if ($childNode->nodeType->get('Name') == 'XimNewsImageFile') {

					$images[] = array('id' => $idChild, 'value' => $childNode->get('Name'), 'label' => $childNode->get('Name'));
				}
			}
		}

		$this->sendJSON($images);

	}
}
?>
