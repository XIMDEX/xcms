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




ModulesManager::file('/inc/parsers/TolFiltersRNG.class.php', 'tolDOX');
ModulesManager::file('/inc/model/structureddocument.inc');
ModulesManager::file('/inc/helper/Utils.class.php');
ModulesManager::file('/inc/Toldox.class.php', 'tolDOX');
ModulesManager::file('/actions/browser3/inc/GenericDatasource.class.php');
ModulesManager::file('/inc/model/RelValidityVersions.class.php');
ModulesManager::file('/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/validator/TolValidator.class.php', 'tolDOX');


class Action_tol_wizard extends ActionAbstract {

	private $domDoc;
	private $xpath;

	public function index () {

		$idNode = $this->request->getParam('nodeid');

		if (ModulesManager::isEnabled('tolDOX') && !is_numeric($idNode)) {

			$sir = new Toldox();
//			$idNode = $sir->read($idNode, Toldox::getToldoxRoot());
			$idNode = GenericDatasource::read($this->request);
			$idNode = $idNode['__nodeid'];

			/*if (is_object($idNode)) {

				// $idNode is a node entity
				$idNode = $idNode->get('idnode');
			} else if (is_array($idNode)) {

				// Show errors
				foreach ($idNode as $error) {
					$this->messages->add(_($error['error']), MSG_TYPE_ERROR);
				}
				$this->renderMessages();
			}*/
		}

		if (!($idNode > 0)) {
			$this->messages->add(_('Any document to edit has been specified'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}

		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('Specified document does not exist'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}

		$structuredDocument = new StructuredDocument($idNode);
		$idTemplate = $structuredDocument->get('IdTemplate');

		if (!($idTemplate > 0)) {
			$this->messages->add(_('Document template could not be obtained'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}

		$template = new Node($idTemplate);
		if (!($template->get('IdNode') > 0)) {
			$this->messages->add(_('Document template cannot be obtained, consult with your administrator'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}
		
		$templateName = preg_replace('/\.xml$/', '', $template->get('Name'));
		if ($templateName == 'Indice') {
			$this->request->setParam('mod', 'tolDOX');
			$this->request->setParam('module', 'tolDOX');
			$this->request->setParam('action', 'indexWizard');
			$this->request->setParam('actionName', 'indexWizard');
			$this->request->setParam('actionid', null);
			$_GET['mod'] = 'tolDOX';
			$_GET['action'] = 'indexWizard';
			$this->redirectTo('index', 'indexWizard', $this->request->getRequests());
			return;
		}
		
		// Invalid node or template
		$rngParser = new TolFiltersRNG();
		if (!$rngParser->loadNode($idTemplate)) {
			$this->messages->add(_('Rng could not be loaded'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}

		$formElements = $rngParser->transform();

		$node = new Node($idNode);
		$content = $node->getContent();
		$content = self::setHeaderInContent($content);

		$this->domDoc = new DOMDocument();
		$this->domDoc->validateOnParse = true;
		$this->domDoc->preserveWhiteSpace = false;
		$this->domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8));

		$this->xpath = new DOMXPath($this->domDoc);

		$formElements = $this->recoverData($formElements);

		if (empty($formElements)) {
			$this->messages->add(_('Requested information to create form could not be extracted'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}

		$this->addCss('/actions/tol_wizard/resources/css/tol_wizard.css');
		$this->addJs('/actions/tol_wizard/resources/js/tol_wizard.js');
		$this->addJs('/resources/js/wizard/TolFormFactory.js', 'tolDOX');
		$this->addJs('/inc/js/collection.js');

		// includes the dialogs controller

		$dialogs = self::getDialogs($formElements);

		if (!is_null($dialogs) > 0) {

			foreach ($dialogs as $dialog) {
				if (file_exists(XIMDEX_ROOT_PATH . '/actions/tol_wizard/resources/js/' . $dialog . '.js')) {
					$this->addJs('/actions/tol_wizard/resources/js/' . $dialog . '.js');
				}
			}
		}

		$seed = Utils::generateRandomChars(10, true, true, true);

		$values = array('form_elements' => $formElements,
					'view_folder' => XIMDEX_ROOT_PATH . '/actions/tol_wizard/template/Smarty/',
					'id_template' => $idTemplate,
					'template_name' => $templateName,
					'seed' => $seed,
					'go_method' => 'save',
					'nodeid' => $idNode,
					'actionid' => $this->request->getParam('actionid')
		);

		$this->render($values);
	}

	public function save() {

		$idTemplate = $this->request->getParam('id_template');
		$formData = $this->request->getParam('d');
		$idNode = $this->request->getParam('nodeid');
		$seed = $this->request->getParam('seed');

		if (!isset($formData[$seed])) {
			$this->messages->add(_('Form information could not be recovered'), MSG_TYPE_ERROR);
			$this->renderMessages();
		} else {
			$formData = $formData[$seed];
		}

		$rngParser = new TolFiltersRNG();
		if (!$rngParser->loadRNG($idTemplate)) {
			$this->messages->add(_('Rng could not be loaded'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}
		$content = $rngParser->getDefaultContent();

		$this->domDoc = new DOMDocument();
		$this->domDoc->validateOnParse = false;
		$this->domDoc->preserveWhiteSpace = true;
		$this->domDoc->loadXML(XmlBase::recodeSrc($content, XML::UTF8));

		$this->xpath = new DOMXPath($this->domDoc);

		if (empty($formData)) {
			$this->messages->add(_('No information has been found to create the document'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}

		$this->fillData($formData);

		$content = $this->domDoc->saveXML();
		$content = XmlBase::recodeSrc($content, Config::getValue('dataEncoding'));
		
		$ret = $this->validateDocument($idNode, $content);
		if (count($ret) > 0) {
			foreach ($ret as $error) {
				$this->messages->add(_($error), MSG_TYPE_ERROR);
			}
			$this->renderMessages();
			return;
		}
		
		$node = new Node($idNode);
		$node->setContent(TolDocumentNode::stripToldocHeader($content));
		$this->messages->add(_('Content has been successfuly saved'), MSG_TYPE_NOTICE);

		$ret = $this->publicateValidVersion($node);
		foreach ($ret as $message) {
			$this->messages->add(_($message), MSG_TYPE_NOTICE);
		}

		$this->renderMessages();
	}
	
	protected function validateDocument($idNode, $content) {

		$validator = new TolValidator($idNode, $content);
		$result = $validator->validate();
		return $result;
	}

	protected function publicateValidVersion($node) {

		$messages = array();
		$idNode = $node->get('IdNode');

		// If it is a active version it should be published when content is saved
		$rel = RelValidityVersions::getByIdNode($idNode);

		// TolId should be empty
		$tolId = $node->getProperty('tolID');

		if ($rel->getId() > 0) {

			// Now plus 10 seconds
			$up = time() + 10;
			$down = NULL;
			$flagsPublication = array(
				'markEnd' => NULL,
				'linked' => NULL,
				'structure' => '1',
				'deeplevel' => NULL,
				'force' => '1',
				'recurrence' => NULL,
				'workflow' => '1',
				'otfPublication' => NULL
			);

			$ret = SynchroFacade::pushDocInPublishingPool($idNode, $up, $down, $flagsPublication);
			if (isset($ret['ok']) && count($ret['ok']) > 0) {
				$messages[] = _('Active version has been published with its dependencies.');
			}
		}

		return $messages;
	}

	public function serverImages() {

		$idNode = $this->request->getParam('nodeid');

		$data = array(
			array(
				'nodeid' => 0,
				'label' => '< Ninguna >',
				'url' => '',
				'key' => 0
			)
		);

		$node = new Node($idNode);
		if ($node->get('IdNode') <= 0) {
			XMD_log::info(_("It has been tried to obtain a node which does not exist:") . $idNode);
			header('Content-type: application/xjson');
			echo json_encode($data);
			return;
		}

		$idServer = $node->getServer();
		$node->setId($idServer);
		$imgFolder = $node->getChildren(5016);

		if (count($imgFolder) < 1) {
			XMD_log::info(_("Image folders have not been found on server:") . $idServer);
			header('Content-type: application/xjson');
			echo json_encode($data);
			return;
		}

		// TODO: Find all image folders under the server node
		$imgFolder = new Node($imgFolder[0]);
		$path = $imgFolder->GetPathList();
		$children = $imgFolder->getChildren();

		foreach ($children as $image) {
			$node->setId($image);
			$data[] = array(
				'nodeid' => $image,
				'label' => $node->get('Name'),
				'url' => sprintf('%s/data/nodes%s/%s', Config::getValue('UrlRoot'), $path, $node->get('Name')),
				'key' => count($data)
			);
		}

		header('Content-type: application/xjson');
		echo json_encode($data);
	}

	private function fillData($data, $previousTagNames = NULL) {

		if (empty($data)) {
			return;
		}

		foreach($data as $key => $childrens) {

			$xpathExpression = sprintf('%s/%s', $previousTagNames, $key);
			$nodes = $this->xpath->query($xpathExpression);

			if (!($nodes->length > 0)) {
				break;
			}

			$domNode = $nodes->item(0);

			if (array_key_exists('_value_', $childrens)) {
				$value = $childrens['_value_'];
				unset ($childrens['_value_']);

				//if count == 1 insert content
				if ($nodes->length == 1) {
					if (in_array(strtolower(get_class($domNode)), array('domnode', 'domelement'))) {
						$value = stripslashes($value);
						$this->replaceText($domNode, $value);
					}
				}
			}

			// set multiple values

			if (array_key_exists('value', $childrens)) {

				foreach ($childrens['value']['_value_'] as $val) {

					$valueNode = $this->domDoc->createElement("value");
					$valueNode->nodeValue = $val;
					$domNode->appendChild($valueNode);
					unset($valueNode);
				}
			}
		}

		$this->fillData($childrens, $previousTagNames . '/' . $key);
	}

	private function recoverData($data, $previousTagNames = NULL) {

		if (empty($data)) {
			return NULL;
		}

		foreach($data as $key => $childrens) {
			if (!is_string($key)) {
				continue;
			}

			if (array_key_exists('children', $childrens)) {
				$data[$key]['children'] = $this->recoverData($childrens['children'], $previousTagNames . '/' . $key);
			}

			$xpathExpression = sprintf('%s/%s', $previousTagNames, $key);
			$nodes = $this->xpath->query($xpathExpression);

			$nodesLength = $nodes->length;

			//if count == 1 get content
			if ($nodesLength == 1) {
				$domNode = $nodes->item(0);

				if (in_array(strtolower(get_class($domNode)), array('domnode', 'domelement'))) {

					// check for multiple values

					if (isset($childrens['attributes']['view']) &&
						in_array($childrens['attributes']['view'], array('checkboxes'))) {

						$data[$key]['_value_'] = $this->getTextMultiple($domNode);
					} else if (isset($childrens['attributes']['view']) && ($childrens['attributes']['view'] == 'hidden' ||
						($childrens['attributes']['view'] == 'text' && isset($childrens['attributes']['editable']) &&
						 $childrens['attributes']['editable'] == 'false'))) {

						$xmlValue = $this->getText($domNode);
						$rngValue = isset($childrens['children'][0]['textnode']) ? $childrens['children'][0]['textnode'] : '';
						$data[$key]['_value_'] = empty($xmlValue) ? $rngValue : $xmlValue;
					} else {

						$data[$key]['_value_'] = $this->getText($domNode);
					}
				}
			}
		}
		return $data;
	}


	private function replaceText($domNode, $text) {

		$node = $domNode->firstChild;
		while ($node) {
		   /* only process text or element nodes here */
		   if ($node->nodeType == XML_TEXT_NODE) {
		      $node->nodeValue = self::escape($text); /* modify text content */
		      return;
		   }
		   $node = $node->nextSibling;
		}

		// Node does not contain any text to replace
		$textNode = $this->domDoc->createTextNode(self::escape($text));
		$domNode->appendChild($textNode);
	}

	private static function escape($text) {
		return XmlBase::recodeSrc($text, XML::UTF8);
	}

	private function getTextMultiple($domNode) {
		$values = array();
		$node = $domNode->firstChild;
		while ($node) {
			if (in_array(strtolower(get_class($domNode)), array('domnode', 'domelement'))) {
		      $values[] = $node->nodeValue;
		   }
		   $node = $node->nextSibling;
		}
		return $values;
	}

	private function getText($domNode) {
		$node = $domNode->firstChild;
		while ($node) {
		   /* only process text or element nodes here */
		   if ($node->nodeType == XML_TEXT_NODE) {
		      return $node->nodeValue;
		   }
		   $node = $node->nextSibling;
		}
		return NULL;
	}

	private static function setHeaderInContent($content) {
		return sprintf('<toldoc>%s</toldoc>', $content);

	}

	private function getDialogs($formElements, &$dialogs = array()) {

		foreach ($formElements as $key => $element) {

			if (!isset($element['attributes']['view']) && isset($element['children'])) {

				self::getDialogs($element['children'], $dialogs);
			}

			if (isset($element['attributes']['dialog'])) {

				$dialogs[] = $element['attributes']['dialog'];
			}
		}

		return isset($dialogs) ? $dialogs : NULL;
	}

	/**
	* queryProcessor workaround to get for dialogs
	*/

	public function queryprocessor_workaround() {

		$dialog = $this->request->getParam('dialog');

		$xml = QueryProcessor::getQueryDefinition(
			sprintf('modules/tolDOX/resources/queryDefinitions/%s.xml', $dialog)
		);

		$qp = QueryProcessor::getInstance('TOL_Forms_Datasource');
		$elements = $qp->search($xml);

		echo Serializer::encode(SZR_JSON, $elements);
		die();
	}

}
?>
