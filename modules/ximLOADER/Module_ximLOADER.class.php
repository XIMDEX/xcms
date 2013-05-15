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





ModulesManager::file('/inc/modules/Module.class.php');
ModulesManager::file('/inc/cli/CliParser.class.php');
ModulesManager::file('/inc/cli/CliReader.class.php');
ModulesManager::file('/inc/io/BaseIO.class.php');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::file(MODULE_XIMLOADER_PATH.'/BuildParser.class.php');
ModulesManager::file('/inc/helper/DebugLog.class.php');

class Module_ximLOADER extends Module {

	private $projectName = null;
	private $project = null;
	private $templates = null;

	function Module_ximLOADER() {
		// Call Module constructor.
		parent::Module('ximLOADER', dirname(__FILE__));
	}

	function getProjects() {
		return  FsUtils::readFolder(dirname(__FILE__) . '/projects', false);
	}


	function preInstall($idProject=null) {
		if(defined("CLI_MODE") && CLI_MODE) {
			return $this->preInstallCli($idProject);
		}else {
			return $this->preInstallGUI($idProject);
		}
	}

	function preInstallGUI($idProject=null) {
		$projects = $this->getProjects();

		//Default, project 2
		$reader = 2;	
		if(!empty($idProject) )
			$reader = $idProject -1;
		else 	if(defined("XIMLOADER_DEFAULT") )
			$reader = ((int) XIMLOADER_DEFAULT - 1);
	
		$project = isset($projects[$reader]) ? $projects[$reader] : null;

		$this->projectName = $project;

		$buildFile = sprintf('%s/projects/%s/build.xml', dirname(__FILE__), $this->projectName);

		$b = new BuildParser($buildFile);
		$this->project = $b->getProject();

		return true;
	}

	function preInstallCli($idProject=null) {
		$projects = $this->getProjects();
		$sp = '';
		foreach ($projects as $i=>$p) {
			$sp .= sprintf("\t%s. %s\n", ($i+1), $p);
		}

		if(!defined("XIMLOADER_DEFAULT") && !$idProject ) {
			$reader = CliReader::getString(sprintf("\nAvailable projects:\n%s\nType the index of the project you want to install: ", $sp));
		}
		else if ($idProject && is_int($idProject)){
			$reader = $idProject;
		}
		else {
			$reader = (int) XIMLOADER_DEFAULT;
		}

		if (empty($reader)) {
			return false;
		}

		$reader = intval($reader) - 1;
		$project = isset($projects[$reader]) ? $projects[$reader] : null;

		if (empty($project)) {
			$this->log(Module::ERROR, 'The selected project doesn\'t exists.');
		}

		$this->projectName = $project;

		if(defined("XIMLOADER_DEFAULT") ) {
			echo "Installing project demo {$this->projectName}:";
		}

		$buildFile = sprintf('%s/projects/%s/build.xml', dirname(__FILE__), $this->projectName);
		$b = new BuildParser($buildFile);
		$this->project = $b->getProject();

		return true;
	}

	function install($idProject=null) {

		if (!$this->preInstall($idProject)) {
			return false;
		}

		if ($this->insertProject() === false) {
			$this->log(Module::ERROR, 'Error while inserting project');
			return false;
		}

		
		return true;
	}

	function getPath($folder) {
		return sprintf('%s/%s/%s/%s', dirname(__FILE__), 'projects', $this->project->name, $folder);
	}

	function getChannel() {
		$channels = Channel::GetAllChannels();
		if (is_null($channels)) {
			return false;
		}
		$channelId = $channels[0];
		$this->log(Module::SUCCESS, "Using channel with ID " . $channelId);
		return $channelId;
	}

	function getLanguage() {
		$language = new Language();
		$langs = $language->GetList();
		if (is_null($langs)) {
			return false;
		}
		$langId = $langs[0];
		$this->log(Module::SUCCESS, "Using language with ID " . $langId);
		return $langId;
	}

	function deleteProject($projectName) {

		$projects = new Node(10000);
		$projectid = $projects->GetChildByName($projectName);

		if (!($projectid > 0)) {
			return true;
		}

		$reader = false;
		while (!$reader) {
			$reader = CliReader::getString(sprintf("\nThe project %s already exists, Rename, Delete or Cancel? [R/D/C]: ", $projectName));
			$reader = empty($reader) ? 'R' : $reader;
			$reader = in_array(strtoupper($reader), array('R', 'D', 'C')) ? strtoupper($reader) : false;
		}


		if ($reader == 'C') {
			return false;
		}

		if ($reader == 'R') {

			$projects = new Node(10000);
			$ret = false;
			do {
				$projectid = $projects->GetChildByName($this->projectName);

				if (!($projectid > 0)) {
					$ret = true;
				} else {
					$reader = CliReader::getString(sprintf("\nWrite a new name for the project: ", $this->projectName));
					if (strlen($reader) > 0) $this->projectName = $reader;
				}

			} while ($ret === false);

			return true;
		}

		if ($reader == 'D') {

			$data = array('ID' => $projectid);
			$io = new BaseIO();
			$ret = $io->delete($data);

			if ($ret != 1) {
				$this->log(Module::ERROR, "Can't delete project $projectName ($ret)");
				return false;
			}
		}

		return true;
	}

	function insertProject() {

		if(defined("CLI_MODE") && CLI_MODE) {
			$insert = $this->insertProjectCLI();
		}else {
			$insert =  $this->insertProjectGUI();
		}

		if(!$insert) return false;


		$nodeType = new NodeType();
		$nodeType->SetByName($this->project->nodetypename);
		$idNodeType = ($nodeType->get('IdNodeType') > 0) ? $nodeType->get('IdNodeType') : NULL;


		$data = array(
			'NODETYPENAME' => $this->project->nodetypename,
			'NAME' => $this->projectName,
			'NODETYPE' => $idNodeType,
			'PARENTID' => 10000
		);

		$io = new BaseIO();
		$projectId = $io->build($data);
		if ($projectId < 1) {
			return false;
		}

		$project = new Node($projectId);
		$this->project->projectid = $projectId;

		$channel = $this->project->channel;
		$channel = $channel == '{?}' ? $this->getChannel() : $channel;
		$this->project->channel = $channel;

		$lang = $this->project->language;
		$lang = $lang == '{?}' ? $this->getLanguage() : $lang;
		$this->project->language = $lang;

		$project->setProperty('Transformer', $this->project->Transformer);
		$project->setProperty('channel', $this->project->channel);
		$project->setProperty('language', $this->project->lang);

		$this->log(Module::SUCCESS, "Project creation O.K.");

		// TODO: ximlink
		$links = $this->project->getXimlink();
		$this->templates = $this->insertFiles($this->project->projectid, 'ximlink', $links);

		// RNGs
		$pvds = $this->project->getPVD('RNG');
		$this->templates = $this->insertFiles($this->project->projectid, 'ximpvd', $pvds);

		// Update XSL
		$xsls = $this->project->getPTD('XSL');
		$this->insertFiles($this->project->projectid, 'ximptd', $xsls); 
		$ret = $this->updateXsl($this->project->projectid, $xsls);

		// Servers
		$servers = $this->project->getServers();
		foreach ($servers as $server) {
			$this->insertServer($server);
		}

		return $projectId;
	}

	function insertProjectGUI() {
		$i = 1;
		$projectid = 0;
		//Find unique name
		$projectName = $this->projectName;
		$project_choose = "";
		do {
			$i++;

			$projects = new Node(10000);
			$projectid = $projects->GetChildByName($projectName );
			$project_choose  = $projectName;
			$projectName  = $this->projectName.$i;

		}while (!empty($projectid));
		$this->projectName = $project_choose;

		return true;
	}

	function insertProjectCLI() {
		if (!$this->deleteProject($this->projectName)) {
			$this->log(Module::WARNING, $this->projectName . " already exists, aborting installation");
			return false;
		}
		return true;
	}

	function insertServer($server) {

		$nodeType = new NodeType();
		$nodeType->SetByName($server->nodetypename);
		$idNodeType = ($nodeType->get('IdNodeType') > 0) ? $nodeType->get('IdNodeType') : NULL;

		$data = array(
			'NODETYPENAME' => $server->nodetypename,
			'NAME' => $server->name,
			'NODETYPE' => $idNodeType,
			'PARENTID' => $this->project->projectid
		);

		$io = new BaseIO();
		$serverId = $io->build($data);
		if ($serverId < 1) {
			return false;
		}

		$server->serverid = $serverId;
		$server->url = preg_replace('/\{URL_ROOT\}/', Config::GetValue('UrlRoot'), $server->url);
		$server->initialDirectory = preg_replace('/\{XIMDEX_ROOT_PATH\}/', XIMDEX_ROOT_PATH, $server->initialDirectory);

		$nodeServer = new Node($serverId);
		$physicalServerId = $nodeServer->class->AddPhysicalServer(
			$server->protocol, $server->login, $server->password, $server->host, $server->port,
			$server->url, $server->initialDirectory, $server->overrideLocalPaths, $server->enabled,
			$server->previsual, $server->description, $server->isServerOTF
		);

		$nodeServer->class->AddChannel($physicalServerId, $this->project->channel);
		$this->log(Module::SUCCESS, "Server creation O.K.");


		// common
		$common = $server->getCommon();
		$ret = $this->insertFiles($server->serverid, 'common', $common);

		// ximclude
		$clude = $server->getXimclude();
		$ret = $this->insertFiles($server->serverid, 'ximclude', $clude);

		// ximlet
		$let = $server->getXimlet();
		$ret = $this->insertFiles($server->serverid, 'ximlet', $let);

		// CSSs
		$css = $server->getCSS();
		$ret = $this->insertFiles($server->serverid, 'css', $css);

		// images
		$img = $server->getImages();
		$ret = $this->insertFiles($server->serverid, 'images', $img);

		// Update XSL
		$xsls = $server->getPTD('XSL');
		$ret = $this->insertFiles($server->serverid, 'ximptd', $xsls); 
		$ret = $this->updateXsl($server->serverid, $xsls);

		// ximdoc
		$docs = $server->getXimdocs();
		$ret = $this->insertDocs($server->serverid, $docs);

		return $serverId;
	}

	function insertDocs($parentId, $files) {

		$xFolderName = 'ximdoc';
		$nodeTypeName = 'XMLDOCUMENT';
		$ret = array();
		if (count($files) == 0) return $ret;

		$project = new Node($parentId);
		$xFolderId = $project->GetChildByName($xFolderName);

		if (empty($xFolderId)) {
			$this->log(Module::ERROR, $xFolderName .' folder not found');
			return false;
		}

		$nodeType = new NodeType();
		$nodeType->SetByName($nodeTypeName);
		$idNodeType = $nodeType->get('IdNodeType') > 0 ? $nodeType->get('IdNodeType') : NULL;

		$io = new BaseIO();

		foreach ($files as $file) {
			echo ".";
			$templateId = $this->templates[$file->templatename];
			$file->channel = $file->channel == '{?}' ? $this->project->channel : $file->channel;
			$file->language = $file->language == '{?}' ? $this->project->language : $file->language;

			$data = array(
				'NODETYPENAME' => 'XMLCONTAINER',
				'NAME' => $file->name,
				'PARENTID' => $xFolderId,
				'CHILDRENS' => array(
					array(
						'NODETYPENAME' => 'VISUALTEMPLATE',
						'ID' => $templateId
					)
				)
			);

			$containerId = $io->build($data);

			if (!($containerId > 0)) {
				$this->log(Module::ERROR, "ximdoc container ".$file->name." couldn't be created ($containerId)");
				continue;
			}

			$data = array(
				'NODETYPENAME' => 'XMLDOCUMENT',
				'NAME' => $file->name,
				'NODETYPE' => $idNodeType,
				'PARENTID' => $containerId,
				'CHILDRENS' => array(
					array('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $templateId),
					array('NODETYPENAME' => 'CHANNEL', 'ID' => $file->channel),
					array('NODETYPENAME' => 'LANGUAGE', 'ID' => $file->language),
					array('NODETYPENAME' => 'PATH', 'SRC' => $file->getPath())
				)
			);

			$docId = $io->build($data);
			if ($docId > 0) {
				$ret[$file->filename] = $docId;
				$this->log(Module::SUCCESS, "Importing " . $file->name);
			} else {
//				debug::log($project, $file, $data);
				$this->log(Module::ERROR, "ximdoc document ".$file->name." couldn't be created ($docId)");
			}
		}

		if (count($ret) == 0) $ret = false;
		return $ret;
	}

	function insertFiles($parentId, $xFolderName, $files) {

		$ret = array();
		if (count($files) == 0) return $ret;

		$project = new Node($parentId);
		$xFolderId = $project->GetChildByName($xFolderName);

		if (empty($xFolderId)) {
			$this->log(Module::ERROR, $xFolderName .' folder not found');
			return false;
		}


		$io = new BaseIO();

		foreach ($files as $file) {
			echo ".";
			$nodeType = new NodeType();
			$nodeType->SetByName($file->nodetypename);
			$idNodeType = $nodeType->get('IdNodeType') > 0 ? $nodeType->get('IdNodeType') : NULL;


			$data = array(
				'NODETYPENAME' => $file->nodetypename,
				'NAME' => $file->basename,
				'NODETYPE' => $idNodeType,
				'PARENTID' => $xFolderId,
				'CHILDRENS' => array(
					array(
						'NODETYPENAME' => 'PATH',
						'SRC' => $file->path
					)
				)
			);

			$id = $io->build($data);
			if ($id > 0) {
				$ret[$file->filename] = $id;
				$this->log(Module::SUCCESS, "Importing " . $file->basename);
			} else {
				$this->log(Module::ERROR, "Error ($id) importing " . $file->basename);
				$this->log(Module::ERROR, print_r($io->messages->messages, true));
			}
		}

		if (count($ret) == 0) $ret = false;
		return $ret;
	}

	function updateXsl($parentId, $files) {

		if (count($files) == 0) return false;

		$project = new Node($parentId);
		$ptdFolderId = $project->GetChildByName('ximptd');

		$nodePtds = new Node($ptdFolderId);
		if (empty($ptdFolderId)) {
			$this->log(Module::ERROR, 'Ptd folder not found');
			return false;
		}

		$nodeType = new NodeType();
		$nodeType->SetByName('XSLTEMPLATE');
		$idNodeType = ($nodeType->get('IdNodeType') > 0) ? $nodeType->get('IdNodeType') : NULL;


		$node = new Node($ptdFolderId);
		$io = new BaseIO();

		$ximdexUrl = Config::getValue('UrlRoot');
		$projectUrl = Config::getValue('UrlRoot') . '/data/nodes/' . $this->projectName;
		$servers = $this->project->getServers();
		$serverUrl = $projectUrl . '/' . $servers[0]->name;

		foreach ($files as $file) {
			echo ".";
			$content = $file->getContent();

			if (preg_match('/\{URL_ROOT\}/', $content)) {
				$content = preg_replace('/\{URL_ROOT\}/', $ximdexUrl, $content);
			}
			if (preg_match('/\{URL_PROJECT\}/', $content)) {
				$content = preg_replace('/\{URL_PROJECT\}/', $projectUrl, $content);
			}
			if (preg_match('/\{URL_SERVER\}/', $content)) {
				$content = preg_replace('/\{URL_SERVER\}/', $serverUrl, $content);
			}

			$children = $nodePtds->GetChildByName($file->basename);
			$ch = new Node($children);
			if (!($ch->get('IdNode') > 0)) {
				$this->log(Module::ERROR, "Updated xsl not O.K. Cannot find the file ".$file->basename);
				continue;
			}

			$result = $ch->setContent($content);

			if (!$result) {
				$this->log(Module::SUCCESS, "Updated xsl O.K. ".$file->basename);
			} else {
				$this->log(Module::ERROR, "Updated xsl not O.K. ".$file->basename);
			}
		}
	}


	function uninstall() {

	}

	/**
	 * return array with install params
	 *
	 */
	function getInstallParams() { 

		return array(
			"projects" => array(
									"label" => "Choose a project",
									"list" => $this->getProjects() 
							)
		); 
	}
}

?>
