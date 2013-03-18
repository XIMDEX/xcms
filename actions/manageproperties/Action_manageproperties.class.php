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



ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/channel.inc');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');


class Action_manageproperties extends ActionAbstract {

	public function index() {

		$this->addCss('/actions/manageproperties/resources/css/styles.css');
		$this->addJs('/actions/manageproperties/resources/js/dialog.js');
		$this->addJs('/actions/manageproperties/resources/js/index.js');

		$nodeId = $this->request->getParam('nodeid');
		$nodeId = $nodeId < 10000 ? 10000 : $nodeId;

		$properties = InheritedPropertiesManager::getValues($nodeId);

		$inherit = array();
		foreach ($properties as $name=>$prop) {
			$checked = false;
			if(!empty($prop) ) {
				foreach ($prop as $value) {
					if ($value['Checked'] == 1) {
						$checked = true;
						break;
					}
				}
			}
			if ($checked) {
				$inherit[sprintf('%s_inherited', $name)] = 'overwrite';
			} else {
				$inherit[sprintf('%s_inherited', $name)] = 'inherited';
			}
		}

		$values = array(
			'properties' => $properties,
			'go_method' => 'save_changes'
		);
		$values = array_merge($values, $inherit);

		$this->render($values, '', 'default-3.0.tpl');
	}

	public function save_changes() {

		$nodeId = $this->request->getParam('nodeid');
		$nodeId = $nodeId < 10000 ? 10000 : $nodeId;
		$confirmed = $this->request->getParam('confirmed');
		$confirmed = $confirmed == 'YES' ? true : false;

		$inherited_channels = $this->request->getParam('inherited_channels');
		$channel_recursive = $this->request->getParam('Channel_recursive');
		$channel_recursive = empty($channel_recursive) ? array() : $channel_recursive;
		$channels = $this->request->getParam('Channel');
		$channels = empty($channels) || $inherited_channels == 'inherited' ? array() : $channels;

		$inherited_languages = $this->request->getParam('inherited_languages');
		$languages = $this->request->getParam('Language');
		$languages = empty($languages) || $inherited_languages == 'inherited' ? array() : $languages;

		$inherited_schemas = $this->request->getParam('inherited_schemas');
		$schemas = $this->request->getParam('Schema');
		$schemas = empty($schemas) || $inherited_schemas == 'inherited' ? array() : $schemas;

		$transformer = $this->request->getParam('Transformer');
		$transformer = empty($transformer) ? array() : $transformer;


		$properties = array(
			'Channel' => $channels,
			'Language' => $languages,
			'Schema' => $schemas,
			'Transformer' => $transformer,
		);

		$confirm = false;
		if (!$confirmed) {

			$affected = InheritedPropertiesManager::getAffectedNodes($nodeId, $properties);

			foreach ($affected as $prop=>$value) {
				if ($value !== false) {
					$confirm = true;
					break;
				}
			}
		}

		if ($confirm) {

			$this->showConfirmation($nodeId, $properties, $affected);
		} else {
			$results = InheritedPropertiesManager::setValues($nodeId, $properties);

			$applyResults = array();
			if (count($channel_recursive) > 0) {
				$applyResults = array_merge($applyResults, $this->_applyPropertyRecursively('Channel', $nodeId, $channel_recursive));
			}

			$this->showResult($nodeId, $results, $applyResults, $confirmed);
		}
	}

	protected function showConfirmation($nodeId, $properties, $affected) {

		$this->addJs('/actions/manageproperties/resources/js/dialog.js');
		$this->addJs('/actions/manageproperties/resources/js/confirm.js');

		foreach ($affected as $prop=>$value) {

			if ($value !== false) {

				$totalNodes = count($value['nodes']);
				$totalProps = count($value['props']);

				$message = '';
				switch ($prop) {
					case 'Channel':
						$message = sprintf(_('A total of %s channels are going to be disassociated from %s nodes.'), $totalProps, $totalNodes);
						break;
					case 'Language':
						$message = sprintf(_('A total of %s idiomatic versions are going to be deleted.'), $totalNodes);
						break;
				}

				$this->messages->add(_($message), MSG_TYPE_WARNING);
			}
		}

		$values = array(
			'nodeId' => $nodeId,
			'properties' => $properties,
			'messages' => $this->messages->messages
		);

		$this->render($values, 'confirm', 'default-3.0.tpl');
	}

	protected function showResult($nodeId, $results, $applyResults, $confirmed) {

		foreach ($results as $prop=>$value) {

			if ($value !== false) {

				$affectedNodes = $value['affectedNodes'];
				if ($affectedNodes !== false) $affectedNodes = $value['affectedNodes']['affectedNodes'];
				$totalProps = count($value['values']);

				$message = array();
				switch ($prop) {
					case 'Channel':

						if ($affectedNodes !== false) {
							$totalNodes = count($affectedNodes['nodes']);
							$totalProps = count($affectedNodes['props']);
							$message[] = sprintf(_('A total of %s channels have been disassociated from %s nodes.'), $totalProps, $totalNodes);
						} else {
							if ($totalProps == 0) {
								$message[] = _('Channel values will be inherited.');
							} else {
								$message[] = sprintf(_('%s Channels have been successfully assigned.'), $totalProps);
							}
						}

						if (isset($applyResults['Channel']) && $applyResults['Channel'] !== false && $applyResults['Channel']['nodes'] > 0) {
							$message[] = sprintf(
								_('A total of %s channels have been recursively associated with %s documents.'),
								count($applyResults['Channel']['values']),
								$applyResults['Channel']['nodes']
							);
						}

						break;

					case 'Language':

						if ($affectedNodes !== false) {
							$totalProps = count($affectedNodes['props']);
							$message[] = sprintf(_('A total of %s idiomatic versions have been deleted.'), $totalProps);
						} else {
							if ($totalProps == 0) {
								$message[] = _('Language values will be inherited.');
							} else {
								$message[] = sprintf(_('%s Languages have been successfully assigned.'), $totalProps);
							}
						}

						if (isset($applyResults['Language']) && $applyResults['Language'] !== false && $applyResults['Language']['nodes'] > 0) {
							$message[] = sprintf(
								_('A total of %S idiomatic versions have been recursively created.'),
								count($applyResults['Language']['values'])
							);
						}

						break;

					case 'Schema':
						if ($totalProps == 0) {
							$message[] = _('Template values will be inherited.');
						} else {
							$message[] = sprintf(_('%s Templates have been successfully assigned.'), $totalProps);
						}
						break;
					case 'Transformer':
						if(empty($value['values']) ) $value['values'] = null;
						$transformer = (is_array($value['values']) ) ? $value['values'][0] : $value['values'];
						$message[] = sprintf(_('%s will be used as document transformer.'), $transformer);
						break;
				}

				foreach ($message as $msg) {
					$this->messages->add(_($msg), MSG_TYPE_NOTICE);
				}
			}
		}

		$values = array(
			'messages' => $this->messages->messages,
			'goback' => true,
			'history_value' => $confirmed ? 2 : 1
		);
		$this->render($values);
	}

	public function applyPropertyRecursively() {

		$nodeId = $this->request->getParam('nodeid');
		$nodeId = $nodeId < 10000 ? 10000 : $nodeId;
		$property = $this->request->getParam('property');
		$values = $this->request->getParam('values');

		$result = $this->_applyPropertyRecursively($property, $nodeId, $values);
		$this->sendJSON(array('nodeId' => $nodeId, 'property'=>$property, 'result'=>$result[$property]));
	}

	protected function _applyPropertyRecursively($property, $nodeId, $values) {

		$result = InheritedPropertiesManager::applyPropertyRecursively($property, $nodeId, $values);
		return $result;
	}
}
?>
