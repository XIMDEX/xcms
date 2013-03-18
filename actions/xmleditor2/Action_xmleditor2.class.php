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
 *  @version $Revision: 8529 $
 */


ModulesManager::file('/inc/serializer/Serializer.class.php');
ModulesManager::file('/inc/helper/String.class.php');
ModulesManager::file('/inc/mvc/Request.class.php');
ModulesManager::file('/actions/xmleditor2/XimlinkResolver.class.php');
ModulesManager::file('/inc/i18n/I18N.class.php');
ModulesManager::file('/inc/model/locale.inc');


class Action_xmleditor2 extends ActionAbstract {

	private $_editor = null;

	public function index() {
		$idnode = $this->request->getParam('nodeid');
		$view = $this->request->getParam('view');

		$strDoc = new StructuredDocument($idnode);
                if($strDoc->GetSymLink()) {
                        $this->messages->add(_('Document cannot be opened.'), MSG_TYPE_WARNING);
                        $this->messages->add(_('It is a symbolic link'), MSG_TYPE_WARNING);
                        $values = array('id_node' => $idnode,'messages' => $this->messages->messages);
                        $this->render($values, NULL, 'messages.tpl');
                        return;
                }

		$queryManager = new QueryManager();
		$locale = new XimLocale();
		$user_locale = $locale->GetLocaleByCode(XSession::get('locale'));
		$locales = $locale->GetEnabledLocales();

		$action = $queryManager->getPage() . $queryManager->buildWith(array(
			'method' => 'load',
			'on_resize_functions' => '',
			'time_id' => microtime(true),  //timestamp for javascripts
			'user_locale' => $user_locale,
			'action' => 'xmleditor2',
			'nodeid' => $idnode
		));
		$this->render(array('action' => $action), NULL, 'iframe.tpl');
	}

	// Main method: shows initial form
	public function load() {
		$idnode = $this->request->getParam('nodeid');
		$view = $this->request->getParam('view');
		$this->getEditor($idnode);

		$xslIncludesOnServer = Config::getValue("XslIncludesOnServer");
		$values = $this->_editor->openEditor($idnode, $view);
		$values['on_resize_functions'] = '';
		$values['xinversion'] = Config::getValue("VersionName");
		$template = 'loadEditor_' . $this->_editor->getEditorName();
		//Adding Config params for xsl:includes
		$values["xslIncludesOnServer"] = $xslIncludesOnServer;
	

		$values["user_connect"] = null;
		$values['time_id'] = 0;
		if(ModulesManager::isEnabled('ximADM') ) {
			$userID = (int) XSession::get('userID');

			$time_id = time()."_".$userID;
			$values['time_id'] = $time_id;
			$values["user_connect"] = $this->addJs('/utils/user_connect.js.php?id='.$time_id.'&lang='.XSession::get('locale'), 'ximADM');
		}



		$this->render($values, $template, 'xmleditor2.tpl');

	}

	private function &getEditor($idnode) {

		$params = $this->request->getParam("params");

		$editorName = strtoupper('KUPU');
		$msg = new Messages();

		$class = 'XmlEditor_' . $editorName;
		$file =  '/actions/xmleditor2/model/XmlEditor_' . $editorName . '.class.php';
		$editor = null;

		if (!is_readable(XIMDEX_ROOT_PATH .$file)) {
			$msg->add(_('A non-existing editor has been refered.'), MSG_TYPE_ERROR);
			$this->render(array('nodeid' => $idnode, 'messages' => $msg->messages));
			exit();
		}

		 ModulesManager::file($file);

		if (!class_exists($class)) {
			$msg->add(_('A non-existing editor has been refered.'), MSG_TYPE_ERROR);
			$this->render(array('nodeid' => $idnode, 'messages' => $msg->messages));
			exit();
		}

		$query = App::get('QueryManager');
		$base_url = $query->getPage() . $query->buildWith(array());

		$editor = new $class();
		$editor->setBaseURL($base_url);
		$editor->setEditorName($editorName);
		$this->_editor = & $editor;
		return $editor;
	}

	private function printContent($content, $serialize=true) {
		// TODO: Use MVC renderers?, JSON renderer?, ...


		$ajax = $this->request->getParam('ajax');

		if ($ajax != 'json') {
			// TODO: Detect content type, at the moment is XML...
			header('Content-type: text/xml');
		} else {
			if ($serialize) {
				// TODO: Return the response through the MVC... (I don't like JSON implementation on the MVC !!!)
				if (!is_array($content) && !is_object($content)) {
					$content = array('data' => $content);
				}
				$content = Serializer::encode(SZR_JSON, $content);
			}
			header('Content-type: application/json');
		}

		print $content;
		exit();
	}

	public function getConfig() {
		$idnode = $this->request->getParam('nodeid');
		$this->getEditor($idnode);
		$content = $this->_editor->getConfig($idnode);
		$this->printContent($content);
	}

	public function getInfo() {
		$idnode = $this->request->getParam('nodeid');

		$node = new Node($idnode);
		$info = $node->loadData();
		if(!empty($info ) ) {
			$info = json_encode($info);
		}
		echo $info;
		die();
	}

	public function getXmlFile() {
		$idnode = $this->request->getParam('nodeid');
		$view = $this->request->getParam('view');
		$content = $this->request->getParam('content');
		$this->getEditor($idnode);
		$content = $this->_editor->getXmlFile($idnode, $view, $content);
		$this->printContent($content);
	}

	public function verifyTmpFile() {
		$idnode = $this->request->getParam('nodeid');
		$this->getEditor($idnode);
		$content = $this->_editor->verifyTmpFile($idnode);
		$this->printContent($content);
	}

	public function removeTmpFile() {
		$idnode = $this->request->getParam('nodeid');
		$this->getEditor($idnode);
		$content = $this->_editor->removeTmpFile($idnode);
		$this->printContent($content);
	}

	public function recoverTmpFile() {
		$idnode = $this->request->getParam('nodeid');
		$this->getEditor($idnode);
		$content = $this->_editor->recoverTmpFile($idnode);
		$this->printContent($content);
	}

	public function getXslFile() {
		$idnode = $this->request->getParam('nodeid');
		$view = $this->request->getParam('view');
		$includesOnServer = $this->request->getParam("includesInServer");
		$this->getEditor($idnode);
		$content = $this->_editor->getXslFile($idnode, $view, $includesOnServer);
		$this->printContent($content);
	}

	public function getSchemaFile() {
		$idnode = $this->request->getParam('nodeid');
		$this->getEditor($idnode);
		$content = $this->_editor->getSchemaFile($idnode);
		$this->printContent($content);
	}

	public function canEditNode() {
		$ximcludeId = $this->request->getParam('nodeid');
		$userId = XSession::get('userID');
		$ret = Auth::canWrite($userId, array('node_id' => $ximcludeId));
		$this->printContent(array('editable' => $ret));
	}

	public function validateSchema() {
		$idnode = $this->request->getParam('nodeid');
		$xmldoc = Request::post('content');
		$xmldoc = String::stripslashes($xmldoc);
		$this->getEditor($idnode);
		$ret = $this->_editor->validateSchema($idnode, $xmldoc);
		$this->printContent($ret);
	}

	public function saveXmlFile() {
		$idnode = $this->request->getParam('nodeid');
		$content = Request::post('content');
		$autoSave = ($this->request->getParam('autosave') == 'true') ? true : false;
		$this->getEditor($idnode);
		$response = $this->_editor->saveXmlFile($idnode, $content, $autoSave);

		// TODO: Evaluate $response['saved']...


		foreach ($response['headers'] as $header) {
			header($header);
		}
		$this->printContent($response['content']);
	}

	public function publicateFile() {
		$idnode = $this->request->getParam('nodeid');
		$content = Request::post('content');
		$this->getEditor($idnode);
		$response = $this->_editor->publicateFile($idnode, $content);

		foreach ($response['headers'] as $header) {
			header($header);
		}
		$this->printContent($response['content']);
	}

	public function getSpellCheckingFile() {
		$idnode = $this->request->getParam('nodeid');
		$content = Request::post('content');
		$this->getEditor($idnode);
		$content = $this->_editor->getSpellCheckingFile($idnode, $content);
		$this->printContent($content);
	}

	public function getAnnotationFile() {
		$idnode = $this->request->getParam('nodeid');
		$content = Request::post('content');
		$this->getEditor($idnode);
		$content = $this->_editor->getAnnotationFile($idnode, $content);
		$this->printContent($content, false);
	}

	/**
	 * Returns a JSON string with the allowed nodes under especified uid
	 */
	public function getAllowedChildrens() {
		$idnode = $this->request->getParam('nodeid');
		$uid = $this->request->getParam('uid');
		$content = $this->request->getParam('content');
		$this->getEditor($idnode);
		$allowedChildrens = $this->_editor->getAllowedChildrens($idnode, $uid, $content);
		$this->printContent($allowedChildrens);
	}

	public function getPreviewInServerFile() {
		$idnode = $this->request->getParam('nodeid');
		$content = Request::post('content');
		$idChannel = Request::post('channelid');
		$this->getEditor($idnode);
		$content = $this->_editor->getPreviewInServerFile($idnode, $content, $idChannel);
		$this->printContent($content);
	}

	public function getNoRenderizableElements() {
		$idnode = $this->request->getParam('nodeid');
		$this->getEditor($idnode);
		$content = $this->_editor->getNoRenderizableElements($idnode);
		$this->printContent($content);
	}

	public function getAvailableXimlinks() {

		$docid = $this->request->getParam('docid');
		$term =  $this->request->getParam('term');

		$xr = new XimlinkResolver();
		$data = $xr->getAvailableXimlinks($docid,$term);

		$this->sendJSON($data);
	}

	public function resolveXimlinkUrl() {

		$idnode = $this->request->getParam('nodeid');
		$channel = $this->request->getParam('channel');

		$xr = new XimlinkResolver();
		$data = $xr->resolveXimlinkUrl($idnode, $channel);

		$this->sendJSON($data);
	}

}

?>
