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
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



ModulesManager::file('/inc/model/Versions.inc');
ModulesManager::file('/inc/model/node.inc');
ModulesManager::file('/inc/model/channel.inc');
ModulesManager::file('/inc/model/Server.class.php');
ModulesManager::file('/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/PAS_Conector.class.php', 'ximPAS');
ModulesManager::file( '/inc/modules/ModulesManager.class.php');
ModulesManager::file('/inc/repository/nodeviews/Abstract_View.class.php');
ModulesManager::file('/inc/repository/nodeviews/Interface_View.class.php');

class View_FilterMacros extends Abstract_View implements Interface_View {
	
	private $_node = NULL;
	private $_server = NULL;
	private $_serverNode = NULL;
	private $_projectNode = NULL;
	private $_idChannel;
	private $_isPreviewServer = false;
	private $_depth = NULL;
	private $_idSection = NULL;
	private $_nodeName = "";
	
	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		
		$content = $this->retrieveContent($pointer);
		if (!$this->_setNode($idVersion))
			return NULL;
		
		if (!$this->_setIdChannel($args))
			return NULL;
		
		if (!$this->_setServer($args))
			return NULL;
		
		if (!$this->_setServerNode($args))
			return NULL;
		
		if (!$this->_setProjectNode($args))
			return NULL;
		
		if (!$this->_setDepth($args))
			return NULL;
		
		if (!$this->_setNodeName($args))
			return NULL;
			
		// This transformation can be exploded into more macros, so must be first
		if (ModulesManager::isEnabled('ximTHEMES')) {
			$content = preg_replace_callback("/@@@RMximdex\.breadcrumbs\(\)@@@/", 
					array($this, 'deployBreadCrumbs'), $content);
			
			$content = preg_replace_callback("/@@@RMximdex\.ximMenu\(\)@@@/", 
					array($this, 'deployMenu'), $content);
		}
		
		$serverName = $this->_serverNode->get('Name');
		$content = preg_replace("/@@@RMximdex\.servername\(\)@@@/", $serverName, $content);
		
		if (preg_match("/@@@RMximdex\.projectname\(\)@@@/", $content)) {
			$project = new Node($this->_projectNode);
			$projectName = $project->get('Name');
			$content = preg_replace("/@@@RMximdex\.projectname\(\)@@@/", $projectName, $content);
		}
		
		$content = preg_replace("/@@@RMximdex\.nodename\(\)@@@/", $this->_nodeName, $content);
		
		$content = preg_replace_callback("/@@@RMximdex\.sectionpath\(([0-9]+)\)@@@/", 
				array($this, 'getSectionPath'), $content);
		
		$content = preg_replace_callback("/@@@RMximdex\.dotdot\(([^\)]*)\)@@@/", 
				array($this, 'getdotdotpath'), $content);
		
		// TODO: Join these 2 regex
		$content = preg_replace_callback("/@@@RMximdex\.pathto\(([0-9]+),([0-9]*)\)@@@/", 
				array($this, 'getLinkPath'), $content);
		
		$content = preg_replace_callback("/@@@RMximdex\.pathto\(([0-9]+)\)@@@/", 
				array($this, 'getLinkPath'), $content);
		
		$content = preg_replace_callback("/@@@RMximdex\.rdf\(([^\)]+)\)@@@/", 
				array($this, 'getRDFByNodeId'), $content);
		
		$content = preg_replace_callback("/@@@RMximdex\.rdfa\(([^\)]+)\)@@@/", 
				array($this, 'getRDFaByNodeId'), $content);
		
		$content = preg_replace_callback("/(<.*?)(uid=\".*?\")(.*?\/?>)/", array($this, 'removeUIDs'), $content);

		return $this->storeTmpContent($content);
	}
	
	private function deployBreadCrumbs($matches) {
		
		$lastInsertedBreadCrumb = $this->_node->get('IdNode');
		
		$result = $this->_node->query(
				sprintf(
						"select n.IdNode from FastTraverse ft" . " INNER JOIN Nodes n ON ft.IdNode = n.IdNode" .
								 " INNER JOIN NodeTypes nt ON nt.IdNodeType = n.IdNodeType" .
								 " WHERE ft.IdChild = %d AND nt.IsFolder = '1'", 
								$this->_node->get('IdNode')), MONO);
		
		$breadCrumbString = $this->_formBreadCrumbString($this->_node->get('Name'));
		if (is_array($result)) {
			foreach ($result as $parentNode) {
				
				$node = new Node($parentNode);
				$index = $node->class->getIndex();
				
				$idSection = $node->getSection();
				$section = new Node($idSection);
				if ($index > 0 && $index != $lastInsertedBreadCrumb) {
					if ($section->get('IdNodeType') != 5015) {
						$name = 'Home';
					} else {
						$name = $section->get('Name');
					}
					$breadCrumbString = $this->_formBreadCrumbString($name, $index) . $breadCrumbString;
					$lastInsertedBreadCrumb = $index;
				}
			}
		}
		return $breadCrumbString;
	}
	
	private function deployMenu() {
		
		$idNode = $this->_node->get('IdNode');
		
		$node = new Node();
		$result = $node->query(
				sprintf(
						"SELECT distinct IdChild" . " FROM FastTraverse ft" . " INNER JOIN NodeProperties np" .
								 " ON ft.IdChild = np.IdNode AND Property = 'is_section_index'" .
								 " ORDER BY ft.Depth DESC", $idNode), MONO);
		
		$paths = array();
		
		$recursiveArrays = array();
		foreach ($result as $idNode) {
			$node = new Node($idNode);
			$paths[] = array('path' => $node->getPublishedPath($node->getServer()), 
					'id_node' => $idNode);
		}
		
		// bucle para generar pathparts
		foreach ($paths as $pathInfo) {
			$idNode = $pathInfo['id_node'];
			$path = $pathInfo['path'];
			
			if ($path == '/') {
				continue;
			}
			
			$pathParts = explode('/', $path);
			array_shift($pathParts);
			
			if (!is_array($pathParts)) {
				continue;
			}
			
			assert(is_array($pathParts));
			assert(!in_array('', $pathParts));
			
			$reversedParts[] = array('path' => array_reverse($pathParts), 'id_node' => $idNode);
		
		}
		
		foreach ($reversedParts as $pathPart) {
			$recursiveArrays[] = $this->_iterativeToRecursiveArray($pathPart);
		}
		
		foreach ($recursiveArrays as $value) {
			assert(count($value) == 1);
		}
		
		$result = array();
		foreach ($recursiveArrays as $recursiveArray) {
			$result = array_merge_recursive($recursiveArray, $result);
		}
		
		return sprintf("<menu>%s</menu>", $this->_renderMenu($result));
	}
	
	private function _renderMenu($elements) {
		$menu = '<cuerpo>';
		assert(count($elements) == 1);
		
		// Bucle para coger el elemento actual
		foreach ($elements as $levelName => $value) {
			if (is_string($value)) {
				$menu .= sprintf('<opcion a_enlace_id="%s">%s</opcion>', $value, $levelName);
			}
			if (is_array($value)) {
				foreach ($value as $key => $kk) {
					if (is_string($kk)) {
						$menu .= sprintf('<opcion a_enlace_id="%s">%s</opcion>', $kk, 
								$levelName);
					}
				}
			}
		}
		
		//Bucle para coger el array de elementos anidados
		foreach ($elements as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $menuElement) {
					if (is_array($menuElement)) {
						$menu .= $this->_renderMenu($menuElement);
					}
				}
			}
		}
		
		$menu .= '</cuerpo>';
		return $menu;
	}
	
	private function _iterativeToRecursiveArray($pathPart) {
		$idNode = $pathPart['id_node'];
		$path = $pathPart['path'];
		
		$tmpArray = NULL;
		
		foreach ($path as $pathPart) {
			if (is_null($tmpArray)) {
				$tmpArray[$pathPart] = $idNode;
			} else {
				if (empty($pathPart)) {
					continue;
				} else {
					$tmpArray[$pathPart][] = $tmpArray;
				}
			}
		}
		
		$maxKey = 0;
		$maxDepth = 0;
		if (count($tmpArray) > 1) {
			foreach ($tmpArray as $key => $value) {
				$depth = $this->_getBranchDepth($value);
				if ($depth > $maxDepth) {
					$maxKey = $key;
					$maxDepth = $depth;
				}
			
			}
		}
		
		foreach ($tmpArray as $key => $value) {
			if ($key != $maxKey) {
				unset($tmpArray[$key]);
			}
		}
		
		return $tmpArray;
	}
	
	private function _getBranchDepth($array) {
		$levels = 0;
		if (!is_array($array)) {
			return $levels;
		}
		
		while (is_array($array)) {
			$keys = array_keys($array);
			
			assert(count($keys) == 1);
			$levels ++;
			
			$array = $array[$keys[0]];
		}
		return $levels;
	}
	
	private function _formBreadCrumbString($string, $idNode = NULL) {
		if (!empty($idNode)) {
			$link = sprintf(
					'<breadcrumb_link to="@@@RMximdex.pathto(%d)@@@">%s</breadcrumb_link>', 
					$idNode, XmlBase::recodeSrc($string, Config::getValue('displayEncoding')));
		}
		return sprintf("<breadcrumb>%s</breadcrumb>", 
				XmlBase::recodeSrc(isset($link) ? $link : $string, 
						Config::getValue('displayEncoding')));
	
	}
	
	private function _setNode($idVersion = NULL) {
		
		if (!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				XMD_Log::error(
						'VIEW FILTERMACROS: Se ha cargado una versión incorrecta (' . $idVersion .
								 ')');
				return NULL;
			}
			
			$this->_node = new Node($version->get('IdNode'));
			if (!($this->_node->get('IdNode') > 0)) {
				XMD_Log::error(
						'VIEW FILTERMACROS: El nodo que se está intentando convertir no existe: ' .
								 $version->get('IdNode'));
				return NULL;
			}
		}
		
		return true;
	}
	
	private function _setIdChannel($args = array()) {
		
		if (array_key_exists('CHANNEL', $args)) {
			$this->_idChannel = $args['CHANNEL'];
		}
		
		// Check Params:
		if (!isset($this->_idChannel) || !($this->_idChannel > 0)) {
			XMD_Log::error(
					'VIEW FILTERMACROS: No se ha especificado el canal del nodo ' . $args['NODENAME'] .
							 ' que quiere renderizar');
			return NULL;
		}
		
		return true;
	}
	
	private function _setServer($args = array()) {
		
		if (array_key_exists('SERVER', $args)) {
			$this->_server = new Server($args['SERVER']);
			if (!($this->_server->get('IdServer') > 0)) {
				XMD_Log::error(
						'VIEW FILTERMACROS: No se ha especificado el servidor en el que se quiere renderizar el nodo');
				return NULL;
			}
			$this->_isPreviewServer = $this->_server->get('Previsual');
		}
		
		return true;
	}
	
	private function _setServerNode($args = array()) {
		
		if ($this->_node) {
			$this->_serverNode = new Node($this->_node->getServer());
		} elseif (array_key_exists('SERVERNODE', $args)) {
			$this->_serverNode = $args['SERVERNODE'];
		}
		
		// Check Params:
		if (!($this->_serverNode) || !is_object($this->_serverNode)) {
			XMD_Log::error(
					'VIEW FILTERMACROS: No se ha especificado el servidor del nodo ' . $args['NODENAME'] .
							 ' que quiere renderizar');
			return NULL;
		}
		
		return true;
	}
	
	private function _setProjectNode($args = array()) {
		
		if ($this->_node) {
			$this->_projectNode = $this->_node->getProject();
		} elseif (array_key_exists('PROJECTNODE', $args)) {
			$this->_projectNode = $args['PROJECTNODE'];
		}
		
		// Check Params:
		if (!isset($this->_projectNode) || !($this->_projectNode > 0)) {
			XMD_Log::error(
					'VIEW FILTERMACROS: No se ha especificado el proyecto del nodo ' . $args['NODENAME'] .
							 ' que quiere renderizar');
			return NULL;
		}
		
		return true;
	}
	
	private function _setDepth($args = array()) {
		
		if ($this->_node) {
			$this->_depth = $this->_node->GetPublishedDepth();
		} elseif (array_key_exists('DEPTH', $args)) {
			$this->_depth = $args['DEPTH'];
		}
		
		// Check Param:
		if (!isset($this->_depth) || !($this->_depth > 0)) {
			XMD_Log::error(
					'VIEW FILTERMACROS: No se ha especificado la profundidad del nodo ' . $args['NODENAME'] .
							 ' que quiere renderizar');
			return NULL;
		}
		
		return true;
	}
	
	private function _setNodeName($args = array()) {
		
		if ($this->_node) {
			$this->_nodeName = $this->_node->get('Name');
		} elseif (array_key_exists('NODENAME', $args)) {
			$this->_nodeName = $args['NODENAME'];
		}
		
		// Check Param:
		if (!isset($this->_nodeName) || $this->_nodeName == "") {
			XMD_Log::error(
					'VIEW FILTERMACROS: No se ha especificado el nombre del nodo que quiere renderizar');
			return NULL;
		}
		
		return true;
	}
	
	/**
	* <p>Remove the uid attributes generated by the editor</p> 
	* @param array $matches Array containing the matches of the regular expression
	*
	* @return string String to be used to replace the matching of the regular expression 
	*/
	private function removeUIDs($matches) {
		return str_replace(" >",">", $matches[1].$matches[3]);
	}

	private function getSectionPath($matches) {
		
		$target = $matches[1];
		$node = new Node($target);
		if (!($node->get('IdNode') > 0)) {
			return Config::getValue('EmptyHrefCode');
		}
		$idSection = $node->GetSection();
		$section = new Node($idSection);
		if ($this->_isPreviewServer) {
			return Config::getValue('UrlRoot') . Config::getValue('NodeRoot') . '/' . $section->GetPublishedPath(
					NULL, true);
		}
		
		$idTargetServer = $node->getServer();
		
		if (!$this->_server->get('OverrideLocalPaths') && ($idTargetServer == $this->_serverNode->get(
				'IdNode'))) {
			$dotdot = str_repeat('../', $this->_depth - 2);
			return $dotdot . $section->GetPublishedPath($idTargetChannel, true);
		}
		
		return $targetServer->get('Url') . $section->GetPublishedPath($idTargetChannel, true);
	
	}
	
	private function getdotdotpath($matches) {
		
		$targetPath = $matches[1];
		
		if (!($this->_serverNode->get('IdNode') > 0)) {
			return Config::getValue("EmptyHrefCode");
		}
		
		/// Si es un un previo (absoluto a ximdex/nodes)
		if ($this->_isPreviewServer) {
			return Config::GetValue("UrlRoot") . Config::GetValue("NodeRoot") . "/" . $targetPath;
		} else {
			// Si es sincronizacion a produccion, puede ser relativo o absoluto.
			if ($this->_server->get('OverrideLocalPaths')) {
				return $this->_server->get('Url') . "/" . $targetPath;
			}
			
			$dotdot = str_repeat('../', $this->_depth - 2);
			return $dotdot . $targetPath;
		}
	}
	
	private function getLinkPath($matches) {
		$targetID = $matches[1];
		// Nodo de Destino del Enlace
		$targetNode = new Node($targetID);
		
		if (!$targetNode->get('IdNode')) {
			return '';
		}
		
		if ($this->_node && !$this->_node->get('IdNode')) {
			return '';
		}
		
		$isStructuredDocument = $targetNode->nodeType->GetIsStructuredDocument();
		
		// Canal de destino
		$idTargetChannel = isset($matches[2]) ? $matches[2] : NULL;
		
		$targetChannelNode = new Channel($idTargetChannel);
		
		if ($isStructuredDocument) {
			$idTargetChannel = ($targetChannelNode->get('IdChannel') > 0) ? $targetChannelNode->get(
					'IdChannel') : $this->_idChannel;
		}
		
        //      $idTargetChannel = isset($idTargetChannel) ? $idTargetChannel :  $this->_idChannel; 

		// Si es un enlace externo
		if ($targetNode->nodeType->get('Name') == 'Link') {
			return $targetNode->class->GetUrl();
		}
		
		if ($this->_isPreviewServer) {
			if ($isStructuredDocument) {
				return Config::getValue('UrlRoot') . Config::getValue('NodeRoot') . $targetNode->GetPublishedPath(
						$idTargetChannel, true);
			} else {
				return $targetNode->class->GetNodeURL();
			}
		}
		
		if (Config::getValue('PullMode') == 1) {
			
			return Config::getValue('UrlRoot') . '/services/pull/index.php?idnode=' . $targetNode->get(
					'IdNode') . '&idchannel=' . $idTargetChannel . '&idportal=' . $this->_serverNode->get(
					'IdNode');
		
		}
		
		$sync = new SynchroFacade();
		$idTargetServer = $sync->getServer($targetNode->get('IdNode'), $idTargetChannel, 
				$this->_server->get('IdServer'));
		
		$targetServer = new server($idTargetServer);
		$idTargetServer = $targetServer->get('IdServer');
		if (!($idTargetServer > 0)) {
			return Config::getValue('EmptyHrefCode');
		}
		
		if (!$this->_server->get('OverrideLocalPaths') && ($idTargetServer == $this->_server->get(
				'IdServer'))) {
			$dotdot = str_repeat('../', $this->_depth - 2);
			
			//Eliminamos la barra final
			$dotdot = preg_replace('/\/$/', '', $dotdot);
			$dotdot = './' . $dotdot;
			$urlDotDot = $dotdot . $targetNode->GetPublishedPath($idTargetChannel, true);
			$urlDotDot = str_replace("//", "/", $urlDotDot);
			return $urlDotDot;
		
		}
		
		return $targetServer->get('Url') . $targetNode->GetPublishedPath($idTargetChannel, true);
	}
	
	private function getRDFByNodeId($params, $rdfa = false) {
		
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
