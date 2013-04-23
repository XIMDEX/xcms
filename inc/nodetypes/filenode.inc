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










if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

include_once (XIMDEX_ROOT_PATH . "/inc/utils.inc");
include_once (XIMDEX_ROOT_PATH . "/inc/persistence/datafactory.inc");
include_once (XIMDEX_ROOT_PATH . "/inc/persistence/Config.class.php");
include_once (XIMDEX_ROOT_PATH . "/actions/fileupload/baseIO.php");
include_once (XIMDEX_ROOT_PATH . "/actions/workflow_forward/baseIO.php");
require_once (XIMDEX_ROOT_PATH . "/actions/workflow_forward/baseIO.php");
include_once (XIMDEX_ROOT_PATH . "/inc/fsutils/FsUtils.class.php");
require_once (XIMDEX_ROOT_PATH . "/inc/model/RelTemplateContainer.class.php");
require_once (XIMDEX_ROOT_PATH . "/inc/model/NodeDependencies.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/dependencies/DepsManager.class.php');

ModulesManager::file('/inc/CacheWriter.class.php', 'ximRAM');

/**
*  @brief Handles files.
*
*  Files are located in data/files directory.
*  Its versions are stored in the Versions table who is handle by the class DataFactory.
*  Also a file copy is stored in data/nodes directory.
*/

class FileNode extends Root {

	/**
	*  Creates a file in data/nodes directory.
	*  @return unknown
	*/

	function RenderizeNode() {

		$parentID = $this->parent->GetParent();
		$parent = new Node($parentID);

		if(!$parent->IsRenderized()) {
			$parent->RenderizeNode();
		}

		$file = $this->GetNodePath();

		$data = new DataFactory($this->nodeID);
		$content = $data->GetContent();

		/// Si existia lo borramos
		if(file_exists($file))
			FsUtils::delete($file);


		/// Y lo volvemos a crear
		FsUtils::file_put_contents($file, $content);
	}

	/**
	*  Wrapper for GetContent.
	*  @param int channel
	*  @param string content
	*  @return string
	*/

	function getRenderizedContent($channel = NULL, $content = NULL) {

		return $this->GetContent();
	}

	/**
	*  Adds a row to Versions table and creates the file.
	*  @param string name
	*  @param int parentID
	*  @param int nodeTypeID
	*  @param int stateID
	*  @param string sourcePath
	*  @return unknown
	*/

	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $sourcePath = null) {

		$content = FsUtils::file_get_contents($sourcePath);
		$data = new DataFactory($this->parent->get('IdNode'));
		$ret = $data->SetContent($content);

		$this->updatePath();

		return $ret;
	}

	/**
	*  Stores a content on the file located.
	*  @param string content
	*  @return string
	*/

	function SetContent($content, $commitNode = NULL) {

		$data = new DataFactory($this->nodeID);

		/// @todo: move this piece to Template nodetype
		// Not neccesary up version here for PTD nodetypes (makes previously for insert correct idversion in xml wrapper)

		if ($this->parent->nodeType->get('Name') == 'Template') {
			$lastVersionID = $data->GetLastVersionId();
			list($version, $subversion) = $data->GetVersionAndSubVersion($lastVersionID);
			$data->SetContent($content, $version, $subversion, $commitNode);
		} else {
			$data->SetContent($content, NULL, NULL, $commitNode);
		}

		if (($this->parent->nodeType->get('Name') == 'VisualTemplate')
			&& ModulesManager::isEnabled('ximRAM')) {
			CacheWriter::writeCache($this->nodeID, $content);
		}

		if ($this->parent->nodeType->get('Name') == 'CssFile')  {
			$dependencies = new ParsingDependences();
			$dependencies->parseCssDependencies($this->nodeID, $content);
		}


	}

	/**
	*  Gets the content of the file.
	*  @return string
	*/

	function GetContent() {

		$data = new DataFactory($this->nodeID);
		$content = $data->GetContent();

		return $content;
	}

	/**
	*  Gets the nodes that must be published together with the file.
	*  @return array
	*/

	function GetDependencies() {

		$nodeDependencies = new NodeDependencies();
		return $nodeDependencies->getByTarget($this->nodeID);
	}

	/**
	*  Builds a XML wich contains the properties of the file.
	*  @param int depth
	*  @param array files
	*  @param bool recurrence
	*/

	function ToXml($depth, & $files, $recurrence) {

		$query = sprintf("SELECT File FROM `Versions` WHERE idNode = %d ORDER BY Version DESC, SubVersion DESC LIMIT 1",
					$this->parent->get('IdNode'));
		$this->dbObj->Query($query);

		if (!$this->dbObj->numRows > 0) {
			XMD_Log::error("***************** Version de archivo no encontrada -->" . $this->parent->get('IdNode'));
		} else {
			$nodeFile = $this->dbObj->GetValue('File');

			$routeToFile = sprintf("%s/data/files/%s", XIMDEX_ROOT_PATH, $nodeFile);
			if (!in_array($routeToFile, $files)) $files[] = $routeToFile;

			$indexTabs = str_repeat("\t", $depth + 1);
			return sprintf("%s<path src=\"%s\" />\n", $indexTabs, $routeToFile);
		}
	}

	/**
	*  Promotes the File to the next workflow state.
	*  @param string newState
	*  @return bool
	*/

	function promoteToWorkFlowState($newState) {

		$state = new State();
		$idState = $state->loadByName($newState);

		$idActualState = $this->parent->GetState();

		if ($idState == $idActualState) {
			XMD_Log::warning('Se ha solicitado pasar a un estado y ya nos encontramos en ese estado');
			return true;
		}

		$actualState = new State($idActualState);

		baseIO_CambiarEstado($this->nodeID, $idState);
		$lastState = new State();
		$idLastState = $lastState->loadLastState();
		if ($idState == $idLastState) {
			$up = time();
			$down = $up + 36000000; // fecha de despublicacion up+1 year
			baseIO_PublicarDocumento($this->nodeID, $up, $down,null);
		}
	}

	/**
	*  Deletes the dependencies of file.
	*  @return bool
	*/

	function DeleteNode() {

		/// @todo: move this piece to Visualtemplate nodetype

		if (strtoupper($this->parent->nodeType->get('Name')) == 'VISUALTEMPLATE') {
			$reltc = new RelTemplateContainer();
			$reltc->deleteRelByTemplate($this->parent->get('IdNode'));
		}

		// Deletes dependencies in rel tables

		$depsMngr = new DepsManager();
		$depsMngr->deleteByTarget(DepsManager::STRDOC_NODE, $this->parent->get('IdNode'));

		XMD_Log::info('Filenode dependencies deleted');

		return true;
	}

	/**
	*	Gets the documents that must be publicated together with the file
	*	@param array $params
	*	@return null
	*/

	function getPublishabledDeps($params) {

		if ($this->parent->nodeType->get('Name') == 'CssFile')  {
			$depsMngr = new DepsManager();
			$dependencies = $depsMngr->getBySource(DepsManager::STRDOC_NODE, $this->nodeID);
			return $dependencies;
		}
		return NULL;
	}

	function getLastVersionFile() {

		$data = new DataFactory($this->nodeID);
		$idVersion = $data->GetLastVersionId();

		$version = new Version($idVersion);
		return $version->get('File');
	}

	function UpdatePath() {
		$node = new Node($this->nodeID);
		$path = pathinfo($node->GetPath());
		$db = new DB();
		$db->execute(sprintf("update Nodes set Path = '%s' where IdNode = %s", $path['dirname'], $this->nodeID));
	}

	function RenameNode($name=null) {
		$this->updatePath();
	}
}
?>
