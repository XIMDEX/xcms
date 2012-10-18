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



require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/node.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/channel.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/Server.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/PAS_Conector.class.php', 'ximPAS');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_FilterMacrosPreview extends Abstract_View implements Interface_View {

	private $_node = NULL;
	private $_server = NULL;
	private $_serverNode = NULL;
	private $_projectNode = NULL;
	private $_idChannel;
	private $_isPreviewServer = false;
	private $_depth = NULL;
	private $_idSection = NULL;
	private $_nodeName = "";
	private $_nodeTypeName = NULL;

	private $mode = NULL;

	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {

		$content = $this->retrieveContent($pointer);
		$this->mode = (isset($args['MODE']) && $args['MODE'] == 'dinamic') ? 'dinamic' : 'static';

		if(!$this->_setNode($idVersion,$args))
			return NULL;

		if(!$this->_setIdChannel($args))
			return NULL;

		if(!$this->_setServer($args))
			return NULL;

		if(!$this->_setServerNode($args))
			return NULL;

		if(!$this->_setIdSection($args))
			return NULL;

		if(!$this->_setProjectNode($args))
			return NULL;

		if(!$this->_setDepth($args))
			return NULL;

		if(!$this->_setNodeName($args))
			return NULL;

		$serverName = $this->_serverNode->get('Name');
		$content = preg_replace("/@@@RMximdex\.servername\(\)@@@/", $serverName, $content);

		if (preg_match("/@@@RMximdex\.projectname\(\)@@@/", $content)) {
			$project = new Node($this->_ProjectNode);
			$projectName = $project->get('Name');
			$content = preg_replace("/@@@RMximdex\.projectname\(\)@@@/", $projectName, $content);
		}

		$content = preg_replace("/@@@RMximdex\.nodename\(\)@@@/", $this->_nodeName, $content);

		$content = preg_replace_callback("/@@@RMximdex\.sectionpath\(([0-9]+)\)@@@/",
			array($this, 'getSectionPath'), $content);

		$content = preg_replace_callback("/@@@RMximdex\.dotdot\(([^\)]*)\)@@@/",
			array($this, 'getdotdotpath'), $content);

		// Posiblemente estas dos expresiones regulares se puedan convertir en una.
		$content = preg_replace_callback("/@@@RMximdex\.pathto\(([0-9]+),([0-9]*)\)@@@/",
			array($this, 'getLinkPath'), $content);

		$content = preg_replace_callback("/@@@RMximdex\.pathto\(([0-9]+)\)@@@/",
			array($this, 'getLinkPath'), $content);

		$content = preg_replace_callback("/@@@RMximdex\.rdf\(([^\)]+)\)@@@/",
			array($this, 'getRDFByNodeId'), $content);

		$content = preg_replace_callback("/@@@RMximdex\.rdfa\(([^\)]+)\)@@@/",
			array($this, 'getRDFaByNodeId'), $content);

		return $this->storeTmpContent($content);
	}

	private function _setNode ($idVersion = NULL,$args = NULL) {

		if(!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				XMD_Log::error('VIEW FILTERMACROSPREVIEW: Se ha cargado una versión incorrecta (' . $idVersion . ')');
				return NULL;
			}

			$this->_node = new Node($version->get('IdNode'));
			if (!($this->_node->get('IdNode') > 0)) {
				XMD_Log::error('VIEW FILTERMACROSPREVIEW: El nodo que se está intentando convertir no existe: ' . $version->get('IdNode'));
				return NULL;
			}
		}else{
			if (array_key_exists('NODETYPENAME', $args)) {
				$this->_nodeTypeName = $args['NODETYPENAME'];
			}
		}

		return true;
	}

	private function _setIdChannel ($args = array()) {

		if (array_key_exists('CHANNEL', $args)) {
			$this->_idChannel = $args['CHANNEL'];
		}

		// Check Params:
		if (!isset($this->_idChannel) || !($this->_idChannel > 0)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: No se ha especificado el canal del nodo ' . $args['NODENAME'] . ' que quiere renderizar');
			return NULL;
		}

		return true;
	}

	private function _setServer ($args = array()) {

		if (array_key_exists('SERVER', $args)) {
			$this->_server = new Server($args['SERVER']);
			if (!($this->_server->get('IdServer') > 0)) {
				XMD_Log::error('VIEW FILTERMACROSPREVIEW: No se ha especificado el servidor en el que se quiere renderizar el nodo');
				return NULL;
			}
			$this->_isPreviewServer = $this->_server->get('Previsual');
		}

		return true;
	}

	private function _setServerNode ($args = array()) {

		if($this->_node) {
			$this->_serverNode = new Node($this->_node->getServer());
		} elseif (array_key_exists('SERVERNODE', $args)) {
			$this->_serverNode = new Node($args['SERVERNODE']);
		}

		// Check Params:
		if (!($this->_serverNode) || !is_object($this->_serverNode)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: No se ha especificado el servidor del nodo ' . $args['NODENAME'] . ' que quiere renderizar');
			return NULL;
		}

		return true;
	}

	private function _setIdSection ($args = array()) {
		if (array_key_exists('SECTION', $args)) {
			$this->_idSection = $args['SECTION'];
		}

		// Check Params:
		if (!isset($this->_idSection) || !($this->_idSection > 0)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: No se ha especificado la sección del nodo ' . $args['NODENAME'] . ' que quiere renderizar');
			return NULL;
		}

		return true;
	}

	private function _setProjectNode ($args = array()) {

		if($this->_node) {
			$this->_projectNode = $this->_node->getProject();
		} elseif (array_key_exists('PROJECT', $args)) {
			$this->_projectNode = $args['PROJECT'];
		}

		// Check Params:
		if (!isset($this->_projectNode) || !($this->_projectNode > 0)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: No se ha especificado el proyecto del nodo ' . $args['NODENAME'] . ' que quiere renderizar');
			return NULL;
		}

		return true;
	}

	private function _setDepth ($args = array()) {

		if($this->_node) {
			$this->_depth = $this->_node->GetPublishedDepth();
		} elseif (array_key_exists('DEPTH', $args)) {
			$this->_depth = $args['DEPTH'];
		}

		// Check Param:
		if (!isset($this->_depth) || !($this->_depth > 0)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: No se ha especificado la profundidad del nodo ' . $args['NODENAME'] . ' que quiere renderizar');
			return NULL;
		}

		return true;
	}

	private function _setNodeName ($args = array()) {

		if($this->_node) {
			$this->_nodeName = $this->_node->get('Name');
		} elseif (array_key_exists('NODENAME', $args)) {
			$this->_nodeName = $args['NODENAME'];
		}

		// Check Param:
		if (!isset($this->_nodeName) || $this->_nodeName == "") {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: No se ha especificado el nombre del nodo que quiere renderizar');
			return NULL;
		}

		return true;
	}

	private function getSectionPath($matches) {

		$target = $matches[1];
		$node = new Node($target);
		if (!($node->get('IdNode') > 0)) {
			return Config::getValue('EmptyHrefCode');
		}
		// Target Channel
		$idTargetChannel = isset($matches[2]) ? $matches[2] : NULL;
		$idSection = $node->GetSection();
		$section = new Node($idSection);
		$dotdot = str_repeat('../', $this->_depth - 2);
		return $dotdot . $section->GetPublishedPath($idTargetChannel, true);
	}

	private function getdotdotpath($matches) {

		$section = new Node($this->_idSection);
		$sectionPath = $section->class->GetNodeURL() . "/";

		if ($this->_node != null){
			if ($this->_node->nodeType->geddt('Name') == 'XimNewsNewLanguage'){
				 $sectionPath .= 'news/';
			}
		}else if ($this->_nodeTypeName != null){
			if ($this->_nodeTypeName == 'XimNewsNewLanguage'){
				$sectionPath .= 'news/';
			}
		}else{
			XMD_Log::error("VIEW FILTERMACROSPREVIEW:no se ha podido determinar si se trata de un node de tipo XimNewsNewLanguage");
		}

		$targetPath = $matches[1];
		$dotdot = str_repeat('../', $this->_depth - 2);
		return $sectionPath . $dotdot . $targetPath;
	}


	private function getLinkPath($matches) {
		
		$targetID = $matches[1];
		// Link target-node
		$targetNode = new Node($targetID);

		if (!$targetNode->get('IdNode')) {
			return '';
		}

		if($this->_node && !$this->_node->get('IdNode')) {
			return '';
		}

		$isStructuredDocument = $targetNode->nodeType->GetIsStructuredDocument();

		// Target Channel
		$idTargetChannel = isset($matches[2]) ? $matches[2] : $this->_idChannel;
		$targetChannelNode = new Channel($idTargetChannel);

		// External Link
		if ($targetNode->nodeType->get('Name') == 'Link') {
			return $targetNode->class->GetUrl();
		}

		if ($isStructuredDocument) {
			if ($this->mode == 'dinamic') {
				error_log('returning js');
//				error_log('javascript:onclick(loadDivsPreview(' . $targetID . '))');
				return "javascript:parent.loadDivsPreview(" . $targetID . ")";
			} else {
				$query = App::get('QueryManager');
	    		return $query->getPage() . $query->buildWith(array('nodeid' => $targetID, 'idchannel' => $idTargetChannel));
			}
		} else {
			return $targetNode->class->GetNodeURL();
		}
	}

	private function getRDFByNodeId($params, $rdfa=false) {

		if (!ModulesManager::isEnabled('ximPAS')) {
			return '';
		}

		$nodeId = $params[1];
		$node = new Node($nodeId);
		if (!$node->get('IdNode')) {
			return '';
		}

		$pas = new PAS_Conector();
		$rdf = $rdfa === false ? $pas->getRDFByNodeId($nodeId) : $pas->getRDFaByNodeId($nodeId);
		return "\n$rdf\n";
	}

	private function getRDFaByNodeId($params) {
		return $this->getRDFByNodeId($params, true);
	}
}
?>
