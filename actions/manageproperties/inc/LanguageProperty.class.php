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




ModulesManager::file('/actions/manageproperties/inc/InheritableProperty.class.php');
ModulesManager::file('/inc/model/language.inc');

class LanguageProperty extends InheritableProperty {

	public function getPropertyName() {
		return 'language';
	}

	public function getValues() {

		// Selected languages on the node
		$nodeLanguages = $this->getProperty(false);
		if (empty($nodeLanguages)) $nodeLanguages = array();

		$language = new Language();

		// The Project node shows all the system languages
		$availableLanguages = $language->find('IdLanguage, Name', 'Enabled = 1', NULL);

		if ($this->nodeTypeId != 5013) {

			// Nodes below the Project shows only inherited languages
			$parentId = $this->node->getParent();
			$parent = new Node($parentId);
			$inheritedLanguages = $parent->getProperty($this->getPropertyName(), true);

			if (empty($inheritedLanguages)) {

				// Inherits all the system properties
				$inheritedLanguages = $availableLanguages;
			} else {

				$availableLanguages = $language->find(
					'IdLanguage, Name', 'Enabled = 1 and IdLanguage in (%s)',
					array(implode(', ', $inheritedLanguages)),
					MULTI, false
				);
			}
		}

		$availableLanguages = null;
		if(!empty($availableLanguages) ) {
			foreach ($availableLanguages as &$language) {
				unset($language[0], $language[1]);
				$language['Checked'] = in_array($language['IdLanguage'], $nodeLanguages) ? true : false;
			}
		}

		return $availableLanguages;
	}

	public function setValues($values) {

		if (!is_array($values)) $values = array();

		$affected = $this->updateAffectedNodes($values);
		$this->deleteProperty($values);

		if (is_array($values) && count($values) > 0) {

			$this->setProperty($values);
		}

		return array('affectedNodes' => $affected, 'values' => $values);
	}

	public function getAffectedNodes($values) {

		$languagesToDelete = $this->getAffectedProperties($values);
		$strLanguages = implode(', ', $languagesToDelete);

		if (count($values) == 0 || count($languagesToDelete) == 0) {
			// Inherits all the languages or there are languages to delete
			return false;
		}

		$sql = 'select distinct(s.IdDoc) as affectedNodes
				from FastTraverse f join StructuredDocuments s on f.IdChild = s.IdDoc
				where f.IdNode = %s and s.IdLanguage in (%s)';

		$sqlAffectedNodes = sprintf(
			$sql,
			$this->nodeId,
			$strLanguages
		);
//		debug::log($sqlAffectedNodes);

		// Language versions to delete
		$affectedNodes = array();
		$db = new DB();
		$db->query($sqlAffectedNodes);
		while (!$db->EOF) {
			$affectedNodes[] = $db->getValue('affectedNodes');
			$db->next();
		}

		if (count($affectedNodes) == 0) return false;

		return array('nodes' => $affectedNodes, 'props' => $languagesToDelete);
	}

	protected function updateAffectedNodes($values) {

		$affectedNodes = $this->getAffectedNodes($values);
		if (!$affectedNodes) return false;

		$messages = array();

		foreach ($affectedNodes['nodes'] as $nodeId) {

			$node = new Node($nodeId);
			$nodeType = new NodeType($node->get('IdNodeType'));

			$data = array(
				'ID' => $nodeId,
				'NODETYPENAME' => $nodeType->get('Name')
			);

			$baseIO = new baseIO();
			$result = $baseIO->delete($data);
			if (!($result > 0)) {
				$messages = $baseIO->messages->messages;
				foreach ($messages as $message) {
					XMD_Log::error($message['message']);
				}
			}
		}

		return array('affectedNodes' => $affectedNodes, 'messages' => $messages);
	}

	public function applyPropertyRecursively($values) {

		// Ooops! we need the template ID for language creation
		return false;
	}
}
