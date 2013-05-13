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



ModulesManager::file('/inc/model/node.inc');
ModulesManager::file('/inc/cli/Shell.class.php');
ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/channel.inc');
ModulesManager::file('/inc/model/group.inc');


class Action_console_wrapper extends ActionAbstract {
	public function index() {
//        $query = App::get('QueryManager');
//        $action = $query->getPage() . $query->build();
		$values = array(
			'id_action' => $this->request->get('actionid'),
//			'action' => $action
		);

		$this->render($values);
	}

	public function export($getParams = false) {
		$values = array(
				'id_action' => $this->request->get('actionid'),
				'go_method' => 'confirmExport'
		);

		if ($getParams) {
			return $values;
		}
		$this->render($values);
		return NULL;
	}

	public function confirmExport($getCommand = false) {
		$idNode = $this->request->getParam('nodes');
		$file = $this->request->getParam('file');

		$node = new Node($idNode);

		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('Specified node is not correct'), MSG_TYPE_ERROR);
		}

		if (!empty($file) && !(preg_match('/[a-zA-Z0-9\-\_]+/', $file) > 0)) {
			$this->messages->add(_('Specified name to export should meet the following pattern').' [a-zA-Z0-9\-\_]+', MSG_TYPE_ERROR);
		}

		if ($this->messages->count(MSG_TYPE_ERROR) > 0) {
			$this->render(array_merge($this->export(true),
				array('messages' => $this->messages->messages)
			), 'export', 'default.tpl');
			return;
		}

		$command = sprintf('php %s'.ModulesManager::path('ximIO').'/actions/run.php --mode export --nodes %s --test 1',
			XIMDEX_ROOT_PATH, $idNode);
		if (!empty($file)) {
			$command .= sprintf(' --file %s', $file);
		}

		if ($getCommand) {
			return $command;
		}
		$commandExecuter = new Shell('');
		$messages = $commandExecuter->exec($command);
		foreach ($messages as $message) {
			$this->messages->add($message, MSG_TYPE_NOTICE);
		}

        $values = array('messages' => $this->messages->messages,
        	'go_method' => 'executeExport',
        	'nodes' => $idNode,
        	'file' => $file,
        	'recursive' => $recursive);

		$this->render($values, 'confirmExport', 'default.tpl');
		return NULL;
	}

	function executeExport() {
		$command = $this->confirmExport(true);
		$command = str_replace(' --test 1', ' --no-require-confirm 1', $command);

		$commandExecuter = new Shell('');
		$messages = $commandExecuter->exec($command);
		$this->messages->add(_('It has been executed the command: ') . $command, MSG_TYPE_NOTICE);
		foreach ($messages as $message) {
			$this->messages->add($message, MSG_TYPE_NOTICE);
		}

		$this->render(array('messages' => $this->messages->messages));
	}

	public function import() {

		$ximIOFolder = XIMDEX_ROOT_PATH . '/data/backup/';
		$blackList = array('.', '..');
		$dirh = opendir($ximIOFolder);
		$backups = array();
		if ($dirh) {
			while (($dir_element = readdir($dirh)) !== false) {
				if (!is_dir($ximIOFolder . $dir_element)) {
					continue;
				}
				if (in_array($dir_element, $blackList)) {
					continue;
				}
				$matches = array();
				if (!preg_match('/(.*)_ximio/', $dir_element, $matches) > 0) {
					continue;
				}

				$backups[] = $matches[1];
			}
			unset($dir_element);
			closedir($dirh);
		}
		$values = array(
			'backups' => $backups,
			'go_method' => 'getImportValues'
		);

		$this->render($values);
	}

	public function getImportValues() {
		$backup = $this->request->getParam('backup');

		$idAction = $this->request->getParam('actionid');

		$xml = new XmlReader();
		$ximIOFolder = XIMDEX_ROOT_PATH . '/data/backup/';

		$xml->open($ximIOFolder . $backup . '_ximio/ximio.xml');
		$whiteList = array('ximio-structure', 'ChannelManager', 'Channel', 'LanguageManager', 'Language', 'GroupManager', 'Group');
		$stripList = array('Channel', 'Language', 'Group');
		$importedValues = array();
		while ($xml->read()) {
			if ($xml->nodeType != XMLReader::ELEMENT) {
				continue;
			}

			if (!in_array($xml->localName, $whiteList)) {
				break;
			}

			if (in_array($xml->localName, $stripList)) {
				$importedValues[$xml->localName][] = array('id' => $xml->getAttribute('id'), 'name' => $xml->getAttribute('name'));
			}
		}
		$language = new Language();
		$allLanguages = $language->find('IdLanguage, Name');

		$channel = new Channel();
		$allChannels = $channel->find('IdChannel, Name');

		$group = new Group();
		$allGroups = $group->find('IdGroup, Name');

		$values = array(
			'languages' => $allLanguages,
			'channels' => $allChannels,
			'groups' => $allGroups,
			'imported_channels' => $importedValues['Channel'],
			'imported_languages' => $importedValues['Language'],
			'imported_groups' => $importedValues['Group'],
			'id_action' => $idAction,
			'go_method' => 'confirmImport'
		);

		$this->render($values);
	}

	public function confirmImport() {

		$idAction = $this->request->getParam('actionid');
		$node = $this->request->getParam('node');
		$file = $this->request->getParam('file');
		$user = $this->request->getParam('user');
		$password = $this->request->getParam('password');
		$processFirstNode = $this->request->getParam('processFirstNode');
		$copyMode = $this->request->getParam('copyMode');
		$channel = $this->request->getParam('channel');
		$language = $this->request->getParam('language');
		$group = $this->request->getParam('group');

		$command = sprintf('php %s'.ModulesManager::path('ximIO').'/actions/run.php --mode import --interfaceWeb 1',
			XIMDEX_ROOT_PATH);

		if (!empty($node) && is_numeric($node)) {
			$command .= ' --node ' . $node;
		}

		if (!empty($file)) {
			$command .= ' --file ' . $file;
		}

		if (!empty($user)) {
			$command .= ' --user ' . $user;
		}

		if (!empty($password)) {
			$command .= ' --password ' . $password;
		}

		if (!empty($processFirstNode) && $processFirstNode == 'on') {
			$command .= ' --processFirstNode 1';
		}

		if (!empty($copyMode) && $copyMode == 'on') {
			$command .= ' --copyMode 1';
		}

		$associations = array();
		if (!empty($channel) && is_array($channel)) {
			foreach ($channel as $key => $value) {
				$associations[] = sprintf('%s=%s', $key, $value);
			}
		}

		if (!empty($language) && is_array($language)) {
			foreach ($language as $key => $value) {
				$associations[] = sprintf('%s=%s', $key, $value);
			}
		}

		if (!empty($group) && is_array($group)) {
			foreach ($group as $key => $value) {
				$associations[] = sprintf('%s=%s', $key, $value);
			}
		}

		if (!empty($associations) && !(!empty($copyMode) && $copyMode == 'on')) {
			$command .= ' --associations ' . implode(',', $associations);
		}
		$commandExecuter = new Shell('');

		$messages = $commandExecuter->exec($command);
		foreach ($messages as $message) {
			$this->messages->add($message, MSG_TYPE_NOTICE);
		}

		$values = array(
			'command' => $command,
			'id_action' => $idAction,
			'go_method' => 'executeImport',
			'messages' => $this->messages->messages
		);

		$this->render($values, 'executeImport', 'default.tpl');
	}

	public function executeImport() {
		$command = $this->request->getParam('command');
		$command = str_replace('interfaceWeb', 'interfaceWebRun', $command);
		$commandExecuter = new Shell('');

		$messages = $commandExecuter->exec($command);
		foreach ($messages as $message) {
			$this->messages->add($message, MSG_TYPE_NOTICE);
		}
		$this->messages->add(_('It has been executed the command: ') . $command, MSG_TYPE_NOTICE);
		$this->render(array('messages' => $this->messages->messages));
	}

	public function file_import() {

        $db = new DB();
        $query = "SELECT timeStamp FROM XimIOExportations";
        $db->query($query);
        $exportations = array();
        while (!$db->EOF) {
        	$exportations[] = $db->getValue('timeStamp');
        	$db->next();
        }
		$values = array(
			'backups' => $exportations,
			'go_method' => 'executeFileImport'
		);

		$this->render($values, 'import.tpl', 'default.tpl');

	}

	public function executeFileImport() {
		$backup = $this->request->getParam('backup');
		$command = sprintf('php %s'.ModulesManager::path('ximIO').'/actions/run.php --mode file_import',
			XIMDEX_ROOT_PATH);

		$command .= ' --file ' . $backup;
		$commandExecuter = new Shell('');

		$messages = $commandExecuter->exec($command);
		$this->messages->add(_('It has been executed the command: ') . $command, MSG_TYPE_NOTICE);
		foreach ($messages as $message) {
			$this->messages->add($message, MSG_TYPE_NOTICE);
		}

		$values = array('messages' => $this->messages->messages);

		$this->render($values);

	}
}
?>
